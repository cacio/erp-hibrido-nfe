<?php

namespace App\Controllers;

use App\Core\Authorize;
use App\Core\Controller;
use App\Core\EntityManagerFactory;
use App\Models\Filial;
use App\Models\Participante;
use App\Models\User;
use App\Models\VenCabecalho;
use Doctrine\ORM\EntityManagerInterface;

use function Symfony\Component\String\s;

class NfeController extends Controller
{
    private EntityManagerInterface $em;

    public function __construct()
    {
        parent::__construct();
        $em = EntityManagerFactory::create();
        $this->em = $em;
    }
    public function index(): void
    {

        Authorize::authorize('nfe.view');
        $em = $this->em;
        $nfeList = $em->getRepository(VenCabecalho::class)->findBy(
            ['filial' => $this->em->find(Filial::class, $_SESSION['auth']['filial_id'])],
            ['data_emissao' => 'DESC']
        );

        $this->render('nfe/index', [
            'nfeList' => $nfeList
        ]);

    }
    public function create(): void
    {
        Authorize::authorize('nfe.create');

        $em = $this->em;

        // 🔑 cria NF-e em rascunho
        $nfe = new VenCabecalho();
        $nfe->setId(\Ramsey\Uuid\Uuid::uuid4()->toString());
        $nfe->setFilial($this->em->find(Filial::class, $_SESSION['auth']['filial_id']));
        $nfe->setUsuario($this->em->find(User::class, $_SESSION['auth']['user_id']));
        $nfe->setStatus(0); // rascunho
        $nfe->setModelo('55');
        $nfe->setSerie('1');
        $nfe->setTipoOperacao('SAIDA');
        $nfe->setNaturezaOperacao('VENDA');
        $nfe->setDataEmissao(new \DateTime());

        $em->persist($nfe);
        $em->flush();

        // 🔁 redireciona para edição
        header('Location: /nfe/' . $nfe->getId() . '/edit');
        exit;
    }
    public function edit(string $id): void
    {
        $nfe = $this->em->find(VenCabecalho::class, $id);

        if (!$nfe) {
            throw new \Exception('NF-e não encontrada');
        }

        $this->render('nfe/edit', [
            'nfe' => $nfe
        ]);
    }

    public function salvarRascunho(string $id): void
    {
        $em = $this->em;

        $nfe = $em->find(VenCabecalho::class, $id);
        if (!$nfe) {
            $this->json(['error' => 'NF-e não encontrada'], 404);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        // Modalidade de frete
        $mod = (int)($data['modalidade_frete'] ?? 0);
        $nfe->setModalidadeFrete($mod);

        // Transportadora
        if ($mod === 2 && !empty($data['transportadora_id'])) {
            $transp = $em->find(Participante::class, $data['transportadora_id']);
            if (!$transp) {
                $this->json(['error' => 'Transportadora inválida'], 422);
                return;
            }
            $nfe->setTransportadora($transp);
        } else {
            $nfe->setTransportadora(null);
        }

        $em->flush();

        $this->json(['success' => true]);
    }


    public function setDestinatario(string $id): void
    {
        $em = $this->em;
        $data = json_decode(file_get_contents('php://input'), true);

        $nfe = $em->find(VenCabecalho::class, $id);
        if (!$nfe) {
            $this->json(['error' => 'NF-e não encontrada'], 404);
            return;
        }

        // 🔎 verifica se já existem itens
        if ($nfe->getItens()->count() > 0 && empty($data['force'])) {
            $this->json([
                'confirm' => true,
                'message' => 'Alterar o destinatário irá remover os itens existentes. Deseja continuar?'
            ], 409);
            return;
        }


        if (!empty($data['force'])) {
            foreach ($nfe->getItens() as $item) {
                $em->remove($item);
            }
        }

        // segue normalmente
        $participante = $em->getReference(
            \App\Models\Participante::class,
            $data['participante_id']
        );

        $nfe->setParticipante($participante);
        $em->flush();

        $this->json(['success' => true]);
    }

    public function dados(string $id): void
    {
        $em = $this->em;

        $nfe = $em->find(VenCabecalho::class, $id);
        if (!$nfe) {
            $this->json(['error' => 'NF-e não encontrada'], 404);
            return;
        }

        $itens = [];
        foreach ($nfe->getItens() as $item) {
            $itens[] = [
                'id' => $item->getId(),
                'produto' => $item->getProduto()->getDescricao(),
                'produto_id' => $item->getProduto()->getId(),
                'ncm' => $item->getProduto()->getNcm(),
                'cfop' => $item->getCfop(),
                'quantidade' => (float)$item->getQuantidade(),
                'valor_unitario' => (float)$item->getValorUnitario(),
                'total' => (float)$item->getValorTotal(),
                'impostos' => $item->getImpostosJson()
            ];
        }

        //  $endereco = is_array($row['endereco_json'])
        //     ? $row['endereco_json']
        //     : json_decode($row['endereco_json'], true);

        $this->json([
            'id' => $nfe->getId(),
            'destinatario' => $nfe->getParticipante() ? [
                'id' => $nfe->getParticipante()->getId(),
                'nome' => $nfe->getParticipante()->getNomeRazao(),
                'documento' => $nfe->getParticipante()->getCpfCnpj(),
                'uf' => $nfe->getParticipante()->getUfPrincipal(),
                'municipio' => $nfe->getParticipante()->getEnderecoPrincipal()['municipio']
            ] : null,
            'transportadora' => $nfe->getTransportadora() ? [
                'id' => $nfe->getTransportadora()->getId(),
                'nome' => $nfe->getTransportadora()->getNomeRazao(),
                'documento' => $nfe->getTransportadora()->getCpfCnpj(),
                'municipio' => $nfe->getTransportadora()->getEnderecoPrincipal()['municipio']
            ] : null,
            'itens' => $itens
        ]);
    }



    private function json(array $data, int $status = 200): void
    {
        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($data);
    }
}

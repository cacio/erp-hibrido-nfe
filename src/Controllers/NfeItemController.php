<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\EntityManagerFactory;
use App\Models\VenCabecalho;
use App\Models\VenItem;
use App\Models\Produto;
use App\Services\FiscalRuleService;
use App\Services\TaxService;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class NfeItemController extends Controller
{
    private EntityManagerInterface $em;
    private FiscalRuleService $fiscalRuleService;
    private TaxService $taxService;
    public function __construct()
    {
        parent::__construct();
        $em = EntityManagerFactory::create();
        $this->em = $em;
        $this->fiscalRuleService = new FiscalRuleService($em);
        $this->taxService = new TaxService($em);
    }
    public function store(
        string $vendaId
    ) {

        $tenantId = $_SESSION['auth']['tenant_id'];

        $em = $this->em;
        $data = json_decode(file_get_contents('php://input'), true);

        $venda = $em->find(VenCabecalho::class, $vendaId);
        if (!$venda) {
            return $this->json(['error' => 'NF-e não encontrada'], 404);
        }

        if ($venda->getStatus() !== 0) {
            return $this->json(['error' => 'NF-e não pode ser alterada'], 400);
        }

        $produto = $em->find(Produto::class, $data['produto_id'] ?? null);
        if (!$produto) {
            return $this->json(['error' => 'Produto inválido'], 422);
        }

        // 🔹 Cria item
        $item = new VenItem();
        $item->setId(Uuid::uuid4()->toString());
        $item->setVenda($venda);
        $item->setProduto($produto);
        $item->setQuantidade((float)$data['quantidade']);
        $item->setValorUnitario((float)$data['valor_unitario']);
        $item->setNumeroItem($venda->getItens()->count() + 1);

        $item->setValorTotal(
            $item->getQuantidade() * $item->getValorUnitario()
        );

        if (!$venda->getParticipante()) {
            return $this->json([
                'error' => 'Selecione o destinatário antes de adicionar itens'
            ], 422);
        }
        // 🔹 Resolver regra fiscal
        $ufDestino = $venda->getParticipante()?->getUfPrincipal(); // pode ser null


        $regra = $this->fiscalRuleService->resolverRegra(
            $tenantId,
            $venda->getFilial()->getUf(),
            $ufDestino,
            $produto->getNcm()
        );

        if (!$regra) {
            return $this->json(['error' => 'Regra fiscal não encontrada'], 422);
        }


        // print_r($regra); exit;
        // 🔹 Calcular imposto OFICIAL
        $impostos = $this->taxService->calcularItem(
            $item,
            $venda,
            $regra
        );

        $item->setCfop($regra->getCfopPadrao());
        $item->setImpostosJson($impostos);

        $em->persist($item);
        $em->flush();

        // 🔹 Retorno pronto pra view
        $this->json([
            'item' => [
                'id' => $item->getId(),
                'numero' => $item->getNumeroItem(),
                'produto' => $produto->getDescricao(),
                'ncm' => $produto->getNcm(),
                'cfop' => $item->getCfop(),
                'quantidade' => $item->getQuantidade(),
                'valor_unitario' => number_format($item->getValorUnitario(), 2, ',', '.'),
                'total' => number_format($item->getValorTotal(), 2, ',', '.'),
                'impostos' => $impostos
            ]
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

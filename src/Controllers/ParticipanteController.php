<?php

namespace App\Controllers;

use App\Core\Authorize;
use App\Core\Controller;
use App\Services\ParticipanteService;
use App\Models\Participante;
use App\Core\EntityManagerFactory;
use App\Services\CepLookupService;
use App\Services\CnpjLookupService;
use App\Services\ParticipanteFiscalValidator;

class ParticipanteController extends Controller
{
    private ParticipanteService $service;

    public function __construct()
    {
        parent::__construct();

        $em = EntityManagerFactory::create();

        $this->service = new ParticipanteService($em);
    }

    // =========================
    // LISTAGEM
    // =========================
    public function index(): void
    {

        Authorize::authorize('cadastro.participantes.view');

        $tenantId = $_SESSION['auth']['tenant_id'];

        $pagina = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $q      = $_GET['q'] ?? '';
        $tipo   = $_GET['tipo'] ?? '';
        $ativo  = $_GET['ativo'] ?? '1';
        $nome_razao = $_GET['nome_razao'] ?? '';
        $filterdocumento = $_GET['filter-documento'] ?? '';

        $resultado = $this->service->buscarPaginado(
            $tenantId,
            [
                'q'    => $q,
                'tipo' => $tipo,
                'ativo' => $ativo,
                'nome_razao'=> $nome_razao,
                'filter-documento' => $filterdocumento,
            ],
            $pagina,
            20
        );

        $this->render('participantes/index', [
            'participantes' => $resultado['dados'],
            'paginacao'     => $resultado,
            'filtros'       => [
                'q'    => $q,
                'tipo' => $tipo,
                'ativo' => $ativo,
            ]
        ]);
    }

    // =========================
    // FORM DE CADASTRO
    // =========================
    public function create(): void
    {
        Authorize::authorize('cadastro.participantes.create');
        $this->render('participantes/create');
    }

    // =========================
    // SALVAR NOVO
    // =========================
    public function store(): void
    {
        $tenantId = $_SESSION['auth']['tenant_id'];
        $dados    = $_POST;

        try {
            $validator = new ParticipanteFiscalValidator();
            $validator->validar($dados);

            $this->service->criar($tenantId, $dados);

            $this->flash('success', 'Participante cadastrado com sucesso.');
            $this->redirect('/participantes');
        } catch (\DomainException $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect('/participantes/create');
        }
    }

    // =========================
    // EDITAR
    // =========================
    public function edit(string $id): void
    {
        $tenantId = $_SESSION['auth']['tenant_id'];
        Authorize::authorize('cadastro.participantes.edit');
        $participante = $this->service
            ->buscarPorId($tenantId, $id);

        if (!$participante) {
            $this->flash('error', 'Participante não encontrado.');
            $this->redirect('/participantes');
            return;
        }

        $this->render('participantes/edit', [
            'participante' => $participante
        ]);
    }

    // =========================
    // ATUALIZAR
    // =========================
    public function update(string $id): void
    {
        $tenantId = $_SESSION['auth']['tenant_id'];
        $dados    = $_POST;

        $participante = $this->service
            ->buscarPorId($tenantId, $id);

        if (!$participante) {
            $this->flash('error', 'Participante não encontrado.');
            $this->redirect('/participantes');
            return;
        }

        try {


            $this->service->atualizar($participante, $dados);

            $this->flash('success', 'Participante atualizado com sucesso.');
            $this->redirect('/participantes');
        } catch (\DomainException $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect("/participantes/{$id}/edit");
        }
    }

    // =========================
    // (FUTURO) BUSCA POR CPF/CNPJ
    // =========================
    public function buscarDocumento(): void
    {
        $tenantId = $_SESSION['auth']['tenant_id'];
        $doc = $_GET['cpf_cnpj'] ?? '';

        if (empty($doc)) {
            http_response_code(400);
            echo json_encode(['error' => 'Documento não informado']);
            return;
        }

        $participantes = $this->service
            ->buscarPorDocumento($tenantId, $doc);

        header('Content-Type: application/json');

        if (!$participantes['participante']) {
            echo json_encode([
                'cadastro_duplicar' => $participantes['duplicar'] ? true : false,
            ]);
            return;
        }

        echo json_encode([
            'id'            => $participantes['participante']->getId(),
            'cpf_cnpj'      => $participantes['participante']->getCpfCnpj(),
            'nome_razao'    => $participantes['participante']->getNomeRazao(),
            'nome_fantasia' => $participantes['participante']->getNomeFantasia(),
            'tipo_cadastro' => $participantes['participante']->getTipoCadastro(),
            'ind_iedest'    => $participantes['participante']->getIndIeDest(),
            'ie'            => $participantes['participante']->getIe(),
            'telefone'      => $participantes['participante']->getTelefone(),
            'email'         => $participantes['participante']->getEmail(),
            'enderecos'     => $participantes['participante']->getEnderecoJson(),
            'ativo'         => $participantes['participante']->isAtivo(),
            'cadastro_duplicar' => $participantes['duplicar'] ? true : false,
        ]);
    }

    public function buscarCnpjExterno(): void
    {
        $cnpj = $_GET['cnpj'] ?? '';

        $service = new CnpjLookupService();
        $dados = $service->buscar($cnpj);

        header('Content-Type: application/json');

        if (!$dados) {
            echo json_encode(null);
            return;
        }

        echo json_encode($dados);
    }


    public function buscarCep(): void
    {
        $cep = $_GET['cep'] ?? '';

        $service = new CepLookupService();
        $dados = $service->buscar($cep);

        header('Content-Type: application/json');

        if (!$dados) {
            echo json_encode(null);
            return;
        }

        echo json_encode($dados);
    }
}

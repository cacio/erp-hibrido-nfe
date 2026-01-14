<?php

namespace App\Controllers;

use App\Core\Authorize;
use App\Core\Controller;
use App\Services\ParticipanteService;
use App\Models\Participante;
use App\Core\EntityManagerFactory;

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
        $tenantId = $_SESSION['auth']['tenant_id'];

        Authorize::authorize('cadastro.participantes.view');

        $pagina = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $q      = $_GET['q'] ?? '';
        $tipo   = $_GET['tipo'] ?? '';
        $ativo  = $_GET['ativo'] ?? '1';

        $resultado = $this->service->buscarPaginado(
            $tenantId,
            [
                'q'    => $q,
                'tipo' => $tipo,
                'ativo'=> $ativo,
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
                'ativo'=> $ativo,
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
        $doc      = $_GET['cpf_cnpj'] ?? '';

        $participante = $this->service
            ->buscarPorDocumento($tenantId, $doc);

        header('Content-Type: application/json');
        echo json_encode($participante ? [
            'id'             => $participante->getId(),
            'cpf_cnpj'       => $participante->getCpfCnpj(),
            'nome_razao'     => $participante->getNomeRazao(),
            'nome_fantasia'  => $participante->getNomeFantasia(),
            'tipo_cadastro'  => $participante->getTipoCadastro(),
            'enderecos'      => $participante->getEnderecoJson()
        ] : null);
    }
}

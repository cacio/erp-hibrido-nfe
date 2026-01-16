<?php

namespace App\Controllers;

use App\Core\Authorize;
use App\Core\Controller;
use App\Core\EntityManagerFactory;
use App\Services\ProdutoService;
use Doctrine\ORM\EntityManagerInterface;

class ProdutoController extends Controller
{
    private ProdutoService $service;

    public function __construct()
    {
        parent::__construct();
        $em = EntityManagerFactory::create();
        $this->service = new ProdutoService($em);
    }

    // =========================
    // LISTAGEM
    // =========================
    public function index(): void
    {
        $tenantId = $_SESSION['auth']['tenant_id'];

        Authorize::authorize('cadastro.produto.view');

        $pagina = max(1, (int) ($_GET['page'] ?? 1));
        $limite = 20;

        $filtros = [
            'q'       => $_GET['q'] ?? '',
            'ativo'   => $_GET['ativo'] ?? '',
            'unidade' => $_GET['unidade'] ?? ''
        ];

        $resultado = $this->service->listarPaginado(
            $tenantId,
            $filtros,
            $pagina,
            $limite
        );

        $totalPaginas = (int) ceil($resultado['total'] / $limite);

        $this->render('produtos/index', [
            'produtos'  => $resultado['dados'],
            'filtros'   => $filtros,
            'paginacao' => [
                'total'      => $resultado['total'],
                'pagina'     => $pagina,
                'limite'     => $limite,
                'totalPages' => $totalPaginas
            ]
        ]);
    }

    // =========================
    // FORM CREATE
    // =========================
    public function create(): void
    {
        Authorize::authorize('cadastro.produto.create');
        $this->render('produtos/create');
    }

    // =========================
    // STORE
    // =========================
    public function store(): void
    {
        try {
            $tenantId = $_SESSION['auth']['tenant_id'];

            $this->service->criar($tenantId, $_POST);

            $this->redirect('/produtos');
        } catch (\DomainException $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect('/produtos/create');
        }
    }

    // =========================
    // FORM EDIT
    // =========================
    public function edit(string $id): void
    {
        $tenantId = $_SESSION['auth']['tenant_id'];

        Authorize::authorize('cadastro.produto.edit');

        $produto = $this->service->buscarPorId($tenantId, $id);

        if (!$produto) {
            $this->flash('error', 'Produto não encontrado.');
            $this->redirect('/produtos');
            return;
        }

        $this->render('produtos/edit', [
            'produto' => $produto
        ]);
    }

    // =========================
    // UPDATE
    // =========================
    public function update(string $id): void
    {
        try {
            $tenantId = $_SESSION['auth']['tenant_id'];

            $produto = $this->service->buscarPorId($tenantId, $id);

            if (!$produto) {
                throw new \DomainException('Produto não encontrado.');
            }

            $this->service->atualizar($produto, $_POST);

            $this->redirect('/produtos');
        } catch (\DomainException $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect("/produtos/{$id}/edit");
        }
    }
}

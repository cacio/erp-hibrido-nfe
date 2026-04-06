<?php

namespace App\Controllers;

use App\Core\Authorize;
use App\Core\Controller;
use App\Core\EntityManagerFactory;
use App\Services\EstoqueService;
use App\Services\ProdutoService;
use Doctrine\ORM\EntityManagerInterface;

class AjusteEstoqueController extends Controller
{
    private EstoqueService $estoque;
    private ProdutoService $produtos;

    public function __construct()
    {
        parent::__construct();
        $em = EntityManagerFactory::create();
        $this->estoque = new EstoqueService($em);
        $this->produtos = new ProdutoService($em);
    }

    public function create(): void
    {
        $tenantId = $_SESSION['auth']['tenant_id'];
        $produtos = $this->produtos->listarPorTenant($tenantId);

        $this->render('estoque/ajuste', [
            'produtos' => $produtos
        ]);
    }

    public function store(): void
    {
        try {
            Authorize::authorize('ajustar_estoque.create');
            $filialId =$_SESSION['auth']['filial_id'];
            $produtoId = $_POST['produto_id'];
            $quantidade = (float) $_POST['quantidade'];
            $custo = (float) $_POST['custo_unitario'];

            $produto = $this->produtos->buscarPorId(
                $_SESSION['auth']['tenant_id'],
                $produtoId
            );

            if (!$produto) {
                throw new \DomainException('Produto não encontrado.');
            }

            $this->estoque->entrada(
                $filialId,
                $produto,
                $quantidade,
                $custo,
                'AJUSTE',
                null,
                null,
                'AJUSTE_ESTOQUE',
                $_SESSION['auth']['user_id'],
                'Ajuste manual de estoque'
            );

            $this->flash('success', 'Estoque ajustado com sucesso.');
            $this->redirect('/estoque/ajuste');

        } catch (\DomainException $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect('/estoque/ajuste');
        }
    }
}

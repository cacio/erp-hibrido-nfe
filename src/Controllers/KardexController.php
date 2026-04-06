<?php

namespace App\Controllers;

use App\Core\Authorize;
use App\Core\Controller;
use App\Core\EntityManagerFactory;
use App\Services\EstoqueKardexService;
use App\Services\ProdutoService;
use Doctrine\ORM\EntityManagerInterface;

class KardexController extends Controller
{
    private EstoqueKardexService $kardex;
    private ProdutoService $produtos;
    private EntityManagerInterface $em;

    public function __construct()
    {
        parent::__construct();
        $em = EntityManagerFactory::create();
        $this->em = $em;
        $this->kardex = new EstoqueKardexService($em);
        $this->produtos = new ProdutoService($em);
    }

    public function index(): void
    {

        Authorize::authorize('estoque.kardex.view');
        $filialId = $_SESSION['auth']['filial_id'];

        $filtros = [
            'produto_id' => $_GET['produto_id'] ?? '',
            'tipo'       => $_GET['tipo'] ?? '',
            'origem'     => $_GET['origem'] ?? '',
            'data_ini'   => $_GET['data_ini'] ?? '',
            'data_fim'   => $_GET['data_fim'] ?? '',
        ];

        $movs = $this->kardex->listar($filialId, $filtros);

        // Monta dados com produto (sem JOIN no Service)
        $dados = [];
        foreach ($movs as $m) {
            $produto = $this->em
                ->getRepository(\App\Models\Produto::class)
                ->find($m->getProdutoId());

            $dados[] = [
                'mov'     => $m,
                'produto' => $produto
            ];
        }

        $produtos = $this->produtos->listarPorTenant($_SESSION['auth']['tenant_id']);

        $this->render('estoque/kardex', [
            'dados'    => $dados,
            'produtos' => $produtos,
            'filtros'  => $filtros
        ]);
    }
}

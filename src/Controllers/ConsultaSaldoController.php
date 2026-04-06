<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Authorize;
use App\Services\EstoqueConsultaService;
use Doctrine\ORM\EntityManagerInterface;
use App\Core\EntityManagerFactory;

class ConsultaSaldoController extends Controller
{
    private EstoqueConsultaService $service;
    private EntityManagerInterface $em;
    public function __construct()
    {
        parent::__construct();
        $em = EntityManagerFactory::create();
        $this->em = $em;
        $this->service = new EstoqueConsultaService($em);
    }

    public function index(): void
    {

        Authorize::authorize('estoque.saldo.view');
        $filialId = $_SESSION['auth']['filial_id']; //$this->getFilialId();

        $filtros = [
            'produto' => $_GET['produto'] ?? ''
        ];

        $saldos = $this->service->listarSaldoPorFilial(
            $filialId
        );

        $dados = [];

        foreach ($saldos as $saldo) {
            $produto = $this->em->getRepository(\App\Models\Produto::class)
                ->find($saldo->getProdutoId());

            if (!$produto) {
                continue;
            }

            if (
                !empty($filtros['produto']) &&
                stripos($produto->getDescricao(), $filtros['produto']) === false
            ) {
                continue;
            }

            $dados[] = [
                'produto' => $produto,
                'saldo'   => $saldo
            ];
        }

        // ordenar por descrição
        usort(
            $dados,
            fn($a, $b) =>
            strcmp($a['produto']->getDescricao(), $b['produto']->getDescricao())
        );

        $this->render('estoque/saldo', [
            'dados'   => $dados,
            'filtros' => $filtros
        ]);
    }
}

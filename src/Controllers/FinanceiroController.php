<?php

namespace App\Controllers;

use App\Core\Authorize;
use App\Core\Controller;
use App\Core\EntityManagerFactory;
use App\Services\FinanceiroService;
use App\Models\FinTitulo;
use Doctrine\ORM\EntityManagerInterface;

class FinanceiroController extends Controller
{
    private FinanceiroService $financeiro;
    private EntityManagerInterface $em;
    public function __construct()
    {
        parent::__construct();
        $em = EntityManagerFactory::create();
        $this->em = $em;
        $this->financeiro = new FinanceiroService($this->em);
    }

    /**
     * LISTAGEM
     */
    public function index(): void
    {
        Authorize::authorize('financeiro.view');
        $filialId = $_SESSION['auth']['filial_id'];
        $pagina = (int) ($_GET['page'] ?? 1);

        $filtros = [
            'tipo'   => $_GET['tipo'] ?? null,
            'status' => $_GET['status'] ?? null,
            'inicio' => $_GET['vencimento_de'] ?? null,
            'fim'    => $_GET['vencimento_ate'] ?? null,
        ];


        $resultado = $this->financeiro->listarPaginado(
            $_SESSION['auth']['filial_id'],
            $filtros,
            $pagina,
            20
        );

        $resumo  = $this->financeiro->resumoFinanceiro($filialId);

        $this->render('financeiro/index', [
            'titulos'   => $resultado['data'],
            'paginas'   => $resultado['pagination'],
            'resumo'  => $resumo,
            'filtros' => $filtros
        ]);
    }

    /**
     * FORMULÁRIO
     */
    public function create(): void
    {
        Authorize::authorize('financeiro.create');
        $this->render('financeiro/create');
    }

    /**
     * SALVAR
     */
    public function store(): void
    {
        try {
            $dados = $_POST;
           // print_r($dados); exit;
            $this->financeiro->criarManual(
                $_SESSION['auth']['filial_id'],
                $dados
            );

            $this->flash('success', 'Título financeiro cadastrado com sucesso.');
            $this->redirect('/financeiro');
        } catch (\Throwable $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect('/financeiro/create');
        }
    }

    public function pagar(array $params): void
    {
        try {
            $id = $params['id'];

            $titulo = $this->em->getRepository(FinTitulo::class)->find($id);

            if (!$titulo) {
                throw new \DomainException('Título não encontrado.');
            }

            $valor = (float) $_POST['valor_pago'];
            $forma = $_POST['forma_pagamento'] ?? 'N/I';

            if ($valor <= 0) {
                throw new \DomainException('Valor inválido.');
            }

            if ($valor < $titulo->getValor()) {
                $this->financeiro->pagarParcial($titulo, $valor, $forma);
            } else {
                $this->financeiro->pagar($titulo, $valor, $forma);
            }

            $this->flash('success', 'Pagamento registrado com sucesso.');
            $this->redirect('/financeiro');
        } catch (\Throwable $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect('/financeiro');
        }
    }

    public function cancelar(array $params): void
    {
        try {
            $id = $params['id'];

            $titulo = $this->em->getRepository(FinTitulo::class)->find($id);

            if (!$titulo) {
                throw new \DomainException('Título não encontrado.');
            }

            $this->financeiro->cancelar($titulo);

            $this->flash('success', 'Título cancelado.');
            $this->redirect('/financeiro');
        } catch (\Throwable $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect('/financeiro');
        }
    }

    public function detalhes(string $id): void
    {
        Authorize::authorize('financeiro.view');

        try {
            $dados = $this->financeiro->buscarDetalhes($id);
            header('Content-Type: application/json');
            echo json_encode($dados);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function baixaRapida(string $id): void
    {
        Authorize::authorize('financeiro.pay');

        try {
            $titulo = $this->em->getRepository(FinTitulo::class)
                ->find($id);

            if (!$titulo) {
                throw new \DomainException('Título não encontrado.');
            }

            $valor = (float) str_replace(
                [',', 'R$', ' '],
                ['.', '', ''],
                $_POST['valor']
            );

            $forma = $_POST['forma_pagamento'] ?? 'N/I';

            if ($valor < $titulo->getValor()) {
                $this->financeiro->pagarParcial($titulo, $valor, $forma);
            } else {
                $this->financeiro->pagar($titulo, $valor, $forma);
            }

            echo json_encode(['success' => true]);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function baixaLote(): void
    {
        Authorize::authorize('financeiro.pay');

        $conn = $this->em->getConnection();
        $conn->beginTransaction();

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['ids']) || !is_array($input['ids'])) {
                throw new \DomainException('Nenhum título selecionado.');
            }

            $tipo = $input['tipo'] ?? 'TOTAL';

            $resultado = [
                'pagos' => [],
                'falhas' => [],
                'total' => count($input['ids'])
            ];

            foreach ($input['ids'] as $id) {

                $titulo = $this->em->getRepository(FinTitulo::class)->find($id);

                if (!$titulo) {
                    throw new \DomainException("Título {$id} não encontrado.");
                }

                if ($titulo->getStatus() !== 'ABERTO') {
                    throw new \DomainException(
                        "Título {$id} com status inválido ({$titulo->getStatus()})"
                    );
                }

                $saldo = $titulo->getValor() - $titulo->getValorPago();

                if ($saldo <= 0) {
                    throw new \DomainException("Título {$id} com saldo zerado.");
                }

                // 🔥 calcula valor
                if ($tipo === 'FIXO') {
                    $valor = min($saldo, (float) $input['valor']);
                } elseif ($tipo === 'PERCENTUAL') {
                    $valor = round(
                        $saldo * ((float) $input['percentual'] / 100),
                        2
                    );
                } else {
                    $valor = $saldo;
                }

                if ($valor <= 0) {
                    throw new \DomainException("Valor inválido no título {$id}.");
                }

                // 🔁 reaproveita regras existentes
                if ($valor < $saldo) {
                    $this->financeiro->pagarParcial($titulo, $valor, 'LOTE');
                } else {
                    $this->financeiro->pagar($titulo, $valor, 'LOTE');
                }

                $resultado['pagos'][] = [
                    'id' => $id,
                    'valor' => $valor
                ];
            }

            // 🔐 tudo OK → commit
            $conn->commit();

            echo json_encode([
                'success' => true,
                'resultado' => $resultado
            ]);
        } catch (\Throwable $e) {

            // 🔥 qualquer erro → rollback
            $conn->rollBack();

            http_response_code(400);
            echo json_encode([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function previewBaixaLote(): void
    {
        Authorize::authorize('financeiro.pay');

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['ids']) || !is_array($input['ids'])) {
                throw new \DomainException('Nenhum título selecionado.');
            }

            $resumo = [
                'total_selecionados' => count($input['ids']),
                'pagaveis' => 0,
                'ignorados' => 0,
                'valor_total' => 0,
                'itens' => []
            ];

            foreach ($input['ids'] as $id) {

                $titulo = $this->em->getRepository(FinTitulo::class)->find($id);

                if (!$titulo || $titulo->getStatus() !== 'ABERTO') {
                    $resumo['ignorados']++;
                    continue;
                }

                $saldo = $titulo->getValor() - $titulo->getValorPago();

                if ($saldo <= 0) {
                    $resumo['ignorados']++;
                    continue;
                }

                $resumo['pagaveis']++;
                $resumo['valor_total'] += $saldo;

                $resumo['itens'][] = [
                    'id' => $id,
                    'saldo' => $saldo
                ];
            }

            echo json_encode([
                'success' => true,
                'resumo' => $resumo
            ]);
        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

<?php

namespace App\Services;

use App\Core\EntityManagerFactory;
use App\Models\FinTitulo;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use DomainException;

class FinanceiroService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function listarPaginado(
        string $filialId,
        array $filtros = [],
        int $page = 1,
        int $perPage = 20
    ): array {
        $offset = ($page - 1) * $perPage;

        $params = ['filial' => $filialId];
        $where  = ['t.filial_id = :filial'];

        if (!empty($filtros['tipo'])) {
            $where[] = 't.tipo = :tipo';
            $params['tipo'] = $filtros['tipo'];
        }

        if (!empty($filtros['status'])) {
            $where[] = 't.status = :status';
            $params['status'] = $filtros['status'];
        }

        if (!empty($filtros['inicio'])) {
            $where[] = 't.data_vencimento >= :inicio';
            $params['inicio'] = $filtros['inicio'];
        }

        if (!empty($filtros['fim'])) {
            $where[] = 't.data_vencimento <= :fim';
            $params['fim'] = $filtros['fim'];
        }

        if (!empty($filtros['search'])) {
            $where[] = '(p.nome_razao LIKE :search OR t.numero_documento LIKE :search)';
            $params['search'] = '%' . $filtros['search'] . '%';
        }

        $sqlWhere = implode(' AND ', $where);

        // TOTAL
        $total = $this->em->getConnection()->fetchOne(
            "SELECT COUNT(*)
         FROM fin_titulo t
         LEFT JOIN cad_participantes p ON p.id = t.participante_id
         WHERE {$sqlWhere}",
            $params
        );

        // LISTA
        $dados = $this->em->getConnection()->fetchAllAssociative(
            "
        SELECT
            t.id,
            t.data_vencimento,
            t.tipo,
            t.parcela,
            t.valor,
            t.status,
            t.numero_documento,
            p.nome_razao AS participante,
            t.valor_pago
        FROM fin_titulo t
        LEFT JOIN cad_participantes p ON p.id = t.participante_id
        WHERE {$sqlWhere}
        ORDER BY t.data_vencimento ASC
        LIMIT {$perPage} OFFSET {$offset}
        ",
            $params
        );

        return [
            'data' => $dados,
            'pagination' => [
                'page'       => $page,
                'per_page'   => $perPage,
                'total'      => (int) $total,
                'last_page'  => (int) ceil($total / $perPage)
            ]
        ];
    }


    public function resumoFinanceiro(string $filialId): array
    {
        $conn = $this->em->getConnection();

        $pagarHoje = $conn->fetchAssociative(
            "SELECT COUNT(*) qtd, COALESCE(SUM(valor - valor_pago),0) total
             FROM fin_titulo
             WHERE filial_id = :filial
               AND tipo = 'PAGAR'
               AND status IN ('ABERTO','PARCIAL')
               AND data_vencimento = CURDATE()",
            ['filial' => $filialId]
        );

        $receberHoje = $conn->fetchAssociative(
            "SELECT COUNT(*) qtd, COALESCE(SUM(valor - valor_pago),0) total
             FROM fin_titulo
             WHERE filial_id = :filial
               AND tipo = 'RECEBER'
               AND status IN ('ABERTO','PARCIAL')
               AND data_vencimento = CURDATE()",
            ['filial' => $filialId]
        );

        $atraso = $conn->fetchAssociative(
            "SELECT COUNT(*) qtd, COALESCE(SUM(valor - valor_pago),0) total
             FROM fin_titulo
             WHERE filial_id = :filial
               AND status IN ('ABERTO','PARCIAL')
               AND data_vencimento < CURDATE()",
            ['filial' => $filialId]
        );

        $saldoPrevisto = $conn->fetchOne(
            "SELECT COALESCE(SUM(
                CASE
                    WHEN tipo = 'RECEBER' THEN (valor - valor_pago)
                    WHEN tipo = 'PAGAR' THEN -(valor - valor_pago)
                    ELSE 0
                END
            ),0)
            FROM fin_titulo
            WHERE filial_id = :filial
              AND status IN ('ABERTO','PARCIAL')
              AND data_vencimento = CURDATE()",
            ['filial' => $filialId]
        );

        return [
            'pagar_hoje' => $pagarHoje,
            'receber_hoje' => $receberHoje,
            'atraso' => $atraso,
            'saldo_previsto' => $saldoPrevisto
        ];
    }

    /**
     * =========================
     * CRIAÇÃO MANUA
     * =========================
     */
    public function criarManual(string $filialId, array $dados): void
    {
        if (empty($dados['parcelas']) || !is_array($dados['parcelas'])) {
            throw new DomainException('É obrigatório informar as parcelas.');
        }

        $totalParcelas = count($dados['parcelas']);

        if ($totalParcelas < 1) {
            throw new DomainException('Quantidade de parcelas inválida.');
        }

        // =========================
        // Valores principais
        // =========================
        $valorOriginal = $this->money($dados['valor'] ?? 0);
        $juros         = $this->money($dados['juros'] ?? 0);
        $multa         = $this->money($dados['multa'] ?? 0);
        $desconto      = $this->money($dados['desconto'] ?? 0);

        $valorLiquido = $valorOriginal + $juros + $multa - $desconto;

        if ($valorLiquido <= 0) {
            throw new DomainException('Valor líquido inválido.');
        }

        // =========================
        // Número do documento
        // =========================
        $numeroDocumento = !empty($dados['numero_documento'])
            ? strtoupper(trim($dados['numero_documento']))
            : $this->gerarNumeroDocumento('MAN');



        // =========================
        // Cálculo das parcelas
        // =========================
        $valorBase = round($valorLiquido / $totalParcelas, 2);
        $resto    = round($valorLiquido - ($valorBase * $totalParcelas), 2);

        // Distribuição proporcional (opcional, mas correta)
        $jurosParcela    = round($juros / $totalParcelas, 2);
        $multaParcela    = round($multa / $totalParcelas, 2);
        $descontoParcela = round($desconto / $totalParcelas, 2);

        // =========================
        // Criação dos títulos
        // =========================
        foreach ($dados['parcelas'] as $idx => $parcela) {

            if (empty($parcela['vencimento'])) {
                throw new DomainException(
                    'Data de vencimento não informada na parcela ' . ($idx + 1)
                );
            }

            $numeroParcela = $idx + 1;

            $valorParcela = $valorBase;

            // Ajuste de centavos na primeira parcela
            if ($numeroParcela === 1) {
                $valorParcela += $resto;
            }

            $titulo = new FinTitulo(
                Uuid::uuid4()->toString(),
                $filialId
            );

            $titulo->setTipo($dados['tipo']);
            $titulo->setOrigem('MANUAL');
            $titulo->setStatus('ABERTO');

            $titulo->setNumeroDocumento($numeroDocumento);
            $titulo->setParcela($numeroParcela . '/' . $totalParcelas);

            $titulo->setParticipanteId($dados['participante_id'] ?? null);
            $titulo->setPlanoId($dados['plano_id'] ?? null);

            $titulo->setDataEmissao(
                new \DateTimeImmutable($dados['data_emissao'] ?? 'now')
            );

            $titulo->setDataVencimento(
                new \DateTimeImmutable($parcela['vencimento'])
            );

            $titulo->setValor($valorParcela);
            $titulo->setValorPago(0);

            // Distribuição correta
            $titulo->setJuros($jurosParcela);
            $titulo->setMulta($multaParcela);
            $titulo->setDesconto($descontoParcela);

            $titulo->setFormaPagamento($dados['forma_pagamento'] ?? null);
            $titulo->setObservacoes($dados['observacoes'] ?? null);
            $titulo->setUsuarioId($_SESSION['auth']['user_id']);

            $this->em->persist($titulo);

            AuditService::log(
                'CREATE',
                'fin_titulo',
                $titulo->getId(),
                sprintf(
                    'Criado título manual %s parcela %s (%s)',
                    $titulo->getTipo(),
                    $titulo->getParcela(),
                    $numeroDocumento
                )
            );
        }

        $this->em->flush();
    }

    private function money(string|float $valor): float
    {
        if (is_float($valor) || is_int($valor)) {
            return (float) $valor;
        }

        // remove tudo que não é número, ponto ou vírgula
        $valor = preg_replace('/[^\d,\.]/', '', $valor);

        // se tiver vírgula, ela é decimal
        if (str_contains($valor, ',')) {
            $valor = str_replace('.', '', $valor); // remove milhar
            $valor = str_replace(',', '.', $valor); // decimal
        }

        return (float) $valor;
    }


    /**
     * =========================
     * CRIAÇÃO PARCELADA
     * =========================
     */
    public function criarParcelado(
        string $filialId,
        array $dados,
        int $parcelas,
        int $intervaloDias = 30
    ): array {

        if ($parcelas < 1) {
            throw new DomainException('Número de parcelas inválido.');
        }

        if (empty($dados['valor'])) {
            throw new DomainException('Valor total não informado.');
        }

        $valorTotal = (float) $dados['valor'];
        $valorBase  = round($valorTotal / $parcelas, 2);
        $resto      = $valorTotal - ($valorBase * $parcelas);

        $titulos = [];

        for ($i = 1; $i <= $parcelas; $i++) {

            $valor = $valorBase;
            if ($i === 1) {
                $valor += $resto; // ajuste de centavos
            }

            $titulo = new FinTitulo(
                Uuid::uuid4()->toString(),
                $filialId
            );

            $dados['valor'] = $valor;

            $dados['data_vencimento'] = (new \DateTimeImmutable(
                $dados['data_vencimento']
            ))->modify('+' . (($i - 1) * $intervaloDias) . ' days')
                ->format('Y-m-d');

            $this->mapearDados(
                $titulo,
                $dados,
                "{$i}/{$parcelas}"
            );

            $this->em->persist($titulo);
            $titulos[] = $titulo;
        }

        $this->em->flush();

        AuditService::log(
            'CREATE',
            'fin_titulo',
            null,
            sprintf(
                'Criado lançamento financeiro parcelado (%dx) valor total R$ %.2f',
                $parcelas,
                $valorTotal
            )
        );

        return $titulos;
    }

    /**
     * =========================
     * PAGAMENTO TOTAL
     * =========================
     */
    public function pagar(
        FinTitulo $titulo,
        float $valorPago,
        string $formaPagamento
    ): void {

        if (in_array($titulo->getStatus(), ['ABERTO', 'PARCIAL']) === false) {
            throw new DomainException('Somente títulos em aberto podem ser pagos.');
        }

        $titulo->setValorPago($valorPago);
        $titulo->setFormaPagamento($formaPagamento);
        $titulo->setDataPagamento(new \DateTimeImmutable());
        $titulo->setStatus('PAGO');

        $this->em->flush();

        AuditService::log(
            'PAY',
            'fin_titulo',
            $titulo->getId(),
            sprintf(
                'Título pago no valor de R$ %.2f via %s',
                $valorPago,
                $formaPagamento
            )
        );
    }

    /**
     * =========================
     * PAGAMENTO PARCIAL
     * =========================
     */
    public function pagarParcial(
        FinTitulo $titulo,
        float $valorPago,
        string $formaPagamento
    ): void {

        if (in_array($titulo->getStatus(), ['ABERTO', 'PARCIAL']) === false) {
            throw new DomainException('Título não está em aberto.');
        }

        if ($valorPago <= 0) {
            throw new DomainException('Valor inválido.');
        }

        $novoPago = $titulo->getValorPago() + $valorPago;

        if ($novoPago >= $titulo->getValor()) {
            $this->pagar($titulo, $titulo->getValor(), $formaPagamento);
            return;
        }

        $titulo->setValorPago($novoPago);
        $titulo->setFormaPagamento($formaPagamento);
        $titulo->setStatus('PARCIAL');

        $this->em->flush();

        AuditService::log(
            'PAY_PARTIAL',
            'fin_titulo',
            $titulo->getId(),
            sprintf(
                'Pagamento parcial de R$ %.2f',
                $valorPago
            )
        );
    }

    /**
     * =========================
     * CANCELAMENTO
     * =========================
     */
    public function cancelar(FinTitulo $titulo): void
    {
        if ($titulo->getStatus() === 'PAGO') {
            throw new DomainException('Título pago não pode ser cancelado.');
        }

        $titulo->setStatus('CANCELADO');
        $this->em->flush();

        AuditService::log(
            'CANCEL',
            'fin_titulo',
            $titulo->getId(),
            'Título financeiro cancelado'
        );
    }

    public function buscarDetalhes(string $tituloId): array
    {
        $conn = $this->em->getConnection();

        $titulo = $conn->fetchAssociative(
            "SELECT
            t.id,
            t.tipo,
            t.valor,
            t.valor_pago,
            t.status,
            t.numero_documento,
            t.parcela,
            t.data_vencimento,
            p.nome_razao AS participante
         FROM fin_titulo t
         LEFT JOIN cad_participantes p ON p.id = t.participante_id
         WHERE t.id = :id",
            ['id' => $tituloId]
        );

        if (!$titulo) {
            throw new \DomainException('Título não encontrado.');
        }

        $parcelas = $conn->fetchAllAssociative(
            "SELECT
            id,
            parcela,
            data_vencimento,
            valor,
            valor_pago,
            status
         FROM fin_titulo
         WHERE numero_documento = :doc
         ORDER BY data_vencimento ASC",
            ['doc' => $titulo['numero_documento']]
        );

        return [
            'titulo' => $titulo,
            'parcelas' => $parcelas
        ];
    }


    /**
     * =========================
     * MAPEAMENTO PADRÃO
     * =========================
     */
    private function mapearDados(
        FinTitulo $titulo,
        array $dados,
        string $parcela
    ): void {

        $titulo->setTipo($dados['tipo']);
        $titulo->setOrigem($dados['origem'] ?? 'MANUAL');
        $titulo->setValor((float) $dados['valor']);
        $titulo->setDataVencimento(
            new \DateTimeImmutable($dados['data_vencimento'])
        );
        $titulo->setParcela($parcela);

        if (!empty($dados['plano_id'])) {
            $titulo->setPlanoId($dados['plano_id']);
        }

        if (!empty($dados['participante_id'])) {
            $titulo->setParticipanteId($dados['participante_id']);
        }

        if (!empty($dados['documento_id'])) {
            $titulo->setDocumentoId($dados['documento_id']);
        }

        if (!empty($dados['documento_tipo'])) {
            $titulo->setDocumentoTipo($dados['documento_tipo']);
        }

        if (!empty($dados['numero_documento'])) {
            $titulo->setNumeroDocumento($dados['numero_documento']);
        }

        if (!empty($dados['observacoes'])) {
            $titulo->setObservacoes($dados['observacoes']);
        }

        if (!empty($_SESSION['auth']['user_id'])) {
            $titulo->setUsuarioId($_SESSION['auth']['user_id']);
        }
    }

    private function gerarNumeroDocumento(string $prefixo): string
    {
        $conn = $this->em->getConnection();
        $conn->beginTransaction();

        try {
            $row = $conn->fetchAssociative(
                'SELECT ultimo_numero FROM fin_documento_seq WHERE prefixo = :p FOR UPDATE',
                ['p' => $prefixo]
            );

            if (!$row) {
                $numero = 1;
                $conn->insert('fin_documento_seq', [
                    'prefixo' => $prefixo,
                    'ultimo_numero' => $numero
                ]);
            } else {
                $numero = $row['ultimo_numero'] + 1;
                $conn->update(
                    'fin_documento_seq',
                    ['ultimo_numero' => $numero],
                    ['prefixo' => $prefixo]
                );
            }

            $conn->commit();

            return sprintf('%s-%06d', $prefixo, $numero);
        } catch (\Throwable $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}

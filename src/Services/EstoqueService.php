<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use App\Models\EstoqueMovimento;
use App\Models\EstoqueSaldo;
use App\Models\Produto;

class EstoqueService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    // ======================================================
    // ENTRADA DE ESTOQUE (compra, produção, ajuste positivo)
    // ======================================================
    public function entrada(
        string $filialId,
        Produto $produto,
        float $quantidade,
        float $custoUnitario,
        string $origem,
        ?string $loteId = null,
        ?string $documentoId = null,
        ?string $documentoTipo = null,
        ?string $usuarioId = null,
        ?string $observacao = null
    ): void {
        if ($quantidade <= 0) {
            throw new \DomainException('Quantidade inválida para entrada.');
        }

        $saldo = $this->obterOuCriarSaldo($filialId, $produto->getId(), $loteId);

        $saldoAnterior = $saldo->getQuantidade();
        $novoSaldo = $saldoAnterior + $quantidade;

        // custo médio ponderado
        $custoAnterior = $saldo->getCustoMedio();
        $novoCustoMedio = $this->calcularCustoMedio(
            $saldoAnterior,
            $custoAnterior,
            $quantidade,
            $custoUnitario
        );

        // movimentação
        $mov = new EstoqueMovimento(
            Uuid::uuid4()->toString(),
            $filialId,
            $produto->getId(),
            $loteId
        );

        $mov->registrarEntrada(
            $quantidade,
            $custoUnitario,
            $custoAnterior,
            $novoCustoMedio,
            $saldoAnterior,
            $novoSaldo,
            $origem,
            $documentoId,
            $documentoTipo,
            $usuarioId,
            $observacao
        );

        // atualiza saldo
        $saldo->setQuantidade($novoSaldo);
        $saldo->setCustoMedio($novoCustoMedio);

        $this->em->persist($mov);
        $this->em->persist($saldo);
        $this->em->flush();
    }

    // ======================================================
    // SAÍDA DE ESTOQUE (venda, produção, ajuste negativo)
    // ======================================================
    public function saida(
        string $filialId,
        Produto $produto,
        float $quantidade,
        string $origem,
        ?string $loteId = null,
        ?string $documentoId = null,
        ?string $documentoTipo = null,
        ?string $usuarioId = null,
        ?string $observacao = null
    ): void {
        if ($quantidade <= 0) {
            throw new \DomainException('Quantidade inválida para saída.');
        }

        $saldo = $this->obterOuCriarSaldo($filialId, $produto->getId(), $loteId);

        if ($saldo->getQuantidade() < $quantidade) {
            throw new \DomainException('Estoque insuficiente.');
        }

        $saldoAnterior = $saldo->getQuantidade();
        $novoSaldo = $saldoAnterior - $quantidade;

        $custoMedio = $saldo->getCustoMedio();

        $mov = new EstoqueMovimento(
            Uuid::uuid4()->toString(),
            $filialId,
            $produto->getId(),
            $loteId
        );

        $mov->registrarSaida(
            $quantidade,
            $custoMedio,
            $saldoAnterior,
            $novoSaldo,
            $origem,
            $documentoId,
            $documentoTipo,
            $usuarioId,
            $observacao
        );

        $saldo->setQuantidade($novoSaldo);

        $this->em->persist($mov);
        $this->em->persist($saldo);
        $this->em->flush();
    }

    // ======================================================
    // SALDO
    // ======================================================
    private function obterOuCriarSaldo(
        string $filialId,
        string $produtoId,
        ?string $loteId
    ): EstoqueSaldo {
        $repo = $this->em->getRepository(EstoqueSaldo::class);

        $saldo = $repo->findOneBy([
            'filialId'  => $filialId,
            'produtoId' => $produtoId,
            'loteId'    => $loteId
        ]);

        if ($saldo) {
            return $saldo;
        }

        $saldo = new EstoqueSaldo(
            Uuid::uuid4()->toString(),
            $filialId,
            $produtoId,
            $loteId
        );

        $saldo->setQuantidade(0);
        $saldo->setCustoMedio(0);

        $this->em->persist($saldo);

        return $saldo;
    }

    // ======================================================
    // CUSTO MÉDIO
    // ======================================================
    private function calcularCustoMedio(
        float $saldoAnterior,
        float $custoAnterior,
        float $entradaQtd,
        float $entradaCusto
    ): float {
        $totalAnterior = $saldoAnterior * $custoAnterior;
        $totalEntrada = $entradaQtd * $entradaCusto;

        $novoTotal = $totalAnterior + $totalEntrada;
        $novaQtd = $saldoAnterior + $entradaQtd;

        if ($novaQtd <= 0) {
            return 0;
        }

        return round($novoTotal / $novaQtd, 4);
    }
}

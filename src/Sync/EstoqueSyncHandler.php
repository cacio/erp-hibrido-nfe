<?php

namespace App\Sync;

use App\Models\EstoqueMovimento;
use App\Models\EstoqueSaldo;
use App\Services\SyncMapService;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Ramsey\Uuid\Uuid;

class EstoqueSyncHandler implements SyncHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private SyncMapService $map
    ) {}

    /**
     * UPSERT → registra movimentação + atualiza saldo
     */
    public function upsert(array $payload, SyncContext $ctx): void
    {
        // 🔒 Validações mínimas
        foreach (['produto_id', 'tipo', 'origem', 'quantidade'] as $campo) {
            if (!isset($payload[$campo])) {
                throw new DomainException(
                    "Campo obrigatório ausente no estoque sync: {$campo}"
                );
            }
        }

        // 🔁 Produto DESK → WEB
        $produtoWebId = $this->map->getWebId(
            $ctx->filialId,
            'cad_produtos',
            (string) $payload['produto_id']
        );

        if (!$produtoWebId) {
            throw new DomainException('Produto não mapeado no Web.');
        }

        // 📦 Busca ou cria saldo
        $saldo = $this->obterOuCriarSaldo(
            $ctx->filialId,
            $produtoWebId
        );

        $saldoAnterior      = $saldo->getQuantidade();
        $custoMedioAnterior = $saldo->getCustoMedio();
        $quantidade         = (float) $payload['quantidade'];

        // 🧾 Cria movimento
        $mov = new EstoqueMovimento(
            Uuid::uuid4()->toString(),
            $ctx->filialId,
            $produtoWebId,
            $payload['lote_id'] ?? null
        );

        if (!empty($payload['data_mov'])) {
            $mov->setDataMov(new \DateTimeImmutable($payload['data_mov']));
        }

        // ➕ ENTRADA
        if ($payload['tipo'] === 'ENTRADA') {

            $novoSaldo = $saldoAnterior + $quantidade;

            $novoCustoMedio = $novoSaldo > 0
                ? (
                    ($saldoAnterior * $custoMedioAnterior)
                    + ($quantidade * (float) ($payload['custo_unitario'] ?? 0))
                ) / $novoSaldo
                : 0;

            // Atualiza saldo
            $saldo->setQuantidade($novoSaldo);
            $saldo->setCustoMedio($novoCustoMedio);

            // Registra movimento
            $mov->registrarEntrada(
                $quantidade,
                (float) ($payload['custo_unitario'] ?? 0),
                $custoMedioAnterior,
                $novoCustoMedio,
                $saldoAnterior,
                $novoSaldo,
                $payload['origem'],
                $payload['documento_id'] ?? null,
                $payload['documento_tipo'] ?? null,
                $ctx->usuarioId ?? null,
                $payload['observacao'] ?? null
            );

        }
        // ➖ SAÍDA
        elseif ($payload['tipo'] === 'SAIDA') {

            if ($saldoAnterior < $quantidade) {
                throw new DomainException('Saldo insuficiente para saída.');
            }

            $novoSaldo = $saldoAnterior - $quantidade;

            $saldo->setQuantidade($novoSaldo);
            // custo médio NÃO muda

            $mov->registrarSaida(
                $quantidade,
                $custoMedioAnterior,
                $saldoAnterior,
                $novoSaldo,
                $payload['origem'],
                $payload['documento_id'] ?? null,
                $payload['documento_tipo'] ?? null,
                $ctx->usuarioId ?? null,
                $payload['observacao'] ?? null
            );

        } else {
            throw new DomainException('Tipo de movimento inválido.');
        }

        // 💾 Persiste tudo
        $this->em->persist($mov);
        $this->em->flush();
    }

    /**
     * DELETE → estoque NÃO permite delete físico
     */
    public function delete(array $payload, SyncContext $ctx): void
    {
        throw new DomainException(
            'Delete de movimento de estoque não é permitido.'
        );
    }

    /**
     * Busca ou cria saldo por filial + produto
     */
    private function obterOuCriarSaldo(
        string $filialId,
        string $produtoId
    ): EstoqueSaldo {

        $repo = $this->em->getRepository(EstoqueSaldo::class);

        $saldo = $repo->findOneBy([
            'filialId'  => $filialId,
            'produtoId' => $produtoId,
        ]);

        if ($saldo) {
            return $saldo;
        }

        $saldo = new EstoqueSaldo(
            Uuid::uuid4()->toString(),
            $filialId,
            $produtoId
        );

        $this->em->persist($saldo);

        return $saldo;
    }
}

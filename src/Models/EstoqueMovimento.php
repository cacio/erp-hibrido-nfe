<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'est_movimento')]
class EstoqueMovimento
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(name: 'filial_id', type: 'string', length: 36)]
    private string $filialId;

    #[ORM\Column(name: 'produto_id', type: 'string', length: 36)]
    private string $produtoId;

    #[ORM\Column(name: 'lote_id', type: 'string', length: 36, nullable: true)]
    private ?string $loteId = null;

    #[ORM\Column(name: 'data_mov', type: 'datetime')]
    private \DateTimeInterface $dataMov;

    #[ORM\Column(type: 'string', length: 10)]
    private string $tipo; // ENTRADA | SAIDA

    #[ORM\Column(type: 'string', length: 20)]
    private string $origem;

    #[ORM\Column(type: 'decimal', precision: 15, scale: 4)]
    private float $quantidade;

    #[ORM\Column(name: 'custo_unitario', type: 'decimal', precision: 15, scale: 4)]
    private float $custoUnitario;

    #[ORM\Column(name: 'custo_medio', type: 'decimal', precision: 15, scale: 4)]
    private float $custoMedio;

    #[ORM\Column(name: 'saldo_anterior', type: 'decimal', precision: 15, scale: 4)]
    private float $saldoAnterior;

    #[ORM\Column(name: 'saldo_posterior', type: 'decimal', precision: 15, scale: 4)]
    private float $saldoPosterior;

    #[ORM\Column(name: 'documento_id', length: 36, nullable: true)]
    private ?string $documentoId = null;

    #[ORM\Column(name: 'documento_tipo', length: 50, nullable: true)]
    private ?string $documentoTipo = null;

    #[ORM\Column(name: 'usuario_id', length: 36, nullable: true)]
    private ?string $usuarioId = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $observacao = null;

    public function __construct(
        string $id,
        string $filialId,
        string $produtoId,
        ?string $loteId = null
    ) {
        $this->id = $id;
        $this->filialId = $filialId;
        $this->produtoId = $produtoId;
        $this->loteId = $loteId;
        $this->dataMov = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFilialId(): string
    {
        return $this->filialId;
    }

    public function getProdutoId(): string
    {
        return $this->produtoId;
    }

    public function getLoteId(): ?string
    {
        return $this->loteId;
    }

    public function getDataMov(): \DateTimeInterface
    {
        return $this->dataMov;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getOrigem(): string
    {
        return $this->origem;
    }

    public function getQuantidade(): float
    {
        return $this->quantidade;
    }

    public function getCustoUnitario(): float
    {
        return $this->custoUnitario;
    }

    public function getCustoMedio(): float
    {
        return $this->custoMedio;
    }

    public function getSaldoAnterior(): float
    {
        return $this->saldoAnterior;
    }

    public function getSaldoPosterior(): float
    {
        return $this->saldoPosterior;
    }

    public function getDocumentoId(): ?string
    {
        return $this->documentoId;
    }

    public function getDocumentoTipo(): ?string
    {
        return $this->documentoTipo;
    }

    public function getUsuarioId(): ?string
    {
        return $this->usuarioId;
    }

    public function getObservacao(): ?string
    {
        return $this->observacao;
    }

    public function setObservacao(?string $observacao): void
    {
        $this->observacao = $observacao;
    }

    public function setUsuarioId(?string $usuarioId): void
    {
        $this->usuarioId = $usuarioId;
    }

    public function setDocumentoId(?string $documentoId): void
    {
        $this->documentoId = $documentoId;
    }

    public function setDocumentoTipo(?string $documentoTipo): void
    {
        $this->documentoTipo = $documentoTipo;
    }

    public function setLoteId(?string $loteId): void
    {
        $this->loteId = $loteId;
    }

    public function setDataMov(\DateTimeInterface $dataMov): void
    {
        $this->dataMov = $dataMov;
    }

    public function setOrigem(string $origem): void
    {
        $this->origem = $origem;
    }

    public function setQuantidade(float $quantidade): void
    {
        $this->quantidade = $quantidade;
    }

    public function setCustoUnitario(float $custoUnitario): void
    {
        $this->custoUnitario = $custoUnitario;
    }

    public function setCustoMedio(float $custoMedio): void
    {
        $this->custoMedio = $custoMedio;
    }

    public function setSaldoAnterior(float $saldoAnterior): void
    {
        $this->saldoAnterior = $saldoAnterior;
    }

    public function setSaldoPosterior(float $saldoPosterior): void
    {
        $this->saldoPosterior = $saldoPosterior;
    }

    public function setTipo(string $tipo): void
    {
        $this->tipo = $tipo;
    }

    public function setFilialId(string $filialId): void
    {
        $this->filialId = $filialId;
    }

    public function setProdutoId(string $produtoId): void
    {
        $this->produtoId = $produtoId;
    }

    public function getResumo(): string
    {
        return sprintf(
            '%s - %s: %s unidades a %s cada. Saldo anterior: %s, Saldo posterior: %s',
            $this->tipo,
            $this->origem,
            number_format($this->quantidade, 4),
            number_format($this->custoUnitario, 4),
            number_format($this->saldoAnterior, 4),
            number_format($this->saldoPosterior, 4)
        );
    }

    public function isEntrada(): bool
    {
        return $this->tipo === 'ENTRADA';
    }

    public function isSaida(): bool
    {
        return $this->tipo === 'SAIDA';
    }





    // =======================
    // REGISTROS DE MOVIMENTO
    // =======================

    public function registrarEntrada(
        float $quantidade,
        float $custoUnitario,
        float $custoAnterior,
        float $novoCustoMedio,
        float $saldoAnterior,
        float $saldoPosterior,
        string $origem,
        ?string $documentoId,
        ?string $documentoTipo,
        ?string $usuarioId,
        ?string $observacao
    ): void {
        $this->tipo = 'ENTRADA';
        $this->origem = $origem;
        $this->quantidade = $quantidade;
        $this->custoUnitario = $custoUnitario;
        $this->custoMedio = $novoCustoMedio;
        $this->saldoAnterior = $saldoAnterior;
        $this->saldoPosterior = $saldoPosterior;
        $this->documentoId = $documentoId;
        $this->documentoTipo = $documentoTipo;
        $this->usuarioId = $usuarioId;
        $this->observacao = $observacao;
    }

    public function registrarSaida(
        float $quantidade,
        float $custoMedio,
        float $saldoAnterior,
        float $saldoPosterior,
        string $origem,
        ?string $documentoId,
        ?string $documentoTipo,
        ?string $usuarioId,
        ?string $observacao
    ): void {
        $this->tipo = 'SAIDA';
        $this->origem = $origem;
        $this->quantidade = $quantidade;
        $this->custoUnitario = $custoMedio;
        $this->custoMedio = $custoMedio;
        $this->saldoAnterior = $saldoAnterior;
        $this->saldoPosterior = $saldoPosterior;
        $this->documentoId = $documentoId;
        $this->documentoTipo = $documentoTipo;
        $this->usuarioId = $usuarioId;
        $this->observacao = $observacao;
    }
}

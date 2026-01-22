<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'fin_titulo')]
class FinTitulo
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(name: 'filial_id', type: 'string', length: 36)]
    private string $filialId;

    #[ORM\Column(name: 'plano_id', type: 'string', length: 36, nullable: true)]
    private ?string $planoId = null;

    #[ORM\Column(name: 'participante_id', type: 'string', length: 36, nullable: true)]
    private ?string $participanteId = null;

    #[ORM\Column(name: 'documento_id', type: 'string', length: 36, nullable: true)]
    private ?string $documentoId = null;

    #[ORM\Column(name: 'documento_tipo', type: 'string', length: 50, nullable: true)]
    private ?string $documentoTipo = null;

    #[ORM\Column(type: 'string', length: 10)]
    private string $tipo; // PAGAR | RECEBER

    #[ORM\Column(type: 'string', length: 10)]
    private string $origem = 'MANUAL';

    #[ORM\Column(name: 'numero_documento', type: 'string', length: 50, nullable: true)]
    private ?string $numeroDocumento = null;

    #[ORM\Column(type: 'string', length: 10)]
    private string $parcela = '1/1';

    #[ORM\Column(name: 'data_emissao', type: 'date')]
    private \DateTimeInterface $dataEmissao;

    #[ORM\Column(name: 'data_vencimento', type: 'date')]
    private \DateTimeInterface $dataVencimento;

    #[ORM\Column(name: 'data_pagamento', type: 'date', nullable: true)]
    private ?\DateTimeInterface $dataPagamento = null;

    #[ORM\Column(type: 'decimal', precision: 15, scale: 2)]
    private float $valor;

    #[ORM\Column(name: 'valor_pago', type: 'decimal', precision: 15, scale: 2)]
    private float $valorPago = 0.00;

    #[ORM\Column(type: 'decimal', precision: 15, scale: 2)]
    private float $juros = 0.00;

    #[ORM\Column(type: 'decimal', precision: 15, scale: 2)]
    private float $multa = 0.00;

    #[ORM\Column(type: 'decimal', precision: 15, scale: 2)]
    private float $desconto = 0.00;

    #[ORM\Column(type: 'string', length: 10)]
    private string $status = 'ABERTO';

    #[ORM\Column(name: 'forma_pagamento', type: 'string', length: 50, nullable: true)]
    private ?string $formaPagamento = null;

    #[ORM\Column(name: 'observacoes', type: 'text', nullable: true)]
    private ?string $observacoes = null;

    #[ORM\Column(name: 'usuario_id', type: 'string', length: 36, nullable: true)]
    private ?string $usuarioId = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    public function __construct(string $id, string $filialId)
    {
        $this->id = $id;
        $this->filialId = $filialId;
        $this->dataEmissao = new \DateTimeImmutable();
        $this->createdAt  = new \DateTimeImmutable();
        $this->updatedAt  = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }


    // ========================
    // GETTERS & SETTERS
    // ========================

    public function setParticipanteId(?string $participanteId): void
    {
        $this->participanteId = $participanteId;
    }

    public function setPlanoId(string $planoId): void
    {
        $this->planoId = $planoId;
    }

    public function setTipo(string $tipo): void
    {
        if (!in_array($tipo, ['PAGAR', 'RECEBER'])) {
            throw new \DomainException('Tipo financeiro inválido.');
        }
        $this->tipo = $tipo;
    }

    public function setOrigem(string $origem): void
    {
        if (!in_array($origem, ['MANUAL', 'VENDA', 'COMPRA', 'NFE'])) {
            throw new \DomainException('Origem financeira inválida.');
        }
        $this->origem = $origem;
    }

    public function setDocumentoId(?string $documentoId): void
    {
        $this->documentoId = $documentoId;
    }

    public function setDescricao(string $descricao): void
    {
        $this->observacoes = $descricao;
    }

    public function setValor(float $valor): void
    {
        if ($valor <= 0) {
            throw new \DomainException('Valor deve ser maior que zero.');
        }
        $this->valor = $valor;
    }

    public function setDataVenc(\DateTimeInterface $data): void
    {
        $this->dataVencimento = $data;
    }

    public function setNumeroDocumento(?string $numeroDocumento): void
    {
        $this->numeroDocumento = $numeroDocumento;
    }

    public function setParcela(string $parcela): void
    {
        $this->parcela = $parcela;
    }

    public function setUsuarioId(?string $usuarioId): void
    {
        $this->usuarioId = $usuarioId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getValor(): float
    {
        return $this->valor;
    }

    public function getDataVencimento(): \DateTimeInterface
    {
        return $this->dataVencimento;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getFilialId(): string
    {
        return $this->filialId;
    }

    public function getParticipanteId(): ?string
    {
        return $this->participanteId;
    }

    public function getPlanoId(): ?string
    {
        return $this->planoId;
    }

    public function getNumeroDocumento(): ?string
    {
        return $this->numeroDocumento;
    }

    public function getObservacoes(): ?string
    {
        return $this->observacoes;
    }

    public function getDataEmissao(): \DateTimeInterface
    {
        return $this->dataEmissao;
    }

    public function setDataEmissao(\DateTimeInterface $dataEmissao): void
    {
        $this->dataEmissao = $dataEmissao;
    }

    public function getDataPagamento(): ?\DateTimeInterface
    {
        return $this->dataPagamento;
    }

    public function getValorPago(): float
    {
        return $this->valorPago;
    }

    public function getJuros(): float
    {
        return $this->juros;
    }

    public function getMulta(): float
    {
        return $this->multa;
    }

    public function getDesconto(): float
    {
        return $this->desconto;
    }
    public function getFormaPagamento(): ?string
    {
        return $this->formaPagamento;
    }

    public function setFormaPagamento(?string $formaPagamento): void
    {
        $this->formaPagamento = $formaPagamento;
    }

    public function setDataPagamento(?\DateTimeInterface $dataPagamento): void
    {
        $this->dataPagamento = $dataPagamento;
    }

    public function setValorPago(float $valorPago): void
    {
        $this->valorPago = $valorPago;
    }

    public function setJuros(float $juros): void
    {
        $this->juros = $juros;
    }

    public function setMulta(float $multa): void
    {
        $this->multa = $multa;
    }

    public function setDesconto(float $desconto): void
    {
        $this->desconto = $desconto;
    }

    public function setObservacoes(?string $observacoes): void
    {
        $this->observacoes = $observacoes;
    }

    public function setDocumentoTipo(?string $documentoTipo): void
    {
        $this->documentoTipo = $documentoTipo;
    }
    public function getDocumentoTipo(): ?string
    {
        return $this->documentoTipo;
    }

    public function getDocumentoId(): ?string
    {
        return $this->documentoId;
    }

    public function getOrigem(): string
    {
        return $this->origem;
    }

    public function getParcela(): string
    {
        return $this->parcela;
    }

    public function getUsuarioId(): ?string
    {
        return $this->usuarioId;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setDataVencimento(\DateTimeInterface $dataVencimento): void
    {
        $this->dataVencimento = $dataVencimento;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function isPago(): bool
    {
        return $this->status === 'PAGO';
    }

    public function isAberto(): bool
    {
        return $this->status === 'ABERTO';
    }

    public function isCancelado(): bool
    {
        return $this->status === 'CANCELADO';
    }

    public function marcarComoAberto(): void
    {
        if ($this->status !== 'CANCELADO') {
            throw new \DomainException('Somente títulos cancelados podem ser reabertos.');
        }
        $this->status = 'ABERTO';
    }

    public function marcarComoCancelado(): void
    {
        if ($this->status === 'PAGO') {
            throw new \DomainException('Título pago não pode ser cancelado.');
        }
        $this->status = 'CANCELADO';
    }

    public function marcarComoPendente(): void
    {
        if ($this->status !== 'ABERTO') {
            throw new \DomainException('Somente títulos em aberto podem ser marcados como pendentes.');
        }
        $this->status = 'PENDENTE';
    }

    public function marcarComoVencido(): void
    {
        if ($this->status !== 'ABERTO') {
            throw new \DomainException('Somente títulos em aberto podem ser marcados como vencidos.');
        }
        $this->status = 'VENCIDO';
    }

    public function marcarComoEmAberto(): void
    {
        if (!in_array($this->status, ['PENDENTE', 'VENCIDO'])) {
            throw new \DomainException('Somente títulos pendentes ou vencidos podem ser reabertos.');
        }
        $this->status = 'ABERTO';
    }

    public function marcarComoEmDia(): void
    {
        if ($this->status !== 'ABERTO') {
            throw new \DomainException('Somente títulos em aberto podem ser marcados como em dia.');
        }
        $this->status = 'EM_DIA';
    }

    public function marcarComoAtrasado(): void
    {
        if ($this->status !== 'ABERTO') {
            throw new \DomainException('Somente títulos em aberto podem ser marcados como atrasados.');
        }
        $this->status = 'ATRASADO';
    }



    public function marcarComoPago(): void
    {
        if ($this->status !== 'ABERTO') {
            throw new \DomainException('Somente títulos em aberto podem ser pagos.');
        }
        $this->status = 'PAGO';
    }

    public function cancelar(): void
    {
        if ($this->status === 'PAGO') {
            throw new \DomainException('Título pago não pode ser cancelado.');
        }
        $this->status = 'CANCELADO';
    }
}

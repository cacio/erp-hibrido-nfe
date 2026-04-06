<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'est_lotes')]
class EstoqueLote
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'string', length: 36)]
    private string $tenantId;

    #[ORM\Column(name: 'produto_id', type: 'string', length: 36)]
    private string $produtoId;

    #[ORM\Column(name: 'numero_lote', length: 50)]
    private string $numeroLote;

    #[ORM\Column(name: 'data_fabricacao', type: 'date', nullable: true)]
    private ?\DateTimeInterface $dataFabricacao = null;

    #[ORM\Column(name: 'data_validade', type: 'date', nullable: true)]
    private ?\DateTimeInterface $dataValidade = null;

    #[ORM\Column(name: 'data_abate', type: 'date', nullable: true)]
    private ?\DateTimeInterface $dataAbate = null;

    #[ORM\Column(name: 'sif_origem', length: 20, nullable: true)]
    private ?string $sifOrigem = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $observacao = null;

    public function __construct(
        string $id,
        string $tenantId,
        string $produtoId,
        string $numeroLote
    ) {
        $this->id = $id;
        $this->tenantId = $tenantId;
        $this->produtoId = $produtoId;
        $this->numeroLote = $numeroLote;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProdutoId(): string
    {
        return $this->produtoId;
    }

    public function getNumeroLote(): string
    {
        return $this->numeroLote;
    }

    public function getDataValidade(): ?\DateTimeInterface
    {
        return $this->dataValidade;
    }

    public function setDataValidade(?\DateTimeInterface $dataValidade): void
    {
        $this->dataValidade = $dataValidade;
    }

    public function getObservacao(): ?string
    {
        return $this->observacao;
    }

    public function setObservacao(?string $observacao): void
    {
        $this->observacao = $observacao;
    }

    public function getDataFabricacao(): ?\DateTimeInterface
    {
        return $this->dataFabricacao;
    }

    public function setDataFabricacao(?\DateTimeInterface $dataFabricacao): void
    {
        $this->dataFabricacao = $dataFabricacao;
    }

    public function getDataAbate(): ?\DateTimeInterface
    {
        return $this->dataAbate;
    }

    public function setDataAbate(?\DateTimeInterface $dataAbate): void
    {
        $this->dataAbate = $dataAbate;
    }

    public function getSifOrigem(): ?string
    {
        return $this->sifOrigem;
    }

    public function setSifOrigem(?string $sifOrigem): void
    {
        $this->sifOrigem = $sifOrigem;
    }


}

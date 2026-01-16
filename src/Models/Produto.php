<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'cad_produtos')]
class Produto
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'string', length: 36)]
    private string $tenantId;

    #[ORM\Column(name: 'descricao', length: 200)]
    private string $descricao;

    #[ORM\Column(name: 'gtin_ean', length: 14, nullable: true)]
    private ?string $gtinEan = null;

    #[ORM\Column(name: 'codigo_sku', length: 50, nullable: true)]
    private ?string $codigoSku = null;

    #[ORM\Column(name: 'ncm', length: 8, nullable: true)]
    private ?string $ncm = null;

    #[ORM\Column(name: 'cest', length: 7, nullable: true)]
    private ?string $cest = null;

    #[ORM\Column(name: 'unidade', length: 6)]
    private string $unidade = 'UN';

    #[ORM\Column(name: 'tipo_item_sped', length: 2)]
    private string $tipoItemSped = '00';

    #[ORM\Column(name: 'peso_liquido', type: 'decimal', precision: 12, scale: 4)]
    private float $pesoLiquido = 0.0000;

    #[ORM\Column(name: 'peso_bruto', type: 'decimal', precision: 12, scale: 4)]
    private float $pesoBruto = 0.0000;

    #[ORM\Column(name: 'preco_venda', type: 'decimal', precision: 15, scale: 2)]
    private float $precoVenda = 0.00;

    #[ORM\Column(name: 'preco_custo', type: 'decimal', precision: 15, scale: 4)]
    private float $precoCusto = 0.0000;

    #[ORM\Column(type: 'boolean')]
    private bool $ativo = true;

    public function __construct(string $id, string $tenantId)
    {
        $this->id = $id;
        $this->tenantId = $tenantId;
    }

    // ================= GETTERS / SETTERS =================

    public function getId(): string
    {
        return $this->id;
    }

    public function getTenantId(): string
    {
        return $this->tenantId;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }

    public function getGtinEan(): ?string
    {
        return $this->gtinEan;
    }

    public function setGtinEan(?string $gtinEan): void
    {
        $this->gtinEan = $gtinEan;
    }

    public function getCodigoSku(): ?string
    {
        return $this->codigoSku;
    }

    public function setCodigoSku(?string $codigoSku): void
    {
        $this->codigoSku = $codigoSku;
    }

    public function getNcm(): ?string
    {
        return $this->ncm;
    }

    public function setNcm(?string $ncm): void
    {
        $this->ncm = $ncm;
    }

    public function getCest(): ?string
    {
        return $this->cest;
    }

    public function setCest(?string $cest): void
    {
        $this->cest = $cest;
    }

    public function getUnidade(): string
    {
        return $this->unidade;
    }

    public function setUnidade(string $unidade): void
    {
        $this->unidade = strtoupper($unidade);
    }

    public function getTipoItemSped(): string
    {
        return $this->tipoItemSped;
    }

    public function setTipoItemSped(string $tipo): void
    {
        $this->tipoItemSped = $tipo;
    }

    public function getPesoLiquido(): float
    {
        return $this->pesoLiquido;
    }

    public function setPesoLiquido(float $peso): void
    {
        $this->pesoLiquido = $peso;
    }

    public function getPesoBruto(): float
    {
        return $this->pesoBruto;
    }

    public function setPesoBruto(float $peso): void
    {
        $this->pesoBruto = $peso;
    }

    public function getPrecoVenda(): float
    {
        return $this->precoVenda;
    }

    public function setPrecoVenda(float $preco): void
    {
        $this->precoVenda = $preco;
    }

    public function getPrecoCusto(): float
    {
        return $this->precoCusto;
    }

    public function setPrecoCusto(float $preco): void
    {
        $this->precoCusto = $preco;
    }

    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    public function setAtivo(bool $ativo): void
    {
        $this->ativo = $ativo;
    }
}

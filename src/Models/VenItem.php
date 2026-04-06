<?php

namespace App\Models;

use App\Models\Produto;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'ven_itens')]
class VenItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: VenCabecalho::class, inversedBy: 'itens')]
    #[ORM\JoinColumn(name: 'venda_id', referencedColumnName: 'id', nullable: false)]
    private VenCabecalho $venda;

    #[ORM\ManyToOne(targetEntity: Produto::class)]
    #[ORM\JoinColumn(name: 'produto_id', referencedColumnName: 'id', nullable: false)]
    private Produto $produto;

    #[ORM\Column(name:"numero_item",type: 'integer')]
    private int $numeroItem;

    #[ORM\Column(type: 'string', length: 4)]
    private string $cfop;

    #[ORM\Column(type: 'decimal', precision: 15, scale: 4)]
    private float $quantidade;

    #[ORM\Column(name:"valor_unitario",  type: 'decimal', precision: 15, scale: 4)]
    private float $valorUnitario;

    #[ORM\Column(name:"valor_desconto",type: 'decimal', precision: 15, scale: 2)]
    private float $valorDesconto = 0.00;

    #[ORM\Column(name:"valor_total",  type: 'decimal', precision: 15, scale: 2)]
    private float $valorTotal;

    #[ORM\Column(name:"impostos_json", type: 'json', nullable: true)]
    private ?array $impostosJson = null;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getProduto(): Produto
    {
        return $this->produto;
    }

    public function setProduto(Produto $produto): void
    {
        $this->produto = $produto;
    }

    public function getNumeroItem(): int
    {
        return $this->numeroItem;
    }

    public function setNumeroItem(int $numeroItem): void
    {
        $this->numeroItem = $numeroItem;
    }

    public function getCfop(): string
    {
        return $this->cfop;
    }

    public function setCfop(string $cfop): void
    {
        $this->cfop = $cfop;
    }

    public function getQuantidade(): float
    {
        return $this->quantidade;
    }

    public function setQuantidade(float $quantidade): void
    {
        $this->quantidade = $quantidade;
    }

    public function getValorUnitario(): float
    {
        return $this->valorUnitario;
    }

    public function setValorUnitario(float $valorUnitario): void
    {
        $this->valorUnitario = $valorUnitario;
    }

    public function getValorDesconto(): float
    {
        return $this->valorDesconto;
    }

    public function setValorDesconto(float $valorDesconto): void
    {
        $this->valorDesconto = $valorDesconto;
    }

    public function getValorTotal(): float
    {
        return $this->valorTotal;
    }

    public function setValorTotal(float $valorTotal): void
    {
        $this->valorTotal = $valorTotal;
    }

    public function getImpostosJson(): ?array
    {
        return $this->impostosJson;
    }

    public function setImpostosJson(?array $impostosJson): void
    {
        $this->impostosJson = $impostosJson;
    }

    public function getVenda(): VenCabecalho
    {
        return $this->venda;
    }

    public function setVenda(VenCabecalho $venda): void
    {
        $this->venda = $venda;
    }

    public function calcularTotal(): void
    {
        $this->valorTotal = ($this->quantidade * $this->valorUnitario) - $this->valorDesconto;
    }
}

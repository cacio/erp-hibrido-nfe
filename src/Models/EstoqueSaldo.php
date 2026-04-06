<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'est_saldo')]
class EstoqueSaldo
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

    #[ORM\Column(type: 'decimal', precision: 15, scale: 4)]
    private float $quantidade = 0;

    #[ORM\Column(name: 'custo_medio', type: 'decimal', precision: 15, scale: 4)]
    private float $custoMedio = 0;

    #[ORM\Column(type: 'decimal', precision: 15, scale: 4)]
    private float $reservado = 0;

/*    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'produto_id', referencedColumnName: 'id')]
    private Produto $produto;*/


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



    public function getQuantidade(): float
    {
        return $this->quantidade;
    }

    public function setQuantidade(float $qtd): void
    {
        $this->quantidade = $qtd;
    }

    public function getCustoMedio(): float
    {
        return $this->custoMedio;
    }

    public function setCustoMedio(float $custo): void
    {
        $this->custoMedio = $custo;
    }

    public function getReservado(): float
    {
        return $this->reservado;
    }
    public function setReservado(float $reservado): void
    {
        $this->reservado = $reservado;
    }

   /* public function getProduto(): Produto
    {
        return $this->produto;
    }

    public function setProduto(Produto $produto): void
    {
        $this->produto = $produto;
    }*/




}

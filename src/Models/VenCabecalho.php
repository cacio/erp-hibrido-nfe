<?php

namespace App\Models;

use App\Models\Filial;
use App\Models\Participante;
use App\Models\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'ven_cabecalho')]
class VenCabecalho
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Filial::class)]
    #[ORM\JoinColumn(name: 'filial_id', referencedColumnName: 'id', nullable: false)]
    private Filial $filial;

    #[ORM\ManyToOne(targetEntity: Participante::class)]
    #[ORM\JoinColumn(name: 'participante_id', referencedColumnName: 'id', nullable: true)]
    private ?Participante $participante = null;

    #[ORM\ManyToOne(targetEntity: Participante::class)]
    #[ORM\JoinColumn(name: 'transportadora_id', referencedColumnName: 'id', nullable: true)]
    private ?Participante $transportadora = null;

    #[ORM\Column(type: 'string', length: 2)]
    private string $modelo = '55';

    #[ORM\Column(type: 'string', length: 3)]
    private string $serie = '1';

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $numero = null;

    #[ORM\Column(type: 'string', length: 44, nullable: true)]
    private ?string $chave_acesso = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $protocolo = null;

    #[ORM\Column(type: 'integer')]
    private int $status = 0; // 0=Digitação

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $data_emissao;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $data_saida = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $natureza_operacao = 'VENDA';

    #[ORM\Column(type: 'string', length: 10)]
    private string $tipo_operacao = 'SAIDA';

    #[ORM\Column(name:'valor_produtos', type: 'decimal', precision: 15, scale: 2)]
    private float $valor_produtos = 0.00;

    #[ORM\Column(name:'valor_frete', type: 'decimal', precision: 15, scale: 2)]
    private float $valorFrete = 0.00;

    #[ORM\Column(name:'valor_desconto',type: 'decimal', precision: 15, scale: 2)]
    private float $valorDesconto = 0.00;

    #[ORM\Column(name:'valor_total',  type: 'decimal', precision: 15, scale: 2)]
    private float $valorTotal = 0.00;

    #[ORM\Column(name:'xml_nfe',type: 'text', nullable: true)]
    private ?string $xmlNfe = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $observacoes = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'usuario_id', referencedColumnName: 'id', nullable: true)]
    private ?User $usuario = null;

    #[ORM\OneToMany(
        mappedBy: 'venda',
        targetEntity: VenItem::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $itens;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->data_emissao = new \DateTime();
        $this->itens = new ArrayCollection();
    }

    /* =============================
     * RELACIONAMENTO ITENS
     * ============================= */

    public function addItem(VenItem $item): void
    {
        $item->setVenda($this);
        $this->itens->add($item);
    }

    public function getItens(): Collection
    {
        return $this->itens;
    }

    /* =============================
     * GETTERS / SETTERS essenciais
     * ============================= */

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getFilial(): Filial
    {
        return $this->filial;
    }

    public function setFilial(Filial $filial): void
    {
        $this->filial = $filial;
    }

    public function getParticipante(): ?Participante
    {
        return $this->participante;
    }

    public function setParticipante(Participante $participante): void
    {
        $this->participante = $participante;
    }

    public function getTransportadora(): ?Participante
    {
        return $this->transportadora;
    }

    public function setTransportadora(?Participante $transportadora): void
    {
        $this->transportadora = $transportadora;
    }

    public function getDataEmissao(): \DateTimeInterface
    {
        return $this->data_emissao;
    }

    public function setDataEmissao(\DateTimeInterface $data): void
    {
        $this->data_emissao = $data;
    }

    public function getProtocolo(): ?string
    {
        return $this->protocolo;
    }

    public function setProtocolo(?string $protocolo): void
    {
        $this->protocolo = $protocolo;
    }

    public function getModelo(): string
    {
        return $this->modelo;
    }

    public function setModelo(string $modelo): void
    {
        $this->modelo = $modelo;
    }

    public function getSerie(): string
    {
        return $this->serie;
    }

    public function setSerie(string $serie): void
    {
        $this->serie = $serie;
    }

    public function getChaveAcesso(): ?string
    {
        return $this->chave_acesso;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function getNaturezaOperacao(): string
    {
        return $this->natureza_operacao;
    }

    public function setNaturezaOperacao(string $natureza): void
    {
        $this->natureza_operacao = $natureza;
    }

    public function getTipoOperacao(): string
    {
        return $this->tipo_operacao;
    }

    public function setTipoOperacao(string $tipo): void
    {
        $this->tipo_operacao = $tipo;
    }

    public function getValorProdutos(): float
    {
        return $this->valor_produtos;
    }

    public function setValorProdutos(float $valor): void
    {
        $this->valor_produtos = $valor;
    }

    public function getValorFrete(): float
    {
        return $this->valorFrete;
    }

    public function setValorFrete(float $valor): void
    {
        $this->valorFrete = $valor;
    }
    public function getValorDesconto(): float
    {
        return $this->valorDesconto;
    }

    public function setValorDesconto(float $valor): void
    {
        $this->valorDesconto = $valor;
    }

    public function getXmlNfe(): ?string
    {
        return $this->xmlNfe;
    }

    public function setXmlNfe(?string $xml): void
    {
        $this->xmlNfe = $xml;
    }

    public function getObservacoes(): ?string
    {
        return $this->observacoes;
    }

    public function setObservacoes(?string $obs): void
    {
        $this->observacoes = $obs;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $user): void
    {
        $this->usuario = $user;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getValorTotal(): float
    {
        return $this->valorTotal;
    }

    public function setValorTotal(float $valor): void
    {
        $this->valorTotal = $valor;
    }

    public function setChaveAcesso(?string $chave): void
    {
        $this->chave_acesso = $chave;
    }

    public function setNumero(?int $numero): void
    {
        $this->numero = $numero;
    }
}

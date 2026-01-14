<?php
namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'cad_participantes')]
#[ORM\Index(columns: ['tenant_id'], name: 'idx_participantes_tenant')]
#[ORM\Index(columns: ['cpf_cnpj'], name: 'idx_participantes_cpf_cnpj')]
#[ORM\Index(columns: ['tipo_cadastro'], name: 'idx_participantes_tipo')]
#[ORM\UniqueConstraint(columns: ['tenant_id', 'cpf_cnpj'], name: 'idx_participantes_tenant_doc')]
class Participante
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(name: 'tenant_id', type: 'string', length: 36)]
    private string $tenantId;

    #[ORM\Column(name: 'cpf_cnpj', type: 'string', length: 14, nullable: true)]
    private ?string $cpfCnpj = null;

    #[ORM\Column(name: 'nome_razao', type: 'string', length: 255)]
    private string $nomeRazao;

    #[ORM\Column(name: 'nome_fantasia', type: 'string', length: 255, nullable: true)]
    private ?string $nomeFantasia = null;

    /**
     * SET no banco → array no PHP
     * Ex: ['CLIENTE', 'FORNECEDOR']
     */
    #[ORM\Column(name: 'tipo_cadastro', type: 'simple_array')]
    private array $tipoCadastro = [];

    /**
     * 1 = Contribuinte
     * 2 = Isento
     * 9 = Não Contribuinte
     */
    #[ORM\Column(name: 'ind_iedest', type: 'integer')]
    private int $indIeDest = 9;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $ie = null;

    #[ORM\Column(name: 'endereco_json', type: 'json', nullable: true)]
    private ?array $enderecoJson = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $telefone = null;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'boolean')]
    private bool $ativo = true;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private \DateTime $updatedAt;

    public function __construct(string $id, string $tenantId)
    {
        $this->id        = $id;
        $this->tenantId  = $tenantId;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    // ========================
    // GETTERS & SETTERS
    // ========================

    public function getId(): string
    {
        return $this->id;
    }

    public function getTenantId(): string
    {
        return $this->tenantId;
    }

    public function getCpfCnpj(): ?string
    {
        return $this->cpfCnpj;
    }

    public function setCpfCnpj(?string $cpfCnpj): void
    {
        $this->cpfCnpj = $cpfCnpj ? preg_replace('/\D/', '', $cpfCnpj) : null;
    }

    public function getNomeRazao(): string
    {
        return $this->nomeRazao;
    }

    public function setNomeRazao(string $nomeRazao): void
    {
        $this->nomeRazao = $nomeRazao;
    }

    public function getNomeFantasia(): ?string
    {
        return $this->nomeFantasia;
    }

    public function setNomeFantasia(?string $nomeFantasia): void
    {
        $this->nomeFantasia = $nomeFantasia;
    }

    public function getTipoCadastro(): array
    {
        return $this->tipoCadastro;
    }

    public function setTipoCadastro(array $tipos): void
    {
        $this->tipoCadastro = array_values(array_unique($tipos));
    }

    public function isCliente(): bool
    {
        return in_array('CLIENTE', $this->tipoCadastro, true);
    }

    public function isFornecedor(): bool
    {
        return in_array('FORNECEDOR', $this->tipoCadastro, true);
    }

    public function isTransportadora(): bool
    {
        return in_array('TRANSPORTADORA', $this->tipoCadastro, true);
    }

    public function getIndIeDest(): int
    {
        return $this->indIeDest;
    }

    public function setIndIeDest(int $indIeDest): void
    {
        $this->indIeDest = $indIeDest;
    }

    public function getIe(): ?string
    {
        return $this->ie;
    }

    public function setIe(?string $ie): void
    {
        $this->ie = $ie;
    }

    public function getEnderecoJson(): ?array
    {
        return $this->enderecoJson;
    }

    public function setEnderecoJson(?array $enderecos): void
    {
        $this->enderecoJson = $enderecos;
    }

    public function getEndereco(string $tipo = 'principal'): ?array
    {
        return $this->enderecoJson[$tipo] ?? null;
    }

    public function getTelefone(): ?string
    {
        return $this->telefone;
    }

    public function setTelefone(?string $telefone): void
    {
        $this->telefone = $telefone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function isAtivo(): bool
    {
        return $this->ativo;
    }

    public function setAtivo(bool $ativo): void
    {
        $this->ativo = $ativo;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
}
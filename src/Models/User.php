<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use App\Repositories\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'sis_usuarios')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Tenant::class, inversedBy: 'usuarios')]
    #[ORM\JoinColumn(name: 'tenant_id', referencedColumnName: 'id', nullable: false)]
    private Tenant $tenant;

    #[ORM\Column(type: 'string', length: 100)]
    private string $nome;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $filiais_permitidas = null;


    #[ORM\ManyToMany(targetEntity: Filial::class)]
    #[ORM\JoinTable(
        name: 'sis_users_filiais',
        joinColumns: [
            new ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'filial_id', referencedColumnName: 'id', nullable: false)
        ]
    )]
    private Collection $filiais;

     #[ORM\PrePersist]
    public function onCreate(): void
    {
        $now = new \DateTime();

        $this->created_at = $now;
        $this->updated_at = $now;
    }

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->updated_at = new \DateTime();
    }

    #[ORM\Column(type: 'string', length: 150, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    private string $senha_hash;

    #[ORM\Column(type: 'boolean')]
    private bool $ativo = true;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $ultimo_acesso = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $created_at;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $updated_at;

     #[ORM\OneToMany(
        mappedBy: 'user',
        targetEntity: UserFilialPermission::class
    )]
    private $userFilialPermissions;

    public function __construct()
    {
        $this->filiais = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getTenant(): Tenant
    {
        return $this->tenant;
    }

    public function getTenantId(): string
    {
        return $this->tenant->getId();
    }
    // getters e setters (exemplo)
    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSenhaHash(): string
    {
        return $this->senha_hash;
    }

    public function isAtivo(): bool
    {
        return $this->ativo;
    }



    public function getFiliais(): Collection
    {
        return $this->filiais;
    }

    public function podeAcessarFilial(Filial $filial): bool
    {
        return $this->filiais->contains($filial);
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setTenant(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setSenhaHash(string $hash): void
    {
        $this->senha_hash = $hash;
    }

    public function setAtivo(bool $ativo): void
    {
        $this->ativo = $ativo;
    }

    public function addFilial(Filial $filial): void
    {
        if (!$this->filiais->contains($filial)) {
            $this->filiais->add($filial);
        }
    }

    public function setCreatedAt(\DateTime $dt): void
    {
        $this->created_at = $dt;
    }

    public function setUpdatedAt(\DateTime $dt): void
    {
        $this->updated_at = $dt;
    }

    public function getUserFilialPermissions()
    {
        return $this->userFilialPermissions;
    }

    private ?string $accessSummary = null;

    public function setAccessSummary(?string $summary): void
    {
        $this->accessSummary = $summary;
    }

    public function getAccessSummary(): ?string
    {
        return $this->accessSummary;
    }

}

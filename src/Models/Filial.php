<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Models\Permission;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'sis_filiais')]
class Filial
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\ManyToOne(targetEntity: Tenant::class)]
    #[ORM\JoinColumn(name: 'tenant_id', referencedColumnName: 'id', nullable: false)]
    private Tenant $tenant;

    #[ORM\Column(type: 'string', length: 10)]
    private string $tipo_unidade; // MATRIZ | FILIAL

    #[ORM\Column(type: 'string', length: 255)]
    private string $razao_social;

    #[ORM\Column(type: 'string', length: 14, unique: true)]
    private string $cnpj;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $ie = null;

    #[ORM\Column(type: 'string', length: 2)]
    private string $uf;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $config_nfe = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $created_at;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $updated_at;

    #[ORM\ManyToMany(targetEntity: Permission::class, inversedBy: 'filiais')]
    #[ORM\JoinTable(
        name: 'sis_filial_permissions',
        joinColumns: [
            new ORM\JoinColumn(name: 'filial_id', referencedColumnName: 'id', nullable: false)
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'permission_id', referencedColumnName: 'id', nullable: false)
        ]
    )]
    private Collection $permissions;

    #[ORM\ManyToMany(targetEntity: Role::class)]
    #[ORM\JoinTable(name: 'sis_user_filial_roles')]
    private Collection $roles;

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

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    // ---------------- GETTERS ----------------

    public function getId(): string
    {
        return $this->id;
    }

    public function getTenant(): Tenant
    {
        return $this->tenant;
    }

    public function getRazaoSocial(): string
    {
        return $this->razao_social;
    }

    public function getCnpj(): string
    {
        return $this->cnpj;
    }

    public function getUf(): string
    {
        return $this->uf;
    }

    public function getConfigNfe(): ?array
    {
        return $this->config_nfe ?? [];
    }

    public function getTipoUnidade(): string
    {
        return $this->tipo_unidade;
    }

    public function getAmbienteNfe(): string
    {
        return $this->config_nfe['ambiente'] ?? 'HOMOLOGACAO';
    }

    public function getUltimoNumeroNfe(): int
    {
        return $this->config_nfe['numeracao']['nfe']['ultimo_numero'] ?? 0;
    }

    public function setConfigNfe(array $config): void
    {
        $this->config_nfe = $config;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setTenant(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function setTipoUnidade(string $tipo): void
    {
        $this->tipo_unidade = $tipo;
    }

    public function setRazaoSocial(string $razao): void
    {
        $this->razao_social = $razao;
    }

    public function setCnpj(string $cnpj): void
    {
        $this->cnpj = $cnpj;
    }

    public function setUf(string $uf): void
    {
        $this->uf = $uf;
    }

    public function setCreatedAt(\DateTime $dt): void
    {
        $this->created_at = $dt;
    }

    public function setUpdatedAt(\DateTime $dt): void
    {
        $this->updated_at = $dt;
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): void
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
        }
    }

    public function removePermission(Permission $permission): void
    {
        $this->permissions->removeElement($permission);
    }

    public function clearPermissions(): void
    {
        $this->permissions->clear();
    }
}

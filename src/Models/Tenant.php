<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'sis_tenants')]
class Tenant
{

    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;



    #[ORM\Column(type: 'string', length: 100)]
    private string $nome_grupo;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status = 'ATIVO';

    #[ORM\Column(type: 'datetime')]
    private \DateTime $data_criacao;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $created_at;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $updated_at;

    #[ORM\OneToMany(mappedBy: 'tenant', targetEntity: Filial::class)]
    private Collection $filiais;

    // 🔁 Relacionamento: um Tenant tem vários usuários
    #[ORM\OneToMany(mappedBy: 'tenant', targetEntity: User::class)]
    private Collection $usuarios;

    public function __construct()
    {
        $this->usuarios = new ArrayCollection();
        $this->filiais = new ArrayCollection();
    }

    // getters essenciais
    public function getId(): string
    {
        return $this->id;
    }

    public function getNomeGrupo(): string
    {
        return $this->nome_grupo;
    }

    public function isAtivo(): bool
    {
        return $this->status === 'ATIVO';
    }

    public function getFiliais(): Collection
    {
        return $this->filiais;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setNomeGrupo(string $nome): void
    {
        $this->nome_grupo = $nome;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setDataCriacao(\DateTime $data_criacao): void{
        $this->data_criacao = $data_criacao;
    }

    public function setCreatedAt(\DateTime $dt): void
    {
        $this->created_at = $dt;
    }

    public function setUpdatedAt(\DateTime $dt): void
    {
        $this->updated_at = $dt;
    }
}

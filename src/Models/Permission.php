<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'sis_permissions')]
class Permission
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $nome;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $descricao = null;

    #[ORM\ManyToMany(targetEntity: Filial::class, mappedBy: 'permissions')]
    private Collection $filiais;

    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'permissions')]
    private Collection $roles;


    public function __construct()
    {
        $this->filiais = new ArrayCollection();
    }

    // ---------------- GETTERS ----------------

    public function getId(): string
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function getFiliais(): Collection
    {
        return $this->filiais;
    }

     public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function setDescricao(?string $descricao): void
    {
        $this->descricao = $descricao;
    }
}

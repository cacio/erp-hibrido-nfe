<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'sis_audit_logs')]
class AuditLog
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\Column(type: 'guid')]
    private string $tenant_id;

    #[ORM\Column(type: 'guid', nullable: true)]
    private ?string $user_id;

    #[ORM\Column(type: 'string', length: 100)]
    private string $action;

    #[ORM\Column(type: 'string', length: 100)]
    private string $entity;

    #[ORM\Column(type: 'guid', nullable: true)]
    private ?string $entity_id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $description;

    #[ORM\Column(type: 'string', length: 45)]
    private string $ip_address;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $created_at;

    // getters / setters simples
    public function getId(): string
    {
        return $this->id;
    }

    public function getTenantId(): string
    {
        return $this->tenant_id;
    }

    public function getUserId(): ?string
    {
        return $this->user_id;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function getEntityId(): ?string
    {
        return $this->entity_id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getIpAddress(): string
    {
        return $this->ip_address;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setTenantId(string $tenant_id): void
    {
        $this->tenant_id = $tenant_id;
    }

    public function setUserId(?string $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function setEntity(string $entity): void
    {
        $this->entity = $entity;
    }

    public function setEntityId(?string $entity_id): void
    {
        $this->entity_id = $entity_id;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
    public function setIpAddress(string $ip_address): void
    {
        $this->ip_address = $ip_address;
    }

    public function setCreatedAt(\DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }


}

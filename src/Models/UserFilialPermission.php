<?php

namespace App\Models;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'sis_user_filial_permissions')]
class UserFilialPermission
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Filial::class)]
    #[ORM\JoinColumn(name: 'filial_id', referencedColumnName: 'id')]
    private Filial $filial;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Permission::class)]
    #[ORM\JoinColumn(name: 'permission_id', referencedColumnName: 'id')]
    private Permission $permission;

    public function getFilial(): Filial
    {
        return $this->filial;
    }

    public function getPermission(): Permission
    {
        return $this->permission;
    }
}

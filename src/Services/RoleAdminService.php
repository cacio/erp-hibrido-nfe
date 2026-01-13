<?php

namespace App\Services;

use App\Core\EntityManagerFactory;
use App\Models\Role;
use App\Models\Permission;
use Ramsey\Uuid\Uuid;

class RoleAdminService
{
    public function all(): array
    {
        return EntityManagerFactory::create()
            ->getRepository(Role::class)
            ->findBy([], ['nome' => 'ASC']);
    }

    public function find(string $id): ?Role
    {
        return EntityManagerFactory::create()->find(Role::class, $id);
    }

    public function permissions(): array
    {
        return EntityManagerFactory::create()
            ->getRepository(Permission::class)
            ->findBy([], ['nome' => 'ASC']);
    }

    public function create(string $nome, array $permissionIds): void
    {
        $em = EntityManagerFactory::create();

        if ($em->getRepository(Role::class)->findOneBy(['nome' => $nome])) {
            throw new \Exception('Role já existe');
        }

        $role = new Role();
        $role->setId(Uuid::uuid4()->toString());
        $role->setNome($nome);

        foreach ($permissionIds as $pid) {
            $permission = $em->find(Permission::class, $pid);
            if ($permission) {
                $role->getPermissions()->add($permission);
            }
        }

        $em->persist($role);
        $em->flush();
    }

    public function update(string $id, string $nome, array $permissionIds): void
    {
        $em = EntityManagerFactory::create();
        $role = $em->find(Role::class, $id);

        if (!$role) {
            throw new \Exception('Role não encontrada');
        }

        $role->setNome($nome);

        // limpa permissões atuais
        $role->getPermissions()->clear();

        foreach ($permissionIds as $pid) {
            $permission = $em->find(Permission::class, $pid);
            if ($permission) {
                $role->getPermissions()->add($permission);
            }
        }

        $em->flush();
    }

    public function delete(string $id): void
    {
        $em = EntityManagerFactory::create();
        $role = $em->find(Role::class, $id);

        if (!$role) {
            throw new \Exception('Role não encontrada');
        }

        if ($role->getNome() === 'ADMIN') {
            throw new \Exception('Role ADMIN não pode ser removida');
        }

        $em->remove($role);
        $em->flush();
    }

    public function rolesDoUsuarioNaFilial(string $userId, string $filialId): array
    {
        $conn = EntityManagerFactory::create()->getConnection();

        $sql = "
        SELECT role_id
        FROM sis_user_filial_roles
        WHERE user_id = :user
          AND filial_id = :filial
    ";

        return $conn->fetchFirstColumn($sql, [
            'user'   => $userId,
            'filial' => $filialId,
        ]);
    }

    public function syncUserRolesInFilial(
        string $userId,
        string $filialId,
        array $roles
    ): void {
        $conn = EntityManagerFactory::create()->getConnection();

        // remove tudo
        $conn->executeStatement(
            'DELETE FROM sis_user_filial_roles WHERE user_id = ? AND filial_id = ?',
            [$userId, $filialId]
        );

        // recria
        foreach ($roles as $roleId) {
            $conn->insert('sis_user_filial_roles', [
                'user_id'   => $userId,
                'filial_id' => $filialId,
                'role_id'   => $roleId,
            ]);
        }


    }
}

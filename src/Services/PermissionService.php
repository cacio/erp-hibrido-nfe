<?php

namespace App\Services;

use App\Core\EntityManagerFactory;
use Doctrine\DBAL\Connection;

class PermissionService
{
    private array $permissions = [];

    public function __construct()
    {
        if (!isset($_SESSION['permissions'])) {
            $this->load();
        }
    //print_r($_SESSION['permissions']);
        $this->permissions = $_SESSION['permissions'] ?? [];
    }

    private function load(): void
    {
        $em = EntityManagerFactory::create();
        /** @var Connection $conn */
        $conn = $em->getConnection();

        $userId   = $_SESSION['auth']['user_id'];
        $filialId = $_SESSION['auth']['filial_id'];

        $permissions = [];

        /**
         * 1️⃣ PERMISSÕES DIRETAS (usuário + filial)
         */
        $sql = "
            SELECT p.nome
            FROM sis_user_filial_permissions ufp
            INNER JOIN sis_permissions p ON p.id = ufp.permission_id
            WHERE ufp.user_id   = :user
              AND ufp.filial_id = :filial
              AND ufp.allowed   = 1
        ";

        $permissions = $conn->fetchFirstColumn($sql, [
            'user'   => $userId,
            'filial' => $filialId,
        ]);

        /**
         * 2️⃣ PERMISSÕES VIA ROLES (usuário + filial → role → permission)
         */
        $sql = "
            SELECT DISTINCT p.nome
            FROM sis_user_filial_roles ufr
            INNER JOIN sis_role_permissions rp ON rp.role_id = ufr.role_id
            INNER JOIN sis_permissions p ON p.id = rp.permission_id
            WHERE ufr.user_id   = :user
              AND ufr.filial_id = :filial
        ";

        $rolePermissions = $conn->fetchFirstColumn($sql, [
            'user'   => $userId,
            'filial' => $filialId,
        ]);

        $permissions = array_merge($permissions, $rolePermissions);

        /**
         * 3️⃣ HERANÇA DA MATRIZ (se não houver permissões na filial)
         */
        if (empty($permissions)) {
            $sql = "
                SELECT DISTINCT p.nome
                FROM sis_user_filial_permissions ufp
                INNER JOIN sis_permissions p ON p.id = ufp.permission_id
                INNER JOIN sis_filiais f ON f.id = ufp.filial_id
                WHERE ufp.user_id = :user
                  AND f.tipo_unidade = 'MATRIZ'
                  AND ufp.allowed = 1
            ";

            $permissions = $conn->fetchFirstColumn($sql, [
                'user' => $userId,
            ]);
        }

        /**
         * 4️⃣ NORMALIZA E CACHEIA
         */
        $_SESSION['permissions'] = array_values(array_unique($permissions));
    }

    public function can(string $permission): bool
    {
       // print_r($this->permissions);
        return in_array($permission, $this->permissions, true);
    }

    public function all(): array
    {
        return $this->permissions;
    }
}

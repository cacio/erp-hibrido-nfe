<?php

namespace App\Services;

use App\Core\EntityManagerFactory;
use Doctrine\DBAL\Connection;

class UserFilialAccessService
{
    public function rolesDoUsuario(string $userId, string $filialId): array
    {
        $conn = EntityManagerFactory::create()->getConnection();

        $sql = "
            SELECT r.id, r.nome
            FROM sis_user_filial_roles ufr
            INNER JOIN sis_roles r ON r.id = ufr.role_id
            WHERE ufr.user_id = :user
              AND ufr.filial_id = :filial
        ";

        return $conn->fetchAllAssociative($sql, [
            'user'   => $userId,
            'filial' => $filialId,
        ]);
    }

    public function resumoPorUsuario(string $userId): array
    {
        $conn = EntityManagerFactory::create()->getConnection();

        $sql = "
        SELECT
            f.id   AS filial_id,
            f.razao_social,
            GROUP_CONCAT(r.nome ORDER BY r.nome SEPARATOR ', ') AS roles
        FROM sis_user_filial_roles ufr
        INNER JOIN sis_filiais f ON f.id = ufr.filial_id
        INNER JOIN sis_roles r   ON r.id = ufr.role_id
        WHERE ufr.user_id = :user
        GROUP BY f.id, f.razao_social
    ";

        return $conn->fetchAllAssociative($sql, [
            'user' => $userId,
        ]);
    }


    public function salvar(string $userId, string $filialId, array $roles): void
    {
        $conn = EntityManagerFactory::create()->getConnection();

        // limpa acesso atual
        $conn->executeStatement(
            'DELETE FROM sis_user_filial_roles WHERE user_id = ? AND filial_id = ?',
            [$userId, $filialId]
        );

        foreach ($roles as $roleId) {
            $conn->insert('sis_user_filial_roles', [
                'user_id'   => $userId,
                'filial_id' => $filialId,
                'role_id'   => $roleId,
            ]);
        }
    }
}

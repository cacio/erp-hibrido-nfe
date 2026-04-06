<?php

namespace App\Services;

use App\Core\EntityManagerFactory;
use Doctrine\DBAL\Connection;

class FilialAccessAdminService
{
    public function usuariosDaFilial(string $filialId): array
    {
        $conn = EntityManagerFactory::create()->getConnection();

        $sql = "
            SELECT
                u.id   AS user_id,
                u.nome AS user_nome,
                GROUP_CONCAT(r.nome ORDER BY r.nome SEPARATOR ', ') AS roles
            FROM sis_users_filiais uf
            INNER JOIN sis_usuarios u ON u.id = uf.user_id
            LEFT JOIN sis_user_filial_roles ufr
                   ON ufr.user_id = u.id AND ufr.filial_id = uf.filial_id
            LEFT JOIN sis_roles r ON r.id = ufr.role_id
            WHERE uf.filial_id = :filial
            GROUP BY u.id, u.nome
            ORDER BY u.nome
        ";

        return $conn->fetchAllAssociative($sql, [
            'filial' => $filialId,
        ]);
    }

    public function usuariosDisponiveis(string $tenantId, string $filialId): array
    {
        $conn = EntityManagerFactory::create()->getConnection();

        $sql = "
            SELECT u.id, u.nome
            FROM sis_usuarios u
            WHERE u.tenant_id = :tenant
              AND u.id NOT IN (
                SELECT user_id FROM sis_users_filiais WHERE filial_id = :filial
              )
            ORDER BY u.nome
        ";

        return $conn->fetchAllAssociative($sql, [
            'tenant' => $tenantId,
            'filial' => $filialId,
        ]);
    }

    public function vincularUsuario(string $userId, string $filialId): void
    {
        $conn = EntityManagerFactory::create()->getConnection();

        $conn->insert('sis_users_filiais', [
            'user_id'   => $userId,
            'filial_id' => $filialId,
        ]);
    }

    public function removerUsuario(string $userId, string $filialId): void
    {
        $conn = EntityManagerFactory::create()->getConnection();

        $conn->executeStatement(
            'DELETE FROM sis_user_filial_roles WHERE user_id = ? AND filial_id = ?',
            [$userId, $filialId]
        );

        $conn->executeStatement(
            'DELETE FROM sis_users_filiais WHERE user_id = ? AND filial_id = ?',
            [$userId, $filialId]
        );
    }
}

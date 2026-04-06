<?php

namespace App\Services;

use App\Core\EntityManagerFactory;
use App\Models\Filial;
use Ramsey\Uuid\Uuid;

class FilialAdminService
{
    public function allByTenant(string $tenantId): array
    {
        return EntityManagerFactory::create()
            ->getRepository(Filial::class)
            ->findBy(
                ['tenant' => $tenantId],
                ['tipo_unidade' => 'ASC', 'razao_social' => 'ASC']
            );
    }

    public function find(string $id): ?Filial
    {
        return EntityManagerFactory::create()->find(Filial::class, $id);
    }

    public function create(array $data): void
    {
        $em = EntityManagerFactory::create();

        $filial = new Filial();
        $filial->setId(Uuid::uuid4()->toString());
        $filial->setTenant(
            $em->getReference(\App\Models\Tenant::class, $_SESSION['auth']['tenant_id'])
        );
        $filial->setTipoUnidade($data['tipo_unidade']);
        $filial->setRazaoSocial($data['razao_social']);
        $filial->setCnpj(preg_replace('/[^\d]/', '', $data['cnpj']));
        $filial->setUf($data['uf']);

        $em->persist($filial);
        $em->flush();
    }

    public function update(string $id, array $data): void
    {
        $em = EntityManagerFactory::create();
        $filial = $em->find(Filial::class, $id);

        if (!$filial) {
            throw new \Exception('Filial não encontrada');
        }

        $filial->setTipoUnidade($data['tipo_unidade']);
        $filial->setRazaoSocial($data['razao_social']);
        $filial->setCnpj(preg_replace('/[^\d]/', '', $data['cnpj']));
        $filial->setUf($data['uf']);

        $em->flush();
    }

    public function delete(string $id): void
    {
        $em = EntityManagerFactory::create();
        $filial = $em->find(Filial::class, $id);

        if (!$filial) {
            throw new \Exception('Filial não encontrada');
        }

        if ($filial->getTipoUnidade() === 'MATRIZ') {
            throw new \Exception('Não é permitido remover a matriz');
        }

        $em->remove($filial);
        $em->flush();
    }

    public function usuariosDaFilial(string $filialId): array
    {
        $em = EntityManagerFactory::create();
        $conn = $em->getConnection();

        $sql = "
        SELECT
            u.id,
            u.nome,
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
    public function usuariosDisponiveis(string $filialId): array
    {
        $em = EntityManagerFactory::create();
        $conn = $em->getConnection();

        $sql = "
        SELECT u.id, u.nome
        FROM sis_usuarios u
        WHERE u.tenant_id = :tenant
          AND u.id NOT IN (
              SELECT user_id
              FROM sis_users_filiais
              WHERE filial_id = :filial
          )
        ORDER BY u.nome
    ";
        //echo trim($_SESSION['auth']['tenant_id']).' [] ',$filialId;
        return $conn->fetchAllAssociative($sql, [
            'tenant' => trim($_SESSION['auth']['tenant_id']),
            'filial' => $filialId,
        ]);
    }
    public function addUser(string $filialId, string $userId): void
    {
        $conn = EntityManagerFactory::create()->getConnection();

        // evita duplicidade
        $exists = $conn->fetchOne(
            'SELECT COUNT(*) FROM sis_users_filiais WHERE user_id = ? AND filial_id = ?',
            [$userId, $filialId]
        );

        if ($exists) {
            return;
        }

        $conn->insert('sis_users_filiais', [
            'user_id'   => $userId,
            'filial_id' => $filialId,
        ]);
    }

    public function removeUser(string $filialId, string $userId): void
    {
        $conn = EntityManagerFactory::create()->getConnection();

        // remove roles primeiro (integridade)
        $conn->executeStatement(
            'DELETE FROM sis_user_filial_roles WHERE user_id = ? AND filial_id = ?',
            [$userId, $filialId]
        );

        // remove vínculo
        $conn->executeStatement(
            'DELETE FROM sis_users_filiais WHERE user_id = ? AND filial_id = ?',
            [$userId, $filialId]
        );
    }

    public function filiaisDisponiveisParaUsuario(string $userId): array
    {
        $conn = EntityManagerFactory::create()->getConnection();

        $sql = "
        SELECT f.id, f.razao_social
        FROM sis_filiais f
        WHERE f.tenant_id = :tenant
          AND f.id NOT IN (
              SELECT filial_id
              FROM sis_users_filiais
              WHERE user_id = :user
          )
        ORDER BY f.razao_social
    ";

        return $conn->fetchAllAssociative($sql, [
            'tenant' => $_SESSION['auth']['tenant_id'],
            'user'   => $userId,
        ]);
    }
}

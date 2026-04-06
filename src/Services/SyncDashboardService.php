<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

class SyncDashboardService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function cards(string $tenantId): array
    {
        $conn = $this->em->getConnection();

        return [
            'pendentes' => (int) $conn->fetchOne(
                "SELECT COUNT(*) FROM sync_fila f
                 JOIN sis_filiais fi ON fi.id = f.filial_id
                 WHERE fi.tenant_id = :tenant AND f.status = 'PENDENTE'",
                ['tenant' => $tenantId]
            ),
            'processando' => (int) $conn->fetchOne(
                "SELECT COUNT(*) FROM sync_fila f
                 JOIN sis_filiais fi ON fi.id = f.filial_id
                 WHERE fi.tenant_id = :tenant AND f.status = 'PROCESSANDO'",
                ['tenant' => $tenantId]
            ),
            'erro' => (int) $conn->fetchOne(
                "SELECT COUNT(*) FROM sync_fila f
                 JOIN sis_filiais fi ON fi.id = f.filial_id
                 WHERE fi.tenant_id = :tenant AND f.status = 'ERRO'",
                ['tenant' => $tenantId]
            ),
            'sucesso_hoje' => (int) $conn->fetchOne(
                "SELECT COUNT(*) FROM sync_fila f
                 JOIN sis_filiais fi ON fi.id = f.filial_id
                 WHERE fi.tenant_id = :tenant
                   AND f.status = 'SUCESSO'
                   AND DATE(f.processed_at) = CURDATE()",
                ['tenant' => $tenantId]
            ),
        ];
    }

    public function listar(array $filtros): array
    {
        $sql = "
            SELECT f.*, fi.razao_social AS filial_nome
            FROM sync_fila f
            JOIN sis_filiais fi ON fi.id = f.filial_id
            WHERE fi.tenant_id = :tenant
        ";

        $params = ['tenant' => $filtros['tenant_id']];

        if (!empty($filtros['status'])) {
            $sql .= " AND f.status = :status";
            $params['status'] = $filtros['status'];
        }

        if (!empty($filtros['tabela'])) {
            $sql .= " AND f.tabela = :tabela";
            $params['tabela'] = $filtros['tabela'];
        }

        if (!empty($filtros['direcao'])) {
            $sql .= " AND f.direcao = :direcao";
            $params['direcao'] = $filtros['direcao'];
        }

        $sql .= " ORDER BY f.created_at DESC LIMIT 100";

        return $this->em->getConnection()->fetchAllAssociative($sql, $params);
    }
}

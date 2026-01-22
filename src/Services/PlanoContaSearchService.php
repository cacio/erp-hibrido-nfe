<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

class PlanoContaSearchService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function search(string $tenantId, string $q, int $limit = 15): array
    {
        $q = '%' . strtolower(trim($q)) . '%';

        $sql = "
            SELECT
                id,
                codigo,
                nome,
                sinal_dre
            FROM fin_plano
            WHERE tenant_id = :tenant
              AND (
                    LOWER(nome) LIKE :q
                 OR codigo LIKE :q
              )
            ORDER BY codigo
            LIMIT :limite
        ";

        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->bindValue('tenant', $tenantId);
        $stmt->bindValue('q', $q);
        $stmt->bindValue('limite', $limit, \PDO::PARAM_INT);

        return $stmt->executeQuery()->fetchAllAssociative();
    }
}

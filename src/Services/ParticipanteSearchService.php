<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

class ParticipanteSearchService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function search(string $tenantId, string $q, int $limit = 10): array
    {
        $q = '%' . strtolower(trim($q)) . '%';

        $sql = "
            SELECT
                id,
                nome_razao,
                nome_fantasia,
                cpf_cnpj,
                tipo_cadastro
            FROM cad_participantes
            WHERE tenant_id = :tenant
              AND ativo = 1
              AND (
                    LOWER(nome_razao) LIKE :q
                 OR LOWER(nome_fantasia) LIKE :q
                 OR cpf_cnpj LIKE :qdoc
              )
            ORDER BY nome_razao
            LIMIT :limite
        ";

        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->bindValue('tenant', $tenantId);
        $stmt->bindValue('q', $q);
        $stmt->bindValue('qdoc', preg_replace('/\D/', '', $q));
        $stmt->bindValue('limite', $limit, \PDO::PARAM_INT);

        return $stmt->executeQuery()->fetchAllAssociative();
    }
}

<?php

namespace App\Controllers\Api;

use App\Core\EntityManagerFactory;
use Doctrine\ORM\EntityManagerInterface;

class SyncPullController
{
    private EntityManagerInterface $em;

    public function __construct()
    {
        $this->em = EntityManagerFactory::create();
    }

    public function pull(): void
    {
        header('Content-Type: application/json');

        $filialId = $_GET['filial_id'] ?? null;
        $limit    = (int) ($_GET['limit'] ?? 50);

        if (!$filialId) {
            http_response_code(422);
            echo json_encode(['error' => 'filial_id é obrigatório']);
            return;
        }

        $conn = $this->em->getConnection();

        // 🔒 Lock simples: marca como PROCESSANDO
        $sqlSelect = "
            SELECT *
            FROM sync_fila
            WHERE filial_id = :filial
              AND direcao = 'WEB_TO_DESK'
              AND status = 'PENDENTE'
            ORDER BY created_at ASC
            LIMIT {$limit}
        ";

        $registros = $conn->fetchAllAssociative($sqlSelect, [
            'filial' => $filialId,
        ]);

        if (!$registros) {
            echo json_encode([]);
            return;
        }

        // marca como PROCESSANDO
        $ids = array_column($registros, 'id');

        $conn->executeStatement(
            "UPDATE sync_fila
             SET status = 'PROCESSANDO'
             WHERE id IN (?)",
            [$ids],
            [\Doctrine\DBAL\Connection::PARAM_STR_ARRAY]
        );

        // resposta enxuta
        $payload = array_map(fn ($r) => [
            'fila_id'    => $r['id'],
            'tabela'     => $r['tabela'],
            'operacao'   => $r['operacao'],
            'id_web'     => $r['id_web'],
            'id_desk'    => $r['id_desk'],
            'payload'    => json_decode($r['payload'], true),
            'created_at'=> $r['created_at'],
        ], $registros);

        echo json_encode($payload);
    }
}

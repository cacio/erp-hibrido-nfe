<?php

namespace App\Controllers\Api;

use App\Core\EntityManagerFactory;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class SyncAckController
{
    private EntityManagerInterface $em;

    public function __construct()
    {
        $this->em = EntityManagerFactory::create();
    }

    /**
     * 🔎 Consulta status
     * GET /api/sync/status?id=UUID
     */
    public function status(): void
    {
        header('Content-Type: application/json');

        $filaId = $_GET['id'] ?? null;

        if (!$filaId) {
            http_response_code(422);
            echo json_encode(['error' => 'Parâmetro id é obrigatório']);
            return;
        }

        $registro = $this->em->getConnection()->fetchAssociative(
            "SELECT status, erro_log, processed_at
             FROM sync_fila
             WHERE id = :id
             LIMIT 1",
            ['id' => $filaId]
        );

        if (!$registro) {
            http_response_code(404);
            echo json_encode(['error' => 'Evento não encontrado']);
            return;
        }

        echo json_encode([
            'status'        => $registro['status'],
            'processed_at'  => $registro['processed_at'],
            'erro'          => $registro['status'] === 'ERRO'
                                ? $registro['erro_log']
                                : null
        ]);
    }

    /**
     * ✅ ACK de processamento
     * POST /api/sync/ack
     */
    public function ack(): void
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || empty($data['fila_id']) || empty($data['status'])) {
            http_response_code(422);
            echo json_encode(['error' => 'Payload inválido']);
            return;
        }

        $conn = $this->em->getConnection();

        $fila = $conn->fetchAssociative(
            'SELECT * FROM sync_fila WHERE id = :id',
            ['id' => $data['fila_id']]
        );

        if (!$fila) {
            http_response_code(404);
            echo json_encode(['error' => 'Evento não encontrado']);
            return;
        }

        // ===== SUCESSO =====
        if ($data['status'] === 'SUCESSO') {

            if (empty($data['id_desk'])) {
                http_response_code(422);
                echo json_encode(['error' => 'id_desk é obrigatório para SUCESSO']);
                return;
            }

            $conn->update(
                'sync_fila',
                [
                    'status'       => 'SUCESSO',
                    'id_desk'      => (string) $data['id_desk'],
                    'erro_log'     => null,
                    'processed_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                ],
                ['id' => $fila['id']]
            );

            // cria de-para WEB → DESK
            $conn->insert('sync_map', [
                'id'        => Uuid::uuid4()->toString(),
                'filial_id' => $fila['filial_id'],
                'tabela'    => $fila['tabela'],
                'id_web'    => $fila['id_web'],
                'id_desk'   => (string) $data['id_desk'],
                'created_at'=> (new \DateTime())->format('Y-m-d H:i:s'),
            ]);

            echo json_encode(['status' => 'ACK_OK']);
            return;
        }

        // ===== ERRO =====
        if ($data['status'] === 'ERRO') {

            $conn->update(
                'sync_fila',
                [
                    'status'       => 'ERRO',
                    'erro_log'     => $data['erro'] ?? 'Erro não informado',
                    'processed_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                ],
                ['id' => $fila['id']]
            );

            echo json_encode(['status' => 'ACK_ERRO']);
            return;
        }

        http_response_code(400);
        echo json_encode(['error' => 'Status inválido']);
    }
}

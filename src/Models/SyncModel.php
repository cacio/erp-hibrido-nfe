<?php

namespace App\Models;

use PDO;
use Exception;

class SyncModel
{
    private $db;

    public function __construct()
    {
        // Conexão básica com PDO (ajuste para usar sua classe de conexão real)
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $this->db = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    /**
     * Insere uma nova tarefa na fila de sincronização (sync_fila).
     * @param array $data Dados da tarefa (filial_id, tabela, id_desk, operacao, payload).
     */
    public function enqueueTask(array $data): void
    {
        // Gera um UUID para a PK da fila
        $id = $this->generateUuid();

        $sql = "INSERT INTO sync_fila (id, filial_id, tabela, id_desk, operacao, payload, status)
                VALUES (:id, :filial_id, :tabela, :id_desk, :operacao, :payload, 'PENDENTE')";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'filial_id' => $data['filial_id'],
            'tabela' => $data['tabela'],
            'id_desk' => $data['id_desk'],
            'operacao' => $data['operacao'],
            'payload' => json_encode($data['payload'] ?? []), // O payload completo
        ]);
    }

    /**
     * Simples gerador de UUID v4 (substitua por uma biblioteca mais robusta se desejar).
     */
    private function generateUuid(): string
    {
        // Exemplo simplificado de UUID v4
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
    public function getPendingTasks(int $limit = 50): array
    {
        $sql = "SELECT * FROM sync_fila WHERE status = 'PENDENTE' ORDER BY created_at ASC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tenta encontrar o UUID (Web) correspondente a um ID do Legado (Desktop).
     */
    public function getWebId(string $filialId, string $tableName, string $idDesk): ?string
    {
        $sql = "SELECT id_web FROM sync_map WHERE filial_id = :filial_id AND tabela = :tabela AND id_desk = :id_desk";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'filial_id' => $filialId,
            'tabela' => $tableName,
            'id_desk' => $idDesk
        ]);
        $result = $stmt->fetch();
        return $result ? $result['id_web'] : null;
    }

    /**
     * Cria um novo mapeamento entre ID Web e ID Desktop.
     */
    public function createMapping(string $filialId, string $tableName, string $idWeb, string $idDesk): void
    {
        $sql = "INSERT INTO sync_map (id, filial_id, tabela, id_web, id_desk) VALUES (:id, :filial_id, :tabela, :id_web, :id_desk)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $this->generateUuid(),
            'filial_id' => $filialId,
            'tabela' => $tableName,
            'id_web' => $idWeb,
            'id_desk' => $idDesk
        ]);
    }

    /**
     * Atualiza o status de uma tarefa na fila.
     */
    public function updateTaskStatus(string $id, string $status, ?string $errorMsg = null): void
    {
        $sql = "UPDATE sync_fila SET status = :status, erro_log = :erro_log, processed_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'status' => $status,
            'erro_log' => $errorMsg
        ]);
    }

    /**
     * Executa o UPSERT (Update ou Insert) dinâmico na tabela de destino.
     */
    public function upsertData(string $tableName, array $data): void
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":$col", $columns);
        $updates = array_map(fn($col) => "$col = VALUES($col)", $columns);

        $sql = "INSERT INTO $tableName (" . implode(', ', $columns) . ")
                VALUES (" . implode(', ', $placeholders) . ")
                ON DUPLICATE KEY UPDATE " . implode(', ', $updates);

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
    }
}

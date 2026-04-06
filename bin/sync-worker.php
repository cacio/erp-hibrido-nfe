<?php

// bin/sync-worker.php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

use App\Models\SyncModel;

$syncModel = new SyncModel();

echo "[".date('Y-m-d H:i:s')."] Iniciando processamento da fila...\n";

$tasks = $syncModel->getPendingTasks(100);

if (empty($tasks)) {
    echo "Nenhuma tarefa pendente.\n";
    exit;
}

foreach ($tasks as $task) {
    try {
        $payload = json_decode($task['payload'], true);
        $tableName = $task['tabela'];
        $idDesk = $task['id_desk'];
        $filialId = $task['filial_id'];

        // 1. Verifica se já existe mapeamento para este ID do legado
        $idWeb = $syncModel->getWebId($filialId, $tableName, $idDesk);

        if (!$idWeb) {
            // Se não existe, é um novo registro. Geramos um UUID.
            $idWeb = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));

            // Cria o mapeamento para futuras atualizações
            $syncModel->createMapping($filialId, $tableName, $idWeb, $idDesk);
        }

        // 2. Prepara os dados para o UPSERT na tabela real
        $dataToSave = array_merge($payload, ['id' => $idWeb]);

        // 3. Salva na tabela de destino (ex: cad_produtos)
        $syncModel->upsertData($tableName, $dataToSave);

        // 4. Marca como sucesso
        $syncModel->updateTaskStatus($task['id'], 'SUCESSO');
        echo "Tarefa {$task['id']} ({$tableName}) processada com sucesso.\n";

    } catch (Exception $e) {
        // 5. Em caso de erro, loga e marca na fila para análise
        $syncModel->updateTaskStatus($task['id'], 'ERRO', $e->getMessage());
        echo "ERRO na tarefa {$task['id']}: " . $e->getMessage() . "\n";
    }
}

echo "[".date('Y-m-d H:i:s')."] Processamento finalizado.\n";

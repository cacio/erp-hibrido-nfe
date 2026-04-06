<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\SyncModel;

class SyncController extends Controller
{
    /**
     * Recebe o payload JSON do sistema legado (Webhook).
     * Sua única função é enfileirar a tarefa.
     */
    public function receive()
    {
        // 1. Garante que a requisição é JSON
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if ($contentType !== "application/json") {
            http_response_code(415 ); // Unsupported Media Type
            echo json_encode(['error' => 'Content-Type deve ser application/json']);
            exit;
        }

        // 2. Lê o corpo da requisição
        $payload = file_get_contents("php://input");
        $data = json_decode($payload, true);

        // 3. Validação básica do payload (ajuste conforme a necessidade)
        if (!isset($data['filial_id']) || !isset($data['tabela']) || !isset($data['id_desk']) || !isset($data['operacao'])) {
            http_response_code(400 ); // Bad Request
            echo json_encode(['error' => 'Payload inválido. Campos obrigatórios ausentes.']);
            exit;
        }

        // 4. Enfileira a tarefa
        $syncModel = new SyncModel();
        $syncModel->enqueueTask($data);

        // 5. Resposta de sucesso (muito importante para o legado saber que a tarefa foi recebida)
        http_response_code(202 ); // Accepted
        echo json_encode(['status' => 'accepted', 'message' => 'Tarefa enfileirada com sucesso.']);
    }
}

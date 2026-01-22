<?php

namespace App\Controllers\Api;

use App\Core\EntityManagerFactory;
use App\Services\ParticipanteSearchService;

class ParticipanteSearchController
{
    public function search(): void
    {
        header('Content-Type: application/json');

        $q = $_GET['q'] ?? '';

        if (strlen($q) < 2) {
            echo json_encode([]);
            return;
        }

        $em = EntityManagerFactory::create();

        $service = new ParticipanteSearchService($em);

        $result = $service->search(
            $_SESSION['auth']['tenant_id'],
            $q
        );

        $payload = array_map(function ($row) {
            return [
                'id'        => $row['id'],
                'label'     => $row['nome_razao'],
                'fantasia'  => $row['nome_fantasia'],
                'documento' => $row['cpf_cnpj'],
                'tipo'      => explode(',', $row['tipo_cadastro']),
            ];
        }, $result);

        echo json_encode($payload);
    }
}

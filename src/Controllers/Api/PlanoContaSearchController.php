<?php

namespace App\Controllers\Api;

use App\Core\EntityManagerFactory;
use App\Services\PlanoContaSearchService;

class PlanoContaSearchController
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
        $service = new PlanoContaSearchService($em);

        $result = $service->search(
            $_SESSION['auth']['tenant_id'],
            $q
        );

        $payload = array_map(function ($row) {
            return [
                'id'    => $row['id'],
                'label' => "{$row['codigo']} - {$row['nome']}",
                'codigo'=> $row['codigo'],
                'nome'  => $row['nome'],
                'sinal' => $row['sinal_dre'],
            ];
        }, $result);

        echo json_encode($payload);
    }
}

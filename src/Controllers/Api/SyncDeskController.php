<?php

namespace App\Controllers\Api;

use App\Core\EntityManagerFactory;
use App\Services\SyncFilaService;
use Doctrine\ORM\EntityManagerInterface;

class SyncDeskController
{
    private EntityManagerInterface $em;
    private SyncFilaService $fila;
    public function __construct()
    {

        $em = EntityManagerFactory::create();
        $this->em = $em;
        $this->fila = new SyncFilaService($em);
    }

    public function receive(): void
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON inválido']);
            return;
        }

        $filialExiste = $this->em->getConnection()->fetchOne(
            "SELECT id FROM sis_filiais WHERE id = :id",
            ['id' => $data['filial_id']]
        );

        if (!$filialExiste) {
            http_response_code(422);
            echo json_encode([
                'error' => 'Filial inválida ou não cadastrada'
            ]);
            return;
        }

        // validações mínimas
        foreach (['filial_id', 'tabela', 'operacao', 'id_desk', 'payload'] as $campo) {
            if (empty($data[$campo])) {
                http_response_code(422);
                echo json_encode(['error' => "Campo obrigatório: {$campo}"]);
                return;
            }
        }

        // cria fila
        $idFila = $this->fila->criar(
            $data['filial_id'],
            $data['tabela'],
            null,                       // id_web desconhecido no DESK→WEB
            (string) $data['id_desk'],
            $data['operacao'],
            'DESK_TO_WEB',
            $data['payload']
        );

        echo json_encode([
            'status'  => 'OK',
            'fila_id' => $idFila
        ]);
    }
}

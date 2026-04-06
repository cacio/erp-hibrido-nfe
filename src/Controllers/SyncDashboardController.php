<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\EntityManagerFactory;
use App\Services\SyncDashboardService;
use Doctrine\ORM\EntityManagerInterface;

class SyncDashboardController extends Controller
{
    private EntityManagerInterface $em;
    public function __construct()
    {
        parent::__construct();
        $em = EntityManagerFactory::create();
        $this->em = $em;
    }
    public function index(): void
    {
        $service = new SyncDashboardService($this->em);

        $cards = $service->cards($_SESSION['auth']['tenant_id']);

        $lista = $service->listar([
            'tenant_id' => $_SESSION['auth']['tenant_id'],
            'status'    => $_GET['status'] ?? null,
            'tabela'    => $_GET['tabela'] ?? null,
            'direcao'   => $_GET['direcao'] ?? null,
        ]);

        $this->render('sync/dashboard', compact('cards', 'lista'));
    }

    public function reprocessar(): void
    {
        $filaId = $_POST['id'] ?? null;

        if (!$filaId) {
            $_SESSION['flash_error'] = 'ID da fila não informado.';
            $this->redirect('/sync');
            return;
        }

        try {
            $service = new \App\Services\SyncFilaService($this->em);
            $service->reprocessar($filaId);

            $_SESSION['flash_success'] = 'Registro enviado novamente para sincronização.';
        } catch (\DomainException $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        $this->redirect('/sync');
    }
    public function payload(): void
    {
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? null;

        if (!$id) {
            http_response_code(422);
            echo json_encode(['error' => 'ID não informado']);
            return;
        }

        $registro = $this->em->getConnection()->fetchAssociative(
            'SELECT tabela, payload, erro_log FROM sync_fila WHERE id = :id',
            ['id' => $id]
        );

        if (!$registro) {
            http_response_code(404);
            echo json_encode(['error' => 'Registro não encontrado']);
            return;
        }

        echo json_encode([
            'tabela'  => $registro['tabela'],
            'payload' => $registro['payload']
                ? json_decode($registro['payload'], true)
                : null,
            'erro'    => $registro['erro_log']
        ]);
    }
}

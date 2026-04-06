<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\EntityManagerFactory;
use App\Services\FiscalRuleService;
use App\Services\TaxService;
use App\Models\Produto;
use App\Models\Participante;
use App\Models\Filial;
use Doctrine\ORM\EntityManagerInterface;

class FiscalPreviewController extends Controller
{
    private EntityManagerInterface $em;
    private FiscalRuleService $fiscalRuleService;
    private TaxService $taxService;
    public function __construct()
    {
        parent::__construct();
        $em = EntityManagerFactory::create();
        $this->em = $em;
        $this->fiscalRuleService = new FiscalRuleService($em);
        $this->taxService = new TaxService($em);

    }
    public function preview(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $tenantId = $_SESSION['auth']['tenant_id'];
        $produto = $this->em->find(Produto::class, $data['produto_id']);
        $participante = $this->em->find(Participante::class, $data['participante_id']);
        $filial = $this->em->find(Filial::class,  $_SESSION['auth']['filial_id']);

        if (!$produto || !$participante || !$filial) {
            $this->json(['error' => 'Dados inválidos'], 422);
            return;
        }

        $ufDestino = $participante->getUfPrincipal();

        $regra = $this->fiscalRuleService->resolverRegra(
            $tenantId,
            $filial->getUf(),
            $ufDestino,
            $produto->getNcm()
        );

            $mostra = $tenantId.' - '.
            $filial->getUf().' - '.
            $ufDestino.' - '.
            $produto->getNcm();
        if (!$regra) {
            $this->json(['error' => 'Regra fiscal não encontrada '.$mostra.' '], 422);
            return;
        }

        $impostos = $this->taxService->calcularPreview(
            $produto,
            $data['quantidade'],
            $data['valor_unitario'],
            $regra
        );

        $this->json([
            'cfop' => $regra->getCfopPadrao(),
            'impostos' => $impostos
        ]);
    }

    private function json(array $data, int $status = 200): void
    {
        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($data);
    }
}

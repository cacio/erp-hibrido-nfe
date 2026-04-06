<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\EntityManagerFactory;
use App\Services\FiscalRuleService;
use App\Services\TaxService;
use App\Models\VenCabecalho;
use App\Models\VenItem;
use Doctrine\ORM\EntityManagerInterface;

class VendaItemFiscalController extends Controller
{
    protected FiscalRuleService $fiscalRuleService;
    protected TaxService $taxService;
    private EntityManagerInterface $em;

    public function __construct(
        FiscalRuleService $fiscalRuleService,
        TaxService $taxService
    ) {
        $this->fiscalRuleService = $fiscalRuleService;
        $this->taxService = $taxService;
        $em = EntityManagerFactory::create();
        $this->em = $em;

    }

    public function calcular(
        string $vendaId,
        string $itemId
    ): void {

        $em = $this->em;

        $venda = $em->find(VenCabecalho::class, $vendaId);
        $item  = $em->find(VenItem::class, $itemId);
        $tenantId = $_SESSION['auth']['tenant_id'];
        if (!$venda || !$item) {
            $this->json(['error' => 'Venda ou item não encontrado'], 404);
            return;
        }

        if ($venda->getStatus() !== 0) {
            $this->json(['error' => 'Nota não pode ser recalculada'], 400);
            return;
        }

        $regra = $this->fiscalRuleService->resolverRegra(
            $tenantId,
            $venda->getFilial()->getUf(),
            $venda->getParticipante()->getUf(),
            $item->getProduto()->getNcm()
        );

        if (!$regra) {
            $this->json(['error' => 'Nenhuma regra fiscal encontrada'], 422);
            return;
        }

        $impostos = $this->taxService->calcularItem($item, $venda, $regra);

        $item->setImpostosJson($impostos);
        $em->flush();

        $this->json([
            'success' => true,
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

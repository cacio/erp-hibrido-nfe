<?php

namespace App\Services;

use App\Models\FisRegra as ModelsFisRegra;
use App\Models\Produto;
use App\Models\VenCabecalho as ModelsVenCabecalho;
use App\Models\VenItem as ModelsVenItem;

class TaxService
{
    public function calcularItem(
        ModelsVenItem $item,
        ModelsVenCabecalho $venda,
        ModelsFisRegra $regra
    ): array {

        $valorProduto = $item->getQuantidade() * $item->getValorUnitario();
        $valorLiquido = $valorProduto - $item->getValorDesconto();

        $baseIcms = $valorLiquido;
        if ($regra->getRedBcIcms() > 0) {
            $baseIcms *= (1 - $regra->getRedBcIcms() / 100);
        }

        $valorIcms = round($baseIcms * ($regra->getAliqIcms() / 100), 2);
        $valorPis  = round($valorLiquido * ($regra->getAliqPis() / 100), 2);
        $valorCof  = round($valorLiquido * ($regra->getAliqCofins() / 100), 2);

        return [
            'cfop' => $regra->getCfopPadrao(),
            'icms' => [
                'cst' => $regra->getCstIcms(),
                'aliquota' => $regra->getAliqIcms(),
                'base' => round($baseIcms, 2),
                'valor' => $valorIcms,
                'reducao_bc' => $regra->getRedBcIcms()
            ],
            'pis' => [
                'cst' => $regra->getCstPisCofins(),
                'aliquota' => $regra->getAliqPis(),
                'valor' => $valorPis
            ],
            'cofins' => [
                'cst' => $regra->getCstPisCofins(),
                'aliquota' => $regra->getAliqCofins(),
                'valor' => $valorCof
            ],
            'ibs_cbs' => [
                'cst' => $regra->getCstIbsCbs(),
                'aliq_ibs' => $regra->getAliqIbs(),
                'aliq_cbs' => $regra->getAliqCbs()
            ],
            'snapshot' => [
                'regra_id' => $regra->getId(),
                'ncm' => $item->getProduto()->getNcm(),
                'uf_origem' => $venda->getFilial()->getUf(),
                'uf_destino' => $venda->getParticipante()->getUfPrincipal(),
                'calculado_em' => (new \DateTime())->format('c')
            ]
        ];
    }

    public function calcularPreview(
        Produto $produto,
        float $qtd,
        float $valorUnit,
        ModelsFisRegra $regra
    ): array {
        $base = $qtd * $valorUnit;

        return [
            'base_calculo' => $base,
            'icms' => $base * ($regra->getAliqIcms() / 100),
            'pis' => $base * ($regra->getAliqPis() / 100),
            'cofins' => $base * ($regra->getAliqCofins() / 100)
        ];
    }


}

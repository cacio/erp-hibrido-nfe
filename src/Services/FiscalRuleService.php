<?php

namespace App\Services;

use App\Models\FisRegra;
use App\Models\VenCabecalho;
use App\Models\VenItem;
use Doctrine\ORM\EntityManager;

class FiscalRuleService
{
    public function __construct(private EntityManager $em) {}

    public function resolverRegra(
        string $tenantId,
        string $ufOrigem,
        string $ufDestino,
        ?string $ncm
    ): ?FisRegra {

        $qb = $this->em->createQueryBuilder();

        $qb->select('r')
            ->from(FisRegra::class, 'r')
            ->where('r.tenantId = :tenant')
            ->andWhere('r.ufOrigem = :ufOrigem')
            ->andWhere('(r.ufDestino = :ufDestino OR r.ufDestino = :td)')
            ->andWhere('r.ativo = 1')
            ->setParameters([
                'tenant'   => $tenantId,
                'ufOrigem' => $ufOrigem,
                'ufDestino' => $ufDestino,
                'td'       => 'TD'
            ])
            ->orderBy('LENGTH(r.ncmPrefixo)', 'DESC')
            ->addOrderBy('r.ufDestino', 'DESC')
            ->setMaxResults(1);

        if ($ncm) {
            $qb->andWhere('r.ncmPrefixo IS NULL OR :ncm LIKE CONCAT(r.ncmPrefixo, \'%\')')
                ->setParameter('ncm', $ncm);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }


}

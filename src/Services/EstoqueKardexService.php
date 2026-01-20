<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use App\Models\EstoqueMovimento;

class EstoqueKardexService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function listar(
        string $filialId,
        array $filtros = []
    ): array {
        $qb = $this->em->createQueryBuilder()
            ->select('m')
            ->from(EstoqueMovimento::class, 'm')
            ->where('m.filialId = :filial')
            ->setParameter('filial', $filialId);

        if (!empty($filtros['produto_id'])) {
            $qb->andWhere('m.produtoId = :produto')
               ->setParameter('produto', $filtros['produto_id']);
        }

        if (!empty($filtros['tipo'])) {
            $qb->andWhere('m.tipo = :tipo')
               ->setParameter('tipo', $filtros['tipo']);
        }

        if (!empty($filtros['origem'])) {
            $qb->andWhere('m.origem = :origem')
               ->setParameter('origem', $filtros['origem']);
        }

        if (!empty($filtros['data_ini'])) {
            $qb->andWhere('m.dataMov >= :ini')
               ->setParameter('ini', $filtros['data_ini'] . ' 00:00:00');
        }

        if (!empty($filtros['data_fim'])) {
            $qb->andWhere('m.dataMov <= :fim')
               ->setParameter('fim', $filtros['data_fim'] . ' 23:59:59');
        }

        return $qb
            ->orderBy('m.dataMov', 'asc')
            ->getQuery()
            ->getResult();
    }
}

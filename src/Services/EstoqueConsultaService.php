<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use App\Models\EstoqueSaldo;

class EstoqueConsultaService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function listarSaldoPorFilial(
        string $filialId
    ): array {
       return $this->em->createQueryBuilder()
        ->select('s')
        ->from(EstoqueSaldo::class, 's')
        ->where('s.filialId = :filial')
        ->setParameter('filial', $filialId)
        ->getQuery()
        ->getResult();
    }
}

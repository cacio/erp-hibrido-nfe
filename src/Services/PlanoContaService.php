<?php

namespace App\Services;
use App\Models\FinPlano as PlanoConta;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PlanoContaService
{

     public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function listar(int $pagina = 1, int $itensPorPagina = 10): array
    {
        $query = $this->em->createQueryBuilder()
            ->select('pc')
            ->from(PlanoConta::class, 'pc')
            ->setFirstResult(($pagina - 1) * $itensPorPagina)
            ->setMaxResults($itensPorPagina)
            ->getQuery();

        $paginator = new Paginator($query);
        $totalItens = count($paginator);
        $totalPaginas = ceil($totalItens / $itensPorPagina);

        return [
            'data' => iterator_to_array($paginator),
            'total_itens' => $totalItens,
            'total_paginas' => $totalPaginas,
            'pagina_atual' => $pagina,
        ];
    }

    public function listarAnaliticos(): array
    {
        return $this->em->getRepository(PlanoConta::class)->findBy(['tipo' => 'A']);
    }


}

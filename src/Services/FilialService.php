<?php

namespace App\Services;

use App\Core\EntityManagerFactory;
use App\Models\Filial;

class FilialService
{
    public function find(string $id): ?Filial
    {
        return EntityManagerFactory::create()
            ->find(Filial::class, $id);
    }

    public function allByTenant(string $tenantId): array
    {
        return EntityManagerFactory::create()
            ->getRepository(Filial::class)
            ->findBy(
                ['tenant' => $tenantId],
                ['razao_social' => 'ASC']
            );
    }
}

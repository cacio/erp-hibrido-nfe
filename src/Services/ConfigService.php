<?php

namespace App\Services;

use App\Models\Filial;
use Doctrine\ORM\EntityManagerInterface;

class ConfigService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function getFilialAtual(): ?Filial
    {
        $filialId = $_SESSION['auth']['filial_id'] ?? null;

        if (!$filialId) return null;

        return $this->em->find(Filial::class, $filialId);
    }

    public function permitirDocumentoDuplicado(): bool
    {
        $filial = $this->getFilialAtual();

        if (!$filial) return false;

        $config = $filial->getConfigNfe();

        return (bool) ($config['participante']['permitir_documento_duplicado'] ?? false);
    }
}

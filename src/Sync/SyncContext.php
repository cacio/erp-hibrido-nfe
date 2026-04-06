<?php

namespace App\Sync;

class SyncContext
{
    public function __construct(
        public string $filaId,
        public string $filialId,
        public string $tabela,
        public ?string $idWeb,
        public string $idDesk,
        public string $operacao,
        public string $direcao
    ) {}
}

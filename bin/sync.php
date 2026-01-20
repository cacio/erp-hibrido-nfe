<?php

$em = require __DIR__ . '/../bootstrap/cli.php';

use App\Services\SyncFilaService;
use App\Services\SyncMapService;
use App\Services\SyncProcessor;
use App\Sync\ParticipanteSyncHandler;
use App\Sync\ProdutoSyncHandler;
use App\Sync\EstoqueSyncHandler;
// serviços
$fila = new SyncFilaService($em);
$map  = new SyncMapService($em);

$processor = new SyncProcessor($em, $fila, $map);

// handlers
$processor->registrarHandler(
    'cad_produtos',
    new ProdutoSyncHandler($em, $map)
);

$processor->registrarHandler(
    'cad_participantes',
    new ParticipanteSyncHandler($em, $map)
);



$processor->registrarHandler(
    'est_movimento',
    new EstoqueSyncHandler($em, $map)
);


// processa fila DESK → WEB
$processor->processar('DESK_TO_WEB', 50);

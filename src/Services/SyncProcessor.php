<?php

namespace App\Services;

use App\Sync\SyncContext;
use App\Sync\SyncHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class SyncProcessor
{
    private array $handlers = [];

    public function __construct(
        private EntityManagerInterface $em,
        private SyncFilaService $fila,
        private SyncMapService $map
    ) {}

    /**
     * Registra handlers por tabela
     */
    public function registrarHandler(string $tabela, SyncHandlerInterface $handler): void
    {
        $this->handlers[$tabela] = $handler;
    }

    /**
     * Processa eventos pendentes
     */
    public function processar(
        string $direcao,
        int $limite = 20
    ): void {
        $eventos = $this->fila->buscarPendentes($direcao, $limite);

        foreach ($eventos as $evento) {
            $this->processarEvento($evento);
        }
    }

    private function processarEvento(array $evento): void
    {
        $idFila = $evento['id'];

        try {
            $this->fila->marcarProcessando($idFila);

            if (!isset($this->handlers[$evento['tabela']])) {
                throw new \RuntimeException(
                    "Handler não registrado para tabela {$evento['tabela']}"
                );
            }

            $ctx = new SyncContext(
                filaId:   $evento['id'],
                filialId: $evento['filial_id'],
                tabela:   $evento['tabela'],
                idWeb:    $evento['id_web'],
                idDesk:   $evento['id_desk'],
                operacao: $evento['operacao'],
                direcao:  $evento['direcao']
            );

            $payload = $evento['payload']
                ? json_decode($evento['payload'], true)
                : [];

            $handler = $this->handlers[$evento['tabela']];

            if ($evento['operacao'] === 'DELETE') {
                $handler->delete($payload, $ctx);
            } else {
                $handler->upsert($payload, $ctx);
            }

            $this->fila->marcarSucesso($idFila);

        } catch (\Throwable $e) {
            $this->fila->marcarErro(
                $idFila,
                $e->getMessage()
            );
        }
    }
}

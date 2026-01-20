<?php

namespace App\Sync;

use App\Models\Produto;
use App\Services\SyncMapService;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class ProdutoSyncHandler implements SyncHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private SyncMapService $map
    ) {}

    public function upsert(array $payload, SyncContext $ctx): void
    {
        // resolve ID Web
        $idWeb = $this->map->buscarPorDesk(
            $ctx->filialId,
            'cad_produtos',
            $ctx->idDesk
        );

        if ($idWeb) {
            $produto = $this->em->find(Produto::class, $idWeb);
        } else {
            $produto = new Produto(
                Uuid::uuid4()->toString(),
                $payload['tenant_id']
            );
            $this->em->persist($produto);
        }

        // mapeia dados
        $produto->setDescricao($payload['descricao']);
        $produto->setCodigoSku($payload['codigo_sku'] ?? null);
        $produto->setNcm($payload['ncm'] ?? null);
        $produto->setUnidade($payload['unidade'] ?? 'UN');
        $produto->setAtivo((bool) ($payload['ativo'] ?? true));

        $this->em->flush();

        // salva de-para
        $this->map->salvar(
            $ctx->filialId,
            'cad_produtos',
            $produto->getId(),
            $ctx->idDesk
        );
    }

    public function delete(array $payload, SyncContext $ctx): void
    {
        $idWeb = $this->map->buscarPorDesk(
            $ctx->filialId,
            'cad_produtos',
            $ctx->idDesk
        );

        if (!$idWeb) {
            return;
        }

        $produto = $this->em->find(Produto::class, $idWeb);
        if ($produto) {
            $produto->setAtivo(false); // delete lógico
            $this->em->flush();
        }
    }
}

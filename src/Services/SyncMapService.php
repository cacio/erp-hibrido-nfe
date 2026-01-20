<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class SyncMapService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    /**
     * Salva ou atualiza o mapeamento WEB <-> DESK
     */
    public function salvar(
        string $filialId,
        string $tabela,
        string $idWeb,
        string $idDesk
    ): void {
        // tenta buscar existente
        $sqlBusca = "
            SELECT id
            FROM sync_map
            WHERE filial_id = :filial
              AND tabela = :tabela
              AND id_desk = :id_desk
            LIMIT 1
        ";

        $existente = $this->em->getConnection()->fetchOne($sqlBusca, [
            'filial'  => $filialId,
            'tabela'  => $tabela,
            'id_desk' => $idDesk,
        ]);

        if ($existente) {
            // atualiza id_web se necessário
            $this->em->getConnection()->executeStatement(
                "UPDATE sync_map SET id_web = :id_web WHERE id = :id",
                [
                    'id'     => $existente,
                    'id_web' => $idWeb,
                ]
            );
            return;
        }

        // cria novo
        $this->em->getConnection()->executeStatement(
            "INSERT INTO sync_map (
                id, filial_id, tabela, id_web, id_desk
             ) VALUES (
                :id, :filial, :tabela, :id_web, :id_desk
             )",
            [
                'id'      => Uuid::uuid4()->toString(),
                'filial'  => $filialId,
                'tabela'  => $tabela,
                'id_web'  => $idWeb,
                'id_desk' => $idDesk,
            ]
        );
    }

    /**
     * Busca ID WEB a partir do ID DESK
     */
    public function buscarPorDesk(
        string $filialId,
        string $tabela,
        string $idDesk
    ): ?string {
        $sql = "
            SELECT id_web
            FROM sync_map
            WHERE filial_id = :filial
              AND tabela = :tabela
              AND id_desk = :id_desk
            LIMIT 1
        ";

        $idWeb = $this->em->getConnection()->fetchOne($sql, [
            'filial'  => $filialId,
            'tabela'  => $tabela,
            'id_desk' => $idDesk,
        ]);

        return $idWeb ?: null;
    }

    /**
     * Busca ID DESK a partir do ID WEB
     */
    public function buscarPorWeb(
        string $filialId,
        string $tabela,
        string $idWeb
    ): ?string {
        $sql = "
            SELECT id_desk
            FROM sync_map
            WHERE filial_id = :filial
              AND tabela = :tabela
              AND id_web = :id_web
            LIMIT 1
        ";

        $idDesk = $this->em->getConnection()->fetchOne($sql, [
            'filial' => $filialId,
            'tabela' => $tabela,
            'id_web' => $idWeb,
        ]);

        return $idDesk ?: null;
    }

    /**
     * Retorna o ID WEB a partir do ID DESK
     */
    public function getWebId(
        string $filialId,
        string $tabela,
        string $idDesk
    ): ?string {
        $conn = $this->em->getConnection();

        $sql = "
            SELECT id_web
            FROM sync_map
            WHERE filial_id = :filial
              AND tabela = :tabela
              AND id_desk = :id_desk
            LIMIT 1
        ";

        $idWeb = $conn->fetchOne($sql, [
            'filial'  => $filialId,
            'tabela'  => $tabela,
            'id_desk' => $idDesk,
        ]);

        return $idWeb ?: null;
    }

    /**
     * Cria o de-para (DESK → WEB)
     */
    public function criar(
        string $filialId,
        string $tabela,
        string $idDesk,
        string $idWeb
    ): void {
        $conn = $this->em->getConnection();

        $conn->insert('sync_map', [
            'id'        => Uuid::uuid4()->toString(),
            'filial_id' => $filialId,
            'tabela'    => $tabela,
            'id_desk'   => $idDesk,
            'id_web'    => $idWeb,
        ]);
    }
}

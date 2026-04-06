<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class SyncFilaService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    /**
     * Cria um evento de sincronização
     */
    public function criar(
        string $filialId,
        string $tabela,
        ?string $idWeb,
        string $idDesk,
        string $operacao,
        string $direcao,
        array $payload
    ): string {
        $id = Uuid::uuid4()->toString();

        $sql = "
            INSERT INTO sync_fila (
                id, filial_id, tabela, id_web, id_desk,
                operacao, direcao, payload, status, tentativas
            ) VALUES (
                :id, :filial, :tabela, :id_web, :id_desk,
                :operacao, :direcao, :payload, 'PENDENTE', 0
            )
        ";

        $this->em->getConnection()->executeStatement($sql, [
            'id'        => $id,
            'filial'    => $filialId,
            'tabela'    => $tabela,
            'id_web'    => $idWeb,
            'id_desk'   => $idDesk,
            'operacao'  => $operacao,
            'direcao'   => $direcao,
            'payload'   => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ]);

        return $id;
    }

    /**
     * Marca evento como PROCESSANDO
     */
    public function marcarProcessando(string $id): void
    {
        $this->em->getConnection()->executeStatement(
            "UPDATE sync_fila SET status = 'PROCESSANDO' WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Marca evento como SUCESSO
     */
    public function marcarSucesso(string $id): void
    {
        $this->em->getConnection()->executeStatement(
            "UPDATE sync_fila
             SET status = 'SUCESSO',
                 processed_at = NOW()
             WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Marca evento como ERRO
     */
    public function marcarErro(string $id, string $erro): void
    {
        $this->em->getConnection()->executeStatement(
            "UPDATE sync_fila
             SET status = 'ERRO',
                 tentativas = tentativas + 1,
                 erro_log = :erro
             WHERE id = :id",
            [
                'id'   => $id,
                'erro' => substr($erro, 0, 65000),
            ]
        );
    }

    /**
     * Busca eventos pendentes (para worker)
     */
    public function buscarPendentes(
        string $direcao,
        int $limite = 50
    ): array {
        $sql = "
            SELECT *
            FROM sync_fila
            WHERE status = 'PENDENTE'
              AND direcao = :direcao
            ORDER BY created_at
            LIMIT :limite
        ";

        return $this->em->getConnection()->fetchAllAssociative(
            $sql,
            [
                'direcao' => $direcao,
                'limite'  => $limite,
            ],
            [
                'limite' => \PDO::PARAM_INT
            ]
        );
    }

    public function reprocessar(string $filaId): void
    {
        $conn = $this->em->getConnection();

        $fila = $conn->fetchAssociative(
            'SELECT status, tentativas FROM sync_fila WHERE id = :id',
            ['id' => $filaId]
        );

        if (!$fila) {
            throw new \DomainException('Registro de fila não encontrado.');
        }

        if ($fila['status'] !== 'ERRO') {
            throw new \DomainException('Somente registros com erro podem ser reprocessados.');
        }

        $conn->update(
            'sync_fila',
            [
                'status'     => 'PENDENTE',
                'erro_log'   => null,
                'tentativas' => $fila['tentativas'] + 1
            ],
            ['id' => $filaId]
        );
    }
}

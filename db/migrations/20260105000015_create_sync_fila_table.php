<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migration: sync_fila
 * Módulo: 8. Integração (Sync)
 * Descrição: Fila de sincronização Web <-> Desktop (Firebird).
 * Dependências: sis_filiais
 */
final class CreateSyncFilaTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sync_fila', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Fila de Sincronização Web <-> Desktop'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID). Fila de sincronização.'
            ])
            ->addColumn('filial_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para sis_filiais. De qual filial veio ou para qual vai o dado.'
            ])
            ->addColumn('tabela', 'string', [
                'limit' => 50,
                'null' => false,
                'comment' => 'Nome da tabela afetada (ex: cad_produtos).'
            ])
            ->addColumn('id_web', 'char', [
                'limit' => 36,
                'null' => true,
                'comment' => 'ID UUID do registro na Web.'
            ])
            ->addColumn('id_desk', 'string', [
                'limit' => 50,
                'null' => false,
                'comment' => 'ID Firebird Original (Desktop).'
            ])
            ->addColumn('operacao', 'enum', [
                'values' => ['UPSERT', 'DELETE'],
                'null' => false,
                'comment' => 'Tipo de operação.'
            ])
            ->addColumn('direcao', 'enum', [
                'values' => ['DESK_TO_WEB', 'WEB_TO_DESK'],
                'default' => 'DESK_TO_WEB',
                'null' => false,
                'comment' => 'Direção da sincronização.'
            ])
            ->addColumn('payload', 'json', [
                'null' => true,
                'comment' => 'O dado completo em JSON.'
            ])
            ->addColumn('status', 'enum', [
                'values' => ['PENDENTE', 'PROCESSANDO', 'SUCESSO', 'ERRO'],
                'default' => 'PENDENTE',
                'null' => false,
                'comment' => 'Status do processamento.'
            ])
            ->addColumn('tentativas', 'integer', [
                'default' => 0,
                'null' => false,
                'comment' => 'Número de tentativas de processamento.'
            ])
            ->addColumn('erro_log', 'text', [
                'null' => true,
                'comment' => 'Log de erro em caso de falha.'
            ])
            ->addColumn('processed_at', 'datetime', [
                'null' => true,
                'comment' => 'Data/hora do processamento.'
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false
            ])
            // Índices
            ->addIndex(['filial_id'], ['name' => 'idx_syncfila_filial'])
            ->addIndex(['tabela'], ['name' => 'idx_syncfila_tabela'])
            ->addIndex(['status'], ['name' => 'idx_syncfila_status'])
            ->addIndex(['direcao'], ['name' => 'idx_syncfila_direcao'])
            ->addIndex(['status', 'created_at'], ['name' => 'idx_syncfila_pendentes'])
            // Foreign Key
            ->addForeignKey('filial_id', 'sis_filiais', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_syncfila_filial'
            ])
            ->create();
    }
}

<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migration: sync_map
 * Módulo: 8. Integração (Sync)
 * Descrição: Dicionário De-Para (Evita duplicação de registros).
 * Dependências: sis_filiais
 */
final class CreateSyncMapTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sync_map', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Mapeamento De-Para entre IDs Web (UUID) e Desktop (Firebird)'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID). Dicionário De-Para.'
            ])
            ->addColumn('filial_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para sis_filiais. Escopo do mapeamento.'
            ])
            ->addColumn('tabela', 'string', [
                'limit' => 50,
                'null' => false,
                'comment' => 'Nome da tabela (ex: cad_produtos).'
            ])
            ->addColumn('id_web', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'UUID do registro na Web.'
            ])
            ->addColumn('id_desk', 'string', [
                'limit' => 50,
                'null' => false,
                'comment' => 'ID Inteiro do Firebird (Desktop).'
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false
            ])
            // Índices
            ->addIndex(['filial_id'], ['name' => 'idx_syncmap_filial'])
            ->addIndex(['tabela'], ['name' => 'idx_syncmap_tabela'])
            ->addIndex(['id_web'], ['name' => 'idx_syncmap_web'])
            ->addIndex(['id_desk'], ['name' => 'idx_syncmap_desk'])
            // Índice único composto para evitar duplicatas
            ->addIndex(['filial_id', 'tabela', 'id_desk'], ['name' => 'idx_syncmap_unique', 'unique' => true])
            // Foreign Key
            ->addForeignKey('filial_id', 'sis_filiais', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_syncmap_filial'
            ])
            ->create();
    }
}

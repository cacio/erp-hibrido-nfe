<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migration: est_saldo
 * Módulo: 4. Estoque
 * Descrição: Saldo Físico (Snapshot) - Tabela Crítica de estoque por filial.
 * Dependências: sis_filiais, cad_produtos
 */
final class CreateEstSaldoTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('est_saldo', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Saldo de Estoque por Filial - Tabela Crítica'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID). Saldo Físico (Snapshot).'
            ])
            ->addColumn('filial_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para sis_filiais. O estoque é propriedade da Filial.'
            ])
            ->addColumn('produto_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para cad_produtos.'
            ])
            ->addColumn('lote_id', 'char', [
                'limit' => 36,
                'null' => true,
                'comment' => 'FK para est_lotes (opcional, se controla por lote).'
            ])
            ->addColumn('quantidade', 'decimal', [
                'precision' => 15,
                'scale' => 4,
                'default' => 0,
                'null' => false,
                'comment' => 'Quantidade em estoque nesta filial.'
            ])
            ->addColumn('custo_medio', 'decimal', [
                'precision' => 15,
                'scale' => 4,
                'default' => 0,
                'null' => false,
                'comment' => 'Custo médio unitário.'
            ])
            ->addColumn('reservado', 'decimal', [
                'precision' => 15,
                'scale' => 4,
                'default' => 0,
                'null' => false,
                'comment' => 'Quantidade reservada (pedidos em aberto).'
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false
            ])
            ->addColumn('updated_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP',
                'null' => false
            ])
            // Índices
            ->addIndex(['filial_id'], ['name' => 'idx_saldo_filial'])
            ->addIndex(['produto_id'], ['name' => 'idx_saldo_produto'])
            ->addIndex(['lote_id'], ['name' => 'idx_saldo_lote'])
            ->addIndex(['filial_id', 'produto_id'], ['name' => 'idx_saldo_filial_produto', 'unique' => true])
            // Foreign Keys
            ->addForeignKey('filial_id', 'sis_filiais', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_saldo_filial'
            ])
            ->addForeignKey('produto_id', 'cad_produtos', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_saldo_produto'
            ])
            ->addForeignKey('lote_id', 'est_lotes', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
                'constraint' => 'fk_saldo_lote'
            ])
            ->create();
    }
}

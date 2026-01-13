<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migration: est_lotes
 * Módulo: 4. Estoque
 * Descrição: Rastreabilidade de Lotes (compartilhado no grupo, saldo por filial).
 * Dependências: cad_produtos
 */
final class CreateEstLotesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('est_lotes', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Lotes de Produtos - Rastreabilidade'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID). Rastreabilidade de lotes.'
            ])
            ->addColumn('tenant_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para sis_tenants.'
            ])
            ->addColumn('produto_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para cad_produtos.'
            ])
            ->addColumn('numero_lote', 'string', [
                'limit' => 50,
                'null' => false,
                'comment' => 'Número do lote (impresso na etiqueta).'
            ])
            ->addColumn('data_fabricacao', 'date', [
                'null' => true,
                'comment' => 'Data de fabricação.'
            ])
            ->addColumn('data_validade', 'date', [
                'null' => true,
                'comment' => 'Data de vencimento.'
            ])
            ->addColumn('data_abate', 'date', [
                'null' => true,
                'comment' => 'Data de abate (Específico Frigorífico).'
            ])
            ->addColumn('sif_origem', 'string', [
                'limit' => 20,
                'null' => true,
                'comment' => 'SIF do fornecedor (se houver).'
            ])
            ->addColumn('observacao', 'text', [
                'null' => true,
                'comment' => 'Observações adicionais do lote.'
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
            ->addIndex(['tenant_id'], ['name' => 'idx_lotes_tenant'])
            ->addIndex(['produto_id'], ['name' => 'idx_lotes_produto'])
            ->addIndex(['numero_lote'], ['name' => 'idx_lotes_numero'])
            ->addIndex(['data_validade'], ['name' => 'idx_lotes_validade'])
            ->addIndex(['produto_id', 'numero_lote'], ['name' => 'idx_lotes_produto_numero', 'unique' => true])
            // Foreign Keys
            ->addForeignKey('tenant_id', 'sis_tenants', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_lotes_tenant'
            ])
            ->addForeignKey('produto_id', 'cad_produtos', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_lotes_produto'
            ])
            ->create();
    }
}

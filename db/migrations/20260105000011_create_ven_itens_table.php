<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migration: ven_itens
 * Módulo: 5. Comercial (NFe)
 * Descrição: Itens das Notas Fiscais de Venda.
 * Dependências: ven_cabecalho, cad_produtos
 */
final class CreateVenItensTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('ven_itens', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Itens de Vendas/Notas Fiscais'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID). Itens da Nota.'
            ])
            ->addColumn('venda_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para ven_cabecalho.'
            ])
            ->addColumn('produto_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para cad_produtos.'
            ])
            ->addColumn('lote_id', 'char', [
                'limit' => 36,
                'null' => true,
                'comment' => 'FK para est_lotes (rastreabilidade).'
            ])
            ->addColumn('numero_item', 'integer', [
                'null' => false,
                'comment' => 'Número sequencial do item na NF.'
            ])
            ->addColumn('cfop', 'string', [
                'limit' => 4,
                'null' => false,
                'comment' => 'Código Fiscal de Operações e Prestações.'
            ])
            ->addColumn('quantidade', 'decimal', [
                'precision' => 15,
                'scale' => 4,
                'null' => false,
                'comment' => 'Quantidade vendida.'
            ])
            ->addColumn('valor_unitario', 'decimal', [
                'precision' => 15,
                'scale' => 4,
                'null' => false,
                'comment' => 'Valor unitário.'
            ])
            ->addColumn('valor_desconto', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => 'Valor do desconto no item.'
            ])
            ->addColumn('valor_total', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'null' => false,
                'comment' => 'Valor total do item (qtd * unit - desc).'
            ])
            ->addColumn('impostos_json', 'json', [
                'null' => true,
                'comment' => 'Snapshot de todos impostos calculados (ICMS, IPI, PIS, COFINS, IBS, CBS).'
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
            ->addIndex(['venda_id'], ['name' => 'idx_venitens_venda'])
            ->addIndex(['produto_id'], ['name' => 'idx_venitens_produto'])
            ->addIndex(['lote_id'], ['name' => 'idx_venitens_lote'])
            ->addIndex(['venda_id', 'numero_item'], ['name' => 'idx_venitens_seq', 'unique' => true])
            // Foreign Keys
            ->addForeignKey('venda_id', 'ven_cabecalho', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_venitens_venda'
            ])
            ->addForeignKey('produto_id', 'cad_produtos', 'id', [
                'delete' => 'RESTRICT',
                'update' => 'CASCADE',
                'constraint' => 'fk_venitens_produto'
            ])
            ->addForeignKey('lote_id', 'est_lotes', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
                'constraint' => 'fk_venitens_lote'
            ])
            ->create();
    }
}

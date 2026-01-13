<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migration: est_movimento
 * Módulo: 4. Estoque
 * Descrição: Kardex - Histórico de movimentações para Bloco K do SPED.
 * Dependências: sis_filiais, cad_produtos, est_lotes
 */
final class CreateEstMovimentoTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('est_movimento', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Movimentações de Estoque (Kardex) - Histórico para Bloco K'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID). Kardex (Histórico p/ Bloco K).'
            ])
            ->addColumn('filial_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para sis_filiais. Onde ocorreu a movimentação.'
            ])
            ->addColumn('produto_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para cad_produtos.'
            ])
            ->addColumn('lote_id', 'char', [
                'limit' => 36,
                'null' => true,
                'comment' => 'FK para est_lotes. Qual lote foi movimentado.'
            ])
            ->addColumn('data_mov', 'datetime', [
                'null' => false,
                'comment' => 'Data/hora da movimentação.'
            ])
            ->addColumn('tipo', 'enum', [
                'values' => ['ENTRADA', 'SAIDA'],
                'null' => false,
                'comment' => 'Tipo da movimentação.'
            ])
            ->addColumn('origem', 'enum', [
                'values' => ['COMPRA', 'VENDA', 'TRANSFERENCIA', 'PRODUCAO', 'AJUSTE', 'DEVOLUCAO'],
                'null' => false,
                'comment' => 'Origem da movimentação.'
            ])
            ->addColumn('documento_id', 'char', [
                'limit' => 36,
                'null' => true,
                'comment' => 'ID do documento de origem (NF, Pedido, etc.).'
            ])
            ->addColumn('documento_tipo', 'string', [
                'limit' => 50,
                'null' => true,
                'comment' => 'Tipo do documento (ven_cabecalho, etc.).'
            ])
            ->addColumn('quantidade', 'decimal', [
                'precision' => 15,
                'scale' => 4,
                'null' => false,
                'comment' => 'Quantidade movimentada.'
            ])
            ->addColumn('custo_unitario', 'decimal', [
                'precision' => 15,
                'scale' => 4,
                'default' => 0,
                'null' => false,
                'comment' => 'Custo unitário no momento da movimentação.'
            ])
            ->addColumn('custo_medio', 'decimal', [
                'precision' => 15,
                'scale' => 4,
                'default' => 0,
                'null' => false,
                'comment' => 'Custo médio calculado após a movimentação.'
            ])
            ->addColumn('saldo_anterior', 'decimal', [
                'precision' => 15,
                'scale' => 4,
                'default' => 0,
                'null' => false,
                'comment' => 'Saldo antes da movimentação.'
            ])
            ->addColumn('saldo_posterior', 'decimal', [
                'precision' => 15,
                'scale' => 4,
                'default' => 0,
                'null' => false,
                'comment' => 'Saldo após a movimentação.'
            ])
            ->addColumn('observacao', 'text', [
                'null' => true,
                'comment' => 'Observações da movimentação.'
            ])
            ->addColumn('usuario_id', 'char', [
                'limit' => 36,
                'null' => true,
                'comment' => 'FK para sis_usuarios. Quem realizou.'
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false
            ])
            // Índices
            ->addIndex(['filial_id'], ['name' => 'idx_movimento_filial'])
            ->addIndex(['produto_id'], ['name' => 'idx_movimento_produto'])
            ->addIndex(['lote_id'], ['name' => 'idx_movimento_lote'])
            ->addIndex(['data_mov'], ['name' => 'idx_movimento_data'])
            ->addIndex(['tipo'], ['name' => 'idx_movimento_tipo'])
            ->addIndex(['origem'], ['name' => 'idx_movimento_origem'])
            ->addIndex(['filial_id', 'produto_id', 'data_mov'], ['name' => 'idx_movimento_kardex'])
            // Foreign Keys
            ->addForeignKey('filial_id', 'sis_filiais', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_movimento_filial'
            ])
            ->addForeignKey('produto_id', 'cad_produtos', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_movimento_produto'
            ])
            ->addForeignKey('lote_id', 'est_lotes', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
                'constraint' => 'fk_movimento_lote'
            ])
            ->addForeignKey('usuario_id', 'sis_usuarios', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
                'constraint' => 'fk_movimento_usuario'
            ])
            ->create();
    }
}

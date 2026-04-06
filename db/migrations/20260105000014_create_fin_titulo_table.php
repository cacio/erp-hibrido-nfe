<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migration: fin_titulo
 * Módulo: 7. Financeiro
 * Descrição: Contas a Pagar e Receber.
 * Dependências: sis_filiais, fin_plano, cad_participantes
 */
final class CreateFinTituloTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('fin_titulo', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Títulos Financeiros (Contas a Pagar/Receber)'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID). Contas a Pagar/Receber.'
            ])
            ->addColumn('filial_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para sis_filiais. A qual caixa/banco pertence este título.'
            ])
            ->addColumn('plano_id', 'char', [
                'limit' => 36,
                'null' => true,
                'comment' => 'FK para fin_plano. Classificação DRE.'
            ])
            ->addColumn('participante_id', 'char', [
                'limit' => 36,
                'null' => true,
                'comment' => 'FK para cad_participantes. Cliente/Fornecedor.'
            ])
            ->addColumn('documento_id', 'char', [
                'limit' => 36,
                'null' => true,
                'comment' => 'ID do documento de origem (NF, CTe, etc.).'
            ])
            ->addColumn('documento_tipo', 'string', [
                'limit' => 50,
                'null' => true,
                'comment' => 'Tipo do documento de origem.'
            ])
            ->addColumn('tipo', 'enum', [
                'values' => ['PAGAR', 'RECEBER'],
                'null' => false,
                'comment' => 'Tipo do título.'
            ])
            ->addColumn('numero_documento', 'string', [
                'limit' => 50,
                'null' => true,
                'comment' => 'Número do documento/título.'
            ])
            ->addColumn('parcela', 'string', [
                'limit' => 10,
                'default' => '1/1',
                'null' => false,
                'comment' => 'Parcela (ex: "1/3").'
            ])
            ->addColumn('data_emissao', 'date', [
                'null' => false,
                'comment' => 'Data de emissão do título.'
            ])
            ->addColumn('data_vencimento', 'date', [
                'null' => false,
                'comment' => 'Data de vencimento.'
            ])
            ->addColumn('data_pagamento', 'date', [
                'null' => true,
                'comment' => 'Data de pagamento/recebimento efetivo.'
            ])
            ->addColumn('valor', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'null' => false,
                'comment' => 'Valor original do título.'
            ])
            ->addColumn('valor_pago', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => 'Valor efetivamente pago/recebido.'
            ])
            ->addColumn('juros', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => 'Valor de juros.'
            ])
            ->addColumn('multa', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => 'Valor de multa.'
            ])
            ->addColumn('desconto', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => 'Valor de desconto.'
            ])
            ->addColumn('status', 'enum', [
                'values' => ['ABERTO', 'PAGO', 'PARCIAL', 'CANCELADO'],
                'default' => 'ABERTO',
                'null' => false,
                'comment' => 'Status do título.'
            ])
            ->addColumn('forma_pagamento', 'string', [
                'limit' => 50,
                'null' => true,
                'comment' => 'Forma de pagamento (Dinheiro, Boleto, PIX, etc.).'
            ])
            ->addColumn('observacoes', 'text', [
                'null' => true,
                'comment' => 'Observações do título.'
            ])
            ->addColumn('usuario_id', 'char', [
                'limit' => 36,
                'null' => true,
                'comment' => 'FK para sis_usuarios. Quem cadastrou.'
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
            ->addIndex(['filial_id'], ['name' => 'idx_titulo_filial'])
            ->addIndex(['plano_id'], ['name' => 'idx_titulo_plano'])
            ->addIndex(['participante_id'], ['name' => 'idx_titulo_participante'])
            ->addIndex(['tipo'], ['name' => 'idx_titulo_tipo'])
            ->addIndex(['status'], ['name' => 'idx_titulo_status'])
            ->addIndex(['data_vencimento'], ['name' => 'idx_titulo_vencimento'])
            ->addIndex(['filial_id', 'tipo', 'status'], ['name' => 'idx_titulo_busca'])
            // Foreign Keys
            ->addForeignKey('filial_id', 'sis_filiais', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_titulo_filial'
            ])
            ->addForeignKey('plano_id', 'fin_plano', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
                'constraint' => 'fk_titulo_plano'
            ])
            ->addForeignKey('participante_id', 'cad_participantes', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
                'constraint' => 'fk_titulo_participante'
            ])
            ->addForeignKey('usuario_id', 'sis_usuarios', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
                'constraint' => 'fk_titulo_usuario'
            ])
            ->create();
    }
}

<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migration: ven_cabecalho
 * Módulo: 5. Comercial (NFe)
 * Descrição: Cabeçalho das Notas Fiscais de Venda.
 * Dependências: sis_filiais, cad_participantes
 */
final class CreateVenCabecalhoTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('ven_cabecalho', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Cabeçalho de Vendas/Notas Fiscais'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID). Nota Fiscal.'
            ])
            ->addColumn('filial_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para sis_filiais. Quem emitiu a nota (CNPJ Emissor).'
            ])
            ->addColumn('participante_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para cad_participantes. Cliente.'
            ])
            ->addColumn('transportadora_id', 'char', [
                'limit' => 36,
                'null' => true,
                'comment' => 'FK para cad_participantes. Transportadora.'
            ])
            ->addColumn('modelo', 'string', [
                'limit' => 2,
                'default' => '55',
                'null' => false,
                'comment' => '55 (NFe), 65 (NFCe).'
            ])
            ->addColumn('serie', 'string', [
                'limit' => 3,
                'default' => '1',
                'null' => false,
                'comment' => 'Série da NF.'
            ])
            ->addColumn('numero', 'integer', [
                'null' => true,
                'comment' => 'Número sequencial da NF.'
            ])
            ->addColumn('chave_acesso', 'string', [
                'limit' => 44,
                'null' => true,
                'comment' => 'Chave de acesso Sefaz (44 dígitos).'
            ])
            ->addColumn('protocolo', 'string', [
                'limit' => 20,
                'null' => true,
                'comment' => 'Protocolo de autorização Sefaz.'
            ])
            ->addColumn('status', 'integer', [
                'default' => 0,
                'null' => false,
                'comment' => '0=Digitação, 100=Autorizada, 101=Cancelada, 110=Denegada.'
            ])
            ->addColumn('data_emissao', 'datetime', [
                'null' => false,
                'comment' => 'Data/hora de emissão.'
            ])
            ->addColumn('data_saida', 'datetime', [
                'null' => true,
                'comment' => 'Data/hora de saída.'
            ])
            ->addColumn('natureza_operacao', 'string', [
                'limit' => 100,
                'default' => 'VENDA',
                'null' => false,
                'comment' => 'Natureza da operação.'
            ])
            ->addColumn('tipo_operacao', 'enum', [
                'values' => ['ENTRADA', 'SAIDA'],
                'default' => 'SAIDA',
                'null' => false,
                'comment' => 'Tipo da operação fiscal.'
            ])
            ->addColumn('valor_produtos', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => 'Valor total dos produtos.'
            ])
            ->addColumn('valor_frete', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => 'Valor do frete.'
            ])
            ->addColumn('valor_desconto', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => 'Valor do desconto.'
            ])
            ->addColumn('valor_total', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => 'Valor total da nota.'
            ])
            ->addColumn('xml_nfe', 'text', [
                'null' => true,
                'comment' => 'XML da NFe assinada.'
            ])
            ->addColumn('observacoes', 'text', [
                'null' => true,
                'comment' => 'Observações/Informações complementares.'
            ])
            ->addColumn('usuario_id', 'char', [
                'limit' => 36,
                'null' => true,
                'comment' => 'FK para sis_usuarios. Quem emitiu.'
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
            ->addIndex(['filial_id'], ['name' => 'idx_vencab_filial'])
            ->addIndex(['participante_id'], ['name' => 'idx_vencab_participante'])
            ->addIndex(['chave_acesso'], ['name' => 'idx_vencab_chave', 'unique' => true])
            ->addIndex(['status'], ['name' => 'idx_vencab_status'])
            ->addIndex(['data_emissao'], ['name' => 'idx_vencab_data'])
            ->addIndex(['filial_id', 'modelo', 'serie', 'numero'], ['name' => 'idx_vencab_nf', 'unique' => true])
            // Foreign Keys
            ->addForeignKey('filial_id', 'sis_filiais', 'id', [
                'delete' => 'RESTRICT',
                'update' => 'CASCADE',
                'constraint' => 'fk_vencab_filial'
            ])
            ->addForeignKey('participante_id', 'cad_participantes', 'id', [
                'delete' => 'RESTRICT',
                'update' => 'CASCADE',
                'constraint' => 'fk_vencab_participante'
            ])
            ->addForeignKey('transportadora_id', 'cad_participantes', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
                'constraint' => 'fk_vencab_transportadora'
            ])
            ->addForeignKey('usuario_id', 'sis_usuarios', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
                'constraint' => 'fk_vencab_usuario'
            ])
            ->create();
    }
}

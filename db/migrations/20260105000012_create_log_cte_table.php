<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migration: log_cte
 * Módulo: 6. Logística (CTe)
 * Descrição: Conhecimento de Transporte Eletrônico.
 * Dependências: sis_filiais, cad_participantes
 */
final class CreateLogCteTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('log_cte', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Conhecimento de Transporte Eletrônico (CTe)'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID). Conhecimento de Transporte.'
            ])
            ->addColumn('filial_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para sis_filiais. Transportadora (Filial emissora).'
            ])
            ->addColumn('remetente_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para cad_participantes. Remetente da carga.'
            ])
            ->addColumn('destinatario_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para cad_participantes. Destinatário da carga.'
            ])
            ->addColumn('tomador_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para cad_participantes. Quem paga o frete.'
            ])
            ->addColumn('modelo', 'string', [
                'limit' => 2,
                'default' => '57',
                'null' => false,
                'comment' => '57 (CTe).'
            ])
            ->addColumn('serie', 'string', [
                'limit' => 3,
                'default' => '1',
                'null' => false,
                'comment' => 'Série do CTe.'
            ])
            ->addColumn('numero', 'integer', [
                'null' => true,
                'comment' => 'Número sequencial do CTe.'
            ])
            ->addColumn('chave_acesso', 'string', [
                'limit' => 44,
                'null' => true,
                'comment' => 'Chave de acesso Sefaz (44 dígitos).'
            ])
            ->addColumn('status', 'integer', [
                'default' => 0,
                'null' => false,
                'comment' => '0=Digitação, 100=Autorizado, 101=Cancelado.'
            ])
            ->addColumn('data_emissao', 'datetime', [
                'null' => false,
                'comment' => 'Data/hora de emissão.'
            ])
            ->addColumn('valor_carga', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => 'Valor total da carga transportada.'
            ])
            ->addColumn('valor_frete', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => 'Valor do frete.'
            ])
            ->addColumn('peso_carga', 'decimal', [
                'precision' => 15,
                'scale' => 4,
                'default' => 0,
                'null' => false,
                'comment' => 'Peso total da carga (KG).'
            ])
            ->addColumn('chaves_nfe', 'json', [
                'null' => true,
                'comment' => 'Lista das chaves de NFe transportadas.'
            ])
            ->addColumn('xml_cte', 'text', [
                'null' => true,
                'comment' => 'XML do CTe assinado.'
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
            ->addIndex(['filial_id'], ['name' => 'idx_cte_filial'])
            ->addIndex(['remetente_id'], ['name' => 'idx_cte_remetente'])
            ->addIndex(['destinatario_id'], ['name' => 'idx_cte_destinatario'])
            ->addIndex(['chave_acesso'], ['name' => 'idx_cte_chave', 'unique' => true])
            ->addIndex(['data_emissao'], ['name' => 'idx_cte_data'])
            // Foreign Keys
            ->addForeignKey('filial_id', 'sis_filiais', 'id', [
                'delete' => 'RESTRICT',
                'update' => 'CASCADE',
                'constraint' => 'fk_cte_filial'
            ])
            ->addForeignKey('remetente_id', 'cad_participantes', 'id', [
                'delete' => 'RESTRICT',
                'update' => 'CASCADE',
                'constraint' => 'fk_cte_remetente'
            ])
            ->addForeignKey('destinatario_id', 'cad_participantes', 'id', [
                'delete' => 'RESTRICT',
                'update' => 'CASCADE',
                'constraint' => 'fk_cte_destinatario'
            ])
            ->addForeignKey('tomador_id', 'cad_participantes', 'id', [
                'delete' => 'RESTRICT',
                'update' => 'CASCADE',
                'constraint' => 'fk_cte_tomador'
            ])
            ->create();
    }
}

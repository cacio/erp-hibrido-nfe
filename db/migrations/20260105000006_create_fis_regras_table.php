<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migration: fis_regras
 * Módulo: 3. Fiscal (Regras)
 * Descrição: Matriz de Tributação Inteligente por UF/NCM.
 * Dependências: sis_tenants
 */
final class CreateFisRegrasTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('fis_regras', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Regras Fiscais - Matriz de Tributação Inteligente'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID). Matriz de Tributação Inteligente.'
            ])
            ->addColumn('tenant_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para sis_tenants.'
            ])
            ->addColumn('uf_origem', 'char', [
                'limit' => 2,
                'null' => false,
                'comment' => 'Estado de saída (Filial emissora).'
            ])
            ->addColumn('uf_destino', 'char', [
                'limit' => 2,
                'null' => false,
                'comment' => 'Estado do cliente (ou "TD" para todos).'
            ])
            ->addColumn('ncm_prefixo', 'string', [
                'limit' => 8,
                'null' => true,
                'comment' => 'NCM completo ou início dele (ex: "0201").'
            ])
            ->addColumn('cfop_padrao', 'string', [
                'limit' => 4,
                'null' => true,
                'comment' => 'CFOP sugerido.'
            ])
            ->addColumn('cst_icms', 'string', [
                'limit' => 3,
                'null' => true,
                'comment' => 'CST/CSOSN do ICMS.'
            ])
            ->addColumn('aliq_icms', 'decimal', [
                'precision' => 5,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => 'Alíquota ICMS (%).'
            ])
            ->addColumn('red_bc_icms', 'decimal', [
                'precision' => 5,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => 'Redução Base de Cálculo ICMS (%).'
            ])
            ->addColumn('cst_pis_cofins', 'string', [
                'limit' => 3,
                'null' => true,
                'comment' => 'CST PIS/COFINS.'
            ])
            ->addColumn('aliq_pis', 'decimal', [
                'precision' => 5,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => 'Alíquota PIS (%).'
            ])
            ->addColumn('aliq_cofins', 'decimal', [
                'precision' => 5,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => 'Alíquota COFINS (%).'
            ])
            // Campos para Reforma Tributária 2026
            ->addColumn('cst_ibs_cbs', 'string', [
                'limit' => 3,
                'null' => true,
                'comment' => '(2026) Nova Regra Reforma Tributária.'
            ])
            ->addColumn('aliq_ibs', 'decimal', [
                'precision' => 5,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => '(2026) Alíquota IBS Estadual/Municipal (%).'
            ])
            ->addColumn('aliq_cbs', 'decimal', [
                'precision' => 5,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => '(2026) Alíquota CBS Federal (%).'
            ])
            ->addColumn('ativo', 'boolean', [
                'default' => true,
                'null' => false,
                'comment' => 'Se a regra está ativa.'
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
            ->addIndex(['tenant_id'], ['name' => 'idx_fisregras_tenant'])
            ->addIndex(['uf_origem', 'uf_destino'], ['name' => 'idx_fisregras_uf'])
            ->addIndex(['ncm_prefixo'], ['name' => 'idx_fisregras_ncm'])
            ->addIndex(['tenant_id', 'uf_origem', 'uf_destino', 'ncm_prefixo'], ['name' => 'idx_fisregras_busca'])
            // Foreign Key
            ->addForeignKey('tenant_id', 'sis_tenants', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_fisregras_tenant'
            ])
            ->create();
    }
}

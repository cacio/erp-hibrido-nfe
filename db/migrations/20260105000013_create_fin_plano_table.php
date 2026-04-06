<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migration: fin_plano
 * Módulo: 7. Financeiro
 * Descrição: Plano de Contas (DRE) - Compartilhado no Grupo.
 * Dependências: sis_tenants
 */
final class CreateFinPlanoTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('fin_plano', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Plano de Contas para DRE'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID). Plano de Contas (DRE).'
            ])
            ->addColumn('tenant_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para sis_tenants. Compartilhado no Grupo.'
            ])
            ->addColumn('codigo', 'string', [
                'limit' => 20,
                'null' => false,
                'comment' => 'Código hierárquico (ex: "1.01", "2.03").'
            ])
            ->addColumn('nome', 'string', [
                'limit' => 100,
                'null' => false,
                'comment' => 'Nome da conta (ex: "Receita Vendas", "Energia Elétrica").'
            ])
            ->addColumn('tipo', 'enum', [
                'values' => ['SINTETICA', 'ANALITICA'],
                'default' => 'ANALITICA',
                'null' => false,
                'comment' => 'Sintética (agrupadora) ou Analítica (lançamento).'
            ])
            ->addColumn('natureza', 'enum', [
                'values' => ['RECEITA', 'DESPESA', 'ATIVO', 'PASSIVO'],
                'null' => false,
                'comment' => 'Natureza da conta.'
            ])
            ->addColumn('sinal_dre', 'integer', [
                'limit' => 2,
                'default' => 1,
                'null' => false,
                'comment' => '1 (Receita/Crédito), -1 (Despesa/Débito).'
            ])
            ->addColumn('conta_pai_id', 'char', [
                'limit' => 36,
                'null' => true,
                'comment' => 'FK para fin_plano (auto-relacionamento hierárquico).'
            ])
            ->addColumn('ativo', 'boolean', [
                'default' => true,
                'null' => false,
                'comment' => 'Se a conta está ativa.'
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
            ->addIndex(['tenant_id'], ['name' => 'idx_finplano_tenant'])
            ->addIndex(['codigo'], ['name' => 'idx_finplano_codigo'])
            ->addIndex(['tenant_id', 'codigo'], ['name' => 'idx_finplano_tenant_codigo', 'unique' => true])
            ->addIndex(['conta_pai_id'], ['name' => 'idx_finplano_pai'])
            // Foreign Keys
            ->addForeignKey('tenant_id', 'sis_tenants', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_finplano_tenant'
            ])
            ->addForeignKey('conta_pai_id', 'fin_plano', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'CASCADE',
                'constraint' => 'fk_finplano_pai'
            ])
            ->create();
    }
}

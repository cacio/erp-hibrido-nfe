<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migration: sis_tenants
 * Módulo: 1. Sistema (SaaS)
 * Descrição: Tabela raiz do sistema Multi-Tenant. Representa o GRUPO ECONÔMICO (O Dono).
 * Dependências: Nenhuma (tabela pai)
 */
final class CreateSisTenantsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sis_tenants', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Grupos Econômicos (Tenants) - Tabela raiz do sistema Multi-Tenant'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID). Representa o GRUPO ECONÔMICO.'
            ])
            ->addColumn('nome_grupo', 'string', [
                'limit' => 100,
                'null' => false,
                'comment' => 'Nome do grupo empresarial (ex: Grupo CristianoFoods).'
            ])
            ->addColumn('status', 'enum', [
                'values' => ['ATIVO', 'BLOQUEADO'],
                'default' => 'ATIVO',
                'null' => false,
                'comment' => 'Status do tenant.'
            ])
            ->addColumn('data_criacao', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false,
                'comment' => 'Data de cadastro.'
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
            ->addIndex(['status'], ['name' => 'idx_tenants_status'])
            ->create();
    }
}

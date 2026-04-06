<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migration: sis_filiais
 * Módulo: 1. Sistema (SaaS)
 * Descrição: Representa o CNPJ/Unidade Física (Matriz e Filiais).
 * Dependências: sis_tenants
 */
final class CreateSisFiliaisTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sis_filiais', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Filiais/CNPJs vinculados aos Tenants'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID). Representa o CNPJ/Unidade Física.'
            ])
            ->addColumn('tenant_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para sis_tenants. Vincula a loja ao dono.'
            ])
            ->addColumn('tipo_unidade', 'enum', [
                'values' => ['MATRIZ', 'FILIAL'],
                'default' => 'FILIAL',
                'null' => false,
                'comment' => 'Tipo da unidade.'
            ])
            ->addColumn('razao_social', 'string', [
                'limit' => 255,
                'null' => false,
                'comment' => 'Razão Social fiscal deste CNPJ.'
            ])
            ->addColumn('cnpj', 'string', [
                'limit' => 14,
                'null' => false,
                'comment' => 'CNPJ específico desta unidade.'
            ])
            ->addColumn('ie', 'string', [
                'limit' => 20,
                'null' => true,
                'comment' => 'Inscrição Estadual.'
            ])
            ->addColumn('uf', 'char', [
                'limit' => 2,
                'null' => false,
                'comment' => 'Estado da filial (Define tributação interna).'
            ])
            ->addColumn('config_nfe', 'json', [
                'null' => true,
                'comment' => 'Certificado A1, Senha, Última NFe emitida, Ambiente (Homolog/Prod).'
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
            ->addIndex(['tenant_id'], ['name' => 'idx_filiais_tenant'])
            ->addIndex(['cnpj'], ['name' => 'idx_filiais_cnpj', 'unique' => true])
            ->addIndex(['uf'], ['name' => 'idx_filiais_uf'])
            // Foreign Key
            ->addForeignKey('tenant_id', 'sis_tenants', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_filiais_tenant'
            ])
            ->create();
    }
}

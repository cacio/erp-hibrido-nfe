<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migration: sis_usuarios
 * Módulo: 1. Sistema (SaaS)
 * Descrição: Usuários do sistema com controle de acesso por filiais.
 * Dependências: sis_tenants
 */
final class CreateSisUsuariosTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sis_usuarios', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Usuários do sistema'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID).'
            ])
            ->addColumn('tenant_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para sis_tenants.'
            ])
            ->addColumn('nome', 'string', [
                'limit' => 100,
                'null' => false,
                'comment' => 'Nome completo do usuário.'
            ])
            ->addColumn('filiais_permitidas', 'json', [
                'null' => true,
                'comment' => 'Array de IDs das filiais que ele pode acessar (ou "*" para todas).'
            ])
            ->addColumn('email', 'string', [
                'limit' => 150,
                'null' => false,
                'comment' => 'Login único.'
            ])
            ->addColumn('senha_hash', 'string', [
                'limit' => 255,
                'null' => false,
                'comment' => 'Hash seguro (Bcrypt).'
            ])
            ->addColumn('ativo', 'boolean', [
                'default' => true,
                'null' => false,
                'comment' => 'Se o usuário está ativo.'
            ])
            ->addColumn('ultimo_acesso', 'datetime', [
                'null' => true,
                'comment' => 'Data/hora do último login.'
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
            ->addIndex(['tenant_id'], ['name' => 'idx_usuarios_tenant'])
            ->addIndex(['email'], ['name' => 'idx_usuarios_email', 'unique' => true])
            // Foreign Key
            ->addForeignKey('tenant_id', 'sis_tenants', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_usuarios_tenant'
            ])
            ->create();
    }
}

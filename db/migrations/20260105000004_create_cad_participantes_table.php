<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migration: cad_participantes
 * Módulo: 2. Cadastros (Geral)
 * Descrição: Clientes, Fornecedores e Transportadoras (Compartilhado no grupo).
 * Dependências: sis_tenants
 */
final class CreateCadParticipantesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('cad_participantes', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Cadastro de Participantes (Clientes/Fornecedores/Transportadoras)'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID). Cliente/Fornecedor compartilhado entre todas as lojas do grupo.'
            ])
            ->addColumn('tenant_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK (Pertence ao Grupo).'
            ])
            ->addColumn('cpf_cnpj', 'string', [
                'limit' => 14,
                'null' => true,
                'comment' => 'CPF ou CNPJ do participante.'
            ])
            ->addColumn('nome_razao', 'string', [
                'limit' => 255,
                'null' => false,
                'comment' => 'Nome ou Razão Social.'
            ])
            ->addColumn('nome_fantasia', 'string', [
                'limit' => 255,
                'null' => true,
                'comment' => 'Nome Fantasia.'
            ])
            ->addColumn('tipo_cadastro', 'set', [
                'values' => ['CLIENTE', 'FORNECEDOR', 'TRANSPORTADORA'],
                'null' => false,
                'comment' => 'Tipo do cadastro (pode ter múltiplos).'
            ])
            ->addColumn('ind_iedest', 'integer', [
                'limit' => 1,
                'default' => 9,
                'null' => false,
                'comment' => '1=Contribuinte, 2=Isento, 9=Não Contribuinte.'
            ])
            ->addColumn('ie', 'string', [
                'limit' => 20,
                'null' => true,
                'comment' => 'Inscrição Estadual.'
            ])
            ->addColumn('endereco_json', 'json', [
                'null' => true,
                'comment' => 'Logradouro, Número, Bairro, CEP, Cod Município IBGE.'
            ])
            ->addColumn('telefone', 'string', [
                'limit' => 20,
                'null' => true,
                'comment' => 'Telefone principal.'
            ])
            ->addColumn('email', 'string', [
                'limit' => 150,
                'null' => true,
                'comment' => 'E-mail de contato.'
            ])
            ->addColumn('ativo', 'boolean', [
                'default' => true,
                'null' => false,
                'comment' => 'Se o cadastro está ativo.'
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
            ->addIndex(['tenant_id'], ['name' => 'idx_participantes_tenant'])
            ->addIndex(['cpf_cnpj'], ['name' => 'idx_participantes_cpf_cnpj'])
            ->addIndex(['tenant_id', 'cpf_cnpj'], ['name' => 'idx_participantes_tenant_doc', 'unique' => true])
            ->addIndex(['tipo_cadastro'], ['name' => 'idx_participantes_tipo'])
            // Foreign Key
            ->addForeignKey('tenant_id', 'sis_tenants', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_participantes_tenant'
            ])
            ->create();
    }
}

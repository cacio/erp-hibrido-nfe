<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Migration: cad_produtos
 * Módulo: 2. Cadastros (Geral)
 * Descrição: Cadastro de produtos único para o grupo (Facilita gestão).
 * Dependências: sis_tenants
 */
final class CreateCadProdutosTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('cad_produtos', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Cadastro de Produtos (compartilhado no grupo)'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID). Cadastro único para o grupo.'
            ])
            ->addColumn('tenant_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK (Pertence ao Grupo).'
            ])
            ->addColumn('descricao', 'string', [
                'limit' => 200,
                'null' => false,
                'comment' => 'Nome/Descrição do produto.'
            ])
            ->addColumn('gtin_ean', 'string', [
                'limit' => 14,
                'null' => true,
                'comment' => 'Código de Barras Global (EAN/GTIN).'
            ])
            ->addColumn('codigo_sku', 'string', [
                'limit' => 50,
                'null' => true,
                'comment' => 'Código interno usado pelo cliente.'
            ])
            ->addColumn('ncm', 'string', [
                'limit' => 8,
                'null' => true,
                'comment' => 'Classificação Fiscal (NCM).'
            ])
            ->addColumn('cest', 'string', [
                'limit' => 7,
                'null' => true,
                'comment' => 'Código Especificador da Substituição Tributária.'
            ])
            ->addColumn('unidade', 'string', [
                'limit' => 6,
                'default' => 'UN',
                'null' => false,
                'comment' => 'Unidade de medida (UN, KG, PC, etc.).'
            ])
            ->addColumn('tipo_item_sped', 'string', [
                'limit' => 2,
                'default' => '00',
                'null' => false,
                'comment' => '00=Mercadoria, 01=Matéria Prima, 04=Prod. Acabado.'
            ])
            ->addColumn('peso_liquido', 'decimal', [
                'precision' => 12,
                'scale' => 4,
                'default' => 0,
                'null' => false,
                'comment' => 'Peso padrão em KG (Importante p/ Frigorífico).'
            ])
            ->addColumn('peso_bruto', 'decimal', [
                'precision' => 12,
                'scale' => 4,
                'default' => 0,
                'null' => false,
                'comment' => 'Peso bruto em KG.'
            ])
            ->addColumn('preco_venda', 'decimal', [
                'precision' => 15,
                'scale' => 2,
                'default' => 0,
                'null' => false,
                'comment' => 'Preço de venda padrão.'
            ])
            ->addColumn('preco_custo', 'decimal', [
                'precision' => 15,
                'scale' => 4,
                'default' => 0,
                'null' => false,
                'comment' => 'Preço de custo médio.'
            ])
            ->addColumn('ativo', 'boolean', [
                'default' => true,
                'null' => false,
                'comment' => 'Se o produto está ativo.'
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
            ->addIndex(['tenant_id'], ['name' => 'idx_produtos_tenant'])
            ->addIndex(['gtin_ean'], ['name' => 'idx_produtos_gtin'])
            ->addIndex(['codigo_sku'], ['name' => 'idx_produtos_sku'])
            ->addIndex(['ncm'], ['name' => 'idx_produtos_ncm'])
            ->addIndex(['tenant_id', 'codigo_sku'], ['name' => 'idx_produtos_tenant_sku', 'unique' => true])
            // Foreign Key
            ->addForeignKey('tenant_id', 'sis_tenants', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                'constraint' => 'fk_produtos_tenant'
            ])
            ->create();
    }
}

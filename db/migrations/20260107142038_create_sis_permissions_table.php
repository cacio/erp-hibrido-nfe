<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSisPermissionsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sis_permissions', [
            'id' => false,
            'primary_key' => ['id'],
        ]);

        $table
            ->addColumn('id', 'uuid', [
                'null' => false,
            ])
            ->addColumn('nome', 'string', [
                'limit' => 100,
                'null'  => false,
            ])
            ->addColumn('descricao', 'string', [
                'limit' => 255,
                'null'  => true,
            ])
            ->addIndex(['nome'], [
                'unique' => true,
                'name'   => 'idx_permission_nome_unique',
            ])
            ->create();
    }
}

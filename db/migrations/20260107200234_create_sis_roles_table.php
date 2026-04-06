<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSisRolesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sis_roles', [
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
            ->addIndex(['nome'], [
                'unique' => true,
                'name'   => 'idx_role_nome_unique',
            ])
            ->create();
    }
}

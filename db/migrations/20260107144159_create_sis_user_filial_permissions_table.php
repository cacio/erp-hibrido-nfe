<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSisUserFilialPermissionsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sis_user_filial_permissions');

        $table
            ->addColumn('user_id', 'uuid', ['null' => false])
            ->addColumn('filial_id', 'uuid', ['null' => false])
            ->addColumn('permission_id', 'uuid', ['null' => false])
            ->addColumn('allowed', 'tinyinteger', ['limit' => 1, 'null' => true, 'default' => 1])
            ->addIndex(
                ['user_id', 'filial_id', 'permission_id'],
                ['unique' => true, 'name' => 'uniq_user_filial_permission']
            )

            ->addForeignKey('user_id', 'sis_usuarios', 'id', ['delete' => 'CASCADE'])
            ->addForeignKey('filial_id', 'sis_filiais', 'id', ['delete' => 'CASCADE'])
            ->addForeignKey('permission_id', 'sis_permissions', 'id', ['delete' => 'CASCADE'])

            ->create();
    }
}

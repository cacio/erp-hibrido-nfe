<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSisRolePermissionsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sis_role_permissions', [
            'id' => false,
            'primary_key' => ['role_id', 'permission_id'],
        ]);

        $table
            ->addColumn('role_id', 'uuid', ['null' => false])
            ->addColumn('permission_id', 'uuid', ['null' => false])

            ->addForeignKey(
                'role_id',
                'sis_roles',
                'id',
                ['delete' => 'CASCADE']
            )
            ->addForeignKey(
                'permission_id',
                'sis_permissions',
                'id',
                ['delete' => 'CASCADE']
            )
            ->create();
    }
}

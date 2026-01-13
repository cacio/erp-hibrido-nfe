<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSisUserFilialRolesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sis_user_filial_roles', [
            'id' => false,
            'primary_key' => ['user_id', 'filial_id', 'role_id'],
        ]);

        $table
            ->addColumn('user_id', 'uuid', ['null' => false])
            ->addColumn('filial_id', 'uuid', ['null' => false])
            ->addColumn('role_id', 'uuid', ['null' => false])

            ->addForeignKey(
                'user_id',
                'sis_usuarios',
                'id',
                ['delete' => 'CASCADE']
            )
            ->addForeignKey(
                'filial_id',
                'sis_filiais',
                'id',
                ['delete' => 'CASCADE']
            )
            ->addForeignKey(
                'role_id',
                'sis_roles',
                'id',
                ['delete' => 'CASCADE']
            )
            ->create();
    }
}

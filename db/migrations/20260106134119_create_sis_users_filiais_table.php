<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSisUsersFiliaisTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sis_users_filiais', [
            'id' => false,
            'primary_key' => ['user_id', 'filial_id'],
            'engine' => 'InnoDB'
        ]);

        $table
            ->addColumn('user_id', 'char', [
                'limit' => 36,
                'null' => false
            ])
            ->addColumn('filial_id', 'char', [
                'limit' => 36,
                'null' => false
            ])
            ->addForeignKey('user_id', 'sis_usuarios', 'id', [
                'delete' => 'CASCADE'
            ])
            ->addForeignKey('filial_id', 'sis_filiais', 'id', [
                'delete' => 'CASCADE'
            ])
            ->create();
    }
}

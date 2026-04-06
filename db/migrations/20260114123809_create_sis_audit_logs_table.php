<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSisAuditLogsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('sis_audit_logs', [
            'id' => false,
            'primary_key' => ['id'],
        ]);

        $table
            ->addColumn('id', 'uuid',[
                'null' => false,
            ])
            ->addColumn('tenant_id', 'uuid', ['null' => false])
            ->addColumn('user_id', 'uuid', ['null' => true, 'comment' => 'Quem executou'])
            ->addColumn('action', 'string', ['limit' => 100])
            ->addColumn('entity', 'string', ['limit' => 100])
            ->addColumn('entity_id', 'uuid', ['null' => true])
            ->addColumn('description', 'string', ['limit' => 255])
            ->addColumn('ip_address', 'string', ['limit' => 45])
            ->addColumn('created_at', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
            ])
            ->create();
    }
}

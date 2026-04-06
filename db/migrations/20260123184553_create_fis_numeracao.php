<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateFisNumeracao extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('fis_numeracao', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => 'Controle de numeração fiscal (NF-e / NFC-e)'
        ]);

        $table
            ->addColumn('id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'PK (UUID)'
            ])
            ->addColumn('filial_id', 'char', [
                'limit' => 36,
                'null' => false,
                'comment' => 'FK para sis_filiais'
            ])
            ->addColumn('modelo', 'string', [
                'limit' => 2,
                'null' => false,
                'comment' => 'Modelo fiscal (55=NFe, 65=NFCe)'
            ])
            ->addColumn('serie', 'string', [
                'limit' => 3,
                'null' => false,
                'comment' => 'Série da NF'
            ])
            ->addColumn('ultimo_numero', 'integer', [
                'default' => 0,
                'null' => false,
                'comment' => 'Último número emitido'
            ])
            ->addIndex(
                ['filial_id', 'modelo', 'serie'],
                [
                    'unique' => true,
                    'name' => 'uniq_fis_numeracao'
                ]
            )
            ->addForeignKey(
                'filial_id',
                'sis_filiais',
                'id',
                [
                    'delete' => 'RESTRICT',
                    'update' => 'CASCADE',
                    'constraint' => 'fk_fisnumeracao_filial'
                ]
            )
            ->create();
    }
}

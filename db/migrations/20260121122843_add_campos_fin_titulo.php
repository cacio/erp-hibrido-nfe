<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddCamposFinTitulo extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('fin_titulo');


        // origem do lançamento
        $table->addColumn('origem', 'enum', [
            'values' => ['MANUAL', 'VENDA', 'COMPRA', 'NFE'],
            'null'   => false,
            'default'=> 'MANUAL',
            'after'  => 'tipo',
        ]);


        // índices
        $table->addIndex(['tipo'], ['name' => 'idx_fin_titulo_tipo']);
        $table->addIndex(['origem'], ['name' => 'idx_fin_titulo_origem']);

        $table->update();
    }
}

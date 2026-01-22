<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateFinDocumentoSeq extends AbstractMigration
{
    public function change(): void
    {
        /**
         * =====================================
         * TABELA: fin_documento_seq
         * =====================================
         */
        if (!$this->hasTable('fin_documento_seq')) {
            $table = $this->table('fin_documento_seq', [
                'id' => false,
                'primary_key' => ['prefixo'],
                'comment' => 'Controle de sequência de números de documentos financeiros'
            ]);

            $table
                ->addColumn('prefixo', 'string', [
                    'limit' => 10,
                    'null' => false,
                    'comment' => 'Prefixo do documento (MAN, REC, PAG, NF, etc)'
                ])
                ->addColumn('ultimo_numero', 'integer', [
                    'null' => false,
                    'default' => 0,
                    'comment' => 'Último número gerado para o prefixo'
                ])
                ->addColumn('created_at', 'timestamp', [
                    'default' => 'CURRENT_TIMESTAMP',
                    'null' => false
                ])
                ->addColumn('updated_at', 'timestamp', [
                    'default' => 'CURRENT_TIMESTAMP',
                    'update'  => 'CURRENT_TIMESTAMP',
                    'null' => false
                ])
                ->create();
        }

        /**
         * =====================================
         * SEED INICIAL DE PREFIXOS
         * =====================================
         */
        $prefixos = ['MAN', 'REC', 'PAG'];

        foreach ($prefixos as $prefixo) {

            $exists = $this->fetchRow(
                "SELECT prefixo FROM fin_documento_seq WHERE prefixo = '{$prefixo}'"
            );

            if (!$exists) {
                $this->execute(
                    "INSERT INTO fin_documento_seq (prefixo, ultimo_numero)
             VALUES ('{$prefixo}', 0)"
                );
            }
        }

        /**
         * =====================================
         * ÍNDICE ÚNICO: fin_titulo
         * =====================================
         */
        if ($this->hasTable('fin_titulo')) {

            $tableTitulo = $this->table('fin_titulo');

            if (!$tableTitulo->hasIndex(['numero_documento', 'parcela'])) {
                $tableTitulo
                    ->addIndex(
                        ['numero_documento', 'parcela'],
                        [
                            'unique' => true,
                            'name' => 'ux_fin_titulo_documento_parcela'
                        ]
                    )
                    ->update();
            }
        }
    }
}

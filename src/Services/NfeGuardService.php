<?php

namespace App\Services;

use App\Models\Filial;
use RuntimeException;

class NfeGuardService
{
    public function assertPodeEmitir(Filial $filial): void
    {
        $config = $filial->getConfigNfe();

        if (empty($config)) {
            throw new RuntimeException(
                'Filial sem configuração fiscal de NFe'
            );
        }

        if (empty($config['ambiente'])) {
            throw new RuntimeException(
                'Ambiente da NFe não configurado'
            );
        }

        if (empty($config['certificado']['arquivo'])) {
            throw new RuntimeException(
                'Certificado digital não configurado'
            );
        }

        if (!file_exists(BASE_PATH . $config['certificado']['arquivo'])) {
            throw new RuntimeException(
                'Arquivo do certificado não encontrado'
            );
        }

        if (empty($config['certificado']['senha'])) {
            throw new RuntimeException(
                'Senha do certificado não informada'
            );
        }

        if (!isset($config['numeracao']['nfe']['serie'])) {
            throw new RuntimeException(
                'Série da NFe não configurada'
            );
        }

        if (!isset($config['numeracao']['nfe']['ultimo_numero'])) {
            throw new RuntimeException(
                'Numeração da NFe não configurada'
            );
        }
    }
}

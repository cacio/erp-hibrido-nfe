<?php

namespace App\Services;

use App\Models\Filial;
use DateTime;
use RuntimeException;

class NfeCertificadoStatusService
{
    public function getStatus(Filial $filial): array
    {
        $config = $filial->getConfigNfe();

        if (
            empty($config['certificado']['arquivo']) ||
            empty($config['certificado']['senha'])
        ) {
            return [
                'status' => 'NAO_CONFIGURADO',
                'mensagem' => 'Certificado não configurado',
            ];
        }

       $dir = defined('BASE_PATH') ? BASE_PATH : dirname(dirname(__DIR__));
        // if (!is_dir($dir) || !is_readable($dir)) {
        //     return [
        //         'status' => 'ERRO',
        //         'mensagem' => 'Diretório do certificado inválido ou inacessível',
        //     ];
        // }

        $arquivo = $dir . $config['certificado']['arquivo'];
        $senha   = $config['certificado']['senha'];

        if (!file_exists($arquivo)) {
            return [
                'status' => 'ERRO',
                'mensagem' => 'Arquivo do certificado não encontrado',
            ];
        }

        $conteudo = file_get_contents($arquivo);
        $certs = [];

        if (!openssl_pkcs12_read($conteudo, $certs, $senha)) {
            return [
                'status' => 'ERRO',
                'mensagem' => 'Senha inválida ou certificado corrompido',
            ];
        }

        $dados = openssl_x509_parse($certs['cert']);
        if (!$dados) {
            return [
                'status' => 'ERRO',
                'mensagem' => 'Não foi possível ler o certificado',
            ];
        }

        $expiracao = (new DateTime())->setTimestamp($dados['validTo_time_t']);
        $hoje      = new DateTime();
        $dias      = (int) $hoje->diff($expiracao)->format('%r%a');

        // Definição de status
        if ($dias < 0) {
            $status = 'EXPIRADO';
        } elseif ($dias <= 30) {
            $status = 'ATENCAO';
        } else {
            $status = 'OK';
        }

        return [
            'status'     => $status,
            'expira_em'  => $expiracao->format('d/m/Y'),
            'dias'       => $dias,
            'titular'    => $dados['subject']['CN'] ?? '',
        ];
    }
}

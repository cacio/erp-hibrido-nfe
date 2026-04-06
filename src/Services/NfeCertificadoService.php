<?php

namespace App\Services;

use RuntimeException;

class NfeCertificadoService
{
    /**
     * Valida certificado A1 (.pfx)
     */
    public function validar(string $arquivo, string $senha): array
    {
        if (!file_exists($arquivo)) {
            throw new RuntimeException('Arquivo do certificado não encontrado');
        }

        $conteudo = file_get_contents($arquivo);

        $certs = [];
        if (!openssl_pkcs12_read($conteudo, $certs, $senha)) {
            throw new RuntimeException('Senha do certificado inválida ou certificado corrompido');
        }

        if (empty($certs['cert'])) {
            throw new RuntimeException('Certificado inválido (sem certificado público)');
        }

        if (empty($certs['pkey'])) {
            throw new RuntimeException('Certificado inválido (sem chave privada)');
        }

        $dados = openssl_x509_parse($certs['cert']);

        if (!$dados) {
            throw new RuntimeException('Não foi possível ler os dados do certificado');
        }

        $agora = time();
        if ($dados['validTo_time_t'] < $agora) {
            throw new RuntimeException('Certificado expirado');
        }

        return [
            'valido'      => true,
            'titular'     => $dados['subject']['CN'] ?? '',
            'cnpj'        => $dados['subject']['serialNumber'] ?? '',
            'inicio'      => date('d/m/Y', $dados['validFrom_time_t']),
            'expiracao'   => date('d/m/Y', $dados['validTo_time_t']),
        ];
    }
}

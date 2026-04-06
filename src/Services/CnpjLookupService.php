<?php

namespace App\Services;

class CnpjLookupService
{
    public function buscar(string $cnpj): ?array
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) !== 14) {
            return null;
        }

        $url = "https://brasilapi.com.br/api/cnpj/v1/{$cnpj}";

        $context = stream_context_create([
            'http' => [
                'timeout' => 5
            ]
        ]);

        $response = @file_get_contents($url, false, $context);

        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);
       // print_r($data); exit;
        if (isset($data['message'])) {
            return null;
        }

        return [
            'nome_razao'    => $data['razao_social'] ?? null,
            'nome_fantasia' => $data['nome_fantasia'] ?? null,
            'telefone'      => $data['ddd_telefone_1'] ?? null,
            'email'         => $data['email'] ?? null,
            'endereco'      => [
                'logradouro'    => $data['logradouro'] ?? '',
                'numero'        => $data['numero'] ?? '',
                'complemento'   => $data['complemento'] ?? '',
                'bairro'        => $data['bairro'] ?? '',
                'cep'           => preg_replace('/\D/', '', $data['cep'] ?? ''),
                'municipio'     => $data['municipio'] ?? '',
                'uf'            => $data['uf'] ?? '',
                'codigo_municipio' => $data['codigo_municipio_ibge'] ?? ''
            ]
        ];
    }
}

<?php

namespace App\Services;

class CepLookupService
{
    public function buscar(string $cep): ?array
    {
        $cep = preg_replace('/\D/', '', $cep);

        if (strlen($cep) !== 8) {
            return null;
        }

        $url = "https://brasilapi.com.br/api/cep/v2/{$cep}";

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

        if (isset($data['errors'])) {
            return null;
        }

        return [
            'logradouro'    => $data['street'] ?? '',
            'bairro'        => $data['neighborhood'] ?? '',
            'municipio'     => $data['city'] ?? '',
            'uf'            => $data['state'] ?? '',
            'cod_municipio' => $data['city_ibge_code'] ?? '',
        ];
    }
}

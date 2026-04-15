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

        // 🔥 1ª API (BrasilAPI)
        $data = $this->buscarBrasilApi($cnpj);

        if ($data) {
            return $data;
        }

        // 🔥 2ª API (fallback)
        return $this->buscarCnpjWs($cnpj);
    }


    private function buscarBrasilApi(string $cnpj): ?array
    {
        $url = "https://brasilapi.com.br/api/cnpj/v1/{$cnpj}";

        $response = @file_get_contents($url);

        if (!$response) return null;

        $data = json_decode($response, true);

        if (!$data || isset($data['message'])) return null;

        return [
            'nome_razao'    => $data['razao_social'] ?? null,
            'nome_fantasia' => $data['nome_fantasia'] ?? null,
            'telefone'      => $data['ddd_telefone_1'] ?? null,
            'email'         => $data['email'] ?? null,
            'inscricao_estadual' => $data['inscricao_estadual'] ?? null,
            'endereco'      => [
                'logradouro' => $data['logradouro'] ?? '',
                'numero'     => $data['numero'] ?? '',
                'complemento' => $data['complemento'] ?? '',
                'bairro'     => $data['bairro'] ?? '',
                'cep'        => preg_replace('/\D/', '', $data['cep'] ?? ''),
                'municipio'  => $data['municipio'] ?? '',
                'uf'         => $data['uf'] ?? '',
                'codigo_municipio' => $data['codigo_municipio_ibge'] ?? ''
            ]
        ];
    }


    private function buscarCnpjWs(string $cnpj): ?array
    {
        $url = "https://publica.cnpj.ws/cnpj/{$cnpj}";

        $response = @file_get_contents($url);

        if (!$response) return null;

        $data = json_decode($response, true);

        if (!$data || !isset($data['estabelecimento'])) return null;

        $est = $data['estabelecimento'];

        $ie = null;

        if (!empty($est['inscricoes_estaduais'])) {
            foreach ($est['inscricoes_estaduais'] as $insc) {
                if (!empty($insc['inscricao_estadual'])) {
                    $ie = $insc['inscricao_estadual'];
                    break;
                }
            }
        }

        return [
            'nome_razao'    => $data['razao_social'] ?? null,
            'nome_fantasia' => $est['nome_fantasia'] ?? null,
            'telefone'      => ($est['ddd1'] ?? '') . ($est['telefone1'] ?? ''),
            'email'         => $est['email'] ?? null,
            'inscricao_estadual' => $ie,
            'endereco'      => [
                'logradouro' => ($est['tipo_logradouro'] ?? '') . ' ' . ($est['logradouro'] ?? ''),
                'numero'     => $est['numero'] ?? '',
                'complemento' => $est['complemento'] ?? '',
                'bairro'     => $est['bairro'] ?? '',
                'cep'        => preg_replace('/\D/', '', $est['cep'] ?? ''),
                'municipio'  => $est['cidade']['nome'] ?? '',
                'uf'         => $est['estado']['sigla'] ?? '',
                'codigo_municipio' => $est['cidade']['ibge_id'] ?? ''
            ]
        ];
    }
}

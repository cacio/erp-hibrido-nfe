<?php

namespace App\Controllers;

use App\Core\Controller;

class CepController extends Controller
{
    public function buscar(string $cep): void
    {
        $cep = preg_replace('/\D/', '', $cep);

        if (strlen($cep) !== 8) {
            $this->json(['error' => 'CEP inválido'], 400);
            return;
        }

        // 🔥 usa ViaCEP (rápido e confiável)
        $url = "https://viacep.com.br/ws/{$cep}/json/";

        $response = @file_get_contents($url);

        if (!$response) {
            $this->json([]);
            return;
        }

        $data = json_decode($response, true);

        if (isset($data['erro'])) {
            $this->json([]);
            return;
        }

        $this->json([
            'cep'        => $data['cep'] ?? '',
            'logradouro' => $data['logradouro'] ?? '',
            'bairro'     => $data['bairro'] ?? '',
            'municipio'  => $data['localidade'] ?? '',
            'uf'         => $data['uf'] ?? '',

            // ⚠️ ViaCEP NÃO traz IBGE correto sempre
            'cod_municipio' => $data['ibge'] ?? null
        ]);
    }

     private function json(array $data, int $status = 200): void
    {
        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($data);
    }
}
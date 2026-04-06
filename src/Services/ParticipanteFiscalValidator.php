<?php

namespace App\Services;

use App\Models\Participante;
use App\Services\DocumentoValidator;
class ParticipanteFiscalValidator
{
    public function validar(array $dados, ?Participante $participante = null): void
    {
        // ================= DOCUMENTO =================
        $cpfCnpj = $dados['cpf_cnpj']
            ?? $participante?->getCpfCnpj();

        if (empty($cpfCnpj)) {
            throw new \DomainException('CPF ou CNPJ é obrigatório.');
        }

        if (!DocumentoValidator::validarCpfCnpj($cpfCnpj)) {
            throw new \DomainException('CPF ou CNPJ inválido.');
        }

        $doc = preg_replace('/\D/', '', $cpfCnpj);

        if (!in_array(strlen($doc), [11, 14])) {
            throw new \DomainException('CPF ou CNPJ inválido.');
        }

        // ================= NOME / RAZÃO SOCIAL =================
        $nomeRazao = trim(
            $dados['nome_razao']
                ?? $participante?->getNomeRazao()
                ?? ''
        );

        if ($nomeRazao === '') {
            throw new \DomainException('Nome ou Razão Social é obrigatório.');
        }


        // ================= TIPO =================
        if (empty($dados['tipo_cadastro'])) {
            throw new \DomainException('Selecione ao menos um tipo de cadastro.');
        }

        // ================= ENDEREÇOS =================
        $enderecos = $dados['enderecos']
            ?? $participante?->getEnderecoJson();

        if (empty($enderecos['principal'])) {
            throw new \DomainException('Endereço principal é obrigatório.');
        }

        $camposObrigatorios = [
            'logradouro',
            'bairro',
            'cep',
            'municipio',
            'cod_municipio',
            'uf'
        ];

        foreach ($enderecos as $tipo => $endereco) {

            // ignora endereço vazio (extra segurança)
            if (empty(array_filter($endereco))) {
                continue;
            }

            foreach ($camposObrigatorios as $campo) {
                if (empty($endereco[$campo])) {
                    throw new \DomainException(
                        "Endereço '{$tipo}' incompleto. Campo '{$campo}' é obrigatório."
                    );
                }
            }
        }

        // ================= IE =================
        $indIEDest = (int) (
            $dados['ind_iedest']
            ?? $participante?->getIndIeDest()
            ?? 9
        );

        $ie = trim(
            $dados['ie']
                ?? $participante?->getIe()
                ?? ''
        );

        if ($indIEDest === 1 && $ie === '') {
            throw new \DomainException(
                'Inscrição Estadual é obrigatória para contribuinte ICMS.'
            );
        }
    }
}

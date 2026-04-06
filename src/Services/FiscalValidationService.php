<?php

namespace App\Services;

use App\Models\Participante;

class FiscalValidationService
{
    public function validarParticipanteParaNfe(Participante $p): void
    {
        if (!$p->getCpfCnpj()) {
            throw new \DomainException('CPF/CNPJ não informado.');
        }

        if (!$p->getEndereco('principal')) {
            throw new \DomainException('Endereço fiscal não informado.');
        }

        $end = $p->getEndereco('principal');

        $camposObrigatorios = [
            'logradouro',
            'bairro',
            'cep',
            'municipio',
            'cod_municipio',
            'uf'
        ];

        foreach ($camposObrigatorios as $campo) {
            if (empty($end[$campo])) {
                throw new \DomainException(
                    "Endereço fiscal incompleto. Campo {$campo} é obrigatório."
                );
            }
        }

        if ($p->getIndIeDest() === 1 && !$p->getIe()) {
            throw new \DomainException(
                'IE obrigatória para contribuinte ICMS.'
            );
        }
    }
}

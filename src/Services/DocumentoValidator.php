<?php

namespace App\Services;

class DocumentoValidator
{
    public static function validarCpfCnpj(string $doc): bool
    {
        $doc = preg_replace('/\D/', '', $doc);

        if (strlen($doc) === 11) {
            return self::validarCpf($doc);
        }

        if (strlen($doc) === 14) {
            return self::validarCnpj($doc);
        }

        return false;
    }

    // ================= CPF =================
    private static function validarCpf(string $cpf): bool
    {
        // Elimina CPFs inválidos conhecidos
        if (preg_match('/^(.)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += $cpf[$i] * (($t + 1) - $i);
            }
            $digito = ((10 * $soma) % 11) % 10;
            if ($cpf[$t] != $digito) {
                return false;
            }
        }

        return true;
    }

    // ================= CNPJ =================
    private static function validarCnpj(string $cnpj): bool
    {
        if (preg_match('/^(.)\1{13}$/', $cnpj)) {
            return false;
        }

        $pesos1 = [5,4,3,2,9,8,7,6,5,4,3,2];
        $pesos2 = [6,5,4,3,2,9,8,7,6,5,4,3,2];

        $calc = function ($pesos) use ($cnpj) {
            $soma = 0;
            foreach ($pesos as $i => $peso) {
                $soma += $cnpj[$i] * $peso;
            }
            $resto = $soma % 11;
            return ($resto < 2) ? 0 : 11 - $resto;
        };

        if ($calc($pesos1) != $cnpj[12]) {
            return false;
        }

        if ($calc($pesos2) != $cnpj[13]) {
            return false;
        }

        return true;
    }
}

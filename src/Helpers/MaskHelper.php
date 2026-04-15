<?php

namespace App\Helpers;

final class MaskHelper
{
    private const MASK_CHAR = '#';

    public static function mask(string $value, string $format): string
    {
        $cleanValue = self::clean($value);
        if ($cleanValue === '' || $format === '') {
            return $value;
        }

        $masked = '';
        $index = 0;

        foreach (str_split($format) as $char) {
            $masked .= ($char === self::MASK_CHAR && isset($cleanValue[$index]))
                ? $cleanValue[$index++]
                : $char;
        }

        return $masked;
    }

    public static function cpf(string $value): string
    {
        return self::mask($value, '###.###.###-##');
    }

    public static function cnpj(string $value): string
    {
        return self::mask($value, '##.###.###/####-##');
    }

    public static function cpfCnpj(string $value): string
    {
        return match (strlen(self::clean($value))) {
            11 => self::cpf($value),
            14 => self::cnpj($value),
            default => $value,
        };
    }

    private static function clean(string $value): string
    {
        return preg_replace('/\D/', '', $value) ?? '';
    }
}
<?php

namespace App\Core;

class Request
{
    public static function isApi(): bool
    {
        return str_starts_with($_SERVER['REQUEST_URI'], '/api');
    }
}

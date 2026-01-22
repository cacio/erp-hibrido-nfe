<?php

namespace App\Core;

class Logger
{
    public static function error(\Throwable $e): void
    {
        $log = sprintf(
            "[%s] %s in %s:%d\n%s\n\n",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        file_put_contents(
            __DIR__ . '/../../storage/logs/error.log',
            $log,
            FILE_APPEND
        );
    }
}

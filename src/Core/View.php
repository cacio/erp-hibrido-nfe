<?php

namespace App\Core;

class View
{
    public static function render($view, $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . "/../../views/{$view}.php";

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "View não encontrada: {$viewPath}";
        }
    }
}

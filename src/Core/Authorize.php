<?php

namespace App\Core;

use App\Services\PermissionService;

class Authorize
{
    public static function authorize(string $permission): void
    {
        if (!isset($_SESSION['auth']['user_id'])) {
            header('Location: /login');
            exit;
        }

        $service = new PermissionService();

        if (!$service->can($permission)) {
            http_response_code(403);

            if (php_sapi_name() !== 'cli') {
                echo '<h1>403 - Acesso negado</h1>';
                echo '<p>Você não tem permissão para acessar esta funcionalidade.</p>';
            }

            exit;
        }
    }
}

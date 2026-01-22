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
                require __DIR__ . '/../../views/errors/403.php';
            }

            exit;
        }
    }
}

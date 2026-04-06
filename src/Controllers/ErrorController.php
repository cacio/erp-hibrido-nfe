<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class ErrorController extends Controller
{
    public function notFound(): void
    {
        http_response_code(404);

        if (Request::isApi()) {
            echo json_encode(['error' => 'Not Found']);
            return;
        }

        $this->render('errors/404');
    }

    public function methodNotAllowed(array $allowed): void
    {
        http_response_code(405);

        if (Request::isApi()) {
            echo json_encode([
                'error' => 'Method Not Allowed',
                'allowed' => $allowed
            ]);
            return;
        }

        $this->render('errors/405', compact('allowed'));
    }

    public function internalError(): void
    {
        http_response_code(500);

        if (Request::isApi()) {
            echo json_encode(['error' => 'Internal Server Error']);
            return;
        }

        $this->render('errors/500');
    }
}

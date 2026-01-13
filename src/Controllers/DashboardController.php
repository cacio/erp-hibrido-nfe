<?php

namespace App\Controllers;

use App\Core\Authorize;
use App\Core\Controller; // Importa o Controller Base

class DashboardController extends Controller // Herda do Controller
{

    public function index()
    {
        $this->checkAuth();
        Authorize::authorize('dashboard.view');
        // A variável $userName já é passada globalmente pelo método render()
        $this->render('dashboard/index', [
            'user'   => $this->user,
            'tenant' => $this->tenant,
            'filial' => $this->filial,
        ]);
    }
}

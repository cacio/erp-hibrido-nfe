<?php

namespace App\Controllers;

use App\Core\Authorize;
use App\Core\Controller; // Importa o Controller Base
use App\Core\EntityManagerFactory;
use App\Services\FiscalDashboardService;
use App\Services\NfeCertificadoStatusService;

class DashboardController extends Controller // Herda do Controller
{

    public function index()
    {
        $this->checkAuth();
        Authorize::authorize('dashboard.view');

        $filial = $this->filial;

        $certStatus = (new NfeCertificadoStatusService())
            ->getStatus($filial);
        $conn = EntityManagerFactory::create()->getConnection();
        $fiscal = (new FiscalDashboardService($conn))
            ->resumoPorFilial($_SESSION['auth']['filial_id']);

        // A variável $userName já é passada globalmente pelo método render()
        $this->render('dashboard/index', [
            'user'   => $this->user,
            'tenant' => $this->tenant,
            'filial' => $this->filial,
            'certificado' => $certStatus,
            'fiscal' => $fiscal,
        ]);
    }
}

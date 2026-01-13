<?php

namespace App\Core;

use App\Models\User;
use App\Models\Filial;
use App\Models\Tenant;

abstract class Controller
{

    protected ?User $user = null;
    protected ?Filial $filial = null;
    protected ?Tenant $tenant = null;

    public function __construct()
    {

        if (!isset($_SESSION['auth']['user_id'])) {
            return;
        }

        $em = EntityManagerFactory::create();

        $this->user = $em->find(User::class, $_SESSION['auth']['user_id']);

        if ($this->user) {
            $this->tenant = $this->user->getTenant();
        }

        if (isset($_SESSION['auth']['filial_id'])) {
            $this->filial = $em->find(Filial::class, $_SESSION['auth']['filial_id']);
        }
    }

    protected function render(string $view, array $data = []): void
    {
        $viewPath = dirname(__DIR__, 2) . '/views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo "View não encontrada: {$viewPath}";
            exit;
        }

        extract($data);
        require $viewPath;
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    protected function flash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    protected function getFlash(string $key): ?string
    {
        if (!isset($_SESSION['flash'][$key])) {
            return null;
        }

        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);

        return $message;
    }

    protected function authRequired(): void
    {
        if (!$this->user) {
            $this->redirect('/login');
        }
    }

    protected function tenantRequired(): void
    {
        if (!isset($_SESSION['tenant_id'])) {
            session_destroy();
            $this->redirect('/login');
        }
    }

    protected function filialRequired(): void
    {
        if (!isset($_SESSION['filial_id'])) {
            $this->redirect('/select-filial');
        }
    }

    /**
     * Verifica se o usuário está autenticado. Se não estiver,
     * redireciona para a página de login.
     *
     * Este método deve ser chamado no construtor dos controllers
     * que protegem áreas restritas.
     */
    protected function checkAuth(): void
    {
        if (!isset($_SESSION['auth']['user_id'])) {
            $this->redirect('/login');
        }
    }
}

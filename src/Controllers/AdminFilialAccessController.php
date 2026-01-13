<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Authorize;
use App\Core\EntityManagerFactory;
use App\Services\FilialAdminService;
use App\Services\UserAdminService;
use App\Services\RoleAdminService;

class AdminFilialAccessController extends Controller
{
    private FilialAdminService $filialService;
    private UserAdminService $userService;
    private RoleAdminService $roleService;

    public function __construct()
    {
        parent::__construct();

        $this->filialService = new FilialAdminService();
        $this->userService   = new UserAdminService();
        $this->roleService   = new RoleAdminService();
    }

    /**
     * Tela: Acessos da Filial
     */
    public function index(string $filialId)
    {
        Authorize::authorize('admin.filial.access');

        $this->render('admin/filiais/acessos', [
            'filial'             => $this->filialService->find($filialId),
            'usuarios'           => $this->filialService->usuariosDaFilial($filialId),
            'usuariosDisponiveis'=> $this->filialService->usuariosDisponiveis($filialId),
        ]);
    }

    /**
     * Tela: Gerenciar Roles do Usuário na Filial
     */
    public function roles(string $filialId, string $userId)
    {
        Authorize::authorize('admin.filial.roles');

        $this->render('admin/filiais/roles', [
            'filial'    => $this->filialService->find($filialId),
            'user'      => $this->userService->find($userId),
            'roles'     => $this->roleService->all(),
            'userRoles' => $this->roleService
                ->rolesDoUsuarioNaFilial($userId, $filialId),
        ]);
    }

    /**
     * Salva roles do usuário na filial
     */
    public function saveRoles(string $filialId, string $userId)
    {
        Authorize::authorize('admin.filial.roles');

        $roles = $_POST['roles'] ?? [];

        $this->roleService->syncUserRolesInFilial(
            $userId,
            $filialId,
            $roles
        );

        $this->redirect("/admin/filiais/{$filialId}/acessos");
    }

    /**
     * Adiciona usuário à filial
     */
    public function addUser(string $filialId)
    {
        Authorize::authorize('admin.filial.access');

        $userId = $_POST['user_id'];

        $this->filialService->addUser($filialId, $userId);

        $this->redirect("/admin/filiais/{$filialId}/acessos");
    }

    /**
     * Remove usuário da filial
     */
    public function removeUser(string $filialId, string $userId)
    {
        Authorize::authorize('admin.filial.access');

        $this->filialService->removeUser($filialId, $userId);

        $this->redirect("/admin/filiais/{$filialId}/acessos");
    }
}

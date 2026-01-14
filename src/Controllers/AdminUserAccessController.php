<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Authorize;
use App\Services\FilialAdminService;
use App\Services\UserFilialAccessService;
use App\Services\UserAdminService;
use App\Services\RoleAdminService;
use App\Services\FilialService;

class AdminUserAccessController extends Controller
{
    private UserFilialAccessService $access;
    private UserAdminService $users;
    private RoleAdminService $roles;

    public function __construct()
    {
        parent::__construct();
        $this->access = new UserFilialAccessService();
        $this->users  = new UserAdminService();
        $this->roles  = new RoleAdminService();
    }

    public function index(string $id)
    {
        Authorize::authorize('admin.users');

        $user = $this->users->find($id);
        $accessService = new UserFilialAccessService();
        $this->render('admin/usuarios/acesso', [
            'user'    => $user,
            'filiais' => $user->getFiliais(),
            'roles'   => $this->roles->all(),
            'acessos' => $accessService->acessosDoUsuario($id),
            'filiaisDisponiveis' => (new FilialAdminService())
                ->filiaisDisponiveisParaUsuario($id),
        ]);
    }

    public function save()
    {
        Authorize::authorize('admin.users');

        $this->access->salvar(
            $_POST['user_id'],
            $_POST['filial_id'],
            $_POST['roles'] ?? []
        );

        $this->flash('success', 'Acessos atualizados');
        $this->redirect('/admin/usuarios');
    }
}

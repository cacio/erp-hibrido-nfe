<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Authorize;
use App\Services\FilialAdminService;
use App\Services\UserAdminService;
use App\Services\UserFilialAccessService;

class AdminUserController extends Controller
{
    private UserAdminService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new UserAdminService();
    }

    public function index()
    {
        Authorize::authorize('admin.users');

        $access = new UserFilialAccessService();
        $users = $this->service->all();

        $filiais = (new FilialAdminService())->allByTenant($_SESSION['auth']['tenant_id']);

        $accessMap = [];
        foreach ($users as $u) {
            $accessMap[$u->getId()] =
                $access->resumoPorUsuario($u->getId());
        }



        $this->render('admin/usuarios/index', [
            'users' => $users,
            'edit'  => null,
            'filiais'=>$filiais,
            'accessMap' => $accessMap,
        ]);
    }

    public function edit(string $id)
    {
        Authorize::authorize('admin.users');

        $filiais = (new FilialAdminService())->allByTenant($_SESSION['auth']['tenant_id']);

        $this->render('admin/usuarios/index', [
            'users' => $this->service->all(),
            'edit'  => $this->service->find($id),
            'filiais'=>$filiais
        ]);
    }

    public function store()
    {
        Authorize::authorize('admin.users');

        try {
            $this->service->create($_POST);
            $this->flash('success', 'Usuário criado');
        } catch (\Exception $e) {
            $this->flash('error', $e->getMessage());
        }

        $this->redirect('/admin/usuarios');
    }

    public function update(string $id)
    {
        Authorize::authorize('admin.users');

        try {
            $this->service->update($id, $_POST);
            $this->flash('success', 'Usuário atualizado');
        } catch (\Exception $e) {
            $this->flash('error', $e->getMessage());
        }

        $this->redirect('/admin/usuarios');
    }

    public function resetPassword(string $id)
    {
        Authorize::authorize('admin.users');

        $this->service->resetPassword($id);
        $this->flash('success', 'Senha resetada para 123456');
        $this->redirect('/admin/usuarios');
    }
}

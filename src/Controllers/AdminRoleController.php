<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Authorize;
use App\Services\RoleAdminService;

class AdminRoleController extends Controller
{
    private RoleAdminService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new RoleAdminService();
    }

    public function index()
    {
        Authorize::authorize('admin.roles');

        $this->render('admin/roles/index', [
            'roles'       => $this->service->all(),
            'permissions' => $this->service->permissions(),
            'edit'        => null,
            'editPermissionIds'=>null
        ]);
    }

    public function edit(string $id)
    {
        Authorize::authorize('admin.roles');

        $edit = $this->service->find($id);

        $editPermissionIds = [];

        if ($edit) {
            foreach ($edit->getPermissions() as $perm) {
                $editPermissionIds[] = $perm->getId();
            }
        }

        $this->render('admin/roles/index', [
            'roles'       => $this->service->all(),
            'permissions' => $this->service->permissions(),
            'edit' => $edit,
            'editPermissionIds' => $editPermissionIds,
        ]);
    }

    public function store()
    {
        Authorize::authorize('admin.roles');

        try {
            $this->service->create(
                $_POST['nome'] ?? '',
                $_POST['permissions'] ?? []
            );
            $this->flash('success', 'Role criada com sucesso');
        } catch (\Exception $e) {
            $this->flash('error', $e->getMessage());
        }

        $this->redirect('/admin/roles');
    }

    public function update(string $id)
    {
        Authorize::authorize('admin.roles');

        try {
            $this->service->update(
                $id,
                $_POST['nome'] ?? '',
                $_POST['permissions'] ?? []
            );
            $this->flash('success', 'Role atualizada com sucesso');
        } catch (\Exception $e) {
            $this->flash('error', $e->getMessage());
        }

        $this->redirect('/admin/roles');
    }

    public function delete(string $id)
    {
        Authorize::authorize('admin.roles');

        try {
            $this->service->delete($id);
            $this->flash('success', 'Role removida');
        } catch (\Exception $e) {
            $this->flash('error', $e->getMessage());
        }

        $this->redirect('/admin/roles');
    }
}

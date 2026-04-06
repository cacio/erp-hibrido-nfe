<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Authorize;
use App\Services\FilialAdminService;

class AdminFilialController extends Controller
{
    private FilialAdminService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new FilialAdminService();
    }

    public function index()
    {
        Authorize::authorize('admin.filiais');

        $this->render('admin/filiais/index', [
            'filiais' => $this->service->allByTenant($_SESSION['auth']['tenant_id']),
            'edit'    => null,
        ]);
    }

    public function edit(string $id)
    {
        Authorize::authorize('admin.filiais');

        $this->render('admin/filiais/index', [
            'filiais' => $this->service->allByTenant($_SESSION['auth']['tenant_id']),
            'edit'    => $this->service->find($id),
        ]);
    }

    public function store()
    {
        Authorize::authorize('admin.filiais');

        try {
            $this->service->create($_POST);
            $this->flash('success', 'Filial criada com sucesso');
        } catch (\Exception $e) {
            $this->flash('error', $e->getMessage());
        }

        $this->redirect('/admin/filiais');
    }

    public function update(string $id)
    {
        Authorize::authorize('admin.filiais');

        try {
            $this->service->update($id, $_POST);
            $this->flash('success', 'Filial atualizada');
        } catch (\Exception $e) {
            $this->flash('error', $e->getMessage());
        }

        $this->redirect('/admin/filiais');
    }

    public function delete(string $id)
    {
        Authorize::authorize('admin.filiais');

        try {
            $this->service->delete($id);
            $this->flash('success', 'Filial removida');
        } catch (\Exception $e) {
            $this->flash('error', $e->getMessage());
        }

        $this->redirect('/admin/filiais');
    }

    public function configNfe(string $id)
    {
        Authorize::authorize('admin.filiais');
        $filial = $this->service->find($id);

        $this->render('admin/filiais/nfe_config', [
            'filial' => $filial,
        ]);
    }
}

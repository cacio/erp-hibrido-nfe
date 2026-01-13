<?php
// App\Controllers\AdminPermissionController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Authorize;
use App\Services\PermissionAdminService;
use Ramsey\Uuid\Uuid;

class AdminPermissionController extends Controller
{
    private PermissionAdminService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new PermissionAdminService();
    }

    public function index()
    {
        Authorize::authorize('admin.permissions');

        $this->render('admin/permissoes/index', [
            'permissions' => $this->service->all(),
            'edit'=>null
        ]);
    }

    public function edit(string $id)
    {
        Authorize::authorize('admin.permissions');

        $this->render('admin/permissoes/index', [
            'permissions' => $this->service->all(),
            'edit' => $this->service->find($id),
        ]);
    }

    public function store()
    {
        Authorize::authorize('admin.permissions');

        try {
            $this->service->create(
                $_POST['nome'] ?? '',
                $_POST['descricao'] ?? null
            );

            $this->flash('success', 'Permissão criada com sucesso');
        } catch (\Exception $e) {
            // echo '<pre>';

            // echo "MENSAGEM:\n";
            // var_dump($e->getMessage());

            // echo "\nCLASSE DO ERRO:\n";
            // var_dump(get_class($e));

            // echo "\nTRACE:\n";
            // var_dump($e->getTraceAsString());

            // echo '</pre>';
        }

        $this->redirect('/admin/permissoes');
    }

    public function update(string $id)
    {
        Authorize::authorize('admin.permissions');

        try {
            $this->service->update(
                $id,
                $_POST['nome'] ?? '',
                $_POST['descricao'] ?? null
            );

            $this->flash('success', 'Permissão atualizada com sucesso');
        } catch (\Exception $e) {
            $this->flash('error', $e->getMessage());
        }

        $this->redirect('/admin/permissoes');
    }

    public function delete(string $id)
    {
        Authorize::authorize('admin.permissions');

        try {
            $this->service->delete($id);
            $this->flash('success', 'Permissão removida');
        } catch (\Exception $e) {
            $this->flash('error', $e->getMessage());
        }

        $this->redirect('/admin/permissoes');
    }
}

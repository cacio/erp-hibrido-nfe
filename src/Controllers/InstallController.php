<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\EntityManagerFactory;
use App\Models\Tenant;
use App\Models\Filial;
use App\Models\User;
use Ramsey\Uuid\Uuid;

class InstallController extends Controller
{
    public function index()
    {
        // Se já estiver instalado, bloqueia
        if (file_exists(__DIR__ . '/../../storage/installed.lock')) {
            $this->redirect('/login');
        }

        $this->render('install/index');
    }

    public function store()
    {
        if (file_exists(__DIR__ . '/../../storage/installed.lock')) {
            $this->redirect('/login');
        }

        $em = EntityManagerFactory::create();

        $em->beginTransaction();

        try {
           // var_dump($_POST);
            // 🔹 Tenant
            $tenant = new Tenant();
            $this->setPrivate($tenant, 'id', Uuid::uuid4()->toString());
            $this->setPrivate($tenant, 'nome_grupo', $_POST['nome_grupo']);
            $this->setPrivate($tenant, 'status', 'ATIVO');
            $this->setPrivate($tenant, 'data_criacao', new \DateTime());
            $this->setPrivate($tenant, 'created_at', new \DateTime());
            $this->setPrivate($tenant, 'updated_at', new \DateTime());

            // 🔹 Filial
            $filial = new Filial();
            $this->setPrivate($filial, 'id', Uuid::uuid4()->toString());
            $this->setPrivate($filial, 'tenant', $tenant);
            $this->setPrivate($filial, 'tipo_unidade', 'MATRIZ');
            $this->setPrivate($filial, 'razao_social', $_POST['razao_social']);
            $this->setPrivate($filial, 'cnpj', $_POST['cnpj']);
            $this->setPrivate($filial, 'uf', $_POST['uf'] ?? '');
            $this->setPrivate($filial, 'created_at', new \DateTime());
            $this->setPrivate($filial, 'updated_at', new \DateTime());

            // 🔹 Usuário Admin
            $user = new User();
            $this->setPrivate($user, 'id', Uuid::uuid4()->toString());
            $this->setPrivate($user, 'tenant', $tenant);
            $this->setPrivate($user, 'nome', 'Administrador');
            $this->setPrivate($user, 'email', $_POST['email']);
            $this->setPrivate($user, 'senha_hash', password_hash($_POST['senha'], PASSWORD_DEFAULT));
            $this->setPrivate($user, 'ativo', true);
            $this->setPrivate($user, 'created_at', new \DateTime());
            $this->setPrivate($user, 'updated_at', new \DateTime());

            // relaciona usuário ↔ filial
            $user->getFiliais()->add($filial);

            $em->persist($tenant);
            $em->persist($filial);
            $em->persist($user);

            $em->flush();
            $em->commit();

            // 🔒 trava instalação
            file_put_contents(__DIR__ . '/../../storage/installed.lock', date('Y-m-d H:i:s'));

            $this->redirect('/login');

        } catch (\Throwable $e) {
            $em->rollback();
            throw $e;
        }
    }

    /**
     * Hack controlado para setar propriedades privadas (instalação apenas)
     */
    private function setPrivate(object $obj, string $prop, mixed $value): void
    {
        $ref = new \ReflectionClass($obj);
        $property = $ref->getProperty($prop);
        $property->setAccessible(true);
        $property->setValue($obj, $value);
    }
}

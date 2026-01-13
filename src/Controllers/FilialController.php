<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\EntityManagerFactory;
use App\Models\Filial;
use App\Models\User;

class FilialController extends Controller
{
    public function __construct()
    {
        // Chama o método de verificação de autenticação do pai
        $this->checkAuth();
    }

    public function selecionarFilial()
    {
        $em = EntityManagerFactory::create();
        $user = $em->find(User::class, $_SESSION['user_id']);

        $this->render('auth/select-filial', [
            'filiais' => $user->getFiliais()
        ]);
    }

    public function confirmarFilial()
    {
        $filialId = $_POST['filial_id'];

        $em = EntityManagerFactory::create();
        $filial = $em->find(Filial::class, $filialId);

        if (!$filial) {
            throw new \Exception('Filial inválida');
        }

        $_SESSION['filial_id'] = $filial->getId();

        $this->redirect('/dashboard');
    }
}

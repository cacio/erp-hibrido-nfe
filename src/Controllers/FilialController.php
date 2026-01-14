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
        $user = $em->find(User::class, $_SESSION['auth']['user_id']);

        $filiaisSessao = [];

        foreach ($user->getFiliais() as $filial) {
            $filiaisSessao[] = [
                'id' => $filial->getId(),
                'razao_social' => $filial->getRazaoSocial(),
            ];
        }

        $_SESSION['auth']['filiais'] = $filiaisSessao;

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

        $_SESSION['auth']['filial_id'] = $filial->getId();
        $_SESSION['auth']['filial_nome'] = $filial->getRazaoSocial();

        $this->redirect('/dashboard');
    }
}

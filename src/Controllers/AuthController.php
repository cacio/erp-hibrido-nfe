<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\EntityManagerFactory;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }

        $this->render('auth/login');
    }

    public function login()
    {
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $this->flash('error', 'Informe e-mail e senha');
           $this->redirect('/login');
        }

        $em = EntityManagerFactory::create();
        $user = $em->getRepository(User::class)->findByEmail($email);

        if (!$user) {
            $this->flash('error', 'Usuário não encontrado');

            $this->redirect('/login');

        }

        if (!$user->isAtivo()) {
            $this->flash('error', 'Usuário inativo');
            $this->redirect('/login');
        }

        if (!$user->getTenant()->isAtivo()) {
            $this->flash('error', 'Empresa bloqueada');
            $this->redirect('/login');
        }

        if (!password_verify($password, $user->getSenhaHash())) {
            $this->flash('error', 'Senha inválida');
            $this->redirect('/login');
        }

        // ---------------- LOGIN OK ----------------
        $_SESSION['auth'] = [
            'user_id'     => $user->getId(),
            'user_nome'   => $user->getNome(),
            'tenant_id'   => $user->getTenant()->getId(),
            'tenant_nome' => $user->getTenant()->getNomeGrupo(),
        ];

        // --------- CONTROLE DE FILIAL ---------
        $filiais = $user->getFiliais();

        if ($filiais->isEmpty()) {
            session_destroy();
            $this->flash('error', 'Usuário sem filiais vinculadas');
            $this->redirect('/login');
        }

        if ($filiais->count() === 1) {
            $filial = $filiais->first();
            $_SESSION['auth']['filial_id']   = $filial->getId();
            $_SESSION['auth']['filial_nome'] = $filial->getRazaoSocial();

            $this->redirect('/dashboard');
        }

        // múltiplas filiais → seleção
        $this->flash('info', 'Selecione a filial para continuar');
        $this->redirect('/select-filial');
    }



    public function logout()
    {
        session_destroy();
        $this->redirect('/login');
    }
}

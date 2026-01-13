<?php

namespace App\Controllers;

use App\Core\View;

class UserController
{
    public function __construct()
    {
        // Protege todas as rotas deste controller
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Mostra os detalhes de um usuário.
     * O parâmetro $id é injetado automaticamente pelo nosso despachante.
     *
     * @param string $id O ID do usuário vindo da URL.
     */
    public function show($id)
    {
        // Aqui você buscaria o usuário no banco de dados com o ID recebido
        // $user = UserModel::find($id);

        // Apenas para exemplo:
        echo "<h1>Exibindo detalhes do Usuário ID: " . htmlspecialchars($id) . "</h1>";

        // Em um caso real, você renderizaria uma view:
        // View::render('users/show', ['userId' => $id]);
    }
}

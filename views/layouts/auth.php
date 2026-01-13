<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Login - ERP Híbrido' ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body class="login-body">

    <?= $content ?>

    <script>
        const loginForm = document.getElementById('login-form');
        const forgotPasswordForm = document.getElementById('forgot-password-form');
        const forgotPasswordLink = document.getElementById('forgot-password-link');
        const backToLoginLink = document.getElementById('back-to-login');

        // Alternar para Recuperação de Senha
        forgotPasswordLink.addEventListener('click', (e) => {
            e.preventDefault();
            loginForm.style.display = 'none';
            forgotPasswordForm.style.display = 'block';
        });

        // Voltar para Login
        backToLoginLink.addEventListener('click', (e) => {
            e.preventDefault();
            forgotPasswordForm.style.display = 'none';
            loginForm.style.display = 'block';
        });

        // Simulação de envio
        document.getElementById('recovery-form').addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Link de recuperação enviado para o seu e-mail!');
            forgotPasswordForm.style.display = 'none';
            loginForm.style.display = 'block';
        });
    </script>
</body>

</html>
<?php ob_start(); ?>

<div class="login-container">
    <!-- Formulário de Login -->
    <div class="login-box" id="login-form">
        <div class="login-header">
            <h1>Bem-vindo</h1>
            <p>Acesse sua conta para continuar</p>
        </div>
        <?php if ($error = $this->getFlash('error')): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($info = $this->getFlash('info')): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($info) ?>
            </div>
        <?php endif; ?>
        <form action="/login" method="POST">
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="seu@email.com" required>
            </div>
            <div class="input-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="login-options">
                <label><input type="checkbox"> Lembrar-me</label>
                <a href="#" id="forgot-password-link">Esqueceu a senha?</a>
            </div>
            <button type="submit" class="btn-login">Entrar</button>
        </form>
        <div class="login-footer">
            <p>Não tem uma conta? <a href="register.html">Cadastre-se</a></p>
        </div>
    </div>

    <!-- Formulário de Recuperação de Senha (Oculto por padrão) -->
    <div class="login-box" id="forgot-password-form" style="display: none;">
        <div class="login-header">
            <h1>Recuperar Senha</h1>
            <p>Insira seu e-mail para receber as instruções</p>
        </div>
        <form id="recovery-form">
            <div class="input-group">
                <label for="recovery-email">E-mail cadastrado</label>
                <input type="email" id="recovery-email" placeholder="seu@email.com" required>
            </div>
            <button type="submit" class="btn-login">Enviar Link</button>
        </form>
        <div class="login-footer">
            <p><a href="#" id="back-to-login">Voltar para o login</a></p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Login';
include __DIR__ . '/../layouts/auth.php';
?>
<?php ob_start(); ?>

<div class="login-container">
    <div class="login-box">
        <div class="login-header">
            <div style="width: 60px; height: 60px; background: var(--primary-color); color: white; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; margin: 0 auto 20px;">
                🏢
            </div>
            <h1>Selecionar Unidade</h1>
            <p>Identificamos múltiplas filiais vinculadas à sua conta. Por favor, escolha em qual deseja entrar.</p>
        </div>

        <form method="post" action="/confirmar-filial">
            <div class="input-group">
                <label for="filial_id">Unidade / Filial</label>
                <select name="filial_id" id="filial_id" class="form-control" style="width: 100%; height: 50px; padding: 0 15px;" required>
                    <option value="" disabled selected>Selecione uma filial...</option>
                    <?php foreach ($filiais as $filial): ?>
                        <option value="<?= $filial->getId() ?>">
                            <?= htmlspecialchars($filial->getRazaoSocial()) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>

            <button type="submit" class="btn-login" style="margin-top: 10px;">
                Acessar Unidade
            </button>
        </form>

        <div class="login-footer">
            <p>Deseja sair? <a href="login.html">Voltar para o login</a></p>
        </div>
    </div>

    <div style="text-align: center; margin-top: 30px; color: var(--text-muted); font-size: 12px;">
        &copy; 2026 ERP Moderno. Todos os direitos reservados.
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Filial';
include __DIR__ . '/../layouts/auth.php';
?>
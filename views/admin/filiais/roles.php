<?php ob_start(); ?>
<div class="top-bar-header">
    <div>
        <h2 style="margin-bottom: 5px;">Atribuição de Perfis</h2>
        <p style="color: var(--text-muted); font-size: 14px;">Defina quais perfis este usuário terá nesta unidade específica.</p>
    </div>
    <a href="acessos.html" class="btn btn-outline">← Voltar para Acessos</a>
</div>

<!-- Card de Informações de Contexto -->
<section class="stat-card" style="margin-bottom: 30px; padding: 25px; border-left: 4px solid var(--primary-color);">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
        <div>
            <label class="group-label" style="margin-bottom: 5px;">Usuário</label>
            <div style="display: flex; align-items: center; gap: 10px;">
                <div class="avatar" style="width: 32px; height: 32px; font-size: 12px;">JS</div>
                <strong style="font-size: 16px;"> <?= htmlspecialchars($user->getNome()) ?></strong>
            </div>
        </div>
        <div>
            <label class="group-label" style="margin-bottom: 5px;">Filial / Unidade</label>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px;">🏢</span>
                <strong style="font-size: 16px;"><?= htmlspecialchars($filial->getRazaoSocial()) ?></strong>
            </div>
        </div>
    </div>
</section>
<hr>
<!-- Formulário de Seleção de Roles -->
<section class="stat-card" style="padding: 30px;">
    <h3 class="section-title" style="margin-bottom: 25px;">Perfis Disponíveis</h3>
    <form method="post" action="/admin/filiais/<?= $filial->getId() ?>/usuarios/<?= $user->getId() ?>/roles">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 15px; background: var(--bg-color); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color);">

            <?php foreach ($roles as $role): ?>

                <label class="form-check" style="background: var(--card-bg); padding: 12px 15px; border-radius: 10px; border: 1px solid var(--border-color);">
                    <input type="checkbox" class="form-check-input" name="roles[]" value="<?= $role->getId() ?>" <?= in_array($role->getId(), $userRoles, true) ? 'checked' : '' ?>>
                    <div style="margin-left: 5px;">
                        <span style="font-weight: 600; display: block;"><?= htmlspecialchars($role->getNome()) ?></span>
                        <small style="color: var(--text-muted); font-size: 11px;">descricao</small>
                    </div>
                </label>
            <?php endforeach; ?>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 15px; align-items: center;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 30px;">Salvar Alterações</button>
            <a href="/admin/filiais/<?= $filial->getId() ?>/acessos" class="link-primary" style="font-size: 14px;">Cancelar e voltar</a>
        </div>
    </form>
</section>

<?php
$content = ob_get_clean();
$title = 'Roles do Sistema';
$titletopbar = "Gerenciamento de Perfis (Roles)";
include __DIR__ . '/../../layouts/app.php';
?>
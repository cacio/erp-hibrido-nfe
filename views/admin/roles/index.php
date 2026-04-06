<?php ob_start(); ?>
<?php if ($info = $this->getFlash('info')): ?>
    <div class="alert alert-info">
        <?= htmlspecialchars($info) ?>
    </div>
<?php endif; ?>

<?php if ($error = $this->getFlash('error')): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php if ($success = $this->getFlash('success')): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>
<section class="stat-card" style="margin-bottom: 40px; padding: 30px;">
    <h3 class="section-title" style="margin-bottom: 25px;"><?= $edit ? 'Editar Perfil' : 'Criar Novo Perfil' ?></h3>
    <form method="post" action="<?= $edit ? '/admin/roles/' . $edit->getId() . '/update' : '/admin/roles' ?>">

        <div class="form-group" style="max-width: 400px; margin-bottom: 30px;">
            <label class="form-label">Nome do Perfil</label>
            <input type="text" class="form-control" name="nome" value="<?= $edit ? htmlspecialchars($edit->getNome()) : '' ?>" placeholder="ex: ADMINISTRADOR, GERENTE, VENDEDOR" required>
        </div>

        <div class="form-section">
            <h3 class="section-title" style="font-size: 13px; border-bottom: none; margin-bottom: 15px;">Atribuir Permissões</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; background: var(--bg-color); padding: 20px; border-radius: 12px; border: 1px solid var(--border-color);">

                <?php foreach ($permissions as $p): ?>
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" name="permissions[]" value="<?= $p->getId() ?>"
                            <?= $editPermissionIds ? (in_array($p->getId(), $editPermissionIds, true) ? 'checked' : '') : '' ?>>
                        <?= htmlspecialchars($p->getNome()) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary"><?= $edit ? 'Atualizar Perfil de Acesso' : 'Criar Perfil de Acesso' ?></button>
            <?php if ($edit): ?>
                <a href="/admin/roles" class="btn btn-outline">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>
</section>
<hr>

<h3 class="section-title" style="border-bottom: none; margin-bottom: 20px;">Perfis Cadastrados</h3>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
    <?php foreach ($roles as $role): ?>
        <div class="stat-card" style="padding: 20px; border-top: 4px solid var(--primary-color);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <h4 style="font-size: 18px; font-weight: 700;"><?= htmlspecialchars($role->getNome()) ?></h4>
                <span class="badge badge-info">Total: <?= count($role->getPermissions()) ?></span>
            </div>

            <div style="display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 20px;">
                <?php foreach ($role->getPermissions() as $perm): ?>
                    <span class="badge badge-success" style="font-size: 10px;"><?= htmlspecialchars($perm->getNome()) ?></span>
                <?php endforeach; ?>
            </div>
            <div style="display: flex; gap: 10px; border-top: 1px solid var(--border-color); padding-top: 15px;">
                <a href="/admin/roles/<?= $role->getId() ?>/edit" class="btn btn-ghost" style="font-size: 12px; padding: 5px 10px; flex-grow: 1;">Editar</a>
                <a href="/admin/roles/<?= $role->getId() ?>/delete" onclick="return confirm('Deseja remover esta role?')" class="btn btn-ghost" style="font-size: 12px; padding: 5px 10px; flex-grow: 1; color: #ef4444;">Excluir</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php
$content = ob_get_clean();
$title = 'Roles do Sistema';
$titletopbar = "Gerenciamento de Perfis (Roles)";
include __DIR__ . '/../../layouts/app.php';
?>
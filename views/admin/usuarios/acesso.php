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

<h1>Acesso por Filial</h1>

<p>
    Usuário: <strong><?= htmlspecialchars($user->getNome()) ?></strong>
</p>

<form method="post" action="/admin/usuarios/acesso/salvar">
    <input type="hidden" name="user_id" value="<?= $user->getId() ?>">

    <label>Filial</label>
    <select name="filial_id" required onchange="this.form.submit()">
        <option value="">Selecione</option>
        <?php foreach ($filiais as $f): ?>
            <option value="<?= $f->getId() ?>"
                <?= ($_POST['filial_id'] ?? '') === $f->getId() ? 'selected' : '' ?>>
                <?= htmlspecialchars($f->getRazaoSocial()) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php if (!empty($_POST['filial_id'])): ?>
        <fieldset>
            <legend>Roles</legend>

            <?php
                $userRoles = array_column(
                    (new \App\Services\UserFilialAccessService())
                        ->rolesDoUsuario($user->getId(), $_POST['filial_id']),
                    'id'
                );
            ?>

            <?php foreach ($roles as $r): ?>
                <label style="display:block">
                    <input type="checkbox"
                           name="roles[]"
                           value="<?= $r->getId() ?>"
                           <?= in_array($r->getId(), $userRoles, true) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($r->getNome()) ?>
                </label>
            <?php endforeach; ?>
        </fieldset>

        <button type="submit">Salvar</button>
    <?php endif; ?>
</form>
<?php
$content = ob_get_clean();
$title = 'Acesso por Filial';
$titletopbar = "Gerenciamento de Acesso por Filial";
include __DIR__ . '/../../layouts/app.php';
?>
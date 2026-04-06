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

<?php $edit = $edit ?? null; ?>
<?php
$edit = $edit ?? null;
$userFiliais = [];

if ($edit) {
    foreach ($edit->getFiliais() as $f) {
        $userFiliais[] = $f->getId();
    }
}
?>

<section class="stat-card" style="margin-bottom: 40px; padding: 30px;">
    <h3 class="section-title" style="margin-bottom: 25px;">Novo Usuário</h3>
    <form method="post" action="<?= $edit ? '/admin/usuarios/' . $edit->getId() . '/update' : '/admin/usuarios' ?>">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div class="form-group">
                <label class="form-label">Nome Completo</label>
                <input type="text" class="form-control" name="nome" value="<?= $edit ? $edit->getNome() : '' ?>" placeholder="Ex: João Silva" required>
            </div>
            <div class="form-group">
                <label class="form-label">E-mail</label>
                <input type="email" class="form-control" name="email" value="<?= $edit ? $edit->getEmail() : '' ?>" placeholder="joao@email.com" required>
            </div>
            <div class="form-group">
                <label class="form-label">Senha Inicial</label>
                <input type="password" class="form-control" name="senha" placeholder="••••••••" required>
            </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 10px;">
            <label class="form-switch">
                <label class="switch">
                    <input type="checkbox" name="ativo" <?= $edit && $edit->isAtivo() ? 'checked' : '' ?>>
                    <span class="slider"></span>
                </label>
                <span>Usuário Ativo</span>
            </label>
            <button type="submit" class="btn btn-primary"><?= $edit ? 'Atualizar Usuário' : 'Criar Usuário' ?></button>
        </div>
        <fieldset>
            <legend>Filiais de acesso</legend>

            <?php foreach ($filiais as $f): ?>
                <label style="display:block">
                    <input type="checkbox"
                        name="filiais[]"
                        value="<?= $f->getId() ?>"
                        <?= in_array($f->getId(), $userFiliais, true) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($f->getRazaoSocial()) ?>
                    (<?= $f->getTipoUnidade() ?>)
                </label>
            <?php endforeach; ?>
        </fieldset>

    </form>
</section>

<hr>
<!-- Tabela de Usuários -->
<section class="data-table-container">
    <div class="table-filters">
        <h3 style="font-size: 16px; font-weight: 600;">Lista de Usuários</h3>
        <div class="filter-group">
            <input type="text" class="filter-input" placeholder="Pesquisar usuário...">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>E-mail</th>
                    <th>Filiais / Acessos</th>
                    <th>Status</th>
                    <th style="text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="avatar" style="width: 32px; height: 32px; font-size: 12px;">
                                    <?= strtoupper(substr($u->getNome(), 0, 2)); ?>
                                </div>
                                <strong><?= $u->getNome() ?></strong>
                            </div>
                        </td>
                        <td><?= $u->getEmail() ?></td>
                        <td>
                            <?php if (!empty($accessMap[$u->getId()] ?? [])): ?>
                                <?php foreach ($accessMap[$u->getId()] as $a): ?>
                                    <div style="margin-bottom:4px">
                                        <strong><?= htmlspecialchars($a['razao_social']) ?></strong><br>
                                        <small><?= htmlspecialchars($a['roles']) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <em>Sem acesso definido</em>
                            <?php endif; ?>
                        </td>
                        <td><?= $u->isAtivo() ? '<span class="badge badge-success">Ativo</span>' : '<span class="badge badge-danger">Inativo</span>' ?></td>
                        <td style="text-align: right;">
                            <a href="/admin/usuarios/<?= $u->getId() ?>/edit" class="btn btn-ghost" style="padding: 5px 10px; color: var(--primary-color);">Editar</a>
                            <a href="/admin/usuarios/<?= $u->getId() ?>/reset" class="btn btn-ghost" style="padding: 5px 10px; color: var(--text-muted);">Resetar Senha</a>
                            <a href="/admin/usuarios/<?= $u->getId() ?>/acesso" class="btn btn-ghost" style="padding: 5px 10px; color: var(--text-muted);">Acessos por Filial</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="pagination">
        <div class="pagination-info">Mostrando <strong>2</strong> usuários</div>
        <div class="pagination-buttons">
            <button class="page-link active">1</button>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
$title = 'Usuários';
$titletopbar = "Gerenciamento de Usuários";
include __DIR__ . '/../../layouts/app.php';
?>
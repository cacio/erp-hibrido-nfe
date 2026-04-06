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


<div class="top-bar-header " style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h2 style="margin-bottom: 5px;">Acessos do Usuário</h2>
        <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
            <div class="avatar" style="width: 24px; height: 24px; font-size: 10px;"><?= strtoupper(substr($user->getNome(), 0, 2)); ?></div>
            <span style="font-weight: 600; color: var(--primary-color);"><?= htmlspecialchars($user->getNome()) ?></span>
        </div>
    </div>
    <a href="/admin/usuarios" class="btn btn-outline">← Voltar para Usuários</a>
</div>
<!-- Alertas de Feedback -->
<div class="alert alert-info">
    <span>ℹ️</span>
    <div><strong>Informação:</strong> Este usuário pode alternar entre as filiais listadas abaixo após o login.</div>
</div>

<div style="display: grid; grid-template-columns: 1fr 350px; gap: 30px; align-items: start;">

    <!-- Listagem de Filiais com Acesso -->
    <section class="data-table-container">
        <div class="table-filters">
            <h3 style="font-size: 16px; font-weight: 600;">Unidades com Acesso</h3>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Filial / Unidade</th>
                        <th>Perfis Atribuídos</th>
                        <th style="text-align: right;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($acessos as $a): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="font-size: 18px;">🏢</span>
                                    <strong><?= htmlspecialchars($a['razao_social']) ?></strong>
                                </div>
                            </td>
                            <td>
                                <?php
                                $rolesArray = explode(',', $a['roles']);
                                foreach ($rolesArray as $role):
                                ?>
                                    <span class="badge badge-info"><?= htmlspecialchars(trim($role)) ?></span>
                                <?php
                                endforeach;
                                ?>
                            </td>
                            <td style="text-align: right;">
                                <a href="/admin/filiais/<?= $a['filial_id'] ?>/usuarios/<?= $user->getId() ?>/roles" class="btn btn-ghost" style="padding: 5px 10px; color: var(--primary-color);">Gerenciar Roles</a>
                                <a href="/admin/filiais/<?= $a['filial_id'] ?>/usuarios/<?= $user->getId() ?>/remover" class="btn btn-ghost" style="padding: 5px 10px; color: #ef4444;" onclick="return confirm('Remover acesso?')">Remover</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Formulário de Adição de Filial -->
    <section class="stat-card" style="padding: 25px;">
        <h3 class="section-title" style="margin-bottom: 20px;">Vincular Nova Filial</h3>
        <form method="post" action="/admin/usuarios/<?= $user->getId() ?>/acessos/adicionar">
            <div class="form-group">
                <label class="form-label">Selecionar Filial Disponível</label>
                <select class="form-control" name="filial_id" required>
                    <option value="">Selecione uma unidade...</option>
                    <?php foreach ($filiaisDisponiveis as $f): ?>
                        <option value="<?= $f->getId() ?>">
                            <?= htmlspecialchars($f->getRazaoSocial()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                Adicionar Acesso
            </button>
        </form>
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);">
            <p style="font-size: 12px; color: var(--text-muted); line-height: 1.4;">
                💡 Após adicionar a filial, não esqueça de configurar as <strong>Roles</strong> específicas para que o usuário tenha as permissões corretas naquela unidade.
            </p>
        </div>
    </section>

</div>

<?php
$content = ob_get_clean();
$title = 'Acesso por Filial';
$titletopbar = "Gerenciamento de Acesso por Filial";
include __DIR__ . '/../../layouts/app.php';
?>
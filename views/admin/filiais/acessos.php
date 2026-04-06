<?php ob_start(); ?>

<div class="top-bar-header">
    <div>
        <h2 style="margin-bottom: 5px;">Acessos da Filial</h2>
        <p style="color: var(--primary-color); font-weight: 600; font-size: 18px;">
            🏢 <?= htmlspecialchars($filial->getRazaoSocial()) ?>
        </p>
    </div>
    <a href="/admin/filiais" class="btn btn-outline">← Voltar para Filiais</a>
</div>
<hr style="margin-top: 5px; margin-bottom: 5px;">
<div style="display: grid; grid-template-columns: 1fr 350px; gap: 30px; align-items: start;">

    <!-- Listagem de Usuários com Acesso -->
    <section class="data-table-container">
        <div class="table-filters">
            <h3 style="font-size: 16px; font-weight: 600;">Usuários com Acesso</h3>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Roles (Perfis)</th>
                        <th style="text-align: right;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>

                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="avatar" style="width: 28px; height: 28px; font-size: 10px;"><?= strtoupper(substr($u['nome'], 0, 2)); ?></div>
                                    <strong><?= htmlspecialchars($u['nome']) ?></strong>
                                </div>
                            </td>
                            <td>
                                <?php
                                if ($u['roles']):
                                ?>
                                    <span class="badge badge-info"><?= $u['roles'] ?></span>
                                <?php
                                else:
                                ?>
                                    <em>Sem roles</em>
                                <?php
                                endif
                                ?>

                            </td>
                            <td style="text-align: right;">
                                <a href="/admin/filiais/<?= $filial->getId() ?>/usuarios/<?= $u['id'] ?>/roles" class="btn btn-ghost" style="padding: 5px 10px; color: var(--primary-color);">Gerenciar Roles</a>
                                <a href="/admin/filiais/<?= $filial->getId() ?>/usuarios/<?= $u['id'] ?>/remover" class="btn btn-ghost" style="padding: 5px 10px; color: #ef4444;" onclick="return confirm('Remover acesso?')">Remover</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Formulário de Adição Rápida -->
    <section class="stat-card" style="padding: 25px;">
        <h3 class="section-title" style="margin-bottom: 20px;">Adicionar Usuário</h3>
        <form method="post" action="/admin/filiais/<?= $filial->getId() ?>/usuarios/adicionar">
            <input type="hidden" name="filial_id" value="<?= $filial->getId() ?>">
            <div class="form-group">
                <label class="form-label">Selecionar Usuário Disponível</label>
                <select class="form-control" name="user_id" required>
                    <option value="">Selecione um usuário...</option>
                    <?php foreach ($usuariosDisponiveis as $u): ?>
                        <option value="<?= $u['id'] ?>">
                            <?= htmlspecialchars($u['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                Conceder Acesso
            </button>
        </form>
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);">
            <p style="font-size: 12px; color: var(--text-muted); line-height: 1.4;">
                ℹ️ Ao adicionar um usuário, ele poderá visualizar os dados desta filial. Você deverá configurar as roles logo em seguida.
            </p>
        </div>
    </section>

</div>
<?php
$content = ob_get_clean();
$title = 'Acessos da Filial';
$titletopbar = "Gerenciamento de Acessos da Filial";
include __DIR__ . '/../../layouts/app.php';
?>
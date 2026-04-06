<?php ob_start(); ?>

<div class="alert alert-info">
  <span>ℹ️</span>
  <div><strong>Informação:</strong> Configure as permissões de acesso para cada módulo do sistema.</div>
</div>

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



<section class="stat-card" style="margin-bottom: 30px; padding: 25px;">
  <h3 class="section-title" style="margin-bottom: 20px;"><?= $edit ? 'Editar Permissão' : 'Nova Permissão' ?></h3>
  <form method="post" action="<?= $edit ? '/admin/permissoes/' . $edit->getId() . '/update' : '/admin/permissoes' ?>">
   <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; align-items: flex-end;">
      <div class="form-group" style="margin-bottom: 0;">
        <label class="form-label">Nome da Permissão</label>
        <input type="text" class="form-control" name="nome" placeholder="ex: dashboard.view"
          value="<?= $edit ? htmlspecialchars($edit->getNome()) : '' ?>" required>
      </div>
      <div class="form-group" style="margin-bottom: 0;">
        <label class="form-label">Descrição</label>
        <input type="text" class="form-control" name="descricao" placeholder="O que esta permissão faz?"
          value="<?= $edit ? htmlspecialchars($edit->getDescricao() ?? '') : '' ?>">
      </div>
      <div style="display: flex; gap: 10px;">
        <button type="submit" class="btn btn-primary" style="flex-grow: 1;"><?= $edit ? 'Atualizar' : 'Criar' ?></button>

        <?php if ($edit): ?>
          <a href="/admin/permissoes" class="btn btn-outline">Cancelar</a>
        <?php endif; ?>
      </div>
    </div>
  </form>
</section>

<hr style="margin-top: 5px; margin-bottom: 5px;">
<section class="data-table-container">
  <div class="table-filters">
    <h3 style="font-size: 16px; font-weight: 600;">Permissões Cadastradas</h3>
    <div class="filter-group">
      <input type="text" class="filter-input" placeholder="Buscar permissão...">
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Permissão</th>
          <th>Descrição</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($permissions as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p->getNome()) ?></td>
            <td><?= htmlspecialchars($p->getDescricao() ?? '-') ?></td>
            <td>
              <a href="/admin/permissoes/<?= $p->getId() ?>/edit" class="btn btn-ghost" style="padding: 5px 10px; color: var(--primary-color);">Editar</a>
              |
              <a href="/admin/permissoes/<?= $p->getId() ?>/delete" class="btn btn-ghost" style="padding: 5px 10px; color: #ef4444;" onclick="return confirm('Deseja remover esta permissão?')">
                Remover
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <!-- Paginação Simples -->
  <div class="pagination">
    <div class="pagination-info">Total de <strong>3</strong> permissões</div>
    <div class="pagination-buttons">
      <button class="page-link active">1</button>
    </div>
  </div>
</section>
<?php
$content = ob_get_clean();
$title = 'Permissões do Sistema';
$titletopbar = "Gerenciamento de Permissões";
include __DIR__ . '/../../layouts/app.php';
?>
<?php ob_start(); ?>

<div class="top-bar-header">
    <h1>Ajuste de Estoque</h1>
</div>
<?php if ($info = $this->getFlash('info')): ?>
    <div class="alert alert-info">
        <?= htmlspecialchars($info) ?>
    </div>
<?php endif; ?>

<?php if ($error = $this->getFlash('error')): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php if ($success = $this->getFlash('success')): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>
<form method="post" action="/estoque/ajuste" class="stat-card" style="max-width:600px;">
    <div class="form-group">
        <label class="group-label">Produto</label>
        <select name="produto_id" class="form-control" required>
            <option value="">Selecione</option>
            <?php foreach ($produtos as $p): ?>
                <option value="<?= $p->getId() ?>">
                    <?= htmlspecialchars($p->getDescricao()) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
        <div class="form-group">
            <label class="group-label">Quantidade</label>
            <input type="number" step="0.0001" name="quantidade"
                   class="form-control" required>
        </div>

        <div class="form-group">
            <label class="group-label">Custo Unitário</label>
            <input type="number" step="0.0001" name="custo_unitario"
                   class="form-control" required>
        </div>
    </div>

    <button class="btn btn-primary" style="margin-top:20px;">
        Ajustar Estoque
    </button>
</form>

<?php
$content = ob_get_clean();
$title = 'Ajuste de Estoque';
$titletopbar = 'Ajuste de Estoque';
include __DIR__ . '/../layouts/app.php';

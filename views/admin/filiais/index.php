<?php ob_start(); ?>
<?php $edit = $edit ?? null; ?>

<!-- Card de Formulário (Criar/Editar) -->
<section class="stat-card" style="margin-bottom: 40px; padding: 30px;">
    <h3 class="section-title" style="margin-bottom: 25px;">Nova Unidade</h3>
    <form method="post" action="<?= $edit ? '/admin/filiais/' . $edit->getId() . '/update' : '/admin/filiais' ?>">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div class="form-group">
                <label class="form-label">Tipo de Unidade</label>
                <select class="form-control" name="tipo_unidade" required>
                    <option value="MATRIZ" <?= $edit && $edit->getTipoUnidade() === 'MATRIZ' ? 'selected' : '' ?>>MATRIZ</option>
                    <option value="FILIAL" <?= $edit && $edit->getTipoUnidade() === 'FILIAL' ? 'selected' : '' ?>>FILIAL</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Razão Social</label>
                <input type="text" class="form-control" name="razao_social" placeholder="Nome da Empresa" value="<?= $edit ? $edit->getRazaoSocial() : '' ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">CNPJ</label>
                <input type="text" class="form-control" name="cnpj" placeholder="00.000.000/0000-00" value="<?= $edit ? $edit->getCnpj() : '' ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">UF</label>
                <input type="text" class="form-control" name="uf" placeholder="Ex: SP" maxlength="2" value="<?= $edit ? $edit->getUf() : '' ?>" required>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 10px; justify-content: flex-end;">
            <!-- Botão Cancelar (Apenas no modo edição) -->
            <a href="/admin/filiais" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary"><?= $edit ? 'Atualizar Unidade' : 'Salvar Unidade' ?></button>

        </div>
    </form>
</section>

<!-- Tabela de Filiais -->
<section class="data-table-container">
    <div class="table-filters">
        <h3 style="font-size: 16px; font-weight: 600;">Unidades Cadastradas</h3>
        <div class="filter-group">
            <input type="text" class="filter-input" placeholder="Buscar unidade...">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Razão Social</th>
                    <th>CNPJ</th>
                    <th>UF</th>
                    <th style="text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filiais as $f): ?>
                    <tr>
                        <td><span class="badge badge-info" style="background-color: var(--primary-color); color: white;"><?= $f->getTipoUnidade() ?></span></td>
                        <td><strong><?= htmlspecialchars($f->getRazaoSocial()) ?></strong></td>
                        <td><?= $f->getCnpj() ?></td>
                        <td><?= $f->getUf() ?></td>
                        <td style="text-align: right;">
                            <a href="/admin/filiais/<?= $f->getId() ?>/edit" class="btn btn-ghost" style="padding: 5px 10px; color: var(--primary-color);">Editar</a>
                            <?php if ($f->getTipoUnidade() !== 'MATRIZ'): ?>
                                <a href="/admin/filiais/<?= $f->getId() ?>/delete"" class=" btn btn-ghost" style="padding: 5px 10px; color: #ef4444;" onclick="return confirm('Remover?')">Remover</a>
                            <?php endif; ?>
                            <a href="/admin/filiais/<?= $f->getId() ?>/acessos" class="btn btn-ghost" style="padding: 5px 10px; color: var(--text-muted);">Acessos</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="pagination">
        <div class="pagination-info">Total de <strong>2</strong> unidades</div>
        <div class="pagination-buttons">
            <button class="page-link active">1</button>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
$title = 'Filiais';
$titletopbar = "Gerenciamento de Filiais";
include __DIR__ . '/../../layouts/app.php';
?>
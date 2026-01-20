<?php ob_start(); ?>
<div class="top-bar-header" style="margin-bottom: 20px;">
    <div>
        <h1>Produtos</h1>
        <p style="font-size: 13px; color: var(--text-muted);">
            Cadastro de produtos comerciais e fiscais
        </p>
    </div>
    <div class="top-bar-actions">
        <a href="/produtos/create" class="btn btn-primary">+ Novo Produto</a>
    </div>
</div>
<div class="stat-card" style="margin-bottom: 20px;">
    <form method="get" action="/produtos"
        style="display:grid; grid-template-columns:1fr 160px 160px auto; gap:10px;">

        <input type="text" name="q" class="form-control"
            placeholder="Buscar por descrição, SKU ou GTIN"
            value="<?= htmlspecialchars($filtros['q']) ?>">

        <select name="unidade" class="form-control">
            <option value="">Unidade</option>
            <?php foreach (['UN', 'KG', 'PC', 'CX'] as $u): ?>
                <option value="<?= $u ?>"
                    <?= $filtros['unidade'] === $u ? 'selected' : '' ?>>
                    <?= $u ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="ativo" class="form-control">
            <option value="">Status</option>
            <option value="1" <?= $filtros['ativo'] === '1' ? 'selected' : '' ?>>Ativo</option>
            <option value="0" <?= $filtros['ativo'] === '0' ? 'selected' : '' ?>>Inativo</option>
        </select>

        <button class="btn btn-secondary">Filtrar</button>
    </form>

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
<section class="data-table-container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>SKU</th>
                    <th>GTIN</th>
                    <th>Unidade</th>
                    <th align="right">Preço</th>
                    <th>Status</th>
                    <th width="100">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($produtos)): ?>
                    <tr>
                        <td colspan="7" style="padding: 30px; text-align: center; color: var(--text-muted);">
                            Nenhum produto cadastrado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($produtos as $p): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p->getDescricao()) ?></strong></td>
                            <td>
                                <?= htmlspecialchars($p->getCodigoSku() ?? '-') ?>
                            </td>
                            <td align="center"><?= htmlspecialchars($p->getGtinEan() ?? '-') ?></td>
                            <td align="center">
                                <?= $p->getUnidade() ?>
                            </td>
                            <td align="right">
                                R$ <?= number_format($p->getPrecoVenda(), 2, ',', '.') ?>
                            </td>
                            <td align="center">
                                <?php if ($p->isAtivo()): ?>
                                    <span class="badge badge-success">Ativo</span>
                                <?php else: ?>
                                    <span style="color: red;">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td align="center">
                                <a href="/produtos/<?= $p->getId() ?>/edit" class="btn btn-ghost">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

            </tbody>
        </table>
    </div>

    <?php
    $paginaAtual = $paginacao['pagina'];
    $totalPaginas = $paginacao['totalPages'];
    $inicio = (($paginaAtual - 1) * $paginacao['limite']) + 1;
    $fim = min($inicio + $paginacao['limite'] - 1, $paginacao['total']);
    ?>

    <?php if ($totalPaginas > 1): ?>
        <div class="pagination">
            <div class="pagination-info">
                Mostrando <?= $inicio ?>-<?= $fim ?>
                de <?= $paginacao['total'] ?> produtos
            </div>

            <div class="pagination-buttons">
                <?php if ($paginaAtual > 1): ?>
                    <a class="page-btn page-link"
                        href="?page=<?= $paginaAtual - 1 ?>&<?= http_build_query($filtros) ?>">
                        Anterior
                    </a>
                <?php else: ?>
                    <button class="page-btn page-link" disabled>Anterior</button>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <?php if ($i == $paginaAtual): ?>
                        <button class="page-btn active page-link"><?= $i ?></button>
                    <?php else: ?>
                        <a class="page-btn page-link"
                            href="?page=<?= $i ?>&<?= http_build_query($filtros) ?>">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($paginaAtual < $totalPaginas): ?>
                    <a class="page-btn page-link"
                        href="?page=<?= $paginaAtual + 1 ?>&<?= http_build_query($filtros) ?>">
                        Próximo
                    </a>
                <?php else: ?>
                    <button class="page-btn page-link" disabled>Próximo</button>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</section>

<?php
$content = ob_get_clean();
$title = 'Produtos - Visão Geral';
$titletopbar = "Produtos";
include __DIR__ . '/../layouts/app.php';
?>
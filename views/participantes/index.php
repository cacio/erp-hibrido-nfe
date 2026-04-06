<?php ob_start(); ?>


<div class="top-bar-header" style="margin-bottom: 20px;">
    <h1>Participantes</h1>
    <div class="top-bar-actions">
        <a href="/participantes/create" class="btn btn-primary">+ Novo Participante</a>
    </div>
</div>

<!-- ================= BUSCA ================= -->
<form method="GET" style="margin-bottom: 15px;">
    <section class="data-table-container">
        <div class="table-filters">
            <div style="display: flex; gap: 10px; flex-grow: 1; flex-wrap: wrap;">
                <input type="text" name="q" class="filter-input" placeholder="Buscar por nome, CPF/CNPJ ou e-mail..." style="flex-grow: 1; min-width: 200px;">
                <select name="tipo" class="filter-input" style="width: 180px;">
                    <option value="">Todos os Tipos</option>
                    <option value="CLIENTE" <?= $filtros['tipo'] === 'CLIENTE' ? 'selected' : '' ?>>Cliente</option>
                    <option value="FORNECEDOR" <?= $filtros['tipo'] === 'FORNECEDOR' ? 'selected' : '' ?>>Fornecedor</option>
                    <option value="TRANSPORTADORA" <?= $filtros['tipo'] === 'TRANSPORTADORA' ? 'selected' : '' ?>>Transportadora</option>
                </select>
                <select name="ativo" class="filter-input" style="width: 120px;">
                    <option value="">Status</option>
                    <option value="1" <?= $filtros['ativo'] == 1 ? 'selected' : '' ?>>Ativos</option>
                    <option value="0" <?= $filtros['ativo'] == 0 ? 'selected' : '' ?>>Inativos</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
        </div>
    </section>
</form>

<!-- ================= TABELA ================= -->
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Nome / Razão Social</th>
                <th>CPF / CNPJ</th>
                <th>Tipo</th>
                <th>Contato</th>
                <th>Status</th>
                <th width="120">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($participantes)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Nenhum registro encontrado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($participantes as $p): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 600;"><?= htmlspecialchars($p->getNomeRazao()) ?></div>
                            <div style="font-size: 11px; color: var(--text-muted);"><?= htmlspecialchars($p->getNomeFantasia() ?? '') ?></div>
                        </td>
                        <td><?= $p->getCpfCnpj() ?: '-' ?></td>
                        <td>
                            <div style="display: flex; gap: 4px;">
                                <?php foreach ($p->getTipoCadastro() as $tipo): ?>
                                    <span class="badge badge-info"><?= ucfirst(strtolower($tipo)) ?></span><br>
                                <?php endforeach; ?>
                            </div>

                        </td>
                        <td>
                            <div style="font-size: 12px;"><?= htmlspecialchars($p->getEmail() ?: '-') ?></div>
                            <div style="font-size: 11px; color: var(--text-muted);"><?= htmlspecialchars($p->getTelefone() ?: '-') ?></div>
                        </td>
                        <td>
                            <?= $p->isAtivo()
                                ? '<span class="badge badge-success">Ativo</span>'
                                : '<span class="badge badge-red">Inativo</span>' ?>
                        </td>
                        <td>
                            <a href="/participantes/<?= $p->getId() ?>/edit" class="btn btn-ghost" style="padding: 5px 10px;">Editar</a>
                            |
                            <a href="#" class="btn btn-ghost" style="padding: 5px 10px; color: #ef4444;">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<!-- ================= PAGINAÇÃO ================= -->
<?php
$paginaAtual = $paginacao['pagina'];
$totalPaginas = $paginacao['totalPages'];
$inicio = (($paginaAtual - 1) * $paginacao['limite']) + 1;
$fim = min(
    $inicio + $paginacao['limite'] - 1,
    $paginacao['total']
);
?>

<?php if ($totalPaginas > 0): ?>

    <div class="pagination">
        <div class="pagination-info">
            Mostrando <?= $inicio ?>-<?= $fim ?>
            de <?= $paginacao['total'] ?> participantes
        </div>

        <div class="pagination-buttons">

            <!-- ANTERIOR -->
            <?php if ($paginaAtual > 1): ?>
                <a class="page-btn page-link"
                    href="?page=<?= $paginaAtual - 1 ?>
               &q=<?= urlencode($filtros['q']) ?>
               &tipo=<?= urlencode($filtros['tipo']) ?>">
                    Anterior
                </a>
            <?php else: ?>
                <button class="page-btn page-link" disabled>Anterior</button>
            <?php endif; ?>

            <!-- NÚMEROS -->
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <?php if ($i == $paginaAtual): ?>
                    <button class="page-btn page-link active"><?= $i ?></button>
                <?php else: ?>
                    <a class="page-btn page-link"
                        href="?page=<?= $i ?>
                   &q=<?= urlencode($filtros['q']) ?>
                   &tipo=<?= urlencode($filtros['tipo']) ?>">
                        <?= $i ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>

            <!-- PRÓXIMO -->
            <?php if ($paginaAtual < $totalPaginas): ?>
                <a class="page-btn page-link"
                    href="?page=<?= $paginaAtual + 1 ?>
               &q=<?= urlencode($filtros['q']) ?>
               &tipo=<?= urlencode($filtros['tipo']) ?>">
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
$title = 'Participantes - Visão Geral';
$titletopbar = "Participantes";
include __DIR__ . '/../layouts/app.php';
?>
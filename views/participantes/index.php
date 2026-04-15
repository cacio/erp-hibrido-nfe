<?php ob_start(); ?>
<?php

use App\Helpers\MaskHelper;
?>
<div class="content-header">
    <div class="filter-input-wrapper">
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="search-input" placeholder="Buscar participante por nome, CNPJ, etc.">
            <button class="btn btn-primary">Buscar</button>
        </div>
        <div class="filter-wrapper">
            <button id="filter-btn" class="btn btn-ghost"><i class="bx bx-menu-filter bx-sm"></i></button>
            <div id="filter-dropdown" style="display: none;"></div>
        </div>
        <div class="filtroavancado">
            <a href="#" class="btn btn-info" id="filtro-avancado-link" onclick="openModal('modal-filtroavancado')"><i class="bx bx-filter bx-tada"> </i> Filtro Avançado</a>
        </div>
    </div>
    <div class="filter-actions">
        <button id="btn-excel" class="btn btn-secondary" title="Exportar para Excel"><i class="fas fa-file-excel"></i> Excel</button>
        <button id="btn-pdf" class="btn btn-secondary" title="Exportar para PDF"><i class="fas fa-file-pdf"></i> PDF</button>
        <button id="btn-print" class="btn btn-secondary" title="Imprimir"><i class="fas fa-print"></i> Imprimir</button>
        <button id="btn-delete" class="btn btn-secondary" title="Excluir Selecionados" style="display: none;"><i class="fas fa-trash-alt"></i> Excluir</button>
        <a href="/participantes/create" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Participante</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Participantes</h2>
        <div style="display: flex; gap: 8px; position: relative;">
            <div class="table-controls">
                <div class="export-buttons">
                    <div class="column-selector">
                        <button id="clear-filters">Limpar Filtros</button>
                        <button class="btn-table" id="col-toggle-btn">
                            <i class="bx bx-table-list bx-tada"></i> Colunas <i class="fas fa-chevron-down" style="font-size: 9px;"></i>
                        </button>
                        <div id="column-menu" class="column-menu" style="display: none;">
                            <input type="text" id="column-search" placeholder="Search">

                            <div id="column-list"></div>

                            <div class="column-footer">
                                <span id="clear-columns">Clear</span>
                                <button id="apply-columns">Done</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="filters-topbar">
            <div id="active-filters"></div>
        </div>
        <div class="table-responsive">

            <table class="tabulator-table" id="participantes-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="select-all-participantes" style="cursor: pointer;">
                        </th>
                        <th>Nome / Razão Social</th>
                        <th>CPF / CNPJ</th>
                        <th>Tipo</th>
                        <th>Contato</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($participantes)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Nenhum registro encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($participantes as $p): ?>
                            <tr data-id="<?= $p->getId() ?>" class="participante-row">
                                <td style="text-align: center;">
                                    <input type="checkbox" class="participante-checkbox" value="<?= $p->getId() ?>" style="cursor: pointer;">
                                </td>
                                <td>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($p->getNomeRazao()) ?></div>
                                    <div style="font-size: 11px; color: var(--text-muted);"><?= htmlspecialchars($p->getNomeFantasia() ?? '') ?></div>
                                </td>
                                <td><?= MaskHelper::cpfCnpj($p->getCpfCnpj()) ?: '-' ?></td>
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

                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Barra de Ações em Massa -->
<div class="bulk-actions-bar" id="bulk-bar">
    <div style="font-size: 14px; font-weight: 500;">
        <span id="selected-count">0</span> selecionados
    </div>
    <div class="bulk-actions-buttons">
        <button class="btn btn-sm"><i class="fas fa-file-export"></i> Exportar Selecionados</button>
        <button class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> Excluir em Massa</button>
    </div>
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

<div id="modal-filtroavancado" class="modal-overlay">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h2>Filtro Avançado</h2>
            <button class="modal-close" onclick="closeModal('modal-filtroavancado')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="GET" id="form-filtro-avancado" style="margin-bottom: 15px;">
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="input-group">
                            <label for="filter-tipo">Tipo de Participante</label>
                            <select name="tipo" id="filter-tipo" class="form-control">
                                <option value="">Todos os Tipos</option>
                                <option value="CLIENTE" <?= $filtros['tipo'] === 'CLIENTE' ? 'selected' : '' ?>>Cliente</option>
                                <option value="FORNECEDOR" <?= $filtros['tipo'] === 'FORNECEDOR' ? 'selected' : '' ?>>Fornecedor</option>
                                <option value="TRANSPORTADORA" <?= $filtros['tipo'] === 'TRANSPORTADORA' ? 'selected' : '' ?>>Transportadora</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="filter-nome">Nome/Razão Social</label>
                            <input type="text" name="nome_razao" class="form-control" id="filter-nome" placeholder="Digite o nome">
                        </div>
                        <div class="input-group">
                            <label for="filter-documento">CPF/CNPJ</label>
                            <input type="text" name="filter-documento" class="form-control" id="filter-documento" placeholder="Digite o documento">
                        </div>

                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="submit" onclick="document.getElementById('form-filtro-avancado').submit()" class="btn btn-primary">Aplicar Filtros</button>
            <button type="reset" onclick="document.getElementById('form-filtro-avancado').reset()" class="btn btn-secondary">Limpar Filtros</button>
            <button class="btn btn-outline" onclick="closeModal('modal-filtroavancado')">Cancelar</button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Participantes - Visão Geral';
$titletopbar = "Participantes";
include __DIR__ . '/../layouts/app.php';
?>
<script src="/js/participantes.js?v=1.4.6"></script>
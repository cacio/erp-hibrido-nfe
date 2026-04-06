<?php
ob_start();

/** @var \App\Models\Produto $produto */
?>

<div class="top-bar-header">
    <div style="display:flex; align-items:center; gap:15px;">
        <a href="/produtos" class="btn btn-ghost">←</a>
        <h1>Editar Produto</h1>
    </div>

    <div class="top-bar-actions">
        <button type="button"
            onclick="document.getElementById('form-produto').submit()"
            class="btn btn-primary">
            Salvar Alterações
        </button>
    </div>
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
<form id="form-produto"
    method="post"
    action="/produtos/<?= $produto->getId() ?>">

    <div style="display:grid; grid-template-columns:1fr 360px; gap:25px;">

        <!-- ================= COLUNA PRINCIPAL ================= -->
        <div style="display:flex; flex-direction:column; gap:25px;">

            <!-- IDENTIFICAÇÃO -->
            <section class="stat-card">
                <h2 class="panel-title">Identificação</h2>

                <div class="form-group">
                    <label class="group-label">Descrição do Produto *</label>
                    <input type="text"
                        name="descricao"
                        class="form-control"
                        required
                        value="<?= htmlspecialchars($produto->getDescricao()) ?>">
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label class="group-label">Código SKU</label>
                        <input type="text"
                            name="codigo_sku"
                            class="form-control"
                            value="<?= htmlspecialchars($produto->getCodigoSku() ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="group-label">GTIN / EAN</label>
                        <input type="text"
                            name="gtin_ean"
                            class="form-control"
                            value="<?= htmlspecialchars($produto->getGtinEan() ?? '') ?>">
                    </div>
                </div>
            </section>

            <!-- DADOS FISCAIS -->
            <section class="stat-card">
                <h2 class="panel-title">Dados Fiscais</h2>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label class="group-label">NCM</label>
                        <input type="text"
                            name="ncm"
                            class="form-control"
                            value="<?= htmlspecialchars($produto->getNcm() ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="group-label">CEST</label>
                        <input type="text"
                            name="cest"
                            class="form-control"
                            value="<?= htmlspecialchars($produto->getCest() ?? '') ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-top:15px;">
                    <label class="group-label">Tipo do Item (SPED)</label>
                    <select name="tipo_item_sped" class="form-control">
                        <option value="00" <?= $produto->getTipoItemSped() === '00' ? 'selected' : '' ?>>
                            00 - Mercadoria para Revenda
                        </option>
                        <option value="01" <?= $produto->getTipoItemSped() === '01' ? 'selected' : '' ?>>
                            01 - Matéria-prima
                        </option>
                        <option value="04" <?= $produto->getTipoItemSped() === '04' ? 'selected' : '' ?>>
                            04 - Produto Acabado
                        </option>
                    </select>
                </div>
            </section>

            <!-- PESOS -->
            <section class="stat-card">
                <h2 class="panel-title">Pesos</h2>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label class="group-label">Peso Líquido (KG)</label>
                        <input type="number" step="0.0001"
                            name="peso_liquido"
                            class="form-control"
                            value="<?= number_format($produto->getPesoLiquido(), 4, '.', '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="group-label">Peso Bruto (KG)</label>
                        <input type="number" step="0.0001"
                            name="peso_bruto"
                            class="form-control"
                            value="<?= number_format($produto->getPesoBruto(), 4, '.', '') ?>">
                    </div>
                </div>
            </section>
        </div>

        <!-- ================= COLUNA LATERAL ================= -->
        <div style="display:flex; flex-direction:column; gap:25px;">

            <!-- UNIDADE -->
            <section class="stat-card">
                <h2 class="panel-title">Unidade</h2>

                <div class="form-group">
                    <label class="group-label">Unidade de Medida</label>
                    <select name="unidade" class="form-control">
                        <?php foreach (['UN', 'KG', 'PC', 'CX'] as $u): ?>
                            <option value="<?= $u ?>"
                                <?= $produto->getUnidade() === $u ? 'selected' : '' ?>>
                                <?= $u ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </section>

            <!-- PREÇOS -->
            <section class="stat-card">
                <h2 class="panel-title">Preços</h2>

                <div class="form-group">
                    <label class="group-label">Preço de Venda</label>
                    <input type="number" step="0.01"
                        name="preco_venda"
                        class="form-control"
                        value="<?= number_format($produto->getPrecoVenda(), 2, '.', '') ?>">
                </div>

                <div class="form-group" style="margin-top:10px;">
                    <label class="group-label">Preço de Custo</label>
                    <input type="number" step="0.0001"
                        name="preco_custo"
                        class="form-control"
                        value="<?= number_format($produto->getPrecoCusto(), 4, '.', '') ?>">
                </div>
            </section>

            <!-- STATUS -->
            <section class="stat-card">
                <h2 class="panel-title">Status</h2>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span>Produto Ativo</span>
                    <label class="switch">
                        <input type="checkbox"
                            name="ativo"
                            value="1"
                            <?= $produto->isAtivo() ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>
            </section>

        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
$title = 'Editar Produto';
$titletopbar = 'Editar Produto';
include __DIR__ . '/../layouts/app.php';

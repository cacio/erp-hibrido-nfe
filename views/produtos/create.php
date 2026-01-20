<?php ob_start(); ?>

<div class="top-bar-header">
    <div style="display:flex; align-items:center; gap:15px;">
        <a href="/produtos" class="btn btn-ghost">←</a>
        <h1>Novo Produto</h1>
    </div>

    <div class="top-bar-actions">
        <button type="button"
                onclick="document.getElementById('form-produto').submit()"
                class="btn btn-primary">
            Salvar Produto
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
<form id="form-produto" method="post" action="/produtos">
    <div style="display:grid; grid-template-columns:1fr 360px; gap:25px;">

        <!-- ================= COLUNA PRINCIPAL ================= -->
        <div style="display:flex; flex-direction:column; gap:25px;">

            <!-- IDENTIFICAÇÃO -->
            <section class="stat-card">
                <h2 class="panel-title">Identificação</h2>

                <div class="form-group">
                    <label class="group-label">Descrição do Produto *</label>
                    <input type="text" name="descricao"
                           class="form-control"
                           required
                           placeholder="Ex: Carne Bovina Dianteiro">
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label class="group-label">Código SKU</label>
                        <input type="text" name="codigo_sku"
                               class="form-control"
                               placeholder="Código interno">
                    </div>

                    <div class="form-group">
                        <label class="group-label">GTIN / EAN</label>
                        <input type="text" name="gtin_ean"
                               class="form-control"
                               placeholder="Código de barras">
                    </div>
                </div>
            </section>

            <!-- DADOS FISCAIS -->
            <section class="stat-card">
                <h2 class="panel-title">Dados Fiscais</h2>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label class="group-label">NCM</label>
                        <input type="text" name="ncm"
                               class="form-control"
                               placeholder="8 dígitos">
                    </div>

                    <div class="form-group">
                        <label class="group-label">CEST</label>
                        <input type="text" name="cest"
                               class="form-control"
                               placeholder="Opcional">
                    </div>
                </div>

                <div class="form-group" style="margin-top:15px;">
                    <label class="group-label">Tipo do Item (SPED)</label>
                    <select name="tipo_item_sped" class="form-control">
                        <option value="00">00 - Mercadoria para Revenda</option>
                        <option value="01">01 - Matéria-prima</option>
                        <option value="04">04 - Produto Acabado</option>
                    </select>
                </div>
            </section>

            <!-- PESO -->
            <section class="stat-card">
                <h2 class="panel-title">Pesos</h2>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label class="group-label">Peso Líquido (KG)</label>
                        <input type="number" step="0.0001"
                               name="peso_liquido"
                               class="form-control"
                               value="0">
                    </div>

                    <div class="form-group">
                        <label class="group-label">Peso Bruto (KG)</label>
                        <input type="number" step="0.0001"
                               name="peso_bruto"
                               class="form-control"
                               value="0">
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
                        <option value="UN">Unidade (UN)</option>
                        <option value="KG">Quilo (KG)</option>
                        <option value="PC">Peça (PC)</option>
                        <option value="CX">Caixa (CX)</option>
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
                           value="0.00">
                </div>

                <div class="form-group" style="margin-top:10px;">
                    <label class="group-label">Preço de Custo</label>
                    <input type="number" step="0.0001"
                           name="preco_custo"
                           class="form-control"
                           value="0.0000">
                </div>
            </section>

            <!-- STATUS -->
            <section class="stat-card">
                <h2 class="panel-title">Status</h2>

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span>Produto Ativo</span>
                    <label class="switch">
                        <input type="checkbox" name="ativo" value="1" checked>
                        <span class="slider"></span>
                    </label>
                </div>
            </section>

        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
$title = 'Novo Produto';
$titletopbar = 'Novo Produto';
include __DIR__ . '/../layouts/app.php';

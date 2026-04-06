<?php ob_start(); ?>

<div class="top-bar-header">
    <h1>Kardex – Movimentações de Estoque</h1>
</div>

<section class="stat-card" style="margin-bottom:20px;">
    <form method="get" style="display:grid; grid-template-columns:2fr 120px 140px 140px 140px auto; gap:10px;">
        <select name="produto_id" class="form-control">
            <option value="">Produto</option>
            <?php foreach ($produtos as $p): ?>
                <option value="<?= $p->getId() ?>"
                    <?= $filtros['produto_id'] === $p->getId() ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p->getDescricao()) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="tipo" class="form-control">
            <option value="">Tipo</option>
            <option value="ENTRADA" <?= $filtros['tipo']==='ENTRADA'?'selected':'' ?>>Entrada</option>
            <option value="SAIDA"   <?= $filtros['tipo']==='SAIDA'?'selected':'' ?>>Saída</option>
        </select>

        <select name="origem" class="form-control">
            <option value="">Origem</option>
            <?php foreach (['COMPRA','VENDA','PRODUCAO','TRANSFERENCIA','AJUSTE','DEVOLUCAO'] as $o): ?>
                <option value="<?= $o ?>" <?= $filtros['origem']===$o?'selected':'' ?>><?= $o ?></option>
            <?php endforeach; ?>
        </select>

        <input type="date" name="data_ini" class="form-control" value="<?= htmlspecialchars($filtros['data_ini']) ?>">
        <input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($filtros['data_fim']) ?>">

        <button class="btn btn-secondary">Filtrar</button>
    </form>
</section>

<section class="stat-card">
    <table width="100%" cellpadding="8">
        <thead>
            <tr>
                <th>Data</th>
                <th>Produto</th>
                <th>Tipo</th>
                <th>Origem</th>
                <th align="right">Qtd</th>
                <th align="right">Custo Unit.</th>
                <th align="right">Saldo Ant.</th>
                <th align="right">Saldo Pós</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($dados)): ?>
            <tr>
                <td colspan="8" align="center" style="padding:30px;">
                    Nenhuma movimentação encontrada.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($dados as $row):
                $m = $row['mov'];
                $p = $row['produto'];
            ?>
            <tr>
                <td><?= $m->getDataMov()->format('d/m/Y H:i') ?></td>
                <td><?= htmlspecialchars($p?->getDescricao() ?? 'Produto removido') ?></td>
                <td><?= $m->getTipo() ?></td>
                <td><?= $m->getOrigem() ?></td>
                <td align="right"><?= number_format($m->getQuantidade(), 4, ',', '.') ?></td>
                <td align="right"><?= number_format($m->getCustoUnitario(), 4, ',', '.') ?></td>
                <td align="right"><?= number_format($m->getSaldoAnterior(), 4, ',', '.') ?></td>
                <td align="right"><?= number_format($m->getSaldoPosterior(), 4, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</section>

<?php
$content = ob_get_clean();
$title = 'Kardex';
$titletopbar = 'Kardex';
include __DIR__ . '/../layouts/app.php';

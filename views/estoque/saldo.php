<?php ob_start(); ?>

<div class="top-bar-header">
    <h1>Saldo de Estoque</h1>
</div>

<section class="stat-card" style="margin-bottom:20px;">
    <form method="get" style="display:flex; gap:10px;">
        <input type="text"
            name="produto"
            class="form-control"
            placeholder="Buscar produto"
            value="<?= htmlspecialchars($filtros['produto']) ?>">

        <button class="btn btn-secondary">Buscar</button>
    </form>
</section>

<section class="stat-card">
    <table width="100%" cellpadding="8">
        <thead>
            <tr>
                <th align="left">Produto</th>
                <th align="center">Quantidade</th>
                <th align="right">Custo Médio</th>
                <th align="center">Reservado</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($dados)): ?>
                <tr>
                    <td colspan="4" align="center" style="padding:30px;">
                        Nenhum saldo encontrado.
                    </td>
                </tr>
            <?php else: ?>
                <<?php foreach ($dados as $row): ?>
                    <tr>
                    <td><?= htmlspecialchars($row['produto']->getDescricao()) ?></td>

                    <td align="center">
                        <?= number_format($row['saldo']->getQuantidade(), 4, ',', '.') ?>
                    </td>

                    <td align="right">
                        R$ <?= number_format($row['saldo']->getCustoMedio(), 4, ',', '.') ?>
                    </td>

                    <td align="center">
                        <?= number_format($row['saldo']->getReservado(), 4, ',', '.') ?>
                    </td>
                    </tr>
                <?php endforeach; ?>

            <?php endif; ?>
        </tbody>
    </table>
</section>

<?php
$content = ob_get_clean();
$title = 'Saldo de Estoque';
$titletopbar = 'Saldo de Estoque';
include __DIR__ . '/../layouts/app.php';

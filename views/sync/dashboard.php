<?php ob_start(); ?>
<h1>Dashboard de Sincronização</h1>

<div class="cards">
    <div class="card">🔄 Pendentes: <?= $cards['pendentes'] ?></div>
    <div class="card">⚙️ Processando: <?= $cards['processando'] ?></div>
    <div class="card">❌ Erros: <?= $cards['erro'] ?></div>
    <div class="card">✅ Hoje: <?= $cards['sucesso_hoje'] ?></div>
</div>

<table class="table table-hover">
    <thead>
        <tr>
            <th>Data</th>
            <th>Filial</th>
            <th>Tabela</th>
            <th>Operação</th>
            <th>Direção</th>
            <th>Status</th>
            <th>Tent.</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lista as $l): ?>
            <tr>
                <td><?= $l['created_at'] ?></td>
                <td><?= $l['filial_nome'] ?></td>
                <td><?= $l['tabela'] ?></td>
                <td><?= $l['operacao'] ?></td>
                <td><?= $l['direcao'] ?></td>
                <td><?= $l['status'] ?></td>
                <td><?= $l['tentativas'] ?></td>
                <td>
                    <button class="btn btn-sm btn-info"
                        onclick="openPayloadModal('<?= $l['id'] ?>')">
                        👁 Ver
                    </button>
                    <?php if ($l['status'] === 'ERRO'): ?>
                        <form method="post" action="/sync/reprocessar" style="display:inline">
                            <input type="hidden" name="id" value="<?= $l['id'] ?>">
                            <button class="btn btn-warning btn-sm"
                                onclick="return confirm('Reprocessar este registro?')">
                                🔁 Reprocessar
                            </button>
                        </form>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<script>
    function openPayloadModal(id) {
        fetch('/sync/payload?id=' + id)
            .then(r => r.json())
            .then(data => {
                const pre = document.getElementById('payload-content');

                if (data.payload) {
                    pre.textContent = JSON.stringify(data.payload, null, 2);
                } else {
                    pre.textContent = 'Sem payload disponível.';
                }

                openModal('payload-modal');
            });
    }


    function copyPayload() {
        const text = document.getElementById('payload-content').textContent;
        navigator.clipboard.writeText(text);
        alert('Payload copiado!');
    }
</script>


<?php
$content = ob_get_clean();
$title = 'Dashboard de Sincronização';
$titletopbar = "Dashboard de Sincronização";
include __DIR__ . '/../layouts/app.php';
?>
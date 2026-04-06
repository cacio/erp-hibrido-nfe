<?php ob_start(); ?>

<div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
    <a href="/financeiro" class="btn btn-outline">⬅️ Voltar</a>
    <h1 style="font-size: 20px; font-weight: 700;">
        ✏️ Editar Lançamento Financeiro
    </h1>
</div>

<?php if ($error = $this->getFlash('error')): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php
[$numParcela, $totalParcelas] = explode('/', $titulo->getParcela());
$isParcelado = ((int)$totalParcelas) > 1;
?>

<?php if ($isParcelado): ?>
    <div class="alert alert-warning">
        Este título faz parte de um documento parcelado.
        O valor não pode ser alterado.
    </div>
<?php endif; ?>

<form method="POST" action="/financeiro/<?= $titulo->getId() ?>/update">

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">

        <!-- Coluna principal -->
        <div style="display: flex; flex-direction: column; gap: 25px;">

            <!-- Dados do título -->
            <div class="stat-card">
                <h3 class="section-title">Dados do Título</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

                    <!-- Tipo -->
                    <div class="input-group">
                        <label>Tipo</label>
                        <input type="text"
                            class="form-control"
                            value="<?= $titulo->getTipo() ?>"
                            disabled>
                    </div>

                    <!-- Documento -->
                    <div class="input-group">
                        <label>Número do Documento</label>
                        <input type="text"
                            class="form-control"
                            value="<?= htmlspecialchars($titulo->getNumeroDocumento()) ?>"
                            disabled>
                    </div>

                    <!-- Parcela -->
                    <div class="input-group">
                        <label>Parcela</label>
                        <input type="text"
                            class="form-control"
                            value="<?= $titulo->getParcela() ?>"
                            disabled>
                    </div>

                    <!-- Participante -->

                    <div class="input-group">
                        <label>Participante (Cliente / Fornecedor)</label>
                        <?php
                        $participanteNome = $titulo->getParticipante() ? htmlspecialchars($titulo->getParticipante()->getNomeRazao()) : '';

                        //var_dump($titulo->getParticipante()->getNomeRazao());
                        ?>
                        <input
                            type="text"
                            id="participante_busca"
                            placeholder="Digite nome ou CPF/CNPJ"
                            autocomplete="off"
                            class="form-control" disabled value="<?= $participanteNome ?>">

                        <input type="hidden" name="participante_id" id="participante_id" value="<?= $titulo->getParticipanteId() ?>">

                        <div id="participante_resultados" class="autocomplete-box"></div>
                    </div>

                    <!-- Data de vencimento -->
                    <div class="input-group">
                        <label>Data de vencimento</label>
                        <input type="date"
                            name="data_vencimento"
                            class="form-control"
                            value="<?= $titulo->getDataVencimento()->format('Y-m-d') ?>"
                            required>
                    </div>

                    <!-- Valor -->
                    <div class="input-group">
                        <label>Valor</label>
                        <input type="text"
                            name="valor"
                            class="form-control money-input"
                            value="R$ <?= number_format($titulo->getValor(), 2, ',', '.') ?>"
                            <?= $isParcelado ? 'disabled' : '' ?>>
                    </div>

                    <!-- Plano -->
                     <div class="input-group">
                        <?php
                            $planoDre = $titulo->getPlano();
                        ?>
                        <label>Plano de Contas (DRE)</label>
                        <input
                            type="text"
                            id="plano_busca"
                            placeholder="Digite código ou nome da conta"
                            autocomplete="off"
                            class="form-control" disabled value="<?= $planoDre->getCodigo() .' - '. $planoDre->getNome()  ?>">

                        <input type="hidden" name="plano_id" id="plano_id" value="<?= $planoDre->getId() ?>">

                        <div id="plano_resultados" class="autocomplete-box"></div>
                    </div>

                </div>
            </div>

            <!-- Observações -->
            <div class="stat-card">
                <h3 class="section-title">Observações</h3>
                <textarea name="observacoes"
                    class="form-control"
                    rows="4"><?= htmlspecialchars($titulo->getObservacoes() ?? '') ?></textarea>
            </div>
        </div>

        <!-- Coluna lateral -->
        <div style="display: flex; flex-direction: column; gap: 25px;">
            <div class="stat-card">
                <h3 class="section-title">Status</h3>

                <p><strong>Status:</strong> <?= $titulo->getStatus() ?></p>
                <p><strong>Valor Pago:</strong>
                    R$ <?= number_format($titulo->getValorPago(), 2, ',', '.') ?>
                </p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <button type="submit"
                    class="btn btn-primary"
                    style="width: 100%; padding: 15px; font-weight: 700;">
                    💾 Salvar Alterações
                </button>

                <a href="/financeiro"
                    class="btn btn-outline"
                    style="width: 100%;">
                    Cancelar
                </a>
            </div>
        </div>

    </div>

</form>

<script>
    const participanteInput = document.getElementById('participante_busca');
    const participanteBox = document.getElementById('participante_resultados');
    const participanteId = document.getElementById('participante_id');

    let participanteTimeout = null;

    participanteInput.addEventListener('input', () => {
        const q = participanteInput.value.trim();

        clearTimeout(participanteTimeout);

        if (q.length < 2) {
            participanteBox.style.display = 'none';
            return;
        }

        participanteTimeout = setTimeout(() => {
            fetch(`/api/participantes/search?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(lista => {
                    participanteBox.innerHTML = '';

                    if (!lista.length) {
                        participanteBox.style.display = 'none';
                        return;
                    }

                    lista.forEach(p => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.innerHTML = `
                        <strong>${p.label}</strong><br>
                        <span class="autocomplete-muted">
                            ${p.documento ?? ''} • ${p.tipo.join(', ')}
                        </span>
                    `;

                        div.onclick = () => {
                            participanteInput.value = p.label;
                            participanteId.value = p.id;
                            participanteBox.style.display = 'none';
                        };

                        participanteBox.appendChild(div);
                    });

                    participanteBox.style.display = 'block';
                });
        }, 300);
    });

    // fecha ao clicar fora
    document.addEventListener('click', e => {
        if (!participanteInput.contains(e.target)) {
            participanteBox.style.display = 'none';
        }
    });
</script>
<script>
    const planoInput = document.getElementById('plano_busca');
    const planoBox = document.getElementById('plano_resultados');
    const planoId = document.getElementById('plano_id');

    let planoTimeout = null;

    planoInput.addEventListener('input', () => {
        const q = planoInput.value.trim();

        clearTimeout(planoTimeout);

        if (q.length < 2) {
            planoBox.style.display = 'none';
            return;
        }

        planoTimeout = setTimeout(() => {
            fetch(`/api/planos/search?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(lista => {
                    planoBox.innerHTML = '';

                    if (!lista.length) {
                        planoBox.style.display = 'none';
                        return;
                    }

                    lista.forEach(p => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.innerHTML = `
                        <strong>${p.codigo}</strong> - ${p.nome}
                    `;

                        div.onclick = () => {
                            planoInput.value = `${p.codigo} - ${p.nome}`;
                            planoId.value = p.id;
                            planoBox.style.display = 'none';
                        };

                        planoBox.appendChild(div);
                    });

                    planoBox.style.display = 'block';
                });
        }, 300);
    });

    document.addEventListener('click', e => {
        if (!planoInput.contains(e.target)) {
            planoBox.style.display = 'none';
        }
    });
</script>

<?php
$content = ob_get_clean();
$title = 'Editar Lançamento Financeiro';
$titletopbar = 'Editar Lançamento Financeiro';
include __DIR__ . '/../layouts/app.php';

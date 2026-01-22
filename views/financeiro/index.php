<?php ob_start(); ?>


<div class="top-bar">
    <div class="top-bar-header">
        <button id="menu-toggle" class="action-btn">☰</button>
        <h1 style="font-size: 20px; font-weight: 700;">Contas a Pagar e Receber</h1>
    </div>
    <div class="top-bar-actions">
        <button id="btn-baixa-lote" class="btn btn-outline" style="display: none;">💰 Baixar Selecionados</button>
        <a href="/financeiro/create" class="btn btn-primary">➕ Novo Lançamento</a>
    </div>
</div>
<?php if ($info = $this->getFlash('info')): ?>
    <div class="alert alert-info">
        <?= htmlspecialchars($info) ?>
    </div>
<?php endif; ?>

<?php if ($error = $this->getFlash('error')): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php if ($success = $this->getFlash('success')): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>
<!-- Resumo Financeiro -->
<section class="stats-grid">
    <div class="stat-card" style="border-left: 4px solid #ef4444;">
        <h3 style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">A Pagar (Hoje)</h3>
        <p class="value" style="color: #ef4444;">R$ <?= number_format($resumo['pagar_hoje']['total'], 2, ',', '.') ?></p>
        <span style="font-size: 11px; color: var(--text-muted);"><?= $resumo['pagar_hoje']['qtd'] ?> títulos vencendo hoje</span>
    </div>
    <div class="stat-card" style="border-left: 4px solid #10b981;">
        <h3 style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">A Receber (Hoje)</h3>
        <p class="value" style="color: #10b981;">R$ <?= number_format($resumo['receber_hoje']['total'], 2, ',', '.') ?></p>
        <span style="font-size: 11px; color: var(--text-muted);"><?= $resumo['receber_hoje']['qtd'] ?> títulos previstos</span>
    </div>
    <div class="stat-card" style="border-left: 4px solid #f59e0b;">
        <h3 style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Total em Atraso</h3>
        <p class="value" style="color: #f59e0b;">R$ <?= number_format($resumo['atraso']['total'], 2, ',', '.') ?></p>
        <span style="font-size: 11px; color: var(--text-muted);"><?= $resumo['atraso']['qtd'] ?> títulos pendentes</span>
    </div>
    <div class="stat-card" style="border-left: 4px solid var(--primary-color);">
        <h3 style="font-size: 12px; color: var(--text-muted); text-transform: uppercase;">Saldo Previsto</h3>
        <p class="value" style="color: var(--primary-color);">R$ <?= number_format($resumo['saldo_previsto'], 2, ',', '.') ?></p>
        <span style="font-size: 11px; color: var(--text-muted);">Projeção para o dia</span>
    </div>
</section>

<!-- Filtros -->
<section class="stat-card" style="margin-bottom: 25px; padding: 15px;">
    <form style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; align-items: end;">
        <div class="input-group" style="margin-bottom: 0;">
            <label>Período</label>
            <input type="date" class="form-control" style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-color);">
        </div>
        <div class="input-group" style="margin-bottom: 0;">
            <label>Até</label>
            <input type="date" class="form-control" style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-color);">
        </div>
        <div class="input-group" style="margin-bottom: 0;">
            <label>Tipo</label>
            <select style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-color);">
                <option>Todos</option>
                <option value="PAGAR" <?= $filtros['tipo'] == 'PAGAR' ? 'selected' : '' ?>>A Pagar</option>
                <option value="RECEBER" <?= $filtros['tipo'] == 'RECEBER' ? 'selected' : '' ?>>A Receber</option>
            </select>
        </div>
        <div class="input-group" style="margin-bottom: 0;">
            <label>Status</label>
            <select style="width: 100%; padding: 8px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-color);">
                <option>Aberto</option>
                <option>Pago</option>
                <option>Cancelado</option>
            </select>
        </div>
        <button type="button" class="btn btn-primary" style="height: 38px;">Filtrar</button>
    </form>
</section>

<!-- Tabela de Títulos -->
<section class="data-table-container">
    <div class="table-responsive">
        <table class="table table-hover" id="tabela-financeiro">
            <thead>
                <tr>
                    <th style="width: 40px;"><input type="checkbox" id="select-all"></th>
                    <th>Vencimento</th>
                    <th>Tipo</th>
                    <th>Participante</th>
                    <th>Documento</th>
                    <th>Parcela</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($titulos as $t): ?>
                    <tr
                        data-id="<?= $t['id'] ?>"
                        data-valor="<?= $t['valor'] ?>"
                        data-participante="<?= htmlspecialchars($t['participante']) ?>"
                        data-doc="<?= htmlspecialchars($t['parcela']) ?>"
                        data-valor-pago="<?= $t['valor_pago'] ?? 0 ?>">
                        <td><input type="checkbox" class="row-select"></td>
                        <td><strong><?= date('d/m/Y', strtotime($t['data_vencimento'])) ?></strong></td>
                        <td>
                            <span class="badge <?= $t['tipo'] === 'PAGAR' ? 'badge-danger' : 'badge-success' ?>">
                                <?= $t['tipo'] ?>
                            </span>
                        </td>
                        <td><?= $t['participante'] ?? '-' ?></td>
                        <td><?= $t['numero_documento'] ?></td>
                        <td><?= $t['numero_documento'] ?>/<?= explode('/', $t['parcela'])[0] ?></td>
                        <td>R$ <?= number_format($t['valor'], 2, ',', '.') ?></td>
                        <td>
                            <span class="badge badge-warning"><?= $t['status'] ?></span>
                        </td>
                        <td>
                            <div style="display:flex;gap:5px">
                                <?php if (in_array($t['status'], ['ABERTO', 'PARCIAL'])): ?>
                                    <button class="btn btn-ghost btn-baixa">💰</button>
                                <?php endif ?>
                                <button class="btn btn-ghost btn-view">👁️</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>

            </tbody>
        </table>
    </div>
</section>
<?php
$paginaAtual  = $paginas['page'];
$totalPaginas = $paginas['last_page'];

$inicio = max(1, $paginaAtual - 3);
$fim    = min($totalPaginas, $paginaAtual + 3);

?>
<?php if ($totalPaginas > 1): ?>
    <div class="pagination">

        <?php if ($paginaAtual > 1): ?>
            <a class="page-btn"
                href="?<?= http_build_query(array_merge($_GET, ['page' => $paginaAtual - 1])) ?>">
                ← Anterior
            </a>
        <?php endif; ?>

        <?php for ($p = $inicio; $p <= $fim; $p++): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>"
                class="page-btn <?= $p == $paginaAtual ? 'active' : '' ?>">
                <?= $p ?>
            </a>
        <?php endfor; ?>

        <?php if ($paginaAtual < $totalPaginas): ?>
            <a class="page-btn"
                href="?<?= http_build_query(array_merge($_GET, ['page' => $paginaAtual + 1])) ?>">
                Próxima →
            </a>
        <?php endif; ?>

    </div>
<?php endif; ?>


<!-- Modal: Baixa Rápida -->
<div id="modal-baixa" class="modal-overlay">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2>Confirmar Baixa</h2>
            <button class="modal-close" onclick="closeModal('modal-baixa')">&times;</button>
        </div>
        <div class="modal-body">
            <div style="background: var(--bg-color); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 5px;">Participante</p>
                <p id="baixa-participante" style="font-weight: 600; margin-bottom: 15px;">-</p>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 5px;">Documento</p>
                <p id="baixa-documento" style="font-weight: 600;">-</p>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:15px">
                <div>
                    <small>Valor do Título</small>
                    <div id="baixa-valor-total" style="font-weight:700">R$ 0,00</div>
                </div>
                <div>
                    <small>Valor Pago</small>
                    <div id="baixa-valor-pago" style="font-weight:700">R$ 0,00</div>
                </div>
                <div>
                    <small>Saldo Restante</small>
                    <div id="baixa-saldo" style="font-weight:700;color:#ef4444">R$ 0,00</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="input-group">
                    <label>Data do Pagamento</label>
                    <input type="date" id="baixa-data" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-color);">
                </div>
                <div class="input-group">
                    <label>Valor Pago</label>
                    <input type="text" id="baixa-valor" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-color); font-weight: 700;">
                </div>
            </div>
            <div class="input-group">
                <label>Conta / Caixa</label>
                <select class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-color);">
                    <option>Caixa Geral</option>
                    <option>Banco do Brasil</option>
                    <option>Itaú</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-baixa')">Cancelar</button>
            <button class="btn btn-primary" onclick="confirmarBaixa()">Confirmar Pagamento</button>
        </div>
    </div>
</div>

<!-- Modal: Visualização de Parcelas -->
<div id="modal-view" class="modal-overlay">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h2>Detalhes do Título</h2>
            <button class="modal-close" onclick="closeModal('modal-view')">&times;</button>
        </div>
        <div class="modal-body">
            <div id="view-parcelas-lista" style="display: flex; flex-direction: column; gap: 10px;">
                <!-- Lista de parcelas -->
                <div style="display: grid; grid-template-columns: 60px 1fr 1fr 100px; padding: 12px; background: var(--bg-color); border-radius: 8px; align-items: center;">
                    <span style="font-weight: 700;">1/3</span>
                    <span>Venc: 25/01/2026</span>
                    <span style="font-weight: 600;">R$ 1.383,33</span>
                    <span class="badge badge-warning">ABERTO</span>
                </div>
                <div style="display: grid; grid-template-columns: 60px 1fr 1fr 100px; padding: 12px; background: var(--bg-color); border-radius: 8px; align-items: center;">
                    <span style="font-weight: 700;">2/3</span>
                    <span>Venc: 25/02/2026</span>
                    <span style="font-weight: 600;">R$ 1.383,33</span>
                    <span class="badge badge-warning">ABERTO</span>
                </div>
                <div style="display: grid; grid-template-columns: 60px 1fr 1fr 100px; padding: 12px; background: var(--bg-color); border-radius: 8px; align-items: center;">
                    <span style="font-weight: 700;">3/3</span>
                    <span>Venc: 25/03/2026</span>
                    <span style="font-weight: 600;">R$ 1.383,34</span>
                    <span class="badge badge-warning">ABERTO</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="closeModal('modal-view')">Fechar</button>
        </div>
    </div>
</div>

<div id="modal-baixa-lote" class="modal-overlay">
    <div class="modal-content" style="max-width:600px">
        <div class="modal-header">
            <h2>Confirmar Baixa em Lote</h2>
            <button class="modal-close" onclick="closeModal('modal-baixa-lote')">&times;</button>
        </div>

        <div class="modal-body">
            <p><strong>Total selecionados:</strong> <span id="lote-total"></span></p>
            <p><strong>Pagáveis:</strong> <span id="lote-pagaveis"></span></p>
            <p><strong>Ignorados:</strong> <span id="lote-ignorados"></span></p>
            <hr>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px">
                <div>
                    <label>
                        <input type="radio" name="tipo_lote" value="TOTAL" checked>
                        Quitar saldo total
                    </label>
                </div>

                <div>
                    <label>
                        <input type="radio" name="tipo_lote" value="FIXO">
                        Valor fixo por título
                    </label>
                    <input type="text" id="lote-valor-fixo" class="form-control" disabled>
                </div>

                <div>
                    <label>
                        <input type="radio" name="tipo_lote" value="PERCENTUAL">
                        Percentual do saldo
                    </label>
                    <input type="number" id="lote-percentual" min="1" max="100" disabled>
                </div>
            </div>

            <hr>

            <p style="font-size:18px">
                <strong>Valor total:</strong>
                <span id="lote-valor" style="color:#10b981"></span>
            </p>
        </div>


        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-baixa-lote')">Cancelar</button>
            <button class="btn btn-primary" id="btn-confirmar-lote">
                Confirmar Baixa
            </button>
        </div>
    </div>
</div>


<script>
    let tituloBaixaId = null;
    let saldoRestante = 0;
    // Baixa Rápida
    const baixaValorMask = IMask(document.getElementById('baixa-valor'), {
        mask: 'R$ num',
        blocks: {
            num: {
                mask: Number,
                thousandsSeparator: '.',
                padFractionalZeros: true,
                radix: ','
            }
        }
    });

    const loteValorMask = IMask(
        document.getElementById('lote-valor-fixo'), {
            mask: 'R$ num',
            blocks: {
                num: {
                    mask: Number,
                    thousandsSeparator: '.',
                    radix: ',',
                    padFractionalZeros: true
                }
            }
        }
    );


    document.querySelectorAll('.btn-baixa').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const tr = e.target.closest('tr');

            tituloBaixaId = tr.dataset.id;

            const valorTotal = Number(tr.dataset.valor);
            const valorPago = Number(tr.dataset.valorPago || 0);

            saldoRestante = valorTotal - valorPago;

            document.getElementById('baixa-participante').innerText = tr.dataset.participante;
            document.getElementById('baixa-documento').innerText = tr.dataset.doc;
            console.log(saldoRestante.toFixed(2));
            baixaValorMask.value = saldoRestante.toFixed(2).replace('.', ',');

            document.getElementById('baixa-valor-total').innerText =
                `R$ ${formatMoney(valorTotal)}`;

            document.getElementById('baixa-valor-pago').innerText =
                `R$ ${formatMoney(valorPago)}`;

            document.getElementById('baixa-saldo').innerText =
                `R$ ${formatMoney(saldoRestante)}`;

            document.getElementById('baixa-data').value =
                new Date().toISOString().split('T')[0];

            openModal('modal-baixa');
        });
    });

    async function confirmarBaixa() {

        if (!tituloBaixaId) {
            alert('Título inválido.');
            return;
        }

        const valorDigitado = Number(baixaValorMask.unmaskedValue);
        const data = document.getElementById('baixa-data').value;

        if (valorDigitado <= 0) {
            alert('Informe um valor válido.');
            return;
        }

        if (valorDigitado > saldoRestante) {
            alert('O valor não pode ser maior que o saldo restante.');
            return;
        }

        const formData = new FormData();
        formData.append('valor', valorDigitado);
        formData.append('data', data);
        formData.append('forma_pagamento', 'CAIXA');

        try {
            const res = await fetch(`/financeiro/baixa/${tituloBaixaId}`, {
                method: 'POST',
                body: formData
            });

            const json = await res.json();

            if (!res.ok || json.error) {
                throw new Error(json.error || 'Erro ao registrar pagamento.');
            }

            closeModal('modal-baixa');
            location.reload();

        } catch (e) {
            alert(e.message);
        }
    }


    document.getElementById('baixa-valor').addEventListener('input', () => {

        const valorDigitado = baixaValorMask.unmaskedValue ?
            Number(baixaValorMask.unmaskedValue) :
            0;
        console.log({
            valorDigitado,
            saldoRestante
        });
        const novoSaldo = saldoRestante - valorDigitado;

        const saldoEl = document.getElementById('baixa-saldo');

        saldoEl.innerText = `R$ ${formatMoney(novoSaldo >= 0 ? novoSaldo : 0)}`;

        saldoEl.style.color = novoSaldo <= 0 ? '#10b981' : '#ef4444';
    });



    // Visualização
    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            const tr = e.target.closest('tr');
            const id = tr.dataset.id;

            const res = await fetch(`/financeiro/detalhes/${id}`);
            const json = await res.json();

            const lista = document.getElementById('view-parcelas-lista');
            lista.innerHTML = '';

            json.parcelas.forEach(p => {
                lista.innerHTML += `
                <div class="parcela-item" style="display: grid; grid-template-columns: 60px 1fr 1fr 100px; padding: 12px; background: var(--bg-color); border-radius: 8px; align-items: center;">
                    <strong style="font-weight: 700;">${p.parcela}</strong>
                    <span>Venc: ${formatDate(p.data_vencimento)}</span>
                    <span style="font-weight: 600;">R$ ${formatMoney(p.valor)}</span>
                    <span class="badge badge-${p.status === 'PAGO' ? 'success' : 'warning'}">
                        ${p.status}
                    </span>
                </div>
            `;
            });

            openModal('modal-view');
        });
    });

    // Seleção Múltipla
    const selectAll = document.getElementById('select-all');
    const rowSelects = document.querySelectorAll('.row-select');
    const btnBaixaLote = document.getElementById('btn-baixa-lote');

    selectAll.addEventListener('change', () => {
        rowSelects.forEach(cb => cb.checked = selectAll.checked);
        toggleLoteBtn();
    });

    rowSelects.forEach(cb => {
        cb.addEventListener('change', toggleLoteBtn);
    });

    function toggleLoteBtn() {
        const selectedCount = Array.from(rowSelects).filter(cb => cb.checked).length;
        btnBaixaLote.style.display = selectedCount > 0 ? 'block' : 'none';
        btnBaixaLote.innerText = `💰 Baixar ${selectedCount} selecionados`;
    }

    btnBaixaLote.addEventListener('click', async () => {

        const ids = getTitulosSelecionados();

        if (ids.length === 0) {
            alert('Nenhum título selecionado.');
            return;
        }

        try {
            const res = await fetch('/financeiro/baixa-lote/preview', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    ids
                })
            });

            const json = await res.json();

            if (!res.ok || json.error) {
                throw new Error(json.error || 'Erro ao gerar resumo.');
            }

            const r = json.resumo;

            document.getElementById('lote-total').innerText = r.total_selecionados;
            document.getElementById('lote-pagaveis').innerText = r.pagaveis;
            document.getElementById('lote-ignorados').innerText = r.ignorados;
            document.getElementById('lote-valor').innerText =
                `R$ ${formatMoney(r.valor_total)}`;

            // guarda IDs globalmente
            window.idsBaixaLote = ids;

            openModal('modal-baixa-lote');

        } catch (e) {
            alert(e.message);
        }
    });


    function mostrarResultadoBaixa(resultado) {

        let msg = `Resultado da Baixa em Lote\n\n`;

        msg += `✔️ Pagos: ${resultado.pagos.length}\n`;
        resultado.pagos.forEach(p => {
            msg += ` - ID ${p.id} | Valor R$ ${formatMoney(p.valor)}\n`;
        });

        msg += `\n❌ Falhas: ${resultado.falhas.length}\n`;
        resultado.falhas.forEach(f => {
            msg += ` - ID ${f.id} | ${f.motivo}\n`;
        });

        alert(msg);
        location.reload();
    }

    document.getElementById('btn-confirmar-lote')
        .addEventListener('click', async () => {

            const tipo = document.querySelector(
                'input[name="tipo_lote"]:checked'
            ).value;

            let payload = {
                ids: window.idsBaixaLote,
                tipo
            };

            if (tipo === 'FIXO') {
                payload.valor =
                    Number(loteValorMask.unmaskedValue) / 100;

                if (payload.valor <= 0) {
                    alert('Informe um valor fixo válido.');
                    return;
                }
            }

            if (tipo === 'PERCENTUAL') {
                payload.percentual =
                    Number(document.getElementById('lote-percentual').value);

                if (payload.percentual <= 0 || payload.percentual > 100) {
                    alert('Percentual inválido.');
                    return;
                }
            }

            try {
                const res = await fetch('/financeiro/baixa-lote', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const json = await res.json();

                if (!res.ok || json.error) {
                    throw new Error(json.error || 'Erro na baixa em lote.');
                }

                closeModal('modal-baixa-lote');
                mostrarResultadoBaixa(json.resultado);

            } catch (e) {
                alert(e.message);
            }
        });


    document.querySelectorAll('input[name="tipo_lote"]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.getElementById('lote-valor-fixo').disabled =
                radio.value !== 'FIXO';

            document.getElementById('lote-percentual').disabled =
                radio.value !== 'PERCENTUAL';
        });
    });


    function formatDate(date) {
        if (!date) return '-';

        // date vem do backend como YYYY-MM-DD ou YYYY-MM-DD HH:MM:SS
        const d = new Date(date);

        if (isNaN(d.getTime())) return '-';

        return d.toLocaleDateString('pt-BR');
    }

    function formatMoney(valor) {
        if (valor === null || valor === undefined) return '0,00';

        return Number(valor).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function getTitulosSelecionados() {
        return Array.from(document.querySelectorAll('.row-select:checked'))
            .map(cb => cb.closest('tr').dataset.id);
    }
</script>
<?php
$content = ob_get_clean();
$title = 'Financeiro';
$titletopbar = "Financeiro";
include __DIR__ . '/../layouts/app.php';

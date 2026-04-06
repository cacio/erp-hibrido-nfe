<?php ob_start(); ?>

<div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
    <a href="/financeiro" class="btn btn-outline">⬅️ Voltar</a>
    <h1 style="font-size: 20px; font-weight: 700;">Novo Lançamento Financeiro</h1>
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
<form id="financeiro-form" method="POST" action="/financeiro">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
        <!-- Coluna Principal -->
        <div style="display: flex; flex-direction: column; gap: 25px;">
            <!-- Card: Dados do Título -->
            <div class="stat-card">
                <h3 class="section-title">Dados do Título</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="input-group">
                        <label>Tipo de Lançamento</label>
                        <select name="tipo" class="form-control" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-color);">
                            <option value="PAGAR">🔴 Contas a Pagar</option>
                            <option value="RECEBER">🟢 Contas a Receber</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Participante (Cliente / Fornecedor)</label>

                        <input
                            type="text"
                            id="participante_busca"
                            placeholder="Digite nome ou CPF/CNPJ"
                            autocomplete="off"
                            class="form-control">

                        <input type="hidden" name="participante_id" id="participante_id">

                        <div id="participante_resultados" class="autocomplete-box"></div>
                    </div>

                    <div class="input-group">
                        <label>Número do Documento</label>
                        <input type="text" name="numero_documento" placeholder="Ex: NF 1234" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-color);">
                    </div>
                    <div class="input-group">
                        <label>Plano de Contas (DRE)</label>

                        <input
                            type="text"
                            id="plano_busca"
                            placeholder="Digite código ou nome da conta"
                            autocomplete="off"
                            class="form-control">

                        <input type="hidden" name="plano_id" id="plano_id">

                        <div id="plano_resultados" class="autocomplete-box"></div>
                    </div>

                </div>
            </div>

            <!-- Card: Valores e Parcelamento -->
            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 class="section-title" style="margin-bottom: 0;">Valores e Parcelamento</h3>
                    <div id="total-info" style="text-align: right;">
                        <span style="font-size: 12px; color: var(--text-muted);">Total Calculado:</span>
                        <div id="display-total-calculado" style="font-weight: 700; color: var(--primary-color);">R$ 0,00</div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px;">
                    <div class="input-group">
                        <label>Valor Original</label>
                        <input type="text" id="valor_original" name="valor" placeholder="R$ 0,00" class="money-input" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-color); font-weight: 600;">
                    </div>
                    <div class="input-group">
                        <label>Juros (+)</label>
                        <input type="text" id="valor_juros" name="juros" placeholder="R$ 0,00" class="money-input" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-color);">
                    </div>
                    <div class="input-group">
                        <label>Multa (+)</label>
                        <input type="text" id="valor_multa" name="multa" placeholder="R$ 0,00" class="money-input" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-color);">
                    </div>
                    <div class="input-group">
                        <label>Desconto (-)</label>
                        <input type="text" id="valor_desconto" name="desconto" placeholder="R$ 0,00" class="money-input" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-color);">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px; padding: 15px; background: var(--bg-color); border-radius: 10px;">
                    <div class="input-group" style="margin-bottom: 0;">
                        <label>Qtd. Parcelas</label>
                        <input type="number" id="qtd_parcelas" value="1" min="1" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-color);">
                    </div>
                    <div class="input-group" style="margin-bottom: 0;">
                        <label>1º Vencimento</label>
                        <input type="date" id="data_inicio" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-color);">
                    </div>
                    <div style="display: flex; align-items: flex-end;">
                        <button type="button" id="btn-gerar-parcelas" class="btn btn-primary" style="width: 100%; height: 42px;">🔄 Gerar Parcelas</button>
                    </div>
                </div>

                <div id="alert-diferenca" style="display: none; padding: 10px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 15px; font-size: 13px; font-weight: 500; border: 1px solid #fecaca;">
                    ⚠️ Atenção: A soma das parcelas não confere com o valor total. Diferença: <span id="valor-diferenca">R$ 0,00</span>
                </div>

                <div id="parcelas-header" style="display: grid; grid-template-columns: 60px 80px 1fr 1fr 40px; gap: 15px; padding: 0 15px 10px 15px; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">
                    <span>Parc.</span>
                    <span>Dias</span>
                    <span>Vencimento</span>
                    <span>Valor</span>
                    <span></span>
                </div>
                <div id="parcelas-container" style="display: flex; flex-direction: column; gap: 8px;">
                    <!-- Parcelas geradas via JS -->
                </div>
            </div>
        </div>

        <!-- Coluna Lateral -->
        <div style="display: flex; flex-direction: column; gap: 25px;">
            <div class="stat-card">
                <h3 class="section-title">Configurações</h3>
                <div class="input-group">
                    <label>Data de Emissão</label>
                    <input type="date" id="data_emissao" name="data_emissao" value="2026-01-25" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-color);">
                </div>
                <div class="input-group">
                    <label>Forma de Pagamento</label>
                    <select name="forma_pagamento" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-color);">
                        <option>Boleto Bancário</option>
                        <option>PIX</option>
                        <option>Transferência</option>
                        <option>Dinheiro</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Observações</label>
                    <textarea name="observacoes" rows="4" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-color); resize: none;"></textarea>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-weight: 700;">💾 Salvar Lançamento</button>
                <button type="reset" class="btn btn-outline" style="width: 100%;">Limpar</button>
            </div>
        </div>
    </div>
</form>
</main>
</div>

<script>
    // Configuração de Máscaras
    const moneyConfig = {
        mask: 'R$ num',
        blocks: {
            num: {
                mask: Number,
                thousandsSeparator: '.',
                padFractionalZeros: true,
                radix: ','
            }
        }
    };

    const masks = {
        original: IMask(document.getElementById('valor_original'), moneyConfig),
        juros: IMask(document.getElementById('valor_juros'), moneyConfig),
        multa: IMask(document.getElementById('valor_multa'), moneyConfig),
        desconto: IMask(document.getElementById('valor_desconto'), moneyConfig)
    };

    let parcelasEditadasManualmente = new Set();

    function getValorLiquido() {
        const original = parseFloat(masks.original.unmaskedValue) || 0;
        const juros = parseFloat(masks.juros.unmaskedValue) || 0;
        const multa = parseFloat(masks.multa.unmaskedValue) || 0;
        const desconto = parseFloat(masks.desconto.unmaskedValue) || 0;
        return original + juros + multa - desconto;
    }

    function formatMoney(value) {
        return value.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
    }

    function atualizarTotalCalculado() {
        const totalLiquido = getValorLiquido();
        document.getElementById('display-total-calculado').innerText = formatMoney(totalLiquido);
        validarSomaParcelas();
    }

    Object.values(masks).forEach(mask => {
        mask.on('accept', atualizarTotalCalculado);
    });

    document.getElementById('btn-gerar-parcelas').addEventListener('click', () => {
        const totalLiquido = getValorLiquido();
        const qtd = parseInt(document.getElementById('qtd_parcelas').value);
        const dataInicio = new Date(document.getElementById('data_inicio').value);

        if (totalLiquido <= 0 || isNaN(qtd) || isNaN(dataInicio.getTime())) {
            alert('Preencha os valores e a data inicial corretamente.');
            return;
        }

        parcelasEditadasManualmente.clear();
        gerarParcelasUI(totalLiquido, qtd, dataInicio);
    });

    function gerarParcelasUI(total, qtd, dataInicio) {
        const container = document.getElementById('parcelas-container');
        container.innerHTML = '';

        const valorBase = Math.floor((total / qtd) * 100) / 100;
        const sobra = parseFloat((total - (valorBase * qtd)).toFixed(2));

        for (let i = 1; i <= qtd; i++) {
            const dataVenc = new Date(dataInicio);
            dataVenc.setDate(dataVenc.getDate() + ((i - 1) * 30)); // Padrão 30 dias

            const valorFinal = i === 1 ? (valorBase + sobra) : valorBase;
            adicionarParcelaRow(i, qtd, dataVenc.toISOString().split('T')[0], valorFinal);
        }
        validarSomaParcelas();
    }

    function adicionarParcelaRow(index, total, data, valor) {
        const container = document.getElementById('parcelas-container');
        const row = document.createElement('div');
        row.className = 'parcela-row';
        row.dataset.index = index;
        row.style.display = 'grid';
        row.style.gridTemplateColumns = '60px 80px 1fr 1fr 40px';
        row.style.gap = '15px';
        row.style.padding = '12px 15px';
        row.style.background = 'var(--card-bg)';
        row.style.borderRadius = '10px';
        row.style.border = '1px solid var(--border-color)';
        row.style.alignItems = 'center';

        const dataEmissao = new Date(document.getElementById('data_emissao').value);
        const dataVenc = new Date(data);
        const diffDias = Math.ceil((dataVenc - dataEmissao) / (1000 * 60 * 60 * 24));

        row.innerHTML = `
                <span style="font-weight: 700; color: var(--text-muted); font-size: 13px;">${index}/${total}</span>
                <input type="number" class="parcela-dias-input" value="${diffDias}" style="padding: 8px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-color); text-align: center;">
                <input type="date" name="parcelas[${index}][vencimento]" value="${data}" class="parcela-vencimento-input" style="padding: 8px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-color);">
                <div style="position: relative;">
                    <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 12px; color: var(--text-muted);">R$</span>
                    <input type="text" name="parcelas[${index}][valor]" value="${valor.toFixed(2).replace('.', ',')}" class="parcela-valor-input" style="width: 100%; padding: 8px 8px 8px 30px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-color); font-weight: 600;">
                </div>
                <button type="button" class="btn-remover-parcela" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 18px;" title="Remover Parcela">×</button>
            `;

        container.appendChild(row);

        const inputDias = row.querySelector('.parcela-dias-input');
        const inputVenc = row.querySelector('.parcela-vencimento-input');
        const inputValor = row.querySelector('.parcela-valor-input');
        const maskValor = IMask(inputValor, moneyConfig);

        // Lógica de Dias -> Data
        inputDias.addEventListener('change', () => {
            const emissao = new Date(document.getElementById('data_emissao').value);
            const novosDias = parseInt(inputDias.value);
            const novaData = new Date(emissao);
            novaData.setDate(novaData.getDate() + novosDias);

            if (validarCronologia(index, novaData)) {
                inputVenc.value = novaData.toISOString().split('T')[0];
            } else {
                alert('O vencimento não pode ser anterior à parcela anterior.');
                // Reverte para o valor anterior baseado na data atual do input
                const dataAtual = new Date(inputVenc.value);
                inputDias.value = Math.ceil((dataAtual - emissao) / (1000 * 60 * 60 * 24));
            }
        });

        // Lógica de Data -> Dias
        inputVenc.addEventListener('change', () => {
            const emissao = new Date(document.getElementById('data_emissao').value);
            const novaData = new Date(inputVenc.value);

            if (validarCronologia(index, novaData)) {
                inputDias.value = Math.ceil((novaData - emissao) / (1000 * 60 * 60 * 24));
            } else {
                alert('O vencimento não pode ser anterior à parcela anterior.');
                // Reverte para a data anterior baseada nos dias atuais do input
                const diasAtuais = parseInt(inputDias.value);
                const dataRevertida = new Date(emissao);
                dataRevertida.setDate(dataRevertida.getDate() + diasAtuais);
                inputVenc.value = dataRevertida.toISOString().split('T')[0];
            }
        });

        maskValor.on('accept', () => {
            if (maskValor.el.isActive) {
                parcelasEditadasManualmente.add(index);
                recalcularParcelasRestantes(index);
            }
        });

        row.querySelector('.btn-remover-parcela').addEventListener('click', () => {
            row.remove();
            reorganizarIndices();
            recalcularParcelasRestantes();
        });
    }

    function validarCronologia(index, novaData) {
        const rows = Array.from(document.querySelectorAll('.parcela-row'));
        const idx = parseInt(index);

        // Verifica com a parcela anterior
        if (idx > 1) {
            const anterior = rows.find(r => parseInt(r.dataset.index) === idx - 1);
            if (anterior) {
                const dataAnterior = new Date(anterior.querySelector('.parcela-vencimento-input').value);
                if (novaData < dataAnterior) return false;
            }
        }

        // Verifica com a parcela posterior
        const posterior = rows.find(r => parseInt(r.dataset.index) === idx + 1);
        if (posterior) {
            const dataPosterior = new Date(posterior.querySelector('.parcela-vencimento-input').value);
            if (novaData > dataPosterior) return false;
        }

        return true;
    }

    function recalcularParcelasRestantes(indexEditado = null) {
        const totalLiquido = getValorLiquido();
        const rows = Array.from(document.querySelectorAll('.parcela-row'));

        let somaFixa = 0;
        let parcelasParaDistribuir = [];

        rows.forEach(row => {
            const idx = parseInt(row.dataset.index);
            const val = parseFloat(IMask.createMask(moneyConfig).resolve(row.querySelector('.parcela-valor-input').value).replace('R$ ', '').replace(/\./g, '').replace(',', '.')) || 0;

            if (parcelasEditadasManualmente.has(idx) || idx === indexEditado) {
                somaFixa += val;
            } else {
                parcelasParaDistribuir.push(row);
            }
        });

        const saldoRestante = totalLiquido - somaFixa;

        if (parcelasParaDistribuir.length > 0 && saldoRestante >= 0) {
            const valorBase = Math.floor((saldoRestante / parcelasParaDistribuir.length) * 100) / 100;
            const sobra = parseFloat((saldoRestante - (valorBase * parcelasParaDistribuir.length)).toFixed(2));

            parcelasParaDistribuir.forEach((row, i) => {
                const input = row.querySelector('.parcela-valor-input');
                const newVal = i === 0 ? (valorBase + sobra) : valorBase;
                input.value = newVal.toFixed(2).replace('.', ',');
                IMask(input, moneyConfig).value = newVal.toFixed(2).replace('.', ',');
            });
        }
        validarSomaParcelas();
    }

    function validarSomaParcelas() {
        const totalLiquido = getValorLiquido();
        const rows = Array.from(document.querySelectorAll('.parcela-row'));
        let somaTotal = 0;

        rows.forEach(row => {
            const val = parseFloat(row.querySelector('.parcela-valor-input').value.replace('R$ ', '').replace(/\./g, '').replace(',', '.')) || 0;
            somaTotal += val;
        });

        const diferenca = Math.abs(totalLiquido - somaTotal);
        const alertBox = document.getElementById('alert-diferenca');

        if (diferenca > 0.01) {
            alertBox.style.display = 'block';
            document.getElementById('valor-diferenca').innerText = formatMoney(totalLiquido - somaTotal);
        } else {
            alertBox.style.display = 'none';
        }
    }

    function reorganizarIndices() {
        const rows = Array.from(document.querySelectorAll('.parcela-row'));
        const total = rows.length;
        rows.forEach((row, i) => {
            const newIdx = i + 1;
            row.dataset.index = newIdx;
            row.querySelector('span').innerText = `${newIdx}/${total}`;
        });
    }

    document.getElementById('data_inicio').value = new Date().toISOString().split('T')[0];
    document.getElementById('data_emissao').value = new Date().toISOString().split('T')[0];
</script>

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
$title = 'Novo Lançamento Financeiro';
$titletopbar = "Novo Lançamento Financeiro";
include __DIR__ . '/../layouts/app.php';

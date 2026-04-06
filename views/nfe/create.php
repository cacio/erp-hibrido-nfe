<?php ob_start(); ?>

<div class="top-bar">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="dashboard.html" class="btn btn-outline">⬅️ Voltar</a>
        <h1 style="font-size: 20px; font-weight: 700;">Nova Nota Fiscal Eletrônica (NF-e)</h1>
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" class="btn btn-outline">💾 Salvar Rascunho</button>
        <button type="button" class="btn btn-primary">🚀 Transmitir NF-e</button>
    </div>
</div>

<form id="nfe-form" method="POST" action="/nfe">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
        <!-- Coluna Principal -->
        <div style="display: flex; flex-direction: column; gap: 25px;">

            <!-- Card: Dados da Nota -->
            <div class="stat-card">
                <h3 class="section-title">Dados da Nota</h3>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
                    <div class="input-group">
                        <label>Modelo</label>
                        <select name="modelo" class="form-control">
                            <option value="55">55 - NF-e</option>
                            <option value="65">65 - NFC-e</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Série</label>
                        <input type="text" name="serie" value="1" class="form-control">
                    </div>
                    <div class="input-group">
                        <label>Data de Emissão</label>
                        <input type="datetime-local" name="data_emissao" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                    <div class="input-group">
                        <label>Natureza da Operação</label>
                        <input type="text" name="natureza_operacao" value="VENDA" class="form-control">
                    </div>
                </div>
            </div>

            <!-- Card: Destinatário -->
            <div class="stat-card">
                <h3 class="section-title">Destinatário</h3>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div class="input-group" style="position: relative;">
                        <label>Buscar Cliente (Nome ou CPF/CNPJ)</label>
                        <input type="text" id="cliente_busca" placeholder="Digite para buscar..." class="form-control" style="width: 100%;">
                        <input type="hidden" id="cliente_id" name="participante_id">
                        <div id="cliente_resultados" class="autocomplete-results" style="display: none;">
                            <!-- Resultados do autocomplete -->
                        </div>
                    </div>
                    <div id="cliente-info-box" style="display: none; padding: 15px; background: var(--bg-color); border-radius: 10px; border: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <p id="cliente-nome" style="font-weight: 700; margin-bottom: 5px;">-</p>
                                <p id="cliente-doc" style="font-size: 13px; color: var(--text-muted); margin-bottom: 10px;">-</p>
                                <p id="cliente-endereco" style="font-size: 13px; line-height: 1.4;">-</p>
                            </div>
                            <button type="button" class="btn btn-ghost" style="color: #ef4444;" onclick="removerCliente()">Remover</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Itens da Nota -->
            <div class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 class="section-title" style="margin-bottom: 0;">Itens da Nota</h3>
                    <button type="button" class="btn btn-primary btn-sm" onclick="openModal('modal-item')">+ Adicionar Item</button>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Produto</th>
                                <th>NCM</th>
                                <th>CFOP</th>
                                <th>Qtd</th>
                                <th>Vlr Unit</th>
                                <th>Total</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="itens-container">
                            <!-- Itens adicionados via JS -->
                            <tr id="no-items-row">
                                <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">Nenhum item adicionado à nota.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card: Transporte -->
            <div class="stat-card">
                <h3 class="section-title">Transporte</h3>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <label class="checkbox-container" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" id="transp_propria" checked onchange="toggleTransportadora()">
                        <span style="font-weight: 500;">Transporte por conta própria (Remetente)</span>
                    </label>

                    <div id="transportadora-box" style="display: none; padding-top: 10px; border-top: 1px solid var(--border-color);">
                        <div class="input-group" style="position: relative;">
                            <label>Buscar Transportadora</label>
                            <input type="text" id="transportadora_busca" placeholder="Nome ou CNPJ da transportadora..." class="form-control" style="width: 100%;">
                            <input type="hidden" id="transportadora_id" name="transportadora_id">
                            <div id="transportadora_resultados" class="autocomplete-results" style="display: none;">
                                <!-- Resultados do autocomplete -->
                            </div>
                        </div>
                        <div id="transp-info-box" style="display: none; margin-top: 15px; padding: 15px; background: var(--bg-color); border-radius: 10px; border: 1px solid var(--border-color);">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <p id="transp-nome" style="font-weight: 700; margin-bottom: 2px;">-</p>
                                    <p id="transp-doc" style="font-size: 13px; color: var(--text-muted);">-</p>
                                </div>
                                <button type="button" class="btn btn-ghost" style="color: #ef4444;" onclick="removerTransp()">Remover</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coluna Lateral: Totais -->
        <div style="display: flex; flex-direction: column; gap: 25px;">
            <div class="stat-card" style="background: var(--primary-color); color: white;">
                <h3 class="section-title" style="color: white; border-bottom-color: rgba(255,255,255,0.2);">Resumo de Totais</h3>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span>Total Produtos:</span>
                        <span id="total-produtos">R$ 0,00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span>Total ICMS:</span>
                        <span id="total-icms">R$ 0,00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span>Total PIS/COFINS:</span>
                        <span id="total-pis-cofins">R$ 0,00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span>Descontos:</span>
                        <span>R$ 0,00</span>
                    </div>
                    <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.2); margin: 5px 0;">
                    <div style="display: flex; justify-content: space-between; font-size: 20px; font-weight: 800;">
                        <span>Total NF-e:</span>
                        <span id="total-nfe">R$ 0,00</span>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <h3 class="section-title">Informações Adicionais</h3>
                <div class="input-group">
                    <label>Finalidade de Emissão</label>
                    <select class="form-control">
                        <option value="1">1 - NF-e Normal</option>
                        <option value="2">2 - NF-e Complementar</option>
                        <option value="3">3 - NF-e de Ajuste</option>
                        <option value="4">4 - Devolução de Mercadoria</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Informações Complementares</label>
                    <textarea class="form-control" rows="5" placeholder="Observações que sairão no DANFE..."></textarea>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Modal: Adicionar Item -->
<div id="modal-item" class="modal-overlay">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h2>Adicionar Item à NF-e</h2>
            <button class="modal-close" onclick="closeModal('modal-item')">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="item_id">
            <input type="hidden" id="produto_id">
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div class="input-group" style="position: relative;">
                    <label>Buscar Produto</label>
                    <input type="text" id="produto_busca" placeholder="Nome ou Código do Produto..." class="form-control">
                    <div id="produto_resultados" class="autocomplete-results" style="display: none;">
                        <!-- Resultados do autocomplete -->
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="input-group">
                        <label>Quantidade</label>
                        <input type="number" id="item_qtd" value="1.0000" step="0.0001" class="form-control">
                    </div>
                    <div class="input-group">
                        <label>Valor Unitário</label>
                        <input type="text" id="item_vlr_unit" placeholder="R$ 0,0000" class="form-control">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="input-group">
                        <label>CFOP</label>
                        <input type="text" id="item_cfop" placeholder="Ex: 5102" class="form-control">
                    </div>
                    <div class="input-group">
                        <label>NCM</label>
                        <input type="text" id="item_ncm" placeholder="Ex: 0201.30.00" class="form-control" readonly>
                    </div>
                </div>

                <div style="padding: 15px; background: var(--bg-color); border-radius: 10px; border: 1px solid var(--border-color);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h4 style="font-size: 14px; font-weight: 700;">Simulação de Impostos</h4>
                        <button type="button" class="btn btn-ghost btn-sm" onclick="calcularImpostos()">🔄 Calcular Impostos</button>
                    </div>
                    <div id="impostos-preview" style="font-family: monospace; font-size: 12px; color: var(--text-muted);">
                        Clique em calcular para ver a simulação tributária...
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-item')">Cancelar</button>
            <button class="btn btn-primary" onclick="salvarItem()">Adicionar à Nota</button>
        </div>
    </div>
</div>

<script>
    // Máscaras
    const moneyConfig = {
        mask: 'R$ num',
        blocks: {
            num: {
                mask: Number,
                thousandsSeparator: '.',
                padFractionalZeros: true,
                radix: ',',
                scale: 4
            }
        }
    };

    const maskVlrUnit = IMask(document.getElementById('item_vlr_unit'), moneyConfig);

    function calcularImpostos() {
        const payload = {
            produto_id: document.getElementById('produto_id').value,
            quantidade: document.getElementById('item_qtd').value,
            valor_unitario: document.getElementById('item_vlr_unit').value,
            participante_id: document.getElementById('cliente_id').value,
            filial_id: window.FILIAL_ID // já existe na sessão/layout
        };

        fetch('/api/fiscal/preview', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(res => {
                if (res.error) {
                    alert(res.error);
                    return;
                }

                document.getElementById('item_cfop').value = res.cfop;
                document.getElementById('impostos-preview').innerText =
                    JSON.stringify(res.impostos, null, 2);
            });
    }


    // Lógica de Transporte
    function toggleTransportadora() {
        const isPropria = document.getElementById('transp_propria').checked;
        const box = document.getElementById('transportadora-box');
        box.style.display = isPropria ? 'none' : 'block';
    }

    // Autocomplete de Transportadora
    const transpBusca = document.getElementById('transportadora_busca');
    const transpResultados = document.getElementById('transportadora_resultados');
    const transpId = document.getElementById('transportadora_id');

    let transpTimeout = null;

    transpBusca.addEventListener('input', (e) => {
        const q = e.target.value.trim();
        clearTimeout(transpTimeout);

        if (q.length < 2) {
            transpResultados.style.display = 'none';
            return;
        }

        transpTimeout = setTimeout(() => {
            fetch(`/api/participantes/search?q=${encodeURIComponent(q)}&tipo=transportadora`)
                .then(r => r.json())
                .then(lista => {
                    transpResultados.innerHTML = '';

                    if (!lista.length) {
                        transpResultados.style.display = 'none';
                        return;
                    }

                    lista.forEach(p => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.innerHTML = `
                        <strong>${p.label}</strong><br>
                        <small>${p.documento} • ${p.municipio}/${p.uf}</small>
                    `;
                        div.onclick = () => selecionarTransp(p);
                        transpResultados.appendChild(div);
                    });

                    transpResultados.style.display = 'block';
                });
        }, 300);
    });

    function selecionarTransp(p) {
        document.getElementById('transp-nome').innerText = p.label;
        document.getElementById('transp-doc').innerText = p.documento;
        document.getElementById('transp-info-box').style.display = 'block';

        transpBusca.parentElement.style.display = 'none';
        transpResultados.style.display = 'none';

        transpId.value = p.id;
    }

    function removerTransp() {
        document.getElementById('transp-info-box').style.display = 'none';
        transpBusca.parentElement.style.display = 'block';
        transpBusca.value = '';
        transpId.value = '';
    }


    // Simulação de Autocomplete de Cliente
    const clienteBusca = document.getElementById('cliente_busca');
    const clienteResultados = document.getElementById('cliente_resultados');
    const clienteId = document.getElementById('cliente_id');

    let clienteTimeout = null;

    clienteBusca.addEventListener('input', (e) => {
        const q = e.target.value.trim();
        clearTimeout(clienteTimeout);

        if (q.length < 2) {
            clienteResultados.style.display = 'none';
            return;
        }

        clienteTimeout = setTimeout(() => {
            fetch(`/api/participantes/search?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(lista => {
                    clienteResultados.innerHTML = '';

                    if (!lista.length) {
                        clienteResultados.style.display = 'none';
                        return;
                    }

                    lista.forEach(p => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.innerHTML = `
                        <strong>${p.label}</strong><br>
                        <small>${p.documento} • ${p.municipio}/${p.uf}</small>
                    `;
                        div.onclick = () => selecionarCliente(p);
                        clienteResultados.appendChild(div);
                    });

                    clienteResultados.style.display = 'block';
                });
        }, 300);
    });

    function selecionarCliente(p) {
        document.getElementById('cliente-nome').innerText = p.label;
        document.getElementById('cliente-doc').innerText = p.documento;
        document.getElementById('cliente-endereco').innerText =
            `${p.municipio}/${p.uf}`;

        document.getElementById('cliente-info-box').style.display = 'block';
        clienteBusca.parentElement.style.display = 'none';
        clienteResultados.style.display = 'none';

        clienteId.value = p.id;
    }

    function removerCliente() {
        document.getElementById('cliente-info-box').style.display = 'none';
        clienteBusca.parentElement.style.display = 'block';
        clienteBusca.value = '';
        clienteId.value = '';
    }
</script>
<script>
    const prodBusca = document.getElementById('produto_busca');
    const prodResultados = document.getElementById('produto_resultados');

    let prodTimeout = null;

    prodBusca.addEventListener('input', e => {
        const q = e.target.value.trim();
        clearTimeout(prodTimeout);

        if (q.length < 2) {
            prodResultados.style.display = 'none';
            return;
        }

        prodTimeout = setTimeout(() => {
            fetch(`/api/produtos/search?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(lista => {
                    prodResultados.innerHTML = '';

                    if (!lista.length) {
                        prodResultados.style.display = 'none';
                        return;
                    }

                    lista.forEach(p => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.innerHTML = `
                        <strong>${p.label}</strong><br>
                        <small>${p.codigo} • NCM ${p.ncm}</small>
                    `;
                        div.onclick = () => selecionarProduto(p);
                        prodResultados.appendChild(div);
                    });

                    prodResultados.style.display = 'block';
                });
        }, 300);
    });

    function selecionarProduto(p) {
        document.getElementById('produto_id').value = p.id;
        document.getElementById('produto_busca').value = p.label;
        document.getElementById('item_ncm').value = p.ncm;
        document.getElementById('item_vlr_unit').value = p.preco;

        prodResultados.style.display = 'none';
    }

    function salvarItem() {
        fetch(`/nfe/${vendaId}/itens`, {
                method: 'POST',
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(res => {
                const itemId = res.item.id;

                // AGORA SIM calcula imposto
                return fetch(`/vendas/${vendaId}/itens/${itemId}/calcular-impostos`, {
                    method: 'POST'
                });
            })
            .then(r => r.json())
            .then(impostoRes => {
                atualizarItemNaTabela(impostoRes.impostos);
            });
    }
</script>
<script>
    function atualizarTotais() {
        let totalProdutos = 0;
        let totalIcms = 0;
        let totalPis = 0;
        let totalCofins = 0;

        document.querySelectorAll('#itens-container tr').forEach(tr => {
            totalProdutos += parseFloat(tr.dataset.total || 0);
            totalIcms += parseFloat(tr.dataset.icms || 0);
            totalPis += parseFloat(tr.dataset.pis || 0);
            totalCofins += parseFloat(tr.dataset.cofins || 0);
        });

        document.getElementById('total-produtos').innerText = totalProdutos.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
        document.getElementById('total-icms').innerText = totalIcms.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
        document.getElementById('total-pis').innerText = totalPis.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
        document.getElementById('total-cofins').innerText = totalCofins.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
        document.getElementById('total-nfe').innerText =
            (totalProdutos + totalIcms).toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
    }
</script>

<?php
$content = ob_get_clean();
$title = 'Nova NF-e';
$titletopbar = 'Nova NF-e';
include __DIR__ . '/../layouts/app.php';

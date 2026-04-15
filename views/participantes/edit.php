<?php ob_start(); ?>
<?php
    use App\Helpers\MaskHelper;
?>
<style>
    .address-card {
        background: var(--bg-main);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        position: relative;
        transition: all 0.3s ease;
    }

    .address-card:hover {
        border-color: var(--primary-color);
    }

    .address-badge {
        position: absolute;
        top: -10px;
        left: 20px;
        background: var(--primary-color);
        color: white;
        padding: 2px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .remove-address {
        position: absolute;
        top: 15px;
        right: 15px;
        color: var(--danger-color);
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
    }

    .remove-address:hover {
        text-decoration: underline;
    }

    .main-address {
        border-left: 4px solid var(--primary-color);
    }

    .input-error {
        border-color: #ef4444 !important;
        background: #fff5f5;
    }

    .error-message {
        font-size: 12px;
        color: #ef4444;
        margin-top: 5px;
    }

    .btn-loading {
        opacity: 0.7;
        pointer-events: none;
    }

    .spinner {
        width: 16px;
        height: 16px;
        border: 2px solid white;
        border-top: 2px solid transparent;
        border-radius: 50%;
        display: inline-block;
        animation: spin 0.6s linear infinite;
        margin-right: 8px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .spinner-mini {
        width: 12px;
        height: 12px;
        border: 2px solid #ccc;
        border-top: 2px solid #333;
        border-radius: 50%;
        display: inline-block;
        animation: spin 0.6s linear infinite;
        margin-right: 6px;
    }

    .input-wrapper {
        position: relative;
    }

    .input-loader {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);

        width: 16px;
        height: 16px;

        border: 2px solid #ccc;
        border-top: 2px solid #333;
        border-radius: 50%;

        animation: spin 0.6s linear infinite;

        display: none;
    }

    .input-loading .input-loader {
        display: block;
    }

    .input-loading input {
        padding-right: 35px;
        /* espaço pro loader */
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
<div class="top-bar-header">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="/participantes" class="btn btn-ghost" style="padding: 8px;">← Voltar</a>
        <h1>Editar Participante</h1>
    </div>
    <div class="top-bar-actions">
        <button type="submit" form="main-form" id="btn-submit" class="btn btn-primary"> <span class="btn-text">Salvar Alterações</span></button>
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
<form id="main-form" method="post" action="/participantes/<?= $participante->getId() ?>">
    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 25px; align-items: start;">

        <!-- Coluna Principal: Dados e Endereços -->
        <div style="display: flex; flex-direction: column; gap: 25px;">

            <!-- Card: Identificação -->
            <section class="stat-card">
                <h2 class="panel-title">Identificação</h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">

                    <div class="form-group" style="grid-column: span 2;">
                        <label class="group-label">CPF / CNPJ</label>
                        <div class="input-wrapper">
                            <input type="text" class="form-control" name="cpf_cnpj" id="cpf_cnpj" value="<?=  MaskHelper::cpfCnpj(htmlspecialchars($participante->getCpfCnpj())) ?>" required placeholder="Somente números" maxlength="18">
                            <span class="input-loader" id="doc-loader"></span>
                        </div>
                        <div id="doc-loading" style="display:none; font-size:12px; color:#666; margin-top:5px;">
                            <span class="spinner-mini"></span> Buscando dados...
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="group-label">Nome ou Razão Social *</label>
                        <input type="text" class="form-control" name="nome_razao" value="<?= htmlspecialchars($participante->getNomeRazao()) ?>" required placeholder="Ex: João Silva ou Empresa LTDA">
                    </div>
                    <div class="form-group">
                        <label class="group-label">Nome Fantasia</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($participante->getNomeFantasia() ?? '') ?>" name="nome_fantasia" placeholder="Nome comercial">
                    </div>

                </div>
            </section>

            <!-- Card: Dados Fiscais -->
            <section class="stat-card">
                <h2 class="panel-title">Dados Fiscais</h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label class="group-label">Indicador IE Destinatário</label>
                        <select class="form-control" name="ind_iedest">
                            <option value="1" <?= $participante->getIndIeDest() == 1 ? 'selected' : '' ?>>Contribuinte ICMS</option>
                            <option value="2" <?= $participante->getIndIeDest() == 2 ? 'selected' : '' ?>>Isento</option>
                            <option value="9" <?= $participante->getIndIeDest() == 9 ? 'selected' : '' ?>>Não Contribuinte</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="group-label">Inscrição Estadual</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($participante->getIe() ?? '') ?>" name="ie" placeholder="IE">
                    </div>
                </div>
            </section>
            <?php
            $enderecos = $participante->getEnderecoJson() ?? [];
            $principal = $enderecos['principal'] ?? [];

            //print_r($enderecos);
            ?>
            <!-- Card: Endereços -->
            <section class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 class="panel-title" style="margin-bottom: 0;">Endereços</h2>
                    <button type="button" class="btn btn-ghost" onclick="addAddress()" style="font-size: 12px;">
                        + Adicionar Outro Endereço
                    </button>
                </div>

                <div id="address-container">

                    <!-- ================= ENDEREÇO PRINCIPAL ================= -->
                    <div class="address-card main-address" id="main-address-card">
                        <span class="address-badge">Endereço Principal</span>

                        <input type="hidden" name="enderecos[principal][pais]" value="1058">

                        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 15px; margin-top: 10px;">
                            <div class="form-group">
                                <label class="group-label" style="font-size: 10px;">CEP</label>
                                <input type="text" class="form-control"
                                    name="enderecos[principal][cep]"
                                    value="<?= htmlspecialchars($principal['cep'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="group-label" style="font-size: 10px;">Logradouro</label>
                                <input type="text" class="form-control"
                                    name="enderecos[principal][logradouro]"
                                    value="<?= htmlspecialchars($principal['logradouro'] ?? '') ?>">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 100px 1fr; gap: 15px; margin-top: 10px;">
                            <div class="form-group">
                                <label class="group-label" style="font-size: 10px;">Número</label>
                                <input type="text" class="form-control"
                                    name="enderecos[principal][numero]"
                                    value="<?= htmlspecialchars($principal['numero'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="group-label" style="font-size: 10px;">Complemento</label>
                                <input type="text" class="form-control"
                                    name="enderecos[principal][complemento]"
                                    value="<?= htmlspecialchars($principal['complemento'] ?? '') ?>">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 80px; gap: 15px; margin-top: 10px;">
                            <div class="form-group">
                                <label class="group-label" style="font-size: 10px;">Bairro</label>
                                <input type="text" class="form-control"
                                    name="enderecos[principal][bairro]"
                                    value="<?= htmlspecialchars($principal['bairro'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="group-label" style="font-size: 10px;">Município</label>
                                <input type="text" class="form-control"
                                    name="enderecos[principal][municipio]"
                                    value="<?= htmlspecialchars($principal['municipio'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="group-label" style="font-size: 10px;">Cód. IBGE</label>
                                <input type="text" class="form-control"
                                    name="enderecos[principal][cod_municipio]"
                                    value="<?= htmlspecialchars($principal['cod_municipio'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label class="group-label" style="font-size: 10px;">UF</label>
                                <input type="text" class="form-control"
                                    name="enderecos[principal][uf]"
                                    maxlength="2"
                                    value="<?= htmlspecialchars($principal['uf'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- ================= ENDEREÇOS ADICIONAIS ================= -->
                    <?php foreach ($enderecos as $tipo => $endereco): ?>
                        <?php if ($tipo === 'principal') continue; ?>

                        <div class="address-card additional-address">
                            <span class="address-badge"><?= ucfirst(str_replace('_', ' ', $tipo)) ?></span>
                            <span class="remove-address" onclick="removeAddress(this)">Remover</span>

                            <input type="hidden"
                                class="addr-pais"
                                name="enderecos[<?= $tipo ?>][pais]"
                                value="<?= htmlspecialchars($endereco['pais'] ?? '1058') ?>">

                            <div style="display: grid; grid-template-columns: 150px 1fr; gap: 15px; margin-top: 10px;">
                                <div class="form-group">
                                    <label class="group-label" style="font-size: 10px;">CEP</label>
                                    <input type="text" class="form-control addr-cep"
                                        name="enderecos[<?= $tipo ?>][cep]"
                                        value="<?= htmlspecialchars($endereco['cep'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label class="group-label" style="font-size: 10px;">Logradouro</label>
                                    <input type="text" class="form-control addr-logradouro"
                                        name="enderecos[<?= $tipo ?>][logradouro]"
                                        value="<?= htmlspecialchars($endereco['logradouro'] ?? '') ?>">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 100px 1fr; gap: 15px; margin-top: 10px;">
                                <div class="form-group">
                                    <label class="group-label" style="font-size: 10px;">Número</label>
                                    <input type="text" class="form-control addr-numero"
                                        name="enderecos[<?= $tipo ?>][numero]"
                                        value="<?= htmlspecialchars($endereco['numero'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label class="group-label" style="font-size: 10px;">Complemento</label>
                                    <input type="text" class="form-control addr-complemento"
                                        name="enderecos[<?= $tipo ?>][complemento]"
                                        value="<?= htmlspecialchars($endereco['complemento'] ?? '') ?>">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 80px; gap: 15px; margin-top: 10px;">
                                <div class="form-group">
                                    <label class="group-label" style="font-size: 10px;">Bairro</label>
                                    <input type="text" class="form-control addr-bairro"
                                        name="enderecos[<?= $tipo ?>][bairro]"
                                        value="<?= htmlspecialchars($endereco['bairro'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label class="group-label" style="font-size: 10px;">Município</label>
                                    <input type="text" class="form-control addr-municipio"
                                        name="enderecos[<?= $tipo ?>][municipio]"
                                        value="<?= htmlspecialchars($endereco['municipio'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label class="group-label" style="font-size: 10px;">Cód. IBGE</label>
                                    <input type="text" class="form-control addr-cod-municipio"
                                        name="enderecos[<?= $tipo ?>][cod_municipio]"
                                        value="<?= htmlspecialchars($endereco['cod_municipio'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label class="group-label" style="font-size: 10px;">UF</label>
                                    <input type="text" class="form-control addr-uf"
                                        maxlength="2"
                                        name="enderecos[<?= $tipo ?>][uf]"
                                        value="<?= htmlspecialchars($endereco['uf'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>
            </section>
        </div>

        <!-- Coluna Lateral: Configurações e Contato -->
        <div style="display: flex; flex-direction: column; gap: 25px;">
            <?php $tipos = $participante->getTipoCadastro(); ?>
            <!-- Card: Tipo de Cadastro -->
            <section class="stat-card">
                <h2 class="panel-title">Tipo de Cadastro</h2>
                <div style="display: flex; flex-direction: column; gap: 10px; background: var(--bg-main); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color);">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="tipo_cadastro[]" value="CLIENTE" <?= in_array('CLIENTE', $tipos) ? 'checked' : '' ?>> <span>Cliente</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="tipo_cadastro[]" value="FORNECEDOR" <?= in_array('FORNECEDOR', $tipos) ? 'checked' : '' ?>> <span>Fornecedor</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="tipo_cadastro[]" value="TRANSPORTADORA" <?= in_array('TRANSPORTADORA', $tipos) ? 'checked' : '' ?>> <span>Transportadora</span>
                    </label>
                </div>
            </section>

            <!-- Card: Contato -->
            <section class="stat-card">
                <h2 class="panel-title">Contato</h2>
                <div class="form-group">
                    <label class="group-label">Telefone</label>
                    <input type="text" class="form-control" name="telefone" value="<?= $participante->getTelefone()  ?>" placeholder="(00) 00000-0000">
                </div>
                <div class="form-group" style="margin-top: 15px;">
                    <label class="group-label">E-mail</label>
                    <input type="email" class="form-control" name="email" value="<?= $participante->getEmail();  ?>" placeholder="email@exemplo.com">
                </div>
            </section>

            <!-- Card: Status -->
            <section class="stat-card">
                <h2 class="panel-title">Status</h2>
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; background: var(--bg-main); border-radius: 8px;">
                    <span style="font-size: 14px; font-weight: 500;">Cadastro Ativo</span>
                    <label class="switch">
                        <input type="checkbox" name="ativo" value="1" <?php if ($participante->isAtivo()) echo 'checked'; ?>>
                        <span class="slider"></span>
                    </label>
                </div>
            </section>
        </div>
    </div>
</form>

<!-- Template de Endereço Adicional (Invisível) -->
<template id="address-template">
    <div class="address-card additional-address">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <span class="address-badge">Endereço</span>
            <span class="remove-address">Remover</span>
        </div>

        <!-- TIPO DO ENDEREÇO -->
        <div class="form-group" style="margin-top:10px;">
            <label class="group-label" style="font-size:10px;">Tipo do Endereço</label>
            <select class="form-control addr-tipo">
                <option value="">Selecione</option>
                <option value="entrega">Entrega</option>
                <option value="cobranca">Cobrança</option>
                <option value="retirada">Retirada</option>
                <option value="outro">Outro</option>
            </select>
        </div>

        <input type="hidden" class="addr-pais" value="1058">

        <!-- CEP / LOGRADOURO -->
        <div style="display:grid; grid-template-columns:150px 1fr; gap:15px; margin-top:10px;">
            <div class="form-group">
                <label class="group-label" style="font-size:10px;">CEP</label>
                <input type="text" class="form-control addr-cep">
            </div>
            <div class="form-group">
                <label class="group-label" style="font-size:10px;">Logradouro</label>
                <input type="text" class="form-control addr-logradouro">
            </div>
        </div>

        <!-- NÚMERO / COMPLEMENTO -->
        <div style="display:grid; grid-template-columns:100px 1fr; gap:15px; margin-top:10px;">
            <div class="form-group">
                <label class="group-label" style="font-size:10px;">Número</label>
                <input type="text" class="form-control addr-numero">
            </div>
            <div class="form-group">
                <label class="group-label" style="font-size:10px;">Complemento</label>
                <input type="text" class="form-control addr-complemento">
            </div>
        </div>

        <!-- BAIRRO / MUNICÍPIO / IBGE / UF -->
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr 80px; gap:15px; margin-top:10px;">
            <div class="form-group">
                <label class="group-label" style="font-size:10px;">Bairro</label>
                <input type="text" class="form-control addr-bairro">
            </div>
            <div class="form-group">
                <label class="group-label" style="font-size:10px;">Município</label>
                <input type="text" class="form-control addr-municipio">
            </div>
            <div class="form-group">
                <label class="group-label" style="font-size:10px;">Cód. IBGE</label>
                <input type="text" class="form-control addr-cod-municipio">
            </div>
            <div class="form-group">
                <label class="group-label" style="font-size:10px;">UF</label>
                <input type="text" class="form-control addr-uf" maxlength="2">
            </div>
        </div>
    </div>
</template>

<script>
    function addAddress() {
        const container = document.getElementById('address-container');
        const template = document.getElementById('address-template');
        const clone = template.content.cloneNode(true);
        container.appendChild(clone);
        updateAddressNames();
    }

    document.addEventListener('click', function(e) {
        if (!e.target.classList.contains('remove-address')) return;

        const card = e.target.closest('.address-card.additional-address');
        if (!card) return;

        card.remove();
        updateAddressNames();
    });

    function updateAddressNames() {
        const cards = document.querySelectorAll('.address-card.additional-address');

        cards.forEach((card) => {

            const tipoSelect = card.querySelector('.addr-tipo');

            // 🔥 PROTEÇÃO
            if (!tipoSelect) return;

            const tipo = tipoSelect.value;

            if (!tipo) return;

            const badge = card.querySelector('.address-badge');
            if (badge) {
                badge.textContent =
                    'Endereço ' + tipo.charAt(0).toUpperCase() + tipo.slice(1);
            }

            const setName = (selector, name) => {
                const el = card.querySelector(selector);
                if (el) el.name = name;
            };

            setName('.addr-pais', `enderecos[${tipo}][pais]`);
            setName('.addr-cep', `enderecos[${tipo}][cep]`);
            setName('.addr-logradouro', `enderecos[${tipo}][logradouro]`);
            setName('.addr-numero', `enderecos[${tipo}][numero]`);
            setName('.addr-complemento', `enderecos[${tipo}][complemento]`);
            setName('.addr-bairro', `enderecos[${tipo}][bairro]`);
            setName('.addr-municipio', `enderecos[${tipo}][municipio]`);
            setName('.addr-cod-municipio', `enderecos[${tipo}][cod_municipio]`);
            setName('.addr-uf', `enderecos[${tipo}][uf]`);
        });
    }


    // Inicializar nomes se já houver algum (embora comece vazio)
    updateAddressNames();

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('addr-tipo')) {
            updateAddressNames();
        }
    });

    function showDocLoading() {
        const el = document.getElementById('doc-loading');
        if (el) el.style.display = 'block';
    }

    function hideDocLoading() {
        const el = document.getElementById('doc-loading');
        if (el) el.style.display = 'none';
    }

    function showInputLoader() {
        document.querySelector('.input-wrapper').classList.add('input-loading');
    }

    function hideInputLoader() {
        document.querySelector('.input-wrapper').classList.remove('input-loading');
    }

    function mostrarFeedback(msg, tipo = 'info') {
        const div = document.createElement('div');

        div.innerText = msg;
        div.style.position = 'fixed';
        div.style.bottom = '20px';
        div.style.right = '20px';
        div.style.padding = '10px 15px';
        div.style.borderRadius = '8px';
        div.style.color = '#fff';
        div.style.zIndex = 9999;
        div.style.fontSize = '13px';

        if (tipo === 'success') div.style.background = '#22c55e';
        else if (tipo === 'error') div.style.background = '#ef4444';
        else if (tipo === 'warning') div.style.background = '#f59e0b';
        else div.style.background = '#3b82f6';

        document.body.appendChild(div);

        setTimeout(() => div.remove(), 3000);
    }

    document.getElementById('cpf_cnpj').addEventListener('blur', function() {
        const doc = this.value.replace(/\D/g, '');

        if (doc.length !== 14) return;

        showDocLoading();
        showInputLoader();

        // 1️⃣ Busca interna
        fetch('/participantes/buscar-doc?cpf_cnpj=' + doc)
            .then(r => r.json())
            .then(data => {

                if (data) {
                    hideDocLoading();
                    hideInputLoader();
                    preencherFormulario(data);

                    if (data.cadastro_duplicar) {
                        //mostrarFeedback('⚠️ Participante encontrado, mas com cadastro duplicado. Verifique os dados antes de salvar.', 'warning');
                        buscarDadosExternos(doc);
                        console.log('aqui');
                        return true;
                    } else {
                        mostrarFeedback('Participante já cadastrado ✔', 'success');
                        return false;
                    }

                }
                console.log('passo');
                // 2️⃣ Busca externa

                buscarDadosExternos(doc);

            })
            .catch(() => {
                hideDocLoading();
                hideInputLoader();
                mostrarFeedback('Erro ao buscar dados ❌', 'error');
            });
    });

    function buscarDadosExternos(doc) {
        fetch('/participantes/buscar-cnpj-externo?cnpj=' + doc)
            .then(r => r.json())
            .then(api => {

                hideDocLoading();
                hideInputLoader();

                if (!api) {
                    mostrarFeedback('Nenhum dado encontrado', 'warning');
                    return;
                }

                mostrarFeedback('Dados carregados da Receita ✔', 'success');

                document.querySelector('[name="nome_razao"]').value = api.nome_razao ?? '';
                document.querySelector('[name="nome_fantasia"]').value = api.nome_fantasia ?? '';
                document.querySelector('[name="telefone"]').value = api.telefone ?? '';
                document.querySelector('[name="email"]').value = api.email ?? '';
                document.querySelector('[name="ie"]').value = api.inscricao_estadual ?? '';

                if (api.endereco) {
                    const e = api.endereco;

                    console.log('Endereço encontrado:', e);

                    document.querySelector('[name="enderecos[principal][cep]"]').value = e.cep ?? '';
                    document.querySelector('[name="enderecos[principal][logradouro]"]').value = e.logradouro ?? '';
                    document.querySelector('[name="enderecos[principal][numero]"]').value = e.numero ?? '';
                    document.querySelector('[name="enderecos[principal][complemento]"]').value = e.complemento ?? '';
                    document.querySelector('[name="enderecos[principal][bairro]"]').value = e.bairro ?? '';
                    document.querySelector('[name="enderecos[principal][municipio]"]').value = e.municipio ?? '';
                    document.querySelector('[name="enderecos[principal][uf]"]').value = e.uf ?? '';
                    document.querySelector('[name="enderecos[principal][cod_municipio]"]').value = e.codigo_municipio ?? '';

                }
            });
    }

    function preencherFormulario(data) {
        document.querySelector('[name="nome_razao"]').value = data.nome_razao ?? '';
        document.querySelector('[name="nome_fantasia"]').value = data.nome_fantasia ?? '';
        document.querySelector('[name="telefone"]').value = data.telefone ?? '';
        document.querySelector('[name="email"]').value = data.email ?? '';
    }

    document.addEventListener('focusout', function(e) {

        // CEP do endereço principal
        const isPrincipal =
            e.target.name === 'enderecos[principal][cep]';

        // CEP dos endereços adicionais
        const isAdicional =
            e.target.classList.contains('addr-cep');

        if (!isPrincipal && !isAdicional) return;

        const cep = e.target.value.replace(/\D/g, '');
        if (cep.length !== 8) return;

        fetch('/enderecos/buscar-cep?cep=' + cep)
            .then(r => r.json())
            .then(data => {
                if (!data) return;

                const card = e.target.closest('.address-card');

                const setValue = (selector, value) => {
                    const el = card.querySelector(selector);
                    if (el && !el.value) {
                        el.value = value ?? '';
                    }
                };

                setValue('[name$="[logradouro]"], .addr-logradouro', data.logradouro);
                setValue('[name$="[bairro]"], .addr-bairro', data.bairro);
                setValue('[name$="[municipio]"], .addr-municipio', data.municipio);
                setValue('[name$="[uf]"], .addr-uf', data.uf);
                setValue('[name$="[cod_municipio]"], .addr-cod-municipio', data.cod_municipio);
            })
            .catch(() => {
                console.warn('Erro ao buscar CEP');
            });
    });



    /* ========================================
       🎨 ERRO VISUAL
    ======================================== */

    function setError(input, message) {
        clearError(input);

        input.classList.add('input-error');

        const msg = document.createElement('div');
        msg.className = 'error-message';
        msg.innerText = message;

        input.parentElement.appendChild(msg);
    }

    function clearError(input) {
        input.classList.remove('input-error');

        const old = input.parentElement.querySelector('.error-message');
        if (old) old.remove();
    }


    /* ========================================
       🧠 VALIDAÇÃO POR CAMPO
    ======================================== */

    function validarCampo(input) {
        const name = input.name;
        const value = input.value.trim();

        clearError(input);

        if (name === 'nome_razao' && !value) {
            setError(input, 'Nome obrigatório');
            return false;
        }

        if (name === 'cpf_cnpj') {
            const doc = value.replace(/\D/g, '');

            if (doc && doc.length !== 11 && doc.length !== 14) {
                setError(input, 'CPF/CNPJ inválido');
                return false;
            }
        }


        if (input.name === 'ie') {

            const tipo = document.querySelector('[name="ind_iedest"]').value;
            const value = input.value.trim();

            if (tipo === '1' && !value) {
                setError(input, 'IE obrigatória para contribuinte');
                return false;
            }

            if (tipo === '9' && value) {
                setError(input, 'Não contribuinte não deve ter IE');
                return false;
            }
        }

        const labelsEndereco = {
            cep: 'CEP',
            logradouro: 'Logradouro',
            numero: 'Número',
            bairro: 'Bairro',
            municipio: 'Município',
            uf: 'UF'
        };

        const camposEnderecoObrigatorios = [
            'cep',
            'logradouro',
            'numero',
            'bairro',
            'municipio',
            'uf',
            'cod_municipio'
        ];

        // console.log('Validando campo:', name, 'com valor:', value);
        // 🔥 VALIDA ENDEREÇO PRINCIPAL
        if (name.startsWith('enderecos[principal]')) {

            const match = name.match(/\[principal\]\[(.*?)\]/);
            const campo = match ? match[1] : null;

            if (campo && camposEnderecoObrigatorios.includes(campo)) {

                if (!value) {
                    setError(input, `${labelsEndereco[campo]} obrigatório`);
                    return false;
                }

                // valida CEP
                if (campo === 'cep') {
                    const cep = value.replace(/\D/g, '');
                    if (cep.length !== 8) {
                        setError(input, 'CEP inválido');
                        return false;
                    }
                }
            }

            console.log('Campo endereço:', campo, 'Valor:', value);
        }



        return true;
    }


    /* ========================================
       🚀 VALIDAÇÃO DO FORM
    ======================================== */

    function validarParticipante() {
        let valido = true;

        const campos = document.querySelectorAll('#main-form input, #main-form select, #main-form textarea');

        campos.forEach(input => {

            // ignora campos sem name
            if (!input.name) return;

            // ignora hidden
            if (input.type === 'hidden') return;

            const ok = validarCampo(input);

            if (!ok) {
                valido = false;
            }
        });

        console.log('Validação finalizada. Formulário é válido?', valido);

        // valida checkbox tipo cadastro
        const tipos = document.querySelectorAll('[name="tipo_cadastro[]"]:checked');
        if (tipos.length === 0) {
            alert('Selecione pelo menos um tipo');
            valido = false;
        }

        return valido;
    }


    /* ========================================
       🎯 VALIDAÇÃO EM TEMPO REAL
    ======================================== */

    document.querySelectorAll('#main-form input, #main-form select').forEach(input => {
        input.addEventListener('blur', () => validarCampo(input));
    });


    /* ========================================
       📄 MÁSCARA CPF/CNPJ
    ======================================== */

    const docInput = document.getElementById('cpf_cnpj');

    if (docInput) {
        docInput.addEventListener('input', () => {

            let v = docInput.value.replace(/\D/g, '');

            // if (v.length > 11) {
            //     docInput.placeholder = 'CNPJ';
            // } else {
            //     docInput.placeholder = 'CPF';
            // }

            // limita
            if (v.length > 14) v = v.slice(0, 14);

            // 🔥 REGRA IMPORTANTE:
            // até 11 → CPF
            // acima disso → CNPJ (sem travar antes)
            if (v.length <= 11) {

                // CPF
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');

            } else {

                // CNPJ
                v = v.replace(/^(\d{2})(\d)/, '$1.$2');
                v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
                v = v.replace(/(\d{4})(\d)/, '$1-$2');
            }

            docInput.value = v;
        });

        docInput.addEventListener('blur', () => {
            const tipo = detectarDocumento(docInput.value);

            if (!tipo) {
                setError(docInput, 'CPF ou CNPJ inválido');
                return;
            }

            clearError(docInput);

            console.log('Tipo detectado:', tipo);
        });
    }

    function validarCPF(cpf) {
        if (/^(\d)\1+$/.test(cpf)) return false;

        let soma = 0;
        for (let i = 0; i < 9; i++) {
            soma += parseInt(cpf[i]) * (10 - i);
        }

        let resto = (soma * 10) % 11;
        if (resto === 10) resto = 0;
        if (resto !== parseInt(cpf[9])) return false;

        soma = 0;
        for (let i = 0; i < 10; i++) {
            soma += parseInt(cpf[i]) * (11 - i);
        }

        resto = (soma * 10) % 11;
        if (resto === 10) resto = 0;

        return resto === parseInt(cpf[10]);
    }

    function validarCNPJ(cnpj) {
        if (/^(\d)\1+$/.test(cnpj)) return false;

        const calc = (base, pesos) => {
            let soma = 0;
            for (let i = 0; i < pesos.length; i++) {
                soma += base[i] * pesos[i];
            }
            const resto = soma % 11;
            return resto < 2 ? 0 : 11 - resto;
        };

        const base = cnpj.slice(0, 12).split('').map(Number);

        const dig1 = calc(base, [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);
        const dig2 = calc([...base, dig1], [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);

        return dig1 === Number(cnpj[12]) && dig2 === Number(cnpj[13]);
    }

    function detectarDocumento(valor) {
        const v = valor.replace(/\D/g, '');

        if (v.length === 11 && validarCPF(v)) {
            return 'CPF';
        }

        if (v.length === 14 && validarCNPJ(v)) {
            return 'CNPJ';
        }

        return null;
    }


    /* ========================================
       💾 AUTOSAVE (LOCALSTORAGE)
    ======================================== */

    document.querySelectorAll('#main-form input, #main-form select').forEach(el => {

        el.addEventListener('input', () => {
            if (!el.name) return;
            localStorage.setItem('form_' + el.name, el.value);
        });

        const saved = localStorage.getItem('form_' + el.name);
        if (saved && !el.value) {
            el.value = saved;
        }
    });


    function limparStorageForm() {
        document.querySelectorAll('#main-form input, #main-form select').forEach(el => {
            if (!el.name) return;
            localStorage.removeItem('form_' + el.name);
        });
    }


    /* ========================================
       🔥 LOADING + BLOQUEIO DE DUPLO SUBMIT
    ======================================== */

    const form = document.getElementById('main-form');
    const btn = document.getElementById('btn-submit');

    if (form && btn) {

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Validando participante antes de enviar...');
            if (!validarParticipante()) return;

            if (btn.classList.contains('btn-loading')) return;

            ativarLoading();
            limparStorageForm();

            form.submit();
        });

        form.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && btn.classList.contains('btn-loading')) {
                e.preventDefault();
            }
        });
    }


    function ativarLoading() {
        const text = btn.querySelector('.btn-text');

        btn.classList.add('btn-loading');

        text.innerHTML = `
        <span class="spinner"></span>
        Salvando...
    `;
    }

    function desativarLoading() {
        const text = btn.querySelector('.btn-text');

        btn.classList.remove('btn-loading');
        text.innerText = 'Salvar Cadastro';
    }


    document.querySelector('[name="ind_iedest"]').addEventListener('change', function() {

        const ieInput = document.querySelector('[name="ie"]');
        const tipo = this.value;

        if (tipo === '1') {
            ieInput.removeAttribute('readonly');
            ieInput.value = '';
        }

        if (tipo === '2') {
            ieInput.value = 'ISENTO';
            ieInput.setAttribute('readonly', true);
        }

        if (tipo === '9') {
            ieInput.value = '';
            ieInput.setAttribute('readonly', true);
        }

    });


    /* ========================================
       📢 SCROLL PARA ALERTAS
    ======================================== */

    window.addEventListener('load', () => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    });


    /* =====================================================
   🧠 TRAVAR CIDADE POR PADRÃO
===================================================== */

    function travarCamposCidade() {
        document.querySelectorAll('.addr-municipio, [name$="[municipio]"]')
            .forEach(input => input.setAttribute('readonly', true));
    }

    travarCamposCidade();


    /* =====================================================
       🔄 LIMPAR IBGE SE ALTERAR CIDADE
    ===================================================== */

    document.addEventListener('input', function(e) {

        if (e.target.matches('.addr-municipio, [name$="[municipio]"]')) {

            const card = e.target.closest('.address-card');
            const cod = card.querySelector('[name$="[cod_municipio]"], .addr-cod-municipio');

            if (cod) cod.value = '';
        }
    });


    /* =====================================================
       🔎 BUSCA MUNICÍPIOS
    ===================================================== */

    function buscarMunicipios(q, callback) {
        fetch(`/api/municipios?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(callback)
            .catch(() => callback([]));
    }


    /* =====================================================
       🧠 AUTOCOMPLETE MUNICÍPIO
    ===================================================== */

    document.addEventListener('focusin', function(e) {

        if (!e.target.matches('.addr-municipio, [name$="[municipio]"]')) return;

        const input = e.target;
        const card = input.closest('.address-card');

        let box = card.querySelector('.municipio-resultados');

        if (!box) {
            box = document.createElement('div');
            box.className = 'autocomplete-results municipio-resultados';
            input.parentElement.appendChild(box);
        }

        input.addEventListener('input', () => {

            const q = input.value.trim();

            if (q.length < 2) {
                box.style.display = 'none';
                return;
            }

            buscarMunicipios(q, lista => {

                box.innerHTML = '';

                if (!lista.length) {
                    box.style.display = 'none';
                    return;
                }

                lista.forEach(m => {

                    const div = document.createElement('div');
                    div.className = 'autocomplete-item';

                    div.innerHTML = `
                    <strong>${m.nome}</strong><br>
                    <small>${m.uf}</small>
                `;

                    div.onclick = () => {

                        input.value = m.nome;

                        const cod = card.querySelector('[name$="[cod_municipio]"], .addr-cod-municipio');
                        const uf = card.querySelector('[name$="[uf]"], .addr-uf');

                        if (cod) cod.value = m.codigo;
                        if (uf) uf.value = m.uf;

                        input.setAttribute('readonly', true);
                        clearError(input);

                        box.style.display = 'none';
                    };

                    box.appendChild(div);
                });

                box.style.display = 'block';
            });

        });

    });


    /* =====================================================
       ❌ FECHAR AUTOCOMPLETE
    ===================================================== */

    document.addEventListener('click', function(e) {
        document.querySelectorAll('.municipio-resultados').forEach(box => {
            if (!box.contains(e.target)) {
                box.style.display = 'none';
            }
        });
    });

    /* =====================================================
       📍 CEP INTELIGENTE (AJUSTADO)
    ===================================================== */

    document.addEventListener('blur', function(e) {

        if (!e.target.matches('.addr-cep, [name$="[cep]"]')) return;

        const input = e.target;
        const card = input.closest('.address-card');
        const cep = input.value.replace(/\D/g, '');

        if (cep.length !== 8) return;

        fetch(`/api/cep/${cep}`)
            .then(r => r.json())
            .then(data => {

                if (!data) return;

                const set = (selector, value) => {
                    const el = card.querySelector(selector);
                    if (el) el.value = value || '';
                };

                set('[name$="[logradouro]"], .addr-logradouro', data.logradouro);
                set('[name$="[bairro]"], .addr-bairro', data.bairro);
                set('[name$="[uf]"], .addr-uf', data.uf);

                const municipioInput = card.querySelector('[name$="[municipio]"], .addr-municipio');
                const codInput = card.querySelector('[name$="[cod_municipio]"], .addr-cod-municipio');

                if (municipioInput) municipioInput.value = data.municipio || '';
                if (codInput) codInput.value = data.cod_municipio || '';

                // 🔥 LÓGICA INTELIGENTE
                if (!data.cod_municipio) {

                    municipioInput.removeAttribute('readonly');
                    setError(municipioInput, 'Selecione a cidade correta');

                } else {

                    municipioInput.setAttribute('readonly', true);
                    clearError(municipioInput);
                }
            });

    }, true);


    /* =====================================================
       🔥 VALIDAÇÃO DE ENDEREÇO
    ===================================================== */

    function validarEndereco(input) {

        const name = input.name;
        const value = input.value.trim();

        if (!name || !name.includes('enderecos')) return true;

        const match = name.match(/\[(.*?)\]\[(.*?)\]/);

        if (!match) return true;

        const campo = match[2];

        const obrigatorios = [
            'cep',
            'logradouro',
            'numero',
            'bairro',
            'municipio',
            'uf',
            'cod_municipio'
        ];

        const labels = {
            cep: 'CEP',
            logradouro: 'Logradouro',
            numero: 'Número',
            bairro: 'Bairro',
            municipio: 'Município',
            uf: 'UF',
            cod_municipio: 'Cidade'
        };

        if (obrigatorios.includes(campo)) {

            if (!value) {
                setError(input, `${labels[campo]} obrigatório`);
                return false;
            }

            if (campo === 'cep') {
                const cep = value.replace(/\D/g, '');
                if (cep.length !== 8) {
                    setError(input, 'CEP inválido');
                    return false;
                }
            }

            if (campo === 'municipio') {

                const card = input.closest('.address-card');
                const cod = card.querySelector('[name$="[cod_municipio]"], .addr-cod-municipio');

                if (!cod || !cod.value) {
                    setError(input, 'Selecione a cidade da lista');
                    return false;
                }
            }
        }

        return true;
    }


    /* =====================================================
       🔗 INTEGRAR COM SUA VALIDAÇÃO
    ===================================================== */

    const oldValidarCampo = validarCampo;

    validarCampo = function(input) {

        const base = oldValidarCampo(input);

        if (!base) return false;

        return validarEndereco(input);
    };


    /* =====================================================
       🔁 REAPLICAR EM NOVOS ENDEREÇOS
    ===================================================== */

    const oldAddAddress = addAddress;

    addAddress = function() {
        oldAddAddress();

        setTimeout(() => {
            travarCamposCidade();
        }, 100);
    };
</script>
<?php
$content = ob_get_clean();
$title = 'Editar Participante';
$titletopbar = "Editar Participante";
include __DIR__ . '/../layouts/app.php';
?>
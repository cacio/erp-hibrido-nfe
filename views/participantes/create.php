<?php ob_start(); ?>
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
</style>
<div class="top-bar-header">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="/participantes" class="btn btn-ghost" style="padding: 8px;">←</a>
        <h1>Novo Participante</h1>
    </div>
    <div class="top-bar-actions">
        <button type="button" onclick="document.getElementById('main-form').submit()" class="btn btn-primary">Salvar Cadastro</button>
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
<form id="main-form" method="post" action="/participantes">
    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 25px; align-items: start;">

        <!-- Coluna Principal: Dados e Endereços -->
        <div style="display: flex; flex-direction: column; gap: 25px;">

            <!-- Card: Identificação -->
            <section class="stat-card">
                <h2 class="panel-title">Identificação</h2>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="group-label">Nome ou Razão Social *</label>
                        <input type="text" class="form-control" name="nome_razao" required placeholder="Ex: João Silva ou Empresa LTDA">
                    </div>
                    <div class="form-group">
                        <label class="group-label">Nome Fantasia</label>
                        <input type="text" class="form-control" name="nome_fantasia" placeholder="Nome comercial">
                    </div>
                    <div class="form-group">
                        <label class="group-label">CPF / CNPJ</label>
                        <input type="text" class="form-control" name="cpf_cnpj" id="cpf_cnpj" placeholder="Somente números" maxlength="14">
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
                            <option value="1">Contribuinte ICMS</option>
                            <option value="2">Isento</option>
                            <option value="9" selected>Não Contribuinte</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="group-label">Inscrição Estadual</label>
                        <input type="text" class="form-control" name="ie" placeholder="IE">
                    </div>
                </div>
            </section>

            <!-- Card: Endereços -->
            <section class="stat-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 class="panel-title" style="margin-bottom: 0;">Endereços</h2>
                    <button type="button" class="btn btn-ghost" onclick="addAddress()" style="font-size: 12px;">+ Adicionar Outro Endereço</button>
                </div>

                <div id="address-container">
                    <!-- Endereço Principal Fixo -->
                    <div class="address-card main-address" id="main-address-card">
                        <span class="address-badge">Endereço Principal</span>
                        <input type="hidden" name="enderecos[principal][pais]" value="1058">

                        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 15px; margin-top: 10px;">
                            <div class="form-group">
                                <label class="group-label" style="font-size: 10px;">CEP</label>
                                <input type="text" class="form-control" name="enderecos[principal][cep]" placeholder="00000-000" style="height: 35px; font-size: 13px;">
                            </div>
                            <div class="form-group">
                                <label class="group-label" style="font-size: 10px;">Logradouro</label>
                                <input type="text" class="form-control" name="enderecos[principal][logradouro]" placeholder="Rua, Av..." style="height: 35px; font-size: 13px;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 100px 1fr; gap: 15px; margin-top: 10px;">
                            <div class="form-group">
                                <label class="group-label" style="font-size: 10px;">Número</label>
                                <input type="text" class="form-control" name="enderecos[principal][numero]" placeholder="Nº" style="height: 35px; font-size: 13px;">
                            </div>
                            <div class="form-group">
                                <label class="group-label" style="font-size: 10px;">Complemento</label>
                                <input type="text" class="form-control" name="enderecos[principal][complemento]" placeholder="Apto, Bloco..." style="height: 35px; font-size: 13px;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 80px; gap: 15px; margin-top: 10px;">
                            <div class="form-group">
                                <label class="group-label" style="font-size: 10px;">Bairro</label>
                                <input type="text" class="form-control" name="enderecos[principal][bairro]" placeholder="Bairro" style="height: 35px; font-size: 13px;">
                            </div>
                            <div class="form-group">
                                <label class="group-label" style="font-size: 10px;">Município</label>
                                <input type="text" class="form-control" name="enderecos[principal][municipio]" placeholder="Nome da Cidade" style="height: 35px; font-size: 13px;">
                            </div>
                            <div class="form-group">
                                <label class="group-label" style="font-size: 10px;">Cód. IBGE</label>
                                <input type="text" class="form-control" name="enderecos[principal][cod_municipio]" placeholder="Código" style="height: 35px; font-size: 13px;">
                            </div>
                            <div class="form-group">
                                <label class="group-label" style="font-size: 10px;">UF</label>
                                <input type="text" class="form-control" name="enderecos[principal][uf]" maxlength="2" placeholder="UF" style="height: 35px; font-size: 13px;">
                            </div>
                        </div>
                    </div>
                    <!-- Outros endereços serão inseridos aqui -->
                </div>
            </section>
        </div>

        <!-- Coluna Lateral: Configurações e Contato -->
        <div style="display: flex; flex-direction: column; gap: 25px;">

            <!-- Card: Tipo de Cadastro -->
            <section class="stat-card">
                <h2 class="panel-title">Tipo de Cadastro</h2>
                <div style="display: flex; flex-direction: column; gap: 10px; background: var(--bg-main); padding: 15px; border-radius: 8px; border: 1px solid var(--border-color);">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="tipo_cadastro[]" value="CLIENTE"> <span>Cliente</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="tipo_cadastro[]" value="FORNECEDOR"> <span>Fornecedor</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="tipo_cadastro[]" value="TRANSPORTADORA"> <span>Transportadora</span>
                    </label>
                </div>
            </section>

            <!-- Card: Contato -->
            <section class="stat-card">
                <h2 class="panel-title">Contato</h2>
                <div class="form-group">
                    <label class="group-label">Telefone</label>
                    <input type="text" class="form-control" name="telefone" placeholder="(00) 00000-0000">
                </div>
                <div class="form-group" style="margin-top: 15px;">
                    <label class="group-label">E-mail</label>
                    <input type="email" class="form-control" name="email" placeholder="email@exemplo.com">
                </div>
            </section>

            <!-- Card: Status -->
            <section class="stat-card">
                <h2 class="panel-title">Status</h2>
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; background: var(--bg-main); border-radius: 8px;">
                    <span style="font-size: 14px; font-weight: 500;">Cadastro Ativo</span>
                    <label class="switch">
                        <input type="checkbox" name="ativo" value="1" checked>
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
            const tipo = tipoSelect.value;

            if (!tipo) return;

            card.querySelector('.address-badge').textContent =
                'Endereço ' + tipo.charAt(0).toUpperCase() + tipo.slice(1);

            card.querySelector('.addr-pais').name = `enderecos[${tipo}][pais]`;
            card.querySelector('.addr-cep').name = `enderecos[${tipo}][cep]`;
            card.querySelector('.addr-logradouro').name = `enderecos[${tipo}][logradouro]`;
            card.querySelector('.addr-numero').name = `enderecos[${tipo}][numero]`;
            card.querySelector('.addr-complemento').name = `enderecos[${tipo}][complemento]`;
            card.querySelector('.addr-bairro').name = `enderecos[${tipo}][bairro]`;
            card.querySelector('.addr-municipio').name = `enderecos[${tipo}][municipio]`;
            card.querySelector('.addr-cod-municipio').name = `enderecos[${tipo}][cod_municipio]`;
            card.querySelector('.addr-uf').name = `enderecos[${tipo}][uf]`;
        });
    }


    // Inicializar nomes se já houver algum (embora comece vazio)
    updateAddressNames();

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('addr-tipo')) {
            updateAddressNames();
        }
    });

    document.getElementById('cpf_cnpj').addEventListener('blur', function() {
        const doc = this.value.replace(/\D/g, '');

        if (doc.length !== 14) return;

        // 1️⃣ Busca interna
        fetch('/participantes/buscar-doc?cpf_cnpj=' + doc)
            .then(r => r.json())
            .then(data => {
                if (data) {
                    alert('Participante já cadastrado. Dados carregados.');
                    preencherFormulario(data);
                    return;
                }

                // 2️⃣ Busca externa
                fetch('/participantes/buscar-cnpj-externo?cnpj=' + doc)
                    .then(r => r.json())
                    .then(api => {
                        if (!api) return;

                        alert('Dados carregados da Receita. Confira antes de salvar.');

                        document.querySelector('[name="nome_razao"]').value = api.nome_razao ?? '';
                        document.querySelector('[name="nome_fantasia"]').value = api.nome_fantasia ?? '';
                        document.querySelector('[name="telefone"]').value = api.telefone ?? '';
                        document.querySelector('[name="email"]').value = api.email ?? '';

                        if (api.endereco) {
                            const e = api.endereco;
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
            });
    });

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
</script>
<?php
$content = ob_get_clean();
$title = 'Criar Participante';
$titletopbar = "Criar Novo Participante";
include __DIR__ . '/../layouts/app.php';
?>
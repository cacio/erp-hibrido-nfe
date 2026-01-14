<?php ob_start(); ?>

<div class="top-bar-header">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="/participantes" class="btn btn-ghost" style="padding: 8px;">←</a>
        <h1>Novo Participante</h1>
    </div>
    <div class="top-bar-actions">
        <button type="button" onclick="document.getElementById('main-form').submit()" class="btn btn-primary">Salvar Cadastro</button>
    </div>
</div>

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
                        <input type="text" class="form-control" name="cpf_cnpj" placeholder="Somente números" maxlength="14">
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
<!-- Template de Endereço Adicional (Invisível) -->
<template id="address-template">
    <div class="address-card additional-address">
        <span class="address-badge">Endereço Adicional</span>
        <span class="remove-address" onclick="removeAddress(this)">Remover</span>
        <input type="hidden" class="addr-pais" value="1058">

        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 15px; margin-top: 10px;">
            <div class="form-group">
                <label class="group-label" style="font-size: 10px;">CEP</label>
                <input type="text" class="form-control addr-cep" placeholder="00000-000" style="height: 35px; font-size: 13px;">
            </div>
            <div class="form-group">
                <label class="group-label" style="font-size: 10px;">Logradouro</label>
                <input type="text" class="form-control addr-logradouro" placeholder="Rua, Av..." style="height: 35px; font-size: 13px;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 100px 1fr; gap: 15px; margin-top: 10px;">
            <div class="form-group">
                <label class="group-label" style="font-size: 10px;">Número</label>
                <input type="text" class="form-control addr-numero" placeholder="Nº" style="height: 35px; font-size: 13px;">
            </div>
            <div class="form-group">
                <label class="group-label" style="font-size: 10px;">Complemento</label>
                <input type="text" class="form-control addr-complemento" placeholder="Apto, Bloco..." style="height: 35px; font-size: 13px;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 80px; gap: 15px; margin-top: 10px;">
            <div class="form-group">
                <label class="group-label" style="font-size: 10px;">Bairro</label>
                <input type="text" class="form-control addr-bairro" placeholder="Bairro" style="height: 35px; font-size: 13px;">
            </div>
            <div class="form-group">
                <label class="group-label" style="font-size: 10px;">Município</label>
                <input type="text" class="form-control addr-municipio" placeholder="Nome da Cidade" style="height: 35px; font-size: 13px;">
            </div>
            <div class="form-group">
                <label class="group-label" style="font-size: 10px;">Cód. IBGE</label>
                <input type="text" class="form-control addr-cod-municipio" placeholder="Código" style="height: 35px; font-size: 13px;">
            </div>
            <div class="form-group">
                <label class="group-label" style="font-size: 10px;">UF</label>
                <input type="text" class="form-control addr-uf" maxlength="2" placeholder="UF" style="height: 35px; font-size: 13px;">
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

    function removeAddress(btn) {
        btn.parentElement.remove();
        updateAddressNames();
    }

    function updateAddressNames() {
        const cards = document.querySelectorAll('.address-card.additional-address');
        cards.forEach((card, index) => {
            const badge = card.querySelector('.address-badge');
            badge.textContent = `Endereço Adicional ${index + 1}`;

            // Atualizar os nomes dos campos para o formato array: enderecos[adicionais][index][campo]
            card.querySelector('.addr-pais').name = `enderecos[adicionais][${index}][pais]`;
            card.querySelector('.addr-cep').name = `enderecos[adicionais][${index}][cep]`;
            card.querySelector('.addr-logradouro').name = `enderecos[adicionais][${index}][logradouro]`;
            card.querySelector('.addr-numero').name = `enderecos[adicionais][${index}][numero]`;
            card.querySelector('.addr-complemento').name = `enderecos[adicionais][${index}][complemento]`;
            card.querySelector('.addr-bairro').name = `enderecos[adicionais][${index}][bairro]`;
            card.querySelector('.addr-municipio').name = `enderecos[adicionais][${index}][municipio]`;
            card.querySelector('.addr-cod-municipio').name = `enderecos[adicionais][${index}][cod_municipio]`;
            card.querySelector('.addr-uf').name = `enderecos[adicionais][${index}][uf]`;
        });
    }

    // Inicializar nomes se já houver algum (embora comece vazio)
    updateAddressNames();
</script>
<?php
$content = ob_get_clean();
$title = 'Criar Participante';
$titletopbar = "Criar Novo Participante";
include __DIR__ . '/../layouts/app.php';
?>
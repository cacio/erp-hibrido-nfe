<?php ob_start(); ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div style="display: flex; align-items: center; gap: 20px;">
        <a href="nfe_lista.html" class="btn btn-outline" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 10px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 style="font-size: 24px;">Configurações de NF-e</h1>
            <p style="color: var(--text-muted); font-size: 14px;">Filial: <strong><?= htmlspecialchars($filial->getRazaoSocial()) ?></strong></p>
        </div>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('form-nfe').submit()" style="padding: 12px 30px; border-radius: 12px; font-weight: 600;">
        <i class="fas fa-save" style="margin-right: 8px;"></i> Salvar Alterações
    </button>
</div>
<div class="settings-layout">
    <aside class="settings-sidebar">
        <div class="settings-menu-item active" data-section="geral">
            <i class="fas fa-cog"></i> Geral e Ambiente
        </div>
        <div class="settings-menu-item" data-section="certificado">
            <i class="fas fa-certificate"></i> Certificado Digital
        </div>
        <div class="settings-menu-item" data-section="numeracao">
            <i class="fas fa-list-ol"></i> Numeração e Série
        </div>
        <div class="settings-menu-item" data-section="impressao">
            <i class="fas fa-print"></i> Impressão e DANFE
        </div>
        <div class="settings-menu-item" data-section="email">
            <i class="fas fa-envelope"></i> E-mail e Notificações
        </div>
    </aside>
    <div class="settings-content">
        <form id="form-nfe" method="post" enctype="multipart/form-data">

            <!-- Coluna Principal -->

            <!-- Card: Ambiente e Emissão -->
            <!-- Seção: Geral -->
            <section id="geral" class="settings-section active">
                <div class="section-header">
                    <h2>Geral e Ambiente</h2>
                    <p>Configure o ambiente de destino e parâmetros básicos de emissão.</p>
                </div>

                <div class="form-card">
                    <h3><i class="fas fa-server"></i> Ambiente de Destino</h3>
                    <div class="form-group">
                        <label class="group-label">Ambiente</label>
                        <select class="form-control" style="width: 100%;">
                            <option value="HOMOLOGACAO" <?= ($config['ambiente'] ?? '') === 'HOMOLOGACAO' ? 'selected' : '' ?>>Homologação (Testes)</option>
                            <option value="PRODUCAO" <?= ($config['ambiente'] ?? '') === 'PRODUCAO' ? 'selected' : '' ?>>Produção (Validade Jurídica)</option>
                        </select>
                        <div class="info-box" style="margin-top: 15px;">
                            <i class="fas fa-exclamation-triangle" style="margin-top: 3px;"></i>
                            <span>O ambiente de <strong>Produção</strong> emite notas com valor fiscal real perante a SEFAZ. Use Homologação para realizar testes de integração.</span>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <h3><i class="fas fa-clock"></i> Timeouts e Tentativas</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="group-label">Tempo de Espera (segundos)</label>
                            <input type="number" class="form-control" value="30">
                        </div>
                        <div class="form-group">
                            <label class="group-label">Tentativas de Reenvio</label>
                            <input type="number" class="form-control" value="3">
                        </div>
                    </div>
                </div>
                <div class="form-card">
                    <h3><i class="fas fa-user-shield"></i> Validação de Cadastro</h3>
                    <label style="display:flex; gap:10px; align-items:center;">
                        <input type="checkbox" name="permitir_documento_duplicado"
                            <?= !empty($config['participante']['permitir_documento_duplicado']) ? 'checked' : '' ?>>
                        Permitir cadastro duplicado de CPF/CNPJ
                    </label>
                </div>
            </section>

            <!-- Card: Certificado Digital -->

            <!-- Seção: Certificado -->
            <section id="certificado" class="settings-section">
                <div class="section-header">
                    <h2>Certificado Digital</h2>
                    <p>Gerencie o certificado A1 utilizado para assinatura das notas.</p>
                </div>

                <div class="form-card">
                    <h3><i class="fas fa-file-signature"></i> Certificado Atual</h3>


                    <?php if (!empty($config['certificado']['arquivo'])): ?>
                        <div class="current-file-badge">
                            <i class="fas fa-check-circle"></i>
                            <span>certificado_modelo_2026.pfx (Válido até 15/12/2026)</span>
                        </div>
                    <?php else: ?>
                        <div class="current-file-badge-dark">
                            <i class="fas fa-times-circle"></i>
                            <span>Nenhum certificado carregado</span>
                        </div>
                    <?php
                    endif;
                    ?>


                    <div class="file-upload-zone" onclick="document.getElementById('cert-upload').click()">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 32px; color: var(--primary-color); margin-bottom: 15px; display: block;"></i>
                        <p style="font-weight: 600; margin-bottom: 5px;">Clique para atualizar o certificado</p>
                        <p style="font-size: 12px; color: var(--text-muted);">Arraste o arquivo .pfx ou .p12 aqui</p>
                        <input type="file" id="cert-upload" accept=".pfx" name="certificado" style="display: none;" onchange="updateFileName(this)">
                    </div>
                </div>

                <div class="form-card">
                    <h3><i class="fas fa-key"></i> Segurança</h3>
                    <div class="form-group">
                        <label class="group-label">Senha do Certificado</label>
                        <div style="position: relative;">
                            <input type="password" class="form-control" placeholder="Digite a senha do arquivo">
                            <button type="button" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer;" onclick="togglePassword(this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Card: Numeração -->

            <section id="numeracao" class="settings-section">
                <div class="section-header">
                    <h2>Numeração e Série</h2>
                    <p>Controle a sequência numérica das suas notas fiscais.</p>
                </div>

                <div class="form-card">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="group-label">Série da NF-e</label>
                            <input type="number" class="form-control" value="<?= $config['numeracao']['nfe']['serie'] ?? 1 ?>" name="serie">
                        </div>
                        <div class="form-group">
                            <label class="group-label">Próximo Número</label>
                            <input type="number" class="form-control" name="ultimo_numero" value="<?= $config['numeracao']['nfe']['ultimo_numero'] ?? 0 ?>">
                        </div>
                    </div>
                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 15px;">
                        <i class="fas fa-info-circle"></i> O sistema incrementará este número automaticamente a cada emissão autorizada.
                    </p>
                </div>
            </section>

            <!-- Card: Ajuda/Info -->
            <section id="impressao" class="settings-section">
                <div class="section-header">
                    <h2>Impressão e DANFE</h2>
                    <p>Personalize a aparência do documento auxiliar da nota fiscal.</p>
                </div>
                <div class="form-card">
                    <p style="text-align: center; padding: 40px; color: var(--text-muted);">Configurações de layout de impressão em desenvolvimento...</p>
                </div>
            </section>

            <section id="email" class="settings-section">
                <div class="section-header">
                    <h2>E-mail e Notificações</h2>
                    <p>Configure o envio automático de XML e PDF para seus clientes.</p>
                </div>
                <div class="form-card">
                    <p style="text-align: center; padding: 40px; color: var(--text-muted);">Configurações de servidor SMTP em desenvolvimento...</p>
                </div>
            </section>

        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const menuItems = document.querySelectorAll('.settings-menu-item');
        const sections = document.querySelectorAll('.settings-section');

        menuItems.forEach(item => {
            item.addEventListener('click', () => {
                const targetSection = item.getAttribute('data-section');

                // Atualizar Menu
                menuItems.forEach(i => i.classList.remove('active'));
                item.classList.add('active');

                // Atualizar Seção
                sections.forEach(section => {
                    section.classList.remove('active');
                    if (section.id === targetSection) {
                        section.classList.add('active');
                    }
                });
            });
        });
    });

    function updateFileName(input) {
        const display = document.getElementById('file-name-display');
        if (input.files && input.files[0]) {
            display.innerHTML = `<strong>Arquivo selecionado:</strong> ${input.files[0].name}`;
            display.style.color = 'var(--primary-color)';
        }
    }

    function togglePassword(btn) {
        const input = btn.parentElement.querySelector('input');
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = '🙈';
        } else {
            input.type = 'password';
            btn.textContent = '👁️';
        }
    }
</script>
<?php
$content = ob_get_clean();
$title = 'Configuração de NFE - Filiais';
$titletopbar = "Configuração de NFE - Filiais";
include __DIR__ . '/../../layouts/app.php';
?>
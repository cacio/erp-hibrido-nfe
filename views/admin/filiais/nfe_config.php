<?php ob_start(); ?>

<div class="top-bar-header" style="margin-bottom: 10px;">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="/admin/filiais" class="btn btn-ghost" style="padding: 8px;">←</a>
        <div>
            <h1>Configuração NFe</h1>
            <p style="font-size: 13px; color: var(--text-muted);">
                Filial: <strong><?= htmlspecialchars($filial->getRazaoSocial()) ?></strong>
            </p>
        </div>
    </div>
    <div class="top-bar-actions">
        <button type="button" onclick="document.getElementById('form-nfe').submit()" class="btn btn-primary">Salvar Configuração</button>
    </div>
</div>

<form id="form-nfe" method="post" enctype="multipart/form-data">
    <div style="display: grid; grid-template-columns: 1fr 380px; gap: 25px; align-items: start;">
        <!-- Coluna Principal -->
        <div style="display: flex; flex-direction: column; gap: 25px;">

            <!-- Card: Ambiente e Emissão -->
            <section class="stat-card">
                <h2 class="panel-title">Ambiente de Emissão</h2>
                <div class="form-group">
                    <label class="group-label">Ambiente de Destino</label>
                    <select name="ambiente" class="form-control">
                        <option value="HOMOLOGACAO" <?= ($config['ambiente'] ?? '') === 'HOMOLOGACAO' ? 'selected' : '' ?>>Homologação (Testes)</option>
                        <option value="PRODUCAO" <?= ($config['ambiente'] ?? '') === 'PRODUCAO' ? 'selected' : '' ?>>Produção (Validade Jurídica)</option>
                    </select>
                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 8px;">
                        ⚠️ O ambiente de produção emite notas com valor fiscal real.
                    </p>
                </div>
            </section>

            <!-- Card: Certificado Digital -->
            <section class="stat-card">
                <h2 class="panel-title">Certificado Digital (A1)</h2>
                <?php if (!empty($config['certificado']['arquivo'])): ?>
                    <div class="current-cert">
                        <span>📜</span>
                        <div>
                            Certificado atual: <strong><?= basename($config['certificado']['arquivo']) ?></strong>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label class="group-label">Novo Certificado (.pfx)</label>
                    <div class="file-upload-wrapper" onclick="document.getElementById('cert-file').click()">
                        <span style="font-size: 24px; display: block; margin-bottom: 10px;">📁</span>
                        <span id="file-name-display">Clique para selecionar ou arraste o arquivo .pfx</span>
                        <input type="file" id="cert-file" name="certificado" accept=".pfx" style="display: none;" onchange="updateFileName(this)">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label class="group-label">Senha do Certificado</label>
                    <div style="position: relative;">
                        <input type="password" name="senha_certificado" class="form-control" placeholder="Digite a senha do arquivo .pfx">
                        <button type="button" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted);" onclick="togglePassword(this)">👁️</button>
                    </div>
                </div>
            </section>
        </div>

        <!-- Coluna Lateral -->
        <div style="display: flex; flex-direction: column; gap: 25px;">

            <!-- Card: Numeração -->
            <section class="stat-card">
                <h2 class="panel-title">Numeração e Série</h2>

                <div class="form-group">
                    <label class="group-label">Série da NFe</label>
                    <input type="number" name="serie" class="form-control" value="<?= $config['numeracao']['nfe']['serie'] ?? 1 ?>">
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label class="group-label">Última NFe Emitida</label>
                    <input type="number" name="ultimo_numero" class="form-control" value="<?= $config['numeracao']['nfe']['ultimo_numero'] ?? 0 ?>">
                    <p style="font-size: 11px; color: var(--text-muted); margin-top: 8px;">
                        O sistema utilizará o próximo número sequencial.
                    </p>
                </div>
            </section>

            <!-- Card: Ajuda/Info -->
            <section class="stat-card" style="background: var(--bg-color); border-style: dashed;">
                <h2 class="panel-title" style="font-size: 14px;">Dica Técnica</h2>
                <p style="font-size: 13px; color: var(--text-muted); line-height: 1.6;">
                    Certifique-se de que o certificado A1 esteja no formato <strong>.pfx</strong> ou <strong>.p12</strong>. A senha é obrigatória para a descriptografia durante a assinatura das notas.
                </p>
            </section>
        </div>

    </div>
</form>

<script>
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
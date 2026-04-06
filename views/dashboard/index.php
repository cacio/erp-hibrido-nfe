<?php ob_start(); ?>
<?php
$map = [
    'OK' => [
        'border' => '#10b981',
        'bg'     => 'rgba(16, 185, 129, 0.1)',
        'bar'    => '#10b981',
        'icon'   => '📜',
    ],
    'ATENCAO' => [
        'border' => '#f59e0b',
        'bg'     => 'rgba(245, 158, 11, 0.1)',
        'bar'    => '#f59e0b',
        'icon'   => '⚠️',
    ],
    'EXPIRADO' => [
        'border' => '#ef4444',
        'bg'     => 'rgba(239, 68, 68, 0.1)',
        'bar'    => '#ef4444',
        'icon'   => '❌',
    ],
    'NAO_CONFIGURADO' => [
        'border' => '#9ca3af',
        'bg'     => 'rgba(156, 163, 175, 0.1)',
        'bar'    => '#9ca3af',
        'icon'   => 'ℹ️',
    ],
];

$status = $certificado['status'] ?? 'NAO_CONFIGURADO';
$ui     = $map[$status];

$dias = $certificado['dias'] ?? 0;

// limite visual (ex: 365 dias = 100%)
$percent = max(0, min(100, ($dias / 365) * 100));
?>
<!-- Status do Certificado Digital -->
<div id="cert-alert-container" style="margin-bottom: 25px;">
    <div class="stat-card"
        style="display: flex; align-items: center; justify-content: space-between;
                border-left: 5px solid <?= $ui['border'] ?>;
                padding: 20px 30px;">

        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="
                width: 50px;
                height: 50px;
                background: <?= $ui['bg'] ?>;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;">
                <?= $ui['icon'] ?>
            </div>

            <div>
                <h3 style="font-size: 14px; color: var(--text-muted); margin-bottom: 4px;">
                    Certificado Digital A1
                </h3>

                <p style="font-size: 18px; font-weight: 700;">
                    <?php if ($status === 'NAO_CONFIGURADO'): ?>
                        NFe não configurada
                    <?php elseif ($status === 'EXPIRADO'): ?>
                        Certificado expirado
                    <?php else: ?>
                        Válido até <?= $certificado['expira_em'] ?>
                    <?php endif; ?>
                </p>

                <?php if ($status !== 'NAO_CONFIGURADO'): ?>
                    <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
                        <div style="width: 150px; height: 6px; background: var(--bg-color); border-radius: 3px; overflow: hidden;">
                            <div style="width: <?= $percent ?>%; height: 100%; background: <?= $ui['bar'] ?>;"></div>
                        </div>

                        <span style="font-size: 12px; font-weight: 600; color: <?= $ui['bar'] ?>;">
                            <?= $certificado['dias'] ?> dias restantes
                        </span>
                    </div>
                <?php else: ?>
                    <small style="color:#3b82f6">
                        Configure o certificado para liberar a emissão de NFe
                    </small>
                <?php endif; ?>
            </div>
        </div>

        <a href="/admin/filiais/<?= $_SESSION['auth']['filial_id'] ?>/nfe"
            class="btn btn-outline"
            style="font-size: 13px; padding: 8px 16px;">
            <?= $status === 'NAO_CONFIGURADO' ? 'Configurar Agora' : 'Configurar Certificado' ?>
        </a>
    </div>
</div>


<!-- Stats Cards -->
<section class="stats-grid">
    <!-- Card: NF-e Hoje -->
    <div class="stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h3 style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">NF-e Hoje</h3>
                <p style="font-size: 24px; font-weight: 700; margin: 8px 0;"><?= $fiscal['hoje']['total'] ?> <span style="font-size: 14px; font-weight: 400; color: var(--text-muted);">notas</span></p>
                <p style="font-size: 15px; color: var(--primary-color); font-weight: 600;">R$ <?= number_format($fiscal['hoje']['valor'], 2, ',', '.') ?></p>
            </div>
            <div style="width: 40px; height: 40px; background: rgba(79, 70, 229, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">💰</div>
        </div>
    </div>

    <!-- Card: NF-e no Mês -->
    <div class="stat-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h3 style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">NF-e no Mês</h3>
                <p style="font-size: 24px; font-weight: 700; margin: 8px 0;"><?= $fiscal['mes']['total'] ?> <span style="font-size: 14px; font-weight: 400; color: var(--text-muted);">notas</span></p>
                <p style="font-size: 15px; color: #10b981; font-weight: 600;">R$ <?= number_format($fiscal['mes']['valor'], 2, ',', '.') ?></p>
            </div>
            <div style="width: 40px; height: 40px; background: rgba(16, 185, 129, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">📊</div>
        </div>
    </div>

    <!-- Card: Status das Notas -->
    <div class="stat-card" style="grid-column: span 2;">
        <h3 style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; margin-bottom: 15px;">Status das Notas (Mês)</h3>
        <div style="display: flex; gap: 20px; justify-content: space-between;">
            <div style="flex: 1; text-align: center; padding: 10px; background: var(--bg-color); border-radius: 12px;">
                <span style="display: block; font-size: 20px; font-weight: 700; color: #10b981;"><?= $fiscal['status']['AUTORIZADA'] ?? 0 ?></span>
                <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Autorizadas</span>
            </div>
            <div style="flex: 1; text-align: center; padding: 10px; background: var(--bg-color); border-radius: 12px;">
                <span style="display: block; font-size: 20px; font-weight: 700; color: #ef4444;"><?= $fiscal['status']['REJEITADA'] ?? 0 ?></span>
                <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Rejeitadas</span>
            </div>
            <div style="flex: 1; text-align: center; padding: 10px; background: var(--bg-color); border-radius: 12px;">
                <span style="display: block; font-size: 20px; font-weight: 700; color: #f59e0b;"><?= $fiscal['status']['PENDENTE'] ?? 0 ?></span>
                <span style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Pendentes</span>
            </div>
        </div>
    </div>
</section>

<!-- Charts Grid -->
<div class="charts-grid">
    <!-- Line Chart -->
    <section class="chart-container">
        <h2>Desempenho de Vendas</h2>
        <canvas id="salesChart"></canvas>
    </section>

    <!-- Bar Chart -->
    <section class="chart-container">
        <h2>Novos Clientes</h2>
        <canvas id="clientsChart"></canvas>
    </section>
</div>

<section class="data-table-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 18px; font-weight: 700;">Linha do Tempo Operacional</h2>
        <button class="btn btn-ghost" style="font-size: 12px;">Ver Tudo</button>
    </div>

    <div class="timeline">
        <!-- Item: NFe Autorizada -->
        <div class="timeline-item">
            <div class="timeline-icon" style="color: #10b981;">📄</div>
            <div class="timeline-content">
                <span class="timeline-time">Há 5 minutos</span>
                <h4 class="timeline-title">NF-e #4502 Autorizada</h4>
                <p class="timeline-desc">Cliente: <strong>Frigorífico Boi Forte</strong> - Valor: R$ 12.450,00</p>
                <span class="timeline-badge badge-success">Sincronizado SEFAZ</span>
            </div>
        </div>

        <!-- Item: Abate Concluído -->
        <div class="timeline-item">
            <div class="timeline-icon" style="color: #4f46e5;">🐄</div>
            <div class="timeline-content">
                <span class="timeline-time">Há 45 minutos</span>
                <h4 class="timeline-title">Lote de Abate #882 Finalizado</h4>
                <p class="timeline-desc">Total de 42 cabeças processadas. Rendimento médio de 54.8%.</p>
                <span class="timeline-badge badge-info">Processado</span>
            </div>
        </div>

        <!-- Item: Alerta de Temperatura -->
        <div class="timeline-item">
            <div class="timeline-icon" style="color: #ef4444;">⚠️</div>
            <div class="timeline-content">
                <span class="timeline-time">Há 2 horas</span>
                <h4 class="timeline-title">Alerta: Câmara Fria 02</h4>
                <p class="timeline-desc">Oscilação de temperatura detectada: -1.2°C (Limite: -2.0°C).</p>
                <span class="timeline-badge badge-danger">Crítico</span>
            </div>
        </div>

        <!-- Item: Novo Participante -->
        <div class="timeline-item">
            <div class="timeline-icon" style="color: #f59e0b;">👤</div>
            <div class="timeline-content">
                <span class="timeline-time">Há 4 horas</span>
                <h4 class="timeline-title">Novo Fornecedor Cadastrado</h4>
                <p class="timeline-desc">Fazenda Santa Maria - Produtor: Ricardo Oliveira.</p>
                <span class="timeline-badge badge-warning">Aguardando SIF</span>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
$title = 'Dashboard';
$titletopbar = "Visão Geral";
include __DIR__ . '/../layouts/app.php';
?>
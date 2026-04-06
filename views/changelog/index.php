<?php ob_start(); ?>

<div class="changelog-container">
    <a href="#" class="back-btn">
        <i class="fas fa-arrow-left"></i> Voltar para o Sistema
    </a>

    <div style="margin-bottom: 40px;">
        <h1 style="font-size: 28px; margin-bottom: 10px;">O que há de novo</h1>
        <p style="color: var(--text-muted);">Acompanhe as últimas atualizações e melhorias do sistema.</p>
    </div>

    <div class="timeline">
        <!-- Versão Atual -->
        <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-date">06 de Abril, 2026</div>
            <div class="timeline-content">
                <div class="version-title">
                    <h3>Atualização de Interface</h3>
                    <span class="version-tag">v2.4.0</span>
                </div>
                <ul class="change-list">
                    <li class="change-item">
                        <span class="change-type type-new">Novo</span>
                        <span>Implementada barra de ações em lote flutuante na listagem de NF-e.</span>
                    </li>
                    <li class="change-item">
                        <span class="change-type type-imp">Melhoria</span>
                        <span>Redesign completo dos filtros avançados e barra de busca superior.</span>
                    </li>
                    <li class="change-item">
                        <span class="change-type type-imp">Melhoria</span>
                        <span>Adicionado indicador de versão no topo para acesso rápido ao changelog.</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Versão Anterior -->
        <div class="timeline-item">
            <div class="timeline-dot" style="background: var(--border-color);"></div>
            <div class="timeline-date">28 de Março, 2026</div>
            <div class="timeline-content">
                <div class="version-title">
                    <h3>Módulo de NF-e</h3>
                    <span class="version-tag">v2.3.5</span>
                </div>
                <ul class="change-list">
                    <li class="change-item">
                        <span class="change-type type-new">Novo</span>
                        <span>Lançamento do novo módulo de gerenciamento de Notas Fiscais Eletrônicas.</span>
                    </li>
                    <li class="change-item">
                        <span class="change-type type-fix">Correção</span>
                        <span>Ajustado erro de carregamento de dados em conexões lentas.</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Versão Inicial -->
        <div class="timeline-item">
            <div class="timeline-dot" style="background: var(--border-color);"></div>
            <div class="timeline-date">15 de Março, 2026</div>
            <div class="timeline-content">
                <div class="version-title">
                    <h3>Layout Moderno</h3>
                    <span class="version-tag">v2.3.0</span>
                </div>
                <ul class="change-list">
                    <li class="change-item">
                        <span class="change-type type-new">Novo</span>
                        <span>Implementação do novo layout baseado em Glassmorphism e Dark Mode.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Log de mudanças';
$titletopbar = 'Log de mudanças';
if (!isset($_GET['winbox'])) {
    include __DIR__ . '/../layouts/app.php';
} else {
    include __DIR__ . '/../layouts/appbox.php';
}

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> - ERP Híbrido</title>
    <link rel="stylesheet" href="/css/style.css?v=1.0.0.5">
</head>

<body>
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <!-- Modal de Busca Global -->
    <div class="modal-overlay" id="search-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Busca Global</h3>
                <button class="modal-close" onclick="closeModal('search-modal')">&times;</button>
            </div>
            <div class="search-input-wrapper">
                <span class="search-icon-inner">🔍</span>
                <input type="text" placeholder="Buscar por projetos, clientes ou arquivos..." id="global-search-input">
            </div>
            <div class="search-results">
                <p style="font-size: 13px; color: var(--text-muted);">Sugestões recentes:</p>
                <ul style="list-style: none; margin-top: 10px;">
                    <li style="padding: 10px; border-radius: 8px; cursor: pointer; background: var(--bg-color); margin-bottom: 5px;">📁 Projeto E-commerce</li>
                    <li style="padding: 10px; border-radius: 8px; cursor: pointer; background: var(--bg-color); margin-bottom: 5px;">👥 Maria Oliveira</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="dashboard-container" id="dashboard-container">
        <!-- Mini Sidebar -->
        <aside class="mini-sidebar">
            <div class="mini-logo">
                <div class="logo-box">M</div>
            </div>
            <nav class="mini-nav">
                <div class="mini-item">
                    <button class="mini-link active" data-target="home-panel">🏠</button>
                    <span class="tooltip">Início</span>
                </div>
                <div class="mini-item">
                    <button class="mini-link" onclick="openModal('search-modal')">🔍</button>
                    <span class="tooltip">Busca Global</span>
                </div>
                <div class="mini-item">
                    <button class="mini-link" data-target="projects-panel">📁</button>
                    <span class="tooltip">Projetos</span>
                </div>
                <div class="mini-item">
                    <button class="mini-link" data-target="team-panel">👥</button>
                    <span class="tooltip">Equipe</span>
                </div>
                <div class="mini-item">
                    <button class="mini-link" data-target="settings-panel">⚙️</button>
                    <span class="tooltip">Configurações</span>
                </div>

                <div class="mini-item">
                    <a href="/logout" class="mini-link" data-target="logout-panel">
                        <svg xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 474 512.46">
                            <path d="M249.71.13 12.08 35.6C5.46 36.59 0 43.43 0 50.23v418.88c0 6.77 5.39 9.38 12.08 10.31l237.63 32.97c6.68.92 12.08-7.77 12.08-14.63V10.44c0-6.86-5.53-11.28-12.08-10.31zm124.96 329.08-.01-34.07c-.58.17-1.2.27-1.83.27h-53.47c-3.55 0-6.45-2.96-6.45-6.45v-66.2c0-3.48 2.97-6.45 6.45-6.45h53.47c.63 0 1.24.1 1.82.27v-34.06c0-6.29 5.1-11.4 11.39-11.4 3.29 0 6.25 1.4 8.33 3.63l76.01 70.9c4.59 4.27 4.85 11.47.58 16.06l-76.95 75.59c-4.47 4.4-11.67 4.34-16.07-.13a11.439 11.439 0 0 1-3.27-7.96zm-87.26 129.54h31.02V345.46h25.37v113.9c0 6.77-2.8 12.95-7.27 17.44-4.47 4.52-10.67 7.31-17.49 7.31h-31.63v-25.36zm31.02-292.48V52.98h-31.02V27.62h31.63c6.81 0 13.01 2.79 17.49 7.27 4.47 4.48 7.27 10.68 7.27 17.49v113.89h-25.37zm-87.67 58.52-24.93-5.68v74.24l24.93-7.18v-61.38z" />
                        </svg>
                    </a>
                    <span class="tooltip">Sair</span>
                </div>
            </nav>
            <div class="mini-footer">
                <div class="mini-avatar">U</div>
            </div>
        </aside>

        <!-- Detail Sidebar -->
        <aside class="detail-sidebar open" id="detail-sidebar">
            <button class="close-sidebar" id="close-sidebar">❮</button>

            <!-- Painel Home -->
            <div class="detail-panel active" id="home-panel">
                <h3 class="panel-title">Início</h3>
                <ul class="detail-menu">
                    <li><a href="#" class="detail-link active"><span class="icon">📊</span> Visão Geral</a></li>
                    <li><a href="#" class="detail-link"><span class="icon">⚡</span> Atividades</a></li>
                    <li><a href="#" class="detail-link"><span class="icon">🔔</span> Notificações</a></li>
                </ul>
            </div>

            <!-- Painel Projetos -->
            <div class="detail-panel" id="projects-panel">
                <h3 class="panel-title">Projetos</h3>
                <div class="panel-group">
                    <span class="group-label">WORKSPACE</span>
                    <ul class="detail-menu">
                        <li><a href="#" class="detail-link"><span class="icon">📁</span> Todos Projetos</a></li>
                        <li><a href="#" class="detail-link"><span class="icon">💳</span> Faturamento</a></li>
                        <li><a href="#" class="detail-link"><span class="icon">📈</span> Uso</a></li>
                    </ul>
                </div>
            </div>

            <!-- Painel Equipe -->
            <div class="detail-panel" id="team-panel">
                <h3 class="panel-title">Equipe</h3>
                <ul class="detail-menu">
                    <li><a href="#" class="detail-link"><span class="icon">👥</span> Membros</a></li>
                    <li><a href="#" class="detail-link"><span class="icon">🛡️</span> Permissões</a></li>
                </ul>
            </div>

            <!-- Painel Configurações -->
            <div class="detail-panel" id="settings-panel">
                <h3 class="panel-title">Configurações</h3>
                <div class="panel-group">
                    <span class="group-label">Administração</span>
                    <ul class="detail-menu">
                        <li><a href="/admin/usuarios" class="detail-link"><span class="icon">👤</span> Usuários</a></li>
                        <li><a href="/admin/filiais" class="detail-link"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-buildings-fill" viewBox="0 0 16 16">
                                        <path d="M15 .5a.5.5 0 0 0-.724-.447l-8 4A.5.5 0 0 0 6 4.5v3.14L.342 9.526A.5.5 0 0 0 0 10v5.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V14h1v1.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5zM2 11h1v1H2zm2 0h1v1H4zm-1 2v1H2v-1zm1 0h1v1H4zm9-10v1h-1V3zM8 5h1v1H8zm1 2v1H8V7zM8 9h1v1H8zm2 0h1v1h-1zm-1 2v1H8v-1zm1 0h1v1h-1zm3-2v1h-1V9zm-1 2h1v1h-1zm-2-4h1v1h-1zm3 0v1h-1V7zm-2-2v1h-1V5zm1 0h1v1h-1z" />
                                    </svg></span>Filiais</a></li>
                        <li><a href="/admin/permissoes" class="detail-link"><span class="icon">🔒</span> Permissões</a></li>
                        <li><a href="/admin/roles" class="detail-link"><span class="icon">🔒</span> Perfis (Roles)</a></li>
                        <li><a href="#" class="detail-link"><span class="icon">🔒</span>Acessos por Usuário</a></li>
                    </ul>
                </div>
            </div>
        </aside>
        <main class="main-content">
            <header class="top-bar">
                <div class="top-bar-header">
                    <button class="action-btn" id="menu-toggle" style="margin-right: 10px;">☰</button>
                    <h2><?= $titletopbar ?></h2>
                </div>
                <div class="top-bar-actions">
                    <button class="action-btn" onclick="openModal('search-modal')" title="Busca Global">🔍</button>
                    <!-- <button class="action-btn" onclick="openModal('example-modal')" title="Adicionar Novo">+</button> -->
                    <button id="theme-toggle" class="action-btn">🌙</button>
                    <div class="user-profile">
                        <span><?= $this->user->getNome() ?></span>
                        <div class="avatar">U</div>
                    </div>
                </div>
            </header>


            <?= $content ?>
        </main>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/js/dashboard.js?v=1.0.2"></script>
</body>

</html>
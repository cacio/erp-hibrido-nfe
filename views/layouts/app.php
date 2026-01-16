<?php

use App\Services\MenuService; ?>
<?php $menu = MenuService::getMenu(); ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> - ERP Híbrido</title>
    <link rel="stylesheet" href="/css/style.css?v=1.0.0.7">
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
                <?php foreach ($menu as $item): ?>
                    <div class="mini-item">
                        <button class="mini-link" data-target="<?= $item['id'] ?>">
                            <?= $item['icon'] ?? '•' ?>
                        </button>
                        <span class="tooltip"><?= htmlspecialchars($item['label']) ?></span>
                    </div>
                <?php endforeach; ?>

                <div class="mini-item">
                    <button class="mini-link" onclick="openModal('search-modal')">🔍</button>
                    <span class="tooltip">Busca Global</span>
                </div>
                <div class="mini-item">
                    <a href="/logout" class="mini-link" data-target="logout-panel">🚪</a>
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
            <?php foreach ($menu as $module): ?>
                <div class="detail-panel" id="<?= $module['id'] ?>">
                    <h3 class="panel-title"><?= htmlspecialchars($module['label']) ?></h3>

                    <?php foreach ($module['children'] ?? [] as $child): ?>

                        <?php if (isset($child['group'])): ?>
                            <!-- 🔹 GRUPO -->
                            <div class="panel-group">
                                <span class="group-label"><?= htmlspecialchars($child['group']) ?></span>

                                <ul class="detail-menu">
                                    <?php foreach ($child['items'] as $item): ?>
                                        <li>
                                            <a href="<?= $item['route'] ?>" class="detail-link">
                                                <span class="icon"><?= $item['icon'] ?></span>
                                                <?= htmlspecialchars($item['label']) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                        <?php else: ?>
                            <!-- 🔹 CHILDREN SIMPLES -->
                            <ul class="detail-menu">
                                <li>
                                    <a href="<?= $child['route'] ?>" class="detail-link">
                                        <span class="icon"><?= $child['icon'] ?></span>
                                        <?= htmlspecialchars($child['label']) ?>
                                    </a>
                                </li>
                            </ul>

                        <?php endif; ?>

                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>



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
                    <!-- Profile Dropdown -->

                    <div class="profile-dropdown-container">
                        <div class="user-profile" id="profile-trigger">
                            <div class="user-meta">
                                <span class="user-name-top"><?= $this->user->getNome() ?></span>
                                <span class="user-branch-top">🏢 <?= $this->filial->getRazaoSocial() ?></span>
                            </div>
                            <div class="avatar"><?= strtoupper(substr($this->user->getNome(), 0, 2)); ?></div>
                        </div>

                        <div class="profile-dropdown" id="profile-menu">
                            <div class="dropdown-header">
                                <div class="avatar"><?= strtoupper(substr($this->user->getNome(), 0, 2)); ?></div>
                                <div class="user-info">
                                    <span class="user-name"><?= $this->user->getNome() ?></span>
                                    <span class="user-email"><?= $this->user->getEmail() ?></span>
                                </div>
                            </div>
                            <?php if (!empty($_SESSION['auth']['filiais'])): ?>
                                <div class="dropdown-divider"></div>
                                <!-- Branch Switcher -->
                                <div class="dropdown-branch-selector">
                                    <span class="group-label" style="margin: 0 16px 8px 16px; font-size: 10px;">Trocar Unidade</span>
                                    <div style="padding: 0 16px 8px 16px;">
                                        <form method="post" action="/trocar-filial">
                                            <select name="filial_id" class="filter-input" style="width: 100%; font-size: 13px;" onchange="this.form.submit()">
                                                <?php foreach ($_SESSION['auth']['filiais'] as $f): ?>
                                                    <option value="<?= $f['id'] ?>"
                                                        <?= $f['id'] === $_SESSION['auth']['filial_id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($f['razao_social']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <ul class="dropdown-menu">
                                <li><a href="user_roles.html"><span class="icon">⚙️</span> Settings</a></li>
                                <li><a href="permissions.html"><span class="icon">🔒</span> Security</a></li>
                            </ul>
                            <div class="dropdown-divider"></div>
                            <ul class="dropdown-menu">
                                <li><a href="team-panel"><span class="icon">👥</span> Teams</a></li>
                                <li><a href="#"><span class="icon">💎</span> Subscriptions</a></li>
                                <li><a href="#"><span class="icon">🎁</span> Referrals</a></li>
                            </ul>
                            <div class="dropdown-divider"></div>
                            <div class="dropdown-item-flex">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span class="icon">🌙</span> Dark mode
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="theme-toggle-checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <div class="dropdown-divider"></div>
                            <ul class="dropdown-menu">
                                <li><a href="login.html" style="color: #ef4444;"><span class="icon">🚪</span> Log out</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>


            <?= $content ?>
        </main>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/js/dashboard.js?v=1.0.6"></script>
</body>

</html>
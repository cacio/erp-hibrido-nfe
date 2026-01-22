<?php ob_start(); ?>
<div class="error-container">
    <div class="error-code">404</div>
    <div class="error-illustration">🔍</div>
    <h1 class="error-title">Página Não Encontrada</h1>
    <p class="error-message">
        Desculpe, a página que você está procurando não existe ou foi movida para um novo endereço.
    </p>
    <div style="display: flex; gap: 15px;">
        <a href="/dashboard" class="btn btn-primary">Voltar ao Dashboard</a>
        <button onclick="history.back()" class="btn btn-outline">Página Anterior</button>
    </div>
</div>
<?php
$content = ob_get_clean();
$title = '404 - Página Não Encontrada';
$titletopbar = "404 - Página Não Encontrada";
include __DIR__ . '/../layouts/erro.php';

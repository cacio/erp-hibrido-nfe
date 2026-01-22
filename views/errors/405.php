<?php ob_start(); ?>

<div class="error-container">
    <div class="error-code">405</div>
    <div class="error-illustration">⚠️</div>
    <h1 class="error-title">Método Não Permitido</h1>
    <p class="error-message">
        A requisição feita não é suportada por este recurso. Por favor, verifique a URL ou tente novamente mais tarde.
    </p>
    <div style="display: flex; gap: 15px;">
        <a href="/dashboard" class="btn btn-primary">Voltar ao Dashboard</a>
        <button onclick="history.back()" class="btn btn-outline">Voltar</button>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = '405 - Método Não Permitido';
$titletopbar = "405 - Método Não Permitido";
include __DIR__ . '/../layouts/erro.php';
<?php ob_start(); ?>
<div class="error-container">
    <div class="error-code">403</div>
    <div class="error-illustration">🚫</div>
    <h1 class="error-title">Acesso Negado</h1>
    <p class="error-message">
        Você não tem permissão para acessar esta área. Caso acredite que isso seja um erro, entre em contato com o administrador do sistema.
    </p>
    <div style="display: flex; gap: 15px;">
        <a href="/dashboard" class="btn btn-primary">Voltar ao Dashboard</a>
        <button onclick="history.back()" class="btn btn-outline">Solicitar Acesso</button>
    </div>
</div>
<?php
$content = ob_get_clean();
$title = '403 - Acesso Negado';
$titletopbar = "403 - Acesso Negado";
include __DIR__ . '/../layouts/erro.php';

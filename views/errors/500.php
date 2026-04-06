<?php ob_start(); ?>
<div class="error-container">
    <div class="error-code">500</div>
    <div class="error-illustration">🛠️</div>
    <h1 class="error-title">Erro Interno do Servidor</h1>
    <p class="error-message">
        Ocorreu um problema inesperado em nossos servidores. Nossa equipe técnica já foi notificada e está trabalhando para resolver.
    </p>
    <div style="display: flex; gap: 15px;">
        <a href="/dashboard" class="btn btn-primary">Tentar Novamente</a>
        <button onclick="location.reload()" class="btn btn-outline">Recarregar Página</button>
    </div>
</div>
<?php
$content = ob_get_clean();
$title = '500 - Erro Interno';
$titletopbar = "500 - Erro Interno";
include __DIR__ . '/../layouts/erro.php';

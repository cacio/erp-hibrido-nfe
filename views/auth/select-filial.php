<?php ob_start(); ?>

<div class="card">
    <div class="card-body">
        <h1 class="h3 mb-3 fw-normal text-center">Login ERP</h1>
        <form method="post" action="/confirmar-filial">
            <select name="filial_id" required>
                <?php foreach ($filiais as $filial): ?>
                    <option value="<?= $filial->getId() ?>">
                        <?= htmlspecialchars($filial->getRazaoSocial()) ?>
                    </option>
                <?php endforeach ?>
            </select>

            <button type="submit">Entrar</button>
        </form>

    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Filial';
include __DIR__ . '/../layouts/auth.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> - ERP Híbrido</title>
    <link rel="stylesheet" href="/css/style.css?v=1.0.1.4">
    <script src="https://unpkg.com/imask"></script>
</head>

<body>
    <div class="dashboard-container" id="dashboard-container">


        <main class="main-content">
             <?= $content ?>
        </main>

    </div>

</body>

</html>
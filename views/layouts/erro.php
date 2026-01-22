<!DOCTYPE html>
<html lang="pt-BR" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> - ERP Híbrido</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .error-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            background: var(--bg-color);
        }
        .error-code {
            font-size: 120px;
            font-weight: 800;
            color: var(--primary-color);
            line-height: 1;
            margin-bottom: 20px;
            opacity: 0.2;
        }
        .error-illustration {
            font-size: 80px;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--text-color);
        }
        .error-message {
            color: var(--text-muted);
            max-width: 400px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <?= $content ?>
</body>
</html>

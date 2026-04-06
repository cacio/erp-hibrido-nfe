<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação do Sistema</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-container" style="max-width: 500px;">
        <div class="login-box">
            <div class="login-header">
                <h1>Configuração Inicial</h1>
                <p>Preencha os dados para instalar o sistema</p>
            </div>

            <form method="post" action="/install">
                <div class="form-section">
                    <h3 class="section-title">Empresa</h3>
                    <div class="input-group">
                        <label for="nome_grupo">Nome do Grupo</label>
                        <input name="nome_grupo" id="nome_grupo" placeholder="Nome do Grupo" required>
                    </div>
                    <div class="input-group">
                        <label for="razao_social">Razão Social</label>
                        <input name="razao_social" id="razao_social" placeholder="Razão Social" required>
                    </div>
                    <div class="input-group">
                        <label for="cnpj">CNPJ</label>
                        <input name="cnpj" id="cnpj" placeholder="00.000.000/0000-00" required>
                    </div>
                </div>

                <div class="form-section" style="margin-top: 30px;">
                    <h3 class="section-title">Administrador</h3>
                    <div class="input-group">
                        <label for="email">E-mail do Administrador</label>
                        <input type="email" name="email" id="email" placeholder="admin@empresa.com" required>
                    </div>
                    <div class="input-group">
                        <label for="senha">Senha</label>
                        <input type="password" name="senha" id="senha" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn-login" style="margin-top: 20px;">Instalar Sistema</button>
            </form>

            <div class="login-footer">
                <p>Precisa de ajuda? <a href="#">Suporte Técnico</a></p>
            </div>
        </div>
    </div>
</body>
</html>

<?php
session_start();

// Processa o login ao enviar o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['txt_usuario']);
    $senha = trim($_POST['txt_senha']);

    // Verifica se os campos estão preenchidos
    if (empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos.";
    } else {
        // Endpoint da sua API Spring Boot
        $url = "http://localhost:8080/tostao/login";//Conexão API

        // Prepara os dados para enviar via POST
        $dados = [
            "emailUsuario" => $email,
            "senhaUsuario" => $senha
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode == 200) {
            $resposta = json_decode($response, true);

            // Salva dados essenciais na sessão
            $_SESSION['usuario'] = $resposta['emailUsuario'] ?? $email;
            $_SESSION['token'] = $resposta['token'] ?? '';
            $_SESSION['email'] = $resposta['emailUsuario'] ?? $email;
            $_SESSION['nome'] = $resposta['nomeUsuario'] ?? 'Usuário';
            $_SESSION['saldo'] = $resposta['saldoUsuario'] ?? 0.0;
            $_SESSION['telefone'] = $resposta['telefoneUsuario'] ?? 'Telefone não disponível';
            $_SESSION['cartao'] = $resposta['cartaoUsuario'] ?? "Número de cartão não disponível";
            $_SESSION['id'] = $resposta['idUsuario'] ?? null;

            header("Location: dashboard.php");
            exit;
        } elseif ($httpcode == 401) {
            $erro = "Senha incorreta.";
        } elseif ($httpcode == 404) {
            $erro = "Usuário não encontrado.";
        } else {
            $erro = "Erro ao conectar com a API (Código $httpcode)";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../resources/css/login.css">
    <link rel="icon" type="image/x-icon" href="../resources/files/pics/logo_tostao_bank.png"/>
</head>
<body>
    <div class='header' id='header'>
        <button onclick='toggleSideBar()' class='btn_icon_header'>
            <!-- SVGs omitidos por brevidade -->
        </button>
        <textoG>Tostão Bank</textoG>
        <div onclick='toggleSideBar()' class='headerNav' id='headerNav'>
            <button class='btn_icon_header'>X</button>
            <a href='index.html'>Inicio</a>
            <a href='login.php' class='ativo'>Login</a>
            <a href='cadastro.php'>Criar conta</a>
        </div>
    </div>

    <div class='centro'>
        <form method="POST" action="login.php">
            <div id="campos_login">
                <h1>Login</h1>
                <input type="text" name="txt_usuario" id="usuario" required>

                <h1>Senha</h1>
                <input type="password" name="txt_senha" id="senha" required>
            </div>

            <button type="button" id="toggleSenha" onclick="togglePasswordVisibility()">Revelar</button>
            <br><br>
            <button type="submit">Login</button>
            <br><br>

            <a class="jumento" href="cadastro.php">Não tem uma conta? Faça seu Cadastro por aqui!</a>

            <?php if (isset($erro)): ?>
                <p style="color: red;"><strong><?= htmlspecialchars($erro) ?></strong></p>
            <?php endif; ?>
        </form>
    </div>

    <script>
        function togglePasswordVisibility() {
            const senha = document.getElementById("senha");
            const toggle = document.getElementById("toggleSenha");

            if (senha.type === "password") {
                senha.type = "text";
                toggle.textContent = "Ocultar";
            } else {
                senha.type = "password";
                toggle.textContent = "Revelar";
            }
        }

        // Sidebar JS omitido por brevidade
    </script>
</body>
</html>
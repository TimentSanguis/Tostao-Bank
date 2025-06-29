<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["txt_nome"]);
    $email = trim($_POST["txt_email"]);
    $senha = trim($_POST["txt_senha"]);
    $cpf = trim($_POST["txt_cpf"]);
    $renda = trim($_POST["txt_renda"]);

    // Validações básicas
    if (empty($nome) || empty($email) || empty($senha) || empty($cpf) || empty($renda)) {
        echo "<script>alert('Preencha todos os campos.');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email inválido.');</script>";
    } else {
        // Converte renda (de "1.234,56" → "1234.56")
        $renda_convertida = floatval(str_replace(',', '.', str_replace('.', '', $renda)));

        // Monta dados para API
        $dados = [
            "nomeUsuario" => $nome,
            "emailUsuario" => $email,
            "senhaUsuario" => $senha,
            "cpfUsuario" => $cpf,
            "rendaUsuario" => $renda_convertida
        ];

        // Requisição para a API
        $url = "http://localhost:8080/tostao/cadastro";//Conexão API
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));

        $response = curl_exec($ch);

        if ($response === false) {
            echo "<script>alert('Erro ao conectar com a API.');</script>";
            curl_close($ch);
            exit;
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode == 200) {
            echo "<script>alert('Conta criada com sucesso!'); window.location.href='login.php';</script>";
            exit;
        } elseif ($httpcode == 409) {
            echo "<script>alert('Usuário já existe.');</script>";
        } elseif ($httpcode == 400) {
            echo "<script>alert('Dados inválidos.');</script>";
        } else {
            echo "<script>alert('Erro no cadastro. Código HTTP: $httpcode');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
    <link rel="stylesheet" href="../resources/css/cadastro.css">
    <link rel="icon" type="image/x-icon" href="../resources/files/pics/logo_tostao_bank.png"/>
</head>
<body>
    <div class='centro'>
        <form method="POST" action="cadastro.php">
            <h1>Cadastro</h1>
            <label>Nome:</label><br>
            <input type="text" name="txt_nome" required><br><br>

            <label>Email:</label><br>
            <input type="email" name="txt_email" required><br><br>

            <label>Senha:</label><br>
            <input type="password" name="txt_senha" required><br><br>

            <label>CPF:</label><br>
            <input type="text" name="txt_cpf" required><br><br>

            <label>Renda mensal:</label><br>
            <input type="text" name="txt_renda" placeholder="Ex: 3500,00" required><br><br>

            <button type="submit">Cadastrar</button><br><br>

            <a href="login.php">Já tem uma conta? Faça login</a>
        </form>
    </div>
</body>
</html>
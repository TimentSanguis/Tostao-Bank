<?php
include 'php/conexao.php'; 

session_start();
header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['mensagem' => 'Usuário não logado.']);
    exit;
}

// Define o fuso horário
date_default_timezone_set('America/Sao_Paulo');

// Acessa as informações da sessão
$nome_usuario = $_SESSION['usuario'];
$email_usuario = $_SESSION['email'];
$hoje = date('Y-m-d H:i:s');
$tipo = 'Recarga';

// Receber dados do formulário
$numero = trim($_POST['txt_numero']);
$valor = floatval(str_replace(',', '.', $_POST['txt_valor'])); // Converter para float

// Busca o saldo do usuário remetente
$querySaldo = $conecta_db->prepare("SELECT saldo_usuario FROM usuario WHERE nome_usuario = ?");
$querySaldo->bind_param("s", $nome_usuario);
$querySaldo->execute();
$resultSaldo = $querySaldo->get_result();
$row = $resultSaldo->fetch_assoc();

if (!$row) {
    echo json_encode(['mensagem' => 'Erro ao buscar o saldo do usuário.']);
    exit;
}

$saldo_usuario = $row['saldo_usuario'];

// Verifica se o usuário tem saldo suficiente
if ($saldo_usuario >= $valor) {
    // Subtrai o valor do saldo do remetente
    $novoSaldoRemetente = $saldo_usuario - $valor;
    $updateRemetente = $conecta_db->prepare("UPDATE usuario SET saldo_usuario = ? WHERE nome_usuario = ?");
    $updateRemetente->bind_param("ds", $novoSaldoRemetente, $nome_usuario);
    $updateRemetente->execute();

    // Insere informações no histórico
    $insertHist = $conecta_db->prepare("INSERT INTO historico (data_transferencia, valor_transferencia, email_transferencia, tipo_transferencia, telefone_transferencia) VALUES (?, ?, ?, ?, ?)");
    $insertHist->bind_param("sdsss", $hoje, $valor, $email_usuario, $tipo, $numero);
    $insertHist->execute();
	

    // Verifica se a atualização foi bem-sucedida
    if ($updateRemetente->affected_rows > 0 && $insertHist->affected_rows > 0) {
        echo json_encode([
            'mensagem' => "<h1><center>Pix de R$ " . number_format($valor, 2, ',', '.') . " para $numero realizado com sucesso!</center></h1>",
            'saldo' => number_format($novoSaldoRemetente, 2, ',', '.')
        ]);

    } else {
        echo json_encode(['mensagem' => "<h1><center>Erro ao processar a transferência.</center></h1>"]);
    }
} else {
    echo json_encode(['mensagem' => "<h1><center>Saldo insuficiente.</center></h1>"]);
}

// Fecha as declarações e a conexão
$querySaldo->close();
$updateRemetente->close();
$insertHist->close();
$conecta_db->close();
?>

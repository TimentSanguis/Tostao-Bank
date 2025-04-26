<?php
include 'php/conexao.php';

session_start();
header('Content-Type: application/json');
error_log("Usuário logado: " . $_SESSION['usuario']);

// Define o fuso horário
date_default_timezone_set('America/Sao_Paulo');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['mensagem' => 'Usuário não logado.']);
    exit;
}

// Acessa as informações da sessão
$nome_usuario = $_SESSION['usuario'];
$email_usuario = $_SESSION['email'];
$hoje = date('Y-m-d H:i:s');
$tipo = 'Pix';

// Receber dados do formulário
$usuario = trim($_POST['txt_usuario']);
$valor = floatval(str_replace(',', '.', $_POST['txt_number'])); // Converte para float



// Verificação do saldo
$querySaldo = $conecta_db->prepare("SELECT saldo_usuario FROM usuario WHERE nome_usuario = ?");
$querySaldo->bind_param("s", $nome_usuario);
$querySaldo->execute();
$resultSaldo = $querySaldo->get_result();
$row = $resultSaldo->fetch_assoc();
$saldo_usuario = $row['saldo_usuario'];

// Validação do e-mail do destinatário
if (!filter_var($usuario, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $usuario)) {
    echo json_encode(['mensagem' => "<h1><center>Por favor, insira um e-mail válido do Gmail.</center></h1>"]);
    exit;
}

// Verifica se o destinatário existe no banco de dados
$queryUsuario = $conecta_db->prepare("SELECT id_usuario, saldo_usuario FROM usuario WHERE email_usuario = ?");
$queryUsuario->bind_param("s", $usuario);
$queryUsuario->execute();
$resultUsuario = $queryUsuario->get_result();

if ($resultUsuario && $resultUsuario->num_rows > 0) {
    var_dump($usuario, $hoje, $valor, $nome_usuario, $tipo);
    // Se o usuário existir, obter os dados
    $cliente = $resultUsuario->fetch_assoc();
    $id_destinatario = $cliente['id_usuario'];
    $saldoDestinatario = $cliente['saldo_usuario'];

    // Verifica se há saldo suficiente
    if ($saldo_usuario >= $valor) {
        // Subtrai o valor e atualiza o saldo do remetente
        $novoSaldoRemetente = $saldo_usuario - $valor;
        $updateRemetente = $conecta_db->prepare("UPDATE usuario SET saldo_usuario = ? WHERE email_usuario = ?");
        $updateRemetente->bind_param("ds", $novoSaldoRemetente, $email_usuario);
        $updateRemetente->execute();

        // Adiciona o valor ao saldo do destinatário
        $novoSaldoDestinatario = $saldoDestinatario + $valor;
        $updateDestinatario = $conecta_db->prepare("UPDATE usuario SET saldo_usuario = ? WHERE id_usuario = ?");
        $updateDestinatario->bind_param("di", $novoSaldoDestinatario, $id_destinatario);
        $updateDestinatario->execute();

        // Insere informações no histórico
        $insertHist = $conecta_db->prepare("INSERT INTO historico (email_recebe_Transferencia, data_Transferencia, valor_transferencia, email_Transferencia, tipo_Transferencia) VALUES (?, ?, ?, ?, ?)");
        $insertHist->bind_param("ssdss", $usuario, $hoje, $valor, $email_usuario, $tipo);
        $insertHist->execute();

        // Verifica se as operações foram bem-sucedidas
        if ($updateRemetente->affected_rows > 0 && $updateDestinatario->affected_rows > 0 && $insertHist->affected_rows > 0) {
            echo json_encode([
                'mensagem' => "<h1><center>Pix de R$ " . number_format($valor, 2, ',', '.') . " para $usuario realizado com sucesso!</center></h1>",
                'saldo' => number_format($novoSaldoRemetente, 2, ',', '.')
            ]);
        } else {
            echo json_encode(['mensagem' => "<h1><center>Erro ao processar a transferência.</center></h1>"]);
        }
    } else {
        echo json_encode(['mensagem' => "<h1><center>Saldo insuficiente.</center></h1>"]);
    }
} else {
    echo json_encode(['mensagem' => "<h1><center>Usuário não encontrado.</center></h1>"]);
}
error_log("Usuário logado: " . $_SESSION['usuario']);


// Fecha as declarações e a conexão
$querySaldo->close();
$queryUsuario->close();
$conecta_db->close();
?>

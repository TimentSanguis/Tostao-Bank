<?php
include '../php/conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// Acessa as informações da sessão
$nome_usuario = $_SESSION['usuario'];
$saldo_usuario = $_SESSION['saldo'];
$cliente_id = $_SESSION['id'];
$email_usuario = $_SESSION['email'];
$hoje = date('Y-m-d H:i:s');

$desc="Investido";
// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valor_investir = $_POST['valor'];
    $tipo_investimento = $_POST['tipo_investimento'];

    // Verificar o saldo atual do cliente
    $sql = "SELECT saldo_usuario FROM usuario WHERE id_usuario = ?";
    $stmt = $conecta_db->prepare($sql);
    if (!$stmt) {
        die("Erro na preparação da consulta: " . $conecta_db->error);
    }

    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $saldo_cliente = $row['saldo_usuario'];

        // Verificar se há saldo suficiente
        if ($saldo_cliente >= $valor_investir) {
            // Atualizar saldo no banco de dados
            $novo_saldo = $saldo_cliente - $valor_investir;

            $update_sql = "UPDATE usuario SET saldo_usuario = ? WHERE id_usuario = ?";
            $update_stmt = $conecta_db->prepare($update_sql);
            $update_stmt->bind_param("di", $novo_saldo, $cliente_id);
            $update_stmt->execute();

            // Atualizar o saldo na sessão
            $_SESSION['saldo'] = $novo_saldo;

            // Verificar se já existe um investimento desse tipo
            $investimento_sql = "SELECT valor_investimento FROM investimento WHERE cliente_id = ? AND tipo_investimento = ?";
            $investimento_stmt = $conecta_db->prepare($investimento_sql);
            $investimento_stmt->bind_param("is", $cliente_id, $tipo_investimento);
            $investimento_stmt->execute();
            $investimento_result = $investimento_stmt->get_result();

            if ($investimento_result->num_rows > 0) {
                // Atualizar o investimento existente
                $investimento_row = $investimento_result->fetch_assoc();
                $valor_atual = $investimento_row['valor_investimento'];
                $novo_valor = $valor_atual + $valor_investir;

                $update_investimento_sql = "UPDATE investimento SET valor_investimento = ? WHERE cliente_id = ? AND tipo_investimento = ?";
                $update_investimento_stmt = $conecta_db->prepare($update_investimento_sql);
                $update_investimento_stmt->bind_param("dis", $novo_valor, $cliente_id, $tipo_investimento);
                $update_investimento_stmt->execute();
            } else {
                // Inserir um novo investimento
                $insert_investimento_sql = "INSERT INTO investimento (cliente_id, valor_investimento, tipo_investimento) VALUES (?, ?, ?)";
                $insert_investimento_stmt = $conecta_db->prepare($insert_investimento_sql);
                $insert_investimento_stmt->bind_param("ids", $cliente_id, $valor_investir, $tipo_investimento);
                $insert_investimento_stmt->execute();
            }

            header('Location: ../confirmado.html');
            $insertHist = $conecta_db->prepare("INSERT INTO historico (data_Transferencia, valor_Transferencia, email_Transferencia, tipo_Transferencia, desc_transferencia) VALUES (?, ?, ?, ?, ?)");
            $insertHist->bind_param("sdsss", $hoje, $valor_investir, $email_usuario, $tipo_investimento, $desc);
            $insertHist->execute();
        } else {
            echo "Saldo insuficiente para realizar o investimento.";
        }
    } else {
        echo "Cliente não encontrado.";
    }

    // Fechar as instruções e a conexão
    $stmt->close();
    if (isset($update_stmt)) $update_stmt->close();
    if (isset($investimento_stmt)) $investimento_stmt->close();
}

?>

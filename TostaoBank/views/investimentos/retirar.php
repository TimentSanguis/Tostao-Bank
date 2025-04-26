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
$email_usuario = $_SESSION['email'];
$saldo_usuario = $_SESSION['saldo'];
$cliente_id = $_SESSION['id'];
$hoje = date('Y-m-d H:i:s');

// Verificar se o cliente existe
$sql = "SELECT * FROM usuario WHERE id_usuario = ?";
$stmt = $conecta_db->prepare($sql);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$result = $stmt->get_result();

$desc = "Retirada";

if ($result->num_rows == 0) {
    die("Cliente não encontrado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valor_retirar = $_POST['valor_retirar'];
    $tipo = $_POST['tipo_investimento'];
    

    // Consultar o investimento específico do cliente
    $sql = "SELECT valor_investimento FROM investimento WHERE cliente_id = ? AND tipo_investimento = ?";
    $stmt = $conecta_db->prepare($sql);
    $stmt->bind_param("is", $cliente_id, $tipo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $valor_investido = $row['valor_investimento'];

        // Verificar se o cliente pode retirar o valor
        if ($valor_retirar <= $valor_investido) {
            // Atualizar o saldo do cliente
            $novo_saldo = $saldo_usuario + $valor_retirar;
            $update_sql = "UPDATE usuario SET saldo_usuario = ? WHERE id_usuario = ?";
            $update_stmt = $conecta_db->prepare($update_sql);
            $update_stmt->bind_param("di", $novo_saldo, $cliente_id);
            $update_stmt->execute();

            // Atualizar ou remover o investimento
            $novo_valor = $valor_investido - $valor_retirar;
            if ($novo_valor > 0) {
                // Atualiza o valor do investimento existente
                $update_investimento_sql = "UPDATE investimento SET valor_investimento = ? WHERE cliente_id = ? AND tipo_investimento = ?";
                $update_investimento_stmt = $conecta_db->prepare($update_investimento_sql);
                $update_investimento_stmt->bind_param("dis", $novo_valor, $cliente_id, $tipo);
                $update_investimento_stmt->execute();
            } else {
                // Remove o investimento se o valor se tornar zero
                $delete_investimento_sql = "DELETE FROM investimento WHERE cliente_id = ? AND tipo_investimento = ?";
                $delete_investimento_stmt = $conecta_db->prepare($delete_investimento_sql);
                $delete_investimento_stmt->bind_param("is", $cliente_id, $tipo);
                $delete_investimento_stmt->execute();
            }

            // Atualizar saldo na sessão
            $_SESSION['saldo'] = $novo_saldo;

            header('Location: ../confirmado.html');
            $insertHist = $conecta_db->prepare("INSERT INTO historico (data_Transferencia, valor_Transferencia, email_Transferencia, tipo_Transferencia, desc_transferencia) VALUES (?, ?, ?, ?, ?)");
            $insertHist->bind_param("sdsss", $hoje, $valor_retirar, $email_usuario, $tipo, $desc );
            $insertHist->execute();
        } else {
            echo "Valor de retirada maior do que o valor investido.";
        }
    } else {
        echo "Nenhum investimento encontrado para este tipo.";
    }

    // Fechar as instruções
    $stmt->close();
    if (isset($update_stmt)) $update_stmt->close();
    if (isset($update_investimento_stmt)) $update_investimento_stmt->close();
    if (isset($delete_investimento_stmt)) $delete_investimento_stmt->close();
}

$conecta_db->close();
?>

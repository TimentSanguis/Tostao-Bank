<?php
session_start();
include 'conexao.php'; 

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['saldo' => 0]);
    exit;
}

// Acessa as informações da sessão
$nome_usuario = $_SESSION['usuario'];

// Prepara a consulta
$stmt = $conecta_db->prepare("SELECT saldo_usuario FROM usuario WHERE nome_usuario = ?");
$stmt->bind_param("s", $nome_usuario);
$stmt->execute();
$result = $stmt->get_result();

// Busca o saldo do usuário
$row = $result->fetch_assoc();

// Retorna o saldo em formato JSON
if ($row) {
    echo json_encode(['saldo' => number_format($row['saldo_usuario'], 2, ',', '.')]);
} else {
    echo json_encode(['saldo' => 0]); // Caso o usuário não tenha saldo
}

// Fecha a conexão
$stmt->close();
$conecta_db->close();
?>

<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['saldo' => 0]);
    exit;
}

$nome_usuario = $_SESSION['usuario'];

// Função para buscar saldo via API (supondo que sua API aceite buscar por nome_usuario)
function obterSaldoViaAPI($nome_usuario) {
    $url = "http://localhost:8080/tostao/saldo?nome_usuario=" . //Conexão API
    
    urlencode($nome_usuario);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Se precisar de token para a API, insira aqui, exemplo:
    // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $_SESSION['token']]);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode === 200) {
        return json_decode($response, true);
    }
    return null;
}

$saldoData = obterSaldoViaAPI($nome_usuario);

if ($saldoData && isset($saldoData['saldo_usuario'])) {
    echo json_encode(['saldo' => number_format($saldoData['saldo_usuario'], 2, ',', '.')]);
} else {
    echo json_encode(['saldo' => 0]);
}
?>

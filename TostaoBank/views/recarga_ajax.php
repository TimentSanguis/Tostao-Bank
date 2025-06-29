<?php
session_start();
header('Content-Type: application/json');

// Verifica se o usuário está logado e tem token
if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    echo json_encode(['mensagem' => 'Usuário não autenticado.']);
    exit;
}

$email = $_SESSION['email'];
$token = $_SESSION['token'];
$valor = $_POST['txt_valor'];
$numero = $_POST['txt_numero'];

$data = [
    'valor' => $valor,
    'numero' => $numero,
    'email' => $email
];

// Requisição para a API
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => 'http://localhost:8080/tostao/recarga',//Conexão API
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        "Authorization: Bearer $token"
    ],
]);

$response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($http_code === 200) {
    $res = json_decode($response, true);
    echo json_encode([
        'mensagem' => $res['mensagem'] ?? 'Recarga realizada com sucesso.',
        'saldo' => $res['saldo'] ?? null
    ]);
} else {
    echo json_encode(['mensagem' => 'Erro ao comunicar com a API de recarga.']);
}

?>
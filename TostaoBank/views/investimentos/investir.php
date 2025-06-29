<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$cliente_id = $_SESSION['id'];
$email_usuario = $_SESSION['email'];
$hoje = date('Y-m-d H:i:s');
$desc = "Investido";

function callAPI($method, $url, $data = false) {
    $curl = curl_init();

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case "DELETE":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            if ($data) curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        default:
            if (is_array($data) && !empty($data)) {
                $url = sprintf("%s?%s", $url, http_build_query($data));
            }
            break;
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $result = curl_exec($curl);

    if ($result === false) {
        die("Erro na requisição cURL: " . curl_error($curl));
    }

    curl_close($curl);

    return json_decode($result, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['valor'], $_POST['tipo_investimento'])) {
        die("Dados do formulário incompletos.");
    }

    $valor_investir = floatval($_POST['valor']);
    $tipo_investimento = $_POST['tipo_investimento'];

    $apiBase = "http://localhost:8080/tostao/investir";

    // 1. Buscar saldo atual do usuário via API
    $urlSaldo = "{$apiBase}/usuario/{$cliente_id}/saldo";
    $saldoResp = callAPI("GET", $urlSaldo);

    if (!isset($saldoResp['saldo'])) {
        die("Não foi possível obter saldo do usuário.");
    }

    $saldo_cliente = floatval($saldoResp['saldo']);

    if ($saldo_cliente < $valor_investir) {
        echo "Saldo insuficiente para realizar o investimento.";
        exit;
    }

    // 2. Atualizar saldo do usuário
    $novo_saldo = $saldo_cliente - $valor_investir;
    $urlAtualizaSaldo = "{$apiBase}/usuario/{$cliente_id}/saldo";
    $atualizaSaldoResp = callAPI("PUT", $urlAtualizaSaldo, ['saldo_usuario' => $novo_saldo]);

    if (!isset($atualizaSaldoResp['success']) || !$atualizaSaldoResp['success']) {
        die("Falha ao atualizar saldo.");
    }

    // 3. Buscar investimento do tipo específico
    $urlBuscaInvest = "{$apiBase}/investimento";
    $investimentos = callAPI("GET", $urlBuscaInvest, ['cliente_id' => $cliente_id, 'tipo_investimento' => $tipo_investimento]);

    if (!empty($investimentos) && isset($investimentos[0])) {
        $investimento = $investimentos[0];
        $novo_valor = floatval($investimento['valor_investimento']) + $valor_investir;

        $urlAtualizaInvest = "{$apiBase}/investimento/{$investimento['id']}";
        $respAtualizaInvest = callAPI("PUT", $urlAtualizaInvest, ['valor_investimento' => $novo_valor]);

        if (!isset($respAtualizaInvest['success']) || !$respAtualizaInvest['success']) {
            die("Falha ao atualizar investimento.");
        }
    } else {
        $urlCriarInvest = "{$apiBase}/investimento";
        $respCriarInvest = callAPI("POST", $urlCriarInvest, [
            'cliente_id' => $cliente_id,
            'valor_investimento' => $valor_investir,
            'tipo_investimento' => $tipo_investimento
        ]);
        if (!isset($respCriarInvest['success']) || !$respCriarInvest['success']) {
            die("Falha ao criar investimento.");
        }
    }

    // 4. Criar histórico da operação
    $urlHist = "{$apiBase}/historico";
    $respHist = callAPI("POST", $urlHist, [
        'data_Transferencia' => $hoje,
        'valor_Transferencia' => $valor_investir,
        'email_Transferencia' => $email_usuario,
        'tipo_Transferencia' => $tipo_investimento,
        'desc_transferencia' => $desc
    ]);

    if (!isset($respHist['success']) || !$respHist['success']) {
        die("Falha ao registrar histórico.");
    }

    $_SESSION['saldo'] = $novo_saldo;
    header('Location: ../confirmado.html');
    exit;
}
?>
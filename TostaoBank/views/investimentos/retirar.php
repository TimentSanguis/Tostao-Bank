<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); 
    exit;
}

$nome_usuario = $_SESSION['usuario'];
$email_usuario = $_SESSION['email'];
$saldo_usuario = $_SESSION['saldo'];
$cliente_id = $_SESSION['id'];
$hoje = date('Y-m-d H:i:s');

$desc = "Retirada";

function callAPI($method, $url, $data = false) {
    $curl = curl_init();

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            break;

        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            break;

        case "DELETE":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            break;

        default:
            // Para GET e outros, adiciona query string só se $data for um array ou algo válido
            if ($data && is_array($data)) {
                $url = sprintf("%s?%s", $url, http_build_query($data));
            }
            break;
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        //'Authorization: Bearer ' . $_SESSION['token'] // Se usar token
    ]);

    $result = curl_exec($curl);
    if (!$result) {
        die("Erro ao acessar API: " . curl_error($curl));
    }
    curl_close($curl);
    return json_decode($result, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valor_retirar = floatval($_POST['valor_retirar']);
    $tipo = $_POST['tipo_investimento'];

    // 1. Verifica investimento do cliente e tipo via API
    $investimento = callAPI("GET", "http://localhost:8080/tostao/investir", [
        "cliente_id" => $cliente_id,
        "tipo_investimento" => $tipo
    ]);

    if (!$investimento || !isset($investimento['valor_investimento'])) {
        die("Nenhum investimento encontrado para este tipo.");
    }

    $valor_investido = floatval($investimento['valor_investimento']);

    if ($valor_retirar <= $valor_investido) {
        // 2. Atualiza saldo do cliente (soma o valor da retirada)
        $novo_saldo = $saldo_usuario + $valor_retirar;
        $updateSaldo = callAPI("PUT", "http://localhost:8080/tostao/$cliente_id/saldo", [
            "saldo" => $novo_saldo
        ]);

        if (!$updateSaldo || !isset($updateSaldo['success']) || !$updateSaldo['success']) {
            die("Falha ao atualizar saldo.");
        }

        // 3. Atualiza investimento (diminui ou remove)
        $novo_valor = $valor_investido - $valor_retirar;

        if ($novo_valor > 0) {
            $updateInvestimento = callAPI("PUT", "http://localhost:8080/tostao/retirar", [
                "cliente_id" => $cliente_id,
                "tipo_investimento" => $tipo,
                "valor_investimento" => $novo_valor
            ]);
            if (!$updateInvestimento || !isset($updateInvestimento['success']) || !$updateInvestimento['success']) {
                die("Falha ao atualizar investimento.");
            }
        } else {
            $deleteInvestimento = callAPI("DELETE", "http://localhost:8080/tostao/investir", [
                "cliente_id" => $cliente_id,
                "tipo_investimento" => $tipo
            ]);
            if (!$deleteInvestimento || !isset($deleteInvestimento['success']) || !$deleteInvestimento['success']) {
                die("Falha ao deletar investimento.");
            }
        }

        // 4. Atualiza saldo na sessão
        $_SESSION['saldo'] = $novo_saldo;

        // 5. Registra histórico da transação
        $insertHist = callAPI("POST", "http://localhost:8080/tostao/historico", [
            "data_Transferencia" => $hoje,
            "valor_Transferencia" => $valor_retirar,
            "email_Transferencia" => $email_usuario,
            "tipo_Transferencia" => $tipo,
            "desc_transferencia" => $desc
        ]);

        if (!$insertHist || !isset($insertHist['success']) || !$insertHist['success']) {
            die("Falha ao registrar histórico.");
        }

        // Redireciona após sucesso
        header('Location: ../confirmado.html');
        exit;
    } else {
        echo "Valor de retirada maior do que o valor investido.";
    }
}
?>
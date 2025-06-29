<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$email_sessao = $_SESSION['email'];
$apiBaseUrl = 'http://localhost:8080/tostao';

function apiGetRequest($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        curl_close($ch);
        return false;
    }
    curl_close($ch);
    return json_decode($response, true);
}

// ======================
// Tratamento do PIX
// ======================
$valor = 0.0;
$mensagem = '';
$sucesso = false;

// Variáveis de resposta
$mensagem = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailRemetente = $_SESSION['email'] ?? '';
    $valorInput = $_POST['valor'] ?? '';
    $emailDestino = $_POST['email_destino'] ?? ''; // Corrigido: nome correto do campo vindo do formulário

    // Validação simples
    $valorLimpo = str_replace(['.', ','], ['', '.'], $valorInput);
    $valor = floatval($valorLimpo);

    if ($valor > 0 && filter_var($emailDestino, FILTER_VALIDATE_EMAIL) && !empty($emailRemetente)) {
        $data = [
            "email" => $emailRemetente,
            "valor" => $valor,
            "emailRecebe" => $emailDestino
        ];

        $payload = json_encode($data);

        $ch = curl_init($apiBaseUrl . "/pix");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload)
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $mensagem = 'Erro na conexão com a API: ' . curl_error($ch);
        } else {
            if ($http_code === 200) {
                $sucesso = true;
                $mensagem = 'Transferência realizada com sucesso!';
                header("Location: confirmado.html");
            } elseif ($http_code === 400) {
                $mensagem = 'Erro: Saldo insuficiente.';
            } elseif ($http_code === 404) {
                $mensagem = 'Erro: Usuário não encontrado.';
            } else {
                $mensagem = 'Erro na transferência. Código: ' . $http_code;
            }
        }

        curl_close($ch);
    } else {
        $mensagem = 'Preencha corretamente todos os campos.';
    }

}

// ======================
// Obter dados do usuário
// ======================
$userData = apiGetRequest($apiBaseUrl . "/usuario?email=" . urlencode($email_sessao));

$nome_usuario = isset($userData['nomeUsuario']) ? htmlspecialchars($userData['nomeUsuario']) : 'Usuário';
$saldo_usuario = isset($userData['saldoUsuario']) && is_numeric($userData['saldoUsuario']) ? number_format($userData['saldoUsuario'], 2, ',', '.') : '0,00';
$email_usuario = isset($userData['emailUsuario']) ? htmlspecialchars($userData['emailUsuario']) : '';
$telefone_usuario = isset($userData['telefoneUsuario']) ? (string)$userData['telefoneUsuario'] : '';
$num_cartao = isset($userData['cartaoUsuario']) ? (string)$userData['cartaoUsuario'] : "Número de cartão não disponível";
$num_formatado = preg_replace('/(\d{4})/', '$1   ', $num_cartao);

$telefone_formatado = $telefone_usuario ? preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone_usuario) : '';
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../resources/css/dashboard-style.css" />
    <link rel="shortcut icon" type="image/x-icon" href="../resources/files/pics/logo_tostao_bank.png"/>
    <!-- <script src="./js/dashboard-js.js"></script> -->
    <title>Dashboard</title>
  </head>
  <body>
    <header></header>
    <main>
      <section>
        <div class="container">
          <div class="ham-menu">
            <img
              src="../resources/files/pics/hamburgur.svg"
              id="btn"
              alt=""
              onclick="myFunction()"
            />
          </div>
          <div class="home-button">
            <img
              src="../resources/files/pics/home.svg"
              id="btn"
              alt=""
              onclick="window.location.href='dashboard.php'"
            />
          </div>
          <div class="row">
            <div class="column-left" id="column-left">
              <section>
                <div class="profile">
                  <div class="container-2">
                    <div class="photo">
                      <div class="img">
                        <img src="../resources/files/pics/user.png" alt="" />
                      </div>
                    </div>
                    <div class="name">
                      <div class="container-3">
                        <div class="text">
                          <p class="hello"><span>Bem vindo,</span></p>
                          <p class="fname"><span><?php echo $nome_usuario; ?></span></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="balance-container">
                  <div class="balance-text">
                    <p><span>Seus Trocados:</span></p>
                      <p>
                        <span class="money">
                          <span><span style="color: green; font-size: 25px">R$ </span><?php echo $saldo_usuario; ?></span>
                        </span>
                      </p>
                  </div>
                </div>
                <div class="account-info">
                  <div class="info-text">
                    <p style="margin-bottom: 10px">
                      <span style="font-size: 20px">Dados do Pobre</span>
                    </p>
                    <p><span>Email: <?php echo $email_usuario; ?></span></p>
                    <p><span>Celular: <?php echo $telefone_formatado; ?></span></p> <!-- Exibindo o telefone formatado -->
                  </div>
                </div>
              </section>
            </div>
            <div class="column-middle">
              <section>
                <div class="transaction">
                  <div class="trans-container">
                    <div class="title">
                      <p><span>Pix</span></p>
                    </div>
                    <div class="trans-info">
                      <div class="container">
                      <form id="pixForm" method="POST">
                        <label for="money">Valor transferência</label>
                        <input type="text" id="money" name="valor" oninput="limitarDecimais(event)" required />
                        
                        <div class="dollor-sign">
                          <p><span>$</span></p>
                        </div>
                        
                        <label for="card-num">Chave Pix</label>
                        <input type="email" id="card-num" name="email_destino" placeholder="abc@gmail.com" required />
                        
                        <button type="submit">Transferir</button>

                        <?php if (!empty($mensagem)): ?>
                          <div class="mensagem <?php echo $sucesso ? 'sucesso' : 'erro'; ?>">
                            <?php echo htmlspecialchars($mensagem); ?>
                          </div>
                        <?php endif; ?>
                        
                      </form>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
            </div>
            <div class="column-right">
              <section>
                <div class="card">
                <div class="card-container">
                    <div class="title">
                      <p><span>Cartão & Ferramentas</span></p>
                    </div>
                    <div class="card-visual">
                      <div class="card-pic">
                        <div class="card-brand">
                          <p><span>Tostão Bank</span></p>
                        </div>
                        <div class="chip">
                          <img src="../resources/files/pics/chip.png" alt="" />
                        </div>
                        <div class="name">
                          <p class="fname"><span><?php echo $nome_usuario; ?></span></p>
                        </div>
                        <div class="card-num" id="card-number">
                          <p class="fname"><span><?php echo trim($num_formatado); ?></span></p>
                        </div>
                        <div class="card-exp-cvv">
                          <p>
                            <span style="float: left">exp: 2038/07</span
                            ><span style="float: right">cvv: 999</span>
                          </p>
                        </div>
                      </div>
                    </div>
                    <div class="tools-container">
                      <div class="tools-row">
                        <div class="tools-column">
                          <div class="option" style="box-shadow: 0px 1px 15px rgba(0, 0, 0);
                          transition: all 0.3s; background-color: #f4505083;">
                            <div class="container">
                              <div class="logo">
                                <img
                                  src="../resources/files/pics/transfer.svg"
                                  alt=""
                                />
                              </div>
                              
                              <div class="text"><p>Pix</p></div>
                            </div>
                          </div>
                          <div class="option" onclick="window.location.href='dashboard-caixinha.php'">
                            <div class="container">
                              <div class="logo">
                                <img src="../resources/files/pics/bill.svg" alt="">
                              </div>
                              <div class="text"><p>Caixinha</p></div>
                            </div>
                          </div>

                          <!--Botão de Seguros-->
                        <div class="option" onclick="window.location.href='dashboard-seguros.php'">
                          <div class="container">
                            <div class="logo">
                              <img src="../resources/files/pics/seguros.svg" alt=""/>
                            </div>
                            <div class="text"><p>Seguros</p></div>
                          </div>
                        </div>
                        
                          <div class="option" onclick="window.location.href='dashboard-recarga.php'">
                            <div class="container">
                              <div class="logo">
                                <img src="../resources/files/pics/simcard.svg" alt="">
                              </div>
                              <div class="text"><p>Recarga Celular</p></div>
                            </div>
                          </div>
                        </div>
                        <div class="tools-column">
                          <div class="option" id="copy-card">
                            <div class="container">
                              <div class="logo">
                                <img src="../resources/files/pics/copy.svg" alt="">
                              </div>
                              <div class="text"><p>Copiar Cartão</p></div>
                            </div>
                          </div>

                          <div class="option" onclick="window.location.href='dashboard-senha.php'">
                            <div class="container">
                              <div class="logo">
                                <img src="../resources/files/pics/password.svg" alt="">
                              </div>
                              <div class="text"><p>Senha Online</p></div>
                            </div>
                          </div>
                          <div class="option" onclick="window.location.href='dashboard-desativar.php'">
                            <div class="container">
                              <div class="logo">
                                <img src="../resources/files/pics/disable.svg" alt="">
                              </div>
                              <div class="text"><p>Desativar conta</p></div>
                            </div>
                          </div>
                          <div class="option" onclick="window.location.href='dashboard.php?logout=true'">
                              <div class="container">
                                  <div class="logo">
                                      <img src="../resources/files/pics/more.svg" alt="">
                                  </div>
                                  <div class="text"><p>Sair</p></div>
                              </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
            </div>
          </div>
        </div>
      </section>
    </main>
    <footer></footer>
    <script src="../resources/js/dashboard-js.js" defer></script>
<script>
  function limitarDecimais(event) {
    let valor = event.target.value;
    let partes = valor.split(",");
    if (partes.length === 2 && partes[1].length > 2) {
      event.target.value = partes[0] + "," + partes[1].substring(0, 2);
    }
  }

  document.addEventListener("DOMContentLoaded", function() {
    // Limitar decimais no input txt_valor (exemplo: id="charge")
    const valorInput = document.getElementById('charge');
    if (valorInput) {
      valorInput.addEventListener('input', limitarDecimais);
    }

    const saldoValue = document.getElementById('saldoValue');
    const saldoDisplay = document.getElementById('saldoDisplay'); // Se existir na sua página

    // Função para atualizar saldo via fetch
    function atualizarSaldo() {
      fetch('php/obter_saldo.php')
      .then(response => {
        if (!response.ok) throw new Error('Resposta inválida da rede');
        return response.json();
      })
      .then(data => {
        if (data.saldo) {
          saldoValue.innerHTML = data.saldo;
          if (saldoDisplay) saldoDisplay.innerHTML = `Seu saldo: R$ ${data.saldo}`;
        } else if (data.erro) {
          console.error(data.erro);
        }
      })
      .catch(error => console.error('Erro ao atualizar saldo:', error));
    }

    // Atualiza saldo a cada 10 segundos
    setInterval(atualizarSaldo, 10000);

    // Copiar número do cartão ao clicar no botão
    const optionElement = document.querySelector('#copy-card');
    if (optionElement) {
      optionElement.addEventListener('click', () => {
        const text = document.querySelector('#card-number').textContent.trim();
        navigator.clipboard.writeText(text)
          .then(() => alert('Copiado para área de transferência!'))
          .catch(() => alert('Falha ao copiar o texto.'));
      });
    }
  });
</script>
	</body>
</html>
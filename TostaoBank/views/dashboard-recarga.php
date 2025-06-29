<?php
session_start();

// Verifica se o usuário está logado e tem token
if (!isset($_SESSION['usuario']) || !isset($_SESSION['token'])) {
    header("Location: login.php");
    exit;
}

// Pega id do usuário da sessão
$cliente_id = $_SESSION['id'] ?? null;
if (!$cliente_id) {
    echo "ID do usuário não encontrado na sessão.";
    exit;
}

// Inicializa variáveis com dados da sessão (fallback)
$nome_usuario = htmlspecialchars($_SESSION['nome'] ?? '', ENT_QUOTES, 'UTF-8');
$email_usuario = htmlspecialchars($_SESSION['email'] ?? '', ENT_QUOTES, 'UTF-8');
$telefone_usuario = $_SESSION['telefone'] ?? '';
$num_cartao = $_SESSION['num_cartao'] ?? "Número de cartão não disponível";
$saldo_usuario = number_format($_SESSION['saldo'] ?? 0, 2, ',', '.');

// Faz requisição à API para pegar dados atualizados
$token = $_SESSION['token'];
$apiUrl = "http://localhost:8080/tostao/usuario/$cliente_id";

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode == 200 && $response !== false) {
    $dados = json_decode($response, true);

    // Atualiza variáveis com dados da API, se existir
    $nome_usuario = htmlspecialchars($dados['nomeUsuario'] ?? $nome_usuario, ENT_QUOTES, 'UTF-8');
    $email_usuario = htmlspecialchars($dados['emailUsuario'] ?? $email_usuario, ENT_QUOTES, 'UTF-8');
    $telefone_usuario = $dados['telefoneUsuario'] ?? $telefone_usuario;
    $saldo_usuario = number_format($dados['saldoUsuario'] ?? 0, 2, ',', '.');
    $num_cartao = isset($dados['cartaoUsuario']) ? (string)$dados['cartaoUsuario'] : $num_cartao;
}

// Formata número do cartão (grupos de 4 dígitos) ou mantém texto se inválido
if (is_numeric($num_cartao)) {
    $num_formatado = preg_replace('/(\d{4})(?=\d)/', '$1 ', $num_cartao);
} else {
    $num_formatado = $num_cartao;
}

// Formata telefone ou define texto padrão
if (!empty($telefone_usuario)) {
    $telefone_formatado = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone_usuario);
} else {
    $telefone_formatado = 'Telefone não disponível';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../resources/css/dashboard-style.css" />
    <link
      rel="shortcut icon"
      type="image/x-icon"
      href="../resources/files/pics/logo_tostao_bank.png"
    />
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
                    <p><span>Celular: <?php echo $telefone_formatado; ?></span></p>
                  </div>
                </div>
              </section>
            </div>
            <div class="column-middle">
              <section>
                <div class="transaction">
                  <div class="trans-container">
                    <div class="title">
                      <p><span>Recarga Sim</span></p>
                    </div>
                    <div class="trans-info">
                      <div class="container">
                        <form id="recargaForm">
                          <label for="charge">Valor</label>
                          <input type="text" id="charge" name="txt_valor" required />
                          <label for="Phone-num">Número de celular</label>
                          <input type="number" id="Phone-num" name="txt_numero" required />
                          <button type="submit">Recarga</button>
                        </form>
                        <div id="mensagem"></div>
                          <span id="saldoValue"></span>
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
                          <div class="option" onclick="window.location.href='dashboard-pix.php'">
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
                        
                          <div class="option" style="box-shadow: 0px 1px 15px rgba(0, 0, 0); /* add a light gray shadow */
                          transition: all 0.3s; background-color: #f4505083;">
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
document.addEventListener("DOMContentLoaded", function() {
  // Limita casas decimais no input do valor
  const valorInput = document.getElementById('charge');
  valorInput.addEventListener('input', function(event) {
    let valor = event.target.value;
    let partes = valor.split(",");
    if (partes.length === 2 && partes[1].length > 2) {
      event.target.value = partes[0] + "," + partes[1].substring(0, 2);
    }
  });

  const form = document.getElementById('recargaForm');
  const mensagemDiv = document.getElementById('mensagem');

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    mensagemDiv.textContent = ''; // limpa mensagem

    // Pega dados do form e monta objeto JSON
    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => {
      if (key === 'txt_valor') {
        value = value.replace(',', '.'); // troca vírgula por ponto decimal
      }
      data[key] = value;
    });

    // Pega email do PHP (usuário logado)
    const emailUsuario = '<?php echo $email_usuario; ?>';

    // Monta objeto para enviar
    const bodyJson = {
      email: emailUsuario,
      valor: parseFloat(data['txt_valor']),
      telefone: data['txt_numero']
    };

    // Valida valor para evitar envio inválido
    if (isNaN(bodyJson.valor) || bodyJson.valor <= 0) {
      mensagemDiv.style.color = 'red';
      mensagemDiv.textContent = 'Por favor, insira um valor válido para recarga.';
      return;
    }

    fetch('http://localhost:8080/tostao/recarga', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(bodyJson)
    })
    .then(async response => {
      if (!response.ok) {
        const text = await response.text();
        throw new Error('Erro na resposta da API: ' + response.status + ' - ' + text);
      }
      return response.json();
    })
    .then(data => {
      console.log('Recarga efetuada:', data);
      mensagemDiv.style.color = 'green';
      mensagemDiv.textContent = 'Recarga efetuada com sucesso!';
      window.location.href = 'confirmado.html';

      // Atualiza saldo na tela
      const saldoEl = document.getElementById('saldoValue');
      if (saldoEl && data.saldoUsuario !== undefined) {
        // Formata saldo para BRL
        saldoEl.textContent = data.saldoUsuario.toFixed(2).replace('.', ',');
      }

      // Limpa o formulário
      form.reset();
    })
    .catch(error => {
      console.error('Erro capturado:', error);
      mensagemDiv.style.color = 'red';
      mensagemDiv.textContent = error.message;
    });
  });
});
</script>
  </body>
</html>

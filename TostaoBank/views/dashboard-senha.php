<?php
session_start(); // Inicia a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$email_sessao = $_SESSION['email'];

// URL base da API
$apiBaseUrlUsuario = 'http://localhost:8080/tostao';//Conexão API
$apiBaseUrlHistorico = 'http://localhost:8080/historico';

// Função para fazer requisição GET via cURL
function apiGetRequest($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        // Em caso de erro, pode tratar aqui
        curl_close($ch);
        return false;
    }
    curl_close($ch);
    return json_decode($response, true);
}

// Buscar dados do usuário via API
$userData = apiGetRequest($apiBaseUrlUsuario . "/usuario?email=" . urlencode($email_sessao));
$transacoes = apiGetRequest($apiBaseUrlHistorico . "/transferencias?email=" . urlencode($email_sessao));

if (!$userData) {
    die("Erro ao buscar dados do usuário na API");
}

$nome_usuario = isset($userData['nomeUsuario']) ? htmlspecialchars($userData['nomeUsuario'], ENT_QUOTES, 'UTF-8') : 'Usuário';
$saldo_usuario = isset($userData['saldoUsuario']) && is_numeric($userData['saldoUsuario'])? number_format($userData['saldoUsuario'], 2, ',', '.'): '0,00';
$email_usuario = isset($userData['emailUsuario']) ? htmlspecialchars($userData['emailUsuario'], ENT_QUOTES, 'UTF-8') : '';
$telefone_usuario = isset($userData['telefoneUsuario']) ? (string)$userData['telefoneUsuario'] : '';
$num_cartao = isset($userData['cartaoUsuario']) ? (string)$userData['cartaoUsuario'] : "Número de cartão não disponível";
$num_formatado = preg_replace('/(\d{4})/', '$1   ', $num_cartao);

// Formatar telefone se estiver presente
if ($telefone_usuario) {
    $telefone_formatado = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone_usuario);
} else {
    $telefone_formatado = '';
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
                    <!-- <div class="bank-name">
                      <p><span>Nura Bank</span></p>
                    </div> -->
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
                      <p><span>Senha Online</span></p>
                    </div>
                    <div class="trans-info">
                      <div class="container">
                        <div class="warning">
                          <p>
                            <span
                              >Para comprar online, você precisa de uma senha online
                              de uso único. Ao pressionar o botão,
                              você receberá a senha via SMS.
                            </span>
                          </p>
                        </div>
                        <div class="online-password">
                          <form action="" method="">
                            <button id="online-pass-button" class="activation">Receber Senha</button>
                          </form>
                        </div>
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
                          <div
                            class="option"
                            onclick="window.location.href='dashboard-pix.php'"
                          >
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
                          <div
                            class="option"
                            onclick="window.location.href='dashboard-caixinha.php'"
                          >
                            <div class="container">
                              <div class="logo">
                                <img
                                  src="../resources/files/pics/bill.svg"
                                  alt=""
                                />
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

                          <div
                            class="option"
                            onclick="window.location.href='dashboard-recarga.php'"
                          >
                            <div class="container">
                              <div class="logo">
                                <img
                                  src="../resources/files/pics/simcard.svg"
                                  alt=""
                                />
                              </div>
                              <div class="text"><p>Recarga Celular</p></div>
                            </div>
                          </div>
                        </div>
                        <div class="tools-column">
                          <div class="option" id="copy-card">
                            <div class="container">
                              <div class="logo">
                                <img
                                  src="../resources/files/pics/copy.svg"
                                  alt=""
                                />
                              </div>
                              <div class="text"><p>Copiar Cartão</p></div>
                            </div>
                          </div>

                          <div
                            class="option"
                            style="
                              box-shadow: 0px 1px 15px rgba(0, 0, 0); /* add a light gray shadow */
                              transition: all 0.3s;
                              background-color: #f4505083;
                            "
                          >
                            <div class="container">
                              <div class="logo">
                                <img
                                  src="../resources/files/pics/password.svg"
                                  alt=""
                                />
                              </div>
                              <div class="text"><p>Senha Online</p></div>
                            </div>
                          </div>
                          <div
                            class="option"
                            onclick="window.location.href='dashboard-desativar.php'"
                          >
                            <div class="container">
                              <div class="logo">
                                <img
                                  src="../resources/files/pics/disable.svg"
                                  alt=""
                                />
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
  </body>
</html>

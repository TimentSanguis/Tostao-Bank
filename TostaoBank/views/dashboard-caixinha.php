<?php
session_start(); // Inicia a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); // Redireciona para o login se não estiver logado
    exit;
}

// Pega informações da sessão
$nome_usuario = htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8');
$email_usuario = htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8');
$telefone_usuario = $_SESSION['telefone'] ?? null;

// Pega o id do cliente da sessão
$cliente_id = $_SESSION['id'] ?? null;

if (!$cliente_id) {
    echo "ID do usuário não encontrado na sessão.";
    exit;
}

// Função para fazer requisição GET à API e pegar dados do usuário
function pegarDadosUsuarioAPI($cliente_id) {
    $url = "http://localhost:8080/tostao/usuario/$cliente_id";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode === 200) {
        return json_decode($response, true);
    } else {
        return null;
    }
}

$dadosUsuario = pegarDadosUsuarioAPI($cliente_id);

if (!$dadosUsuario) {
    echo "Erro ao obter dados do usuário via API.";
    exit;
}

// Extrai e formata os dados do usuário
$nome = htmlspecialchars($dadosUsuario['nomeUsuario'] ?? 'Nome não disponível', ENT_QUOTES, 'UTF-8');
$saldo = number_format($dadosUsuario['saldoUsuario'] ?? 0, 2, ',', '.');

// Número do cartão vindo da API (melhor do que depender da sessão)
$num_cartao = $dadosUsuario['cartaoUsuario'] ?? 'Número não disponível';

// Formatação do número do cartão
if (is_numeric($num_cartao)) {
    $num_formatado = trim(chunk_split($num_cartao, 4, ' '));
} else {
    $num_formatado = 'Número de cartão inválido';
}

// Formatação do telefone
if ($telefone_usuario && preg_match('/^\d{11}$/', $telefone_usuario)) {
    $telefone_formatado = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone_usuario);
} else {
    $telefone_formatado = 'Telefone não disponível';
}

// ----------- PROCESSO DE INVESTIR -----------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? null;
    $valor = $_POST['valor'] ?? null;
    $tipo_investimento = $_POST['tipo_investimento'] ?? null;

    if (!$valor || !is_numeric($valor) || floatval($valor) <= 0) {
        $erro = "Valor inválido.";
    } elseif (!$tipo_investimento) {
        $erro = "Tipo de investimento inválido.";
    } elseif (!$email_usuario) {
        $erro = "Usuário não autenticado.";
    }

    if (!isset($erro)) {
        $dados_investimento = [
            'valorInvestimento' => floatval($valor),
            'tipoInvestimento' => $tipo_investimento,
            'emailUsuario' => $email_usuario
        ];

        // Define a URL dependendo da ação
        if ($acao === 'investir') {
            $url = "http://localhost:8080/investimentos/investir";
        } elseif ($acao === 'retirar') {
            $url = "http://localhost:8080/investimentos/retirar";
        } else {
            $erro = "Ação inválida.";
        }

        if (!isset($erro)) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados_investimento));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode === 200 || $httpcode === 201) {
                header("Location: confirmado.html");
                exit;
            } else {
                $response_decoded = json_decode($response, true);
                $mensagem_erro_api = $response_decoded['message'] ?? "Erro desconhecido";
                $erro = "Erro ao realizar ação '$acao': $mensagem_erro_api (HTTP $httpcode)";
            }
        }
    }
}


?>


<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../resources/css/dashboard-style.css" />
    <link rel="icon" type="image/x-icon" href="../resources/files/pics/logo_tostao_bank.png"/>
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
                          <p class="fname"><span><?php echo $nome; ?></span></p>
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
                        <span><span style="color: green; font-size: 25px">R$ </span><?php echo $saldo; ?></span>
                      </span>
                    </p>
                  </div>
                </div>
                <div class="account-info">
                  <div class="info-text">
                    <p style="margin-bottom: 10px">
                      <span style="font-size: 20px">Dados do Pobre</span>
                  
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
                      <p><span>Caixinhas</span></p>
                    </div>
                    <div class="trans-info">
                      <div class="history">
                      <table>
                      <thead>
                          <tr>
                              <th>Caixinha</th>
                              <th>Descrição</th>
                              <th>Rendimento</th>
                          </tr>
                      </thead>
                      <tbody>
                          <tr>
                              <td><h3>Reserva de Emergência</h3></td>
                              <td>Ideal para imprevistos, como despesas médicas ou consertos urgentes.</td>
                              <td>Rendimento médio de 100% do CDI, liquidez diária.</td>
                          </tr>
                          <tr>
                              <td><h3>Fazer uma Viagem</h3></td>
                              <td>Guarde dinheiro para realizar aquela viagem dos sonhos.</td>
                              <td>Rendimento de até 105% do CDI para planos de médio prazo.</td>
                          </tr>
                          <tr>
                              <td><h3>Meu Sonho de Consumo</h3></td>
                              <td>Conquiste bens materiais ou serviços desejados.</td>
                              <td>Rendimento de até 110% do CDI, ideal para metas de longo prazo.</td>
                          </tr>
                      </tbody>
                  </table>

                      <!-- Formulário de Investimento -->
                      <section id="investir">
                        <div class='container'>
                          <form method="post">
                              <input type="hidden" name="acao" value="investir">
                              <label>Valor a Investir:</label>
                              <input type="number" name="valor" step="0.01" min="0.01" required>
                              <label>Tipo de Investimento:</label>
                              <select name="tipo_investimento" required>
                                  <option value="Reserva de Emergência">Reserva de Emergência</option>
                                  <option value="Fazer uma viagem">Fazer uma viagem</option>
                                  <option value="Meu sonho de consumo">Meu sonho de consumo</option>
                              </select>
                              <button type="submit">Investir</button>
                          </form>
                        </div>
                      </section>

                      <!-- Formulário de Retirada -->
                      <section id="retirar">
						<div class='container'>
              <form method="post">
                  <input type="hidden" name="acao" value="retirar">
                  <label>Valor a Retirar:</label>
                  <input type="number" name="valor" step="0.01" min="0.01" required>
                  <label>Tipo de Investimento:</label>
                  <select name="tipo_investimento" required>
                      <option value="Reserva de Emergência">Reserva de Emergência</option>
                      <option value="Fazer uma viagem">Fazer uma viagem</option>
                      <option value="Meu sonho de consumo">Meu sonho de consumo</option>
                  </select>
                  <button type="submit">Retirar</button>
              </form>
						</div>
                      </section>  
                      <!-- Tabela de Investimentos -->
                      <?php
                      // Suponha que $cliente_id esteja definido

                      // URL da API para pegar investimentos do cliente
                      $url = "http://localhost:8080/investimentos/investimento?email=$email_usuario";

                      // Inicializa cURL
                      $ch = curl_init($url);
                      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                      // Se precisar de autenticação, adicione aqui o cabeçalho, exemplo:
                      // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer seu_token']);

                      // Executa requisição
                      $response = curl_exec($ch);
                      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                      curl_close($ch);

                      if ($httpcode !== 200) {
                          echo "Erro ao acessar API de investimentos. Código HTTP: $httpcode";
                          exit;
                      }

                      // Decodifica resposta JSON
                      $investimentos = json_decode($response, true);

                      if (!$investimentos || count($investimentos) === 0) {
                          echo "Nenhum investimento encontrado para este cliente.";                                     
                      }else{

                        // Monta tabela HTML com os dados da API
                        echo "<table border='1'>";
                        echo "<tr><th>Tipo</th><th>Valor</th><th>Data</th></tr>";

                        foreach ($investimentos as $inv) {
                            $tipo = htmlspecialchars($inv['tipoInvestimento'] ?? 'N/A');
                            $valor = number_format($inv['valorInvestimento'] ?? 0, 2, ',', '.');
                            $data = isset($inv['dataInvestimento']) ? date("d/m/Y", strtotime($inv['dataInvestimento'])) : 'N/A';

                            echo "<tr>";
                            echo "<td>$tipo</td>";
                            echo "<td>R$ $valor</td>";
                            echo "<td>$data</td>";
                            echo "</tr>";
                        }
                    }
                      echo "</table>";
                      ?>
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
                          <p class="fname"><span><?php echo $nome; ?></span></p>
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
                            style="
                              box-shadow: 0px 1px 15px rgba(0, 0, 0); /* add a light gray shadow */
                              transition: all 0.3s;
                              background-color: #f4505083;
                            "
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
                        
                          <div class="option" onclick="window.location.href='dashboard-recarga.php'">
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

                          <div class="option" onclick="window.location.href='dashboard-senha.php'">
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
						  
                          <div class="option" onclick="window.location.href='dashboard-desativar.php'">
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

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
$nome_usuario = htmlspecialchars($dadosUsuario['nomeUsuario'] ?? 'Nome não disponível', ENT_QUOTES, 'UTF-8');
$saldo_usuario = number_format($dadosUsuario['saldoUsuario'] ?? 0, 2, ',', '.');

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

// -------------------------------------------------------------------------------------------------------------------
$mensagem = "";
$method = $_SERVER['REQUEST_METHOD'];

function chamarApi($url, $metodo, $dados = []) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

  if (!empty($dados)) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
  }

  $response = curl_exec($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  return [$httpcode, $response];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $acao = $_POST['acao'] ?? null;
  $tipo_seguro = $_POST['tipo_seguro'] ?? null;
  $valor_cobrir = $_POST['valor_cobrir'] ?? null; // captura o valor a cobrir

  if (!$tipo_seguro) {
      $mensagem = "Tipo de seguro inválido.";
  } elseif (!$acao || !in_array($acao, ['contratar', 'cancelar'])) {
      $mensagem = "Ação inválida.";
  } elseif ($acao === 'contratar' && (!$valor_cobrir || !is_numeric($valor_cobrir))) {
      // opcional: valida se valor_cobrir é numérico e está preenchido só para contratar
      $mensagem = "Valor a cobrir inválido.";
  } else {
      $dados = [
          'emailUsuario' => $email_usuario,
          'tipoSeguro' => $tipo_seguro
      ];

      if ($acao === 'contratar') {
          $dados['valorCobertura'] = (float)$valor_cobrir; // adiciona o valor a cobrir no array
      }

      $url = ($acao === 'contratar')
          ? "http://localhost:8080/seguros/contratar"
          : "http://localhost:8080/seguros/cancelar";

      $metodo = ($acao === 'contratar') ? 'POST' : 'DELETE';

      list($httpcode, $response) = chamarApi($url, $metodo, $dados);

      if ($httpcode === 200 || $httpcode === 201) {
          header("Location: confirmado.html");
          exit;
      } elseif ($httpcode === 404) {
          $mensagem = 'Erro: Usuário não encontrado.';
      } elseif ($httpcode === 409) {
          $mensagem = 'Erro na transferência. Seguro já contratado Código: ' . $httpcode;
      } else {
          $mensagem = 'Erro desconhecido. Código: ' . $httpcode;
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
                      <p><span>Seguros</span></p>
                    </div>
                    <div class="trans-info">
                      <div class="history">
                      <div class="tabs">
                          <button class="tab-button" onclick="showTab('residencial')">Seguro Residencial</button>
                          <button class="tab-button" onclick="showTab('vida')">Seguro de Vida</button>
                          <button class="tab-button" onclick="showTab('automovel')">Seguro de Automóvel</button>
                          <button class="tab-button" onclick="showTab('meusSeguros')">Meus Seguros</button>                        
                      </div>

                      <div id="residencial" class="tab-content active">
                          <label>Seguro Residencial</label>
                          <p>O seguro residencial protege imóveis utilizados para moradia, cobrindo tanto a estrutura física quanto os bens dentro dela.</p>
                      </div>

                      <div id="vida" class="tab-content">
                          <label>Seguro de Vida</label>
                          <p>O seguro de vida garante proteção econômica do segurado e de sua família.</p>
                      </div>

                      <div id="automovel" class="tab-content">
                          <label>Seguro de Automóvel</label>
                          <p>O seguro de automóvel cobre danos ao veículo e protege a vida financeira do proprietário.</p>                          
                      </div>


                      <div id="meusSeguros" class="tab-content">
                        <!-- Tabela de Seguros -->
                       <?php
                      // Suponha que $cliente_id esteja definido

                      // URL da API para pegar investimentos do cliente
                      $url = "http://localhost:8080/seguros/segs?email=$email_usuario";

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
                        echo "<tr><th>Tipo</th><th>Mensalidade</th><th>Cobertura</th><th>Inicio</th><th>Fim</th></tr>";

                        foreach ($investimentos as $inv) {
                            $tipo = htmlspecialchars($inv['tipoSeguro'] ?? 'N/A');
                            $valor = number_format($inv['valorSeguro'] ?? 0, 2, ',', '.');
                            $cobertura = number_format($inv['valorCobertura'] ?? 0, 2, ',', '.');
                            $dataIni = isset($inv['dataInicio']) ? date("d/m/Y", strtotime($inv['dataInicio'])) : 'N/A';
                            $dataFim = isset($inv['dataTermino']) ? date("d/m/Y", strtotime($inv['dataTermino'])) : 'N/A';

                            echo "<tr>";
                            echo "<td>$tipo</td>";
                            echo "<td>R$ $valor</td>";
                            echo "<td>R$ $cobertura</td>";
                            echo "<td>$dataIni</td>";
                            echo "<td>$dataFim</td>";
                            echo "</tr>";
                        }
                       }
                      echo "</table>";
                      ?>
                      <!-- Formulário de Cancelar Seguros -->
                      <section id="cancelar">
                        <div class='container'>
                            <form method="post">
                              <input type="hidden" name="acao" value="cancelar">
                                <label for="tipo_investimento">Cancelar Seguro<br><br>Tipo do Seguro:</label>

                                <select id="tipo_seguro" name="tipo_seguro" required>
                                    <option value="Seguro Residencial">Seguro Residencial</option>
                                    <option value="Seguro de Vida">Seguro de Vida</option>
                                    <option value="Seguro de Automóvel">Seguro de Automóvel</option>
                                </select>
                                <button type="submit">Cancelar Seguro</button>
                                <?php if (!empty($mensagem)): ?>
                                  <div class="mensagem mensagem-feedback <?php echo $sucesso ? 'sucesso' : 'erro'; ?>">
                                    <?php echo htmlspecialchars($mensagem); ?>
                                  </div>
                                <?php endif; ?>
                            </form>
                        </div>
                      </section>

                      </div>

                      <!-- Formulário de Comprar Seguros -->
                      <section id="contratar">
                        <div class='container'>                          
                            <form method="post">
                              <input type="hidden" name="acao" value="contratar">
                                <label for="tipo_seguro">Contratar Seguro<br><br>Tipo do Seguro:</label>
                                
                                <select id="tipo_seguro" name="tipo_seguro">
                                    <option value="Seguro Residencial">Seguro Residencial</option>
                                    <option value="Seguro de Vida">Seguro de Vida</option>
                                    <option value="Seguro de Automóvel">Seguro de Automóvel</option>
                                </select>
                                <label for="money">Valor a Cobrir</label>
                                <input type="text" id="money" name="valor_cobrir" oninput="limitarDecimais(event)" required />
                                
                                <div class="dollor-sign">
                                  <p><span>$</span></p>
                                </div>

                                <button type="submit">Contratar Seguro</button>
                                <?php if (!empty($mensagem)): ?>
                                  <div class="mensagem mensagem-feedback <?php echo $sucesso ? 'sucesso' : 'erro'; ?>">
                                    <?php echo htmlspecialchars($mensagem); ?>
                                  </div>
                                <?php endif; ?>
                            </form>                          
                        </div>
                      </section>                    
                    </div>
                    </div>
                  </div>
                </div>
              </section>
            </div>

            <!--Coluna da Direita-->
            
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
                            <span style="float: left">exp: 2038/07</span>
                            <span style="float: right">cvv: 999</span>
                          </p>
                        </div>
                      </div>
                    </div>

                    <div class="tools-container">
                      <div class="tools-row">
                        <div class="tools-column">

                          <!--Botão do Pix-->
                          <div class="option" onclick="window.location.href='dashboard-pix.php'">
                            <div class="container">
                              <div class="logo">
                                <img src="../resources/files/pics/transfer.svg" alt=""/>
                              </div>
                              <div class="text"><p>Pix</p></div>
                            </div>
                          </div>

                          <!--Botão da Caixinha-->
                          <div class="option" onclick="window.location.href='dashboard-caixinha.php'">
                            <div class="container">
                              <div class="logo">
                                <img src="../resources/files/pics/bill.svg" alt=""/>
                              </div>
                              <div class="text"><p>Caixinha</p></div>
                            </div>
                          </div>

                          <!--Botão de Seguros-->
                          <div class="option"
                            style="
                              box-shadow: 0px 1px 15px rgba(0, 0, 0); /* add a light gray shadow */
                              transition: all 0.3s;
                              background-color: #f4505083;
                            ">
                            <div class="container">
                              <div class="logo">
                                <img src="../resources/files/pics/seguros.svg" alt=""/>
                              </div>
                              <div class="text"><p>Seguros</p></div>
                            </div>
                          </div>

                          <!--Botão de Recarga de Celular-->
                          <div class="option" onclick="window.location.href='dashboard-recarga.php'">
                            <div class="container">
                              <div class="logo">
                                <img src="../resources/files/pics/simcard.svg" alt=""/>
                              </div>
                              <div class="text"><p>Recarga Celular</p></div>
                            </div>
                          </div>
                        </div>
						
                        <div class="tools-column">
                          
                          <!--Botão de Copiar o Número do Cartão-->
                          <div class="option" id="copy-card">
                            <div class="container">
                              <div class="logo">
                                <img src="../resources/files/pics/copy.svg" alt=""/>
                              </div>
                              <div class="text"><p>Copiar Cartão</p></div>
                            </div>
                          </div>

                          <!--Botão de Senha Online-->
                          <div class="option" onclick="window.location.href='dashboard-senha.php'">
                            <div class="container">
                              <div class="logo">
                                <img src="../resources/files/pics/password.svg" alt=""/>
                              </div>
                              <div class="text"><p>Senha Online</p></div>
                            </div>
                          </div>

                          <!--Botão de Desativar Cartão-->
                          <div class="option" onclick="window.location.href='dashboard-desativar.php'">
                            <div class="container">
                              <div class="logo">
                                <img src="../resources/files/pics/disable.svg" alt=""/>
                              </div>
                              <div class="text"><p>Desativar conta</p></div>
                            </div>
                          </div>
						  
                          <!--Botão de Sair da Conta-->
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
      setTimeout(() => {
    const mensagens = document.querySelectorAll('.mensagem-feedback');
    mensagens.forEach(msg => {
      msg.style.transition = 'opacity 0.5s ease';
      msg.style.opacity = '0';

      setTimeout(() => msg.remove(), 500);
    });
  }, 5000);

    function showTab(tabName) {
      // Esconde todas as abas
      const tabs = document.querySelectorAll('.tab-content');
      tabs.forEach(tab => tab.classList.remove('active'));

      // Mostra a aba clicada
      const activeTab = document.getElementById(tabName);
      if (activeTab) {
          activeTab.classList.add('active');
      }

      // Atualiza o tipo de seguro no formulário de contratação
      const contratarSection = document.getElementById('contratar');

      if (contratarSection) {
          const tipoSeguroSelect = contratarSection.querySelector('select[name="tipo_seguro"]');

          if (['residencial', 'vida', 'automovel'].includes(tabName)) {
              contratarSection.style.display = 'block';

              if (tipoSeguroSelect) {
                  switch (tabName) {
                      case 'residencial':
                          tipoSeguroSelect.value = 'Seguro Residencial';
                          break;
                      case 'vida':
                          tipoSeguroSelect.value = 'Seguro de Vida';
                          break;
                      case 'automovel':
                          tipoSeguroSelect.value = 'Seguro de Automóvel';
                          break;
                  }
              }
          } else {
              // Esconde o formulário de contratação em outras abas
              contratarSection.style.display = 'none';
          }
      }
  }

</script>

  </body>
</html>
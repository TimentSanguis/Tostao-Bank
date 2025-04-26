<?php
include 'php/conexao.php';
session_start(); // Inicia a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); // Redireciona para o login se não estiver logado
    exit;
}

// Acessa as informações da sessão
$nome_usuario = htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8');
$saldo_usuario = number_format($_SESSION['saldo'], 2, ',', '.');
$email_usuario = htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8');
$telefone_usuario = $_SESSION['telefone'];
$num_cartao = isset($_SESSION['num_cartao']) ? $_SESSION['num_cartao'] : "Número de cartão não disponível";
$num_formatado = preg_replace('/(\d{4})/', '$1   ', $num_cartao); // Divide em blocos de 4 separados por espaço


// Aqui você pode formatar o telefone, se necessário. Exemplo de formatação simples:
$telefone_formatado = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone_usuario);

if (isset($_SESSION['telefone'])) {
    $telefone_usuario = $_SESSION['telefone'];
    $telefone_formatado = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone_usuario);
} else {
    $telefone_formatado = 'Telefone não disponível';  // Ou algum outro valor padrão
}

 // Acessa as informações da sessão
 $nome_usuario = $_SESSION['usuario'];
 $cliente_id = $_SESSION['id'];
 
  // Consulta o saldo atualizado do usuário
 $sql = "SELECT saldo_usuario FROM usuario WHERE id_usuario = ?";
 $stmt = $conecta_db->prepare($sql);
 $stmt->bind_param("i", $cliente_id);
 $stmt->execute();
 $result = $stmt->get_result();

 if ($result->num_rows > 0) {
     $row = $result->fetch_assoc();
     $saldo_usuario = number_format($row['saldo_usuario'],2,',','.');
 } else {
     echo "Usuário não encontrado.";
     exit;
 }

 $stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
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
                          <p class="fname"><span><?php echo $nome_usuario; ?></span></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="balance-container">
                  <div class="balance-text">
                    <p><span>Seu Saldo:</span></p>
                    <p>
                      <span class="money"
                        ><?php echo $saldo_usuario; ?>
                        <span style="color: green; font-size: 50px"
                          >$</span
                        ></span
                      >
                    </p>
                  </div>
                </div>
                <div class="account-info">
                  <div class="info-text">
                    <p style="margin-bottom: 10px">
                      <span style="font-size: 20px">Dados Conta</span>
                  
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
                          <form action="investimentos/investir.php" method="post">
                              <label for="valor">Valor a Investir:</label>
                              <input type="number" id="valor" name="valor" step="0.01" min="0" required>
                              
                              <label for="tipo_investimento">Tipo de Investimento:</label>

									<select id="tipo_investimento" name="tipo_investimento" required>
										<option value="Reserva de Emergência">Reserva de Emergência</option>
										<option value="Fazer uma viagem">Fazer uma viagem</option>
										<option value="Meu sonho de consumo">Meu sonho de consumo</option>
									</select>

                              <button type="submit" onclick="window.location.href='confirmado.html'">Investir</button>
                          </form>
						</div>
                      </section>

                      <!-- Formulário de Retirada -->
                      <section id="retirar">
						<div class='container'>
                          <form action="investimentos/retirar.php" method="post">
                              <label for="valor_retirar">Valor a Retirar:</label>
                              <input type="number" id="valor_retirar" name="valor_retirar" step="0.01" min="0" required>

                              <label for="tipo_investimento">Tipo de Investimento:</label>
                              <select id="tipo_investimento" name="tipo_investimento" required>
                                  <option value="Reserva de Emergência">Reserva de Emergência</option>
                                  <option value="Fazer uma viagem">Fazer uma viagem</option>
                                  <option value="Meu sonho de consumo">Meu sonho de consumo</option>
                              </select>
                              <button type="submit" onclick="window.location.href='confirmado.html'">Retirar</button>
							<label for='valor_retirar'> Investimentos Realizados</label>
						  </form>
						</div>
                      </section>  


                                        <!-- Tabela de Investimentos -->
                          <?php
                          // Consultar investimentos do cliente
                          $sql = "SELECT valor_investimento, tipo_investimento, data_investimento FROM investimento WHERE cliente_id = ?";
                          $stmt = $conecta_db->prepare($sql);

                          if ($stmt === false) {
                              die("Erro na preparação da consulta: " . $conecta_db->error);
                          }

                          $stmt->bind_param("i", $cliente_id);
                          $stmt->execute();
                          $result = $stmt->get_result();

                          if ($result->num_rows > 0) {
                              echo "<table border='1'>";
                              echo "<tr><th>Tipo</th><th>Valor</th><th>Data</th></tr>";

                              while ($row = $result->fetch_assoc()) {
                                  echo "<tr>";
                                  echo "<td>" . htmlspecialchars($row['tipo_investimento']) . "</td>";
                                  echo "<td>R$ " . number_format($row['valor_investimento'], 2, ',', '.') . "</td>";
                                  echo "<td>" . date("d/m/Y", strtotime($row['data_investimento'])) . "</td>";
                                  echo "</tr>";
                              }

                              echo "</table>";
                          } else {
                              echo "Nenhum investimento encontrado para este cliente.";
                          }

                          $stmt->close();
                          $conecta_db->close();
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
                          <p class="fname"><span><?php echo $nome_usuario; ?></span></p>
                        </div>
                        <div class="card-num" id="card-number">
                          <p class="fname"><span><?php echo trim($num_formatado); ?></span></p>
                        </div>
                        <div class="card-exp-cvv">
                          <p>
                            <span style="float: left">exp: 2024/07</span
                            ><span style="float: right">cvv2: 999</span>
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
						  
                          <div class="option" onclick="window.location.href='index.html'">
                            <div class="container">
                              <div class="logo">
                                <img
                                  src="../resources/files/pics/more.svg"
                                  alt=""
                                />
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

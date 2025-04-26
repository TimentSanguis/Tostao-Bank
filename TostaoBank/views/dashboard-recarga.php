<?php
include 'php/conexao.php';

session_start(); // Inicia a sessão

date_default_timezone_set('America/Sao_Paulo');

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
                    <p><span>Seu Saldo:</span></p>
                    <p>
						<span id="saldoValue" class="money"><?php echo $saldo_usuario; ?> </span>
						<span id="saldoValue" class="money" style="color: green; font-size: 50px">$</span>
                    </p>
                  </div>
                </div>
                <div class="account-info">
                  <div class="info-text">
                    <p style="margin-bottom: 10px">
                      <span style="font-size: 20px">Dados Conta</span>
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
                        <form id="pixForm">
							<label for="amount">Valor</label>
							<input type="text" id="charge" name="txt_valor" required />
							<label for="Phone-num">Número de celular</label>
							<input type="number" id="Phone-num" name="txt_numero" required />
							<button type="submit">Recarga</button>
						</form>
						<div id="mensagem"></div>
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
                          <div class="option" onclick="window.location.href='index.html'">
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
        const toggleSaldoButton = document.getElementById('toggleSaldo');
        const saldoDisplay = document.getElementById('saldoDisplay');
        const saldoValue = document.getElementById('saldoValue');

        document.getElementById('pixForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Impede o envio normal do formulário

            const formData = new FormData(this);
            fetch('recarga_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Atualiza a mensagem na página
                document.getElementById('mensagem').innerHTML = data.mensagem;
				window.location.href = 'confirmado.html';

                // Atualiza o saldo na tela se a transferência for bem-sucedida
                if (data.saldo) {
                    saldoValue.innerHTML = data.saldo;
                    saldoDisplay.innerHTML = `Seu saldo: R$ ${data.saldo}`;	
                }
            })
            .catch(error => {
                console.error('Erro:', error);
				});
			});

			// Função para atualizar o saldo via AJAX
			function atualizarSaldo() {
				fetch('php/obter_saldo.php')
					.then(response => {
						if (!response.ok) {
							throw new Error('Network response was not ok');
						}
						return response.json();
					})
					.then(data => {
						if (data.saldo) {
							saldoValue.innerHTML = data.saldo;
							saldoDisplay.innerHTML = `Seu saldo: R$ ${data.saldo}`;
						} else if (data.erro) {
							console.error(data.erro);
						}
					})
					.catch(error => {
						console.error('Erro:', error);
					});
			}

			// Chama a função para atualizar o saldo periodicamente (a cada 10 segundos)
			setInterval(atualizarSaldo, 10000);
		});
		</script>
  </body>
</html>

<?php
session_start(); // Inicia a sessão
include('php/conexao.php');

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

// Formatação do telefone, se necessário
$telefone_formatado = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone_usuario);

// Buscar transações do usuário logado
$query = "SELECT * FROM historico WHERE email_recebe_Transferencia = '$email_usuario' OR email_Transferencia = '$email_usuario' ORDER BY data_Transferencia DESC";
$result = mysqli_query($conecta_db, $query);

// Verificar se há transações
$transacoes = mysqli_num_rows($result) > 0 ? mysqli_fetch_all($result, MYSQLI_ASSOC) : null;

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
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../resources/css/dashboard-style.css" />
    <link rel="shortcut icon" type="image/x-icon" href="../resources/files/pics/logo_tostao_bank.png" />
    <title>Dashboard</title>
</head>
<body>
    <header></header>
    <main>
        <section>
            <div class="container">
                <div class="ham-menu">
                    <img src="../resources/files/pics/hamburgur.svg" id="btn" alt="" onclick="myFunction()" />
                </div>
                <div class="row">
                <!--Coluna da Esquerda-->
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
                                        <span class="money"><?php echo $saldo_usuario; ?>
                                            <span style="color: green; font-size: 50px">$</span>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="account-info">
                                <div class="info-text">
                                    <p style="margin-bottom: 10px"><span style="font-size: 20px">Dados Conta</span></p>
                                    <p><span>Email: <?php echo $email_usuario; ?></span></p>
                                    <p><span>Celular: <?php echo $telefone_formatado; ?></span></p>
                                </div>
                            </div>
                        </section>
                    </div>
                    <!--Coluna do Meio-->
                    <div class="column-middle">
    <section>
        <div class="transaction">
            <div class="trans-container">
                <div class="title">
                    <p><span>Histórico</span></p>
                </div>
                <div class="trans-info">
                    <div class="history">
                        <?php if ($transacoes): ?>
                            <?php foreach ($transacoes as $transacao): ?>
                                <?php if ($transacao['email_recebe_Transferencia'] == $email_usuario): ?>
                                    <!-- Transação recebida -->
                                    <div class="element">
                                        <p>
                                            <span class="reason"><?php echo $transacao['tipo_Transferencia']; ?></span>
                                            <span class="flash">
                                                <?php if ($transacao['tipo_Transferencia'] == 'Pix'): ?>
                                                    <img src="../resources/files/pics/down.svg" alt="Recebido" />
                                                <?php elseif (stripos($transacao['desc_transferencia'], 'Retirada') !== false): ?>
                                                    <img src="../resources/files/pics/down.svg" alt="Retirada de Investimentos" />
                                                <?php elseif ($transacao['tipo_Transferencia'] == 'Recarga'): ?>
                                                    <img src="../resources/files/pics/up.svg" alt="Recarga" />
                                                <?php endif; ?>
                                            </span>
                                        </p>
                                        <?php if ($transacao['tipo_Transferencia'] == 'Pix'): ?>
                                            <p>
                                                <span class="details">Enviado por: <?php echo $transacao['email_Transferencia']; ?></span>
                                            </p>
                                        <?php elseif (stripos($transacao['desc_transferencia'], 'Retirada') !== false): ?>
                                            <p>
                                                <span class="details">Retirada de Investimentos</span>
                                            </p>
                                        <?php elseif ($transacao['tipo_Transferencia'] == 'Recarga'): ?>
                                            <p>
                                                <span class="details">Recarga realizada</span>
                                            </p>
                                        <?php endif; ?>
                                        <p>
                                            <span class="date"><?php echo date("d/m/Y", strtotime($transacao['data_Transferencia'])); ?></span>
                                            <span class="money">
                                                <?php echo number_format($transacao['valor_Transferencia'], 2, ',', '.'); ?>
                                                <?php
                                                // Mudando a cor do $ para verde nas transações de Retirada ou Pix recebido
                                                if ($transacao['tipo_Transferencia'] == 'Pix' || stripos($transacao['desc_transferencia'], 'Retirada') !== false) {
                                                    echo '<span style="color: green;">R$</span>';
                                                } else {
                                                    echo '<span style="color: red;">R$</span>';
                                                }
                                                ?>
                                            </span>
                                        </p>
                                        <hr />
                                    </div>
                                <?php elseif ($transacao['email_Transferencia'] == $email_usuario): ?>
                                    <!-- Transação enviada -->
                                    <div class="element">
                                        <p>
                                            <span class="reason"><?php echo $transacao['tipo_Transferencia']; ?></span>
                                            <span class="flash">
                                                <?php if ($transacao['tipo_Transferencia'] == 'Pix'): ?>
                                                    <img src="../resources/files/pics/up.svg" alt="Enviado" />
                                                <?php elseif (stripos($transacao['desc_transferencia'], 'Investido') !== false): ?>
                                                    <img src="../resources/files/pics/up.svg" alt="Investido em Investimentos" />
                                                <?php elseif (stripos($transacao['desc_transferencia'], 'Retirada') !== false): ?>
                                                    <img src="../resources/files/pics/down.svg" alt="Retirada de Investimentos" />
                                                <?php elseif ($transacao['tipo_Transferencia'] == 'Recarga'): ?>
                                                    <img src="../resources/files/pics/up.svg" alt="Recarga" />
                                                <?php endif; ?>
                                            </span>
                                        </p>
                                        <?php if ($transacao['tipo_Transferencia'] == 'Pix'): ?>
                                            <p>
                                                <span class="details">Enviado para: <?php echo $transacao['email_recebe_Transferencia']; ?></span>
                                            </p>
                                        <?php elseif (stripos($transacao['desc_transferencia'], 'Investido') !== false): ?>
                                            <p>
                                                <span class="details">Investido em Investimentos</span>
                                            </p>
                                        <?php elseif (stripos($transacao['desc_transferencia'], 'Retirada') !== false): ?>
                                            <p>
                                                <span class="details">Retirada de Investimentos</span>
                                            </p>
                                        <?php elseif ($transacao['tipo_Transferencia'] == 'Recarga'): ?>
                                            <p>
                                                <span class="details">Recarga realizada</span>
                                            </p>
                                        <?php endif; ?>
                                        <p>
                                            <span class="date"><?php echo date("d/m/Y", strtotime($transacao['data_Transferencia'])); ?></span>
                                            <span class="money">
                                                <?php echo number_format($transacao['valor_Transferencia'], 2, ',', '.'); ?>
                                                <span style="color: red;">R$</span>
                                            </span>
                                        </p>
                                        <hr />
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Nenhuma transação encontrada.</p>
                        <?php endif; ?>
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

  </body>
</html>
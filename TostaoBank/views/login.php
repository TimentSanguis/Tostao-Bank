<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../resources/css/login.css">
    <link rel="icon" type="image/x-icon" href="../resources/files/pics/logo_tostao_bank.png"/>
</head>
<body>
	<div class='header' id='header'>
		<button onclick='toggleSideBar()' class='btn_icon_header'>
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
				<path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
			</svg>
		</button>
	  <textoG>Tostão Bank</textoG>
		<div onclick='toggleSideBar()' class='headerNav' id='headerNav'>
			<button class='btn_icon_header'>
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
					<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
				</svg>
			</button>
		  <a href='index.html'>Inicio</a>
		  <a href='login.php' class='ativo'>Login</a>
		  <a href='cadastro.php'>Criar conta</a>
		</div>
	</div>
	<br><br><br><br><br><br><br><br>
	<div class='centro'>
		<form method="POST" action="login.php">
			<div id="campos_login">
				<h1>Login</h1>
					<input type="text" name="txt_usuario" id="usuario">
				<br><br>
				<h1>Senha</h1>
					<input type="password" name="txt_senha" id="senha">
			</div><br>
			
			<button type="button" id="toggleSenha" onclick="togglePasswordVisibility()">Revelar</button>
			<br><br>
			<button type="submit">Login</button>
			<br><br>
			
			<a class="jumento" href="cadastro.php">Não tem uma conta? Faça seu Cadastro por aqui!</a>
		</form>
	</div> <br><br>
    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("senha");
            var passwordToggle = document.getElementById("toggleSenha");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordToggle.textContent = "Ocultar";
            } else {
                passwordInput.type = "password";
                passwordToggle.textContent = "Revelar";
            }
        }
    </script>
</body>
<script>
		var header = document.getElementById('header');
		var nav = document.getElementById('headerNav');
		var content = document.getElementById('content');
		var showSideBar = false;
		
		function toggleSideBar(){
			showSideBar = !showSideBar;
			if(showSideBar){
				headerNav.style.marginLeft = '-10vw';
				headerNav.style.animationName = 'showSideBar';
				content.style.filter = 'blur(2px)';

			}else {
				headerNav.style.marginLeft = '-100vw';
				headerNav.style.animationName = '';
				content.style.filter = '';

			}
		}
		
		function closeSideBar(){
			if(showSideBar){
				toggleSideBar();
			}
		}
		window.addEventListener('resize', function(event) {
			if(window.innerWidth > 700 && showSideBar){
				toggleSideBar();
			}
		})
	</script>
</html>

<?php
session_start();
include 'php/conexao.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['txt_usuario'];
    $senha = $_POST['txt_senha'];

   

    // Preparar a consulta
    $email_usuario = $conecta_db->real_escape_string($email);
    $stmt = $conecta_db->query("SELECT * FROM usuario WHERE email_usuario = '$email_usuario'");

    if (!$stmt) {
        die("Erro na consulta: " . $conecta_db->error);
    }

    if ($stmt->num_rows === 1) {
        $usuario = $stmt->fetch_assoc();
        var_dump($usuario);
        // Verificar a senha
        if ($senha === $usuario['senha_usuario']) {
            $_SESSION['usuario'] = $usuario['nome_usuario'];
            $_SESSION['email'] = $usuario['email_usuario']; 
            $_SESSION['saldo'] = $usuario['saldo_usuario']; 
			$_SESSION['telefone'] = $usuario['telefone_usuario']; 
            $_SESSION['id'] = $usuario['id_usuario']; 
			$_SESSION['num_cartao'] = $usuario['num_cartao'];
            header("Location: dashboard.php");
            exit;
        } else {    
            echo "<h1><center>Usuário ou senha incorretos. Tente novamente.</center></h1>";
        }
    } else {
        echo "<h1><center>Usuário ou senha incorretos. Tente novamente.</center></h1>";
    }
}
?>

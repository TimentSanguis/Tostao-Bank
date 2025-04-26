<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="../resources/files/pics/logo_tostao_bank.png"/>
    <link rel="stylesheet" href="../resources/css/cadastro.css">
    <title>Cadastro</title>
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
		  <a href='login.php'>Login</a>
		  <a href='cadastro.php' class='ativo'>Criar conta</a>
		</div>
	</div>
	<br><br>
	<div class='centro'>
	    <div class="formularios-container">
			<form class="receba" name="form1" method="POST" onsubmit="return validarFormulario();">
				<div id="campos_cadastro">
					<h1>Nome Completo</h1>
						<input type="text" name="txt_usuario" id="usuario" maxlength='50' required onkeypress="focusOnEnter(event, 'email')">
					<br><br>
					<h1>Email</h1>
						<input type="text" name="txt_email" id="email" maxlength='50' required onkeypress="focusOnEnter(event, 'senha')">
					<br><br>
					<h1>Senha</h1>
						<input type="password" name="txt_senha" id="senha" maxlength='50' required onkeypress="handleEnter(event)">
						<br><br>
					<h1>Data Nascimento</h1>
						<input type="date" name="txt_nascimento" id="nascimento" required onkeypress="focusOnEnter(event, 'email')">					
				</div>
				<div id="campos_cadastro2">
					<h1>CPF</h1>
						<input type="text" name="txt_cpf" id="cpf" maxlength='11' required>
					<br><br>
					<h1>Número Telefone</h1>
						<input type="text" name="txt_telefone" id="telefone" maxlength='11' required>
					<br><br>
					<h1>Endereço</h1>
						<input type="text" name="txt_endereco" id="endereco" maxlength='80'required>
						<br><br>
					<h1>Renda</h1>
						<input type="text" name="txt_renda" id="renda" maxlength='20'required>
						<br><br>
				</div>
				</div>	
				
				<div class='botoes-centralizados'>
					<button type="button" id="toggleSenha" onclick="togglePasswordVisibility()">Revelar</button>
					<br><br>
					<button type="submit" id="cadastrar" disabled>Cadastrar</button>
					<br><br>
					<a class="jumento" href="login.php">Já tem uma conta? Faça Login por Aqui!</a>
					<br><br>
				</div>
			</form>
	</div> <br><br>
	
<script>
    function validarFormulario() {
        var usuario = document.getElementById("usuario").value;
        var email = document.getElementById("email").value;
        var senha = document.getElementById("senha").value;
		var telefone = document.getElementById("telefone").value;
		
        if (usuario === "") {
            alert("Por favor, preencha o campo do Nome.");
            document.getElementById("usuario").focus();
            return false;
        } else if (!email.includes("@gmail.com")) {
            alert("Por favor, insira um endereço de e-mail do Gmail.");
            document.getElementById("email").focus();
            return false;
        } else if (senha === "") {
            alert("Por favor, preencha o campo de senha.");
            document.getElementById("senha").focus();
            return false;
        }
        
        return true; // Permite o envio do formulário
    }

    // Habilitar o botão de cadastro quando todos os campos estiverem preenchidos
    document.querySelectorAll('#campos_cadastro input').forEach(input => {
        input.addEventListener('input', function() {
            const usuario = document.getElementById("usuario").value;
            const email = document.getElementById("email").value;
            const senha = document.getElementById("senha").value;

            const cadastrarButton = document.getElementById("cadastrar");
            cadastrarButton.disabled = !(usuario && email.includes("@gmail.com") && senha);
        });
    });

    function focusOnEnter(event, nextFieldId) {
        if (event.key === "Enter") {
            document.getElementById(nextFieldId).focus();
        }
    }

    function handleEnter(event) {
        if (event.key === "Enter") {
            if (validarFormulario()) {
                document.forms['form1'].submit();
            } else {
                event.preventDefault(); // Impede o envio do formulário se não for válido
            }
        }
    }

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

</body>
</html>
<?php
include 'php/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['txt_usuario']);
    $email = trim($_POST['txt_email']);
    $senha = trim($_POST['txt_senha']);
	$telefone = trim($_POST['txt_telefone']);
    $saldo = 0;

    // Escape de entradas para evitar SQL Injection
    $usuario = $conecta_db->real_escape_string($usuario);
    $email = $conecta_db->real_escape_string($email);
    $senha = $conecta_db->real_escape_string($senha);
    
    // Consulta para verificar se o usuário já existe
    $sql = "SELECT * FROM usuario WHERE email_usuario = '$email'";
    $result = $conecta_db->query($sql);

    if ($result->num_rows > 0) {
        echo "<center><h1>Usuário já Existe!<h1><br></center>";
    } else {
        // Insira um novo usuário
        $sql = "INSERT INTO usuario (nome_usuario, telefone_usuario,email_usuario, senha_usuario, saldo_usuario) 
                VALUES ('$usuario', '$telefone','$email', '$senha', '$saldo')";
        
        if ($conecta_db->query($sql) === TRUE) {
            echo "<center><h1>CONTA CRIADA COM SUCESSO!<h1><br></center>";
        } else {
            echo "Erro ao criar conta: " . $conecta_db->error;
        }
    }
}

// Fechar a conexão
$conecta_db->close();
?>
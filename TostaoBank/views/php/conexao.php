<?php
$servidor = "127.0.0.1";
$usuario = "root";
$senha = "";
$banco = "tostao";

// Criar conexão
$conecta_db = new mysqli($servidor, $usuario, $senha, $banco);

// Verificar conexão
if ($conecta_db->connect_error) {
    die("Erro ao conectar: " . $conecta_db->connect_error);
}

//echo "Conexão realizada com sucesso!";

// Fechar conexão quando não for mais necessária
//$conecta_db->close();
?>
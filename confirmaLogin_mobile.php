<?php
// Inclui a conexão com o banco de dados
include "Bd.php";

// Inicia a sessão
session_start();

// 1. Pega os dados do formulário
$NomeUsuario = $_POST['Login'];
$SenhaDigitada = $_POST['Senha'];

// 2. Busca o utilizador pelo NOME
$sql = "SELECT * FROM usuarios WHERE nome = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $NomeUsuario);
$stmt->execute();
$resultado = $stmt->get_result();

// 3. Verifica se o utilizador foi encontrado e valida a senha
if ($resultado->num_rows === 1) {
    $usuario = $resultado->fetch_assoc();
    if ($usuario['senha'] === $SenhaDigitada) {
        // Guarda as informações na sessão
        $_SESSION['Nome'] = $usuario['nome'];
        $_SESSION['Senha'] = $usuario['senha'];
        $_SESSION['id'] = $usuario['id']; 
        // Redireciona para o splasha
        header('Location: garcom_mesas.php');
        exit();
    } else {
        // Senha incorreta
        header('Location: login.php?erro=1');
        exit();
    }
} else {
    // Usuário não encontrado
    header('Location: login.php?erro=1');
    exit();
}

$stmt->close();
$con->close();
?>
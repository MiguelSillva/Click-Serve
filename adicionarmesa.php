<?php
session_start();
include 'Bd.php'; // Seu estilo de conexão

// 1. Verificação de sessão (Seu estilo)
if ((!isset($_SESSION['Nome']) == true ) and (!isset($_SESSION['Senha']) == true)) {
    unset( $_SESSION['Nome']);
    unset($_SESSION['Senha']);
    header('Location: login.php');
    exit;
}

// 2. Lógica para adicionar a mesa
try {

    // Agora, inserir a nova mesa (usando prepared statement, como em ExcluirProduto.php)
    $sql_insert = "INSERT INTO mesa ( status_mesa ) VALUES (1)";
    $stmt = $con->prepare($sql_insert);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // 3. Redirecionar de volta (Seu estilo)
        header('Location: index.php?adicao=sucesso');
    } else {
        header('Location: index.php?adicao=erro');
    }

    $stmt->close();
    
} catch (Exception $e) {
    // Tratar erro, se houver
    header('Location: index.php?adicao=erro');
}

$con->close(); //
?>
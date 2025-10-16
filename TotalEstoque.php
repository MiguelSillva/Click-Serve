<?php
    session_start();
    include 'Bd.php';

    $sql = "SELECT SUM(COALESCE(quantidade_em_estoque, 0)) as total1 FROM produtos;";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $dados = $resultado->fetch_assoc();
    $total = $dados['total1'] ?? 0;
    $_SESSION['total_estoque'] = (int)$total;
$stmt->close();
$con->close();

$Estoquetotal = (int)($_SESSION['total_estoque'] ?? 0);

$Logado = $_SESSION['Nome'] ?? 'Usuário';

?>
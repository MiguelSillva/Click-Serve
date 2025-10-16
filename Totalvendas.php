<?php
    session_start();
    include 'Bd.php';

    $id = (int) $_SESSION['id'];

    $sql = "SELECT COUNT(*) as total FROM vendas WHERE usuario_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id); 
    $stmt->execute();
    $resultado = $stmt->get_result();

$dados = $resultado->fetch_assoc();
$_SESSION['TotalVendas'] = $dados['total'];
(int)$teste = $_SESSION['TotalVendas'];
echo "$teste";

$stmt->close();
$con->close();

?>
<?php
session_start();
include 'Bd.php';

if (isset($_GET['id'])) {
    $idProduto = (int)$_GET['id'];

  
    if ($idProduto > 0) {
      
        $sql = "DELETE FROM produtos WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $idProduto);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            
            header('Location: Estoque.php?exclusao=sucesso');
        } else {
            
            header('Location: Estoque.php?exclusao=erro');
        }

        $stmt->close();
    } else {
    
        header('Location: Estoque.php?exclusao=erro');
    }
} else {
    
    header('Location: Estoque.php?exclusao=erro');
}

$con->close();
?>

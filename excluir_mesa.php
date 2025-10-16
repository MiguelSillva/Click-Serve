<?php
// Inclui nosso guardião de sessão e a conexão com o BD
include 'Bd.php';

// Verifica se o ID da mesa foi passado e é válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    
    $id_mesa = (int)$_GET['id'];

    // Prepara a query SQL para evitar SQL Injection
    $sql = "DELETE FROM mesa WHERE id_mesa = ?";
    $stmt = $con->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id_mesa);
        
        if ($stmt->execute()) {
            header('Location: index.php?exclusao=sucesso');
        } else {
            header('Location: index.php?exclusao=erro');
        }
        $stmt->close();
    } else {
        header('Location: index.php?exclusao=erro');
    }

} else {
    header('Location: index.php?exclusao=erro');
}

$con->close();
exit();
?>
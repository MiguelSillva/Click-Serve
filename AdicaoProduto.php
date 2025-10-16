<?php 
     include "Bd.php";
     session_start();
     $NomeProduto = mysqli_real_escape_string($con, $_POST['nomeProduto']);
     $Quantidade =  mysqli_real_escape_string($con , $_POST['quantidade']);
     $Preco = mysqli_real_escape_string($con , $_POST['preco']);
     $Categoria = mysqli_real_escape_string($con , $_POST['categoria']);
     $usuarioID = $_SESSION['id'];
     $sql = "INSERT INTO produtos(nomeProduto, preco , quantidade_em_estoque , categoria_id) VALUES ( '$NomeProduto' , $Preco , $Quantidade , $Categoria );";
    
     if ($con->query($sql) === TRUE) {
        header('Location: AdicionarEstoque.php');
    } else {
        echo "Erro: " . $con->error;
    }


    
?>
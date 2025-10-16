<?php 
    $local="localhost";
    $usuario = "root";
    $senha = "";
    $nome = "clickserve";

    $con = new mysqli($local , $usuario , $senha , $nome );

    if ($con->connect_error) {
        die ("Erro na Conexão: " . $con->connect_error);
    }
?>
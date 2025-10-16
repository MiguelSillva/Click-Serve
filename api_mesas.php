<?php
session_start();
include 'Bd.php';

// Proteção: apenas usuários logados podem acessar os dados
if (!isset($_SESSION['Nome']) || !isset($_SESSION['Senha'])) {
    http_response_code(403); // Proibido
    echo json_encode(['erro' => 'Acesso negado']);
    exit();
}

try {
    // Busca todas as mesas, ordenadas pelo ID
    $sql = "SELECT id_mesa, status_mesa, garcom_mesa, nome_cliente FROM mesa ORDER BY id_mesa ASC";
    $resultado = $con->query($sql);

    $mesas = [];
    if ($resultado) {
        while ($row = $resultado->fetch_assoc()) {
            $mesas[] = $row;
        }
    }

    // Define o cabeçalho da resposta como JSON
    header('Content-Type: application/json');
    // Imprime o array de mesas convertido para o formato JSON
    echo json_encode($mesas);

} catch (Exception $e) {
    http_response_code(500); // Erro interno do servidor
    echo json_encode(['erro' => 'Erro ao consultar o banco de dados.']);
}

$con->close();
?>
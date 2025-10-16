<?php
session_start();
include 'Bd.php';

// Proteção: apenas usuários logados podem registrar vendas
if (!isset($_SESSION['Nome']) || !isset($_SESSION['id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acesso negado.']);
    exit();
}

// Pega os dados do carrinho enviados pelo JavaScript
$cart = json_decode(file_get_contents('php://input'), true);

if (empty($cart)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Carrinho vazio.']);
    exit();
}

$usuario_id = (int)$_SESSION['id'];
$con->begin_transaction();

try {
    $valor_total_venda = 0;
    
    // Prepara as queries para reutilização dentro do loop
    $stmt_produto = $con->prepare("SELECT preco FROM produtos WHERE id = ?");
    $stmt_update_estoque = $con->prepare("UPDATE produtos SET quantidade_em_estoque = quantidade_em_estoque - ? WHERE id = ?");

    // Recalcula o total no backend para segurança e verifica o estoque
    foreach ($cart as $item) {
        $id = (int)$item['id'];
        $quantidade = (int)$item['quantity'];

        $stmt_produto->bind_param('i', $id);
        $stmt_produto->execute();
        $produto_db = $stmt_produto->get_result()->fetch_assoc();

        if (!$produto_db) {
            throw new Exception("Produto com ID {$id} não encontrado.");
        }
        
        $valor_total_venda += $produto_db['preco'] * $quantidade;
    }

    // Insere a venda na tabela 'vendas'
    $sql_venda = "INSERT INTO vendas (data, valor, usuario_id) VALUES (NOW(), ?, ?)";
    $stmt_venda = $con->prepare($sql_venda);
    $stmt_venda->bind_param("di", $valor_total_venda, $usuario_id);
    if (!$stmt_venda->execute()) {
        throw new Exception("Não foi possível registrar a venda.");
    }

    // Dá baixa no estoque para cada produto vendido
    foreach ($cart as $item) {
        $id = (int)$item['id'];
        $quantidade = (int)$item['quantity'];
        
        $stmt_update_estoque->bind_param('ii', $quantidade, $id);
        if (!$stmt_update_estoque->execute()) {
            throw new Exception("Não foi possível atualizar o estoque do produto ID {$id}.");
        }
    }

    $con->commit();
    echo json_encode(['status' => 'success', 'message' => 'Venda finalizada e estoque atualizado!']);

} catch (Exception $e) {
    $con->rollback();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$con->close();
?>
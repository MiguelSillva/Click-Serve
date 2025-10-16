<?php
session_start();
include 'Bd.php';

// 1. VERIFICAÇÕES DE SEGURANÇA
if (!isset($_SESSION['Nome']) || !isset($_SESSION['Senha'])) {
    header('Location: login.php');
    exit();
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?finalizacao=erro_id'); // Volta para o index em caso de erro grave
    exit();
}

$id_mesa = (int)$_GET['id'];
$usuario_id = (int)$_SESSION['id'];

// --- LÓGICA DE REDIRECIONAMENTO INTELIGENTE ---
// Define 'index.php' como o padrão.
$return_page = 'index.php'; 
// Se a URL nos disser para voltar para 'garcom_mesas.php', nós obedecemos.
if (isset($_GET['return_url']) && $_GET['return_url'] === 'garcom_mesas.php') {
    $return_page = 'garcom_mesas.php';
}

// 2. TRANSAÇÃO NO BANCO DE DADOS (lógica continua a mesma)
$con->begin_transaction();
try {
    // Calcula o valor total da venda
    $sql_total = "SELECT SUM(pp.quantidade * p.preco) as valor_total FROM pedido_produtos pp JOIN produtos p ON pp.id_produto = p.id WHERE pp.id_mesa = ?";
    $stmt_total = $con->prepare($sql_total);
    $stmt_total->bind_param("i", $id_mesa);
    $stmt_total->execute();
    $valor_total_venda = $stmt_total->get_result()->fetch_assoc()['valor_total'] ?? 0;

    // Registra a venda se houver valor
    if ($valor_total_venda > 0) {
        $sql_venda = "INSERT INTO vendas (data, valor, usuario_id) VALUES (NOW(), ?, ?)";
        $stmt_venda = $con->prepare($sql_venda);
        $stmt_venda->bind_param("di", $valor_total_venda, $usuario_id);
        $stmt_venda->execute();
    }

    // Limpa os itens do pedido da mesa
    $sql_limpar_itens = "DELETE FROM pedido_produtos WHERE id_mesa = ?";
    $stmt_limpar_itens = $con->prepare($sql_limpar_itens);
    $stmt_limpar_itens->bind_param("i", $id_mesa);
    $stmt_limpar_itens->execute();
    
    // Libera a mesa, atualizando seu status
    $sql_mesa = "UPDATE mesa SET status_mesa = 1, garcom_mesa = NULL, nome_cliente = NULL, n_pedido = NULL WHERE id_mesa = ?";
    $stmt_mesa = $con->prepare($sql_mesa);
    $stmt_mesa->bind_param("i", $id_mesa);
    $stmt_mesa->execute();

    $con->commit();
    // --- USA A VARIÁVEL $return_page PARA REDIRECIONAR CORRETAMENTE ---
    header("Location: {$return_page}?finalizacao=sucesso");
    exit();

} catch (Exception $e) {
    $con->rollback();
    // --- USA A VARIÁVEL $return_page PARA REDIRECIONAR CORRETAMENTE ---
    header("Location: {$return_page}?finalizacao=erro");
    exit();
}
?>
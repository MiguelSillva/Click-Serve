<?php
// Habilita a exibição de todos os erros na tela
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'Bd.php'; 

echo "<h1>Iniciando Depuração...</h1>";

// 1. VERIFICAÇÕES DE SEGURANÇA E DADOS DE ENTRADA
echo "<h2>Passo 1: Verificando Sessão e Parâmetros</h2>";
echo "Dados da Sessão (SESSION): <pre>" . print_r($_SESSION, true) . "</pre>";
echo "Dados da URL (GET): <pre>" . print_r($_GET, true) . "</pre>";

// Garante que o usuário está logado
if (!isset($_SESSION['Nome']) || !isset($_SESSION['Senha'])) {
    die("<strong>ERRO FATAL:</strong> Usuário não está logado. A sessão 'Nome' ou 'Senha' não existe.");
}
echo "<p style='color:green;'>OK - Usuário está logado.</p>";

// Garante que o ID do usuário existe na sessão (MUITO IMPORTANTE)
if (!isset($_SESSION['id']) || !is_numeric($_SESSION['id'])) {
    die("<strong>ERRO FATAL:</strong> ID do usuário não encontrado na sessão. Verifique se a variável `\$_SESSION['id']` é criada corretamente no seu script de login.");
}
echo "<p style='color:green;'>OK - ID do usuário na sessão encontrado (ID: " . $_SESSION['id'] . ").</p>";


// Garante que um ID de mesa foi passado e que é um número
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<strong>ERRO FATAL:</strong> ID da mesa não foi encontrado na URL ou não é um número. Verifique o link no `index.php`.");
}
echo "<p style='color:green;'>OK - ID da mesa na URL encontrado (ID: " . $_GET['id'] . ").</p>";


$id_mesa = (int)$_GET['id'];
$usuario_id = (int)$_SESSION['id'];

// 2. INICIA A TRANSAÇÃO NO BANCO DE DADOS
echo "<h2>Passo 2: Processando Transação no Banco de Dados</h2>";
$con->begin_transaction();
echo "<p>Iniciando transação...</p>";

try {
    // ETAPA A: CALCULAR O VALOR TOTAL DA VENDA
    echo "<p><strong>Etapa A:</strong> Calculando valor total da venda...</p>";
    $sql_total = "
        SELECT SUM(pp.quantidade * p.preco) as valor_total
        FROM pedido_produtos pp
        JOIN produtos p ON pp.id_produto = p.id
        WHERE pp.id_mesa = ?
    ";
    
    $stmt_total = $con->prepare($sql_total);
    if (!$stmt_total) { die("<strong>ERRO na preparação da Query A:</strong> " . $con->error); }
    $stmt_total->bind_param("i", $id_mesa);
    $stmt_total->execute();
    $resultado_total = $stmt_total->get_result();
    $dados_venda = $resultado_total->fetch_assoc();
    $valor_total_venda = $dados_venda['valor_total'] ?? 0;
    echo "<p style='color:green;'>OK - Valor total calculado: <strong>R$ " . number_format($valor_total_venda, 2, ',', '.') . "</strong></p>";

    // Se houver um valor a ser registrado, insere na tabela de vendas
    if ($valor_total_venda > 0) {
        // ETAPA B: REGISTRAR A VENDA NA TABELA `vendas`
        echo "<p><strong>Etapa B:</strong> Registrando na tabela 'vendas'...</p>";
        $sql_venda = "INSERT INTO vendas (data, valor, usuario_id) VALUES (NOW(), ?, ?)";
        $stmt_venda = $con->prepare($sql_venda);
        if (!$stmt_venda) { die("<strong>ERRO na preparação da Query B:</strong> " . $con->error); }
        $stmt_venda->bind_param("di", $valor_total_venda, $usuario_id);
        $stmt_venda->execute();
        echo "<p style='color:green;'>OK - Venda registrada com sucesso (" . $stmt_venda->affected_rows . " linha inserida).</p>";
    } else {
        echo "<p><strong>Etapa B:</strong> Pulada. O valor total é zero, nenhuma venda registrada.</p>";
    }

    // ETAPA C: LIMPAR OS ITENS DO PEDIDO DA MESA
    echo "<p><strong>Etapa C:</strong> Limpando itens da tabela 'pedido_produtos'...</p>";
    $sql_limpar_itens = "DELETE FROM pedido_produtos WHERE id_mesa = ?";
    $stmt_limpar_itens = $con->prepare($sql_limpar_itens);
    if (!$stmt_limpar_itens) { die("<strong>ERRO na preparação da Query C:</strong> " . $con->error); }
    $stmt_limpar_itens->bind_param("i", $id_mesa);
    $stmt_limpar_itens->execute();
    echo "<p style='color:green;'>OK - Itens do pedido limpos (" . $stmt_limpar_itens->affected_rows . " linhas removidas).</p>";
    
    // ETAPA D: ATUALIZAR O STATUS DA MESA PARA "NÃO ATENDIDO/LIVRE"
    echo "<p><strong>Etapa D:</strong> Atualizando status da 'mesa'...</p>";
    $sql_mesa = "UPDATE mesa SET status_mesa = 1, garcom_mesa = NULL, nome_cliente = NULL, n_pedido = NULL WHERE id_mesa = ?";
    $stmt_mesa = $con->prepare($sql_mesa);
    if (!$stmt_mesa) { die("<strong>ERRO na preparação da Query D:</strong> " . $con->error); }
    $stmt_mesa->bind_param("i", $id_mesa);
    $stmt_mesa->execute();
    echo "<p style='color:green;'>OK - Status da mesa atualizado (" . $stmt_mesa->affected_rows . " linha atualizada).</p>";

    // 3. CONFIRMAR A TRANSAÇÃO
    $con->commit();
    echo "<h2><strong style='color:green;'>SUCESSO!</strong></h2>";
    echo "<p>Transação concluída e confirmada no banco de dados.</p>";

} catch (Exception $e) {
    // 4. DESFAZER A TRANSAÇÃO EM CASO DE ERRO
    $con->rollback();
    echo "<h2><strong style='color:red;'>ERRO NA TRANSAÇÃO!</strong></h2>";
    echo "<p>Ocorreu um erro e todas as alterações foram desfeitas.</p>";
    echo "<p><strong>Mensagem do Erro:</strong> " . $e->getMessage() . "</p>";
}

echo "<br><br><a href='index.php'>Voltar para a lista de mesas</a>";
?>
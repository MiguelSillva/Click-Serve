<?php
session_start();
include 'Bd.php';

// (Toda a sua lógica PHP existente para editar a mesa permanece aqui)
if (!isset($_SESSION['Nome']) || !isset($_SESSION['Senha'])) { header('Location: login.php'); exit(); }
$Logado = $_SESSION['Nome'];
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { header('Location: garcom_mesas.php'); exit(); }
$id_mesa = (int)$_GET['id'];

// Lógica do POST para salvar alterações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // A lógica para pegar os nomes já existia, agora eles virão do formulário
    $garcom = $_POST['garcom'];
    $cliente = $_POST['cliente'];
    $produtos_selecionados_novos = $_POST['produtos'] ?? [];
    
    $con->begin_transaction();
    try {
        $sql_itens_antigos = "SELECT id_produto, quantidade FROM pedido_produtos WHERE id_mesa = ?";
        $stmt_antigos = $con->prepare($sql_itens_antigos);
        $stmt_antigos->bind_param("i", $id_mesa);
        $stmt_antigos->execute();
        $resultado_antigos = $stmt_antigos->get_result();
        $stmt_devolve_estoque = $con->prepare("UPDATE produtos SET quantidade_em_estoque = quantidade_em_estoque + ? WHERE id = ?");
        while ($item = $resultado_antigos->fetch_assoc()) {
            $stmt_devolve_estoque->bind_param("ii", $item['quantidade'], $item['id_produto']);
            $stmt_devolve_estoque->execute();
        }
        $stmt_delete_itens = $con->prepare("DELETE FROM pedido_produtos WHERE id_mesa = ?");
        $stmt_delete_itens->bind_param("i", $id_mesa);
        $stmt_delete_itens->execute();
        
        // Atualiza a mesa com os novos nomes E os produtos
        $sql_update_mesa = "UPDATE mesa SET garcom_mesa = ?, nome_cliente = ? WHERE id_mesa = ?";
        $stmt_mesa = $con->prepare($sql_update_mesa);
        $stmt_mesa->bind_param("ssi", $garcom, $cliente, $id_mesa);
        $stmt_mesa->execute();
        
        $stmt_insert_item = $con->prepare("INSERT INTO pedido_produtos (id_mesa, id_produto, quantidade) VALUES (?, ?, ?)");
        $stmt_baixa_estoque = $con->prepare("UPDATE produtos SET quantidade_em_estoque = quantidade_em_estoque - ? WHERE id = ?");
        foreach ($produtos_selecionados_novos as $produto_id => $quantidade) {
            $qtd = (int)$quantidade;
            if ($qtd > 0) {
                $stmt_insert_item->bind_param("iii", $id_mesa, $produto_id, $qtd);
                $stmt_insert_item->execute();
                $stmt_baixa_estoque->bind_param("ii", $qtd, $produto_id);
                $stmt_baixa_estoque->execute();
            }
        }
        $con->commit();
        header('Location: garcom_mesas.php?edicao=sucesso');
        exit();
    } catch (Exception $e) {
        $con->rollback();
        header('Location: editar_mesa_mobile.php?id=' . $id_mesa . '&erro=salvar');
        exit();
    }
}

// Lógica para buscar dados atuais da mesa e dos produtos
$stmt_mesa_atual = $con->prepare("SELECT garcom_mesa, nome_cliente FROM mesa WHERE id_mesa = ?");
$stmt_mesa_atual->bind_param("i", $id_mesa); $stmt_mesa_atual->execute();
$resultado_mesa_atual = $stmt_mesa_atual->get_result()->fetch_assoc();
$garcom_atual = $resultado_mesa_atual['garcom_mesa'] ?? '';
$cliente_atual = $resultado_mesa_atual['nome_cliente'] ?? '';
$stmt_mesa_atual->close();
$stmt_produtos_atuais = $con->prepare("SELECT id_produto, quantidade FROM pedido_produtos WHERE id_mesa = ?");
$stmt_produtos_atuais->bind_param("i", $id_mesa); $stmt_produtos_atuais->execute();
$resultado_produtos_atuais = $stmt_produtos_atuais->get_result();
$produtos_selecionados = [];
while ($row = $resultado_produtos_atuais->fetch_assoc()) { $produtos_selecionados[$row['id_produto']] = $row['quantidade']; }
$stmt_produtos_atuais->close();
$resultado_produtos_todos = $con->query("SELECT id, nomeProduto, preco FROM produtos ORDER BY nomeProduto ASC");
$produtos_disponiveis = [];
if ($resultado_produtos_todos) { while($row = $resultado_produtos_todos->fetch_assoc()) { $produtos_disponiveis[] = $row; } }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Editar Mesa <?php echo $id_mesa; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
    </style>
</head>
<body class="bg-gray-100">
    <main class="p-4 max-w-2xl mx-auto">
        <header class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-black">
                    Pedido Mesa
                    <span class="ml-2 bg-yellow-400 text-black text-xl font-semibold px-4 py-1 rounded-full align-middle"><?php echo $id_mesa; ?></span>
                </h1>
            </div>
            <a href="garcom_mesas.php" class="flex items-center gap-2 px-4 py-2 rounded-lg text-black bg-gray-200 hover:bg-gray-300 font-semibold transition">
                <span class="material-icons">arrow_back</span>
                Voltar
            </a>
        </header>

        <form id="editarForm" action="editar_mesa_mobile.php?id=<?php echo $id_mesa; ?>" method="POST" class="space-y-6">
            
            <div class="bg-white rounded-xl shadow-md">
                <div class="p-5 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-black">Informações da Mesa</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label for="garcom" class="block text-sm font-semibold text-gray-600 mb-2">Nome do Garçom</label>
                        <input type="text" name="garcom" id="garcom" value="<?php echo htmlspecialchars($garcom_atual); ?>" required class="px-4 py-2 rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 w-full transition">
                    </div>
                    <div>
                        <label for="cliente" class="block text-sm font-semibold text-gray-600 mb-2">Nome do Cliente (Opcional)</label>
                        <input type="text" name="cliente" id="cliente" value="<?php echo htmlspecialchars($cliente_atual); ?>" class="px-4 py-2 rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 w-full transition">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md">
                <div class="p-5 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-black">Adicionar/Editar Produtos</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php foreach ($produtos_disponiveis as $produto): ?>
                        <?php $quantidade_selecionada = $produtos_selecionados[$produto['id']] ?? 0; ?>
                        <div class="p-5 flex justify-between items-center hover:bg-yellow-50">
                            <div>
                                <h3 class="font-semibold text-black text-lg"><?php echo htmlspecialchars($produto['nomeProduto']); ?></h3>
                                <p class="text-sm text-gray-500">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" class="quantity-change w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center" data-action="decrement"><span class="material-icons text-xl">remove</span></button>
                                <input type="number" name="produtos[<?php echo $produto['id']; ?>]" value="<?php echo $quantidade_selecionada; ?>" min="0" class="quantity-input w-12 text-center text-lg font-bold bg-transparent border-none focus:ring-0">
                                <button type="button" class="quantity-change w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center" data-action="increment"><span class="material-icons text-xl">add</span></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="space-y-3">
                <button type="submit" class="w-full flex justify-center items-center gap-3 bg-yellow-400 text-black font-bold py-3 px-4 rounded-lg shadow-md hover:bg-yellow-500 transition">
                    <span class="material-icons">save</span>
                    Salvar Alterações
                </button>
            </div>
        </form>
    </main>

    <script>
    // Lógica dos botões de quantidade (+/-)
    document.querySelectorAll('.quantity-change').forEach(button => {
        button.addEventListener('click', () => {
            const action = button.dataset.action;
            const input = button.parentElement.querySelector('.quantity-input');
            let currentValue = parseInt(input.value, 10);
            if (action === 'increment') currentValue++;
            else if (action === 'decrement' && currentValue > 0) currentValue--;
            input.value = currentValue;
        });
    });
    </script>
</body>
</html>
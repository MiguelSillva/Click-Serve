<?php
session_start();
include 'Bd.php';

// (Toda a sua lógica PHP existente para atender a mesa permanece aqui)
if (!isset($_SESSION['Nome']) || !isset($_SESSION['Senha'])) { header('Location: login.php'); exit(); }
$Logado = $_SESSION['Nome'];
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { header('Location: garcom_mesas.php?erro=mesainvalida'); exit(); }
$id_mesa = (int)$_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $garcom = $_POST['garcom'];
    $cliente = $_POST['cliente'];
    $produtos_selecionados = $_POST['produtos'] ?? [];
    $con->begin_transaction();
    try {
        $sql_update_mesa = "UPDATE mesa SET garcom_mesa = ?, nome_cliente = ?, status_mesa = 0 WHERE id_mesa = ?";
        $stmt_mesa = $con->prepare($sql_update_mesa);
        $stmt_mesa->bind_param("ssi", $garcom, $cliente, $id_mesa);
        $stmt_mesa->execute();
        $stmt_estoque = $con->prepare("UPDATE produtos SET quantidade_em_estoque = quantidade_em_estoque - ? WHERE id = ?");
        $stmt_pedido_item = $con->prepare("INSERT INTO pedido_produtos (id_mesa, id_produto, quantidade) VALUES (?, ?, ?)");
        foreach ($produtos_selecionados as $produto_id => $quantidade) {
            $qtd = (int)$quantidade;
            if ($qtd > 0) {
                $stmt_pedido_item->bind_param("iii", $id_mesa, $produto_id, $qtd);
                $stmt_pedido_item->execute();
                $stmt_estoque->bind_param("ii", $qtd, $produto_id);
                $stmt_estoque->execute();
            }
        }
        $con->commit();
        header('Location: garcom_mesas.php?atendimento=sucesso');
        exit();
    } catch (Exception $e) {
        $con->rollback();
        header('Location: atender_mesa_mobile.php?id=' . $id_mesa . '&erro=salvar');
        exit();
    }
}
$sql_produtos = "SELECT id, nomeProduto, preco FROM produtos WHERE quantidade_em_estoque > 0 ORDER BY nomeProduto ASC";
$resultado_produtos = $con->query($sql_produtos);
$produtos = [];
if ($resultado_produtos->num_rows > 0) {
    while($row = $resultado_produtos->fetch_assoc()) {
        $produtos[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Atender Mesa <?php echo $id_mesa; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">
    <main class="p-4 max-w-2xl mx-auto">
        <header class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-black">
                    Atender Mesa
                    <span class="ml-2 bg-yellow-400 text-black text-xl font-semibold px-4 py-1 rounded-full align-middle"><?php echo $id_mesa; ?></span>
                </h1>
            </div>
            <a href="garcom_mesas.php" class="flex items-center gap-2 px-4 py-2 rounded-lg text-black bg-gray-200 hover:bg-gray-300 font-semibold transition">
                <span class="material-icons">arrow_back</span>
                Voltar
            </a>
        </header>
        
        <form id="atenderForm" action="atender_mesa_mobile.php?id=<?php echo $id_mesa; ?>" method="POST" class="space-y-6">
            <div class="bg-white rounded-xl shadow-md">
                <div class="p-5 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-black">Informações Iniciais</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label for="garcom" class="block text-sm font-semibold text-gray-600 mb-2">Seu Nome (Garçom)</label>
                        <input type="text" name="garcom" id="garcom" value="<?php echo htmlspecialchars($Logado); ?>" required class="px-4 py-2 rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 w-full transition">
                    </div>
                    <div>
                        <label for="cliente" class="block text-sm font-semibold text-gray-600 mb-2">Nome do Cliente (Opcional)</label>
                        <input type="text" name="cliente" id="cliente" placeholder="Nome ou identificação" class="px-4 py-2 rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 w-full transition">
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md">
                <div class="p-5 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-black">Pedido Inicial</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php if (!empty($produtos)): ?>
                        <?php foreach ($produtos as $produto): ?>
                        <div class="p-5 flex justify-between items-center hover:bg-yellow-50">
                            <div>
                                <h3 class="font-semibold text-black text-lg"><?php echo htmlspecialchars($produto['nomeProduto']); ?></h3>
                                <p class="text-sm text-gray-500">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                            </div>
                            <input type="number" name="produtos[<?php echo $produto['id']; ?>]" value="0" min="0" class="produto-quantidade w-24 text-center text-lg font-bold bg-gray-100 border-gray-300 rounded-lg focus:ring-yellow-500">
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-gray-500 p-6">Nenhum produto com estoque disponível.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center items-center gap-3 bg-yellow-400 text-black font-bold py-3 px-4 rounded-lg shadow-md hover:bg-yellow-500 transition">
                    <span class="material-icons">restaurant_menu</span>
                    Registrar Atendimento
                </button>
            </div>
        </form>
    </main>

    <script>
    document.getElementById('atenderForm').addEventListener('submit', function(event) {
        // Seleciona todos os campos de quantidade de produtos
        const produtos = document.querySelectorAll('.produto-quantidade');
        let algumSelecionado = false;

        // Verifica se pelo menos um produto tem quantidade maior que 0
        produtos.forEach(produto => {
            if (parseInt(produto.value) > 0) {
                algumSelecionado = true;
            }
        });

        // Se nenhum produto foi selecionado, impede o envio do formulário e mostra um alerta
        if (!algumSelecionado) {
            event.preventDefault(); // Impede o envio
            Swal.fire({
                icon: 'warning',
                title: 'Nenhum produto selecionado',
                text: 'Você deve adicionar pelo menos 1 produto para iniciar o atendimento.',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#facc15', // Cor do tema
                customClass: {
                    confirmButton: 'swal-confirm-button' 
                }
            });
        }
    });

    // Classe para garantir o texto preto no botão de confirmação do Swal
    const style = document.createElement('style');
    style.innerHTML = `
        .swal-confirm-button {
            color: black !important;
            font-weight: 600 !important;
        }
    `;
    document.head.appendChild(style);
    </script>

</body>
</html>
<?php
session_start();
include 'Bd.php';

// --- TODA A LÓGICA PHP ANTERIOR É MANTIDA ---

// --- VERIFICAÇÃO DE SESSÃO ---
if (!isset($_SESSION['Nome']) || !isset($_SESSION['Senha'])) {
    header('Location: loginIA.php');
    exit();
}
$Logado = $_SESSION['Nome'];

// --- VALIDAÇÃO DO ID DA MESA ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?erro=mesainvalida');
    exit();
}
$id_mesa = (int)$_GET['id'];

// --- LÓGICA DE ATUALIZAÇÃO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $garcom = $_POST['garcom'];
    $cliente = $_POST['cliente'];
    $produtos_selecionados_novos = $_POST['produtos'] ?? [];

    $con->begin_transaction();
    try {
        // 1. Devolve itens antigos ao estoque
        $stmt_antigos = $con->prepare("SELECT id_produto, quantidade FROM pedido_produtos WHERE id_mesa = ?");
        $stmt_antigos->bind_param("i", $id_mesa);
        $stmt_antigos->execute();
        $resultado_antigos = $stmt_antigos->get_result();
        $stmt_devolve_estoque = $con->prepare("UPDATE produtos SET quantidade_em_estoque = quantidade_em_estoque + ? WHERE id = ?");
        while ($item = $resultado_antigos->fetch_assoc()) {
            $stmt_devolve_estoque->bind_param("ii", $item['quantidade'], $item['id_produto']);
            $stmt_devolve_estoque->execute();
        }
        $stmt_antigos->close();

        // 2. Deleta registros antigos
        $stmt_delete_itens = $con->prepare("DELETE FROM pedido_produtos WHERE id_mesa = ?");
        $stmt_delete_itens->bind_param("i", $id_mesa);
        $stmt_delete_itens->execute();
        $stmt_delete_itens->close();

        // 3. Atualiza mesa
        $stmt_mesa = $con->prepare("UPDATE mesa SET garcom_mesa = ?, nome_cliente = ? WHERE id_mesa = ?");
        $stmt_mesa->bind_param("ssi", $garcom, $cliente, $id_mesa);
        $stmt_mesa->execute();
        $stmt_mesa->close();

        // 4. Insere novos itens e dá baixa no estoque
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
        header('Location: index.php?edicao=sucesso');
        exit();
    } catch (Exception $e) {
        $con->rollback();
        header('Location: editar_mesa.php?id=' . $id_mesa . '&erro=salvar');
        exit();
    }
}

// --- BUSCA DE DADOS PARA PREENCHER O FORMULÁRIO ---
$stmt_mesa_atual = $con->prepare("SELECT garcom_mesa, nome_cliente FROM mesa WHERE id_mesa = ?");
$stmt_mesa_atual->bind_param("i", $id_mesa);
$stmt_mesa_atual->execute();
$resultado_mesa_atual = $stmt_mesa_atual->get_result()->fetch_assoc();
$garcom_atual = $resultado_mesa_atual['garcom_mesa'] ?? '';
$cliente_atual = $resultado_mesa_atual['nome_cliente'] ?? '';
$stmt_mesa_atual->close();

$stmt_produtos_atuais = $con->prepare("SELECT id_produto, quantidade FROM pedido_produtos WHERE id_mesa = ?");
$stmt_produtos_atuais->bind_param("i", $id_mesa);
$stmt_produtos_atuais->execute();
$resultado_produtos_atuais = $stmt_produtos_atuais->get_result();
$produtos_selecionados = [];
while ($row = $resultado_produtos_atuais->fetch_assoc()) {
    $produtos_selecionados[$row['id_produto']] = $row['quantidade'];
}
$stmt_produtos_atuais->close();

$resultado_produtos_todos = $con->query("SELECT id, nomeProduto, preco FROM produtos ORDER BY nomeProduto ASC");
$produtos_disponiveis = [];
if ($resultado_produtos_todos) {
    while($row = $resultado_produtos_todos->fetch_assoc()) {
        $produtos_disponiveis[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Editar Mesa <?php echo $id_mesa; ?> - Click&Serve</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Remove as setas padrão do input de número */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
</head>
<body class="bg-gray-50">

    <div class="flex">
        <aside class="sidebar-main flex flex-col justify-between bg-white shadow-2xl min-h-screen w-72 p-6 fixed top-0 left-0 h-screen z-40" style="box-shadow: 8px 0 32px -8px rgba(0,0,0,0.18);">
            <div>
                <div class="flex items-center gap-3 mb-8">
                    <img alt="Click&Serve" height="40" src="imagens/click.png" width="40" class="rounded-full shadow-md"/>
                    <h1 class="font-bold text-2xl text-black">Click&Serve <span class="text-gray-400 text-sm font-normal">v.01</span></h1>
                </div>
                <nav class="flex flex-col gap-4">
                    <a href="Grafico.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-yellow-100 transition" type="button">
                        <span class="material-icons text-black">dashboard</span>
                        <span class="font-semibold text-black">Dashboard</span>
                        <span class="material-icons ml-auto text-gray-400">chevron_right</span>
                    </a>
                    <a href="Estoque.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-yellow-100 transition" type="button">
                        <span class="material-icons text-black">inventory_2</span>
                        <span class="font-semibold text-black">Estoque</span>
                        <span class="material-icons ml-auto text-gray-400">chevron_right</span>
                    </a>
                    <a href="index.php" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-yellow-100 transition" type="button">
                        <span class="material-icons text-black">table_restaurant</span>
                        <span class="font-semibold text-black">Mesas</span>
                        <span class="material-icons ml-auto text-gray-400">chevron_right</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-yellow-100 transition" type="button">
                        <span class="material-icons text-black">add</span>
                        <span class="font-semibold text-black">Adicionar Mesas</span>
                        <span class="material-icons ml-auto text-gray-400">chevron_right</span>
                    </a>
                </nav>
            </div>
            <div class="flex items-center gap-3 mt-8">
                <img alt="User profile photo" class="rounded-full shadow-md" height="32" src="https://storage.googleapis.com/a1aa/image/7d0bfb8f-e4fd-47bb-8e94-8cf907b30106.jpg" width="32"/>
                <div>
                    <p class='font-bold text-black m-0'><?php echo htmlspecialchars($Logado); ?></p>
                    <p class="text-gray-500 m-0">Gerente</p>
                </div>
                <a href='logout.php' class="ml-auto flex items-center gap-1 px-3 py-2 rounded-lg bg-yellow-400 hover:bg-yellow-500 text-black font-semibold shadow transition">
                    <span class="material-icons text-black">logout</span> Sair
                </a>
            </div>
        </aside>

        <main class="ml-72 p-8 w-full">
            <header class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-black">
                        Editar Atendimento da Mesa
                        <span class="ml-2 bg-yellow-400 text-black text-xl font-semibold px-4 py-1 rounded-full align-middle">
                            <?php echo $id_mesa; ?>
                        </span>
                    </h1>
                    <p class="text-gray-500 mt-1">Faça as alterações necessárias no pedido da mesa.</p>
                </div>
                <a href="index.php" class="flex items-center gap-2 px-4 py-2 rounded-lg text-black bg-gray-200 hover:bg-gray-300 font-semibold transition">
                    <span class="material-icons">arrow_back</span>
                    Voltar
                </a>
            </header>

            <?php if (isset($_GET['erro'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                    <p class="font-bold">Erro!</p>
                    <p>Ocorreu um problema ao salvar as alterações. Tente novamente.</p>
                </div>
            <?php endif; ?>

            <form id="editarForm" action="editar_mesa.php?id=<?php echo $id_mesa; ?>" method="POST" class="space-y-8">
                
                <div class="bg-white rounded-xl shadow-lg border border-yellow-200">
                    <div class="p-5 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-black">Informações do Atendimento</h2>
                    </div>
                    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="garcom" class="block text-sm font-semibold text-gray-600 mb-2">Nome do Garçom</label>
                            <input type="text" id="garcom" name="garcom" value="<?php echo htmlspecialchars($garcom_atual); ?>" required class="px-4 py-2 rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 w-full transition">
                        </div>
                        <div>
                            <label for="cliente" class="block text-sm font-semibold text-gray-600 mb-2">Nome do Cliente (Opcional)</label>
                            <input type="text" id="cliente" name="cliente" value="<?php echo htmlspecialchars($cliente_atual); ?>" class="px-4 py-2 rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 w-full transition">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg border border-yellow-200">
                    <div class="p-5 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-black">Adicionar/Editar Produtos</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <?php if (!empty($produtos_disponiveis)): ?>
                            <?php foreach ($produtos_disponiveis as $produto): ?>
                                <?php $quantidade_selecionada = $produtos_selecionados[$produto['id']] ?? 0; ?>
                                <div class="p-5 flex justify-between items-center hover:bg-yellow-50 transition-colors duration-200">
                                    <div>
                                        <h3 class="font-semibold text-black text-lg"><?php echo htmlspecialchars($produto['nomeProduto']); ?></h3>
                                        <p class="text-sm text-gray-500">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" class="quantity-change w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-700 flex items-center justify-center transition" data-action="decrement">
                                            <span class="material-icons text-xl">remove</span>
                                        </button>
                                        <input type="number" name="produtos[<?php echo $produto['id']; ?>]" value="<?php echo $quantidade_selecionada; ?>" min="0" 
                                               class="quantity-input w-12 text-center text-lg font-bold bg-transparent border-none focus:ring-0">
                                        <button type="button" class="quantity-change w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-700 flex items-center justify-center transition" data-action="increment">
                                            <span class="material-icons text-xl">add</span>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center text-gray-500 py-10">Nenhum produto cadastrado no sistema.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center items-center gap-3 bg-yellow-400 text-black font-bold py-3 px-4 rounded-lg shadow-md hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400 transition">
                        <span class="material-icons">save</span>
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Lógica para o Alerta de Confirmação do formulário
        document.getElementById('editarForm').addEventListener('submit', function(event) {
            const produtos = document.querySelectorAll('.quantity-input');
            let algumSelecionado = false;
            produtos.forEach(produto => {
                if (parseInt(produto.value, 10) > 0) {
                    algumSelecionado = true;
                }
            });

            if (!algumSelecionado) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Nenhum produto selecionado',
                    text: 'A mesa deve ter pelo menos 1 produto.',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#facc15' // Amarelo do tema
                });
            }
        });

        // Lógica para os botões de Aumentar/Diminuir quantidade
        document.querySelectorAll('.quantity-change').forEach(button => {
            button.addEventListener('click', () => {
                const action = button.dataset.action;
                const wrapper = button.parentElement;
                const input = wrapper.querySelector('.quantity-input');
                let currentValue = parseInt(input.value, 10);

                if (action === 'increment') {
                    currentValue++;
                } else if (action === 'decrement' && currentValue > 0) {
                    currentValue--;
                }

                input.value = currentValue;
            });
        });
    </script>
</body>
</html>
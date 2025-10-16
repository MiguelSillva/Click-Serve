<?php
session_start();
include 'Bd.php';

// Validações de segurança
if (!isset($_SESSION['Nome']) || !isset($_SESSION['Senha'])) {
    header('Location: login.php');
    exit();
}
$Logado = $_SESSION['Nome'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: Estoque.php?edicao=erro_id');
    exit();
}
$produto_id = (int)$_GET['id'];

// Lógica de ATUALIZAÇÃO quando o formulário é enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nomeProduto'];
    $quantidade = (int)$_POST['quantidade'];
    // BUG CORRIGIDO: Agora o preço é tratado diretamente como número (float), que é mais seguro.
    $preco = (float)$_POST['preco']; 
    $categoria_id = (int)$_POST['categoria_id'];

    $sql_update = "UPDATE produtos SET nomeProduto = ?, quantidade_em_estoque = ?, preco = ?, categoria_id = ? WHERE id = ?";
    $stmt_update = $con->prepare($sql_update);
    $stmt_update->bind_param("sidii", $nome, $quantidade, $preco, $categoria_id, $produto_id);

    if ($stmt_update->execute()) {
        header('Location: Estoque.php?edicao=sucesso');
    } else {
        // Para depuração futura, você pode logar o erro: error_log($stmt_update->error);
        header('Location: Estoque.php?edicao=erro');
    }
    exit();
}

// Lógica para BUSCAR dados do produto e preencher o formulário
$sql_produto = "SELECT nomeProduto, quantidade_em_estoque, preco, categoria_id FROM produtos WHERE id = ?";
$stmt_produto = $con->prepare($sql_produto);
$stmt_produto->bind_param("i", $produto_id);
$stmt_produto->execute();
$result_produto = $stmt_produto->get_result();
$produto = $result_produto->fetch_assoc();

if (!$produto) {
    header('Location: Estoque.php?edicao=notfound');
    exit();
}

// Buscar todas as categorias para o dropdown
$sql_categorias = "SELECT ID_CategoriaProd, nome FROM categoria ORDER BY nome ASC";
$result_categorias = $con->query($sql_categorias);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Editar Produto - Click&Serve</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <style> body { font-family: 'Inter', sans-serif; } </style>
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
                    <a href="Grafico.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-yellow-100 transition">
                        <span class="material-icons text-black">dashboard</span>
                        <span class="font-semibold text-black">Dashboard</span>
                        <span class="material-icons ml-auto text-gray-400">chevron_right</span>
                    </a>
                    <a href="Estoque.php" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-yellow-100 transition">
                        <span class="material-icons text-black">inventory_2</span>
                        <span class="font-semibold text-black">Estoque</span>
                        <span class="material-icons ml-auto text-gray-400">chevron_right</span>
                    </a>
                    <a href="index.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-yellow-100 transition">
                        <span class="material-icons text-black">table_restaurant</span>
                        <span class="font-semibold text-black">Mesas</span>
                        <span class="material-icons ml-auto text-gray-400">chevron_right</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-yellow-100 transition">
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
                    <h1 class="text-3xl font-bold text-black">Editar Produto</h1>
                    <p class="text-gray-500">Altere os dados do produto abaixo.</p>
                </div>
                <a href="Estoque.php" class="flex items-center gap-2 px-4 py-2 rounded-lg text-black bg-gray-200 hover:bg-gray-300 font-semibold transition">
                    <span class="material-icons">arrow_back</span>
                    Voltar para o Estoque
                </a>
            </header>
            
            <div class="bg-white rounded-xl shadow-lg border border-yellow-200 max-w-2xl mx-auto">
                <div class="p-5 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-black"><?php echo htmlspecialchars($produto['nomeProduto']); ?></h2>
                </div>
                <form action="editar_produto.php?id=<?php echo $produto_id; ?>" method="POST" class="p-5 space-y-6">
                    <div>
                        <label for="nomeProduto" class="block text-sm font-semibold text-gray-600 mb-2">Nome do Produto</label>
                        <input type="text" name="nomeProduto" id="nomeProduto" value="<?php echo htmlspecialchars($produto['nomeProduto']); ?>" required class="px-4 py-2 rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 w-full transition">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="quantidade" class="block text-sm font-semibold text-gray-600 mb-2">Quantidade em Estoque</label>
                            <input type="number" name="quantidade" id="quantidade" value="<?php echo htmlspecialchars($produto['quantidade_em_estoque']); ?>" required class="px-4 py-2 rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 w-full transition">
                        </div>
                        <div>
                            <label for="preco" class="block text-sm font-semibold text-gray-600 mb-2">Preço (R$)</label>
                            <input type="number" step="0.01" name="preco" id="preco" value="<?php echo htmlspecialchars($produto['preco']); ?>" required class="px-4 py-2 rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 w-full transition">
                        </div>
                    </div>
                    <div>
                        <label for="categoria_id" class="block text-sm font-semibold text-gray-600 mb-2">Categoria</label>
                        <select name="categoria_id" id="categoria_id" required class="px-4 py-2 rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 w-full transition bg-white">
                            <option value="">Selecione uma categoria</option>
                            <?php
                            while ($categoria = $result_categorias->fetch_assoc()) {
                                $selected = ($categoria['ID_CategoriaProd'] == $produto['categoria_id']) ? 'selected' : '';
                                echo "<option value='" . $categoria['ID_CategoriaProd'] . "' " . $selected . ">" . htmlspecialchars($categoria['nome']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="w-full flex justify-center items-center gap-3 bg-yellow-400 text-black font-bold py-3 px-4 rounded-lg shadow-md hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400 transition">
                            <span class="material-icons">save</span>
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
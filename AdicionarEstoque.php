<?php
session_start();
include 'Bd.php';

if (!isset($_SESSION['Nome']) || !isset($_SESSION['Senha'])) {
    header('Location: login.php');
    exit();
}
$Logado = $_SESSION['Nome'];

// --- MELHORIA: Buscar todas as categorias do banco de dados para o dropdown ---
$sql_categorias = "SELECT ID_CategoriaProd, nome FROM categoria ORDER BY nome ASC";
$result_categorias = $con->query($sql_categorias);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Adicionar Produto - Click&Serve</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <h1 class="text-3xl font-bold text-black">Adicionar Novo Produto</h1>
                    <p class="text-gray-500">Preencha os dados para cadastrar um item no estoque.</p>
                </div>
                <a href="Estoque.php" class="flex items-center gap-2 px-4 py-2 rounded-lg text-black bg-gray-200 hover:bg-gray-300 font-semibold transition">
                    <span class="material-icons">arrow_back</span>
                    Voltar para o Estoque
                </a>
            </header>
            
            <div class="bg-white rounded-xl shadow-lg border border-yellow-200 max-w-2xl mx-auto">
                <div class="p-5 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-black">Detalhes do Produto</h2>
                </div>
                <form action="AdicaoProduto.php" id="formulario" method="post" class="p-5 space-y-6">
                    <div>
                        <label for="nomeProduto" class="block text-sm font-semibold text-gray-600 mb-2">Nome do Produto</label>
                        <input type="text" name="nomeProduto" id="nomeProduto" placeholder="Ex: Coca-Cola Lata" required class="px-4 py-2 rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 w-full transition">
                    </div>
                    
                    <div>
                        <label for="categoria" class="block text-sm font-semibold text-gray-600 mb-2">Categoria</label>
                        <select name="categoria" id="categoria" required class="px-4 py-2 rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 w-full transition bg-white">
                            <option value="">Selecione uma categoria</option>
                            <?php
                            // Loop para criar as opções de categoria dinamicamente
                            if ($result_categorias->num_rows > 0) {
                                while ($categoria = $result_categorias->fetch_assoc()) {
                                    echo "<option value='" . $categoria['ID_CategoriaProd'] . "'>" . htmlspecialchars($categoria['nome']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="quantidade" class="block text-sm font-semibold text-gray-600 mb-2">Quantidade Inicial</label>
                            <input type="number" name="quantidade" id="quantidade" min="0" placeholder="0" required class="px-4 py-2 rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 w-full transition">
                        </div>
                        <div>
                            <label for="preco" class="block text-sm font-semibold text-gray-600 mb-2">Preço Unitário (R$)</label>
                            <input type="number" step="0.01" name="preco" id="preco" placeholder="Ex: 5.50" required class="px-4 py-2 rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 w-full transition">
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" id="btnSalvar" class="w-full flex justify-center items-center gap-3 bg-yellow-400 text-black font-bold py-3 px-4 rounded-lg shadow-md hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400 transition">
                            <span class="material-icons">add_circle</span>
                            Salvar Produto
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
    // Lógica de confirmação para salvar (mantida do seu código original)
    document.getElementById('btnSalvar').addEventListener('click', function(event) {
        event.preventDefault(); // Previne o envio imediato do formulário
        
        const form = document.getElementById('formulario');

        // Verifica se o formulário é válido (campos required preenchidos)
        if (!form.checkValidity()) {
            form.reportValidity(); // Mostra as mensagens de erro do navegador
            return; 
        }

        // Se o formulário for válido, mostra o alerta de confirmação
        Swal.fire({
            title: 'Confirmar Adição',
            text: 'Você deseja adicionar este produto ao estoque?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#facc15', // Cor do tema
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Sim, adicionar!',
            customClass: {
                confirmButton: 'swal-confirm-button' // Garante texto preto no botão
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Envia o formulário após a confirmação
            }
        });
    });
    </script>
</body>
</html>
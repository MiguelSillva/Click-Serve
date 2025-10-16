<?php
session_start();
include 'Bd.php';

if (!isset($_SESSION['Nome']) || !isset($_SESSION['Senha'])) {
    header('Location: login.php');
    exit();
}
$Logado = $_SESSION['Nome'];

// --- CONSULTAS OTIMIZADAS PARA OS KPIs ---
// KPI 1: Total de unidades no estoque
$query_total_itens = "SELECT SUM(quantidade_em_estoque) as total FROM produtos";
$result_total_itens = $con->query($query_total_itens);
$total_itens_estoque = $result_total_itens->fetch_assoc()['total'] ?? 0;

// KPI 2: Total de produtos com baixo estoque (<= 10 unidades)
$query_baixo_estoque = "SELECT COUNT(id) as total FROM produtos WHERE quantidade_em_estoque <= 10";
$result_baixo_estoque = $con->query($query_baixo_estoque);
$total_estoque_baixo = $result_baixo_estoque->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Estoque - Click&Serve</title>
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
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-black">Estoque</h1>
                <p class="text-gray-500">Gerencie todos os produtos do seu estabelecimento.</p>
            </header>

            <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-lg p-6 border border-yellow-200">
                    <span class="material-icons text-yellow-500 text-4xl mb-2">inventory_2</span>
                    <p class="text-sm text-gray-500 font-semibold">Total de Unidades em Estoque</p>
                    <p class="text-3xl font-bold text-black mt-1"><?php echo $total_itens_estoque; ?></p>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 border border-yellow-200">
                    <span class="material-icons text-yellow-500 text-4xl mb-2">warning</span>
                    <p class="text-sm text-gray-500 font-semibold">Itens com Baixo Estoque</p>
                    <p class="text-3xl font-bold text-black mt-1"><?php echo $total_estoque_baixo; ?></p>
                </div>
            </section>

            <section class="bg-white rounded-xl shadow-lg border border-yellow-200">
                <header class="p-5 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-black">TODOS OS PRODUTOS</h3>
                        <p class="text-sm text-gray-500">Produtos ativos no sistema</p>
                    </div>
                    <a href="AdicionarEstoque.php" class="flex items-center gap-2 bg-yellow-400 hover:bg-yellow-500 text-black font-bold px-5 py-2 rounded-lg shadow transition">
                        <span class="material-icons">add</span>
                        Adicionar Produto
                    </a>
                </header>

                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Produto</th>
                                <th scope="col" class="px-6 py-3">Categoria</th>
                                <th scope="col" class="px-6 py-3">Quantidade</th>
                                <th scope="col" class="px-6 py-3">Preço Unitário</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            // CONSULTA OTIMIZADA com JOIN para buscar o nome da categoria
                            $sql_lista = "
                                SELECT p.id, p.nomeProduto, c.nome as categoria_nome, p.quantidade_em_estoque, p.preco 
                                FROM produtos p
                                LEFT JOIN categoria c ON p.categoria_id = c.ID_CategoriaProd
                                ORDER BY p.nomeProduto ASC
                            ";
                            $resultado_lista = $con->query($sql_lista);
                            
                            if ($resultado_lista->num_rows > 0) {
                                while ($produto = $resultado_lista->fetch_assoc()) {
                                    $quantidade = $produto['quantidade_em_estoque'];
                                    
                                    if ($quantidade == 0) {
                                        $status_texto = 'Indisponível';
                                        $status_classe = 'bg-red-100 text-red-800';
                                    } elseif ($quantidade <= 10) {
                                        $status_texto = 'Baixo estoque';
                                        $status_classe = 'bg-yellow-100 text-yellow-800';
                                    } else {
                                        $status_texto = 'Em estoque';
                                        $status_classe = 'bg-green-100 text-green-800';
                                    }

                                    echo '<tr class="bg-white border-b hover:bg-gray-50">';
                                    echo '<th scope="row" class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">' . htmlspecialchars($produto['nomeProduto']) . '</th>';
                                    echo '<td class="px-6 py-4">' . htmlspecialchars($produto['categoria_nome'] ?? 'N/A') . '</td>';
                                    echo '<td class="px-6 py-4">' . htmlspecialchars($quantidade) . '</td>';
                                    echo '<td class="px-6 py-4">R$ ' . number_format($produto['preco'], 2, ',', '.') . '</td>';
                                    echo '<td class="px-6 py-4"><span class="text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full ' . $status_classe . '">' . $status_texto . '</span></td>';
                                    echo '<td class="px-6 py-4 text-center flex justify-center gap-2">';
                                    // NOVO BOTÃO DE EDITAR
                                    echo '<a href="editar_produto.php?id=' . $produto['id'] . '" class="font-medium text-blue-700 hover:bg-blue-100 rounded-full p-2 transition" title="Editar"><span class="material-icons">edit</span></a>';
                                    echo '<a href="#" onclick="confirmDelete(' . $produto['id'] . ')" class="font-medium text-red-700 hover:bg-red-100 rounded-full p-2 transition" title="Excluir"><span class="material-icons">delete</span></a>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhum produto encontrado no estoque.</td></tr>';
                            }
                            $con->close();
                        ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script>
    // SweetAlert para confirmação de exclusão
    function confirmDelete(id) {
        Swal.fire({
            title: 'Tem certeza?',
            text: "Esta ação não pode ser revertida!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'ExcluirProduto.php?id=' + id;
            }
        });
    }

    // SweetAlert para mostrar sucesso/erro na edição de produtos
    const urlParams = new URLSearchParams(window.location.search);
    const edicaoStatus = urlParams.get('edicao');

    if (edicaoStatus === 'sucesso') {
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Produto atualizado com sucesso!",
            showConfirmButton: false,
            timer: 3000
        });
    } else if (edicaoStatus === 'erro') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Não foi possível atualizar o produto.'
        });
    }
    </script>
</body>
</html>
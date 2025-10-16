<?php 
    session_start();
    include 'Bd.php';
    if (!isset($_SESSION['Nome']) || !isset($_SESSION['Senha'])) {
            header('Location: login.php');
            exit();
    }
    $Logado = $_SESSION['Nome'];
    $usuario_id = (int) $_SESSION['id'];

    // --- LÓGICA DO FILTRO E PAGINAÇÃO COMBINADAS ---
    $filtro = $_GET['filtro'] ?? 'todas';
    $pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    if ($pagina_atual < 1) { $pagina_atual = 1; }

    $whereClause = "";
    if ($filtro === 'atendidas') {
        $whereClause = " WHERE status_mesa = 0";
    } elseif ($filtro === 'livres') {
        $whereClause = " WHERE status_mesa = 1";
    }

    $sql_total_mesas = "SELECT COUNT(*) FROM mesa" . $whereClause;
    $resultado_total_mesas = $con->query($sql_total_mesas);
    $total_mesas = $resultado_total_mesas->fetch_row()[0];

    $limite_por_pagina = 10;
    $total_paginas = ceil($total_mesas / $limite_por_pagina);
    if ($pagina_atual > $total_paginas && $total_paginas > 0) { $pagina_atual = $total_paginas; }
    $offset = ($pagina_atual - 1) * $limite_por_pagina;

    // Lógica para os cards de KPI
    $total_de_pedidos = $con->query("SELECT COUNT(*) as total FROM vendas")->fetch_assoc()['total'] ?? 0;
    $pedidos_atendidos_agora = $con->query("SELECT COUNT(*) as total FROM mesa WHERE status_mesa = 0")->fetch_assoc()['total'] ?? 0;
?> 
<html lang="pt-br">
 <head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Click&Serve Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="css/styleDashbord.css">
    <link rel="stylesheet" href="style.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
 </head>
 <body class="bg-gray-50">
    <script src="script.js"></script>

    <aside class="sidebar-main flex flex-col justify-between bg-white shadow-2xl min-h-screen w-72 p-6 fixed top-0 left-0 h-screen z-40" style="box-shadow: 8px 0 32px -8px rgba(0,0,0,0.18);">
        <div>
            <div class="flex items-center gap-3 mb-8">
                <img alt="Click&Serve" height="40" src="imagens/click.png" width="40" class="rounded-full shadow-md"/>
                <h1 class="font-bold text-2xl text-black">Click&Serve <span class="text-gray-400 text-sm font-normal">v.01</span></h1>
            </div>
            <nav class="flex flex-col gap-4">
                <a href="Grafico.php" class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-yellow-100 transition">
                    <span class="material-icons text-black">dashboard</span>
                    <span class="font-semibold text-black">Dashboard</span>
                    <span class="material-icons ml-auto text-gray-400">chevron_right</span>
                </a>
                <a href="Estoque.php" class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-yellow-100 transition">
                    <span class="material-icons text-black">inventory_2</span>
                    <span class="font-semibold text-black">Estoque</span>
                    <span class="material-icons ml-auto text-gray-400">chevron_right</span>
                </a>
                <a href="index.php" class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg bg-yellow-100 transition">
                    <span class="material-icons text-black">table_restaurant</span>
                    <span class="font-semibold text-black">Mesas</span>
                    <span class="material-icons ml-auto text-gray-400">chevron_right</span>
                </a>
                <a href="caixa.php" class="nav-link flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-yellow-100 transition">
                    <span class="material-icons text-black">point_of_sale</span>
                    <span class="font-semibold text-black">Frente de Caixa</span>
                    <span class="material-icons ml-auto text-gray-400">chevron_right</span>
                </a>
            </nav>
        </div>
        <div class="flex items-center gap-3 mt-8">
            <img alt="User profile" class="rounded-full shadow-md" height="32" src="https://storage.googleapis.com/a1aa/image/7d0bfb8f-e4fd-47bb-8e94-8cf907b30106.jpg" width="32"/>
            <div>
                <p class='font-bold text-black m-0'><?php echo htmlspecialchars($Logado); ?></p>
                <p class="text-gray-500 m-0">Gerente</p>
            </div>
            <a href='logout.php' class="ml-auto flex items-center gap-1 px-3 py-2 rounded-lg bg-yellow-400 hover:bg-yellow-500 text-black font-semibold shadow transition">
                <span class="material-icons text-black">logout</span> Sair
            </a>
        </div>
    </aside>

    <main class="content ml-72 p-8 w-full">
        <h2 class='text-xl text-gray-800 mb-4'>Olá, <?php echo htmlspecialchars($Logado); ?></h2>
        
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="flex flex-col items-center bg-white rounded-xl shadow-lg p-6 border border-yellow-200">
                <span class="material-icons text-yellow-500 text-4xl mb-2">assignment</span>
                <p class="text-sm text-gray-500 font-semibold">Total de vendas (histórico)</p>
                <p class="text-2xl font-bold text-black mt-1"><?php echo htmlspecialchars($total_de_pedidos); ?></p>
            </div>
            <div class="flex flex-col items-center bg-white rounded-xl shadow-lg p-6 border border-yellow-200">
                <span class="material-icons text-yellow-500 text-4xl mb-2">groups</span>
                <p class="text-sm text-gray-500 font-semibold">Clientes (Exemplo)</p>
                <p class="text-2xl font-bold text-black mt-1">1,893</p>
            </div>
            <div class="flex flex-col items-center bg-white rounded-xl shadow-lg p-6 border border-yellow-200">
                <span class="material-icons text-yellow-500 text-4xl mb-2">check_circle</span>
                <p class="text-sm text-gray-500 font-semibold">Mesas Atendidas Agora</p>
                <p class="text-2xl font-bold text-black mt-1"><?php echo htmlspecialchars($pedidos_atendidos_agora); ?></p>
            </div>
        </section>

        <section class="bg-white rounded-xl shadow-lg border border-yellow-200">
            <header class="p-5 border-b border-gray-200 flex flex-wrap justify-between items-center gap-4">
                <div>
                    <h3 class="text-xl font-bold text-black">TODAS AS MESAS</h3>
                    <p class="text-sm text-gray-500">Gerencie as mesas do seu estabelecimento</p>
                </div>
                <div class="flex items-center gap-3">
                    <select id="filtroStatus" onchange="aplicarFiltro()" class="px-4 py-2 rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 bg-white">
                        <option value="todas" <?php if ($filtro === 'todas') echo 'selected'; ?>>Todas as Mesas</option>
                        <option value="atendidas" <?php if ($filtro === 'atendidas') echo 'selected'; ?>>Apenas Atendidas</option>
                        <option value="livres" <?php if ($filtro === 'livres') echo 'selected'; ?>>Apenas Não Atendidas</option>
                    </select>
                    <button class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold px-6 py-2 rounded-lg shadow transition" id="btnMesa">+ Adicionar Mesa</button>
                </div>
            </header>
            
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">Garçom</th>
                            <th scope="col" class="px-6 py-3">Nº Mesa</th>
                            <th scope="col" class="px-6 py-3">Nome cliente</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $sql = "SELECT id_mesa, garcom_mesa, n_pedido, nome_cliente, status_mesa FROM mesa" . $whereClause . " ORDER BY id_mesa ASC LIMIT ? OFFSET ?";
                        $stmt = $con->prepare($sql);
                        $stmt->bind_param("ii", $limite_por_pagina, $offset);
                        $stmt->execute();
                        $resultado = $stmt->get_result();

                        if ($resultado->num_rows > 0) {
                            while ($mesa = $resultado->fetch_assoc()) {
                                if ($mesa['status_mesa'] == 0) {
                                    $status_texto = 'Atendido';
                                    $status_classe = 'bg-green-100 text-green-700';
                                } else {
                                    $status_texto = 'Não Atendido';
                                    $status_classe = 'bg-red-100 text-red-700';
                                }
                                echo '<tr class="bg-white border-b hover:bg-gray-50">';
                                echo '<td class="px-6 py-4 font-medium text-gray-900">' . htmlspecialchars($mesa['garcom_mesa'] ?? 'N/A') . '</td>';
                                echo '<td class="px-6 py-4">' . htmlspecialchars($mesa['id_mesa']) . '</td>';
                                echo '<td class="px-6 py-4">' . htmlspecialchars($mesa['nome_cliente'] ?? 'N/A') . '</td>';
                                echo '<td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-xs font-bold ' . $status_classe . '">' . $status_texto . '</span></td>';
                                echo '<td class="px-6 py-4 flex justify-center gap-2">';
                                if ($mesa['status_mesa'] == 0) {
                                    echo '<a href="finalizar_mesa.php?id=' . $mesa['id_mesa'] . '&return_url=index.php" class="btn-finalizar font-medium text-yellow-700 hover:bg-yellow-100 rounded-full p-2 transition" title="Finalizar"><span class="material-symbols-outlined text-yellow-700 text-xl">check_circle</span></a>';
                                } else {
                                    echo '<a href="atender_mesa.php?id=' . $mesa['id_mesa'] . '" class="btn-atender font-medium text-green-700 hover:bg-green-100 rounded-full p-2 transition" title="Atender"><span class="material-symbols-outlined text-green-700 text-xl">restaurant_menu</span></a>';
                                }
                                echo '<a href="editar_mesa.php?id=' . $mesa['id_mesa'] . '" class="btn-editar font-medium text-blue-700 hover:bg-blue-100 rounded-full p-2 transition" title="Editar"><span class="material-symbols-outlined text-blue-700 text-xl">edit</span></a>';
                                echo '<a href="excluir_mesa.php?id=' . $mesa['id_mesa'] . '" class="btn-excluir font-medium text-red-700 hover:bg-red-100 rounded-full p-2 transition" title="Excluir"><span class="material-symbols-outlined text-red-700 text-xl">delete</span></a>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhuma mesa encontrada com este filtro.</td></tr>';
                        }
                        $stmt->close();
                        $con->close();
                    ?>
                    </tbody>
                </table>
            </div>
            <nav class="p-4 flex justify-center">
                <ul class="flex items-center -space-x-px h-8 text-sm">
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li>
                            <a href="index.php?filtro=<?php echo $filtro; ?>&pagina=<?php echo $i; ?>" class="flex items-center justify-center px-3 h-8 leading-tight <?php echo ($i == $pagina_atual) ? 'text-black bg-yellow-400 border border-yellow-400' : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700'; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="transicoes.js" defer></script>
    <script>
        // Restante do seu JavaScript (sem alterações)
    </script>
 </body>
</html>
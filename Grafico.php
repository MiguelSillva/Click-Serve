<?php
session_start();
include 'Bd.php'; // Conexão com o banco de dados

// Verificação de Login
if (!isset($_SESSION['Nome']) || !isset($_SESSION['Senha'])) {
    unset($_SESSION['Nome']);
    unset($_SESSION['Senha']);
    header('Location: login.php');
    exit();
}
$Logado = $_SESSION['Nome'];

// --- CONSULTAS AO BANCO DE DADOS PARA OS KPIs (Continuam focados no dia) ---
$query_faturamento_dia = "SELECT SUM(valor) as total FROM vendas WHERE DATE(data) = CURDATE()";
$result_faturamento_dia = $con->query($query_faturamento_dia);
$faturamento_dia = $result_faturamento_dia->fetch_assoc()['total'] ?? 0;

$query_vendas_dia = "SELECT COUNT(id) as total FROM vendas WHERE DATE(data) = CURDATE()";
$result_vendas_dia = $con->query($query_vendas_dia);
$vendas_dia = $result_vendas_dia->fetch_assoc()['total'] ?? 0;

$query_mesas_ativas = "SELECT COUNT(id_mesa) as total FROM mesa WHERE status_mesa = 0";
$result_mesas_ativas = $con->query($query_mesas_ativas);
$mesas_ativas = $result_mesas_ativas->fetch_assoc()['total'] ?? 0;

$query_ticket_geral = "SELECT AVG(valor) as media FROM vendas";
$result_ticket_geral = $con->query($query_ticket_geral);
$ticket_medio_geral = $result_ticket_geral->fetch_assoc()['media'] ?? 0;


// --- DADOS PARA O GRÁFICO PRINCIPAL (TODOS OS PERÍODOS) ---

// HOJE (por hora)
$labels_hoje = [];
for ($h = 0; $h < 24; $h++) { $labels_hoje[] = str_pad($h, 2, '0', STR_PAD_LEFT) . 'h'; }
$data_hoje = array_fill(0, 24, 0);
$query_hoje = "SELECT HOUR(data) as hora, SUM(valor) as total FROM vendas WHERE DATE(data) = CURDATE() GROUP BY hora";
$result_hoje = $con->query($query_hoje);
while($row = $result_hoje->fetch_assoc()) { $data_hoje[$row['hora']] = $row['total']; }
$labels_hoje_json = json_encode($labels_hoje);
$data_hoje_json = json_encode($data_hoje);

// 7 DIAS
$labels_7_dias = [];
$data_7_dias = [];
for ($i = 6; $i >= 0; $i--) {
    $data = date('Y-m-d', strtotime("-$i days"));
    $labels_7_dias[] = date('d/m', strtotime($data));
    $query_7_dias = "SELECT SUM(valor) as total FROM vendas WHERE DATE(data) = '$data'";
    $result_7_dias = $con->query($query_7_dias);
    $data_7_dias[] = $result_7_dias->fetch_assoc()['total'] ?? 0;
}
$labels_7_dias_json = json_encode($labels_7_dias);
$data_7_dias_json = json_encode($data_7_dias);

// ÚLTIMO MÊS (últimos 30 dias)
$labels_mes = [];
$data_mes = [];
for ($i = 29; $i >= 0; $i--) {
    $data = date('Y-m-d', strtotime("-$i days"));
    $labels_mes[] = date('d/m', strtotime($data));
    $query_mes = "SELECT SUM(valor) as total FROM vendas WHERE DATE(data) = '$data'";
    $result_mes = $con->query($query_mes);
    $data_mes[] = $result_mes->fetch_assoc()['total'] ?? 0;
}
$labels_mes_json = json_encode($labels_mes);
$data_mes_json = json_encode($data_mes);

// ÚLTIMO ANO (por mês)
$labels_ano = [];
$data_ano = array_fill(0, 12, 0);
for ($i = 11; $i >= 0; $i--) {
    $mes_label = date('M/y', strtotime("-$i month"));
    $labels_ano[] = $mes_label;
}
$query_ano = "SELECT DATE_FORMAT(data, '%Y-%m') as mes_ano, SUM(valor) as total FROM vendas WHERE data >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY mes_ano ORDER BY mes_ano ASC";
$result_ano = $con->query($query_ano);
$vendas_por_mes_ano = [];
while($row = $result_ano->fetch_assoc()){ $vendas_por_mes_ano[$row['mes_ano']] = $row['total']; }
$temp_data_ano = [];
for ($i = 11; $i >= 0; $i--) {
    $mes_chave = date('Y-m', strtotime("-$i month"));
    $temp_data_ano[] = $vendas_por_mes_ano[$mes_chave] ?? 0;
}
$data_ano_json = json_encode($temp_data_ano);
$labels_ano_json = json_encode($labels_ano);


// GRÁFICO 2: Top 5 Produtos Mais Vendidos (sem alteração)
$query_top_produtos = "
    SELECT p.nomeProduto, SUM(pp.quantidade) as total_vendido
    FROM pedido_produtos pp
    JOIN produtos p ON pp.id_produto = p.id
    GROUP BY p.nomeProduto
    ORDER BY total_vendido DESC
    LIMIT 5";
$result_top_produtos = $con->query($query_top_produtos);
$labels_produtos = [];
$data_produtos = [];
while ($row = $result_top_produtos->fetch_assoc()) {
    $labels_produtos[] = $row['nomeProduto'];
    $data_produtos[] = $row['total_vendido'];
}
$labels_produtos_json = json_encode($labels_produtos);
$data_produtos_json = json_encode($data_produtos);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <title>Dashboard - Click&Serve</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
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
                    <a href="Grafico.php" class="flex items-center gap-3 px-4 py-2 rounded-lg bg-yellow-100 transition" type="button">
                        <span class="material-icons text-black">dashboard</span>
                        <span class="font-semibold text-black">Dashboard</span>
                        <span class="material-icons ml-auto text-gray-400">chevron_right</span>
                    </a>
                    <a href="Estoque.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-yellow-100 transition" type="button">
                        <span class="material-icons text-black">inventory_2</span>
                        <span class="font-semibold text-black">Estoque</span>
                        <span class="material-icons ml-auto text-gray-400">chevron_right</span>
                    </a>
                    <a href="index.php" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-yellow-100 transition" type="button">
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
            <header class="mb-8">
                <h1 class="text-3xl font-bold text-black">Dashboard</h1>
                <p class="text-gray-500">Olá <?php echo htmlspecialchars($Logado); ?>, veja um resumo do seu negócio.</p>
            </header>

            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-lg p-6 border border-yellow-200">
                    <span class="material-icons text-yellow-500 text-4xl mb-2">paid</span>
                    <p class="text-sm text-gray-500 font-semibold">Faturamento do Dia</p>
                    <p class="text-3xl font-bold text-black mt-1">R$ <?php echo number_format($faturamento_dia, 2, ',', '.'); ?></p>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 border border-yellow-200">
                    <span class="material-icons text-yellow-500 text-4xl mb-2">receipt_long</span>
                    <p class="text-sm text-gray-500 font-semibold">Vendas Realizadas Hoje</p>
                    <p class="text-3xl font-bold text-black mt-1"><?php echo $vendas_dia; ?></p>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 border border-yellow-200">
                    <span class="material-icons text-yellow-500 text-4xl mb-2">table_restaurant</span>
                    <p class="text-sm text-gray-500 font-semibold">Mesas Ativas</p>
                    <p class="text-3xl font-bold text-black mt-1"><?php echo $mesas_ativas; ?></p>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 border border-yellow-200">
                    <span class="material-icons text-yellow-500 text-4xl mb-2">monetization_on</span>
                    <p class="text-sm text-gray-500 font-semibold">Ticket Médio (Geral)</p>
                    <p class="text-3xl font-bold text-black mt-1">R$ <?php echo number_format($ticket_medio_geral, 2, ',', '.'); ?></p>
                </div>
            </section>
            
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6 border border-yellow-200">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                        <h3 id="chartTitle" class="text-xl font-bold text-black">Vendas nos Últimos 7 Dias</h3>
                        <div id="chartFilters" class="flex items-center gap-2 mt-3 sm:mt-0 p-1 bg-gray-100 rounded-lg">
                            <button data-period="today" class="filter-btn px-3 py-1 text-sm font-semibold text-gray-600 rounded-md hover:bg-yellow-200">Hoje</button>
                            <button data-period="7days" class="filter-btn px-3 py-1 text-sm font-semibold text-black bg-yellow-400 rounded-md">7 Dias</button>
                            <button data-period="month" class="filter-btn px-3 py-1 text-sm font-semibold text-gray-600 rounded-md hover:bg-yellow-200">Mês</button>
                            <button data-period="year" class="filter-btn px-3 py-1 text-sm font-semibold text-gray-600 rounded-md hover:bg-yellow-200">Ano</button>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="vendasChart"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 border border-yellow-200">
                    <h3 class="text-xl font-bold text-black mb-4">Top 5 Produtos Vendidos</h3>
                    <div class="h-80">
                         <canvas id="topProdutosChart"></canvas>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const fontStyle = { family: 'Inter' };

        // Armazena todos os dados de vendas pré-carregados
        const salesData = {
            today: {
                labels: <?php echo $labels_hoje_json; ?>,
                data: <?php echo $data_hoje_json; ?>,
                title: 'Vendas de Hoje'
            },
            '7days': {
                labels: <?php echo $labels_7_dias_json; ?>,
                data: <?php echo $data_7_dias_json; ?>,
                title: 'Vendas nos Últimos 7 Dias'
            },
            month: {
                labels: <?php echo $labels_mes_json; ?>,
                data: <?php echo $data_mes_json; ?>,
                title: 'Vendas no Último Mês'
            },
            year: {
                labels: <?php echo $labels_ano_json; ?>,
                data: <?php echo $data_ano_json; ?>,
                title: 'Vendas no Último Ano'
            }
        };

        // Configuração inicial do gráfico de vendas
        const ctxVendas = document.getElementById('vendasChart').getContext('2d');
        const vendasChart = new Chart(ctxVendas, {
            type: 'line',
            data: {
                labels: salesData['7days'].labels,
                datasets: [{
                    label: 'Faturamento',
                    data: salesData['7days'].data,
                    backgroundColor: 'rgba(250, 204, 21, 0.2)',
                    borderColor: 'rgba(250, 204, 21, 1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'rgba(250, 204, 21, 1)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { font: fontStyle, callback: value => 'R$ ' + value } },
                    x: { ticks: { font: fontStyle } }
                }
            }
        });

        // Função para atualizar o gráfico de vendas
        const chartTitle = document.getElementById('chartTitle');
        const filterButtons = document.querySelectorAll('.filter-btn');

        function updateChart(period) {
            const { labels, data, title } = salesData[period];
            
            // Atualiza os dados do gráfico
            vendasChart.data.labels = labels;
            vendasChart.data.datasets[0].data = data;
            vendasChart.update();

            // Atualiza o título
            chartTitle.innerText = title;

            // Atualiza o estilo dos botões
            filterButtons.forEach(button => {
                if (button.dataset.period === period) {
                    button.classList.add('bg-yellow-400', 'text-black');
                    button.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-yellow-200');
                } else {
                    button.classList.remove('bg-yellow-400', 'text-black');
                    button.classList.add('text-gray-600', 'hover:bg-yellow-200');
                }
            });
        }

        // Adiciona o evento de clique aos botões de filtro
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                const period = button.dataset.period;
                updateChart(period);
            });
        });

        // Gráfico 2: Top Produtos (sem alteração na lógica)
        const ctxProdutos = document.getElementById('topProdutosChart').getContext('2d');
        new Chart(ctxProdutos, {
            type: 'bar',
            data: {
                labels: <?php echo $labels_produtos_json; ?>,
                datasets: [{
                    label: 'Quantidade Vendida',
                    data: <?php echo $data_produtos_json; ?>,
                    backgroundColor: [
                        'rgba(250, 204, 21, 1)',
                        'rgba(250, 204, 21, 0.8)',
                        'rgba(250, 204, 21, 0.6)',
                        'rgba(250, 204, 21, 0.4)',
                        'rgba(250, 204, 21, 0.2)',
                    ],
                    borderColor: 'rgba(217, 119, 6, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { font: fontStyle, precision: 0 } },
                    y: { ticks: { font: fontStyle } }
                }
            }
        });
    });
    </script>
</body>
</html>
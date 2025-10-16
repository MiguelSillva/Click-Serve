<?php
session_start();
if (!isset($_SESSION['Nome']) || !isset($_SESSION['Senha'])) {
    header('Location: login.php');
    exit();
}
$Logado = $_SESSION['Nome'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Mesas - Garçom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .action-icon { -webkit-tap-highlight-color: transparent; }
    </style>
</head>
<body class="bg-gray-100">

    <header class="bg-white shadow-md sticky top-0 z-10 p-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img alt="Click&Serve" height="32" src="imagens/click.png" width="32" class="rounded-full"/>
                <h1 class="font-bold text-xl text-black">Mesas</h1>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-gray-800"><?php echo htmlspecialchars($Logado); ?></p>
                <a href="logout.php" class="text-xs text-red-600 font-bold">Sair</a>
            </div>
        </div>
        <div id="datetime-indicator" class="mt-3 text-sm text-gray-500 font-medium"></div>
    </header>

    <main class="p-4 pb-20">
        <div id="grid-mesas" class="grid grid-cols-2 md:grid-cols-3 gap-4 transition-opacity duration-300">
            </div>
    </main>

    <a href="adicionar_mesa.php" title="Adicionar Nova Mesa" class="fixed bottom-6 right-6 bg-yellow-400 text-black p-4 rounded-full shadow-lg hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400 transition transform hover:scale-110 z-20">
        <span class="material-icons">add</span>
    </a>

    <script>
    function confirmFinalizar(event, mesaId) {
        event.preventDefault();
        Swal.fire({
            title: 'Finalizar a Mesa?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, finalizar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // --- LINHA CORRIGIDA ---
                // Agora estamos enviando a informação de que a página de retorno é 'garcom_mesas.php'
                window.location.href = `finalizar_mesa.php?id=${mesaId}&return_url=garcom_mesas.php`;
            }
        });
    }

    async function carregarMesas() {
        const grid = document.getElementById('grid-mesas');
        grid.style.opacity = '0.5'; 

        try {
            const response = await fetch('api_mesas.php');
            const mesas = await response.json();
            grid.innerHTML = '';

            if (mesas.length === 0) {
                grid.innerHTML = '<p class="text-center p-4 text-gray-500 col-span-full">Nenhuma mesa encontrada.</p>';
                return;
            }

            mesas.forEach(mesa => {
                let statusIcon, statusTexto, statusHeaderClasse, acoesHTML, infoExtraHTML;
                if (mesa.status_mesa == 1) { // Mesa Livre
                    statusIcon = 'check';
                    statusTexto = 'Livre';
                    statusHeaderClasse = 'bg-green-500';
                    infoExtraHTML = '<div class="h-10"></div>';
                    acoesHTML = `<a href="atender_mesa_mobile.php?id=${mesa.id_mesa}" class="w-full text-center font-bold py-3 px-3 rounded-lg transition bg-green-600 hover:bg-green-700 text-white shadow-lg">Atender</a>`;
                } else { // Mesa Ocupada
                    statusIcon = 'restaurant_menu';
                    statusTexto = 'Ocupada';
                    statusHeaderClasse = 'bg-yellow-500';
                    infoExtraHTML = `
                        <div class="text-left text-xs text-gray-600 space-y-1 w-full h-10">
                            <p class="flex items-center gap-1 truncate"><span class="material-icons text-sm">person</span> ${mesa.garcom_mesa || 'N/A'}</p>
                            <p class="flex items-center gap-1 truncate"><span class="material-icons text-sm">face</span> ${mesa.nome_cliente || 'N/A'}</p>
                        </div>`;
                    acoesHTML = `
                        <div class="w-full flex justify-center items-center gap-2">
                            <a href="editar_mesa_mobile.php?id=${mesa.id_mesa}" title="Editar Pedido" class="flex-1 flex items-center justify-center gap-1 bg-blue-600 text-white font-bold py-2 px-2 rounded-lg shadow-md hover:bg-blue-700 transition transform hover:scale-105">
                                <span class="material-icons text-base">edit</span>
                                <span class="text-sm">Editar</span>
                            </a>
                            <a href="finalizar_mesa.php?id=${mesa.id_mesa}" onclick="confirmFinalizar(event, ${mesa.id_mesa})" title="Finalizar Mesa" class="flex-1 flex items-center justify-center gap-1 bg-red-600 text-white font-bold py-2 px-2 rounded-lg shadow-md hover:bg-red-700 transition transform hover:scale-105">
                                <span class="material-icons text-base">check_circle</span>
                                <span class="text-sm">Finalizar</span>
                            </a>
                        </div>`;
                }
                const cardMesaHTML = `
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col transform hover:scale-105 transition-transform duration-200">
                        <div class="p-3 flex items-center gap-2 text-white font-bold ${statusHeaderClasse}">
                            <span class="material-icons text-base">${statusIcon}</span>
                            <span class="text-sm">${statusTexto}</span>
                        </div>
                        <div class="p-4 flex-grow flex flex-col items-center justify-between">
                             <h2 class="text-6xl font-extrabold text-gray-800">${mesa.id_mesa}</h2>
                             <p class="text-gray-400 font-medium -mt-2 mb-3">Mesa</p>
                             ${infoExtraHTML}
                             ${acoesHTML}
                        </div>
                    </div>
                `;
                grid.innerHTML += cardMesaHTML;
            });

        } catch (error) {
            console.error("Erro ao carregar mesas:", error);
        } finally {
            grid.style.opacity = '1';
        }
    }
    
    function atualizarDataHora() {
        const agora = new Date();
        const opcoes = { weekday: 'long', hour: '2-digit', minute: '2-digit' };
        document.getElementById('datetime-indicator').textContent = agora.toLocaleDateString('pt-BR', opcoes);
    }

    document.addEventListener('DOMContentLoaded', () => {
        carregarMesas();
        atualizarDataHora();
        setInterval(carregarMesas, 5000);
        setInterval(atualizarDataHora, 60000);

        const urlParams = new URLSearchParams(window.location.search);
        const adicaoStatus = urlParams.get('adicao');
        const finalizacaoStatus = urlParams.get('finalizacao');
        if (adicaoStatus === 'sucesso') {
            Swal.fire({ position: "top-end", icon: "success", title: "Nova mesa adicionada!", showConfirmButton: false, timer: 3000 });
        } else if (adicaoStatus === 'erro') {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Não foi possível adicionar a mesa.' });
        }
        if (finalizacaoStatus === 'sucesso') {
            Swal.fire({ position: "top-end", icon: "success", title: "Mesa finalizada com sucesso!", showConfirmButton: false, timer: 3000 });
        } else if (finalizacaoStatus === 'erro') {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Não foi possível finalizar a mesa.' });
        }
    });
    </script>
</body>
</html>
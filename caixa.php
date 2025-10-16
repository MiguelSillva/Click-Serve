<?php
session_start();
include 'Bd.php';

if (!isset($_SESSION['Nome']) || !isset($_SESSION['Senha'])) {
    header('Location: login.php');
    exit();
}
$Logado = $_SESSION['Nome'];

// Busca todos os produtos com estoque para exibir na tela
$sql_produtos = "SELECT id, nomeProduto, preco, quantidade_em_estoque, categoria_id FROM produtos WHERE quantidade_em_estoque > 0 ORDER BY nomeProduto ASC";
$result_produtos = $con->query($sql_produtos);
$produtos = [];
if ($result_produtos) {
    while($row = $result_produtos->fetch_assoc()) {
        $produtos[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frente de Caixa - Click&Serve</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        @keyframes flash-bg {
            0% { background-color: #fef9c3; }
            100% { background-color: #f9fafb; }
        }
        .highlight-flash {
            animation: flash-bg 0.7s ease-out;
        }
    </style>
</head>
<body class="bg-gray-100 h-screen flex flex-col">

    <header class="bg-white shadow-md p-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <a href="index.php" class="text-gray-600 hover:text-black"><span class="material-icons">arrow_back</span></a>
            <img alt="Click&Serve" height="32" src="imagens/click.png" width="32" class="rounded-full"/>
            <h1 class="font-bold text-xl text-black">Frente de Caixa</h1>
        </div>
        <div class="text-right">
            <p class="text-sm font-semibold text-gray-800"><?php echo htmlspecialchars($Logado); ?></p>
            <a href="logout.php" class="text-xs text-red-600 font-bold">Sair</a>
        </div>
    </header>

    <div class="flex-grow flex p-4 gap-4 overflow-hidden">
        <div class="w-3/5 flex flex-col bg-white rounded-xl shadow-lg border border-yellow-200">
            <div class="p-4 border-b">
                <h2 class="font-bold text-lg">Selecione os Produtos</h2>
                <input type="text" id="searchInput" placeholder="Buscar produto..." class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition">
            </div>
            <div id="product-grid" class="p-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 overflow-y-auto"></div>
        </div>
        <div class="w-2/5 flex flex-col bg-white rounded-xl shadow-lg border border-yellow-200">
            <div class="p-4 border-b"><h2 class="font-bold text-lg">Comanda da Venda</h2></div>
            <div id="cart-items" class="flex-grow p-4 space-y-3 overflow-y-auto"></div>
            <div class="p-4 border-t space-y-4">
                <div class="flex justify-between items-center font-bold text-2xl">
                    <span>TOTAL</span>
                    <span id="cart-total">R$ 0,00</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button id="cancel-sale-btn" class="w-full text-center font-bold py-3 px-3 rounded-lg transition bg-gray-200 hover:bg-gray-300 text-gray-700">Cancelar</button>
                    <button id="finish-sale-btn" class="w-full flex justify-center items-center gap-2 text-center font-bold py-3 px-3 rounded-lg transition bg-yellow-400 hover:bg-yellow-500 text-black shadow-md">
                        <span class="material-icons">check_circle</span>
                        Finalizar Venda
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    const allProducts = <?php echo json_encode($produtos); ?>;
    let cart = [];
    let lastTouchedProductId = null;

    const productGrid = document.getElementById('product-grid');
    const searchInput = document.getElementById('searchInput');
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');
    const finishSaleBtn = document.getElementById('finish-sale-btn');
    const cancelSaleBtn = document.getElementById('cancel-sale-btn');

    function renderProducts(productsToRender) {
        productGrid.innerHTML = '';
        if (productsToRender.length === 0) {
            productGrid.innerHTML = '<p class="col-span-full text-center text-gray-500">Nenhum produto encontrado.</p>';
            return;
        }
        productsToRender.forEach(product => {
            const productCard = `
                <div onclick="addToCart(${product.id})" 
                     class="p-3 bg-white rounded-lg shadow-md border-2 border-transparent hover:border-yellow-400 hover:ring-1 hover:ring-yellow-400 transition-all duration-200 cursor-pointer flex flex-col justify-between text-center">
                    <div>
                        <p class="font-bold text-gray-800 truncate">${product.nomeProduto}</p>
                        <p class="text-sm font-semibold text-gray-600">R$ ${parseFloat(product.preco).toFixed(2)}</p>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Estoque: ${product.quantidade_em_estoque}</p>
                </div>
            `;
            productGrid.innerHTML += productCard;
        });
    }

    function renderCart() {
        if (cart.length === 0) {
            cartItemsContainer.innerHTML = '<p class="text-center text-gray-500 mt-10">Nenhum item adicionado</p>';
            cartTotalElement.textContent = 'R$ 0,00';
            return;
        }

        let total = 0;
        let cartHTML = '';
        cart.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            cartHTML += `
                <div id="cart-item-${item.id}" class="flex items-center gap-3 bg-gray-50 p-2 rounded-lg">
                    <div class="flex-grow">
                        <p class="font-semibold">${item.name}</p>
                        <p class="text-sm text-gray-500">${item.quantity} x R$ ${item.price.toFixed(2)}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="updateQuantity(${index}, -1, ${item.id})" class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 font-bold">-</button>
                        <button onclick="updateQuantity(${index}, 1, ${item.id})" class="w-7 h-7 rounded-full bg-gray-200 hover:bg-gray-300 font-bold">+</button>
                    </div>
                    <p class="font-semibold w-20 text-right">R$ ${itemTotal.toFixed(2)}</p>
                </div>
            `;
        });
        
        cartItemsContainer.innerHTML = cartHTML;
        cartTotalElement.textContent = `R$ ${total.toFixed(2)}`;

        if (lastTouchedProductId) {
            const highlightedElement = document.getElementById(`cart-item-${lastTouchedProductId}`);
            if (highlightedElement) {
                highlightedElement.classList.add('highlight-flash');
                highlightedElement.addEventListener('animationend', () => {
                    highlightedElement.classList.remove('highlight-flash');
                }, { once: true });
            }
            lastTouchedProductId = null;
        }
    }

    function addToCart(productId) {
        lastTouchedProductId = productId;
        
        // --- A CORREÇÃO ESTÁ AQUI ---
        // Usando '==' em vez de '===' para uma comparação mais flexível (número vs texto)
        const product = allProducts.find(p => p.id == productId);

        // Se o produto não for encontrado por algum motivo, encerra a função para evitar erros.
        if (!product) {
            console.error("Produto não encontrado com o ID:", productId);
            return;
        }

        const cartItem = cart.find(item => item.id == productId);
        if (cartItem) {
            cartItem.quantity++;
        } else {
            cart.push({ id: product.id, name: product.nomeProduto, price: parseFloat(product.preco), quantity: 1 });
        }
        renderCart();
    }

    function updateQuantity(index, change, productId) {
        lastTouchedProductId = productId;
        cart[index].quantity += change;
        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        }
        renderCart();
    }

    // Event Listeners (escutadores de eventos)
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const filteredProducts = allProducts.filter(p => p.nomeProduto.toLowerCase().includes(searchTerm));
        renderProducts(filteredProducts);
    });

    cancelSaleBtn.addEventListener('click', () => {
        if (cart.length > 0) {
            Swal.fire({
                title: 'Cancelar Venda?', text: "Todos os itens serão removidos.", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#d33',
                confirmButtonText: 'Sim, cancelar!', cancelButtonText: 'Não'
            }).then((result) => { if (result.isConfirmed) { cart = []; renderCart(); } });
        }
    });

    finishSaleBtn.addEventListener('click', async () => {
        if (cart.length === 0) {
            Swal.fire('Comanda Vazia!', 'Adicione um produto para finalizar a venda.', 'error');
            return;
        }
        const { isConfirmed } = await Swal.fire({
            title: 'Finalizar Venda?', icon: 'question', showCancelButton: true,
            confirmButtonColor: '#facc15', confirmButtonText: 'Sim, finalizar!', cancelButtonText: 'Não',
            customClass: { confirmButton: 'swal-confirm-button' }
        });
        if (isConfirmed) {
            try {
                const response = await fetch('finalizar_venda_caixa.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(cart)
                });
                const result = await response.json();
                if (result.status === 'success') {
                    Swal.fire('Venda Registrada!', result.message, 'success');
                    cart = [];
                    renderCart();
                } else { Swal.fire('Erro!', result.message, 'error'); }
            } catch (error) { Swal.fire('Erro de Conexão!', 'Não foi possível finalizar a venda.', 'error'); }
        }
    });

    // Inicialização da página
    renderProducts(allProducts);
    </script>
</body>
</html>
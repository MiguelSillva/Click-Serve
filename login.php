<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Click&Serve</title>
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Flowbite -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
  <!-- Google Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: url('assets/img/fundo.jpg') no-repeat center center fixed;
      background-size: cover;
    }
    .img-logo { display: block; margin: 0 auto 30px auto; max-width: 200px; }
  </style>
</head>
<body>
  <div class="min-h-screen flex items-center justify-center bg-transparent">
  <div class="w-full max-w-md rounded-2xl shadow-xl border border-yellow-300 p-8" style="background: rgba(255,255,255,0.25); backdrop-filter: blur(8px);">
      <img src="imagens/clicksplash.png" alt="Logo Click&Serve" class="img-logo mb-6">
      <?php
        if (isset($_GET['erro'])) {
          echo '<div class="mb-4 p-3 rounded-lg bg-red-100 border border-black text-black flex items-center gap-2">
            <span class="material-icons text-red-700">error</span>
            Usuário ou senha inválidos!
          </div>';
        }
      ?>
      <form action="ConfirmaLogin.php" method="post" class="space-y-6">
        <div class="relative">
          <span class="material-icons absolute left-3 top-1/2 transform -translate-y-1/2 text-black">person</span>
          <input type="text" id="Login" name="Login" placeholder="Nome de Usuário" required autofocus class="pl-12 pr-4 py-3 rounded-lg border border-black focus:border-black focus:ring-2 focus:ring-black w-full text-black bg-yellow-50 placeholder-black font-semibold">
        </div>
        <div class="relative">
          <span class="material-icons absolute left-3 top-1/2 transform -translate-y-1/2 text-black">lock</span>
          <input type="password" id="Senha" name="Senha" placeholder="Sua Senha" required class="pl-12 pr-4 py-3 rounded-lg border border-black focus:border-black focus:ring-2 focus:ring-black w-full text-black bg-yellow-50 placeholder-black font-semibold">
        </div>
        <button type="submit" name="submit" class="w-full py-3 rounded-lg bg-red-700 hover:bg-red-900 text-black font-bold text-lg flex items-center justify-center gap-2 shadow-md transition-all">
          <span class="material-icons text-black">login</span> Entrar
        </button>
        <div class="flex justify-between items-center text-sm mt-2 text-black">
          <a href="Registrar.php" class="text-black hover:underline font-semibold">Não tem uma conta?</a>
          <a href="#" class="text-black hover:underline font-semibold">Esqueci minha senha</a>
        </div>
      </form>
    </div>
  </div>
    
  <!-- Flowbite JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
</body>
</html>
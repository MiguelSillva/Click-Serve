<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Splash - Click&Serve</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: #fff;
      width: 100vw;
      height: 100vh;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .splash-logo {
      max-width: 80vw;
      max-height: 80vh;
      display: block;
      margin: auto;
      animation: pulse 1.2s infinite;
    }
    @keyframes pulse {
      0% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.08);
      }
      100% {
        transform: scale(1);
      }
    }
  </style>
</head>
<body>
  <img class="splash-logo" alt="Click&Serve" src="imagens/clicksplash.png">
  <script>
    setTimeout(function() {
      window.location.href = 'index.php';
    }, 2200);
  </script>
</body>
</html>
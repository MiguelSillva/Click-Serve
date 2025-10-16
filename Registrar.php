<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cadastro - Click&Serve</title>
  
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    :root {
      --cor-primaria: #b32512;
      --cor-primaria-hover: #851e17;
      --cor-fundo-padrao: #f8fafc;
      --cor-sombra: rgba(0, 0, 0, 0.1);
      --cor-texto: #333;
    }

    body {
      background-color: var(--cor-fundo-padrao);
      background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23e5e7eb' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      font-family: 'Inter', sans-serif;
    }

    .container-login { min-height: 100vh; }
    .login-card {
      background-color: white;
      border-radius: 15px;
      padding: 40px;
      width: 100%;
      max-width: 450px;
      box-shadow: 0 10px 30px var(--cor-sombra);
      border: 1px solid #e5e7eb;
    }
    .img-logo { display: block; margin: 0 auto 30px auto; max-width: 200px; }
    .form-group { position: relative; }
    .form-control { height: 50px; border-radius: 10px; padding-left: 45px; border: 1px solid #ddd; }
    .form-control:focus { border-color: var(--cor-primaria); box-shadow: 0 0 0 0.2rem rgba(179, 37, 18, 0.25); }
    .form-group .form-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #aaa; }
    .btn-login { background-color: var(--cor-primaria); color: white; border: none; padding: 15px; border-radius: 10px; width: 100%; font-size: 16px; font-weight: 600; letter-spacing: 0.5px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(179, 37, 18, 0.3); }
    .btn-login:hover { cursor: pointer; background-color: var(--cor-primaria-hover); transform: translateY(-3px); box-shadow: 0 6px 20px rgba(179, 37, 18, 0.4); }
    .login-links { text-align: center; margin-top: 20px; font-size: 14px; }
    .login-links a { color: var(--cor-primaria); text-decoration: none; transition: opacity 0.2s ease; }
    .login-links a:hover { opacity: 0.8; }

    /* --- Estilo Personalizado para Radio Buttons --- */
    .form-check { position: relative; padding-left: 35px; }
    .form-check-input { position: absolute; opacity: 0; cursor: pointer; }
    .form-check-label::before { content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%); width: 22px; height: 22px; border-radius: 50%; background: #fff; border: 2px solid #ddd; transition: all 0.2s ease; }
    .form-check:hover .form-check-label::before { border-color: var(--cor-primaria); }
    .form-check-label::after { content: ''; position: absolute; left: 5px; top: 50%; transform: translateY(-50%) scale(0); width: 12px; height: 12px; border-radius: 50%; background: var(--cor-primaria); transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1); }
    .form-check-input:checked + .form-check-label::before { border-color: var(--cor-primaria); }
    .form-check-input:checked + .form-check-label::after { transform: translateY(-50%) scale(1); }

    /* --- Animação para o Campo de Código do Gerente --- */
    #campoCodigoGerente {
      overflow: hidden;
      max-height: 0;
      opacity: 0;
      margin-top: 0 !important;
      padding-top: 0;
      padding-bottom: 0;
      border: none;
      transition: all 0.4s ease-in-out;
    }
    #campoCodigoGerente.visible {
      max-height: 100px;
      opacity: 1;
      margin-top: 1rem !important;
    }
  </style>
</head>
<body>
  <div class="container d-flex align-items-center justify-content-center container-login">
    <div class="login-card">
      <img src="imagens/Click.png" alt="Logo Click&Serve" class="img-logo">

      <form id="formCadastro" action="CriaLogin.php" method="post">
        <div class="form-group">
          <i class="fas fa-user form-icon"></i>
          <input type="text" class="form-control" id="Login" name="Login" placeholder="Crie um nome de usuário" required autofocus>
        </div>

        <div class="form-group">
          <i class="fas fa-lock form-icon"></i>
          <input type="password" class="form-control" id="Senha" name="Senha" placeholder="Crie uma senha (mín. 8 caracteres)" required>
        </div>

        <div class="form-group">
          <i class="fas fa-check-circle form-icon"></i>
          <input type="password" class="form-control" id="ConfirmaSenha" name="ConfirmaSenha" placeholder="Confirme sua senha" required>
        </div>

        <div class="form-group text-center mt-4">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="cargo" id="cargoFuncionario" value="funcionario" checked>
                <label class="form-check-label" for="cargoFuncionario">Sou Funcionário</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="cargo" id="cargoGerente" value="gerente">
                <label class="form-check-label" for="cargoGerente">Sou Gerente</label>
            </div>
        </div>

        <div class="form-group" id="campoCodigoGerente">
            <i class="fas fa-key form-icon"></i>
            <input type="password" class="form-control" id="codigoGerente" name="codigo_gerente" placeholder="Código de Gerente">
        </div>

        <button class="btn-login mt-3" type="submit" name="submit">Cadastrar</button>
        
        <div class="login-links">
          <a href="login.php">Já tenho uma conta</a>
        </div>
      </form>
    </div>
  </div>
    
  <script>
    document.getElementById('formCadastro').addEventListener('submit', function(event) {
        const senha = document.getElementById('Senha').value;
        const confirmarSenha = document.getElementById('ConfirmaSenha').value;

        if (senha.length < 8) {
            event.preventDefault();
            Swal.fire({ icon: 'error', title: 'Senha muito curta!', text: 'A senha deve ter pelo menos 8 caracteres.' });
            return;
        }
        if (senha !== confirmarSenha) {
            event.preventDefault();
            Swal.fire({ icon: 'error', title: 'Erro!', text: 'As senhas não coincidem.' });
        }
    });

    document.querySelectorAll('input[name="cargo"]').forEach((radio) => {
        radio.addEventListener('change', function() {
            const campoCodigo = document.getElementById('campoCodigoGerente');
            const inputCodigo = document.getElementById('codigoGerente');

            if (this.value === 'gerente') {
                campoCodigo.classList.add('visible');
                inputCodigo.required = true;
            } else {
                campoCodigo.classList.remove('visible');
                inputCodigo.required = false;
            }
        });
    });
  </script>

  <?php if (isset($_GET['erro']) && $_GET['erro'] == 'usuario_existente'): ?>
    <script type="text/javascript">
        Swal.fire({ icon: "error", title: "Oops...", text: "Esse nome de usuário já existe!" });
    </script>
  <?php endif; ?>
  
  <?php if (isset($_GET['erro']) && $_GET['erro'] == 'codigo_invalido'): ?>
    <script type="text/javascript">
        Swal.fire({ icon: "error", title: "Oops...", text: "O código de gerente informado é inválido!" });
    </script>
  <?php endif; ?>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>
</html>
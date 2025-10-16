<?php
// Inclui a sua conexão com o banco de dados
include "Bd.php";

// 1. Pega os dados do formulário de forma segura
// Usaremos prepared statements, então não precisamos de mysqli_real_escape_string
$NomeUsuario = $_POST['Login'];
$SenhaUsuario = $_POST['Senha'];
$cargo = $_POST['cargo']; // 'funcionario' ou 'gerente'

// 2. Lógica para verificar o código de Gerente (se aplicável)
if ($cargo === 'gerente') {
    $codigo_gerente_digitado = $_POST['codigo_gerente'] ?? '';

    if (empty($codigo_gerente_digitado)) {
        // Se o cargo é gerente mas o código está vazio, retorna erro.
        header('Location: Registrar.php?erro=codigo_invalido');
        exit();
    }

    // Prepara a query para verificar se o código existe na sua tabela 'codigo'
    $sql_verifica_codigo = "SELECT id_codigo FROM codigos WHERE id_codigo = ?";
    $stmt_codigo = $con->prepare($sql_verifica_codigo);
    $stmt_codigo->bind_param("s", $codigo_gerente_digitado);
    $stmt_codigo->execute();
    $resultado_codigo = $stmt_codigo->get_result();

    // Se a contagem de linhas for 0, o código é inválido
    if ($resultado_codigo->num_rows == 0) {
        header('Location: Registrar.php?erro=codigo_invalido');
        exit();
    }
    $stmt_codigo->close();
}

// 3. Verifica se o nome de utilizador já existe (de forma segura)
// Nota: O seu código usa a coluna 'nome', então mantive. Se for 'Login', ajuste aqui.
$sqlCheck = "SELECT nome FROM usuarios WHERE nome = ?";
$stmtCheck = $con->prepare($sqlCheck);
$stmtCheck->bind_param("s", $NomeUsuario);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows > 0) {
    header('Location: Registrar.php?erro=usuario_existente');
    exit();
}
$stmtCheck->close();

// 4. Se tudo estiver correto, CRIA UM HASH DA SENHA (MUITO IMPORTANTE!)
$senha_hash = password_hash($SenhaUsuario, PASSWORD_DEFAULT);

// 5. Insere o novo utilizador com o status 'Pendente' (0) e o cargo correto
$sql_insert = "INSERT INTO usuarios (nome, senha) VALUES (?, ?)";
$stmt_insert = $con->prepare($sql_insert);
// "sss" -> string, string, string (para nome, senha_hash, cargo)
$stmt_insert->bind_param("ss", $NomeUsuario, $senha_hash);

if ($stmt_insert->execute()) {
    // Redireciona para a página de login com uma mensagem de sucesso
    header('Location: login.php?sucesso=cadastro');
    exit();
} else {
    // Se houver um erro na inserção
    echo "Erro ao criar a conta: " . $con->error;
}

$stmt_insert->close();
$con->close();
?>
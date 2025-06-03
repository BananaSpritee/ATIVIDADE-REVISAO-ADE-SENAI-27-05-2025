<?php

require_once('./database/db.php');

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['password'] ?? '';
    $senha2 = $_POST['password2'] ?? '';

    if ($senha !== $senha2) {
        $erro = "As senhas não coincidem.";
    } else {
        // Verifica se email já existe
        $sql = "SELECT COUNT(*) FROM usuario WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $erro = "Este email já está cadastrado.";
        } else {
            // Insere no banco
            $sql = "INSERT INTO usuario (nome_usuario, email, senha) VALUES (:nome, :email, :senha)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senha); // Lembre-se de usar hash no futuro
            if ($stmt->execute()) {
                $sucesso = "Cadastro realizado com sucesso!";
            } else {
                $erro = "Erro ao cadastrar.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Online - Cadastro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="icon" href="./assets/favicon.ico" type="image/x-icon">
</head>

<body class="pagina-cadastro">

    <main class="login-container">

        <form class="formulario-cadastro" method="post" action="">

            <h2 style="text-align:center; margin-bottom:1rem;">Criar Conta</h2>

            <?php if ($erro): ?>
                <p style="color:red;"><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>

            <!-- 🔹 DUAS COLUNAS -->
            <div class="formulario-duas-colunas">
                <!-- Coluna 1 -->
                <div class="coluna-formulario">
                    <label for="name">Nome:</label>
                    <input type="text" name="name" id="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

                    <label for="email">E-mail:</label>
                    <input type="email" name="email" id="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <!-- Coluna 2 -->
                <div class="coluna-formulario">
                    <label for="password">Senha:</label>
                    <input type="password" name="password" id="password" required>

                    <label for="password2">Repita a senha:</label>
                    <input type="password" name="password2" id="password2" required>
                </div>
            </div>

            <button type="submit">Cadastrar-se</button>

                        <?php if ($sucesso): ?>
                <p style="color:green;"><?= htmlspecialchars($sucesso) ?></p>
                <div class="link-cadastro">
                    <a href="./index.php">Fazer Login</a>
                </div>
            <?php endif; ?>

            <div class="link-cadastro">
                <a href="./index.php">Já tenho uma conta</a>
            </div>

        </form>

    </main>

</body>

</html>
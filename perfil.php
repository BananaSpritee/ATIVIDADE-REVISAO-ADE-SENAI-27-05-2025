<?php
session_start();
require_once('./database/db.php');

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$mensagem = "";

// Buscar dados do usuário
$stmt = $conn->prepare("SELECT nome_usuario, email FROM usuario WHERE id_usuario = :id");
$stmt->bindParam(':id', $id_usuario);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se já é organizador
$stmt = $conn->prepare("SELECT * FROM organizadores WHERE id_usuario = :id");
$stmt->bindParam(':id', $id_usuario);
$stmt->execute();
$ehOrganizador = $stmt->rowCount() > 0;

// Atualizar nome
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_nome'])) {
    $novo_nome = trim($_POST['novo_nome']);
    if (!empty($novo_nome)) {
        $stmt = $conn->prepare("UPDATE usuario SET nome_usuario = :nome WHERE id_usuario = :id");
        $stmt->bindParam(':nome', $novo_nome);
        $stmt->bindParam(':id', $id_usuario);
        if ($stmt->execute()) {
            $_SESSION['nome_usuario'] = $novo_nome;
            $mensagem = "Nome atualizado com sucesso.";
        } else {
            $mensagem = "Erro ao atualizar nome.";
        }
    }
}

// Atualizar email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_email'])) {
    $novo_email = trim($_POST['novo_email']);
    if (filter_var($novo_email, FILTER_VALIDATE_EMAIL)) {
        // Verifica se e-mail já existe
        $stmt = $conn->prepare("SELECT COUNT(*) FROM usuario WHERE email = :email AND id_usuario != :id");
        $stmt->bindParam(':email', $novo_email);
        $stmt->bindParam(':id', $id_usuario);
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $stmt = $conn->prepare("UPDATE usuario SET email = :email WHERE id_usuario = :id");
            $stmt->bindParam(':email', $novo_email);
            $stmt->bindParam(':id', $id_usuario);
            if ($stmt->execute()) {
                $_SESSION['email'] = $novo_email;
                $mensagem = "E-mail atualizado com sucesso.";
            } else {
                $mensagem = "Erro ao atualizar e-mail.";
            }
        } else {
            $mensagem = "Este e-mail já está em uso.";
        }
    } else {
        $mensagem = "E-mail inválido.";
    }
}

// Atualizar senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['senha_atual'], $_POST['nova_senha'], $_POST['confirmar_senha'])) {
    $senhaAtual = $_POST['senha_atual'];
    $novaSenha = $_POST['nova_senha'];
    $confirmarSenha = $_POST['confirmar_senha'];

    // Buscar senha atual
    $stmt = $conn->prepare("SELECT senha FROM usuario WHERE id_usuario = :id");
    $stmt->bindParam(':id', $id_usuario);
    $stmt->execute();
    $senhaHash = $stmt->fetchColumn();

    if (!password_verify($senhaAtual, $senhaHash)) {
        $mensagem = "Senha atual incorreta.";
    } elseif ($novaSenha !== $confirmarSenha) {
        $mensagem = "A nova senha e a confirmação não coincidem.";
    } else {
        $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuario SET senha = :senha WHERE id_usuario = :id");
        $stmt->bindParam(':senha', $novaSenhaHash);
        $stmt->bindParam(':id', $id_usuario);
        if ($stmt->execute()) {
            $mensagem = "Senha atualizada com sucesso.";
        } else {
            $mensagem = "Erro ao atualizar senha.";
        }
    }
}

// Tornar-se organizador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tornar_organizador'])) {
    $tipo = $_POST['tipo_organizador'] ?? '';
    $telefone = $_POST['telefone'] ?? '';

    if (!$ehOrganizador && in_array($tipo, ['PF', 'CNPJ']) && !empty($telefone)) {
        $stmt = $conn->prepare("INSERT INTO organizadores (nome_organizador, tipo_organizador, telefone, id_usuario)
                                VALUES (:nome, :tipo, :telefone, :id)");
        $stmt->bindParam(':nome', $_SESSION['nome_usuario']);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':id', $id_usuario);

        if ($stmt->execute()) {
            $mensagem = "Agora você é um organizador!";
            $ehOrganizador = true;
        } else {
            $mensagem = "Erro ao se tornar organizador.";
        }
    } else {
        $mensagem = "Preencha corretamente o tipo e telefone.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Online - Perfil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="icon" href="./assets/favicon.ico" type="image/x-icon">

    <style>

        .container-perfil {
            max-width: 700px;
            margin: 50px auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .container-perfil h2 {
            text-align: center;
            margin-bottom: 2rem;
        }

        form {
            margin-bottom: 2rem;
        }

        input, select {
            width: 100%;
            padding: 0.8rem;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 0.7rem 1.5rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .mensagem {
            text-align: center;
            color: green;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-weight: bold;
            margin-top: 1.5rem;
        }

    </style>

</head>

<body>

<header>

    <nav class="nav-bar">

        <ul class="itens-nav-bar">

            <li><a href="home.php">🏠 Início</a></li>
            <li><a href="eventos.php">🎉 Eventos</a></li>
            <li><a href="criar_evento.php">🛠️ Criar Evento</a></li>
            <li><a href="meus_eventos.php">📅 Meus Eventos</a></li>

            <div class="grupo-direita">

                <li><a href="perfil.php">👤 Perfil</a></li>
                <li><a href="logout.php">🚪 Sair</a></li>

            </div>

        </ul>

        </nav>

</header>

<main>

    <div class="container-perfil">
        <h2>Perfil do Usuário</h2>
    
        <?php if (!empty($mensagem)): ?>
            <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>
    
        <form method="post">
            <label class="section-title">Nome atual: <?= htmlspecialchars($usuario['nome_usuario']) ?></label>
            <input type="text" name="novo_nome" placeholder="Novo nome">
            <button type="submit">Atualizar Nome</button>
        </form>
    
        <form method="post">
            <label class="section-title">E-mail atual: <?= htmlspecialchars($usuario['email']) ?></label>
            <input type="email" name="novo_email" placeholder="Novo e-mail">
            <button type="submit">Atualizar E-mail</button>
        </form>
    
        <form method="post">
            <label class="section-title">Atualizar Senha:</label>
            <input type="password" name="senha_atual" placeholder="Senha atual" required>
            <input type="password" name="nova_senha" placeholder="Nova senha" required>
            <input type="password" name="confirmar_senha" placeholder="Confirmar nova senha" required>
            <button type="submit">Atualizar Senha</button>
        </form>
    
        <?php if (!$ehOrganizador): ?>
        <form method="post">
            <label class="section-title">Quero me tornar organizador:</label>
            <select name="tipo_organizador" required>
                <option value="">Selecione o tipo</option>
                <option value="PF">Pessoa Física</option>
                <option value="CNPJ">Pessoa Jurídica (CNPJ)</option>
            </select>
            <input type="text" name="telefone" placeholder="Telefone com DDD" required>
            <input type="hidden" name="tornar_organizador" value="1">
            <button type="submit">Tornar-se Organizador</button>
        </form>
        <?php else: ?>
            <p style="color: #007bff;">Você já é organizador ✅</p>
        <?php endif; ?>
    </div>

</main>

</body>

</html>
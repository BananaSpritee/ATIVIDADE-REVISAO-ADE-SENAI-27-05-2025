<?php
session_start();
require_once('./database/db.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}

$erro = "";
$sucesso = "";

// Obtém o ID do organizador do usuário logado
$sqlOrganizador = "SELECT id_organizador FROM organizadores WHERE id_usuario = :id_usuario";
$stmt = $conn->prepare($sqlOrganizador);
$stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
$stmt->execute();
$organizador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$organizador) {
    $erro = "Você precisa se cadastrar como organizador antes de criar eventos.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome_evento'] ?? '';
    $local = $_POST['local_evento'] ?? '';
    $endereco = $_POST['endereco_evento'] ?? '';
    $inicio = $_POST['comeco_evento'] ?? '';
    $fim = $_POST['fim_evento'] ?? null;
    $preco = $_POST['preco'] ?? 0.00;
    $id_organizador = $organizador['id_organizador'];

    $imagem_nome = null;

    // Upload de imagem
    if (isset($_FILES['imagem_evento']) && $_FILES['imagem_evento']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagem_evento']['name'], PATHINFO_EXTENSION);
        $imagem_nome = uniqid('evento_', true) . '.' . $ext;
        $destino = __DIR__ . '/uploads/' . $imagem_nome;
        move_uploaded_file($_FILES['imagem_evento']['tmp_name'], $destino);
    }

    // Inserção no banco
    $sql = "INSERT INTO eventos (nome_evento, local_evento, endereco_evento, comeco_evento, fim_evento, preco, id_organizador, imagem_evento)
            VALUES (:nome, :local, :endereco, :comeco, :fim, :preco, :id_organizador, :imagem)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':local', $local);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':comeco', $inicio);
    $stmt->bindParam(':fim', $fim);
    $stmt->bindParam(':preco', $preco);
    $stmt->bindParam(':id_organizador', $id_organizador);
    $stmt->bindParam(':imagem', $imagem_nome);

    if ($stmt->execute()) {
        $sucesso = "Evento criado com sucesso!";
    } else {
        $erro = "Erro ao criar evento.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Eventos Online - Criar Evento</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" />
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="icon" href="./assets/favicon.ico" type="image/x-icon" />

</head>

<body class="pagina-criar-evento">

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

    <main class="form-container">

        <form method="POST" action="criar_evento.php" enctype="multipart/form-data" class="formulario-criar-evento">

            <h2>Criar Novo Evento</h2>

            <?php if ($erro): ?>
                <p style="color: red;"><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>

            <?php if ($sucesso): ?>
                <p style="color: green;"><?= htmlspecialchars($sucesso) ?></p>
            <?php endif; ?>

            <label for="nome_evento">Nome do Evento:</label>
            <input type="text" id="nome_evento" name="nome_evento" required value="<?= htmlspecialchars($_POST['nome_evento'] ?? '') ?>" />

            <label for="local_evento">Local do Evento:</label>
            <input type="text" id="local_evento" name="local_evento" required value="<?= htmlspecialchars($_POST['local_evento'] ?? '') ?>" />

            <label for="endereco_evento">Endereço do Evento:</label>
            <input type="text" id="endereco_evento" name="endereco_evento" required value="<?= htmlspecialchars($_POST['endereco_evento'] ?? '') ?>" />

            <label for="comeco_evento">Data e Hora de Início:</label>
            <input type="datetime-local" id="comeco_evento" name="comeco_evento" required value="<?= htmlspecialchars($_POST['comeco_evento'] ?? '') ?>" />

            <label for="fim_evento">Data e Hora de Término:</label>
            <input type="datetime-local" id="fim_evento" name="fim_evento" value="<?= htmlspecialchars($_POST['fim_evento'] ?? '') ?>" />

            <label for="preco">Preço:</label>
            <input type="number" id="preco" name="preco" step="0.01" value="<?= htmlspecialchars($_POST['preco'] ?? '0.00') ?>" />

            <label for="imagem_evento">Imagem do Evento:</label>
            <input type="file" id="imagem_evento" name="imagem_evento" accept="image/*" required />

            <button type="submit" class="btn-enviar">Criar Evento</button>

        </form>

    </main>

</body>

</html>

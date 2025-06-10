<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}

require_once('./database/db.php');

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // (mesma lógica do POST)
    $nome_evento = $_POST['nome_evento'] ?? '';
    $local_evento = $_POST['local_evento'] ?? '';
    $endereco_evento = $_POST['endereco_evento'] ?? '';
    $comeco_evento = $_POST['comeco_evento'] ?? '';
    $fim_evento = $_POST['fim_evento'] ?? null;
    $preco = $_POST['preco'] ?? 0;

    $id_usuario = $_SESSION['id_usuario'];

    $sql = "SELECT id_organizador FROM organizadores WHERE id_usuario = :id_usuario LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();
    $organizador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$organizador) {
        $erro = "Você precisa ser um organizador para criar eventos.";
    } else {
        $sql = "INSERT INTO eventos 
                (nome_evento, local_evento, endereco_evento, comeco_evento, fim_evento, preco, id_organizador)
                VALUES (:nome_evento, :local_evento, :endereco_evento, :comeco_evento, :fim_evento, :preco, :id_organizador)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome_evento', $nome_evento);
        $stmt->bindParam(':local_evento', $local_evento);
        $stmt->bindParam(':endereco_evento', $endereco_evento);
        $stmt->bindParam(':comeco_evento', $comeco_evento);
        $stmt->bindParam(':fim_evento', $fim_evento);
        $stmt->bindParam(':preco', $preco);
        $stmt->bindParam(':id_organizador', $organizador['id_organizador']);

        if ($stmt->execute()) {
            $sucesso = "Evento criado com sucesso!";
        } else {
            $erro = "Erro ao criar evento.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Online - Criar Evento</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="icon" href="./assets/favicon.ico" type="image/x-icon">

    <style>
        /* Container centralizado com largura máxima e padding */
        .container {
            max-width: 600px;
            margin: 40px auto; /* centraliza e dá margem superior/baixo */
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        form label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #555;
        }

        form input[type="text"],
        form input[type="datetime-local"],
        form input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }

        form button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
        }

        .error {
            color: #d9534f;
        }

        .success {
            color: #28a745;
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

<div class="container">
    <h1>Criar Evento</h1>

    <?php if ($erro): ?>
        <p class="message error"><?=htmlspecialchars($erro)?></p>
    <?php elseif ($sucesso): ?>
        <p class="message success"><?=htmlspecialchars($sucesso)?></p>
    <?php endif; ?>

    <form method="post">
        <label for="nome_evento">Nome do Evento:</label>
        <input type="text" id="nome_evento" name="nome_evento" required>

        <label for="local_evento">Local:</label>
        <input type="text" id="local_evento" name="local_evento" required>

        <label for="endereco_evento">Endereço:</label>
        <input type="text" id="endereco_evento" name="endereco_evento" required>

        <label for="comeco_evento">Data e Hora Início:</label>
        <input type="datetime-local" id="comeco_evento" name="comeco_evento" required>

        <label for="fim_evento">Data e Hora Fim:</label>
        <input type="datetime-local" id="fim_evento" name="fim_evento">

        <label for="preco">Preço (R$):</label>
        <input type="number" id="preco" name="preco" min="0" step="0.01" value="0.00">

        <button type="submit">Criar Evento</button>
    </form>
</div>

</body>
</html>

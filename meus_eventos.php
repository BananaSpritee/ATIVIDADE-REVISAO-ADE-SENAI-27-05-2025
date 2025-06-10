<?php
session_start();
require_once('./database/db.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$mensagem = "";

// Verifica se o usuário é um organizador
$stmt = $conn->prepare("SELECT id_organizador FROM organizadores WHERE id_usuario = :id");
$stmt->bindParam(':id', $id_usuario);
$stmt->execute();
$organizador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$organizador) {
    $mensagem = "Você ainda não é um organizador. Vá até a página de perfil para se tornar um.";
} else {
    $id_organizador = $organizador['id_organizador'];

    // Excluir evento
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_evento'])) {
        $id_evento = $_POST['id_evento'];

        $stmt = $conn->prepare("DELETE FROM eventos WHERE id_evento = :id AND id_organizador = :id_org");
        $stmt->bindParam(':id', $id_evento);
        $stmt->bindParam(':id_org', $id_organizador);
        if ($stmt->execute()) {
            $mensagem = "Evento excluído com sucesso.";
        } else {
            $mensagem = "Erro ao excluir evento.";
        }
    }

    // Buscar eventos do organizador
    $stmt = $conn->prepare("SELECT * FROM eventos WHERE id_organizador = :id ORDER BY comeco_evento DESC");
    $stmt->bindParam(':id', $id_organizador);
    $stmt->execute();
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Online - Meus Eventos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="icon" href="./assets/favicon.ico" type="image/x-icon">

    <style>
        .container-eventos {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.1);
        }

        .evento {
            border-bottom: 1px solid #ccc;
            padding: 1rem 0;
        }

        .evento:last-child {
            border-bottom: none;
        }

        .botoes {
            margin-top: 10px;
        }

        .botoes form {
            display: inline;
        }

        .botoes button {
            margin-right: 10px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .mensagem {
            color: green;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        h1 {
            text-align: center;
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

    <div class="container-eventos">
        <h1>Meus Eventos</h1>
    
        <?php if (!empty($mensagem)): ?>
            <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>
    
        <?php if (isset($eventos) && count($eventos) > 0): ?>
            <?php foreach ($eventos as $evento): ?>
                <div class="evento">
                    <h3><?= htmlspecialchars($evento['nome_evento']) ?></h3>
                    <p><strong>Local:</strong> <?= htmlspecialchars($evento['local_evento']) ?></p>
                    <p><strong>Endereço:</strong> <?= htmlspecialchars($evento['endereco_evento']) ?></p>
                    <p><strong>Início:</strong> <?= date('d/m/Y H:i', strtotime($evento['comeco_evento'])) ?></p>
                    <p><strong>Fim:</strong> <?= $evento['fim_evento'] ? date('d/m/Y H:i', strtotime($evento['fim_evento'])) : 'Não informado' ?></p>
                    <p><strong>Preço:</strong> R$ <?= number_format($evento['preco'], 2, ',', '.') ?></p>
    
                    <div class="botoes">
                        <form action="editar_evento.php" method="get">
                            <input type="hidden" name="id_evento" value="<?= $evento['id_evento'] ?>">
                            <button type="submit">✏️ Editar</button>
                        </form>
    
                        <form method="post" onsubmit="return confirm('Tem certeza que deseja excluir este evento?');">
                            <input type="hidden" name="id_evento" value="<?= $evento['id_evento'] ?>">
                            <input type="hidden" name="excluir_evento" value="1">
                            <button type="submit" style="background-color: #dc3545; color: white;">🗑️ Excluir</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif (isset($eventos)): ?>
            <p style="text-align: center;">Você ainda não criou nenhum evento.</p>
        <?php endif; ?>
    </div>

</main>


</body>

</html>
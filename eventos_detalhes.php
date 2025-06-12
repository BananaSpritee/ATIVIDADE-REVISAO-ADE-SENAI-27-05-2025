<?php
session_start();
require_once('./database/db.php');

// Validação do ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do evento não especificado.");
}
$id_evento = intval($_GET['id']);

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Buscar dados do evento
$stmt = $conn->prepare("SELECT * FROM eventos WHERE id_evento = :id");
$stmt->bindParam(':id', $id_evento);
$stmt->execute();
$evento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$evento) {
    die("Evento não encontrado.");
}

// CURTIR evento
if (isset($_POST['curtir'])) {
    $stmt = $conn->prepare("SELECT * FROM curtidas WHERE id_evento = :id_evento AND id_usuario = :id_usuario");
    $stmt->bindParam(':id_evento', $id_evento);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        $stmt = $conn->prepare("INSERT INTO curtidas (id_evento, id_usuario) VALUES (:id_evento, :id_usuario)");
        $stmt->bindParam(':id_evento', $id_evento);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
    }
}

// COMENTAR evento
if (isset($_POST['comentario']) && trim($_POST['comentario']) != '') {
    $comentario = trim($_POST['comentario']);
    $stmt = $conn->prepare("INSERT INTO comentarios (comentario, id_evento, id_usuario) VALUES (:comentario, :id_evento, :id_usuario)");
    $stmt->bindParam(':comentario', $comentario);
    $stmt->bindParam(':id_evento', $id_evento);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();
}

// Buscar número total de likes
$stmt = $conn->prepare("SELECT COUNT(*) FROM curtidas WHERE id_evento = :id_evento");
$stmt->bindParam(':id_evento', $id_evento);
$stmt->execute();
$total_likes = $stmt->fetchColumn();

// Verificar se usuário já curtiu
$stmt = $conn->prepare("SELECT * FROM curtidas WHERE id_evento = :id_evento AND id_usuario = :id_usuario");
$stmt->bindParam(':id_evento', $id_evento);
$stmt->bindParam(':id_usuario', $id_usuario);
$stmt->execute();
$ja_curtiu = $stmt->rowCount() > 0;

// Buscar comentários
$stmt = $conn->prepare("
    SELECT c.comentario, c.created_at, u.nome_usuario 
    FROM comentarios c 
    JOIN usuario u ON c.id_usuario = u.id_usuario
    WHERE c.id_evento = :id_evento
    ORDER BY c.created_at DESC
");
$stmt->bindParam(':id_evento', $id_evento);
$stmt->execute();
$comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($evento['nome_evento']) ?> - Detalhes do Evento</title>

    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="./css/style.css">
    <link rel="icon" href="./assets/favicon.ico" type="image/x-icon">

</head>
<body>

<header>
    <nav class="nav-bar">
        <ul class="itens-nav-bar">
            <li><a href="home.php">🏠 Início</a></li>
            <li><a href="criar_evento.php">🛠️ Criar Evento</a></li>
            <li><a href="meus_eventos.php">📅 Meus Eventos</a></li>
            <div class="grupo-direita">
                <li><a href="perfil.php">👤 Perfil</a></li>
                <li><a href="logout.php">🚪 Sair</a></li>
            </div>
        </ul>
    </nav>
</header>

<main class="meus-eventos-container" style="max-width: 700px; margin: 2rem auto;">
    <h2><?= htmlspecialchars($evento['nome_evento']) ?></h2>
    <img src="<?= htmlspecialchars($evento['imagem_evento'] ?? './assets/default.jpg') ?>" alt="Imagem do Evento" style="width:100%; max-height: 300px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;">
    
    <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($evento['descricao'] ?? 'Sem descrição.')) ?></p>
    <p><strong>Data e Hora:</strong> <?= htmlspecialchars($evento['comeco_evento']) ?></p>
    <p><strong>Local:</strong> <?= htmlspecialchars($evento['local_evento']) ?></p>
    <p><strong>Valor:</strong> R$ <?= number_format($evento['preco'], 2, ',', '.') ?></p>

    <form method="post" style="margin-bottom: 1rem;">
        <button type="submit" name="curtir" <?= $ja_curtiu ? 'disabled style="background:#ccc; cursor:default;"' : '' ?>>
            👍 Curtir (<?= $total_likes ?>)
        </button>
    </form>

    <section>
        <h3>Comentários (<?= count($comentarios) ?>)</h3>

        <form method="post" style="margin-bottom: 1rem;">
            <textarea name="comentario" rows="3" required placeholder="Escreva seu comentário..." style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;"></textarea>
            <button type="submit" style="margin-top: 0.5rem;">Comentar</button>
        </form>

        <?php if (count($comentarios) === 0): ?>
            <p>Nenhum comentário ainda. Seja o primeiro!</p>
        <?php else: ?>
            <?php foreach ($comentarios as $com): ?>
                <div style="border-bottom: 1px solid #ddd; margin-bottom: 8px; padding-bottom: 8px;">
                    <p><strong><?= htmlspecialchars($com['nome_usuario']) ?></strong> - <small><?= date('d/m/Y H:i', strtotime($com['created_at'])) ?></small></p>
                    <p><?= nl2br(htmlspecialchars($com['comentario'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

</body>
</html>

<?php
session_start();
require_once('./database/db.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID do evento não especificado.");
}
$id_evento = intval($_GET['id']);
echo "ID do evento recebido: " . $id_evento;
exit;

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_evento = $_GET['id'] ?? null;

if (!$id_evento) {
    die("Evento não especificado.");
}

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
    // Verifica se já curtiu
    $stmt = $conn->prepare("SELECT * FROM evento_likes WHERE id_evento = :id_evento AND id_usuario = :id_usuario");
    $stmt->bindParam(':id_evento', $id_evento);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        // Inserir like
        $stmt = $conn->prepare("INSERT INTO evento_likes (id_evento, id_usuario) VALUES (:id_evento, :id_usuario)");
        $stmt->bindParam(':id_evento', $id_evento);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();
    }
}

// COMENTAR evento
if (isset($_POST['comentario']) && trim($_POST['comentario']) != '') {
    $comentario = trim($_POST['comentario']);
    $stmt = $conn->prepare("INSERT INTO evento_comentarios (id_evento, id_usuario, comentario) VALUES (:id_evento, :id_usuario, :comentario)");
    $stmt->bindParam(':id_evento', $id_evento);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':comentario', $comentario);
    $stmt->execute();
}

// Buscar número total de likes
$stmt = $conn->prepare("SELECT COUNT(*) FROM evento_likes WHERE id_evento = :id_evento");
$stmt->bindParam(':id_evento', $id_evento);
$stmt->execute();
$total_likes = $stmt->fetchColumn();

// Verificar se usuário já curtiu
$stmt = $conn->prepare("SELECT * FROM evento_likes WHERE id_evento = :id_evento AND id_usuario = :id_usuario");
$stmt->bindParam(':id_evento', $id_evento);
$stmt->bindParam(':id_usuario', $id_usuario);
$stmt->execute();
$ja_curtiu = $stmt->rowCount() > 0;

// Buscar comentários
$stmt = $conn->prepare("
    SELECT c.comentario, c.data_comentario, u.nome_usuario 
    FROM evento_comentarios c 
    JOIN usuario u ON c.id_usuario = u.id_usuario
    WHERE c.id_evento = :id_evento
    ORDER BY c.data_comentario DESC
");
$stmt->bindParam(':id_evento', $id_evento);
$stmt->execute();
$comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Detalhes do Evento - <?= htmlspecialchars($evento['nome_evento']) ?></title>
    <link rel="stylesheet" href="./css/style.css" />
</head>
<body>

<header>
    <nav class="nav-bar">
        <ul class="itens-nav-bar">
            <li><a href="home.php">🏠 Início</a></li>
            <li><a href="eventos.php">🎉 Eventos</a></li>
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
    <img src="<?= htmlspecialchars($evento['imagem_url'] ?? './assets/default.jpg') ?>" alt="Imagem do Evento" style="width:100%; max-height: 300px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;">
    
    <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($evento['descricao'])) ?></p>
    <p><strong>Data e Hora:</strong> <?= htmlspecialchars($evento['data_evento']) ?></p>
    <p><strong>Local:</strong> <?= htmlspecialchars($evento['local']) ?></p>
    <p><strong>Valor:</strong> R$ <?= number_format($evento['valor'], 2, ',', '.') ?></p>

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
                    <p><strong><?= htmlspecialchars($com['nome_usuario']) ?></strong> - <small><?= date('d/m/Y H:i', strtotime($com['data_comentario'])) ?></small></p>
                    <p><?= nl2br(htmlspecialchars($com['comentario'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

</main>

</body>
</html>

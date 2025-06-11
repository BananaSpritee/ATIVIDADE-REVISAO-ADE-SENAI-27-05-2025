<?php
session_start();
require_once('./database/db.php');

if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$msg = "";

// Exclusão do evento via GET
if (isset($_GET['excluir'])) {
    $id_evento = intval($_GET['excluir']);

    $stmtOrg = $conn->prepare("SELECT id_organizador FROM organizadores WHERE id_usuario = ?");
    $stmtOrg->bindParam(1, $id_usuario);
    $stmtOrg->execute();
    $id_organizador = $stmtOrg->fetchColumn();

    if ($id_organizador) {
        $stmtDel = $conn->prepare("DELETE FROM eventos WHERE id_evento = ? AND id_organizador = ?");
        $stmtDel->bindParam(1, $id_evento, PDO::PARAM_INT);
        $stmtDel->bindParam(2, $id_organizador, PDO::PARAM_INT);
        $stmtDel->execute();

        if ($stmtDel->rowCount() > 0) {
            $msg = "Evento excluído com sucesso!";
        } else {
            $msg = "Evento não encontrado ou você não tem permissão para excluir.";
        }
    } else {
        $msg = "Organizador não encontrado.";
    }
}

// Buscar eventos do usuário
$stmtOrg = $conn->prepare("SELECT id_organizador FROM organizadores WHERE id_usuario = ?");
$stmtOrg->bindParam(1, $id_usuario);
$stmtOrg->execute();
$id_organizador = $stmtOrg->fetchColumn();

$eventos = [];
if ($id_organizador) {
    $stmt = $conn->prepare("SELECT * FROM eventos WHERE id_organizador = ? ORDER BY comeco_evento DESC");
    $stmt->bindParam(1, $id_organizador);
    $stmt->execute();
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Eventos Online - Meus Eventos</title>
<link rel="stylesheet" href="./css/style.css" />
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

<main class="tela-inicial">

    <h1>Meus Eventos</h1>

    <?php if ($msg): ?>

        <div class="msg-sucesso"><?= htmlspecialchars($msg) ?></div>

    <?php endif; ?>

    <?php if (!$eventos): ?>

        <p>Você não tem eventos cadastrados.</p>

    <?php else: ?>

        <div class="eventos-grid">

            <?php foreach ($eventos as $evento): ?>

                <div class="card-evento">

                    <h3><?= htmlspecialchars($evento['nome_evento']) ?></h3>
                    <p><strong>Local:</strong> <?= htmlspecialchars($evento['local_evento']) ?></p>
                    <p><strong>Início:</strong> <?= date('d/m/Y H:i', strtotime($evento['comeco_evento'])) ?></p>
                    <p><strong>Fim:</strong> <?= $evento['fim_evento'] ? date('d/m/Y H:i', strtotime($evento['fim_evento'])) : 'Não informado' ?></p>
                    <p><strong>Preço:</strong> R$ <?= number_format($evento['preco'], 2, ',', '.') ?></p>

                    <a href="editar_evento.php?id=<?= $evento['id_evento'] ?>" class="btn-editar">Editar</a>
                    <a href="meus_eventos.php?excluir=<?= $evento['id_evento'] ?>" 
                       onclick="return confirm('Tem certeza que deseja excluir este evento?');" 
                       class="btn-excluir">Excluir
                    </a>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</main>

</body>

</html>

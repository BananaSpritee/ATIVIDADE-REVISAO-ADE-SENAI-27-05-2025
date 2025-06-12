<?php

session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php'); // redireciona pro login
    exit;
}

require_once('./database/db.php');

// Pegar eventos recentes (exemplo: últimos 5)
$sql = "SELECT eventos.*, organizadores.nome_organizador 
        FROM eventos 
        JOIN organizadores ON eventos.id_organizador = organizadores.id_organizador
        ORDER BY comeco_evento DESC LIMIT 5";

$stmt = $conn->prepare($sql);
$stmt->execute();
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Online - Home</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="icon" href="./assets/favicon.ico" type="image/x-icon">

</head>

<body class="pagina-home">

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

    <main>

        <div class="tela-inicial">

            <h1>Bem-vindo(a), <?= htmlspecialchars($_SESSION['nome_usuario']) ?>!</h1>

            <h2>Eventos Recentes</h2>

            <?php if (count($eventos) === 0): ?>

                <p>Nenhum evento cadastrado ainda.</p>

            <?php else: ?>

                <div class="eventos-grid">

                    <?php foreach ($eventos as $evento): ?>

                        <div class="card-evento">

                            <?php if (!empty($evento['imagem_evento'])): ?>

                                <img src="uploads/<?= htmlspecialchars($evento['imagem_evento']) ?>" alt="Imagem do evento">

                            <?php endif; ?>

                            <h3><?= htmlspecialchars($evento['nome_evento']) ?></h3>

                            <p><strong>Organizador:</strong> <?= htmlspecialchars($evento['nome_organizador']) ?></p>

                            <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($evento['comeco_evento'])) ?></p>

                            <p><strong>Local:</strong> <?= htmlspecialchars($evento['local_evento']) ?></p>

                            <a href="/ATIVIDADE-REVISAO-ADE-SENAI-27-05-2025/eventos_detalhes.php?id=<?= $evento['id_evento'] ?>">Ver detalhes</a>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

            <h2>Pesquisar Eventos</h2>

            <form method="GET" action="home.php" style="margin-bottom: 20px;">

                <input type="text" name="busca" placeholder="Digite o nome do evento" value="<?= isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : '' ?>">
                <button type="submit">🔍 Pesquisar</button>
                <a href="home.php"><button type="button">🔄 Mostrar Todos</button></a>

            </form>

            <?php
            $busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
            $sqlTodos = "SELECT eventos.*, organizadores.nome_organizador 
             FROM eventos 
             JOIN organizadores ON eventos.id_organizador = organizadores.id_organizador";

            if (!empty($busca)) {
                $sqlTodos .= " WHERE eventos.nome_evento LIKE :busca";
            }

            $sqlTodos .= " ORDER BY comeco_evento DESC";
            $stmtTodos = $conn->prepare($sqlTodos);

            if (!empty($busca)) {
                $stmtTodos->bindValue(':busca', "%$busca%", PDO::PARAM_STR);
            }

            $stmtTodos->execute();
            $eventosTodos = $stmtTodos->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <h2>Todos os Eventos</h2>

            <?php if (count($eventosTodos) === 0): ?>

                <p>Nenhum evento encontrado.</p>

            <?php else: ?>

                <div class="eventos-grid">

                    <?php foreach ($eventosTodos as $evento): ?>

                        <div class="card-evento">

                            <?php if (!empty($evento['imagem_evento'])): ?>

                                <img src="uploads/<?= htmlspecialchars($evento['imagem_evento']) ?>" alt="Imagem do evento">

                            <?php endif; ?>

                            <h3><?= htmlspecialchars($evento['nome_evento']) ?></h3>

                            <p><strong>Organizador:</strong> <?= htmlspecialchars($evento['nome_organizador']) ?></p>
                            <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($evento['comeco_evento'])) ?></p>
                            <p><strong>Local:</strong> <?= htmlspecialchars($evento['local_evento']) ?></p>
                            <a href="eventos_detalhes.php?id=<?= $evento['id_evento'] ?>">Ver detalhes</a>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>

    </main>

    <footer class="footer">

        <div class="footer-container">

            <div class="footer-section">

                <h4>Sobre Nós</h4>
                <p>Somos uma plataforma dedicada a conectar pessoas a eventos incríveis e experiências únicas.</p>

            </div>

            <div class="footer-section">

                <h4>Links Úteis</h4>
                <ul>
                    <li><a href="#">Início</a></li>
                    <li><a href="#">Eventos</a></li>
                    <li><a href="#">Contato</a></li>
                    <li><a href="#">Ajuda</a></li>
                </ul>

            </div>

            <div class="footer-section">

                <h4>Contato</h4>
                <p>Email: contato@seusite.com</p>
                <p>Telefone: (11) 1234-5678</p>
                <p>Endereço: Rua Exemplo, 123, Cidade</p>

            </div>

        </div>

        <div class="footer-bottom">

            <p>© 2025 SeuSite. Todos os direitos reservados.</p>

        </div>

    </footer>

</body>

</html>
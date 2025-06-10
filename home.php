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

        <div class="tela-inicial">

            <h1>Bem-vindo(a), <?=htmlspecialchars($_SESSION['nome_usuario'])?>!</h1>
            
            <h2>Eventos Recentes</h2>
            
            <?php if (count($eventos) === 0): ?>

                <p>Nenhum evento cadastrado ainda.</p>

                <?php else: ?>
                    
                    <ul>
                        
                        <?php foreach ($eventos as $evento): ?>
                            
                            <li>
                                
                                <strong><?=htmlspecialchars($evento['nome_evento'])?></strong><br/>
                                Organizador: <?=htmlspecialchars($evento['nome_organizador'])?><br/>
                                Data: <?=date('d/m/Y H:i', strtotime($evento['comeco_evento']))?><br/>
                                Local: <?=htmlspecialchars($evento['local_evento'])?>
                                <br/><a href="evento_detalhes.php?id=<?= $evento['id_evento'] ?>">Ver detalhes</a>
                                
                            </li>
                            
                        <?php endforeach; ?>
                            
                    </ul>

                <?php endif; ?>
                        
        </div>

        </main>

    <footer>
        
    </footer>

</body>

</html>
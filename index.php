<?php

require("./database/db.php");

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Online - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="icon" href="./assets/favicon.ico" type="image/x-icon">

</head>

<body class="pagina-login">

    <header>

    </header>

        <main class="login-container">

            <div class="formulario-login">

                <form action="./functions/loginDB.php" method="post">

                <h2 style="text-align:center; margin-bottom:1rem;">Entrar</h2>

                    <label for="email">E-mail:</label><br>
                    <input name="email_login" id="email" type="email" placeholder=""><br>

                    <label for="password">Senha:</label><br>
                    <input name="password_login" id="password" type="password" placeholder=""><br>

                    <button type="submit">Login</button>

                    <div class="link-cadastro">

                        <!-- <a href="/esqueci-senha">Esqueci a senha</a> |  -->
                        <a href="./cadastro.php">Não tenho login, cadastrar-se</a>

                    </div>

                </form>

            </div>

        </main>

    <footer>

    </footer>

</body>

</html>
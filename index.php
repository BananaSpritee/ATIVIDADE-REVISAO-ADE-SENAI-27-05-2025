<?php

require("./database/db.php");

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Online - Login</title>

</head>

<body>

    <header>

    </header>

        <main>
            <box class="formulario-login">

                <form action="" method="post">

                    <label for="email">E-mail:</label>
                    <input name="email_login" id="email" type="email" placeholder="">

                    <label for="password">Senha:</label>
                    <input name="password_login" id="password" type="password" placeholder="">

                    <button type="submit">Login</button>

                    <div style="margin-top: 10px;">

                        <!-- <a href="/esqueci-senha">Esqueci a senha</a> |  -->
                        <a href="./cadastro.php">Não tenho login, cadastrar-se</a>

                    </div>

                </form>

            </box>

        </main>

    <footer>

    </footer>
</body>
</html>
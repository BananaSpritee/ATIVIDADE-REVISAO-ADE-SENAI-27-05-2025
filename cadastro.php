<?php

require_once("./functions/cadastroDB.php");

// if ($_SERVER["REQUEST_METHOD"] == "POST") {

//     if (isset($_POST["acao"])) {

//         switch ($_POST["acao"]) {

//             case "cadastro":
//                 require "./functions/cadastroDB.php";
//                 break;

//         }
//     }
// }

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Online - Cadastro</title>
    <script>
        function validarFormulario(event) {
            const senha = document.getElementById("password").value;
            const confirmarSenha = document.getElementById("passsword2").value;

            if (senha !== confirmarSenha) {
                alert("As senhas não coincidem!");
                event.preventDefault(); // Impede o envio do formulário
            } else {
                alert("Cadastro criado com sucesso!")
            }
        }
    </script>

</head>

<body>

    <header>

    </header>

        <main>

            <form action="./index.php" method="post" onsubmit="validarFormulario(event)">

                <input type="hidden" name="acao" value="cadastro">

                <label for="name">Digite seu nome:</label>
                <input type="text" id="name" name="name" placeholder="">
                
                <label for="password">Digite seu e-mail:</label>
                <input type="email" id="email" name="email" placeholder="">

                <label for="password">Digite a sua senha:</label>
                <input type="password" id="password" name="password" placeholder="">

                <label for="password2">Repita a senha:</label>
                <input type="password" id="password2" name="password2" placeholder="">

                <button type="submit">Cadastrar-se</button>

            </form>

        </main>

    <footer>

    </footer>

</body>
</html>
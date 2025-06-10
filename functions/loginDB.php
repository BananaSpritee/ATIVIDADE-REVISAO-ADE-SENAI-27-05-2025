<?php

require_once("../database/db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email_login'], $_POST['password_login'])) {

    $email = $_POST['email_login'];
    $senha = $_POST['password_login'];

    // Busca o usuário pelo email
    $sql = "SELECT id_usuario, nome_usuario, email, senha FROM usuario WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {

        // Verifica se a senha bate com o hash armazenado
        if (password_verify($senha, $usuario['senha'])) {

            // Sucesso no login, inicia sessão
            session_start();

            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nome_usuario'] = $usuario['nome_usuario'];
            $_SESSION['email'] = $usuario['email'];

            // Redireciona para página principal (dashboard ou home)
            header("Location: /ATIVIDADE-REVISAO-ADE-SENAI-27-05-2025/home.php");
            exit;

        } else {

            // Senha incorreta
            echo "Senha incorreta!";

        }

    } else {

        // Usuário não encontrado
        echo "Usuário não encontrado!";

    }
}
?>

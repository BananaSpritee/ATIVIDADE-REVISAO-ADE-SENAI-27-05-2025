<?php

require_once("/laragon/www/ATIVIDADE-REVISAO-ADE-SENAI-27-05-2025/database/db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'], $_POST['email'], $_POST['password'])) {

    $nome_usuario = $_POST["name"];
    $email_usuario = $_POST["email"];
    $senha_usuario = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Verifica se o email já existe
    $check = $conn->prepare("SELECT COUNT(*) FROM usuario WHERE email = :email");
    $check->bindParam(':email', $email_usuario);
    $check->execute();

    if ($check->fetchColumn() > 0) {
        echo "E-mail já cadastrado. Por favor, use outro e-mail.";
        exit;
    }

    // Insere o usuário
    $sql = "INSERT INTO usuario (nome_usuario, email, senha) VALUES (:nome_usuario, :email, :senha)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':nome_usuario', $nome_usuario);
    $stmt->bindParam(':email', $email_usuario);
    $stmt->bindParam(':senha', $senha_usuario);

    $result = $stmt->execute();

    if ($result) {
        // Redireciona para tela de login
        header("Location: /ATIVIDADE-REVISAO-ADE-SENAI-27-05-2025/index.php");
        exit;
    } else {
        echo "Erro ao cadastrar usuário.";
    }
}
?>

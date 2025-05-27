<?php

require("./database/db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'], $_POST['email'], $_POST['password'])) {

    $sql = ("INSERT INTO usuario (nome_usuario, email, senha) VALUES (:nome_usuario, :email, :senha)");
    $stmt = $conn->prepare($sql);

    $nome_usuario = $_POST["name"];
    $email_usuario = $_POST["email"];
    $senha_usuario = $_POST["password"];

    $stmt->bindParam(':nome_usuario', $nome_usuario);
    $stmt->bindParam(':email', $email_usuario);
    $stmt->bindParam(':senha', $senha_usuario);

    $stmt->execute();
}

?>
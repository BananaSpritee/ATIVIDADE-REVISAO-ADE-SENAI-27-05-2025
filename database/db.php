<?php

$host = "localhost";
$db = "eventos_online";
$user = "root";
$pass = "";

$conn = new PDO("mysql:host=$host", $user, $pass);

$conn->query("CREATE DATABASE IF NOT EXISTS eventos_online");

$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);

$conn->query("CREATE TABLE IF NOT EXISTS usuario (
            id_usuario INT PRIMARY KEY AUTO_INCREMENT UNIQUE,
            nome_usuario VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            senha VARCHAR(255) NOT NULL
            );
            ");

$conn->query("CREATE TABLE IF NOT EXISTS organizadores (
            id_organizador INT PRIMARY KEY AUTO_INCREMENT UNIQUE,
            nome_organizador VARCHAR(100) NOT NULL,
            tipo_organizador ENUM('CNPJ', 'PF') NOT NULL,
            telefone VARCHAR (20) NOT NULL,
            id_usuario INT NOT NULL,
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
            );
            ");

$conn->query("CREATE TABLE IF NOT EXISTS eventos (
            id_evento INT PRIMARY KEY AUTO_INCREMENT UNIQUE,
            nome_evento VARCHAR(100) NOT NULL,
            local_evento VARCHAR(100) NOT NULL,
            endereco_evento VARCHAR(100) NOT NULL,
            comeco_evento DATETIME NOT NULL,
            fim_evento DATETIME,
            preco DECIMAL(10,2) DEFAULT 0.00,
            id_organizador INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_organizador) REFERENCES organizadores(id_organizador) ON DELETE CASCADE
            );
            ");

$conn->query("CREATE TABLE IF NOT EXISTS curtidas (
            id_curtida INT PRIMARY KEY AUTO_INCREMENT UNIQUE,
            id_usuario INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
            );
            ");

$conn->query("CREATE TABLE IF NOT EXISTS comentarios (
            id_comentario INT PRIMARY KEY AUTO_INCREMENT UNIQUE,
            comentario VARCHAR(250) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            id_evento INT NOT NULL,
            id_usuario INT NOT NULL,
            FOREIGN KEY (id_evento) REFERENCES eventos(id_evento) ON DELETE CASCADE,
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
            );
            ");

$conn->query("CREATE TABLE IF NOT EXISTS seguidores (
            id_seguidor INT PRIMARY KEY AUTO_INCREMENT UNIQUE,
            id_organizador INT NOT NULL,
            id_usuario INT NOT NULL,
            FOREIGN KEY (id_organizador) REFERENCES organizadores(id_organizador) ON DELETE CASCADE,
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
            );
            ");

// CRIPTOGRAFIA DOS DADOS

// $count = $conn->query("SELECT COUNT(*) FROM categoria")->fetchColumn();

// if ($count == 0) {
//     $stmt = $conn->prepare("INSERT INTO categoria (nome) VALUES (:nome)");

//     $categorias = [
        
//     ];

//     foreach ($categorias as $categoria) {
//         $stmt->bindParam(':nome', $categoria);
//         $stmt->execute();
//     }
// }

?>
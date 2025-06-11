<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: meus_eventos.php');
    exit;
}

$evento_id = intval($_GET['id']);
$usuario_id = $_SESSION['usuario_id'];

// Deleta evento só se for do usuário logado
$stmt = $conn->prepare("DELETE FROM eventos WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $evento_id, $usuario_id);

if ($stmt->execute()) {
    header('Location: meus_eventos.php?msg=Evento excluído com sucesso');
} else {
    header('Location: meus_eventos.php?msg=Erro ao excluir evento');
}
exit;
?>
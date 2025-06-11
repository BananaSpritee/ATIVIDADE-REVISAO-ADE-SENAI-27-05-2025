<?php
if (isset($_GET['id'])) {
    echo "ID recebido: " . htmlspecialchars($_GET['id']);
} else {
    echo "Nenhum ID recebido.";
}
?> 
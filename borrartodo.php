<?php
session_start();

header('Content-Type: text/plain');

// Verifica si el administrador ha iniciado sesión
if (!isset($_SESSION['admin_logado'])) {
    http_response_code(403);
    echo "Acceso denegado.";
    exit();
}

include 'conexao.php';

// Verifica que el método sea POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conexao->prepare("DELETE FROM reservas");

    if ($stmt->execute()) {
        echo "ok";
    } else {
        http_response_code(500);
        echo "Error al eliminar todas las reservas.";
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo "Método inválido.";
}
?>

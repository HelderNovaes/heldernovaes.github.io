<?php
session_start();

header('Content-Type: text/plain');

if (!isset($_SESSION['admin_logado'])) {
    http_response_code(403);
    echo "Acceso denegado.";
    exit();
}

include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = intval($_POST['id']);

    $stmt = $conexao->prepare("DELETE FROM reservas WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "ok"; // respuesta esperada por el JS
    } else {
        http_response_code(500);
        echo "Error al eliminar la reserva.";
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo "ID inválido o método incorrecto.";
}
?>

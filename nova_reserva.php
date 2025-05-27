<?php
session_start();

if (!isset($_SESSION['admin_logado'])) {
    header("Location: login.php");
    exit();
}

include 'conexao.php';

$busca = '';
$resultado = null;

if (isset($_GET['busca']) && trim($_GET['busca']) !== '') {
    $busca = trim($_GET['busca']);
    $busca_sql = "%" . $busca . "%";

    $stmt = $conexao->prepare("
        SELECT * FROM reservas 
        WHERE cliente LIKE ? OR whatsapp LIKE ? OR email LIKE ?
        ORDER BY fecha DESC, hora DESC
    ");

    if ($stmt) {
        $stmt->bind_param("sss", $busca_sql, $busca_sql, $busca_sql);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $stmt->close();
    } else {
        die("Erro ao preparar statement: " . $conexao->error);
    }
} else {
    $resultado = $conexao->query("SELECT * FROM reservas ORDER BY fecha DESC, hora DESC");
}
?>

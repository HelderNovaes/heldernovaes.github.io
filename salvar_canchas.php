<?php
// Ativa a exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'conexao.php'; // Aqui você define $conexao

if (isset($_POST['precios'])) {
    foreach ($_POST['precios'] as $id => $precio) {
        $id = intval($id);
        $precio = floatval($precio);
        $sql = "UPDATE canchas SET precio_por_hora = $precio WHERE id = $id";
        mysqli_query($conexao, $sql); // Corrigido de $conn para $conexao
    }
}

header("Location: editar_canchas.php");
exit();

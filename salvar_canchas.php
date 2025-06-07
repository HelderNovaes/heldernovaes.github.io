<?php
include 'conexao.php';

if (isset($_POST['precios'])) {
    foreach ($_POST['precios'] as $id => $precio) {
        $id = intval($id);
        $precio = floatval($precio);
        $sql = "UPDATE canchas SET precio_por_hora = $precio WHERE id = $id";
        mysqli_query($conn, $sql);
    }
}

header("Location: editar_canchas.php");
exit();

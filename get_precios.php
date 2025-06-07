<?php
header('Content-Type: application/json');
include 'conexao.php'; // ou seu arquivo de conexão

$sql = "SELECT nombre, precio_por_hora FROM canchas";
$result = mysqli_query($conexao, $sql); // <-- corrigido aqui

$precios = [];

while ($row = mysqli_fetch_assoc($result)) {
  $precios[$row['nombre']] = (float) $row['precio_por_hora'];
    // Convertendo para float para garantir que seja numérico
}

echo json_encode($precios);
?>

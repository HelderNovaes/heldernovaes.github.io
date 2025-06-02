<?php
// validar_horario.php
include 'conexao.php';  // Ajusta la conexión

$cancha = $_POST['cancha'] ?? '';
$data = $_POST['data'] ?? '';
$hora = $_POST['hora'] ?? '';
$duracao = isset($_POST['duracao']) ? (int)$_POST['duracao'] : 0;

if (!$cancha || !$data || !$hora || !$duracao) {
    echo json_encode(['disponible' => false, 'mensaje' => 'Faltan parámetros']);
    exit;
}

// Convertir a timestamp para comparar
$inicioNueva = strtotime("$data $hora");
$fimNueva = strtotime("+$duracao hour", $inicioNueva);

// Consulta reservas existentes para esa cancha y fecha
$stmt = $conexao->prepare("SELECT hora, duracion FROM reservas WHERE cancha = ? AND fecha = ?");
$stmt->bind_param("ss", $cancha, $data);
$stmt->execute();
$result = $stmt->get_result();

$conflito = false;

while ($row = $result->fetch_assoc()) {
    $inicioExistente = strtotime("$data " . $row['hora']);
    $fimExistente = strtotime("+{$row['duracion']} hour", $inicioExistente);

    // Verificar si hay superposición
    if (($inicioNueva < $fimExistente) && ($fimNueva > $inicioExistente)) {
        $conflito = true;
        break;
    }
}

if ($conflito) {
    echo json_encode(['disponible' => false, 'mensaje' => 'Horario no disponible.']);
} else {
    echo json_encode(['disponible' => true, 'mensaje' => 'Horario disponible.']);
}
?>

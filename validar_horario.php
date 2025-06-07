<?php
// validar_horario.php
include 'conexao.php';
header('Content-Type: application/json');

// Validar e capturar dados
$cancha   = isset($_POST['cancha']) ? trim($_POST['cancha']) : '';
$data     = isset($_POST['data']) ? trim($_POST['data']) : '';
$hora     = isset($_POST['hora']) ? trim($_POST['hora']) : '';
$duracao = isset($_POST['duracao']) ? round((float) $_POST['duracao'], 2) : 0.00;
if (!$cancha || !$data || !$hora || !$duracao) {
    echo json_encode([
        'disponible' => false,
        'mensaje' => '❌ Faltan parámetros requeridos.'
    ]);
    exit;
}

// Convertir a timestamps
$inicioNueva = strtotime("$data $hora");
$fimNueva = $inicioNueva + ($duracao * 3600);

// Buscar reservas existentes
$stmt = $conexao->prepare("SELECT hora, duracion FROM reservas WHERE cancha = ? AND fecha = ?");
if (!$stmt) {
    echo json_encode([
        'disponible' => false,
        'mensaje' => '❌ Error de consulta: ' . $conexao->error
    ]);
    exit;
}

$stmt->bind_param("ss", $cancha, $data);
$stmt->execute();
$result = $stmt->get_result();

$conflito = false;

while ($row = $result->fetch_assoc()) {
    $inicioExistente = strtotime("$data " . $row['hora']);
    $fimExistente = $inicioExistente + ($row['duracion'] * 3600);

    // Verificar sobreposição
    if (($inicioNueva < $fimExistente) && ($fimNueva > $inicioExistente)) {
        $conflito = true;
        break;
    }
}

$stmt->close();

if ($conflito) {
    echo json_encode([
        'disponible' => false,
        'mensaje' => '❌ Horario no disponible.'
    ]);
} else {
    echo json_encode([
        'disponible' => true,
        'mensaje' => '✅ Horario disponible.'
    ]);
}
?>

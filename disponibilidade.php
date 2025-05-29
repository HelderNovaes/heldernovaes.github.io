<?php
header('Content-Type: application/json');
include 'conexao.php';

$cancha = $_GET['cancha'] ?? '';
$data = $_GET['data'] ?? '';

if (!$cancha || !$data) {
    echo json_encode(['error' => 'Parâmetros inválidos.']);
    exit;
}

$stmt = $conexao->prepare("SELECT hora, duracion FROM reservas WHERE cancha = ? AND fecha = ?");
$stmt->bind_param("ss", $cancha, $data);
$stmt->execute();
$result = $stmt->get_result();

$ocupados = [];

while ($row = $result->fetch_assoc()) {
    $horaInicio = strtotime($data . ' ' . $row['hora']);
    $duracao = (int)$row['duracion'];

    // Adiciona 30min para cada meia hora de duração
    $totalIntervalos = $duracao * 2;

    for ($i = 0; $i < $totalIntervalos; $i++) {
        $horaOcupada = date('H:i', strtotime("+".($i * 30)." minutes", $horaInicio));
        $ocupados[] = $horaOcupada;
    }
}

echo json_encode($ocupados);
?>

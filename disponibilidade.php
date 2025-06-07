<?php
header('Content-Type: application/json');
include 'conexao.php';

$cancha = $_GET['cancha'] ?? '';
$data = $_GET['data'] ?? '';

if (!$cancha || !$data) {
    echo json_encode(['error' => 'Parámetros inválidos.']);
    exit;
}

$stmt = $conexao->prepare("SELECT hora, duracion FROM reservas WHERE cancha = ? AND fecha = ?");
$stmt->bind_param("ss", $cancha, $data);
$stmt->execute();
$result = $stmt->get_result();

$ocupados = [];

while ($row = $result->fetch_assoc()) {
    $horaInicio = strtotime($data . ' ' . $row['hora']);
    $duracao = (float) $row['duracion']; // Lê como decimal do banco de dados
    $duracao = max($duracao, 0.25); // Garante que a duração mínima seja de 15 minutos
    $totalIntervalos = intval($duracao * 60 / 15); // Total de blocos de 15 min

    for ($i = 0; $i < $totalIntervalos; $i++) {
        $horaOcupada = date('H:i', strtotime("+".($i * 15)." minutes", $horaInicio));
        $ocupados[] = $horaOcupada;
    }
}

echo json_encode($ocupados);
?>

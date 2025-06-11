<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include 'conexao.php';

$cancha = $_GET['cancha'] ?? '';
$data = $_GET['data'] ?? '';

if (!$cancha || !$data) {
    echo json_encode(['error' => 'Parámetros inválidos.']);
    exit;
}

try {
    $stmt = $conexao->prepare("SELECT hora, duracion, cliente, whatsapp, cancha FROM reservas WHERE cancha = ? AND fecha = ?");
    $stmt->bind_param("ss", $cancha, $data);
    $stmt->execute();
    $result = $stmt->get_result();

    $reservas = [];

    while ($row = $result->fetch_assoc()) {
        $inicio = strtotime($data . ' ' . $row['hora']);
        $duracao = (float) $row['duracion'];
        $fim = strtotime("+".($duracao * 60)." minutes", $inicio);

        $reservas[] = [
            'hora_inicio' => date('H:i', $inicio),
            'hora_fim' => date('H:i', $fim),
            'nombre' => $row['cliente'],
            'telefono' => $row['whatsapp'],
            'cancha' => $row['cancha']
        ];
    }

    echo json_encode($reservas);

} catch (Exception $e) {
    echo json_encode(['error' => 'Erro no servidor.', 'detalhes' => $e->getMessage()]);
}

<?php
include 'conexao.php';

// Captura y validación de datos
$cancha   = isset($_POST['cancha']) ? $conexao->real_escape_string(trim($_POST['cancha'])) : '';
$data     = isset($_POST['data']) ? $conexao->real_escape_string(trim($_POST['data'])) : '';
$hora     = isset($_POST['hora']) ? $conexao->real_escape_string(trim($_POST['hora'])) : '';
$duracao  = isset($_POST['duracao']) ? (int) $_POST['duracao'] : 0;
$cliente  = isset($_POST['cliente']) ? $conexao->real_escape_string(trim($_POST['cliente'])) : '';
$valor    = isset($_POST['valor']) ? (float) $_POST['valor'] : 0;
$whatsapp = isset($_POST['whatsapp']) ? $conexao->real_escape_string(trim($_POST['whatsapp'])) : '';
$email    = isset($_POST['email']) ? $conexao->real_escape_string(trim($_POST['email'])) : '';

// Validar si los campos obligatorios fueron llenados
if (!$cancha || !$data || !$hora || !$duracao || !$cliente || !$valor || !$whatsapp || !$email) {
    echo json_encode([
        'status' => 'error',
        'mensagem' => '❌ Llena todos los campos obligatorios.',
        'disponivel' => false
    ]);
    exit;
}

// Verificar conflictos de horario
$dataReserva = $data;
$inicioNova = strtotime("$data $hora");
$fimNova = strtotime("+$duracao hour", $inicioNova);

$stmt = $conexao->prepare("SELECT hora, duracion FROM reservas WHERE cancha = ? AND fecha = ?");
$stmt->bind_param("ss", $cancha, $dataReserva);
$stmt->execute();
$result = $stmt->get_result();

$conflito = false;

while ($row = $result->fetch_assoc()) {
    $inicioExistente = strtotime("$dataReserva " . $row['hora']);
    $fimExistente = strtotime("+{$row['duracion']} hour", $inicioExistente);

    if (($inicioNova < $fimExistente) && ($fimNova > $inicioExistente)) {
        $conflito = true;
        break;
    }
}

if ($conflito) {
    echo json_encode([
        'status' => 'error',
        'mensagem' => '❌ ¡Horario no disponible! Ya existe una reserva en este horario.',
        'disponivel' => false
    ]);
    exit;
}

// Insertar reserva
$stmt = $conexao->prepare("INSERT INTO reservas (cancha, fecha, hora, duracion, cliente, valor_total, whatsapp, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'mensagem' => '❌ Error al preparar el registro: ' . $conexao->error,
        'disponivel' => false
    ]);
    exit;
}

$stmt->bind_param("sssisdss", $cancha, $data, $hora, $duracao, $cliente, $valor, $whatsapp, $email);

if ($stmt->execute()) {
    // Envío de correo electrónico
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $assunto = "Confirmación de Reserva - Pentagol";
        $mensagem = "Hola $cliente,\n\n¡Tu reserva fue registrada con éxito!";
        $cabecalhos = "From: helderpes@gmail.com";
        @mail($email, $assunto, $mensagem, $cabecalhos);
    }

    // Link de WhatsApp
    $numeroLimpo = preg_replace('/\D/', '', $whatsapp);
    $mensagemWpp = urlencode("¡Hola $cliente! Tu reserva en *Pentagol* fue registrada con éxito:\n\n📅 Fecha: $data\n⏰ Hora: $hora\n🕒 Duración: $duracao hora(s)\n💰 Valor: $valor Bs\n\n¡Gracias por elegir Pentagol!");
    $linkWpp = "https://wa.me/$numeroLimpo?text=$mensagemWpp";

    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => '✅ ¡Reserva registrada con éxito!',
        'disponivel' => true,
        'whatsapplink' => $linkWpp
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'mensagem' => '❌ Error al registrar la reserva: ' . $stmt->error,
        'disponivel' => false
    ]);
}

$stmt->close();
$conexao->close();
?>

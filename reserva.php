<?php
include 'conexao.php';

// Captura e validação de dados
$cancha   = isset($_POST['cancha']) ? $conexao->real_escape_string(trim($_POST['cancha'])) : '';
$data     = isset($_POST['data']) ? $conexao->real_escape_string(trim($_POST['data'])) : '';
$hora     = isset($_POST['hora']) ? $conexao->real_escape_string(trim($_POST['hora'])) : '';
$duracao  = isset($_POST['duracao']) ? (int) $_POST['duracao'] : 0;
$cliente  = isset($_POST['cliente']) ? $conexao->real_escape_string(trim($_POST['cliente'])) : '';
$valor    = isset($_POST['valor']) ? (float) $_POST['valor'] : 0;
$whatsapp = isset($_POST['whatsapp']) ? $conexao->real_escape_string(trim($_POST['whatsapp'])) : '';
$email    = isset($_POST['email']) ? $conexao->real_escape_string(trim($_POST['email'])) : '';

// Validar se campos obrigatórios foram preenchidos
if (!$cancha || !$data || !$hora || !$duracao || !$cliente || !$valor || !$whatsapp || !$email) {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => '❌ Preencha todos os campos obrigatórios.',
        'disponivel' => false
    ]);
    exit;
}

// Verificar conflitos de horário
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
        'status' => 'erro',
        'mensagem' => '❌ Horário indisponível! Já existe uma reserva neste horário.',
        'disponivel' => false
    ]);
    exit;
}

// Inserir reserva
$stmt = $conexao->prepare("INSERT INTO reservas (cancha, fecha, hora, duracion, cliente, valor_total, whatsapp, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => '❌ Erro ao preparar a inserção: ' . $conexao->error,
        'disponivel' => false
    ]);
    exit;
}

$stmt->bind_param("sssisdss", $cancha, $data, $hora, $duracao, $cliente, $valor, $whatsapp, $email);

if ($stmt->execute()) {
    // E-mail
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $assunto = "Confirmação de Reserva - Pentagol";
        $mensagem = "Olá $cliente,\n\nSua reserva foi registrada com sucesso!";
        $cabecalhos = "From: helderpes@gmail.com";
        @mail($email, $assunto, $mensagem, $cabecalhos);
    }

    // Link do WhatsApp
    $numeroLimpo = preg_replace('/\D/', '', $whatsapp);
    $mensagemWpp = urlencode("Olá $cliente! Sua reserva na *Pentagol* foi registrada com sucesso:\n\n📅 Data: $data\n⏰ Hora: $hora\n🕒 Duração: $duracao hora(s)\n💰 Valor: $valor Bs\n\nObrigado por escolher a Pentagol!");
    $linkWpp = "https://wa.me/$numeroLimpo?text=$mensagemWpp";

   echo json_encode([
    'status' => 'sucesso',
    'mensagem' => '✅ Reserva registrada com sucesso',
    'disponivel' => true,
    'whatsapplink' => $linkWpp
]);
} else {
    echo json_encode([
        'status' => 'erro',
        'mensagem' => '❌ Erro ao registrar a reserva: ' . $stmt->error,
        'disponivel' => false
    ]);
}

$stmt->close();
$conexao->close();
?>

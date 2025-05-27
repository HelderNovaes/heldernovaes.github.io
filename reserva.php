<?php
include 'conexao.php';

// Captura e valida dados do POST
$cancha = $conexao->real_escape_string(trim($_POST['cancha']));
$data = $conexao->real_escape_string(trim($_POST['data']));
$hora = $conexao->real_escape_string(trim($_POST['hora']));
$duracao = (int)$_POST['duracao'];
$cliente = $conexao->real_escape_string(trim($_POST['cliente']));
$valor = (float)$_POST['valor'];
$whatsapp = isset($_POST['whatsapp']) ? trim($_POST['whatsapp']) : null;
$email = isset($_POST['email']) ? trim($_POST['email']) : null;

// Convertemos para timestamp para comparar
$dataReserva = $data;
$inicioNova = strtotime("$data $hora");
$fimNova = strtotime("+$duracao hour", $inicioNova);

// Buscar reservas existentes na mesma data e cancha
$stmt = $conexao->prepare("
    SELECT hora, duracion 
    FROM reservas 
    WHERE cancha = ? AND fecha = ?
");
$stmt->bind_param("ss", $cancha, $dataReserva);
$stmt->execute();
$result = $stmt->get_result();

$conflito = false;

while ($row = $result->fetch_assoc()) {
    $inicioExistente = strtotime("$dataReserva " . $row['hora']);
    $fimExistente = strtotime("+" . $row['duracion'] . " hour", $inicioExistente);

    // Verificar se há sobreposição
    if (
        ($inicioNova < $fimExistente) && ($fimNova > $inicioExistente)
    ) {
        $conflito = true;
        break;
    }
}

if ($conflito) {
   echo "Horário indisponível! Já existe uma reserva neste horário. Por favor, escolha outro horário.";
exit;

}


$stmt = $conexao->prepare("INSERT INTO reservas (cancha, fecha, hora, duracion, cliente, valor_total, whatsapp, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Erro no prepare: " . $conexao->error);
}

$stmt->bind_param("sssisdss", $cancha, $data, $hora, $duracao, $cliente, $valor, $whatsapp, $email);

if ($stmt->execute()) {
    // Enviar e-mail
    $assunto = "Confirmação de Reserva - Pentagol";
    $mensagem = "Olá $cliente,\n\nSua reserva foi registrada com sucesso:\n\n".
                "📅 Data: $data\n⏰ Hora: $hora\n🕒 Duração: $duracao hora(s)\n".
                "💰 Valor: $valor Bs\n\nObrigado por reservar com a Pentagol!";
    $cabecalhos = "From: helderpes@gmail.com";

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        mail($email, $assunto, $mensagem, $cabecalhos);
    }

    // Enviar WhatsApp
    $numeroLimpo = preg_replace('/\D/', '', $whatsapp);
    $mensagemWpp = urlencode("Olá $cliente! Sua reserva na *Pentagol* foi registrada com sucesso:\n\n📅 Data: $data\n⏰ Hora: $hora\n🕒 Duração: $duracao hora(s)\n💰 Valor: $valor Bs\n\nObrigado por escolher a Pentagol!");
    $link = "https://wa.me/$numeroLimpo?text=$mensagemWpp";

    echo "Reserva registrada com sucesso! Vamos te redirecionar ao WhatsApp...";
    exit;

} else {
    echo "Erro ao registrar: " . $stmt->error;
}

$stmt->close();
$conexao->close();
?>

<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['admin_logado'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("ID inválido.");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cancha = $_POST['cancha'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
   $valor     = isset($_POST['valor']) ? (float) str_replace(' Bs', '', trim($_POST['valor'])) : 0;
$duracion  = isset($_POST['duracion']) ? (float) $_POST['duracion'] : 0;

    $cliente = $_POST['cliente'];
    $whatsapp = $_POST['whatsapp'];
    $email = $_POST['email'];

    // Buscar dados atuais do banco
    $stmt = $conexao->prepare("SELECT * FROM reservas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $reserva_atual = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$reserva_atual) {
        die("Reserva no encontrada.");
    }

    // Se todos os campos forem iguais, não atualizar
    if (
        $reserva_atual['cancha'] === $cancha &&
        $reserva_atual['fecha'] === $fecha &&
        $reserva_atual['hora'] === $hora &&
        floatval($reserva_atual['valor_total']) === floatval($valor) &&
        $reserva_atual['cliente'] === $cliente &&
        $reserva_atual['whatsapp'] === $whatsapp &&
        $reserva_atual['email'] === $email
    ) {
        $erro = "No se realizaron cambios en la reserva.";
    } else {
        // Verificar conflito com outra reserva
$stmt = $conexao->prepare("
    UPDATE reservas 
    SET cancha=?, fecha=?, hora=?, duracion=?, valor_total=?, cliente=?, whatsapp=?, email=?
    WHERE id=?
");
$stmt->bind_param("sssdssssi", $cancha, $fecha, $hora, $duracion, $valor, $cliente, $whatsapp, $email, $id);

if ($stmt->execute()) {
    header("Location: admin.php?editado=1");
    exit();
} else {
    $erro = "Error al actualizar la reserva.";
}
$stmt->close();
        }
    }



// Carregar dados da reserva
$stmt = $conexao->prepare("SELECT * FROM reservas WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$reserva = $res->fetch_assoc();
$stmt->close();

if (!$reserva) {
    die("Reserva no encontrada");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Editar Reserva</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        .form-container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
        }

        form label {
            display: block;
            margin-bottom: 12px;
            font-weight: bold;
            color: #333;
        }

        form input {
            width: 100%;
            padding: 10px;
            margin-top: 4px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #27ae60;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #219150;
        }

        .erro {
            color: #e74c3c;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Editar Reserva</h2>

    <?php if (isset($erro)): ?>
        <div class="erro"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post">
        <label>Cancha:
            <input type="text" name="cancha" value="<?= htmlspecialchars($reserva['cancha']) ?>" required>
        </label>

        <label>Fecha:
            <input type="date" name="fecha" value="<?= htmlspecialchars($reserva['fecha']) ?>" required>
        </label>

        <label>Hora:
            <input type="time" name="hora" value="<?= htmlspecialchars($reserva['hora']) ?>" required>
        </label>

        <label>Duración (horas):
        <input type="number" name="duracion" value="<?= htmlspecialchars($reserva['duracion']) ?>" min="0.5" step="0.5" required>
   </label>

        <label>Cliente:
            <input type="text" name="cliente" value="<?= htmlspecialchars($reserva['cliente']) ?>" required>
        </label>

        <label>WhatsApp:
            <input type="text" name="whatsapp" value="<?= htmlspecialchars($reserva['whatsapp']) ?>" required>
        </label>

        <label>Email:
            <input type="email" name="email" value="<?= htmlspecialchars($reserva['email']) ?>" required>
        </label>

        <label>Valor (Bs):
        <input type="text" name="valor" value="<?= htmlspecialchars($reserva['valor_total']) ?>" required>
        </label>
        <button type="submit">Guardar</button>
    </form>
</div>

</body>
</html>

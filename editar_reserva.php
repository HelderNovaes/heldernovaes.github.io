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
    $duracion = intval($_POST['duracion']);
    $cliente = $_POST['cliente'];
    $whatsapp = $_POST['whatsapp'];
    $email = $_POST['email'];

    // Verificar conflito de horário
    $stmt = $conexao->prepare("
        SELECT * FROM reservas 
        WHERE cancha = ? AND fecha = ? 
        AND hora = ? AND id != ?
    ");
    $stmt->bind_param("sssi", $cancha, $fecha, $hora, $id);
    $stmt->execute();
    $conflito = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($conflito) {
        $erro = "Já existe uma reserva para essa cancha, data e hora.";
    } else {
        $stmt = $conexao->prepare("
            UPDATE reservas 
            SET cancha=?, fecha=?, hora=?, duracion=?, cliente=?, whatsapp=?, email=?
            WHERE id=?
        ");
        $stmt->bind_param("sssisssi", $cancha, $fecha, $hora, $duracion, $cliente, $whatsapp, $email, $id);
        if ($stmt->execute()) {
            header("Location: admin.php?editado=1");
            exit();
        } else {
            $erro = "Erro ao atualizar a reserva.";
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
    die("Reserva não encontrada.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
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

        <label>Duração (horas):
            <input type="number" name="duracion" value="<?= htmlspecialchars($reserva['duracion']) ?>" min="1" required>
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

        <button type="submit">Salvar</button>
    </form>
</div>

</body>
</html>

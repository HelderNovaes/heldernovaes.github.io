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
$duracion = isset($_POST['duracion']) ? (float) str_replace(',', '.', $_POST['duracion']) : 0;
    if ($duracion <= 0) {
        $erro = "A duração deve ser maior que zero.";
    }

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
    floatval($reserva_atual['duracion']) === floatval($duracion) &&
    floatval($reserva_atual['valor_total']) === floatval($valor) &&
    $reserva_atual['cliente'] === $cliente &&
    $reserva_atual['whatsapp'] === $whatsapp &&
    $reserva_atual['email'] === $email
) {
    // Mesmo sem alteração, redireciona como se tivesse salvado
    header("Location: admin.php?editado=1");
    exit();
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
.btn-volver {
    position: fixed;
    top: 20px;
    right: 20px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background-color: #1976d2;
    color: white;
    padding: 10px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 15px;
    font-weight: bold;
    box-shadow: 0 4px 8px rgba(25, 118, 210, 0.2);
    z-index: 1000;
    transition: background-color 0.3s ease, transform 0.1s ease;
}

.btn-volver:hover {
    background-color: #1565c0;
    transform: scale(1.03);
}

.btn-volver:active {
    transform: scale(0.98);
}

.btn-volver .icon {
    width: 18px;
    height: 18px;
    stroke: white;
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
        @media (max-width: 600px) {
    .btn-volver {
        padding: 8px 12px;
        font-size: 13px;
        gap: 6px;
    }

    .btn-volver .icon {
        width: 15px;
        height: 15px;
    }
}
    </style>
</head>
<body>
<a href="admin.php" class="btn-volver" title="Volver al panel">
  <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
  </svg>
  Volver
</a>

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

        <input type="number" name="duracion" 
       value="<?= htmlspecialchars($reserva['duracion']) ?>" 
       min="0.25" step="0.01" required>

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

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();


if (!isset($_SESSION['admin_logado'])) {
    header("Location: login.php");
    exit();
}

include 'conexao.php';

$busca = '';
$resultado = null;

if (isset($_GET['busca']) && trim($_GET['busca']) !== '') {
    $busca = trim($_GET['busca']);
    $busca_sql = "%" . $busca . "%";

    $stmt = $conexao->prepare("
        SELECT * FROM reservas 
        WHERE cliente LIKE ? OR whatsapp LIKE ? OR email LIKE ?
        ORDER BY fecha DESC, hora DESC
    ");

    if ($stmt) {
        $stmt->bind_param("sss", $busca_sql, $busca_sql, $busca_sql);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $stmt->close();
    } else {
        die("Erro ao preparar statement: " . $conexao->error);
    }
} else {
    $resultado = $conexao->query("SELECT * FROM reservas ORDER BY fecha DESC, hora DESC");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - Reservas</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            margin: 0;
            background: #f9f9f9;
            color: #333;
        }
button[type="submit"] {
    padding: 12px 20px;
    background-color: #3498db;
    color: #fff;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    margin-left: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

button[type="submit"]:hover {
    background-color: #2980b9;
    transform: scale(1.03);
}

button[type="submit"]:active {
    transform: scale(0.98);
}

.actions form {
    display: flex;
    gap: 10px; /* Espaço entre o input e o botão */
    flex-wrap: wrap;
    align-items: center;
    max-width: 500px;
    margin-right: 20px;
}

.actions input[type="text"] {
    flex: 1;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
    min-width: 200px;
}

.actions button[type="submit"] {
    padding: 10px 20px;
    background-color: #3498db;
    color: #fff;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.actions button[type="submit"]:hover {
    background-color: #2980b9;
    transform: scale(1.02);
}


        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 20px;
        }

        .actions form {
            flex-grow: 1;
            max-width: 400px;
            margin-right: 20px;
        }

        .actions input[type="text"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .logout, .new-reserva {
            padding: 10px 20px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-top: 10px;
            display: inline-block;
        }

        .new-reserva {
            background-color: #27ae60;
        }

        .new-reserva:hover {
            background-color: #219150;
        }

        .logout {
            background-color: #e74c3c;
        }

        .logout:hover {
            background-color: #c0392b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #34495e;
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 2;
        }

        tr:nth-child(even) {
            background-color: #f5f7fa;
        }

        tr:hover {
            background-color: #e1ecf7;
        }

        td a {
            color: #27ae60;
            text-decoration: none;
            font-weight: bold;
        }

        td a:hover {
            text-decoration: underline;
        }

        .remover {
            color: #e74c3c;
            cursor: pointer;
        }

        @media (max-width: 900px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                margin-bottom: 20px;
                border: 1px solid #ccc;
                border-radius: 8px;
                padding: 10px;
                background: white;
            }

            td {
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
                text-align: right;
                font-size: 14px;
            }

            td:last-child {
                border-bottom: 0;
            }

            td::before {
                position: absolute;
                top: 12px;
                left: 15px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                font-weight: bold;
                text-align: left;
                color: #34495e;
                content: attr(data-label);
            }
        }
    </style>
    <script>
        function removerReserva(id) {
            if (confirm('Deseja realmente remover esta reserva?')) {
                window.location.href = 'remover_reserva.php?id=' + id;
            }
        }
    </script>
</head>
<body>

    <h1>Painel de Admin - Reservas</h1>

    <div class="actions">
      <form method="get">
    <input type="text" name="busca" placeholder="Buscar por cliente, WhatsApp ou email" value="<?= htmlspecialchars($busca) ?>" />
    <button type="submit">Buscar</button>
</form>

        <div>
            <a href="index.html" class="new-reserva">Nova Reserva</a>
            <a href="logout.php" class="logout">Sair</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cancha</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Duración</th>
                <th>Cliente</th>
                <th>WhatsApp</th>
                <th>Email</th>
                <th>Valor Total</th>
                <th>Registrado em</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $resultado->fetch_assoc()): ?>
                <tr>
                    <td data-label="ID"><?= htmlspecialchars($row['id']) ?></td>
                    <td data-label="Cancha"><?= htmlspecialchars($row['cancha']) ?></td>
                    <td data-label="Fecha"><?= htmlspecialchars($row['fecha']) ?></td>
                    <td data-label="Hora"><?= htmlspecialchars($row['hora']) ?></td>
                    <td data-label="Duración"><?= htmlspecialchars($row['duracion']) ?>h</td>
                    <td data-label="Cliente"><?= htmlspecialchars($row['cliente']) ?></td>
                    <td data-label="WhatsApp">
                        <a href="https://wa.me/<?= preg_replace('/\D/', '', $row['whatsapp']) ?>" target="_blank">
                            <?= htmlspecialchars($row['whatsapp']) ?>
                        </a>
                    </td>
                    <td data-label="Email"><?= htmlspecialchars($row['email']) ?></td>
                    <td data-label="Valor Total"><?= number_format($row['valor_total'], 2, ',', '.') ?> Bs</td>
                    <td data-label="Registrado em"><?= htmlspecialchars($row['data_reserva']) ?></td>
                    <td data-label="Ações">
                        <span class="remover" onclick="removerReserva(<?= $row['id'] ?>)">Remover</span>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<script>
function removerReserva(id) {
    if (!confirm("Tem certeza que deseja remover esta reserva?")) {
        return;
    }

    fetch('remover_reserva.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'id=' + encodeURIComponent(id)
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === 'ok') {
            // Encontrar a linha da tabela com o ID correspondente e removê-la
            const rows = document.querySelectorAll('tbody tr');
            for (let row of rows) {
                const idCell = row.querySelector('td[data-label="ID"]');
                if (idCell && parseInt(idCell.textContent.trim()) === id) {
                    row.remove();
                    break;
                }
            }
        } else {
            alert("Erro ao remover a reserva: " + data);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert("Erro ao tentar remover a reserva.");
    });
}
</script>


</body>
</html>

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<!-- DatePicker (opcional para mejor filtro de fechas) -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/i18n/jquery-ui-i18n.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
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
        .boton-superior {
    position: absolute;
    top: 20px;
    right: 20px;
    background-color: #007BFF;
    color: white;
    padding: 10px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s ease;
    z-index: 1000;
  }

  .boton-superior:hover {
    background-color: #0056b3;
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
tr.fade-out {
    transition: opacity 0.3s ease-out;
    opacity: 0;
}

/* Estilo base para os filtros */
#filtros {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
    margin-bottom: 20px;
    font-family: Arial, sans-serif;
}

#filtros label {
    font-weight: bold;
    color: #333;
    font-size: 14px;
}

#filtros input[type="text"] {
    padding: 6px 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    width: 200px;
    box-sizing: border-box;
}

#filtros input[type="text"]:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 3px rgba(0, 123, 255, 0.3);
}
#DataTables_Table_0_filter {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 15px;
    font-family: Arial, sans-serif;
    font-size: 14px;
}

#DataTables_Table_0_filter label {
    font-weight: bold;
    color: #333;
}

#DataTables_Table_0_filter input {
    padding: 6px 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    width: 200px;
    transition: border-color 0.3s ease;
}

#DataTables_Table_0_filter input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 3px rgba(0, 123, 255, 0.3);
}


        td a:hover {
            text-decoration: underline;
        }

        .remover {
            color: #e74c3c;
            cursor: pointer;
        }
/* Corrige el layout do datepicker */
.ui-datepicker table {
    display: table !important;
    width: 100%;
    border-collapse: collapse;
}

.ui-datepicker th,
.ui-datepicker td {
    display: table-cell !important;
    text-align: center;
    padding: 5px;
}

.ui-datepicker td span,
.ui-datepicker td a {
    display: block;
    text-decoration: none;
    padding: 4px;
    color: #333;
}
#btnRelatorio {
    background-color: #17a2b8; /* Azul tipo "info" */
    color: white;
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 10px;
}

#btnRelatorio:hover {
    background-color: #138496;
}
.ui-datepicker {
    z-index: 9999 !important;
    background: #fff;
    border: 1px solid #ccc;
    padding: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

@media (max-width: 900px) {

    /* 🔄 Tabela responsiva estilo cartão */
    table.responsiva,
    table.responsiva thead,
    table.responsiva tbody,
    table.responsiva tr,
    table.responsiva td,
    table.responsiva th {
        display: block;
    }

    table.responsiva thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px;
    }

    table.responsiva tr {
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 10px;
        background: white;
    }

    table.responsiva td {
        border: none;
        border-bottom: 1px solid #eee;
        position: relative;
        padding-left: 50%;
        text-align: right;
        font-size: 14px;
    }

    table.responsiva td:last-child {
        border-bottom: 0;
    }

    table.responsiva td::before {
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

    /* ❌ Exceção: Coluna "Fecha" deve manter estilo normal */
    table.responsiva td[data-label="Fecha"] {
        padding-left: 10px !important;
        text-align: left !important;
    }

    table.responsiva td[data-label="Fecha"]::before {
        content: none !important;
    }

    /* 🔄 Filtros empilhados */
    #filtros {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 20px;
    }

    #filtros label,
    #filtros input[type="text"] {
        width: 100%;
        max-width: 300px;
    }
    
    .boton-superior {
      padding: 8px 12px;
      font-size: 13px;
      top: 12px;
      right: 12px;
    }
  
}
@media (max-width: 600px) {
    .boton-superior {
      padding: 6px 10px;
      font-size: 12px;
      top: 10px;
      right: 10px;
    }
  }
.dataTables_filter {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 15px;
    font-family: Arial, sans-serif;
    font-size: 14px;
}
.dataTables_filter label {
    font-weight: bold;
    color: #333;
}
.dataTables_filter input {
    padding: 6px 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    width: 200px;
    transition: border-color 0.3s ease;
}
    </style>
   
</head>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<body>
<a href="editar_canchas.php" class="boton-superior">⚙️ Editar precios</a>

    <h1>Panel de Administración - Reservas</h1>

    <div class="actions">
        <form method="get">
            <input type="text" name="busca" placeholder="Buscar por cliente, WhatsApp o correo electrónico" value="<?= htmlspecialchars($busca) ?>" />
            <button type="submit">Buscar</button>
        </form>

        <div>
            <a href="index.html" class="new-reserva">Nueva Reserva</a>
            <a href="logout.php" class="logout">Salir</a>
        </div>
    </div>

    <div id="filtros">
        <label for="fechaFiltro">Filtrar por Fecha:</label>
        <input type="text" id="fechaFiltro" placeholder="AAAA-MM-DD">

        <label for="horaFiltro">Filtrar por Hora:</label>
        <input type="text" id="horaFiltro" placeholder="HH:MM">
        <button id="btnRelatorio" class="btn btn-info">📄 Generar informe</button>

    </div>

    <table class="responsiva" id="tabelaPrincipal">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cancha</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Duración</th>
                <th>Cliente</th>
                <th>WhatsApp</th>
                <th>Correo electrónico</th>
                <th>Valor Total</th>
                <th>Registrado en</th>
                <th>Acciones</th>
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
                        <?php
                        $numero = preg_replace('/\D/', '', $row['whatsapp']);
                        $mensagem = urlencode("/ReservasPentagol");
                        ?>
                        <a href="https://wa.me/<?= $numero ?>?text=<?= $mensagem ?>" target="_blank">
                            <?= htmlspecialchars($row['whatsapp']) ?>
                        </a>
                    </td>
                    <td data-label="Correo electrónico"><?= htmlspecialchars($row['email']) ?></td>
                    <td data-label="Valor Total"><?= number_format($row['valor_total'], 2, ',', '.') ?> Bs</td>
                    <td data-label="Registrado en"><?= htmlspecialchars($row['data_reserva']) ?></td>
                    <td data-label="Acciones">
                        <a href="editar_reserva.php?id=<?= $row['id'] ?>">Editar</a>
                        <span class="remover" onclick="removerReserva(<?= $row['id'] ?>)">
                            <i class="fas fa-trash-alt"></i>
                        </span>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/i18n/jquery-ui-i18n.min.js"></script>
<script>
  $(function() {
    var userLang = navigator.language || navigator.userLanguage;
    userLang = userLang.toLowerCase();

    $.datepicker.setDefaults($.datepicker.regional[userLang] || $.datepicker.regional["es"]);

    $("#fechaFiltro").datepicker({
      dateFormat: "yy-mm-dd"
    });
  });
  row.classList.add('fade-out');
setTimeout(() => row.remove(), 300);

</script>

<script>
function removerReserva(id) {
    if (!confirm("¿Estás seguro de que deseas eliminar esta reserva?")) {
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
            const rows = document.querySelectorAll('tbody tr');
            for (let row of rows) {
                const idCell = row.querySelector('td[data-label="ID"]');
                if (idCell && parseInt(idCell.textContent.trim()) === id) {
                    row.remove();
                    break;
                }
            }
        } else {
            alert("Error al eliminar la reserva: " + data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Error al intentar eliminar la reserva.");
    });
}
$(document).ready(function() {
    var userLang = (navigator.language || navigator.userLanguage).toLowerCase();
    
    var dataTablesLangMap = {
        'es': 'es-ES',
        'pt': 'pt-BR',
        'en': 'en-GB',
        'fr': 'fr-FR',
    };

    var baseLang = userLang.split('-')[0];

    var dataTablesLang = dataTablesLangMap[baseLang] || 'es-ES';

    var table = $('table').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/" + dataTablesLang + ".json"
        }
    });

    $.datepicker.setDefaults($.datepicker.regional[userLang] || $.datepicker.regional[baseLang] || $.datepicker.regional["es"]);

    $("#fechaFiltro").datepicker({
        dateFormat: "yy-mm-dd",
        beforeShow: function(input, inst) {
            setTimeout(function() {
                inst.dpDiv.css({
                    top: $(input).offset().top -150,
                    left: $(input).offset().left + $(input).outerWidth() + 10
                });
            }, 0);
        }
    });

    $('#fechaFiltro').on('keyup change', function () {
        table.column(2).search(this.value).draw();
    });

    $('#horaFiltro').on('keyup change', function () {
        table.column(3).search(this.value).draw();
    });
});
</script>

<script>
document.getElementById('btnRelatorio').addEventListener('click', function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');

    doc.setFontSize(11);
    doc.text("Informe de Reservas - Hoja de Pago", 14, 15);

    const data = [];
    const headers = [];

    const allTh = document.querySelectorAll('#tabelaPrincipal thead th');
    for (let i = 0; i < allTh.length - 1; i++) {
        headers.push(allTh[i].innerText.trim());
    }

    $('#tabelaPrincipal').DataTable().rows({ search: 'applied' }).every(function () {
        const rowNodes = this.nodes().toArray();
        rowNodes.forEach(node => {
            const cells = node.querySelectorAll('td');
            const rowData = [];
            for (let i = 0; i < cells.length - 1; i++) {
                rowData.push(cells[i].innerText.trim());
            }
            data.push(rowData);
        });
    });

    doc.autoTable({
        head: [headers],
        body: data,
        startY: 20,
        margin: { top: 20, left: 8, right: 8 },
        styles: {
            fontSize: 7.5, // AUMENTADO
            cellPadding: 1.2,
            overflow: 'linebreak',
            valign: 'middle'
        },
        columnStyles: {
            0: { cellWidth: 7 },
            1: { cellWidth: 12 },
            2: { cellWidth: 16 },
            3: { cellWidth: 13 },
            4: { cellWidth: 13 },
            5: { cellWidth: 24 },
            6: { cellWidth: 24 },
            7: { cellWidth: 28 },
            8: { cellWidth: 16 },
            9: { cellWidth: 22 }
        },
        tableWidth: 'wrap',
        theme: 'grid'
    });

    let nomeCliente = document.querySelector('input[name="busca"]').value.trim();
    nomeCliente = nomeCliente.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_]/g, '').toLowerCase();
    const nomeArquivo = nomeCliente ? `relatorio_pagamento_${nomeCliente}.pdf` : 'relatorio_pagamento.pdf';

    doc.save(nomeArquivo);
});
</script>

</body>

</html>

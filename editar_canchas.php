<?php
include 'conexao.php'; // ajuste o caminho se necessário

$sql = "SELECT * FROM canchas";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
  <title>Editar Preços - Admin</title>
  <style>
    <style>
  body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f2f4f8;
    margin: 0;
    padding: 0;
  }

  h2 {
    text-align: center;
    color: #333;
    margin-top: 40px;
  }

  form {
    max-width: 600px;
    margin: 20px auto;
    background-color: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 0 12px rgba(0,0,0,0.1);
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }

  th, td {
    padding: 14px 10px;
    text-align: left;
  }

  th {
    background-color: #007BFF;
    color: white;
    border-radius: 8px 8px 0 0;
  }

  tr:nth-child(even) {
    background-color: #f9f9f9;
  }

  input[type="number"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
  }

  button {
    display: block;
    margin: 20px auto 0;
    background-color: #28a745;
    color: white;
    padding: 12px 24px;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  button:hover {
    background-color: #218838;
  }

  @media (max-width: 600px) {
    form {
      margin: 10px;
      padding: 15px;
    }

    th, td {
      padding: 10px;
    }

    button {
      width: 100%;
    }
  }
</style>

  </style>
</head>
<body>
  <h2 style="text-align:center;">Editar preços das canchas</h2>
  <form action="salvar_canchas.php" method="POST">
    <table>
      <tr><th>Cancha</th><th>Preço por Hora (Bs)</th></tr>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= htmlspecialchars($row['nombre']) ?></td>
          <td>
            <input type="number" step="0.01" name="precios[<?= $row['id'] ?>]" value="<?= $row['precio_por_hora'] ?>" required>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
    <div style="text-align:center;">
      <button type="submit">Salvar Cambios</button>
    </div>
  </form>
</body>
</html>

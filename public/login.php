<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    // Usuario y contraseña que vas a usar para el admin
    $usuarioAdmin = 'admin';
    $senhaAdmin = '1234';

    if ($usuario === $usuarioAdmin && $senha === $senhaAdmin) {
        $_SESSION['admin_logado'] = true;
        header('Location: admin.php'); // Redirige al panel
        exit();
    } else {
        $erro = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Login Admin - Pentagol</title>
<style>
  body { font-family: Arial, sans-serif; background: #f7f7f7; }
  .login-container {
    width: 300px; margin: 100px auto; background: white; padding: 20px;
    border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
  }
  input { width: 100%; padding: 10px; margin: 10px 0; }
  button {
    width: 100%; padding: 10px; background: #2c3e50; color: white; border: none; cursor: pointer;
  }
  button:hover { background: #34495e; }
  .error { color: red; }
</style>
</head>
<body>
  <div class="login-container">
    <h2>Login Admin</h2>
    <?php if (isset($erro)) echo "<p class='error'>$erro</p>"; ?>
    <form method="POST" action="">
      <input type="text" name="usuario" placeholder="Usuario" required />
      <input type="password" name="senha" placeholder="Contraseña" required />
      <button type="submit">Entrar</button>
    </form>
  </div>
</body>
</html>

<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    $usuarioAdmin = 'admin';
    $senhaAdmin = '2023Pentagol';

    if ($usuario === $usuarioAdmin && $senha === $senhaAdmin) {
        $_SESSION['admin_logado'] = true;
        header('Location: admin.php');
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
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
<style>
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  body {
    font-family: Arial, sans-serif;
    background: #f0f2f5;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .login-container {
    background: #ffffff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 350px;
    text-align: center;
  }

  h2 {
    margin-bottom: 1rem;
    color: #333;
  }

  .error {
    color: red;
    margin-bottom: 1rem;
    font-size: 0.9rem;
  }

  input[type="text"],
  input[type="password"] {
    width: 100%;
    padding: 0.75rem;
    margin: 0.5rem 0;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
  }

  button {
    width: 100%;
    padding: 0.75rem;
    background-color: #007bff;
    border: none;
    border-radius: 8px;
    color: white;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  button:hover {
    background-color: #0056b3;
  }
  @media (max-width: 480px) {
  .login-container {
    padding: 1.5rem 1rem;
    max-width: 90%;
  }

  input[type="text"],
  input[type="password"],
  button {
    font-size: 0.95rem;
    padding: 0.65rem;
  }

  h2 {
    font-size: 1.2rem;
  }
}

/* Para tablets - largura média */
@media (min-width: 481px) and (max-width: 768px) {
  .login-container {
    max-width: 80%;
    padding: 2rem;
  }

  input[type="text"],
  input[type="password"],
  button {
    font-size: 1rem;
    padding: 0.75rem;
  }

  h2 {
    font-size: 1.4rem;
  }
}
  @media (max-width: 400px) {
  .login-container {
    padding: 1.5rem 1rem;
    max-width: 90%;
  }

  input[type="text"],
  input[type="password"],
  button {
    font-size: 0.95rem;
    padding: 0.65rem;
  }

  h2 {
    font-size: 1.25rem;
  }
}

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

<?php
$servidor = "sql110.infinityfree.com";
$usuario = "if0_39089337";
$senha = "HSNS2017";
$banco = "if0_39089337_pentagol";

$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "pentagol";
$conexao = new mysqli($servidor, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}
// Define o charset para evitar problemas com caracteres especiais$busca = '';
if (isset($_GET['busca']) && !empty(trim($_GET['busca']))) {
    $busca = trim($_GET['busca']);
    $busca_sql = "%$busca%";

    $stmt = $conexao->prepare("
        SELECT * FROM reservas
        WHERE cliente LIKE ? OR whatsapp LIKE ? OR email LIKE ?
        ORDER BY fecha DESC, hora DESC
    ");
    $stmt->bind_param("sss", $busca_sql, $busca_sql, $busca_sql);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $resultado = $conexao->query("SELECT * FROM reservas ORDER BY fecha DESC, hora DESC");
}

?>

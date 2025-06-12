<?php
// Dados reais do banco na Hostinger
//$servidor = "localhost";
//$usuario = "u359778512_HelderNovaes";
//$senha = "HSNS2017.n";
//$banco = "u359778512_pentagol";
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "pentagol"; // substitua pelo nome do seu banco de dados local

// Conexão
//$conexao = new mysqli($servidor, $usuario, $senha, $banco);
$conexao = new mysqli($servidor, $usuario, $senha, $banco);
// Verifica erro de conexão
if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

// Define charset para UTF-8
$conexao->set_charset("utf8");

// Busca (opcional, via GET)
$busca = '';
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

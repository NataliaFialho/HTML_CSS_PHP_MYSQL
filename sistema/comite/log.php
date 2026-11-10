<!-- Log de cadastrar usuarios -->

<?php
session_start();
$logs = [];
$erroLog = null;

if (!isset($_SESSION["matricula"])) {
    http_response_code(401);
    $erroLog = "É necessário estar autenticado para consultar os logs.";
    return;}

try {
    require_once __DIR__ . "/../conexao.php";
    $sqlLog = "SELECT id, operacao, matricula_usuario, tabela_afetada,DATE_FORMAT(data_hora, '%d/%m/%Y %H:%i:%s') AS data_hora FROM log ORDER BY data_hora DESC, id DESC";
    $consultaLog = $pdo->query($sqlLog);
    $logs = $consultaLog->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao consultar o log do Comitê: " . $e->getMessage());
    http_response_code(500);
    $erroLog = "Não foi possível carregar os registros do log.";
}

// Log de cadastrar usuarios
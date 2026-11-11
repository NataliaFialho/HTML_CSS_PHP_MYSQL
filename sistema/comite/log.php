<?php

// Log de cadastrar usuarios

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


// Log de cadastro de categorias de ações

try {
    $sqlLogCategorias = "SELECT id, operacao, matricula_usuario, tabela_afetada, DATE_FORMAT(data_hora, '%d/%m/%Y %H:%i:%s') AS data_hora FROM log WHERE tabela_afetada = 'categoria' ORDER BY data_hora DESC, id DESC";

    $consultaLogCategorias = $pdo->query($sqlLogCategorias);
    $logsCategorias = $consultaLogCategorias->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao consultar o log de categorias: " . $e->getMessage());
    http_response_code(500);
    $erroLogCategorias = "Não foi possível carregar os registros de cadastro de categorias.";
}
// Log de cadastro de categorias de ações

// Log de desativação de usuários

try {
    $sqlLogDesativacaoUsuarios = "SELECT log.id, log.operacao, log.matricula_usuario, log.tabela_afetada, DATE_FORMAT(log.data_hora, '%d/%m/%Y %H:%i:%s') AS data_hora FROM log WHERE log.tabela_afetada = 'usuarios' AND log.operacao LIKE 'Administrador desativou%' ORDER BY log.data_hora DESC, log.id DESC";

    $consultaLogDesativacaoUsuarios = $pdo->query($sqlLogDesativacaoUsuarios);
    $logsDesativacaoUsuarios = $consultaLogDesativacaoUsuarios->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao consultar o log de desativação de usuários: " . $e->getMessage());
    http_response_code(500);
    $erroLogDesativacaoUsuarios = "Não foi possível carregar os registros de desativação de usuários.";
}
// Log de desativação de usuários

// Log de edição de usuários

try {
    $sqlLogEdicaoUsuarios = "SELECT log.id, log.operacao, log.matricula_usuario, log.tabela_afetada, DATE_FORMAT(log.data_hora, '%d/%m/%Y %H:%i:%s') AS data_hora FROM log WHERE log.tabela_afetada = 'usuarios' AND log.operacao LIKE 'Administrador editou%' ORDER BY log.data_hora DESC, log.id DESC";

    $consultaLogEdicaoUsuarios = $pdo->query($sqlLogEdicaoUsuarios);
    $logsEdicaoUsuarios = $consultaLogEdicaoUsuarios->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao consultar o log de edição de usuários: " . $e->getMessage());
    http_response_code(500);
    $erroLogEdicaoUsuarios = "Não foi possível carregar os registros de edição de usuários.";
}
// Log de edição de usuários

// Log de validação de ações

try {
    $sqlLogValidacaoAcoes = "SELECT log.id, log.operacao, log.matricula_usuario, log.tabela_afetada, DATE_FORMAT(log.data_hora, '%d/%m/%Y %H:%i:%s') AS data_hora FROM log WHERE log.tabela_afetada = 'acao' AND log.operacao LIKE 'Validador%' ORDER BY log.data_hora DESC, log.id DESC";

    $consultaLogValidacaoAcoes = $pdo->query($sqlLogValidacaoAcoes);
    $logsValidacaoAcoes = $consultaLogValidacaoAcoes->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao consultar o log de validação de ações: " . $e->getMessage());
    http_response_code(500);
    $erroLogValidacaoAcoes = "Não foi possível carregar os registros de validação de ações.";
}
// Log de validação de ações

// Log das minhas ações

try {
    $sqlLogMinhasAcoes = "SELECT log.id, log.operacao, log.matricula_usuario, log.tabela_afetada, DATE_FORMAT(log.data_hora, '%d/%m/%Y %H:%i:%s') AS data_hora FROM log WHERE log.matricula_usuario = :matricula ORDER BY log.data_hora DESC, log.id DESC";

    $consultaLogMinhasAcoes = $pdo->prepare($sqlLogMinhasAcoes);
    $consultaLogMinhasAcoes->execute([":matricula" => $_SESSION["matricula"]]);
    $logsMinhasAcoes = $consultaLogMinhasAcoes->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao consultar o log das minhas ações: " . $e->getMessage());
    http_response_code(500);
    $erroLogMinhasAcoes = "Não foi possível carregar os registros das minhas ações.";
}
// Log das minhas ações

// session_start();
// $logs = [];
// $erroLog = null;

// if (!isset($_SESSION["matricula"])) {
//     http_response_code(401);
//     $erroLog = "É necessário estar autenticado para consultar os logs.";
//     return;}

// try {
//     require_once __DIR__ . "/../conexao.php";
//     $sqlLog = "SELECT id, operacao, matricula_usuario, tabela_afetada,DATE_FORMAT(data_hora, '%d/%m/%Y %H:%i:%s') AS data_hora FROM log ORDER BY data_hora DESC, id DESC";
//     $consultaLog = $pdo->query($sqlLog);
//     $logs = $consultaLog->fetchAll(PDO::FETCH_ASSOC);

// } catch (PDOException $e) {
//     error_log("Erro ao consultar o log do Comitê: " . $e->getMessage());
//     http_response_code(500);
//     $erroLog = "Não foi possível carregar os registros do log.";
// }
?> 

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOG</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body class="paineldecadastrarusuarios-pagina">

    <header class="paineldecriaracoes-cabecalho">
        <img src="../imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="paineldecriaracoes-logo">
        <h1 class="paineldecriaracoes-titulo-pesquisador">COMITÊ</h1>
        <a href="../index.php" class="cadastrocategoria-btn-sair">Sair</a>
    </header>
<main>
    <div class="painel-minha-acoes-container-card">
        <?php
        require_once '../conexao.php';

        try {
            $query = "SELECT 
                        u.nome AS usuario_ativo, 
                        l.matricula_usuario AS matricula, 
                        l.data_hora, 
                        l.operacao, 
                        l.tabela_afetada 
                      FROM log l
                      INNER JOIN usuarios u ON l.matricula_usuario = u.matricula
                      ORDER BY l.data_hora DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($logs)):
                foreach ($logs as $log): 
                    $dataFormatada = date('d/m/Y H:i:s', strtotime($log['data_hora']));
            ?>
                    <div class="painel-linhas-acoes-card-pesquisa">
                        <h3 class="card-titulo"><?= htmlspecialchars($log['usuario_ativo']) ?></h3>
                        <p class="card-categoria">Matrícula: <?= htmlspecialchars($log['matricula']) ?></p>
                        <p class="card-data">Data e hora: <?= htmlspecialchars($dataFormatada) ?></p>
                        <p class="card-descricao">Operação: <?= htmlspecialchars($log['operacao']) ?></p>
                        <p class="card-status status-analise">Tabela Afetada: <?= htmlspecialchars($log['tabela_afetada']) ?></p>
                    </div>
            <?php 
                endforeach; 
            else:
                echo "<p style='color: black; padding: 20px;'>Nenhum registro de log encontrado.</p>";
            endif;

        } catch (PDOException $e) {
            echo "<p style='color: red; padding: 20px;'>Erro ao carregar os dados: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?> 
    </div>
</main>
</body>
</html>
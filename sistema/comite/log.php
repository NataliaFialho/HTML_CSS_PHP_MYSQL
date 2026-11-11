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


 // Log de edição de categorias de ações

try {
    $sqlLogEdicaoCategorias = "SELECT id, operacao, matricula_usuario, tabela_afetada, DATE_FORMAT(data_hora, '%d/%m/%Y %H:%i:%s') AS data_hora FROM log WHERE tabela_afetada = 'categoria' AND operacao LIKE 'Administrador editou uma categoria de ação:%' ORDER BY data_hora DESC, id DESC";
    $consultaLogEdicaoCategorias = $pdo->query($sqlLogEdicaoCategorias);
    $logsEdicaoCategorias = $consultaLogEdicaoCategorias->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao consultar o log de edição de categorias: " . $e->getMessage());
    http_response_code(500);
    $erroLogEdicaoCategorias = "Não foi possível carregar os registros de edição de categorias.";
}
 // Log de edição de categorias de ações


 // Log de edição de usuários

try {
    $sqlLogEdicaoUsuarios = "SELECT id, operacao, matricula_usuario, tabela_afetada, DATE_FORMAT(data_hora, '%d/%m/%Y %H:%i:%s') AS data_hora FROM log WHERE tabela_afetada = 'usuarios, usuario_cargo' AND operacao LIKE 'Administrador editou um usuário:%' ORDER BY data_hora DESC, id DESC";
    $consultaLogEdicaoUsuarios = $pdo->query($sqlLogEdicaoUsuarios);
    $logsEdicaoUsuarios = $consultaLogEdicaoUsuarios->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Erro ao consultar o log de edição de usuários: " . $e->getMessage());
    http_response_code(500);
    $erroLogEdicaoUsuarios = "Não foi possível carregar os registros de edição de usuários.";
}
 // Log de edição de usuários

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

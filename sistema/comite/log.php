<?php
session_start();

if (!isset($_SESSION['matricula'])) {
    header('Location: ../index.php');
    exit;
}

require_once __DIR__ . '/../conexao.php';

$logs = [];
$erroLog = '';

try {
    $sqlLog = "SELECT COALESCE(u.nome, 'Usuário não encontrado') AS nome_usuario, l.matricula_usuario, l.operacao, l.tabela_afetada, DATE_FORMAT(l.data_hora, '%d/%m/%Y %H:%i:%s') AS data_hora FROM log l LEFT JOIN usuarios u ON u.matricula = l.matricula_usuario ORDER BY l.data_hora DESC, l.id DESC";
    $consulta = $pdo->query($sqlLog);
    $logs = $consulta->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Erro ao consultar o log: ' . $e->getMessage());
    $erroLog = 'Não foi possível carregar os registros do log.';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilo.css?v=<?= filemtime(__DIR__ . '/../css/estilo.css') ?>">
</head>

<body class="paineldecadastrarusuarios-pagina">
    <header class="paineldecriaracoes-cabecalho">
        <img src="../imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="paineldecriaracoes-logo">
        <h1 class="paineldecriaracoes-titulo-pesquisador">COMITÊ</h1>
        <a href="../tela_dois_cargos.php" class="cadastrocategoria-btn-sair">Sair</a>
    </header>

    <main>
        <div class="painel-minha-acoes-container-card">

            <?php if ($erroLog !== ''): ?>
                <p role="alert"><?= htmlspecialchars($erroLog, ENT_QUOTES, 'UTF-8') ?></p>

            <?php elseif (empty($logs)): ?>
                <p style="color: black; padding: 20px;">Nenhum registro de log encontrado.</p>

            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <div class="painel-linhas-acoes-card-pesquisa">
                        <h3 class="card-titulo">
                            <?= htmlspecialchars($log['nome_usuario'], ENT_QUOTES, 'UTF-8') ?>
                        </h3>
                        <p class="card-categoria">
                            Matrícula: <?= htmlspecialchars((string) $log['matricula_usuario'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <p class="card-data">
                            Data e hora: <?= htmlspecialchars($log['data_hora'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <p class="card-descricao">
                            Operação: <?= htmlspecialchars($log['operacao'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                        <p class="card-status status-analise">
                            Tabela afetada: <?= htmlspecialchars($log['tabela_afetada'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </main>
</body>
</html>
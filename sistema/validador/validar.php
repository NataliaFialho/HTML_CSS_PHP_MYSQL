<?php
require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/../auth.php';

$matriculaValidador = exigirCargo($pdo, 2);

$erroAtualizacao = '';
$erroBusca = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao_id = filter_var($_POST['acao_id'] ?? '', FILTER_VALIDATE_INT);
    $novo_status = $_POST['novo_status'] ?? '';

    if ($acao_id !== false && in_array($novo_status, ['Aprovada', 'Reprovada'], true)) {
        try {
            $pdo->beginTransaction();

            // Só altera ações que ainda estão em análise
            $stmtUpdate = $pdo->prepare("UPDATE acao SET status = :status WHERE id = :id AND status = 'Em Análise'");
            $stmtUpdate->execute([
                ':status' => $novo_status,
                ':id' => $acao_id
            ]);

            if ($stmtUpdate->rowCount() > 0) {
                $stmtTitulo = $pdo->prepare('SELECT titulo FROM acao WHERE id = :id');
                $stmtTitulo->execute([':id' => $acao_id]);
                $tituloAcao = (string) $stmtTitulo->fetchColumn();

                $verbo = $novo_status === 'Aprovada' ? 'aprovou' : 'reprovou';
                $operacaoLog = mb_substr('Validador ' . $verbo . ' a ação: ' . $tituloAcao, 0, 100, 'UTF-8');

                $logStmt = $pdo->prepare('INSERT INTO log (operacao, matricula_usuario, tabela_afetada, data_hora) VALUES (:operacao, :matricula, :tabela, NOW())');
                $logStmt->execute([':operacao' => $operacaoLog, ':matricula' => $matriculaValidador, ':tabela' => 'acao']);

                $pdo->commit();
                header('Location: validar.php');
                exit;
            }

            $pdo->rollBack();
            $erroAtualizacao = 'Essa ação não está mais em análise ou não foi encontrada.';
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Erro ao atualizar status da ação: ' . $e->getMessage());
            $erroAtualizacao = 'Não foi possível atualizar o status da ação. Tente novamente.';
        }
    }
}

try {
    $sqlAcoes = "SELECT 
                    a.id, 
                    a.titulo, 
                    a.descricao, 
                    a.data, 
                    a.status, 
                    c.nome AS categoria_nome, 
                    u.nome AS pesquisador_nome 
                 FROM acao a
                 JOIN categoria c ON a.id_categoria = c.id
                 JOIN usuarios u ON a.matricula_pesquisador = u.matricula
                 ORDER BY (a.status = 'Em Análise') DESC, a.data DESC, a.id DESC";
                 
    $stmtAcoes = $pdo->prepare($sqlAcoes);
    $stmtAcoes->execute();
    $acoes = $stmtAcoes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $acoes = [];
    error_log('Erro ao carregar ações: ' . $e->getMessage());
    $erroBusca = 'Não foi possível carregar as ações.';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validador de Ações</title>
    <link rel="stylesheet" href="../css/estilo.css?v=<?= filemtime(__DIR__ . '/../css/estilo.css') ?>">
</head>
<body class="validaracao-pagina">
    <header class="validaracao-cabecalho">
        <img src="../imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="validaracao-logo">
        <h1 class="validaracao-titulo-administrador">VALIDADOR</h1>
        <a href="../tela_dois_cargos.php" class="validaracao-btn-sair">Sair</a>
    </header>

    <section class="validaracao-faixa-titulo">
        <h2 class="validaracao-titulo-acoes">VALIDAR AÇÕES</h2>
    </section>

    <main class="validaracao-area-principal">
        <section class="validaracao-lista" aria-label="Lista de ações para validação">

            <?php if (!empty($erroAtualizacao)): ?>
                <p style="color: red; margin: 10px;"><?= htmlspecialchars($erroAtualizacao) ?></p>
            <?php endif; ?>

            <?php if (!empty($erroBusca)): ?>
                <p style="color: red; margin: 10px;"><?= htmlspecialchars($erroBusca) ?></p>
            <?php endif; ?>

            <?php if (empty($acoes)): ?>
                <p style="margin: 20px; font-weight: bold;">Nenhuma ação cadastrada no momento.</p>
            <?php else: ?>
                <?php foreach ($acoes as $acao): ?>
                    <?php 
                        $statusClass = 'validaracao-status-em-analise';
                        if ($acao['status'] === 'Aprovada' || $acao['status'] === 'Aprovado') {
                            $statusClass = 'validaracao-status-aprovado';
                        } elseif ($acao['status'] === 'Reprovada' || $acao['status'] === 'Reprovado') {
                            $statusClass = 'validaracao-status-reprovado';
                        }
                    ?>
                    <article class="validaracao-card">
                        <div class="validaracao-card-cabecalho">
                            <div class="validaracao-info-grupo">
                                <span class="validaracao-item-info"><strong><?= htmlspecialchars($acao['pesquisador_nome']) ?></strong></span>
                                <span class="validaracao-item-info"><strong><?= htmlspecialchars($acao['titulo']) ?></strong></span>
                                <span class="validaracao-item-info"><strong><?= htmlspecialchars($acao['categoria_nome']) ?></strong></span>
                            </div>
                            <div class="validaracao-data-status">
                                <span class="validaracao-data"><strong><?= date('d/m/Y', strtotime($acao['data'])) ?></strong></span>
                                <div class="validaracao-status <?= $statusClass ?>" role="status"><?= htmlspecialchars($acao['status']) ?></div>
                            </div>
                        </div>
                        <div class="validaracao-card-corpo">
                            <p class="validaracao-descricao"><?= htmlspecialchars($acao['descricao']) ?></p>

                            <?php if ($acao['status'] === 'Em Análise'): ?>
                                <form method="POST" class="validaracao-acoes-card">
                                    <input type="hidden" name="acao_id" value="<?= (int) $acao['id'] ?>">

                                    <button
                                        type="button"
                                        class="validaracao-btn-invalidar"
                                        onclick="abrirPopupInvalidacao(this.form)">
                                        REPROVAR
                                    </button>

                                    <button
                                        type="button"
                                        class="validaracao-btn-validar"
                                        onclick="abrirPopupValidacao(this.form)">
                                        APROVAR
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>

        </section>
    </main>

<?php include __DIR__ . '/popup_confirmar_validacao.php'; ?>    
<?php include __DIR__ . '/popup_confirmar_invalidacao_acao.php'; ?>    
</body>
</html>
<?php
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_id'], $_POST['novo_status'])) {
    $acao_id = $_POST['acao_id'];
    $novo_status = $_POST['novo_status'];

    if (in_array($novo_status, ['Aprovado', 'Reprovado'])) {
        try {
            $sqlUpdate = "UPDATE acao SET status = :status WHERE id = :id";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([
                ':status' => $novo_status,
                ':id' => $acao_id
            ]);
        } catch (PDOException $e) {
            $erroAtualizacao = "Erro ao atualizar status: " . $e->getMessage();
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
                 ORDER BY a.data DESC";
                 
    $stmtAcoes = $pdo->prepare($sqlAcoes);
    $stmtAcoes->execute();
    $acoes = $stmtAcoes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $acoes = [];
    $erroBusca = "Erro ao carregar ações: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validador de Ações</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body class="validaracao-pagina">
    <header class="validaracao-cabecalho">
        <img src="../imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="validaracao-logo">
        <h1 class="validaracao-titulo-administrador">VALIDADOR</h1>
        <a href="../index.php" class="validaracao-btn-sair">Sair</a>
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
                        if ($acao['status'] === 'Aprovado') {
                            $statusClass = 'validaracao-status-aprovado';
                        } elseif ($acao['status'] === 'Reprovado') {
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
                                    <input type="hidden" name="acao_id" value="<?= $acao['id'] ?>">

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
</body>
</html>

<?php
session_start();
require_once '../conexao.php'; 

// 1. Só entra quem está logado
if (!isset($_SESSION['matricula'])) {
    header('Location: login.php'); // ajuste para sua tela de login
    exit;
}

// 2. Busca as ações DESTA matrícula
$sql = "SELECT a.id, a.titulo, a.descricao, a.data, a.status, c.nome AS categoria_nome
        FROM acao a
        INNER JOIN categoria c ON c.id = a.id_categoria
        WHERE a.matricula_pesquisador = ?
        ORDER BY a.data DESC, a.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['matricula']]);
$acoes = $stmt->fetchAll();

// 3. Status do banco -> classe de CSS
$classes = [
    'Em Análise' => 'analise',
    'Aprovada'   => 'aprovada',
    'Negada'     => 'negada',
];
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../sistema/css/estilo.css">
    <title>Minhas Ações</title>
</head>
<body>    
    <header class="paineldecriaracoes-cabecalho">
        <img src="..//imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="paineldecriaracoes-logo">
        <h1 class="paineldecriaracoes-titulo-pesquisador">PESQUISADOR</h1>
        <button type="button" class="paineldecriaracoes-btn-sair">Sair</button>
    </header>
    
    <div class="painel-minhas-acoes-subcabecalho">
        <h2>Minhas ações</h2>
    </div>

    <div class="painel-minhas-acoes-container-card">

        <?php if (empty($acoes)): ?>
            <p class="painel-minhas-acoes-vazio">Nenhuma ação cadastrada</p>

        <?php else: ?>
            <?php foreach ($acoes as $a): ?>
                <div class="painel-minhas-acoes-card-pesquisa">
                    <h3 class="card-titulo"><?= htmlspecialchars($a['titulo']) ?></h3>
                    <p class="card-categoria"><?= htmlspecialchars($a['categoria_nome']) ?></p>
                    <p class="card-data"><?= date('d/m/Y', strtotime($a['data'])) ?></p>

                    <p class="card-descricao"><?= htmlspecialchars($a['descricao']) ?></p>

                    <p class="card-status status-<?= $classes[$a['status']] ?? 'analise' ?>">
                        <?= htmlspecialchars($a['status']) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>



<!-- 
    <div class="painel-minhas-acoes-container-card">
        <div class="painel-minhas-acoes-card-pesquisa">
            <h3 class="card-titulo">Título</h3>
            <p class="card-categoria"></p>
            <p class="card-data"></p>

            <p class="card-descricao">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis ut sunt, et exercitationem necessitatibus nihil fugit soluta labore iusto eum eos quo aperiam tenetur hic assumenda est eveniet minus. Et.
            </p>
            
            <p class="card-status"></p>
        </div>
    </div> -->

</body>
</html>


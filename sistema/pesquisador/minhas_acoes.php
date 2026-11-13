<?php
require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/../auth.php';

$matricula = exigirCargo($pdo, 4);

$sql = "SELECT a.id, a.titulo, a.descricao, a.data, a.status, c.nome AS categoria_nome
        FROM acao a
        INNER JOIN categoria c ON c.id = a.id_categoria
        WHERE a.matricula_pesquisador = ?
        ORDER BY a.data DESC, a.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$matricula]);
$acoes = $stmt->fetchAll();

$classes = [
    'Em Análise' => 'analise',
    'Aprovada'   => 'aprovada',
    'Reprovada'  => 'reprovada',
    'Aprovado'   => 'aprovada',
    'Reprovado'  => 'reprovada',
    'Negada'     => 'reprovada',
];
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilo.css?v=<?= filemtime(__DIR__ . '/../css/estilo.css') ?>">
    <title>Minhas Ações</title>
</head>

<body class="paineldecadastrarusuarios-pagina">

    <header class="paineldecriaracoes-cabecalho">
        <img src="../imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="paineldecriaracoes-logo">
        <h1 class="paineldecriaracoes-titulo-pesquisador">PESQUISADOR</h1>
        <a href="../tela_dois_cargos.php" class="cadastrocategoria-btn-sair">Sair</a>
    </header>

    <div class="painel-minhas-acoes-subcabecalho">
        <h2>Minhas ações</h2>
    </div>

    <div class="usuario-lista-voltar">
        <a href="painel_pesquisador.php" class="gerenciacategoria-btn-editar">VOLTAR</a>
    </div>

    <main>
        <div class="painel-minhas-acoes-container-card">

            <?php if (empty($acoes)): ?>

                <p class="painel-minhas-acoes-vazio">
                    Nenhuma ação cadastrada.
                </p>

            <?php else: ?>

                <?php foreach ($acoes as $a): ?>

                    <div class="painel-minhas-acoes-card-pesquisa">

                        <h3 class="card-titulo">
                            <?= htmlspecialchars($a['titulo']) ?>
                        </h3>

                        <p class="card-categoria">
                            Categoria: <?= htmlspecialchars($a['categoria_nome']) ?>
                        </p>

                        <p class="card-data">
                            Data: <?= date('d/m/Y', strtotime($a['data'])) ?>
                        </p>

                        <p class="card-descricao">
                            <?= htmlspecialchars($a['descricao']) ?>
                        </p>

                        <p class="card-status status-<?= $classes[$a['status']] ?? 'analise' ?>">
                            Status: <?= htmlspecialchars($a['status']) ?>
                        </p>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </main>

</body>
</html>

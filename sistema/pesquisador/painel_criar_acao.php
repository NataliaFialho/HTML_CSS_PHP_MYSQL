<?php

require_once '../conexao.php';
$sql = "SELECT id, nome FROM categoria WHERE status = 1 ORDER BY nome";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$categorias = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova ação</title>
    <link rel="stylesheet" href="../../sistema/css/estilo.css">
</head>
<body class="paineldecriaracoes-pagina">

    <header class="paineldecriaracoes-cabecalho">
        <img src="imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="paineldecriaracoes-logo">
        <h1 class="paineldecriaracoes-titulo-pesquisador">PESQUISADOR</h1>
        <button type="button" class="paineldecriaracoes-btn-sair">Sair</button>
    </header>

    <section class="paineldecriaracoes-barra-titulo">
        <h2 class="paineldecriaracoes-titulo-acoes">MINHAS AÇÕES</h2>
    </section>

    <section class="paineldecriaracoes-barra-nova-acao">
        <h2 class="paineldecriaracoes-titulo-nova-acao">NOVA AÇÃO</h2>
    </section>

    <main class="paineldecriaracoes-conteudo">
        <form class="paineldecriaracoes-formulario" method="POST" action="">
            <div class="paineldecriaracoes-linha-superior">
                <input type="text" name="titulo" class="paineldecriaracoes-campo-titulo" placeholder="TÍTULO DA AÇÃO" maxlength="50" required>
                <input type="date" name="data_realizacao" class="paineldecriaracoes-campo-data" required>
            </div>

            <textarea name="descricao" class="paineldecriaracoes-campo-descricao" placeholder="DESCRIÇÃO" maxlength="100" required></textarea>

            <select name="categoria" class="paineldecriaracoes-campo-categoria" required>
                <option value="" disabled selected>CATEGORIA DA AÇÃO</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars((string) $categoria['id']) ?>">
                        <?= htmlspecialchars($categoria['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if (empty($categorias)): ?>
                <p class="paineldecriaracoes-aviso-categorias">Nenhuma categoria ativa foi encontrada. Cadastre uma categoria no banco de dados.</p>
            <?php endif; ?>

            <div class="paineldecriaracoes-btn">
                <button type="button" class="paineldecriaracoes-btn-cancelar" onclick="window.location.href='painel_pesquisador.php'">Cancelar</button>
                <button type="submit" class="paineldecriaracoes-btn-confirmar">Confirmar</button>
            </div>
        </form>
    </main>
</body>
</html>
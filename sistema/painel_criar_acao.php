<?php

if (!isset($categorias)) {
    $categorias = [];
}?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

    <header class="paineldecriaracoes-cabecalho">
        <img src="imagens/logo_embrapa.png" alt="Embrapa Gado de Corte" class="paineldecriaracoes-logo">
        <h1 class="paineldecriaracoes-titulo-pesquisador">PESQUISADOR</h1>
        <button type="button" class="paineldecriaracoes-btn-sair">Sair</button>
    </header>

    <section class="paineldecriaracoes-barra-titulo">
        <h2 class="paineldecriaracoes-titulo-acoes">MINHAS AÇÕES</h2>
    </section>

    <section class="paineldecriaracoes-barra-nova-acao">
        <h2 class="paineldecriaracoes-titulo-nova-acao">NOVA AÇÃO</h2>
    </section>

    <main>
        <form class="paineldecriaracoes-formulario" method="POST" action="">

            <div class="paineldecriaracoes-linha-superior">
                <input type="text" name="titulo" class="paineldecriaracoes-campo-titulo" placeholder="TÍTULO DA AÇÃO" required>
                <input type="date" name="data_realizacao" class="paineldecriaracoes-campo-data" required>
            </div>

            <textarea name="descricao" class="paineldecriaracoes-campo-descricao" placeholder="DESCRIÇÃO" required></textarea>

            <select name="categoria" class="paineldecriaracoes-campo-categoria" required>

                <option value="" disabled selected>CATEGORIA DA AÇÃO</option>

                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria['id']) ?>">
                        <?= htmlspecialchars($categoria['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="paineldecriaracoes-btn">
                <button type="reset" class="paineldecriaracoes-btn-cancelar">Cancelar</button>
                <button type="submit" class="paineldecriaracoes-btn-confirmar">Confirmar</button>
            </div>

        </form>
    </main>
</body>
</html>
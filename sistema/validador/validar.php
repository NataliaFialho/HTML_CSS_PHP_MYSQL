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
        <section class="validaracao-lista" aria-label="Exemplos de ações para validação">
            <!-- Cartões demonstrativos: substituir os textos pelos dados fornecidos pelo backend. -->
            <article class="validaracao-card">
                <div class="validaracao-card-cabecalho">
                    <div class="validaracao-info-grupo">
                        <span class="validaracao-item-info"><strong>[Nome do pesquisador]</strong></span>
                        <span class="validaracao-item-info"><strong>[Título da ação]</strong></span>
                        <span class="validaracao-item-info"><strong>[Categoria]</strong></span>
                    </div>
                    <span class="validaracao-data"><strong>[Data]</strong></span>
                </div>

                <div class="validaracao-card-corpo">
                    <p class="validaracao-descricao">[Descrição da ação]</p>
                    <div class="validaracao-status validaracao-status-em-analise" role="status">
                        Status: Em Análise
                    </div>
                    <div class="validaracao-acoes-card">
                        <button type="button" class="validaracao-btn-invalidar">INVALIDAR</button>
                        <button type="button" class="validaracao-btn-validar">VALIDAR</button>
                    </div>
                </div>
            </article>

            <article class="validaracao-card">
                <div class="validaracao-card-cabecalho">
                    <div class="validaracao-info-grupo">
                        <span class="validaracao-item-info"><strong>[Nome do pesquisador]</strong></span>
                        <span class="validaracao-item-info"><strong>[Título da ação]</strong></span>
                        <span class="validaracao-item-info"><strong>[Categoria]</strong></span>
                    </div>
                    <span class="validaracao-data"><strong>[Data]</strong></span>
                </div>

                <div class="validaracao-card-corpo">
                    <p class="validaracao-descricao">[Descrição da ação]</p>
                    <div class="validaracao-status validaracao-status-aprovado" role="status">
                        Status: Aprovado
                    </div>
                </div>
            </article>

            <article class="validaracao-card">
                <div class="validaracao-card-cabecalho">
                    <div class="validaracao-info-grupo">
                        <span class="validaracao-item-info"><strong>[Nome do pesquisador]</strong></span>
                        <span class="validaracao-item-info"><strong>[Título da ação]</strong></span>
                        <span class="validaracao-item-info"><strong>[Categoria]</strong></span>
                    </div>
                    <span class="validaracao-data"><strong>[Data]</strong></span>
                </div>

                <div class="validaracao-card-corpo">
                    <p class="validaracao-descricao">[Descrição da ação]</p>
                    <div class="validaracao-status validaracao-status-reprovado" role="status">
                        Status: Reprovado
                    </div>
                </div>
            </article>
        </section>
    </main>
</body>
</html>
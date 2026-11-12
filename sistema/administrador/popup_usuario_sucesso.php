<?php
// if (isset($cadastroSucesso) && $cadastroSucesso === true):
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>

    <div class="popupusuariosucesso-overlay">
        <section class="popupusuariosucesso-container">
            <div class="popupusuariosucesso-icone">
                <img src="../imagens/correto.png" alt="Sucesso" class="popupusuariosucesso-icone-imagem">
            </div>

            <h2 class="popupusuariosucesso-titulo">Usuário cadastrado com sucesso!</h2>
            <p class="popupusuariosucesso-mensagem">Esse usuário foi cadastrado com sucesso!</p>
            <a href="tela_escolha.php"class="popupusuariosucesso-btn">Voltar para o painel principal</a>
        </section>

    </div>

</body>
</html>

<?php // endif; ?>
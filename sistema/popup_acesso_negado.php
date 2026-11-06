<?php
/* No arquivo responsável pelo login, define $mostrarPopupAcessoNegado
 * como true quando a validação falhar e coloque esse arquivo.*/

if (isset($mostrarPopupAcessoNegado) && $mostrarPopupAcessoNegado === true):
?>

<div class="popupdeacessonegado-overlay" id="popupdeacessonegado-overlay">
    <section class="popupdeacessonegado-container" role="alertdialog" aria-modal="true" aria-labelledby="popupdeacessonegado-titulo" aria-describedby="popupdeacessonegado-mensagem">

        <div class="popupdeacessonegado-icone">
            <img src="imagens/errado.png" alt="Acesso negado" class="popupdeacessonegado-icone-imagem">
        </div>

        <h2 class="popupdeacessonegado-titulo" id="popupdeacessonegado-titulo"> Acesso negado!</h2>

        <p class="popupdeacessonegado-mensagem" id="popupdeacessonegado-mensagem"> Seu email ou senha estão incorretos! Ou, você<br class="popupdeacessonegado-quebra"> não tem acesso ao sistema.</p>

        <button type="button" class="popupdeacessonegado-btn" id="popupdeacessonegado-btn-ok">OK</button>
    </section>
</div>

<script>
    document
        .getElementById('popupdeacessonegado-btn-ok')
        .addEventListener('click', function () {
            document.getElementById('popupdeacessonegado-overlay').remove();
        });
</script>

<?php endif; ?>
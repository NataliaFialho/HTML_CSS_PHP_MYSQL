






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
        .getElementById('popupdeacessonegado-btn-ok').addEventListener('click', function () {
            document.getElementById('popupdeacessonegado-overlay').remove();});
</script>

<?php endif; ?>




<!-- 
<div class="popupdeacessonegado-overlay"
     id="popupdesativar-overlay"
     style="display: none;">

    <section class="popupdeacessonegado-container"
             role="alertdialog"
             aria-modal="true">

        <div class="popupdeacessonegado-icone">
            <img src="../imagens/correto.png"
                 alt="Correto"
                 class="popupdeacessonegado-icone-imagem">
        </div>

        <h2 class="popupdeacessonegado-titulo">
            Categoria editada com sucesso!
        </h2>

        <p class="popupdeacessonegado-mensagem">
            A categoria foi editada com sucesso!
        </p>
<!-- 
        <div class="popupdeacessonegadoacao-btn">
        <button type="button"
            class="button-desativar"
            id="popupdesativar-cancelar">
            Cancelar
        </button> -->

<!-- 
        <button type="button"
            class="button-desativar"
            id="popupdesativar-confirmar">
            Voltar para o painel principal
        </button>
        </div>


    </section>
</div>

<script>
(function () {

    const overlay = document.getElementById('popupdesativar-overlay');
    const cancelar = document.getElementById('popupdesativar-cancelar');
    const confirmar = document.getElementById('popupdesativar-confirmar');

    let formularioSelecionado = null;

    if (!overlay || !cancelar || !confirmar) {
        console.error('Elementos do popup não encontrados.');
        return;
    }

    document.addEventListener('submit', function(event) {

        const formulario = event.target;

        if (formulario.classList.contains('gerenciacategoria-form-desativar')) {

            event.preventDefault();

            formularioSelecionado = formulario;

            overlay.style.display = 'flex';
        }

    }, true);

    cancelar.addEventListener('click', function() {

        overlay.style.display = 'none';

        formularioSelecionado = null;

    });

    confirmar.addEventListener('click', function() {

        if (formularioSelecionado) {

            const formulario = formularioSelecionado;

            formularioSelecionado = null;

            formulario.submit();

        }

    });

})();
</script> --> -->

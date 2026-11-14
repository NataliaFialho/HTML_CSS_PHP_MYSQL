<div class="popupdeacessonegado-overlay"
     id="popup-invalidacao-overlay"
     style="display: none;">

    <section class="popupdeacessonegado-container"
             role="alertdialog"
             aria-modal="true"
             aria-labelledby="popup-invalidacao-titulo">

        <div class="popupdeacessonegado-icone">
            <img src="../imagens/atencao.png"
                 alt="Atenção"
                 class="popupdeacessonegado-icone-imagem">
        </div>

        <h2 class="popupdeacessonegado-titulo"
            id="popup-invalidacao-titulo">
            Reprovar Ação?
        </h2>

        <div class="popupconfirmarinvaliacao-div--validador">
            <p class="popupdeacessonegado-mensagem">
                Essa ação será reprovada e não terá mais valor.
            </p>
        </div>

        <div class="popupdeacessonegadoacao-btn">

            <button type="button"
                    class="button-desativar"
                    id="popup-invalidacao-cancelar">
                Cancelar
            </button>

            <button type="button"
                    class="button-desativar"
                    id="popup-invalidacao-confirmar">
                Confirmar
            </button>

        </div>
    </section>
</div>

<script>
(function () {

    const overlay = document.getElementById('popup-invalidacao-overlay');
    const cancelar = document.getElementById('popup-invalidacao-cancelar');
    const confirmar = document.getElementById('popup-invalidacao-confirmar');

    let formularioSelecionado = null;

    if (!overlay || !cancelar || !confirmar) {
        console.error('Elementos do popup não encontrados.');
        return;
    }

    window.abrirPopupInvalidacao = function(formulario) {
        formularioSelecionado = formulario;
        overlay.style.display = 'flex';
    };

    cancelar.addEventListener('click', function () {
        overlay.style.display = 'none';
        formularioSelecionado = null;
    });

    confirmar.addEventListener('click', function () {

        if (formularioSelecionado) {

            const formulario = formularioSelecionado;

            formularioSelecionado = null;

            const campoStatus = document.createElement('input');
            campoStatus.type = 'hidden';
            campoStatus.name = 'novo_status';
            campoStatus.value = 'Reprovada';

            formulario.appendChild(campoStatus);

            overlay.style.display = 'none';

            formulario.submit();
        }

    });

})();
</script>
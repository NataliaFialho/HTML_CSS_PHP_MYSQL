
<div class="popupdeacessonegado-overlay"
     id="popupdesativar-overlay"
     style="display: none;">

    <section class="popupdeacessonegado-container"
             role="alertdialog"
             aria-modal="true"
             aria-labelledby="popupdesativar-titulo">

        <div class="popupdeacessonegado-icone">
            <img src="../imagens/atencao.png"
                 alt="Atenção"
                 class="popupdeacessonegado-icone-imagem">
        </div>

        <h2 class="popupdeacessonegado-titulo"
            id="popupdesativar-titulo">
            Confirmar Validação?
        </h2>

        <div class="popupconfirmarinvalidacao-div--validador">
            <p class="popupdeacessonegado-mensagem">

            </p>
        </div>

        <div class="popupdeacessonegadoacao-btn">

            <button type="button"
                    class="button-desativar"
                    id="popupdesativar-cancelar">
                Cancelar
            </button>

            <button type="button"
                    class="button-desativar"
                    id="popupdesativar-confirmar--validador">
                Confirmar
            </button>

        </div>
    </section>
</div>

<script>
(function () {

    const overlay = document.getElementById('popupdesativar-overlay');
    const cancelar = document.getElementById('popupdesativar-cancelar');
    const confirmar = document.getElementById('popupdesativar-confirmar--validador');

    let formularioSelecionado = null;

    if (!overlay || !cancelar || !confirmar) {
        console.error('Elementos do popup não encontrados.');
        return;
    }

    window.abrirPopupValidacao = function(formulario) {
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
            campoStatus.value = 'Aprovado';

            formulario.appendChild(campoStatus);

            overlay.style.display = 'none';

            formulario.submit();
        }

    });

})();
</script>

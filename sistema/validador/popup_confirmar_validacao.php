<div class="popupvalidacao-overlay"
     id="popupdesativar-overlay"
     style="display: none;">

    <section class="popupvalidacao-container"
             role="alertdialog"
             aria-modal="true"
             aria-labelledby="popupdesativar-titulo">

        <div class="popupvalidacao-icone">
            <img src="../imagens/atencao.png"
                 alt="Atenção"
                 class="popupvalidacao-icone-imagem">
        </div>

        <h1 class="popupvalidacao-titulo"
            id="popupdesativar-titulo">
            Confirmar Validação?
        </h1>

        <p class="popupvalidacao-mensagem">
            Essa ação não poderá ser desfeita.
        </p>

        <div class="popupvalidacao-botoes">
            <button type="button"
                    class="popupvalidacao-btn-cancelar"
                    id="popupdesativar-cancelar">
                Cancelar
            </button>

            <button type="button"
                    class="popupvalidacao-btn-confirmar"
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
            campoStatus.value = 'Aprovada';

            formulario.appendChild(campoStatus);

            overlay.style.display = 'none';

            formulario.submit();
        }

    });

})();
</script>
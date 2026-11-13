<div class="popupdeacessonegado-overlay"
     id="popupdesativar-overlay"
     style="display: none;">

    <section class="popupdeacessonegado-container"
             role="alertdialog"
             aria-modal="true">

        <div class="popupdeacessonegado-icone">
            <img src="../imagens/atencao.png"
                 alt="Atenção"
                 class="popupdeacessonegado-icone-imagem">
        </div>

        <h2 class="popupdeacessonegado-titulo">
            Tem certeza que deseja desativar esta categoria?
        </h2>

        <p class="popupdeacessonegado-mensagem">
            A categoria ficará desativada e não poderá ser usada em novas ações.
        </p>

        <div class="popupdeacessonegadoacao-btn">
        <button type="button"
            class="button-desativar"
            id="popupdesativar-cancelar">
            Cancelar
        </button>
        <button type="button"
            class="button-desativar"
            id="popupdesativar-confirmar">
            Desativar
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
</script>
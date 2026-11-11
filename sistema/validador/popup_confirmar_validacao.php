
<?php
if (isset($mostrarPopupConfirmarValidacao) && $mostrarPopupConfirmarValidacao === true):
?>

<div class="popupvalidacao-overlay" id="popupvalidacao-overlay">
    <section class="popupvalidacao-container" role="alertdialog" aria-modal="true" aria-labelledby="popupvalidacao-titulo" aria-describedby="popupvalidacao-mensagem">

        <div class="popupvalidacao-icone">
            <img src="../imagens/atencao.png" alt="Atenção" class="popupvalidacao-icone-imagem">
        </div>

        <h2 class="popupvalidacao-titulo" id="popupvalidacao-titulo">
            Confirmar validação
        </h2>

        <p class="popupvalidacao-mensagem" id="popupvalidacao-mensagem">
            Essa ação não pode ser desfeita.
        </p>

        <div class="popupvalidacao-botoes">

            <button type="button" class="popupvalidacao-btn-cancelar" id="popupvalidacao-btn-cancelar">
                CANCELAR
            </button>

            <button type="button" class="popupvalidacao-btn-confirmar" id="popupvalidacao-btn-confirmar">
                CONFIRMAR
            </button>

        </div>

    </section>
</div>

<script>
    document.getElementById('popupvalidacao-btn-cancelar').addEventListener('click', function () {
        document.getElementById('popupvalidacao-overlay').remove();
    });
</script>

<?php endif; ?>

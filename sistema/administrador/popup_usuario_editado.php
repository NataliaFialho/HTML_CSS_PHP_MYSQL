<?php
/* No arquivo responsável por editar o usuário, define $mostrarPopupUsuarioEditado
 * como true quando a edição for bem-sucedida e inclua este arquivo.*/

if (isset($mostrarPopupUsuarioEditado) && $mostrarPopupUsuarioEditado === true):
?>

<div class="popupusuarioeditado-overlay" id="popupusuarioeditado-overlay">
    <section class="popupusuarioeditado-container" role="alertdialog" aria-modal="true" aria-labelledby="popupusuarioeditado-titulo" aria-describedby="popupusuarioeditado-mensagem">

        <div class="popupusuarioeditado-icone">
            <img src="../imagens/correto.png" alt="Sucesso" class="popupusuarioeditado-icone-imagem">
        </div>

        <h2 class="popupusuarioeditado-titulo" id="popupusuarioeditado-titulo">Usuário editado com sucesso!</h2>
        <p class="popupusuarioeditado-mensagem" id="popupusuarioeditado-mensagem">Esse usuário foi editado com sucesso!</p>
        <button type="button" class="popupusuarioeditado-btn" id="popupusuarioeditado-btn-ok">Voltar para Minhas Ações</button>
    </section>
</div>

<script>
    document
        document.getElementById('popupusuarioeditado-btn-ok').addEventListener('click', function () {
    window.location.href = 'tela_lista_usuarios.php';
});
</script>

<?php endif; ?>
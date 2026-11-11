<?php
/* No arquivo responsável pela edição de categoria, define $mostrarPopupCategoriaEditada
 * como true quando a atualização der certo e inclua esse arquivo.*/

if (isset($mostrarPopupCategoriaEditada) && $mostrarPopupCategoriaEditada === true):
?>

<div class="popupusuariosucesso-overlay" id="popupcategoriaeditada-overlay">
    <section class="popupusuariosucesso-container" role="alertdialog" aria-modal="true" aria-labelledby="popupcategoriaeditada-titulo" aria-describedby="popupcategoriaeditada-mensagem">

        <div class="popupusuariosucesso-icone">
            <img src="../imagens/correto.png" alt="Sucesso" class="popupusuariosucesso-icone-imagem">
        </div>

        <h2 class="popupusuariosucesso-titulo" id="popupcategoriaeditada-titulo">Categoria editada com sucesso!</h2>
        <p class="popupusuariosucesso-mensagem" id="popupcategoriaeditada-mensagem">A categoria foi editada com sucesso!</p>
        <a href="gerenciar_categorias.php" class="popupusuariosucesso-btn" id="popupcategoriaeditada-btn-voltar">Voltar para o painel principal</a>
    </section>
</div>

<?php endif; ?>
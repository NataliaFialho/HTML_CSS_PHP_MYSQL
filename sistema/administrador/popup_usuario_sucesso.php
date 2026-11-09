<?php
if (isset($cadastroSucesso) && $cadastroSucesso === true):?>

<div class="popupusuariosucesso-overlay" id="popupusuariosucesso-overlay">
    <section class="popupusuariosucesso-container" role="alertdialog" aria-modal="true" aria-labelledby="popupusuariosucesso-titulo" aria-describedby="popupusuariosucesso-mensagem">

        <div class="popupusuariosucesso-icone">
            <img src="imagens/correto.png" alt="Cadastro realizado com sucesso" class="popupusuariosucesso-icone-imagem">
        </div>

        <h2 class="popupusuariosucesso-titulo" id="popupusuariosucesso-titulo">Usuário cadastrado com sucesso!</h2>
        <p class="popupusuariosucesso-mensagem" id="popupusuariosucesso-mensagem">Esse usuário foi cadastrado com sucesso!</p>

        <a href="" class="popupusuariosucesso-btn">Voltar para o Painel principal</a>
    </section>
</div>
<?php endif; ?>
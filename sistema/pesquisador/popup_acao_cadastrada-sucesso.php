<div class="popup-acao-sucesso-overlay">
    <div class="popup-acao-sucesso-container">
        <div class="popup-acao-sucesso-icone">
            <img class="popupacaosucesso-icone" src="../imagens/correto.png" alt="">
        </div>
        <h2 class="popup-acao-sucesso-titulo">Ação cadastrada com sucesso!</h2>
        <p class="popup-acao-sucesso-mensagem">Sua ação já está salva no sistema.</p>
        <button type="button" class="popup-acao-sucesso-btn" onclick="voltarParaAcoes()">
            OK
        </button>
    </div>
</div>

<script>
    function voltarParaAcoes() {
        window.location.href = "painel_pesquisador.php";
    }
</script>
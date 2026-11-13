<?php
require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/../auth.php';

exigirCargo($pdo, 1);

$cadastroSucesso = ($_GET['cadastro'] ?? '') === 'sucesso';
$errosCadastro = [
    'matricula' => 'Já existe um usuário com essa matrícula.',
    'email'     => 'Já existe um usuário com esse e-mail.',
];
$mensagemErroCadastro = $errosCadastro[$_GET['erro'] ?? ''] ?? '';
$cargos = [];
$erroCargos = '';

try {
    $cargos = $pdo->query('SELECT id, cargo FROM cargo ORDER BY cargo')->fetchAll();

    if ($cargos === []) {
        $erroCargos = 'Nenhum cargo foi encontrado.';
    }
} catch (PDOException $e) {
    error_log('Erro ao carregar cargos: ' . $e->getMessage());
    $erroCargos = 'Não foi possível carregar os cargos. Recarregue a página.';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar usuário</title>
    <link rel="stylesheet" href="../css/estilo.css?v=<?= filemtime(__DIR__ . '/../css/estilo.css') ?>">
</head>
<body class="paineldecadastrarusuarios-pagina">

    <header class="paineldecriaracoes-cabecalho">
        <img src="../imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="paineldecriaracoes-logo">
        <h1 class="paineldecriaracoes-titulo-pesquisador">ADMINISTRADOR</h1>
        <button type="button" class="paineldecriaracoes-btn-sair" onclick="window.location.href='../tela_dois_cargos.php'">Sair</button>
    </header>

    <section class="paineldecriaracoes-barra-titulo">
    </section>

    <section class="paineldecriaracoes-barra-nova-acao">
        <h2 class="paineldecriaracoes-titulo-nova-acao">CADASTRAR USUÁRIO</h2>
    </section>

    <?php if ($mensagemErroCadastro !== ''): ?>
        <p class="editarusuario-mensagem editarusuario-erro" role="alert">
            <?= htmlspecialchars($mensagemErroCadastro, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form class="cadastrodeusuario-formulario" method="POST" action="cadastrar.php">
        <input type="text" name="nome" placeholder="Nome do Usuário" maxlength="50" required>
        <input type="number" name="matricula" placeholder="Matrícula" min="1" required>
        <input type="email" name="email" placeholder="Email" maxlength="100" required>
        <input type="password" name="senha" placeholder="Senha (8 caracteres)" minlength="8" maxlength="8" required>
        <div class="cadastrousuario-checkbox">
            <div id="cargos-disponiveis">
                <?php foreach ($cargos as $cargo): ?>
                    <label>
                        <input type="checkbox" name="cargos[]" value="<?= (int) $cargo['id'] ?>">
                        <span><?= htmlspecialchars($cargo['cargo'], ENT_QUOTES, 'UTF-8') ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <p id="erro-cargos" role="alert"<?= $erroCargos === '' ? ' hidden' : '' ?>><?= htmlspecialchars($erroCargos, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="cadastrodeusuario-buttons">
            <button type="button" class="cadastrousuario-cancelbutton" onclick="window.location.href='tela_escolha.php'">Cancelar</button>
            <button type="submit" class="cadastrousuario-submitbutton" id="submitusuario">Cadastrar Usuário</button>
        </div>
    </form>

    <script>
        const formulario = document.querySelector('.cadastrodeusuario-formulario');
        const listaCargos = document.getElementById('cargos-disponiveis');
        const erroCargos = document.getElementById('erro-cargos');

        formulario.addEventListener('submit', (evento) => {
            if (!listaCargos.querySelector('input:checked')) {
                evento.preventDefault();
                erroCargos.hidden = false;
                erroCargos.textContent = 'Selecione pelo menos um cargo.';
            }
        });
    </script>

    <?php if ($cadastroSucesso): ?>
        <?php include __DIR__ . '/popup_usuario_sucesso.php'; ?>
    <?php endif; ?>
</body>
</html>
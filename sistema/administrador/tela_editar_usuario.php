<?php
require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/../auth.php';

exigirCargo($pdo, 1);

$mostrarPopupUsuarioEditado = isset($_GET['sucesso']) && $_GET['sucesso'] === '1';

$usuario = null;
$cargos = [];
$cargosSelecionados = [];
$erroEdicao = '';
$erroCargos = '';

$matriculaInformada = $_GET['matricula'] ?? $_GET['id'] ?? null;

if ($matriculaInformada !== null) {
    $matricula = is_string($matriculaInformada)
        ? filter_var($matriculaInformada, FILTER_VALIDATE_INT)
        : false;

    if ($matricula === false || $matricula < 1) {
        http_response_code(400);
        $erroEdicao = 'Matrícula inválida.';
    } else {
        try {
            $usuarioStmt = $pdo->prepare(
                'SELECT matricula, nome, email
                 FROM usuarios
                 WHERE matricula = :matricula AND status = 1'
            );
            $usuarioStmt->execute([':matricula' => $matricula]);
            $usuario = $usuarioStmt->fetch() ?: null;

            if ($usuario === null) {
                http_response_code(404);
                $erroEdicao = 'Usuário não encontrado ou desativado.';
            } else {
                $cargos = $pdo->query('SELECT id, cargo FROM cargo ORDER BY cargo')->fetchAll();

                $cargosUsuarioStmt = $pdo->prepare(
                    'SELECT id_cargo FROM usuario_cargo WHERE matricula = :matricula'
                );
                $cargosUsuarioStmt->execute([':matricula' => $matricula]);
                $cargosSelecionados = array_map('strval', $cargosUsuarioStmt->fetchAll(PDO::FETCH_COLUMN));

                if ($cargos === []) {
                    $erroCargos = 'Nenhum cargo foi encontrado.';
                }
            }
        } catch (PDOException $e) {
            error_log('Erro ao carregar usuário para edição: ' . $e->getMessage());
            http_response_code(500);
            $usuario = null;
            $erroEdicao = 'Não foi possível carregar os dados do usuário.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar usuário</title>
    <link rel="stylesheet" href="../css/estilo.css?v=<?= filemtime(__DIR__ . '/../css/estilo.css') ?>">
</head>
<body class="paineldecadastrarusuarios-pagina">
    <?php require_once 'popup_usuario_editado.php'; ?>

    <header class="paineldecriaracoes-cabecalho">
        <img src="../imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="paineldecriaracoes-logo">
        <h1 class="paineldecriaracoes-titulo-pesquisador">ADMINISTRADOR</h1>
        <button type="button" class="paineldecriaracoes-btn-sair" onclick="window.location.href='../tela_dois_cargos.php'">Sair</button>
    </header>

    <section class="paineldecriaracoes-barra-titulo">
    </section>

    <section class="paineldecriaracoes-barra-nova-acao">
        <h2 class="paineldecriaracoes-titulo-nova-acao">EDITAR USUÁRIO</h2>
    </section>

    <?php if ($erroEdicao !== ''): ?>
        <p id="erro-edicao" class="editarusuario-mensagem editarusuario-erro" role="alert"><?= htmlspecialchars($erroEdicao, ENT_QUOTES, 'UTF-8') ?></p>
    <?php elseif ($usuario === null): ?>
        <p id="aviso-lista">Selecione um usuário na lista de usuários para editá-lo.</p>
    <?php else: ?>
        <form class="cadastrodeusuario-formulario" method="POST" action="editar.php" style="display: flex;">
            <input type="hidden" name="matricula_atual" value="<?= (int) $usuario['matricula'] ?>">
            <input type="text" name="nome" placeholder="Nome do Usuário" maxlength="50" value="<?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>" required>
            <input type="number" name="matricula" placeholder="Matrícula" aria-label="Matrícula" min="1" value="<?= (int) $usuario['matricula'] ?>" required>
            <input type="email" name="email" placeholder="Email" maxlength="100" value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>" required>
            <input type="password" name="senha" placeholder="Nova senha (opcional, 8 caracteres)" minlength="8" maxlength="8" autocomplete="new-password">
            <div class="cadastrousuario-checkbox">
                <div id="cargos-disponiveis">
                    <?php foreach ($cargos as $cargo): ?>
                        <label>
                            <input type="checkbox" name="cargos[]" value="<?= (int) $cargo['id'] ?>"<?= in_array((string) $cargo['id'], $cargosSelecionados, true) ? ' checked' : '' ?>>
                            <span><?= htmlspecialchars($cargo['cargo'], ENT_QUOTES, 'UTF-8') ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <p id="erro-cargos" class="editarusuario-mensagem editarusuario-erro" role="alert"<?= $erroCargos === '' ? ' hidden' : '' ?>><?= htmlspecialchars($erroCargos, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="cadastrodeusuario-buttons">
                <button type="button" class="cadastrousuario-cancelbutton" id="cancelar-edicao" onclick="window.location.href='tela_lista_usuarios.php'">Cancelar</button>
                <button type="submit" class="cadastrousuario-submitbutton">Salvar alterações</button>
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
    <?php endif; ?>
</body>
</html>
<?php

require_once __DIR__ . '/../conexao.php';

$mensagemErro = '';

$matricula = filter_input(INPUT_GET, 'matricula', FILTER_VALIDATE_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $matricula = filter_input(INPUT_POST, 'matricula', FILTER_VALIDATE_INT);

    if (!$matricula || $matricula <= 0) {

        header('Location: tela_lista_usuarios.php?erro=matricula');
        exit;

    }

    try {

        $sql = $pdo->prepare(
            'UPDATE usuarios
             SET status = 0
             WHERE matricula = :matricula
             AND status = 1'
        );

        $sql->execute([
            ':matricula' => $matricula
        ]);

        if ($sql->rowCount() > 0) {

            header('Location: tela_lista_usuarios.php?desativado=1');
            exit;

        } else {

            $mensagemErro = 'Usuário não encontrado ou já desativado.';

        }

    } catch (PDOException $e) {

        error_log('Erro ao desativar usuário: ' . $e->getMessage());

        $mensagemErro = 'Não foi possível desativar o usuário. Tente novamente.';

    }
}

// Se não existe matrícula válida, volta para a lista
if (!$matricula || $matricula <= 0) {

    header('Location: tela_lista_usuarios.php');
    exit;

}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Confirmar desativação</title>

    <link rel="stylesheet" href="../css/estilo.css">
</head>

<body>

    <div class="popup-confirmar-desativacao">

        <div class="icone-erro">
            <img src="../imagens/atencao.png" alt="Ícone de atenção">
        </div>

        <h1>Certeza que deseja desativar esse Usuário?</h1>

        <p class="mensagem">
            Ele não terá acesso ao sistema.
        </p>

        <?php if ($mensagemErro !== ''): ?>

            <p role="alert" style="color: #d91f1f; text-align: center;">
                <?= htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8') ?>
            </p>

        <?php endif; ?>

        <div class="botoes">

            <a class="botao-cancelar" href="tela_lista_usuarios.php">
                CANCELAR
            </a>

            <form method="POST"
                  action="popup_confirmar_desativacao.php"
                  style="margin: 0;">

                <input
                    type="hidden"
                    name="matricula"
                    value="<?= (int) $matricula ?>"
                >

                <button class="botao-desativar" type="submit">
                    DESATIVAR
                </button>

            </form>

        </div>

    </div>

</body>
</html>

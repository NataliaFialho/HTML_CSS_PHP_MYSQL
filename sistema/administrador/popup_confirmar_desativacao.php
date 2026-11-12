<?php

require_once __DIR__ . '/../conexao.php';


$mensagemErro = '';
$matricula = filter_input(INPUT_GET, 'matricula', FILTER_VALIDATE_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = filter_input(INPUT_POST, 'matricula', FILTER_VALIDATE_INT);

    if (!$matricula || $matricula <= 0) {
        $mensagemErro = 'Matrícula inválida.';
    } else {
        try {
            $desativa = $pdo->prepare('UPDATE usuarios SET status = 0 WHERE matricula = :matricula AND status = 1');
            $desativa->execute([':matricula' => $matricula]);

            if ($desativa->rowCount() > 0) {
                header('Location: tela_lista_usuarios.php?desativado=1');
                exit;
            }

           $mensagemErro = 'Usuário não encontrado ou já desativado.';
        } catch (PDOException $e) {
            error_log('Erro ao desativar usuário: ' . $e->getMessage());
            $mensagemErro = 'Não foi possível desativar o usuário. Tente novamente.';
        }
    }            

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tela_lista_usuarios.php');
    exit;
}

$matricula = filter_input(INPUT_POST, 'matricula', FILTER_VALIDATE_INT);

if ($sql->rowCount() > 0) {
        header('Location: tela_lista_usuarios.php?desativado=1');
        exit;
    };

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar desativação</title>

    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    try {
        $sql = $pdo->prepare('UPDATE usuarios SET status = 0 WHERE matricula = :matricula AND status = 1'
        );
    <div class="popup-confirmar-desativacao">
        <div class="icone-erro">
            <img src="../imagens/atencao.png" alt="Ícone de atenção">
        </div>
    $sql->execute([':matricula' => $matricula
    ]);

<h1>Certeza que deseja desativar esse Usuário?</h1>
        <p class="mensagem">Ele não terá acesso ao sistema.</p>

        <?php if ($mensagemErro !== ''): ?>
            <p role="alert" style="color: #d91f1f; text-align: center;">
                <?= htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>
    if ($sql->rowCount() > 0) {
        header('Location: tela_lista_usuarios.php?desativado=1');
        exit;
    }

        <div class="botoes">
            <a class="botao-cancelar" href="tela_lista_usuarios.php">CANCELAR</a>
    header('Location: tela_lista_usuarios.php?erro=usuario');
    exit;

            <?php if ($matricula && $matricula > 0 && $_SERVER['REQUEST_METHOD'] !== 'POST' || ($matricula && $matricula > 0 && $mensagemErro !== '')): ?>
                <form method="POST" action="popup_confirmar_desativacao.php" style="margin: 0;">
                    <input type="hidden" name="matricula" value="<?= (int) $matricula ?>">
                    <button class="botao-desativar" type="submit">DESATIVAR</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

} catch (PDOException $e) {
    error_log('Erro ao desativar usuário: ' . $e->getMessage());
    header('Location: tela_lista_usuarios.php?erro=banco');
    exit;
}
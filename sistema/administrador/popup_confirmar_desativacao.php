<?php
session_start();

require_once __DIR__ . '/../conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tela_lista_usuarios.php');
    exit;
}

$matricula = filter_input(INPUT_POST, 'matricula', FILTER_VALIDATE_INT);

if (!$matricula || $matricula <= 0) {
    header('Location: tela_lista_usuarios.php?erro=matricula');
    exit;
}

if (!isset($_SESSION['matricula'])) {
    header('Location: tela_lista_usuarios.php?erro=sessao');
    exit;
}

try {
    $pdo->beginTransaction();

    $sql = $pdo->prepare(
        'UPDATE usuarios SET status = 0 WHERE matricula = :matricula AND status = 1'
    );
    $sql->execute([':matricula' => $matricula]);

    if ($sql->rowCount() > 0) {
        $nomeStmt = $pdo->prepare('SELECT nome FROM usuarios WHERE matricula = :matricula');
        $nomeStmt->execute([':matricula' => $matricula]);
        $nomeUsuario = (string) $nomeStmt->fetchColumn();

        $operacaoLog = 'Administrador desativou o usuário ' . $matricula . ' - ' . $nomeUsuario;
        if (function_exists('mb_substr')) {
            $operacaoLog = mb_substr($operacaoLog, 0, 100, 'UTF-8');
        } else {
            $operacaoLog = substr($operacaoLog, 0, 100);
        }
        $logStmt = $pdo->prepare('INSERT INTO log (operacao, matricula_usuario, tabela_afetada, data_hora) VALUES (:operacao, :matricula, :tabela, NOW())');
        $logStmt->execute([':operacao' => $operacaoLog, ':matricula' => $_SESSION['matricula'], ':tabela' => 'usuarios']);

        $pdo->commit();
        header('Location: tela_lista_usuarios.php?desativado=1');
        exit;
    }

    $pdo->rollBack();
    header('Location: tela_lista_usuarios.php?erro=usuario');
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Erro ao desativar usuário: ' . $e->getMessage());
    header('Location: tela_lista_usuarios.php?erro=banco');
    exit;
}

// <!DOCTYPE html>
// <html lang="pt-BR">
// <head>
//     <meta charset="UTF-8">
//     <meta name="viewport" content="width=device-width, initial-scale=1.0">
//     <title>Confirmar desativação</title>

//     <link rel="stylesheet" href="../css/estilo.css">
// </head>
// <body>

//     <div class="popup-confirmar-desativacao">

//         <div class="icone-erro">
//             <img src="../imagens/atencao.png" alt="Ícone de erro">

//         </div>

//         <h1>Certeza que deseja desativar esse Usuário?</h1>

//         <p class="mensagem-popup-confirmar">
//             Ele não terá acesso ao sistema.
//         </p>

//         <div class="botoes-popup-confirmar">

//             <button class="botao-cancelar-popup-confirmar" type="button">
//                 CANCELAR
//             </button>

//             <button class="botao-desativar-popup-confirmar" type="button">
//                 DESATIVAR
//             </button>

//         </div>

//     </div>

// </body>
// </html>
<?php
require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/../auth.php';

$matriculaOperador = exigirCargo($pdo, 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tela_lista_usuarios.php');
    exit;
}

$matricula = filter_var($_POST['matricula'] ?? '', FILTER_VALIDATE_INT);

if ($matricula === false || $matricula < 1) {
    header('Location: tela_lista_usuarios.php?erro=matricula');
    exit;
}

if ($matricula === $matriculaOperador) {
    header('Location: tela_lista_usuarios.php?erro=proprio');
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

        $operacaoLog = mb_substr('Administrador desativou o usuário ' . $matricula . ' - ' . $nomeUsuario, 0, 100, 'UTF-8');

        $logStmt = $pdo->prepare('INSERT INTO log (operacao, matricula_usuario, tabela_afetada, data_hora) VALUES (:operacao, :matricula, :tabela, NOW())');
        $logStmt->execute([':operacao' => $operacaoLog, ':matricula' => $matriculaOperador, ':tabela' => 'usuarios']);

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
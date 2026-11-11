<?php

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

try {
    $sql = $pdo->prepare('UPDATE usuarios SET status = 0 WHERE matricula = :matricula AND status = 1'
    );

    $sql->execute([':matricula' => $matricula
    ]);

    if ($sql->rowCount() > 0) {
        header('Location: tela_lista_usuarios.php?desativado=1');
        exit;
    }

    header('Location: tela_lista_usuarios.php?erro=usuario');
    exit;

} catch (PDOException $e) {
    error_log('Erro ao desativar usuário: ' . $e->getMessage());
    header('Location: tela_lista_usuarios.php?erro=banco');
    exit;
}
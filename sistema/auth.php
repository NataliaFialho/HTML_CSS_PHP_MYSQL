<?php
function exigirCargo(PDO $pdo, int $idCargo, string $paginaLogin = '../index.php', string $paginaCargos = '../tela_dois_cargos.php'): int
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['matricula'])) {
        header('Location: ' . $paginaLogin);
        exit;
    }

    $matricula = (int) $_SESSION['matricula'];

    $ativoStmt = $pdo->prepare('SELECT 1 FROM usuarios WHERE matricula = :matricula AND status = 1');
    $ativoStmt->execute([':matricula' => $matricula]);

    if (!$ativoStmt->fetchColumn()) {
        $_SESSION = [];
        session_destroy();
        header('Location: ' . $paginaLogin);
        exit;
    }

    $cargoStmt = $pdo->prepare('SELECT 1 FROM usuario_cargo WHERE matricula = :matricula AND id_cargo = :cargo LIMIT 1');
    $cargoStmt->execute([':matricula' => $matricula, ':cargo' => $idCargo]);

    if (!$cargoStmt->fetchColumn()) {
        header('Location: ' . $paginaCargos);
        exit;
    }

    return $matricula;
}

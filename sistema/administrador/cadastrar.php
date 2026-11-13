<?php
session_start();
require_once __DIR__ . '/../conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tela_cadastrar_usuario.php');
    exit;
}

if (!isset($_SESSION['matricula'])) {
    http_response_code(401);
    exit('É necessário estar autenticado para cadastrar usuários.');
}

try {
    $permissaoStmt = $pdo->prepare('SELECT 1 FROM usuario_cargo WHERE matricula = :matricula AND id_cargo = 1 LIMIT 1');
    $permissaoStmt->execute([':matricula' => $_SESSION['matricula']]);

    if (!$permissaoStmt->fetchColumn()) {
        http_response_code(403);
        exit('Somente administradores podem cadastrar usuários.');
    }

    $matriculaInformada = $_POST['matricula'] ?? '';
    $nomeInformado = $_POST['nome'] ?? '';
    $emailInformado = $_POST['email'] ?? '';
    $senhaInformada = $_POST['senha'] ?? '';
    $cargosInformados = $_POST['cargos'] ?? [];

    if (
        !is_string($matriculaInformada) ||
        !is_string($nomeInformado) ||
        !is_string($emailInformado) ||
        !is_string($senhaInformada) ||
        !is_array($cargosInformados) ||
        $cargosInformados === []
    ) {
        http_response_code(400);
        exit('Confira os dados e selecione pelo menos um cargo.');
    }

    $matricula = filter_var($matriculaInformada, FILTER_VALIDATE_INT);
    $nome = trim($nomeInformado);
    $email = trim($emailInformado);

    if (
        $matricula === false || $matricula < 1 ||
        $nome === '' || strlen($nome) > 50 ||
        filter_var($email, FILTER_VALIDATE_EMAIL) === false ||
        strlen($email) > 100 ||
        strlen($senhaInformada) !== 8
    ) {
        http_response_code(400);
        exit('Confira os dados informados. A senha deve ter exatamente 8 caracteres.');
    }

    $cargos = [];
    foreach ($cargosInformados as $idCargoInformado) {
        $idCargo = is_string($idCargoInformado)
            ? filter_var($idCargoInformado, FILTER_VALIDATE_INT)
            : false;

        if ($idCargo === false || $idCargo < 1) {
            http_response_code(400);
            exit('Um dos cargos selecionados não é válido.');
        }

        $cargos[] = $idCargo;
    }
    $cargos = array_values(array_unique($cargos));

    $pdo->beginTransaction();

    $verificaUsuario = $pdo->prepare('SELECT 1 FROM usuarios WHERE matricula = :matricula LIMIT 1');
    $verificaUsuario->execute([':matricula' => $matricula]);

    if ($verificaUsuario->fetchColumn()) {
        $pdo->rollBack();
        header('Location: tela_cadastrar_usuario.php?erro=matricula');
        exit;
    }

    $verificaEmail = $pdo->prepare('SELECT 1 FROM usuarios WHERE email = :email LIMIT 1');
    $verificaEmail->execute([':email' => $email]);

    if ($verificaEmail->fetchColumn()) {
        $pdo->rollBack();
        header('Location: tela_cadastrar_usuario.php?erro=email');
        exit;
    }

    $senhaFinal = $senhaInformada;
    $usuarioStmt = $pdo->prepare('INSERT INTO usuarios (matricula, nome, email, senha)VALUES (:matricula, :nome, :email, :senha)');
    $usuarioStmt->execute([
        ':matricula' => $matricula,
        ':nome' => $nome,
        ':email' => $email,
        ':senha' => $senhaFinal,
    ]);

    $cargoStmt = $pdo->prepare('SELECT id FROM cargo WHERE id = :id');
    $usuarioCargoStmt = $pdo->prepare('INSERT INTO usuario_cargo (matricula, id_cargo) VALUES (:matricula, :id_cargo)');

    foreach ($cargos as $idCargo) {
        $cargoStmt->execute([':id' => $idCargo]);

        if (!$cargoStmt->fetchColumn()) {
            throw new InvalidArgumentException('Cargo inválido.');
        }

        $usuarioCargoStmt->execute([
            ':matricula' => $matricula,
            ':id_cargo' => $idCargo,
        ]);
    }

    $operacaoLog = 'Administrador cadastrou usuário ' . $matricula . ' - ' . $nome;
    if (function_exists('mb_substr')) {
        $operacaoLog = mb_substr($operacaoLog, 0, 100, 'UTF-8');
    } else {
        $operacaoLog = substr($operacaoLog, 0, 100);
    }

    $logStmt = $pdo->prepare('INSERT INTO log (operacao, matricula_usuario, tabela_afetada, data_hora) VALUES (:operacao, :matricula, :tabela, NOW())'
    );
    $logStmt->execute([
        ':operacao' => $operacaoLog,
        ':matricula' => $_SESSION['matricula'],
        ':tabela' => 'usuarios',
    ]);

    $pdo->commit();

    header('Location: tela_cadastrar_usuario.php?cadastro=sucesso');
    exit;

} catch (InvalidArgumentException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400);
    exit('Um dos cargos selecionados não é válido.');

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Erro ao cadastrar usuário: ' . $e->getMessage());
    http_response_code(500);
    exit('Não foi possível cadastrar o usuário. Confira os dados e tente novamente.');
}
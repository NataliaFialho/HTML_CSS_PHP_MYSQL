<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $matriculaInformada = $_GET['matricula'] ?? $_GET['id'] ?? null;
    $destino = 'tela_editar_usuario.php';

    if (is_string($matriculaInformada) && $matriculaInformada !== '') {
        $destino .= '?matricula=' . urlencode($matriculaInformada);
    }

    header('Location: ' . $destino);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: GET, POST');
    exit('Método não permitido.');
}

if (!isset($_SESSION['matricula'])) {
    http_response_code(401);
    exit('É necessário estar autenticado para editar usuários.');
}

require_once __DIR__ . '/../conexao.php';

$matriculaAtualInformada = $_POST['matricula_atual'] ?? $_POST['matricula'] ?? null;
$matriculaNovaInformada = $_POST['matricula'] ?? null;
$nomeInformado = $_POST['nome'] ?? null;
$emailInformado = $_POST['email'] ?? null;
$senhaInformada = $_POST['senha'] ?? '';
$cargosInformados = $_POST['cargos'] ?? [];

if (
    !is_string($matriculaAtualInformada) ||
    !is_string($matriculaNovaInformada) ||
    !is_string($nomeInformado) ||
    !is_string($emailInformado) ||
    !is_string($senhaInformada) ||
    !is_array($cargosInformados) ||
    $cargosInformados === []
) {
    http_response_code(400);
    exit('Confira os dados informados e selecione ao menos um cargo.');
}

$matriculaAtual = filter_var($matriculaAtualInformada, FILTER_VALIDATE_INT);
$matriculaNova = filter_var($matriculaNovaInformada, FILTER_VALIDATE_INT);
$nome = trim($nomeInformado);
$email = trim($emailInformado);

if (
    $matriculaAtual === false || $matriculaAtual < 1 ||
    $matriculaNova === false || $matriculaNova < 1 ||
    $nome === '' || strlen($nome) > 50 ||
    filter_var($email, FILTER_VALIDATE_EMAIL) === false ||
    strlen($email) > 100 ||
    ($senhaInformada !== '' && strlen($senhaInformada) < 8)
) {
    http_response_code(400);
    exit('Confira os dados. A nova senha, quando preenchida, deve ter ao menos 8 caracteres.');
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

try {
    $permissaoStmt = $pdo->prepare('SELECT 1 FROM usuario_cargo WHERE matricula = :matricula AND id_cargo = 1 LIMIT 1');
    $permissaoStmt->execute([':matricula' => $_SESSION['matricula']]);

    if (!$permissaoStmt->fetchColumn()) {
        http_response_code(403);
        exit('Somente administradores podem editar usuários.');
    }

    $pdo->beginTransaction();

    $usuarioStmt = $pdo->prepare('SELECT matricula, senha, status FROM usuarios WHERE matricula = :matricula AND status = 1 FOR UPDATE');
    $usuarioStmt->execute([':matricula' => $matriculaAtual]);
    $usuarioAtual = $usuarioStmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuarioAtual) {
        $pdo->rollBack();
        http_response_code(404);
        exit('Usuário não encontrado ou desativado.');
    }

    if ($matriculaNova !== $matriculaAtual) {
        $verificaMatricula = $pdo->prepare('SELECT 1 FROM usuarios WHERE matricula = :matricula LIMIT 1');
        $verificaMatricula->execute([':matricula' => $matriculaNova]);

        if ($verificaMatricula->fetchColumn()) {
            $pdo->rollBack();
            http_response_code(409);
            exit('A nova matrícula já está sendo utilizada por outro usuário.');
        }
    }

    $cargoStmt = $pdo->prepare('SELECT id FROM cargo WHERE id = :id');
    foreach ($cargos as $idCargo) {
        $cargoStmt->execute([':id' => $idCargo]);
        if (!$cargoStmt->fetchColumn()) {
            throw new InvalidArgumentException('Cargo inválido.');
        }
    }

    $senhaFinal = $senhaInformada === ''
        ? $usuarioAtual['senha']
        : $senhaInformada;

    if ($matriculaNova !== $matriculaAtual) {
        $novoUsuarioStmt = $pdo->prepare('INSERT INTO usuarios (matricula, nome, email, senha, status) VALUES (:matricula, :nome, :email, :senha, :status)'
        );
        $novoUsuarioStmt->execute([
            ':matricula' => $matriculaNova,
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senhaFinal,
            ':status' => $usuarioAtual['status'],
        ]);

        $moveCargos = $pdo->prepare('UPDATE usuario_cargo SET matricula = :nova WHERE matricula = :atual');
        $moveCargos->execute([
            ':nova' => $matriculaNova,
            ':atual' => $matriculaAtual,
        ]);

        $moveAcoes = $pdo->prepare('UPDATE acao SET matricula_pesquisador = :nova WHERE matricula_pesquisador = :atual');
        $moveAcoes->execute([
            ':nova' => $matriculaNova,
            ':atual' => $matriculaAtual,
        ]);

        $moveLogs = $pdo->prepare('UPDATE log SET matricula_usuario = :nova WHERE matricula_usuario = :atual');
        $moveLogs->execute([
            ':nova' => $matriculaNova,
            ':atual' => $matriculaAtual,
        ]);

        $removeUsuarioAntigo = $pdo->prepare('DELETE FROM usuarios WHERE matricula = :matricula');
        $removeUsuarioAntigo->execute([':matricula' => $matriculaAtual]);

    } else {
        $atualizaUsuario = $pdo->prepare('UPDATE usuarios SET nome = :nome, email = :email, senha = :senha WHERE matricula = :matricula');
        $atualizaUsuario->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senhaFinal,
            ':matricula' => $matriculaAtual,
        ]);
    }

    $removeCargos = $pdo->prepare('DELETE FROM usuario_cargo WHERE matricula = :matricula');
    $removeCargos->execute([':matricula' => $matriculaNova]);
    $insereCargo = $pdo->prepare('INSERT INTO usuario_cargo (matricula, id_cargo) VALUES (:matricula, :id_cargo)');
    foreach ($cargos as $idCargo) {
        $insereCargo->execute([
            ':matricula' => $matriculaNova,
            ':id_cargo' => $idCargo,
        ]);
    }

    $matriculaOperador = (int) $_SESSION['matricula'];
    if ($matriculaOperador === $matriculaAtual) {
        $matriculaOperador = $matriculaNova;
    }

    $operacaoLog = 'Administrador editou usuário: matrícula ' .
        $matriculaAtual . ' para ' . $matriculaNova . ' - ' . $nome;

    if (function_exists('mb_substr')) {
        $operacaoLog = mb_substr($operacaoLog, 0, 100, 'UTF-8');
    } else {
        $operacaoLog = substr($operacaoLog, 0, 100);
    }

    $logStmt = $pdo->prepare('INSERT INTO log (operacao, matricula_usuario, tabela_afetada, data_hora) VALUES (:operacao, :matricula, :tabela, NOW())');
    $logStmt->execute([
        ':operacao' => $operacaoLog,
        ':matricula' => $matriculaOperador,
        ':tabela' => 'usuarios',
    ]);

    $pdo->commit();

    if ((int) $_SESSION['matricula'] === $matriculaAtual) {
        $_SESSION['matricula'] = $matriculaNova;
        $_SESSION['nome'] = $nome;
        $_SESSION['email'] = $email;
    }

    header('Location: tela_editar_usuario.php?matricula=' . $matriculaNova . '&sucesso=1');
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
    error_log('Erro ao editar usuário: ' . $e->getMessage());
    http_response_code(500);
    exit('Não foi possível editar o usuário. Confira os dados e tente novamente.');
}
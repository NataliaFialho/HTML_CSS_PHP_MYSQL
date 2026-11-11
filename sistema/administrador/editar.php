<?php
session_start();

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $matriculaInformada = $_GET['matricula'] ?? $_GET['id'] ?? null;
    $destino = 'tela_editar_usuario.php';

    if (is_string($matriculaInformada) && $matriculaInformada !== '') {
        $destino .= '?matricula=' . urlencode($matriculaInformada);
    }

    header('Location: ' . $destino);
    exit;
}

if ($metodo === 'POST') {
    if (!isset($_SESSION['matricula'])) {
        http_response_code(401);
        exit('É necessário estar autenticado para editar usuários.');
    }

    $matriculaInformada = $_POST['matricula'] ?? null;
    $nomeInformado = $_POST['nome'] ?? null;
    $emailInformado = $_POST['email'] ?? null;
    $senha = $_POST['senha'] ?? '';
    $cargosInformados = $_POST['cargos'] ?? [];

    if (
        !is_string($matriculaInformada) ||
        !is_string($nomeInformado) ||
        !is_string($emailInformado) ||
        !is_string($senha) ||
        !is_array($cargosInformados) || $cargosInformados === []
    ) {
        http_response_code(400);
        exit('Confira os dados informados e selecione ao menos um cargo.');
    }

    $matricula = filter_var($matriculaInformada, FILTER_VALIDATE_INT);
    $nome = trim($nomeInformado);
    $email = trim($emailInformado);
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
    $cargos = array_unique($cargos);

    if (
        $matricula === false || $matricula < 1 ||
        $nome === '' || strlen($nome) > 50 ||
        filter_var($email, FILTER_VALIDATE_EMAIL) === false ||
        strlen($email) > 100 ||
        ($senha !== '' && strlen($senha) < 8)
    ) {
        http_response_code(400);
        exit('Confira os dados. A nova senha, quando preenchida, deve ter ao menos 8 caracteres.');
    }

    try {
        require_once __DIR__ . '/../conexao.php';
        $pdo->beginTransaction();

        $usuarioStmt = $pdo->prepare(
            'SELECT matricula FROM usuarios WHERE matricula = :matricula AND status = 1'
        );
        $usuarioStmt->execute([':matricula' => $matricula]);
        if (!$usuarioStmt->fetch()) {
            $pdo->rollBack();
            http_response_code(404);
            exit('Usuário não encontrado ou desativado.');
        }

        $cargoStmt = $pdo->prepare('SELECT id FROM cargo WHERE id = :id');
        foreach ($cargos as $idCargo) {
            $cargoStmt->execute([':id' => $idCargo]);
            if (!$cargoStmt->fetch()) {
                throw new InvalidArgumentException('Cargo inválido.');
            }
        }

        if ($senha === '') {
            $usuarioUpdateStmt = $pdo->prepare(
                'UPDATE usuarios SET nome = :nome, email = :email WHERE matricula = :matricula'
            );
            $dadosUsuario = [
                ':nome' => $nome,
                ':email' => $email,
                ':matricula' => $matricula,
            ];
        } else {
            $usuarioUpdateStmt = $pdo->prepare(
                'UPDATE usuarios
                 SET nome = :nome, email = :email, senha = :senha
                 WHERE matricula = :matricula'
            );
            $dadosUsuario = [
                ':nome' => $nome,
                ':email' => $email,
                ':senha' => $senha,
                ':matricula' => $matricula,
            ];
        }
        $usuarioUpdateStmt->execute($dadosUsuario);

        $removeCargosStmt = $pdo->prepare(
            'DELETE FROM usuario_cargo WHERE matricula = :matricula'
        );
        $removeCargosStmt->execute([':matricula' => $matricula]);

        $insereCargoStmt = $pdo->prepare(
            'INSERT INTO usuario_cargo (matricula, id_cargo)
             VALUES (:matricula, :id_cargo)'
        );
        foreach ($cargos as $idCargo) {
            $insereCargoStmt->execute([
                ':matricula' => $matricula,
                ':id_cargo' => $idCargo,
            ]);
        }

        $operacaoLog = 'Administrador editou o usuário ' . $matricula . ' - ' . $nome;
        if (function_exists('mb_substr')) {
            $operacaoLog = mb_substr($operacaoLog, 0, 100, 'UTF-8');
        } else {
            $operacaoLog = substr($operacaoLog, 0, 100);
        }
        $logStmt = $pdo->prepare('INSERT INTO log (operacao, matricula_usuario, tabela_afetada, data_hora) VALUES (:operacao, :matricula, :tabela, NOW())');
        $logStmt->execute([':operacao' => $operacaoLog, ':matricula' => $_SESSION['matricula'], ':tabela' => 'usuarios']);

        $pdo->commit();
        header('Location: tela_editar_usuario.php?matricula=' . $matricula . '&sucesso=1');
        exit;
    } catch (InvalidArgumentException $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        http_response_code(400);
        exit('Um dos cargos selecionados não é válido.');
    } catch (PDOException $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Erro ao editar usuário: ' . $e->getMessage());
        http_response_code(500);
        exit('Não foi possível editar o usuário. Confira os dados e tente novamente.');
    }
}

http_response_code(405);
header('Allow: GET, POST');
echo 'Método não permitido.';

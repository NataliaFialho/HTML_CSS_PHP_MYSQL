<?php
session_start();

$metodo = $_SERVER['REQUEST_METHOD'];

if (!isset($_SESSION['matricula'])) {
    http_response_code(401);
    exit('É necessário estar autenticado para editar usuários.');
}

try {
    require_once __DIR__ . '/../conexao.php';

    $permissaoStmt = $pdo->prepare('SELECT 1 FROM usuario_cargo WHERE matricula = :matricula AND id_cargo = 1 LIMIT 1');
    $permissaoStmt->execute([':matricula' => $_SESSION['matricula']]);

    if (!$permissaoStmt->fetchColumn()) {
        http_response_code(403);
        exit('Somente administradores podem editar usuários.');
    }

} catch (PDOException $e) {
    error_log('Erro ao verificar permissão: ' . $e->getMessage());
    http_response_code(500);
    exit('Não foi possível verificar a permissão.');
}

if ($metodo === 'GET') {

    header('Content-Type: application/json; charset=utf-8');
    $matriculaInformada = $_GET['matricula'] ?? $_GET['id'] ?? null;
    $matricula = is_string($matriculaInformada)
        ? filter_var($matriculaInformada, FILTER_VALIDATE_INT)
        : false;

    if ($matricula === false || $matricula < 1) {
        http_response_code(400);
        echo json_encode(['erro' => 'Matrícula inválida.']);
        exit;}

    try {
        $usuarioStmt = $pdo->prepare('SELECT matricula, nome, email FROM usuarios WHERE matricula = :matricula AND status = 1');
        $usuarioStmt->execute([':matricula' => $matricula]);
        $usuario = $usuarioStmt->fetch();

        if (!$usuario) {
            http_response_code(404);
            echo json_encode(['erro' => 'Usuário não encontrado ou desativado.']);
            exit;
        }

        $cargos = $pdo->query('SELECT id, cargo FROM cargo ORDER BY cargo')->fetchAll();
        $cargosUsuarioStmt = $pdo->prepare('SELECT id_cargo FROM usuario_cargo WHERE matricula = :matricula');
        $cargosUsuarioStmt->execute([':matricula' => $matricula]);

        echo json_encode([
            'usuario' => $usuario,
            'cargos' => $cargos,
            'cargosSelecionados' => $cargosUsuarioStmt->fetchAll(PDO::FETCH_COLUMN),
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

    } catch (PDOException | JsonException $e) {
        error_log('Erro ao carregar usuário para edição: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Não foi possível carregar os dados do usuário.']);
    }

    exit;
}


// EDITAR USUÁRIO
if ($metodo === 'POST') {

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
        !is_array($cargosInformados) ||
        $cargosInformados === []
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

        $pdo->beginTransaction();

        $usuarioStmt = $pdo->prepare(
            'SELECT matricula FROM usuarios
             WHERE matricula = :matricula AND status = 1
             FOR UPDATE'
        );

        $usuarioStmt->execute([':matricula' => $matricula]);

        if (!$usuarioStmt->fetch()) {
            $pdo->rollBack();
            http_response_code(404);
            exit('Usuário não encontrado ou desativado.');
        }

        // Verifica os cargos informados.
        $cargoStmt = $pdo->prepare(
            'SELECT id FROM cargo WHERE id = :id'
        );

        foreach ($cargos as $idCargo) {
            $cargoStmt->execute([':id' => $idCargo]);

            if (!$cargoStmt->fetch()) {
                throw new InvalidArgumentException('Cargo inválido.');
            }
        }

        // Atualiza os dados do usuário.
        if ($senha === '') {

            $usuarioUpdateStmt = $pdo->prepare(
                'UPDATE usuarios
                 SET nome = :nome, email = :email
                 WHERE matricula = :matricula'
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

        // Atualiza os cargos do usuário.
        $removeCargosStmt = $pdo->prepare(
            'DELETE FROM usuario_cargo WHERE matricula = :matricula'
        );

        $removeCargosStmt->execute([
            ':matricula' => $matricula
        ]);

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

        // REGISTRO NO LOG DE EDIÇÃO DE USUÁRIOS.
        $logStmt = $pdo->prepare(
            'INSERT INTO log
             (operacao, matricula_usuario, tabela_afetada, data_hora)
             VALUES (:operacao, :matricula, :tabela, NOW())'
        );

        $logStmt->execute([
            ':operacao' => 'Administrador editou um usuário: matrícula ' . $matricula,
            ':matricula' => $_SESSION['matricula'],
            ':tabela' => 'usuarios, usuario_cargo',
        ]);

        // Confirma a edição e o registro no log.
        $pdo->commit();

        header(
            'Location: tela_editar_usuario.php?matricula=' .
            $matricula . '&edicao=sucesso'
        );

        exit;

    } catch (InvalidArgumentException $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        http_response_code(400);
        exit('Um dos cargos selecionados não é válido.');

    } catch (Throwable $e) {

        if ($pdo->inTransaction()) {
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
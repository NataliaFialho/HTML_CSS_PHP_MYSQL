<?php
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    header('Content-Type: application/json; charset=utf-8');

    $matriculaInformada = $_GET['matricula'] ?? null;
    $matricula = is_string($matriculaInformada)
        ? filter_var($matriculaInformada, FILTER_VALIDATE_INT)
        : false;

    if ($matricula === false || $matricula < 1) {
        http_response_code(400);
        echo json_encode(['erro' => 'Matrícula inválida.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        require_once __DIR__ . '/../conexao.php';

        $usuarioStmt = $pdo->prepare(
            'SELECT matricula, nome, email FROM usuarios WHERE matricula = :matricula'
        );
        $usuarioStmt->execute([':matricula' => $matricula]);
        $usuario = $usuarioStmt->fetch();


        if (!$usuario) {
            http_response_code(404);
            echo json_encode(['erro' => 'Usuário não encontrado.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $cargos = $pdo->query('SELECT id, cargo FROM cargo ORDER BY cargo')->fetchAll();
        $cargosStmt = $pdo->prepare(
            'SELECT id_cargo FROM usuario_cargo WHERE matricula = :matricula'
        );
        $cargosStmt->execute([':matricula' => $matricula]);

        echo json_encode([
            'usuario' => $usuario,
            'cargos' => $cargos,
            'cargosSelecionados' => $cargosStmt->fetchAll(PDO::FETCH_COLUMN),
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    } catch (PDOException | JsonException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Não foi possível carregar os dados do usuário.'], JSON_UNESCAPED_UNICODE);
    }

    exit;
}

if ($metodo === 'POST') {
    $matriculaInformada = $_POST['matricula'] ?? null;
    $nomeInformado = $_POST['nome'] ?? null;
    $emailInformado = $_POST['email'] ?? null;
    $senhaInformada = $_POST['senha'] ?? '';
    $cargosInformados = $_POST['cargos'] ?? [];

    if (
        !is_string($matriculaInformada) ||
        !is_string($nomeInformado) ||
        !is_string($emailInformado) ||
        !is_string($senhaInformada) ||
        !is_array($cargosInformados) || $cargosInformados === []
    ) {
        http_response_code(400);
        echo 'Confira os dados informados e selecione ao menos um cargo.';
        exit;
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
            echo 'Um dos cargos selecionados não é válido.';
            exit;
        }
        $cargos[] = $idCargo;
    }
    $cargos = array_unique($cargos);

    if (
        $matricula === false || $matricula < 1 ||
        $nome === '' || strlen($nome) > 50 ||
        filter_var($email, FILTER_VALIDATE_EMAIL) === false ||
        strlen($email) > 100 ||
        ($senhaInformada !== '' && strlen($senhaInformada) < 8)
    ) {
        http_response_code(400);
        echo 'Confira os dados informados. A senha, quando preenchida, deve ter ao menos 8 caracteres.';
        exit;
    }

    try {
        require_once __DIR__ . '/../conexao.php';
        $pdo->beginTransaction();

        $usuarioStmt = $pdo->prepare('SELECT matricula FROM usuarios WHERE matricula = :matricula');
        $usuarioStmt->execute([':matricula' => $matricula]);
        if (!$usuarioStmt->fetch()) {
            $pdo->rollBack();
            http_response_code(404);
            echo 'Usuário não encontrado.';
            exit;
        }

        $cargoStmt = $pdo->prepare('SELECT id FROM cargo WHERE id = :id');
        foreach ($cargos as $idCargo) {
            $cargoStmt->execute([':id' => $idCargo]);
            if (!$cargoStmt->fetch()) {
                throw new InvalidArgumentException('Cargo inválido.');
            }
        }

        if ($senhaInformada === '') {
            $usuarioUpdateStmt = $pdo->prepare(
                'UPDATE usuarios SET nome = :nome, email = :email WHERE matricula = :matricula'
            );
            $usuarioUpdateStmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':matricula' => $matricula,
            ]);
        } else {
            $usuarioUpdateStmt = $pdo->prepare(
                'UPDATE usuarios
                 SET nome = :nome, email = :email, senha = :senha
                 WHERE matricula = :matricula'
            );
            $usuarioUpdateStmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':senha' => $senhaInformada,
                ':matricula' => $matricula,
            ]);
        }

        $removeCargosStmt = $pdo->prepare(
            'DELETE FROM usuario_cargo WHERE matricula = :matricula'
        );
        $removeCargosStmt->execute([':matricula' => $matricula]);

        $usuarioCargoStmt = $pdo->prepare(
            'INSERT INTO usuario_cargo (matricula, id_cargo)
             VALUES (:matricula, :id_cargo)'
        );
        foreach ($cargos as $idCargo) {
            $usuarioCargoStmt->execute([
                ':matricula' => $matricula,
                ':id_cargo' => $idCargo,
            ]);
        }

        $pdo->commit();
        header('Location: tela_editar_usuario.php?matricula=' . $matricula . '&edicao=sucesso');
        exit;
    } catch (InvalidArgumentException $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        http_response_code(400);
        echo 'Um dos cargos selecionados não é válido.';
        exit;
    } catch (PDOException $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log($e->getMessage());
        http_response_code(500);
        echo 'Não foi possível editar o usuário. Confira os dados e tente novamente.';
        exit;
    }
}

http_response_code(405);
header('Allow: GET, POST');
echo 'Método não permitido.';

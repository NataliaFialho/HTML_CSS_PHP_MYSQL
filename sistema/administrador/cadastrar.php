<?php
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    header('Content-Type: application/json; charset=utf-8');

    try {
        require_once __DIR__ . '/../conexao.php';
        $cargos = $pdo->query('SELECT id, cargo FROM cargo ORDER BY cargo')->fetchAll();
        echo json_encode($cargos, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    } catch (PDOException | JsonException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Não foi possível carregar os cargos.']);
    }

    exit;
}

if ($metodo === 'POST') {
    $matriculaInformada = $_POST['matricula'] ?? null;
    $nomeInformado = $_POST['nome'] ?? null;
    $emailInformado = $_POST['email'] ?? null;
    $senha = $_POST['senha'] ?? null;
    $cargos = $_POST['cargos'] ?? [];

    if (
        !is_string($matriculaInformada) ||
        !is_string($nomeInformado) ||
        !is_string($emailInformado) ||
        !is_string($senha) ||
        !is_array($cargos) || $cargos === []
    ) {
        http_response_code(400);
        echo 'Confira os dados informados e selecione ao menos um cargo.';
        exit;
    }

    $matricula = filter_var($matriculaInformada, FILTER_VALIDATE_INT);
    $nome = trim($nomeInformado);
    $email = trim($emailInformado);
    $cargosValidados = [];
    foreach ($cargos as $idCargo) {
        $idCargoValidado = filter_var($idCargo, FILTER_VALIDATE_INT);
        if ($idCargoValidado === false || $idCargoValidado < 1) {
            http_response_code(400);
            echo 'Um dos cargos selecionados não é válido.';
            exit;
        }
        $cargosValidados[] = $idCargoValidado;
    }
    $cargos = array_unique($cargosValidados);

    if (
        $matricula === false || $matricula < 1 ||
        $nome === '' || strlen($nome) > 50 ||
        filter_var($email, FILTER_VALIDATE_EMAIL) === false ||
        strlen($email) > 100 ||
        strlen($senha) < 8
    ) {
        http_response_code(400);
        echo 'Confira os dados informados e selecione ao menos um cargo.';
        exit;
    }

    try {
        require_once __DIR__ . '/../conexao.php';
        $pdo->beginTransaction();

        $cargoStmt = $pdo->prepare('SELECT id FROM cargo WHERE id = :id');
        foreach ($cargos as $idCargo) {
            $cargoStmt->execute([':id' => $idCargo]);
            if (!$cargoStmt->fetch()) {
                throw new InvalidArgumentException('Cargo inválido.');
            }
        }

        $usuarioStmt = $pdo->prepare(
            'INSERT INTO usuarios (matricula, nome, email, senha)
             VALUES (:matricula, :nome, :email, :senha)'
        );
        $usuarioStmt->execute([
            ':matricula' => $matricula,
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senha,
        ]);

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
        header('Location: tela_cadastrar_usuario.php?cadastro=sucesso');
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
        echo 'Não foi possível cadastrar o usuário. Confira os dados e tente novamente.';
        exit;
    }
}

http_response_code(405);
header('Allow: GET, POST');
echo 'Método não permitido.';

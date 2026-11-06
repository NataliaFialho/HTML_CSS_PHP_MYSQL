<?php
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    header('Content-Type: application/json; charset=utf-8');

    try {
        require_once __DIR__ . '/../conexao.php';
        $cargos = $pdo->query('SELECT id, cargo FROM cargo ORDER BY cargo')->fetchAll();
        echo json_encode($cargos, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Não foi possível carregar os cargos.']);
    } catch (JsonException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode(['erro' => 'Não foi possível preparar a resposta.']);
    }

    exit;
}

if ($metodo === 'POST') {
    try {
        require_once __DIR__ . '/../conexao.php';
        $stmt = $pdo->prepare(
            'INSERT INTO usuarios (matricula, nome, email, id_cargo)
             VALUES (:matricula, :nome, :email, :id_cargo)'
        );
        $stmt->execute([
            ':matricula' => $_POST['matricula'],
            ':nome' => $_POST['nome'],
            ':email' => $_POST['email'],
            ':id_cargo' => $_POST['id_cargo'],
        ]);
        header('Location: ../index.html?cadastro=sucesso');
        exit;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        echo 'Não foi possível cadastrar o usuário. Confira os dados e tente novamente.';
        exit;
    }
}

http_response_code(405);
header('Allow: GET, POST');
echo 'Método não permitido.';

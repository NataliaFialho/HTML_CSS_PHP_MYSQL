<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Allow: GET');
    echo json_encode(['erro' => 'Método não permitido.']);
    exit;
}

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

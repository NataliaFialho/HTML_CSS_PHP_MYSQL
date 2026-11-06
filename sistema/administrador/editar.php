<?php
require_once __DIR__ . '/../conexao.php';

$matricula = $_POST['matricula_original']
    ?? $_GET['matricula']
    ?? $_GET['id']
    ?? null;
$erro = null;
$usuario = null;
$cargos = [];
$salvo = isset($_GET['edicao']) && $_GET['edicao'] === 'sucesso';

if ($matricula === null || $matricula === '') {
    http_response_code(400);
    $erro = 'Informe a matrícula do usuário na URL, por exemplo: editar.php?matricula=12345.';
} else {
    try {
        $stmt = $pdo->prepare(
            'SELECT matricula, nome, email, id_cargo
             FROM usuarios
             WHERE matricula = :matricula'
        );
        $stmt->execute([':matricula' => $matricula]);
        $usuario = $stmt->fetch();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario) {
            $stmt = $pdo->prepare(
                'UPDATE usuarios
                 SET matricula = :nova_matricula, nome = :nome, email = :email, id_cargo = :id_cargo
                 WHERE matricula = :matricula_original'
            );
            $stmt->execute([
                ':nova_matricula' => $_POST['matricula'],
                ':nome' => $_POST['nome'],
                ':email' => $_POST['email'],
                ':id_cargo' => $_POST['id_cargo'],
                ':matricula_original' => $matricula,
            ]);

            header(
                'Location: editar.php?matricula='
                . rawurlencode((string) $_POST['matricula'])
                . '&edicao=sucesso'
            );
            exit;
        }

        if (!$usuario) {
            http_response_code(404);
            $erro = 'Usuário não encontrado.';
        } else {
            $cargos = $pdo->query('SELECT id, cargo FROM cargo ORDER BY cargo')->fetchAll();
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        $erro = 'Não foi possível carregar ou atualizar o usuário.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar usuário</title>
</head>
<body>
    <main>
        <h1>Editar usuário</h1>

        <?php if ($salvo): ?>
            <p role="status">Usuário atualizado com sucesso.</p>
        <?php endif; ?>

        <?php if ($erro !== null): ?>
            <p role="alert"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></p>
        <?php elseif ($usuario !== null): ?>
            <form method="post">
                <input
                    type="hidden"
                    name="matricula_original"
                    value="<?= htmlspecialchars((string) $usuario['matricula'], ENT_QUOTES, 'UTF-8') ?>"
                >

                <label for="matricula">Matrícula</label>
                <input
                    type="number"
                    id="matricula"
                    name="matricula"
                    min="1"
                    step="1"
                    value="<?= htmlspecialchars((string) $usuario['matricula'], ENT_QUOTES, 'UTF-8') ?>"
                    required
                >

                <label for="nome">Nome</label>
                <input
                    type="text"
                    id="nome"
                    name="nome"
                    maxlength="50"
                    value="<?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>"
                    required
                >

                <label for="email">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="100"
                    value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>"
                    required
                >

                <label for="id_cargo">Cargo</label>
                <select id="id_cargo" name="id_cargo" required>
                    <option value="">Selecione um cargo</option>
                    <?php foreach ($cargos as $cargo): ?>
                        <option
                            value="<?= htmlspecialchars((string) $cargo['id'], ENT_QUOTES, 'UTF-8') ?>"
                            <?= (string) $cargo['id'] === (string) $usuario['id_cargo'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($cargo['cargo'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Atualizar</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>

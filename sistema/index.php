<?php
require_once __DIR__ . '/backend/conexao.php';

$cargos = $pdo->query('SELECT id, cargo FROM cargo ORDER BY id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargos</title>
</head>
<body>
    <main>
        <h1>Cargos cadastrados</h1>

        <?php if ($cargos): ?>
            <ul>
                <?php foreach ($cargos as $cargo): ?>
                    <li><?= htmlspecialchars($cargo['cargo'], ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Nenhum cargo cadastrado.</p>
        <?php endif; ?>
    </main>
</body>
</html>

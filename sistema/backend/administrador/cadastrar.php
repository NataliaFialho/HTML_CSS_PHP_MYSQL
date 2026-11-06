<?php
require '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO usuarios (matricula, nome, email, id_cargo) VALUES (:matricula, :nome, :email, :id_cargo)');
    $stmt->execute([
        ':matricula' => $_POST['matricula'],
        ':nome' => $_POST['nome'],
        ':email' => $_POST['email'],
        ':id_cargo' => $_POST['id_cargo'],
    ]);
    header('Location: ../../index.html?cadastro=sucesso');
    exit;
}

$cargos = $pdo->query('SELECT id, cargo FROM cargo ORDER BY cargo')->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>
<h2>Novo usuário</h2>


<form method="post">
    <label for="matricula">Matrícula</label>
    <input type="number" id="matricula" name="matricula" required>

    <label for="nome">Nome</label>
    <input type="text" id="nome" name="nome" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email", required>

    <label for="id_cargo">Cargo</label>
    <select id="id_cargo" name="id_cargo" required>
        <option value="">Selecione um cargo</option>
        <?php foreach ($cargos as $cargo): ?>
            <option value="<?= htmlspecialchars((string) $cargo['id'], ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($cargo['cargo'], ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <button class="btn" type="submit">Salvar</button>
    <a class="btn btn-perigo" href="index.php">Cancelar</a>
</form>
<?php include '../includes/footer.php'; ?>

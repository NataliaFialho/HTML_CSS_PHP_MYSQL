<?php
require '../conexao.php';

$categorias = $pdo->query('SELECT id, nome_categoria FROM categorias ORDER BY nome_categoria')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO acao (id_categoria, matricula_pesquisador, titulo, descricao, data_, status_) VALUES (:id_categoria, :matricula_pesquisador, :titulo, :descricao, :data_, :status_)');
    $stmt->execute([
        ':id_categoria' => $_POST['id_categoria'],
        ':matricula_pesquisador' => $_POST['matricula_pesquisador'],
        ':titulo' => $_POST['titulo'],
        ':descricao' => $_POST['descricao'],
        ':data_' => $_POST['data_'],
        ':status_' => $_POST['status_']
    ]);
    header('Location: index.php');
    exit;
}

include '../includes/header.php';
?>
<h2>Novo cliente</h2>

<form method="post">
    <label for="titulo">Título</label>
    <input type="text" id="titulo" name="titulo" required>

    <label for="data_">Data</label>
    <input type="date" id="data_" name="data_" required>

    

    <label for="id_categoria">Categoria</label>
    <select id="id_categoria" name="id_categoria" required>
    <option value="">Selecione...</option>
    <?php foreach ($categorias as $cat): ?>
        <option value="<?= $cat['id'] ?>">
            <?= htmlspecialchars($cat['nome']) ?>
        </option>
    <?php endforeach; ?>
    </select>

    <label for="cpf">CPF</label>
    <input type="text" id="cpf" name="cpf" maxlength="14" required>

    <label for="email">E-mail</label>
    <input type="email" id="email" name="email">

    <label for="telefone">Telefone</label>
    <input type="text" id="telefone" name="telefone">

    <button class="btn" type="submit">Salvar</button>
    <a class="btn btn-perigo" href="index.php">Cancelar</a>
</form>
<?php include '../includes/footer.php'; ?>
// Criar uma função em PHP para cadastrar ações

<?php
require '../conexao.php';

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
<h2>Nova ação</h2>

<form method="post">
    <label for="titulo">Título</label>
    <input type="text" id="titulo" name="titulo" required>

    <label for="descricao">Descrição</label>
    <input type="text" id="descricao" name="descricao" required>

    <label for="data">Data de execução</label>
    <input type="date" id="data" name="data", required>

    <label for="categoria">Categoria</label>
    <input type="select" id="categoria" name="categoria" required>
    
    <button class="btn" type="submit">Salvar</button>
    <a class="btn btn-perigo" href="index.php">Cancelar</a>
</form>
<?php include '../includes/footer.php'; ?>

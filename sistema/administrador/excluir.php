<?php
require '../conexao.php';

$id = $_GET['matricula'] ?? null;
$erro = null;

if ($id) {
    try {
        $stmt = $pdo->prepare('DELETE FROM clientes WHERE matricula = :matricula');
        $stmt->execute([':matricula' => $matricula]);
    } catch (PDOException $e) {
        $erro = 'Não é possível excluir: o cliente possui locações registradas.';
    }
}

if (!$erro) {
    header('Location: index.php');
    exit;
}

include '../includes/header.php';
?>
<p class="aviso"><?= $erro ?></p>
<a class="btn" href="index.php">Voltar</a>
<?php include '../includes/footer.php'; ?>

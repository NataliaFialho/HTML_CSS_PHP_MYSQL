<?php
session_start();
require_once __DIR__ . '/../conexao.php';

$mensagemSucesso = '';
$mensagemErro = '';

// Depois de cadastrar, a página recarrega com ?sucesso=1 (evita duplicar no F5)
if (isset($_GET['sucesso'])) {
    $mensagemSucesso = 'Ação cadastrada com sucesso!';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo    = trim($_POST['titulo'] ?? '');
    $data      = trim($_POST['data_realizacao'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $idCategoria = filter_input(INPUT_POST, 'categoria', FILTER_VALIDATE_INT);
    $matricula = $_SESSION['matricula'] ?? null; // ajuste conforme seu login

    if ($titulo === '' || $data === '' || $descricao === '' || !$idCategoria) {
        $mensagemErro = 'Preencha todos os campos.';
    } elseif ($matricula === null) {
        $mensagemErro = 'Sessão expirada. Faça login novamente.';
    } else {
        try {
            $insere = $pdo->prepare(
                'INSERT INTO acao (id_categoria, matricula_pesquisador, titulo, descricao, data)
                 VALUES (:categoria, :matricula, :titulo, :descricao, :data)'
            );
            $insere->execute([
                ':categoria' => $idCategoria,
                ':matricula' => $matricula,
                ':titulo'    => $titulo,
                ':descricao' => $descricao,
                ':data'      => $data,
            ]);

            header('Location: painel_criar_acao.php?sucesso=1');
            exit;
        } catch (PDOException $e) {
            error_log('Erro ao cadastrar ação: ' . $e->getMessage());
            $mensagemErro = 'Não foi possível cadastrar a ação. Tente novamente.';
        }
    }
}

try {
    $consulta = $pdo->prepare('SELECT id, nome FROM categoria WHERE status = 1 ORDER BY nome');
    $consulta->execute();
    $categorias = $consulta->fetchAll();
} catch (PDOException $e) {
    error_log('Erro ao consultar categorias: ' . $e->getMessage());
    $categorias = [];
    $mensagemErro = 'Não foi possível carregar as categorias.';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova ação</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body class="paineldecriaracoes-pagina">

    <header class="paineldecriaracoes-cabecalho">
        <img src="../imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="paineldecriaracoes-logo">
        <h1 class="paineldecriaracoes-titulo-pesquisador">PESQUISADOR</h1>
        <button type="button" class="paineldecriaracoes-btn-sair" onclick="window.location.href='../tela_dois_cargos.php'">Sair</button>
    </header>

    <section class="paineldecriaracoes-barra-titulo">
        <h2 class="paineldecriaracoes-titulo-acoes">MINHAS AÇÕES</h2>
    </section>

    <section class="paineldecriaracoes-barra-nova-acao">
        <h2 class="paineldecriaracoes-titulo-nova-acao">NOVA AÇÃO</h2>
    </section>

    <main class="paineldecriaracoes-conteudo">
        <?php if ($mensagemErro !== ''): ?>
            <p class="paineldecriaracoes-aviso-categorias" role="alert">
                <?= htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <form class="paineldecriaracoes-formulario" method="POST" action="painel_criar_acao.php">
            <div class="paineldecriaracoes-linha-superior">
                <input type="text" name="titulo" class="paineldecriaracoes-campo-titulo"
                       placeholder="TÍTULO DA AÇÃO" maxlength="50" required
                       value="<?= htmlspecialchars($_POST['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="date" name="data_realizacao" class="paineldecriaracoes-campo-data" required
                       value="<?= htmlspecialchars($_POST['data_realizacao'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <textarea name="descricao" class="paineldecriaracoes-campo-descricao"
                      placeholder="DESCRIÇÃO" maxlength="100" required><?= htmlspecialchars($_POST['descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

            <select name="categoria" class="paineldecriaracoes-campo-categoria" required>
                <option value="" disabled <?= empty($_POST['categoria']) ? 'selected' : '' ?>>CATEGORIA DA AÇÃO</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= (int) $categoria['id'] ?>"
                        <?= (($_POST['categoria'] ?? '') == $categoria['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if (empty($categorias)): ?>
                <p class="paineldecriaracoes-aviso-categorias">Nenhuma categoria ativa foi encontrada. Cadastre uma categoria no banco de dados.</p>
            <?php endif; ?>

            <div class="paineldecriaracoes-btn">
                <button type="button" class="paineldecriaracoes-btn-cancelar" onclick="window.location.href='painel_pesquisador.php'">Cancelar</button>
                <button type="submit" class="paineldecriaracoes-btn-confirmar">Confirmar</button>
            </div>
        </form>
    </main>

    <?php if ($mensagemSucesso !== ''): ?>
        <?php include __DIR__ . '/popup_acao_cadastrada-sucesso.php'; ?>
    <?php endif; ?>

</body>
</html>
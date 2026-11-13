<?php
session_start();
require_once __DIR__ . '/../conexao.php';

if (!isset($_SESSION['matricula'])) {
    header('Location: ../index.php');
    exit;
}

$permissaoStmt = $pdo->prepare('SELECT 1 FROM usuario_cargo WHERE matricula = :matricula AND id_cargo = 1 LIMIT 1');
$permissaoStmt->execute([':matricula' => $_SESSION['matricula']]);

if (!$permissaoStmt->fetchColumn()) {
    http_response_code(403);
    exit('Somente administradores podem gerenciar categorias.');
}

$mensagemSucesso = '';
$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $idCategoria = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$idCategoria || $idCategoria <= 0) {$mensagemErro = 'Categoria inválida.';
    } else {
        try {
            if ($acao === 'editar') {
                $novoNome = trim($_POST['nome'] ?? '');
                $tamanhoNome = function_exists('mb_strlen')
                    ? mb_strlen($novoNome, 'UTF-8')
                    : strlen($novoNome);

                if ($novoNome === '') {$mensagemErro = 'Informe o nome da categoria.';
                } elseif ($tamanhoNome > 50) {$mensagemErro = 'O nome deve ter no máximo 50 caracteres.';
                } else {
                    $verifica = $pdo->prepare('SELECT id FROM categoria WHERE nome = :nome AND id <> :id LIMIT 1');
                    $verifica->execute([':nome' => $novoNome, ':id' => $idCategoria]);

                    if ($verifica->fetch()) {$mensagemErro = 'Já existe outra categoria com esse nome.';
                    } else {
                        $pdo->beginTransaction();
                        $atualiza = $pdo->prepare('UPDATE categoria SET nome = :nome WHERE id = :id');
                        $atualiza->execute([':nome' => $novoNome, ':id' => $idCategoria]);
                        $operacaoLog = 'Administrador editou a categoria: ' . $novoNome;

                    if (function_exists('mb_substr'))
                        {$operacaoLog = mb_substr($operacaoLog, 0, 100, 'UTF-8');
                    } 
                    else {$operacaoLog = substr($operacaoLog, 0, 100);
                    }
                        $logStmt = $pdo->prepare('INSERT INTO log (operacao, matricula_usuario, tabela_afetada, data_hora) VALUES (:operacao, :matricula, :tabela, NOW())');
                        $logStmt->execute([
                            ':operacao' => $operacaoLog,
                            ':matricula' => $_SESSION['matricula'],
                            ':tabela' => 'categoria'
                        ]);

                        $pdo->commit();
                        $mensagemSucesso = 'Categoria editada com sucesso!';
                    }
                }
            } elseif ($acao === 'desativar') {
                $pdo->beginTransaction();
                $desativa = $pdo->prepare(
                    'UPDATE categoria SET status = 0 WHERE id = :id AND status = 1'
                );
                $desativa->execute([':id' => $idCategoria]);

                if ($desativa->rowCount() > 0) {
                    $operacaoLog = 'Administrador desativou a categoria ' . $idCategoria;
                    $logStmt = $pdo->prepare('INSERT INTO log (operacao, matricula_usuario, tabela_afetada, data_hora) VALUES (:operacao, :matricula, :tabela, NOW())'
                    );
                    $logStmt->execute([':operacao' => $operacaoLog, ':matricula' => $_SESSION['matricula'], ':tabela' => 'categoria',]);
                    $pdo->commit();
                    $mensagemSucesso = 'Categoria desativada com sucesso!';
                } else {
                    $pdo->rollBack();
                    $mensagemErro = 'A categoria não foi encontrada ou já está desativada.';
                }
            } else {
                $mensagemErro = 'Operação inválida.';
            }
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Erro ao gerenciar categoria: ' . $e->getMessage());
            $mensagemErro = 'Não foi possível realizar a operação. Tente novamente.';
        }
    }
}

try {
    $consulta = $pdo->query('SELECT id, nome, status FROM categoria ORDER BY status DESC, nome ASC');
    $categorias = $consulta->fetchAll();
} catch (PDOException $e) {
    error_log('Erro ao consultar categorias: ' . $e->getMessage());
    $categorias = [];
    $mensagemErro = 'Não foi possível carregar as categorias.';
}

$idEmEdicao = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT) ?: 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body class="gerenciacategoria-pagina">

    <header class="gerenciacategoria-cabecalho">
        <img src="../imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="gerenciacategoria-logo">

        <h1 class="gerenciacategoria-titulo-administrador">ADMINISTRADOR</h1>
        <a href="../tela_dois_cargos.php" class="gerenciacategoria-btn-sair">Sair</a>
    </header>

    <section class="gerenciacategoria-faixa-titulo">
        <h2 class="gerenciacategoria-titulo-acoes">AÇÕES</h2>
    </section>

    <main class="gerenciacategoria-area-principal">
        <?php if ($mensagemSucesso !== ''): ?>
            <p class="gerenciacategoria-mensagem gerenciacategoria-sucesso" role="status">
                <?= htmlspecialchars($mensagemSucesso, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <?php if ($mensagemErro !== ''): ?>
            <p class="gerenciacategoria-mensagem gerenciacategoria-erro" role="alert">
                <?= htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <div class="usuario-lista-voltar">
            <a href="tela_escolha.php" class="gerenciacategoria-btn-editar">VOLTAR</a>
        </div>

        <?php if (empty($categorias)): ?>
            <p class="gerenciacategoria-vazio">Nenhuma categoria cadastrada.</p>
        <?php else: ?>
            <section class="gerenciacategoria-lista" aria-label="Categorias cadastradas">
                <?php foreach ($categorias as $categoria): ?>
                    <?php
                        $id = (int) $categoria['id'];
                        $nome = (string) $categoria['nome'];
                        $ativa = (int) $categoria['status'] === 1;
                        $estaEditando = $idEmEdicao === $id;
                    ?>

                    <article class="gerenciacategoria-card <?= $ativa ? '' : 'gerenciacategoria-card-inativa' ?>">
                        <?php if ($estaEditando): ?>
                            <form method="POST" action="gerenciar_categorias.php" class="gerenciacategoria-form-edicao">
                                <input type="hidden" name="acao" value="editar">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <label for="categoria-<?= $id ?>" class="gerenciacategoria-label-edicao">Nome da categoria</label>
                                <input type="text" id="categoria-<?= $id ?>" name="nome" class="gerenciacategoria-input-edicao" value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>" maxlength="50" required autofocus>

                                <div class="gerenciacategoria-acoes-edicao">
                                    <a href="gerenciar_categorias.php" class="gerenciacategoria-btn-cancelar">Cancelar</a>
                                    <button type="submit" class="gerenciacategoria-btn-salvar">Salvar</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <p class="gerenciacategoria-nome"><?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?></p>
                            <div class="gerenciacategoria-acoes-card">
                                <?php if ($ativa): ?>
                                    <form method="POST"
                                        action="gerenciar_categorias.php"
                                        class="gerenciacategoria-form-desativar">

                                        <input type="hidden" name="acao" value="desativar">
                                        <input type="hidden" name="id" value="<?= $id ?>">

                                        <button type="submit" class="gerenciacategoria-btn-desativar">
                                            DESATIVAR
                                        </button>

                                    </form>
                                <?php else: ?>
                                    <span class="gerenciacategoria-status-inativa">DESATIVADA</span>
                                <?php endif; ?>

                                <a href="?editar=<?= $id ?>" class="gerenciacategoria-btn-editar">EDITAR</a>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/popup_confirmar_desativacao_acao.php'; ?>

</body>
</html>
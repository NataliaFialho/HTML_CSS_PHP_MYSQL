<?php
session_start();
require_once __DIR__ . '/../conexao.php';

$novoNomeCategoria = '';
$mensagemSucesso = '';
$mensagemErro = '';

$idCategoria = filter_var(
    $_GET['id'] ?? null,
    FILTER_VALIDATE_INT
);

if ($idCategoria === false || $idCategoria === null || $idCategoria < 1) {
    http_response_code(400);
    $mensagemErro = 'ID da categoria inválido.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $mensagemErro === '') {
    $novoNomeCategoria = trim($_POST['nome'] ?? '');

    if (!isset($_SESSION['matricula'])) {
        http_response_code(401);
        $mensagemErro = 'É necessário estar autenticado para editar categorias.';

    } elseif ($novoNomeCategoria === '') {
        $mensagemErro = 'Informe o nome da categoria.';

    } elseif (
        (function_exists('mb_strlen')
            ? mb_strlen($novoNomeCategoria, 'UTF-8')
            : strlen($novoNomeCategoria)) > 50
    ) {
        $mensagemErro = 'O nome da categoria deve ter no máximo 50 caracteres.';

    } else {

        try {
            $permissaoStmt = $pdo->prepare('SELECT 1 FROM usuario_cargo WHERE matricula = :matricula AND id_cargo = 1 LIMIT 1'
            );

            $permissaoStmt->execute([':matricula' => $_SESSION['matricula']]);

            if (!$permissaoStmt->fetchColumn()) {
                http_response_code(403);
                $mensagemErro = 'Somente administradores podem editar categorias.';

            } else {
                $pdo->beginTransaction();
                $categoriaStmt = $pdo->prepare('SELECT id, nome FROM categoria WHERE id = :id FOR UPDATE');
                $categoriaStmt->execute([':id' => $idCategoria]);
                $categoriaAtual = $categoriaStmt->fetch(PDO::FETCH_ASSOC);

                if (!$categoriaAtual) {
                    $pdo->rollBack();
                    $mensagemErro = 'Categoria não encontrada.';

                } else {
                    $consulta = $pdo->prepare('SELECT id FROM categoria WHERE nome = :nome AND id != :id LIMIT 1');
                    $consulta->execute([':nome' => $novoNomeCategoria,':id' => $idCategoria
                    ]);

                    if ($consulta->fetch()) {
                        $pdo->rollBack();
                        $mensagemErro = 'Essa categoria já está cadastrada.';

                    } else {
                        $atualizacao = $pdo->prepare('UPDATE categoria SET nome = :nome WHERE id = :id');
                        $atualizacao->execute([':nome' => $novoNomeCategoria, ':id' => $idCategoria]);


                        $logStmt = $pdo->prepare('INSERT INTO log (operacao, matricula_usuario, tabela_afetada, data_hora) VALUES (:operacao, :matricula, :tabela, NOW())');
                        $logStmt->execute([':operacao' => 'Administrador editou uma categoria de ação: ' . $novoNomeCategoria, ':matricula' => $_SESSION['matricula'], ':tabela' => 'categoria']);
                        $pdo->commit();
                        $mensagemSucesso = 'Categoria atualizada com sucesso!';
                        $novoNomeCategoria = '';
                    }
                }
            }

        } catch (Throwable $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Erro ao editar categoria: ' . $e->getMessage());
            http_response_code(500);
            $mensagemErro = 'Não foi possível editar a categoria. Tente novamente.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar categoria de ação</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body class="cadastrocategoria-pagina">

    <header class="cadastrocategoria-cabecalho">
        <img src="../imagens/logo-embrapa.png"
             alt="Embrapa Gado de Corte"
             class="cadastrocategoria-logo">

        <h1 class="cadastrocategoria-titulo-administrador">ADMINISTRADOR</h1>
        <a href="../tela_dois_cargos.php" class="cadastrocategoria-btn-sair">Sair</a>
    </header>

    <section class="cadastrocategoria-faixa-titulo">
        <h2 class="cadastrocategoria-titulo-acoes">AÇÕES</h2>
    </section>

    <main class="cadastrocategoria-area-principal">
        <div class="cadastrocategoria-barra-adicionar">
            <h2>EDITAR</h2>
        </div>

        <section class="cadastrocategoria-painel">
            <form class="cadastrocategoria-formulario" method="POST" action="">

                <?php if ($mensagemSucesso !== ''): ?>
                    <p class="cadastrocategoria-mensagem cadastrocategoria-sucesso" role="status">
                        <?= htmlspecialchars($mensagemSucesso, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <?php if ($mensagemErro !== ''): ?>
                    <p class="cadastrocategoria-mensagem cadastrocategoria-erro" role="alert">
                        <?= htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <label class="cadastrocategoria-label" for="nome-categoria">Novo nome</label>
                <input
                    type="text"
                    id="nome-categoria"
                    name="nome"
                    class="cadastrocategoria-campo-nome"
                    placeholder="Novo nome"
                    maxlength="50"
                    value="<?= htmlspecialchars($novoNomeCategoria, ENT_QUOTES, 'UTF-8') ?>"
                    required>

                <div class="cadastrocategoria-botoes">
                    <!-- Analisar esse uso do history.back(), senão funcionar quando a todas as paginas estiverem prontas deverá ser alterado por um link -->
                    <button type="button" onclick="history.back(); return false;" class="cadastrocategoria-btn-cancelar" >
                        Cancelar
                    </button>
                    <button type="submit" class="cadastrocategoria-btn-confirmar">
                        Confirmar
                    </button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
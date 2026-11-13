<?php
require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/../auth.php';

$matriculaOperador = exigirCargo($pdo, 1);

$nomeCategoria = '';
$mensagemSucesso = '';
$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeCategoria = trim($_POST['nome'] ?? '');

    if ($nomeCategoria === '') {
        $mensagemErro = 'Informe o nome da categoria.';
    } elseif (mb_strlen($nomeCategoria, 'UTF-8') > 50) {
        $mensagemErro = 'O nome da categoria deve ter no máximo 50 caracteres.';
    } else {
        try {
            $consulta = $pdo->prepare('SELECT id FROM categoria WHERE nome = :nome LIMIT 1');
            $consulta->execute([':nome' => $nomeCategoria]);

            if ($consulta->fetch()) {
                $mensagemErro = 'Essa categoria já está cadastrada.';
            } else {
                $pdo->beginTransaction();

                $cadastro = $pdo->prepare('INSERT INTO categoria (nome) VALUES (:nome)');
                $cadastro->execute([':nome' => $nomeCategoria]);

                $operacaoLog = mb_substr('Administrador cadastrou uma nova categoria de ação: ' . $nomeCategoria, 0, 100, 'UTF-8');

                $logStmt = $pdo->prepare('INSERT INTO log (operacao, matricula_usuario, tabela_afetada, data_hora) VALUES (:operacao, :matricula, :tabela, NOW())');
                $logStmt->execute([':operacao' => $operacaoLog, ':matricula' => $matriculaOperador, ':tabela' => 'categoria']);

                $pdo->commit();
                $mensagemSucesso = 'Categoria cadastrada com sucesso!';
                $nomeCategoria = '';
            }
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Erro ao cadastrar categoria: ' . $e->getMessage());
            $mensagemErro = 'Não foi possível cadastrar a categoria. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar categoria</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body class="cadastrocategoria-pagina">
    
    <?php if ($mensagemSucesso !== ''): ?>
        <?php include __DIR__ . '/../popup_categoria_criada.php'; ?>
    <?php endif; ?>

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
            <h2>ADICIONAR</h2>
        </div>

        <section class="cadastrocategoria-painel">
            <form class="cadastrocategoria-formulario" method="POST" action="">
                <?php if ($mensagemErro !== ''): ?>
                    <p class="cadastrocategoria-mensagem cadastrocategoria-erro" role="alert">
                        <?= htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <label class="cadastrocategoria-label" for="nome-categoria">Nome</label>
                <input
                    type="text"
                    id="nome-categoria"
                    name="nome"
                    class="cadastrocategoria-campo-nome"
                    placeholder="Nome"
                    maxlength="50"
                    value="<?= htmlspecialchars($nomeCategoria, ENT_QUOTES, 'UTF-8') ?>"
                    required>

                <div class="cadastrocategoria-botoes">
                    <button type="button" class="cadastrocategoria-btn-cancelar" onclick="window.location.href='tela_escolha.php'">
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
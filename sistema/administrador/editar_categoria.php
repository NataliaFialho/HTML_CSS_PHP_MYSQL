<?php
require_once __DIR__ . '/../conexao.php';

$novoNomeCategoria = '';
$mensagemSucesso = '';
$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        $novoNomeCategoria = trim($_POST['nome'] ?? '');

        if ($novoNomeCategoria === '') 
            {
                $mensagemErro = 'Informe o nome da categoria.';
            } 
        elseif ((function_exists('mb_strlen') ? mb_strlen($novoNomeCategoria, 'UTF-8') : strlen($novoNomeCategoria)) > 50) 
            {
                $mensagemErro = 'O nome da categoria deve ter no máximo 50 caracteres.';
            } 
        else 
            {
                try {
                        $consulta = $pdo->prepare(
                            'SELECT id FROM categoria WHERE nome = :nome AND id != :id LIMIT 1'
                        );
                        $consulta->execute([
                            ':nome' => $novoNomeCategoria,
                            ':id'   => $_GET['id'] ?? 0
                        ]);

                        if ($consulta->fetch()) 
                        {
                            $mensagemErro = 'Essa categoria já está cadastrada.';
                        } 
                        else 
                        {
                            $atualizacao = $pdo->prepare(
                                'UPDATE categoria SET nome = :nome WHERE id = :id'
                            );
                            $atualizacao->execute([
                                ':nome' => $novoNomeCategoria,
                                ':id'   => $_GET['id'] ?? 0
                            ]);
                            $mensagemSucesso = 'Categoria atualizada com sucesso!';
                        }
                    }
                catch (PDOException $e) 
                    {
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
    <title>Editar categoria de ação</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body class="cadastrocategoria-pagina">

    <header class="cadastrocategoria-cabecalho">
        <img src="../imagens/logo-embrapa.png"
             alt="Embrapa Gado de Corte"
             class="cadastrocategoria-logo">

        <h1 class="cadastrocategoria-titulo-administrador">ADMINISTRADOR</h1>
        <a href="../index.php" class="cadastrocategoria-btn-sair">Sair</a>
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

                <label class="cadastrocategoria-label" for="nome-categoria">Nome</label>
                <input
                    type="text"
                    id="nome-categoria"
                    name="nome"
                    class="cadastrocategoria-campo-nome"
                    placeholder="Nome"
                    maxlength="50"
                    value="<?= htmlspecialchars($novoNomeCategoria, ENT_QUOTES, 'UTF-8') ?>"
                    required>

                <div class="cadastrocategoria-botoes">
                    <button type="reset" class="cadastrocategoria-btn-cancelar">
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
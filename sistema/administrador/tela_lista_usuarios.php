<?php
require_once __DIR__ . '/../conexao.php';

$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Allow: GET');
    exit('Método não permitido.');
}

try {
    $usuarios = $pdo->query(
        'SELECT usuarios.matricula, usuarios.nome, usuarios.email, usuarios.status,
                (
                    SELECT GROUP_CONCAT(cargo.cargo ORDER BY cargo.cargo SEPARATOR ", ")
                    FROM usuario_cargo
                    INNER JOIN cargo ON cargo.id = usuario_cargo.id_cargo
                    WHERE usuario_cargo.matricula = usuarios.matricula
                ) AS funcoes
         FROM usuarios
         ORDER BY usuarios.status DESC, usuarios.nome ASC'
    )->fetchAll();
} catch (PDOException $e) {
    error_log('Erro ao carregar usuários: ' . $e->getMessage());
    $usuarios = [];
    $mensagemErro = 'Não foi possível carregar os usuários.';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários cadastrados</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body class="gerenciacategoria-pagina">
    <header class="paineldecriaracoes-cabecalho">
        <img src="../imagens/logo-embrapa.png" alt="Embrapa Gado de Corte" class="paineldecriaracoes-logo">
        <h1 class="paineldecriaracoes-titulo-pesquisador">ADMINISTRADOR</h1>
        <button type="button" class="paineldecriaracoes-btn-sair">Sair</button>
    </header>

    <section class="paineldecriaracoes-barra-titulo">
    </section>

    <section class="paineldecriaracoes-barra-nova-acao">
        <h2 class="paineldecriaracoes-titulo-nova-acao">USUÁRIOS CADASTRADOS</h2>
    </section>

    <main class="gerenciacategoria-area-principal">
        <?php if ($mensagemErro !== ''): ?>
            <p class="gerenciacategoria-mensagem gerenciacategoria-erro" role="alert">
                <?= htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <div class="usuario-lista-voltar">
            <a href="tela_escolha.php" class="gerenciacategoria-btn-editar">VOLTAR</a>
        </div>

        <?php if ($usuarios === []): ?>
            <p class="gerenciacategoria-vazio">Nenhum usuário cadastrado.</p>
        <?php else: ?>
            <section class="gerenciacategoria-lista" aria-label="Usuários cadastrados">
                <?php foreach ($usuarios as $usuario): ?>
                    <?php
                    $matricula = (int) $usuario['matricula'];
                    $ativo = (int) $usuario['status'] === 1;
                    ?>
                    <article class="gerenciacategoria-card usuario-card<?= $ativo ? '' : ' gerenciacategoria-card-inativa' ?>">
                        <a class="usuario-card-dados"
                           href="tela_editar_usuario.php?matricula=<?= $matricula ?>"
                           aria-label="Editar usuário <?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>">
                            <p class="usuario-card-nome">
                                <?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <p class="usuario-card-matricula">Matrícula: <?= $matricula ?></p>
                            <p class="usuario-card-email">
                                <?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <p class="usuario-card-funcoes">
                                Funções: <?= htmlspecialchars($usuario['funcoes'] ?? 'Nenhuma atribuída', ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </a>
                        <div class="usuario-card-acoes">
                            <?php if ($ativo): ?>
                                <button type="button" class="gerenciacategoria-btn-desativar">DESATIVAR</button>
                            <?php else: ?>
                                <button type="button" class="gerenciacategoria-btn-desativar" disabled>
                                    DESATIVADO
                                </button>
                            <?php endif; ?>
                            <a href="tela_editar_usuario.php?matricula=<?= $matricula ?>"
                               class="gerenciacategoria-btn-editar">EDITAR</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>

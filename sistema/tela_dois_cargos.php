<?php

session_start();

require_once "conexao.php";

if (isset($_GET['sair']) && $_GET['sair'] === '1') {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $parametrosCookie = session_get_cookie_params();
        setcookie(session_name(),'',time() - 42000,
            $parametrosCookie['path'],
            $parametrosCookie['domain'],
            $parametrosCookie['secure'],
            $parametrosCookie['httponly']
        );
    }

    session_destroy();
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION["matricula"])) {
    header("Location: index.php");
    exit;
}

$matricula = $_SESSION["matricula"];

$sql = "SELECT cargo.id, cargo.cargo FROM usuario_cargo INNER JOIN cargo ON usuario_cargo.id_cargo = cargo.id WHERE usuario_cargo.matricula = :matricula";
$consulta = $pdo->prepare($sql);
$consulta->execute(["matricula" => $matricula]);
$cargos = $consulta->fetchAll(PDO::FETCH_ASSOC);

$paineis = [
    1 => "administrador/tela_escolha.php",
    2 => "validador/validar.php",
    3 => "comite/log.php",
    4 => "pesquisador/painel_pesquisador.php"
];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilo.css?v=<?= filemtime(__DIR__ . '/css/estilo.css') ?>">
</head>
<body>
    <div class="formatacao-div">
        <div class="logo-embrapa">
            <img src="imagens/logo-embrapa.png" alt="Logo Embrapa">
        </div>
        <a class="botao-sair-cargos" href="tela_dois_cargos.php?sair=1">Sair</a>

        <?php if (count($cargos) > 0): ?>
            <?php foreach ($cargos as $cargo): ?>
                <?php if (isset($paineis[$cargo["id"]])): ?>
                    <a class="botao"
                       href="<?php echo htmlspecialchars($paineis[$cargo["id"]]); ?>">
                        <p class="texto-formatacao">
                            <?php echo htmlspecialchars($cargo["cargo"]); ?>
                        </p>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Você não possui cargos cadastrados.</p>
        <?php endif; ?>
    </div>
</body>
</html>
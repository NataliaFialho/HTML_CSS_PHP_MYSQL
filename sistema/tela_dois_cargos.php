
<?php

session_start();

require_once "conexao.php";

// 1. Verifica se existe uma matrícula na sessão
// if (!isset($_SESSION["matricula"])) {
//     header("Location: index.php");
//     exit;
// }

// 2. Recupera a matrícula do usuário
$matricula = $_SESSION["matricula"];

// 3. Consulta os cargos vinculados ao usuário
$sql = "SELECT cargo.id, cargo.cargo
        FROM usuario_cargo
        INNER JOIN cargo
            ON usuario_cargo.id_cargo = cargo.id
        WHERE usuario_cargo.matricula = :matricula";

$consulta = $pdo->prepare($sql);

$consulta->execute([
    "matricula" => $matricula
]);

// 4. Recupera todos os cargos encontrados
$cargos = $consulta->fetchAll(PDO::FETCH_ASSOC);

// 5. Define os destinos de cada cargo
$paineis = [
    1 => "", //Aqui tem de por o painel do Administrador
    2 => "", //Aqui tem de por o painel do Validador
    3 => "", //Aqui tem de por o painel do Comitê
    4 => "" //Aqui tem de por o painel do Pesquisador
];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de seleção de cargos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <div class="formatacao-div">

        <div class="logo-embrapa">
            <img src="imagens/logo-embrapa.png" alt="Logo Embrapa">
        </div>

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

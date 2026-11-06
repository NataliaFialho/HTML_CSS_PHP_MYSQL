<?php

session_start();

require_once "conexao.php";

$mostrarPopupAcessoNegado = false;
$loginSucesso = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];

    $sql = "SELECT * FROM usuarios WHERE email = :email";

    $consulta = $pdo->prepare($sql);

    $consulta->execute([
        "email" => $email
    ]);

    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    if (
        $usuario &&
        password_verify($senha, $usuario["senha"]) &&
        $usuario["status"] == 1
    ) {

        session_regenerate_id(true);

        $_SESSION["matricula"] = $usuario["matricula"];
        $_SESSION["nome"] = $usuario["nome"];
        $_SESSION["email"] = $usuario["email"];
        $_SESSION["id_cargo"] = $usuario["id_cargo"];

        $loginSucesso = true;

    } else {

        $mostrarPopupAcessoNegado = true;

    }
}

?>




<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Embrapa - Gado de Corte</title>

    <link rel="stylesheet" href="css/estilo.css">

</head>

<body>

    <main class="pagina-principal">

        <div class="logo-embrapa">

            <img src="imagens/logo-embrapa.png" alt="Embrapa Gado de Corte">

        </div>

        <section class="caixa-login">

            <h1 class="titulo-login">Tela de Login</h1>

            <?php if ($loginSucesso): ?>

                <p>Login realizado com sucesso!</p>

            <?php endif; ?>

            <form class="formulario-login" method="POST" action="index.php">

                <input
                    type="email"
                    name="email"
                    class="input-email"
                    placeholder="Email"
                    required
                >

                <input
                    type="password"
                    name="senha"
                    class="input-senha"
                    placeholder="Senha"
                    required
                >

                <button type="submit" id="btn-login">
                    Entrar
                </button>

            </form>

        </section>

    </main>

    <?php include "popup_acesso_negado.php"; ?>

</body>

</html>

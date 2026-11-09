<?php
session_start();

require_once "conexao.php";

$mostrarPopupAcessoNegado = false;
$erroSistema = false;
$loginSucesso = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";

    if (isset($pdo) && $pdo instanceof PDO) {

        try {
            $sql = "SELECT * FROM usuarios WHERE email = :email";

            $consulta = $pdo->prepare($sql);
            $consulta->execute(["email" => $email]);

            $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

            if (
                $usuario &&
                isset($usuario["senha"]) &&
                password_verify($senha, $usuario["senha"]) &&
                isset($usuario["status"]) &&
                (int)$usuario["status"] === 1
            ) {

                session_regenerate_id(true);

                $_SESSION["matricula"] = $usuario["matricula"];
                $_SESSION["nome"] = $usuario["nome"];
                $_SESSION["email"] = $usuario["email"];

                $loginSucesso = true;

            } else {
                $mostrarPopupAcessoNegado = true;
            }

        } catch (PDOException $e) {
            $erroSistema = true;
        }

    } else {
        $erroSistema = true;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Embrapa Gado de Corte</title>

    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

    <main class="pagina-principal">

        <img
            src="imagens/logo-embrapa.png"
            alt="Embrapa Gado de Corte"
            class="logo-embrapa"
        >

        <section class="caixa-login">

            <h1 class="titulo-login">Tela de Login</h1>

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

            <?php if ($loginSucesso): ?>
                <p>Login realizado com sucesso!</p>
            <?php endif; ?>

            <?php if ($erroSistema): ?>
                <p>Não foi possível conectar ao banco de dados.</p>
            <?php endif; ?>

        </section>

    </main>

    <?php
    if ($mostrarPopupAcessoNegado === true) {
        include "popup_acesso_negado.php";
    }
    ?>

</body>

</html>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela dois cargos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>
    <?php
    // $cargo = $_GET["cargo"] ?? "";

    // if ($cargo == "1") 
    // {
    //     header("Location: tela_administrador.php");
    //     exit;
    // }
    // elseif ($cargo == "2") 
    // {
    //     header("Location: tela_validador.php");
    //     exit;
    // }
    // elseif ($cargo == "3") 
    // {
    //     header("Location: tela_comite.php");
    //     exit;
    // }
    // elseif ($cargo == "4")
    // {
    //     header("Location: tela_pesquisador.php");
    //     exit;
    // }
    // else 
    // {
    //     echo "Cargo inválido.";
    //     exit;
    // }
    ?>
    
    <div class="formatacao-div">
        <div class="logo-embrapa">
            <img src="imagens/logo-embrapa.png" alt="Logo Embrapa">
        </div>

        <a class="botao" href="/sistema/tela_dois_cargos.php?cargo=1">
            <p class="texto-formatacao">Administrador</p>
        </a>

        <a class="botao" href="/sistema/tela_dois_cargos.php?cargo=2">
            <p class="texto-formatacao">Validador</p>
        </a>

        <a class="botao" href="/sistema/tela_dois_cargos.php?cargo=3">
            <p class="texto-formatacao">Comite</p>
        </a>

        <a class="botao" href="/sistema/tela_dois_cargos.php?cargo=4">
            <p class="texto-formatacao">Pesquisador</p>
        </a>
    </div>
        



</body>

</html>
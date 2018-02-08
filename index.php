<?php
/**
 *    Created by DevWolf.
 *   Author: Kevin Marques
 *    Designer: Iuri
 *   Date: 19/12/2017 - 13:47
 */

//Todos as páginas devem incluir o init
include "init.php";
/** WARNING DEVINFO: O SISTEMA É BÁSICO E NÃO PARTICIPA DE ASSINCRONICIDADE
 ENTAO, O FUNCIONAMENTO SERA A PARTIR DE SELF-PAGE
 *
 * htaccess ativo em todas as páginas
 */
use Modules\Authenticate;
$authenticate = new Authenticate();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Projeto Marcio</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Actor">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Sans">
    <!-- INFORM: TRABALHANDO COM HTACCESS DO SERVIDOR APACHE, CAMINHO ABSOLUTO(/) NA FOLHA ABAIXO-->
    <link rel="stylesheet" type="text/css" href="/assets/css/styles.min.css">
</head>

<body>
    <div class="login-dark" style="background-color:rgb(255,255,255);background-image:url(&quot;0&quot;);height:650px;">
        <form method="post" enctype="application/x-www-form-urlencoded" action="/autenticar/">
            <h3 style="text-align: center"><?php echo _tr("Texts")->system_access; ?></h3>
            <div class="illustration"><i class="icon ion-ios-locked-outline"></i></div>
            <!-- MESSAGES PLACE -->
            <div class="messages"><?php $authenticate->showMessages(); ?></div>
            <!-- ==============================  -->
            <div class="form-group"><input class="form-control" type="text" name="login" required="required" placeholder="Login" autofocus="autofocus"></div>
            <div class="form-group"><input class="form-control" type="password" required="required" name="senha" placeholder="<?php _tr("Texts")->password ?>"></div>
            <div class="form-group"><button class="btn btn-primary btn-block" type="submit" style="background-color:rgb(0,81,188);">Logar </button></div></form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
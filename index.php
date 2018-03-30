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
    <?php include "Template/header.php";?>
    <!-- INFORM: TRABALHANDO COM HTACCESS DO SERVIDOR APACHE, CAMINHO ABSOLUTO(/) NA FOLHA ABAIXO-->

</head>

<body class="bg">
    <div class="main">
        <div class="login-dark" style="background-color:rgb(255,255,255);height:650px;">
            <form method="post" enctype="application/x-www-form-urlencoded" action="/autenticar/">
                <h3 style="text-align: center"><?php echo _tr("Texts")->system_access; ?></h3>
                <div class="illustration"><i class="dw icon-locked-outline text-primary extra-icon in-center"></i></div>
                <!-- MESSAGES PLACE -->
                <div class="messages"><?php $authenticate->showMessages(); ?></div>
                <!-- ==============================  -->
                <div class="form-group"><input class="form-control" type="text" name="login" required="required" placeholder="<?php echo _tr("Texts")->login ?>" autofocus="autofocus"></div>
                <div class="form-group"><input class="form-control" type="password" required="required" name="senha" placeholder="<?php echo _tr("Texts")->password ?>"></div>
                <div class="form-group"><button class="btn btn-primary btn-block" type="submit" style="background-color:rgb(0,81,188);"><?php echo _tr("Texts")->logon ?> </button></div></form>
        </div>
    </div>

    <?php include "Template/footer.php";?>
</body>

</html>
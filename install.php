<?php
/**
 *    Created by DevWolf.
 *   Author: Kevin Marques
 *   Date: 27/03/2018 - 20:39
 */
include "Core/Installer.module";

?>
<!DOCTYPE html>


<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php Installer::Title();?></title>
    <?php include "Template/header.php";?>
    <link rel="stylesheet" type="text/css" href="/assets/css/extras.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/install.min.css">
</head>
<?php  $Installer = new Installer();?>
<body>
<div class="main" role="main">
    <article class="header">
        <i class="fa fa-cogs align-self-center iconInstall" aria-hidden="true"></i>
        <h2 class="text-center">Sistema de instalação!</h2>
        <h4 class="text-center">A partir de agora voce definirá alguma informações úteis para o funcionamento do sistema</h4>
    </article>
    <div class="container">
        <div class="row ">

            <div class="col-sm">

                <?php


                switch ($_GET['area1']){

                    case "Passo-1":
                        $Installer->ui_step01();
                        break;
                    case "Passo-2":
                        $Installer->ui_step02();
                        break;
                    case "Passo-3":
                        $Installer->ui_step03();
                        break;
                    case "Final":
                        $Installer->ui_final();
                        break;
                    case "Limpar":
                        $Installer->clearAll();
                        break;
                    default:
                        $Installer->ui_defaultStep();
                        break;
                }

                ?>
            </div>

        </div>
    </div>

</div>

<footer class="page-footer ftr">
    <div class="devLogo float-right"></div>
    <div class="copy text-center">&copy; 2017 DevWolf Team JR.</div>
    <div class="reserve text-center">Todos os Direitos Reservados.</div>
</footer>

</body>

</html>
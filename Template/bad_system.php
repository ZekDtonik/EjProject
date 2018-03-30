<!DOCTYPE html>
<?php
/**
 *    Created by DevWolf.
 *   Author: Kevin Marques
 *   Date: 27/03/2018 - 20:39
 */
?>



<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro Fatal - Sistema Corrompido!</title>
    <?php include "header.php";?>
    <link rel="stylesheet" type="text/css" href="/assets/css/extras.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/install.min.css">
</head>
<body>
<div class="main" role="main">
    <article class="header">

        <h1 class="text-center ">Sistema corrompido</h1>
        <h4 class="text-center">Não é possível iniciar o Sistema</h4>
    </article>
    <div class="container">
        <div class="row ">

            <div class="col-sm p-2">
                <i class=" fa fa-times-circle text-danger align-self-center  iconInstall" style="font-size: 80px;"></i>
                <h3 class="text-danger  text-center">Não é possível iniciar o sistema devido a falta de um módulo crítico.</h3>
                <p class="text-center">Impossível prosseguir a partir daqui. É necessário copiar todo o sistema novamente para o servidor e inicar uma nova instalaão</p>
                <p class="text-center">Não se preocupe, o seu banco está protegido, apenas esteja atento ao refazer a instalação para desmarcar a opção de redefinir banco.</p>

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
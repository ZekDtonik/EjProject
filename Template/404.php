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
    <title>Oops! 404 - Documento não Encontrado</title>
    <?php include "header.php";?>
    <link rel="stylesheet" type="text/css" href="/assets/css/extras.min.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/install.min.css">
</head>
<body>
<div class="main" role="main">
    <article class="header">

        <h1 class="text-center h404">404!</h1>
        <h4 class="text-center">Oops! O documento que esta procurando não existe!</h4>
    </article>
    <div class="container">
        <div class="row ">

            <div class="col-sm p-2">
                <i class=" fa fa-warning text-danger align-self-center  iconInstall" style="font-size: 80px;"></i>
                <h1 class="text-danger  text-center">O link ou arquivo que esta tentando acessar não existe!</h1>
                <p class="text-center">Verifique o link que esta tentando acessar. O documento em questão não existe no servidor.</p>
            </div>

        </div>
    </div>

</div>

<?php include "footer.php";?>


</body>

</html>
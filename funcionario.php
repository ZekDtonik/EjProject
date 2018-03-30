
<?php
/**
 *    Created by DevWolf.
 *   Author: Kevin Marques
 *   Date: 19/12/2017 - 22:49
 */

include "init.php";
use Modules\Employee;
$Employee = new Employee();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo _tr("Titles")->employee;?></title>
    <?php include "Template/header.php";?>
</head>

<body>
    <?php
    $Employee->ui_header();
    switch ($_GET[_mainAction]){
        case _documents:
            switch ($_GET[_subAction]){
                case _download:
                    $Employee->ui_back(DS._employee.DS._documents);
                    $Employee->setShowMessage($Employee->downloadSystem($_GET[_lastAction]));
                    $Employee->showMessage();
                    break;
                default:
                    $Employee->visualizaMsg(true);
                    break;
            }
            break;
        //FUNCIONARIOS
            //página de saida, o link envia para aqui, e o metodo aplica a saida
        case _logout:
            //logout (true) Redirecionamento ativo apos remoção de sessão
            Modules\Authenticate::logout(true);
            break;
        case _settings:
            if($_GET[_subAction] == _make){
                $Employee->settings();
            }
            $Employee->ui_settings();
            break;
        default:
            $Employee->visualizaMsg();
            //padrão de tela inicial.s
            //$Administration->ui_home();
            break;
    }

    ?>
    <?php include "Template/footer.php";?>
</body>

</html>

<?php
/**
 *    Created by DevWolf.
 *   Author: Kevin Marques
 *   Date: 19/12/2017 - 22:49
 */

include "init.php";
use Modules\Administrator;
$Administration = new Administrator();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo _tr("Titles")->admin;?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Actor">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Sans">
    <link rel="stylesheet" href="/assets/css/styles.min.css">
    <link rel="stylesheet" href="/assets/css/pagination.css">
</head>

<body>
    <?php
    $Administration->ui_header();
    switch ($_GET[_mainAction]){
        //PARA ENVIAR DOCUMENTOS
        case _send:
            //Sub ação, observe se os menus possuem subcategorias,
            //caso exista, e necessário adicionar um sub switch
            switch ($_GET[_subAction]){
                //caso seja documento (anexo)
                case _doc:
                    if($_GET[_lastAction] == _make){
                        $Administration->enviarMsgs(true);
                    }
                    $Administration->ui_send_messages(true);
                    break;
                //caso seja mensagem (texto sem anexo)
                default:
                    if($_GET[_lastAction] == _make){

                        $Administration->enviarMsgs();
                    }
                    $Administration->ui_send_messages();
                    break;
            }
            break;
        //FUNCIONARIOS
        case _employee:
                switch ($_GET[_subAction]){
                    case _register:
                        if ($_GET[_lastAction] == _make){
                                $Administration->cadastrarFuncionario();
                        }
                        $Administration->ui_register_user();
                        break;
                    default:
                        $Administration->ui_visualize_employee();
                        break;
                }
            break;
        case _category:
            switch ($_GET[_subAction]){
                case _edit:
                    if($_GET[_endAction] == _make){
                        $Administration->editarCategoria();
                    }
                    $Administration->ui_visualize_category('edit');
                    break;
                case _remove:
                    if($_GET[_endAction] == _make){
                        $Administration->removercategoria();
                    }
                    $Administration->ui_remove_category();
                    break;
                default:
                    if($_GET[_lastAction] == _make){
                        $Administration->cadastrarCategoria();
                    }
                    $Administration->ui_visualize_category();
                    break;
            }
            break;
            //página de saida, o link envia para aqui, e o metodo aplica a saida
        case _logout:
            //logout (true) Redirecionamento ativo apos remoção de sessão
            Modules\Authenticate::logout(true);
            break;
        default:
            //padrão de tela inicial.s
            $Administration->ui_home();
            break;
    }

    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
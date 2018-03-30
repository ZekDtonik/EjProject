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
    <?php include "Template/header.php";?>
    <noscript><?php \Classes\System::noScript();?></noscript>
    <!-- TODO: css para no script-->
</head>

<body>
<?php $Administration->ui_header();?>
    <div class="main fix-main">
        <?php

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
                    case _edit:
                        if ($_GET[_lastAction] == _make){
                            $Administration->cadastrarFuncionario(true);
                        }
                        $Administration->ui_edit_user();
                        break;
                    case _remove:
                        if ($_GET[_endAction] == _make){
                            $Administration->removerFuncionario();
                        }
                        $Administration->ui_remove_user();
                        break;
                    default:
                        $Administration->ui_visualize_employee();

                        break;
                }
            break;
        ///CATEGORIA
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
        case _report:
            switch ($_GET[_subAction])
            {

                default:
                    $Administration->ui_report();
                    break;
            }
            break;
            //página de saida, o link envia para aqui, e o metodo aplica a saida
        case _logout:
            //logout (true) Redirecionamento ativo apos remoção de sessão
            Modules\Authenticate::logout(true);
            break;
        case _settings:
            if($_GET[_subAction] == _make){
                $Administration->settings();
            }
            $Administration->ui_settings();
            break;
        default:
            //padrão de tela inicial.s
            $Administration->ui_home();
            break;
    }

    ?>
    </div>
    <?php include "Template/footer.php";?>

</body>

</html>
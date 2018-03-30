<?php
/**
 *    Created by DevWolf.
 *   Author: Kevin Marques
 *   Date: 24/03/2018 - 13:57
 */
include "init.php";
$Administration = new \Modules\Administrator();
switch ($_GET[_mainAction]){
    //PARA ENVIAR DOCUMENTOS
    //FUNCIONARIOS
    case _employee:
        switch ($_GET[_subAction]){
            case _register:
                    $Administration->cadastrarFuncionario();
                    break;
            case _edit:
                $Administration->cadastrarFuncionario(true);
                break;
        }
        break;
    case _pdf:
        $Administration->showPdf($_GET[_subAction]);
        break;
    case _chvwd:
        $Administration->checkView($_GET[_subAction]);
        break;

}
//Mostra a mensagem padrão independente da área
$Administration->showMessage();
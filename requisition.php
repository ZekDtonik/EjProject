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
        }
        break;

}
//Mostra a mensagem padrão independente da área
$Administration->showMessage();
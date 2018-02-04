<?php
/**
 *    Created by DevWolf.
 *      Author: Kevin Marques
 *    Date: 18/12/2017 - 11:34
 */
session_name('devWolf_AppSession');
session_start();
//----------------------
//Tempo padrão para sair do sistema
$sessionExpireTime = 15;//em minutos;
//----------------------
$typeDb = "MySql";//Padrão de utilização de MySql

$WorkPlace = $_SERVER["SERVER_ADDR"];
//Frame para desenvolvimento
/** @param As definições são aplicadas baseado no ip do servidor, assim facilita a modificação
 *
 */
switch ($WorkPlace){
    /** AMBIENTE DE DESENVOLVIMENTO */
    case "127.0.0.1":
    case "192.168.0.100":
    case "127.0.0.3":
        $userName = "root";
        $passwd = "";
        $dataBaseName = "ejdb";
        $hostName = "127.0.0.1";
        $hostPort = 3306;//ATENÇÃO: APLICAÇÃO DE PORTA INCORRETA, ACARRETA EM TRAVAMENTO DO SERVIDOR
        //SEM RESPOSTA OU RETORNO PARA O USUÁRIO
        break;
    /** AMBIENTE PADRÃO
     * -- ALTERAAR AS DEFINIÇÕES AQUI */
    default:
        //INFORMAÇÕES DE BANCO DE DADOS!
        $userName = "root";
        $passwd = "usbw";
        $dataBaseName = "ejdb";
        $hostName = "127.0.0.1";
        $hostPort = 3306;//ATENÇÃO: APLICAÇÃO DE PORTA INCORRETA, ACARRETA EM TRAVAMENTO DO SERVIDOR
        //SEM RESPOSTA OU RETORNO PARA O USUÁRIO
        break;
}


//Nao alterar dependências
//================================//
require "Core/Definitions.php";
//=================================//
//AUTO LOAD MÓDULOS
$Kernel = new Kernel();
$Kernel->checkClientStatus();
//INSTANCIAS DE CLASSE VEM APOIS A INSTANCIA DO KERNEL
function _tr($module = null){

    return empty($module) ? \Modules\Language::getLanguage() : \Modules\Language::getLanguage()->$module;
}







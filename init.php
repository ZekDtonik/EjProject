<?php
/**
 *    Created by DevWolf.
 *      Author: Kevin Marques
 *    Date: 18/12/2017 - 11:34
 */
session_name('devWolf_AppSession');
session_start();
//----------------------
$WorkPlace = $_SERVER["SERVER_ADDR"];
//
$SystemData = [];
include "Core/Assembly.inc";
$Assembly = new Main\Assembly();

//Tempo padrão para sair do sistema
//$sessionExpireTime = 15;//em minutos;
$sessionExpireTime = $Assembly::cfg("sessionexpiretime");
//----------------------
//$typeDb = "MySql";//Padrão de utilização de MySql
$typeDb = $Assembly::cfg("typedb");//Padrão de utilização de MySql
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
        $userName = $Assembly::cfg("username");
        $passwd = $Assembly::cfg("password");
        $dataBaseName = $Assembly::cfg("database");
        $hostName = $Assembly::cfg("hostname");
        $hostPort = $Assembly::cfg("port");//ATENÇÃO: APLICAÇÃO DE PORTA INCORRETA, ACARRETA EM TRAVAMENTO DO SERVIDOR
        //SEM RESPOSTA OU RETORNO PARA O USUÁRIO
        break;
}

//EXTENÇÕES ACEITAS PARA ENVIO DE ARQUIVOS
$docPattern = array("image/jpeg","image/png","image/jpg","image/bmp","application/msword","application/pdf","application/zip","text/plain","application/vnd.openxmlformats-officedocument.wordprocessingml.document","application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application/excel");
define("default_document_patten",$docPattern);

//Nao alterar dependências
//================================//
require "Core/Definitions.php";
//=================================//
//Define informações de Pasta de Documentos
define("MAIN_PATH",__DIR__);
define("DOCFOLDER","documentos");
define("DOCPATH",MAIN_PATH.DS.DOCFOLDER.DS);
//=================================//
//Definições de Segurança
define("_noPassSecurity",true);
//=================================//
//AUTO LOAD MÓDULOS
$Kernel = new Kernel();
$Kernel->checkClientStatus();
$Kernel->setMainFolder();


//INSTANCIAS DE CLASSE VEM APOIS A INSTANCIA DO KERNEL
function _tr($module = null){

    return empty($module) ? \Modules\Language::getLanguage() : \Modules\Language::getLanguage()->$module;
}







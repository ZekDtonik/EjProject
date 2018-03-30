<?php
/**
 *    Created by DevWolf.
 *      Author: Kevin Marques
 *    Date: 18/12/2017 - 11:34
 */

/* DEFINIÇÕES DE CONEXÃO*/
/* ESTRITAMENTE NAO ALTERE AQUI*/
/* **RISCO DE EXPLOSÃO CATASTROFICA** SERIO!! */
define("__TYPE_DB",empty($typeDb) ?'': $typeDb);
define("__HOST__",empty($hostName) ? '' : $hostName);
define("__PORT__",empty($hostPort) ? '' : $hostPort);
define("__USER",empty($userName) ? '' : $userName);
define("__PSWD",empty($passwd) ? '' : $passwd);
define("__DBNAME",empty($dataBaseName) ? '' : $dataBaseName);
/** DEFINIÇÕES DE ENTRADA PADRÃO */
//define("DS",DIRECTORY_SEPARATOR);
define("DS","/");
define("__PATH",dirname(__DIR__));
define("__PATH_LANGUAGE",__PATH.DS."Core".DS."Language".DS);
define("_SESSION_EXPIRE_TIME",empty($sessionExpireTime) ? '': $sessionExpireTime);
/*--------------------------------*/
define("min_login_length",4);
define("max_login_length",12);
define("min_passwd_length",5);
define("max_passwd_length",25);
/*--------------------------------*/
/** NOME DAS TABELAS */
/*--------------------------------*/
define("__USERS","users");
define("__CATEGORY","category");
define("__MESSAGE","message");
define("__VIEWS",'views');
/*--------------------------------*/
/** NOME DAS SOBREPOSIÇÕES */
/*--------------------------------*/
define("__DEFAULT_INSTRUCTION_NAME","instruction");
define("__DEFAULT_LOGIN_NAME","authenticate");

/*------------------------------------------------*/
/** NOME DE PÁGINAS PADRÃO (NAO ADICIONAR AS BARRAS)*/
define("_index",DS);
define("_requisition","requisition");
define("_settings","Configuracoes");
define("_admin","Administracao");
define("_employee","Funcionario");
define("_category","Categoria");
define("_report","Relatorio");
define("_expired","Sessao-Expirada");
define("_people","Pessoal");
define("_remove","Remover");
define("_send","Enviar");
define("_messages","Mensagens");
define("_documents","Documentos");
define("_doc","Documento");
define("_msg","Mensagem");
define("_edit","Editar");
define("_register","Cadastro");
define("_make","Efetivar");
define("_main","Principal");
define("_list","Lista");
define("_logout","sair");
define("_download","Baixar");
define("_pdf","_pdf");
define("_chvwd","checkViewed");
/*--------------------------------*/
/** NOME DAS SESSOES */
/*--------------------------------*/
define("sigVar__","sign_status");
define("sigTime__","sign_time");
define("sigLvl__","sign_level");
define("sigEnr__","sign_enroller");
/*--------------------------------*/
/** GETTERS DE ENDEREÇO */
/*--------------------------------*/
define("_mainAction","mainAction");
define("_subAction","subAction");
define("_lastAction","lastAction");
define("_endAction","endAction");
//LINKA O MODULO KERNEL (NÃO INSTANCIADO AQUI)
require_once "Kernel.php";
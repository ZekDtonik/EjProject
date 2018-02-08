<?php
/**
 *    Created by DevWolf.
 *   Author: Kevin Marques
 *   Date: 18/12/2017 - 11:38
 */

namespace Modules;

use SimpleXMLElement;
class Language extends SimpleXMLElement{

    /* ATRIBUIÇÃO SIMPLES, NAO EXISTE FUNCÃO DE ALTERAÇÃO DINAMICA DE LINGUAGEM */
    /* O SISTEMA É SIMPLES, ESTOU MONTANDO ESTA ESTRUTURA POR QUE FICA INFINITAMENTE MAIS ORGANIZADO */
    /* todos os textos em um unco lugar.. manutenção nivel GOD*/
    public static function getLanguage(){
        try{
            $place = __PATH_LANGUAGE."pt-BR.xml";
            //error_reporting(0);
            @$returnAllData = @new SimpleXMLElement($place,0,true);

            //$default = simplexml_load_string($string);
            return $returnAllData;
        }
        catch (\Exception $e){
            echo " Erro na leitura da linha do arquivo XML ";
        }

    }


}


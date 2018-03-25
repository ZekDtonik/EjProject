<?php
/**
 *    Created by DevWolf.
 *   Author: Kevin Marques
 *   Date: 25/03/2018 - 15:47
 */

namespace Main;


class Assembly
{
    private static $Configs = [];
    private static $ConfigDefsDefault = [];
    public function __construct(){
       //this->

        self::$ConfigDefsDefault['noPassSecurity'] = 'false';
        self::$ConfigDefsDefault['username'] = 'root';
        self::$ConfigDefsDefault['password'] = 'root';
        self::$ConfigDefsDefault['hostname'] = '127.0.0.1';
        self::$ConfigDefsDefault['port'] = '3306';
        self::$ConfigDefsDefault['database'] = 'ejdb';
        self::$ConfigDefsDefault['typedb'] = 'MySql';
        self::$ConfigDefsDefault['sessionexpiretime'] = '15';
        self::$ConfigDefsDefault['logfile'] = 'system_log';

        self::getConfigFile(Assembly::$Configs);
    }

    public function setConfigFile($fileName){
        //$fileName ="Configs/Config.cfg";
        if(!file_exists($fileName)){
            $dirname = dirname($fileName);
            if(!is_dir($dirname)){
                mkdir($dirname,0755,true);
            }

            $fcreate = fopen($fileName,"w+",true);
            fclose($fcreate);
            $fMount = fopen($fileName,"a");
            $Comment = "##############################################################\r\n";
            $Comment.= "#          Arquivo padrão de configurações de Sistema        #\r\n";
            $Comment.= "#                Created by: Kevin Marques                   #\r\n";
            $Comment.= "#         Mantenha o padrão de definição de configuração     #\r\n";
            $Comment.= "# @Nomeclatura nomeDaConfiguração=ValorEspecificado          #\r\n";
            $Comment.= "# A má escrita deste arquivo acarretará na ñ funcionalidade  #\r\n";
            $Comment.= "# Do sistema. Os valores atribuidos são diretamente usados   #\r\n";
            $Comment.= "# na definição de funcinamento. CUIDADO!                     #\r\n";
            $Comment.= "# Caso não saiba o que definir, apague o arquivo config.cfg  #\r\n";
            $Comment.= "##############################################################\r\n";
            fwrite($fMount,$Comment);
            foreach (self::$ConfigDefsDefault as $varName => $value) {
                $StringStreamToSave = $varName." = ".$value."\r\n";
                fwrite($fMount,$StringStreamToSave);
            }
            fclose($fMount);
            self::createLog("File Configs Created",0);


        }
    }
    public function getConfigFile(&$arrayData){
        $fileName ="Configs/Config.cfg";
        if(file_exists($fileName)){
            $arraChumk = file($fileName,FILE_SKIP_EMPTY_LINES);
            $arrayFormatted = [];
            foreach ($arraChumk as $varValue ){
                if(preg_match("/^[a-z0-9_\-]+ ?= ?[a-z0-9.,]+/i",$varValue)){
                    $tempStreamIO = explode("=",trim($varValue));
                    $arrayFormatted[trim(strtolower($tempStreamIO[0]))] =trim($tempStreamIO[1]);
                }
            }
            $arrayData = $arrayFormatted;
        }
        else{
            self::setConfigFile($fileName);
            self::getConfigFile($arrayData);
        }
    }
    public static function cfg($cfgNameParameter){
        $cfgName = trim($cfgNameParameter);
        if(empty(self::$Configs[$cfgName])){
            if(empty(self::$ConfigDefsDefault[$cfgName])){
                exit("Nome de Configuração Definida não existe!");
            }
            else{
                self::createLog("Valor '".$cfgName."' não encontrada nas configurações! Valor legado utilizado. Verifique suas Definições no arquivo de configurações!",1);
                return self::$ConfigDefsDefault[$cfgName];
            }
        }
        else{
            return self::$Configs[$cfgName];
        }
    }
    public static function createLog($descrition,$tipo){
        $extension = ".log";
        //Setando versao de config diramente para evitar loop Infinito
        $varName = empty(self::$Configs['logfile']) ?self::$ConfigDefsDefault['logfile'] :self::$Configs['logfile'];
        $fileName = "logs/".$varName.$extension;

        $date = new \DateTime();
        $dt = $date->format("d/m/Y h:i");

        $preText = "INFO";
        if($tipo == 1)
            $preText = "AVISO";
        else if($tipo == 2)
            $preText = "ERRO";
        else if($tipo == 3)
            $preText = "FATAL";

        $StringToSave = $preText.": [".$dt."] - ".$descrition."\r\n";

        if(!file_exists($fileName)){
            $dirname = dirname($fileName);
            if(!is_dir($dirname)){
                mkdir($dirname,0755,true);
            }

            $fcreate = fopen($fileName,"w+",true);
            fclose($fcreate);
            $fMount = fopen($fileName,"a");
            $Comment = "##############################################################\r\n";
            $Comment.= "#          Arquivo de registro de eventos do Sistema         #\r\n";
            $Comment.= "#                Created by: Kevin Marques                   #\r\n";
            $Comment.= "##############################################################\r\n";
            fwrite($fMount,$Comment);
            fwrite($fMount,$StringToSave);
            fclose($fMount);
        }
        else{
            $fLogSave = fopen($fileName,"a+");
            fwrite($fLogSave,$StringToSave);
            fclose($fLogSave);
        }
    }
}
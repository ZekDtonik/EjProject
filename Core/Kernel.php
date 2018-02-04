<?php
/**
 *    Created by DevWolf.
 *      Author: Kevin Marques
 *    Date: 18/12/2017 - 11:34
 */

class Kernel
{
    public function __construct()
    {
        //REGISTRADORES DA STANDARD PHP LIBRARY
        spl_autoload_extensions(".php");
        spl_autoload_register('self::modules_load');
        //CHAMA FUNÇÃO QUE VERIFICA ESTADO DE LOGIN DO USUÁRIO

    }
    //Modulo sendo chamado pelo registrador de SPL
    private function modules_load($class){
        /**
         *  Nomeclatura de arquivos difere dos nomes das classes;
         * Emitindo Explode e puxando a ultima ocorrencia do retorno
         * do __autoload
         *
         * Litera uma barra e puxa o nome da classe
         * @var string $includeClass quebra a String emitido pelo __autoload em vetor
         * @var array $PageName pega o nome do arquivo
         *
         * @param require inclui a classe especificada pelo autoload
         *
         */
        $includeClass = explode("\\", $class);//
        $PageName = end($includeClass);
        /*  Requisitando todas as classes utilizadas pelo APP;
            Endereçamento de Raiz de Site definido anteriorimente
         */
        require_once(__DIR__. DIRECTORY_SEPARATOR . "Modules" . DIRECTORY_SEPARATOR . "Class." . $PageName . ".php");
    }
    public static function getPwdPattern($passwordUncrypted){
        return md5("devCrypt".$passwordUncrypted);
    }
    /** @method static void redirect
     *  Este métódo é utilizado para forçar o redirect de qualquer página
     * @param utiliza JAVASCRIPT
     */
    public static function redirect($var_place){
        echo "<script type='text/javascript'> window.location.href='".$var_place."'</script>";
    }
    public function checkClientStatus(){
        //Recupera o arquivo atual para verificar o estado cliente
        $streamFile = explode('/',$_SERVER['SCRIPT_FILENAME']);
        $setEndOfStream = end($streamFile);
        $getPlace = explode(".",$setEndOfStream)[0];

        switch($getPlace){
            //Observando Externos
            case 'index':
                Modules\Authenticate::checkSession('out');
                break;
            //Observando Internos
            default:
                Modules\Authenticate::checkSession();
                break;
        }


    }
}
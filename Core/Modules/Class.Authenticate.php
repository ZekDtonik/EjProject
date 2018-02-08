<?php
/**
 *    Created by DevWolf.
 *   Author: Kevin Marques
 *   Date: 19/12/2017 - 14:22
 */

namespace Modules;

use Exception;
use Modules\Connection;
class Authenticate
{
    private $Instruction;
    private $postData;
    private $message;
    private $sql;
    private $debug;
    private $userData;

    /** Setters & Getters */
    public function getInstruction(){return $this->Instruction;}
    public function getMessage(){return $this->message;}
    public function setInstruction($Instruction){$this->Instruction = $Instruction;}
    public function setMessage($message){$this->message = $message;}
    public function getUserData($value = null)
    {
        $return =null;
        if(empty($value)){
            $return = $this->userData;
        }
        else{
            if(!isset($this->userData[$value])){
                $return = null;
            }
            else{
                $return = $this->userData[$value];
            }
        }
        return $return;
    }
    public function setUserData($userData){$this->userData = $userData;}
    public function getPostData($value = null){
        $return =null;
        if(empty($value)){
            $return = $this->postData;
        }
        else{
            if(!isset($this->postData[$value])){
                $return = null;
            }
            else{
                $return = $this->postData[$value];
            }
        }
        return $return;
    }
    public function setPostData($postData){$this->postData = $postData;}
    public function getSql(){return $this->sql;}

    //Inicio
    public function __construct(){

        try{
            /**
            if((!isset($_SESSION['session_login']) || $_SESSION['session_login'] < time()) && !isset($_SESSION[sigVar__])){
                $_SESSION['session_login'] = time() + 60 * 2;
                \Kernel::redirect("/");
            }
             */
            //Define a instrução principal com o GET do usuario
            @$this->setInstruction($_GET[__DEFAULT_INSTRUCTION_NAME]);
            @$this->setPostData($_POST);
            $this->sql = new Connection();
            /** Aplica a tentativa de login apenas quando o get de authenticate é chamado
             * Lembre-se: Isso não é compilado, as informações são privados entre cliente-servidor
             * I/O nem sempre vem do mesmo lado
             * @param const __DEFAULT_AUTHENTICATE_NAME - Constante que define o nome da chamada padrão para login
             * está definido em Definitions.php
             */

            if($this->getInstruction() == __DEFAULT_LOGIN_NAME) {
                //Faz a chamada ao banco para recuperar os dados

                $query = $this->getSql()->prepare("SELECT * FROM ".__USERS." WHERE login=? AND senha=?");
                $query->bindValue(1,$this->getPostData('login'));
                $query->bindValue(2,\Kernel::getPwdPattern($this->getPostData('senha')));
                $query->execute();
                $this->setUserData($query->fetch(\PDO::FETCH_ASSOC));

                if (empty($this->getPostData('login')) || empty($this->getPostData('senha'))) {
                    $this->setMessage("<div class=\"alert alert-danger\" role=\"alert\">
                <strong>"._tr("Texts")->error."</strong> "._tr("Errors")->field_cannot_empty."</div>");
                }
                else if($query->rowCount() == 0){
                    $this->setMessage("<div class=\"alert alert-danger\" role=\"alert\">
                <strong>"._tr("Texts")->error."</strong> "._tr("Errors")->wrong_login."</div>");
                }
                else if($query->rowCount() != 0 && $this->getUserData('status') == 0){
                    $this->setMessage("<div class=\"alert alert-warning\" role=\"alert\">
                <strong>"._tr("Texts")->warning."</strong> "._tr("Infos")->blocked_account."</div>");
                }
                else{
                    $this->setMessage("<div class=\"alert alert-success\" role=\"alert\">
                <strong>"._tr("Texts")->welcome."</strong> "._tr("Infos")->success_login."</div>");
                    //cria a sessão
                    $this->applyLogin();
                }
            }
            else if($this->getInstruction() == _expired){
                //if(Authenticate::checkExpireLogin(true)){
                    $this->setMessage("<div class=\"alert alert-info\" role=\"alert\">
                <strong>"._tr("Texts")->warning."</strong> "._tr("Infos")->session_expired."</div>");
               // }
            }
        }
        catch(Exception $message){
            $this->setMessage($message->getMessage());
        }
    }
    public function showMessages(){
        echo $this->getMessage();
        //var_dump($this->debug);
    }
    public function applyLogin(){
        //cria a sessão para o usuário acessar a página
        $_SESSION[sigVar__] = $this->getPostData()['login'];
        $_SESSION[sigTime__] = time() + 60 * _SESSION_EXPIRE_TIME;
        $_SESSION[sigLvl__] = $this->getUserData('tipo');
        if($this->getUserData('tipo') != 1){
            $_SESSION[sigEnr__] = $this->getUserData('matricula');
        }
        //detecta, qual o nivel de usuário e redireciona
        if($_SESSION[sigLvl__] == 1){
            \Kernel::redirect(DS._index.DS._admin);
        }
        else{
            \Kernel::redirect(DS._index.DS._employee);
        }
    }
    public static function logout($redirect = false){
        unset($_SESSION[sigVar__]);
        unset($_SESSION[sigTime__]);
        if($_SESSION[sigLvl__] != 1){
            unset($_SESSION[sigEnr__]);
        }
        unset($_SESSION[sigLvl__]);

        if($redirect){
            \Kernel::redirect(DS._index);
        }
    }
    public static function checkSession($location = 'inner',$filename = null){

        if(isset($_SESSION[sigVar__]) == false){
            switch ($location){
                case 'out':
                    return null;
                    break;
                default:
                    \Kernel::redirect(DS._index);
                    break;
            }
        }
        //se existe sessao, contudo o tempo util expirou...
        else if(isset($_SESSION[sigVar__]) && !Authenticate::checkExpireLogin(true)){
            //quebra o restante da sessao
            self::logout();
            switch ($location){
                //continua na pagina externa
                case 'out':
                    return null;
                    break;
                //
                default:
                    \Kernel::redirect(_index.DS._expired);
                    break;
            }
        }
        else{
            switch ($location){
                case 'out':
                    //detecta, qual o nivel de usuário
                    if($_SESSION[sigLvl__] == 1){
                        \Kernel::redirect(DS._admin);
                    }
                    else{
                       \Kernel::redirect(DS._employee);
                    }
                    break;
                default:
                    //Recupera novamente
                    //verificar posição e alterar conforme nivel do usuário
                    if($_SESSION[sigLvl__] == 1 && $filename == 'funcionario'){
                        \Kernel::redirect(DS.DS._admin);
                    }
                    else if($_SESSION[sigLvl__] != 1 && $filename == 'admin'){
                        \Kernel::redirect(DS.DS._employee);
                    }

                    break;
            }
        }
    }

    public static function checkExpireLogin($bool = false){
        //Se o tempo atual for maior que o tempo limite
        if(time() > @$_SESSION[sigTime__]){
            //sessão expirou return false
            if(!$bool){
                \Kernel::redirect(_index.DS._expired);
            }
            else{
                return false;
            }
        }
        else{
            $_SESSION[sigTime__] = time() + 60 * _SESSION_EXPIRE_TIME;
            return true;
        }
    }
}
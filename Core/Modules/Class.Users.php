<?php
/**
 *    Created by DevWolf.
 *      Author: Kevin Marques
 *    Date: 18/12/2017 - 11:34
 */

namespace Modules;

use Kernel;
use PDO;

class Users
{
    private $name;
    private $email;
    private $login;
    private $passwd;
    private $type;
    private $status;
    private $matriculation;
    private $account;
    private $bank;
    private $agency;
    private $operation;
    private $ctps;
    private $cpf;
    private $rg;
    private $phone;
    private $phoneAlt;
    private $avatar;
    private $sql;

    public function getAgency(){return $this->agency;}
    public function getName(){return $this->name;}
    public function getBank(){return $this->bank;}
    public function getCtps(){return $this->ctps;}
    public function getEmail(){return $this->email;}
    public function getMatriculation(){return $this->matriculation;}
    public function getOperation(){return $this->operation;}
    public function getPasswd(){return $this->passwd;}
    public function getStatus(){return $this->status;}
    public function getType(){return $this->type;}
    public function getAccount(){return $this->account;}
    public function getCpf(){return $this->cpf;}
    public function getRg(){return $this->rg;}
    public function getLogin(){return $this->login;}
    public function getPhone(){return $this->phone;}
    public function getPhoneAlt(){return $this->phoneAlt;}
    public function getAvatar(){return $this->avatar;}

    //CONSTRUTOR
    public function __construct()
    {
        //Atribuição unica, encapsulamento apenas de retorno!!! (NO SET)
        $this->name = empty($_POST["nome"]) ? null : $_POST["nome"];
        $this->matriculation = empty($_POST["matricula"]) ? null : $_POST["matricula"];
        $this->email = empty($_POST["email"]) ? null : strtolower($_POST["email"]);
        $this->bank = empty($_POST["banco"]) ? null : $_POST["banco"];
        $this->agency = empty($_POST["agencia"]) ? null : $_POST["agencia"];
        $this->account = empty($_POST["conta"]) ? null : $_POST["conta"];
        $this->operation = empty($_POST["operacao"]) ? null : $_POST["operacao"];
        $this->cpf = empty($_POST["cpf"]) ? null : preg_replace("/[^0-9]/",'',$_POST["cpf"]);
        $this->rg = empty($_POST["rg"]) ? null : $_POST["rg"];
        $this->ctps = empty($_POST["ctps"]) ? null : $_POST["ctps"];
        $this->login = empty($_POST["login"]) ? null : strtolower($_POST["login"]);
        $this->passwd = empty($_POST["senha"]) ? null : $_POST["senha"];
        $this->phone = empty($_POST["tel1"]) ? null : $_POST["tel1"];
        $this->phoneAlt = empty($_POST["tel2"]) ? null : $_POST["tel2"];
        $this->phoneAlt = empty($_FILES["avatar"]) ? null : $_FILES["avatar"];
        $this->sql = new Connection();
    }
    //==============================
    //FRONT-END Metodos
    //==============================
    /** @method void - Mostra o cabeçalho do sistema
     * Informações do usuário incluido
     */
    public function ui_header()
    {
        echo "<div></div>
    <div class=\"d-flex justify-content-between\" style=\"background-color:rgb(30,40,51);height:65px;padding-top:0px;padding-bottom:0px;\">
        <div class=\"container d-flex justify-content-between align-items-start\">
            <div class=\"row d-flex justify-content-between\" style=\"width:100%;height:100%;\">
                <div class=\"col-2 offset-0\" style=\"height:100%;padding-top:0px;\">
                    <div style=\"height:100%;background-color:#ffffff;\">
                        <img style=\"height:100%;\">
                    </div>
                </div>
                <div class=\"col-sm-5 col-md-7 col-xl-8 offset-0\" style=\"height:100%;padding-top:10px;padding-right:0px;\">". $this->showInfoUser()."
                </div>
                <div class=\"col-1\" style=\"height:100%;padding-top:15px;padding-left:0px;padding-right:0px;\">
                   <a  href='".DS._admin.DS._logout."'> <button class=\"btn btn-primary float-right align-self-center\" type=\"button\" style=\"width:90px;height:30;background-color:#728EFD;\">"._tr("Texts")->exit." </button></a>
                </div>
            </div>
        </div>
    </div>";
        if($_SESSION[sigLvl__] == 1){
            echo "
    <nav style=\"height:40px;background-color:#007bff;\">
        <div class=\"container\" style=\"height:100%;\">
            <div class=\"row\">
                <div class=\"col\">
                    <div class=\"btn-group d-flex justify-content-center\" role=\"group\" style=\"width:100%;\">
                        <div class=\"dropdown btn-group\" role=\"group\">
                            <button class=\"btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"false\" type=\"button\"
                                    style=\"width:140px;\">"._tr("Texts")->employee." </button>
                            <div class=\"dropdown-menu\" role=\"menu\">
                                <a class=\"dropdown-item\" role=\"presentation\" href=\"".DS._admin.DS._employee.DS._register."\">"._tr("Texts")->register."</a>
                                <a class=\"dropdown-item\" role=\"presentation\" href=\"".DS._admin.DS._employee."\">"._tr("Texts")->employees."</a>
                            </div>
                        </div>
                        <a href=\"".DS._admin.DS._category."\"><button class=\"btn btn-primary\" type=\"button\" style=\"width:140px;\">"._tr("Texts")->category."</button></a>
                        <div class=\"dropdown btn-group\" role=\"group\">
                            <button class=\"btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"false\" type=\"button\"
                                    style=\"width:140px;\">"._tr("Texts")->send." </button>
                            <div class=\"dropdown-menu\" role=\"menu\">
                                <a class=\"dropdown-item\" role=\"presentation\" href=\"".DS._admin.DS._send.DS._msg."\">"._tr("Texts")->message."</a>
                                <a class=\"dropdown-item\" role=\"presentation\" href=\"".DS._admin.DS._send.DS._doc."\">"._tr("Texts")->document."</a>
                            </div>
                        </div>
                        <a href=\"relatorio.html\"><button class=\"btn btn-primary\" type=\"button\" style=\"width:140px;\">"._tr("Texts")->report." </button></a>
                    </div>
                </div>
            </div>
        </div>
    </nav>";
        }
        else{
            echo "<nav style=\"height:40px;background-color:#007bff;\">
        <div class=\"container\" style=\"height:100%;\">
            <div class=\"row\">
                <div class=\"col\">
                    <div class=\"btn-group d-flex justify-content-center\" role=\"group\" style=\"width:100%;\"> 
                    <a href='".DS._employee.DS._messages."'><button class=\"btn btn-primary\" type=\"button\" style=\"width:140px;\">"._tr("Texts")->messages." </button></a> 
                    <a href='".DS._employee.DS._documents."'><button class=\"btn btn-primary\" type=\"button\" style=\"width:140px;\">"._tr("Texts")->documents." </button></div></a>
                </div>
            </div>
        </div>
    </nav>";
        }

    }
    public function ui_back($place){
        echo"<article style=\"margin-top:5px;margin-bottom:15px;\">
        <div class=\"container\" style=\"margin-top:17px;\">
            <a href=\"".$place."\">
                <button class=\"btn btn-light\" type=\"button\" style=\"font-size:22px;\">
                    <i class=\"fa fa-arrow-left\" style=\"color:rgb(132,132,132);\"></i>
                </button>
            </a>
        </div>
    </article>";
    }
    //==============================
    //FRONT-END Metodos
    //==============================

    public function showInfoUser()
    {
        //Seleciona as informações de usuário recuperando via servidor
        $getInfo = $this->getSql()->query("SELECT * FROM " . __USERS . " WHERE login=\"" . $_SESSION[sigVar__] . "\"");
        $giveIn = $getInfo->fetch(\PDO::FETCH_ASSOC);
        $name = $giveIn['nome'];
        return "<h6 class=\"text-right\" style=\"color:rgb(255,255,255);\">$name</h6>
              <h6 class=\"text-right\" style=\"color:rgb(255,255,255);font-family:'Noto Sans', sans-serif;\">" . date("d/m/Y") . "</h6>";
    }
    public function getSql()
    {
        return $this->sql;
    }
    public function visualizarMsg()
    {

    }
    public function downloadSystem($msgId){
        if(empty($msgId)){
            return "Message ID not found, Aborted!";
        }
        else{
            //busca o identificador de mensagem
            $getMessages = $this->getSql()->query("SELECT * FROM ".__MESSAGE." WHERE mid=\"$msgId\" ");
            $getFetchMsg = $getMessages->fetch(PDO::FETCH_ASSOC);
            //para fazer o download é necessário estar logado!
            $getAllusers = json_decode($StreamFetchSource['lista_matricula'],true);
        }
    }

}
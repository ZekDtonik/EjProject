<?php
/**
 *    Created by DevWolf.
 *      Author: Kevin Marques
 *    Date: 18/12/2017 - 11:34
 */

namespace Modules;

use Classes\Auth;
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
    private $showMessage;
    public function setShowMessage($showMessage){$this->showMessage = $showMessage;}
    public function showMessage($return = false){
        if ($return){
            return $this->showMessage;
        }
        else {
            echo $this->showMessage;
        }
    }
    //

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
        $this->name = empty($_POST["nome"]) ? null : $_POST["nome"];//VV
        $this->matriculation = empty($_POST["matricula"]) ? null : $_POST["matricula"];//VV
        $this->email = empty($_POST["email"]) ? null : strtolower($_POST["email"]);//VV
        $this->bank = empty($_POST["banco"]) ? null : $_POST["banco"];//VV
        $this->agency = empty($_POST["agencia"]) ? null : $_POST["agencia"];//VV
        $this->account = empty($_POST["conta"]) ? null : $_POST["conta"];//VV
        $this->operation = empty($_POST["op"]) ? null : $_POST["op"];//VV
        $this->cpf = empty($_POST["cpf"]) ? null : preg_replace("/[^0-9]/",'',$_POST["cpf"]);//VV
        $this->rg = empty($_POST["rg"]) ? null : $_POST["rg"];//VV
        $this->ctps = empty($_POST["ctps"]) ? null : $_POST["ctps"];//VV
        $this->login = empty($_POST["login"]) ? null : strtolower($_POST["login"]);//VV
        $this->passwd = empty($_POST["senha"]) ? null : $_POST["senha"];//VV
        $this->phone = empty($_POST["tel1"]) ? null : $_POST["tel1"];//VV
        $this->phoneAlt = empty($_POST["tel2"]) ? null : $_POST["tel2"];//VV
        $this->avatar = empty($_FILES["avatar"]) ? null : $_FILES["avatar"];
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
    <div class=\"d-flex justify-content-between\" style=\"background-color:rgb(30,40,51);height:65px;padding-top:0;padding-bottom:0;\">
        <div class=\"container d-flex justify-content-between align-items-start\">
            <div class=\"row d-flex justify-content-between\" style=\"width:100%;height:100%;\">
                <div class=\"col-md-1 col-sm-12 col-xl-1 justify-content-end align-content-end\" style=\"height:100%;padding-top:0;\">
                    <div>
                        <img src=\"/assets/img/logo.png\" style=\"height:60px; padding-top:5px\">
                    </div>
                </div>
                <div class=\"col-md-9 col-sm-12 col-xl-9 align-items-end\" style=\"height:100%;padding-top:10px;padding-right:0;\">". $this->showInfoUser()."
                </div>
                <div class=\"col-md-2 col-sm-12 col-xl-2 justify-content-end\" style=\"height:100%;padding:15px;\">
                <a  href='".($_SESSION[sigLvl__] == 1 ? DS._admin.DS._settings : DS._employee.DS._settings)."'> <button class=\"btn btn-success float-left align-self-center fa fa-cog\" type=\"button\" style=\"line-height: 1.5;color:#fff;width:50px;\" title='"._tr("Texts")->config."'></button></a>
                   <a  href='".DS._admin.DS._logout."'> <button class=\"btn btn-primary float-right align-self-center\" type=\"button\" style=\"width:100px;background-color:#728EFD;\">"._tr("Texts")->exit." </button></a>
                </div>
            </div>
        </div>
    </div>";
        if($_SESSION[sigLvl__] == 1){
            echo "
    <nav style=\"min-height:40px; height:auto;background-color:#007bff;\">
        <div class=\"container\" style=\"height:100%;\">
        <div class=\"row\">
                <div class=\"btn-group d-flex justify-content-around flex-wrap\" role=\"group\" style=\"width:100%;\">
                    <div class=\"dropdown btn-group col-md-4 col-sm-6 col-xl-3\" role=\"group\">
                        <button class=\"btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"false\" type=\"button\">"._tr("Texts")->employee."  </button>
                        <div class=\"dropdown-menu\" role=\"menu\">
                             <a class=\"dropdown-item\" role=\"presentation\" href=\"".DS._admin.DS._employee.DS._register."\">"._tr("Texts")->register."</a>
                                <a class=\"dropdown-item\" role=\"presentation\" href=\"".DS._admin.DS._employee."\">"._tr("Texts")->employees."</a>
                        </div>
                    </div>
                    <div class=\"col-md-4 col-sm-6 col-xl-3\">
                         <a href=\"".DS._admin.DS._category."\">
                             <button class=\"btn btn-primary\" type=\"button\">"._tr("Texts")->category."</button>
                             </a>
                    </div>

                    <div class=\"col-md-4 col-sm-6 col-xl-3 dropdown btn-group\" role=\"group\">
                        <button class=\"btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"false\" type=\"button\">"._tr("Texts")->send." </button>
                        <div class=\"dropdown-menu\" role=\"menu\">
                            <a class=\"dropdown-item\" role=\"presentation\" href=\"".DS._admin.DS._send.DS._msg."\">"._tr("Texts")->message."</a>
                            <a class=\"dropdown-item\" role=\"presentation\" href=\"".DS._admin.DS._send.DS._doc."\">"._tr("Texts")->document."</a>
                        </div>
                    </div>
                    <div class=\"col-md-4 col-sm-6 col-xl-3\">
                         <a href=\"".DS._admin.DS._report."\"> 
                              <button class=\"btn btn-primary\" aria-expanded=\"false\" type=\"button\">"._tr("Texts")->report."</button>                         
                         </a>
                        
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
                <button class=\"btn btn-light\" type=\"button\" style=\"width:60px;font-size:22px;\">
                    <i class=\"fa fa-arrow-left\" style=\"color:rgb(132,132,132);\"></i>
                </button>
            </a>
        </div>
    </article>";
    }
    public function ui_settings(){
        $getInfo = $this->getSql()->query("SELECT * FROM " . __USERS . " WHERE matricula=\"" . $_SESSION[sigEnr__] . "\"");
        $giveIn = $getInfo->fetch(\PDO::FETCH_ASSOC);
        $login = $giveIn['login'];
        $name = $giveIn['nome'];
        $senha = $giveIn['senha'];
        echo "
    <article class=\"d-flex justify-content-center\" style=\"height:50px;background-color:#152462;padding-top:9px;\">
        <h4 style=\"color:rgb(255,255,255);letter-spacing:2px;font-weight:bold;padding-top:-12px;\">"._tr("Texts")->settings."</h4>
    </article>";
        self::showMessage();
        echo"
    <article style=\"margin-top:5px;margin-bottom:15px;\">
        <div class=\"container\">";
        if($_SESSION[sigLvl__] == 1){
            self::ui_back(DS._admin.DS);
        }
        else{
            self::ui_back(DS._employee.DS);
        }

        echo"</div>
    </article>";
        echo"
        <article style=\"margin-top:10px;height:auto;\">
        <div class=\"container\" style=\"height:auto;\">
            <div class=\"row\" style=\"height:auto;\">
                <div class=\"col\"></div>
                <div class=\"col\">
                    <form action='".($_SESSION[sigLvl__] == 1 ? DS._admin.DS._settings.DS._make : DS._employee.DS._settings.DS._make)."' class=\"d-flex justify-content-between flex-wrap\" enctype='multipart/form-data' method=\"post\" style=\"width:700px;\">";
                    if($_SESSION[sigLvl__] == 1){
                        echo"    <div class=\"form-group\" style=\"width:45.5%;\">
                            <label>"._tr("Texts")->login.": </label>
                            <input class=\"form-control\" type=\"text\" name='login' value='".$login."'>
                        </div> ";
                        echo"    <div class=\"form-group\" style=\"width:45.5%;\">
                            <label>"._tr("Texts")->name.": </label>
                            <input class=\"form-control\" type=\"text\" name='nome' value='".$name."'>
                        </div> ";
                        echo"<div class=\"form-group\" style=\"width:45.5%;\">
                            <label>"._tr("Texts")->password.": </label>
                            <input class=\"form-control\" type=\"text\" name='senha' value='".$senha."'>
                        </div>";
                    }
                    if($_SESSION[sigLvl__] == 2){
                       echo " 
                       <div class=\"form-group\" style=\"width:45.5%;\">
                            <label>"._tr("Texts")->password.": </label>
                            <input class=\"form-control\" type=\"password\" name='senha'>
                       </div>
                       <div class=\"form-group\" style=\"width:45.5%;\">
                            <label>"._tr("Texts")->repeat_password.": </label>
                            <input class=\"form-control\" type=\"password\" name='titulo'>
                       </div>";
                    }
                        echo "<div class=\"btn-group d-flex justify-content-around\" role=\"group\" style=\"width:100%;margin-top:30px;padding-right:100px;padding-left:100px;\">
                            <button class=\"btn btn-primary\" type=\"reset\" style=\"width:160px;height:40px;background-color:#728EFD;color:rgb(255,255,255);\">".strtoupper(_tr("Texts")->clear)." </button>
                            <button class=\"btn btn-primary\" type=\"submit\" style=\"width:160px;height:40px;background-color:#13297D;\">".strtoupper(_tr("Texts")->save)." </button>
                        </div>
                    </form>
                </div>
                <div class=\"col\"></div>
            </div>
        </div>
    </article>";
    }
    //==============================
    //FRONT-END Metodos
    //==============================
    public function settings(){
        $getLoginNames = $this->getSql()->query("SELECT nome FROM ".__USERS);
        $singleArrayOfData = $getLoginNames->fetchAll(PDO::FETCH_ASSOC);
        $getData = $this->getSql()->query("SELECT nome FROM ".__USERS." WHERE login='".$_SESSION[sigVar__]."'");
        $data = $getData->fetch(PDO::FETCH_ASSOC);

        #Nenhum campo pode ficar em branco.
        if(empty($this->getName()) || empty($this->getLogin()) || empty($this->getPasswd()) ){
            $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->all_fields_need_to_be_filled."</div>");
        }
        #Caracteres inseridos no campo nome não são válidos!
        else if(Auth::isNames($this->getName())==false){
            $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->characters_in_field_name_invalid."</div>");
        }
        #Caracteres inseridos no campo de login são inválidos
        else if(Auth::isLogin($this->getLogin()) == false){
            $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->login_name_invalid." </div>");
        }
        #tamanho de nome de login
        else if(strlen($this->getLogin()) < min_login_length || strlen($this->getLogin()) > max_login_length){
            $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> ".sprintf(_tr("Errors")->login_name_must_be_between,min_login_length,max_login_length)."</div>");
        }
        #Login inserido ja existe no banco
        else if(in_array($this->getLogin(),$singleArrayOfData) && $this->getLogin() != $data['login']){
            $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->login_name_already_exist."1</div>");
        }
        #verifica o tamanho da senha
        else if(strlen($this->getPasswd()) < min_passwd_length || strlen($this->getPasswd()) > max_passwd_length){
            $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->password_length_must_be_between."</div>");
        }
        else{
            $updateValues = $this->getSql()->prepare("UPDATE ".__USERS." SET nome=:nome,login=:login,senha=:senha WHERE matricula=:matricula");
            $updateValues->bindValue(":nome",$this->getName());
            $updateValues->bindValue(":login",$this->getLogin());
            $updateValues->bindValue(":senha",$this->getPasswd());
            $updateValues->bindValue(":matricula",$_SESSION[sigEnr__]);

            $updateValues->execute();
            if($updateValues){
                $this->setShowMessage("<div class='alert alert-success' role='alert'><strong>"._tr("Texts")->success."</strong> "._tr("Infos")->user_data_successful_edited."</div>");
            }
            else{
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Infos")->error_on_procedure."</div>");
            }
        }
    }
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
            if($getMessages->rowCount() != 0){
                //para fazer o download é necessário estar logado!
                $getUser = $getFetchMsg['lista_matricula'];
                //verifico se tem login

                if(empty($_SESSION[sigVar__])){
                    return "<div class='alert alert-warning' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Infos")->user_not_logged."</div>";
                }
                else if(!empty($_SESSION[sigVar__]) && $_SESSION[sigEnr__] != $getUser && !empty($_SESSION[sigVar__]) && $_SESSION[sigLvl__] != 1 ){
                    return "<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Infos")->you_not_have_auth_to_access_this_file."</div>";
                }
                else {
                    //Informaçoes do arquivo
                    $nomearquivo =$getFetchMsg['documento'];
                    $caminho_arquivo = DOCPATH.$nomearquivo;
                    //realiza o procedimento de download


                    if(!file_exists($caminho_arquivo)){
                        return "<div class='alert alert-warning' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Infos")->file_not_found."</div>";
                    }
                    else{
                        header('Content-Description: File Transfer');
                        header('Content-Disposition: attachment; filename="'.$nomearquivo.'"');
                        header('Content-Type: application/octet-stream');
                        header('Content-Transfer-Encoding: binary');
                        header('Content-Length: ' . filesize($caminho_arquivo));
                        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                        header('Pragma: public');
                        header('Expires: 0');
                        $downloadfile = file_get_contents($caminho_arquivo);

                        if($downloadfile){
                            $checkSql = $this->getSql()->query("SELECT * FROM ".__VIEWS." WHERE mid_ref='".$msgId."'");
                            $date = new \DateTime('now',new \DateTimeZone("America/Sao_Paulo"));
                            if($checkSql->rowCount() == 0){

                                $insertView = $this->getSql()->prepare("INSERT INTO ".__VIEWS." (matricula, dateDownload, mid_ref,download) VALUES (?,?,?,?)");
                                $insertView->bindValue(1,$getUser);
                                $insertView->bindValue(2,$date->format("Y-m-d H:i:s"));
                                $insertView->bindValue(3,$msgId);
                                $insertView->bindValue(4,1);
                                $insertView->execute();
                            }
                            else{
                                $ch = $checkSql->fetch(PDO::FETCH_ASSOC);
                                $downloadCount = $ch['download'];
                                $downloadCount += 1;
                                $insertView = $this->getSql()->prepare("UPDATE ".__VIEWS." SET download=:dCount, dateDownload=:dates WHERE mid_ref=:mid_ref");
                                $insertView->bindValue(":dCount",$downloadCount);
                                if($ch['dateDownload'] == null)
                                    $insertView->bindValue(":dates",$date->format("Y-m-d H:i:s"));
                                else
                                    $insertView->bindValue(":dates",$ch['dateDownload']);
                                $insertView->bindValue(":mid_ref",$msgId);
                                $insertView->execute();
                            }
                        }

                    }

                }
            }

        }
    }
    public function checkView($mid_ref){
        $checkIsExist = $this->getSql()->query("SELECT * FROM ".__VIEWS." WHERE mid_ref='".$mid_ref."'");
        $date = new \DateTime('now',new \DateTimeZone("America/Sao_Paulo"));

        if($checkIsExist->rowCount() == 0){
            $insertView = $this->getSql()->prepare("INSERT INTO ".__VIEWS." (matricula, dateViewed, mid_ref) VALUES (:matricula,:dates,:mid)");
            $insertView->bindValue(":dates",$date->format("Y-m-d H:i:s"));
        }
        else{
            $insertView = $this->getSql()->prepare("UPDATE ".__VIEWS." SET matricula=:matricula, dateViewed=:dates, mid_ref=:mid WHERE  mid_ref='".$mid_ref."'");
            $getRst= $checkIsExist->fetch(PDO::FETCH_ASSOC);
            if($getRst['dateViewed'] == null){
                $insertView->bindValue(":dates",$date->format("Y-m-d H:i:s"));
            }
        }
            $insertView->bindValue(":matricula",$_SESSION[sigEnr__]);
            $insertView->bindValue(":mid",$mid_ref);
            $insertView->execute();




    }

}
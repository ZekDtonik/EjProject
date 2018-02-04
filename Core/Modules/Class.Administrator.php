<?php
/**
 *    Created by DevWolf.
 *      Author: Kevin Marques
 *    Date: 18/12/2017 - 11:34
 */

namespace Modules;

use Classes\Auth;
use Classes\Pagination;
use Classes\System;
use PDO;
class Administrator extends Users
{
    private $showMessage;
    public function setShowMessage($showMessage)
    {
        $this->showMessage = $showMessage;
    }
    public function showMessage($return = false){
        if ($return){
            return $this->showMessage;
        }
        else {
            echo $this->showMessage;
        }

    }

    //==============================
    //BACK-END Metodos
    //==============================
    /** */
    public function cadastrarFuncionario(){
        //insere informações
        //Definindo variaveis de POSTDATA
        try{
            //manipulação de eventos basicos
            $StreamVerifyQuery = $this->getSql()->query("SELECT login, email,matricula,cpf FROM ".__USERS);
            $StreamGetArrayFetch = $StreamVerifyQuery->fetchAll(PDO::FETCH_ASSOC);
            $patternConcatOfStream = "";
            foreach($StreamGetArrayFetch as $key => $mainBreak){
                foreach($mainBreak as $joinPoint){
                    $joinPointFormated = strtolower($joinPoint);
                    $patternConcatOfStream .= $joinPointFormated."-";
                }
            }
            $singleArrayOfData = explode("-",$patternConcatOfStream);
            #Nenhum campo pode ficar em branco.
            if(empty($this->getName()) || empty($this->getLogin()) || empty($this->getPasswd()) || empty($this->getEmail()) || empty($this->getAccount()) || empty($this->getAgency()) || empty($this->getBank()) || empty($this->getMatriculation())|| empty($this->getOperation()) || empty($this->getCtps()) || empty($this->getCpf()) || empty($this->getRg())){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->all_fields_need_to_be_filled."</div>");
            }
            #Caracteres inseridos no campo nome não são válidos!
            else if(Auth::isNames($this->getName())==false){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->characters_in_field_name_invalid."</div>");
            }
            #Identificador de matricula ja existe
            else if(in_array($this->getMatriculation(),$singleArrayOfData)){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->matriculation_id_already_exist." </div>");
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
            else if(in_array($this->getLogin(),$singleArrayOfData)){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->login_name_already_exist."</div>");
            }
            #Email inserido inválido
            else if(Auth::isEmail($this->getEmail()) == false){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->email_name_invalid."</div>");
            }
            #email inserido ja existe no banco
            else if(in_array($this->getEmail(),$singleArrayOfData)){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->email_already_exist."</div>");
            }
            #Validaçao de CPF
            else if(Auth::isCpf($this->getCpf()) == false){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->cpf_number_invalid."</div>");

            }
            else if(in_array($this->getCpf(),$singleArrayOfData)){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->cpf_already_exist."</div>");
            }
            #RG aceita apenas caracteres numericos
            else if(!is_numeric($this->getRg())){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->rg_field_only_accept_numbers."</div>");
            }
            #verifica o tamanho da senha
            else if(strlen($this->getPasswd()) < min_passwd_length || strlen($this->getPasswd()) > max_passwd_length){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->password_length_must_be_between."</div>");
            }
            #cpts só aceita caracteres numericos
            else if(strlen($this->getPasswd()) < min_passwd_length || strlen($this->getPasswd()) > max_passwd_length){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->ctps_field_only_accept_numbers."</div>");
            }
            else if(Auth::isPhone($this->getPhone())){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->phone_invalid."</div>");
            }
            else if(!empty($this->getPhoneAlt()) && Auth::isPhone($this->getPhoneAlt())){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->alternative_phone_invalid."</div>");
            }
            else{
                //15 entradas, sendo 2 opcionais (telefone 2, campo de arquivo de foto)
                $insertNewData = $this->getSql()->prepare("INSERT INTO ".__USERS." (nome, matricula,senha,email,rg,cpf,login,operacao,banco,agencia,conta,tel1,tel2,ctps,tipo,`status`,avatar) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $insertNewData->bindValue(1, $this->getName());
                $insertNewData->bindValue(2, $this->getMatriculation());
                $insertNewData->bindValue(3,$this->getPasswd() );
                $insertNewData->bindValue(4,$this->getEmail() );
                $insertNewData->bindValue(5,$this->getRg() );
                $insertNewData->bindValue(6,$this->getCpf() );
                $insertNewData->bindValue(7, $this->getLogin());
                $insertNewData->bindValue(8, $this->getOperation());
                $insertNewData->bindValue(9, $this->getBank());
                $insertNewData->bindValue(10, $this->getAgency());
                $insertNewData->bindValue(11,$this->getAccount() );
                $insertNewData->bindValue(12,$this->getPhone() );
                $insertNewData->bindValue(13, $this->getPhoneAlt());
                $insertNewData->bindValue(14, $this->getCtps());
                $insertNewData->bindValue(15, 2);//tipo de usuário
                $insertNewData->bindValue(16, 1);//tipo de usuário
                $insertNewData->bindValue(17, $this->getAvatar());//tipo de usuário
                $insertNewData->execute();

                if($insertNewData){
                    $this->setShowMessage("<div class='alert alert-success' role='alert'><strong>"._tr("Texts")->success."</strong> "._tr("Infos")->employee_successful_added."</div>");
                }
                else{
                    $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Infos")->error_on_procedure."</div>");
                }
            }
        }
        catch (\Exception $e){

            $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->fatal."</strong>"._tr("Infos")->internal_server_error." "._tr("Texts")->code.": ".$e->getCode()."</div>");
        }

    }
    public function visualizarFuncioarios(){

        try{
            $Pagination = new Pagination(__USERS,"id",$_GET[_subAction]);
            $mainStreamReaderQuery = $this->getSql()->query("SELECT * FROM ".__USERS." WHERE tipo='2' ".$Pagination->getProcessLimit());
            while($StreamHub = $mainStreamReaderQuery->fetch(PDO::FETCH_ASSOC)){
                echo "<div class=\"col-3\" style=\"margin-bottom:30px;\">
                    <div class=\"d-flex justify-content-center\">
                        <img src=\"/assets/img/luciano_cartaxo.jpg\" width=\"80%\" height=\"150px\">
                    </div>
                    <div class=\"nome_func\" style=\"margin-top:18px;\">
                        <h4 class=\"text-center\">".$StreamHub['nome']."</h4>
                    </div>
                    <div class=\"d-flex flex-column align-items-center\">
                        <h5 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->bank.": ".$StreamHub['banco']."</h5>
                        <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->account.": ".$StreamHub['conta']."</h6>
                        <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->agency.": ".$StreamHub['agencia']."</h6>
                        <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->email.": ".$StreamHub['email']."</h6>
                        <a href=\"".DS._admin.DS._employee.DS._people.DS.$StreamHub['matricula']."\" style=\"margin-top:14px;\">
                            <button class=\"btn btn-primary\" type=\"button\">"._tr("Texts")->more_info."</button>
                        </a>
                    </div>
                </div>";
            }
            $Pagination->getPagination(DS._admin.DS._employee);
        }
        catch (\Exception $e){
            $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->fatal."</strong>"._tr("Infos")->internal_server_error." "._tr("Texts")->code.": ".$e->getCode()."</div>");
        }
    }
    public function cadastrarCategoria(){
        try{
            $StreamCategoryQuery = $this->getSql()->query("SELECT * FROM ".__CATEGORY);
            $StreamFetch = $StreamCategoryQuery->fetchAll(PDO::FETCH_ASSOC);
            $singleStreamOfDataStriped = "";
            foreach ($StreamFetch as $key => $value){
                foreach ($value as $pair){
                    $strip = strtolower(trim(Auth::removeSpecialCharacters($pair)));
                    $singleStreamOfDataStriped .= "-".$strip;
                }
            }
            $monoArrayOfCategory = explode("-",$singleStreamOfDataStriped);

            if(empty($this->getName())){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong>"._tr("Errors")->field_cannot_be_empty."</div>");
            }
            else if(Auth::isNames($this->getName()) == false ){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong>"._tr("Errors")->category_name_invalid."</div>");
            }
            else if(in_array(strtolower(trim(Auth::removeSpecialCharacters($this->getName()))),$monoArrayOfCategory)){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong>"._tr("Errors")->category_already_exist."</div>");
            }
            else{
                //cria uma cor aleatoria
                $color = Auth::randomColor();

                $insertCategory = $this->getSql()->prepare("INSERT INTO ".__CATEGORY." (nome, cor) VALUES (?,?) ");
                $insertCategory->bindValue(1,$this->getName());
                $insertCategory->bindValue(2,$color);
                $insertCategory->execute();

                if($insertCategory){
                    $this->setShowMessage("<div class='alert alert-success' role='alert'><strong>"._tr("Texts")->success."</strong> "._tr("Infos")->category_successful_added."</div>");
                }
                else{
                    $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Infos")->error_on_procedure."</div>");
                }
            }
        }
        catch (\Exception $e){
            $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->fatal."</strong>"._tr("Infos")->internal_server_error." "._tr("Texts")->code.": ".$e->getCode()."</div>");
        }
    }
    public function visualizarCategoria(){

    }
    //==============================
    //FRONT-END Metodos
    //==============================
    public function ui_home(){
        echo "<article style=\"margin-top:80px;\">
        <div class=\"container\" style=\"height:auto;\">
            <div class=\"row\" style=\"height:auto;\">
                <div class=\"col-2\"></div>
                <div class=\"col-8\">
                    <div class=\"d-flex justify-content-between flex-wrap\" style=\"width:100%;height:100%;margin-top:20px;background-color:#ffffff;\">
                        <div style=\"width:46.5%;\">
                            <div style=\"height:80%;\">
                                <button class=\"btn btn-light\" type=\"button\" style=\"width:100%;height:100%;background-color:rgb(255,255,255);\">
                                    <img src=\"/assets/img/sent-mail.svg\" width=\"100px\">
                                </button>
                            </div>
                            <div style=\"height:20%;\">
                                <div class=\"dropdown\">
                                    <button class=\"btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"false\"
                                        type=\"button\" style=\"width:100%;height:41px;background-color:#13297D;font-size:18px;\">".strtoupper(_tr("Texts")->send)."  </button>
                                    <div class=\"dropdown-menu\" role=\"menu\">
                                        <a class=\"dropdown-item\" role=\"presentation\" href=\"".DS._admin.DS._send.DS._msg."\">"._tr("Texts")->message."</a>
                                        <a class=\"dropdown-item\" role=\"presentation\" href=\"".DS._admin.DS._send.DS._doc."\">"._tr("Texts")->document."</a>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style=\"width:46.5%;\">
                            <div style=\"height:80%;\">
                                <button class=\"btn btn-light\" type=\"button\" style=\"width:100%;height:100%;background-color:rgb(255,255,255);\">
                                    <img src=\"/assets/img/report.svg\" width=\"100px\">
                                </button>
                            </div>
                            <div style=\"height:20%;\">
                                <button class=\"btn btn-primary\" type=\"button\" style=\"width:100%;background-color:#13297D;font-size:18px;\">".strtoupper(_tr("Texts")->report)." </button>
                            </div>
                        </div>
                        <div style=\"width:46.5%;margin-top:30px;\">
                            <div style=\"height:80%;\">
                                <button class=\"btn btn-light\" type=\"button\" style=\"width:100%;height:100%;background-color:rgb(255,255,255);\">
                                    <img src=\"/assets/img/register.svg\" width=\"100px\">
                                </button>
                            </div>
                            <div style=\"height:20%;\">
                                <button class=\"btn btn-primary\" type=\"button\" style=\"width:100%;background-color:#13297D;font-size:18px;\">".strtoupper(_tr("Texts")->register)." </button>
                            </div>
                        </div>
                        <div style=\"width:46.5%;margin-top:30px;\">
                            <div style=\"height:80%;\">
                                <button class=\"btn btn-light\" type=\"button\" style=\"width:100%;height:100%;background-color:rgb(255,255,255);\">
                                    <img src=\"/assets/img/four-black-squares.svg\" width=\"100px\">
                                </button>
                            </div>
                            <div style=\"height:20%;\">
                                <button class=\"btn btn-primary\" type=\"button\" style=\"width:100%;background-color:#13297D;font-size:18px;\">".strtoupper(_tr("Texts")->category)." </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=\"col-2\"></div>
            </div>
        </div>
    </article>";
    }
    public function ui_register_user(){
        //var_dump($_GET);
        parent::ui_back(DS._admin.DS);
        self::showMessage();
        echo "<article style=\"margin-top:10px;height:auto;\">
        <div class=\"container\" style=\"height:auto;\">
            <div class=\"row relative\" style=\"height:auto;\">
                <div class=\"col\">
                    <form action=\"".DS._admin.DS._employee.DS._register.DS._make."\" class=\"d-flex justify-content-between flex-wrap\" method=\"POST\" enctype='application/x-www-form-urlencoded'>
                        <div class=\"form-group\" style=\"width:30%;\">
                            <label>"._tr("Texts")->name.": </label>
                            <input class=\"form-control\" type=\"text\" autofocus=\"\" name=\"nome\">
                        </div>
                        <div class=\"form-group\" style=\"width:30%;\">
                            <label>"._tr("Texts")->enroller.": </label>
                            <input class=\"form-control\" type=\"text\" name=\"matricula\">
                        </div>
                        <div class=\"form-group\" style=\"width:30%;\">
                            <label>"._tr("Texts")->email.": </label>
                            <input class=\"form-control\" type=\"text\" name=\"email\">
                        </div>
                        <div class=\"form-group\" style=\"width:22.5%;\">
                            <label>"._tr("Texts")->bank.": </label>
                            <input class=\"form-control\" type=\"text\" name=\"banco\">
                        </div>
                        <div class=\"form-group\" style=\"width:22.5%;\">
                            <label>"._tr("Texts")->agency.": </label>
                            <input class=\"form-control\" type=\"text\" name=\"agencia\">
                        </div>
                        <div class=\"form-group\" style=\"width:22.5%;\">
                            <label>"._tr("Texts")->account.": </label>
                            <input class=\"form-control\" type=\"text\" name=\"conta\">
                        </div>
                        <div class=\"form-group\" style=\"width:22.5%;\">
                            <label>"._tr("Texts")->operation.": </label>
                            <input class=\"form-control\" type=\"text\" name=\"operacao\">
                        </div>
                        <div class=\"form-group\" style=\"width:30%;\">
                            <label>"._tr("Texts")->cpf.": </label>
                            <input class=\"form-control\" type=\"text\" name=\"cpf\">
                        </div>
                        <div class=\"form-group\" style=\"width:30%;\">
                            <label>"._tr("Texts")->rg.": </label>
                            <input class=\"form-control\" type=\"text\" name=\"rg\">
                        </div>
                        <div class=\"form-group\" style=\"width:30%;\">
                            <label>"._tr("Texts")->work_wallet.":</label>
                            <input class=\"form-control\" type=\"text\" name=\"ctps\">
                        </div>
                        <div class=\"form-group\" style=\"width:30%;margin-left:150px;\">
                            <label>"._tr("Texts")->login.": </label>
                            <input class=\"form-control\" type=\"text\" name=\"login\">
                        </div>
                        <div class=\"form-group\" style=\"width:30%;margin-right:150px;\">
                            <label>"._tr("Texts")->password.": </label>
                            <input class=\"form-control\" type=\"text\" name=\"senha\">
                        </div>
                        <div class=\"btn-group d-flex justify-content-between\" role=\"group\" style=\"width:100%;padding-left:32%;padding-right:32%;margin-top:30px;\">
                            <button class=\"btn btn-primary\" type=\"button\" style=\"width:160px;height:40px;background-color:#728EFD;color:rgb(255,255,255);\">".strtoupper(_tr("Texts")->clear)." </button>
                            <button class=\"btn btn-primary\" type=\"submit\" style=\"width:160px;height:40px;background-color:#13297D;\">".strtoupper(_tr("Texts")->save)." </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </article>";
    }
    public function ui_visualize_employee(){
        parent::ui_back(DS._admin.DS);
        echo"<article style=\"margin-top:10px;height:auto;\">
                <div class=\"container\" style=\"height:auto;\">
                    <div class=\"row\">";
                self::visualizarFuncioarios();
        echo"                         
                    </div>
                </div>
        </article>";
    }
}
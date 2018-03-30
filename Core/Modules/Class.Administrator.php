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
use Interfaces\Employ;
use Kernel;
class Administrator extends Users implements Employ
{

    private $color;
    private $title;
    private $msg;
    private $sendTo;
    private $category;
    private $doc;
    public function __construct()
    {
        parent::__construct();
        $this->color = empty($_POST['cor']) ? null : $_POST['cor'];
        $this->title = empty($_POST['titulo']) ? null : $_POST['titulo'];
        $this->msg = empty($_POST['mensagem']) ? null : $_POST['mensagem'];
        $this->sendTo = empty($_POST['usuarios']) ? null : $_POST['usuarios'];
        $this->category = empty($_POST['categoria']) ? null : $_POST['categoria'];
        $this->doc = empty($_FILES['doc']) ? null : $_FILES['doc'];
    }
    public function getColor(){return $this->color;}
    public function getMsg(){return $this->msg;}
    public function getTitle(){return $this->title;}
    public function getSendTo(){return $this->sendTo;}
    public function getDoc(){return $this->doc;}
    public function getCategory(){return $this->category;}
    //Definição de retorna visual para o usuárop


    //==============================
    //BACK-END Metodos
    //==============================
    /** */
    public function cadastrarFuncionario($edit = false){
        //insere informações
        //Definindo variaveis de POSTDATA
        try{
            $permitedExtensionsFile = array("image/bmp","image/jpeg","image/png","image/pjpeg");
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
            //Caso seja Edição, entao faz a chamada de uma query especifica do usuário
            if($edit){
                $editQuery = $this->getSql()->query("SELECT * FROM ".__USERS." WHERE matricula='".$this->getMatriculation()."'");
                $editData = $editQuery->fetch(PDO::FETCH_ASSOC);
            }
            //var_dump($_POST);
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
            else if(!$edit && in_array($this->getMatriculation(),$singleArrayOfData)){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->matriculation_id_already_exist." </div>");
            }
            #Total de caracteres de matricula
            else if(!$edit && strlen($this->getMatriculation()) > 12){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> ".sprintf(_tr("Errors")->matriculation_id_max_length,12)." </div>");
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
            else if(!$edit && in_array($this->getLogin(),$singleArrayOfData)){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->login_name_already_exist."1</div>");
            }
            #Login inserido ja existe no banco MODO EDIÇÃO
            else if($edit && ($this->getLogin() != $editData['login'] && in_array($this->getLogin(),$singleArrayOfData) )){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->login_name_already_exist."</div>".$editData['login']);
            }
            #Email inserido inválido
            else if(Auth::isEmail($this->getEmail()) == false){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->email_name_invalid."</div>");
            }
            #email inserido ja existe no banco
            else if(!$edit && in_array($this->getEmail(),$singleArrayOfData)){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->email_already_exist."</div>");
            }
            #email inserido ja existe no banco MODO EDIÇÃO
            else if($edit && in_array($this->getEmail(),$singleArrayOfData) && $this->getEmail() != $editData['email']){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->email_already_exist."</div>");
            }
            #Validaçao de CPF
            else if(Auth::isCpf($this->getCpf()) == false){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->cpf_number_invalid."</div>");
            }
            #Cpf já existe
            else if(!$edit && in_array($this->getCpf(),$singleArrayOfData)){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->cpf_already_exist."</div>");
            }
            #Cpf já existe MODO DE EDICÃO
            else if($edit && in_array($this->getCpf(),$singleArrayOfData) && $this->getCpf() != $editData['cpf']){
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
            else if(!is_numeric($this->getCtps())){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->ctps_field_only_accept_numbers."</div>");
            }
            else if(!Auth::isPhone($this->getPhone())){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->phone_invalid."</div>");
            }
            else if(!empty($this->getPhoneAlt()) && Auth::isPhone($this->getPhoneAlt())){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->alternative_phone_invalid."</div>");
            }
            else if(!empty($this->getAvatar()) && $this->getAvatar()['size'] > 1024 * 1024){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> ".sprintf(_tr("Errors")->file_size_too_large,1)."</div>");
            }
            else if($this->getAvatar()['error'] != 4 && (!in_array($this->getAvatar()['type'],$permitedExtensionsFile))){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> ".sprintf(_tr("Errors")->file_extension_invalid,"<kbd>.jpg, .png, .bmp</kbd>")."</div>");
            }
            else if($edit && empty($this->getMatriculation())){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->user_data_not_found."</div>");
            }
            else if($edit && $editQuery->rowCount() == 0){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->user_not_found."</div>");
            }
            else{
                //imagem padrao de usuário.. icone da Acctos (não reproduzir) MODO CADASTRO
                //--------------------------------------------------------------------------------------------------
                if(!$edit)
                    $default_image = "data:image/png;charset=utf-8;base64,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";
                //--------------------------------------------------------------------------------------------------
                else
                    $default_image = $editData['image'];

                $dataImage='';
                $blobImage = $this->getAvatar()['error']!= 4 ? file_get_contents($this->getAvatar()['tmp_name']): null;
                $mimeFileType = $this->getAvatar()['error']!= 4 ?  mime_content_type($this->getAvatar()['tmp_name']): null;
                $dataFileBase64 = base64_encode($blobImage);
                $dataImageUpd = "data:".$mimeFileType.";charset=utf-8;base64,".$dataFileBase64;
                if($this->getAvatar()['error'] == 4){
                    $dataImage = $default_image;
                }
                else{
                    $dataImage = $dataImageUpd;
                }

                if(!$edit)
                    $insertNewData = $this->getSql()->prepare("INSERT INTO ".__USERS." 
                    (nome, matricula,senha,email,rg,cpf,login,operacao,banco,agencia,conta,tel1,tel2,ctps,tipo,`status`,image) 
                    VALUES (:nome,:matricula,:senha,:email,:rg,:cpf,:login,:op,:banco,:agencia,:conta,:tel1,:tel2,:ctps,:tipo,:status,:image)");
                else
                    $insertNewData = $this->getSql()->prepare("UPDATE ".__USERS." SET 
                    nome=:nome, senha=:senha,email=:email,rg=:rg,cpf=:cpf,login=:login,operacao=:op,banco=:banco,
                    agencia=:agencia,conta=:conta,tel1=:tel1,tel2=:tel2,ctps=:ctps,tipo=:tipo,`status`=:status,
                    image=:image WHERE matricula=:matricula");
                //15 entradas, sendo 2 opcionais (telefone 2, campo de arquivo de foto)
                $insertNewData->bindValue(":nome", $this->getName());
                $insertNewData->bindValue(":matricula", $this->getMatriculation());
                $insertNewData->bindValue(":senha", Kernel::getPwdPattern($this->getPasswd()));
                $insertNewData->bindValue(":email", $this->getEmail() );
                $insertNewData->bindValue(":rg", $this->getRg() );
                $insertNewData->bindValue(":cpf", $this->getCpf() );
                $insertNewData->bindValue(":login", $this->getLogin());
                $insertNewData->bindValue(":op", $this->getOperation());
                $insertNewData->bindValue(":banco", $this->getBank());
                $insertNewData->bindValue(":agencia", $this->getAgency());
                $insertNewData->bindValue(":conta", $this->getAccount() );
                $insertNewData->bindValue(":tel1", $this->getPhone() );
                $insertNewData->bindValue(":tel2", $this->getPhoneAlt());
                $insertNewData->bindValue(":ctps", $this->getCtps());
                $insertNewData->bindValue(":tipo", 2);//tipo de usuário
                $insertNewData->bindValue(":status", 1);//Status
                $insertNewData->bindValue(":image",$dataImage);
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
            echo $e->getMessage();
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->fatal."</strong> "._tr("Infos")->internal_server_error." "._tr("Texts")->code.": ".$e->getCode()."</div>");
        }

    }
    public function removerFuncionario(){
        //Confirma a ação com o POST recuperado
        if($_POST['send'] == true){
            $StreamMessagesUSer = $this->getSql()->query("DELETE FROM ".__MESSAGE." WHERE mid='".$_GET[_lastAction]."'");
            $StreamViewsQuery =  $this->getSql()->query("DELETE FROM ".__VIEWS." WHERE mid_ref='".$_GET[_lastAction]."'");
            $StreamUserQuery = $this->getSql()->query("DELETE FROM ".__USERS." WHERE matricula='".$_GET[_lastAction]."'");

            if($StreamUserQuery){
                $this->setShowMessage("<div class='alert alert-success' role='alert'><strong>"._tr("Texts")->success."</strong> "._tr("Infos")->employee_successful_removed."</div>");
            }
            else{
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Infos")->error_on_procedure."</div>");
            }
        }
    }
    public function visualizarFuncioarios(){

        try {
            $Pagination = new Pagination(__USERS, "id", $_GET[_subAction]);
            $mainStreamReaderQuery = $this->getSql()->query("SELECT * FROM " . __USERS . " WHERE tipo='2' " . $Pagination->getProcessLimit());
            if ($mainStreamReaderQuery->rowCount() == 0) {
                echo "
                 
                    <div class=\"col\">
                     <br/><br/>
                        <div class='illustration'> 
                            <i class='fa fa-users text-primary extra-icon in-center'></i>
                        </div>
                        <br/>
                        <h4 class='text-center text-primary'>" . _tr("Infos")->none_employees_registered . "</h4>             
                    </div>
                    
                ";
            } else {
                $countUser = 1;
                while ($StreamHub = $mainStreamReaderQuery->fetch(PDO::FETCH_ASSOC)) {
                    $breakName = explode(" ",$StreamHub['nome']);
                    $firstName = $breakName[0];
                    echo "<div class=\"col-md-4 col-sm-12 col-xl-3\" style=\"margin-bottom:30px;\">
                    <div class=\"d-flex justify-content-center\"> 
                        <div class='image-border'> 
                        <img src=\"".$StreamHub["image"]."\"/>
                        </div>
                       
                    </div>
                    <div class=\"nome_func\" style=\"margin-top:18px;\">
                        <h4 class=\"text-center\">" .$firstName . "</h4>
                    </div>
                    <div class=\"d-flex flex-column align-items-center\">
                        <h5 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif; margin-top:10px;\">"._tr("Texts")->contact."</h5>
                        <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->email.": ".$StreamHub['email']."</h6>
                        <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->phone.": ".System::mask($StreamHub['tel1'],"_tel")."</h6>
                        <h5 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif; margin-top:10px;\">"._tr("Texts")->bank_data."</h5>
                        <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->bank.": ".$StreamHub['banco']."</h6>
                        <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->account.": ".$StreamHub['conta']."</h6>
                        <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->agency." ".$StreamHub['agencia']."</h6>
                        <a href=\"#\" style=\"margin-top:14px;\">
                            <a data-toggle=\"modal\" href=\"#normalModal-".$countUser."\" class=\"btn btn-default\">
                                <button class=\"btn btn-primary\" type=\"button\">" . _tr("Texts")->more_info . "</button>
                            </a>
                            <a href=\"".DS._admin.DS._employee.DS._edit.DS.$StreamHub['matricula']."\" class=\"btn btn-default\">
                                <button class=\"btn btn-info\" type=\"button\">" . _tr("Texts")->edit . "</button>
                            </a>
                        </a>
                    </div>


                    <div id=\"normalModal-".$countUser."\" class=\"modal fade\">
                        <div class=\"modal-dialog\">
                            <div class=\"modal-content\">
                                <div class=\"modal-header\">
                                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>

                                </div>
                                <div class=\"modal-body\">
                                    <h5 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif; margin-bottom:20px;\">"._tr("Texts")->full_name.": ".$StreamHub['nome']."</h5>
                                    <h5 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif; margin-top:20px; margin-bottom:20px;\">"._tr("Texts")->personal_information."</h5>
                                    <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->enroller.": ".$StreamHub['matricula']."</h6>
                                    <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->email.": ".$StreamHub['email']."</h6>
                                    <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->cpf.": ".System::mask($StreamHub['cpf'],"_cpf")."</h6>
                                    <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->rg.": ".System::mask($StreamHub['rg'],"_rg")."</h6>
                                    <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->work_wallet.": ".$StreamHub['ctps']."</h6>
                                    <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->phone." 1: ".System::mask($StreamHub['tel1'],"_tel")."</h6>
                                    <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->phone." 2: ".System::mask($StreamHub['tel2'],"_tel")."</h6>
                                    <h5 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif; margin-top:20px; margin-bottom:20px;\">"._tr("Texts")->data_bank."</h5>
                                    <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->bank.": ".$StreamHub['banco']."</h6>
                                    <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->account.": ".$StreamHub['conta']."</h6>
                                    <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->agency." ".$StreamHub['agencia']."</h6>
                                    <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->operation.": ".$StreamHub['operacao']."</h6>
                                    <h5 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif; margin-top:20px; margin-bottom:20px;\">"._tr("Texts")->access."</h5>
                                    <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->login.": ".$StreamHub['login']."</h6>
                                    <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif;\">"._tr("Texts")->password.": ".$StreamHub['agencia']."</h6>
                                </div>
                                <div class=\"modal-footer\">
                                    <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">"._tr("Texts")->close."</button>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <!-- /.modal -->
                    </div>";
                    $countUser++;
                }
                $Pagination->getPagination(DS . _admin . DS . _employee);
            }
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
    public function editarCategoria(){
        try{
            //Padrão para cor
            $color = $_POST['cor'];

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
            //------------------------------------------------------------------------
            $StreamEditCategoryQuery = $this->getSql()->prepare("SELECT * FROM ".__CATEGORY." WHERE id=?");
            $StreamEditCategoryQuery->bindValue(1,$_GET[_lastAction]);
            $StreamEditCategoryQuery->execute();
            $StreamEditFetch = $StreamEditCategoryQuery->fetch(PDO::FETCH_ASSOC);

            if(empty($this->getName()) || empty($this->getColor())){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong>"._tr("Errors")->field_cannot_be_empty."</div>");
            }
            else if(Auth::isNames($this->getName()) == false ){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong>"._tr("Errors")->category_name_invalid."</div>");
            }
            else if(in_array(strtolower(trim(Auth::removeSpecialCharacters($this->getName()))),$monoArrayOfCategory) && $this->getName() != $StreamEditFetch['nome']){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong>"._tr("Errors")->category_already_exist."</div>");
            }
            else if(Auth::isColor($this->getColor()) == false){
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong>"._tr("Errors")->color_hex_pattern_not_detected."</div>");
            }
            else{

                $insertCategory = $this->getSql()->prepare("UPDATE ".__CATEGORY." SET nome=?, cor=? WHERE id=?");
                $insertCategory->bindValue(1,$this->getName());
                $insertCategory->bindValue(2,$this->getColor());
                $insertCategory->bindValue(3,$_GET[_lastAction]);
                $insertCategory->execute();

                if($insertCategory){
                    $this->setShowMessage("<div class='alert alert-success' role='alert'><strong>"._tr("Texts")->success."</strong> "._tr("Infos")->category_successful_edited."</div>");
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
    //Executa a query de remoção da categoria com o id especificado!
    public function removerCategoria(){
        //Confirma a ação com o POST recuperado
        if($_POST['send'] == true){
            $StreamCategoryQuery = $this->getSql()->query("DELETE FROM ".__CATEGORY." WHERE id='".$_GET[_lastAction]."'");
            if($StreamCategoryQuery){
                $this->setShowMessage("<div class='alert alert-success' role='alert'><strong>"._tr("Texts")->success."</strong> "._tr("Infos")->category_successful_removed."</div>");
            }
            else{
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Infos")->error_on_procedure."</div>");
            }
        }
    }
    //Mostrar uma tela de confirmação de remoção
    public function prompt_remove_category(){
        $StreamCategoryQuery = $this->getSql()->query("SELECT * FROM ".__CATEGORY." WHERE id='".$_GET[_lastAction]."'");
        #se a categoria não existir e não existir POST de envio para confirmação de remoção!
        if($StreamCategoryQuery->rowCount() == 0 && empty($_POST['send'])){
            $this->setShowMessage("
                <i class='fa fa-times-circle-o big-icon in-center text-danger mt-1'></i>
                <h3 class='text-danger text-center '>"._tr("Texts")->warning."</h3>
                <h6 class='text-center pt-2'>"._tr("Errors")->category_id_not_found."</h6>");
        }
        #se a categoria existir, mas não foi confirmada a remoção!
        elseif ($StreamCategoryQuery->rowCount() == 1 && empty($_POST['send'])){
            //Recuperação de informações
            $StreamCategoryFetch = $StreamCategoryQuery->fetch(PDO::FETCH_ASSOC);
            $this->setShowMessage( "<h3 class='text-danger text-center mt-5'>"._tr("Texts")->warning."</h3>
                <p class='text-center pt-2'>".sprintf(_tr("Infos")->category_will_be_perm_removed,"<b>".$StreamCategoryFetch['nome']."</b>")."</p>
                <p class='text-center'>"._tr("Infos")->itens_with_removed_category_will_be_called_not_categorized."</p>
                <form action='".DS._admin.DS._category.DS._remove.DS.$StreamCategoryFetch['id'].DS._make.DS."' method='post' enctype='application/x-www-form-urlencoded'>
                    <input type='hidden' value='true' name='send' /> 
                    <div class='btn-group d-flex justify-content-between'> 
                        <a href='".DS._admin.DS._category.DS._edit.DS.$StreamCategoryFetch['id'].DS."'> <button class=\"btn btn-secondary\" type=\"button\" style=\"width:160px;height:40px;color:rgb(255,255,255);\">"._tr("Texts")->cancel." </button></a>
                        <button class=\"btn btn-primary\" type=\"submit\" style=\"width:160px;height:40px;color:rgb(255,255,255);\">"._tr("Texts")->delete." </button>
                    </div>
                </form>");
        }
    }
    private function visualizarCategoria(){
        $getStreamOfQuery = $this->getSql()->query("SELECT * FROM ".__CATEGORY);

        while($IOstream = $getStreamOfQuery->fetch(PDO::FETCH_ASSOC)){
            echo "<tr>
                <td>".$IOstream['nome']." </td>
                <td style=\"padding:0;height:40px;\">
                    <input type=\"color\" disabled='disabled' style=\"width:100%;height:100%;background-color:;padding:0;\" value='".$IOstream['cor']."'>
                </td>
                <td style=\"padding:0;height:50px;\">
                    <a href='".DS._admin.DS._category.DS._edit.DS.$IOstream['id']."'>
                        <button class=\"btn btn-link\" type=\"button\" style=\"width:100%;height:96%;background-color:rgb(255,255,255);\">
                            <i class=\"fa fa-edit\" style=\"font-size:26px;color:rgb(51,23,221);\"></i>
                        </button>
                    </a>
                </td>
            </tr>";
        }
    }
    public function enviarMsgs($isDoc = false){
        //
        if(empty($this->getTitle()) || empty($this->getCategory()) || empty($this->getSendTo()) || empty($this->getMsg())){
            $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->all_fields_need_to_be_filled."</div>");
        }
        else if($isDoc && empty($this->getDoc()) ){
            $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->document_not_annexed."</div>");
        }
        else if(Auth::isNatural($this->getTitle()) == false){
            $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->title_inserted_invalid."</div>");
        }
        else if($isDoc && $this->getDoc()['size'] > (1024* 1024 * 6)){
            $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> ".sprintf(_tr("Errors")->file_size_too_large,"6 Megabytes ")."</div>");
        }
        else if($isDoc && $this->getDoc()['error'] != 0){
            $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->error_on_upload_file."</div>");
        }
        else if($isDoc && !in_array($this->getDoc()['type'],default_document_patten)){
            $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->file_type_not_accept."</div>");
        }
        else if($this->getSendTo() == "N/D" || $this->getSendTo() == null){
            $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->none_users_selected."</div>");
        }
        else{
            $lista_matricula = [];
            $matricula = '';
            if(in_array("all", $this->getSendTo())){
                $sqlgetuser = $this->getSql()->query("SELECT matricula FROM ".__USERS." WHERE tipo = 2");
                while($t = $sqlgetuser->fetch(PDO::FETCH_ASSOC)){
                    $lista_matricula[] = $t['matricula'];
                }
            }
            else{
                $lista_matricula = $this->getSendTo();
            }
            //Aplica o upload da imagem
            $docName = "";
            $type =0;
            if($isDoc && !empty($this->getDoc())){
                $uploadSystem = new System();
                $docName = $uploadSystem->uploadSystem($this->getDoc(),DOCPATH);
                $type = 1;
            }
            #Cria o identificador da mensagem que sera usado para realizar o download
            $randSeed = uniqid(time());
            $sendMessageQuery = $this->getSql()->prepare("INSERT INTO ".__MESSAGE." (mid, titulo, categoria, mensagem, lista_matricula, documento, type) VALUES (?,?,?,?,?,?,?)");
            $sendMessageQuery->bindValue(1,$randSeed);
            $sendMessageQuery->bindValue(2,$this->getTitle());
            $sendMessageQuery->bindValue(3,$this->getCategory());
            $sendMessageQuery->bindValue(4,$this->getMsg());
            $sendMessageQuery->bindParam(5,$matricula);
            $sendMessageQuery->bindValue(6,$docName);
            $sendMessageQuery->bindValue(7, $type);
            foreach ($lista_matricula as $matricul){
                $matricula = $matricul;
                $sendMessageQuery->execute();
            }




            if($isDoc){
                if($sendMessageQuery && $docName){
                    $this->setShowMessage("<div class='alert alert-success' role='alert'><strong>"._tr("Texts")->complete."</strong> "._tr("Infos")->message_successful_sended."</div>");
                }
                else if ($sendMessageQuery && !$docName){
                    $this->setShowMessage("<div class='alert alert-warning' role='alert'><strong>"._tr("Texts")->warning."</strong> "._tr("Errors")->message_sended_but_file_not_uploaded."</div>");
                }
                else if (!$sendMessageQuery && $docName){
                    $this->setShowMessage("<div class='alert alert-warning' role='alert'><strong>"._tr("Texts")->warning."</strong> "._tr("Errors")->file_save_but_message_not_sended."</div>");
                }
                else{
                    $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->impossible_complete_send_message_action."</div>");
                }
                unset($_POST);
            }
            else{
                if($sendMessageQuery){
                    $this->setShowMessage("<div class='alert alert-success' role='alert'><strong>"._tr("Texts")->complete."</strong> "._tr("Infos")->message_successful_sended."</div>");
                }
                else{
                    $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->error."</strong> "._tr("Errors")->impossible_complete_send_message_action."</div>");
                }
            }

        }
    }
    public function visualizaMsg($isDoc = false){}
    private function gerarRelatorio(){
        //Carrega os filtros e os dados recebidos do POST
        $tituloSearch = empty($_POST['tituloPesquisa']) ? null : $_POST['tituloPesquisa'];
        $categorySelect = empty($_POST['categoriaFiltro']) ? null : $_POST['categoriaFiltro'];
        $userSelect = empty($_POST['usuarioFiltro']) ? null : $_POST['usuarioFiltro'];

        $filters = null;
        $ftrArray = [];

        $search = empty($tituloSearch) ? null : " WHERE ".__MESSAGE.".titulo LIKE '%".$tituloSearch."%' ";

        if($categorySelect != "" ){ $ftrArray[] =  __CATEGORY.".id='".$categorySelect."'"; }
        if($userSelect != ""){$ftrArray[] =  __USERS.".matricula LIKE'".$userSelect."'";}
        //$ftrArray[] = !empty($categorySelect) ? __CATEGORY.".id='".$categorySelect."'" : null;
        //$ftrArray[] = !empty($userSelect) ? __USERS.".nome LIKE'".$userSelect."'" : null;
        if(!empty($categorySelect)){
            !empty($search) ? $concat = " AND " : $concat = " WHERE ";
            $filters = $concat;
        }
        if(sizeof($ftrArray)){
            $filters .= implode(" AND ",$ftrArray);
        }
       // echo $search.' '.$filters.sizeof($ftrArray);
        $table =__MESSAGE." LEFT JOIN ".__CATEGORY." ON ".__MESSAGE.".categoria=".__CATEGORY.".id INNER JOIN ".__USERS." ON ".__MESSAGE.".lista_matricula=".__USERS.".matricula";
        //---------------------
        $pagination = new Pagination($table,__MESSAGE.'.id',$_GET[_subAction],$search.' '.$filters,4);
        //---------------------
        //Sql com Paginação direta no Banco
        $sql = "SELECT ".__MESSAGE.".*, ".__CATEGORY.".nome, ".__CATEGORY.".cor, ".__USERS.".nome AS username FROM ".__MESSAGE." INNER JOIN ".__USERS." ON ".__MESSAGE.".lista_matricula=".__USERS.".matricula LEFT JOIN ".__CATEGORY." ON ".__MESSAGE.".categoria=".__CATEGORY.".id ".$search." ".$filters. " ".$pagination->getProcessLimit();
        //Sql com Paginação de loop interno
        //echo $sql;
        //$sql = "SELECT ".__MESSAGE.".*, ".__CATEGORY.".nome, ".__USERS.".nome AS username FROM ".__MESSAGE." INNER JOIN ".__USERS." ON ".__MESSAGE.".lista_matricula=".__USERS.".matricula LEFT JOIN ".__CATEGORY." ON ".__MESSAGE.".categoria=".__CATEGORY.".id ".$search." ".$filters;
        //----------------
        $SqlMain = $this->getSql()->query($sql);
        //-------------------------------------
        $sqlViews = $this->getSql()->query("SELECT * FROM ".__VIEWS);
        $fetchViews = $sqlViews->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($fetchViews);
        $countLoop = 1;
        if($SqlMain->rowCount() == 0){
            echo "<div class=\"col\"><h3 class=\"text-info text-center mt-5\">"._tr("Texts")->none_results_found." </h3>
                <p class=\"text-center pt-2\">"._tr("Infos")->not_found_with_selected_filters."</p>
                <p class=\"text-center\">"._tr("Infos")->try_new_combo_filters."</p>
                </div>";
        }
        else{

            //------------
            while($fetch = $SqlMain->fetch(PDO::FETCH_ASSOC)){
                $visualize = '';
                $download = '';
                $dnTimes = 0;
                for($i = 0; $i <count($fetchViews); $i++){
                    if($fetchViews[$i]['mid_ref'] == $fetch['mid']){
                        $visualize = $fetchViews[$i]['dateViewed'];
                        $download = $fetchViews[$i]['dateDownload'];
                        $dnTimes =  $fetchViews[$i]['download'];
                        break;
                    }
                }
                if(!empty($visualize)){
                    $data = new \DateTime($visualize);
                    $visualized = $data->format("d-m-Y - H:i:s");
                }
                else{
                    $visualized = _tr("Texts")->not_visualized;
                }
                if(!empty($download)){
                    $data = new \DateTime($download);
                    $download = $data->format("d-m-Y - H:i:s");
                }
                else{
                    $download = _tr("Texts")->not_downloaded_yet;
                }


                //var_dump($fetch);
                $categoryName = empty($fetch['nome']) ? _tr("Texts")->not_categorized : $fetch['nome'];
                echo "<div class=\"col-md-4 col-sm-12 col-xl-3 over-border\">
            <div class=\"nome_func\" style=\"margin-top:18px;\">
                <h4 class=\"text-center\">".$fetch['titulo']."</h4>
            </div>
            <div class=\"d-flex flex-column align-items-center\">
                <h5 class=\"text-center w-100 mg-t-10 p-1 rounded\" style=\"font-family:'Noto Sans', sans-serif;color:#fff;background-color: ".$fetch['cor'].";\">".$categoryName."</h5>
                <h6 class=\"text-left\" style=\"font-family:'Noto Sans', sans-serif; margin-top:10px;\">".$fetch['username']."</h6>
               
                <button class=\"btn btn-primary\" type=\"button\" data-toggle=\"collapse\" data-target=\"#message-".$countLoop."\" aria-expanded=\"false\" style=\"margin-top:20px; \"
                aria-controls=\"message-".$countLoop."\">"._tr("Texts")->message."
                </button>
                
                <div class=\"collapse\" id=\"message-".$countLoop."\" style=\"width: 100%\">
                    <div class=\"card card-body\">
                    <div class='card-text'>".$fetch['mensagem']."</div>
                    
                    </div>
                </div>";
                if($fetch['type'] == 1){
                    echo "<p class=\"text-center no-margin\"> <i class='fa fa-download'></i> "._tr("Texts")->downloaded.":<br/>  <small>".$download." ".$dnTimes." "._tr("Texts")->times."</small></p>";
                }

                echo"<p class=\"text-center\"> <i class='fa fa-eye'></i> "._tr("Texts")->visualized_message.":<br/> <small>".$visualized."</small></p>";
                if($fetch['type'] == 1){
                    echo"
                <a href=\"#\" style=\"margin-top:14px;\">
                    <a data-toggle=\"modal\" href=\"#normalModal-".$countLoop."\" >
                        <button class=\"btn btn-primary\" type=\"button\">"._tr("Texts")->document."</button>
                    </a>
                </a>";
                }

             echo "</div>
            <div id=\"normalModal-".$countLoop."\" class=\"modal fade\">
                <div class=\"modal-dialog\">
                    <div class=\"modal-content\">
                        <div class=\"modal-header\">
                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>
    
                        </div>
                        <div class=\"modal-body\">";
                        if($fetch['type'] == 1){
                            $file =DS.DOCFOLDER.DS.$fetch['documento'];
                            $mimeType = mime_content_type(DOCPATH.$fetch['documento']);
                            //.DS._requisition.DS._pdf.DS.$fetch['documento']
                            echo "<object data=\"".$file."#zoom=70#page=1\" type=\"".$mimeType."\" width=\"100%\" height=\"90%\">
                               <p>
                               <b>"._tr("Texts")->error."</b>: "._tr("Infos")->browser_doesnt_support_pdf_download_it.": <a href=\"".DS._requisition.DS._pdf.DS.$fetch['documento']."\" download>"._tr("Texts")->download."</a>.</p>
                            </object>";
                            //echo "<iframe src=\"".DS._requisition.DS._pdf.DS.$fetch['documento']."#zoom=70\" width=\"724 \" height=\"748\" style=\"border: none;\"></iframe>";
                            //self::showPdf($fetch['documento']);
                        }
                        else{
                            echo _tr("Infos")->none_document_annex;
                        }

                        echo "  
                        </div>
                        <div class=\"modal-footer\">
                            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
    
            </div>";
                //var_dump($_SERVER);
                $countLoop++;
            }
            $pagination->getPagination(DS._admin._report);
        }


    }
    public function showPdf($filename){
        //SERVER_NAME
        $file = DOCPATH.$filename;
        //var_dump($_GET);
        header("Content-type: application/pdf");
        //header('Content-disposition: attachment; filename=\"'.$filename.'\";');

        readfile($file);
    }
    public function prompt_remove_user(){
        $StreamCategoryQuery = $this->getSql()->query("SELECT * FROM ".__USERS." WHERE matricula='".$_GET[_lastAction]."'");
        #se a categoria não existir e não existir POST de envio para confirmação de remoção!
        if($StreamCategoryQuery->rowCount() == 0 && empty($_POST['send'])){
            $this->setShowMessage("
                <i class='fa fa-user-times big-icon in-center text-danger mt-1'></i>
                <h3 class='text-danger text-center'>"._tr("Texts")->warning."</h3>
                <h6 class='text-center pt-2'>"._tr("Errors")->employee_enroller_not_found."</h6>");
        }
        #se a categoria existir, mas não foi confirmada a remoção!
        elseif ($StreamCategoryQuery->rowCount() == 1 && empty($_POST['send'])){
            //Recuperação de informações
            $StreamCategoryFetch = $StreamCategoryQuery->fetch(PDO::FETCH_ASSOC);
            $this->setShowMessage( "<i class='fa fa-trash-o big-icon in-center text-danger mt-1'></i><h3 class='text-danger text-center'>"._tr("Texts")->warning."</h3>
                <p class='text-center pt-2'>".sprintf(_tr("Infos")->employee_will_be_perm_removed,"<b>".$StreamCategoryFetch['nome']."</b>")."</p>
                <p class='text-center'>"._tr("Infos")->this_action_cannot_be_undone."
               <br/>"._tr("Infos")->you_is_right_about_this_action."</p>
                <form action='".DS._admin.DS._employee.DS._remove.DS.$StreamCategoryFetch['matricula'].DS._make.DS."' method='post' enctype='application/x-www-form-urlencoded'>
                    <input type='hidden' value='true' name='send' /> 
                    <div class='btn-group d-flex justify-content-between'> 
                        <a href='".DS._admin.DS._employee.DS._edit.DS.$StreamCategoryFetch['matricula'].DS."'> <button class=\"btn btn-secondary\" type=\"button\" style=\"width:160px;height:40px;color:rgb(255,255,255);\">"._tr("Texts")->cancel." </button></a>
                        <button class=\"btn btn-primary\" type=\"submit\" style=\"width:160px;height:40px;color:rgb(255,255,255);\">"._tr("Texts")->delete." </button>
                    </div>
                </form>");
        }
    }
    //==============================
    //FRONT-END Metodos
    //==============================
    public function ui_home(){
        echo "<article style=\"margin-top:20px;\">
        <div class=\"container\" style=\"height:auto;\">
            <div class=\"row\" style=\"height:auto;\">
                <div class=\"col-2\"></div>
                <div class=\"col-8\">
                    <div class=\"d-flex justify-content-between flex-wrap\" style=\"width:100%;height:100%;margin-top:20px;background-color:#ffffff;\">
                        <div style=\"width:46.5%;\">
                            <div style=\"height:80%;\">
                                <button class=\"btn btn-light no-fh\" type=\"button\" style=\"width:100%;height:100%;background-color:rgb(255,255,255);\">
                                    <i class='dw icon-sent in-center text-primary extra-icon'></i>
                                </button>
                            </div>
                            <div style=\"height:20%;\">
                                <div class=\"dropdown\">
                                    <button class=\"btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-expanded=\"false\"
                                        type=\"button\" style=\"width:100%;height:41px;font-size:18px;\">".strtoupper(_tr("Texts")->send)."  </button>
                                    <div class=\"dropdown-menu\" role=\"menu\" style=\"width:100%;font-size:18px;\">
                                        <a class=\"dropdown-item\" role=\"presentation\" href=\"".DS._admin.DS._send.DS._msg."\">"._tr("Texts")->message."</a>
                                        <a class=\"dropdown-item\" role=\"presentation\" href=\"".DS._admin.DS._send.DS._doc."\">"._tr("Texts")->document."</a>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style=\"width:46.5%;\">
                                <div style=\"height:80%;\">
                                    <button class=\"btn btn-light no-fh\" type=\"button\" style=\"width:100%;height:100%;background-color:rgb(255,255,255);\">
                                        <i class='dw icon-report in-center text-primary extra-icon'></i>
                                    </button>
                                </div>
                            <a href='".DS._admin.DS._report.DS."'>
                                <div style=\"height:20%;\">
                                    <button class=\"btn btn-primary\" type=\"button\" style=\"width:100%;font-size:18px;\">".strtoupper(_tr("Texts")->report)." </button>
                                </div>
                            </a>
                        </div>
                        <div style=\"width:46.5%;margin-top:30px;\">
                            <div style=\"height:80%;\">
                                <button class=\"btn btn-light no-fh\" type=\"button\" style=\"width:100%;height:100%;background-color:rgb(255,255,255);\">
                                    <i class='dw icon-register in-center text-primary extra-icon'></i>
                                </button>
                            </div>
                            <a href='".DS._admin.DS._employee.DS._register.DS."'>
                                <div style=\"height:20%;\">
                                    <button class=\"btn btn-primary\" type=\"button\" style=\"width:100%;font-size:18px;\">".strtoupper(_tr("Texts")->register)." </button>
                                </div>
                            </a>
                        </div>
                        <div style=\"width:46.5%;margin-top:30px;\">
                            <div style=\"height:80%;\">
                                <button class=\"btn btn-light no-fh\" type=\"button\" style=\"width:100%;height:100%;background-color:rgb(255,255,255);\">
                                    <i class='dw icon-category in-center text-primary extra-icon'></i>
                                </button>
                            </div>
                            <a href='".DS._admin.DS._category.DS."'>
                                <div style=\"height:20%;\">
                                    <button class=\"btn btn-primary\" type=\"button\" style=\"width:100%;font-size:18px;\">".strtoupper(_tr("Texts")->category)." </button>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class=\"col-2\"></div>
            </div>
        </div>
    </article>";
    }
    public function ui_register_user(){
        echo "<article class=\"d-flex justify-content-center\" style=\"height:50px;background-color:#152462;padding-top:9px;\">
        <div></div>
        <h4 style=\"color:rgb(255,255,255);letter-spacing:2px;font-weight:bold;padding-top:-12px;\">"._tr("Texts")->register." </h4>
    </article>";
        //parent::ui_back(DS._admin.DS);
        self::showMessage();
        echo "
    <div id='propag_message'></div>
    <article style=\"margin-top:10px;height:auto;\">
        <div class=\"container\" style=\"height:auto;\">
            <div class=\"row\" style=\"height:auto;\">
                <div class=\"col\">
                    <form action=\"".DS._admin.DS._employee.DS._register.DS._make."\" requisition=\"".DS._requisition.DS._employee.DS._register."\" id=\"cadastro_func\" class=\"form\" method=\"post\" enctype='multipart/form-data'>
                       
                        <div class=\"d-flex justify-content-between flex-wrap col-12\">
                            <div class=\"col-12\">
                                <h2 class=\"info_cadas\">"._tr("Texts")->personal_information."</h2>
                            </div>
                            <div class=\"form-group col-md-4 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->name.": </label>
                                <input name=\"nome\" class=\"form-control\" type=\"text\" autofocus>
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->enroller.": </label>
                                <input name=\"matricula\" class=\"form-control\" type=\"text\">
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->email.": </label>
                                <input name=\"email\" class=\"form-control\" type=\"text\">
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->cpf.": </label>
                                <input name=\"cpf\" class=\"form-control\" type=\"text\">
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->rg.": </label>
                                <input name=\"rg\" class=\"form-control\" type=\"text\">
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->work_wallet.":</label>
                                <input name=\"ctps\" class=\"form-control\" type=\"text\">
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->phone." 1: </label>
                                <input attrname=\"telephone1\" name=\"tel1\" class=\"form-control\" pattern=\"\([0-9]{2}\)[\s][0-9]{4}-[0-9]{4,5}\" type=\"text\">
                            </div>

                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->phone." 2: </label>
                                <input name=\"tel2\" class=\"form-control\" type=\"text\">
                            </div>
                        </div>

                        <div class=\"d-flex justify-content-between flex-wrap col-12\">
                            <div class=\"col-12\">
                                <h2 class=\"info_cadas\">"._tr("Texts")->bank_data."</h2>
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->bank.": </label>
                                <input name=\"banco\" class=\"form-control\" type=\"text\">
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->agency.": </label>
                                <input name=\"agencia\" class=\"form-control\" type=\"text\">
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->account.": </label>
                                <input name=\"conta\" class=\"form-control\" type=\"text\">
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->operation.": </label>
                                <input name=\"op\" class=\"form-control\" type=\"text\">
                            </div>
                        </div>

                        <div class=\"d-flex justify-content-start flex-wrap col-12\">
                            <div class=\"col-12\">
                                <h2 class=\"info_cadas\">"._tr("Texts")->access_information." & "._tr("Texts")->extras."</h2>
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->login.": </label>
                                <input name=\"login\" class=\"form-control\" type=\"text\">
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->password.": </label>
                                <input name=\"senha\" class=\"form-control\" type=\"text\">
                            </div>
                            
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label for=\"avatar\">"._tr("Texts")->user_image.": </label>
                                <input id=\"avatar\" name=\"avatar\" class=\"form-control-file\" type=\"file\">
                               
                            </div>
                        </div>

                        <div class=\"btn-group d-flex justify-content-between\" role=\"group\" style=\"width:100%;padding-left:32%;padding-right:32%;margin-top:30px;\">
                            <button id=\"limpar\" class=\"btn btn-primary\" type=\"button\" style=\"width:160px;height:40px;background-color:#728EFD;color:rgb(255,255,255);\">".strtoupper(_tr("Texts")->clear)." </button>
                            <button class=\"btn btn-primary\" type=\"submit\" style=\"width:160px;height:40px;background-color:#13297D;\">".strtoupper(_tr("Texts")->save)." </button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </article>";
    }
    public function ui_edit_user(){
        $matriculaReceived = $_GET[_lastAction];
        $getDataUSer = $this->getSql()->query("SELECT * FROM ".__USERS." WHERE matricula='".$matriculaReceived."'");
        if($getDataUSer->rowCount() == 0){
            Kernel::redirect(_index._admin.DS._employee);
        }
        else{
            $StreamReceive = $getDataUSer->fetch(PDO::FETCH_ASSOC);

            echo "<article class=\"d-flex justify-content-center\" style=\"height:50px;background-color:#152462;padding-top:9px;\">
        <div></div>
        <h4 style=\"color:rgb(255,255,255);letter-spacing:2px;font-weight:bold;padding-top:-12px;\">"._tr("Texts")->edit_user." </h4>
    </article>";
            $this->ui_back(_index._admin.DS._employee);
            self::showMessage();
            echo "
    <div id='propag_message'></div>
    <article style=\"margin-top:10px;height:auto;\">
        <div class=\"container\" style=\"height:auto;\">
            <div class=\"row\" style=\"height:auto;\">
                <div class=\"col\">
                    <form action=\"".DS._admin.DS._employee.DS._edit.DS._make."\" requisition=\"".DS._requisition.DS._employee.DS._edit."\" id=\"cadastro_func\" class=\"form\" method=\"post\" enctype='multipart/form-data'>
                       
                        <div class=\"d-flex justify-content-between flex-wrap col-12\">
                            <div class=\"col-12\">
                                <h2 class=\"info_cadas\">"._tr("Texts")->personal_information."</h2>
                            </div>
                            <div class=\"form-group col-md-4 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->name.": </label>
                                <input name=\"nome\" class=\"form-control\" type=\"text\" autofocus value='".$StreamReceive['nome']."'>
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->enroller.": </label>
                                <input name=\"matriculaN\" disabled class=\"form-control\" type=\"text\" title='"._tr("Texts")->not_editable."' value='".$StreamReceive['matricula']."'>
                                <input name=\"matricula\" type=\"hidden\" value='".$StreamReceive['matricula']."'>
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->email.": </label>
                                <input name=\"email\" class=\"form-control\" type=\"text\" value='".$StreamReceive['email']."'>
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->cpf.": </label>
                                <input name=\"cpf\" class=\"form-control\" type=\"text\" value='".$StreamReceive['cpf']."'>
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->rg.": </label>
                                <input name=\"rg\" class=\"form-control\" type=\"text\" value='".$StreamReceive['rg']."'>
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->work_wallet.":</label>
                                <input name=\"ctps\" class=\"form-control\" type=\"text\" value='".$StreamReceive['ctps']."'>
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->phone." 1: </label>
                                <input attrname=\"telephone1\" name=\"tel1\" class=\"form-control\" 
                                pattern=\"\([0-9]{2}\)[\s][0-9]{4}-[0-9]{4,5}\" type=\"text\" value='".$StreamReceive['tel1']."'>
                            </div>

                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->phone." 2: </label>
                                <input name=\"tel2\" class=\"form-control\" type=\"text\" value='".$StreamReceive['tel2']."'>
                            </div>
                        </div>

                        <div class=\"d-flex justify-content-between flex-wrap col-12\">
                            <div class=\"col-12\">
                                <h2 class=\"info_cadas\">"._tr("Texts")->bank_data."</h2>
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->bank.": </label>
                                <input name=\"banco\" class=\"form-control\" type=\"text\" value='".$StreamReceive['banco']."'>
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->agency.": </label>
                                <input name=\"agencia\" class=\"form-control\" type=\"text\" value='".$StreamReceive['agencia']."'>
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->account.": </label>
                                <input name=\"conta\" class=\"form-control\" type=\"text\" value='".$StreamReceive['conta']."'>
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->operation.": </label>
                                <input name=\"op\" class=\"form-control\" type=\"text\" value='".$StreamReceive['operacao']."'>
                            </div>
                        </div>

                        <div class=\"d-flex justify-content-start flex-wrap col-12\">
                            <div class=\"col-12\">
                                <h2 class=\"info_cadas\">"._tr("Texts")->access_information." & "._tr("Texts")->extras."</h2>
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->login.": </label>
                                <input name=\"login\" class=\"form-control\" type=\"text\" value='".$StreamReceive['login']."'>
                            </div>
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label>"._tr("Texts")->password.": </label>
                                <input name=\"senha\" class=\"form-control\" type=\"text\" value='".$StreamReceive['senha']."'>
                            </div>
                            
                            <div class=\"form-group col-md-3 col-sm-6 col-xl-3\">
                                <label for=\"avatar\">"._tr("Texts")->user_image.": </label>
                                <input id=\"avatar\" name=\"avatar\" class=\"form-control-file\" type=\"file\">
                               
                            </div>
                        </div>

                        <div class=\"btn-group d-flex justify-content-between\" role=\"group\" style=\"width:100%;padding-left:32%;padding-right:32%;margin-top:30px;margin-bottom:30px;\">
                           <a href='".DS._admin.DS._employee.DS._remove.DS.$StreamReceive['matricula']."'> <button class=\"btn btn-danger\" type=\"button\" style=\"margin:auto;width:200px;height:40px;\">"._tr("Texts")->remove_employee." </button></a>
                            <button class=\"btn btn-primary\" type=\"submit\" style=\"margin:auto;width:160px;height:40px;background-color:#13297D;\">".strtoupper(_tr("Texts")->save)." </button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </article>";
        }

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
    public function ui_visualize_category($method = 'register'){

        //premontagem de variaveis
        $nameValue = "";
        $defaultAction = DS._admin.DS._category.DS._main.DS._make;
        //caso o método aplicado não seja de cadastro, realiza a operação de edição ou remoção!
        if($method != 'register'){
            $StreamEditCategoryQuery = $this->getSql()->prepare("SELECT * FROM ".__CATEGORY." WHERE id=?");
            $StreamEditCategoryQuery->bindValue(1,$_GET[_lastAction]);
            $StreamEditCategoryQuery->execute();
            $StreamEditFetch = $StreamEditCategoryQuery->fetch(PDO::FETCH_ASSOC);
            $nameValue =$StreamEditFetch['nome'];
            $defaultAction = DS._admin.DS._category.DS._edit.DS.$_GET[_lastAction].DS._make;
        }
        echo "<article class=\"d-flex justify-content-center\" style=\"height:50px;background-color:#152462;padding-top:9px;\">
        <div></div>
        <h4 style=\"color:rgb(255,255,255);letter-spacing:2px;font-weight:bold;padding-top:-12px;\">"._tr("Texts")->category." </h4>
    </article>";
        $this->showMessage();
        if($method == 'register')
            self::ui_back(DS._admin.DS);
        else
            self::ui_back(DS._admin.DS._category);

    echo"
    <article style=\"margin-top:10px;height:auto;\">
        <div class=\"container\" style=\"height:auto;\">
            <div class=\"row\" style=\"height:auto;\">
                <div class=\"col\"></div>
                <div class=\"col\">
                    <form enctype='application/x-www-form-urlencoded' method='post' action='".$defaultAction."'>
                        <div class=\"form-group\">
                            <label>"._tr("Texts")->category."</label>
                            <input class=\"form-control\" type=\"text\" name='nome' autofocus=\"\" value='".$nameValue."'>";
                            if($method != 'register'){
                                echo "<label>"._tr("Texts")->color."</label>
                                <input class=\"form-control\" type=\"color\" name='cor' value='".$StreamEditFetch["cor"]."' style=\"width:20%;height:43px;\" >";
                            }
                            echo "<div class=\"btn-group d-flex justify-content-between\" role=\"group\" style=\"width:100%;margin-top:30px;\">";
                               if($method == 'register'){
                                   echo " <button class=\"btn btn-primary\" type=\"reset\" style=\"width:160px;height:40px;background-color:#728EFD;color:rgb(255,255,255);\">"._tr("Texts")->clear." </button>";
                               }
                               else{
                                   echo " <a href='".DS._admin.DS._category.DS._remove.DS.$_GET[_lastAction].DS."'><button class=\"btn btn-secondary\" type=\"button\" style=\"width:160px;height:40px;color:rgb(255,255,255);\">"._tr("Texts")->delete." </button></a>";
                               }
                                echo "<button class=\"btn btn-primary\" type=\"submit\" style=\"width:160px;height:40px;background-color:#13297D;\">"._tr("Texts")->save." </button>
                            </div>
                        </div>
                    </form>";
        if($method == 'register'){
            echo "
            <table class=\"table table-responsive\" style=\"width:100%;\">
                <thead style=\"width:100%;\">
                    <tr>
                        <th style=\"width:70%;\">"._tr("Texts")->name." </th>
                        <th style=\"width:20%;\">"._tr("Texts")->color." </th>
                        <th style=\"width:20%;\"> </th>
                    </tr>
                </thead>
                <tbody style=\"width:100%;\">";
                self::visualizarCategoria();
            echo"                        
                </tbody>
                        </table>";
        }
            echo"
                    </div>
                    <div class=\"col\"></div>
                </div>
            </div>
            </article>";

    }
    public function ui_remove_category(){
        self::ui_back(DS._admin.DS._category);
        echo "<article>
                <div class='container'>
                    <div class='row'>
                        <div class='col'></div>
                        <div class='col'>";

                        self::prompt_remove_category();
                        self::showMessage();

        echo "          </div>
                        <div class='col'></div>
                    </div>
                </div>
            </article>";
    }
    public function ui_send_messages($isDoc = false){
        $titlePage = _tr("Texts")->send_message;
        $defaultActionForm = DS._admin.DS._send.DS._msg.DS._make;
        if($isDoc){
            $titlePage = _tr("Texts")->send_document;
            $defaultActionForm = DS._admin.DS._send.DS._doc.DS._make;
        }
        //Chamada para listagem de categorias
        $getAllCategorysQuery = $this->getSql()->query("SELECT * FROM ".__CATEGORY);
        //Chamada para listagem de usuários
        $SteamgetAllUsersQuery = $this->getSql()->query("SELECT * FROM ".__USERS." WHERE tipo=2");
        echo "
    <article class=\"d-flex justify-content-center\" style=\"height:50px;background-color:#152462;padding-top:9px;\">
        <h4 style=\"color:rgb(255,255,255);letter-spacing:2px;font-weight:bold;padding-top:-12px;\">".strtoupper($titlePage)."</h4>
    </article>";
        self::showMessage();
        echo"
    <article style=\"margin-top:5px;margin-bottom:15px;\">
        <div class=\"container\">".self::ui_back(DS._admin.DS)."</div>
    </article>";

        echo "
    <article style=\"margin-top:10px;height:auto;\">
        <div class=\"container\" style=\"height:auto;\">
            <div class=\"row\" style=\"height:auto;\">
                <div class=\"col\"></div>
                <div class=\"col\">
                    <form action='".$defaultActionForm."' class=\"d-flex justify-content-between flex-wrap\" enctype='multipart/form-data' method=\"post\" style=\"width:700px;\">
                        <div class=\"form-group\" style=\"width:45.5%;\">
                            <label>"._tr("Texts")->title.": </label>
                            <input class=\"form-control\" type=\"text\" name='titulo'>
                        </div>
                        <div class=\"form-group\" style=\"width:45.5%;\">
                            <label>"._tr("Texts")->category.": </label>
                            <select class=\"form-control\" name='categoria'>
                                <optgroup label=\"\">
                                    <option value=\"\" selected=\"\">"._tr("Texts")->not_defined."</option>";
                                while($dataStreamCategoty = $getAllCategorysQuery->fetch(PDO::FETCH_ASSOC)){
                                    echo "<option value=\"".$dataStreamCategoty['id']."\">".$dataStreamCategoty['nome']."</option>";
                                }
        echo"
                                </optgroup>
                            </select>
                        </div>
                        <div class=\"form-group\" style=\"width:100%;\">
                            <label>"._tr("Texts")->employees.": <h6>".sprintf(_tr("Infos")->use_key_to_select_multiple_employees,"<kbd>Ctrl + Mouse</kbd>")."</h6></label>
                            <select multiple class='form-control' name='usuarios[]'>";
                                if($SteamgetAllUsersQuery->rowCount() ==0){
                                    echo"<option disabled value='N/D'>"._tr("Texts")->not_have_users."</option>";
                                }
                                else{
                                    echo"                        
                                    <option value='N/D'>"._tr("Texts")->not_defined."</option>
                                    <option value='all'>"._tr("Texts")->all."</option>";
                                }
                                while($userList = $SteamgetAllUsersQuery->fetch(PDO::FETCH_ASSOC)){
                                    echo "<option value='".$userList['matricula']."'>".$userList['nome']."</option>";
                                }
                                echo"
                                        
                            </select>
                        </div>
                        <div class=\"form-group\" style=\"width:100%;\">
                            <label>"._tr("Texts")->comment."</label>
                            <textarea class=\"form-control form-control-lg\" name='mensagem' style=\"height:100px;\"></textarea>
                        </div>";
                        if($isDoc){
                            echo "<input type=\"file\" name='doc'>";
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
    public function ui_report(){
        //Querys
        $sqlStreamCategory = $this->getSql()->query("SELECT id,nome FROM ".__CATEGORY);
        $sqlStreamUser = $this->getSql()->query("SELECT matricula, nome FROM ".__USERS." WHERE tipo !='1'");

        echo "";
        self::ui_back(DS._admin);
        echo"
            <article style=\"margin-top:40px\" class=\"container d-flex justify-content-center\">
        
        <div class=\"col-8 \">
            <form class=\"d-flex flex-wrap\" action=\"".DS._admin.DS._report."\" method=\"POST\">

                <div class=\"form-group col-md-4 col-sm-12 col-xl-4 \">
                    <label>"._tr("Texts")->category.":</label>
                    <select class=\"form-control\" name='categoriaFiltro' >
                        <option value=\"\" selected=\"selected\">"._tr("Texts")->not_defined."</option>";
                        while($fetchDisposeCat = $sqlStreamCategory->fetch(PDO::FETCH_ASSOC)){
                           echo "<option value=\"".$fetchDisposeCat['id']."\" >".$fetchDisposeCat['nome']."</option>" ;
                        }
                        echo "               
                    </select>
                </div>
                <div class=\"form-group  col-md-4 col-sm-12 col-xl-4\">
                    <label>Funcionário: </label>
                    <select class=\"form-control\" name='usuarioFiltro'>
                         <option value=\"\" selected=\"selected\">"._tr("Texts")->not_defined."</option>";
                        while($fetchDisposeUser = $sqlStreamUser->fetch(PDO::FETCH_ASSOC)){
                           echo "<option value=\"".$fetchDisposeUser['matricula']."\" >".$fetchDisposeUser['nome']."</option>" ;
                        }
                    echo"</select>
                </div>
                <div class=\"form-group  col-md-4 col-sm-12 col-xl-4\">
                    <label>"._tr("Texts")->title.": </label>
                    <input class=\"form-control\" type=\"text\" name='tituloPesquisa'>
                </div>
                <div style=\"margin-top:20px\" class=\"d-flex justify-content-center col-md-2 col-sm-12 col-xl-12\">
                    <button class=\"btn btn-primary float-right\" type=\"submit\" style=\"width:160px;height:40px;background-color:#13297D;\">".strtoupper(_tr("Texts")->search)."</button>
                </div>

            </form>
            <div>

            </div>
        </div>
    </article>";
        echo"<div class=\"container\" style=\"height:auto;\">
            <div class=\"row\" style=\"height:auto;\"> ";
        self::gerarRelatorio();
        echo "</div></div>";
    }
    public function ui_remove_user(){
        self::ui_back(DS._admin.DS._employee.DS._edit.DS.$_GET[_lastAction]);
        echo "<article>
                <div class='container'>
                    
                    <div class='row'>
                         <div class='col'></div>
                        <div class='col'><br/>";

                        self::prompt_remove_user();
                        self::showMessage();

        echo "          </div><div class='col'></div>
                        
                    </div>
                </div>
            </article>";
    }
}
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
    public function cadastrarFuncionario(){
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
            //var_dump($_FILES);
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
            else if(strlen($this->getMatriculation()) > 12){
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
            else{

                $blobImage = $this->getAvatar()['error'] != 4 ? file_get_contents($this->getAvatar()['tmp_name']):
                    file_get_contents("assets/img/user.png");
                $mimeFileType =$this->getAvatar()['error'] != 4 ? mime_content_type($this->getAvatar()['tmp_name']) :
                    mime_content_type("assets/img/user.png");
                $dataFileBase64 = base64_encode($blobImage);
                $dataImage = "data:".$mimeFileType.";charset=utf-8;base64,".$dataFileBase64;
                ;
                //15 entradas, sendo 2 opcionais (telefone 2, campo de arquivo de foto)
                $insertNewData = $this->getSql()->prepare("INSERT INTO ".__USERS." (nome, matricula,senha,email,rg,cpf,login,operacao,banco,agencia,conta,tel1,tel2,ctps,tipo,`status`,avatar,image) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $insertNewData->bindValue(1, $this->getName());
                $insertNewData->bindValue(2, $this->getMatriculation());
                $insertNewData->bindValue(3, Kernel::getPwdPattern($this->getPasswd()));
                $insertNewData->bindValue(4, $this->getEmail() );
                $insertNewData->bindValue(5, $this->getRg() );
                $insertNewData->bindValue(6, $this->getCpf() );
                $insertNewData->bindValue(7, $this->getLogin());
                $insertNewData->bindValue(8, $this->getOperation());
                $insertNewData->bindValue(9, $this->getBank());
                $insertNewData->bindValue(10, $this->getAgency());
                $insertNewData->bindValue(11, $this->getAccount() );
                $insertNewData->bindValue(12, $this->getPhone() );
                $insertNewData->bindValue(13, $this->getPhoneAlt());
                $insertNewData->bindValue(14, $this->getCtps());
                $insertNewData->bindValue(15, 2);//tipo de usuário
                $insertNewData->bindValue(16, 1);//Status
                $insertNewData->bindValue(17, $this->getAvatar()['name']);//Imagem
                $insertNewData->bindValue(18,$dataImage);
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
                $this->setShowMessage("<div class='alert alert-danger' role='alert'><strong>"._tr("Texts")->fatal."</strong> "._tr("Infos")->internal_server_error." "._tr("Texts")->code.": ".$e->getCode()."</div>");
        }

    }
    public function visualizarFuncioarios(){

        try {
            $Pagination = new Pagination(__USERS, "id", $_GET[_subAction]);
            $mainStreamReaderQuery = $this->getSql()->query("SELECT * FROM " . __USERS . " WHERE tipo='2' " . $Pagination->getProcessLimit());
            if ($mainStreamReaderQuery->rowCount() == 0) {
                echo "
                    <div class='col'></div>
                    <div class=\"col\">
                        <div class='illustration'> 
                            <i class='icon ion-person-stalker'></i>
                        </div>
                        
                        <h4 class='text-center'>" . _tr("Infos")->none_employees_registered . "</h4>             
                    </div>
                    <div class='col'></div>
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
            $this->setShowMessage("<h3 class='text-danger text-center mt-5'>"._tr("Texts")->warning."</h3>
                <p class='text-center pt-2'>"._tr("Errors")->category_id_not_found."</p>");
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
    public function visualizarCategoria(){
        $getStreamOfQuery = $this->getSql()->query("SELECT * FROM ".__CATEGORY);

        while($IOstream = $getStreamOfQuery->fetch(PDO::FETCH_ASSOC)){
            echo "<tr>
                <td>".$IOstream['nome']." </td>
                <td style=\"padding:0px;height:40px;\">
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
        else{
            //Organiza a lista de usuário a visualizar a mensagem
            $lista_matricula = json_encode($this->getSendTo());
            if(in_array("all", $this->getSendTo())){
                $lista_matricula =  array('all');
                $lista_matricula = json_encode($lista_matricula);
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
            $sendMessageQuery->bindValue(5,$lista_matricula);
            $sendMessageQuery->bindValue(6,$docName);
            $sendMessageQuery->bindValue(7, $type);
            $sendMessageQuery->execute();

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

    public function visualizaMsg($isDoc = false){

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
                            <a href='".DS._admin.DS._report.DS."'>
                                <div style=\"height:20%;\">
                                    <button class=\"btn btn-primary\" type=\"button\" style=\"width:100%;background-color:#13297D;font-size:18px;\">".strtoupper(_tr("Texts")->report)." </button>
                                </div>
                            </a>
                        </div>
                        <div style=\"width:46.5%;margin-top:30px;\">
                            <div style=\"height:80%;\">
                                <button class=\"btn btn-light\" type=\"button\" style=\"width:100%;height:100%;background-color:rgb(255,255,255);\">
                                    <img src=\"/assets/img/register.svg\" width=\"100px\">
                                </button>
                            </div>
                            <a href='".DS._admin.DS._employee.DS._register.DS."'>
                                <div style=\"height:20%;\">
                                    <button class=\"btn btn-primary\" type=\"button\" style=\"width:100%;background-color:#13297D;font-size:18px;\">".strtoupper(_tr("Texts")->register)." </button>
                                </div>
                            </a>
                        </div>
                        <div style=\"width:46.5%;margin-top:30px;\">
                            <div style=\"height:80%;\">
                                <button class=\"btn btn-light\" type=\"button\" style=\"width:100%;height:100%;background-color:rgb(255,255,255);\">
                                    <img src=\"/assets/img/four-black-squares.svg\" width=\"100px\">
                                </button>
                            </div>
                            <a href='".DS._admin.DS._category.DS."'>
                                <div style=\"height:20%;\">
                                    <button class=\"btn btn-primary\" type=\"button\" style=\"width:100%;background-color:#13297D;font-size:18px;\">".strtoupper(_tr("Texts")->category)." </button>
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
                    <form action=\"".DS._admin.DS._employee.DS._register.DS._make."\" id=\"cadastro_func\" class=\"form\" method=\"post\" enctype='multipart/form-data'>
                       
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
                                    echo "<option value=\"".$dataStreamCategoty['id']."\" selected=\"\">".$dataStreamCategoty['nome']."</option>";
                                }
        echo"
                                </optgroup>
                            </select>
                        </div>
                        <div class=\"form-group\" style=\"width:100%;\">
                            <label>"._tr("Texts")->employees.": <h6>".sprintf(_tr("Infos")->use_key_to_select_multiple_employees,"<kbd>Ctrl + Mouse</kbd>")."</h6></label>
                            <select multiple class='form-control' name='usuarios[]'>
                                <option value='N/D'>"._tr("Texts")->not_defined."</option>
                                <option value='all'>"._tr("Texts")->all."</option>";
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
        $sqlStreamCategory = $this->getSql()->prepare("SELECT nome FROM ".__CATEGORY);
        $fetchDisposeCat = $sqlStreamCategory->fetch(PDO::FETCH_ASSOC);

        echo "<article style=\"margin-top:40px\" class=\"container d-flex justify-content-center\">
        <div class=\"col-8 \">
            <form class=\"d-flex flex-wrap\" action=\"\" method=\"POST\">

                <div class=\"form-group col-md-4 col-sm-12 col-xl-4 \">
                    <label>Categoria: </label>
                    <select class=\"form-control\">
                        <optgroup label=\"This is a group\">
                            <option value=\"12\" selected=\"\">This is item 1</option>
                            <option value=\"13\">This is item 2</option>
                            <option value=\"14\">This is item 3</option>
                        </optgroup>
                    </select>
                </div>
                <div class=\"form-group  col-md-4 col-sm-12 col-xl-4\">
                    <label>Funcionário: </label>
                    <select class=\"form-control\">
                        <optgroup label=\"This is a group\">
                            <option value=\"12\" selected=\"\">This is item 1</option>
                            <option value=\"13\">This is item 2</option>
                            <option value=\"14\">This is item 3</option>
                        </optgroup>
                    </select>
                </div>
                <div class=\"form-group  col-md-4 col-sm-12 col-xl-4\">
                    <label>Título: </label>
                    <input class=\"form-control\" type=\"text\">
                </div>
                <div style=\"margin-top:20px\" class=\"d-flex justify-content-center col-md-2 col-sm-12 col-xl-12\">
                    <button class=\"btn btn-primary float-right\" type=\"button\" style=\"width:160px;height:40px;background-color:#13297D;\">PESQUISAR</button>
                </div>

            </form>
            <div>

            </div>
        </div>
    </article>";
    }
}
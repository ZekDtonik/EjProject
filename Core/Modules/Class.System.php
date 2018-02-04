<?php
/**
 * Created by MarxMedia Solutions.
 *    Author: Kevin Marques
 *    Date: 02/04/2016 - 22:25
 *      File: Class.System.php
 *              Acctos
 */


namespace Classes;

use PDO;
use Classes\Data;

class System
{
    public static function noScript()
    {
        echo "<noscript>
        <div id='fogBox'>
            <div id='noscriptWarning'>
                <div id='logo' class='separator'></div> 
                <div id='warningText'>
                    <h1>" . _tr(javascript_disabled) . "</h1>
                    <p>" . _tr(app_unable_to_work_properly_without_javascript) . "</p>
                    <span>" . _tr(reload_app_to_proceed) . "</span>
                </div>
            </div>
        </div></noscript>";
    }
    //Cria as sub pastas do sistema de arquivo automaticamente de cada instituição
    //facilitando a manutenção e instalação automatizada de cada instituição iniciada
    //no sistema... As pastas são criadas no primeiro uso por qualquer usuario
    //e recriadas caso removidas por algum motivo

    /**
     * Função Super util de mascaras para valores especificos
     * Explicando:
     * Popula chave por chave da string passada, se a string $mask
     * que é o tipo da mascara a ser mostrada existir a tralha(#)
     * ele verifica se o valor chave do $val informado esta preenchido
     * colocando o valor no lugar, se nao existir a tralha ele coloca o mesmo simbolo
     * especificado na $mask
     */
    public static function mask($val, $mask)
    {
        $maskared = '';
        $k = 0;

        if (strlen($val) > 10 and $mask === "_tel") {
            $mask = "(##) # ####-####";
        } elseif (strlen($val) < 10 and $mask === "_tel") {
            $mask = "(##) ####-####";
        } elseif ($mask === "_cpf") {
            $mask = "###.###.###-##";
        } elseif ($mask === "_rg") {
            $mask = "##.###.###-##";
        }
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k]))
                    $maskared .= $val[$k++];
            } else {
                if (isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
        return $maskared;
    }

    public static function money($value)
    {
        return _tr(default_coin) . ' ' . number_format($value, 2, ',', '.');
    }

    public static function forMoney($money)
    {
        $endMoney = preg_replace('/([^0-9])/', '.', preg_replace('/([^0-9\,])/', '', $money));

        return $endMoney;
    }

    public static function shift($shift)
    {

        switch ($shift) {
            case 1:
                return _tr(matutinal);
            break;
            case 2:
                return _tr(vespertine);
            break;
            case 3:
                return _tr(nightly);
            break;
            case 4:
                return _tr(other);
            break;
        }
    }

    public static function specialNeeds($deficiency, $class = null)
    {
        switch ($class) {
            case "sense":
                switch ($deficiency) {
                    case 1:
                        $return = _tr(visual);
                    break;
                    case 2:
                        $return = _tr(auditory);
                    break;
                    case 3:
                        $return = _tr(gustatoy);
                    break;
                    case 4:
                        $return = _tr(olfactory);
                    break;
                    case 5:
                        $return = _tr(touch);
                    break;
                    default:
                        $return = _tr(none);
                }
            break;
            case "type":
                switch ($deficiency) {
                    case 1://fisica
                        $return = _tr(physics);
                    break;
                    case 2://mental
                        $return = _tr(mental);
                    break;
                    case 3://fisica e mental
                        $return = _tr(physics_and_mental);
                    break;
                    default:
                        $return = _tr(none);
                    break;
                }
            break;
            default:
                switch ($deficiency) {
                    case 1://fisica
                        $return = _tr(yes);
                    break;
                    default:
                        $return = _tr(no);
                    break;
                }
        }
        return $return;
    }

    //publica indicação, de turno com retorno simples

    public static function GridTypeSchool($val)
    {
        if ($val == 1) {
            $data = _tr(school_public);
        } else if ($val == 2) {
            $data = _tr(school_private);
        } else {
            $data = _tr(no_inform);
        }

        return $data;
    }

    public static function setLoadingJS()
    {
        echo "<script>
        window.addEventListener('load',function(){
            var loader = document.getElementsByClassName('loader');
            loader[0].style.opacity = 0;
            loader[0].style.visibility = 'hidden';
        } ) ;
    </script>";
    }

    public static function setLoading($type = null)
    {
        switch ($type) {
            case 'acctos':
                echo "<div class=\"loader\"><div class='acmain acmain64'><p id='aa_up'></p><p id='aa_down'></p></div></div>";
            break;
            default:
                echo "<div class=\"loader\"><div class=\"loading loading64\"></div><h2>" . _tr(loading) . "</h2></div>";
            break;
        }
    }

    public static function randomIdUser()
    {
        $StrPattern = "abcdefghijklmnopqrstuvxwyz0123456789";
        $patternInit = substr(strtoupper(sha1(uniqid())), 0, 4);
        $patternCapEnd = substr(str_shuffle(strtoupper($StrPattern)), 0, 4);
        $pattern = str_shuffle($patternInit . $patternCapEnd);
        return $pattern;
    }

    public static function getWebStorage($key)
    {
        echo "<script>document.write(localStorage.getItem('" . $key . "'));</script>";
    }

    /** @Param Function Static Este metódo envia ao lado cliente em json todos os dados em json */
    public static function sendSettings($true = false)
    {
        $sql = CNN::getSql();
        $initOperation = $sql->query("SELECT * FROM " . __SETTINGS);
        $getDataFromDb = $initOperation->fetch(PDO::FETCH_ASSOC);
        $settings = json_encode($getDataFromDb);
        if (!$true) {
            self::setWebStorage('settings', $settings);
        } else {
            echo $settings;
        }

    }

    public static function setWebStorage($key, $value)
    {
        if (!isset($key) or !isset($value)) {
            return false;
        } else {
            echo "<script>window.localStorage.setItem('" . $key . "','" . $value . "');</script>";
        }
    }

    /* @function  Define Tipo de padrao de senha, (usado em entrada de dados) */
    public static function passPattern($pwd = null, $showout = null)
    {
        /*
        *Nivel de usuario de acordo ao valor, uma verificação em
        * preg_match é definida pelo sistema baseado em Settings
        *
        * Nivel 1 = pelo menos uma maiuscula
        * Nivel 2 = pelo menos um numero e uma maiuscula
        * nivel 3 = pelo menos 1 numero, uma maiuscula e um simbolo
        * Simbolos aceitos = @ $ & # % .
        *  */
        $Sql = CNN::getSql();

        $getPassType = $Sql->query("SELECT pwd_pattern FROM " . __SETTINGS);
        $getPwdStatus = $getPassType->fetch(PDO::FETCH_OBJ);
        //se showout tiver ativo, informa texto especifico do erro, se estiver nulo, emite boolean
        if ($showout != true) {
            $patternOne = "/^.*(?=.*[a-z]?)(?=.*[A-Z]+)(?=[@$#\.&%]*).*$/";
            $patternTwo = "/^.*(?=.*\d)(?=.*[a-z]?)(?=.*[A-Z]+)(?=.*[@$#\.&%]*).*$/";
            $patternTree = "/^.*(?=.*\d)(?=.*[a-z]?)(?=.*[A-Z]+)(?=.*[@$#\.&%]+).*$/";

            switch ($getPwdStatus->pwd_pattern) {
                case 1:
                    if (preg_match($patternOne, $pwd)) {
                        return $rs = true;
                    } else {
                        return $rs = false;
                    }
                break;
                case 2:
                    if (preg_match($patternTwo, $pwd)) {
                        return $rs = true;
                    } else {
                        return $rs = false;
                    }
                break;
                case 3:
                    if (preg_match($patternTree, $pwd)) {
                        return $rs = true;
                    } else {
                        return $rs = false;
                    }
                break;
                default;
            }
        } else {
            switch ($getPwdStatus->pwd_pattern) {
                case 1:
                    return _tr(at_least_one_capital_letter);//pelo menos uma letra maiuscula
                break;
                case 2:
                    return _tr(at_least_one_capital_letter_and_one_number);//pelo menos uma letra maiuscula e um numero
                break;
                case 3:
                    return _tr(at_least_one_capital_letter_one_number_and_one_symbol);//pelo menos uma letra maiuscula, numero e um simbolo
                break;
                default;
            }
        }
    }

    /** @function  retorna o tipo de criptografia da senha de usuários baseado na definição de configuração! */
    public static function pwdEncryptType($pwd, $forceDefault = null)
    {

        /*VERIFICAÇÃO DO TIPO DE ENCRIPTAÇÃO DO SISTEMA
        NÃO PERMITTIR TROCA DE MODO APOS A INSERÇÃO DE DADOS,
        POIS COMPROMETERÁ O SISTEMA DE SENHAS,
        NECESSITANDO SER ALTERADO GLOBALMENTE!
        */
        $sql = CNN::getSql();

        $define = $sql->query("SELECT * FROM " . __SETTINGS);
        $ftDefine = $define->fetch(PDO::FETCH_ASSOC);
        $encrypt = $forceDefault == true ? 0 : $ftDefine['encrypt_type'];
        switch ($encrypt) {
            case 1:
                return md5($pwd);
            break;
            case 2:
                return sha1($pwd);
            break;
            case 3:
                return md5(sha1($pwd));
            break;
            default:
                /*Cuidado: utilização apenas para desenvolvimento e testes
                  a falta de encriptação pode comprometer os dados do usuario final.
                Use com atenção!*/
                return $pwd;

        }
    }

    /** @function  Retorna formatado a ocorrencia do estado de acesso da conta do usuario (lowLevel) */
    public static function userStatus($sStatus)
    {
        $infoStatus = null;
        switch ($sStatus) {
            case 1:
                $infoStatus = " " . _tr(violation_of_terms_of_use);
            break;
            case 2:
                $infoStatus = " " . _tr(privacy_police);
            break;
            case 3:
                $infoStatus = " " . _tr(the_parent_request);
            break;
            case 4:
                $infoStatus = " " . _tr(requested_by_user);
            break;
            default:
                null;
        }

        return $infoStatus;
    }

    /* @function Função basica que converte segundos e horario humano */
    public static function CountSeconds($TotalSeconds)
    {
        $return = null;
        $hora = 0;
        $minutos = 0;
        $seconds = 0;
        if ($TotalSeconds <= 60) {
            $return = $TotalSeconds;
        } else if ($TotalSeconds > 60) {
            $minutos = (int)($TotalSeconds / 60);
            $seconds = (int)($TotalSeconds % 60);
            if ($minutos >= 60) {
                $hora = (int)($minutos / 60);
                $minutos = (int)($hora % 60);
                $seconds = $minutos % 60;
            }
        }
        $zh = null;
        $zm = null;
        $zs = null;
        if ($hora < 10) {
            $zh = "0";
        }
        if ($minutos < 10) {
            $zm = "0";
        }
        if ($seconds < 10) {
            $zs = "0";
        }
        if ($hora > 0) {

            $return = $zh . $hora . ":" . $zm . $minutos . ":" . $zs . $seconds;
        } else {
            if ($minutos > 0)
                $return = $zm . $minutos . ":" . $zs . $seconds;
            else
                $return = $zs . $return;
        }

        return $return;
    }

    public static function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    public static function getHashUserAgent()
    {
        return md5(trim($_SERVER['HTTP_USER_AGENT']));
    }

    /* @function tem a capacidade de gerar um token aleatorio de escolha especifica */
    public static function createToken($length, $onlyNumbers = false, $withSimbols = false)
    {
        empty($length) ? $length = 5 : $length;
        $pattern = [];
        $pattern['simbols'] = "@$#%&()_.=+-";
        $pattern['numbers'] = "0123456789";
        $pattern['default'] = "abcdefghijklmnopqrstuvwxyz";

        if ($onlyNumbers) {
            if ($withSimbols == true) {
                $shuffle = str_shuffle($pattern['numbers'] . $pattern['simbols']);
            } else {
                $shuffle = str_shuffle($pattern['numbers']);
            }
            $shuffle = str_split($shuffle, $length)[0];
        } else {
            if ($withSimbols == true) {
                $shuffle = str_shuffle($pattern['numbers'] . $pattern['default'] . $pattern['simbols']);
            } else {
                $shuffle = str_shuffle($pattern['numbers'] . $pattern['default']);
            }
            $shuffle = str_split($shuffle, $length)[0];
        }

        return $shuffle;
    }

    public static function ipLookup($ip)
    {
        try {
            if (!isset($ip)) {
                //emite um erro de nenhum ip detectado
                // código do erro LG-0100
                throw new \Exception(_tr(internal_error_none_ip_defined));
            } else {
                //Inicia um Curl
                $progress = curl_init();
                //
                curl_setopt($progress, CURLOPT_URL, "http://ip-api.com/json/" . $ip);
                curl_setopt($progress, CURLOPT_RETURNTRANSFER, 1);
                $jsonData = curl_exec($progress);

                $data = json_decode($jsonData, true);
                return $data;
            }
        } catch (\Exception $e) {
            echo "<div class='Bar False'>" . $e->getMessage() . "</div>";
        }
    }

    /** @function Cria uma função que define ou detectar uma sessao para bloquear ou manter
     * uma area especifica*/
    public static function blocked($id, $link = null, $bl_action = 'write')
    {
        if ($bl_action == "write") {
            $_SESSION[$id] = true;
        } elseif ($bl_action == "read") {
            if (!isset($_SESSION[$id])) {
                System::Redirect($link);
            }
        } elseif ($bl_action == "erase") {
            unset($_SESSION[$id]);
        }
    }

    /** @function Sistema independente de redirecionamento
     * @important USA JAVASCRIPT (metodos ajax comuns não recebem javascript como código)
     * @inheritdoc usar jquery que faz o tratamento do texto
     */
    public static function Redirect($place, $json = false)
    {
        if ($json == false) {
            switch ($place) {
                case 'byLevel':
                    $datauser = Access::getUserData();
                    $level = $datauser['login_level'];
                    if ($level == 1) {
                        System::Redirect('adm');
                    } else {
                        System::Redirect('usr');
                    }
                break;
                case 'tfa'://Two Factor Auth
                    echo "<script type='text/javascript'>document.location.href=\"" . SESLOC . "\";</script>";
                break;
                case 'log':
                    echo "<script type='text/javascript'>document.location.href=\"" . SIGLOC . "\";</script>";
                break;
                case 'adm':
                    echo "<script type='text/javascript'>document.location.href=\"" . ADMLOC . "\";</script>";
                break;
                case 'usr':
                    echo "<script type='text/javascript'>document.location.href=\"" . USRLOC . "\";</script>";
                break;
                case 'blk':
                    echo "<script type='text/javascript'>document.location.href=\"" . USRLOC . "\";</script>";
                break;
                case 'exp':
                    echo "<script type='text/javascript'>document.location.href=\"" . EXPLOC . "\";</script>";
                break;
                default:
                    echo "<script type='text/javascript'>document.location.href=\"" . $place . "\";</script>";
                break;
            }
        } else {
            $res = [];
            $res['url'] = $place;
            $json = json_encode($res);
            echo $json;
        }
    }

    public static function change_title($title, $description = null, $keywords = null)
    {
        // This function made by Jamil Hammash
        error_reporting(E_ALL);
        ob_start();
        $output = ob_get_contents();
        if (ob_get_length() > 0) {
            ob_end_clean();
        }
        $patterns = "/<title>(.*?)<\/title>/";
        //,"<meta name='description' content='(.*)'>","<meta name='keywords' content='(.*)'>"
        //$replacements = array("<title>$title</title>","meta name='description' content='$description'","meta name='keywords' content='$keywords'");
        $replacements = "<title>" . $title . "</title>";

        $output = preg_replace($patterns, $replacements, $output);
        echo $output;
    }

    /**
     * Define o local que ira guardar informações da instituição
     *
     * Verifica se existe e se é possivel escrita
     *
     * @var string $folder
     *
     * @return string localPath
     */
    public function mainPath()
    {
        $DocumentPath = INSTT_DOCS . SIGLA;
        if (file_exists($DocumentPath)) {
            return $DocumentPath;
        } else {
            if (!file_exists(INSTT_DOCS)) {
                mkdir(INSTT_DOCS, 0777);
            } else {
                if (!is_writable(INSTT_DOCS)) {
                    chmod(INSTT_DOCS, 0777);
                }
                $CreateDocumentPath = mkdir(INSTT_DOCS . SIGLA . DIRECTORY_SEPARATOR);
                if ($CreateDocumentPath) {
                    return $DocumentPath;
                } else {
                    return false;
                }
            }
        }
    }

    public function setSubMainFolder()
    {
        if (!file_exists(INST_PATH . '/' . ACT_DOCS)) {
            mkdir(INST_PATH . '/' . ACT_DOCS, 0777);
        }
        if (!file_exists(INST_PATH . '/' . IMG_DOCS)) {
            mkdir(INST_PATH . '/' . IMG_DOCS, 0777);
        }
        if (!file_exists(INST_PATH . '/' . AVATAR_DOCS)) {
            mkdir(INST_PATH . '/' . AVATAR_DOCS, 0777);
        }
        if (!file_exists(INST_PATH . '/' . IMG_DOCS . IMG_PHT)) {
            mkdir(INST_PATH . '/' . IMG_DOCS . IMG_PHT, 0777);
        }
    }

    /** @function Sistema completo de upload de dados, funciona com qualquer tipo, necessario formatação */
    public function uploadSystem($arrayFile, $toLocation = null, $exclude_file = false)
    {
        /*Identificador unico do livro*/
        $idb = rand(1000000, 9999999);
        $catalogDate = Data::setData();
        $upd = "undefined";
        /** Upload de imagem caso exista*/
        if (!empty($arrayFile['name'])) {
            $setRamdomName = uniqid(time());
            $getExtension = explode('/', $arrayFile['type']);
            $ext = $getExtension[1];
            $fileName = $setRamdomName . '.' . $ext;
            $place = null;
            switch ($toLocation) {
                case 'activity':
                case 'act':
                case pg_activities:
                case ACT_DOCS:
                    $place = INSTT_DOCS . SIGLA . DIRECTORY_SEPARATOR . ACT_DOCS;
                break;
                case 'avatar':
                case 'avt':
                case AVATAR_DOCS:
                    $place = INSTT_DOCS . SIGLA . DIRECTORY_SEPARATOR . AVATAR_DOCS;
                break;
                case pg_library:
                case IMG_LIB:
                case 'lib':
                    $place = INSTT_DOCS . SIGLA . DIRECTORY_SEPARATOR . IMG_DOCS . IMG_LIB;
                break;
                case 'photos':
                case 'pht':
                case IMG_PHT:
                    $place = INSTT_DOCS . SIGLA . DIRECTORY_SEPARATOR . IMG_DOCS . IMG_PHT;
                break;
                default:
                    $place = $toLocation;
                break;
            }

            /** verifica se pasta de imagem da biblioteca existe*/
            if (!file_exists($place)) {
                mkdir($place, 0777, true);
            }
            $moveToPlace = move_uploaded_file($arrayFile["tmp_name"], $place . $fileName);
            if ($moveToPlace) {
                //caso o arquivo tenha sido movido com sucesso, apga o anterior
                if ($exclude_file != (false or '' or null)) {
                    unlink($place . $exclude_file);
                }
                //caso envie com sucesso mostra o nome do arquivo
                $upd = $fileName;
            } elseif ($arrayFile['error'] != (0 or 4)) {
                $upd = 'error';
            } else {
                //caso contrario mostra false
                $upd = false;
            }

            return $upd;
        }
    }

    private function mainActionInterface(){
        $interface = "<input type='checkbox' class='hidden' id='mainclick' />";
        $interface .= "<div class='mainBox'>";
        $interface .=  "<label for='mainclick' class='mainActionInterface' title='"._tr(tools)."'></label>";
        $interface .=  "<div class='content'></div>";
        $interface .= "</div>";

        return $interface;
    }
    public function pinnedInterface()
    {
        return self::mainActionInterface();
        //return self::pinnedBtnInterface($this->interfaceOpt);
    }

    public static function pinnedBtnInterface($opt = [])
    {
        //seta a variavel pre existente
        $interface = null;
        //Verifica se o tipo de interface foi definida
        !isset($opt['type']) ? $opt['type'] = false : null;
        //verifica se o metodo de botoes foi definido
        !isset($opt['method']) ? $opt['method'] = 'js' : null;
        switch ($opt['type']) {
            case 'save':
                //inteface HREF botoes
                !isset($opt['save']) ? $savePath = "javascript:void(0);" : $savePath = $opt['save'];
                !isset($opt['cancel']) ? $cancelPath = WWW_PATH . PathAppSession : $cancelPath = $opt['cancel'];

                //botao salvar
                $interface .= "<a href='" . $savePath . "' id='beforePrependSaveData'>";
                $interface .= "<label class='prepend-gui btnPinned' id='prepend_save_data'>";
                $interface .= "<div name='save' class='dButton btnSavePinned' id='save_data' title='" . _tr(save) . "'>" . _tr(save) . "</div>";
                $interface .= "<label for='extras' class='labelIcon save'></label>";
                $interface .= "</label>";
                $interface .= "</a>";
                //botao cancelar
                $interface .= "<a href='" . $cancelPath . "'>";
                $interface .= "<label class='prepend-gui btnPinned' id='prepend_cancel_data'>";
                $interface .= "<div name='cancel' id='cancel'  class='dButton btnCancelPinned' title='" . _tr(cancel) . "'>" . _tr(cancel) . "</div>";
                $interface .= "<label for='withdraw' class='labelIcon cancel'></label>";
                $interface .= "</label>";
                $interface .= "</a>";
            break;
            case 'update':
                switch ($opt['subtype']) {
                    case 'complete':
                        $btName = _tr(complete);
                        $clBtIcon = 'check';
                    break;
                    default:
                        $btName = _tr(cancel);
                        $clBtIcon = 'cancel';
                    break;

                }
                //inteface HREF botoes
                !isset($opt['save']) ? $savePath = "javascript:void(0);" : $savePath = $opt['save'];
                //!isset($opt['cancel']) ? $cancelPath = "javascript:void(0);" : $cancelPath = $opt['cancel'];
                !isset($opt['cancel']) ? $cancelPath = WWW_PATH . PathAppSession : $cancelPath = $opt['cancel'];

                //botao atualizar
                $interface .= "<a href='" . $savePath . "' id='beforePrependSaveData'>";
                $interface .= "<label class='prepend-gui btnPinned' id='prepend_save_data'>";
                $interface .= "<div name='save' class='dButton btnUpdatePinned'  id='save_data' title='" . _tr(update) . "'>" . _tr(update) . "</div>";;
                $interface .= "<label for='extras' class='labelIcon update'></label>";
                $interface .= "</label>";
                $interface .= "</a>";
                //botao cancelar ou finalizar
                $interface .= "<a href='" . $cancelPath . "'>";
                $interface .= "<label class='prepend-gui  btnPinned' id='prepend_cancel_data'>";
                $interface .= "<div name='cancel' id='cancel'  class='dButton btnCancelPinned' title='" . $btName . "'>" . $btName . "</div>";
                $interface .= "<label for='withdraw' class='labelIcon " . $clBtIcon . "'></label>";
                $interface .= "</label>";
                $interface .= "</a>";
            break;
            case 'library':
                //inteface HREF botoes
                !isset($opt['withdraw']) ? $withdrawPath = "javascript:void(0);" : $withdrawPath = $opt['withdraw'];
                !isset($opt['deposit']) ? $depositPath = "javascript:void(0);" : $depositPath = $opt['deposit'];
                !isset($opt['reserve']) ? $reservePath = "javascript:void(0);" : $reservePath = $opt['reserve'];
                !isset($opt['renew']) ? $renewPath = "javascript:void(0);" : $renewPath = $opt['renew'];

                //Botao Retirar livros (emprestar)
                $interface .= "<a href='" . $withdrawPath . "' id='href_wdraw'>";
                $interface .= "<label class='prepend-gui btnPinned'>";
                $interface .= "<div name='withdraw' id='withdraw'  class='specialBtnPinned' 
				title='" . _tr(to_lend) . "'>" . _tr(to_lend) . "</div>";
                $interface .= "<label for='withdraw' class='labelIcon withdraw '></label>";
                $interface .= "</label>";
                $interface .= "</a>";
                //Botao renovar livros (emprestar)
                $interface .= "<a href='" . $renewPath . "' id='href_wdraw'>";
                $interface .= "<label class='prepend-gui btnPinned'>";
                $interface .= "<div name='renew' id='renew'  class='specialBtnPinned' 
                title='" . _tr(renew) . "'>" . _tr(renew) . "</div>";
                $interface .= "<label for='renew' class='labelIcon renew'></label>";
                $interface .= "</label>";
                $interface .= "</a>";
                //botao devolver livros
                $interface .= "<a href='" . $depositPath . "'>";
                $interface .= "<label for='deposit' class='prepend-gui btnPinned'>";
                $interface .= "<div name='deposit' id='deposit' class='specialBtnPinned' 
				title='" . _tr(deposit) . "'>" . _tr(deposit) . "</div>";
                $interface .= "<label for='deposit' class='labelIcon deposit'></label>";
                $interface .= "</label>";
                $interface .= "</a>";
                //botao reservar livros
                $interface .= "<a href='" . $reservePath . "'>";
                $interface .= "<label for='reserve' class='prepend-gui btnPinned' >";
                $interface .= "<div name='reserve' class='specialBtnPinned' id='reserve' 
				title='" . _tr(reserve) . "'>" . _tr(reserve) . "</div>";
                $interface .= "<label for='reserve' class='labelIcon reserve_book'></label>";
                $interface .= "</label>";
                $interface .= "</div></div>";
                $interface .= "</a>";
            break;
            case 'withdraw':
                //inteface HREF botoes
                !isset($opt['save']) ? $savePath = "javascript:void(0);" : $savePath = $opt['save'];
                !isset($opt['cancel']) ? $cancelPath = WWW_PATH . PathAppSession : $cancelPath = $opt['cancel'];

                //botao emprestar
                $interface .= "<a href='" . $savePath . "' id='beforePrependSaveData'>";
                $interface .= "<label class='prepend-gui btnPinned' id='prepend_save_data'>";
                $interface .= "<div name='save' class='dButton btnSavePinned' id='save_data' title='" . _tr(to_lend) . "'>" . _tr(to_lend) . "</div>";
                $interface .= "<label for='extras' class='labelIcon withdraw'></label>";
                $interface .= "</label>";
                $interface .= "</a>";
                //botao cancelar
                $interface .= "<a href='" . $cancelPath . "'>";
                $interface .= "<label class='prepend-gui btnPinned' id='prepend_cancel_data'>";
                $interface .= "<div name='cancel' id='cancel'  class='dButton btnCancelPinned' title='" . _tr(cancel) . "'>" . _tr(cancel) . "</div>";
                $interface .= "<label for='withdraw' class='labelIcon cancel'></label>";
                $interface .= "</label>";
                $interface .= "</a>";
            break;
            case 'reserve':
                //inteface HREF botoes
                !isset($opt['save']) ? $savePath = "javascript:void(0);" : $savePath = $opt['save'];
                !isset($opt['cancel']) ? $cancelPath = WWW_PATH . PathAppSession : $cancelPath = $opt['cancel'];

                //botao reservar
                $interface .= "<a href='" . $savePath . "' id='beforePrependSaveData'>";
                $interface .= "<label class='prepend-gui btnPinned' id='prepend_save_data'>";
                $interface .= "<div name='save' class='dButton btnSavePinned bgLgOrange' id='save_data' title='" . _tr(reserve) . "'>" . _tr(reserve) . "</div>";
                $interface .= "<label for='extras' class='labelIcon time'></label>";
                $interface .= "</label>";
                $interface .= "</a>";
                //botao cancelar
                $interface .= "<a href='" . $cancelPath . "'>";
                $interface .= "<label class='prepend-gui btnPinned' id='prepend_cancel_data'>";
                $interface .= "<div name='cancel' id='cancel'  class='dButton btnCancelPinned' title='" . _tr(cancel) . "'>" . _tr(cancel) . "</div>";
                $interface .= "<label for='withdraw' class='labelIcon cancel'></label>";
                $interface .= "</label>";
                $interface .= "</a>";
            break;
        }
        //Mostra a interface
        if (empty($opt['type'])) {
            return false;
        } else {
            //<div id='pnInterface'><div id='inputGroup' class='agnRight'>
            echo "<div id='BoxApp' class='inFullSize mDashboard_pinned'>
		    <div id='Group-mPinned' class='dFlex'>";
            echo $interface;
            echo "</div></div>";
        }

    }
    public static function sample_list($_const_title, $_const_sub_title, $echo_data, $pagination = false){

        echo "<div id='BoxApp'> 
            <div class='header'>
                <h3>"._tr($_const_sub_title)."</h3>
                <h1>"._tr($_const_title)."</h1>
            </div>  
            <div class='content'>".$echo_data."</div>
            <div class='footer'>
                    ".$pagination."
            </div>
        </div>";
    }
    public function setInterface($opt) {
        $this->interfaceOpt = $opt;
    }
    public function checkBrowser($return = 'browser') {
        $ip = $_SERVER['REMOTE_ADDR'];

        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'Linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'Mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'Windows';
        }


        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent) ||
            preg_match('/Trident/i',$u_agent) && !preg_match('/Opera/i',$u_agent) )
        {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        elseif(preg_match('/Firefox/i',$u_agent))
        {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif(preg_match('/Chrome/i',$u_agent))
        {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif(preg_match('/AppleWebKit/i',$u_agent))
        {
            $bname = 'AppleWebKit';
            $ub = "Opera";
        }
        elseif(preg_match('/Safari/i',$u_agent))
        {
            $bname = 'Apple Safari';
            $ub = "Safari";
        }

        elseif(preg_match('/Netscape/i',$u_agent))
        {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
        }


        $i = count($matches['browser']);
        if ($i != 1) {
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if ($version==null || $version=="") {$version="?";}

        $Browser = array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern,
            'ub'        => $ub
        );

        $navegador = "Navegador: " . $Browser['name'] . " " . $Browser['version'];
        $so = "SO: " . $Browser['platform'];
        if($return == 'browser'){
            return $Browser['ub'];
        }
        else{
            return $Browser['platform'];
        }

        /* Para finalizar coloquei aqui o meu insert para salvar na base de dados... Não fiz nada para mostrar em tela, pois só uso para fins de log do sistema  */
    }
    public function messageMSIE(){

        if(self::checkBrowser('browser') == 'MSIE'){

            die("<div class='loader'>".upshot::noEdata(_tr(warning),_tr(warning_msie_not_supported.warning_msie_descr),'wrong',"https://www.google.com.br/chrome/browser/desktop/index.html",true)."</div>");
        }
    }





}//Fim da Classe

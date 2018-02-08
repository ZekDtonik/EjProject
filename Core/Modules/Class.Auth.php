<?php
/**
 * Created by MarxMedia Solutions.
 *    Author: Kevin Marques
 *    Date: 30/09/2017 - 15:53
 *      File: Class.Auth.php
 *        Acctos
 */


namespace Classes;


class Auth
{
    /**
     * @param string $_SESSION ["LoginId"] Acessa a identificação
     *                         (ID) especifica do usuario
     *
     * @return string ID do usuario logado atualmente
     */
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

    public static function isCpf($cpf = null)
    {
        /*Variaveis de controle*/
        $digitOne = 0;
        $digitTwo = 0;

        // Verifica se um número foi informado
        if (empty($cpf)) {
            return null;
        }

        // Elimina possiveis mascaras
        $cpf = preg_replace('/[^0-9]/', '', $cpf);


        // Verifica se o numero de digitos informados é igual a 11
        if (strlen($cpf) != 11) {
            return false;
        } // Calcula os digitos verificadores para verificar se o

        else {
            /**  Explicando:
             *
             * @Param
             * {
             * $a: Valor de referencia para digito verificador 1
             * $i: Valor de referencia para digito verificador 2
             *
             * $t1 e $t2: indices multiplicativos de cada valor especificado
             * pelos valores de referencia na rotação do looping
             *
             * Ao final das multiplicações dos numeros,
             * o valor total é somado dando os resultados esperados
             * }
             */
            for ($a = 0, $x = 10; $a <= 8; $a++, $x--) {
                $digitOne += $cpf[$a] * $x;
                /**  Esta condição verifica um possivel hacking de numeros
                 * !Caso todos os digitos de 0~11 sejam os mesmos, o algoritmo
                 * retornará TRUE, mesmo sendo obviamente um cpf inválido!
                 *
                 * Retorna falso caso o cpf contenha todos os números iguais!
                 */
                if (str_repeat($a, 11) == $cpf) {
                    return false;
                }
            }
            for ($i = 0, $x = 11; $i <= 9; $i++, $x--) {
                $digitTwo += $cpf[$i] * $x;
            }
            /*Utiliza modulos e verifica se são menores que 2, caso true, pela regra
            automaticamente se tornam zero, caso false subtrai 11 do resto da divisao
            da soma recebida por 11*/

            $VerifyOne = (($digitOne % 11) < 2) ? 0 : 11 - ($digitOne % 11);
            $VerifyTwo = (($digitTwo % 11) < 2) ? 0 : 11 - ($digitTwo % 11);
            /* se os ultimos digitos não forem iguais aos informados, o cpf é INVALIDO*/
            if ($VerifyOne <> $cpf[9] || $VerifyTwo <> $cpf[10]) {
                return false;
            }

            // CPF é válido
            return true;


        }

    }

    public static function isVoter($voter = null)
    {
        $voter = str_pad(preg_replace('/[^0-9]/', '', $voter), 12, '0', STR_PAD_LEFT);
        $uf = intval(substr($voter, 8, 2));

        if (strlen($voter) != 12 || $uf < 1 || $uf > 28) {
            return false;
        } else {
            $d = 0;

            for ($i = 0; $i < 8; $i++) {
                $d += $voter{$i} * (9 - $i);
            }

            $d %= 11;

            if ($d < 2) {
                if ($uf < 3) {
                    $d = 1 - $d;
                } else {
                    $d = 0;
                }
            } else {
                $d = 11 - $d;
            }

            if ($voter{10} != $d) {
                return false;
            }

            $d *= 2;

            for ($i = 8; $i < 10; $i++) {
                $d += $voter{$i} * (12 - $i);
            }

            $d %= 11;

            if ($d < 2) {
                if ($uf < 3) {
                    $d = 1 - $d;
                } else {
                    $d = 0;
                }
            } else {
                $d = 11 - $d;
            }

            if ($voter{11} != $d) {
                return false;
            }

            return true;
        }

    }

    public static function isPhone($phone, $output = null, $returnClearNumber = null)
    {
        $phone = preg_replace("/[^0-9]/", '', $phone);
        if (empty($phone)) {
            return false;
        } elseif (!is_numeric($phone)) {
            return false;
        } elseif (strlen($phone) < 11) {
            return false;
        } else {
            /*  Verificação de código de area   */
            $phoneArea = substr($phone, 0, 2);
            $CodeArea = array(
                "AC" => 68,
                "AL" => 82,
                "AP" => 96,
                "AM" => 97,
                "BA" => 71, 73, 74, 75, 77,
                "CE" => 85, 88,
                "ES" => 27, 28,
                "GO" => 61, 62, 64,
                "MA" => 98, 99,
                "MT" => 65, 66,
                "MS" => 67,
                "MG" => 31, 32, 33, 34, 35, 37, 38,
                "PA" => 91, 93, 94,
                "PB" => 83,
                "PR" => 41, 42, 43, 44, 45, 46,
                "PE" => 81, 87,
                "PI" => 86, 89,
                "RJ" => 21, 22, 24,
                "RN" => 84,
                "RS" => 51, 53, 54, 55,
                "RO" => 69,
                "RR" => 95,
                "SC" => 47, 48, 49,
                "SP" => 11, 12, 13, 14, 15, 16, 17, 18, 19,
                "SE" => 79,
                "TO" => 63);

            if ($output == true) {
                if (in_array($phoneArea, $CodeArea)) {
                    $chaveEstado = array_search($phoneArea, $CodeArea);

                    return $chaveEstado;
                }
            } elseif (!in_array($phoneArea, $CodeArea)) {
                return false;
            } elseif ($returnClearNumber != null) {
                return $phone;
            }

            return true;

        }

    }

    /** Função complexo que verifica a validade de um cep passado
     *
     * @var string $CEP valor a ser testado
     * @var mixed $output indica o tipo de retorno da instrução
     *
     * @param        string @url viacep.com.br/ws/
     *
     * @param string $CEP faz uma verificação basica de quantidade e depois
     *                       uma analise externa nos correios por meio de um link exterior que
     *                       verifica a autenticidade do cep passado!
     *
     * @return boolean
     */
    public static function isCep($CEP, $output = null)
    {

        $cep = preg_replace("/[^0-9]/", '', $CEP);
        if (empty($cep)) {
            return false;
        } elseif (strlen($cep) > 8 or strlen($cep) < 8) {
            return false;
        } else {
            // parametros passados pela URL
            $postCorreios = $cep . "/json/";

            $urlHandler = curl_init("viacep.com.br/ws/" . $postCorreios);
            // seta opcoes para fazer a requisicao
            curl_setopt($urlHandler, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($urlHandler, CURLOPT_HEADER, false);
            //curl_setopt($urlHandler, CURLOPT_POST, true);
            //curl_setopt($urlHandler, CURLOPT_POSTFIELDS, $postCorreios);

            // faz a requisicao e retorna o conteudo do endereco
            $saida = curl_exec($urlHandler);

            curl_close($urlHandler);// encerra e retorna os dados
            // $saida = utf8_encode($saida); // codifica conteudo para utf-8

            $dataCep = json_decode($saida, true);

            //var_dump($dataCep);
            if (@$dataCep['erro'] == true) {
                return false;
            }
            if ($output == true) {
                return $dataCep;
            }
        }

        return true;

    }

    /** Função que verifica uma string com o valor passado de string endereços WEB
     *
     * @var string $www valor a ser testado
     *
     * @param string $www o padrao verifica dominios de sites, aceita barras, pontos, letras e
     *                    numeros de a-z
     *
     * @return boolean
     */
    public static function isWeb($www)
    {
        if (empty($www)) {
            return false;
        } else {
            if (!preg_match("/([a-z0-9\.\/\_]+)/i", $www)) {
                return false;
            }

            return true;
        }
    }

    /** Função que verifica uma string com o valor passado de siglas de estados
     *
     * @var string $uf valor a ser testado
     *
     * @param string $uf o padrao verifica a abreviação do estado com duas letras
     *
     * @return boolean
     */
    public static function isUf($uf)
    {
        if (empty($uf)) {
            return false;
        } else {
            if (!preg_match("/([a-z]{2})/i", $uf)) {
                return false;
            }

            return true;
        }
    }

    /** Função que verifica uma string com o valor passado de palavras simples
     *
     * @var string $letter valor a ser testado
     *
     * @param string $letter o padrao aceita apenas letras e espaços
     *
     * @return boolean
     */
    public static function isLetter($letter)
    {
        if (empty($letter)) {
            return false;
        } else {
            if (!preg_match("/^([a-z ]+)$/i", $letter)) {
                return false;
            }

            return true;
        }
    }

    /** Função que verifica uma string
     *
     * @var string $letter valor a ser testado
     *
     * @param string $letter o padrao aceita apenas caracteres alfanumericos
     *
     * @return boolean
     */
    public static function isAlpha($letter)
    {
        if (empty($letter)) {
            return false;
        } else {
            if (!preg_match("/^([a-z0-9 ]+)$/i", $letter)) {
                return false;
            }

            return true;
        }
    }

    /** Função que verifica uma string com o valor passado de nomes complexos
     *
     * @var string $names valor a ser testado
     *
     * @param string $names o padrao aceita caracteres alfanumericos com acentuações,
     *                      mas não aceita numeros ou simbolos
     *
     * @return boolean
     */
    public static function isNames($names)
    {
        if (empty($names)) {
            return false;
        } else {
            if (!preg_match("/^([a-z á-úà-ùãõâ-ûçä-ü\.]+)$/i", $names)) {
                return false;
            }

            return true;
        }
    }

    /** Função que verifica uma string com o valor passado de textos
     *
     * @var string $adr valor a ser testado
     *
     * @param string $adr o padrao aceita caracteres alfanumericos com acentuações e
     *                    simbolos, espaços e arroba
     *
     * @return boolean
     */
    public static function isText($adr)
    {
        if (empty($adr)) {
            return false;
        } else {
            if (!preg_match("/^([a-z0-9 á-úà-ùãõâ-ûçä-ü\.\?\!\,\%@\/\[\]\(\)\;\:]+)$/i", $adr)) {
                return false;
            }
            return true;
        }
    }

    /** Função que verifica uma string ou valor passado de endereços
     *
     * @var string $adr valor a ser testado
     *
     * @param string $adr o padrao aceita caracteres alfanumericos com acentuações,
     *                    mas não permite simbolos
     *
     * @return boolean
     */
    public static function isAddress($adr)
    {
        if (empty($adr)) {
            return false;
        } else {
            if (!preg_match("/^([a-z0-9 á-úà-ùãõâ-ûçä-ü]+)$/i", $adr)) {
                return false;
            }

            return true;
        }
    }
    public static function isNatural($text){
        if (empty($text)) {
            return false;
        } else {
            if (!preg_match("/^([a-z0-9 á-úà-ùãõâ-ûçä-ü\.\-\_,\?\!\$%&\*\:]+)$/i", $text)) {
                return false;
            }

            return true;
        }
    }
    /** Função que verifica uma string ou valor passado de email
     *
     * @var string $email valor a ser testado
     *
     * @return boolean
     */
    public static function isEmail($email)
    {
        if (empty($email)) {
            return false;
        } elseif (!preg_match("/^[a-zA-z0-9\.\_\-]+[@]{1}[a-zA-z0-9\.\_\-]+\.{1}[a-zA-z0-9]{2,4}$/", $email)) {
            return false;
        } else {
            return true;
        }

    }
    public static function isLogin($login){
        if (empty($login)) {
            return false;
        } elseif (!preg_match("/^[a-z0-9\.\_]+$/i", $login)) {
            return false;
        } else {
            return true;
        }
    }
    public static function isColor($color){
        if (empty($color)) {
            return false;
        } elseif (!preg_match("/^#([a-z0-9]{6})$/i", $color)) {
            return false;
        } else {
            return true;
        }
    }
    public static function replaceSearch($s)
    {
        return preg_replace('/[^a-z0-9çáàãâäëéèêíìîôõöóòúùûü]/i', '', $s);
    }

    /** Função que verifica uma string ou valor passado de data e hora
     *
     * @var string $date valor a ser testado
     *
     * @return boolean
     */
    public static function isDate($date)
    {
        $date = stripslashes($date);
        if (empty($date)) {
            return false;
        } else {
            /** @var string $patterns são os tipos de ordenação em data e hora em REGEX */
            $patternEU = "/^(0[1-9]|[12][0-9]|3[01])(\/|\-|\.)(0[1-9]|1[012])(\/|\-|\.)([12][7-9]{1}[0-9]{2}|[2][0-9]{1}[0-9]{2})$/";
            $patternNA = "/^(0[1-9]|1[012])(\/|\-|\.)(0[1-9]|[1][0-9]|3[01])(\/|\-|\.)([12][7-9]{1}[0-9]{2}|[2][0-9]{1}[0-9]{2})$/";
            $patternIN = "/^([12][7-9]{1}[0-9]{2}|[2][0-9]{1}[0-9]{2})(\/|\-|\.)(0[1-9]|1[012])(\/|\-|\.)(0[1-9]|[1][0-9]|3[01])$/";
            /**
             * Definição de tipo de data e hora, esse switch esta especificado no DEFINES.PHP
             *
             * @const i18nDef tipo de leitura de data definida na página de linguagem do sistema
             */
            switch (i18nDef) {
                case 'EU':
                    if (!preg_match($patternEU, $date)) {
                        return false;
                    }
                    return true;
                break;
                case "NA":
                    if (!preg_match($patternNA, $date)) {
                        return false;
                    }
                    return true;
                break;
                case "IN":
                    if (!preg_match($patternIN, $date)) {
                        return false;
                    }
                    return true;
                break;
            }
        }
    }

    public static function removeSpecialCharacters($string)
    {
        return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/", "/(ç)/", "/(Ç)/"), explode(" ", "a A e E i I o O u U n N c C"), $string);
    }
    public static function randomColor(){
        $pattern = "ABCDEF0123456789";
        return "#".substr(str_shuffle($pattern),0,1).substr(str_shuffle($pattern),0,1).substr(str_shuffle($pattern),0,1).substr(str_shuffle($pattern),0,1).substr(str_shuffle($pattern),0,1).substr(str_shuffle($pattern),0,1);

    }
}
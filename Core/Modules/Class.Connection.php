<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 17/12/2017
 * Time: 11:59
 */

namespace Modules;

use PDO;
use PDOException;
class Connection extends PDO
{
    private $DSN;
    private function getDSN(){return $this->DSN;}
    /**
     * @param string $DSN
     */
    private function setDSN($DSN){$this->DSN = $DSN;}
    public function __construct()
    {
        try{
            /**
             * @var $this->typeDb
             */
            switch (__TYPE_DB){
                case "SQLite":
                    $this->setDSN("sqlite:".__DBNAME);
                    break;
                case "PostGre":
                    $this->setDSN("pgsql:host=".__HOST__.";port=".__PORT__.";dbname=".__DBNAME.";charset=utf8");
                    break;
                default:
                    $this->setDSN("mysql:host=".__HOST__.";port=".__PORT__.";dbname=".__DBNAME.";charset=utf8");
                    break;
            }

            parent::__construct($this->getDSN(), __USER, __PSWD);
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setAttribute(PDO::ATTR_TIMEOUT,2);
        }
        catch (PDOException $a){
            echo "<h1>EJ Project Exception!</h1><br>";

            //dados tratados para melhor aparencia e compreenção do usuario
            if ($a->getCode() == 1049) {
                echo "Oops... Banco de Dados não encontrado..";
                echo "<br/> Dica: Verifique se o nome do banco de dados estão correto..";
                echo "<br/> Código: 1049.";
                die();
            } elseif ($a->getCode() == 1045) {
                echo "Oops... Acesso negado.";
                echo "<br/> Dica: Verifique se o nome de login e senha foram digitados corretamente!";
                echo "<br/> Código: 1045.";
                die();
            } elseif ($a->getCode() == 2002) {
                echo "Oops... O Hospedador nao foi reconhecido ou encontrado...";
                echo "<br/> Dica: Verifique se o nome do Servidor, número de IP ou a Porta de conexao foram digitados corretamente..";
                echo "<br/> Código: 2002.";
                die();
            } elseif ($a->getCode() == 2003) {
                echo "Oops... Não foi possivel conectar  ao servidor...";
                echo "<br/> Dica: Verifique se o nome do Servidor, número de IP ou a Porta de conexao foram digitados corretamente..";
                echo "<br/> Código: 2003.";
                die();
            }
            else{
                echo $a->getMessage();
            }
        }
    }
    /* Função Estatica, adianta o processo de aquisição de SQL, caso nao precise de transaction..
    */
    public static function getSql(){
        return new Connection();
    }
}
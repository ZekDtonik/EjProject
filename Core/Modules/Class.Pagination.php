<?php
/**
 * Created by MarxMedia Solutions.
 *    Author: Kevin Marques
 *    Date: 18/03/2016 - 18:47
 *  updated to use in this system
 *    Date: 03/02/2018 - 19:32
 */

namespace Classes;

use Modules\Connection;
use PDO;
class Pagination
{

    /*  Sistema de página global do app   */
    private $limit;
    private $StartIn;
    private $MaxLimit;
    private $page;
    private $filters;
    private $sql;
    public function getSql()
    {
        return $this->sql;
    }
    /** Metodo de inicialização de páginação
     * $table = Tabela a ser usada para formação de páginação
     * $column = nome da coluna utilizada para contagem de elementos da tabela
     * $filter_conditions = opções adicionais para manipulação do resultado final da paginação
     * Utilizando quando é uma página de pesquisa com filtros de retorno WHERE por exemplo
     * $js = Sistema inicia ajax para retorno de página sem redirecionamento
     */
    public function __construct($table, $column, $get, $filter_conditions = '',$get_filter = null)
    {
        /*  SQL  */
        $this->sql = new Connection();

        //
        $this->filters = base64_encode($filter_conditions);
        //

        //recupera o numero de valores a ser mostrado na chamada 
        $this->limit = 5;
        /*  Query que faz a chamada especifica da tabela especificada requerida.    */
        $pagination = $this->getSql()->query("SELECT ".$column." FROM ".$table." ".$filter_conditions);
        /*  Conta os dados nas colunas  */
        $dataRowCount = $pagination->rowCount();

        //var_dump($filter_conditions);
        //var_dump($_SERVER['QUERY_STRING']);
        /*  Instrução utilizada para gerar os valores da páginação (Numero de página)  */
        $this->MaxLimit = ceil($dataRowCount / $this->limit);

        /*  Instrução utilizada para indicação de páginas via GET  (Usada nas query) */
        //!empty($get) ? $this->page = $get : $this->page = 1;

        if(!empty($get)){
            $this->page = $get;
            if($get == 0 or !is_numeric($get)){
                $this->page = 1;
            }
            elseif($get > $this->MaxLimit){
                $this->page = $this->MaxLimit;
            }
            else{
                $this->page = $get;
            }

        }
        else{
            $this->page =1;
        }

        /*  Inicia a partir do valor definido no GET    */
        // (página atual * quantidade por página) - quantidade por página
        //$this->StartIn = ($this->page * $this->limit) - $this->page;
        $this->StartIn = ($this->page * $this->limit) -  $this->limit;
    
    }
    protected function getStart()
    {
        return $this->StartIn;
    }
    protected function getLimit()
    {
        return $this->limit;
    }
    /** Esse metodo é obrigatorio para a limitação da chamada do query*/
    public function getProcessLimit(){
        $Limit = " LIMIT ".self::getStart().', '.self::getLimit();
        return $Limit;
    }

    public function getPagination($LinkRdr){
        //Limite de link máximo na pagina
        $maxLinks = 2;
        /**
         * Se a subtração da pagina com o limite de links for maior que 1, inicia pela pagina
         * caso contrario inicia pelo 1 */
        $start = ($this->page - $maxLinks) > 1 ? $this->page - $maxLinks : 1;

        $limit = ($this->page + $maxLinks) < $this->MaxLimit ? ($this->page + $maxLinks) < 5 ? 5 : ($this->page + $maxLinks) : $this->MaxLimit;

        ($this->page == 1)? $linkLess = '' : $linkLess = "href='".$LinkRdr."/".($this->page - 1)."/'";
        ($this->page != $this->MaxLimit) ? $linkMore = "href='".$LinkRdr."/".($this->page + 1)."/'" : $linkMore = '';
        if($limit != 1):
            echo "<div id='buildPage'><ul>";
            echo "<a class='iconPagination arrowLeft L' ".$linkLess."></a>";
            for($i = $start; $i <= $limit; $i++){
            //for($i = 1; $i < $this->MaxLimit + 1; $i++){
                if($i >= 1 and $i <= $this->MaxLimit){
                    if($i != $this->page){
                        $link = "href='".$LinkRdr."/".$i."/'";
                        $ac ='';
                    }
                    else{
                        $link ='';
                        $ac ='actual';
                    }
                    echo "<li class='".$ac."'><a ".$link.">$i</a></li>";
                    //echo "<li class='".$ac."'><a id='".$i."' onclick='clstd(this)'>$i</a></li>";
                }
            }
            echo "<a class='iconPagination arrowRight R' ".$linkMore."></a>";
            echo "</ul></div>";
        endif;
        return null;
    }



}
<?php
/**
 *    Created by DevWolf.
 *   Author: Kevin Marques
 *   Date: 26/03/2018 - 13:10
 */

namespace Modules;


class Pagelist
{
    /*  Sistema de página global do app   */
    private $limit;
    private $StartIn;
    private $MaxLimit;
    private $page;
    /** Metodo de inicialização de páginação
     * $table = Tabela a ser usada para formação de páginação
     * $column = nome da coluna utilizada para contagem de elementos da tabela
     * $filter_conditions = opções adicionais para manipulação do resultado final da paginação
     * Utilizando quando é uma página de pesquisa com filtros de retorno WHERE por exemplo
     * $js = Sistema inicia ajax para retorno de página sem redirecionamento
     */
    public function __construct($list, $get ,$limite = null)
    {

        /*  Instrução utilizada para gerar os valores da páginação (Numero de página)  */
        $this->MaxLimit = ceil(count($list) / $limite);
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
        //

        // (página atual * quantidade por página) - quantidade por página
        $this->StartIn = ($this->page * $limite) - $limite < 0 ? 0 : ($this->page * $limite) - $limite ;
        $finalLimite = count($list) < ($limite * $this->page) ? count($list) : $limite * $this->page;
        //$finalLimite = $limite * $this->page;
        //recupera o numero de valores a ser mostrado na chamada
        $this->limit = empty($limite) ? 5 * $this->page : $finalLimite;
        /*  Instrução utilizada para gerar os valores da páginação (Numero de página)  */
        //$this->MaxLimit = ceil(count($list) / $this->limit);
        /*  Inicia a partir do valor definido no GET    */

        //$this->StartIn = $this->limit - 1 < 0 ? 0 : $this->limit  -1 ;

    }
    public function getStart()
    {
        return $this->StartIn;
    }
    public function getLimit()
    {
        return $this->limit;
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
                    echo "<a ".$link."><li class='".$ac."'>$i</li></a>";
                    //echo "<li class='".$ac."'><a id='".$i."' onclick='clstd(this)'>$i</a></li>";
                }
            }
            echo "<a class='iconPagination arrowRight R' ".$linkMore."></a>";
            echo "</ul></div>";
        endif;
        return null;
    }



}
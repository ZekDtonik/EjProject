<?php
/**
 *    Created by DevWolf.
 *   Author: Kevin Marques
 *   Date: 08/02/2018 - 10:39
 */

namespace Modules;


use Classes\Pagination;
use Interfaces\Employ;
use Modules\Users;
use PDO;
class Employee extends Users implements Employ
{
    public function visualizaMsg($isDoc = false){
        parent::ui_back(DS._employee.DS);
        echo "<article style=\"margin-top:80px;\">
        <div class=\"container\" style=\"height:auto;\">";

            echo"<div class=\"row\" style=\"height:auto;\">";
            
            self::getMessages($isDoc);

            echo "</div>
        </div>
    </article>";

    }
    public function getMessages($isDoc){
        //Recupera as informações de mensagem
        $sql ="SELECT ".__MESSAGE.".*, ".__CATEGORY.".id, ".__CATEGORY.".cor, ".__CATEGORY.".nome AS catname  
        FROM ".__MESSAGE." LEFT JOIN ".__CATEGORY." ON ".__MESSAGE.".categoria=".__CATEGORY.".id 
        INNER JOIN ".__USERS." ON ".__USERS.".matricula=".__MESSAGE.".lista_matricula 
        WHERE ".__USERS.".matricula = '".Authenticate::getSessionEnroller()."' ";
        //Quiery para a páginação
        //$tables = __MESSAGE." INNER JOIN ".__USERS." ON ".__USERS.".matricula=".__MESSAGE.".lista_matricula
        //WHERE ".__USERS. ".matricula='".Authenticate::getSessionEnroller(). "' ";
        //Paginação
        //$pagination = new Pagination($tables,__MESSAGE.'.id',$_GET[_subAction],null,4);
        //QueryMAin
        $StreamGetAllData = $this->getSql()->query($sql);


        //Para evitar fazer mais de uma chamada ao banco, definiremos
        //3 listas, um principel que vem do banco e duas priorizadas
        //Lista principal
        $mainList = $StreamGetAllData->fetchAll(PDO::FETCH_ASSOC);
        //Lista para apenas documentos
        $listDocs = [];
        $listMsg = [];
        $c1=0;
        $c2=0;

        for($a = 0 ; $a < sizeof($mainList); $a++){
            //popula a lista para mensagens pardrao
            if($mainList[$a]['type'] == 0){
                $listMsg[] = $mainList[$a];
            }
            else{
                $listDocs[] = $mainList[$a];
            }
        }
        if($isDoc){
            if(sizeof($listDocs) == 0){
                echo "<div class=\"container\">
                    <div class=\"row\">
                        <div class=\"col\"></div>
                        <div class=\"col\"><h3 class=\"text-info text-center mt-5\">"._tr("Texts")->nothing_to_show." </h3>
                            <h5 class=\"text-center pt-2\">"._tr("Infos")->none_download_message_to_show."</h5>
                            <p class=\"text-center\">"._tr("Infos")->try_reload_page_to_upate."</p>
                        </div>
                        <div class=\"col\"></div>
                    </div>
                </div>";
            }
            else{
                $paginationDoc = new Pagelist($listDocs,$_GET[_subAction],4);

                for($b = $paginationDoc->getStart(); $b < $paginationDoc->getLimit(); $b++){
                    $StreamFetchSource = $listDocs[$b];
                    $categoryColor = empty($StreamFetchSource['cor']) ? "#999999" : $StreamFetchSource['cor'];
                    $categoryName = empty($StreamFetchSource['catname']) ? _tr("Texts")->not_categorized : $StreamFetchSource['catname'];

                    echo "<div class=\"col-3\" id=\"doc\" style=\"margin-bottom:20px;\">
                    <div style=\"height:30px;background-color:".$categoryColor.";\">
                        <h6 class=\"text-center\" style=\"color:rgb(255,255,255);padding-top:5px;\"> ".$categoryName."</h6>
                    </div>
                    <div class=\"d-flex flex-column\" style=\"height:80px;margin-bottom:15px;\">
                        <h6 class=\"text-center\" style=\"margin-top:10px;margin-bottom:12px;\">".$StreamFetchSource['titulo']."</h6>
                        <a class='text-center' href='".DS._employee.DS._documents.DS._download.DS.$StreamFetchSource['mid'].DS."'>
                            <button class=\"btn btn-info align-self-center\" type=\"button\" style=\"width:60%;\">
                            "._tr("Texts")->download." 
                            <i class=\"fa fa-download\"></i> 
                            </button>
                        </a>
                        </div>
                    <div role=\"tablist\" id=\"accordion-".$b."\">
                        <div class=\"card\">
                            <div class=\"card-header\" role=\"tab\" style=\"height:33px;padding:6px;\">
                                <h6 class=\"text-center mb-0\"><a class=\"makeViewed\" data-toggle=\"collapse\" view-url='".DS._requisition.DS._chvwd.DS.$StreamFetchSource['mid']."' aria-expanded=\"false\" aria-controls=\"accordion-".$b." .item-".$b."\" href=\"#accordion-".$b." .item-".$b."\">"._tr("Texts")->description." </a></h6>
                            </div>
                            <div class=\"collapse item-".$b."\" role=\"tabpanel\" data-parent=\"#accordion-".$b."\">
                                <div class=\"card-body\">
                                    <p class=\"card-text\">".$StreamFetchSource['mensagem']."</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style=\"background-color:#ffffff;height:30px;\">
                        <h6 class=\"text-center\" style=\"color:rgb(125,125,125);padding-top:5px;\">".\Kernel::getFormatedDate($StreamFetchSource['datetime'])."</h6>
                    </div>
                </div>";
                }
                $paginationDoc->getPagination(DS._employee._documents);
            }
        }
        else{
            if(sizeof($listMsg) == 0){
                echo "<div class=\"container\">
                    <div class=\"row\">
                        <div class=\"col\"></div>
                        <div class=\"col\"><h3 class=\"text-info text-center mt-5\">"._tr("Texts")->nothing_to_show." </h3>
                            <p class=\"text-center pt-2\">"._tr("Infos")->none_message_to_show."</p>
                            <span class=\"text-center\">"._tr("Infos")->try_reload_page_to_upate."</span>
                        </div>
                        <div class=\"col\"></div>
                    </div>
                </div>";
            }
            else{
                $paginationMsg = new Pagelist($listMsg,$_GET[_subAction],4);
                for($c = $paginationMsg->getStart(); $c < $paginationMsg->getLimit(); $c++) {

                    $StreamFetchSourceTwo = $listMsg[$c];
                    $categoryColor = empty($StreamFetchSourceTwo['cor']) ? "#999999" : $StreamFetchSourceTwo['cor'];
                    $categoryName = empty($StreamFetchSourceTwo['catname']) ? _tr("Texts")->not_categorized : $StreamFetchSourceTwo['catname'];
                    echo "<div class=\"col-3\" id=\"msg\" style=\"margin-bottom:20px;\">
                        <div style=\"height:30px;background-color:" . $categoryColor . ";\">
                        <h6 class=\"text-center\" style=\"color:rgb(255,255,255);padding-top:5px;\">" . $categoryName . " </h6>
                        </div>
                        <div class=\"d-flex flex-column\" style=\"height:80px;margin-bottom:15px;\">
                            <h6 class=\"text-center\" style=\"margin-top:10px;margin-bottom:12px;\">" . $c.$StreamFetchSourceTwo['titulo'] . "</h6>
                            <div class=\" btn align-self-center\"style=\"width:80%;\">
                                " . _tr("Texts")->common_message . " 
                                
                                </div>
                        </div>
                        <div role=\"tablist\" id=\"accordion-" . $c . "\">
                            <div class=\"card\">
                                <div class=\"card-header\" role=\"tab\" style=\"height:33px;padding:6px;\">
                                    <h6 class=\"text-center mb-0\">
                                    <a class=\"makeViewed\" data-toggle=\"collapse\" view-url='".DS._requisition.DS._chvwd.DS.$StreamFetchSourceTwo['mid']."' aria-expanded=\"false\" aria-controls=\"accordion-" . $c . " .item-" . $c . "\" href=\"#accordion-" . $c . " .item-" . $c . "\">" . _tr("Texts")->description . " </a>
                                    </h6>
                                </div>
                                <div class=\"collapse item-" . $c . "\" role=\"tabpanel\" data-parent=\"#accordion-" . $c . "\">
                                    <div class=\"card-body\">
                                        <p class=\"card-text\">" . $StreamFetchSourceTwo['mensagem'] . "</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style=\"background-color:#ffffff;height:30px;\">
                            <h6 class=\"text-center\" style=\"color:rgb(125,125,125);font-family:'Noto Sans', sans-serif;padding-top:5px;\">"
                        . \Kernel::getFormatedDate($StreamFetchSourceTwo['datetime']) . "</h6>
                        </div>
                    </div>";
                }
                $paginationMsg->getPagination(DS._employee.DS._messages);
            }

        }



    }
}
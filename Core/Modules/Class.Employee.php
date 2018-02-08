<?php
/**
 *    Created by DevWolf.
 *   Author: Kevin Marques
 *   Date: 08/02/2018 - 10:39
 */

namespace Modules;


use Interfaces\Employ;
use Modules\Users;
use PDO;
class Employee extends Users implements Employ
{
    public function visualizaMsg($isDoc = false){
        echo "<article style=\"margin-top:80px;\">
        <div class=\"container\" style=\"height:auto;\">
            <div class=\"row\" style=\"height:auto;\">";
            self::getMessages($isDoc);
            echo "</div>
        </div>
    </article>";

    }
    public function getMessages($isDoc){
        //Recupera as informações de mensagem
        $StreamGetAllData = $this->getSql()->query("SELECT ".__MESSAGE.".*, ".__CATEGORY.".id, ".__CATEGORY.".cor, ".__CATEGORY.".nome AS catname  FROM ".__MESSAGE." LEFT JOIN ".__CATEGORY." ON ".__MESSAGE.".categoria=".__CATEGORY.".id");
        //Lista todas as categoras em uma array
        $count = 0;
        while($StreamFetchSource = $StreamGetAllData->fetch(PDO::FETCH_ASSOC)){
            $count++;
            $getAllusers = array();
            $showMsg = false;
            $getAllusers = json_decode($StreamFetchSource['lista_matricula'],true);
            $categoryColor = empty($StreamFetchSource['cor']) ? "#999999" : $StreamFetchSource['cor'];
            $categoryName = empty($StreamFetchSource['catname']) ? _tr("Texts")->not_categorized : $StreamFetchSource['catname'];
            if(in_array('all',$getAllusers)){
                $showMsg = true;
            }
            elseif(in_array($_SESSION[sigEnr__],$getAllusers)){
                $showMsg = true;
            }
            else{
                $showMsg = false;
            }
            //Caso seja true mostra a mensagem
            #DEFINE A INTERFACE
            //----------------------------------------------------------//
            #Interface para Documento
            if($isDoc){
                if($showMsg){
                    echo "<div class=\"col-3\" id=\"doc\" style=\"margin-bottom:20px;\">
                    <div style=\"height:30px;background-color:".$categoryColor.";\">
                        <h6 class=\"text-center\" style=\"padding-top:5px;\"> ".$categoryName."</h6>
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
                    <div role=\"tablist\" id=\"accordion-".$count."\">
                        <div class=\"card\">
                            <div class=\"card-header\" role=\"tab\" style=\"height:33px;padding:6px;\">
                                <h6 class=\"text-center mb-0\"><a data-toggle=\"collapse\" aria-expanded=\"false\" aria-controls=\"accordion-".$count." .item-".$count."\" href=\"#accordion-".$count." .item-".$count."\">"._tr("Texts")->description." </a></h6>
                            </div>
                            <div class=\"collapse item-".$count."\" role=\"tabpanel\" data-parent=\"#accordion-".$count."\">
                                <div class=\"card-body\">
                                    <p class=\"card-text\">".$StreamFetchSource['mensagem']."</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style=\"background-color:#ffffff;height:30px;\">
                        <h6 class=\"text-center\" style=\"color:rgb(125,125,125);font-family:'Noto Sans', sans-serif;padding-top:5px;\">".\Kernel::getFormatedDate($StreamFetchSource['datetime'])."</h6>
                    </div>
                </div>";
                }
            }
            #Interface para Mensagem padrão
            else{

            }
        }
    }
}
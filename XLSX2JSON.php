<?php
class XLSX2JSON{

    private headers = array();

    function __construct($elem){
        if(get_class($elem)!="XLSXElement") return;
        foreach($elem->getSheetList() as $id){
            $cols = $elem->getSheetColumns($id);
            $header[] = $elem->getSheetRow($id,0);
        }
    }

    //key,key,key
    function setSheet(...$keys){

    }

    //array(sheetKey=>array(headers))
    function setHeaders($headers){

    }

}
?>

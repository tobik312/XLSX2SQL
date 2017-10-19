<?php
class XLSX2JSON{

    private $elem;
    private $headers = array();
    private $cols = array();
    private $jsonArray;

    function __construct($elem){
        if(get_class($elem)!="XLSXElement") return;
        $this->elem = $elem;
        foreach($this->elem->getSheetList() as $id){
            $this->cols[$id][] = $this->elem->getSheetColumns($id);
            $this->header[$id] = $this->elem->getSheetRow($id,0);
        }
        $this->compile();
    }

    private function compile(){
        $tmp = array();
        foreach($this->header as $sheetKey=>)
    }

    //key,key,key
    function setSheet(...$keys){
        $this->cols = array();
        foreach($keys as $key){
            $this->cols[] = $this->elem->getSheetColumns($key);
        }
        $this->compile();
    }

    //array(sheetKey=>array(headers))
    function setHeaders($headers){
        $this->header = array();
        foreach($headers as $key=>$headers){
            $this->header[$key] = $headers;
        }
        $this->compile();
    }

    function getJSONString(){

    }

}
?>

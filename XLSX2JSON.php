<?php
class XLSX2JSON{

    private $elem;
    private $headers = array();
    private $cols = array();
    private $sheetsList = array();
    private $jsonArray = array();
    private $defaultHeader = true;

    function compile(){
        $this->jsonArray = array();
        foreach($this->sheetsList as $sheetName=>$id){
            $this->cols[$id] = $this->elem->getSheetColumns($id);
            if(!$this->cols[$id]) $this->cols[$id] = array();
            if($this->defaultHeader){
                $this->headers[$id] = $this->elem->getSheetRow($id,0);
            }else{

            }
            foreach($this->cols[$id] as $i=>$col){
                $tmpArray = $col;
                if($this->defaultHeader) array_shift($tmpArray);
                $this->jsonArray[$sheetName][$this->headers[$id][$i]] = $tmpArray;
            }
        }
    }

    function __construct($elem){
        if(get_class($elem)!="XLSXElement") return;
        $this->elem = $elem;
        $this->sheetsList = $this->elem->getSheetList();
    }

    //key,key,key
    function setSheets(...$keys){
        $this->sheetsList = array();
        $tmpList = $this->elem->getSheetList();
        foreach($keys as $key){
            if(is_int($key)){
                $value = $key;
                $key = array_search($key,$tmpList);
            }else{
                $value = array_key_exists($key,$tmpList) ? $tmpList[$key] : null;
            }
            $this->sheetsList[$key] = $value;
        }
    }

    //array(sheetKey=>array(headers))
    //string - num - auto 1,2,3...
    function setHeaders($headers){
        $this->defaultHeader = false;
        if($headers=='num'){

        }
        $this->header = $headers;

    }

    function getJSONString(){
        $this->compile();
        return json_encode($this->jsonArray);
    }

}
?>

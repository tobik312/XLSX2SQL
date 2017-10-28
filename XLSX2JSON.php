<?php
class XLSX2JSON{

    private $elem;
    private $headers = array();
    private $cols = array();
    private $sheetsList = array();
    private $jsonArray = array();
    private $defaultHeader = true;

    const ARRAY = "arrayType";

    function compile(){
        $this->jsonArray = array();
        foreach($this->sheetsList as $sheetName=>$id){
            $this->cols[$id] = $this->elem->getSheetColumns($id);
            if(!$this->cols[$id]) $this->cols[$id] = array();
            if($this->defaultHeader){
                $this->headers[$id] = $this->elem->getSheetRow($id,0);
            }else{
                if($this->headers!=self::ARRAY){
                    $this->headers[$sheetName] = array_key_exists($sheetName,$this->headers) ? $this->headers[$sheetName] : "empty";

                }
            }
            foreach($this->cols[$id] as $i=>$col){
                $tmpArray = $col;
                if($this->defaultHeader){
                    array_shift($tmpArray);
                }else{
                    $this->headers[$id][$i] = $i;
                }
                $tmpHeader = ($this->headers==self::ARRAY) ? $i : $this->headers[$id][$i];
                $this->jsonArray[$sheetName][$tmpHeader] = $tmpArray;
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
    //string - ARRAY - auto 1,2,3...
    function setHeaders($headers){
        $this->defaultHeader = false;
        $this->headers = $headers;
    }

    function getJSONString(){
        $this->compile();
        return json_encode($this->jsonArray);
    }

}
?>

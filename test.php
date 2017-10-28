<?php 

require('XLSXElement.php');

$file = new XLSXElement("db.xlsx");

$types[] = array();

foreach($file->getSheetList() as $sheetName=>$sheetNum){
    $types[$sheetName] = array();
    foreach($file->getSheetColumns($sheetNum) as $colKey=>$col){
        foreach($col as $colKey=>$colData){
            $numericTypes = array();
            $type = null;
            if($colKey==0)
                continue;
            if(is_numeric($colData)){
                $numericTypes[] = (int) $colData == $colData ? "int" : "float";
                $type = in_array("float",$numericTypes) ? "float" : "int";
            }else{
                $date = strtotime($colData);
                $colDate = date("Y-m-d",$date);
                $type = $colDate == $colData ? "date" : "string";
            }
        }
        $types[$sheetName][] = $type;
        
    }
}

foreach($types as $key=>$element){
    if(empty($element)){
        array_shift($types);
    }
}

var_dump($types);

?>
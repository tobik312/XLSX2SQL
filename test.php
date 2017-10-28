<?php 

require('XLSXElement.php');

$file = new XLSXElement("db.xlsx");


$sheetList = $file->getSheetList();

$types = array();

foreach($sheetList as $sheetName=>$sheetNum){
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

$headers = array();

foreach($sheetList as $sheetName=>$sheetNum){
    $headers[$sheetName] = array();
    $firstRow = $file->getSheetRow($sheetNum,0);
    $rowTypes = $types[$sheetName];
    foreach($firstRow as $rowNum=>$rowName){
        $headers[$sheetName][$rowName] = $rowTypes[$rowNum];
    }
}


var_dump($headers);






?>
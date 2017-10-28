<?php 

require('XLSXElement.php');

$file = new XLSXElement("db.xlsx");


$sheetList = $file->getSheetList();


//getTypes
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
                $type = in_array("float",$numericTypes) ? "float" : "int(11)";
            }else{
                $date = strtotime($colData);
                $colDate = date("Y-m-d",$date);
                $type = $colDate == $colData ? "date" : "varchar(100)";
            }
        }
        $types[$sheetName][] = $type;
    }
}


//getHeaders
$headers = array();

foreach($sheetList as $sheetName=>$sheetNum){
    $headers[$sheetName] = array();
    $firstRow = $file->getSheetRow($sheetNum,0);
    $rowTypes = $types[$sheetName];
    foreach($firstRow as $rowNum=>$rowName){
        $headers[$sheetName][$rowName] = $rowTypes[$rowNum];
    }
}

//getSQL
$sqlCode = "";

foreach($headers as $tabName=>$header){
    $sqlCode.="CREATE TABLE $tabName (\n\r";
    foreach($header as $colName=>$colType){
        $sqlCode.="$colName $colType NOT NULL,\n\r";
    }
    reset($header);
    $first_key = key($header);
    $sqlCode.="PRIMARY KEY ($first_key)\n\r";
    $sqlCode.=") ENGINE=InnoDB DEFAULT CHARSET=utf8\n\r\n\r";
}

echo $sqlCode;

?>
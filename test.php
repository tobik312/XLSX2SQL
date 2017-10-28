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
    $sheetInfo[$sheetName] = $file->getSheetInfo($sheetNum);
    $firstRow = $file->getSheetRow($sheetNum,0);
    $rowTypes = $types[$sheetName];
    foreach($firstRow as $rowNum=>$rowName){
        $headers[$sheetName][$rowName] = $rowTypes[$rowNum];
    }
}

//getSQL
$sqlCode = "";
foreach($headers as $tabName=>$header){
    $sqlCode .= "DROP TABLE IF EXISTS $tabName;\n\rCREATE TABLE $tabName (\n\r";
    foreach($header as $colName=>$colType){
        $sqlCode .= "$colName $colType NOT NULL,\n\r";
    }
    ///???????????????????/// \|/ ///???????????????????///
    $sqlCode .= "PRIMARY KEY (???)\n\r";
    $sqlCode .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8\n\r\n\r";
    $sqlCode .= "INSERT INTO $tabName VALUES\n\r";
    for($i = 1; $i < $sheetInfo[$tabName]['rows']; $i++){
        $tmp = $file->getSheetRow($sheetInfo[$tabName]['id'],$i);
        $sqlCode .= "(";
        for($j = 0; $j < count($tmp); $j++){
            $sqlCode .= "$tmp[$j]";
            $sqlCode .= $j == count($tmp) - 1? "" : ",";
        }
        $sqlCode .= $i+1 == $sheetInfo[$tabName]['rows'] ? ");\n\r" : "),\n\r";
    }
    $sqlCode .= "\n\r";
}

echo $sqlCode;

?>
<?php 

require('XLSXElement.php');

$file = new XLSXElement("db.xlsx");

foreach($file->getSheetList() as $sheetNum){
    foreach($file->getSheetColumns($sheetNum) as $colKey=>$col){
        foreach($col as $colKey=>$colData){
            $numericTypes = array();
            $type = null;
            if($colKey==0)
                continue;
            if(is_numeric($colData)){
                if((int) $colData == $colData){
                    $numericTypes[] = "int";
                }else if((float) $colData == $colData){
                    $numericTypes[] = "float";
                }

                if(in_array("float",$numericTypes)){
                    $type = "float";
                }else{
                    $type = "int";
                }
            }else{
                $date = strtotime($colData);
                $colDate = date("Y-m-d",$date);
                if($colDate == $colData)
                    $type = "date";
                else
                    $type = "string";
            }
        }
        echo "$type<br><br>";
    }
    echo "<br><br><br><br>";
}


?>
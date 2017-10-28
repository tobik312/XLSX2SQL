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






/*

$headers = $file->getSheetRow(3,0);

foreach($headers as $num=>$col){
    $data = $file->getSheetColumn(3,$num);
    $typeOf = null;
    $types = array();
    foreach($data as $elNum=>$elVal){
        if($elNum==0)
            continue;
        if(is_numeric($elVal)){
            if((int) $elVal == $elVal){
                $types[] = "int";
            }else if((float) $elVal == $elVal){
                $types[] = "float";
            }
        }else{
            $typeOf = "string";
            break;
        }
    }
    if(in_array("float",$types)){
        $typeOf = "float";
    }else{
        $typeOf = "int";
    }
}

/*
foreach($file->getSheetRow(3,1) as $ex){
    echo "$ex ";
    if(is_numeric($ex)){
        if((int) $ex==$ex){
            echo "int<br>";
        }else if((float) $ex==$ex){
            echo "float<br>";
        }
    }else
        echo "string<br>";
}


$types = array();
foreach($file->getSheetColumn(3,3) as $ex){
    if(!is_numeric($ex))
        continue;
    if((int) $ex==$ex){
        $types[] = "int";
    }else if((float) $ex==$ex){
        $types[] = "float";
    }
}
if(in_array("float",$types)){
    echo "float";
}else{
    echo "int";
}






















/*
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }

    }
    return rmdir($dir);
}
function dayConvertion($days){
    $start = array(1900,1,1);
    while($days!=0){
        if($days>365){
            $days-= ($start[0]%4==0) ? 366 : 365;
            $start[0]++;
        }else{
            $month_days = cal_days_in_month(CAL_GREGORIAN,$start[1],$start[0]);
            if($days>$month_days){
                $days-=$month_days;
                $start[1]++;
            }else{
                $start[2] = $days;
                $days=0;
            }
        }
    }
    return $start;
}

$zip = new ZipArchive();
$filename = "db.xlsx";
$tmp = explode(".",$filename);
$sqlname = $tmp[0]."sql";
if ($zip->open($filename)===TRUE) {
    $folder_name = time()."_local";
    mkdir($folder_name);
    $zip->extractTo("./".$folder_name);
    //sharedStrings
    $sharedStrings = array();
    $sharedString_xml = new SimpleXMLElement(file_get_contents($folder_name."/xl/sharedStrings.xml"));
    foreach($sharedString_xml->children() as $val){
        $sharedStrings[] = (String) $val->t;
    }
    //workBook
    $workbook = new SimpleXMLElement(file_get_contents($folder_name."/xl/workbook.xml"));
    $sheets = $workbook->sheets[0];
    $sheets_length = $sheets->count();
    for($i=1;$i<=$sheets_length;$i++){
        $sheet_info = $sheets->sheet[$i-1];
        $sheet = new SimpleXMLElement(file_get_contents($folder_name."/xl/worksheets/sheet$i.xml"));
        $sheet_rows = $sheet->sheetData[0];
        echo "<h5>$i ".$sheet_info['name']."</h5>";
        //sql
        $tableName = $sheet_info['name'];
        $types = array();
        foreach($sheet_rows->children()[1]->c as $c){
            if(isset($c['t'])){
                $types[] = "varchar(125)";
            }else if(isset($c['s'])){
                $types[] = "date";
            }else{
                if(ctype_digit((string)$c->v)){
                    $types[] = "int";
                }else{
                    $types[] = "float";
                }
                
            }
        }
        //
        foreach($sheet_rows->children() as $row){
            $r = array();
            foreach($row->c as $c){
                if(isset($c['t'])){
                    $r[] = $sharedStrings[(int) $c->v];
                }else if(isset($c['s'])){
                    $r[] = implode("-",dayConvertion($c->v));
                }else{
                    $r[] = $c->v;
                }
            }
            //echo implode(" ",$r)."</br>";
        }
    }
    deleteDirectory($folder_name);
}else{
    echo "iz fukd";
}
*/
?>
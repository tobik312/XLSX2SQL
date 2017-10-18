<?php 

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
function createSQL($table, $rows){
    $tableName = $arr['name'];
    $types = array();
    
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
        var_dump($sheet_info);
        foreach($sheet_rows->children() as $row){
            var_dump($row);
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
            var_dump($r);
            //echo implode(" ",$r)."</br>";
        }
    }
    deleteDirectory($folder_name);
}else{
    echo "iz fukd";
}

?>
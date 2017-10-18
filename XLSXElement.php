<?php
require "XLSX2SQL.php";

class XLSXElement{
    
    //Elems
    private $folder_name;
    private $sharedStrings = array();
    //info - array(),data - array();
    private $sheets = array();
    private $sheetList = array();
    //Utils
    function deleteDirectory($dir) {
    if(!file_exists($dir)) return true;
    if(!is_dir($dir)) return unlink($dir);
    foreach (scandir($dir) as $item) {
        if($item == '.' || $item == '..') continue;
        if(!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
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
    //
    function __construct($filename){
        if(mime_content_type("db.xlsx")!="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") return;
        $this->folder_name = time()."_tmp";
        mkdir($this->folder_name);
        $zip = new ZipArchive();
        if ($zip->open($filename)===TRUE) {
            $zip->extractTo("./$this->folder_name");
            //sharedStrings
            $sharedString_xml = simplexml_load_file("$this->folder_name/xl/sharedStrings.xml");
            foreach($sharedString_xml->children() as $val){
                $this->sharedStrings[] = (String) $val->t;
            }
            //workBook
            $workbook = simplexml_load_file("$this->folder_name/xl/workbook.xml");
            $sheets = $workbook->sheets[0];
            for($i=1;$i<=$sheets->count();$i++){
                $this->sheets[$i] = array();
                $sheet = simplexml_load_file("$this->folder_name/xl/worksheets/sheet$i.xml");
                $sheet_name = (string) $sheets->sheet[$i-1]['name'];
                $sheet_rows = $sheet->sheetData[0];
                $this->sheetList[$sheet_name] = $i;
                $this->sheets[$i]['info'] = array("id"=>$i,"name"=>$sheet_name); 
                foreach($sheet_rows->children() as $row){
                    foreach($row->c as $c){
                        if(isset($c['t'])){
                            $r[] = $this->sharedStrings[(int) $c->v];
                        }else if(isset($c['s'])){
                            $r[] = implode("-",$this->dayConvertion((int)$c->v));
                        }else{
                            $r[] = (string) $c->v;
                        }
                    }
                    $this->sheets[$i]['data'][] = $r;
                }
            }
        }
        $this->deleteDirectory($this->folder_name);
    }
    
    //SheetFunction
    function getSheetList(){
        return $this->sheetList;
    }
    
    function getSheetData($key){
        $key = is_int($key) ? $key : $this->sheetList[$key];
        return array_key_exists($key,$this->sheets) ? $this->sheets[$key]['data'] : null;
    }
    
    function getSheetInfo($key){
        $key = is_int($key) ? $key : $this->sheetList[$key];
        return array_key_exists($key,$this->sheets) ? $this->sheets[$key]['info'] : null;
    }
    //SharedStrings
    function getSharedStrings(){
        return $this->sharedStrings;
    }
    //Conversion
    /*
        Class XLSX2SQL
    */
    
}
?>
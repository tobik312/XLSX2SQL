<?php
class XLSXElement{
    
    //Elems
    private $folder_name;
    private $sharedStrings = array();
    
    //info - array(),rows - array(),cols - array()
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
        if(!file_exists($filename) || empty(trim($filename)) || mime_content_type($filename)!="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") return;
        $this->folder_name = time()."_tmp";
        mkdir($this->folder_name);
        $zip = new ZipArchive();
        if ($zip->open($filename)===TRUE) {
            $zip->extractTo("./$this->folder_name");
            //sharedStrings
            if(file_exists("$this->folder_name/xl/sharedStrings.xml")){
                $sharedString_xml = simplexml_load_file("$this->folder_name/xl/sharedStrings.xml");
                foreach($sharedString_xml->children() as $val){
                    $this->sharedStrings[] = (String) $val->t;
                }
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
                $this->sheets[$i]['info'] = array("id"=>$i,"name"=>$sheet_name,"rows"=>count($sheet_rows->children()),"cols"=>0);
                $row_id = 0;
                foreach($sheet_rows->children() as $row){
                    $r = array();
                    foreach($row->c as $c){
                        if(isset($c['t'])){
                            $r[] = $this->sharedStrings[(int) $c->v];
                        }else if(isset($c['s'])){
                            $r[] = implode("-",$this->dayConvertion((int)$c->v));
                        }else{
                            $r[] = (string) $c->v;
                        }
                    }

                    if($this->sheets[$i]['info']['cols']<count($row)) $this->sheets[$i]['info']['cols'] = count($row);
                    $this->sheets[$i]['rows'][] = $r;

                    foreach($r as $col_id=>$col){
                        $this->sheets[$i]['cols'][$col_id][$row_id] = $col;
                    }
                    $row_id++;
                }
            }
        }
        $this->deleteDirectory($this->folder_name);
    }
    
    //SheetFunction
    function getSheetList(){
        return $this->sheetList;
    }
    
    function getSheetRows($key){
        $key = is_int($key) ? $key : $this->sheetList[$key];
        return array_key_exists($key,$this->sheets) ? $this->sheets[$key]['rows'] : null;
    }

    function getSheetRow($key,$row){
          return $this->getSheetRows($key)[$row];
    }
    
    function getSheetInfo($key){
        $key = is_int($key) ? $key : $this->sheetList[$key];
        return array_key_exists($key,$this->sheets) ? $this->sheets[$key]['info'] : null;
    }
    
    function getSheetColumns($key){
        $key = is_int($key) ? $key : $this->sheetList[$key];
        return array_key_exists($key,$this->sheets) ? $this->sheets[$key]['cols'] : null;
    }

    function getSheetColumn($key,$row){
          return $this->getSheetColumns($key)[$row];
    }

    //SharedStrings
    function hasSharedStrings(){
        return (boolean) $this->sharedStrings;
    }

    function getSharedStrings(){
        return $this->sharedStrings;
    }
    
    function getSharedString($key){
        return array_key_exists($key,$this->sharedStrings) ? $this->sharedStrings[$key] : null;
    }

}
?>

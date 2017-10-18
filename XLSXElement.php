<?php 
class XLSXElement{
    
    //Elems
    private $folder_name;
    private $sharedStrings = array();
    //info - array(),data - array();
    private $sheets = array();
    //Utils
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
    //
    function _contructor($file){
        $this->folder_name = time()."_tmp";
        mkdir($this->folder_name);
        $zip = new ZipArchive();
        if ($zip->open($filename)===TRUE) {
            $zip->extractTo("./$this->folder_name");
            //sharedStrings
            $sharedString_xml = new SimpleXMLElement(file_get_contents("$this->folder_name/xl/sharedStrings.xml"));
            foreach($sharedString_xml->children() as $val){
                $this->sharedStrings[] = (String) $val->t;
            }
            //workBook
            $workbook = new SimpleXMLElement(file_get_contents("$this->folder_name/xl/workbook.xml"));
            $sheets = $workbook->sheets[0];
            $sheets_length = $sheets->count();
            for($i=1;$i<=$sheets_length;$i++){
                $sheet = new SimpleXMLElement(file_get_contents("$this->folder_namee/xl/worksheets/sheet$i.xml"));
                $sheet_rows = $sheet->sheetData[0];
                $this->sheets[$i]['info'] = array("id"=>$i,"name"=>$sheets->sheet[$i-1]['name']); 
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
                    $this->sheets[$i]['data'][] = $r;
                }
            }
            //
        }
    }
    
    function getSheetInfo($id){
        return $this->sheets[$id];
    }
    
    function _deconstructor(){
       $this->deleteDirectory($this->folder_name); 
    }
    
}

$elem = new XLSXElement("db.xlsx");
var_dump($elem->getSheetInfo(1));

?>
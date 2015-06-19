<?php

class skeleton {

 public $export_data, $import_data;
 public $jid, $jf, $jattr;
 public $printer_name, $jobs_path;

 
 public function __construct(&$auth,&$import_data=null, $job_id=null, $job_file=null, $job_attr=null, $printer_name=null) {
    $this->import_data = $import_data;
	$this->jid = $job_id;
	$this->jf = $job_file;
	$this->jattr = (array) $job_attr;
	
	$this->printer_name = $printer_name;
	
	$this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/'.$this->printer_name;
	
 }
 
 public function execute() {
 
    $this->export_data = 'test';
    return true;
    //return $this->import_data;
	 
    return false;	
 }
 
    function write2disk($file,$data=null) {
	        if (!defined('SERVER_LOG'))
			    return null; 

            if ($fp = @fopen ($file , "a+")) {
	        //echo $file,"<br>";
                 fwrite ($fp, $data);
                 fclose ($fp);

                 return true;
            }
            else {
              echo "File creation error ($file)!<br>";
            }
            return false;

    }  
}


?>
<?php

require_once('skeleton.php');

class addhoc extends skeleton {

 var $path;  
 
 public function __construct(&$auth,&$fp=null, $job_id=null, $job_file=null, $job_attr=null, $printer_name=null) {
 
    parent::__construct($auth,$fp,$job_id,$job_file,$job_attr,$printer_name);
	
    //$this->import_data = $import_data;
	$this->jid = $job_id;
	$this->jf = $job_file;
	$this->jattr = (array) $job_attr;
	
	$this->printer_name = $printer_name;
	
	$this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/'.$this->printer_name;	
  
    $this->path = $_SERVER['DOCUMENT_ROOT'] .pathinfo($_SERVER['PHP_SELF'],PATHINFO_DIRNAME).'/';
  
    if (is_object($auth)) {
	   self::write2disk($this->path.'addhoc.log','yes');
	}
	//self::write2disk($this->jobs_path.'/job'.$job_id.'.dropbox',"a\r\n"); 
 }
 
 //override
 public function execute($addhoc_code=null, $testbed=null) {
 
    if ($addhoc_code) {
	
	       if (!$testbed)
	         self::write2disk($this->path.'addhoc.log',$addhoc_code."\r\n\r\n");
	
	       $code = str_replace("<?php\r\n","",str_replace("\r\n?>","",$addhoc_code));
		   
		   //eval php
           @trigger_error("");
           $result = eval($code);
           $error = error_get_last();		
		   //print_r($error);
		   /*if ($error['message']) //fetch every last error/warning
		     $result .= $error['message'] .' : line '.$error['line'] ;
           */   
           //$result .= 'test...............';

           if ($testbed)
             return ($result); 		   
		   
		   if ($result)
             $bytes = self::_write($result); 
		   
           return ($bytes);		   
	}
	
    return false;	
 }
}
?>
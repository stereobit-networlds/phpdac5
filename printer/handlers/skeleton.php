<?php

class skeleton {

 public $export_data, $import_data;
 public $jid, $jf, $jattr;
 public $printer_name, $jobs_path;
 public $jowner;
 
 public function __construct($user,$data=null, $job_id=null, $job_file=null, $job_attr=null, $printer_name=null) {

	$this->jid = $job_id;
	$this->jf = $job_file;
	$this->jattr = (array) $job_attr;
	$this->jowner = $user;
	
	$this->printer_name = $printer_name;
	
	$this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/'.$this->printer_name;	
	$this->admin_path = $_SERVER['DOCUMENT_ROOT'] .'/admin/'.$this->printer_name;
	
	//OPEN & READ HERE
	$this->import_data = $this->_read();	
	
 }
 
 public function execute() {
 
    $this->export_data = 'test';
    return true;
    //return $this->import_data;
	 
    return false;	
 }
 
 function _write($data=null) {
   
    if (!$length = strlen($data)) 
	  return false;
 
	/*fseek($this->fp, 0);
	$bytes = fwrite($this->fp, $data, $length);
	ftruncate($this->fp, $length); */
	
	if ($fp = fopen($this->jf, "w")) {	
	   $bytes = fwrite($fp, $data, $length);
	   //ftruncate($fp, $length);
       fclose($fp);	   
	   return ($bytes);
	}

	return false; 
 }
 
 function _writeutf8($data=null) {
   
    if (!$length = strlen($data)) 
	  return false;
	
	if ($fp = fopen($this->jf, "wb")) {	
	
       // Now UTF-8 - Add byte order mark 
       fwrite($f, pack("CCC",0xef,0xbb,0xbf)); 
	   
	   $bytes = fwrite($fp, $data, $length);
       fclose($fp);	   
	   return ($bytes);
	}

	return false; 
 } 
 
  function _read() {
    $data = null; 
   
	if ($fp = fopen($this->jf, "r+b")) {
        $data = fread($fp, filesize($this->jf)); 
		fclose($fp);
	}	
	
	return ($data);
 }
 
 function _set_attr($attr_name=null, $value=null) {
 
    if (($attr_name) && ($value))
       $this->jattr[$attr_name] = $value;
 }
 
 function _get_attr($attr_name=null) {
 
    if (!$attr_name) {//all as array
	  if (is_array($this->jattr))
	    return ($this->jattr); 
	  else
        return array();//null array	  
	}
	else
      return ($this->jattr[$attr_name]);
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
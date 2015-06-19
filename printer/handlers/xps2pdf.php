<?php

require_once('skeleton.php');

//http://online2pdf.com
//http://xps2pdf.co.uk/

class xps2pdf extends skeleton {
 
 public function __construct($user,$data=null, $job_id=null, $job_file=null, $job_attr=null, $printer_name=null) {
 
    parent::__construct($user,$data,$job_id,$job_file,$job_attr,$printer_name);
    //$this->import_data = $import_data;
	
	$this->jid = $job_id;
	$this->jf = $job_file;
	$this->jattr = (array) $job_attr;	
  

	self::write2disk('xps2jpg.log','yes');

 }
 
 //override
 public function execute($test=false) {

    $ret = $this->xps2pdf($test);
 
	return ($ret);
 } 
 
 public function xps2pdf($test=false) {
 
	
	
	  $ch = curl_init();
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
      curl_setopt($ch, CURLOPT_URL, "http://xps2pdf.co.uk/");
      curl_setopt($ch, CURLOPT_POST, true);
      // same as <input type="file" name="file_box">
      $post = array(
        "fileUpload"=>'@'.$this->jf
		//, "userfile_name"=>$this->jattr['job-name']
      );
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
      $response = curl_exec($ch);
	  
	
	  //TEST..................
      if ($_GET['test']) { 
	     //echo '>',$this->jf;
	     header("Content-type: application/pdf");
	     echo $response;
	  }	 
	  
	  $bytes = self::_write($response);
	  return ($bytes);
 } 
 
}

/********************************************************************************/
//test
if ($_GET['test']) {
$test_file = getcwd() . '/magda.xps';
//echo 'test_file:', $test_file,'<br>';
if ($fp = fopen($test_file, "r+b")) {  

  $testbed = new xps2pdf('admin',$fp,1,$test_file,null,'test.printer');
  $ret = $testbed->execute();
  
  //echo 'result:',$ret,'<br>';
  
  //read data
  //$this->fp = $fp;
  //$this->import_data = fread($this->fp, filesize($job_file)); 
  
  fclose($fp);
}
else
  echo 'file is not readable<br>';
}  
/****************************************************************************/

?>
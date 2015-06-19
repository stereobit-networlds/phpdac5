<?php


require_once("ippclient/PrintIPP.php");

class ippclient {
 protected $export_data, $import_data;
 protected $jid, $jf, $jattr;
 
 protected $printer_name;

 
 public function __construct($import_data=null, $job_id=null, $job_file=null, $job_attr=null, $printer_name=null) {
    $this->import_data = $import_data;
	$this->jid = $job_id;
	$this->jf = $job_file;
	$this->jattr = (array) $job_attr;
	
	$this->printer_name = $printer_name;
	
	$this->ip = 'www.networlds.org';
	
	self::_print();	
	//self::_printCURL();
 }
 
 public function execute() {
 
    $this->export_data = null;
	 
	if ($this->export_data = self::_getJob()) {
	
       return $this->export_data;
	}   
	else
       return false; 
 
    return false;	 
 }
 
 
 //print client, based on PrintIPP lib
 protected function _print() {
 
    $ipp = new PrintIPP();
    //$ipp->setUnix();
    //$ipp->setHost("localhost:80/printers/");
    $ipp->setPort(80);
    $ipp->setPrinterUri("http://".$this->ip."/printers/index.php");//"ipp://localhost:631/printers/Parallel_Port_1");
    $ipp->setData('test 123');//$this->import_data
    $ipp->setUserName($this->printer_name);	
    $ipp->printJob(); 
	
	unset($ipp);
 }
 
 //print client, based on curl 
 protected function _printCURL() {
 
	$this->output = self::_stringjob() . $this->import_data;
 
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "IPP Server as Client");
    curl_setopt($ch, CURLOPT_URL, "http://".$this->ip."/printers/index.php");
    curl_setopt($ch, CURLOPT_POST, true);
    $post = array(
         "Content-Type" => "application/ipp",
         "Data" => $this->output,
         //"File" => $this->data
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
    $response = curl_exec($ch);
	
	//get job-id from response
	//.....
	//.....
	
	/*  
	if ($this->export_data = $response)
	     return $this->export_data;
	else
         return false; 
 
    return false;	*/
 } 
 
 //create print job command
 protected function _stringJob() {
 
  $ipp = new PrintIPP();
  
  $ipp->setPort(80);
  $ipp->setPrinterUri("http://".$this->ip."/printers/index.php");//"ipp://localhost:631/printers/Parallel_Port_1");
  $ipp->setData('test 123');//$this->import_data
  $ipp->setUserName($this->printer_name);	  
 
  $stringjob = chr(0x01) . chr(0x01) // 1.1  | version-number
   . chr(0x00) . chr(0x02) // Print-Job | operation-id
   . $ipp->meta->operation_id //operation-id
   . chr(0x01) // start operation-attributes | operation-attributes-tag
   . $ipp->meta->charset
   . $ipp->meta->language
   . $ipp->meta->printer_uri
   . $ipp->meta->username
   . $ipp->meta->jobname
   . $ipp->meta->fidelity
   . $ipp->meta->document_name
   . $ipp->meta->mime_media_type
   . $operationattributes;
  if ($ipp->meta->copies || $ipp->meta->sides || $ipp->meta->page_ranges || !empty($jobattributes)) 
  {
   $stringjob .=
    chr(0x02) // start job-attributes | job-attributes-tag
    . $ipp->meta->copies
    . $ipp->meta->sides
    . $ipp->meta->page_ranges
    . $jobattributes
    ;
  }
  $stringjob.= chr(0x03); // end-of-attributes | end-of-attributes-tag 
 
  unset($ipp);
 
  return ($stringjob);
 }

 protected function _getJob($job=null) {
 
    //$job = $job ? $job :
 
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "IPP Server as Client");
    curl_setopt($ch, CURLOPT_URL, "http://".$this->ip."/printers/index.php?job=".$job);
    curl_setopt($ch, CURLOPT_POST, true);
    $post = array(
         "Content-Type" => "application/ipp",
         "Data" => $this->output,
         //"File" => $this->data
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
    $response = curl_exec($ch); 
 }

?>
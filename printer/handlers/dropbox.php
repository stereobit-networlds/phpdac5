<?php

require_once('skeleton.php');
require_once('dropbox/DropboxUploader.php');

class dropbox extends skeleton {
 
 public function __construct($user,$data=null, $job_id=null, $job_file=null, $job_attr=null, $printer_name=null) {
 
    parent::__construct($user,$data,$job_id,$job_file,$job_attr,$printer_name);
	
    //$this->import_data = $import_data;
	$this->jid = $job_id;
	$this->jf = $job_file;
	$this->jattr = (array) $job_attr;
	
	$this->printer_name = $printer_name;
	
	$this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/'.$this->printer_name;	

	self::write2disk('dropbox.log','yes');

	//self::write2disk($this->jobs_path.'/job'.$job_id.'.dropbox',"a\r\n"); 
 }
 
 //override
 public function execute() {
 
 
    //send the job file to dropbox
 
	$myfile = $this->jf . '.pdf';
	
 
    // Upload
    $uploader = new DropboxUploader('b.alexiou@stereobit.gr','basilvk7dp');//'balexiou@stereobit.com', 'basilvk7dp');
    $ret = $uploader->upload($this->jf, '/printQueue', $myfile);
	
    //self::write2disk($this->jobs_path.'/job'.$this->jid.'.dropbox',"b\r\n");
	
	if ($ret)
	  return true; 
	  //return ($this->import_data); //as is
	
    return false;	
 }
}
?>
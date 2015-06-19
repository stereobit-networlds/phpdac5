<?php

require_once('skeleton.php');
//require 'tweet/tmhOAuth.php';
//require 'tweet/tmhUtilities.php';

class tweet extends skeleton {

 protected $tmhOAuth; 
 
 public function __construct($user,$data=null, $job_id=null, $job_file=null, $job_attr=null, $printer_name=null) {
  
    parent::__construct($user,$data,$job_id,$job_file,$job_attr,$printer_name);
	
    //$this->import_data = $import_data;
	$this->jid = $job_id;
	$this->jf = $job_file;
	$this->jattr = (array) $job_attr;
	
	$this->printer_name = $printer_name;
	
	$this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/'.$this->printer_name;		
  

	self::write2disk('tweet.log','yes');

	/*
    $this->tmhOAuth = new tmhOAuth(array(
    'consumer_key'    => 'NwjTDX9Xb8s5MHsU9Pfj9Q',
    'consumer_secret' => 'ZbZSyvSJbjPbFzDKV5MIikO1wTann8jAHZX8RVb2w',
    'user_token'      => '56724620-mbmABlNAWVZZ60CDMFVY9aFXnczBSAF2AaJrgyZ1j',
    'user_secret'     => 'ehA7owwaEbPTgCt32H7MuCqDtd8zVXXI0Tmn6c0y94c',
    ));	
	*/
 }
 
 //override
 public function execute() {
 
    //.......................140 chars for twitter
	$this->export_data = substr($this->import_data,0,140);
	//.......................
	
    $code = $this->tmhOAuth->request('POST', $this->tmhOAuth->url('1/statuses/update'), 
	        array('status' => $this->export_data));

    if ($code == 200) {
      tmhUtilities::pr(json_decode($this->tmhOAuth->response['response']));
	  return true;
    } 
	else {
      tmhUtilities::pr($this->tmhOAuth->response['response']);
	  return false;
    }	
	
  
    return false;		
 }
}
?>
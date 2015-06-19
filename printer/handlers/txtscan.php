<?php

require_once('skeleton.php');

class txtscan extends skeleton {
 
 public function __construct($user,$data=null, $job_id=null, $job_file=null, $job_attr=null, $printer_name=null) {
  
    parent::__construct($user,$data,$job_id,$job_file,$job_attr,$printer_name);
	
	//$this->fp = $import_data;
    //$this->import_data = fread($this->fp, filesize($job_file)); //$import_data;
	//if ($this->fp = fopen($job_file, "r+b"))
	  //$this->import_data = fread($this->fp, filesize($job_file));
	
	//$this->import_data = $import_data;
	$this->jid = $job_id;
	$this->jf = $job_file;
	$this->jattr = (array) $job_attr;
	
	$this->printer_name = $printer_name;
	
	$this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/'.$this->printer_name;		
  

	self::write2disk('txtscan.log','yes');


 }
 
 //override
 public function execute() {
 
    //$ret = $this->import_data;//original text

	//$cleanstring = preg_replace('/[^0-9@.a-z ]+/i', '', (strtolower($uncleanstring))); 
	$this->export_data = ereg_replace('[[:cntrl:]]', '<NEWLINE>', $this->import_data);
	
    /* Replace 2 or more spaces with one */ 
    $this->export_data = preg_replace('/<NEWLINE>\s+/','',$this->export_data);
	$this->export_data = preg_replace('/<NEWLINE>/',' ',$this->export_data);
	
	
	if ($this->send_mails($this->export_data)) {
	
	  //export
      preg_match_all('/(\S+):(.*?)\s{3,}/',$this->export_data, $fields);//fields // /(\S+):(\S+)/
	  preg_match_all('/(\d{5,}\s+\d+)(.*?)\s(\d+),(\d+)\s/',$this->export_data, $items);//item lines // /(\d{7}\s+\d+)(.*?)(\d+)\s*\,\s*(\d+)/
	  //$ret .= '<pre>' . var_export($fields[0],1);
	  $ret .= implode("\r\n",$fields[0]);
	  //$ret .= var_export($items[0],1) . '</pre>';
	  $ret .="\r\n\r\n";
	  $ret .= implode("\r\n",$items[0]);
	
	  $bytes = self::_write($ret);
	  return ($bytes);	
	}
	
	return false;//proceesing until true 
 
 }
 
  function send_mails($data) {
  
    preg_match_all('/([\w\d\.\-\_]+)@([\w\d\.\_\-]+)/mi', $data, $emails);
	$m = '<pre>' . var_export($emails,1);
    self::write2disk('txtscan.log',"\r\n".$m);
	
	if (!empty($emails[0])) {
	
	  foreach ($emails[0] as $i=>$to) {
	     self::sendmail($to);
		 self::write2disk('txtscan.log',"\r\n Mail send to:".$to);
	  }	 
		 
	  return true;	 
	}	 
	return false;
  }

  function parseTextForEmail($text=null) {
  
    $text = $text ? $text : $this->import_data;
	
	//alternative ?
	//preg_match_all(‘/([\w\d\.\-\_]+)@([\w\d\.\_\-]+)/mi’, $text, $matches);
    //var_dump($matches);
  
	$email = array();
	$invalid_email = array();
 
	$text = ereg_replace("[^A-Za-z._0-9@ ]"," ",$text);
 
	$token = trim(strtok($text, " "));
 
	while($token !== "") {
 
		if(strpos($token, "@") !== false) {
 
			$token = ereg_replace("[^A-Za-z._0-9@]","", $token);
 
			//checking to see if this is a valid email address
			if(self::is_valid_email($email) !== true) {
				$email[] = strtolower($token);
			}
			else {
				$invalid_email[] = strtolower($token);
			}
		}
 
		$token = trim(strtok(" "));
	}
 
	$email = array_unique($email);
	$invalid_email = array_unique($invalid_email);
 
	return array("valid_email"=>$email, "invalid_email" => $invalid_email);
 
  }
 
  function is_valid_email($email) {
	if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.([a-z]){2,4})$",$email)) return true;
	else return false;
  }
  
  function sendmail($to=null) {
       
	    $to = $to ? $to : 'b.alexiou@stereobit.gr';
  
                        $headers  = 'MIME-Version: 1.0' . "\r\n";
                        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                        $headers .= 'From: balexiou@stereobit.com' . "\r\n" .
                                    'Reply-To: balexiou@stereobit.com' . "\r\n" .
                                    'IPP-Printer: 1.0-/' . phpversion();						
						$ret = mail($to,'send you a mail', 'hey, how are you?', $headers);
						
	return ($ret);					
  }
 
  //var_dump(parseTextForEmail($text)); 
}
?>
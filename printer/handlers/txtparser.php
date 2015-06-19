<?php

require_once('skeleton.php');

class txtparser extends skeleton {
 
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

	self::write2disk('txtparser.log','yes');


 }
 
 //override
 public function execute() {
 
    //$ret = $this->import_data;//original text

	//$cleanstring = preg_replace('/[^0-9@.a-z ]+/i', '', (strtolower($uncleanstring))); 
	$this->export_data = ereg_replace('[[:cntrl:]]', '<NEWLINE>', $this->import_data);
	
    /* Replace 2 or more spaces with one */ 
    $this->export_data = preg_replace('/<NEWLINE>\s+/','',$this->export_data);
	$this->export_data = preg_replace('/<NEWLINE>/',' ',$this->export_data);
	
	//find : fields
//	$this->export_data = preg_replace('/(\d{7}\s+\d+)(.*?)(\d+)\s*\,\s*(\d+)/','<item>$0</item>',$this->export_data);//item lines
//	$this->export_data = preg_replace('/(\d+).(\d+).(\d+)/', '<number>$0</number>',$this->export_data);//date, numbers
//	$this->export_data = preg_replace('/(\d+)\s*\,\s*(\d+)/', '<float>$1.$2</float>',$this->export_data); //floats replace with.	
//	$this->export_data = preg_replace('/(\S+):(\S+)/', '<field>$1</field><value>$2</value>',$this->export_data); //field/value pairsd	
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
	
//	$this->export_data = preg_replace('/\d{3,}/', '<number>$0</number>',$this->export_data);
//	$this->export_data = preg_replace('/\S{2,}/', '<string>$0</string>',$this->export_data);	
//	$this->export_data = preg_replace('/([^0-9@]+){2,}/', '<code>$1</code>',$this->export_data);
//	$this->export_data = preg_replace('/([^A-Za-z._0-9@]+):([^A-Za-z._0-9@ ]+)\s{2,}/', '<field>$1:$2</field>',$this->export_data);
//	$this->export_data = preg_replace('/([^A-Za-z._0-9@ ]+)\s{2,}/', '<field>$1</field>',$this->export_data);
	
	//find phrases any char without space
    //$this->export_data = preg_replace("/\S+/",'<string>$1</string>',$this->export_data);
	
	//remove white spaces between chars only
	//$this->export_data = preg_replace('#(\d+)\s*\,\s*(\d+)#', '$1.$2',$this->export_data);
	
	//FIRST combine digits with , of typw xx, xx as xx,xx
//	$this->export_data = preg_replace('#(\d+)\s*\,\s*(\d+)#', '<float>$1.$2</float>',$this->export_data);
	//$this->export_data = preg_replace('#(\d+),(\d+)#', '<float>$1.$2</float>',$this->export_data);
	
	//$this->export_data = preg_replace('#(\d+)\s*\.\s*(\d+)#', '$1.$2',$this->export_data);
	//$this->export_data = preg_replace('^\d{1,2}\/\d{1,2}\/\d{4}$', '<date>$1.$2</date>',$this->export_data);
	
	//AFTER find number seqs
//	$this->export_data = preg_replace('/([\d]+)/', '<number>$1</number>',$this->export_data);
 	//AFTER remove white spaces
	//$this->export_data = preg_replace('/\s+/', '',$this->export_data);	

 
 	preg_match_all('/([\w\d\.\-\_]+)@([\w\d\.\_\-]+)/mi', $this->export_data, $emails);
	$ret .= '<pre>' . var_export($emails,1);
	
	preg_match_all('\d*(?:\, \d+)?', $this->export_data, $numbers);
	///([\d]+)/      //xx 
	//\d*(?:\.\d+)?  //xx.xx
	//\d*(?:\,\d+)?  //xx,xx
	//\d*(?:\, \d+)? //xx, xx
	$ret .= "\r\n" . var_export($numbers,1);
	$ret .= '</pre>';
	$ret .= $this->export_data;
	
	//fseek($this->fp, 0);
	//$bytes = fwrite($this->fp, $ret, strlen($ret));
	//ftruncate($this->fp, strlen($ret));
	
	$bytes = self::_write($ret);
	
	//fclose($this->fp);
	
	return ($bytes);	
	
    //return ($ret); 
 
    $mails = self::parseTextForEmail();
	
	if (!empty($mails)) {
	
	  foreach ($mails['valid_email'] as $i=>$to)
	     self::sendmail($to);	
	  
	  //$this->export_data = self::parseTextforNums();
	
	  //return ($this->export_data); //after mod
	  
	  return ($this->import_data);
	  
	  //return true;
	}
	
	//return false;
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
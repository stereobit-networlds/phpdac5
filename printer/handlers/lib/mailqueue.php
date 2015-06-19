<?php


class mailqueue {

    protected $imailto, $imailto_files;
	protected $printer_name, $mailbody, $queued_file, $subject, $from;

    function __construct($printer_name=null, $queued_file=null) {
	
	    $this->imailto = array();
		$this->imailto_files = array();
		
		$this->printer_name = $printer_name;
		$this->mailbody = null; 
		$this->queued_file = $queued_file;
		$this->subject = null;
		$this->from = null;
	}
	
	public function addfrom($from) {
	
        $this->from = $from; 	
	}

	public function addsubject($subject) {
	
        $this->mailbody .= $subject; 	
	}	
	
	public function addmailbody($text) {
	
        $this->mailbody .= $text; 	
	}
	
	public function addmail($email, $id=null) {

		if (self::is_valid_email($email) !== true) 
            return false;		
	
	    if ($id)
			$this->imailto[$id] = strtolower($email);	
		else
			$this->imailto[strtolower($email)] = strtolower($email);	
			
		return ($email);	
	}

	public function removemail($email, $id=null) {	
	
		if (self::is_valid_email($email) !== true) 
            return false;		
	
	    if ($id)
			$this->imailto[$id] = null;	
		else
			$this->imailto[strtolower($email)] = null;	
			
		return ($email);	
	}
	
	public function addmail_attachment($file, $id=null) {

		if (!is_readable($file)) 
            return false;		
	
	    if ($id)
			$this->imailto_files[$id] = $file;	
		else
			$this->imailto_files[$file] = $file;	
			
		return ($file);	
	}

	public function removemail_attachment($file, $id=null) {	
	
		if (!is_readable($file)) 
            return false;			
	
	    if ($id)
			$this->imailto_files[$id] = null;	
		else
			$this->imailto_files[$file] = null;	
			
		return ($file);	
	}	
	
	public function parseTextForEmail($text=null) {
  
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
			if(self::is_valid_email($email) === true) {
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
	
	public function is_valid_email($email) {
		if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.([a-z]){2,4})$",$email)) return true;
		else return false;
	}
  
	public function _sendmail($from=null,$to=null,$subject=null,$body=null,$mailfile=null, $html=false) {
        $from = $from ? $from : $this->printer_name . '@' . str_replace('www.','',$_ENV["HTTP_HOST"]);
        $br = $html ? '<br/>' : '';		
		
	    ini_set("SMTP","localhost"); 
        ini_set('sendmail_from', $from);	
       
	    if (!$to)
            return false;		
		
		if ($mailfile) 
		    $body = file_get_contents($mailfile); 
  
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n"; //<<utf-8
        $headers .= 'From:' . $from . "\r\n" .
                    'Reply-To: '. $from . "\r\n" .
                    'taxcloud-printer: 1.0-/' . phpversion();
        //$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
        //$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";					

        // The message
        //$message = "Line 1\nLine 2\nLine 3";
		//...replace br/cr/lf to \n...
		$message = str_replace("\r\n",'',$body);
        // In case any of our lines are larger than 70 characters, we should use wordwrap()
        $message = wordwrap($message, 70);
		
		if ($br)
		    $message_utf8 = '<html><head><meta charset="utf-8" /></head><body>'.
			                iconv("ISO-8859-7", "UTF-8",str_replace("\n",$br,$message)).
							'</body></html>';
		else
		    $message_utf8 = iconv("ISO-8859-7", "UTF-8",$message);
					
		$ret = @mail($to,$subject,$message_utf8,$headers);
						
	    return ($ret);					
	}	

	//public
	public function email_queue_attachments() {	
	
	    $ret = $this->_sendmail_attachment(true, true);
		return ($ret);
	}
	
	//send mail async files to client
	protected function _sendmail_attachment($ishtml=false, $trackmail=false) { 
 
    //if (!$this->imailto) $this->imailto = 'balexiou@stereobit.com,';
    if (empty($this->imailto))
	    return false;
 
	if (empty($this->imailto_files)) 
	    return false; 
		
	//return true;
	
    $sendermail = $this->printer_name . '@' . str_replace('www.','',$_ENV["HTTP_HOST"]); 
	$from = $this->from ? $this->from : "xpsmail.printer service <".$sendermail.">"; 
    $subject = $this->subject ? $this->subject : date("d.M H:i")." Invoice F=".count($this->imailto_files); 
	if ($ishtml) {
	    $content_type = "text/html";
	    $message = '<html><body>';
	    $message.= date("Y.m.d H:i:s")."\n".count($this->imailto_files)." attachments";
	    if ($trackmail) $message .= "@TRACK@";		
		$message.= '</body></html>';
	}	
	else {
	    $content_type = "text/plain";
        $message = date("Y.m.d H:i:s")."\n".count($this->imailto_files)." attachments";
	    if ($trackmail) $message .= "@TRACK@";
	}
	
    $headers = "From: $from";	

    // boundary 
    $semi_rand = md5(time()); 
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
 
    // headers for attachment 
    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 
 
    // multipart boundary 
    $message = "--{$mime_boundary}\n" . "Content-Type: $content_type; charset=\"iso-8859-1\"\n" .
    "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
 	
    // preparing attachments
	if (!empty($this->imailto_files)) {
      foreach ($this->imailto_files as $targetfile=>$file) {
        if (is_file($file)) {
            $message .= "--{$mime_boundary}\n";
            $fp = @fopen($file,"rb");
			$data =  @fread($fp,filesize($file));
            @fclose($fp);
            $data = chunk_split(base64_encode($data));
            $message .= "Content-Type: application/octet-stream; name=\"".basename($targetfile)."\"\n" . 
            "Content-Description: ".basename($targetfile)."\n" .
            "Content-Disposition: attachment;\n" . " filename=\"".basename($targetfile)."\"; size=".filesize($file).";\n" . 
            "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        }
	  }	
	}
	
    $message .= "--{$mime_boundary}--";
    $returnpath = "-f" . $sendermail;
	
	$ok = false;
	//$mails2send = explode(',',$this->imailto);
	
	//if (!empty($mails2send)) {
	    foreach ($this->imailto as $i=>$to) {
		    if ($to) {
			
			    if ($this->mailbody) {
                    $message = str_replace('</body>',$this->mailbody . '</body>',$message); 
					$this->mailbody = null; //reset
				}
			
			    if ($trackmail) { //track mail
					$trackid = $this->get_trackid();
					$track_message = $this->add_tracker_to_mailbody($message,$trackid,$to,$ishtml);
			   
					$ok = @mail($to, $subject, str_replace('@EMAIL@',$to,$track_message), $headers, $returnpath); 	
			    }
			    else //no track
			        $ok = @mail($to, $subject, str_replace('@EMAIL@',$to,$message), $headers, $returnpath); 	
			}   
	    }
	//}
	if ($ok) { return $i; } else { return 0; }	
	//return $ok;
	} 	
	
	protected function add_tracker_to_mailbody($mailbody=null,$id=null,$receiver=null,$is_html=false) {
	
	   if (!$id) return;
	   $http_host = $_ENV["HTTP_HOST"];
	   
	   $i = $id;//rawurlencode(encode($id));
	
	   if ($receiver) {
	     $r = $receiver;//rawurlencode(encode($receiver));
	     $ret = "<img src=\"http://$http_host/mtrack.php?i=$i&r=$r\" border=\"0\" width=\"1\" height=\"2\">";
	   }
	   else
	     $ret = "<img src=\"http://$http_host/mtrack.php?i=$i\" border=\"0\" width=\"1\" height=\"2\">";
		 
	   $out = str_replace('@TRACK@',$ret,$mailbody); 	 
		  
	   //@file_put_contents($this->jobs_path.'/trackcode.txt',$out);
		 
	   return ($out);	 
	}		
	
	protected function get_trackid() {
	
		 //$i = rand(1000,1999);//++$m;	 
		 //$tid = date('YmdHms') .  $i . '@' . $this->printer_name;
		 $job_name = str_replace('job-','track-',array_pop(explode('/',$this->queued_file)));
		 
		 $tid = $job_name . '@' . $this->printer_name;
		 
		 return ($tid);	
	}	
	
}	
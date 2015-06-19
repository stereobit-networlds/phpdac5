<?php

//require_once('skeleton.php');
//require_once('ImageResize/SimpleImage.php');
//require_once('lib/sha256.php'); //use hash instead

require_once('xpsfactory.php');
require_once('qrfactory.php');

class taxcalc extends skeleton {
 
 var $filename;
 var $admin_path;
 var $dropbox;
 var $taxid, $test_taxid;
 var $efs_header, $efs_footer, $efs_taxid_prefix, $payps_prefix, $hadfss_prefix, $gadfss_prefix,
     $dhfass_title, $dhfass_prefix, $gpayps_prefix, $dot_line, $empty_line, $dhfass_prefix_i, $dhfass_prefix_si,
	 $dhfass_ethed, $dhfass_cmoserr, $dhfass_ctitle, $dhfass_ctech, $dhfass_aposfm, $dhfass_aposprn, $dhfass_empty_z, 
	 $dsym_title, $dsym_prefix, $g_meter, $payps_outlaw_string, $tax_outlaw_string;	 
 
 protected  $itaxserial, $ionomasia, $icommerTitle, $iactLongDescr, $iafm,
		    $idoyDescr, $ipostalAddress, $ipostalAddressNo, $iparDescription,
		    $ipostalZipCode, $ifirmPhone, $ifirmFax, $ifacActivity, $iregistDate,
		    $istopDate, $ideactivationFlag, $idoy, $itaxuser_mail, $itaxactive,			
		    $itaxmode, $itax_sign_x, $itax_sign_y, $itax_sign_mode, $itax_sign_key,
		    $itax_sign_prefix, $itax_sign_lines, $itaxcopies, $itaxheader, $itaxautoz, $itaxwrap, 
			$iwalpha,$iwposition, $iwfile, $iautoresize, $ioptimize, $iftp_server, $iftp_username,
            $iftp_password, $iftp_path, $iftp_pathpersize, $idropbox, $idbfolder;
			
 protected $logpath, $indir, $handler_log, $stats_log;
 protected $dropbox_files, $actasfiscal; 
	
 public function __construct($user,$data=null, $job_id=null, $job_file=null, $job_attr=null, $printer_name=null) {
 
    parent::__construct($user,$data,$job_id,$job_file,$job_attr,$printer_name);
	
	$this->jid = $job_id;
	$this->jf = $job_file;
	$this->jattr = (array) $job_attr;
	$this->printer_name = $printer_name;
	$this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/'.$this->printer_name;	
	
	$this->indir = isset($_SESSION['indir']) ? $_SESSION['indir'].'/' : null;
	$this->log_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->indir;//str_replace('.printer','',$this->printer_name) .'/'; 	
	//echo '<br>LOG PATH:',$this->log_path;
	$this->handler_log = $this->log_path . 'taxcalc.log';
	//echo '<br>HANDLER LOG FILE:',$this->handler_log;
    self::write2disk($this->handler_log, date(DATE_RFC822)."INIT\r\n");

	$this->stats_log = $this->log_path . 'taxstats.log';//null;	
	
	$this->filename = $this->jattr['job-name'];	
	
	$this->admin_path = $_SERVER['DOCUMENT_ROOT'] .'/admin/';
    if ($printer_name) //must be..
	    $this->admin_path .= $printer_name . '/';	
		
	$this->taxid = null; //store valid serial number ..	
	$this->test_taxid = 'XXX00000000';
	
	$this->dot_line = "---------------------------\n";
	$this->empty_line = PHP_EOL;//"\n";
	$this->g_meter = " цемийос ";//"\n";	

    $this->efs_header = "еидийо жояокоцийо дектио - емаянг";// . PHP_EOL;	
	$this->efs_footer = "еидийо жояокоцийо дектио - кгнг" . PHP_EOL;
	$this->efs_taxid_prefix = "ая. лгтяыоу еаждсс ";
	$this->payps_prefix = "пагьс ";
	$this->hadfss_prefix = "гадьсс ";
	$this->gadfss_prefix = "цадьсс ";
	
	$this->dhfass_title = "дектио глеягсиас жояокоцийгс амажояас сгламсгс стоивеиым - ф\n";
	$this->dhfass_prefix = "а/а дек.глея.жояок.амаж. ф #"; 
	$this->gpayps_prefix = "цемийг глеягсиа пагьс ";
	$this->dhfass_prefix_i = "а/а глея.дек.жоя.сгл.стоив. ";
	$this->dhfass_prefix_si = "цем.а/а дек.жоя.сгл.стоив. ";

    $this->dhfass_empty_z = "дем упаявоум пагьс ейдохемтым стоивеиым циа тгм дглиоуяциа тгс цемийгс глеягсиас пагьс\n"; 	
	
	$this->dhfass_ethed = "глея.апос.ехед ";	
	$this->dhfass_cmoserr = "гл.CMOS ERROR ";	
	$this->dhfass_ctitle = "гл.акк.титкоу ";	
	$this->dhfass_ctech = "гл.епел. тевм. ";	
	$this->dhfass_aposfm = "гл.апос.ж.л ";	
	$this->dhfass_aposprn = "гл.апос.ейтуп. ";	
	
	$this->dsym_title = "дектио глеягсиас жояокоцийгс амажояас сгламсгс стоивеиым - X\n";
	$this->dsym_prefix = "а/а дек.глея.жояок.амаж. X #"; 	

    $this->tax_outlaw_string = "\n----- паяамолг ейтупысг -----\n";	
	$this->payps_outlaw_string = "----- дойиластийг сгламсг -----";
	
    // set the default timezone to use. Available since PHP 5.1
    //date_default_timezone_set('Europe/Athens'); //<<set by user .....

    $this->dropbox_files = array(); //empty array per job 	
	$this->actasfiscal = false;
 }
 
 //override 
 public function execute($test=false) {
    // set the default timezone to use. Available since PHP 5.1
    //date_default_timezone_set('Europe/Athens'); //<<set by user ..... 
	
	if ($this->read_config()) {
	    //set timezone
		date_default_timezone_set($this->itaxtimezone);
		
	    $bytes = $this->receive_content($test);
	    return ($bytes);		
	}
	else {	
		//save as error txt file
		$this->jf .= '.txt';	
        $bytes = self::_write('Invalid configuration file.'); 
        return ($bytes);		
	}
 }
 
 private function read_config() {
 
	$filter = 'taxcalc';
	
	if ($this->jowner!='admin')
	  $conf_file = $this->admin_path . $filter.'-'.$this->jowner.'-conf'.'.php';
	else //admin
      $conf_file = $this->admin_path . $filter.'-conf'.'.php';	 
	  
 	
	if (is_readable($conf_file)) {
	    include($conf_file);
		
		$this->itaxserial = $itaxserial;
		$this->itaxtimezone = $itaxtimezone ? $itaxtimezone : 'Europe/Athens';
        $this->itaxsigner = $itaxsigner;	
        $this->itaxfiscal = $itaxfiscal;	
        $this->itaxcprint = $itaxcprint;		
		$this->ionomasia = $ionomasia;
		$this->icommerTitle = $icommerTitle;
		$this->iactLongDescr = $iactLongDescr;		
		$this->iafm = $iafm;
		$this->idoyDescr = $idoyDescr;
		$this->ipostalAddress = $ipostalAddress;
		$this->ipostalAddressNo = $ipostalAddressNo;
		$this->iparDescription = $iparDescription;
		$this->ipostalZipCode = $ipostalZipCode;
		$this->ifirmPhone = $ifirmPhone;
		$this->ifirmFax = $ifirmFax;
		$this->ifacActivity = $ifacActivity;
		$this->iregistDate = $iregistDate;
		$this->istopDate = $istopDate;
		$this->ideactivationFlag = $ideactivationFlag;
		$this->idoy = $idoy;
		$this->itaxuser_mail = $itaxuser_mail;
		$this->itaxactive = $itaxactive;			
		$this->itaxmode = $itaxmode;	
		$this->itax_sign_x = $itax_sign_x;
		$this->itax_sign_y = $itax_sign_y;
		$this->itax_sign_mode = $itax_sign_mode;
		$this->itax_sign_key = $itax_sign_key;
		$this->itax_sign_prefix = $itax_sign_prefix;
		$this->itax_sign_lines = $itax_sign_lines;
		$this->itaxcopies = $itaxcopies;
		$this->itaxheader = iconv("UTF-8", "ISO-8859-7", str_replace("<br>",PHP_EOL,$itaxheader));
		$this->itaxautoz = intval($itaxautoz) ? true : false;//$itaxautoz;
		$this->itaxwrap = $itaxwrap;		
		
		$this->iwalpha = $iwalpha;
		$this->iwposition = $iwposition;
        $this->iwfile = $iwfile;
        $this->iautoresize = (array) $iautoresize;
		$this->ioptimize = $ioptimize;
        $this->iftp_server = $iftp_server;
        $this->iftp_username = $iftp_username;
        $this->iftp_password = $iftp_password;
        $this->iftp_path = $iftp_path;
        $this->iftp_pathpersize = (array) $iftp_pathpersize;
        $this->idropbox = $idropbox; 
        $this->idbfolder = $idbfolder; 
		
		//when acting as fiscal machine no dropbox submition
		//if (stristr($this->jattr['job-name'],'fiscal_')) 
		if (($this->itaxfiscal) && (substr($this->jattr['job-name'],0,7)=='fiscal_')) {//serv is on
		    $this->idropbox = null; //override idropbox
			$this->actasfiscal = true;
		}	
		
		$this->taxid = $this->tax_is_active() ? $this->itaxserial : $this->test_taxid;	 
		return true;
	}

    return false;	
 }
 
 private function tax_is_enabled() {
 
	if ($this->itaxactive>0)
        return true;	
		
	return false;	
 } 
 
 private function tax_is_active() {
 
	if ($this->tax_is_enabled() && ($this->itaxmode))
        return true;	
		
	return false;	
 } 
 
 private function tax_in_test() {
 
	if ($this->tax_is_enabled() && (!$this->itaxmode))
        return true;	
		
	return false;	
 }
 
 private function post_date_time() {
 
    $date_time = date('r');//'d-m-Y H:i:s'); //rfc2822 
	$dst = date('I');
	$d = date('D d M Y');
	$t = date('H:i'); //e timezone

    
	//$greek_d = ...
	$_dst = $dst ? ' йы:' : ' вы:';
	$tt = $d . $_dst . $t;
	
	return ($tt);
 }
 
 //search backward time folder for auto z
 private function daily_date_array($flashback_days=null) {
    $flashback_days = $flashback_days ? $flashback_days : 365;//one year
 
	//create date -flashback days
	$mk_now = time();
	$i=1;
	
	for ($i=1;$i<=$flashback_days;$i++) { 
		$mk_dayback = $mk_now - ($i * 24 * 60 * 60); //one day -
		$dback[] = date('Ymd',$mk_dayback); 
	}
    
	if (!empty($dback))
       return (array) $dback;	
	   
	return false;   
 } 
 
 protected function _sendmail($from=null,$to=null,$subject=null,$body=null,$mailfile=null, $html=false) {
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
					
		$ret = mail($to,$subject,$message_utf8,$headers);
						
	    return ($ret);					
 }	 
 
 //Many mail servers don't handle utf-8 correctly as they assume iso-8859-x encodings, 
 //so you would want to convert the headers, subject and body of an email prior to sending it out.
 //If iconv() and mb_convert_encoding() are missing the following function can be used to convert UTF8 to iso-8859-7 encoding. 
 //It discards all characters that are not 2-byte greek characters or single-byte (ascii).
 function conv_utf8_iso8859_7($s) {
    $len = strlen($s);
    $out = "";
    $curr_char = "";
    for($i=0; $i < $len; $i++) {
        $curr_char .= $s[$i];
        if( ( ord($s[$i]) & (128+64) ) == 128) {
            //character end found
            if ( strlen($curr_char) == 2) {
                // 2-byte character check for it is greek one and convert
                if      (ord($curr_char[0])==205) $out .= chr( ord($curr_char[1])+16 );
                else if (ord($curr_char[0])==206) $out .= chr( ord($curr_char[1])+48 );
                else if (ord($curr_char[0])==207) $out .= chr( ord($curr_char[1])+112 );
                else ; // non greek 2-byte character, discard character
            } else ;// n-byte character, n>2, discard character
            $curr_char = "";
        } else if (ord($s[$i]) < 128) {
            // character is one byte (ascii)
            $out .= $curr_char;
            $curr_char = "";
        }
    }
    return $out;
 }

 //send documents to client
 protected function call_job_action($xpsname=null, $xpsfile=null, $text=null) {
    $ret = false;
    $action = $this->jattr['job-action'];
	if ($action) {
	    $act_p = stristr($action,':') ? explode(':',$action) : array(0=>$action);
	    
		switch ($act_p[0]) {
		  
		   case 'dropbox': //....
		   case 'mailto' : $ret = $this->_sendmail(null,$act_p[1],$xpsname,$text,null,true);
		   default       : //do nothing
		}
	}
    return ($ret); 
 } 
 
 protected function receive_content($test=false) {
								 
	$autoresize = (!empty($this->iautoresize)) ? $this->iautoresize : array('500'); 
	$filetype = $this->ifiletype ? $this->ifiletype : 'jpg';
	$compression = $this->icompression ? $this->icompression : 75;
	$wopacity = $this->iwopacity  ? $this->iwopacity  : 100;
	$walpha = $this->iwalpha ? 1 : 0;
	 
    //......check contents
	$data = @file_get_contents($this->jf);
	
    if (substr($data,0,2)=='PK') {//xps ziped file
	
		$ret = $this->create_payps_for_xps($data, 'sha1', true);
		return ($ret);
		
	}
	elseif (substr($data,0,4)=='%!PS') {//postscript	
	//if (stristr($this->jf,'.ps')) {//postscript
	
	    $ok = $this->save_printed_file($data, $this->taxid.'-test', '.ps');//save ps version
	
   	    //echo 'Convert PS2PDF';
        if ($convert = $this->ps2pdf()) {
		
		    if ($test)
		        echo 'Convert postscript to pdf';
			else 
                self::write2disk($this->handler_log, "Convert postscript to pdf.\r\n");

			//1step..convert to image...
            //...???			
            
            //$this->filename = str_replace('.ps','.pdf',$this->jattr['job-name']);				
			
            if ($bytes=self::resize($this->iaction, $autoresize, $filetype, $compression, $this->ixframe, $this->iyframe,
			                        $wopacity, $walpha, $this->iwposition, $this->iwfile, $this->ioptimize,
                                    $this->iftp_server, $this->iftp_user_name, $this->iftp_user_pass,
							        $this->iftp_path, $this->iftp_path_per_size,			
			                        $test))
	            return ($bytes);			   
        } 	
	}	
	elseif (substr($data,0,4)=='%PDF') {//pdf 
	   
   	    //echo 'Convert PDF2IMAGE';
        if ($convert = $this->pdf2image($filetype)) {
		    if ($test)
		        echo 'Convert pdf to image';
			else 
                self::write2disk($this->handler_log, "Convert pdf to image.\r\n");
				
 		    //$this->filename = str_replace('.pdf','.jpg',$this->jattr['job-name']);				

            if ($bytes=self::resize($this->iaction, $autoresize, $filetype, $compression, $this->ixframe, $this->iyframe,
			                        $wopacity, $walpha, $this->iwposition, $this->iwfile, $this->ioptimize,
                                    $this->iftp_server, $this->iftp_user_name, $this->iftp_user_pass,
							        $this->iftp_path, $this->iftp_path_per_size,			
			                        $test))
	            return ($bytes);			   
        }
	}    
 	elseif ((strstr($this->filename,'.png'))||(strstr($this->filename,'.gif'))||(strstr($this->filename,'.jpg'))) { 
	  
	    //is image...
	    if ($bytes=self::resize($this->iaction, $autoresize, $filetype, $compression, $this->ixframe, $this->iyframe,
			                    $wopacity, $walpha, $this->iwposition, $this->iwfile, $this->ioptimize,
                                $this->iftp_server, $this->iftp_user_name, $this->iftp_user_pass,
							    $this->iftp_path, $this->iftp_path_per_size,			
			                    $test))
	    return ($bytes);
	}
	else {//MUST BE TEXT....if (strstr($this->filename,'.txt')) {
	  
	    //is text...
		//$hash = sha256($data);
		//return strlen($hash);
		
		//echo 'text';
		    
		$ret = $this->create_payps_for_text($data, 'sha1', true);
		return ($ret);
	} 
	
    return false;	
 }
 

 //////////////////////////////////////////////////////////////////////////////////////xps documents 
 protected function create_payps_for_xps($data=null, $algo=null, $upper=false) {
    if (!$data) return false;
	
	if (!$this->itaxsigner) { //service disabled
        if ($bytes = self::_write($data)) //as is
            return $bytes;
        else
            return false;		
	}
	
    $algo = $algo ? $algo : 'sha1';
    $date_time = date('d-m-Y H:i:s');
    $date_dir = date('Ymd');
    $payps_path = $this->admin_path . $date_dir;
	$sid_file = $this->admin_path . $this->taxid . '-sid.txt';	
	$test_sign = null; 
	
	if ($this->stats_log) {
	    $stats = null;
	    $ftime = $this->getthemicrotime();
	}	
	
	$_sid = is_readable($sid_file) ? @file_get_contents($sid_file) : '0'; 
	
	if (!is_dir($payps_path)) {
	    @mkdir($payps_path, 0755); 
        //create dir to dropbox..
        if ($this->idropbox) 				
		    $this->save2dropbox(null,null,$date_dir);//NOT ASYNC..			
		
		if ($this->itaxautoz) {
		    //first payps of day...auto z of prev day..
		    $z_sid = intval($_sid); //before inc for today
		    $z = $this->auto_save_z($z_sid);//id computed inside prev folder
			
			if ($this->stats_log) {
			   $ttime =  $this->getthemicrotime() - $ftime;	  
			   $stats .= "Auto Z: ".$ttime. " seconds" . PHP_EOL;
			}			
		}
	}	
	
	$sid = intval($_sid)+1;	//inc global incer			
	$id = $this->_get_id($payps_path,'.txt',$this->taxid.'-dfss-');
	//echo $id.':'.$sid;
	
	if (@file_put_contents($sid_file, strval($sid), LOCK_EX)) {	
	    //echo 'sidfile created';
	    $ssid = sprintf("%08s",   $sid); // zero-padding sid
	    $nid = sprintf("%04s",   $id); // zero-padding id	
 
 	    //5.4
        $sign = //'XXX' . //$this->approval_num . //arithoms egrisis 3 chars
                //'99999999' . //$this->product_num . //arithmos paragogis 8 chars			
				$this->taxid .
			    $ssid .//global sid 0001 8 chars			
			    $nid;//day num id 0001 4 chars
				
		$signed_data = $data . $sign;//add sign at the end of xps..invalid xps file!!!	
		
		//backup xps source
		$signed_file = $payps_path . '/'.$sign.'-source.xps';	
        if (@file_put_contents($signed_file, $signed_data, LOCK_EX)) {
		    //backup of signed data
			//continue...
        }		
 
        if ($upper)
            $hash = strtoupper(hash($algo,$signed_data));
	    else
    	    $hash = hash($algo,$signed_data);
		
	    if ($hash) {
		    //echo $hash;	
			if ($this->stats_log) {
			    $ttime =  $this->getthemicrotime() - $ftime;
			    $stats .= "HASH: ".$ttime. " seconds" . PHP_EOL;
			}		  
		    //$fname = $id;//sprintf("%04s",   $this->jid);					
			//$xpsfile = $payps_path .'/'. $this->taxid.'-OUT-'.$fname.'.xps';
			$xpsname = $sign .'.xps';
			$xpsfile = $payps_path .'/'. $xpsname;
			
            $qrcname = $sign .'-qr.png';
			$qrcfile = $payps_path . '/' . $qrcname;	
            $qrcname1 = $sign .'-qrpay.png';
			$qrcfile1 = $payps_path . '/' . $qrcname1;
            //$qrcname2 = $sign .'-qrsearch.png';
			//$qrcfile2 = $payps_path . '/' . $qrcname2;			
	
			if ($this->save_dfss($hash, $id, $sid)===true) {
			    //echo 'save dfss';
			    if ($this->stats_log) {
			        $ttime =  $this->getthemicrotime() - $ftime;
			        $stats .= "SAVE DFSS FILE: ".$ttime. " seconds" . PHP_EOL;
			    }						
			    //save a file
				if ($id = $this->_save_a_file($data, $id, $sid)) {
				    //echo 'save a file';
			        if ($this->stats_log) {
			            $ttime =  $this->getthemicrotime() - $ftime;
			            $stats .= "SAVE A FILE: ".$ttime. " seconds" . PHP_EOL;
			        }							
				    //save b file
				    if ($payps = $this->_save_b_file($hash, $id, $sid)) {
					    //echo 'save b file';
			            if ($this->stats_log) {
			                $ttime =  $this->getthemicrotime() - $ftime;
			                $stats .= "SAVE B FILE: ".$ttime. " seconds" . PHP_EOL;
			            }								

						if (!$this->tax_is_active())
                            $test_sign = $this->payps_outlaw_string; 
									
                        //qr code out							
						$qrdoc = new qrfactory($payps);
						$qrdoc->QRcreate($qrcfile);
						$qrdoc1 = new qrfactory('http://taxcloud.stereobit.gr/?');//taxpay.php?sign='.$payps);
						$qrdoc1->QRcreate($qrcfile1);
						//$qrdoc2 = new qrfactory('http://taxcloud.stereobit.gr/');//taxsearch.php?sign='.$payps);
						//$qrdoc2->QRcreate($qrcfile2);						
	
	                    //xps add sign with qr code..
						$xpsdoc = new xpsfactory(true, $qrcfile);
			            $xpsdoc->xps_load($xpsfile, $this->jf);
                        if ($xpsdoc->xps_resource_sign($payps)) {
								
						    //copy data of modified xps file to job file  
                            if ($bytes = self::_write(@file_get_contents($xpsfile))) { 
									
						        //stats
							    if ($this->stats_log) {
							        $ttime =  $this->getthemicrotime() - $ftime;
							        $stats .= "START JOB: ".$ttime. " seconds" . PHP_EOL;
							    }										
									
                                //save to dropbox..
                                if ($this->idropbox) { 
			                        $this->save2dropbox_async($xpsfile, $xpsname, $date_dir);		
								    $this->sync2dropbox();
								}	

                                //commit job action...
                                $this->call_job_action($xpsname, $xpsfile);

							    //stats
							    if ($this->stats_log) {
							        $ttime =  $this->getthemicrotime() - $ftime;
							        $stats .= "END JOB: ".$ttime. " seconds" . PHP_EOL;
							        self::write2disk($this->stats_log,PHP_EOL .$stats . PHP_EOL);
							    }										
									
                                return ($bytes);
                            } 
                            else { //else rollback....
                                self::write2disk($this->handler_log, "ERROR WRITING JOB".PHP_EOL);
							    $this->rollback_file($xpsfile, '.xps');	
								$this->_rollback_b_file($id, $sid);
                                $this->_rollback_a_file($id, $sid);
                                $this->rollback_dfss($id, $sid);
								$this->rollback_file($signed_file, '.xps');
								$this->rollback_job($sid);
                            }										
                        } 
                        else { //else rollback....
						    self::write2disk($this->handler_log, "ERROR ON RESOURCE SIGN".PHP_EOL);
                            $this->rollback_file($xpsfile, '.xps');									
                            $this->_rollback_b_file($id, $sid);
                            $this->_rollback_a_file($id, $sid);
                            $this->rollback_dfss($id, $sid);
							$this->rollback_file($signed_file, '.xps');
                            $this->rollback_job($sid);									
						}	

					}
                    else {//else rollback....
                        $this->_rollback_a_file($id, $sid);
                        $this->rollback_dfss($id, $sid);
						$this->rollback_file($signed_file, '.xps');
                        $this->rollback_job($sid);								
					}	
				}
				else {//else rollback...
					$this->rollback_dfss($id, $sid);
					$this->rollback_file($signed_file, '.xps');
					$this->rollback_job($sid);
				}	
			}
		    else {//else rollback...
				self::write2disk($this->handler_log, "HASH ERROR:$hash".PHP_EOL);
			    $this->rollback_file($signed_file, '.xps');
				$this->rollback_job($sid);						
			}	
				
        }//hash
		else {
            self::write2disk($this->handler_log, "NO HASH".PHP_EOL); 
			$this->rollback_file($signed_file, '.xps');
			$this->rollback_job($sid);
		}	
	}
	else {
        self::write2disk($this->handler_log, "FILE ERROR".PHP_EOL);
		$this->rollback_job(null);
	}	
	
    return false;	 
 } 
 
 
 //////////////////////////////////////////////////////////////////////////////////////text documents
 protected function create_payps_for_text($data=null, $algo=null, $upper=false) {
    if (!$data) return false;
	
	if (!$this->itaxsigner) { //service disabled
        if ($bytes = self::_write($data)) //as is
            return $bytes;
        else
            return false;		
	}	
	
    $algo = $algo ? $algo : 'sha1';
    $date_time = date('d-m-Y H:i:s');
    $date_dir = date('Ymd');
    $payps_path = $this->admin_path . $date_dir;
	$sid_file = $this->admin_path . $this->taxid . '-sid.txt';	
	$test_sign = null;
	$log_file = $this->log_path . $this->taxid . '.log';	
	
	if ($this->stats_log) {
	    $stats = null;
	    $ftime = $this->getthemicrotime();
	}	
	
	$_sid = is_readable($sid_file) ? @file_get_contents($sid_file) : '0'; 
	
	if (!is_dir($payps_path)) {
	    @mkdir($payps_path, 0755); //create today folder
        //create dir to dropbox..
        if ($this->idropbox) 				
		    $this->save2dropbox(null,null,$date_dir);//NOT ASYNC..		
		
		if ($this->itaxautoz) {
		    //first payps of day...auto z of prev day..
		    $z_sid = intval($_sid); //before inc for today
		    $z = $this->auto_save_z($z_sid);//id computed inside prev folder
			
			if ($this->stats_log) {
			   $ttime =  $this->getthemicrotime() - $ftime;	  
			   $stats .= "Auto Z: ".$ttime. " seconds" . PHP_EOL;
			}
		}
	}	
		
	$sid = intval($_sid)+1;	//inc global incer			
	$id = $this->_get_id($payps_path,'.txt',$this->taxid.'-dfss-');
	//echo $id.':'.$sid;
	
	if (@file_put_contents($sid_file, strval($sid), LOCK_EX)) {	
	    //echo 'sidfile created';
	    $ssid = sprintf("%08s",   $sid); // zero-padding sid
	    $nid = sprintf("%04s",   $id); // zero-padding id	
 
 	    //5.4
        $sign = //'XXX' . //$this->approval_num . //arithoms egrisis 3 chars
                //'99999999' . //$this->product_num . //arithmos paragogis 8 chars			
				$this->taxid .
			    $ssid .//global sid 0001 8 chars			
			    $nid;//day num id 0001 4 chars
				
		$signed_data = $data . $sign;
		
		$signed_file = $payps_path . '/'.$sign.'-source.txt';	
        if (@file_put_contents($signed_file, $signed_data, LOCK_EX)) {
		    //backup of signed data
			//continue...
        }			
 
        if ($upper)
            $hash = strtoupper(hash($algo,$signed_data));
	    else
    	    $hash = hash($algo,$signed_data);
		
	    if ($hash) {
			//echo $hash;
			//stats
			if ($this->stats_log) {
			    $ttime =  $this->getthemicrotime() - $ftime;
			    $stats .= "HASH: ".$ttime. " seconds" . PHP_EOL;
			}
			
			$xpsname = $sign .'.xps';
			$xpsfile = $payps_path . '/' . $xpsname;	
			
            $qrcname = $sign .'-qr.png';
			$qrcfile = $payps_path . '/' . $qrcname;
            $qrcname1 = $sign .'-qrpay.png';
			$qrcfile1 = $payps_path . '/' . $qrcname1;
            //$qrcname2 = $sign .'-qrsearch.png';
			//$qrcfile2 = $payps_path . '/' . $qrcname2;				
			
			if ($this->save_dfss($hash, $id, $sid)===true) {
			    //echo 'save dfss';
			    if ($this->stats_log) {
			        $ttime =  $this->getthemicrotime() - $ftime;
			        $stats .= "SAVE DFSS: ".$ttime. " seconds" . PHP_EOL;
			    }				
			    //save a file
				if ($id = $this->_save_a_file($data, $id, $sid)) {
				    //echo 'save a file';
			        if ($this->stats_log) {
			            $ttime =  $this->getthemicrotime() - $ftime;
			            $stats .= "SAVE A FILE: ".$ttime. " seconds" . PHP_EOL;
			        }					
				    //save b file
				    if ($payps = $this->_save_b_file($hash, $id, $sid)) {
					    //echo 'save b file';
						
			            if ($this->stats_log) {
			                $ttime =  $this->getthemicrotime() - $ftime;
			                $stats .= "SAVE B FILE: ".$ttime. " seconds" . PHP_EOL;
			            }						
			            //Z 
                        //$z = $this->save_dhfass($id, $sid);//, $date_dir, true);
						if (($lines = $this->itax_sign_lines) && ($lines>1)) {
						    $wrap = intval(strlen($payps) / $lines);
						    $payps_wraped = wordwrap($payps, $wrap ,PHP_EOL , true);
						}
						else
						    $payps_wraped = $payps;//as is

						if (!$this->tax_is_active())
                            $test_sign = $this->payps_outlaw_string; 						
			
			            //echo 'save signed data';
                        if ($bytes = self::_write($data . $payps_wraped . $test_sign)) { 
						
						    //stats
							if ($this->stats_log) {
							    $ttime =  $this->getthemicrotime() - $ftime;
							    $stats .= "START JOB: ".$ttime. " seconds" . PHP_EOL;
							}						
						
                            //qr code out							
							$qrdoc = new qrfactory($payps);
							$qrdoc->QRcreate($qrcfile);
						    $qrdoc1 = new qrfactory('http://taxcloud.stereobit.gr/?');
						    $qrdoc1->QRcreate($qrcfile1);
						    //$qrdoc2 = new qrfactory('http://taxcloud.stereobit.gr/');//taxsearch.php?sign='.$payps);
						    //$qrdoc2->QRcreate($qrcfile2);	
							
                            //xps out	
				            $xpsdoc = new xpsfactory();
				            $xpsdoc->xps_load($xpsfile,$this->admin_path.'/sample.xps');
				            $xpsdoc->xps_save($this->jf);//job file just saved..

                            //save to dropbox..
                            if ($this->idropbox) {
			                    $this->save2dropbox_async($xpsfile, $xpsname, $date_dir);	
								$this->sync2dropbox();
							}	

                            //commit job action...
                            $this->call_job_action($xpsname, $xpsfile, $data . $payps_wraped . $test_sign);						
							
							//stats
							if ($this->stats_log) {
							    $ttime =  $this->getthemicrotime() - $ftime;
							    $stats .= "END JOB: ".$ttime. " seconds" . PHP_EOL;
							    self::write2disk($this->stats_log,PHP_EOL .$stats . PHP_EOL);
							}	
                            return ($bytes);			
						}
						else {//else rollback....
						    self::write2disk($this->handler_log, "ERROR WRITING JOB".PHP_EOL);
                            $this->_rollback_b_file($id, $sid);//..no need
                            $this->_rollback_a_file($id, $sid);
                            $this->rollback_dfss($id, $sid);
							$this->rollback_file($signed_file, '.txt');
                            $this->rollback_job($sid); 							
						}
					}
                    else {//else rollback....
                        $this->_rollback_a_file($id, $sid);
                        $this->rollback_dfss($id, $sid);
						$this->rollback_file($signed_file, '.txt');
                        $this->rollback_job($sid);						
					}   
				}
				else {//else rollback...
				    $this->rollback_dfss($id, $sid);
					$this->rollback_file($signed_file, '.txt');
                   	$this->rollback_job($sid);			   
				}   
			}
		    else {//else rollback...
				self::write2disk($this->handler_log, "HASH ERROR:$hash".PHP_EOL);
				$this->rollback_file($signed_file, '.txt');
				$this->rollback_job($sid);				
			}	
	    }
		else {
            self::write2disk($this->handler_log, "NO HASH".PHP_EOL);
			$this->rollback_file($signed_file, '.txt');
			$this->rollback_job($sid);
		}	
	}
    else {
        self::write2disk($this->handler_log, "FILE ERROR".PHP_EOL);
        $this->rollback_job(null);		
	}	
		
	return false;		
 }
 
 //deleter or rename job file to not reproduce open tasks
 protected function rollback_job($sid=null) {
 	$sid_file = $this->admin_path . $this->taxid . '-sid.txt';	
	
    //decrease counters...
	if (($sid) && (is_readable($sid_file))) {
	    $dec_sid = strval($sid)-1;
	    if (@file_put_contents($sid_file, $dec_sid, LOCK_EX))
            self::write2disk($this->handler_log, "SID COUNTER BACK".PHP_EOL);	
		else
            self::write2disk($this->handler_log, "FILE ERROR:SID COUNTER BACK".PHP_EOL);			
	}
 
    self::write2disk($this->handler_log, "ROLLBACK JOB".PHP_EOL);
    $ret = @rename($this->jf, str_replace('-processing', '', $this->jf).'-completed');//error!!!	
	return ($ret);
 }
 
 protected function rollback_file($file=null, $ext=null) {
	$ext = $ext ? $ext : '.txt'; 
    $ret = false;
	
	switch ($ext) {
	    case '.xps': $r_ext = '.xrback'; break;
	    case '.txt':
	    default    : $r_ext = '.rback';
	}
	
    if (($file) && (is_readable($file))) 	
	    $ret = @rename($file, str_replace($ext,$r_ext,$file));
		
	return ($ret);	
 }
 
 //3.5 dfss, deltio forlogikis shmansis stoixeiou
 protected function save_dfss($payps=null, $id=null, $sid=null) {
    if ((!$payps)||(!$id)||(!$sid)) return false;
	
    $date_time = $this->post_date_time();//date('r');//'d-m-Y H:i:s'); //rfc2822
    $date_dir = date('Ymd');
    $dfss_path = $this->admin_path . $date_dir;
		
	if (!is_dir($dfss_path)) //must be created before...
	    @mkdir($dfss_path, 0755);  
	
	$dfss_name = $this->taxid . '-dfss-'.$id.'.txt';
	$dfss_file = $dfss_path .'/'. $dfss_name;
	
	$pid = sprintf("%04s", strval($id));
    $pgap =  sprintf("% 4s", '');	
	$psid = sprintf("%08s", strval($sid));
	$dfss_data = $date_time . PHP_EOL . //date time
	             $this->hadfss_prefix . $pid . $pgap .//PHP_EOL . //id of day
		    	 $this->gadfss_prefix . $psid . PHP_EOL . //id from beginning
	             $this->payps_prefix . $payps . PHP_EOL ; //payps
			
	
    $ok = $this->save_efs($dfss_data, $dfss_file);	
			
	return $ok ? true : false;
 }
 
 protected function rollback_dfss($id=null, $sid=null) { 
    if ((!$id)||(!$sid)) return false;
	
    $date_dir = date('Ymd');
    $dfss_path = $this->admin_path . $date_dir;
	$dfss_name = $this->taxid . '-dfss-'.$id.'.txt';
	$dfss_file = $dfss_path .'/'. $dfss_name; 
	
	//$this->rollback_file($dfss_file, '.txt');
    $ok = $this->rollback_efs($dfss_file);
	
    self::write2disk($this->handler_log, "ROLLBACK DFSS:$ok".PHP_EOL);	
	
	return ($ok);
 }
 
 //3.6 dsym, deltio synopseon ypografon imeras -X-
 public function save_dsym($fid=null, $filetype=null, $date=null) {
    $fid = $fid ? $fid : 'dsym-';
	$fid_maxc = strlen($fid); 
    $date_time = $this->post_date_time();//date('d-m-Y H:i:s');
    $date_dir = $date ? $date : date('Ymd');
    $dsym_path = $this->admin_path . $date_dir;	
	$dsym_data = '';	
	$id=1;		
	$empty_x_label = null;	
	$log_file = $this->log_path . $this->taxid . '.log';	
	
	//$taxid = $this->taxid ? $this->taxid : $_GET['taxid'];
	//$xid_file = $this->admin_path . $this->taxid . '-xid.txt';
		
	//external call..read config
	if (!$this->read_config()) 
        return false;  	
		
	//check, update serial	
	$this->taxid = ($this->itaxserial==$_GET['taxid']) ? $this->itaxserial : $this->test_taxid;
	$xid_file = $this->admin_path . $this->taxid . '-xid.txt';
	
    $_xid = is_readable($xid_file) ? @file_get_contents($xid_file) : '0'; 
    $xid = $_xid + 1; 

	$dsym_name = $this->taxid . '-dsym-'.$xid.'.txt';
	$dsym_file = $dsym_path . '/' . $dsym_name;	
	//echo $dsym_file;
	
	//in case of no dfss yet.
	if (!is_dir($dsym_path))
	    @mkdir($dsym_path, 0755);  	
	
    if (is_dir($dsym_path)) {
        
	    if ($xid) {
		
	        if ($gpayps = $this->_get_z_hash($dsym_path,'_b.txt',$this->taxid, 'sha1', true)) {//use b files taxid = serial recs...
		
		    //self::write2disk($log_file,$gpayps."\r\n");
 
		    $dsym_data =  $this->dsym_title . 
			              $this->empty_line . 
			              $date_time . PHP_EOL . //date time
						  $this->dot_line .
                          $empty_x_label .						  
		                  $this->dsym_prefix . strval($xid) . PHP_EOL . //xid from beginning
						  $this->dot_line . 
		                  $this->gpayps_prefix . $gpayps . PHP_EOL . //payps
		                  $this->dhfass_prefix_i . $id . PHP_EOL . //id
		                  $this->dhfass_prefix_si . $sid . PHP_EOL . //sid						  
						  $this->dhfass_ethed. strval($aposethed) . $this->g_meter . strval($aposethed) .PHP_EOL . //..	
						  $this->dhfass_cmoserr . strval($cmos_error) . $this->g_meter . strval($cmos_error) . PHP_EOL . //..		
						  $this->dhfass_ctitle . strval($lektika_allagi) . $this->g_meter . strval($lektika_allagi) . PHP_EOL . //..	
						  $this->dhfass_ctech . strval($epemvasis_texnikou) . $this->g_meter . strval($epemvasis_texnikou) . PHP_EOL .
						  $this->dhfass_aposprn . strval($aposyndesis) . $this->g_meter . strval($aposyndesis);
			

			$ok = $this->save_efs($dsym_data, $dsym_file);			
		
		    //save x index file		
		    if (@file_put_contents($xid_file, strval($xid), LOCK_EX)) {
			
                //save log to dropbox..
                if ($this->idropbox) 					
			        $this->save2dropbox_async($log_file,$this->taxid.'.log');	
			
		        return ($ok ? true : false);
			}
		    }	
	    }	
	    return false;		 
    }
    
    return false; 	
 }
 
 //manual run ...
 public function rollback_dsym($fid=null, $filetype=null, $date=null) {
 
    self::write2disk($this->handler_log, "ROLLBACK DSYM:$ok".PHP_EOL);
 }  
 
 //3.7 dhfass, deltio hmerisias forologikis anaforas simansis stroixeion (Z)
 protected function save_dhfass($id=null, $sid=null, $date_dir=null, $zero_z=false) { 	
    $date_time = $this->post_date_time();//date('d-m-Y H:i:s');
    $date_dir = $date_dir ? $date_dir : date('Ymd'); //else today	
    $dhfass_path = $this->admin_path . $date_dir; 
 	$zid_file = $this->admin_path . $this->taxid . '-zid.txt';	
	$sid_file = $this->admin_path . $this->taxid . '-sid.txt';	
 	$empty_z_label = null;	
	$log_file = $this->log_path . $this->taxid . '.log';	
	
    /*if ((!is_readable($sid_file)) || ($zero_z===true)) { //zero_z test
	   $id = $id ? $id : 0; //out of law if has val
	   $sid = $sid ? $sid : 0; //out of law if has val
	   $empty_z_label = $this->dot_line . $this->dhfass_empty_z . $this->dot_line;	   
	}
	elseif ((!$id)||(!$sid)) 
	   return false;*/
	if ((!$id)/*||(!$sid)*/) {//only if no id zero day z
	   $id = 0;
	   $sid = $sid ? $sid : 0;
	   $empty_z_label = $this->dot_line . $this->dhfass_empty_z . $this->dot_line;		
    }	

	
	//in case of no dhfass yet...make the first dir
	if (!is_dir($dhfass_path))
	    @mkdir($dhfass_path, 0755); 		
 
	$_zid = is_readable($zid_file) ? @file_get_contents($zid_file) : '0'; 
	if ((!$id) && (!$sid)) //first zero z
	    $zid = $_zid;//'0'; //= dfass-0
	else	
	    $zid = intval($_zid)+1;	//inc global incer 	
	
	$dhfass_file = $dhfass_path . '/' . $this->taxid . '-dhfass-'.$zid.'.txt';
	$z_file = $dhfass_path . '/' . $this->taxid . '-z-'.$zid.'.txt';		
		
	
	if ($zid) {
	    //self::write2disk($log_file,$zid."\r\n");
		
	    if ($gpayps = $this->_get_z_hash($dhfass_path,'_b.txt',$this->taxid, 'sha1', true)) {//use b files taxid = serial recs...
		
		    //self::write2disk($log_file,$gpayps."\r\n");
 
		    $dhfass_data = $this->dhfass_title . 
			              $this->empty_line . 
			              $date_time . PHP_EOL . //date time
						  $this->dot_line .
                          $empty_z_label .						  
		                  $this->dhfass_prefix . strval($zid) . PHP_EOL . //zid from beginning
						  $this->dot_line . 
		                  $this->gpayps_prefix . $gpayps . PHP_EOL . //payps
		                  $this->dhfass_prefix_i . $id . PHP_EOL . //id
		                  $this->dhfass_prefix_si . $sid . PHP_EOL . //sid						  
						  $this->dhfass_ethed. strval($aposethed) . $this->g_meter . strval($aposethed) .PHP_EOL . //..	
						  $this->dhfass_cmoserr . strval($cmos_error) . $this->g_meter . strval($cmos_error) . PHP_EOL . //..		
						  $this->dhfass_ctitle . strval($lektika_allagi) . $this->g_meter . strval($lektika_allagi) . PHP_EOL . //..	
						  $this->dhfass_ctech . strval($epemvasis_texnikou) . $this->g_meter . strval($epemvasis_texnikou) . PHP_EOL .
						  $this->dhfass_aposprn . strval($aposyndesis) . $this->g_meter . strval($aposyndesis);
			

			$ok = $this->save_efs($dhfass_data, $dhfass_file);			
		
		    //save z index file		
		    if (@file_put_contents($zid_file, strval($zid), LOCK_EX)) {
			
                //save log to dropbox..
                if ($this->idropbox) 				
			        $this->save2dropbox_async($log_file,$this->taxid.'.log');								
			
		        return ($ok ? true : false);
			}
		}	
	}	
	return false;
 }
 
 //manual run....
 protected function rollback_dhfass($id=null, $sid=null, $date_dir=null, $zero_z=false) { 
 
    self::write2disk($this->handler_log, "ROLLBACK DHFASS:$ok".PHP_EOL);
 }
 
 //get last signed payps folder.. //auto save z
 protected function auto_save_z($sid=null) {
    $date_dir = date('Ymd'); //today	
	$log_file = $this->log_path . $this->taxid . '.log';	
	//self::write2disk($log_file,$sid."\r\n");
	
	if ($sid) { //has dfss

		$darray = $this->daily_date_array(90);//90 days back to search for unpublished z
		if (empty($darray)) {
		    self::write2disk($log_file,'Z:ERROR IN TIMELINE'."\r\n"); 
			return false;
		}
		
		foreach ($darray as $day=>$i) {
	
        $dfss_path = $this->admin_path . $i;	
		//self::write2disk($log_file,$dfss_path."\r\n");
		
		if (is_dir($dfss_path)) {
		   
		    //search for files...no empty signed folders to find id of dfss?
		    if ($id = $this->_get_id($dfss_path,'.txt',$this->taxid.'-dfss-', true)) {//has dfss files inside folder
			    
                //in case of first day of use ..0 z exist dhfass-0 = 0				
			    if (!$zid = $this->_get_id($dfss_path,'.txt',$this->taxid.'-dhfass-', true)) {//has NOT z file inside folder
                    //Z 
                    $z = $this->save_dhfass($id, $sid, strval($i));	//<<find folder	
	                return ($z); //true /false					
			    }
		    }
	        else {//id no exist or 0 so empty z
			    if (!$zid = $this->_get_id($payps_path,'.txt',$this->taxid.'-dhfass-', true)) {
	                //zero Z
		            //self::write2disk($log_file,'NO ID'."\r\n");
                    $z = $this->save_dhfass(0, $sid, strval($i));//, null, true);	//<<can be auto...	
	                return ($z); //true /false		
				}	
	        }			
		   
		    //return (strval($i));
        }		    	
        }//for
	}
	else {//sid no exist or 0 so empty z
	    //FIRST zero Z
		//self::write2disk($this->taxid.'.log','NO ID, NO SID'."\r\n");
        $z = $this->save_dhfass(0, 0);//, null, true);	//<<can be auto...	
	    return ($z); //true /false		
	}
	
	return false;
	
 }
 
 //external call save z of specific date
 public function save_z($date=null) {
 
	//external call..read config
	if (!$this->read_config()) 
        return false; 		
		
    $date_dir = $date ? $date : date('Ymd');
    $dfss_path = $this->admin_path . $date_dir;
    //check, update serial
	$this->taxid = ($this->itaxserial==$_GET['taxid']) ? $this->itaxserial : $this->test_taxid;	
	
	if (is_dir($dfss_path)) {
		//get sid
	    $sid_file = $this->admin_path . $this->taxid . '-sid.txt';	
	    $_sid = is_readable($sid_file) ? @file_get_contents($sid_file) : '0'; 
        $sid = intval($_sid);		
	
	    //search for files...no empty signed folders to find id of dfss?
	    if ($id = $this->_get_id($dfss_path,'.txt',$this->taxid.'-dfss-', true)) {//has dfss files inside folder
		
            $z = $this->save_dhfass($id, $sid, $date_dir);				
		}
		else {//id no exist or 0 so empty z
		
            $z = $this->save_dhfass(0, $sid, $date_dir);			
		}
		
	    return ($z); //true /false			
	}
    //else error message	
 }
  
 
 //3.8 dapfmhs, deltio anagnosis periodoy forologikis mnimis hmerision synopseon (Z) apo eos
 public function save_dapfmhs($apo=null, $eos=null) { 
    $date_time = date('d-m-Y H:i:s'); 
	$dapfmhs_file = $this->admin_path . '/' . $this->taxid .'-dapfmhs-'.date('Ydmhms').'.txt';	
	
    //if ($fp = fopen($dapfmhs_file, "w")) {
		
		//if (@file_put_contents($sid_file, strval($sid+1), LOCK_EX)) {
		
		    $dapfmhs_data = $date_time . PHP_EOL . //date time
		                  strval($zid) . PHP_EOL . //zid from beginning
		                  $this->payps_prefix . $payps . PHP_EOL . //payps
						  strval($synopsi) . PHP_EOL . //..
						  strval($aposyndesis) . PHP_EOL . //..	
						  strval($cmos_error) . PHP_EOL . //..		
						  strval($lektika_allagi) . PHP_EOL . //..	
						  strval($epemvasis_texnikou) ;
			
		    //$ok = fwrite($fp, $this->save_efs($dapfmhs_data), strlen($dapfmhs_data));
            //fclose($fp);
			$ok = $this->save_efs($dapfmhs_data, $dapfmhs_file);
		
		    return $ok ? true : false;
		//}
	//}	
	return false;
 } 
 
 //manual run...
 public function rollback_dapfmhs($apo=null, $eos=null) {

    self::write2disk($this->handler_log, "ROLLBACK DAPFMHS:$ok".PHP_EOL); 
 }
 
 //3.9 efs,eidika forologika stoixeia,(all above) headers footers
 private function save_efs($data=null, $efs_file=null) {
    if (!$efs_file) return;
    $efs_data = null;
	$log_file = $this->log_path . $this->taxid . '.log';		
    //echo $log_file;
	
    if ($data) { 
	    //echo '<br>',$data;
		if ($fp = fopen($efs_file, "w")) {
		    //echo 'z';
  		    $efs_data = $this->efs_header . PHP_EOL .
			            $this->itaxheader  . PHP_EOL . 
		                $data . PHP_EOL . 					  
						$this->efs_taxid_prefix . $this->taxid . PHP_EOL .
						$this->efs_footer . PHP_EOL; 
						
			if (!$this->tax_is_active())			
			    $efs_data .= $this->tax_outlaw_string;
			
            if ($wrap = intval($this->itaxwrap)) {//wrap text
			    //echo 'wrap';
			    $efs_data_wraped = wordwrap($efs_data, $wrap ,PHP_EOL , true);
				$ok = fwrite($fp, $efs_data_wraped, strlen($efs_data_wraped));
				self::write2disk($log_file, $efs_data_wraped);
				
				$xpsdoc = new xpsfactory();
				$xpsdoc->xps_load(str_replace('.txt','.xps',$efs_file),$this->admin_path.'/sample.xps');
				$xpsdoc->xps_save($efs_file);		
            } 			
			else {
		        $ok = fwrite($fp, $efs_data, strlen($efs_data));
				self::write2disk($log_file, $efs_data);
				
				$xpsdoc = new xpsfactory();
				$xpsdoc->xps_load(str_replace('.txt','.xps',$efs_file),$this->admin_path.'/sample.xps');
				$xpsdoc->xps_save($efs_file);				
			}	
				
            fclose($fp);
		
		    return $ok ? true : false;						
        }	
	}
    return false;	
 }
 
 private function rollback_efs($efs_file=null, $ext=null) {
    if (!$efs_file) return false;
	$ext = $ext ? $ext : '.txt';
	//text ver
	$this->rollback_file($efs_file, $ext);	
	//xps ver
	$this->rollback_file(str_replace('.txt','.xps',$efs_file), '.xps');
		
    self::write2disk($this->handler_log, "ROLLBACK EFS:$ret".PHP_EOL);
    return ($ret);	
 } 
 
 //return max id
 private function _get_id($dir_name=null, $filetype=null, $fid=null, $not_increment=false) {
    $fid = $fid ? $fid : 'j-';
	$fid_maxc = strlen($fid);
	$ext = $filetype ? $filetype : '.txt';
	$ext_maxc = (strlen($ext))*-1;
 
    if (is_dir($dir_name)) {
        $mydir = dir($dir_name);
		
		$i=0;		
        while ($fileread = $mydir->read ()) { 
		
		    /*if ($filetype)
		        $fr = str_replace($filetype,'',$fileread);
		   	else
               	$fr = $fileread;		
	         
            if (substr($fr,0,$fid_maxc)==$fid) {*/
			if ((substr($fileread,0,$fid_maxc)==$fid) && (substr($fileread,$ext_maxc)==$ext)) {
               $i+=1;
			  
			   //$pf = explode('-',$fr);
			   $pf = explode('-',str_replace($filetype,'',$fileread));
			   $jid[] = intval(array_pop($pf)) ;//intval($pf[1]);
            }
        }
        $mydir->close ();
		

		if ((empty($jid)) || ($jid[0]==0)) //-0 first z meter
		    $ret = $not_increment ? 0 : 1;	 
		else 
		    $ret = $not_increment ? max($jid) : max($jid)+1;
			
		return ($ret);	
    }
    
    return false; 	
 }
 
 //return z payps
 private function _get_z_hash($dir_name=null, $ftr=null, $fid=null, $algo=null, $upper=false) {
    if (!$dir_name) return false;
    $fid = $fid ? $fid : $this->taxid;
    $algo = $algo ? $algo : 'sha1';	
	$fid_maxc = strlen($fid);	
    $tid = $ftr ? $ftr : null;
	$tid_maxc = strlen($tid);	
    $gp_data = null;	
	
	//self::write2disk($this->taxid.'.log',$dir_name."\r\n");
		
    if (is_dir($dir_name)) {

        $mydir = dir($dir_name);	
		
		$i=0;		
        while ($fileread = $mydir->read ()) { 	
	
            if ((substr($fileread,0,$fid_maxc)==$fid) &&
			    (substr($fileread,-($tid_maxc))==$tid)) {
               $i+=1;
			  
			   $bfile = $dir_name . '/' . $fileread;
			   $gp_data .= @file_get_contents($bfile);
			   
			   //self::write2disk($this->taxid.'.log',$bfile."\r\n");
            }
        }
        $mydir->close ();
		
		if ($gp_data) {
            //self::write2disk($this->taxid.'.log',$gp_data);	

            $hash = ($upper) ? strtoupper(hash($algo,$gp_data)) : hash($algo,$gp_data);
            return ($hash);	
        }	
        else { //0 z
		    $hash = ($upper) ? strtoupper(hash($algo,'0')) : hash($algo,$gp_data);
			return ($hash);
        }		
    }
    
    return false; 		
 }
 
 //counting z's!!!!!!!!!!!!!!!!!!NOT USED
 private function _get_zs($dir_name=null, $synopsis=false, $today_folder=false, $apo=false, $eos=false) {
    $zcount = 0; 
	$date_apo = $apo ? $apo : '20000101';//min
	$date_eos = $eos ? $eos : '21110101';//max	
 
    if (is_dir($dir_name)) {
        $mydir = dir($dir_name);
		
		$i=0;		
        while ($fileread = $mydir->read ()) { 
		    
			$current_folder = $dir_name . '/' . $filetype;
			
		    if (is_dir($current_folder)) {
			
			    if (($date_apo) && ($date_eos)) {
				
				    $cdate = intval($filetype);
					
					//if is out of range
				    if (($cdate<$date_apo) || ($cdate>$date_eos)) 
				        continue; //next loop..   
			    }
				
			    //..and has z index file inside
			    if ($zid = $this->_get_file($current_folder, '.txt', 'z-')) { 
					
				    if ($synopsis) {
					    if ($today_folder)//all counters has todays dsym
						    $sid = $this->_get_file($today_folder, '.txt', $this->taxid .'-dsym-');
						else //every array element has its dsym counters
				            $sid = $this->_get_file($current_folder, '.txt', $this->taxid .'-dsym-');
							
						$zcount[$i] = $zid.';'.$sid; //return array ..zid is the last z?
			        } 					
					else
					    $zcount+=1;
				}	
			}   

        }
        $mydir->close ();


		return ($zcount);
    }
    //else is 0 and must create foldr and save inside
    return false; 	
 } 
 
 //search z,.. files
 private function _get_file($dir_name=null, $filetype=null, $fid=null) {
    $fid = $fid ? $fid : 'j-';
	$fid_maxc = strlen($fid);
 
    if (is_dir($dir_name)) {
        $mydir = dir($dir_name);
		
		$i=0;		
        while ($fileread = $mydir->read ()) { 
		
		    if ($filetype)
		        $fr = str_replace($filetype,'',$fileread);
		   	else
               	$fr = $fileread;		
	
            if (substr($fr,0,$fid_maxc)==$fid) {
               $i+=1;
			  
			   $pf = explode('-',$fr);
			   $jid[] = intval($pf[1]);
            }
        }
        $mydir->close ();

		if (!empty($jid))
		  return (max($jid));	
		else
          return 0; 
    } 
 }	
 
 //4.3, save a files
 private function _save_a_file($data=null, $id=null, $sid=null, $headers=null) {
    if ((!$sid)||(!$id)) return false; 
 
    $date_time = date('d-m-Y H:i:s');
    $date_dir = date('Ymd');
	
    $a_path = $this->admin_path . $date_dir; 
 
	//in case of no date folder...
	if (!is_dir($a_path))
	    return false; //must be created	
	
    //get saved dfss id	...ERROR TO PLAY FOR ROLLBACK-----V
	$nid = $id;//$this->_get_id($a_path,'.txt', $this->taxid . '-dfss-', true); 
	//$nid = intval($id)-1; //id is the next aa
	$a_name = $this->_buildname('a', $nid, $sid,'.txt');
    $a_file = $a_path . '/' . $a_name;	
		
    if ($data) { 
	    
		if ($fp = fopen($a_file, "w")) {
		    
  		    $a_data = $headers ?
			          $this->itaxheader  . PHP_EOL . 
		              $data . PHP_EOL . 					  
					  $this->taxid . PHP_EOL
                      :
                      $data; 					  
						
		    $ok = fwrite($fp, $a_data, strlen($a_data));
            fclose($fp);
			
            //save to dropbox..
            if ($this->idropbox) 
			    $this->save2dropbox_async($a_file,$a_name,$date_dir);//,$debug_mode);			
		
		    return $ok ? $nid : false;						
        }	
	}
    return false;	
 }
 
 private function _rollback_a_file($id=null, $sid=null) { 
    if ((!$sid)||(!$id)) return false; 
    $date_dir = date('Ymd');
    $a_path = $this->admin_path . $date_dir; 
	$ext = $ext ? $ext : '.txt';	 
	
	$a_name = $this->_buildname('a', $id, $sid,'.txt');
    $a_file = $a_path . '/' . $a_name;	
	
    if (is_readable($a_file)) {	
	    $ret = @rename($a_file, str_replace($ext,'.rback',$a_file));
		
        //rename to dropbox..
        if ($this->idropbox) 
	        $this->rename2dropbox_async($a_name,$date_dir);		
	}	
		
    self::write2disk($this->handler_log, "ROLLBACK A FILE:$ret".PHP_EOL);
    return ($ret);		
 }
 
 //4.3, save b files
 private function _save_b_file($data=null, $id =null, $sid=null, $headers=null) {
    if ((!$sid)||(!$id)) return false; 
 
    $date_time = date('d-m-Y H:i:s');
    $date_dir = date('Ymd');
		
    $b_path = $this->admin_path . $date_dir; 
 
	//in case of no date folder...must be created ...
	if (!is_dir($b_path))
	    return false; //must be created	
			
    $b_name = $this->_buildname('b', $id, $sid,'.txt');			
    $b_file = $b_path . '/' . $b_name;	
		
    if ($data) { 
	    
		if ($fp = fopen($b_file, "w")) {
		
		    $genikos_aa_simansis = date('ymdHi'); //...
		    
  		    $b_data = $headers . //not applicable
			          $data  . ' ' . 
		              sprintf("%04s",   $id) . ' ' . 					  
					  sprintf("%08s",   $sid) . ' ' .
					  $genikos_aa_simansis . ' ' . //'99999999' . ' ' . //??? genikos aa simansis  8 chars
					  $this->taxid;
					  //'XXX' . //$this->approval_num .  //arithoms egrisis 3 chars
					  //'99999999'; //$this->product_num;//arithmos paragogis 8 chars; 					  
						
		    $ok = fwrite($fp, $b_data, strlen($b_data));
            fclose($fp);
			
            //save to dropbox..
            if ($this->idropbox) 
			    $this->save2dropbox_async($b_file,$b_name,$date_dir);//,$debug_mode);				
		
		    return $ok ? $b_data : false;						
        }	
	}
    return false;	
 } 
 
 private function _rollback_b_file($id=null, $sid=null) { 
    if ((!$sid)||(!$id)) return false; 
    $date_dir = date('Ymd');
    $b_path = $this->admin_path . $date_dir; 
	$ext = $ext ? $ext : '.txt';
	
	$b_name = $this->_buildname('b', $id, $sid,'.txt');
    $b_file = $b_path . '/' . $b_name;
	
    if (is_readable($b_file)) {	
	    $ret = @rename($b_file, str_replace($ext,'.rback',$b_file));
	
        //rename to dropbox..
        if ($this->idropbox) 
	        $this->rename2dropbox_async($b_name,$date_dir);		
	}	
		
    self::write2disk($this->handler_log, "ROLLBACK B FILE:$ret".PHP_EOL);
    return ($ret);		
 }
 
 //build 11chars name
 private function _buildname($type=null, $id=null, $sid=null, $extension=null) {
    if ((!$id)||(!$sid)) return false;
 
    $create_date = date('ymd'); 
	$nid = sprintf("%04s",   $id); // zero-padding id
	$ssid = sprintf("%04s",   $sid); // zero-padding sid	
	$sign_type = $type ? '_'.$type : null;
	$extension = $extension ? $extension : null;
 
    $name = //'XXX' . //$this->approval_num . //arithoms egrisis 3 chars
            //'99999999' . //$this->product_num . //arithmos paragogis 8 chars
			$this->taxid .
            $create_date . //date YYMMDD 6 chars			
			$ssid .//global sid 0001 4 chars			
			$nid .//day num id 0001 4 chars
			$sign_type . //a,b...
			$extension; //.txt
			
	return ($name);		
 }

 
 
 protected function resize($action=null, $autoresize=null, $filetype=null, $compression=null, $xframe=null, $yframe=null,
                           $wopacity=null, $walpha=null, $wposition=null, $wfile=null, $optimize=null,
                           $ftp_server=null, $ftp_user_name=null, $ftp_user_pass=null,
						   $ftp_path=null, $ftp_path_per_size=null,			
                           $debug_mode=false, $sourcefile=null) {
    //if ($debug_mode)
	  // print_r($this->jattr);  
	
	$source = $sourcefile ? $sourcefile : $this->jf;  
	$id   = $this->jattr['job-id'];
	$f = explode('.',$this->jattr['job-name']);   
	$name = $f[0] . '.' . $filetype;
	$source_filetype = $f[1];
	
    $image = new SimpleImage();	
    $image->load($source);
	
	switch ($filetype) {
	    case 'png': $target_filetype = IMAGETYPE_PNG; break;
	    case 'gif': $target_filetype = IMAGETYPE_GIF; break;
	    case 'jpg': $target_filetype = IMAGETYPE_JPEG; break;
	    default   : $target_filetype = $image->image_type;
	}
	//echo '>'.$filetype.'>'.$target_filetype;
	if ($action)
	  $image->do_action($action);
	
	if (($xframe) || ($yframe)) 
	    $image->place_in_frame($xframe, $yframe);
	
    if ($wposition) {//$wfile may exists //add watermark

	    $watermark_filename = $this->admin_path . $wfile;
	    $image->add_watermark($watermark_filename, $wopacity, $wposition, $walpha);
	    //echo '>'.$watermark_filename;
    }
	
	//save the image ..type convertion.. 
	$savefile = $source;//$this->jobs_path .'/job'. $id .'_'.$name;
	//change filetype..NO
	//if (($source_filetype) && ($source_filetype!=$target_filetype))
	  //$savefile = str_replace($source_filetype, $target_filetype, $source);
	//save  
	$image->save($savefile,$target_filetype,$compression);	
	
    if ($ftp_server) {
	    //connect 
        $conn_id = ftp_connect($ftp_server);	
        // login with username and password
        $login = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
	}	
	
    //resize large, medium and small and save at once	
    if (!empty($autoresize)) {	
						
		if ($image->image_type) { //is image
		
            //as in source			
            //$my_filetype = $image->image_type;

			//for any size in array
			foreach ($autoresize as $i=>$size) {
			
			    if ($size) {
				   				
				    if ($optimize)//is height
					   $image->resizeToHeight($size);
					else //is width  
                       $image->resizeToWidth($size);
					
			  	    $file = $this->jobs_path .'/job'. $id .'_'.$size .'_'.$name;
                    $image->save($file,$target_filetype,$compression);
					
					//if ($debug_mode)
					//	echo $source_filetype.' Image:'.$i.'='.$size.",$compression<br>file:".$file.'<br>';
						
					if ($login) {//move to ftp
					    $remote_dir = isset($ftp_path_per_size[$i]) ? $ftp_path_per_size[$i] : null;
						$remote_file = $ftp_path . $remote_dir . $name;
						
						//if ($debug_mode)
						//    echo "remotefile:".$remote_file.'<br>';
						
						if (ftp_put($conn_id, $remote_file, $file, FTP_BINARY)) {
                            @unlink($file);							
                            self::write2disk($this->handler_log, date(DATE_RFC822)."$file transfered\r\n");									
						}
                        else {	    
						    if ($debug_mode)
                               echo "FTP:File not found:".$file.'<br>';						
                            self::write2disk($this->handler_log, date(DATE_RFC822)."$file NOT transfered\r\n");																
						}	
					}

					//save to dropbox..in ftp folder... 
                    if ($this->idropbox) 
					    $this->save2dropbox($name,'image.img',$ftp_path_per_size[$i] ,$debug_mode);
						
			    }	   
			}//foreach
			
            // close the ftp connection		
		    if ($conn_id) 
                ftp_close($conn_id);
						
			return true;			
 		}	
	}
	else {//saved.. just put a copy on ftp..if
	
        if ($image->image_type) { //is image
			

			if ($login) {//copy to ftp
			    $remote_dir = null;
				$remote_file = $ftp_path . $remote_dir . $name;
				
				if ($debug_mode)
				    echo "remotefile:".$remote_file.'<br>';			
				
				if (ftp_put($conn_id, $remote_file, $file, FTP_BINARY)) {
                    @unlink($file);							
                    self::write2disk($this->handler_log, date(DATE_RFC822)."$file transfered\r\n");									
			    }	
			}				
		
            // close the ftp connection		
		    if ($conn_id) 
                ftp_close($conn_id);
			
            //save to dropbox..in dbfolder...			
            if ($this->idropbox) 					
			    $this->save2dropbox($name, 'image.img', $this->idbfolder, $debug_mode);
				
		    return true;
        }		
    }
	
    // close the ftp connection		
	if ($conn_id) 
        ftp_close($conn_id);	
	
	unset($image);
    return false;//not an image	 	
 }

 
 public function ps2pdf($sourcefile=null) {
 
	  $source = $sourcefile ? $sourcefile : $this->jf;   
	  //echo 'ps2pdf:'.$source; 
	
	  $ch = curl_init();
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
      curl_setopt($ch, CURLOPT_URL, "http://www.stuffedcow.net/images/ps2pdf/convert.php");
      curl_setopt($ch, CURLOPT_POST, true);
      // same as <input type="file" name="file_box">
      $post = array(
        "source"=>'@'.$source
		//,"name"=>'source'
      );
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
      $response = curl_exec($ch);
	  
	  
	  $ok = $this->save_printed_file($response, $this->taxid.'-test', '.pdf');//save pdf version
	  
	  
	  //echo 'zzzz';
	  //if (substr($response,0,4)=='%PDF') {//if response is pdf
	    $bytes = self::_write($response);
	    return ($bytes);
	  //}
	  
	  //return (false);
 }  
 
 public function pdf2image($type=null, $sourcefile=null) {
 
      $type = $type ? $type : 'jpg';
	  $source = $sourcefile ? $sourcefile : $this->jf;   
	  //echo 'ps2pdf:'.$source; 
	
	  $ch = curl_init();
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
      curl_setopt($ch, CURLOPT_URL, "http://service.coolutils.com/PDF-Converter.php");
      curl_setopt($ch, CURLOPT_POST, true);
      // same as <input type="file" name="file_box">
      $post = array(
        "filename"=>'@'.$source
		//,"name"=>'source'
      );
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
      $response = curl_exec($ch);
	  
	  //echo 'zzzz';
	  $ok = $this->save_printed_file($response, $this->taxid.'-test', '.jpg');//save pdf version	  
	  
	  //if (substr($response,0,4)=='%xxxPDF') {//if response is jpg
	    $bytes = self::_write($response);
	    return ($bytes);
	  //}
	  
	  //return (false);
 } 

 protected function save2dropbox($sourcefile=null, $targetfile=null, $infolder=null, $debug_mode=false) {
	$tfile = $targetfile ? $targetfile : $this->jattr['job-name'];	
	$myfolder = $infolder ? $infolder : null;
    $app_key = "okjt78blighs0d6";
	$app_secret = "91b2uvhkmbuqj4k";

    $ret = true;	
 
    try {	
		
            // Check whether to use HTTPS and set the callback URL
            $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
            $callback = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];	
	
	        // Instantiate the Encrypter and storage objects
            // $key is a 32-byte encryption key (secret)
            $key = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
            $encrypter = new \Dropbox\OAuth\Storage\Encrypter($key);

            // User ID assigned by your auth system (used by persistent storage handlers)
		    if ($u = $_GET['userid']) //only in test manual mode
                $userID = $u;		
		    else 
                $userID = $this->jowner;
		  
            // Create the storage object, passing it the Encrypter object
		    $storage = new \Dropbox\OAuth\Storage\Filesystem($encrypter, $userID);
	        $storage->setDirectory($this->admin_path);
 
            $OAuth = new \Dropbox\OAuth\Consumer\Curl($app_key, $app_secret, $storage, $callback);
            $this->dropbox = new \Dropbox\API($OAuth);		
			
            //always try to create directory ....
			if ($debug_mode) 
		        echo "<br>CREATE DIR ($userID):". $myfolder;
			else	
                self::write2disk($this->handler_log, "CREATE DIR:".$myfolder . PHP_EOL); 		
				
            if ($myfolder) 
		        $this->create_dropbox_directory($myfolder);  	
	}	
	catch(\Dropbox\Exception $e) {	
	        if ($debug_mode)
		        echo $e->getMessage();
		    else
	            self::write2disk($this->handler_log, "ERROR:".$e->getMessage() . PHP_EOL);
			//$ret = false;	..always true if error exist
    }		  
	
    if (!$sourcefile) return ($ret); //true.. if folder exist
	
	//else continue file put
    try {
 
        if ($debug_mode) {
		  echo "<br>DBOXSAVE FILENAME ($userID):". $sourcefile .':'. $targetfile ;
		  echo "<br>DBOXSAVE FOLDER ($userID):". $myfolder ."<br>";
		}  
		else {
          self::write2disk($this->handler_log, "FILE ($userID):".$sourcefile .':'. $targetfile . PHP_EOL);
		  self::write2disk($this->handler_log, "FOLDER ($userID):".$myfolder. PHP_EOL);
		}  	 
		 
        // Upload the file with an alternative filename
        $put = $this->dropbox->putFile($sourcefile,$tfile,$myfolder,false); //alt name,path,override
    } 
	catch(\Dropbox\Exception $e) {
	    //echo $e->getMessage() . PHP_EOL;
	    //exit('Setup failed! Please try running setup again.');
	    if ($debug_mode)
		  echo $e->getMessage();
		else
	      self::write2disk($this->handler_log, "ERROR:".$e->getMessage() . PHP_EOL);

        $ret = false;		  
    }	
	
    self::write2disk($this->handler_log, "PUT:$put". PHP_EOL);
	//if ($put) return true; 	
	 
    //return false;	
	return ($ret);
 }

 function create_dropbox_directory($path=null) {
    if (!$path) 
	  return false;
 
    $metaData = $this->dropbox->create($path); 
	return ($metaData);
 }

 protected function rename2dropbox($targetfile=null, $infolder=null, $debug_mode=false) {
    $app_key = "okjt78blighs0d6";
	$app_secret = "91b2uvhkmbuqj4k";
    $ret = true;
    try {	
		
        // Check whether to use HTTPS and set the callback URL
        $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
        $callback = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];	
	
	    // Instantiate the Encrypter and storage objects
        // $key is a 32-byte encryption key (secret)
        $key = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
        $encrypter = new \Dropbox\OAuth\Storage\Encrypter($key);

        // User ID assigned by your auth system (used by persistent storage handlers)
		if ($u = $_GET['userid']) //only in test manual mode
            $userID = $u;		
		else 
            $userID = $this->jowner;
		  
        // Create the storage object, passing it the Encrypter object
		$storage = new \Dropbox\OAuth\Storage\Filesystem($encrypter, $userID);
	    $storage->setDirectory($this->admin_path);
 
        $OAuth = new \Dropbox\OAuth\Consumer\Curl($app_key, $app_secret, $storage, $callback);
        $this->dropbox = new \Dropbox\API($OAuth);		
			
        //delete a file
		$myfile = $infolder ? $infolder.'/'.$targetfile : $targetfile;
        $del = $this->dropbox->delete($myfile); 
	}	
	catch(\Dropbox\Exception $e) {

	    if ($debug_mode)
		  echo $e->getMessage();
		else
	      self::write2disk($this->handler_log, "ERROR:".$e->getMessage() . PHP_EOL);

        $ret = false;		  
    }
	self::write2disk($this->handler_log, "DELETE DROPBOX FILE:$del". PHP_EOL);
    return $ret;	
 }
 
 //async save dropbox
 protected function save2dropbox_async($sourcefile=null, $targetfile=null, $infolder=null) { 
 
    $this->dropbox_files[$targetfile] = $sourcefile . ':' . $infolder; 
	return true;
 }
 //async rename dropbox
 protected function rename2dropbox_async($targetfile=null, $infolder=null) {
 
    $this->dropbox_files[$targetfile] = null;
	return true;
 } 
 //commit async files to dropbox
 protected function sync2dropbox($debug_mode=false) {
    $app_key = "okjt78blighs0d6";
	$app_secret = "91b2uvhkmbuqj4k"; 
	
	if (empty($this->dropbox_files)) 
	    return true; //always true
		
    //print_r($this->dropbox_files);
	
    try {	
		
            // Check whether to use HTTPS and set the callback URL
            $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
            $callback = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];	
	
	        // Instantiate the Encrypter and storage objects
            // $key is a 32-byte encryption key (secret)
            $key = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
            $encrypter = new \Dropbox\OAuth\Storage\Encrypter($key);

            // User ID assigned by your auth system (used by persistent storage handlers)
		    if ($u = $_GET['userid']) //only in test manual mode
                $userID = $u;		
		    else 
                $userID = $this->jowner;
		  
            // Create the storage object, passing it the Encrypter object
		    $storage = new \Dropbox\OAuth\Storage\Filesystem($encrypter, $userID);
	        $storage->setDirectory($this->admin_path);
 
            $OAuth = new \Dropbox\OAuth\Consumer\Curl($app_key, $app_secret, $storage, $callback);
            $this->dropbox = new \Dropbox\API($OAuth);		
			
            foreach ($this->dropbox_files as $targetfile=>$dattr) {
		      if (isset($dattr)) {  
			  
			    //echo $tfile.'=>'.$dattr;
				
				if (stristr($dattr ,':')) {
				    $ddata = explode(':',$dattr);
					$sourcefile = $ddata[0];
					$myfolder = $ddata[1];
				}
				else {
				    $sourcefile = $dattr;
					$myfolder = null;
				}	
			    //echo '<br>',$targetfile,'->',$sourcefile,'->',$myfolder;
				
                if ($debug_mode) {
		            echo "<br>DBOXSAVE FILENAME ($userID):". $sourcefile .':'. $targetfile ;
		            echo "<br>DBOXSAVE FOLDER ($userID):". $myfolder ."<br>";
		        }  
		        else {
                    self::write2disk($this->handler_log, "FILE ($userID):".$sourcefile .':'. $targetfile . PHP_EOL);
		            self::write2disk($this->handler_log, "FOLDER ($userID):".$myfolder. PHP_EOL);
		        }  	 				
                $put = $this->dropbox->putFile($sourcefile,$targetfile,$myfolder,false); //alt name,path,override
				//echo $put,'>';
              }//if				
			}		
	}	
	catch(\Dropbox\Exception $e) {	

        if ($debug_mode)
		  echo $e->getMessage();
		else
          self::write2disk($this->handler_log, "ERROR:".$e->getMessage() . PHP_EOL);
    }	

    return true;	
 }  
 
 protected function getthemicrotime() {
   
     list($usec,$sec) = explode(" ",microtime());
     return ((float)$usec + (float)$sec);
 }  
 
}

/********************************************************************************/
//test
if ($test = $_GET['test']) {
$test_file = getcwd() . '/test.txt';
//echo 'test_file:', $test_file,'<br>';
if ($fp = fopen($test_file, "r+b")) {  

  $user = 'test';
  $test_tax = new taxcalc($user,$fp,1,$test_file,null,'taxcloud.printer');  

  $ret = $test_tax->execute($test);
  
  //echo 'result:',$ret,'<br>';
  
  fclose($fp);
}
else
  echo 'file is not readable<br>';
}  
/****************************************************************************/
?>
<?php

//require_once('skeleton.php');
//require_once('ImageResize/SimpleImage.php');
//require_once('lib/sha256.php'); //use hash instead

require_once('xpsfactory.php');

class taxcalc extends skeleton {
 
 var $filename;
 var $admin_path;
 var $dropbox;
 var $taxid, $test_taxid;
 var $efs_header, $efs_footer, $efs_taxid_prefix, $payps_prefix, $hadfss_prefix, $gadfss_prefix,
     $dhfass_title, $dhfass_prefix, $gpayps_prefix, $dot_line, $empty_line, $dhfass_prefix_i, $dhfass_prefix_si,
	 $dhfass_ethed, $dhfass_cmoserr, $dhfass_ctitle, $dhfass_ctech, $dhfass_aposfm, $dhfass_aposprn, $dhfass_empty_z, 
	 $g_meter, $payps_outlaw_string, $tax_outlaw_string;
 
 protected  $itaxserial, $ionomasia, $icommerTitle, $iactLongDescr, $iafm,
		    $idoyDescr, $ipostalAddress, $ipostalAddressNo, $iparDescription,
		    $ipostalZipCode, $ifirmPhone, $ifirmFax, $ifacActivity, $iregistDate,
		    $istopDate, $ideactivationFlag, $idoy, $itaxuser_mail, $itaxactive,			
		    $itaxmode, $itax_sign_x, $itax_sign_y, $itax_sign_mode, $itax_sign_key,
		    $itax_sign_prefix, $itax_sign_lines, $itaxcopies, $itaxheader, $itaxautoz, $itaxwrap, 
			$iwalpha,$iwposition, $iwfile, $iautoresize, $ioptimize, $iftp_server, $iftp_username,
            $iftp_password, $iftp_path, $iftp_pathpersize, $idropbox, $idbfolder;
	
 public function __construct($user,$data=null, $job_id=null, $job_file=null, $job_attr=null, $printer_name=null) {
 
    parent::__construct($user,$data,$job_id,$job_file,$job_attr,$printer_name);
	
	$this->jid = $job_id;
	$this->jf = $job_file;
	$this->jattr = (array) $job_attr;
	$this->printer_name = $printer_name;
	$this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/'.$this->printer_name;							
	
    self::write2disk('taxcalc.log',date(DATE_RFC822)."INIT\r\n");	
	
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

    $this->tax_outlaw_string = "\n----- паяамолг ейтупысг -----\n";	
	$this->payps_outlaw_string = "----- дойиластийг сгламсг -----";
 }
 
 //override 
 public function execute($test=false) {
    // set the default timezone to use. Available since PHP 5.1
    date_default_timezone_set('Europe/Athens'); //<<set by user ..... 
	
	$filter = 'taxcalc';
	
	if ($this->jowner!='admin')
	  $conf_file = $this->admin_path . $filter.'-'.$this->jowner.'-conf'.'.php';
	else //admin
      $conf_file = $this->admin_path . $filter.'-conf'.'.php';	
	
	if (is_readable($conf_file)) {
	    //if ($test)
		  //  echo 'Conf:'.$conf_file.'<br>';
			
	    include($conf_file);
		
		$this->itaxserial = $itaxserial;				
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
		
		$this->taxid = $this->tax_is_active() ? $this->itaxserial : $this->test_taxid;		

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
                self::write2disk('taxcalc.log',"Convert postscript to pdf.\r\n");

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
                self::write2disk('taxcalc.log',"Convert pdf to image.\r\n");
				
 		    //$this->filename = str_replace('.pdf','.jpg',$this->jattr['job-name']);				

            if ($bytes=self::resize($this->iaction, $autoresize, $filetype, $compression, $this->ixframe, $this->iyframe,
			                        $wopacity, $walpha, $this->iwposition, $this->iwfile, $this->ioptimize,
                                    $this->iftp_server, $this->iftp_user_name, $this->iftp_user_pass,
							        $this->iftp_path, $this->iftp_path_per_size,			
			                        $test))
	            return ($bytes);			   
        }
	}    
 	else { //must be jpg image..resize
	
        if ((strstr($this->filename,'.png'))||(strstr($this->filename,'.gif'))||(strstr($this->filename,'.jpg'))) { 
	  
	        //is image...
	        if ($bytes=self::resize($this->iaction, $autoresize, $filetype, $compression, $this->ixframe, $this->iyframe,
			                    $wopacity, $walpha, $this->iwposition, $this->iwfile, $this->ioptimize,
                                $this->iftp_server, $this->iftp_user_name, $this->iftp_user_pass,
							    $this->iftp_path, $this->iftp_path_per_size,			
			                    $test))
	            return ($bytes);
	    }
	    else {//if (strstr($this->filename,'.txt')) {
	  
	        //is text...
		    //$hash = sha256($data);
		    //return strlen($hash);
		
		    //echo 'text';
		    
			$ret = $this->create_payps_for_text($data, 'sha1', true);
		    return ($ret);
	    }
	} 
	
    return false;	
 }

 //////////////////////////////////////////////////////////////////////////////////////xps documents 
 protected function create_payps_for_xps($data=null, $algo=null, $upper=false) {
    if (!$data) return false;
    $algo = $algo ? $algo : 'sha1';
    $date_time = date('d-m-Y H:i:s');
    $date_dir = date('Ymd');
    $payps_path = $this->admin_path . $date_dir;
	$sid_file = $this->admin_path . $this->taxid . '-sid.txt';	
	$test_sign = null; 
	
	$_sid = is_readable($sid_file) ? @file_get_contents($sid_file) : '0'; 
	
	if (!is_dir($payps_path)) {
	    @mkdir($payps_path, 0755);  	
		
		if ($this->itaxautoz) {
		    //first payps of day...auto z of prev day..
		    $z_sid = intval($_sid); //before inc for today
		    $z = $this->auto_save_z($z_sid);//id computed inside prev folder
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
		
		$signed_file = $payps_path . '/'.$sign.'.xps';	
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
		  
		    $key = iconv("ISO-8859-1", "UTF-8", $this->itax_sign_key);//"STEREOOFFICE"); //$this->itax_sign_key; 
		    $ss = iconv("ISO-8859-1", "UTF-8", $this->itax_sign_key . $payps . $test_sign); //add key as prefix..??? 	
		    $fname = sprintf("%04s",   $this->jid);		  
		  
	        if ($this->save_printed_file($data, $this->taxid.'-'.$fname, '.xps')) {//save xps version	
			  if ($zfiles = $this->unzip_xps_file($this->taxid.'-'.$fname.'.xps')) {		
		        if ($ok = $this->modify_xps_file($this->taxid.'-'.$fname.'.xps', $zfiles, null, $key, $ss)) {			
			
			        if ($this->save_dfss($hash, $id, $sid)===true) {
			            //echo 'save dfss';
			            //save a file
				        if ($id = $this->_save_a_file($data, $id, $sid)) {
				            //echo 'save a file';
				            //save b file
				            if ($payps = $this->_save_b_file($hash, $id, $sid)) {
					            //echo 'save b file';

						        if (!$this->tax_is_active())
                                    $test_sign = $this->payps_outlaw_string; 
	
                                
						        //copy data of modified xps file to job file  
                                $bytes = self::_write(@file_get_contents($payps_path .'/'.$this->taxid.'-'.$fname.'.xps')); 
                                return ($bytes);							   

					        }//else rollback....
                            else
                                self::write2disk('taxcalc.log',"H\r\n");					
				        }//else rollback...
				        else
                            self::write2disk('taxcalc.log',"G\r\n");
			        }//else rollback...
                    else
                        self::write2disk('taxcalc.log',"F\r\n");	
			    }
			    else
                    self::write2disk('taxcalc.log',"E\r\n"); 				
		      }
		      else
                self::write2disk('taxcalc.log',"D\r\n"); 
		    }		
		    else
              self::write2disk('taxcalc.log',"C\r\n");
				
        }//hash
		else
            self::write2disk('taxcalc.log',"B\r\n"); 
	}
	else
        self::write2disk('taxcalc.log',"A\r\n");
	
    return false;	 
 } 
 
 private function save_printed_file($data=null, $print_name=null, $extension=null) {
    $print_name = $print_name ? $print_name : $this->filename;
	$print_ext = $extension ? $extension : null;
    $date_dir = date('Ymd');
    $p_path = $this->admin_path . $date_dir;  
	$pn = $print_name . $print_ext;
	$pfile = $p_path .'/'. $pn;
	
	//in case of no dhfass yet...make the first dir
	if (!is_dir($p_path))
	    @mkdir($p_path, 0755); 		
	
    if ($data) { 
	    
		if ($fp = fopen($pfile, "w")) {
  		    $p_data = $data; 
						
		    $ok = fwrite($fp, $p_data, strlen($p_data));
			//self::write2disk($this->taxid.'.log',"SAVE:$pfile\r\n");
				
            fclose($fp);
		
		    return $ok ? true : false;						
        }	
	}
    return false;		
 }
 
 private function unzip_xps_file($file, $extract_to=null) { 
    if (!$file)
        return false;		
			
    $date_dir = date('Ymd');
    $p_path = $this->admin_path . $date_dir;  			
	$p_file = $p_path . '/' . $file;
	$extract_to = $extract_to ? $extract_to .'/' : $this->admin_path . $date_dir . '/' . $this->taxid . '-test/';	
 
    $zip = new ZipArchive; 
    $res = $zip->open($p_file); 
	//self::write2disk('taxcalc.log',"$p_file\r\n");
	//$extfiles = array();
	
    if ($res === TRUE) {
	
	    self::write2disk('taxcalc.log',"Open zip file:" . $p_file ."\r\n");
		
        for($i = 0; $i < $zip->numFiles; $i++) {
		
		  $zfile = $zip->getNameIndex($i);	
		  
		  if (stristr($zfile, 'Documents/1/Pages/1.fpage/')) {//select files to unzip 
		    self::write2disk('taxcalc.log','Filename: ' . $zfile . "\r\n");
		    $extfiles[] = $zfile;			
		  }	
		}  

        //$zip->extractTo($extract_to, $extfiles); 
        $zip->close(); 
        //echo "Ok!"; 
	    //return true;
        return (is_array($extfiles) ? $extfiles : true);//true; //ERROR WHEN ARRAY IS NULL..????	  
	}
    else {
        self::write2disk('taxcalc.log',"Open zip file failed, code:" . $res ."\r\n");
    } 
	
	//$file = fopen('zip://' . $p_file . '#test', 'r');
	
    return false; 
 }
 
  private function modify_xps_file($file, $extfiles, $read_from=null, $searchfor=false, $writeto=false) { 
    if ((!$file)||(empty($extfiles)))
        return false;		
			
    $date_dir = date('Ymd');
    $p_path = $this->admin_path . $date_dir;  			
	$p_file = $p_path . '/' . $file;
	$read_from = $read_from ? $read_from : $this->admin_path . $date_dir . '/' . $this->taxid . '-test/';	
 
    $zip = new ZipArchive; 
    $res = $zip->open($p_file);//, ZIPARCHIVE::OVERWRITE); 
	
    if ($res === TRUE) {
	    self::write2disk('taxcalc.log',"Open zip file for copy:" . $p_file ."\r\n");

        foreach ($extfiles as $i=>$zname) {
		     self::write2disk('taxcalc.log','Filename: ' . $zname . " FINDED!\r\n");	
			 
		    //$zip->addFile($zname);
			if (($searchfor) && ($writeto)) {
			    self::write2disk('taxcalc.log','Filename: ' . $read_from . $zname . " OPENED!\r\n"); 
			    //$mydata = str_replace($searchfor, $writeto, @file_get_contents($read_from . $zname));				
			    //$zip->addFromString($zname, $mydata);				
				
			    //Read contents into memory
                $oldContents = $zip->getFromName($zname); 
				
			    //Modify contents:
                $mydata = str_replace($searchfor, $writeto, $oldContents);//utf8 ????
				//add line ??
				//"<Glyphs Fill=\"#ff000000\" FontUri=\"/Documents/1/Resources/Fonts/38ADF6B2-73F5-498A-A887-A5EFE1591F55.odttf\" FontRenderingEmSize=\"13.4405\" StyleSimulations=\"None\" OriginX=\"65.76\" OriginY=\"1045.44\" Indices=\"22;38;23,55;41;23;27;36;27,57;37,66;25,55;39,73;41,60;41,62;21,55;26;19;28;40;19;21;26,57;27,54;40;38,73;26;25,55;19;28;22,55;20;38,73;22,55;39,73;21;23;23,55;24;26;26;23,96;19;19;19;22,94;19;19,55;19;19,57;19;20,55;21;26,97;20,55;21,55;19;21,57;21,55;28,55;20;25,57;22,54;20,94;40;61;50,79;19,54;27;19,57;20;19,55;27;22;27\" UnicodeString=\"$sign\" />"
				//"</FixedPage>"
				
                //Delete the old...
                $zip->deleteName($zname);
                //Write the new...
                $zip->addFromString($zname, $mydata);
			}			
        }		
	
        $zip->close(); 
        //echo "Ok!"; 	
        return true;		  
	}
    else {
        self::write2disk('taxcalc.log',"Open zip file failed, code:" . $res ."\r\n");
    } 
	
	//$file = fopen('zip://' . $p_file . '#test', 'r');
	
    return false; 
 }
 
 /*private function xps_read_xml($file=null) {
 
    $reader = new XMLReader(); 
	
    $reader->open($file);
    $odt_meta = array();
    while ($reader->read()) {
    if ($reader->nodeType == XMLREADER::ELEMENT) {
        $elm = $reader->name;
    } else {
        if ($reader->nodeType == XMLREADER::END_ELEMENT && $reader->name == 'office:meta') {
            break;
        }
        if (!trim($reader->value)) {
            continue;
        }
        $odt_meta[$elm] = $reader->value;
    }
    }
    print_r($odt_meta);	
 }*/
 
 
 
 //////////////////////////////////////////////////////////////////////////////////////text documents
 protected function create_payps_for_text($data=null, $algo=null, $upper=false) {
    if (!$data) return false;
    $algo = $algo ? $algo : 'sha1';
    $date_time = date('d-m-Y H:i:s');
    $date_dir = date('Ymd');
    $payps_path = $this->admin_path . $date_dir;
	$sid_file = $this->admin_path . $this->taxid . '-sid.txt';	
	$test_sign = null;
	
	$_sid = is_readable($sid_file) ? @file_get_contents($sid_file) : '0'; 
	
	if (!is_dir($payps_path)) {
	    @mkdir($payps_path, 0755); //create today folder  	
		
		if ($this->itaxautoz) {
		    //first payps of day...auto z of prev day..
		    $z_sid = intval($_sid); //before inc for today
		    $z = $this->auto_save_z($z_sid);//id computed inside prev folder
		}
	}
	//test
	//$z_sid = intval($_sid); //before inc for today
	//$z = $this->auto_save_z($z_sid);//id computed inside prev folder	
	//self::write2disk($this->taxid.'.log','ZZZZZZZZZZZzz:'.$z.','.$z_sid."\r\n");
		
	$sid = intval($_sid)+1;	//inc global incer			
	$id = $this->_get_id($payps_path,'.txt',$this->taxid.'-dfss-');
	//echo $id.':'.$sid;
	
	if (@file_put_contents($sid_file, strval($sid), LOCK_EX)) {	
	    //echo 'sidfile created';
	    $ssid = sprintf("%08s",   $sid); // zero-padding sid
	    $nid = sprintf("%04s",   $id); // zero-padding id	
		
		$xps_file = $payps_path . '/' . $this->taxid . '-OUT-'.$id.'.xps';
 
 	    //5.4
        $sign = //'XXX' . //$this->approval_num . //arithoms egrisis 3 chars
                //'99999999' . //$this->product_num . //arithmos paragogis 8 chars			
				$this->taxid .
			    $ssid .//global sid 0001 8 chars			
			    $nid;//day num id 0001 4 chars
				
		$signed_data = $data . $sign;
		
		$signed_file = $payps_path . '/'.$sign.'.txt';	
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
			
			if ($this->save_dfss($hash, $id, $sid)===true) {
			    //echo 'save dfss';
			    //save a file
				if ($id = $this->_save_a_file($data, $id, $sid)) {
				    //echo 'save a file';
				    //save b file
				    if ($payps = $this->_save_b_file($hash, $id, $sid)) {
					    //echo 'save b file';
			            //Z 
                        //$z = $this->save_dhfass($id, $sid);//, $date_dir, true);

						if (!$this->tax_is_active())
                            $test_sign = $this->payps_outlaw_string; 						
			
			            //echo 'save signed data';
                        if ($bytes = self::_write($data . $payps . $test_sign)) { 
						
                            //xps out	
				            $xpsdoc = new xpsfactory();
				            $xpsdoc->xps_load($xps_file,$this->admin_path.'/sample.xps');
				            $xpsdoc->xps_save($this->jf);//job file just saved..							
							
                            return ($bytes);			
						}
					}//else rollback....
                    else
                       self::write2disk('taxcalc.log',"C\r\n");					
				}//else rollback...
				else
                   self::write2disk('taxcalc.log',"B\r\n");
			}//else rollback...
		    else
                self::write2disk('taxcalc.log',"A\r\n"); 
	    }
		else
            self::write2disk('taxcalc.log',"NO HASH\r\n");
	}
    else
        self::write2disk('taxcalc.log',"FILE ERROR\r\n");	
		
	return false;		
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
 
 //3.6 dsym, deltio synopseon ypografon imeras -X-
 public function save_dsym($fid=null, $filetype=null, $date=null) {
    $fid = $fid ? $fid : 'dsym-';
	$fid_maxc = strlen($fid); 
    $date_time = $this->post_date_time();//date('d-m-Y H:i:s');
    $date_dir = $date ? $date : date('Ymd');
    $dsym_path = $this->admin_path . $date_dir;	
	$dsym_data = '';	
	$id=1;
 	$xid_file = $this->admin_path . $this->taxid . '-xid.txt';		
	$empty_x_label = null;	
	$taxid = $this->taxid ? $this->taxid : $_GET['taxid'];
	
    $_xid = is_readable($xid_file) ? @file_get_contents($xid_file) : '0'; 
    $xid = $_xid + 1; 

	$dsym_name = $this->taxid . '-dsym-'.$xid.'.txt';
	$dsym_file = $this->dsym_path . '/' . $dsym_name;	
	echo $dsym_file;
	
	//in case of no dfss yet.
	if (!is_dir($dsym_path))
	    @mkdir($dsym_path, 0755);  	
	
    if (is_dir($dsym_path)) {
        
	    if ($xid) {
		
	        if ($gpayps = $this->_get_z_hash($dsym_path,'_b.txt',$this->taxid, 'sha1', true)) {//use b files taxid = serial recs...
		
		    //self::write2disk($this->taxid.'.log',$gpayps."\r\n");
 
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
                if ($this->idropbox) {
					$log_path = $_SERVER['DOCUMENT_ROOT'] .'/' . str_replace('.printer','',$this->printer_name); 
				    $log_file = $log_path . '/' . $this->taxid . '.log';					
					//self::write2disk($this->taxid.'.log', $log_file."\r\n");
			        $this->save2dropbox($log_file,$this->taxid.'.log');								
				}	
			
		        return ($ok ? true : false);
			}
		    }	
	    }	
	    return false;		 
    }
    
    return false; 	
 }
 
 //3.7 dhfass, deltio hmerisias forologikis anaforas simansis stroixeion (Z)
 public function save_dhfass($id=null, $sid=null, $date_dir=null, $zero_z=false) { 	
    $date_time = $this->post_date_time();//date('d-m-Y H:i:s');
    $date_dir = $date_dir ? $date_dir : date('Ymd'); //else today	
    $dhfass_path = $this->admin_path . $date_dir; 
 	$zid_file = $this->admin_path . $this->taxid . '-zid.txt';	
	$sid_file = $this->admin_path . $this->taxid . '-sid.txt';	
 	$empty_z_label = null;	
	
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
	    //self::write2disk($this->taxid.'.log',$zid."\r\n");
		
	    if ($gpayps = $this->_get_z_hash($dhfass_path,'_b.txt',$this->taxid, 'sha1', true)) {//use b files taxid = serial recs...
		
		    //self::write2disk($this->taxid.'.log',$gpayps."\r\n");
 
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
                if ($this->idropbox) {
					$log_path = $_SERVER['DOCUMENT_ROOT'] .'/' . str_replace('.printer','',$this->printer_name); 
				    $log_file = $log_path . '/' . $this->taxid . '.log';					
					//self::write2disk($this->taxid.'.log', $log_file."\r\n");
			        $this->save2dropbox($log_file,$this->taxid.'.log');								
				}	
			
		        return ($ok ? true : false);
			}
		}	
	}	
	return false;
 }
 
 //get last signed payps folder.. //auto save z
 protected function auto_save_z($sid=null) {
    $date_dir = date('Ymd'); //today	
	//self::write2disk($this->taxid.'.log',$sid."\r\n");
	
	if ($sid) { //has dfss
	    /*$intdate = intval($date_dir); //now
		
		//create date -356 days
		$mk_now = time();
		$mk_yearback = $mk_now - (356 * 24 * 60 * 60);
	    //$yearback = ($intdate-5); //one year back limit..calc...
		$yearback = date('Ymd',$mk_yearback);
		self::write2disk($this->taxid.'.log',$intdate.':'.$yearback."\r\n");	
		
	    //search from <=today (folder has created before) until one year folder back
	    //that has current taxid signed dfss files
		$i = $intdate-1;
        for ($i<$intdate;$i>=$yearback;$i--) { //exclude today < not <=intdate
		*/
		$darray = $this->daily_date_array(90);//90 days back to search for unpublished z
		if (empty($darray)) {
		    self::write2disk($this->taxid.'.log','Z:ERROR IN TIMELINE'."\r\n"); 
			return false;
		}
		
		foreach ($darray as $day=>$i) {
	
        $dfss_path = $this->admin_path . $i;	
		//self::write2disk($this->taxid.'.log',$dfss_path."\r\n");
		
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
		            //self::write2disk($this->taxid.'.log','NO ID'."\r\n");
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
 
 //3.9 efs,eidika forologika stoixeia,(all above) headers footers
 private function save_efs($data=null, $efs_file=null) {
    if (!$efs_file) return;
    $efs_data = null;
 
    if ($data) { 
	    
		if ($fp = fopen($efs_file, "w")) {
  		    $efs_data = $this->efs_header . PHP_EOL .
			            $this->itaxheader  . PHP_EOL . 
		                $data . PHP_EOL . 					  
						$this->efs_taxid_prefix . $this->taxid . PHP_EOL .
						$this->efs_footer . PHP_EOL; 
						
			if (!$this->tax_is_active())			
			    $efs_data .= $this->tax_outlaw_string;
			
            if ($wrap = intval($this->itaxwrap)) {//wrap text
			    $efs_data_wraped = wordwrap($efs_data, $wrap ,PHP_EOL , true);
				$ok = fwrite($fp, $efs_data_wraped, strlen($efs_data_wraped));
				self::write2disk($this->taxid.'.log',$efs_data_wraped);
				
				$xpsdoc = new xpsfactory();
				$xpsdoc->xps_load(str_replace('.txt','.xps',$efs_file),$this->admin_path.'/sample.xps');
				$xpsdoc->xps_save($efs_file);		
            } 			
			else {
		        $ok = fwrite($fp, $efs_data, strlen($efs_data));
				self::write2disk($this->taxid.'.log',$efs_data);
				
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
 
 //return max id
 private function _get_id($dir_name=null, $filetype=null, $fid=null, $not_increment=false) {
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
 
 //counting z's
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
			    $this->save2dropbox($a_file,$a_name,$date_dir);//,$debug_mode);			
		
		    return $ok ? $nid : false;						
        }	
	}
    return false;	
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
			    $this->save2dropbox($b_file,$b_name,$date_dir);//,$debug_mode);				
		
		    return $ok ? $b_data : false;						
        }	
	}
    return false;	
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
                            self::write2disk('taxcalc.log',date(DATE_RFC822)."$file transfered\r\n");									
						}
                        else {	    
						    if ($debug_mode)
                               echo "FTP:File not found:".$file.'<br>';						
                            self::write2disk('taxcalc.log',date(DATE_RFC822)."$file NOT transfered\r\n");																
						}	
					}

					//save to dropbox..in ftp folder... 
                    if ($this->idropbox) 
					    $this->save2dropbox($name,$ftp_path_per_size[$i] ,$debug_mode);
						
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
                    self::write2disk('taxcalc.log',date(DATE_RFC822)."$file transfered\r\n");									
			    }	
			}				
		
            // close the ftp connection		
		    if ($conn_id) 
                ftp_close($conn_id);
			
            //save to dropbox..in dbfolder...			
            if ($this->idropbox) 					
			    $this->save2dropbox($name, $this->idbfolder, $debug_mode);
				
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
 
    //if ($myfolder) {
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
                self::write2disk('taxcalc.log',"CREATE DIR:".$myfolder . PHP_EOL); 		
				
            if ($myfolder) 
		        $this->create_dropbox_directory($myfolder);  	
		}	
	    catch(\Dropbox\Exception $e) {	
	        if ($debug_mode)
		        echo $e->getMessage();
		    else
	            self::write2disk('taxcalc.log',"ERROR:".$e->getMessage() . PHP_EOL);
			//$ret = false;	..always true if error exist
        }		  
	//}
	
    if (!$sourcefile) return ($ret); //true.. if folder exist
	
	//else continue file put
    try {
 
        if ($debug_mode) {
		  echo "<br>DBOXSAVE FILENAME ($userID):". $sourcefile .':'. $targetfile ;
		  echo "<br>DBOXSAVE FOLDER ($userID):". $myfolder ."<br>";
		}  
		else {
          self::write2disk('taxcalc.log',"FILE ($userID):".$sourcefile .':'. $targetfile . PHP_EOL);
		  self::write2disk('taxcalc.log',"FOLDER ($userID):".$myfolder. PHP_EOL);
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
	      self::write2disk('taxcalc.log',"ERROR:".$e->getMessage() . PHP_EOL);

        $ret = false;		  
    }	
	
    self::write2disk('taxcalc.log',"PUT:$put". PHP_EOL);
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
<?php
/* 
 * Class ListenerIPP - Receive Basic IPP requests, Get and parses IPP requests.
 *
 *   Copyright (C) 2012  Alexiou Vassilis
 *
 *   This library is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU Library General Public
 *   License as published by the Free Software Foundation; either
 *   version 2 of the License, or (at your option) any later version.
 *
 *   This library is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *   Library General Public License for more details.
 *
 *   You should have received a copy of the GNU Library General Public
 *   License along with this library; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *   mailto:balexiou@stereobit.com
 *   stereobit.networlds, 27 Allatini st., 54 250 THESSALONIKI -- HELLAS
 *
 */
/*

    This class is intended to implement Internet Printing Protocol on -SERVER- side.

    References needed to debug / add functionnalities:
        - RFC 2910
        - RFC 2911
        - RFC 3380
        - RFC 3382
*/

ignore_user_abort(true);
//set_time_limit(0);

/*spl_autoload_register(function($class){
	$class = str_replace('\\', '/', $class);
	require_once($class . '.php');
});*/            
require_once("ServerIPP.php");

//require_once("AuthIPP.php");//auto
//require_once("UiIPP.php");//auto

define("USE_DATABASE", false);
define("SERVER_LOG", true);
define("AUTH_USER", true); 
define("FILE_DELIMITER", '-');

if (USE_DATABASE==true) {
    require_once("DBStream.php");
    stream_register_wrapper('db', 'DBStream');
}


class IPPlistener extends ServerIPP {

    const AUTH_USER_METHOD = 'BASIC';//''DIGEST';//BASIC//'OAUTH'//'NONE'

    protected $ipp_host, $ipp_ssl, $ipp_printer_uri, $ipp_language, $ipp_user, $ipp_password, $ipp_socument_uri, $ipp_debug_level;
    protected $_request;
	
	protected $printer_uri, $printer_name, $printer_location,  
	          $printer_info, $printer_more_info,  
	          $printer_state, 
	          $document_format_supported, 
			  $printer_is_accepting_jobs, 
			  $printer_state_reasons;
			  
	protected $ip, $printer_path, $printer_path_name;//=path+filename	

    protected $jobs_inline;
    protected $jobs_path, $icons_path, $admin_path;

    protected $force_raw_text;	
	
	protected $authentication_mechanism;
	protected $allowed_users;
	protected $server_time;
	protected $username, $printer_use_quota, $user_admin;
	
	public $authentication;
	
    public function __construct($printer_name=null, $auth=false, $quota=500, $users=null) {
        parent::__construct();

	    spl_autoload_register(array($this, 'loader'));
		//set_error_handler(array($this,'handleError'));		
		
        $this->ip = $_SERVER['SERVER_NAME'].':80';//.$_SERVER['SERVER_PORT'];
        $this->printer_path_name = $_SERVER['PHP_SELF'];
        $this->printers_path = $_SERVER['DOCUMENT_ROOT'] .'/'.pathinfo($_SERVER['PHP_SELF'],PATHINFO_DIRNAME).'/';//'/printers/'; 		
		
		//settings
        $this->printer_uri = 'ipp://' . $this->ip . $this->printer_path_name;		
		$this->printer_name = $printer_name ? $printer_name : pathinfo($_SERVER['PHP_SELF'],PATHINFO_BASENAME);//'index.php';//'ipp-printer'
		$this->printer_location = 'stereobit';
		$this->printer_info = 'stereobit.networlds';
   		$this->printer_more_info = 'http://' . $this->ip . $this->printer_path_name;
		$this->printer_state = 'idle';
		$this->document_format_supported = array("text/plain",
		                                         "text/plain,charset=iso-8859-1",
		                                         "text/plain,charset=iso-8859-7",
												 "application/postscript",
												 "application/vnd.hp-PCL",
												 "application/octet-stream");
		$this->printer_is_accepting_jobs = true;	
		
	    //job-state-reasons...............
	    //none,job-incoming,submission-interrupted,job-outgoing,job-hold-until-specified,resources-are-not-ready
	    //,printer-stopped-partly,printer-stopped,job-interpreting,job-queued,job-transforming,job-printing,job-canceled-by-user
	    //,job-canceled-by-operator,job-canceled-at-device,aborted-by-system,processing-to-stop-point,service-off-line
	    //,job-completed-successfully,job-completed-with-warnings,job-completed-with-errors			
        $this->printer_state_reasons = 'none';	
        $this->authentication_mechanism = $auth ? $auth : constant('self::AUTH_USER_METHOD');//'none'; //none=anonymous user
        //$this->allowed_users = array('billy','test','root','Stelios');//,'anonymous'); //names only
		if (is_array($users)) 
		  $this->allowed_users = (array) $users;
		  						   
		$this->user_admin = null;							 
		
        //??		
		$this->username = null;//$_SESSION['user'] ? $_SESSION['user'] : 'anonymoys';
		$this->printer_use_quota = $quota ? $quota : 500;
		
		$this->jobs_inline = null;
		
		$this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/';
		if ($prn = self::is_named_printer()) {
		    //job dir is sub path when named printer (multiple printers)
		    $this->jobs_path .= $prn . '/'; 
			if (!is_dir($this->jobs_path))
			    @mkdir($this->jobs_path, 0755);
		}	
		
		$this->icons_path = $_SERVER['DOCUMENT_ROOT'] .'/icons/';
		
		$this->admin_path = $_SERVER['DOCUMENT_ROOT'] .'/admin/';
		if ($prn = self::is_named_printer()) {
		    //admin dir is sub path when named printer (multiple printers)
		    $this->admin_path .= $prn . '/'; 
			if (!is_dir($this->admin_path))
			    @mkdir($this->admin_path, 0755);
		}		

        $this->ipp_host = $this->ip; //"localhost"; // set serve'rs host here
        $this->ipp_ssl = false; // enable ssl if true
        $this->ipp_printer_uri = $this->printer_uri; //"/printers/";//Parallel_Port_1"; // set printer uri here
        $this->ipp_language = "en_us";
        //$this->ipp_user = "admin"; // valid user from lpadmin group
        //$this->ipp_password = "password"; // his password
        $this->ipp_document_uri = "http://localhost/";
        $this->ipp_debug_level = 5; // 5: silent; 0: very verbose	

		//$_request = file_get_contents("php://input"); 
        //$printdata = self::print_body($_request);
		
		$this->server_time = date('r');
		self::write2disk('network.log',"\r\n".$_SERVER['REMOTE_ADDR'].":".$this->server_time.":".$this->printer_name.":");					   
		
        //$this->receiveHttp();		
		if (!$this->receiveHttp()) {
		  
		  self::write2disk('network.log',":http:");
		  
          //session_start(); //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< session start moved to UiIPP
		  
		  //echo 'printername:',$this->printer_name,'>';
		  
		  $ui = 'UiIPP';
		  $subclass = str_replace('.php','',pathinfo($_SERVER['PHP_SELF'],PATHINFO_BASENAME));
		  $uiclass = $ui.$subclass;
		  
		  if (class_exists($uiclass)) {//custom ui class per printer
		    //echo $subclass;
		    $con = new $uiclass($this->printer_name, $this->authentication_mechanism, null);
			$ret = $con->printer_console();
		  }	
          elseif (class_exists($ui)) {//default ui class
		    $con = new UiIPP($this->printer_name, $this->authentication_mechanism, null);
		    $ret = $con->printer_console();
		  }	
		  else
		    $ret =  'IPP Server:&nbsp;'.$this->server_version;
		  
		  die($ret);
		}  
		  
		self::write2disk('network.log',":ipp:");
		  

        $this->debug_level = $this->ipp_debug_level; // Debugging
        $this->ssl = $this->ipp_ssl;
        $this->setHost($this->ipp_host);
        $this->setPrinterURI($this->ipp_printer_uri);
        //$this->setUserName("php IPP tester");
        //$this->setFidelity(); // printing abort if every attribute could not be set on printer. NOTE: CUPS do not abort :)
        $this->setCharset('utf-8');
        $this->setLanguage($this->ipp_language);
        //$this->setAuthentication($this->ipp_user,$this->ipp_password); // username & password		
		
        //$printdata = self::ipp_print_body($this->clientoutput->body);		
        //self::write2disk('printjobs.log',"\r\nPRINTJOB[".$this->ipp_printer_uri."]:".$printdata."\r\n");
		
		
		$this->force_raw_text = false;
    }
	
    private function loader($className) {
	
	    //echo 'Trying to load ', $className, ' via ', __METHOD__, "()\n"; 
        self::write2disk($this->printer_name.'.log',"\r\nListerer:Trying to load ". $className. ' via '. __METHOD__ . "()\r\n");
		
		try {
            include $className . '.php';//....PRELOAD UI..NAME ABOVE
			
			//$class = str_replace('\\', '/', $className);
			//require_once($class . '.php');//..error if not exist..
			
			$ok = "\r\n File $className loaded!";
			self::write2disk($this->printer_name.'.log',$ok);
		} 
		catch (Exception $e) {
            $err = "\r\n File $className not exist!";
			self::write2disk($this->printer_name.'.log',$err);
        }
		
		self::write2disk($this->printer_name.'.log',"\r\nListener:End of load ". $className. ' via '. __METHOD__. "()\r\n");
    }	
	
    public function ipp_send_reply($test_file_reply=null) {
	
	    if ($this->authenticate_user(/*true*/)==false) {
		
	        self::ipp_send_response_headers(null, true);

		 	//client-error-not-authenticated 
		    $status = $this->setStatusCode('client-error-not-authenticated');
            $ret = $this->build_ipp_response($status,$operationattributes,$jobattributes,$printerattributes);
			
			self::write2disk('network.log',":$this->username(login-failed):");
			
			return ($ret);		   
		}
		
		self::write2disk('network.log',":$this->username:");
	
	    if (is_readable($test_file_reply)) {
		  
		  $ipp_response = file_get_contents($test_file_reply);
	    }
		else
	   	  $ipp_response = self::ipp_send_response_attributes();
		  
		$content_lenght = strlen($ipp_response);
	   
	    self::ipp_send_response_headers($content_lenght);
	
        self::write2disk('printer.log',"\r\nRESPONSE[".$this->ipp_host."]:".$ipp_response."\r\n");
	   
        echo $ipp_response;	
	    die();
    }		

    protected function ipp_send_response_headers($content_length=null, $not_authenticated=false) {
	
       /*header("Cache-Control: no-cache");
       header("Connection: close");
       header("Date:".date('r'));
       header("Pragma: no-cache");*/
	   
	   //header("Content-Encoding: chunked");
       //header("Transfer-Encoding: chunked");	
	   
	   //header("Server: Virata-EmWeb/R6_2_1");
	   header("Server: ZUPS/1.0.1");
	   header("Connection: Keep-Alive");
	   header("Keep-Alive: timeout=30");
	   header("Content-Language: en_US");
	   
	   if ($not_authenticated) {
         //if (($auth_method = constant('self::AUTH_USER_METHOD')) && ($auth_method==='DIGEST')) {//NOT TESTED....
		 if ($this->authentication_mechanism==='DIGEST') {//NOT TESTED....
		   $uniqid = uniqid("");
		   $realm = $this->printer_name;
           header('WWW-Authenticate: Digest realm="'.$realm.'" qop="auth" nonce="'.$uniqid.'" opaque="'.md5($realm).'"');		 	   
		 }   
	     else
           //header('WWW-Authenticate: Basic realm=\"$this->printer_name\"');
	       header("WWW-Authenticate: Basic realm=\"$this->printer_name\",stale=FALSE");
		 
         header('HTTP/1.0 401 Unauthorized');
		
	   }
       //header("Cache-Control: max-age=3600, public");//no-cache");
	   
       header("Content-Type: application/ipp");	

       if ($content_length)	   
	     header("Content-Length: ".$content_length);
    } 
	
	protected function ipp_send_response_attributes() {
	   $status = null;
	   
	   	/* $status = $this->setStatusCode('client-error-document-access-error');
         $ret = $this->build_ipp_response($status,$operationattributes,$jobattributes,$printerattributes);	
		 return ($ret);*/
		 
		 /*$this->setAttribute('status-message','client-error-document-access-error');  
         $status = $this->setStatusCode('client-error-document-access-error');
         $ret = $this->build_ipp_response($status,$operationattributes,$jobattributes,$printerattributes);	
		 return ($ret);*/			
	
	   if ($this->support_ipp_version()) {
	   
	     //$auth = $this->authenticate_user(); //moved in print-job ....
	     //if (!$auth) {
		    /*
	        $this->setAttribute('printer-uri',$this->printer_uri);			 
		    $this->setAttribute('printer-uri-supported',$this->printer_uri);
		    $this->setAttribute('uri-security-supported','none');//none,ssl3,tls
            $this->setAttribute('uri-authentication-supported','basic'); //none,requesting-user-name,basic,digest,certificate			
					 
            self::_buildValues ($operationattributes,$jobattributes,$printerattributes);
		    ///////////////////////////////////////////////////////////////////////
		    self::write2disk('security.log',"\r\n".$operationattributes.$jobattributes.$printerattributes);
		    */
		 	//client-error-not-authenticated 
		    //$status = $this->setStatusCode('client-error-not-authenticated');
            //$ret = $this->build_ipp_response($status,$operationattributes,$jobattributes,$printerattributes);
			//return ($ret);
		 //}
		 //rfc2911 8.3
	     /*$username = self::read_request_attribute('requesting-user-name');
         if ($username)// = $user ? $user : 'anonymous'; //error ?
		   $this->setAttribute('job-originating-user-name', $username);			 
	     */
		 
		 self::write2disk('network.log',":".$this->clientoutput->operation_id.":");
		 
	     switch ($this->clientoutput->operation_id) {
		 
		 case 'Print-Uri':  $status = $this->print_uri();
		 break;
		 
		 case 'Validate-Job': $status = $this->validate_job();
		 break;
		 
		 case 'Create-Job': $status = $this->create_job();
		 break;

		 case 'Send-Uri': $status = $this->send_uri();
		 break;		 
		 
		 case 'Send-Document': $status = $this->send_document();
		 break;		 
		 
		 case 'Cancel-Job': $status = $this->cancel_job();
		 break;	
		 
		 case 'Hold-Job': $status = $this->hold_job();
		 break;	

		 case 'Release-Job': $status = $this->release_job();
		 break;	

		 case 'Restart-Job': $status = $this->restart_job();
		 break;			 

		 case 'Set-Job-Attributes':	 $status = $this->set_job_attributes();
		 break;			 
		 
		 case 'Get-Job-Attributes':	 $status = $this->get_job_attributes();
		 break;		 
		 
		 case 'Get-Jobs': $status = $this->get_jobs();
		                  $this->_flush_log_files('system::flush'); //periodically calls by connected printers
		 break;
		 
		 case 'Purge-Jobs': $status = $this->purge_jobs();
		 break;			 
		 
		 case 'Get-Printer-Attributes':	 $status = $this->get_printer_attributes();
		                                 $this->_fire_up_agent(null,null,true); //periodically calls by connected printers
										 //$this->_flush_log_files('system::flush'); //periodically calls by connected printers
		 break;
		 
		 case 'Set-Printer-Attributes':	 $status = $this->set_printer_attributes();
		 break;		 
		 
		 case 'Pause-Printer': $status = $this->pause_printer();
		 break;			 
		 
		 case 'Resume-Printer': $status = $this->resume_printer();
		 break;			 
		 
		 case 'Print-Job': $status = $this->print_job();
		 break;
		 
	     default :/*
	                //test collection
		            //$this->setCollection('media-col',array('media-color'=>'blue','media-size'=>'a4'));
		            $this->setCollectionAttribute('media-col','media-color','blue');
		            $this->setCollectionAttribute('media-col','media-size',array('x-dimension','y-dimension'));//array('x-dimension'=>6,'y-dimension'=>4));//'a4');		 
		            $this->setCollectionAttribute('media-size','x-dimension','6');
		            $this->setCollectionAttribute('media-size','y-dimension','4');			 
	   
	                //send reply
		            //$ret = self::print_job_response_successful($this->clientoutput->request_id);
		 
	                $this->setAttribute('job-id',147);		
	                $this->setAttribute('job-uri','ipp://forest/pinetree/123');			
	                $this->setAttribute('job-state','pending');
					
	                $this->setAttribute('status-message','successful-ok');		 
					
		            $status = $this->setStatusCode('successful-ok');*/
					
					//$this->setAttribute('status-message','server-error-operation-not-supported');
					$this->setAttribute('status-message','Missing required attributes!');
					//$status = $this->setStatusCode('server-error-operation-not-supported');
					$status = $this->setStatusCode('client-error-bad-request');
         }
		 
		 if (ord($status) > 0x0002) {//error
		 
		   $ret = $this->build_ipp_response($status,$operationattributes,$jobattributes,$printerattributes);//.'MYMYHEYHEY'.$status;
		   return ($ret);
		 }
		 
		 //switch ($status) {

	     //read response and build attributes..override if set
	     self::build_request_attributes($unsupportedattributes);
         /////////////////////////////////////////////////////////////////////////////////////		 
         //self::write2disk('unsupported.log',"\r\n".$unsupportedattributes);			 
		 
         self::_buildCollections ($operationcollections,$jobcollections,$printercollections);
         /////////////////////////////////////////////////////////////////////////////////////		 
         self::write2disk('collection.log',"\r\n".$operationcollections.$jobcollections.$printercollections);		 
		 
         self::_buildValues ($operationattributes,$jobattributes,$printerattributes);
		 ///////////////////////////////////////////////////////////////////////
		 self::write2disk('tagval.log',"\r\n".$operationattributes.$jobattributes.$printerattributes);		 
		 
		 //null = success = chr(0x00) . chr (0x00)
		 //$status = $this->setStatusCode('successful-ok');
		 
		 if ($this->jobs_inline)//get job list...
		    $ret = $this->build_ipp_response($status,$operationattributes.$operationcollections,
		                                             $this->jobs_inline,
											         $printerattributes.$printercollections,
											         $unsupportedattributes
		                                    );	
		 else
            $ret = $this->build_ipp_response($status,$operationattributes.$operationcollections,
		                                             $jobattributes.$jobcollections,
											         $printerattributes.$printercollections,
											         $unsupportedattributes
									        );									
	   }
	   else {
		 //version-not-supported
		 //chr(0x05) . chr(0x03));//server-error-version-not-supported);// 
		 $status = $this->setStatusCode('server-error-version-not-supported');
         $ret = $this->build_ipp_response($status,$operationattributes,$jobattributes,$printerattributes);		 
	   }
	   
	   return ($ret);
    }	
	
    protected function ipp_print_body(& $data, $file=null) {
	    $body_start_offset = $this->_parsing->offset + 1; //parsing has done, print data follows
		$tail = 0;
 
        if (($file) && ($fp = @fopen ($this->jobs_path . $file , "wb"))) {
		
			//@file_put_contents('postdata.log', ">");//byte meter start
		
		    $this->_parsing->offset += 1;
			if ($this->clientoutput->body[$this->_parsing->offset] == chr(0x16)) {
			    //Forcing data to be interpreted as RAW TEXT
			    $this->force_raw_text = true;
				$this->_parsing->offset += 1;
				$tail = 1;
			}
			
			/*
		    for ($i = $this->_parsing->offset; $i < (strlen($this->clientoutput->body)-$tail) ; $i = $this->_parsing->offset) {
                //$tag = ord($this->clientoutput->body[$this->_parsing->offset]);
			    fwrite ($fp, $this->clientoutput->body[$i], 1);
			    $this->_parsing->offset += 1;
				
			    //@file_put_contents('postdata.log', $this->clientoutput->body[$i], FILE_APPEND);	//byte meter			
		    }
			*/
			//.......SPEED UP!!!!!!
			$length_left_to_read = strlen($this->clientoutput->body) - $this->_parsing->offset;
			fwrite ($fp, substr($this->clientoutput->body, $this->_parsing->offset), $length_left_to_read);
			//@file_put_contents('postdata.log', substr($this->clientoutput->body, $this->_parsing->offset));			
			
            fclose ($fp);
			
            return true;    			
		}
		else {
            $body = substr($data, $body_start_offset);
			
		    if (substr($body,0,1) == chr(0x16)) {
		        //Forcing data to be interpreted as RAW TEXT
		        $this->force_raw_text = true;
		        $body = substr($body,1,strlen($body)-1); //extract head char and tail char...
		    }

		    return ($body);	
		}

        return false;		
    }
	
	
	///////////////////////////////// required/optional responses RFC 2566 5.2.2
	
	//printer object IPP 1.0
	protected function print_job() {//required
	    $status = null;
		
		if (self::_get_printer_state()=='stopped') {
		    //stopped /paused printer
		    $this->setAttribute('status-message','Not allowed to print');
            $status = $this->setStatusCode('server-error-not-accepting-jobs');
			return ($status);			
        }		
		
		//QUOTA
		$quota = self::get_user_quota($this->username,$this->printer_name);
	    if ($quota > $this->printer_use_quota) {
		
		    //self::write2disk('_qqq.log','USER:'.$quota."\r\n".'PRINTER QUOTA:'.$this->printer_use_quota."\r\n");
		
		    //quota failure...client not responding with auth error, just the job is not processing
		    $this->setAttribute('status-message','Not allowed to print');
            $status = $this->setStatusCode('client-error-not-authorized');
			return ($status);		
		}

        $job_data = self::ipp_print_body($this->clientoutput->body, 'temp.tmp');		 
		 
		if ($job_data) {
		 
            if ($job_id = self::_print_job($job_data)) {
		   
		        self::_write_job_attributes($job_id);
				
				$status = $this->get_job_attributes();
				
				//add quota 
				self::set_user_quota(1, $this->username,$this->printer_name); 
		    }
		    else {
		        //server failure
		        $this->setAttribute('status-message','server-error-job-canceled');
		   
                $status = $this->setStatusCode('server-error-job-canceled');		   
		    }
        }
        else {
		    //client failure
		    $this->setAttribute('status-message','server-error-device-error');
		   
            $status = $this->setStatusCode('server-error-device-error');			 
        }
 		 
		return ($status); 
	}
	
	protected function print_uri() {//optional
	    $status = null;
		$job = $_GET['job'];

        $job_data = self::_print_uri();//true;	 
		 
		if ($job_data) {

            if ($job_id = self::_print_job($job_data)) {
		   
		        self::_write_job_attributes($job_id);
				
				$status = $this->get_job_attributes();
		    }
		    else {
		        //server failure
		        $this->setAttribute('status-message','server-error-device-error');
		   
                $status = $this->setStatusCode('server-error-device-error');		   
		    }			
		}
        else {
		    //client document access failure
		    $this->setAttribute('status-message','client-error-document-access-error');
		   
            $status = $this->setStatusCode('client-error-document-access-error');			 
        }
 		 
		return ($status); 		 
	}	
	
	protected function validate_job() {//required
	    $status = null;
		$job = $_GET['job'];
		 
	    $job_id = $job ? $job : self::_get_last_job_id();	
	
        if (self::_validate_job($job_id)) {
		   
		    //self::_write_job_attributes($job_id);
			
		    //$status = $this->get_job_attributes();
			
		    //validate job
		    $this->setAttribute('status-message','successful-ok');
		   
            $status = $this->setStatusCode('successful-ok');			
		}
		else {
		    //server failure
		    $this->setAttribute('status-message','server-error-device-error');
		   
            $status = $this->setStatusCode('server-error-device-error');		   
		}	
	}	
	
	protected function create_job() {//optional
	    $status = null;
		
		//operation not supported...
		//server-error-operation-not-supported or client-error-multiple-document-jobs-not-supported  
		//$this->setAttribute('status-message','server-error-operation-not-supported'); 
        //$status = $this->setStatusCode('server-error-operation-not-supported');	
        //return ($status);
		
        $job_data = true;//self::ipp_print_body($this->clientoutput->body, 'temp.tmp');		 
		 
		if ($job_data) {
		 
            if ($job_id = self::_create_job($job_data)) {
				
	            $this->setAttribute('job-id',$job_id);		
	            $this->setAttribute('job-uri',"ipp://$this->ip/jobs/?job=".$job_id . (self::is_named_printer() ? '&printer='.self::is_named_printer() : ''));			
	            $this->setAttribute('job-state','pending-held');//pending or pending-held / proccesing
			    $this->setAttribute('job-state-reasons','job-data-insufficient'); //job-data-insufficient / job-incoming			
		   
		        self::_write_job_attributes($job_id);
				
				//$status = $this->get_job_attributes();
				$this->setAttribute('status-message','successful-ok');
				
		        $status = $this->setStatusCode('successful-ok');
				
				//waiting for send-doocument,send-uri commands...
		    }
		    else {
		        //server failure
		        $this->setAttribute('status-message','server-error-job-canceled');
		   
                $status = $this->setStatusCode('server-error-job-canceled');		   
		    }
        }
        else {
			//client failure
		    $this->setAttribute('status-message','server-error-device-error');
		   
            $status = $this->setStatusCode('server-error-device-error');			 
        }
 		 
		return ($status); 	
	}	
	
	protected function get_printer_attributes() {//required
	    $status = null;
		
		if (self::_get_printer_state()=='stopped')
		  $this->printer_is_accepting_jobs = false;
		else  
		  $this->printer_is_accepting_jobs = true;
		 
		$requested_attributes = self::read_request_attribute('requested-attributes');
		 
		if ($requested_attributes == 'all') {
		 
	        $this->setAttribute('printer-uri',$this->printer_uri);//"ipp://$this->ip/printers/");			 
	
		    /*$this->setAttribute('printer-uri-supported',//explode('|',printer-uri-supported));
		                                                array("ipp://$this->ip/printers/",
		                                                "ipp://$this->ip/printers/index.php"));
		 
		    $this->setAttribute('uri-security-supported',array('none','none'));	*/
			
		    $this->setAttribute('printer-uri-supported',$this->printer_uri);
		    $this->setAttribute('uri-security-supported','none');//none,ssl3,tls
            $this->setAttribute('uri-authentication-supported','requesting-user-name'); //none,requesting-user-name,basic,digest,certificate			
			
		    $this->setAttribute('printer-name',$this->printer_name);//'ippprinter');	
		    $this->setAttribute('printer-location',$this->printer_location);//'stereobit');
            $this->setAttribute('printer-info',$this->printer_info);//'stereobit.networlds');	
            $this->setAttribute('printer-more-info',$this->printer_more_info);//'http://www.stereobit.gr');	
            $this->setAttribute('printer-driver-installer',"http://$this->ip/printers/install.php");
		    $this->setAttribute('printer-make-and-model','model 1');
		    $this->setAttribute('printer-more-info-manufacturer','http://www.stereobit.gr');
            $this->setAttribute('printer-state',self::_get_printer_state());//'idle');
		    $this->setAttribute('printer-state-reasons','none');
		    $this->setAttribute('printer-state-message','test');//????
            $this->setAttribute('operations-supported',array('Print-Job','Print-URI','Validate-Job','Create-Job','Send-Document','Send-URI','Cancel-Job','Get-Job-Attributes','Get-Jobs','Get-Printer-Attributes'));
            $this->setAttribute('charset-configured','en-us');
            $this->setAttribute('charset-supported',array('us-ascii','iso-8859-1','iso-8859-7','utf-8'));
            $this->setAttribute('natural-language-configured','en-us');		 
		    $this->setAttribute('generated-natural-language-supported',array('en-us','utf-8'));
		    $this->setAttribute('document-format-default','text/plain');
		    $this->setAttribute('document-format-supported',array('text/plain','text/plain,charset=iso-8859-1','text/plain,charset=iso-8859-7','application/postscript','application/vnd.hp-PCL','application/octet-stream'));
		    $this->setAttribute('printer-is-accepting-jobs',$this->printer_is_accepting_jobs); 
		    $this->setAttribute('queued-job-count',self::_count_jobs());//0);
		    $this->setAttribute('pdl-override-supported','not-attempted');
		    $this->setAttribute('printer-up-time',time());	
		    //1.1
		    $this->setAttribute('ipp-versions-supported',$this->ipp_version);//array('1.0','1.1'));	
		    $this->setAttribute('compression-supported',array('none'));
			//CUPS
			$this->setAttribute('printer-is-shared',true);
			$this->setAttribute('server-is-sharing-printers',true);	
			//$this->setAttribute('auth-info-required','negotiate'); //domain,none,username,password,negotiate?
			//....
			$this->setAttribute('printer-op-policy','default');
		 
		    //self::_read_job_attributes(); //?????
		 }
		 else { //is_array($requested_attributes)..read vals and set
		 
			//test values
			$printer_state_reasons = 'none';
			$printer_state_message = 'test';			
				
			//..is requested_attributes an array ...
			if (is_array($requested_attributes)) {
			
				foreach ($requested_attributes as $attr) {
					
				    $attr_value = str_replace('-','_',$attr);
						
				    if ($value = ${$attr_value})
				        $this->setAttribute($attr,$value); 
				    //else
				       //$this->setAttribute($attr,'none'); //unknown attr values.....error......
					   //set unsupported attributes.....????
				}	
			}				
			else { 
			    //all....???
		        //$this->setAttribute('printer-uri',$this->printer_uri); 
				
		        $this->setAttribute('printer-uri-supported',$this->printer_uri);
		        $this->setAttribute('uri-security-supported','tls');//none,ssl3,tls	
                $this->setAttribute('uri-authentication-supported','requesting-user-name'); //none,requesting-user-name,basic,digest,certificate							
		        $this->setAttribute('printer-name',$this->printer_name);//'index.php');//'ippprinter');	
		        $this->setAttribute('printer-location',$this->printer_location);//'stereobit');
                $this->setAttribute('printer-info',$this->printer_info);//'stereobit.networlds');	
                $this->setAttribute('printer-more-info',$this->printer_uri);//$this->printer_more_info);//'http://www.stereobit.gr');	
                //$this->setAttribute('printer-driver-installer',"http://$this->ip/printers/install.php");
		        //$this->setAttribute('printer-make-and-model','model 1');
		        //$this->setAttribute('printer-more-info-manufacturer','http://www.stereobit.gr');
                $this->setAttribute('printer-state',self::_get_printer_state());//'idle');
		        $this->setAttribute('printer-state-reasons','none');
		        $this->setAttribute('printer-state-message','Ready to Print');
                $this->setAttribute('operations-supported',array('Print-Job','Print-URI','Validate-Job','Create-Job','Send-Document','Send-URI','Cancel-Job','Get-Job-Attributes','Get-Jobs','Get-Printer-Attributes'));
                $this->setAttribute('charset-configured','utf-8');//'en-us');
                $this->setAttribute('charset-supported','us-ascii');//array('us-ascii','iso-8859-1','iso-8859-7','utf-8'));
                $this->setAttribute('natural-language-configured','en-us');		 
		        $this->setAttribute('generated-natural-language-supported','en-us');//array('en-us','utf-8'));
		        $this->setAttribute('document-format-default','application/octet-stream');//'text/plain');
		        $this->setAttribute('document-format-supported',array('application/octet-stream','application/pdf','application/postscript','application/rss+xml','application/vnd.cups-banner','application/vnd.cups-command',
				                                                      'application/vnd.cups-pdf','application/vnd.cups-postscript','application/vnd.cups-ppd','application/vnd.cups-raster','application/vnd.cups-raw',
																	  'application/vnd.hp-hpgl','application/x-cshell','application/x-csource','application/x-perl','application/x-shell','image/gif','image/jpeg','image/png','image/tiff',
																	  'image/x-alias','image/x-bitmap','image/x-icon','image/x-photocd','image/x-portable-anymap','image/x-portable-bitmap','image/x-portable-greymap','image/x-portable-pixmap',
																	  'image/x-sgi-rgb','image/x-sun-raster','image/x-xbitmap','image/x-xpixmap','text/css','text/html','text/plain'));
				                                                //array('text/plain','text/plain,charset=iso-8859-1','text/plain,charset=iso-8859-7','application/postscript','application/vnd.hp-PCL','application/octet-stream'));
		        $this->setAttribute('printer-is-accepting-jobs',$this->printer_is_accepting_jobs); 
		        $this->setAttribute('queued-job-count',0);//self::_count_jobs());//0);
		        $this->setAttribute('pdl-override-supported','attempted');//not-attempted');
		        $this->setAttribute('printer-up-time',time());	
		        //1.1
		        $this->setAttribute('ipp-versions-supported',$this->ipp_version);//'1.0');//array('1.0','1.1'));	
		        //$this->setAttribute('compression-supported',array('none'));	
			    //CUPS
			    $this->setAttribute('printer-is-shared',false);
			    $this->setAttribute('server-is-sharing-printers',true);			
				//$this->setAttribute('auth-info-required','negotiate');//domain,none,username,password,negotiate?
				//....
			    $this->setAttribute('printer-op-policy','default');				
			}
		 }
		 
	     //$this->setAttribute('status-message','successful-ok');//'successful-ok-ignored-or-substituted-attributes');		 
		 
		 $status = $this->setStatusCode('successful-ok');//'successful-ok-ignored-or-substituted-attributes');
		 return ($status); 
	}
	
	protected function set_printer_attributes() {
	    $status = null;	
		$job = $_GET['job'];
		 
	    $job_id = $job ? $job : self::_get_last_job_id();	
	
        if (self::_validate_job($job_id)) {
		   
		    //self::_write_job_attributes($job_id, true);//???????
			
		    //$status = $this->get_job_attributes();
			
		    $this->setAttribute('status-message','successful-ok');
		   
            $status = $this->setStatusCode('successful-ok');			
		}
		else {
		    //server failure
		    $this->setAttribute('status-message','server-error-device-error');
		   
            $status = $this->setStatusCode('server-error-device-error');		   
		}	
	}	 

	protected function pause_printer() {
	    $status = null;	 
        $pause = true;	

        if ($this->username != $this->get_printer_admin()) {//'admin') {
		    $this->setAttribute('status-message','Not allowed to pause printer');
            $status = $this->setStatusCode('client-error-not-authorized');
			return ($status);		
		}		
		 
		if ($pause) {
		
			self::_set_printer_state('stopped');
			
            $this->setAttribute('printer-message-from-operator','pause printer');			
			
	        $this->setAttribute('status-message','successful-ok');	
			 
		    $status = $this->setStatusCode('successful-ok');			
		}
        else {
		    //device failure
		    $this->setAttribute('status-message','server-error-device-error');
		   
            $status = $this->setStatusCode('server-error-device-error');			 
        }
 		 
		return ($status); 		
	}	

	protected function resume_printer() {
	    $status = null;	 
        $resume = true;

        if ($this->username != $this->get_printer_admin()){//'admin') {
		    $this->setAttribute('status-message','Not allowed to resume printer');
            $status = $this->setStatusCode('client-error-not-authorized');
			return ($status);		
		}		
		 
		if ($resume) {
		
		    self::_set_printer_state(); //resume...
			
			$this->setAttribute('printer-message-from-operator','resume printer');
			
	        $this->setAttribute('status-message','successful-ok');	
			 
		    $status = $this->setStatusCode('successful-ok');			
		}
        else {
		    //device failure
		    $this->setAttribute('status-message','server-error-device-error');
		   
            $status = $this->setStatusCode('server-error-device-error');			 
        }
 		 
		return ($status); 		
	}	
	
	protected function get_jobs() {//required
	    $status = null;
		$job = $_GET['job'];		 

        $job_data = true;	 
		 
		if ($job_data) {
		
			$requested_attributes = self::read_request_attribute('requested-attributes');
			/////////////////////////////////////////////////////////////
			//$rattr = var_export($requested_attributes,1) . "\r\n";
			//self::write2disk('jobsinline.log',"JOBS_INLINE:\r\n".$rattr."\r\n");
					
            if ( ($jobs = self::_read_jobs($this->username)) && (is_array($jobs)) ) {
			    /////////////////////////////////////////////////////////////
			    //$jbs = var_export($jobs,1) . "\r\n";
			    //self::write2disk('jobsinline.log',"JOBS_INLINE:\r\n".$jbs."\r\n");			 
			 
			    //foreach ($jobs as $i=>$jname) {
				while ($jname = array_shift($jobs)) {//array_pop ..from end of array

				  $job_attr = self::_read_job($jname);
				 
				  /////////////////////////////////////////////////////////////
				  //$jattr = var_export($job_attr,1) . "\r\n";
			      //self::write2disk('jobsinline.log',"JOBS_INLINE:\r\n".$jattr."\r\n");
				 
				  //job values 
				  $job_id = $job_attr['job-id'];
				  $job_uri = "ipp://$this->ip/jobs/?job=" . $job_id . (self::is_named_printer() ? '&printer='.self::is_named_printer() : '');
				  //$job_state = $job_attr['data'] ? 'processing' : 'completed'; //if data exists ..process...
				  $job_state = $job_attr['job-state'] ? $job_attr['job-state'] : 'pending';
				  $job_state_reasons = 'none';
				  $job_name = $job_attr['job-name'];
			      
				  //if ($this->client_attributes->requested_attributes->_value0=='all') {
				  if ($requested_attributes == 'all') {
	                $this->setAttribute('job-id',$job_id);		
	                $this->setAttribute('job-uri',$job_uri);			
	                $this->setAttribute('job-state',$job_state);
			        $this->setAttribute('job-state-reasons',$job_state_reasons);
				    $this->setAttribute('job-name',$job_name);
					
					self::_read_job_attributes($job_id);
				  }
				  else {//is_array($requested_attributes)..read vals and set...
				  
			        //test values
			        //..see above..job values			
				
			        //..is requested_attributes an array ...
			        if (is_array($requested_attributes)) {
			
				        foreach ($requested_attributes as $attr) {
					
				            $attr_value = str_replace('-','_',$attr);
						
				            if ($value = ${$attr_value})
				                $this->setAttribute($attr,$value); 
				            else
				               //$this->setAttribute($attr,'none'); //unknown attr values
							   $this->setAttribute($attr,self::_read_job_attributes($job_id, $attr)); //get attr value from job
				        }	
			        }				
			        else {				  
				  
	                    $this->setAttribute('job-id',$job_id);		
	                    $this->setAttribute('job-uri',$job_uri);			
	                    $this->setAttribute('job-state',$job_state);
			            $this->setAttribute('job-state-reasons',$job_state_reasons);
				        $this->setAttribute('job-name',$job_name);

                        self::_read_job_attributes($job_id);					
					}
				  }
				  
			      self::_buildValues ($dummy,$jobattributes,$dummy);
				 
			      $this->jobs_inline .= $jobattributes;	
                  
                  if (!empty($jobs))  				  
                    $this->jobs_inline .= chr(0x02);  // next job-attributes				 
                }		 
			 
			   /////////////////////////////////////////////////////////////
			   self::write2disk('jobsinline.log',"JOBS_INLINE:\r\n".$this->jobs_inline."\r\n");
			}  
		   
	        $this->setAttribute('status-message','successful-ok');	
			 
		    $status = $this->setStatusCode('successful-ok');		 
		}
        else {
		    //client failure
		    $this->setAttribute('status-message','server-error-device-error');
		   
            $status = $this->setStatusCode('server-error-device-error');			 
        }
 		 
		return ($status); 		
	}	
	
	protected function purge_jobs() {
	    $status = null;		

        if ($this->username != $this->get_printer_admin()) {//'admin') {
		    $this->setAttribute('status-message','Not allowed to pause printer');
            $status = $this->setStatusCode('client-error-not-authorized');
			return ($status);		
		}		
		 
		if ( ($jobs = self::_read_jobs($this->username)) && (is_array($jobs)) ) {
				   
            while ($jname = array_shift($jobs)) //array_pop ..from end of array				   
			        self::_purge_job($jname);
			
	        $this->setAttribute('status-message','successful-ok');	
			 
		    $status = $this->setStatusCode('successful-ok');			
		}
        else {
		    //client failure
		    $this->setAttribute('status-message','server-error-device-error');
		   
            $status = $this->setStatusCode('server-error-device-error');			 
        }
 		 
		return ($status); 		
	}	
	
	//job object IPP 1.0
	protected function send_document() {//optional
	    $status = null;
		$job = $_GET['job'];		

        $job_data = self::_send_document();//self::ipp_print_body($this->clientoutput->body, 'temp.tmp');		 
		 
		if ($job_data) {
		 
		    $create_job_id = $job ? $job : self::_get_last_job_id();
			
            if ($job_id = self::_print_job($job_data, $create_job_id)) {
		   
		        self::_write_job_attributes($job_id);
				
				$status = $this->get_job_attributes();
		    }
		    else {
		        //server failure
		        $this->setAttribute('status-message','server-error-device-error');
		   
                $status = $this->setStatusCode('server-error-device-error');		   
		    }
        }
        else {
		    //client failure
		    $this->setAttribute('status-message','client-error-bad-request');
		   
            $status = $this->setStatusCode('client-error-bad-request');			 
        }
 		 
		return ($status); 		
	}
	
	protected function send_uri() {//optional
	    $status = null;
		$job = $_GET['job'];		

        $job_data = self::_send_uri();
		 
		if ($job_data) {
		 
		    $create_job_id = $job ? $job : self::_get_last_job_id();
			
            if ($job_id = self::_print_job($job_data, $create_job_id)) {
		   
		        self::_write_job_attributes($job_id);
				
				$status = $this->get_job_attributes();
		    }
		    else {
		        //server failure
		        $this->setAttribute('status-message','server-error-device-error');
		   
                $status = $this->setStatusCode('server-error-device-error');		   
		    }
        }
        else {
		    //client document access failure
		    $this->setAttribute('status-message','client-error-document-access-error');
		   
            $status = $this->setStatusCode('client-error-document-access-error');			 
        }
 		 
		return ($status); 		
	}	
	
	protected function cancel_job() {//required
	     $status = null;
		 $job = $_GET['job'];
		 
	     $job_id = $job ? $job : self::_get_last_job_id();
		 
         if (self::_modify_job_attributes($job_id, 'job-status=cancel;', true)) {
			
		    $this->setAttribute('status-message','successful-ok');
		 
		    $status = $this->setStatusCode('successful-ok');		 
		 }
         else {
		    $this->setAttribute('status-message','server-error-device-error');
		   
            $status = $this->setStatusCode('server-error-device-error');			 
         }
 		 
		 return ($status);		 
	}
	
	protected function hold_job() {
	     $status = null;
		 $job = $_GET['job'];
		 
	     $job_id = $job ? $job : self::_get_last_job_id();
		 
		 if (self::_modify_job_attributes($job_id, 'job-status=hold;', true)) {
		 
		    $this->setAttribute('status-message','successful-ok');
		 
		    $status = $this->setStatusCode('successful-ok');	
		 }
         else {
		    $this->setAttribute('status-message','server-error-device-error');
		   
            $status = $this->setStatusCode('server-error-device-error');			 
         }
 		 
		 return ($status);		 
	}	
	
	protected function release_job() {
	     $status = null;
		 $job = $_GET['job'];
		 
	     $job_id = $job ? $job : self::_get_last_job_id();
		 
		 if (self::_modify_job_attributes($job_id, 'job-status=release;', true)) {
		 
		    $this->setAttribute('status-message','successful-ok');
		 
		    $status = $this->setStatusCode('successful-ok');	
		 }
         else {
		    $this->setAttribute('status-message','server-error-device-error');
		   
            $status = $this->setStatusCode('server-error-device-error');			 
         }
 		 
		 return ($status);		 
	}	
	
	protected function restart_job() {
	     $status = null;
		 $job = $_GET['job'];
		 
	     $job_id = $job ? $job : self::_get_last_job_id();
		 
         if (self::_modify_job_attributes($job_id, 'job-status=restart;', true)) {
		 
		    $this->setAttribute('status-message','successful-ok');
		 
		    $status = $this->setStatusCode('successful-ok');	
		 }
         else {
		    $this->setAttribute('status-message','server-error-device-error');
		   
            $status = $this->setStatusCode('server-error-device-error');			 
         }
 		 
		 return ($status);		 
	}		

	protected function get_job_attributes() {//required
	    $status = null;
		$job = $_GET['job'];

        $job_data = true;
        $requested_attributes = self::read_request_attribute('requested-attributes');		 
		 
		if ($job_data) {
		 
		    $job_id = $job ? $job : self::_get_last_job_id();	
		    //..check state...
			$job_attr = self::_read_job(null, $job_id);
			$job_state = $job_attr['job-state'] ? $job_attr['job-state'] : 'pending';
			$job_name = $job_attr['job-name'];
		 
		    if ($requested_attributes == 'all') {
	            $this->setAttribute('job-id',$job_id);		
	            $this->setAttribute('job-uri',"ipp://$this->ip/jobs/?job=".$job_id . (self::is_named_printer() ? '&printer='.self::is_named_printer() : ''));			
	            $this->setAttribute('job-state',$job_state);//'processing');
			    $this->setAttribute('job-state-reasons','none');
			    $this->setAttribute('job-name',$job_name);
				
			   
			    self::_read_job_attributes($job_id);
			}
            else {//is_array($requested_attributes)..read vals and set
			
	            $this->setAttribute('job-id',$job_id);		
	            $this->setAttribute('job-uri',"ipp://$this->ip/jobs/?job=".$job_id . (self::is_named_printer() ? '&printer='.self::is_named_printer() : ''));				
			
			    //test values
			    $job_state = 'completed';
				$job_state_reasons = 'none';
				
				//..is requested_attributes an array ...
			    if (is_array($requested_attributes)) {
				    foreach ($requested_attributes as $attr) {
					
					    $attr_value = str_replace('-','_',$attr);
						
						if ($value = ${$attr_value})
					        $this->setAttribute($attr,$value); 
						else
						    //$this->setAttribute($attr,'none'); //unknown attr values ????
							$this->setAttribute($attr,self::_read_job_attributes($job_id, $attr)); //get attr value from job
					}	
				}
				else {	
                    $this->setAttribute('job-state',$job_state);//'processing');
			        $this->setAttribute('job-state-reasons','none');
			        $this->setAttribute('job-name',$job_name); 				
                } 
                self::_read_job_attributes($job_id); 			   
			}
			 
	        $this->setAttribute('status-message','successful-ok');
	
		    $status = $this->setStatusCode('successful-ok');		 
		}
        else {
		    //client failure
		    $this->setAttribute('status-message','server-error-device-error');
		   
            $status = $this->setStatusCode('server-error-device-error');			 
        }
 		 
		return ($status);	
	
	}	
	
	protected function set_job_attributes() {//.....
	     $status = null;
		 $job = $_GET['job'];

         $job_data = true;
		 
		 
		 if ($job_data) {
			 
			 self::_write_job_attributes();
			 
	         $this->setAttribute('status-message','successful-ok');
	
		     $status = $this->setStatusCode('successful-ok');		 
		 }
         else {
		   //client failure
		   $this->setAttribute('status-message','client-error-bad-request');
		   
           $status = $this->setStatusCode('client-error-bad-request');			 
         }
 		 
		 return ($status);	
	
	}	
	
	
	//////////////////////////////////////////////////////////// lib
	
	private function _set_printer_state($state=null) {
	  $state = $state ? $state : 'idle';
	  $jobs_exists = false;
	  $current_state = trim(file_get_contents($this->printer_name.'.state'));  
		
	  if ( ($jobs = self::_read_jobs($this->username)) && (is_array($jobs)) ) {
	    //$js = implode('\r\n',$jobs);
        //self::write2disk('read_jobs.log',"\r\n".$js);		
	    $jobs_exists = true;
	  }		
	  
	  switch ($state) {
	    
		case 'processing' : 
		                    switch ($current_state) {
							  	case 'processing' :
		                        case 'stopped'    :	
								case 'idle'       :
								                    if ($jobs_exists) {
		                                              $this->printer_state = $state;		
													}
                                                    else {
		                                              $this->printer_state = 'idle';													
                                                    }													
													break;
		                        													
													
							}
		                    break;
					  
		case 'stopped'    : $this->printer_state = $state;					       
		                    break;

		case 'idle'       : 
		default           :
 		                    switch ($current_state) {
		                        case 'stopped'    :								
								case 'idle'       :
								                    if ($jobs_exists) {
		                                              $this->printer_state = 'processing';		
													}
                                                    else {
		                                              $this->printer_state = $state;													
                                                    }													
													break;
		                        													
													
							}
		                    break;
					  
	  }
	  
      //$this->setAttribute('printer-state',$this->printer_state);
	  
	  //other,none,media-needed,media-jam,paused,shutdown,connecting-to-device
	  //timed-out,stopping,stopped-partly,toner-low,toner-empty,spool-area-fool
	  //cover-open,interlock-open,door-open,input-tray-missing,media-low,media-empty
	  //output-tray-missing,output-area-almost-full,output-area-full,marker-supply-low
	  //marker-supply-empty,marker-waste-almost-full,marker-waste-full,fuser-over-temp,
	  //fuser-under-temp,opc-near-eol,opc-life-over,developer-low,developer-empty,interpreter-resource-unavailable
	  //$this->setAttribute('printer-state-reasons','none'); 
	  
	  //$this->setAttribute('printer-state-message','test');

      //self::write2disk($this->printer_name.'.state',$this->printer_state);	  
	  $ret = @file_put_contents($this->printer_name.'.state',$this->printer_state, LOCK_EX);
	  
	  return ($state);
	
	}
	
	private function _get_printer_state() {	

      $state = trim(@file_get_contents($this->printer_name.'.state'));	  
	  $this->state = $state;
	  
	  return ($state);
	}
	
	//print uri ....http request
	private function _print_uri($uri=null) {	
	
	    $uri = $uri ? $uri : self::read_request_attribute('document-uri');
		$last_document = self::read_request_attribute('last-document');
		
		if ($last_document=='true') {
		  //close create-job
		  //write session param last-document = true
		  //$_SESSION['last-document'] = 1;
		  return false;
		}
	
	    if (!$uri) // or last-document session param = true...
		  return false;
	
        // Create a stream
        $opts = array(
                     'http'=>array(
                     'method'=>"GET",
					 'timeout' => 2, //http timeout
                     'header'=>"Accept-language: en\r\n" .  
					           "Cookie: foo=bar\r\n"
                     )
               );

        $context = stream_context_create($opts);

        // Open the file using the HTTP headers set above
        $job_data = file_get_contents($uri, false, $context);
		
		//$job_data = 'xa';
		
		return ($job_data);
	}	

	//send uri ......alias
	private function _send_uri($uri=null) {	
	  
	    $job_data = self::_print_uri($uri);
		
		return ($job_data);
	}
	
	private function _send_document() {
	
        $job_data = self::ipp_print_body($this->clientoutput->body, 'temp.tmp');		 	
		$last_document = self::read_request_attribute('last-document');
		
		if ($last_document=='true') {
		  //close create-job
		  //write session param last-document = true
		  //$_SESSION['last-document'] = 1;
		  return false;
		}	
		
	    if (!$job_data) // or last-document session param = true...
		  return false;	

		return ($job_data);		  
	}
	
	//print job
	private function _print_job(&$job_data, $job_id=null) {
	
        $user = $this->username ? $this->username : self::read_request_attribute('requesting-user-name');
        $username = $user ? $user : 'anonymous';
		//$job_owner = self::read_request_attribute('job-originating-user-name');//..must be set by printer object ?		
		
		if ($job_id) {
		    //send-document, send-uri : job_id indicates previous create-job command job_id generated

		    if ($jobname = self::read_request_attribute('document-name')) { 
		      $jobname = str_replace(':','_',str_replace(FILE_DELIMITER,'_',$jobname));  
			  $jobname.= self::find_data_type_extension(substr($job_data,0,10));
			}  
            else 		
		      $jobname = 'noname.txt';		  
		}
		else {
		
		    $job_id = self::_get_job_id();			
		   
		    if ($jobname = self::read_request_attribute('job-name')) { 
		      $jobname = str_replace(':','_',str_replace(FILE_DELIMITER,'_',$jobname)); 
			  $jobname.= self::find_data_type_extension(substr($job_data,0,10));
			}  
            else 		
		      $jobname = 'noname.txt';
		}

		$jobname .= (self::read_request_attribute('compression') == 'gzip') ? '.gz' : null;			
		 
		$jobtitle = 'job'.FILE_DELIMITER.
		            $job_id.FILE_DELIMITER.
		            str_replace(':','~',$_SERVER['REMOTE_ADDR']).FILE_DELIMITER.
					$username.FILE_DELIMITER.
					$jobname;

		$job_name = $this->jobs_path . $jobtitle;			
		
		if (is_readable($this->jobs_path . 'temp.tmp')) { 
		
		  if (USE_DATABASE==true) {
		    $fw = fopen('db://stereobi_printer:retnirp@localhost/stereobi_printer', 'w');	
		    fwrite($fw, @file_get_contents($this->jobs_path . 'temp.tmp'));
		  }	
			
		  $ret = @rename($this->jobs_path . 'temp.tmp', $job_name); 
		}  
		else {
		
		  if (USE_DATABASE==true) {
		    $fw = fopen('db://stereobi_printer:retnirp@localhost/stereobi_printer', 'w');	
		    fwrite($fw, $job_data);
		  }	
			
		  $ret = @file_put_contents($job_name, $job_data, LOCK_EX);
		}  
		  	
		
        return ($ret?$job_id:false);		 
	}
	
	//validate job
	private function _validate_job($job_id=null) {	
	
	    $job_id = $job_id ? $job_id : self::_get_last_job_id(); 
		
		$user = $this->username ? $this->username : self::read_request_attribute('requesting-user-name');
		
		//if file exists....
		return true;
	}
	
	//create job
	private function _create_job(&$job_data) {	
		
		$job_id = self::_get_job_id();	
        $user = $this->username ? $this->username : self::read_request_attribute('requesting-user-name');
        $username = $user ? $user : 'anonymous';
        //$job_owner = self::read_request_attribute('job-originating-user-name');//..must be set by printer object ?		
		
		$jobname = 'create.job';
		 
		$jobtitle = 'job'.FILE_DELIMITER.
		            $job_id.FILE_DELIMITER.
		            str_replace(':','~',$_SERVER['REMOTE_ADDR']).FILE_DELIMITER.
					$username.FILE_DELIMITER.
					$jobname;

		$job_name = $this->jobs_path . $jobtitle;	
		
		if (is_readable($this->jobs_path . 'temp.tmp')) { 
		  $ret = @rename($this->jobs_path . 'temp.tmp', $job_name); 
		}  
		else
		  $ret = @file_put_contents($job_name, $job_data, LOCK_EX);
		  
		//write session param last-document = false  
		//$_SESSION['last-document'] = 0;
		
        return ($ret?$job_id:false);		 
	}	
	
	//generate next job's id
	private function _get_job_id() {
	
        $mydir = dir($this->jobs_path);
		
		$i=0;		
        while ($fileread = $mydir->read ()) { 
	
           if (substr($fileread,0,4)=='job'.FILE_DELIMITER) {
              $i+=1;
			  
			  //get job number...same job id (create-job with multiple docs)
			  $pf = explode(FILE_DELIMITER,$fileread);
			  $jid[] = intval($pf[1]);
           }
        }
        $mydir->close ();

        //return ($i+1);	
		
		//print_r($jid); 
		if (!empty($jid))
		  return (max($jid)+1);	
		else
          return(1); //first job		
		  
	}
	
	//fetch last job's id
	private function _get_last_job_id() {
	   
	   $ret = self::_get_job_id();
	   
	   return ($ret-1);
	}	
	
	//fetch last job's id
	private function _count_jobs() {
	   
	   $ret = self::_get_job_id();
	   
	   return ($ret-1);
	}	

	//purge job
	private function _purge_job($filename=null, $jid=null) {
	
	    if ($filename)
	        $fp = explode(FILE_DELIMITER,$filename);
	    //else if $jid...

		if ($fp[0]=='job') { 
		
		    $job_id = $fp[1];
		   
       	    if (self::_modify_job_attributes($job_id, 'job-status=purge;', true))
                return true; 		   
		}
		
		return false;
	}
	
	//read job
	private function _read_job($filename=null, $jid=null) {
	   
	   if ($filename) {
	     $fp = explode(FILE_DELIMITER,$filename);
	   }	 
	   elseif ($jid) {
         //search by id
		 if ($filename = self::_get_job_filename($jid)) 
		   $fp = explode(FILE_DELIMITER,$filename);
		 else
           return false;		 
	   }
	   else
	     return false;
	   
	   $user = $this->username ? $this->username : self::read_request_attribute('requesting-user-name');
	   
	   if ($fp[0]=='job') {
	     $ret['job-id'] = $fp[1];
	     $ret['remote-ip'] = str_replace('~',':',$fp[2]);
	     $ret['user-name'] = $fp[3];
		 $ret['job-name'] = $fp[4];
		 
		 if ($status = $fp[6])
		   $ret['job-status'] = $status;
		 
		 //..........print agent work
		 //$ret['data'] = @file_get_contents($this->jobs_path . $filename);
		 
		 return ($ret);
	   }
	   
	   return false;
	}
	
	//read jobs directory
	private function _read_jobs($user_jobs=false, $which_jobs=null) {
	   
	    if (!is_dir($this->jobs_path))
		  return false;
	
        $mydir = dir($this->jobs_path);	

        $which_jobs = $which_jobs ? $which_jobs : self::read_request_attribute('which-jobs');
		
		$limit = self::read_request_attribute('limit');
		$my_jobs = $user_jobs ? $user_jobs : self::read_request_attribute('my-jobs');
		$user = $this->username ? $this->username : self::read_request_attribute('requesting-user-name');
		
		$i=0;		
        while ($fileread = $mydir->read ()) { 
		    if (substr($fileread,0,4)=='job'.FILE_DELIMITER) {
			
                $i+=1;
                $pf = explode(FILE_DELIMITER,$fileread);
                $jid = $pf[1];//sort
				$job_owner = $pf[3];
											 
			    //switch depending on request attr
			    switch ($which_jobs) {
				
				    case 'completed'     : if (($my_jobs) && ($user) && ($job_owner!=$user))
					                         break;
                                           elseif (stristr($fileread,FILE_DELIMITER.'completed')) {
				                             $jobs[intval($jid)] = $fileread;		
                                           } 					
					                       break;
										   
				    case 'processing'    : if (($my_jobs) && ($user) && ($job_owner!=$user))
					                         break;
                                           elseif (stristr($fileread,FILE_DELIMITER.'processing')) {
				                             $jobs[intval($jid)] = $fileread;		
                                           } 					
					                       break;		

				    case 'pending'       : if (($my_jobs) && ($user) && ($job_owner!=$user))
					                         break;
                                           elseif (stristr($fileread,FILE_DELIMITER.'pending')) {
				                             $jobs[intval($jid)] = $fileread;		
                                           } 					
					                       break;										   
										   
				    case 'not-completed' : if (($my_jobs) && ($user) && ($job_owner!=$user))
				                             break;
                                           elseif (stristr($fileread,FILE_DELIMITER.'completed')==false) { 
				                             $jobs[intval($jid)] = $fileread;
				                           } 	
										   
				    //case 'not-completed' :
                    case 'all'           : //my definition ?				    
				    default              : if (($my_jobs) && ($user) && ($job_owner!=$user))
					                         break;
                                           //else { //all
										   elseif ((stristr($fileread,FILE_DELIMITER.'completed')==false) &&
           										   (stristr($fileread,FILE_DELIMITER.'deleted')==false) &&
												   (stristr($fileread,FILE_DELIMITER.'canceled')==false)
												   ){ //only pending & processing
				                             $jobs[intval($jid)] = $fileread;
				                           } 
				}						   
			}
            
            if (($limit) && ($limit>$i)) 			
			  break;
	    }
		
        $mydir->close ();	
			 
		if (is_array($jobs)) {
		
		  switch ($which_jobs) {
		    case 'completed'     : krsort($jobs); //reverese order
			                       break;	 
		    case 'not-completed' :				    
		    default              :	
		                           ksort($jobs);
          }
		  
		  return ($jobs);
		}
		
		return false;
    }

	//save job attributes to file 
    private function _write_job_attributes($jid=null, $reset_attributes=false) {
	    
		$job_template_attributes = array('job-priority', 'job-hold-until', 'job-sheets', 'job-billing', 'scaling',
		                                 'multiple-document-handling', 'copies', 'finishings', 
										 'finishings-suppoted', 'finishings-default',
										 'sides', 'page-ranges', 'number-up', 'orientation-requested',
										 'orientation-requested-supported', 'orientation-requested-default', 
										 'media', 'printer-resolution', 'print-quality', 'cpi', 'lpi');
		//$job_tags = array('job-id', 'job-name');	
        //$job_template_attributes = array_keys($this->job_tags);		
		//$job_template_attributes = array_merge($job_template_attributes, $job_tags);								 
		$data = null;
		
		$job_id = $jid ? $jid : self::_get_last_job_id();
		$jfile = $this->jobs_path . 'job~'.$job_id.'.ini';
		
		if (is_object($this->client_job_attributes->job_0)) { //job_0 = 1st array element...
		
		   foreach ($job_template_attributes as $j=>$param) {
		   
		        //$jp = str_replace('-','_',$param);
		        //if ($p = $this->client_job_attributes->job_0->$jp->_value0) {
				if ($p = self::read_request_job_attribute($param,0)) {
				  $data .= $param.'='.$p.';';
				}  
		   }
		  
		   if ($reset_attributes) {
		     //self::write2disk('ippserver.log',"\r\nNEW JOB FILE:$jfile\r\n");
		     if (($data) && (@file_put_contents($jfile, $data, LOCK_EX)!==false))
		       return true;		   
		   }
		   else {//append data if any...
		     //self::write2disk('ippserver.log',"\r\nAPPEND JOB FILE:$jfile\r\n");
		     if (($data) && (@file_put_contents($jfile, $data, FILE_APPEND | LOCK_EX)!==false))
		        return true;	 
		   }	 
		}
		else {//just create empty file...
		  //self::write2disk('ippserver.log',"\r\nCREATE JOB FILE:$jfile\r\n"); 
		  @file_put_contents($jfile, $data, FILE_APPEND | LOCK_EX); 
		}  
		
		return false;
    }	
	
	//read job attributes from file
    private function _read_job_attributes($jid=null, $return_attribute=null) {
	
		$job_template_attributes = array('job-priority', 'job-hold-until', 'job-sheets', 'job-billing', 'scaling', 
		                                 'multiple-document-handling', 'copies', 'finishings', 
										 'finishings-suppoted', 'finishings-default',
										 'sides', 'page-ranges', 'number-up', 'orientation-requested',
										 'orientation-requested-supported', 'orientation-requested-default', 
										 'media', 'printer-resolution', 'print-quality', 'cpi', 'lpi');
		//$job_tags = array('job-id', 'job-name');	
        //$job_template_attributes = array_keys($this->job_tags);		
		//$job_template_attributes = array_merge($job_template_attributes, $job_tags);
		
	    $job_id = $jid ? $jid : self::_get_last_job_id();
	    $jfile = $this->jobs_path . 'job~'.$job_id.'.ini';
		   
		if ((is_readable($jfile)) && ($attributes = @parse_ini_file($jfile))) {
		
		    if ($return_attribute) {//return value...
			   
			   if ($value = $attributes[$return_attribute])
			      return ($value);
			}
			else {//set attributes...
	           //foreach ($attributes as $param=>$value) {
               //only in $job_template_attributes.... 			   
			   foreach ($job_template_attributes as $j=>$param) {
			   
			       if ($value = $attributes[$param])
		              $this->setAttribute($param, $value);
			   }
               return true; 			
			}
		}
		
		return false;
    }	

	//modify job attributes
    private function _modify_job_attributes($jid=null, $reason=null, $append=false) {
	    $job_id = $jid ? $jid : self::_get_last_job_id();
	    $jfile = $this->jobs_path . 'job~'.$job_id.'.ini';
		$reason = $reason ? $reason : null;
		
		if (!$reason)
		    return false;
		   
		if (is_readable($jfile)) {
		
	        //@unlink($jfile);
            //return true; 	
            
			if ($append)
			  $ret = @file_put_contents($jfile, $reason, FILE_APPEND | LOCK_EX);	
			else
              $ret = @file_put_contents($jfile, $reason, LOCK_EX);
            return true;			
		}
		//else //create a new
		    //$ret = @file_put_contents('job~'.$job_id.'.ini', $reason);//, FILE_APPEND | LOCK_EX)
		
		//return ($ret);
		return false;
    }
	
    private function _get_job_filename($job_id=null) {
	
		$job_id = $job_id ? $job_id : self::_get_last_job_id();	
			
		$mydir = dir($this->jobs_path);

        $i=0;		
        while ($fileread = $mydir->read ()) { 
		    if (substr($fileread,0,4)=='job'.FILE_DELIMITER) {
			    $job_attr = self::_read_job($fileread);
			   
			    if ($job_attr['job-id']==$job_id)
			        return ($fileread);   
            }
        }
        
        return false;		
    }	
	
	//check if it is main (index.php) printer
	protected function get_printer_name() {
	   
	    if (($this->printer_name!='index.php') && ($this->printer_name!='AUTO'))
		    return ($this->printer_name);
		
        return false;		
	}
	
	//alias
	protected function is_named_printer() {
	
	  return (self::get_printer_name());  
	}	

	//periodically calls... by get-printer-attributes responses to installed printers
    private function _fire_up_agent($callback_function=null, $callback_param=null, $log=null) {
	
	    if (self::_get_printer_state()=='stopped') {
		    self::write2disk($this->printer_name.'.log', '\r\nPrinter stopped!');
		    return false; //not working ....move to print-jobs...
		}

		//network.log 
        if ($log)
		    self::write2disk('network.log',":agent:");		
		
		if ((class_exists('AgentIPP', true)) && ($this->username)) {//ONLY IF USERNAME..GET JOBS PER USER
		
			self::write2disk($this->printer_name.'.log', "\r\nPrint agent initialized!\r\n");	
            //if ($log)
		      //self::write2disk('network.log',":start:");			
			
		    $srv = new AgentIPP($this->authentication,
			                    self::get_printer_name(),
			                    $this->username,//??? when called what is the name ??
			                    $callback_function,//must be inside pragent class
							    $callback_param,
							    $log);			   
		    $ret = true;
            //if ($log)
		      //self::write2disk('network.log',":end:");				
		} 
        else {						
		    self::write2disk($this->printer_name.'.log', "\r\nPrint agent failed to initialized!\r\n");
			
            $ret = false;  			
		}  	
		
		return $ret;
    }
	
	//empty log files
	protected function _flush_log_files($message=null) {
	    $ret = true;
		
		//if ($message) //no need ..file is deleted after ...
		  //self::write2disk('network.log',":$message:");
	    
		@unlink('login.log');
	    @unlink('printer.log');
		@unlink('network.log');
	    @unlink('ippserver.log');
		@unlink('tagval.log');
		//@unlink('status-codes.log');
		//@unlink('read_request_attr.log');
		//@unlink('client-headers.log');
		@unlink('collection.log');
		//agent logs..moved to agentIPP
		//@unlink('pragent.log');
		@unlink($this->printer_name . '.log');
		
	    
		if (class_exists('AgentIPP', true)) {
		    $auth = 'dummyref';
		    $srv = new AgentIPP($auth,self::get_printer_name());
			$ret = $srv->flush_log_files();
			
		}	
		
		return $ret?$ret:false;
	}
	
	protected function authenticate_user() {
	  
	    if (!defined('AUTH_USER'))
	      return true;
		
        if ($this->authentication_mechanism) {

		    if ($this->authentication_mechanism=='NONE') {
			    $this->username = 'anonymous';
			    return true;
			}	
			
		    $this->authentication = new AuthIPP($this->authentication_mechanism, $this->allowed_users, 'crc32');

			$this->username = $this->authentication->ipp_auth();
		    //$auth_user = $_SERVER["PHP_AUTH_USER"].':'.$_SERVER['PHP_AUTH_PW'];
		    //self::write2disk('auth-headers.log',"\r\n username>".$this->username."\r\n SERVER_AUTH_USER>".$auth_user."\r\n");		
		
		}		
		
		if ($this->username) { 
		
		    self::write2disk('login.log',"\r\n".$this->server_time.":".$this->printer_name.":".
		                               $_SERVER['REMOTE_ADDR'].":".$this->username);

            return true;									   
		}							 
			

	    return false;
	}	

	//identify file extension from data...
	protected function find_data_type_extension($data=null, $restiction=false) {
	    //echo '>',$data;
		
		$filename = $this->jobs_path . 'temp.tmp';
		  
		if (is_readable($filename)) { 
		    $handle = fopen($filename, "rb");
            $data = fread($handle, 10);//filesize($filename));
            fclose($handle);
		}
        //self::write2disk('content.log',"\r\n$data:".$ext.$filename."\r\n");		
		  
		if (substr($data,0,4)=='%!PS') {//postscript
		  
		  $ext = '.ps';
		
		  return $ext;
		}  
		
        return null;//'.ppp';		
	}	
	
	protected function get_user_quota($user=null,$printer=null) {
	    $pname = $printer ? $printer : $this->printer_name;
		$puser = $user ? FILE_DELIMITER.$user : FILE_DELIMITER.$this->username;
		$qfile = $pname.$puser.'.quota';
	
        if ($quota = @file_get_contents($qfile)) {
		    return (intval($quota)); 
        }
        
        return false; 		
	}
	
	protected function set_user_quota($quota=null,$user=null,$printer=null) {
	    $pname = $printer ? $printer : $this->printer_name;
		$puser = $user ? FILE_DELIMITER.$user : FILE_DELIMITER.$this->username;
		$qfile = $pname.$puser.'.quota';
		$quota = $quota ? $quota : 1;
		$ret = false;
		
		$file = $this->printers_path . $qfile;
	
        if ($prev_quota = @file_get_contents($file)) {
		
		    $new_quota = intval($prev_quota) + intval($quota);
		    $ret = @file_put_contents($file, $new_quota , LOCK_EX);
		
		    return ($new_quota); 
        }
		else
		   $ret = @file_put_contents($file, $quota, LOCK_EX);
        
        return ($ret); 		
	}	
	
    protected function get_printer_admin() {
	
	    if (is_object($this->authentication)) {
	   
	      $this->user_admin = $this->authentication->get_user_admin();
	    }
	    else
	      $this->user_admin = 'admin';
		  
	   	return ($this->user_admin);  
    }

};
?>
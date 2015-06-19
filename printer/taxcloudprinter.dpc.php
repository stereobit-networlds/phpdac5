<?php
$__DPCSEC['TAXCLOUDPRINTER_DPC']='1;1;1;1;1;1;1;1;1';

if (!defined("TAXCLOUDPRINTER_DPC")) {
define("TAXCLOUDPRINTER_DPC",true);

$__DPC['TAXCLOUDPRINTER_DPC'] = 'taxcloudprinter';

$a = GetGlobal('controller')->require_dpc('printer/UiIPPtaxcloud.lib.php');
require_once($a);


$__EVENTS['TAXCLOUDPRINTER_DPC'][0]='taxprinter';
$__EVENTS['TAXCLOUDPRINTER_DPC'][1]='taxshow';
$__EVENTS['TAXCLOUDPRINTER_DPC'][2]='taxxml';
$__EVENTS['TAXCLOUDPRINTER_DPC'][3]='taxjobstats';
$__EVENTS['TAXCLOUDPRINTER_DPC'][4]='taxnetact';
$__EVENTS['TAXCLOUDPRINTER_DPC'][5]='taxlogout';
$__EVENTS['TAXCLOUDPRINTER_DPC'][6]='taxjobs';
$__EVENTS['TAXCLOUDPRINTER_DPC'][7]='taxjobstats';
$__EVENTS['TAXCLOUDPRINTER_DPC'][8]='taxdeljobs';
$__EVENTS['TAXCLOUDPRINTER_DPC'][9]='taxaddprinter';
$__EVENTS['TAXCLOUDPRINTER_DPC'][10]='taxmodprinter';
$__EVENTS['TAXCLOUDPRINTER_DPC'][11]='taxremprinter';
$__EVENTS['TAXCLOUDPRINTER_DPC'][12]='taxinfprinter';
$__EVENTS['TAXCLOUDPRINTER_DPC'][13]='taxlogin';
$__EVENTS['TAXCLOUDPRINTER_DPC'][14]='taxuseprinter';
$__EVENTS['TAXCLOUDPRINTER_DPC'][15]='taxconfprinter';
$__EVENTS['TAXCLOUDPRINTER_DPC'][16]='taxproceed';
$__EVENTS['TAXCLOUDPRINTER_DPC'][17]='taxuploadjob';
$__EVENTS['TAXCLOUDPRINTER_DPC'][17]='taxdelete';
$__EVENTS['TAXCLOUDPRINTER_DPC'][19]='taxregister';
$__EVENTS['TAXCLOUDPRINTER_DPC'][20]='taxpick';
$__EVENTS['TAXCLOUDPRINTER_DPC'][21]='taxdhfass';
$__EVENTS['TAXCLOUDPRINTER_DPC'][22]='taxdsym';
$__EVENTS['TAXCLOUDPRINTER_DPC'][23]='taxdapfmhs';
$__EVENTS['TAXCLOUDPRINTER_DPC'][24]='taxfiscal';
$__EVENTS['TAXCLOUDPRINTER_DPC'][25]='taxmonitor';
$__EVENTS['TAXCLOUDPRINTER_DPC'][26]='taxpay';
$__EVENTS['TAXCLOUDPRINTER_DPC'][27]='taxqrpay';
$__EVENTS['TAXCLOUDPRINTER_DPC'][28]='taxsearch';
$__EVENTS['TAXCLOUDPRINTER_DPC'][29]='taxqrsearch';

$__ACTIONS['TAXCLOUDPRINTER_DPC'][0]='taxprinter';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][1]='taxshow';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][2]='taxxml';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][3]='taxjobstats';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][4]='taxnetact';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][5]='taxlogout';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][6]='taxjobs';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][7]='taxjobstats';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][8]='taxdeljob';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][9]='taxaddprinter';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][10]='taxmodprinter';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][11]='taxremprinter';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][12]='taxinfprinter';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][13]='taxlogin';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][14]='taxuseprinter';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][15]='taxconfprinter';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][16]='taxproceed';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][17]='taxuploadjob';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][18]='taxdelete';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][19]='taxregister';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][20]='taxpick';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][21]='taxdhfass';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][22]='taxdsym';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][23]='taxdapfmhs';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][24]='taxfiscal';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][25]='taxmonitor';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][26]='taxpay';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][27]='taxqrpay';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][28]='taxsearch';
$__ACTIONS['TAXCLOUDPRINTER_DPC'][29]='taxqrsearch';


$__DPCATTR['TAXCLOUDPRINTER_DPC']['taxprinter'] = 'taxprinter,1,0,0,0,0,0,0,0,0,0,0,1';

$__LOCALE['TAXCLOUDPRINTER_DPC'][0]='TAXCLOUDPRINTER_DPC;Printer;Εκτυπωτής';
$__LOCALE['TAXCLOUDPRINTER_DPC'][1]='_SHLOGOUT;Logout;Αποσύνδεση';


class taxcloudprinter extends UiIPPtaxcloud {
	
	var $myprinter, $defdir, $message, $procmd;
	var $url_activate, $url_invitate, $url_register;
	var $test_page;
	
	var $afm_prefix, $doy_prefix;
	var $notify_mail, $fiscal_on, $fiscal_record_limit, $fiscal_log_show, $tax_user_email;
	var $signer_service, $fiscal_service, $cprint_service;
	
	var $config_file, $login;
	
	var $tabs, $tab_id;
	
	function __construct() {   
	
	   spl_autoload_register(array($this, 'loader')); //call dropbox api..process_job
	   
	   $this->myprinter = null;
	   $this->message = null;
	   $this->defdir = $_SESSION['indir'] ? $_SESSION['indir'] : '/';//null//'printers'; 

	   $this->procmd = 'tax'; 
	   
	   //overwrite
	   $this->printer_name = $_SESSION['printer'] ? $_SESSION['printer'] : 'taxcloud.printer';
       //$this->printer_name = 'taxcloud.printer'; //<<<<<<<<<<<<<<<<<<<<<<<<<<<< do not select printer	   
							    
	   parent::__construct($this->printer_name,null,null,true,$this->procmd);
	   
	   //when a user has come from a url request for activation or invite a new user
	   $this->url_activate = $_SESSION['ACTIVATION'] ? $_SESSION['ACTIVATION'] : false;	   
	   $this->url_invitate = $_SESSION['INVITATION'] ? $_SESSION['INVITATION'] : false;	

	   $this->test_page = $_SESSION['TESTPAGE'] ? $_SESSION['TESTPAGE'] : false;  
	   $this->message = null;
	   
	   $this->url_register = $_SESSION['REGISTRATION'] ? $_SESSION['REGISTRATION'] : (GetReq('regid') ? true : false);
	    
	   $this->afm_prefix = iconv("ISO-8859-7", "UTF-8", "ΑΦΜ:");
	   $this->doy_prefix = iconv("ISO-8859-7", "UTF-8", "ΔΟΥ:");	
	   
	   $this->fiscal_on = false;//true when fiscal event
	   $this->fiscal_log_show = true; //every time data submited show win log	   
	   $this->fiscal_record_limit = null; //init.. for other actions no limit
	   
       $this->notify_mail = 'info@smart-printers.net';
       $this->tax_user_email = $_SESSION['itaxusermail'] ? $_SESSION['itaxusermail'] : null; //hold config user mail

	   $this->login = false; //init
	   //conf file
	   /*if ($this->username!=$this->get_printer_admin()) 
		   $this->config_file = $this->admin_path . 'taxcalc-'.$this->username.'-conf'.'.php';
	   else
           $this->config_file = $this->admin_path . 'taxcalc-conf'.'.php';
	   //read conf	   
       if (is_readable($this->config_file))
           include($this->config_file);	   
       */
       $this->signer_service = false;//default $itaxsigner ? true : false; //enable eafdss
	   $this->fiscal_service = false;//default $itaxsigner ? ($itaxfiscal ? true : false) : false; //enable taxfiscal pos only if eafdss is active
       $this->cprint_service = false;//default $itaxcprint ? true : false; //enable consumer services		   

	   //timezone	   
       date_default_timezone_set('Europe/Athens'); //default ..setup_user ..... 
        
       $this->tabs = true;
       $this->tab_id = 2;//tab id to start..	   
	}
	
    function loader($class){
	   $class = str_replace('\\', '/', $class);
	   require_once($class . '.php');
    } 	

    function event($event=null) {
	
	    //ALWAYS... 
		$this->login = self::_login();
	
        switch($event)   {
		    case 'taxsearch'   :  break;
		    case 'taxqrsearch' :  break;			
		    case 'taxpay'      :  break;
			case 'taxqrpay'    :  break;
		    case 'taxmonitor'  :  break;
		    case 'taxfiscal'   :  //$this->login = self::_login();//ALWAYS WHEN FISCAL
			                      if (($this->login) && ($this->fiscal_service) && ($data = GetParam('receipt'))) {
			                         $this->fiscal_on = true; //when fiscal data exist=used
			                         $taxact = GetParam('taxact'); //action after submit
			                         $this->message = $this->add_job_data($data, $taxact); 
								  }	 
			                      break;
		    case 'taxdapfmhs'  :  break;
			case 'taxdsym'     :  break;
		    case 'taxdhfass'   :  break; 
			case 'taxpick'     :  break;  		
		    case 'taxregister' :  $this->registration_check(); 
			                      break;
			case 'taxlogout'   :  self::_logout();
			                      //$ret = self::_login();
			                      break;							
			//case 'taxlogin'    :  //ALWAYS... 
			//default            :  $this->login = self::_login();
			                     		
        }			
	}
	
    function action($action=null)  {

        /*if ($this->login != true)
            return ($this->login_form());	*/	
	
        switch($action)   {	
		    case 'taxqrsearch' :  //$ret = $this->pick_tax_file('pay');
			                      break; 		
		    case 'taxsearch'   :  $ret = $this->search_tax_file('pay',GetReq('qrc'));
			                      break; 		
		    case 'taxqrpay'    :  $ret = $this->pick_tax_file('pay');
			                      break; 		
		    case 'taxpay'      :  //$ret = $this->pick_tax_file('pay');
			                      break; 		
		    case 'taxdapfmhs'  :  $ret = $this->pick_tax_s();
			                      break;
			case 'taxdsym'     :  $ret = $this->pick_tax_x();
			                      break;
			
		    case 'taxdhfass'   :  $ret = $this->pick_tax_z();
			                      break; 		
		    case 'taxpick'     :  $ret = $this->pick_tax_file();
			                      break; 

            case 'taxregister': $ret = $this->registration_form(); break;		
			                   
                                   
			/*case 'taxlogout'  : self::_logout();
			                    $ret = self::_login();
			                    break;*/							
			//case 'taxlogin'   : if ($this->url_invitate) break; 
			
			case 'taxlogout'  : $ret = $this->login_form(); break;			
			
		    case 'taxshow'    : if ($this->url_invitate) break;
			case 'taxxml'     : if ($this->url_invitate) break;
			case 'taxjobstats': if ($this->url_invitate) break;
			case 'taxnetact'  : if ($this->url_invitate) break;
			case 'taxjobs'    : if ($this->url_invitate) break;
			case 'taxjobstats': if ($this->url_invitate) break;
			case 'taxdeljob'  : if ($this->url_invitate) break;	
			
		    case 'taxfiscal'  : //continue.... 
			                    //echo GetParam('receipt').':'.GetParam('taxact');
								$this->fiscal_record_limit = 10; //set limit for standart fiscal view
								$ret = $this->message; //if message..
			                    //continue	
			case 'taxmonitor' : //continue...					
			case 'taxprinter' : 			
			case 'taxlogin'   : //continue..			
			default           :	

								if ($this->login != true) {
								  return ($this->login_form());
                                }
								else {
								  if ($this->url_activate) {//$_GET['activation']) {//return from activation mail...
								    //return ($this->form_useprinter());
								    return ($this->form_configprinter());
								  }	
								  else	//taxuserprinter when login
								    $cmd = str_replace($this->procmd,'',$action);
								
                                  //if ($this->login != true) //moved from top of func..
									//return ($this->login_form());
								  //else
									$ret .= $this->printer_console($cmd);
								}
								/*else
								  $ret = $this->login_form();//$ret = $login;*/	
												
        }

        return ($ret);		
	}
	
	//....
	protected function read_conf_file() {
	
		if (is_readable($this->config_file)) { 
		
		    //make local params to this->
			//$file = 
		
            include($this->config_file);
		}	
        else
            die("ERROR:Invalid configuration");//!!!		
	}	
	
	//get bas stoixeia afm
	protected function afm_check($pAfm=null, $retarray=false) {
	    if (!$pAfm) return false;
		$ret = null;
	
	    // set trace = 1 for debugging
	    $client = new SoapClient("https://www1.gsis.gr/wsgsis/RgWsBasStoixN/RgWsBasStoixNSoapHttpPort?wsdl", array('trace' => 0));
	    // we set the location manually, since the one in the WSDL is wrong
	    $client->__setLocation('https://www1.gsis.gr/wsgsis/RgWsBasStoixN/RgWsBasStoixNSoapHttpPort');			
	
		$pBasStoixNRec_out = array('actLongDescr' => '',
								'postalZipCode' => '', 
								'facActivity' => 0,
								'registDate' => '2011-01-01',
								'stopDate' => '2011-01-01',
								'doyDescr' => '',
								'parDescription' => '',
								'deactivationFlag' => 1,
								'postalAddressNo' => '',
								'postalAddress' => '',
								'doy' => '',
								'firmPhone' => '',
								'onomasia' => '',
								'firmFax' => '',
								'afm' => '',
								'commerTitle' => '');
	
		$pCallSeqId_out = 0;
	
		$pErrorRec_out = array('errorDescr' => '', 'errorCode' => '');			
	
		try {
			$result = $client->rgWsBasStoixN($pAfm, $pBasStoixNRec_out, $pCallSeqId_out, $pErrorRec_out);
		
			$labels = array('actLongDescr' => 'Περιγραφή Κύριας Δραστηριότητας',
						'postalZipCode' => 'Ταχ. κωδικός Αλληλογραφίας',
						'facActivity' => 'Κύρια Δραστηριότητα',
						'registDate' => 'Ημ/νία Έναρξης',
						'stopDate' => 'Ημ/νία Διακοπής',
						'doyDescr' => 'Περιγραφή ΔΟΥ',
						'parDescription' => 'Περιοχή Αλληλογραφίας',
						'deactivationFlag' => 'Ένδειξη Απενεργ. ΑΦΜ',
						'postalAddressNo' => 'Αριθμός Αλληλογραφίας',
						'postalAddress' => 'Οδός Αλληλογραφίας',
						'doy' => 'Κωδικός ΔΟΥ',
						'firmPhone' => 'Τηλέφωνο Επιχείρησης',
						'onomasia' => 'Επωνυμία',
						'firmFax' => 'Fax Επιχείρησης',
						'afm' => 'ΑΦΜ',
						'commerTitle' => 'Τίτλος');
		
			if (!$result['pErrorRec_out']->errorCode) {
			
				foreach($result['pBasStoixNRec_out'] as $k=>$v) {
				    if ($retarray)
					   $ret[$k] = $v; 
					else
					   $ret .= $labels[$k]. ': '.$v.'<br />';	
				}	
			} 
			else {
				$ret = 'ERROR '.$result['pErrorRec_out']->errorCode.': '.$result['pErrorRec_out']->errorDescr;
			}
		
		} 
		catch(SoapFault $fault) {
		    // <xmp> tag displays xml output in html
		    $ret = 'Request: <br /><xmp>'. $client->__getLastRequest(). '</xmp><br /><br /> Error Message: <br />'. $fault->getMessage();
		}

        return ($ret);		
	}
	
	//register form
	protected function registration_check() {
	    $cmd = $this->procmd ? $this->procmd.'register': 'register';
	    $email = GetParam('email'); 
        $vatnumber = GetParam('vatnumber') ? GetParam('vatnumber') : null;
        $regid = GetReq('regid') ? GetReq('regid') : null;//regid by mail link		
		
		if (($regid = $_POST['register']) && ($answer = $_POST['issuedate'])) {//check.....
		    //services to enable...
		    $use_eafdss = GetParam('use_eafdss');
			$use_fhm = GetParam('use_fhm');
			$use_services = GetParam('use_services');
		    
			//echo 'zzzz';
			//print_r($_POST);
			
		    //STEP - 3, create printer				
			if ($tx = $this->create_tax_printer($regid, $use_eafdss, $use_fhm, $use_services)) {
			    //send mail 
                //echo $tx.'zzzz'; //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<test
			   
				$urlstring = rawurldecode(base64_decode($regid));
				//echo $urlstring;
				$args = explode('<|>',$urlstring);
				$afm = $args[0];
				//$doy = $args[1];
				$onomasia = $args[1];
				$mail = $args[2];	
				$printername = $args[3];			   
				
			    //new user procedure..generate pass...
				$txuser = $tx; //same as printer name
				$txpass = hash('crc32',$mail.$onomasia);//'1234567890');//'1234567890';
	
				$ok = $this->_add_user_mail($mail.':'.$afm,$txuser,$txpass,'system',$tx,$tx);
				
				//??????chage user ????? //NO NEED...
                //$this->newuser = $tx;
                //$_SESSION['new_user'] = $tx;					
				//$this->username = $txuser; //CHANGE USER..after mail relation
														
				if ($ok) { 
					$this->message = true;//goto register_form to proceed 
					//..thanks page
					//mail send stop procedure - logout
					//self::_logout(); //not logged in
				}	
				else
				   $this->message = 'Create tx printer: Send mail error!';	
             				   
			}
			else
			   $this->message = 'Unable to create tax printer!';
			   		
		}
		elseif ($regid = $_GET['regid']) {
			//STEP - 2
            //fetch user data	
            //echo $this->afm_check($vatnumber);
			
			$urlstring = rawurldecode(base64_decode($regid));
			//echo $urlstring;
			$args = explode('<|>',$urlstring);
			$afm = $args[0];
			$onomasia = $args[1];
			$mail = $args[2];	
			$printername = $args[3];
			
			//check returned arg with session afm in url_register..IF NO SESSION ?
			if ($afm==$this->url_register) {
			    $this->message = 'VAT NUMBER:' . $afm; //set message..not used
			
			    $_SESSION['REGISTRATION'] = $args; 
			    $this->url_register = $args; //enable reg..set data			
			}
			else {
			    $this->message = "ERROR:INVALID DATA. Please follow the registration procedure.";
				$this->url_register = null;//terminate procedure..form at step-1
				$_SESSION['REGISTRATION'] = null;
			}
		}
		elseif (($email) && ($vatnumber)) {
		    //STEP-1		
            $this->url_register = $vatnumber;//VAT check //true; //enable reg for instruction_page show		
			$_SESSION['REGISTRATION'] = $vatnumber;
		
            $cmp = $this->afm_check($vatnumber, true);
			//print_r($cmp);
			
            //if (!$cmp['deactivationFlag']) {
		        $printermon = "http://" . $_ENV["HTTP_HOST"] . 
		                       pathinfo($_SERVER['PHP_SELF'],PATHINFO_DIRNAME).
					           pathinfo($_SERVER['PHP_SELF'],PATHINFO_BASENAME);	
					  
		        $printermonitor = $printermon . '?printer='.$this->printer_name;				
			
                $keystring = $cmp['afm'].'<|>'.$cmp['onomasia'].'<|>'.$email.'<|>'.$this->printer_name;		   
			    $register_urlstring = rawurlencode(base64_encode($keystring));		
			    $register_link = $printermon."?t=$cmd&regid=".$register_urlstring.'&printer='.$this->printer_name;
						   
		
			    $message = $this->html_show_instruction_page('tx-send-mail',array('[PRINTERNAME]','[onomasia]','[commerTitle]','[afm]','[doyDescr]','[REGLINK]'),
		                                                                    array($this->printer_name, $cmp['onomasia'], $cmp['commerTitle'], $cmp['afm'], $cmp['doyDescr'], $register_link),
																            true);		  
		    /*}
			else
			    $message = $this->html_show_instruction_page('tx-send-mail-error',array('[PRINTERNAME]'), array($this->printer_name), true);			
			*/
			$from = $this->printer_name . '@' . str_replace('www.','',$_ENV["HTTP_HOST"]);//'balexiou@stereobit.com'
			$ok = $this->_sendmail($from,$email,$this->printer_name .' registration',$message);
			//notify
			$ok2 = $this->_sendmail($from,$this->notify_mail,$this->printer_name .' user registration',$mail . $message);		
	
		}
		else
            $this->message = 'Fill the required fields';		
	
	}
	
	//register form
	protected function registration_form() {
	    if ($this->username)
	      return ($this->html_window("Register", "Logged in, not allowed.", $this->printer_name));
	
	    $message = $this->message ? $this->message : null;
		$cmd = $this->procmd ? $this->procmd.'register': 'register';
		$email = GetParam('email');
		$vatnumber = GetParam('vatnumber') ? GetParam('vatnumber') : null;
        $regid = GetReq('regid') ? GetReq('regid') : null;//regid by mail link	

		if ($this->message===true) { //step-3
		    //end of procedure show login screen or thanks page
			//$ret = $this->login_form();
		    $ret = $this->html_show_instruction_page('tx-tax-post');
			return ($ret);
        }
        else //error... 		
	        $msg = $message ? '<h2>'.$message.'</h2>' : null;		
		

		if (($email) && ($vatnumber)) { //data posted step-1
		   //$register_form = "An email send tou you ";
		   $ret = $this->html_show_instruction_page('tx-user-post');
		   return ($ret);
		}
		elseif (($regid) && ($this->url_register)) { //step-2
		    $eafdss_check = GetParam('use_eafdss') ? "checked='checked'": null;
			
		    //print_r($this->url_register); //$this->url_register[0]
		    $register_form = '		  
<li id="li_5" >
<label class="description" for="element_5">Issue date</label>
<div>
<input id="element_5_1" name= "issuedate" class="element text medium" maxlength="20" value=""/>
<label>Issue date</label>
</div>
<p class="guidelines" id="guide_4"><small>Enter Issue date</small></p> 
</li>

<li id="li_1" >
<label class="description" for="element_1">Enable EAFDSS</label>
<span>
<input id="element_1" name="use_eafdss" class="element checkbox" type="checkbox" value="1" '.$eafdss_check.'/>
<label class="choice" for="element_1">EAFDSS</label>
</span><p class="guidelines" id="guide_1"><small>Use virtual EAFDSS implementation.</small></p> 
</li>

<li id="li_2" >
<label class="description" for="element_2">Enable FHM</label>
<span>
<input id="element_2" name="use_fhm" class="element checkbox" type="checkbox" value="1" '.$fhm_check.'/>
<label class="choice" for="element_2">FHM</label>
</span><p class="guidelines" id="guide_2"><small>Use virtual FHM implementation.</small></p> 
</li>

<li id="li_3" >
<label class="description" for="element_2">Enable taxcloud services</label>
<span>
<input id="element_3" name="use_services" class="element checkbox" type="checkbox" value="1" '.$services_check.'/>
<label class="choice" for="element_3">Taxcloud services</label>
</span><p class="guidelines" id="guide_3"><small>Use taxcloud services.</small></p> 
</li>

<input type="hidden" name="register" value="'.$regid.'" />
';		  
		}
		else //start, step-1
		    $register_form = '
<li id="li_1" >
<label class="description" for="element_1">E-mail</label>
<div>
<input id="element_1_1" name= "email" class="element text medium" maxlength="64" value=""/>
<label>e-mail</label>
</div>
<p class="guidelines" id="guide_1"><small>Enter your e-mail</small></p> 
</li>			  
<li id="li_5" >
<label class="description" for="element_5">Vat number</label>
<div>
<input id="element_5_1" name= "vatnumber" class="element text medium" maxlength="11" value=""/>
<label>Vat number</label>
</div>
<p class="guidelines" id="guide_4"><small>Enter your VAT number</small></p> 
</li>		  
';
	    $form .= <<<EOF
		<form id="form_register" class="appnitro"  method="post" action="">
		<div class="form_description">
			<p>Please enter registration data.</p>
			$msg
		</div>						
			<ul >
			
			    
		$register_form		
				
		<input type="hidden" name="form_id" value="470441" />
		<input type="hidden" name="FormAction" value="$cmd" />
		
		<li class="buttons">
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
EOF;
	
	    $ret  = $this->html_window("Register", $form, $this->printer_name, true);	
        return ($ret);	
	}

	//create printer instance
    protected function create_tax_printer($regid=null, $option_eafdss=null, $option_fhm=null, $option_srv=null) {
	    if (!$regid) return false;
		
		$urlstring = rawurldecode(base64_decode($regid));
		//echo $urlstring;
		$args = explode('<|>',$urlstring);
		$afm = $args[0];
		//$doy = $args[1];
		$onomasia = $args[1];
		$mail = $args[2];	
		$printername = $args[3];
		
        //?? doy can be modified !!!	
        $taxdir_old = strtoupper(hash('sha1',$afm . $onomasia));//$doy));
		$taxdir = 'TXC' . strtoupper(hash('crc32',$taxdir_old));//new dir name =itaxserial!!!
		
        $taxprinter_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $taxdir;		
	    if (is_dir($taxprinter_path)) {
		  return false; //exist
		}  
		else {  
	      $ok = @mkdir($taxprinter_path, 0755);  	
		  //$ok = @mkdir($taxprinter_path .'/admin', 0755); //no need admin data saved to root folder
		  //$ok = @mkdir($taxprinter_path .'/jobs', 0755); //no need jobs data saved to root folder	
		}  
		
        if ($ok) {
		    $pcode = "<?php
include ('../../printers/ippserver/ListenerIPP.php');
\$listener = new IPPListener('$taxdir.printer' ,'BASIC' ,100 ,array('admin'=>'8a013b85','demo'=>'27ff9f49',));
\$listener->ipp_send_reply(); 			
?>";			
		  
            //save printer file...		  
		    $ok = @file_put_contents($taxprinter_path .'/index.php', $pcode);		  
			
		    $ccode = "
[SERVICES]
taxcalc=1
[PARAMS]
services=all 			
";		

            //save conf file...		  
		    $ok = @file_put_contents($taxprinter_path ."/$taxdir.printer.conf", $ccode);

	    $htaccess = "
RewriteEngine On

RewriteRule ^(.*)\.prn$ $1.php [L] 
RewriteRule ^(.*)\.printer$ $1.php [L] 

RewriteRule .* - [E=DEVMD_AUTHORIZATION:%{HTTP:Authorization}]		
";	

            //save htaccess file
			if ($htp = fopen($taxprinter_path . "/.htaccess",w)) { 
                $ok = fputs($htp,$htaccess); 
				fclose($htp);			
			}	
			
            if ($ok)
                return ($taxdir);			
        }
		
        return false;	
    }

    /////////////////////////////////////////////////////////////////////////////////////////////


	
	
	//override
	protected function html_window($title=null, $data=null, $footer_title=null, $nomenu=false, $tab_id=false) {
	    $ver = $this->server_name . $this->server_version;	
		$footer_title = $footer_title ? $footer_title :	$ver.'&nbsp;|&nbsp;'.$this->logout_url;
		$header_title = $title ? '<div class="contr"><h2>'.$title.'</h2></div>' : null;
		
	    $menu = $nomenu ? null : $this->html_printer_menu(true);		
	
	    $form = <<<EOF
<!--link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script-->	
	
    <div class="container">
    $header_title
    <div class="upload_form_cont">	
	<div id="form_container">
	    $menu					
		$data
		<hr/>	
		<div id="footer">
        $footer_title
		</div>
	</div>
	<br/>
	</div>
	</div>
EOF;

        if (($this->tabs) && ($tabid = $this->tab_id++)) {
		   $ts = '<div class="tabpage" id="tabpage_'.$tabid.'">';
		   $te = '</div>';
		   return ($ts.$form.$te);
		}
		else
           return ($form); 	
	}		
	
	protected function html_show_instruction_page($page=null,$replace_args=null,$replace_vals=null,$html_page=false,$hasmenu=false, $hasfooter=false) {
	
	    $page_title = 'instruction page '.$page;
		$insfile = $this->admin_path . $page . '.' . $this->printer_name;
		//defaulr file into taxcloud.printer dir
		$insfile_default = str_replace($this->printer_name, 'taxcloud.printer', $this->admin_path) . $page . '.taxcloud.printer';//default insfile
		
		$printer_url =  "http://" . $_ENV["HTTP_HOST"] .
		                pathinfo($_SERVER['PHP_SELF'],PATHINFO_DIRNAME) .
		                $this->printer_name;
		$title = $this->printer_name;// $page;							
        $header = $title ? "<div class=\"contr\"><h2>$title</h2></div>" : null;							
						
		//ONLY IF INVITATION OR ACTIVATION OR NEW USER OR FISCAL USE (QUOTA INSTRUCTIONS)	
        //echo $page,'>';		
        if (($this->url_invitate) || ($this->url_activate) || ($this->newuser) || 
		    ($this->url_register) || ($this->fiscal_on)) {						
		
		if (!is_readable($insfile)) {
		    if (is_readable($insfile_default)) { 
			    $insfile = $insfile_default; //get default file
				//echo $insfile;
			}	
			else
		        return $page_title . ':' . $insfile;
		}	
		
		$data = @file_get_contents($insfile);
		//REPLACE ARGS IF ANY...
		if ((is_array($replace_args)) && (!empty($replace_args))) {
		    foreach ($replace_args as $a=>$arg)
			    $data = str_replace($arg,$replace_vals[$a],$data);
		}
		//DEFAULT ARG
		$page_instructions = str_replace('[PRINTER_URL]',$printer_url,$data);
		
		if ($html_page) {
		    $html_head = '
			<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
			<title>IPP Server '.$this->server_version.' | stereobit.networlds</title>
            </head>
            <body>
			';
			$html_foot = '</body></html>';
			$html_url = "http://" . $_ENV["HTTP_HOST"] .
		                pathinfo($_SERVER['PHP_SELF'],PATHINFO_DIRNAME);
		}
		if ($hasmenu)
            $menu = $this->html_printer_menu(true);	
		if ($hasfooter) {
		    $footer = "
		<div id=\"footer\">
        $this->printer_name
		</div>";
        }		
		
	    $ret = <<<EOF
		$html_head
<link rel="stylesheet" type="text/css" href="{$html_url}view.css" media="all">
<script type="text/javascript" src="{$html_url}view.js"></script>	
	
    <div class="container">
    $header
    <div class="upload_form_cont">	
	<div id="form_container">
	    $menu					

		$page_instructions
	
		$footer
	</div>
	</div>
	</div>
    $html_foot
EOF;
		}//ONLY IF INVITATION OR ACTIVATION
		
	    return ($ret);
	}
	
	//add referer,printername,printerdir for registration procedure...
	protected function _add_user_mail($mailafm=null,$refname=null,$refpass=null, $referer=null, $printername=null, $printerdir=null) {
	    if ($mailafm) { //composite value
		    $x = explode(':',$mailafm);
			$mail = $x[0];
			$afm = $x[1];
        }    		
		else
		    return false;
			
		$printer_name = $printername ? $printername . '.printer' : $this->printer_name;	//personal printer
        $location = $printerdir ? $printerdir : $_SESSION['indir'];	//personal dir	

		$printermon = "http://" . $_ENV["HTTP_HOST"] . 
		              pathinfo($_SERVER['PHP_SELF'],PATHINFO_DIRNAME).
					  pathinfo($_SERVER['PHP_SELF'],PATHINFO_BASENAME);	
					  
		$printermonitor = $printermon . '?printer='.$printer_name;	

		//services to enable...
		$use_eafdss = GetParam('use_eafdss') ? 1 : null;
		$use_fhm = GetParam('use_fhm') ? 1 : null;
		$use_services = GetParam('use_services') ? 1 : null;		
		
        if (($refname) && ($refpass)) { //activate
		   //$location = $_SESSION['indir'];
		   $keystring = $afm.'<|>'.$mail.'<|>'.$refname.'<|>'.$refpass.'<|>'.$printer_name.'<|>'.$location.'<|>'.$use_eafdss.'<|>'.$use_fhm.'<|>'.$use_services; 
		   $activation_urlstring = rawurlencode(base64_encode($keystring));
		   $activation_link = $printermon.'?activation='.$activation_urlstring.'&printer='.$printer_name;
		                      //seturl('activation='. $activation_urlstring.'&printer='.$this->printer_name,'here to activate your account');
        }	

		//$location = $_SESSION['indir'];
		//must be active to invite
        $keystring = $afm.'<|>'.$mail.'<|>'.$refname.'<|>'.$refpass.'<|>'.$printer_name.'<|>'.$location;		   
		$invitation_urlstring = rawurlencode(base64_encode($keystring));		
		$invitation_link = $printermon.'?invitation='.$invitation_urlstring.'&printer='.$printer_name;	
						   
		//if (is_in_list) ..dont send ..already logged in by url ???
		
        //send mail
        $message = $this->html_show_instruction_page('send-mail',array('[PRINTERNAME]','[USERNAME]','[PASSWORD]','[ACTLINK]','[INVLINK]','[PRINTERMON]'),
		                                                         array($this->printer_name, $refname, $refpass, $activation_link, $invitation_link, $printermonitor),
																 true);		  
		
		$from = $this->printer_name . '@' . str_replace('www.','',$_ENV["HTTP_HOST"]);//'balexiou@stereobit.com'
        $ok = $this->_sendmail($from,$mail,$this->printer_name .' activation',$message);
		//notify
        $ok2 = $this->_sendmail($from,$this->notify_mail,$this->printer_name .' user activation',$mail . $message);		
		
		if ($ok) {
		    //echo $refname,'-',$this->username;
			$referer = $referer ? $referer : $this->username;
		    $ret = $this->_save_mail_relation($mail,$refname,$referer);//<<<<there is no user at registration
			return ($ret);
		}  
		  
        return false;		
	}
	
	protected function _setup_user() {
	
	    //refresh conf file
	    if ($this->username!=$this->get_printer_admin()) 
		    $this->config_file = $this->admin_path . 'taxcalc-'.$this->username.'-conf'.'.php';
	    else
            $this->config_file = $this->admin_path . 'taxcalc-conf'.'.php';		
	
	    //read conf	   
        if (is_readable($this->config_file))
            include($this->config_file);	   

        $this->signer_service = $itaxsigner ? true : false; //enable eafdss
	    $this->fiscal_service = $itaxsigner ? ($itaxfiscal ? true : false) : false; //enable taxfiscal pos only if eafdss is active
        $this->cprint_service = $itaxcprint ? true : false; //enable consumer services		   

	    //timezone	   
        date_default_timezone_set($itaxctimezone);	
	}
	
	//login func called every time in loop (event)..calling setup_user for session/conf update
	protected function _login() {
	    $printerdir = $_POST['indir'] ? $_POST['indir'] : $this->defdir;
        $current_printer = GetReq('printer');	
	    $ret = false;
		//echo $printerdir,'>',$current_printer;
		
		//in case of url name...disable
		/*if (($current_printer) && ($current_printer!=$this->printer_name)) {
		    $ret = $this->login_form($current_printer);
		    return ($ret);
		} */ 

		if ($_SESSION['user']) { //already in
		    //echo 'in';
	        $this->_setup_user();			
			return true;		
		}
		elseif ($user = $_POST['username']) { //post login
            
			//echo $printerdir,'>';
			
		    //if ((strlen($printerdir)>10) /*&& (substr($printerdir,0,3)=='TXC')*/) {//40) {//personal tax printer!!!!
			if ($printerdir!='taxcloud') {//is personal tax printer!!!!     
			    $printername = $printerdir . '.printer';//same as indir
			    $bootstrap = $_SERVER['DOCUMENT_ROOT'] . '/' . $printerdir . '/index.php';//<<index	file
				//echo $bootstrap;//..search user inside bootstrap index file
		        $log = $this->get_printer_user($printername,$printerdir,null,null,$bootstrap); //user tax printer
			}	
			else //search user default user into taxcloud.printer  
			    $log = $this->get_printer_user(); //default taxcloud.printer				
			
			if ($log === true) {
			  //set personal tax printer data
	   		  $_SESSION['printer'] = $this->printer_name = $printername;
			  $_SESSION['indir'] = $this->defdir = $printerdir;	
			  
			  $this->username = $user; //..save name login

			  //refresh paths
              $this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/' . $printername .'/';			  
	          $this->admin_path = $_SERVER['DOCUMENT_ROOT'] .'/admin/'. $printername .'/';	
			  
	          $this->_setup_user();			  
			  
			  return true;
			}  
			else {
			  //$ret = $this->login_form();
		      //return ($ret);			
			  return false;
            }			
		}
		elseif ($invite = $_GET['invitation']) {//get param to propose a registration by link
		
			//INVITATE USER....................................

			$urlstring = rawurldecode(base64_decode($invite));
			//echo $urlstring;
			$args = explode('<|>',$urlstring);
			$afm = $args[0];
			$mail = $args[1];
			$user = $args[2];
			$pass = $args[3];	
			$printername = $args[4];
			$printerdir = $args[5];	
			
			$_SESSION['INVITATION'] = $args; 
			$this->url_invitate = $args;
			
            $log = $this->get_printer_user($printername,$printerdir,$user,$pass); //must be active to procced
            //if (!$log) //or use the guest account if not activated ..DISABLED..	
              //  $log = $this->get_printer_user($printername,$printerdir,'guest','guest123');			
			
			if ($log === true) {
			  //..setup user....????
			  return true;
			}  
			else {
              //$ret = $this->login_form();
		      //return ($ret);			
			  return false;
            }				
		}	
		elseif ($active = $_GET['activation']) {//get param to activate by link
			
			$urlstring = rawurldecode(base64_decode($active));
			//echo $_GET['activation'],$urlstring;
			$args = explode('<|>',$urlstring);
			$afm = $args[0];
			$mail = $args[1];
			$user = $args[2];
			$pass = $args[3];	
			$printername = $args[4];
			$printerdir = $args[5];			
			
			//echo $printername,$printerdir,$user,$pass;
			
			//ACTIVATE USER....................................
            //$params = $this->parse_printer_file($printername/*'index.printer'*/, $printerdir);

			$bootfile = $_SERVER['DOCUMENT_ROOT'] . '/' . $printerdir .'/index.php';
			$params = $this->parse_printer_file(null,null,$bootfile);
		    
		    if (empty($params))
		        return ('Activation error #1');
				
		    $printerusers = (array) $params['users'];			
			if (!array_key_exists($user, $printerusers)) { 
			    $printerusers[$user] = hash('crc32',$pass); //< hash of hashed value...
			    			
                $ok = $this->html_mod_printer($bootfile,//<<override to handle subdir file
				                              $printername,
		                                      null,
						                      null,
						                      $printerusers,
						                      $printerdir);
			
			    if ($ok) {
				  $_SESSION['ACTIVATION'] = $args; 
				  $this->url_activate = $args;
				  $this->message = "Activation completed.";	//not used goto conf			  
				}  
	
			}
			else {//just pass params ..re-activate withour reg
		        $_SESSION['ACTIVATION'] = $args; 
			    $this->url_activate = $args;
                $this->message = "Activation has already done.";				
			}
            //echo $this->message;			
            //ACTIVATION...................................
			//echo $printername,':',$printerdir;
			//print_r($this->url_activate);
            //auto-login			
            $log = $this->get_printer_user($printername,$printerdir,$user,$pass,$bootfile);	//<<<bootfile used			

			if ($log === true) {
			  //echo 'z';
	   		  $_SESSION['printer'] = $this->printer_name = $printername;
			  $_SESSION['indir'] = $this->defdir = $printerdir;
			  
			  $this->username = $user; //auto login
			  
			  //refresh paths whrn loggin printername change!!!!!!!!!!!!!!!!!!!!!!!!!!!!
              $this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/' . $printername .'/';			  
	          $this->admin_path = $_SERVER['DOCUMENT_ROOT'] .'/admin/'. $printername .'/';			  

			  //setup user ????
			  
			  //echo 'TEST PAGE:',$user,'>',$printername,'<br>';
			  //send test page to the printer.....................for 2nd step dropbox allow
			  /*
			  if ($testpage_id = $this->send_test_page(null,$printername, $printerdir, $user))
			     $_SESSION['TESTPAGE'] = $testpage_id;  
			  */
			  return true;
			}  
			else {
              //$ret = $this->login_form();
		      //return ($ret);			
			  return false;
            }			
		}
		else { //login form
		    //$ret = $this->login_form($message);
		    //return ($ret);			
			return false;
	    }

        return false; 			
	}
	
	//overrite ipp logout 
   	protected function _logout() {

       //session_destroy();
	   
       if (isset($_SESSION['user'])) {
          $_SESSION['user'] = null; 
		  $_SESSION['printer'] = null; //hold for re-login
		  $_SESSION['indir'] = null;//hold for re-login
		  
		  $_SESSION['new_user'] = null; //new user, allow only one when login
		  $_SESSION['ACTIVATION'] = null; //activation is done
		  $_SESSION['INVITATION'] = null; //invitation is done
		  $_SESSION['REGISTRATION'] = null; //registration is done
		  
		  $_SESSION['TESTPAGE'] = null; //testpage reset
		  
		  $_SESSION['itaxusermail'] = null; //config param to session

          //$ret .=  "Successfully logged out<br>";
          //echo '<p><a href="?action=logIn">LogIn</a></p>';
		  return true;
       }

       //return ($ret);	   
	   return false;
    }		
	
	//login form
	protected function login_form($message=null) {
	    $message = $this->message ? '('.$this->message.')' : ($message ? '('.$message.')' : null);
	    $printername = $this->printer_name ? $this->printer_name : GetParam('printername');
		$indir = $_GET['printer'] ? str_replace('.printer','',$_GET['printer']) :
		         ($_POST['indir'] ? $_POST['indir'] :($_SESSION['indir'] ? $_SESSION['indir'] : $this->defdir));
	    $select_printer = null;
		$cmd = $this->procmd ? $this->procmd.'login': 'login';
		$name = GetReq('prn') ? GetReq('prn').'.printer' : null;
		
		if (strlen($this->message)>1) //step-3	
	        $msg = $message ? '<h2>'.$message.'</h2>' : null;			

	    if (!$printername) 
		    return false; //not possible
		  
		$select_printer = "
<input type=\"hidden\" name=\"printername\" value=\"$printername\" />
<!--input type=\"hidden\" name=\"indir\" value=\"$indir\" /-->
";
        if (strlen($indir)!=40) {//has logout from personal tax printer
		    $select_printer .= '
<li id="li_6" >
<label class="description" for="indir">Tax printer</label>
<div>
<input id="element_6" name="indir" class="element text large" type="text" maxlength="40" value=""/> 
</div>
<p class="guidelines" id="guide_6"><small>Tax printer name (leave it blank for default demo tax printer)</small></p> 
</li>';
        }
        else
		    $select_printer .= "<input type=\"hidden\" name=\"indir\" value=\"$indir\" />";

	    $form .= <<<EOF
		<form id="form_login" class="appnitro"  method="post" action="">
		<div class="form_description">
		    <!--h2>Login</h2-->
			<p>Please enter your details to access your account.</p>
			$msg
		</div>						
		<ul >		
			
		<li id="li_4" >
		<label class="description" for="element_4">User </label>
		<span>
			<input id="element_4_1" name= "username" class="element text" maxlength="40" size="18" value=""/>
			<label>Username</label>
		</span>
		<span>
			<input id="element_4_2" type= "password" name= "password" class="element text" maxlength="40" size="18" value=""/>
			<label>Password</label>
		</span><p class="guidelines" id="guide_4"><small>Printer credentials</small></p> 
		</li>
			
			    
		$select_printer		
				
		<input type="hidden" name="form_id" value="470441" />
		<input type="hidden" name="FormAction" value="$cmd" />
		
		<li class="buttons">
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
		
        <li class="section_break">
		<h2>Create a taxcloud account</h2><p>Enable your personal taxcloud service. 
		<a href="taxprinter.php?t=taxregister">Create your account.</a></p>
		</li>
		
		</ul>
		</form>		
EOF;
	
	    $ret  = $this->html_window("Login ".$msg, $form, $this->printer_name, true);	
        return ($ret);	
	}	

	protected function get_printer_user($printername=null, $printerdir=null, $printeruser=null, $printerpass=null, $bootstrapfile=null) {
        $printername = $printername ? $printername : GetParam('printername');
		$printerdir = $printerdir ? $printerdir : GetParam('indir');
		$printeruser = $printeruser ? $printeruser : GetParam('username');
		$printerpass = $printerpass ? $printerpass : GetParam('password');
		$allowed_users = array();
		
		if (!$printeruser && !$printerpass && !$printername) {
		  $this->message = 'Invalid user';
		  return false;
		}  
	    
		$dir = $printerdir ? $printerdir.'/' : null;
	    $bootstrapfile = $bootstrapfile ?
		                 $bootstrapfile :
		                 $_SERVER['DOCUMENT_ROOT'] .'/'.$dir. str_replace('.printer','.php',$printername);
		//echo $bootstrapfile,'>';
		if (!is_readable($bootstrapfile)) {
		   //echo $bootstrapfile,'>';
		   $this->message = 'Invalid printer';
		   return false;		
		}
		
		$params = $this->parse_printer_file(null, null, $bootstrapfile);
		//print_r($params);
		
		if (empty($params)) {
		   $this->message = 'Invalid configuration';
		   return false;
		}   
		
		$allowed_users = (array) $params['users']; 
        //print_r($allowed_users);		
		   
		if (($printeruser) && ($printerpass)) {
		   //echo 'z';
		   $hashpass = hash('crc32',$printerpass); //hash comparison
		 
		   if ((array_key_exists($printeruser, $allowed_users)) &&
		       ($allowed_users[$printeruser] == $hashpass)) { 
			    //echo 'm';
		        $_SESSION['user'] = $printeruser;
				$_SESSION['printer'] = $printername;//str_replace('.printer','',$printername);
				$_SESSION['indir'] = $printerdir;
				return true;
		   }
        }		
	
	    //return ($message);
		
		return false;
	}
	
	//override 
	public function form_useprinter($printername=null, $indir=null) {
	    $printername = $name ? $name : ($_POST['printername']?$_POST['printername']:$this->printer_name);
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
		$cmd = $this->external_use ? $this->procmd.'useprinter':'useprinter';
        $printerusers = array();
		$ok = false;

	    if ($this->username!=$this->get_printer_admin()) {
           //return false;//disabled..or just change pass ?
		   
		   $eafdss_data = $this->read_documents_data();//null,null,null,$limit); //<<<<<<<<<<<<<<<<<<<<<
		   
		   $service_type_name =  $this->signer_service ? "Signed files viewer" : "TDoc viewer"; 
		   $ret  = $this->html_window($service_type_name, $eafdss_data, $this->printer_name);//, true);
		   return ($ret);
		}   
		//else
		$ret = parent::form_useprinter($printername, $indir);
		return ($ret);
		
    }	
	
	protected function tdoc_data_form() {
	    $cmd = 'taxuseprinter';
		$ftype = 'tdoc';//$_POST['ftype'] ? $_POST['ftype'] : ($_GET['ftype'] ? $_GET['ftype'] : ($filetype ? $filetype : 'dfss'));
		$c1=$c2=$c3=$c4=null;
		//print_r($_POST);
		
		$qrsearch = $this->fiscal_on ? '| <a href="?t=taxqrsearch">QR Search</a>' : null;
		$posted_search = $_POST['stext'];
	
	    $form = <<<EOF
<!--link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script-->	
<script type="text/javascript" src="calendar.js"></script>
		
	<!--div id="form_container"-->
		<form id="form_search" class="appnitro" enctype="multipart/form-data" method="post" action="">	
		<div class="form_description">
			<h2>Search $qrsearch</h2>
			<p>Search documents.</p>
		</div>			
		<ul >
		
		<li id="li_0" >
		<label class="description" for="element_0">Text</label>
		<div>
		<input id="element_0_1" name= "stext" class="element text medium" maxlength="64" value="$posted_search"/>
		<label>Text</label>
		</div>
		<p class="guidelines" id="guide_0"><small>Type a word</small></p> 
		</li>		
			
		<li id="li_1" >
		<label class="description" for="element_1">Date from </label>
		<span>
			<input id="element_1_1" name="date_from_day" class="element text" size="2" maxlength="2" value="" type="text"> /
			<label for="element_1_1">DD</label>
		</span>
		<span>
			<input id="element_1_2" name="date_from_month" class="element text" size="2" maxlength="2" value="" type="text"> /
			<label for="element_1_2">MM</label>
		</span>
		<span>
	 		<input id="element_1_3" name="date_from_year" class="element text" size="4" maxlength="4" value="" type="text">
			<label for="element_1_3">YYYY</label>
		</span>
	
		<span id="calendar_1">
			<img id="cal_img_1" class="datepicker" src="calendar.gif" alt="Pick a date.">	
		</span>
		<script type="text/javascript">
			Calendar.setup({
			inputField	 : "element_1_3",
			baseField    : "element_1",
			displayArea  : "calendar_1",
			button		 : "cal_img_1",
			ifFormat	 : "%B %e, %Y",
			onSelect	 : selectDate
			});
		</script>
		 
		</li>		
		
		<li id="li_2" >
		<label class="description" for="element_2">Date to </label>
		<span>
			<input id="element_2_1" name="date_to_day" class="element text" size="2" maxlength="2" value="" type="text"> /
			<label for="element_2_1">DD</label>
		</span>
		<span>
			<input id="element_2_2" name="date_to_month" class="element text" size="2" maxlength="2" value="" type="text"> /
			<label for="element_2_2">MM</label>
		</span>
		<span>
	 		<input id="element_2_3" name="date_to_year" class="element text" size="4" maxlength="4" value="" type="text">
			<label for="element_2_3">YYYY</label>
		</span>
	
		<span id="calendar_2">
			<img id="cal_img_2" class="datepicker" src="calendar.gif" alt="Pick a date.">	
		</span>
		<script type="text/javascript">
			Calendar.setup({
			inputField	 : "element_2_3",
			baseField    : "element_2",
			displayArea  : "calendar_2",
			button		 : "cal_img_2",
			ifFormat	 : "%B %e, %Y",
			onSelect	 : selectDate
			});
		</script>
		 
		</li>				
			
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="$cmd" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
		</ul>
		</form>	
	<!--/div-->
EOF;
        return ($form);		
		//$ret = $this->html_window("", $form, 'footer', true);
		//return ($ret);		
	}	
	
	protected function eafdss_data_form() {
	    $cmd = 'taxuseprinter';
		$ftype = $_POST['ftype'] ? $_POST['ftype'] : ($_GET['ftype'] ? $_GET['ftype'] : ($filetype ? $filetype : 'dfss'));
		$c1=$c2=$c3=$c4=null;
		//print_r($_POST);
		switch ($ftype) {
		   case 'dhfass' : $c4 = 'checked="checked"'; break;		
		   case 'dapfmhs': $c3 = 'checked="checked"'; break;		
		   case 'dsym'   : $c2 = 'checked="checked"'; break;
		   case 'dfss'   :
		   default       : $c1 = 'checked="checked"';
		}
		
		$qrsearch = $this->fiscal_on ? '| <a href="?t=taxqrsearch">QR Search</a>' : null;
		$posted_search = $_POST['stext'];
	
	    $form = <<<EOF
<!--link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script-->	
<script type="text/javascript" src="calendar.js"></script>
		
	<!--div id="form_container"-->
		<form id="form_search" class="appnitro" enctype="multipart/form-data" method="post" action="">	
		<div class="form_description">
			<h2>Search $qrsearch</h2>
			<p>Search documents.</p>
		</div>			
		<ul >
		
		<li id="li_0" >
		<label class="description" for="element_0">Text</label>
		<div>
		<input id="element_0_1" name= "stext" class="element text medium" maxlength="64" value="$posted_search"/>
		<label>Text</label>
		</div>
		<p class="guidelines" id="guide_0"><small>Type a word</small></p> 
		</li>			
			
		<li id="li_1" >
		<label class="description" for="element_1">Date from </label>
		<span>
			<input id="element_1_1" name="date_from_day" class="element text" size="2" maxlength="2" value="" type="text"> /
			<label for="element_1_1">DD</label>
		</span>
		<span>
			<input id="element_1_2" name="date_from_month" class="element text" size="2" maxlength="2" value="" type="text"> /
			<label for="element_1_2">MM</label>
		</span>
		<span>
	 		<input id="element_1_3" name="date_from_year" class="element text" size="4" maxlength="4" value="" type="text">
			<label for="element_1_3">YYYY</label>
		</span>
	
		<span id="calendar_1">
			<img id="cal_img_1" class="datepicker" src="calendar.gif" alt="Pick a date.">	
		</span>
		<script type="text/javascript">
			Calendar.setup({
			inputField	 : "element_1_3",
			baseField    : "element_1",
			displayArea  : "calendar_1",
			button		 : "cal_img_1",
			ifFormat	 : "%B %e, %Y",
			onSelect	 : selectDate
			});
		</script>
		 
		</li>		
		
		<li id="li_2" >
		<label class="description" for="element_2">Date to </label>
		<span>
			<input id="element_2_1" name="date_to_day" class="element text" size="2" maxlength="2" value="" type="text"> /
			<label for="element_2_1">DD</label>
		</span>
		<span>
			<input id="element_2_2" name="date_to_month" class="element text" size="2" maxlength="2" value="" type="text"> /
			<label for="element_2_2">MM</label>
		</span>
		<span>
	 		<input id="element_2_3" name="date_to_year" class="element text" size="4" maxlength="4" value="" type="text">
			<label for="element_2_3">YYYY</label>
		</span>
	
		<span id="calendar_2">
			<img id="cal_img_2" class="datepicker" src="calendar.gif" alt="Pick a date.">	
		</span>
		<script type="text/javascript">
			Calendar.setup({
			inputField	 : "element_2_3",
			baseField    : "element_2",
			displayArea  : "calendar_2",
			button		 : "cal_img_2",
			ifFormat	 : "%B %e, %Y",
			onSelect	 : selectDate
			});
		</script>
		 
		</li>			
			

        <li id="li_3" >
		<label class="description" for="element_3">Select document type</label>
		<span>
		<input id="element_3_1" name="ftype" class="element radio" type="radio" value="dfss" $c1/>
        <label class="choice" for="element_3_1">dfss</label>
        <input id="element_3_2" name="ftype" class="element radio" type="radio" value="dsym" $c2/>
        <label class="choice" for="element_3_2">dsym</label>
        <input id="element_3_3" name="ftype" class="element radio" type="radio" value="dapfmhs" $c3/>
        <label class="choice" for="element_3_3">dapfmhs</label>
        <input id="element_3_4" name="ftype" class="element radio" type="radio" value="dhfass" $c4/>
        <label class="choice" for="element_3_4">dhfass</label>

		</span> 
		</li>		
			
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="$cmd" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
		</ul>
		</form>	
	<!--/div-->
EOF;
        return ($form);		
		//$ret = $this->html_window("", $form, 'footer', true);
		//return ($ret);		
	}
	
	//read admin dir for documents / payps...
	protected function read_documents_data($flashback_days=null, $filetype=null, $extension=null, $reclimit=null) {	
	    $flashback_days = $flashback_days ? $flashback_days : 1;
		
		if ($this->signer_service)//eafdss service enabled<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
		    $filetype = $_POST['ftype'] ? $_POST['ftype'] : ($_GET['ftype'] ? $_GET['ftype'] : ($filetype ? $filetype : 'dfss'));		
		else
		    $filetype = 'tdoc';
			
		$ret = null;
		$dback = array();		
	    $dst = date('I');		
	    $_dst = $dst ? ' ΚΩ:' : ' ΧΩ:';	
        $records_limit = $reclimit ? $reclimit : $this->fiscal_record_limit;//null;		
		
		/*if ($this->username!=$this->get_printer_admin()) 
		    $config_file = $this->admin_path . 'taxcalc-'.$this->username.'-conf'.'.php';
		else
            $config_file = $this->admin_path . 'taxcalc-conf'.'.php';*/		
		//echo $config_file."<br>";
		//local include conf reading
		if (is_readable($this->config_file)) 
            include($this->config_file);
        else
            return "Invalid configuration!";//.$this->config_file;		
		
        //filter		
		$fid = $filetype ? $itaxserial .'-'. $filetype : $itaxserial .'-';
	    $fid_maxc = strlen($fid);	
        if ($this->signer_service) {//eafdss service enabled<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<		
	        $ext = $extension ? $extension : '.txt';
		    $ext_maxc = (strlen($ext))*-1;
		}
		else {//xps tdoc generated
		    $ext = '.xps';
		    $ext_maxc = (strlen($ext))*-1;
		}
		
		//text filter
	    $search_text = $_POST['stext'] ? $_POST['stext'] : null; //search 'a' file content			
		
		//date filter
		$this->time_diff($fbdays, $limit);
		//echo '<br>'.$fbdays.':'.$limit;
		
	    //create date -flashback days
	    $mk_now = time();
	    $start_ti=0; //1=yestrday, 0=today
		$end_ti = /*$flashback_days ? $flashback_days : */$fbdays; 	
        $limit_ti = $limit;		
	    //echo '<br>'.$start_ti.':'.$end_ti.':'.$limit_ti;
		
	    for ($ti=$start_ti;$ti<=$end_ti;$ti++) { 
		    $mk_dayback = $mk_now - ($ti * 24 * 60 * 60); //one day -
			$folder = date('Ymd',$mk_dayback);
			//up limit
			if (intval($folder)<=intval($limit)) {
			  //echo '<br>--'.$folder.':'.$limit;  
		      $dback[$folder] = $mk_dayback; 
			}
	    }
        //print_r($dback);
		foreach ($dback as $day=>$tstamp) {
	
            $dfss_path = $this->admin_path . $day;	
			//echo $dfss_path."<br>";
			
            if (is_dir($dfss_path)) {
			    $mydir = dir($dfss_path);
				//echo "YES<br>";
				$a_ret = array();
				
				/*if ($filetype=='dhfass')
			        $zlink = '&nbsp;-&nbsp;'.seturl('t=taxdhfass'.'&date='.$day.'&taxid='.$itaxserial,'Pick Z');				
				else
                    $zlink = null;*/				
				
	            $date_print = date('D d M Y', $tstamp);//rfc2822	
	            //$tt = $date_print . $_dst ;				
				
				$ret .= '<h2>'.$date_print/*.$zlink*/.'</h2><hr>';
				
		        $i=0;		
                while ($fileread = $mydir->read ()) { 
				
                  $path_fileread = $dfss_path .'/'.$fileread;
				  
				  if ((is_readable($path_fileread)) && ($fileread!='.') && ($fileread!='..')) { 
				  
	                //$fr = ($filetype) ? str_replace($itaxserial .'-'. $filetype,'',$fileread) : $fileread;
					//echo substr($fileread,-(strlen($ext))==$ext) .strlen($ext). "<br>";
					//echo $fileread,'<br>';
					
	                if ((substr($fileread,0,$fid_maxc)==$fid) && (substr($fileread,$ext_maxc)==$ext)) {
					    //echo $fileread,'<br>';
                        $i+=1;	
						$fr = str_replace($ext,'',$fileread);
			            $pf = explode('-',$fr);
						
						if ($filetype=='tdoc') {
								$id = array_pop($pf);
								$time = date ("F d Y H:i:s.", filemtime($path_fileread));
								$name = str_replace($itaxserial.'-','',$fr);
						
								$qr_photo = $itaxserial.'-tdoc-'.$id.'-qrpay.png';						
								$qrimage = 'admin/'.$this->printer_name.'/'.$day.'/'.$qr_photo;
								$qrcode_image = "<img src='$qrimage' height='42' width='42'/>";	
								$qrpaylink = seturl('t=taxqrpay&ftype='.$filetype.
						                        '&ext='.str_replace('.','',$ext).
												'&date='.$day. 
												'&taxid='.$itaxserial.
												'&id='.$id.
                                                '&sign='.$ssign,
											    $qrcode_image);							
						}
						else {
						
						    $id = intval(array_pop($pf)) ;//intval($pf[1]);
					        $time = date ("F d Y H:i:s.", filemtime($path_fileread));
						    $name = str_replace($itaxserial.'-','',$fr);
						
						    if ($filetype=='dfss') {
						        $bfile = $this->pick_ab_file($id,'b',$itaxserial,$dfss_path);
								
								//search bfile internal contents...
								//if (($search_text) && (stristr($bfile,$search_text)==false))								
								if ($search_text) {
								    //fetch a file data for search when search_text
								    $afile = $this->pick_ab_file($id,'a',$itaxserial,$dfss_path);
								    //echo $afile,'<br/><br/>';
								    if ((self::search_doc_internal($bfile,$search_text)==false) &&
									    (self::search_doc_internal($afile,$search_text)==false))
										continue;
								}	
							}	
						    else
                                $bfile = 'Show';
								
                            $ssign = ($bfile!='Show') ? $bfile : null;							
						    $picklink = seturl('t=taxpick&ftype='.$filetype.
						                            '&ext='.str_replace('.','',$ext).
													'&date='.$day. 
													'&taxid='.$itaxserial.
													'&id='.$id.
                                                    '&sign='.$ssign, 													
													$bfile);//'Show');
                            //qr code
						    if ($ssign) {
								$sp = explode(' ',$ssign);
								$_taxserial = array_pop($sp);
								$_id = sprintf("%04s", intval($sp[1])); 
								$_sid = sprintf("%08s", intval($sp[2]));				
								$qr_photo = $_taxserial.$_sid.$_id.'-qr.png';						
								$qrimage = 'admin/'.$this->printer_name.'/'.$day.'/'.$qr_photo;
								$qrcode_image = "<img src='$qrimage' height='42' width='42'/>";	
								$qrpaylink = seturl('t=taxqrpay&ftype='.$filetype.
						                        '&ext='.str_replace('.','',$ext).
												'&date='.$day. 
												'&taxid='.$itaxserial.
												'&id='.$id.
                                                '&sign='.$ssign,
											    $qrcode_image);							
						    }
						}	
						//$ret .= $fileread . "<br>";				
						$a_ret[$id] = array(/*'id'=>$id,*/'name'=>$name,'time'=>$time,'picklink'=>$picklink,'qrimg'=>$qrpaylink);	
                    } 				
                  }					
				}
				//render array
				if (!empty($a_ret)) {
				    krsort($a_ret);
					$lm = 1;
					foreach ($a_ret as $i=>$attr) {
						
			            $ret .= self::printline($attr,array(/*'left;10%',*/'left;20%','left;20%','left;50%','left;10%'),
									            0,"center::100%::0::group_article_body::left::0::0::");
						$lm+=1;						
					    if (($records_limit) && ($lm>$records_limit))
                            break;						
					}
				}
				else {//empty array..no dhfass files
				    if ($filetype=='dhfass') {
					   $zlink = seturl('t=taxdhfass'.'&date='.$day.'&taxid='.$itaxserial,'Pick Z');
					   $ret .= $zlink; //get z on empty z folder
					}   
				}
				
				//on all folders
				if ($filetype=='dsym') {
					$xlink = seturl('t=taxdsym'.'&date='.$day.'&taxid='.$itaxserial,'Pick X');
					$ret .= '&nbsp;'.$xlink; //get x on any folder
				} 	
                elseif ($filetype=='dapfmhs') {
					$slink = seturl('t=taxdapfmhs'.'&date='.$day.'&taxid='.$itaxserial,'Pick S');
					$ret .= '&nbsp;'.$slink; //get x on any folder				
				}
				
				$mydir->close ();
            }			
	    }
		
		$user_line = "<h2>".
		             $itaxserial . "<br>" . $ionomasia . "-" . $icommerTitle . "<br>" .
		             $iafm . "-" . $idoyDescr .
					 "</h2>";
		
		$hr = null;//"<hr>";
		
		if ($this->signer_service)//eafdss service enabled<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
		    $filter   = $this->eafdss_data_form();
		else
            $filter   = $this->tdoc_data_form();
			
		$cmd_line = null;
		            /*seturl('t=taxuseprinter&ftype=dfss','dfss') . "|" .
		            seturl('t=taxuseprinter&ftype=dsym','dsym') . "|" .
					seturl('t=taxuseprinter&ftype=dapfmhs','dapfmhs') . "|" .
					seturl('t=taxuseprinter&ftype=dhfass','dhfass');*/
		
		$ret = $ret ? $user_line .$hr. $cmd_line .$hr. $ret . "<hr>". $cmd_line . $filter : 
		              $user_line .$hr. "Empty" .$hr. $cmd_line . $filter;
	    return ($ret);
	}
	
	//check doc internal for search...
	protected function search_doc_internal(&$sdata, $search) {
	    if (!$sdata) return false;
	
	    if (substr($sdata,0,2)=='PK') {//xps ziped file
		    //return null
	    }
		elseif (substr($sdata,0,4)=='%!PS') {//postscript
		    //return null 
		}
		elseif (substr($sdata,0,4)=='%PDF') {//pdf 
		    //return null
		}
		else { //text to search
		
		    if (stristr($sdata,$search))
			    return true;
		}
		
		return false;
	}
	
	//calculate dates from post
	protected function time_diff(&$daysback, &$daylimit) {
	    $today = intval(date('Ymd'));
				
	    $date_A = $_POST['date_from_day'] ? 
		          $_POST['date_from_year'] .'-'. $_POST['date_from_month'] .'-'. $_POST['date_from_day'] : date('Y-m-d');
	    $date_B = $_POST['date_to_day'] ? 
		          $_POST['date_to_year'] .'-'. $_POST['date_to_month'] .'-'. $_POST['date_to_day'] : date('Y-m-d');
		//echo $date_A,':',$date_B;
        		
	
		$datetime1 = new DateTime($date_A);
        $datetime2 = new DateTime($date_B);
		$datetime3 = new DateTime("now");
        //$interval = $datetime1->diff($datetime2);
		//$day_diff = $interval->format('%a');//%R%a days');//abs days
	
	    //convert to 
		$date_1 = intval(str_replace('-','',$date_A));
		$date_2 = intval(str_replace('-','',$date_B));
		
		//find day diff between two given dates
		$interval = ($date_1>=$date_2) ? $datetime1->diff($datetime2) : $datetime2->diff($datetime1);
		$day_diff = $interval->format('%a');
        //echo '<br>DAY DIFF:',$day_diff;

        //find day diff from min date til today
        $interval2 = ($date_1>=$date_2) ? 
		             ($date_2<$today ? $datetime3->diff($datetime2) : 0) : 
					 ($date_1<$today ? $datetime3->diff($datetime1) : 0);		    
		$daysback = ($interval2) ? $interval2->format('%a') : 0;//return 0 day = only today
        //echo '<br>DAY DIFF NOW:', $daysback;		
		
		//find max day limit
        $daylimit = ($date_1>=$date_2) ? ($date_1<$today ? $date_1 : $today) : ($date_2<$today ? $date_2 : $today);
        //echo '<br>DAY LIMIT:',$daylimit;
			
	}	
	
	protected function pick_ab_file($id=null,$ab=null,$taxid=null,$dfss_path=null) {
	    if (!$id) return false;
		
	    $dir_name = $dfss_path ? $dfss_path . '/' : '/';
		$taxid = $taxid ? $taxid : null;
		$ab = $ab ? $id.'_'.$ab.'.txt' : $id.'_a.txt';
		$ablen = strlen($ab)*-1;
	
        if (($taxid) && (is_dir($dir_name))) {
		
            $mydir = dir($dir_name);
		
		    $i=0;		
            while ($fileread = $mydir->read ()) {

                if ((substr($fileread,0,strlen($taxid))==$taxid) && (substr($fileread,$ablen)==$ab)) {		
	  
	                $data = file_get_contents($dir_name . $fileread);
					return ($data);
				}	
		    }
		}
		
        return '-';		
	}
	
	protected function pick_tax_file($action=null,$sign=null,$ext=null,$ftype=null,$id=null,$date=null,$taxid=null) {
	    //$printer_name = str_replace('.printer','',$this->printer_name);
	    $id = $id ? $id : $_GET['id'];
		$ssign = $sign ? $sign : $_GET['sign'];
		$qrcode_image = null;
		$action = $action ? $action : $_GET['t'];
		//echo $action;//tax prefix is precutted taxqrpay = qrpay
		if ($id) {
	        $taxid = $taxid ? $taxid : $_GET['taxid'];
		    $date = $date ? $date : $_GET['date'];
		    $ext = $ext ? '.'.$ext : ($_GET['ext'] ? '.'.$_GET['ext']:'.txt');
		    $ftype = $ftype ? $ftype : $_GET['ftype'];// ? $_GET['ftype'] : 'dfss');
			
		    if ($ftype) {
			
			    $file = $taxid .'-'.$ftype.'-'.$id.$ext;
			    $pfile = $this->admin_path .$date.'/'. $file;
				
				if ($ssign) {
				    $sp = explode(' ',$ssign);
				    $_taxserial = array_pop($sp);
				    $_id = sprintf("%04s", intval($sp[1])); 
				    $_sid = sprintf("%08s", intval($sp[2]));				
					$_date = '20' . substr($sp[3],0,6);//prepend year
					$qr_photo = $_taxserial.$_sid.$_id;//.'-qr.png';
					switch ($action) {
					    case 'pay' : $qr_photo .= '-qrpay.png'; break;
					    default    : $qr_photo .= '-qr.png';
					}
				    $qrfile = $this->admin_path .$_date.'/'.$qr_photo;
					
					$trackf = $_taxserial.$_sid.$_id.'.track';
				    $trackfile = $this->admin_path .$_date.'/'.$trackf;
				}

			}
			elseif ($ssign) { //only ssign
			    $sp = explode(' ',$ssign);
				$_taxserial = array_pop($sp);
				$_id = sprintf("%04s", intval($sp[1])); 
				$_sid = sprintf("%08s", intval($sp[2]));
				$_date = '20' . substr($sp[3],0,6); //prepend year
				
				$file = $_taxserial.$_sid.$_id.$ext;
				$pfile = $this->admin_path .$_date.'/'. $file;
				$qr_photo = $_taxserial.$_sid.$_id;
				switch ($action) {
				    case 'pay' : $qr_photo .= '-qrpay.png'; break;
				    default    : $qr_photo .= '-qr.png';
				}				
				$qrfile = $this->admin_path .$_date.'/'.$qr_photo;
				
				$trackf = $_taxserial.$_sid.$_id.'.track';
				$trackfile = $this->admin_path .$_date.'/'.$trackf;
			}	
			//echo $qrfile,'>',$pfile;
            if (is_readable($pfile)) {
			
			
			   switch ($ext) {
			   
			    case '.xps':header('Content-Disposition: attachment; filename='.$file);
				            header("Content-type: application/vnd.ms-xpsdocument");
                            die(file_get_contents($pfile));  //download file...			   
			   
			    case '.txt'://$istxtiso88597 = true;
			    default    ://...show file...
			                $data = iconv("ISO-8859-7", "UTF-8" ,file_get_contents($pfile));
			   
			                //a file contents
			                if (($ftype=='dfss') && ($afile = $this->pick_ab_file($id,'a',$taxid,$this->admin_path .$date))) {
			   
			                    if (substr($afile,0,2)=='PK') //xps ziped file as input
				                    $afile_utf8 = $afile; //as is
				                else 
									$afile_utf8 = iconv("ISO-8859-7", "UTF-8" , $afile);
									//$afile_utf8 = $afile; //as is (xps convertion)										
				   
			                    $afile_data = '
        <li id="li_11" >
		<label class="description" for="element_11">Submited file</label>
		<div>
			<textarea id="element_11" name="itax_a_file" class="element textarea large" readonly>'.$afile_utf8.'</textarea> 
		</div><p class="guidelines" id="guide_11"><small>File contents</small></p> 
		</li>';
                                //???? file name changed...use id +sid 
                                $signed_url = '&nbsp;|&nbsp;'.seturl('t=taxpick&ftype='.//OUT
						                             '&ext=xps'.
							  	                     '&date='.$date. 
									                 '&taxid='.$taxid.
									                 '&id='.$id.
													 '&sign='.$ssign, 
									                 'Pick signed XPS');		
			                }
							else {
							    $afile_data = null;
								$signed_url = null;
							}
			   
			                //xps copy file link to download
			                if (is_readable(str_replace($ext,'.xps',$pfile)))
			      	            $xpslink = seturl('t=taxpick&ftype='.$ftype.
						                          '&ext=xps'.
							   		              '&date='.$date. 
									              '&taxid='.$taxid.
									              '&id='.$id, 
									              'Pick XPS');
							//qr code png file
                            //echo '>',$qrfile;							
							if (is_readable($qrfile)) 
							    $qrimage = 'admin/'.$this->printer_name.'/'.$_date.'/'.$qr_photo;
							else
							    $qrimage = 'icons/err.png';
						    
                            $qrcode_image = "<img src='$qrimage'/>";
							
							//track file
							if (is_readable($trackfile)) {
								$tdata = array_unique(explode(',',@file_get_contents($trackfile)));
								$track_data = '<h2>Send to:</h2>'; 
								$track_data .= implode('<br/>',$tdata);							
							}
							
							$cmd_links = $qrcode_image . '<br/>' . $xpslink . $signed_url . $track_data;
							

			                $out = '
		<form id="form_show_tax_file" class="appnitro" enctype="multipart/form-data" method="post" action="">
		<div class="form_description">
			<h2>Show</h2>
			<p>Show tax file</p>
		</div>						
		<ul >			   
        <li id="li_10" >
		<label class="description" for="element_10">Tax file</label>
		<div>
			<textarea id="element_10" name="itaxdatafile" class="element textarea large" readonly>'.$data.'</textarea> 
		</div><p class="guidelines" id="guide_10"><small>File contents</small></p> 
		</li>'.
		$afile_data.
		'</ul>
		</form>' . $cmd_links;
		
		                    switch ($action) {
		                        case 'pay' : $ret = $this->html_window("Pay ".$ftype."-".$id, $cmd_links, $this->printer_name); break;
			                    default    : $ret = $this->html_window("Show ".$ftype."-".$id, $out, $this->printer_name);//, true);
							}	
			                return ($ret);
			   }//switch
			}				  
		}
		
        return null;		
	}

    protected function search_tax_file($action=null,$sign=null,$ext=null,$ftype=null) {
	    if (!$sign) return null;
		$ext = $ext ? '.'.$ext : ($_GET['ext'] ? '.'.$_GET['ext']:'.txt');
		$ftype = $ftype ? $ftype : ($_GET['ftype'] ? $_GET['ftype'] : 'dfss');		
		
		$sp = explode(' ',$sign);
		//print_r($sp);
		$_taxserial = array_pop($sp);
        $id = intval($sp[1]);		
		$_id = sprintf("%04s", intval($sp[1]));		
		$sid = sprintf("%08s", intval($sp[2]));//08s
		$_sid = sprintf("%04s", intval($sp[2]));//04s
		$date = substr($sp[3],0,6);
		$_date = '20' . $date; //prepend year
		
		$dfss_path = $this->admin_path . $_date;	
        //echo $dfss_path,'>',$_id,'>',$_taxserial;       	    
		
		//if ($bfile = $this->pick_ab_file($_id,'b',$_taxserial,$dfss_path)) {
		$b_filename = $_taxserial.$date.$_sid.$_id.'_b.txt';
		$path_fileread = $dfss_path .'/'.$b_filename;
		//echo '<br>',$b_filename;
		if ($bfile = @file_get_contents($path_fileread)) {
			$name = $ftype;		
            $time = date ("F d Y H:i:s.", filemtime($path_fileread));	
			
            //$ssign = ($bfile!='Show') ? $bfile : null;							
			$picklink = seturl('t=taxpick&ftype='.$ftype.
			                   '&ext='.str_replace('.','',$ext).
			   				   '&date='.$_date. 
							   '&taxid='.$_taxserial.
							   '&id='.$id.
                               '&sign='.$sign, 													
							   $bfile);//'Show');
            //qr code
			//if ($ssign) {			
			$qr_photo = $_taxserial.$sid.$_id.'-qr.png';						
			$qrimage = 'admin/'.$this->printer_name.'/'.$_date.'/'.$qr_photo;
            $qrcode_image = "<img src='$qrimage' height='42' width='42'/>";	
			$qrpaylink = seturl('t=taxqrpay&ftype='.$ftype.
			                    '&ext='.str_replace('.','',$ext).
								'&date='.$_date. 
								'&taxid='.$_taxserial.
								'&id='.$id.
                                '&sign='.$sign,
							    $qrcode_image);							
			//}			
			$a_ret = array(/*'id'=>$id,*/'name'=>$name,'time'=>$time,'picklink'=>$picklink,'qrimg'=>$qrpaylink);			
	        $out = self::printline($a_ret,array(/*'left;10%',*/'left;20%','left;20%','left;50%','left;10%'),
									            0,"center::100%::0::group_article_body::left::0::0::");
												
            $ret = $this->html_window("Search ", $out, $this->printer_name);																				
        }
		else
		    $ret = $this->html_window("Search ", "No result", $this->printer_name);
			
        return ($ret);	
    }	
	
	protected function pick_tax_z() {
	    $date = $_GET['date'];
		
		if ($date) {
		
			$sid_file = $this->admin_path . $this->taxid . '-sid.txt';
		
		    $taxcalc = new taxcalc($this->username, null, null, null, null, $this->printer_name);
			$txret = $taxcalc->save_z($date);
		
		    $ret = $this->html_window("Z", $txret, $this->printer_name);			
		}
		else
		    $ret = $this->html_window("Error", null, $this->printer_name);
		return ($ret);
	}
	
	protected function pick_tax_x($fromdate=null, $todate=null) {
	    $date = $_GET['date'];
		if ($date) {
		
		    $taxcalc = new taxcalc($this->username, null, null, null, null, $this->printer_name);
			$txret = $taxcalc->save_dsym('dsym-',null, $date);
		
		    $ret = $this->html_window("X", $txret, $this->printer_name);	
		}
		else
		    $ret = $this->html_window("Error", null, $this->printer_name);
		return ($ret);
	}	
	
	protected function pick_tax_s() {
	    $date = $_GET['date'];
		if ($date) {
		
		
		
		    $ret = $this->html_window("S", null, $this->printer_name);	
		}
		else
		    $ret = $this->html_window("Error", null, $this->printer_name);
		return ($ret);
	}	
	
	//override
	public function form_configprinter($printername=null, $indir=null, $newuser=null) {
	    $printername = $printername ? $printername : $this->printer_name;
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
        $cmd = $this->external_use ? $this->procmd.'confprinter':'confprinter';		
		
        if (!$printername) 
		    return ('Unknown printer!');		
		
		//echo '>',$this->username,':',$this->newuser,':',$newuser;
	    if ($this->username!=$this->get_printer_admin()) {
	
		    //$myuser = $newuser ? $newuser : $this->newuser; //<<<<<<<<<<<<<<<<<<<<the is no newuser
			//$file = $this->admin_path . md5($myuser) . '.token';		
			$file = $this->admin_path . md5($this->username) . '.token';		
			
            if ($this->url_activate) {
			    /*
			    //..test page submited, execute to allow app from web dropbox account
			    if ($job_id = $this->test_page) {
				
				   //echo 'TEST PAGE SUBMITED:'.$job_id; 
				   $exec = $this->process_job($job_id,$printername,$indir);
				   //echo ":", $exec; //must be true
				   //in case of true file has no renamed to complete..always pending..
				   if ($exec) {
				       //reset TESTPAGE
				       $this->test_page = null;
					   $_SESSION['TESTPAGE'] = null;
				   }
				}
			    */
			    //if ($_POST['dbusername']) //dropbox account data submited..step3
				
				if ($_POST['filtername']) //form submited..
				  $ret  = $this->html_show_instruction_page('config-post');
				//elseif ($_GET['oauth_token'])	//..returing from allow app procedure
			      //$ret  = $this->html_show_instruction_page('config-edit');
				else  //????
				  $ret  = $this->html_show_instruction_page('config-edit'); 
				  
                $ret .= $this->config_filter_form_taxcalc('taxcalc', $printername, $code, $indir);	
			}	
			elseif ($this->url_invitate) {
			    //echo 'b';
                if ($myuser) {	
				    self::_logout(); //<<<<<<<<LOGOUT TO RETURN AS NEW USER 
				    $ret  = $this->html_show_instruction_page('user-post');
					//no need
				    //$ret .= self::html_window(null, 'A mail send to you with account details. Use the link to activate this account.', $this->printer_name);
				}   
                else {				
				    $ret  = $this->html_show_instruction_page('user-error');
                    $ret .= $this->html_window("Error", null, $this->printer_name);//'Not a valid user!', $this->printer_name);			   
				}   
			}			
            else {
                //echo 'c';
				/*if ($this->newuser) {//$_POST['username']) {//after user submit only
				    $ret  = $this->html_show_instruction_page('user-post'); 
					$ret .= self::html_window(null, null, $this->printer_name);
                }				  
				else {*/
				if ($this->newuser)
				  $ret  = $this->html_show_instruction_page('user-post');
				
				  //if ($_POST['dbusername']) //config account data submited..step3
				  /*if ($_POST['filtername']) //config post data submited..
				    $ret  .= $this->html_show_instruction_page('config-post');
				  else
			        $ret  .= $this->html_show_instruction_page('config-edit');	
					*/
				  $ret .= $this->config_filter_form_taxcalc('taxcalc', $printername, $code, $indir);				
				//}
			}   
			   
			return ($ret);
		}   

        $ret = parent::form_configprinter($printername, $indir);  
		return ($ret);	
    }	
	
	//override for goto step 3
	protected function config_filter_form_taxcalc($filter=null, $printername=null, $code=null, $indir=null) {
	    $printername = $printername ? $printername : $this->printer_name;
	    $ver = $this->server_name . $this->server_version;
	    $dir = $indir ? $indir.'/' : ($_SESSION['indir'] ? $_SESSION['indir'] .'/' : '/');
		$filter = $_POST['filtername'] ? $_POST['filtername'] : $filter;
		$cmd = $this->external_use ? $this->procmd.'confprinter':'confprinter';
		$is_activation_process = is_array($this->url_activate) ? true : false;
		
		//$file = $_SERVER['DOCUMENT_ROOT'] .'/'.$dir . str_replace('.printer','',$printername).'.'.$filter.'.php';
		if ($this->username!=$this->get_printer_admin()) {
		    $myuser = $this->newuser ? $this->newuser : $this->username;
		    $userstr = '-'.$myuser;
		}	
		else {
		    $myuser = $this->username;
           	$userstr = null;	
		}	
			
        if (!is_dir($this->admin_path))
		    @mkdir($this->admin_path, 0755);			
			
		$file = $this->admin_path . $filter.$userstr.'-conf'.'.php';
		$adminfile = $this->admin_path . $filter.'-conf'.'.php'; //admin file to save during activation
		//echo $file,'>';
		
		
        //read file	args	
		if (is_readable($file)) {

            include($file);	
			
			$iwfile_src = $iwfile ? 'admin/'.$printername.'/'.$iwfile : null;

			$iautoresize = implode(',',$iautoresize);//set as string
            //$iftp_pathpersize = implode(',',$iftp_pathpersize);//set as string	
			if (!empty($iftp_pathpersize))
		  	    //$iftp_pathpersize = "'" . implode("','",$iftp_pathpersize). "'";//set as string	..keep ' in load
			    $iftp_pathpersize = implode(',',$iftp_pathpersize);
			else  
			    $iftp_pathpersize = ''; 
			
            //keep previous state			
			$dropbox_was_enabled = ($idropbox>0) ? true : false;	
        }	
		
		$readonly_at_activation = $is_activation_process ? '' : 'readonly'; //allow edit 1st time in activate 
		$readonly_at_use = $is_activation_process ? 'readonly' : ''; //allow edit only after activation 
		
		$iafm = $is_activation_process ? trim($this->url_activate[0]) : $iafm; //as readed
		$itaxuser_mail = $is_activation_process ? trim($this->url_activate[1]) : $itaxuser_mail; //as readed	
		$itax_getdata = $is_activation_process ? 1 : ($_POST['itax_getdata'] ? 1 : $itax_getdata);//as readed
		
		$readonly_at_refresh = $itax_getdata ? '' : 'readonly'; //allow edit only when data refresh 		
		
		//..only when needed NOT always..
		if (($is_activation_process) || ($itax_getdata)) { //only when url_activate or refresh...
          if ($stoixeia = $this->afm_check($iafm, true)) {
            $iactLongDescr = $stoixeia['actLongDescr']; //=> 'Περιγραφή Κύριας Δραστηριότητας',
			$ipostalZipCode = $stoixeia['postalZipCode'];//=> 'Ταχ. κωδικός Αλληλογραφίας',
			$ifacActivity = $stoixeia['facActivity'];// => 'Κύρια Δραστηριότητα',
			$iregistDate = $stoixeia['registDate'];// => 'Ημ/νία Έναρξης',
			$istopDate = $stoixeia['stopDate'];// => 'Ημ/νία Διακοπής',
			$idoyDescr = $stoixeia['doyDescr'];// => 'Περιγραφή ΔΟΥ',
			$iparDescription = $stoixeia['parDescription'];// => 'Περιοχή Αλληλογραφίας',
			$ideactivationFlag = $stoixeia['deactivationFlag'];// => 'Ένδειξη Απενεργ. ΑΦΜ',
			$ipostalAddressNo = $stoixeia['postalAddressNo'];// => 'Αριθμός Αλληλογραφίας',
			$ipostalAddress = $stoixeia['postalAddress'];// => 'Οδός Αλληλογραφίας',
			$idoy = $stoixeia['doy'];// => 'Κωδικός ΔΟΥ',
			$ifirmPhone = $stoixeia['firmPhone'];// => 'Τηλέφωνο Επιχείρησης',
			$ionomasia = $stoixeia['onomasia'];// => 'Επωνυμία',
			$ifirmFax = $stoixeia['firmFax'];// => 'Fax Επιχείρησης',
			$iafm = $stoixeia['afm'];// => 'ΑΦΜ',
			$icommerTitle = $stoixeia['commerTitle'];// => 'Τίτλος');	
             			
          }
		  //else error on web service halt..
		}
		
        //save user hash...as readed by ws of gsis..only when register
		if ($is_activation_process) {
            $check_hash = strtoupper(hash('sha1',$iafm.$ionomasia));
			$ok = @file_put_contents($this->admin_path . $this->printer_name.'.hash', $check_hash);
			
			$itaxserial = 'TXC' . strtoupper(hash('crc32',$check_hash));

			$itaxtimezone = 'Europe/Athens';
			
		    //get service subscriptions
		    $itaxsigner = $this->url_activate[6] ? '1' : '0';
		    $itaxfiscal = $this->url_activate[7] ? '1' : '0';
		    $itaxcprint = $this->url_activate[8] ? '1' : '0';			
		}	
        else {//check if is valid..changed
		    $check_hash = @file_get_contents($this->admin_path . $this->printer_name.'.hash');//as saved
			$read_hash = strtoupper(hash('sha1', $iafm.$ionomasia));//as readed 
			if ($check_hash==$read_hash) { 
			   $itaxserial = 'TXC' . strtoupper(hash('crc32',$check_hash));
			}   
			else {//data fetch ws error!!!
			   $itaxserial = $itaxserial ? $itaxserial : 'XXX00000000';//as conf readed or test tax serial
			} 
			
			$itaxtimezone = $itaxtimezone? $itaxtimezone : 'Europe/Athens';//as read

		    //read service subscriptions as is in conf file
		    $itaxsigner = $itaxsigner ? '1' : '0';
		    $itaxfiscal = $itaxfiscal ? '1' : '0';
		    $itaxcprint = $itaxcprint ? '1' : '0';					
        }		
		//?? doy can be modified !!!	
        //$itaxserial = 'TXC' . strtoupper(hash('crc32',strtoupper(hash('sha1', $iafm.$ionomasia))));//$iafm . $idoy)))); //tax machine serial number
		$itaxactive = $itaxsigner ? ($itaxactive ? $itaxactive : '0') : '0'; //only when signer service is on
        $itaxmode = $itaxmode ? $itaxmode : '0'; 		
		$itax_sign_x = $itax_sign_x ? $itax_sign_x : '0';	
        $itax_sign_y = $itax_sign_y ? $itax_sign_y : '0';		
		$itax_sign_mode = $itax_sign_mode ? $itax_sign_mode : '0';	
        $itax_sign_key = $itax_sign_key ? $itax_sign_key : '';		
        $itax_sign_prefix = $itax_sign_prefix ? $itax_sign_prefix : '';			
        $itax_sign_lines = $itax_sign_lines ? $itax_sign_lines : '1';			
        $itaxcopies = $itaxcopies ? $itaxcopies : '';
		//$itaxheader = /*$itaxheader ? $itaxheader :*/ //as readed
		if ($itax_getdata) {
		    $itaxheader = stripslashes(str_replace("'","",$ionomasia.'-'.$icommerTitle."<br>".$iactLongDescr."<br>".
		                                              $ipostalAddress." ".$ipostalAddressNo.'-'.$ipostalZipCode."<br>".
										              $this->afm_prefix . $iafm."-".$this->doy_prefix . $idoyDescr."<br>".
													  $ifirmPhone . ' ' . $ifirmFax));
		}
        else {//as readed
 		    $itaxheader = $itaxheader ? $itaxheader : '';
		}	
        $itaxautoz = $itaxautoz ? $itaxautoz : 0;	
        $itaxwrap = $itaxwrap ? $itaxwrap : '';		
		
		if ($filtername = $_POST['filtername']) {
		    //as readed by web service...
            $ionomasia = $_POST['ionomasia'] ? stripslashes(str_replace("'","",trim($_POST['ionomasia']))) : trim($ionomasia);
            $icommerTitle = $_POST['icommerTitle'] ? stripslashes(str_replace("'","",trim($_POST['icommerTitle']))) : trim($icommerTitle);
            $iactLongDescr = $_POST['iactLongDescr'] ? stripslashes(str_replace("'","",trim($_POST['iactLongDescr']))) : trim($iactLongDescr);		
            $iafm = $_POST['iafm'] ? stripslashes(str_replace("'","",trim($_POST['iafm']))) : trim($iafm);
            $idoyDescr = $_POST['idoyDescr'] ? stripslashes(str_replace("'","",trim($_POST['idoyDescr']))) : trim($idoyDescr);
            $ipostalAddress = $_POST['ipostalAddress'] ? stripslashes(str_replace("'","",trim($_POST['ipostalAddress']))) : trim($ipostalAddress);
            $ipostalAddressNo = $_POST['ipostalAddressNo'] ? stripslashes(str_replace("'","",trim($_POST['ipostalAddressNo']))) : trim($ipostalAddressNo);
            $iparDescription = $_POST['iparDescription'] ? stripslashes(str_replace("'","",trim($_POST['iparDescription']))) : trim($iparDescription);
            $ipostalZipCode = $_POST['ipostalZipCode'] ? stripslashes(str_replace("'","",trim($_POST['ipostalZipCode']))) : trim($ipostalZipCode);
            $ifirmPhone = $_POST['ifirmPhone'] ? stripslashes(str_replace("'","",trim($_POST['ifirmPhone']))) : trim($ifirmPhone);
            $ifirmFax = $_POST['ifirmFax'] ? stripslashes(str_replace("'","",trim($_POST['ifirmFax']))) : trim($ifirmFax);
            $itaxuser_mail = $_POST['itaxuser_mail'] ? stripslashes(str_replace("'","",trim($_POST['itaxuser_mail']))) : trim($itaxuser_mail); 			
		    $itax_getdata = $_POST['itax_getdata'] ? 1 : 0;
		
		    $itaxactive = $itaxsigner ? ($_POST['itaxactive'] ? stripslashes($_POST['itaxactive']) : '0') : '0';//only when signer srv is on
		    $itaxmode = $_POST['itaxmode'] ? stripslashes($_POST['itaxmode']) : '0';	
		    $itax_sign_mode = $_POST['itax_sign_mode'] ? stripslashes($_POST['itax_sign_mode']) : '0';	
            $itax_sign_key = $_POST['itax_sign_key'] ? stripslashes(str_replace("'","",trim($_POST['itax_sign_key']))) : '';		
            $itax_sign_prefix = $_POST['itax_sign_prefix'] ? stripslashes(str_replace("'","",trim($_POST['itax_sign_prefix']))) : '';				
			$itax_sign_lines = $_POST['itax_sign_lines'] ? stripslashes(str_replace("'","",trim($_POST['itax_sign_lines']))) : '1';
			$itax_sign_x  = (($xf = intval($_POST['itax_sign_x'])) && ($xf<2000)) ? $xf : '0';
			$itax_sign_y = (($yf = intval($_POST['itax_sign_y'])) && ($yf<2000)) ? $yf : '0';				
			$itaxcopies = $_POST['itaxcopies'] ? ($_POST['itaxcopies']<=100 ? stripslashes(str_replace("'","",trim($_POST['itaxcopies']))):100) : '';
			$itaxheader = $_POST['itaxheader'] ? str_replace("\r\n","<br>",stripslashes(str_replace("'","",trim($_POST['itaxheader'])))) : $itaxheader;
			$itaxautoz = $_POST['itaxautoz'] ? 1 : 0;
			$itaxwrap = $_POST['itaxwrap'] ? ($_POST['itaxwrap']<=100 ? stripslashes(str_replace("'","",trim($_POST['itaxwrap']))):100) : '';
			
			$iwalpha = $_POST['iwalpha'] ? 1 : 0;
            $iwposition = $_POST['iwposition'] ? $_POST['iwposition'] : 'null';
			$ioptimize = $_POST['ioptimize'] ? $_POST['ioptimize'] : '0';
			//print_r($_FILES['iwfile']);
			if (!empty($_FILES['iwfile']) && (!$_FILES['iwfile']['error'])) {//uploaded file
	            
		        $ufile = $_FILES['iwfile']['tmp_name'];	
				$rfile = $_FILES['iwfile']['name'];//str_replace(FILE_DELIMITER,'_',$_FILES['iwfile']['name']);				
				
				if ((stristr($rfile,'.jpg')) || (stristr($rfile,'.gif')) || (stristr($rfile,'.png')) ) {
				
					$iwfilename = $this->username . FILE_DELIMITER . 'watermark' . '.' . array_pop(explode('.',$rfile));			
	                //echo '>'. $iwfilename;
					
                    if (move_uploaded_file($ufile, $this->admin_path . $iwfilename)) {
					
					    $iwfile = $iwfilename;
						$iwfile_src = 'admin/'.$printername.'/'.$iwfilename;
						//echo '>'. $iwfilename . '>' . $iwfile;
					}	
					else	
                        $message = "Can not upload the file." . $_FILES['iwfile']['error']; 			
				}	
				else	
                    $message = "Invalid image type."; 					
			}			
			
			if (stristr($_POST['iautoresize'],',')) {
			  $iautoresize = explode(',',stripslashes($_POST['iautoresize']));
			  foreach ($iautoresize as $i=>$size)
			    $cp_size .= $size . ',';			
			}
			else
			    $cp_size = stripslashes($_POST['iautoresize']);
				
			$iftp_server = stripslashes($_POST['iftp_server']);
			$iftp_username = stripslashes($_POST['iftp_username']);
			$iftp_password = stripslashes($_POST['iftp_password']);
			$iftp_path = stripslashes($_POST['iftp_path']);
			
			if (stristr($_POST['iftp_pathpersize'],',')) {
			  $iftp_pathpersize = explode(',',stripslashes($_POST['iftp_pathpersize']));
			  foreach ($iftp_pathpersize as $i=>$sp)
			    $sp_path .= "'" . $sp ."',";
			}  
			else
			    $sp_path = "'" . stripslashes($_POST['iftp_pathpersize']) . "'";
				
			$idropbox = $_POST['idropbox'] ? 1 : 0; 
            $idbfolder = $_POST['idbfolder'] ? stripslashes($_POST['idbfolder']) : null; 						
			
		    //echo 'post';
		    $db_code = "<?php
\$itaxserial = '$itaxserial';
\$itaxtimezone = '$itaxtimezone';
\$itaxsigner = $itaxsigner;				
\$itaxfiscal = $itaxfiscal;
\$itaxcprint = $itaxcprint;
\$ionomasia = '$ionomasia';
\$icommerTitle = '$icommerTitle';
\$iactLongDescr = '$iactLongDescr';		
\$iafm = '$iafm';
\$idoyDescr = '$idoyDescr';
\$ipostalAddress = '$ipostalAddress';
\$ipostalAddressNo = '$ipostalAddressNo';
\$iparDescription = '$iparDescription';
\$ipostalZipCode = '$ipostalZipCode';
\$ifirmPhone = '$ifirmPhone';
\$ifirmFax = '$ifirmFax';
\$ifacActivity = '$ifacActivity';
\$iregistDate = '$iregistDate';
\$istopDate = '$istopDate';
\$ideactivationFlag = '$ideactivationFlag';
\$idoy = '$idoy';
\$itaxuser_mail = '$itaxuser_mail';
\$itax_getdata = '$itax_getdata';
\$itaxactive = $itaxactive;			
\$itaxmode = $itaxmode;	
\$itax_sign_x = $itax_sign_x;
\$itax_sign_y = $itax_sign_y;
\$itax_sign_mode = $itax_sign_mode;
\$itax_sign_key = '$itax_sign_key';
\$itax_sign_prefix = '$itax_sign_prefix';
\$itax_sign_lines = '$itax_sign_lines';
\$itaxcopies = '$itaxcopies';
\$itaxheader = '$itaxheader';
\$itaxautoz = '$itaxautoz';
\$itaxwrap = '$itaxwrap';
\$iwalpha = $iwalpha;
\$iwposition = $iwposition;
\$iwfile = '$iwfile';
\$iautoresize = array($cp_size);
\$ioptimize = $ioptimize;
\$iftp_server = '$iftp_server';
\$iftp_username = '$iftp_username';
\$iftp_password = '$iftp_password';
\$iftp_path = '$iftp_path';
\$iftp_pathpersize = array($sp_path);
\$idropbox = $idropbox;
\$idbfolder = '$idbfolder';
?>";

		    //save backup
			if ($old_data = @file_get_contents($file))
			    $y = @file_put_contents(str_replace('.php','.bak.php',$file), $old_data);
				
			//save file...	
		    if ($x = @file_put_contents($file, $db_code)) {			
			    //save admin file (only in activation as basic user)
			    if ($is_activation_process)
			        $z = @file_put_contents($adminfile, $db_code);	
				 
			    //override arrays to view as string
			    if (!empty($iautoresize))
		            $iautoresize = implode(',',$iautoresize) ;//set as string 
			    if (!empty($iftp_pathpersize))	
                    $iftp_pathpersize = implode(',',$iftp_pathpersize);//set as string dont keep ' at save
			  
                //goto step 3.....................................
			    if ($this->url_activate) {
			        $this->url_activate = null; //final step, disable activation process
			        $_SESSION['ACTIVATION'] = null;
			  
                    $form = $this->form_infoprinter(); 
		            return ($form);			
                }
			    elseif ($this->newuser) {
			        //go back yo native user
			        $this->newuser = null;
                    $_SESSION['new_user'] = null;	
				
				    $form = $this->form_infoprinter(); 
		            return ($form);		
			    }
           	    //else stay here		  
			    //}
                //else
                //echo 'error';	

                //if dropbox gonna be enabled...
                if ((!$dropbox_was_enabled) && ($idropbox)) {
			        //echo 'GOTO DROPBOX PAGE:',$this->username,'>',$printername,'<br>';		  
				    //goto dropbox page...
				    $this->enable_dropbox_jobs(null,$printername, $indir);  
                }

			    //if dropbox gonna be disabled...
			    if (($dropbox_was_enabled) && (!$idropbox)) { 
			        //echo 'DISABLE DROPBOX:',$this->username,'>',$printername,'<br>';
                    $this->disable_dropbox_jobs($printername, $indir); 			  
			    }
            }//save conf file
            else 
			    $message = "Unable to save conf file."; 		
		}//post	
        elseif ($_GET['oauth_token']) {	//..returing from allow app procedure
            //echo 'RETURN FROM DROPBOX:',$this->username,'>',$printername,'<br>';
            //send test page to the printer.....................for 2nd step dropbox allow
			if ($testpage_id = $this->send_test_page(null,$printername, $indir, $this->username)) {
              //return from dropbox page...save token...
			  $this->enable_dropbox_jobs($testpage_id,$printername, $indir);
            }			
		}
				
        //handle values to appear in form
		$itax_getdata_check = $itax_getdata ? "checked='checked'": null;	
		
		switch ($itaxactive) {
			case 1 : $iact_select_1 = "selected='selected'"; break; 
			case 2 : $iact_select_2 = "selected='selected'"; break; 
			case 3 : $iact_select_3 = "selected='selected'"; break; 
            case 0 :			
			default: $iact_select_0 = "selected='selected'"; 
		}
		
		switch ($itaxmode) {
			case 1 : $imod_select_1 = "selected='selected'"; break; 
			case 2 : $imod_select_2 = "selected='selected'"; break; 
			case 3 : $imod_select_3 = "selected='selected'"; break; 
            case 0 :			
			default: $imod_select_0 = "selected='selected'"; 
		}
		
		switch ($itax_sign_mode) {
			case 1 : $isignmod_select_1 = "selected='selected'"; break; 
			case 2 : $isignmod_select_2 = "selected='selected'"; break; 
			case 3 : $isignmod_select_3 = "selected='selected'"; break; 
            case 0 :			
			default: $isignmod_select_0 = "selected='selected'"; 
		}		
		
		$itaxheader_data = str_replace('<br>',"\r\n",$itaxheader);		
		$itaxautoz_check = $itaxautoz ? "checked='checked'": null;				
		
		$iwfile_image = $iwfile_src ? "<img src='$iwfile_src' width='128'>" : null;	
		$iwalpha_check = $iwalpha ? "checked='checked'": null;			
		
		switch ($iwposition) {
		    case 1 : $iwpos_select_1 = "selected='selected'"; break; 
			case 2 : $iwpos_select_2 = "selected='selected'"; break; 
			case 3 : $iwpos_select_3 = "selected='selected'"; break; 
			case 4 : $iwpos_select_4 = "selected='selected'"; break; 
			case 5 : $iwpos_select_5 = "selected='selected'"; break; 
			case 0 :
			default: $iwpos_select_0 = "selected='selected'"; 
		}	

		switch ($ioptimize) {
			case 1 : $iopt_select_1 = "selected='selected'"; break; 
            case 0   :			
			default: $iopt_select_0 = "selected='selected'"; 
		}		

        $idropbox_check = $idropbox ? "checked='checked'": null;	

		//dropbox form addon when no new user ........????
        if (!$this->newuser) {
		    $form_dropbox = '
        <li class="section_break"><h2>Dropbox integration</h2><p>Enable dropbox integration.
		This service requires a Dropbox service to be installed on your system. If you don\'t have a Dropbox account, 
		<a href="http://db.tt/Pd430oY0" target=\'_blank\'>create a dropbox account.</a></p></li>
        <li id="li_98" >
		<label class="description" for="element_98">Enable Dropbox</label>
		<span>
		<input id="element_98" name="idropbox" class="element checkbox" type="checkbox" value="1" '.$idropbox_check.'/>
        <label class="choice" for="element_98">Dropbox</label>
		</span><p class="guidelines" id="guide_98"><small>Dropbox integration.</small></p> 
		</li>
		<li id="li_99" >
		<label class="description" for="element_99">Dropbox inbox name </label>
		<div>
			<input id="element_99" name="idbfolder" class="element text medium" type="text" maxlength="20" value="'.$idbfolder.'"/> 
		</div><p class="guidelines" id="guide_99"><small>Please specify a dropbox folder to save outputs</small></p> 
		</li>			
';			
        }
        else
            $form_dropbox = null;		

	    $form = <<<EOF
		<form id="form_printer_conf" class="appnitro" enctype="multipart/form-data" method="post" action="">
		<div class="form_description">
			<h2>Printer settings. $message</h2>
			<p>Printer configuration.</p>
		</div>						
		<ul >		
		
        <!--li class="section_break"><h2>Tax machine</h2><p>Tax machine data.</p></li-->
		
		<li id="li_0" >
		<label class="description" for="element_0">Serial No</label>
		<div>
			<input id="element_0" name="itaxserial" class="element text medium" type="text" maxlength="12" value="$itaxserial" readonly/> 
		</div><p class="guidelines" id="guide_0"><small>Registartion serial number</small></p> 
		</li>
		
		<li id="li_1" >
		<label class="description" for="element_1">Timezone</label>
		<div>
		<select class="element select medium" id="element_1" name="itaxtimezone"> 
		<option value="Europe-Athens">Europe/Athens</option>
		</select>
		</div><p class="guidelines" id="guide_1"><small>Select timezone</small></p> 
		</li>			

        <li id="li_4" >
		<label class="description" for="element_4">User tax data </label>
		
		<div class="left">
			<input id="element_4_4" name="ionomasia" class="element text medium" value="$ionomasia" type="text" readonly>
			<label for="element_4_4">Name</label>
		</div>		
		
		<div class="right">
			<input id="element_4_1" name="icommerTitle" class="element text large" value="$icommerTitle" type="text" readonly>
			<label for="element_4_1">Title</label>
		</div>			
		
		<div>
			<input id="element_4_1" name="iactLongDescr" class="element text large" value="$iactLongDescr" type="text" readonly>
			<label for="element_4_1">Activities</label>
		</div>	

		<div class="left">
			<input id="element_4_4" name="iafm" class="element text medium" value="$iafm" type="text" readonly>
			<label for="element_4_4">VAT</label>
		</div>		
		
		<div class="right">
			<input id="element_4_1" name="idoyDescr" class="element text large" value="$idoyDescr" type="text" readonly>
			<label for="element_4_1">Doy</label>
		</div>		
		
		<div class="left">
			<input id="element_4_1" name="ipostalAddress" class="element text large" value="$ipostalAddress" type="text" readonly>
			<label for="element_4_1">Street Address</label>
		</div>
	
		<div class="right">
			<input id="element_4_2" name="ipostalAddressNo" class="element text large" value="$ipostalAddressNo" type="text" readonly>
			<label for="element_4_2">Address Line 2</label>
		</div>
	
		<div class="left">
			<input id="element_4_3" name="iparDescription" class="element text medium" value="$iparDescription" type="text" readonly>
			<label for="element_4_3">City</label>
		</div>
	
		<div class="right">
			<input id="element_4_4" name="ipostalZipCode" class="element text medium" value="$ipostalZipCode" type="text" readonly>
			<label for="element_4_4">Postal / Zip Code</label>
		</div>
	
		<div class="left">
			<input id="element_4_5" name="ifirmPhone" class="element text medium" maxlength="15" value="$ifirmPhone" type="text" readonly>
			<label for="element_4_5">Telephone</label>
		</div>	

		<div class="right">
			<input id="element_4_6" name="ifirmFax" class="element text medium" value="$ifirmFax" type="text" readonly>
			<label for="element_4_6">Fax</label>
		</div>		
		</li>
		
        <li id="li_3" >
		<label class="description" for="element_3">Email </label>
		<div>
			<input id="element_3" name="itaxuser_mail" class="element text medium" type="text" maxlength="255" value="$itaxuser_mail" $readonly_at_use/> 
		</div><p class="guidelines" id="guide_3"><small>Set e-mail</small></p>
		</li>

        <li id="li_4" >
		<label class="description" for="element_4">Refresh data</label>
		<span>
		<input id="element_4" name="itax_getdata" class="element checkbox" type="checkbox" value="1" $itax_getdata_check />
        <label class="choice" for="element_4">Refresh</label>
		</span><p class="guidelines" id="guide_4"><small>Refresh data</small></p> 
		</li>		
				
		<li class="section_break"><h2>General</h2><p>Basic configuration.</p></li>
		
		<li id="li_2" >
		<label class="description" for="element_2">Active</label>
		<div>
		<select class="element select medium" id="element_2" name="itaxactive"> 
		<option value="0" $iact_select_0 >No</option>
        <option value="1" $iact_select_1 >Yes</option>		
        <option value="2" $iact_select_2 >Disabled</option>		
        <option value="3" $iact_select_3 >Locked</option>
		</select>
		</div><p class="guidelines" id="guide_2"><small>Set status</small></p> 
		</li>		
		<li id="li_3" >
		<label class="description" for="element_3">Mode</label>
		<div>
		<select class="element select medium" id="element_3" name="itaxmode"> 
		<option value="0" $imod_select_0 >Test mode</option>
        <option value="1" $imod_select_1 >Normal mode</option>		
        <option value="2" $imod_select_2 >Debug mode</option>		
        <option value="3" $imod_select_3 >Advance mode</option>
		</select>
		</div><p class="guidelines" id="guide_3"><small>Set mode</small></p> 
		</li>		
		<li id="li_4" >
		<label class="description" for="element_4">Sign Mode</label>
		<div>
		<select class="element select medium" id="element_4" name="itax_sign_mode"> 
		<option value="0" $isignmod_select_0 >None</option>
        <option value="1" $isignmod_select_1 >Per page</option>		
        <option value="2" $isignmod_select_2 >Per document</option>		
        <option value="3" $isignmod_select_3 >Advance mode</option>
		</select>
		</div><p class="guidelines" id="guide_4"><small>Set sign mode</small></p> 
		</li>
		<li id="li_5" >
		<label class="description" for="element_5">Sign key</label>
		<div>
			<input id="element_5" name="itax_sign_key" class="element text medium" type="text" maxlength="12" value="$itax_sign_key"/> 
		</div><p class="guidelines" id="guide_5"><small>Set sign key</small></p> 
		</li>
		<li id="li_6" >
		<label class="description" for="element_6">Sign prefix</label>
		<div>
			<input id="element_6" name="itax_sign_prefix" class="element text medium" type="text" maxlength="12" value="$itax_sign_prefix"/> 
		</div><p class="guidelines" id="guide_6"><small>Set sign prefix</small></p> 
		</li>	
		<li id="li_7" >
		<label class="description" for="element_7">Sign lines</label>
		<div>
			<input id="element_7" name="itax_sign_lines" class="element text medium" type="text" maxlength="12" value="$itax_sign_lines"/> 
		</div><p class="guidelines" id="guide_7"><small>Set sign lines</small></p> 
		</li>		
		<li id="li_8" >
		<label class="description" for="element_8">Sign position</label>
		<span>
			<input id="element_81" name= "itax_sign_x" class="element text" maxlength="4" size="8" value="$itax_sign_x"/>
			<label>X position</label>
		</span>
		<span>
			<input id="element_82" name= "itax_sign_y" class="element text" maxlength="4" size="8" value="$itax_sign_y"/>
			<label>Y position</label>
		</span><p class="guidelines" id="guide_8"><small>Place in page</small></p> 
		</li>
		<li id="li_9" >
		<label class="description" for="element_9">Copies</label>
		<div>
			<input id="element_9" name="itaxcopies" class="element text medium" type="text" maxlength="2" value="$itaxcopies"/> 
		</div><p class="guidelines" id="guide_9"><small>Set document copies</small></p> 
		</li>	
        <li id="li_10" >
		<label class="description" for="element_10">Tax header</label>
		<div>
			<textarea id="element_10" name="itaxheader" class="element textarea medium" $readonly_at_activation>$itaxheader_data</textarea> 
		</div><p class="guidelines" id="guide_10"><small>Tax header</small></p> 
		</li>
        <li id="li_11" >
		<label class="description" for="element_11">Auto Z</label>
		<span>
		<input id="element_11" name="itaxautoz" class="element checkbox" type="checkbox" value="1" $itaxautoz_check />
        <label class="choice" for="element_11">Auto Z</label>
		</span><p class="guidelines" id="guide_11"><small>Automated Z</small></p> 
		</li>	
		<li id="li_12" >
		<label class="description" for="element_12">Wrap Text</label>
		<div>
			<input id="element_12" name="itaxwrap" class="element text medium" type="text" maxlength="2" value="$itaxwrap"/> 
		</div><p class="guidelines" id="guide_12"><small>Wrap text</small></p> 
		</li>			
		
		<li class="section_break"><h2>Watermark</h2><p>Add watermark.</p></li>
		
		<li id="li_13" >
		<label class="description" for="element_13">Watermark position </label>
		<div>
		<select class="element select medium" id="element_13" name="iwposition"> 
		<option value="0" $iwpos_select_0 ">No watermark</option>
        <option value="1" $iwpos_select_1 >Up left</option>
        <option value="2" $iwpos_select_2 >Up right</option>
        <option value="3" $iwpos_select_3 >Down left</option>
        <option value="4" $iwpos_select_4 >Down right</option>
        <option value="5" $iwpos_select_5 >Center</option>
		</select>
		</div><p class="guidelines" id="guide_13"><small>Select where to place the watrmark</small></p> 
		</li>
        <li id="li_12" >
		<label class="description" for="element_12">Upload a watermark file </label>
		<div>
		<input id="element_12" name="iwfile" class="element file" type="file"/> 
		</div> <p class="guidelines" id="guide_12"><small>Select a watermark image file to merge inito your source image. Please upload a smaller image than can fit into your source images.</small></p> 
		</li>
		<li id="li_13" >
		<!--label class="description" for="element_13">Watermark file:$iwfile </label-->
		<img src="$iwfile_src" width="128">
		</li>			
		<li id="li_12" >
		<label class="description" for="element_12">Opacity </label>
		<div>
			<input id="element_12" name="iwopacity" class="element text medium" type="text" maxlength="3" value="$iwopacity"/> 
		</div><p class="guidelines" id="guide_12"><small>Specify watermark opacity rate (0..100)</small></p> 
		</li>
        <li id="li_11" >
		<label class="description" for="element_11">Alpha transparency</label>
		<span>
		<input id="element_11" name="iwalpha" class="element checkbox" type="checkbox" value="1" $iwalpha_check />
        <label class="choice" for="element_11">Alpha transparency</label>
		</span><p class="guidelines" id="guide_11"><small>Use Alpha transparency (Ignore opacity).</small></p> 
		</li>				

        <li class="section_break"><h2>Image resizing</h2><p>Automate image resizing.</p></li>
		
		<li id="li_2" >
		<label class="description" for="element_2">Resize </label>
		<div>
			<input id="element_2" name="iautoresize" class="element text medium" type="text" maxlength="20" value="$iautoresize"/> 
		</div><p class="guidelines" id="guide_2"><small>Please specify autoresize x value separated by commas (500,200,100)</small></p> 
		</li>
		<li id="li_22" >
		<label class="description" for="element_22">Dimension </label>
		<div>
		<select class="element select medium" id="element_22" name="ioptimize"> 
        <option value="0" $iopt_select_0 >is width</option>		
        <option value="1" $iopt_select_1 >is height</option>		
		</select>
		</div><p class="guidelines" id="guide_22"><small>Specify the dimension type.</small></p>
		</li>			
		
        <li class="section_break"><h2>Ftp Account</h2><p>Enable ftp auto uploading.</p></li>
		
		<li id="li_3" >
		<label class="description" for="element_3">Ftp Server</label>
		<div>
			<input id="element_3" name="iftp_server" class="element text medium" type="text" maxlength="20" value="$iftp_server"/> 
		</div><p class="guidelines" id="guide_3"><small>Please specify ftp server address to automatically upload the files. Otherwise leave it blank</small></p> 
		</li>	
		<li id="li_4" >
		<label class="description" for="element_4">Ftp Username</label>
		<div>
			<input id="element_4" name="iftp_username" class="element text medium" type="text" maxlength="50" value="$iftp_username"/> 
		</div><p class="guidelines" id="guide_4"><small>Please specify ftp account username</small></p> 
		</li>
		<li id="li_5" >
		<label class="description" for="element_5">Ftp Password</label>
		<div>
			<input id="element_5" name="iftp_password" class="element text medium" type="text" maxlength="50" value="$iftp_password"/> 
		</div><p class="guidelines" id="guide_5"><small>Please specify ftp account password</small></p> 
		</li>
		<li id="li_6" >
		<label class="description" for="element_6">Ftp Path</label>
		<div>
			<input id="element_6" name="iftp_path" class="element text medium" type="text" maxlength="50" value="$iftp_path"/> 
		</div><p class="guidelines" id="guide_6"><small>Please specify ftp path</small></p> 
		</li>	
		<li id="li_7" >
		<label class="description" for="element_7">Ftp subpath per size</label>
		<div>
			<input id="element_7" name="iftp_pathpersize" class="element text medium" type="text" maxlength="50" value="$iftp_pathpersize"/> 
		</div><p class="guidelines" id="guide_7"><small>Please specify ftp path per size separated by commas (large,medium,small)</small></p> 
		</li>

        $form_dropbox		
		
        <li class="section_break"></li>
		
		<li class="buttons">
				<input type="hidden" name="ifacActivity" value="$ifacActivity" />
				<input type="hidden" name="iregistDate" value="$iregistDate" />
		        <input type="hidden" name="istopDate" value="$istopDate" />
				<input type="hidden" name="ideactivationFlag" value="$ideactivationFlag" />
				<input type="hidden" name="idoy" value="$idoy" />				
		
		        <input type="hidden" name="MAX_FILE_SIZE" value="500000" />
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="$cmd" />			    
				<input type="hidden" name="filtername" value="$filter" />
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
		</ul>
		</form>	
EOF;

        //no buttons while in activation mode
        $nobuttons = $is_activation_process;// is_array($this->url_activate) ? true : false; 

        $ret  = $this->html_window("Printer Configuration", $form, $this->printer_name, $nobuttons);			
        return ($ret);		
	}

	//override
	public function form_infoprinter($printername=null, $indir=null) {
	    $printername = $printername ? $printername : $this->printer_name;
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
		
        if ($this->username!=$this->get_printer_admin()) {		
		
            $filter = 'taxcalc';			
		    $myuser = $this->url_invitate ? $this->newuser : ($this->newuser ? $this->newuser : $this->username);
			
			if ($myuser) {
		        $userstr = '-'.$myuser;
			    $file = $this->admin_path . $filter.$userstr.'-conf'.'.php';
				
				//FILE NOW IS THE DROPBOX TOKEN FILE CREATED DURING 'ALLOW APP' PROCEDURE
				//$file = $this->admin_path . md5($myuser) . '.token';
				
                if (is_readable($file)) {	//if conf file
				
		            $joblist = self::html_get_printer_jobs_info();
		            $ret  = $this->html_window("Printer Queue", $joblist, $this->printer_name);	
                    //$ret .= $this->html_show_instruction_page('job-list');	
                }
				else {
				
		            $ret  = $this->html_show_instruction_page('config-error');
		            $ret .= $this->html_window("Error", 'Invalid configuration.', $this->printer_name);//'Not a valid dropbox account!', $this->printer_name);
			    }
		        return ($ret);				
			}
            else {
			    $ret  = $this->html_show_instruction_page('user-error');
                $ret .= $this->html_window("Error", null, $this->printer_name);//'Not a valid user!', $this->printer_name);  		
				return ($ret);			
            }			
		 
		}//if
		
		$ret = parent::form_infoprinter($printername, $indir);
		return ($ret);
    }	
	
	//override  
	protected function html_get_printer_menu($iconsview=null, $p=null) {
		//$urlicons = 'icons/';	
	    //if custom printer dir icon... 
		$urlicons = strstr($this->icons_path, $this->printer_name) ? 'icons/'.$this->printer_name.'/' : 'icons/';	
		
        $icons = array();		
		$user = $this->username ? $this->username : $_SESSION['user'];
		$indir = $_SESSION['indir'] ? $_SESSION['indir'] : $_GET['indir'];
		
		if ($this->username!=$this->get_printer_admin()) {
		    //choose icons
		    if (($this->url_invitate) || ($this->url_activate) || ($this->newuser)) { 
		        $icons[] = $this->urlpath."?".$this->cmd."useprinter:one";
			    $icons[] = $this->urlpath."?".$this->cmd."confprinter:two";
                $icons[] = $this->urlpath."?".$this->cmd."infprinter:three";		
			    //$icons[] = $this->urlpath."?".$this->cmd."logout:logout"; //<<no logout button when activation, invitation, new user
			}
			else {
			    //if ($this->signer_service)//eafdss service enabled
		           $icons[] = $this->urlpath."?".$this->cmd."useprinter:Printer Users";
				   
			    $icons[] = $this->urlpath."?".$this->cmd."confprinter:Printer Configuration";
                $icons[] = $this->urlpath."?".$this->cmd."infprinter:Printer Info";	
				
				if ($this->fiscal_service)//tacfiscal service enabled..toggle button
				   $icons[] = $this->fiscal_on ? 'taxprinter.php:Tax printer' : 'taxfiscal.php:Tax fiscal';
				if ($this->cprint_service)
				   $icons[] = $this->urlpath."?".$this->cmd."services:Tax services";   
				   
			    $icons[] = $this->urlpath."?".$this->cmd."logout:logout";			
			}
			
		    //RENDER ICONS
		    if ($iconsview) {
		        //print_r($icons);
		        foreach ($icons as $icon) { 
			
			        $icondata = explode(':',$icon);
			
			        if (is_file($this->icons_path.$icondata[1].'.png'))
			          $ifile = $urlicons.$icondata[1].'.png';
			        else
			          $ifile = $urlicons.'index.printer.png';
			   
			        $icco[] = "<a href='".$icondata[0]."'><img src='" . $ifile."' border=0 alt='".$icondata[1]."'></a>";
			        //$link = "<a href='".$icondata[0]."'>" . $icondata[1]  ."</a>";
			        $px = $p ? $p : '25%';
	                $attr[] = 'left;'.$px;
			    }	
                //print_r($icco);			
			    $ret = self::printline($icco,$attr,0,"center::100%::0::group_article_body::left::0::0::");			
		    }
		
		    return ($ret);			
		}
		
		$ret = parent::html_get_printer_menu($iconsview,$p);
		return($ret);
    }
	
	//overridde for conf personal tax printer
    public function html_mod_printer($bootstrapfile=null,$name=null, $auth=null, $quota=null, $users=null, $indir=null) {

        if ((!$name) || (!$bootstrapfile))
            return false;	
     
        $params = $this->parse_printer_file(null, null, $bootstrapfile);		
        //print_r($params);	
		if ($quota>1)
          $quota = $params['quota'] + $quota; //addon		
	    //else reset to 1
		
        $pauth = $auth ? ",'$auth'" : ",'".$params['auth']."'"; //as is
		$pquota = $quota ? ",$quota" : ','.$params['quota']; //as is
		
		if (empty($users)) 
		  $users = (array) $params['users'];
		
		$pusers = ",array(";
		foreach ($users as $username=>$password)
		  $pusers .= "'" . $username ."'=>'".$password."',";
		$pusers .= ")";	

	    $code1 = "<?php
include ('../../printers/ippserver/ListenerIPP.php');
\$listener = new IPPListener('$name' $pauth $pquota $pusers);
\$listener->ipp_send_reply(); 
?>";		  

  
		$ret = file_put_contents($bootstrapfile, $code1);
		
		//echo $file,'>',$indir;
		//$ret .=  seturl('t=modprinter','..Config..');
		
        return ($ret);		
	}


	
	//////////////////////////////////////////////////////////////////////////////////////////////////
	
	//simulate fhm receipt..
	protected function save_fhm($data=null, $customer_id=null) {
	    if (!$data) return false;
		$fhm_cid = $customer_id ? "CID:".$customer_id : null; 		
		
	    $filter = 'taxcalc';
        $date_time = $this->post_date_time();
        $fhm_header = "\nΝΟΜΙΜΗ ΑΠΟΔΕΙΞΗ - ΕΝΑΡΞΗ\n";
        $fhm_footer = "\nΝΟΜΙΜΗ ΑΠΟΔΕΙΞΗ - ΛΗΞΗ\n\n***ΕΥΧΑΡΙΣΤΟΥΜΕ ΠΟΛΥ***\n";	

		$sum=0; 
        $data_to_print = null; 		
        $ln = explode('|',$data);
		foreach ($ln as $line) {
            $rn = explode(':',$line);		
		    $data_to_print .= sprintf("% 12s",$rn[0]) .
		                      sprintf("% 5s",$rn[1]) .
							  sprintf("% 20s",$rn[2]) . PHP_EOL;
		  	$sum+=floatval($rn[2]);				
		} 
        $data_to_print .= "----------------------------------------" . PHP_EOL;
        $data_to_print .= "ΣΥΝΟΛΟ:" . sprintf("% 30s",$sum) . PHP_EOL;
        $data_to_print .= $fhm_cid . PHP_EOL;		
		//print_r($rn);  
	
	    /*if ($this->username!='admin')
	        $config_file = $this->admin_path . $filter.'-'.$this->username.'-conf'.'.php';
	    else //admin
            $config_file = $this->admin_path . $filter.'-conf'.'.php';*/
        //local include
	    if (is_readable($this->config_file)) {
	        include($this->config_file);	
			
			//kepp user mail to the session for renew, etc purposes...
			if (!$this->tax_user_email)
			    $_SESSION['itaxusermail'] = $itaxuser_mail;

            $taxowner = iconv("UTF-8", "ISO-8859-7", str_replace("<br>",PHP_EOL,$itaxheader)); 			
		
	        //$pid = sprintf("%04s", strval($id));
            //$pgap =  sprintf("% 4s", '');	
	        //$psid = sprintf("%08s", strval($sid));
	        $fhm_data = $fhm_header . PHP_EOL .
			            $taxowner  . PHP_EOL . 
		                $data_to_print . PHP_EOL . PHP_EOL .
                        $date_time . PHP_EOL .  					
					    $itaxserial . PHP_EOL .
					    $fhm_footer . PHP_EOL; 
					
            if ($wrap = intval($itaxwrap)) {//wrap text	
                $ret = wordwrap($fhm_data, $wrap ,PHP_EOL , true);
            }
            else
			    $ret = $fhm_data;
			
		    return ($ret);			
		}
        return false;		
	}	
	
	protected function get_printer_limit(&$message) {
	    $bootstrapfile = $_SERVER['DOCUMENT_ROOT'] .'/'.$this->defdir.'/index.php';	
	    //echo '<br>'.$bootstrapfile;
		$ret = false;
		//set quota values
        $this->user_quota = self::get_user_quota();
		$this->printer_quota = self::get_printer_quota(null,null,$bootstrapfile);
		
        $item = 'taxcloud@'.$this->username.'@'.$this->printer_name;  		
        $renew_link = "<a href='http://www.stereobit.gr/download.php?g=$item'>" . 'Feed the Printer'. "($this->username : $this->user_quota > $this->printer_quota)"  ."</a>";		
		
		//echo $this->username,':',$this->user_quota,'>',$this->printer_quota,'<br>';
		if (intval($this->user_quota) > $this->printer_quota) {
		
		    //...renew form		   
			   
			$message = $this->html_window("Renew", $renew_link, $this->printer_name,true);
			return true;//($ret); ...stop procced jobs...
		}
		else { //send warning
		   
		    $qdiff = abs($this->printer_quota - $this->user_quota);
			//echo $qdiff % 10;//<<10th time
			if ($qdiff<=1) {
			    //...renew form 
				$message = $this->html_window("Renew", $renew_link, $this->printer_name,true);
			}
			elseif ($qdiff<10) {//10 left..send/show warning
			
			    if ($ok = $this->mail_printer_limit($qdiff, $renew_link)) {
				
				    //...warning/renew form
			        $form = '
		<form id="form_quota_warning" class="appnitro" enctype="multipart/form-data" method="post" action="">
		<div class="form_description">
			<h2>Quota warning</h2>
			<p>User quota reach its limit very soon. Please renew.</p>
		</div>						
		<ul >			   
        <li id="li_1" >
		<div class="left">
			<input id="element_1_1" name="ipq" class="element text medium" value="'.$this->printer_quota.'" type="text" readonly>
			<label for="element_1_1">Printer quota</label>
		</div>		
		<div class="right">
			<input id="element_1_2" name="iuq" class="element text large" value="'.$this->user_quota.'" type="text" readonly>
			<label for="element_1_2">User quota</label>
		</div>	
		</li>'.
		$renew_link.
		'</ul>
		</form>';
		    
			        
			        $message = $this->html_window("Quota Warning ", $form, $this->printer_name, true);
				}	   
			}
			elseif (($qdiff<50) && ($qdiff % 10 === 0))  {//on 10th send warning..
			
			    $ok = $this->mail_printer_limit($qdiff, $renew_link);
				$message = $this->html_window("Quota Warning ", "<h2>REMAINING:".$qdiff.'</h2>', $this->printer_name,true);
			}
			//else no message
		}

        return false;		
	}
	
	protected function mail_printer_limit($timesleft=null, $rlink=null) {
	    $tdiff = $timesleft ? $timesleft : 0;
		$renew_link = $rlink ? $rlink : null;
	
	    if (!$mail = $this->tax_user_email) 
		    return false;
	
        //send mail
		$from = /*$this->printer_name*/'taxcloud.printer' . '@' . str_replace('www.','',$_ENV["HTTP_HOST"]);		
		$subject = /*$this->printer_name .*/'Taxcloud quota warning';
        $message = $this->html_show_instruction_page('send-quota',array('[PRINTERNAME]','[USERNAME]','[USERQUOTA]','[PRINTERQUOTA]','[LIMIT]','[RENEWLINK]'),
		                                                         array($this->printer_name, $this->username, $this->user_quota, $this->printer_quota, $tdiff, $renew_link),
																 true);																		 
		
        $ok = $this->_sendmail($from,$mail,$subject,$message);
		//notify
        $ok2 = $this->_sendmail($from,$this->notify_mail,$subject, $mail . $message);		
		
		return ($ok);
	}
	
	//send data in queue
    public function add_job_data($data=null, $action=null) {
	    $printername = $this->printer_name;	
	    $dir = $this->defdir;
        $username = $this->username;
		$data = $data ? $data : "test page";
		$message = null;
		//if action is a mail or customer code..save it inside fhm..auto pickit it when in queue
		//and execute actions...
		//else pass action as is, job-attr for execution..
		$customer_id = null;//if ($action is mail or taxcode)  
		
        if ((!$username) || (!$printername))		
		    return false;
			
		//user's printer quota check	
		if ($renew = $this->get_printer_limit($message)) {	
		    //echo $message;//renew;
			return ($message);//false;//stop proceed jobs
		}	
		//else //just warning
	        //echo $message;//..MOVED
			
		$job_id = self::_get_job_id(); 
	    $name = 'fiscal_'.sprintf("%08s", strval($job_id)).'.txt';//'fiscal.txt';  
		
        $jobname = str_replace(FILE_DELIMITER,'_',$name);
				   
		$jobtitle = 'job'.FILE_DELIMITER.
		            $job_id.FILE_DELIMITER.
		            str_replace(':','~',$_SERVER['REMOTE_ADDR']).FILE_DELIMITER.
					$username.FILE_DELIMITER.
					$jobname;
					
		$job_file = $this->jobs_path . $jobtitle;			
				
        if ($fp = fopen($job_file, "w")) {//create it
   
            $fhm_data = $this->save_fhm($data, $customer_id);//<<<<cus id
   
		    $ok = fwrite($fp, $fhm_data, strlen($fhm_data));
            fclose($fp);
			//echo 'Create file:'.$this->jobs_path . $jobtitle;
		}
		//else
		  // echo "Error:".$file;
		
		//add quota
		if ($ok) {
		  //add quota job...
		  self::set_user_quota(1,$username,$printername, $dir);
		  
		  //fire-up agent to proceed just entered job..................
		  $log .= $this->html_proceed_printer_job($job_id, $jobtitle, $action); //,true =read whole dir
		  //echo 'LOG:',$log;
		  
		  if ($this->fiscal_log_show) { 
		    $log .= '<br>' . GetParam('receipt').':'.GetParam('taxact');
		    $message .= $this->html_window("Fiscal Log", $log, $this->printer_name, true);
		  }	
		  
		  //return ($job_id);
		  return ($message);//<<<<<<<<RENEW MESSAGE..
		}  

	    return false;
    }	
	
	//override to process job just submited 
	protected function html_proceed_printer_job($job_id=null, $job_file_name=null, $job_action=null, $proceed_all_jobs=false) {
	    if (!$job_id) return false;
		
	    if ((!$job_file_name) || ($proceed_all_jobs)) {
		    //echo 'all jobs:';
			//..can procced all rending jobs, reading dir, not only job id..TOO SLOW...
		    $ret = parent::html_proceed_printer_job($job_id);
		    return ($ret);
		}
	
	    $job_attr['job-id'] = $job_id;//$pf[1];
	    $job_attr['remote-ip'] = $_SERVER['REMOTE_ADDR'];//str_replace('~',':',$pf[2]);//'FISCALAPI';
	    $job_attr['user-name'] = $this->username;//$pf[3];
		$job_attr['job-name'] = 'fiscal_'.sprintf("%08s", strval($job_id)).'.txt';//$pf[4];
		//add custom job-action attribute to send receipt to customer..(mailto:balexiou@stereobit.com, dropbox:xxyyzz)
        $job_attr['job-action'] = $job_action ? $job_action : null;
		
        if (is_readable($this->jobs_path . $job_file_name)) {

            $ftime = $this->getthemicrotime();		
			
	        //echo '<br>FILE:'.$job_file_name; 
		    if ((class_exists('AgentIPP', true)) && ($this->username)) {//ONLY IF USERNAME..GET JOBS PER USER
		        $srv = new AgentIPP($this->authentication,
			                    $this->printer_name,
			                    $this->username,
			                    null,null,true, true);//<<<manual run...
			   
		        $ret = "Print agent initialized!";
				
				$ret .= $srv->process_job($job_id, $job_file_name, $job_attr);//, true);//<<silent mode
				
		    } 
            else 
              $ret = "Print agent failed to initialized!";
			  
			$ttime =  $this->getthemicrotime() - $ftime;	  
			$ret .= "<br>Agent elapsed: ".$ttime. " seconds<br>";
		}

        return ($ret);		
	}
	
    public function get_tax_id() {
	
	    $ret = $this->username ? $this->username : 'DEMO';
	    
	    return $ret;
	}	
	
    protected function post_date_time() {
 
        $date_time = date('r');//'d-m-Y H:i:s'); //rfc2822 
	    $dst = date('I');
	    $d = date('D d M Y');
	    $t = date('H:i'); //e timezone

    
	    //$greek_d = ...
	    $_dst = $dst ? ' ΚΩ:' : ' ΧΩ:';
	    $tt = $d . $_dst . $t;
	
	    return ($tt);
    }

    protected function getthemicrotime() {
   
     list($usec,$sec) = explode(" ",microtime());
     return ((float)$usec + (float)$sec);
    }  	
	
}
};
?>
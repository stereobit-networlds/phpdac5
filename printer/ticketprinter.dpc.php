<?php
$__DPCSEC['TICKETPRINTER_DPC']='1;1;1;1;1;1;1;1;1';

if (!defined("TICKETPRINTER_DPC")) {
define("TICKETPRINTER_DPC",true);

$__DPC['TICKETPRINTER_DPC'] = 'ticketprinter';

$a = GetGlobal('controller')->require_dpc('printer/UiIPPticket.lib.php');
require_once($a);

$__EVENTS['TICKETPRINTER_DPC'][0]='ticketprinter';
$__EVENTS['TICKETPRINTER_DPC'][1]='ticketshow';
$__EVENTS['TICKETPRINTER_DPC'][2]='ticketxml';
$__EVENTS['TICKETPRINTER_DPC'][3]='ticketjobstats';
$__EVENTS['TICKETPRINTER_DPC'][4]='ticketnetact';
$__EVENTS['TICKETPRINTER_DPC'][5]='ticketlogout';
$__EVENTS['TICKETPRINTER_DPC'][6]='ticketjobs';
$__EVENTS['TICKETPRINTER_DPC'][7]='ticketjobstats';
$__EVENTS['TICKETPRINTER_DPC'][8]='ticketdeljobs';
$__EVENTS['TICKETPRINTER_DPC'][9]='ticketaddprinter';
$__EVENTS['TICKETPRINTER_DPC'][10]='ticketmodprinter';
$__EVENTS['TICKETPRINTER_DPC'][11]='ticketremprinter';
$__EVENTS['TICKETPRINTER_DPC'][12]='ticketinfprinter';
$__EVENTS['TICKETPRINTER_DPC'][13]='ticketlogin';
$__EVENTS['TICKETPRINTER_DPC'][14]='ticketuseprinter';
$__EVENTS['TICKETPRINTER_DPC'][15]='ticketconfprinter';
$__EVENTS['TICKETPRINTER_DPC'][16]='ticketproceed';
$__EVENTS['TICKETPRINTER_DPC'][17]='ticketuploadjob';
$__EVENTS['TICKETPRINTER_DPC'][18]='ticketdelete';
$__EVENTS['TICKETPRINTER_DPC'][19]='ticketqrverify';

$__ACTIONS['TICKETPRINTER_DPC'][0]='ticketprinter';
$__ACTIONS['TICKETPRINTER_DPC'][1]='ticketshow';
$__ACTIONS['TICKETPRINTER_DPC'][2]='ticketxml';
$__ACTIONS['TICKETPRINTER_DPC'][3]='ticketjobstats';
$__ACTIONS['TICKETPRINTER_DPC'][4]='ticketnetact';
$__ACTIONS['TICKETPRINTER_DPC'][5]='ticketlogout';
$__ACTIONS['TICKETPRINTER_DPC'][6]='ticketjobs';
$__ACTIONS['TICKETPRINTER_DPC'][7]='ticketjobstats';
$__ACTIONS['TICKETPRINTER_DPC'][8]='ticketdeljob';
$__ACTIONS['TICKETPRINTER_DPC'][9]='ticketaddprinter';
$__ACTIONS['TICKETPRINTER_DPC'][10]='ticketmodprinter';
$__ACTIONS['TICKETPRINTER_DPC'][11]='ticketremprinter';
$__ACTIONS['TICKETPRINTER_DPC'][12]='ticketinfprinter';
$__ACTIONS['TICKETPRINTER_DPC'][13]='ticketlogin';
$__ACTIONS['TICKETPRINTER_DPC'][14]='ticketuseprinter';
$__ACTIONS['TICKETPRINTER_DPC'][15]='ticketconfprinter';
$__ACTIONS['TICKETPRINTER_DPC'][16]='ticketproceed';
$__ACTIONS['TICKETPRINTER_DPC'][17]='ticketuploadjob';
$__ACTIONS['TICKETPRINTER_DPC'][18]='ticketdelete';
$__ACTIONS['TICKETPRINTER_DPC'][19]='ticketqrverify';


$__DPCATTR['TICKETPRINTER_DPC']['ticketprinter'] = 'ticketprinter,1,0,0,0,0,0,0,0,0,0,0,1';

$__LOCALE['TICKETPRINTER_DPC'][0]='TICKETPRINTER_DPC;Printer;Εκτυπωτής';
$__LOCALE['TICKETPRINTER_DPC'][1]='_SHLOGOUT;Logout;Αποσύνδεση';


class ticketprinter extends UiIPPticket {
	
	var $myprinter, $defdir, $message, $procmd;
	var $url_activate, $url_invitate;
	var $test_page, $instrutions_as_var, $instrution_var;
	
	function __construct() {   
	
	   spl_autoload_register(array($this, 'loader')); //call dropbox api..process_job
	   
	   $this->myprinter = null;
	   $this->message = null;
	   $this->defdir = $_SESSION['indir'] ? $_SESSION['indir'] : '/';//null//'printers'; 

	   $this->procmd = 'ticket'; 
	   
	   //overwrite
	   //$this->printer_name = $_SESSION['printer'] ? $_SESSION['printer'] : $_POST['printername'];
       $this->printer_name = 'ticket.printer'; //<<<<<<<<<<<<<<<<<<<<<<<<<<<< do not select printer	   
							    
	   parent::__construct($this->printer_name,null,null,true,$this->procmd);
	   
	   //when a user has come from a url request for activation or invite a new user
	   $this->url_activate = $_SESSION['ACTIVATION'] ? $_SESSION['ACTIVATION'] : false;	   
	   $this->url_invitate = $_SESSION['INVITATION'] ? $_SESSION['INVITATION'] : false;	

	   $this->test_page = $_SESSION['TESTPAGE'] ? $_SESSION['TESTPAGE'] : false;  
	   $this->instrutions_as_var = true;
	   $this->instrution_var = null;
	}
	
    function loader($class){
	   $class = str_replace('\\', '/', $class);
	   require_once($class . '.php');
    } 	

    function event($event=null) {
	
        switch($event)   {
		
		    case 'ticketaddprinter' : 
			case 'ticketmodprinter' :
			case 'ticketremprinter' : 
			case 'ticketinfprinter' : 
			case 'ticketconfprinter': 
			case 'ticketuseprinter' : 
			case 'ticketlogin'      : 
			case 'ticketlogout'     :	
		    case 'ticketshow'       :
			case 'ticketxml'        :
			case 'ticketjobstats'   :
			case 'ticketnetact'     :
			case 'ticketjobs'       : 
			case 'ticketjobstats'   :
			case 'ticketdeljob'     :			
			case 'ticketprinter'    :
			case 'ticketdelete'     :
			case 'ticketqrverify'   :
			default             : 
			                    
        }			
	}
	
    function action($action=null)  {	
	
        switch($action)   {	

            case 'ticketqrverify'   : //verify ticket...
                                break;			
                                   
			case 'ticketlogout'  : self::_logout();
			                    $ret = self::_login();
			                    break;							
			case 'ticketlogin'   : if ($this->url_invitate) break; 
		    case 'ticketshow'    : if ($this->url_invitate) break;
			case 'ticketxml'     : if ($this->url_invitate) break;
			case 'ticketjobstats': if ($this->url_invitate) break;
			case 'ticketnetact'  : if ($this->url_invitate) break;
			case 'ticketjobs'    : if ($this->url_invitate) break;
			case 'ticketjobstats': if ($this->url_invitate) break;
			case 'ticketdeljob'  : if ($this->url_invitate) break;
			case 'ticketprinter' : 
			default           :	$login = self::_login();
								if ($login === true) {
								  if ($action=='ticketlogin') {
								    $cmd = str_replace($this->procmd,'','ticketinfprinter');
									//echo 'login:',$cmd;
								  } 	
								  else	
								    $cmd = str_replace($this->procmd,'',$action);
									
								  $ret = $this->printer_console($cmd);
								}
								else
								  $ret = $login;	
												
        }

        return ($ret);		
	}
	
	//override
	protected function html_window($title=null, $data=null, $footer_title=null, $nomenu=false) {
	    $ver = $this->server_name . $this->server_version;	
		$footer_title = $footer_title ? $footer_title :	$ver.'&nbsp;|&nbsp;'.$this->logout_url;
		$header_title = $title ? '<div class="contr"><h2>'.$title.'</h2></div>' : null;
		
	    $menu = $nomenu ? null : $this->html_printer_menu(true);		

        return ($data);//bypass form css style
//<link rel="stylesheet" type="text/css" href="view.css" media="all">
//<script type="text/javascript" src="view.js"></script>			
	    $form = <<<EOF
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

        return ($form); 	
	}		
	
	
	public function get_instruction($htmlwrap_start=null, $htmlwrap_end=null) {
		
	    $ret = $htmlwrap_start.$this->instruction_var.$htmlwrap_end;
	    //echo $ret;
	    return ($ret);
	}	
	
	protected function html_show_instruction_page($page=null,$replace_args=null,$replace_vals=null,$html_page=false,$hasmenu=false, $hasfooter=false, $disable_ins_var=false) {
	
	    $page_title = 'instruction page '.$page;
		$insfile = $this->admin_path . $page . '.' . $this->printer_name;//'.htm';
		$printer_url =  "http://" . $_ENV["HTTP_HOST"] .
		                pathinfo($_SERVER['PHP_SELF'],PATHINFO_DIRNAME) .
		                $this->printer_name;
		$title = $this->printer_name;// $page;							
        $header = $title ? "<div class=\"contr\"><h2>$title</h2></div>" : null;							
						
		//ONLY IF INVITATION OR ACTIVATION OR NEW USER	
        //echo $page,'>';		
        if (($this->url_invitate) || ($this->url_activate) || ($this->newuser)) {						
		
		if (!is_readable($insfile))
		    return $page_title;
		
		$data = @file_get_contents($insfile);
		//REPLACE ARGS IF ANY...
		if ((is_array($replace_args)) && (!empty($replace_args))) {
		    foreach ($replace_args as $a=>$arg)
			    $data = str_replace($arg,$replace_vals[$a],$data);
		}
		//DEFAULT ARG
		$page_instructions = str_replace('[PRINTER_URL]',$printer_url,$data);
		
		//SET INSTRUCTIONS AS ARG IN PAGE (EXCEPT MAIL BODY)
		if (($this->instrutions_as_var) && ($disable_ins_var==false)) {
		    $this->instruction_var = $page_instructions;
			return null;//dont show...
		}
		
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
		
//<link rel="stylesheet" type="text/css" href="{$html_url}view.css" media="all">
//<script type="text/javascript" src="{$html_url}view.js"></script>			
	    $ret = <<<EOF
		$html_head
	
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
	
	protected function _add_user_mail($mail=null,$refname=null,$refpass=null) {
	    if (!$mail)
		    return false;
		//$printer = str_replace('.printer', '', $this->printer_name);			

		$printermon = "http://" . $_ENV["HTTP_HOST"] . 
		              pathinfo($_SERVER['PHP_SELF'],PATHINFO_DIRNAME).
					  pathinfo($_SERVER['PHP_SELF'],PATHINFO_BASENAME);	
					  
		$printermonitor = $printermon . '?t='.$this->procmd.'login';//'?printer='.$this->printer_name;			  
		
        if (($refname) && ($refpass)) { //activate
		   $location = $_SESSION['indir'];
		   $keystring = $mail.'<|>'.$refname.'<|>'.$refpass.'<|>'.$this->printer_name.'<|>'.$location; 
		   $activation_urlstring = rawurlencode(base64_encode($keystring));
		   $activation_link = $printermon.'?activation='.$activation_urlstring.'&printer='.$this->printer_name;
		                      //seturl('activation='. $activation_urlstring.'&printer='.$this->printer_name,'here to activate your account');
        }	

		$location = $_SESSION['indir'];
		//$keystring = $mail.'<|>'.'guest'.'<|>'.'guest123'.'<|>'.$this->printer_name.'<|>'.$location; 
		//must be active to invite
        $keystring = $mail.'<|>'.$refname.'<|>'.$refpass.'<|>'.$this->printer_name.'<|>'.$location;		   
		$invitation_urlstring = rawurlencode(base64_encode($keystring));		
		$invitation_link = $printermon.'?invitation='.$invitation_urlstring.'&printer='.$this->printer_name;
		                   //seturl('invitation='.$invitation_urlstring.'&printer='.$this->printer_name,'here to invite another user');	
						   
		//if (is_in_list) ..dont send ..already logged in by url ???
		
        //send mail
        $message = $this->html_show_instruction_page('send-mail',array('[PRINTERNAME]','[USERNAME]','[PASSWORD]','[ACTLINK]','[INVLINK]','[PRINTERMON]'),
		                                                         array($this->printer_name, $refname, $refpass, $activation_link, $invitation_link, $printermonitor),
																 true, false, false, true);		  
		
		$from = $this->printer_name . '@' . str_replace('www.','',$_ENV["HTTP_HOST"]);//'balexiou@stereobit.com'
        $ok = $this->_sendmail($from,$mail,$this->printer_name .' activation',$message);
		//notify
        $ok2 = $this->_sendmail($from,'info@smart-printers.net',$this->printer_name .' user activation',$mail . $message);		
		
		if ($ok) {
		    //echo $refname,'-',$this->username;
		    $ret = $this->_save_mail_relation($mail,$refname,$this->username);
			return ($ret);
		}  
		  
        return false;		
	}
	
	protected function _login() {
        $current_printer = GetReq('printer');	
	    $ret = false;
		
		//in case of url name
		if (($current_printer) && ($current_printer!=$this->printer_name)) {
		    $ret = $this->login_form($current_printer);
		    return ($ret);
		}  
		
		if ($_SESSION['user']) { //already in
			return true;		
		}
		elseif ($_POST['username']) { //post login
		
		    $log = $this->get_printer_user(); 
			
			if ($log === true)
			  return true;
			else {
              $ret = $this->login_form();
		      return ($ret);			
            }			
		}
		elseif ($invite = $_GET['invitation']) {//get param to propose a registration by link
		
			//INVITATE USER....................................

			$urlstring = rawurldecode(base64_decode($invite));
			//echo $urlstring;
			$args = explode('<|>',$urlstring);
			$mail = $args[0];
			$user = $args[1];//'guest';
			$pass = $args[2];//'guest123';	
			$printername = $args[3];//'dropbox.printer';
			$printerdir = $args[4];//null;//'/';	
			//echo $user,':',$pass;
			$_SESSION['INVITATION'] = $args; 
			$this->url_invitate = $args;
			
            $log = $this->get_printer_user($printername,$printerdir,$user,$pass); //must be active to procced
            //if (!$log) //or use the guest account if not activated ..DISABLED..	
              //  $log = $this->get_printer_user($printername,$printerdir,'guest','guest123');			
			
			if ($log === true) {
			  return true;
			}  
			else {
              $ret = $this->login_form();
		      return ($ret);			
            }				
		}	
		elseif ($active = $_GET['activation']) {//get param to activate by link
			
			$urlstring = rawurldecode(base64_decode($active));
			//echo $urlstring;
			$args = explode('<|>',$urlstring);
			$mail = $args[0];
			$user = $args[1];//'guest';
			$pass = $args[2];//'guest123';	
			$printername = $args[3];//'dropbox.printer';
			$printerdir = $args[4];//null;//'/';			
			//echo $printername,$printerdir,$user,$pass;
			
			//ACTIVATE USER....................................
            $params = $this->parse_printer_file($printername, $printerdir);
		    
		    if (empty($params))
		        return ('Activation error #1');
				
		    $printerusers = (array) $params['users'];			
			if (!array_key_exists($user, $printerusers)) {
			    $printerusers[$user] = hash('crc32',$pass); //<<hash
			    			
                $ok = $this->html_mod_printer($printername,
		                                      null,
						                      null,
						                      $printerusers,
						                      $printerdir);
			
			    if ($ok) {
				  $_SESSION['ACTIVATION'] = $args; 
				  $this->url_activate = $args;
				}  
	
			}
			else {//just pass params ..re-activate withour reg
		        $_SESSION['ACTIVATION'] = $args; 
			    $this->url_activate = $args;			
			}	
            //ACTIVATION...................................
			
            $log = $this->get_printer_user($printername,$printerdir,$user,$pass);				
			
			if ($log === true) {
			  //echo 'TEST PAGE:',$user,'>',$printername,'<br>';
			  //send test page to the printer.....................for 2nd step dropbox allow
			  /*
			  if ($testpage_id = $this->send_test_page(null,$printername, $printerdir, $user))
			     $_SESSION['TESTPAGE'] = $testpage_id;  
			  */
			  return true;
			}  
			else {
              $ret = $this->login_form();
		      return ($ret);			
            }			
		}
		else { //login form
		    $ret = $this->login_form($message);
		    return ($ret);			
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
		  
		  $_SESSION['TESTPAGE'] = null; //testpage reset
		  $_SESSION['tdoc'] = null;

          //$ret .=  "Successfully logged out<br>";
          //echo '<p><a href="?action=logIn">LogIn</a></p>';
		  return true;
       }

       //return ($ret);	   
	   return false;
    }		
	
	//login form
	protected function login_form() {
	    $message = $this->message ? '('.$this->message.')' : null;
	    $printername = $this->printer_name ? $this->printer_name : GetParam('printername');
		$indir = $_POST['indir'] ? $_POST['indir'] :($_SESSION['indir'] ? $_SESSION['indir'] : $this->defdir);
	    $select_printer = null;
		$cmd = $this->procmd ? $this->procmd.'login': 'login';
		$name = GetReq('prn') ? GetReq('prn').'.printer' : null;
		
	    if (!$printername) {
		
		  $select_printer = '
<li id="li_5" >
<label class="description" for="element_5">Printer</label>
<div>
<input id="element_5_1" name= "printername" class="element text medium" maxlength="20" value="'.$name.'"/>
<label>Printer Name</label>
</div>
<p class="guidelines" id="guide_4"><small>Printer name (e.g. name.printer)</small></p> 
</li>
<!--li id="li_6" >
<label class="description" for="indir">Printer Path</label>
<div>
<input id="element_6" name="indir" class="element text medium" type="text" maxlength="13" value=""/> 
</div>
<p class="guidelines" id="guide_6"><small>Printer path</small></p> 
</li-->	  
';		  
		}
		else
		  $select_printer = "
<input type=\"hidden\" name=\"printername\" value=\"$printername\" />
<input type=\"hidden\" name=\"indir\" value=\"$indir\" />
";
	
	    $form .= <<<EOF
		<form id="form_470441" class="appnitro"  method="post" action="">
		<div class="form_description">
			<p>Please enter your details to access the printer monitor.</p>
		</div>						
			<ul >
			
		<li id="li_4" >
		<label class="description" for="element_4">User </label>
		<span>
			<input id="element_4_1" name= "username" class="element text" maxlength="20" size="14" value=""/>
			<label>Username</label>
		</span>
		<span>
			<input id="element_4_2" type= "password" name= "password" class="element text" maxlength="20" size="14" value=""/>
			<label>Password</label>
		</span><p class="guidelines" id="guide_4"><small>Printer credentials</small></p> 
		</li>
			
			    
		$select_printer		
				
		<input type="hidden" name="form_id" value="470441" />
		<input type="hidden" name="FormAction" value="$cmd" />
		
		<li class="buttons">
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
EOF;
	
	    $ret  = $this->html_window("Login", $form, $this->printer_name, true);	
        return ($ret);	
	}	

	protected function get_printer_user($printername=null, $printerdir=null, $printeruser=null, $printerpass=null) {
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
	    $bootstrapfile = $_SERVER['DOCUMENT_ROOT'] .'/'.$dir. str_replace('.printer','.php',$printername);
		//echo $bootstrapfile,'>';
		if (!is_readable($bootstrapfile)) {
		   //echo $bootstrapfile,'>';
		   $this->message = 'Invalid printer';
		   return false;		
		}
		
		$params = self::parse_printer_file(null, null, $bootstrapfile);
		//print_r($params);
		
		if (empty($params)) {
		   $this->message = 'Invalid configuration';
		   return false;
		}   
		
		$allowed_users = (array) $params['users']; 
        //print_r($allowed_users);		
		   
		if (($printeruser) && ($printerpass)) {
		
		   if ((array_key_exists($printeruser, $allowed_users)) &&
		    ($allowed_users[$printeruser] == hash('crc32',$printerpass))) { //<<hash...
			
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
		
		    //return ('Not allowed!');
            if (!$printername)
		        return ('Unknown printer!');			
			   
		    $params = $this->parse_printer_file($printername, $printerdir);
		    //print_r($params); echo $printername;
		    if (empty($params))
		        return ('Unknown printer file!');
				
		    $printerusers = (array) $params['users'];
		   
		    if ($_POST['FormAction']!=$cmd) {
			
                if ($this->url_activate) {
				  $ret .= $this->html_show_instruction_page('account-activated');
                  //$ret .= $this->html_window(null, 'Account activated', $this->printer_name);			
				  //goto next step automatically
				  $ret .= $this->form_configprinter($printername, $printerdir);			
				}  
				elseif ($this->url_invitate) {
				  if ($this->newuser) {
				    //$ret .= $this->html_show_instruction_page('user-defined');
                    $ret .= $this->html_window("Add user", 'User ('.$this->newuser.') defined.', $this->printer_name, true);			
				  }	
				  else {
				    //$ret .= self::html_window(null, 'User ('.$this->username.') logged in.', $this->printer_name);
				    $ret .= $this->html_show_instruction_page('invite-user');
				    $ret .= $this->add_user_printer_form("",$printername,$params['users'],$printerdir);
					//$ret .= self::html_window(null, 'Invitation info .('.implode('-',$this->url_invitate).'-'.$this->username.')', $this->printer_name);			
				  }	
				}				
				else {//show data
				  if ($this->newuser) {
				    //$ret .= $this->html_show_instruction_page('user-defined');
				    $ret  = $this->html_show_instruction_page('user-post');
                    $ret .= $this->html_window("Add user", 'User ('.$this->newuser.') defined.', $this->printer_name, true);			
				  }	
				  else {				
				    $ret .= $this->html_show_instruction_page('log-in');
				    //$ret .= self::html_window(null, 'User ('.$this->username.') logged in.', $this->printer_name);
//>>>>>>>>>>>>>>>>  //test....
					//invitation only by these users
					if (($this->username=='guest') || ($this->username=='demo') || ($this->username=='billy')) {		
					
						$ret .= $this->read_documents_data();					
						$ret .= $this->add_user_printer_form("",$printername,$params['users'],$printerdir);
					}
					else {//common user...see only tickets, noy add user form
					
						$ret ='<h2>Tickets</h2>';
						$ret .= $this->read_documents_data();
					} 					
				  }	
				}  
				  
		        return ($ret); 
		    }
			
 		
		    if (!empty($printerusers)) {
                //get user post data	
		        $post_user = 'username';
			    $post_pass = 'password';			
				
		        if (($u = addslashes($_POST[$post_user])) && ($p = addslashes($_POST[$post_pass]))) {
				  //not allowing without mail
				  if ($email = addslashes($_POST['email'])) { 
			        //not allowing double entries
			        if (!array_key_exists($u, $printerusers)) {
						
						    //echo 'aaa'; 
                            $this->newuser = $u;
                            $_SESSION['new_user'] = $u;		
							$subok = $this->_add_user_mail($email,$u,$p);
							$this->username = $u; //CHANGE USER..after mail relation
														
							if ($subok) {
							  $ret .= $this->form_configprinter(null,null,$u);//,$this->username); //goto step 2
							  return ($ret);
							}  
							else
							  $ret .= $this->add_user_printer_form('. Send mail error!',$printername,$printerusers,$printerdir);										
					}
                    else
                        $ret .= $this->add_user_printer_form('. User name exists, please choose another user name!',$printername,$printerusers,$printerdir);					
				  }
                  else  				  
			        $ret .= $this->add_user_printer_form('. Your e-mail required, failed to add user!',$printername,$printerusers,$printerdir);					
				}					  
		    }										 
		    else
 		        $ret .= $this->add_user_printer_form('. Failed to add user!',$printername,$printerusers,$printerdir);

			if (!$ret) 
                $ret .= $this->add_user_printer_form('. Failed to add user!',$printername,$printerusers,$printerdir);			
			
            $ret .= $this->html_show_instruction_page('user-error');			
			return ($ret);		  
		}   
		
		//else..admin
		$ret = parent::form_useprinter($printername, $indir);
		return ($ret);
		
    }	
	
	//override..for email request
	public function add_user_printer_form($message=null, $name=null, $users=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;
		$cmd = $this->external_use ? $this->procmd.'useprinter':'useprinter';	
		
	    $form = <<<EOF
		<form id="form_470441" class="appnitro" enctype="multipart/form-data" method="post" action="">
					<div class="form_description">
			<h2>Add User $message</h2>
			<p>Add a printer account.</p>
		</div>						
			<ul >		

        <li id="li_4" >
		<label class="description" for="user">Account details</label>
		<span>
			<input id="element_1_1" name= "username" class="element text" maxlength="13" size="14" value=""/>
			<label>Username</label>
		</span>
		<span>
			<input id="element_1_2" type= "password" name= "password" class="element text" maxlength="13" size="14" value=""/>
			<label>Password</label>
		</span><p class="guidelines" id="guide_4"><small>Add user account details</small></p> 
		</li>
		
		<li id="li_0" >
		<label class="description" for="element_0">Your e-mail</label>
		<div>
			<input id="element_0" name="email" class="element text medium" type="text" maxlength="30" value=""/> 
		</div><p class="guidelines" id="guide_1"><small>Please specify your email to sent you the activation details</small></p> 
		</li>			
			
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="$cmd" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
EOF;
        $ret  = $this->html_window("Setup new user", $form, $this->printer_name, true);	
        return ($ret);	
	}	
	
	//override
	public function form_configprinter($printername=null, $indir=null, $newuser=null) {
	    $printername = $printername ? $printername : $this->printer_name;
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
        $cmd = $this->external_use ? $this->procmd.'confprinter':'confprinter';		
		
        if (!$printername) 
		    return ('Unknown printer!');		
		
	    if ($this->username!=$this->get_printer_admin()) {
	
		    $myuser = $newuser ? $newuser : $this->newuser;
			$file = $this->admin_path . md5($myuser) . '.token';		
			
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
				  
                $ret .= $this->config_filter_form_ticketsave('ticketsave', $printername, $code, $indir);	
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
                    $ret .= $this->html_window("Error", null, $this->printer_name, true);//'Not a valid user!', $this->printer_name);			   
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
				  $ret .= $this->config_filter_form_ticketsave('ticketsave', $printername, $code, $indir);				
				//}
			}   
			   
			return ($ret);
		}   

        $ret = parent::form_configprinter($printername, $indir);  
		return ($ret);	
    }	
	
	//override for goto step 3
	protected function config_filter_form_ticketsave($filter=null, $printername=null, $code=null, $indir=null) {
	    $printername = $printername ? $printername : $this->printer_name;
	    $ver = $this->server_name . $this->server_version;
	    $dir = $indir ? $indir.'/' : ($_SESSION['indir'] ? $_SESSION['indir'] .'/' : '/');
		$filter = $_POST['filtername'] ? $_POST['filtername'] : $filter;
		$cmd = $this->external_use ? $this->procmd.'confprinter':'confprinter';
		
		$tdoc = GetReq('tdoc') ? GetReq('tdoc') : $_SESSION['tdoc'];		
		$tdoc_path = $this->admin_path .$this->username.'/'.$tdoc.'/';
		
		//$file = $_SERVER['DOCUMENT_ROOT'] .'/'.$dir . str_replace('.printer','',$printername).'.'.$filter.'.php';
		if ($this->username!=$this->get_printer_admin()) {
		    $myuser = $this->newuser ? $this->newuser : $this->username;
		    $userstr = '-'.$myuser;
		}	
		else {
		    $myuser = $this->username;
           	$userstr = null;	
		}	
		
		$file = $this->admin_path . $filter.$userstr.'-conf'.'.php';
		//echo $file,'>';
        //read file	args	
		if (is_readable($file)) {

            include($file);	
			
			//$iwfile_src = $iwfile ? 'admin/'.$printername.'/'.$iwfile : null;

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
		
		//if ($ifiletype = $_POST['ifiletype']) {
		if ($filtername = $_POST['filtername']) {
		    $iaction = $_POST['iaction'] ? stripslashes($_POST['iaction']) : 'null';
		    $ifiletype = $_POST['ifiletype'] ? stripslashes($_POST['ifiletype']) : '';
		    $icompression = $_POST['icompression'] ? stripslashes($_POST['icompression']) : 75;
			$ixframe = (($xf = intval($_POST['ixframe'])) && ($xf<2000)) ? $xf : '0';
			$iyframe = (($yf = intval($_POST['iyframe'])) && ($yf<2000)) ? $yf : '0';		
			$iwopacity = $_POST['iwopacity'] ? ($_POST['iwopacity']<=100 ? stripslashes($_POST['iwopacity']):100) : 100;
			$iwalpha = $_POST['iwalpha'] ? 1 : 0;
            $iwposition = $_POST['iwposition'] ? $_POST['iwposition'] : 'null';
			$ioptimize = $_POST['ioptimize'] ? $_POST['ioptimize'] : '0';
			//print_r($_FILES['iwfile']);
			if (!empty($_FILES['iwfile']) && (!$_FILES['iwfile']['error']) && ($tdoc)) {//uploaded file
	            
		        $ufile = $_FILES['iwfile']['tmp_name'];	
				$rfile = $_FILES['iwfile']['name'];//str_replace(FILE_DELIMITER,'_',$_FILES['iwfile']['name']);				
				
				if ((stristr($rfile,'.txt')) /*|| (stristr($rfile,'.gif')) || (stristr($rfile,'.png'))*/ ) {
				
					$iwfilename = 'maillist' . '.' . array_pop(explode('.',$rfile));			
	                //echo '>'. $iwfilename;
					$tdoc_maillist = $tdoc_path . $iwfilename;
					//echo $tdoc_maillist;
                    if (move_uploaded_file($ufile, $tdoc_maillist)) {
					
					    $iwfile = $iwfilename;
						//$iwfile_src = 'admin/'.$printername.'/'. $tdoc .'/' . $iwfilename;
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
\$iaction = $iaction;			
\$ifiletype = '$ifiletype';			
\$icompression = $icompression;
\$ixframe = $ixframe;
\$iyframe = $iyframe;
\$iwopacity = $iwopacity;
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
		    //save file...	
            //if (eval($db_code)!==false)	{		
		      $x = @file_put_contents($file, $db_code);			
			  //echo $x;
			
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
		}	
        elseif ($_GET['oauth_token']) {	//..returing from allow app procedure
            //echo 'RETURN FROM DROPBOX:',$this->username,'>',$printername,'<br>';
            //send test page to the printer.....................for 2nd step dropbox allow
			if ($testpage_id = $this->send_test_page('testpage.jpg',$printername, $indir, $this->username)) {
              //return from dropbox page...save token...
			  $this->enable_dropbox_jobs($testpage_id,$printername, $indir);
            }			
		}
		elseif ($_GET['not_approved']==='true') {//in case of deny app
			//echo 'deny';
			$idropbox = 0; //override setting		
		}		
				

		switch ($ifiletype) {
		    case 'png' : $ifiletype_select = "<option value='jpg' >jpg</option><option value='png' selected='selected'>png</option><option value='gif' >gif</option>";
			             break;
		    case 'gif' : $ifiletype_select = "<option value='jpg' >jpg</option><option value='png' >png</option><option value='gif' selected='selected'>gif</option>";
			             break;
            case 'jpg' : $ifiletype_select = "<option value='0' >Source</option><option value='jpg' selected='selected'>jpg</option><option value='png' >png</option><option value='gif' >gif</option>";
			             break;  
			default    : $ifiletype_select = "<option value='0' selected='selected'>Source</option><option value='jpg'>jpg</option><option value='png' >png</option><option value='gif' >gif</option>";
		}
		
		switch ($iaction) {
			case 1 : $iact_select_1 = "selected='selected'"; break; 
			case 2 : $iact_select_2 = "selected='selected'"; break; 
			case 3 : $iact_select_3 = "selected='selected'"; break; 
            case 0 :			
			default: $iact_select_0 = "selected='selected'"; 
		}		
					
		
		//$iwfile_image = $iwfile_src ? "<img src='$iwfile_src' width='128'>" : null;	
        $iwfile_tdoc = is_readable($tdoc_path.$iwfile) ?
                       'submited' : 'empty';	
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
        <li id="li_8" >
		<label class="description" for="element_8">Enable Dropbox</label>
		<span>
		<input id="element_8" name="idropbox" class="element checkbox" type="checkbox" value="1" '.$idropbox_check.'/>
        <label class="choice" for="element_8">Dropbox</label>
		</span><p class="guidelines" id="guide_8"><small>Dropbox integration.</small></p> 
		</li>
		<li id="li_9" >
		<label class="description" for="element_9">Dropbox ticket folder name </label>
		<div>
			<input id="element_9" name="idbfolder" class="element text medium" type="text" maxlength="20" value="'.$idbfolder.'"/> 
		</div><p class="guidelines" id="guide_9"><small>Please specify a dropbox folder to save tickets</small></p> 
		</li>			
';			
        }
        else
            $form_dropbox = null;		

	    $form = <<<EOF
		<form id="form_470441" class="appnitro" enctype="multipart/form-data" method="post" action="">
		<div class="form_description">
			<h2>Settings. $message</h2>
			<p>Ticket authoring configuration.</p>
		</div>						
		<ul >		
		
        <li class="section_break"><h2>Ticket authoring</h2><p>Tasks when ticket created.</p></li>
		
		<li id="li_01" >
		<label class="description" for="element_01">Action </label>
		<div>
		<select class="element select medium" id="element_01" name="iaction"> 
		<option value="0" $iact_select_0 >No action</option>
        <option value="1" $iact_select_1 >Reserve</option>		
        <option value="2" $iact_select_2 >Send mail</option>		
        <option value="3" $iact_select_3 >Paid</option>
		</select>
		</div><p class="guidelines" id="guide_01"><small>Select an action.</small></p> 
		</li>		
		<li id="li_0" >
		<label class="description" for="element_0">Image export </label>
		<div>
		<select class="element select medium" id="element_0" name="ifiletype"> 
        $ifiletype_select
		</select>
		</div><p class="guidelines" id="guide_0"><small>Select an image type for output</small></p> 
		</li>
		<li id="li_1" >
		<label class="description" for="element_1">Copies </label>
		<div>
			<input id="element_1" name="icompression" class="element text medium" type="text" maxlength="3" value="$icompression"/> 
		</div><p class="guidelines" id="guide_1"><small>Specify number of generated tickets (1..100)</small></p> 
		</li>
		<li id="li_11" >
		<label class="description" for="element_11">Frame</label>
		<span>
			<input id="element_11" name= "ixframe" class="element text" maxlength="4" size="8" value="$ixframe"/>
			<label>X dimension</label>
		</span>
		<span>
			<input id="element_12" name= "iyframe" class="element text" maxlength="4" size="8" value="$iyframe"/>
			<label>Y dimension</label>
		</span><p class="guidelines" id="guide_11"><small>Place in frame</small></p> 
		</li>			
		
		<li class="section_break"><h2>Mail list</h2><p>Mail list submition.</p></li>
		
		<li id="li_13" >
		<label class="description" for="element_13">Mail list handling </label>
		<div>
		<select class="element select medium" id="element_13" name="iwposition"> 
		<option value="0" $iwpos_select_0 ">Overwrite</option>
        <option value="1" $iwpos_select_1 >Append</option>
        <option value="2" $iwpos_select_2 >Up right</option>
        <option value="3" $iwpos_select_3 >Down left</option>
        <option value="4" $iwpos_select_4 >Down right</option>
        <option value="5" $iwpos_select_5 >Center</option>
		</select>
		</div><p class="guidelines" id="guide_13"><small>Select where to place the watrmark</small></p> 
		</li>
        <li id="li_12" >
		<label class="description" for="element_12">Upload a mail list file </label>
		<div>
		<input id="element_12" name="iwfile" class="element file" type="file"/> 
		</div> <p class="guidelines" id="guide_12"><small>Select a mail list file.</small></p> 
		</li>
		<li id="li_13" >
		<label class="description" for="element_13">Mail list file:$iwfile_tdoc </label>
		<!--img src="$iwfile_src" width="128"-->
		</li>			
		<li id="li_12" >
		<label class="description" for="element_12">Subject </label>
		<div>
			<input id="element_12" name="iwopacity" class="element text medium" type="text" maxlength="64" value="$iwopacity"/> 
		</div><p class="guidelines" id="guide_12"><small>Specify mail subject</small></p> 
		</li>
        <li id="li_11" >
		<label class="description" for="element_11">Callback</label>
		<span>
		<input id="element_11" name="iwalpha" class="element checkbox" type="checkbox" value="1" $iwalpha_check />
        <label class="choice" for="element_11">Set callback</label>
		</span><p class="guidelines" id="guide_11"><small>Check mail if readed.</small></p> 
		</li>				

        <li class="section_break"><h2>Print out</h2><p>Automate ticket printing.</p></li>
		
		<li id="li_2" >
		<label class="description" for="element_2">Resize </label>
		<div>
			<input id="element_2" name="iautoresize" class="element text medium" type="text" maxlength="20" value="$iautoresize"/> 
		</div><p class="guidelines" id="guide_2"><small>Please specify autoresize x value separated by commas (500,200,100)</small></p> 
		</li>
		<li id="li_22" >
		<label class="description" for="element_22">Tickets file creation </label>
		<div>
		<select class="element select medium" id="element_22" name="ioptimize"> 
        <option value="0" $iopt_select_0 >One ticket per file</option>		
        <option value="1" $iopt_select_1 >Paging in one file</option>		
		</select>
		</div><p class="guidelines" id="guide_22"><small>Specify the dimension type.</small></p> 
		</li>			
		
        <!--li class="section_break"><h2>Ftp Account</h2><p>Enable ftp auto uploading.</p></li>
		
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
		</li-->

        $form_dropbox		
		
        <li class="section_break"></li>
		
		<li class="buttons">
		        <input type="hidden" name="MAX_FILE_SIZE" value="500000" />
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="$cmd" />			    
				<input type="hidden" name="filtername" value="$filter" />
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
		</ul>
		</form>	
EOF;

        $ret  = $this->html_window("Printer Configuration", $form, $this->printer_name, true);			
        return ($ret);		
	}

	//override
	public function form_infoprinter($printername=null, $indir=null) {
	    $printername = $printername ? $printername : $this->printer_name;
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
		
        if ($this->username!=$this->get_printer_admin()) {		
		
            $filter = 'ticketsave';			
		    $myuser = $this->url_invitate ? $this->newuser : ($this->newuser ? $this->newuser : $this->username);
			
			if ($myuser) {
		        $userstr = '-'.$myuser;
			    $file = $this->admin_path . $filter.$userstr.'-conf'.'.php';
				
				//FILE NOW IS THE DROPBOX TOKEN FILE CREATED DURING 'ALLOW APP' PROCEDURE
				//$file = $this->admin_path . md5($myuser) . '.token';
				
                if (is_readable($file)) {	//if conf file
				
		            $joblist = self::html_get_printer_jobs_info();
		            $ret  = $this->html_window("Printer Queue", $joblist, $this->printer_name,true);	
                    //$ret .= $this->html_show_instruction_page('job-list');	
                }
				else {
				
		            $ret  = $this->html_show_instruction_page('config-error');
		            $ret .= $this->html_window("Error", 'Invalid configuration.', $this->printer_name,true);//'Not a valid dropbox account!', $this->printer_name);
			    }
		        return ($ret);				
			}
            else {
			    $ret  = $this->html_show_instruction_page('user-error');
                $ret .= $this->html_window("Error", null, $this->printer_name,true);//'Not a valid user!', $this->printer_name);  		
				return ($ret);			
            }			
		 
		}//if
		
		$ret = parent::form_infoprinter($printername, $indir);
		return ($ret);
    }	
	
	//override
	protected function html_get_printer_jobs_info() {
	    $user = $this->newuser ? $this->newuser : ($this->username ? $this->username : $_SESSION['user']);	
		$jstate = array(); 
		
        if (!is_dir($this->jobs_path))
		  return null; 

        $printer_state = null;	
        //$mydir = dir($this->jobs_path);	
        //while ($fileread = $mydir->read ()) { 
		chdir($this->jobs_path); 
		array_multisort(array_map('filemtime', ($files = glob("job-*.*"))), SORT_DESC, $files); 
		foreach($files as $fileread) { 		
		
		    if (substr($fileread,0,4)=='job'.FILE_DELIMITER) {
				//echo $fileread,'<br>';
			    $pf = explode(FILE_DELIMITER,$fileread);
				$jid = $pf[1];//sort	
                $job_owner = $pf[3];
				
			    if (($user==$this->get_printer_admin()) || ($job_owner==$user) || (!defined('AUTH_USER'))) {												
					
				    if (stristr($fileread,FILE_DELIMITER.'completed'))
					    $jstate = 'completed';
					elseif (stristr($fileread,FILE_DELIMITER.'processing'))
					    $jstate = 'processing';
					elseif (stristr($fileread,FILE_DELIMITER.'pending'))
					    $jstate = 'pending';
					else
					    $jstate = 'pending';	
						
					$jtime = date ("F d Y H:i:s.", filemtime($this->jobs_path . $fileread));	
					$jsize = filesize($this->jobs_path . $fileread);	//bytes
						
				    $jobs[intval($jid)] = array('name'=>$fileread, 'job'=>$pf, 'state'=>$jstate, 
					                            'date'=>$jtime, 'size'=>$jsize);							
				}
			}	
		}	
		//$mydir->close();

		
		$ret = '<h2>' . $user . '&nbsp;jobs</h2>';	
		
		$ret .= self::printline(array('No','Date','Size','Name','Status','Tickets'),
		                        array('left;5%','left;30%','left;10%','left;45%','left;5%','left;5%'),
		 					    1,
			                    "center::100%::0::group_article_body::left::0::0::");	 
		
		if (is_array($jobs)) {

			//krsort($jobs);
		    $i=1;
			foreach ($jobs as $jid=>$fileattr) {			   
			   $job_file = $fileattr['name'];
	           $job_id = $fileattr['job'][1];
	           $job_remote_ip = str_replace('~',':',$fileattr['job'][2]);
	           $job_user_name = $fileattr['job'][3];
		       $job_name = $fileattr['job'][4];	
               $job_status = $fileattr['state'];			   
			   $job_time = $fileattr['date'];
			   $job_size = self::bytesToSize1024($fileattr['size'], 1);
			   
			   if ($job_status!='completed') { 
			   
				$proceed_state = $job_status;	   
                $ticketsurl = '...';
               } 				 
			   else { //completed
			     $proceed_state = $job_status;	

				 $owner_path = $this->admin_path . $user;
				 $ticket_path = $owner_path . '/' . $job_name;				 
                 if (is_dir($ticket_path))  				 
                     $ticketsurl = seturl('t=ticketuseprinter&tdoc='.$job_name,'Show');
				 else 
                     $ticketsurl = 'Empty'; 				 
			   }	 
		   
		       //big jobname
		       if (mb_strlen($job_name,'utf-8')>60)
			        $jb_name = mb_substr($job_name,0,60,'utf-8').'...';//'bigname';
			   else
			        $jb_name = $job_name;
               $ret .= self::printline(array($i++,$job_time,$job_size,$jb_name,$proceed_state,$ticketsurl),
			                           array('left;5%','left;30%','left;10%','left;45%','left;5%','left;5%'),
                                       0,
			                           "center::100%::0::group_article_body::left::0::0::");									   									   
			}
	    }
		else {
		   $ret .= 'No Jobs';
		}	

        return ($ret);			
	}	
	
	//override  
	protected function html_get_printer_menu($iconsview=null, $p=null, $icons_size=null) {
		//$urlicons = 'icons/';	
	    //if custom printer dir icon... 
		$urlicons = strstr($this->icons_path, $this->printer_name) ? 'icons/'.$this->printer_name.'/' : 'icons/';	
		$isize = $icons_size ? "width='$icons_size' height='$icons_size'" : null;
		
        $icons = array();		
		$user = $this->username ? $this->username : $_SESSION['user'];
		$indir = $_SESSION['indir'] ? $_SESSION['indir'] : $_GET['indir'];
		
		if ($this->username!=$this->get_printer_admin()) {
		    //choose icons
		    if (($this->url_invitate) || ($this->url_activate) || ($this->newuser)) {
			
			    if (!$this->instrutions_as_var) {//DISABLE ICONS WHEN INSTRUCTIONS VAR PROCEDURE
					$icons[] = $this->urlpath."?".$this->cmd."useprinter:one";
					$icons[] = $this->urlpath."?".$this->cmd."confprinter:two";
					$icons[] = $this->urlpath."?".$this->cmd."infprinter:three";			
					//$icons[] = $this->urlpath."?".$this->cmd."logout:logout"; //<<no logout button when activation, invitation, new user
				}
			}
			else {
		        $icons[] = $this->urlpath."?".$this->cmd."useprinter:Printer Users";
			    $icons[] = $this->urlpath."?".$this->cmd."confprinter:Printer Configuration";
                $icons[] = $this->urlpath."?".$this->cmd."infprinter:Printer Info";					
				$icons[] = $this->urlpath."?".$this->cmd."qrverify:Printer QRMonitor";
			    $icons[] = $this->urlpath."?".$this->cmd."logout:logout";			
			}
			
		    //RENDER ICONS
		    if ($iconsview) {
		        //print_r($icons);
		        foreach ($icons as $icon) { 
			
			        $icondata = explode(':',$icon);
					//echo $this->icons_path.$icondata[1].'.png'.'<br/>';
			        if (is_file($this->icons_path.$icondata[1].'.png'))
			          $ifile = $urlicons.$icondata[1].'.png';
			        else
			          $ifile = $urlicons.'index.printer.png';
			   
			        $icco[] = "<a href='".$icondata[0]."'><img src='" . $ifile."' $isize border=0 alt='".$icondata[1]."'></a>";
			        //$link = "<a href='".$icondata[0]."'>" . $icondata[1]  ."</a>";
			        $px = $p ? $p : '20%'; //25
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
	
	//override colorize
    protected function printline($dat=null,$att=null,$isbold=false,$render=null,$iscolor=null) {
	    $ret = null;
		$isarray = is_array($att);
		
	    if (is_array($dat)) {
		
		   foreach ($dat as $i=>$f) {
		      
			   $fcolor = $iscolor ? "<font color='$iscolor'>".$f.'</font>' : $f; 
		   
			   $data[$i] = $isbold ? "<strong>".$fcolor.'</strong>':$fcolor; 
	           $attr[$i] = $isarray ? $att[$i] : $att;			      
		   }
		   

		   $ret = "\r\n<table><tr>";
		   foreach ($data as $t=>$title) {
		     $attribute = explode(';',$attr[$t]);
		     $ret .= '<td align="'.$attribute[0].'" width="'.$attribute[1].'" valign="top">'.$title.'</td>';
		   }	 
		   $ret .= "</tr></table>";
		}
		
		return ($ret);
    }	
	
	//public dpc call from page
	public function show_printer_menu() {
	
	    if ($this->username)
		    $ret = $this->html_get_printer_menu(true,null,100);
		else
            $ret = false;

        return ($ret);			
	}
	
	//read admin dir for documents 
	protected function read_documents_data() {
	    $user = $_SESSION['user'];	
		$tstate = null;
		
		//get tdoc ticket directory name
		$tdoc_name = GetReq('tdoc') ? GetReq('tdoc') : $_SESSION['tdoc'];
		if (!$tdoc_name) {
		  return '<h2>Ticket document not selected.</h2>';
		}  
		
		$_SESSION['tdoc'] = $tdoc_name; //save in session when selected qr-use
		
		//get hash if any to check
		$hash = GetReq('hash') ? GetReq('hash') : null;
		if ($hash) {
		   $ok = $this->save_ticket_status($tdoc_name);		
		   $qr_callback = GetReq('qr');
		   $filter = $qr_callback ? $hash : null;//when qr select only qr scanned ticket
		}   
		
	    $owner_path = $this->admin_path . $user;
	    $ticket_path = $owner_path . '/' . $tdoc_name; 		
		//echo $ticket_path;
        if (!is_dir($ticket_path))
		  return null; 	
	
		chdir($ticket_path); 
		array_multisort(array_map('filemtime', ($files = glob("*$filter.xps"))), SORT_DESC, $files); 
		foreach($files as $fileread) { 		
			
			if (stristr($fileread,FILE_DELIMITER)) { 
				if (stristr($fileread,'completed'.FILE_DELIMITER))
				    $tstate = 'completed';
				elseif (stristr($fileread,'processing'.FILE_DELIMITER))
				    $tstate = 'processing';
				elseif (stristr($fileread,'pending'.FILE_DELIMITER))
				    $tstate = 'pending';
				else
				    $tstate = 'pending';

				$s = explode(FILE_DELIMITER,str_replace('.xps','',$fileread));	
                $ticket_s = $s[1]; 						
			}
            else {
			    $tstate = 'pending';
			    $ticket_s = str_replace('.xps','',$fileread); 						
			}	
               			
			$ttime = date ("F d Y H:i:s.", filemtime($this->ticket_path . $fileread));	
			$tsize = filesize($this->ticket_path . $fileread);	//bytes
						
			$tickets[] = array('name'=>$ticket_s, 'state'=>$tstate, 
			                   'date'=>$ttime, 'size'=>$tsize);							

		}	
	
	    $ret = '<h2>'.$tdoc_name.'</h2>';
		$ret .= self::printline(array('No','Date','Paid','Name','Status','Pay'),
		                        array('left;5%','left;30%','left;10%','left;45%','left;5%','left;5%'),
		 					    1,
			                    "center::100%::0::group_article_body::left::0::0::");	
								
		if (is_array($tickets)) {

			//krsort($tickets);
		    $i=1;
			foreach ($tickets as $tid=>$fileattr) {			   
		       $ticket_name = $fileattr['name'];	
               $ticket_status = $fileattr['state'];			   
			   $ticket_time = $fileattr['date'];
			   $ticket_size = self::bytesToSize1024($fileattr['size'], 1);
			   
			   if ($ticket_status!='completed')  
			     $proceed_state = $this->set_ticket_status($ticket_name);//$ticket_status;			    
			   else
			     $proceed_state = $ticket_status;			 
 
		   
		       //big jobname
		       if (mb_strlen($ticket_name,'utf-8')>60)
			        $t_name = mb_substr($ticket_name,0,60,'utf-8').'...';//'bigname';
			   else
			        $t_name = $ticket_name;
			
			   $t_is_paid = is_readable($this->ticket_path . $ticket_name . '.paid') ? 'Paid' : 'Pay';
               $t_name_link = "<a href='admin/{$this->printer_name}/$user/$tdoc_name/".$ticket_name.".png'>" . $t_name  ."</a>";			
               $t_anchor = "<a name='$hash'>".$i++."</a>";			   
			   
			   if ($hash==$ticket_name) {//colorize selected hash
                    $ret .= $ok ? self::printline(array($t_anchor,$ticket_time,$ticket_size,$t_name,$proceed_state,$t_is_paid),
			                           array('left;5%','left;30%','left;10%','left;45%','left;5%','left;5%'),
                                       1,
			                           "center::100%::0::group_article_body::left::0::0::", '#00FF00')
						          :	
                                  self::printline(array($t_anchor,$ticket_time,$ticket_size,$t_name,$proceed_state,$t_is_paid),
			                           array('left;5%','left;30%','left;10%','left;45%','left;5%','left;5%'),
                                       1,
			                           "center::100%::0::group_article_body::left::0::0::", '#FF0000');								  
               }
               else 			   
                    $ret .= self::printline(array($t_anchor,$ticket_time,$ticket_size,$t_name_link,$proceed_state,$t_is_paid),
			                           array('left;5%','left;30%','left;10%','left;45%','left;5%','left;5%'),
                                       0,
			                           "center::100%::0::group_article_body::left::0::0::");									   									   
			}
	    }
		else {
		   $ret .= 'No Tickets';
		}	

        return ($ret);			
    }
	
	protected function set_ticket_status($hash,$state=null) {
	
	    $ticket_status = $state ? $state : 'completed';
	    $url = seturl('t=ticketuseprinter&tdoc='.GetReq('tdoc').'&hash='.$hash.'&state=');//.$state);
	
	    $form = <<<EOF
<select name="" class="ContactBox" onChange="location=this.options[this.selectedIndex].value">
<option value="{$url}pending">Pending</option>
<option value="{$url}completed#$hash">Completed</option>
<!--option value="{$url}processing">Processing</option>
<option value="{$url}enable">Enable</option>
<option value="{$url}disable">Disable</option>
<option value="{$url}reset">Reset</option-->
</select>
EOF;
		
	    return ($form);	
	}
	
	protected function save_ticket_status($hash=null) {
	   
	    $state = GetReq('state');
		$hash = GetReq('hash');
		$tdoc = GetReq('tdoc') ? GetReq('tdoc') : $_SESSION['tdoc'];
		$user = $_SESSION['user'];
		
	    $owner_path = $this->admin_path . $user;
	    $ticket_path = $owner_path . '/' . $tdoc; 			
		$ticket = $ticket_path . '/' . $hash . '.xps';
		$sticket = $ticket_path . '/' . $state . '-' . $hash . '.xps';
		
		//echo $ticket,'<br>',$sticket;
		if (is_readable($ticket)) 
		    return @rename($ticket, $sticket);

		
		return false;		
    }	
	
	protected function show_tickets_list() {
    }	
}
};
?>
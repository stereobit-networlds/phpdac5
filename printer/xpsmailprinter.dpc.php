<?php
$__DPCSEC['XPSMAILPRINTER_DPC']='1;1;1;1;1;1;1;1;1';

if (!defined("XPSMAILPRINTER_DPC")) {
define("XPSMAILPRINTER_DPC",true);

$__DPC['XPSMAILPRINTER_DPC'] = 'xpsmailprinter';

$a = GetGlobal('controller')->require_dpc('printer/UiIPPxpsmail.lib.php');
require_once($a);

//already loaded at UiIPPxpsmail
//$b = GetGlobal('controller')->require_dpc('printer/payqueue.lib.php');
//require_once($b);


$__EVENTS['XPSMAILPRINTER_DPC'][0]='xpsmprinter';
$__EVENTS['XPSMAILPRINTER_DPC'][1]='xpsmshow';
$__EVENTS['XPSMAILPRINTER_DPC'][2]='xpsmxml';
$__EVENTS['XPSMAILPRINTER_DPC'][3]='xpsmjobstats';
$__EVENTS['XPSMAILPRINTER_DPC'][4]='xpsmnetact';
$__EVENTS['XPSMAILPRINTER_DPC'][5]='xpsmlogout';
$__EVENTS['XPSMAILPRINTER_DPC'][6]='xpsmjobs';
$__EVENTS['XPSMAILPRINTER_DPC'][7]='xpsmjobstats';
$__EVENTS['XPSMAILPRINTER_DPC'][8]='xpsmdeljobs';
$__EVENTS['XPSMAILPRINTER_DPC'][9]='xpsmaddprinter';
$__EVENTS['XPSMAILPRINTER_DPC'][10]='xpsmmodprinter';
$__EVENTS['XPSMAILPRINTER_DPC'][11]='xpsmremprinter';
$__EVENTS['XPSMAILPRINTER_DPC'][12]='xpsminfprinter';
$__EVENTS['XPSMAILPRINTER_DPC'][13]='xpsmlogin';
$__EVENTS['XPSMAILPRINTER_DPC'][14]='xpsmuseprinter';
$__EVENTS['XPSMAILPRINTER_DPC'][15]='xpsmconfprinter';
$__EVENTS['XPSMAILPRINTER_DPC'][16]='xpsmproceed';
$__EVENTS['XPSMAILPRINTER_DPC'][17]='xpsmudropbox';

$__ACTIONS['XPSMAILPRINTER_DPC'][0]='xpsmprinter';
$__ACTIONS['XPSMAILPRINTER_DPC'][1]='xpsmshow';
$__ACTIONS['XPSMAILPRINTER_DPC'][2]='xpsmxml';
$__ACTIONS['XPSMAILPRINTER_DPC'][3]='xpsmjobstats';
$__ACTIONS['XPSMAILPRINTER_DPC'][4]='xpsmnetact';
$__ACTIONS['XPSMAILPRINTER_DPC'][5]='xpsmlogout';
$__ACTIONS['XPSMAILPRINTER_DPC'][6]='xpsmjobs';
$__ACTIONS['XPSMAILPRINTER_DPC'][7]='xpsmjobstats';
$__ACTIONS['XPSMAILPRINTER_DPC'][8]='xpsmdeljob';
$__ACTIONS['XPSMAILPRINTER_DPC'][9]='xpsmaddprinter';
$__ACTIONS['XPSMAILPRINTER_DPC'][10]='xpsmmodprinter';
$__ACTIONS['XPSMAILPRINTER_DPC'][11]='xpsmremprinter';
$__ACTIONS['XPSMAILPRINTER_DPC'][12]='xpsminfprinter';
$__ACTIONS['XPSMAILPRINTER_DPC'][13]='xpsmlogin';
$__ACTIONS['XPSMAILPRINTER_DPC'][14]='xpsmuseprinter';
$__ACTIONS['XPSMAILPRINTER_DPC'][15]='xpsmconfprinter';
$__ACTIONS['XPSMAILPRINTER_DPC'][16]='xpsmproceed';
$__ACTIONS['XPSMAILPRINTER_DPC'][17]='xpsmudropbox';


$__DPCATTR['XPSMAILPRINTER_DPC']['xpsmailprinter'] = 'xpsmailprinter,1,0,0,0,0,0,0,0,0,0,0,1';

$__LOCALE['XPSMAILPRINTER_DPC'][0]='XPSMAILPRINTER_DPC;Printer;Εκτυπωτής';
$__LOCALE['XPSMAILPRINTER_DPC'][1]='_SHLOGOUT;Logout;Αποσύνδεση';


class xpsmailprinter extends UiIPPxpsmail {
	
	var $myprinter, $defdir, $message, $procmd;
	var $url_activate, $url_invitate;
	var $test_page;
	
	function __construct() {   
	
	   spl_autoload_register(array($this, 'loader')); //call dropbox api..process_job
	   
	   $this->myprinter = null;
	   $this->message = null;
	   $this->defdir = $_SESSION['indir'] ? $_SESSION['indir'] : '/';//null//'printers'; 

	   $this->procmd = 'xpsm'; 
	   
	   //overwrite
	   $this->printer_name = $_SESSION['printer'] ? $_SESSION['printer'] : $_POST['printername'];	
       $this->printer_name = 'xpsmail.printer'; //<<<<<<<<<<<<<<<<<<<<<<<<<<<< do not select printer	   
							    
	   parent::__construct($this->printer_name,null,null,true,$this->procmd);
	   
	   //when a user has come from a url request for activation or invite a new user
	   $this->url_activate = $_SESSION['ACTIVATION'] ? $_SESSION['ACTIVATION'] : false;	   
	   $this->url_invitate = $_SESSION['INVITATION'] ? $_SESSION['INVITATION'] : false;	

	   $this->test_page = $_SESSION['TESTPAGE'] ? $_SESSION['TESTPAGE'] : false;  
	}
	
    function loader($class){
	   $class = str_replace('\\', '/', $class);
	   require_once($class . '.php');
    } 	

    function event($event=null) {
	
        switch($event)   {
		
		    case 'xpsmudropbox'   :
			                        break;		
		
		    case 'xpsmaddprinter' : 
			case 'xpsmmodprinter' :
			case 'xpsmremprinter' : 
			case 'xpsminfprinter' : 
			case 'xpsmconfprinter': 
			case 'xpsmuseprinter' : 
			case 'xpsmlogin'      : 
			case 'xpsmlogout'     :	
		    case 'xpsmshow'       :
			case 'xpsmxml'        :
			case 'xpsmjobstats'   :
			case 'xpsmact'        :
			case 'xpsmjobs'       : 
			case 'xpsmjobstats'   :
			case 'xpsmdeljob'     :			
			case 'xpsmprinter'    :
			default               : 
			                    
        }			
	}
	
    function action($action=null)  {	
	
        switch($action)   {
		
		    case 'xpsmudropbox'    : $ret = $this->authorize_receiver_dropbox();
			                         break;
		
		    /*case 'xpsmaddprinter' : $ret = $this->form_addprinter(null,null,null,null,null,GetReq('indir'));
			                       break;
			case 'xpsmmodprinter' : $ret = $this->form_modprinter();
			                       break;
								
			case 'xpsmremprinter' : break;

			case 'xpsmuseprinter' : $ret = $this->form_useprinter();
			                       break;		
			case 'xpsmconfprinter': $ret = $this->form_configprinter();
			                       break;									
			case 'xpsminfprinter' : $ret = $this->form_infoprinter();
			                       break;*/									
                                   
			case 'xpsmlogout'  :  self::_logout();
			                      $ret = self::_login();
			                      break;							
			case 'xpsmlogin'   : 
		    case 'xpsmshow'    :
			case 'xpsmxml'     :
			case 'xpsmjobstats':
			case 'xpsmact' :
			case 'xpsmjobs'    : 
			case 'xpsmjobstats':
			case 'xpsmdeljob'  :
			case 'xpsmprinter' : 
			default          :	$login = self::_login();
								if ($login === true) {
								  if ($action=='xpsmlogin') {
								    $cmd = str_replace('xpsm','','xpsminfprinter');
									//echo 'login:',$cmd;
								  } 	
								  else	
								    $cmd = str_replace('xpsm','',$action);
									
								  $ret = $this->printer_console($cmd);
								}
								else
								  $ret = $login;	
												
        }

        return ($ret);		
	}
	
	protected function html_show_instruction_page($page=null,$replace_args=null,$replace_vals=null,$html_page=false,$hasmenu=false, $hasfooter=false, $allow=false) {
	    $page_title = 'instruction page '.$page;
		$title = $this->printer_name;// $page;		
		$insfile = $this->admin_path . $page . '.' . $this->printer_name;//'.htm';
		$printer_url =  "http://" . $_ENV["HTTP_HOST"] .
		                pathinfo($_SERVER['PHP_SELF'],PATHINFO_DIRNAME) .
		                $this->printer_name;
						
		//ONLY IF INVITATION OR ACTIVATION OR NEW USER	
        //echo $page,'>';		
        if (($this->url_invitate) || ($this->url_activate) || ($this->newuser) || ($allow)) {						
		
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
		
	<div id="form_container">
	    $menu
		<div class="form_description">
			<h2>$title</h2>
			<p></p>
		</div>						

		$page_instructions
	
		$footer
	</div>
	<br/>
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
					  
		$printermonitor = $printermon . '?printer='.$this->printer_name;			  
		
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
		/*$message  = "Welcome to dropbox.printer,<br> your username is <b> $refname </b> and you password is <b> $refpass </b>.<br>\r\n";
		$message .= "<br>You can access your account be clicking the link bellow.<br>\r\n";
		if ($activation_link)
		  $message .= 'Click ' . $activation_link . ".<br>\r\n";
		if ($invitation_link)  
		  $message .= 'Click ' . $invitation_link . ".\r\n";	
        */
        $message = $this->html_show_instruction_page('send-mail',array('[PRINTERNAME]','[USERNAME]','[PASSWORD]','[ACTLINK]','[INVLINK]','[PRINTERMON]'),
		                                                         array($this->printer_name, $refname, $refpass, $activation_link, $invitation_link, $printermonitor),
																 true);		  
		
		$from = $this->printer_name.'@'.$_ENV["HTTP_HOST"];//'balexiou@stereobit.com'
        $ok = $this->_sendmail($from,$mail,$this->printer_name .' activation',$message);
		//notify
        $ok2 = $this->_sendmail($from,'info@smart-printers.net',$this->printer_name .' user activation',$mail . $message);		
		
		if ($ok) {
		    //echo $refname,'-',$this->username;
		    $ret = $this->_save_mail_relation($mail,$refname,$this->username);
			return ($ret);
			/*
		    $path = $_SERVER['DOCUMENT_ROOT'];// . pathinfo($_SERVER['PHP_SELF'],PATHINFO_BASENAME);
		    //echo $path;
		    //$ret = $this->write2disk($path.'/dropbox-listmail.php',"\r\n'".$mail."',");
			$file = $path.'/dropbox-listmail.php';
            $data = "\r\n \$dropbox_listmail['$mail'] = '".$refname.'<'.$this->username."';"; //save referer and email
				
		    if ($fp = @fopen ($file , "a+")) {
                fwrite ($fp, $data);
                fclose ($fp);
                return true;
            }
            else 
			    return false;*/
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
			    $printerusers[$user] = hash('crc32',$pass);//$pass;
			    			
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
			  if ($testpage_id = $this->send_test_page(null,$printername, $printerdir, $user))
			     $_SESSION['TESTPAGE'] = $testpage_id;  
			  
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
	
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>			
		
	<div id="form_container">
	
		<form id="form_470441" class="appnitro"  method="post" action="">
					<div class="form_description">
			<h2>Login $message $printername</h2>
			<p>Please enter your details to access the printer.</p>
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
		<div id="footer">
		$this->printer_name
		</div>
	</div>

EOF;
	
        return ($form);	
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
		    ($allowed_users[$printeruser]== hash('crc32',$printerpass))) {
			
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
		    //print_r($params);
		    if (empty($params))
		        return ('Unknown printer file!');
				
		    $printerusers = (array) $params['users'];
		   
		    if ($_POST['FormAction']!=$cmd) {
			
                if ($this->url_activate) {
				  $ret .= $this->html_show_instruction_page('account-activated');
                  $ret .= self::html_window(null, 'Account activated'/* .('.implode('-',$this->url_activate).'-'.$this->username.')'*/, $this->printer_name);			
				}  
				elseif ($this->url_invitate) {
				  if ($this->newuser) {
				    //$ret .= $this->html_show_instruction_page('user-defined');
                    $ret .= self::html_window(null, 'User ('.$this->newuser.') defined.', $this->printer_name);			
				  }	
				  else {
				    //$ret .= self::html_window(null, 'User ('.$this->username.') logged in.', $this->printer_name);
				    $ret .= $this->html_show_instruction_page('invite-user');
				    $ret .= $this->add_user_printer_form(null,$printername,$params['users'],$printerdir);
					//$ret .= self::html_window(null, 'Invitation info .('.implode('-',$this->url_invitate).'-'.$this->username.')', $this->printer_name);			
				  }	
				}				
				else {//show data
				  if ($this->newuser) {
				    //$ret .= $this->html_show_instruction_page('user-defined');
				    $ret  = $this->html_show_instruction_page('user-post');
                    $ret .= self::html_window(null, 'User ('.$this->newuser.') defined.', $this->printer_name);			
				  }	
				  else {				
				    $ret .= $this->html_show_instruction_page('log-in');
				    //$ret .= self::html_window(null, 'User ('.$this->username.') logged in.', $this->printer_name);
//>>>>>>>>>>>>>>>>  //test....
				    $ret .= $this->add_user_printer_form(null,$printername,$params['users'],$printerdir);
					
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
					    //NOT NOW ..WAIT FOR ACTIVATION
			            /*$printerusers[$u] = $p;
			    			
                        $ok = $this->html_mod_printer($printername,
		                                              null,
					 				                  null,
									                  $printerusers,
									                  $printerdir);*/ 
						$ok = true;							  
						if ($ok) {
						
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
						    $ret .= $this->add_user_printer_form('. Unable to save user!',$printername,$printerusers,$printerdir);					
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
		//else
		$ret = parent::form_useprinter($printername, $indir);
		return ($ret);
		
    }	
	
	//override..for email request and quota check, user not allowed to add...
	public function add_user_printer_form($message=null, $name=null, $users=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;
		$cmd = $this->external_use ? $this->procmd.'useprinter':'useprinter';	
	
	    $menu = $this->html_printer_menu(true);
		
		//quota-renew message for logged in users
		if (($this->username) && (!$this->url_invitate) && (!$this->url_activate)) {
			//if ($renew = $this->get_printer_limit($message))//..not only if true renew
			$this->get_printer_limit($quota_message);	
            return (self::html_window(null, $quota_message, $this->printer_name));			
		}
		else {
		
		    if (($this->url_invitate) || ($this->url_activate)) {
				$form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu 
		$quota_message
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
			<input id="element_1_2" name= "password" class="element text" maxlength="13" size="14" value=""/>
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
		<div id="footer">
		$this->printer_name
		</div>
	</div>
	<br/>

EOF;
				return ($form);				
			}
			else {
				$quota_message = null;
				return (self::html_window(null, $quota_message, $this->printer_name));
			}
		}
		
	
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
			//FILE NOW IS THE DROPBOX TOKEN FILE CREATED DURING 'ALLOW APP' PROCEDURE
			$file = $this->admin_path . md5($myuser) . '.token';
				
            //if (is_readable($file)) {	//if dropbox token file				
			
            if ($this->url_activate) {//echo 'a';
			
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
			    
			    //if ($_POST['dbusername']) //dropbox account data submited..step3
				
				if ($_POST['dbfolder']) //dropbox dbfolder post data submited..
				  $ret  = $this->html_show_instruction_page('dropbox-post');
				elseif ($_GET['oauth_token'])	//..returing from allow app procedure
			      $ret  = $this->html_show_instruction_page('dropbox-edit');
				else  //????
				  $ret  = $this->html_show_instruction_page('dropbox-edit'); 
				  
                $ret .= $this->config_filter_form_dropbox('dboxsave', $printername, $code, $indir);	
				//$ret .= $this->config_filter_form_xps2mail('xps2mail', $printername, $code, $indir);
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
                    $ret .= self::html_window(null, null, $this->printer_name);//'Not a valid user!', $this->printer_name);			   
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
				
				  //if ($_POST['dbusername']) //dropbox account data submited..step3
				  /*if ($_POST['dbfolder']) //dropbox dbfolder post data submited..
				    $ret  .= $this->html_show_instruction_page('dropbox-post');
				  else
			        $ret  .= $this->html_show_instruction_page('dropbox-edit');	
					*/
				  $ret .= $this->config_filter_form_dropbox('dboxsave', $printername, $code, $indir);				
				  $ret .= $this->config_filter_form_xps2mail('xps2mail', $printername, $code, $indir);
				//}
			}   
			   
			return ($ret);
		}   

        $ret = parent::form_configprinter($printername, $indir);  
		return ($ret);	
    }	
	
	//override for goto step 3
	protected function config_filter_form_dropbox($filter=null, $printername=null, $code=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;
	    $dir = $indir ? $indir.'/' : ($_SESSION['indir'] ? $_SESSION['indir'] .'/' : '/');
		$filter = $_POST['filtername'] ? $_POST['filtername'] : $filter;
		$cmd = $this->external_use ? $this->procmd.'confprinter':'confprinter';
		
		//$file = $_SERVER['DOCUMENT_ROOT'] .'/'.$dir . str_replace('.printer','',$printername).'.'.$filter.'.php';
		if ($this->username!=$this->get_printer_admin()) {
		    $myuser = $this->newuser ? $this->newuser : $this->username;
		    $userstr = '-'.$myuser;
		}	
		else {
		    $myuser = $this->username;
           	$userstr = null;	
		}	
			
		//$file = self::get_printer_path() . str_replace('.printer','',$printername).'.'.$filter.$userstr.'.php'; 
		//echo $file,'>',$code;
		
		$file = $this->admin_path . $filter.$userstr.'-conf'.'.php';
		
		$dp_ui = null;
		//print_r($_POST);
		if (($_POST['dbfolder']) || ($_POST['dbfext'])) {
		    //echo '>>>',$dbfext,'|',$_POST['dbfext'];
			$dbfext = $_POST['dbfext'];
			$dbfolder = $_POST['dbfolder'];
			
		    $db_code = "<?php
\$dbfolder = $dbfolder;	
\$dbfext = $dbfext;			
?>		  
";
		    //save file...		  
		    @file_put_contents($file, $db_code);
			
			/*if (($this->url_activate) || ($this->url_invitate)) {
				//goto step 3.....................................
				$form = $this->form_infoprinter();  			
		        return ($form);			
			}*/	
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
			
		}	
        else {	
            //load file		
		    if (is_readable($file)) {
			    $cnt = file($file,FILE_SKIP_EMPTY_LINES);

			    //scan for dropbox save folder
			    $parts = explode("=",$cnt[1]);
			    $dbfolder = trim(str_replace(';','',$parts[1]));
				//scan for file extension
			    $parts = explode("=",$cnt[2]);
			    $dbfext = trim(str_replace(';','',$parts[1]));				
            }		
		}		
		  
		$menu = $this->html_printer_menu(true);  

	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu 
		<form id="form_470441" class="appnitro" enctype="multipart/form-data" method="post" action="">
					<div class="form_description">
			<h2>Set Dropbox account data for $myuser $message</h2>
			<p>Dropbox configuration.</p>
		</div>						
			<ul >		

        $dp_ui	
		
		<li id="li_0" >
		<label class="description" for="element_0">Dropbox folder </label>
		<div>
			<input id="element_0" name="dbfolder" class="element text medium" type="text" maxlength="20" value="$dbfolder"/> 
		</div><p class="guidelines" id="guide_0"><small>Please specify a dropbox folder to save outputs</small></p> 
		</li>			
		<li id="li_1" >
		<label class="description" for="element_1">Dropbox file extension </label>
		<div>
			<input id="element_1" name="dbfext" class="element text medium" type="text" maxlength="20" value="$dbfext"/> 
		</div><p class="guidelines" id="guide_1"><small>Please specify a file extension to append the saved file name</small></p> 
		</li>			
			
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="$cmd" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
		<div id="footer">
		$this->printer_name
		</div>
	</div>
	<br/>

EOF;
        return ($form);		
	}
	
	//xps2mail second filter
	protected function config_filter_form_xps2mail($filter=null, $printername=null, $code=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;
	    $dir = $indir ? $indir.'/' : ($_SESSION['indir'] ? $_SESSION['indir'] .'/' : '/');
		$filter = $_POST['filtername'] ? $_POST['filtername'] : $filter;
		$cmd = $this->external_use ? $this->procmd.'confprinter':'confprinter';
		
		//$file = $_SERVER['DOCUMENT_ROOT'] .'/'.$dir . str_replace('.printer','',$printername).'.'.$filter.'.php';
		if ($this->username!=$this->get_printer_admin()) {
		    $myuser = $this->newuser ? $this->newuser : $this->username;
		    $userstr = '-'.$myuser;
		}	
		else {
		    $myuser = $this->username;
           	$userstr = null;	
		}	
			
		//$file = self::get_printer_path() . str_replace('.printer','',$printername).'.'.$filter.$userstr.'.php'; 
		//echo $file,'>',$code;
		
		$file = $this->admin_path . $filter.$userstr.'-conf'.'.php';
		
		$dp_ui = null;
		//print_r($_POST);
		if (($_POST['xpssendmail']) || ($_POST['xpssign'])) {
		    //echo '>>>',$xpssendmail,'|',$_POST['xpssign'];
			$xpssendmail = $_POST['xpssendmail'];
			$xpssign = $_POST['xpssign'];
			
		    $db_code = "<?php
\$xpssendmail = $xpssendmail;	
\$xpssign = $xpssign;			
?>		  
";
		    //save file...		  
		    @file_put_contents($file, $db_code);
			
			if (($this->url_activate) || ($this->url_invitate)) {
				//goto step 3.....................................
				$form = $this->form_infoprinter();  			
		        return ($form);			
			}	
		}	
        else {	
            //load file		
		    if (is_readable($file)) {
			    $cnt = file($file,FILE_SKIP_EMPTY_LINES);

			    //scan for dropbox save folder
			    $parts = explode("=",$cnt[1]);
			    $xpssendmail = trim(str_replace(';','',$parts[1]));
				//scan for file extension
			    $parts = explode("=",$cnt[2]);
			    $xpssign = trim(str_replace(';','',$parts[1]));				
            }		
		}		
		  
		//$menu = $this->html_printer_menu(true);  //deactivate as second filter

	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu 
		<form id="form_470441" class="appnitro" enctype="multipart/form-data" method="post" action="">
					<div class="form_description">
			<h2>Set XPSmail account data for $myuser $message</h2>
			<p>XPSmail configuration.</p>
		</div>						
			<ul >		

        $dp_ui	
		
		<li id="li_0" >
		<label class="description" for="element_0">XPS sendmail </label>
		<div>
			<input id="element_0" name="xpssendmail" class="element text medium" type="text" maxlength="20" value="$xpssendmail"/> 
		</div><p class="guidelines" id="guide_0"><small>Allow a copy to send by mail at current scanned document accounts</small></p> 
		</li>			
		<li id="li_1" >
		<label class="description" for="element_1">XPS Sign </label>
		<div>
			<input id="element_1" name="xpssign" class="element text medium" type="text" maxlength="20" value="$xpssign"/> 
		</div><p class="guidelines" id="guide_1"><small>Allow XPS signing</small></p> 
		</li>			
			
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="$cmd" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
		<div id="footer">
		$this->printer_name
		</div>
	</div>
	<br/>

EOF;
        return ($form);		
	}	

	//override
	public function form_infoprinter($printername=null, $indir=null) {
	    $printername = $printername ? $printername : $this->printer_name;
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
		
        if ($this->username!=$this->get_printer_admin()) {		
		
		    $filter = 'dboxsave';		
		    $myuser = $this->url_invitate ? $this->newuser : ($this->newuser ? $this->newuser : $this->username);
			
			if ($myuser) {
		        //$userstr = '-'.$myuser;
			    //$file = $this->admin_path . $filter.$userstr.'-conf'.'.php';
				//FILE NOW IS THE DROPBOX TOKEN FILE CREATED DURING 'ALLOW APP' PROCEDURE
				$file = $this->admin_path . md5($myuser) . '.token';
				
                if (is_readable($file)) {	//if dropbox token file
				
		            $joblist = self::html_get_printer_jobs_info();
		            $ret  = self::html_window(null, $joblist, $this->printer_name);	
                    $ret .= $this->html_show_instruction_page('job-list');	
                }
				else {
				
		            $ret  = $this->html_show_instruction_page('dropbox-error');
		            $ret .= self::html_window(null, 'Not a valid dropbox account.', $this->printer_name);//'Not a valid dropbox account!', $this->printer_name);
			    }
		        return ($ret);				
			}
            else {
			    $ret  = $this->html_show_instruction_page('user-error');
                $ret .= self::html_window(null, null, $this->printer_name);//'Not a valid user!', $this->printer_name);  		
				return ($ret);			
            }			
		    
			/*
		    if ($this->url_activate) {
                $userstr = '-'.$myuser;
			    //$file = self::get_printer_path() . str_replace('.printer','',$printername).'.'.$filter.$userstr.'.php';			
			    $file = $this->admin_path . $filter.$userstr.'-conf'.'.php';
		    }  
            elseif ($this->url_invitate) {
		        if ($myuser) {
                    $userstr = '-'.$myuser;
			        //$file = self::get_printer_path() . str_replace('.printer','',$printername).'.'.$filter.$userstr.'.php';			
				    $file = $this->admin_path . $filter.$userstr.'-conf'.'.php';
			    }
		        else {
			        $ret  = $this->html_show_instruction_page('user-error');
                    $ret .= self::html_window(null, null, $this->printer_name);//'Not a valid user!', $this->printer_name);  		
				    return ($ret);
			    }	
		    }	
		    else {
		    //if($this->newuser)
				//$ret = $this->html_show_instruction_page('user-post');
				
                $userstr = '-'.$myuser;
			    //$file = self::get_printer_path() . str_replace('.printer','',$printername).'.'.$filter.$userstr.'.php';
			    $file = $this->admin_path . $filter.$userstr.'-conf'.'.php';
		    }
			
		    //echo $file;	
		    if (($file) && (!is_readable($file))) {	//if no dropbox conf
		        $ret .= $this->html_show_instruction_page('dropbox-error');
		        $ret .= self::html_window(null, 'Not a valid dropbox account.', $this->printer_name);//'Not a valid dropbox account!', $this->printer_name);
			    return ($ret);
		    }	
			
		    $joblist = self::html_get_printer_jobs_info();
		    $ret .= self::html_window(null, $joblist, $this->printer_name);	
            $ret .= $this->html_show_instruction_page('job-list');		
		    return ($ret);
		    */ 
		}//if
		
		//$ret = parent::form_infoprinter($printername, $indir);
		//override
		if ($this->username!=$this->get_printer_admin()) {
		    $ret = self::html_get_printer_jobs_info();
		}	
		else {
		    $ret = self::info_printer_form();
			$ok = self::html_info_printer($printername, $printerdir); 		
		    $ret .= $ok ? $ok : 'Failed to fetch info!';
		}
        $ret = self::html_window(null, $ret, 'xpsmail.printer');		
		//..override
		
		return ($ret);
    }	
	
	//override to search jobs
	protected function html_get_printer_jobs_info() {
	    $user = $this->newuser ? $this->newuser : ($this->username ? $this->username : $_SESSION['user']);	
		$jstate = array(); 
		
        if (!is_dir($this->jobs_path))
		  return null;  		  
		  
        $posted_search = $_POST['stext'];
		
        $printer_state = null;	
		
        //search form	
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
<!--script type="text/javascript" src="calendar.js"></script-->
		
	<!--div id="form_container"-->
		<form id="form_search" class="appnitro" enctype="multipart/form-data" method="post" action="">	
		<div class="form_description">
			<h2>Search </h2>
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
			
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="xpsminfprinter" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
		</ul>
		</form>	
	<!--/div-->
EOF;
        $ret = $form;		
		
		chdir($this->jobs_path); 
		array_multisort(array_map('filemtime', ($files = glob("job-*.*"))), SORT_DESC, $files); 
		foreach($files as $fileread) 
		{ 
			//echo "<li>".substr($fileread, 0, -4)."</li>";  
			
		    //if (substr($fileread,0,4)=='job'.FILE_DELIMITER) { //no need...glob
				//echo $fileread,'<br>';
			    $pf = explode(FILE_DELIMITER,$fileread);
				$jid = $pf[1];//sort	
                $job_owner = $pf[3];
				$job_name = $pf[4];
				
			    if (($user==$this->get_printer_admin()) || ($job_owner==$user) || (!defined('AUTH_USER'))) {				
			
			        //search
					//if (($posted_search) && (stristr($job_name, $posted_search)==false))
					  //  continue;
					if ($posted_search) {
					    $txtfile = str_replace('job-','txt-',str_replace(FILE_DELIMITER.'completed','',$fileread)).'.txt';
						if (is_readable($this->jobs_path . $txtfile)) {
							$test_xps_data = @file_get_contents($this->jobs_path . $txtfile);					
							//echo $this->jobs_path ,$txtfile,$test_xps_data,'<br/><br/>';
						}
						else
						    $test_xps_data = 'null';//must have value to stristr
							
						if ((stristr($job_name, $posted_search)==false) &&
						    (stristr($test_xps_data, $posted_search)==false))
							continue;  
					}					
					
                    $jobs[intval($jid)] = $fileread;
					
				    if (stristr($fileread,FILE_DELIMITER.'completed'))
					    $jstate[intval($jid)] = 'completed';
					elseif (stristr($fileread,FILE_DELIMITER.'processing'))
					    $jstate[intval($jid)] = 'processing';
					elseif (stristr($fileread,FILE_DELIMITER.'pending'))
					    $jstate[intval($jid)] = 'pending';
					else
					    $jstate[intval($jid)] = 'pending';										  
				}
			//}			
		} 		
		
		//$ret = '<h1>' . $user . '&nbsp;documents</h1>';		
		$ret .= self::printline(array('No','job-id','Date-time','Delivery','Job-Name','Status'),
		                        array('left;5%','left;5%','left;20%','left;20%','left;40%','left;10%'),
		 					    1,
			                    "center::100%::0::group_article_body::left::0::0::");	
				
		if (is_array($jobs)) {

			//krsort($jobs); //<<no sort if new read by glob
		    $i=1;
		    foreach ($jobs as $jid=>$fileread) {
			   $fp = explode(FILE_DELIMITER,$fileread);
	           $job['id'] = $fp[1];
	           $job['remote-ip'] = str_replace('~',':',$fp[2]);
	           $job['user-name'] = $fp[3];
		       $job['job-name'] = $fp[4];
			   
			   //file post time
			   $file_post_time = date ("F d Y H:i:s.", filemtime($this->jobs_path.$fileread));
			   
			   //read track data
			   $trackfile = str_replace('job-','track-',str_replace(FILE_DELIMITER.'completed','',$fileread)).'.track';
			   if (is_readable($this->jobs_path . $trackfile)) {
			     
					//$track_data = str_replace(',','<br/>',@file_get_contents($this->jobs_path . $trackfile));					
					$tdata = array_unique(explode(',',@file_get_contents($this->jobs_path . $trackfile)));
					$track_data = implode('<br/>',$tdata);
			   }		
			   else
				    $track_data = 'none';//must have value to stristr


			   
			   if ($jstate[$job['id']]=='completed') {
				 
				 //search-read txt data for signed document
			     $txtfile = str_replace('job-','txt-',str_replace(FILE_DELIMITER.'completed','',$fileread)).'.txt';
			     if ((is_readable($this->jobs_path . $txtfile)) &&
				     ($txtdata = @file_get_contents($this->jobs_path . $txtfile)) && 
 				     (is_readable($this->jobs_path . strtoupper(hash('sha1',$txtdata)).'.xps')) ) {
					 
					 $xpsfile = strtoupper(hash('sha1',$txtdata)).'.xps';
					 $jobname = strtoupper(hash('sha1',$txtdata));
				 }
				 else {//standart job file
				     $xpsfile = 'out-'.$job['id'].'-'.$job['remote-ip'].'-'.$job['user-name'].'-'.$job['job-name'].'.xps';
					 $jobname = $job['job-name'];
				 } 	 
					 
				 $proceed_state = "<a href='jobs/".$this->printer_name."/".$xpsfile."'>" . $jstate[$job['id']]  ."</a>";
			   }	 
			   else	 
			     $proceed_state = $jstate[$job['id']];
		   			   
		   
               $ret .= self::printline(array($i++,$job['id'],/*$job['remote-ip']*/$file_post_time,/*$job['user-name']*/$track_data,$jobname,/*$jstate[$job['id']]*/$proceed_state),
			                           array('left;5%','left;5%','left;20%','left;20%','left;40%','left;10%'),
                                       0,
			                           "center::100%::0::group_article_body::left::0::0::");		   
			}
	    }
		else {
		   $ret .= 'No documents';
		}				

        return ($ret);			
	}	
	
	//override  
	protected function html_get_printer_menu($iconsview=null, $p=null) {
		$urlicons = 'icons/';	
        $icons = array();		
		$user = $this->username ? $this->username : $_SESSION['user'];
		$indir = $_SESSION['indir'] ? $_SESSION['indir'] : $_GET['indir'];
		
		if ($this->username!=$this->get_printer_admin()) {
		    //choose icons
		    if (($this->url_invitate) || ($this->url_activate) || ($this->newuser)) { 
		        $icons[] = $this->urlpath."?".$this->cmd."useprinter:one";
			    $icons[] = $this->urlpath."?".$this->cmd."confprinter:two";
                $icons[] = $this->urlpath."?".$this->cmd."infprinter:three";		
			    //$icons[] = $this->urlpath."?".$this->cmd."logout:logout";
			}
			else {
		        $icons[] = $this->urlpath."?".$this->cmd."useprinter:Printer Users";
			    $icons[] = $this->urlpath."?".$this->cmd."confprinter:Printer Configuration";
                $icons[] = $this->urlpath."?".$this->cmd."infprinter:Printer Info";		
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
	
	//override get printer limit
	protected function get_printer_limit(&$message) {
	    $bootstrapfile = $_SERVER['DOCUMENT_ROOT'] .'/'.str_replace('.printer','',$this->printer_name).'.php';	
	    //echo '<br>'.$bootstrapfile;
		$ret = false;
		//set quota values
        $this->user_quota = self::get_user_quota();
		$this->printer_quota = self::get_printer_quota(null,null,$bootstrapfile);
		
        $item = 'xpsmail@'.$this->username.'@'.$this->printer_name;  		
        $renew_link = "<a href='http://www.stereobit.gr/download.php?g=$item'>" . 'Feed the Printer'. "($this->username : $this->user_quota > $this->printer_quota)"  ."</a>";		
		
		//paypal pay
        $pay = new payqueue($this->username, $this->printer_name, $this->admin_path);
        $paybutton = $pay->paybutton('test p', 1.99, 'balexiou@stereobit.com'); 
        unset($pay);	
	
		//...warning/renew form
		$form = '
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>		
		<div id="form_container">
		<form id="form_quota_warning" class="appnitro" enctype="multipart/form-data" method="post" action="">
		<div class="form_description">
			<h2>Printer Quota</h2>
			<p>If print jobs quota reach the limit, please renew to reset jobs.</p>
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
		</li>
		</ul>
		</form></div>';		
		
		//echo $this->username,':',$this->user_quota,'>',$this->printer_quota,'<br>';
		if (intval($this->user_quota) > $this->printer_quota) {
		
		    $qdiff = 0;	
			$paymsg = $this->html_show_instruction_page('quota-pay',array('[QUOTALIMIT]','[PAYLINK]'),
		                                                        array($qdiff, $paybutton),
															    false,false,false,true);			
			   
			//$message = $this->html_window("Renew", $renew_link, $this->printer_name,true);
			$message = $paymsg.$form;
			return true;//($ret); ...stop procced jobs...
		}
		else { //send warning
		   
		    $qdiff = abs($this->printer_quota - $this->user_quota);
			//echo $qdiff % 10;//<<10th time
			$paymsg = $this->html_show_instruction_page('quota-pay',array('[QUOTALIMIT]','[PAYLINK]'),
		                                                        array($qdiff, $paybutton),
															    false,false,false,true);			
			
			if ($qdiff<=1) {
			    //...renew form 
				//$message = $this->html_window("Renew", $renew_link, $this->printer_name,true);
				$message = $paymsg.$form;
			}
			elseif ($qdiff<10) {//10 left..send/show warning
			
			    //if ($ok = $this->mail_printer_limit($qdiff, $renew_link)) {
			        //$message = $this->html_window("Quota Warning ", $form, $this->printer_name, true);
				//}	
				
				$message = $paymsg.$form;
			}
			elseif (($qdiff<50) && ($qdiff % 10 === 0))  {//on 10th send warning..
			
			    //$ok = $this->mail_printer_limit($qdiff, $renew_link);
				//$message = $this->html_window("Quota Warning ", "<h2>REMAINING:".$qdiff.'</h2>', $this->printer_name,true);
				$message = $paymsg.$form;
			}
			elseif ($qdiff<50) {
				$message = $paymsg.$form;			
			}
			//else just the form
			else {
				//$message = $this->html_window("Quota Info ", "<h2>REMAINING:".$qdiff.'</h2>', $this->printer_name,true);			
				$message = $form;
			}
		}

        return false;		
	}	
	
	//override
	protected function mail_printer_limit($timesleft=null, $rlink=null) {
	    $tdiff = $timesleft ? $timesleft : 0;
		$renew_link = $rlink ? $rlink : null;
	
	    if (!$mail = $this->tax_user_email) //<<<<<<<<<<<<<<<<<<<<<<<<<<
		    return false;
	
        //send mail
		$from = $this->printer_name . '@' . str_replace('www.','',$_ENV["HTTP_HOST"]);		
		$subject = $this->printer_name . ' quota warning';
        $message = $this->html_show_instruction_page('quota-warning',array('[PRINTERNAME]','[USERNAME]','[USERQUOTA]','[PRINTERQUOTA]','[LIMIT]','[RENEWLINK]'),
		                                                         array($this->printer_name, $this->username, $this->user_quota, $this->printer_quota, $tdiff, $renew_link),
																 true,false,false,true);																		 
		
        $ok = $this->_sendmail($from,$mail,$subject,$message);
		//notify
        //$ok2 = $this->_sendmail($from,$this->notify_mail,$subject, $mail . $message);		
		
		return ($ok);
	}

	//override 
    /*protected function printline($dat=null,$att=null,$isbold=false,$render=null) {
	    $ret = null;
		$isarray = is_array($att);
		
	    if (is_array($dat)) {
		
		   foreach ($dat as $i=>$f) {
		   
			   $data[$i] = $isbold ? '<strong>'.$f.'</strong>':$f; 
	           $attr[$i] = $isarray ? $att[$i] : $att;			      
		   }
		   
	       //$win = new window('',$data,$attr);
		   //$ret = $win->render($render);
		   $ret = "\r\n<table><tr>";
		   foreach ($data as $t=>$title) {
		     $attribute = explode(';',$attr[$t]);
		     $ret .= '<td align="'.$attribute[0].'" width="'.$attribute[1].'" valign="top">'.$title.'</td>';
		   }	 
		   $ret .= "</tr></table>";
		}
		
		return ($ret);
    }*/	

	//override
	protected function html_window($title=null, $data=null, $footer_title=null) {
	    $ver = $this->server_name . $this->server_version;	
		$footer_title = $footer_title ? $footer_title :	$ver.'&nbsp;|&nbsp;'.$this->logout_url;
		
	    $menu = $this->html_printer_menu(true);
        return ($menu . $data); //<<<		
	
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu
		<div class="form_description">
			<h2>$title</h2>
			<p></p>
		</div>						

		$data
		<hr/>	
		<div id="footer">
        $footer_title
		</div>
	</div>
	<br/>

EOF;

        return ($form); 	
	}
	
	
	//receiver (by mail) can enable dropbox capabilities
	//protected function enable_receiver_dropbox() {
    //}	

	protected function authorize_receiver_dropbox() {
	
        /*if ($_GET['oauth_token']) { //..returing from allow app procedure	
		    $ret = 'Authorized!';
			return ($ret);
		}*/
	
	    $receiver_email = $_GET['email'];
		$printer_name = $_GET['printer'];
		if (!$receiver_email) return false;

		//execute test page for allow app callback dropbox ..directly call api
        $app_key = "geuq6gm2b5glofq";
	    $app_secret = "5s9jvk2zd5oc0hq";

        try {
            // Check whether to use HTTPS and set the callback URL
            $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
            $callback = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];	
	
	        // Instantiate the Encrypter and storage objects
            // $key is a 32-byte encryption key (secret)
            $key = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
            $encrypter = new \Dropbox\OAuth\Storage\Encrypter($key);

            // User ID assigned by your auth system (used by persistent storage handlers)
            $userID = $receiver_email;//$username;
		  
            // Create the storage object, passing it the Encrypter object
            //$storage = new \Dropbox\OAuth\Storage\Session($encrypter); 
		    $storage = new \Dropbox\OAuth\Storage\Filesystem($encrypter, $userID);
	        $storage->setDirectory($this->admin_path);
 
            $OAuth = new \Dropbox\OAuth\Consumer\Curl($app_key, $app_secret, $storage, $callback);
            $dropbox = new \Dropbox\API($OAuth);
 
		    //$ret .= "<br>DBOXSAVE FILENAME ($userID):". $file_name ."<br>";
	
            // Upload the file with an alternative filename
			if ($_GET['oauth_token']) {
				$file_name = $this->admin_path .'/test.txt';
				if (!is_readable($file_name))
					@file_put_contents($file_name,'test');
					
				$d = $dropbox->putFile($file_name,null,null,true); //alt name,path,override
				$ret = $d ? 'Authorized' : 'Not authorized';
			}
	        
        } 
	    catch(\Dropbox\Exception $e) {
	        $ret = $e->getMessage();
	        //exit('Setup failed! Please try running setup again.');
        }			

		//in case of true file_read has no renamed to complete..always pending..
        return ($ret);		
    } 		
	
}
};
?>
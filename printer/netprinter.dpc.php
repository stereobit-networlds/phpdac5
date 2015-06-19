<?php
$__DPCSEC['NETPRINTER_DPC']='1;1;1;1;1;1;1;1;1';

if (!defined("NETPRINTER_DPC")) {
define("NETPRINTER_DPC",true);

$__DPC['NETPRINTER_DPC'] = 'netprinter';

$a = GetGlobal('controller')->require_dpc('printer/UiIPPnetko.lib.php');
require_once($a);


$__EVENTS['NETPRINTER_DPC'][0]='netprinter';
$__EVENTS['NETPRINTER_DPC'][1]='netshow';
$__EVENTS['NETPRINTER_DPC'][2]='netxml';
$__EVENTS['NETPRINTER_DPC'][3]='netjobstats';
$__EVENTS['NETPRINTER_DPC'][4]='netnetact';
$__EVENTS['NETPRINTER_DPC'][5]='netlogout';
$__EVENTS['NETPRINTER_DPC'][6]='netjobs';
$__EVENTS['NETPRINTER_DPC'][7]='netjobstats';
$__EVENTS['NETPRINTER_DPC'][8]='netdeljobs';
$__EVENTS['NETPRINTER_DPC'][9]='netaddprinter';
$__EVENTS['NETPRINTER_DPC'][10]='netmodprinter';
$__EVENTS['NETPRINTER_DPC'][11]='netremprinter';
$__EVENTS['NETPRINTER_DPC'][12]='netinfprinter';
$__EVENTS['NETPRINTER_DPC'][13]='netlogin';
$__EVENTS['NETPRINTER_DPC'][14]='netuseprinter';
$__EVENTS['NETPRINTER_DPC'][15]='netconfprinter';


$__ACTIONS['NETPRINTER_DPC'][0]='netprinter';
$__ACTIONS['NETPRINTER_DPC'][1]='netshow';
$__ACTIONS['NETPRINTER_DPC'][2]='netxml';
$__ACTIONS['NETPRINTER_DPC'][3]='netjobstats';
$__ACTIONS['NETPRINTER_DPC'][4]='netnetact';
$__ACTIONS['NETPRINTER_DPC'][5]='netlogout';
$__ACTIONS['NETPRINTER_DPC'][6]='netjobs';
$__ACTIONS['NETPRINTER_DPC'][7]='netjobstats';
$__ACTIONS['NETPRINTER_DPC'][8]='netdeljob';
$__ACTIONS['NETPRINTER_DPC'][9]='netaddprinter';
$__ACTIONS['NETPRINTER_DPC'][10]='netmodprinter';
$__ACTIONS['NETPRINTER_DPC'][11]='netremprinter';
$__ACTIONS['NETPRINTER_DPC'][12]='netinfprinter';
$__ACTIONS['NETPRINTER_DPC'][13]='netlogin';
$__ACTIONS['NETPRINTER_DPC'][14]='netuseprinter';
$__ACTIONS['NETPRINTER_DPC'][15]='netconfprinter';


$__DPCATTR['NETPRINTER_DPC']['netprinter'] = 'netprinter,1,0,0,0,0,0,0,0,0,0,0,1';

$__LOCALE['NETPRINTER_DPC'][0]='NETPRINTER_DPC;Printer;Εκτυπωτής';
$__LOCALE['NETPRINTER_DPC'][1]='_SHLOGOUT;Logout;Αποσύνδεση';


class netprinter extends UiIPPnetko {
	
	var $myprinter, $defdir, $message, $procmd;
	
	function __construct() {   
	   
	   $this->myprinter = null;
	   $this->message = null;
	   $this->defdir = $_SESSION['indir'] ? $_SESSION['indir'] : '/';//null//'printers'; 

	   $this->procmd = 'net'; 
	   
	   //overwrite
	   $this->printer_name = $_SESSION['printer'] ? $_SESSION['printer'] : $_POST['printername'];	   
							    
	   parent::__construct($this->printer_name,null,null,true,$this->procmd);
	   
	}

    function event($event=null) {
	
        switch($event)   {
		
		    case 'netaddprinter' : 
			case 'netmodprinter' :
			case 'netremprinter' : 
			case 'netinfprinter' : 
			case 'netconfprinter': 
			case 'netuseprinter' : 
			case 'netlogin'      : 
			case 'netlogout'     :	
		    case 'netshow'       :
			case 'netxml'        :
			case 'netjobstats'   :
			case 'netnetact'     :
			case 'netjobs'       : 
			case 'netjobstats'   :
			case 'netdeljob'     :			
			case 'netprinter'    :
			default             : 
			                    
        }			
	}
	
    function action($action=null)  {	
	
        switch($action)   {
		
		    /*case 'netaddprinter' : $ret = $this->form_addprinter(null,null,null,null,null,GetReq('indir'));
			                       break;
			case 'netmodprinter' : $ret = $this->form_modprinter();
			                       break;
								
			case 'netremprinter' : break;

			case 'netuseprinter' : $ret = $this->form_useprinter();
			                       break;		
			case 'netconfprinter': $ret = $this->form_configprinter();
			                       break;									
			case 'netinfprinter' : $ret = $this->form_infoprinter();
			                       break;*/									
                                   
			case 'netlogout'  :	  self::_logout();
			                      $ret = self::_login();
			                      break;							
			case 'netlogin'   : 
		    case 'netshow'    :
			case 'netxml'     :
			case 'netjobstats':
			case 'netnetact'  :
			case 'netjobs'    : 
			case 'netjobstats':
			case 'netdeljob'  :
			case 'netprinter' : 
			default          :	$login = self::_login();
								if ($login === true) {
								  $cmd = str_replace('net','',$action);
								  $ret = self::printer_console($cmd);
								}
								else
								  $ret = $login;	
												
        }

        return ($ret);		
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
		
	    if (!$printername) {
		
		  $select_printer = '
<li id="li_5" >
<label class="description" for="element_5">Printer</label>
<div>
<input id="element_5_1" name= "printername" class="element text medium" maxlength="20" value=""/>
<label>Printer Name</label>
</div>
<p class="guidelines" id="guide_4"><small>Printer name (e.g. name.printer)</small></p> 
</li>
<li id="li_6" >
<label class="description" for="indir">Printer Path</label>
<div>
<input id="element_6" name="indir" class="element text medium" type="text" maxlength="13" value=""/> 
</div><p class="guidelines" id="guide_6"><small>Printer path</small></p> 
</li>	  
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
		</div>
	</div>

EOF;
	
        return ($form);	
	}	

	protected function get_printer_user() {
        $printername = GetParam('printername');
		$printerdir = GetParam('indir');
		$printeruser = GetParam('username');
		$printerpass = GetParam('password');
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
		    ($allowed_users[$printeruser]== $printerpass)) {
			
		        $_SESSION['user'] = $printeruser;
				$_SESSION['printer'] = $printername;//str_replace('.printer','',$printername);
				$_SESSION['indir'] = $printerdir;
				return true;
		   }
        }		
	
	    //return ($message);
		
		return false;
	}
	
	
}
};
?>
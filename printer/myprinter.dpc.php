<?php
$__DPCSEC['MYPRINTER_DPC']='1;1;1;1;1;1;1;1;1';

if (!defined("MYPRINTER_DPC")) {
define("MYPRINTER_DPC",true);

$__DPC['MYPRINTER_DPC'] = 'myprinter';

$a = GetGlobal('controller')->require_dpc('printer/UiIPP.lib.php');
require_once($a);


$__EVENTS['MYPRINTER_DPC'][0]='myprinter';
$__EVENTS['MYPRINTER_DPC'][1]='myshow';
$__EVENTS['MYPRINTER_DPC'][2]='myxml';
$__EVENTS['MYPRINTER_DPC'][3]='myjobstats';
$__EVENTS['MYPRINTER_DPC'][4]='mynetact';
$__EVENTS['MYPRINTER_DPC'][5]='mylogout';
$__EVENTS['MYPRINTER_DPC'][6]='myjobs';
$__EVENTS['MYPRINTER_DPC'][7]='myjobstats';
$__EVENTS['MYPRINTER_DPC'][8]='mydeljobs';
$__EVENTS['MYPRINTER_DPC'][9]='myaddprinter';
$__EVENTS['MYPRINTER_DPC'][10]='mymodprinter';
$__EVENTS['MYPRINTER_DPC'][11]='myremprinter';
$__EVENTS['MYPRINTER_DPC'][12]='myinfprinter';
$__EVENTS['MYPRINTER_DPC'][13]='mylogin';
$__EVENTS['MYPRINTER_DPC'][14]='myuseprinter';
$__EVENTS['MYPRINTER_DPC'][15]='myconfprinter';


$__ACTIONS['MYPRINTER_DPC'][0]='myprinter';
$__ACTIONS['MYPRINTER_DPC'][1]='myshow';
$__ACTIONS['MYPRINTER_DPC'][2]='myxml';
$__ACTIONS['MYPRINTER_DPC'][3]='myjobstats';
$__ACTIONS['MYPRINTER_DPC'][4]='mynetact';
$__ACTIONS['MYPRINTER_DPC'][5]='mylogout';
$__ACTIONS['MYPRINTER_DPC'][6]='myjobs';
$__ACTIONS['MYPRINTER_DPC'][7]='myjobstats';
$__ACTIONS['MYPRINTER_DPC'][8]='mydeljob';
$__ACTIONS['MYPRINTER_DPC'][9]='myaddprinter';
$__ACTIONS['MYPRINTER_DPC'][10]='mymodprinter';
$__ACTIONS['MYPRINTER_DPC'][11]='myremprinter';
$__ACTIONS['MYPRINTER_DPC'][12]='myinfprinter';
$__ACTIONS['MYPRINTER_DPC'][13]='mylogin';
$__ACTIONS['MYPRINTER_DPC'][14]='myuseprinter';
$__ACTIONS['MYPRINTER_DPC'][15]='myconfprinter';


$__DPCATTR['MYPRINTER_DPC']['myprinter'] = 'myprinter,1,0,0,0,0,0,0,0,0,0,0,1';

$__LOCALE['MYPRINTER_DPC'][0]='MYPRINTER_DPC;Printer;Εκτυπωτής';
$__LOCALE['MYPRINTER_DPC'][1]='_SHLOGOUT;Logout;Αποσύνδεση';


class myprinter extends UiIPP {
	
	var $myprinter, $defdir, $message;
	
	function __construct() {   
	   
	   $this->myprinter = null;
	   $this->message = null;
	   $this->defdir = $_SESSION['indir'] ? $_SESSION['indir'] : '/';//null//'printers'; 

	   //overwrite
	   $this->printer_name = $_SESSION['printer'] ? $_SESSION['printer'] : $_POST['printername'];	   
							    
	   parent::__construct($this->printer_name,null,null,true,'my');
	   
	}

    function event($event=null) {
	
        switch($event)   {
		
		    case 'myaddprinter' : 
			case 'mymodprinter' :
			case 'myremprinter' : 
			case 'myinfprinter' : 
			case 'myconfprinter': 
			case 'myuseprinter' : 
			case 'mylogin'      : 
			case 'mylogout'     :	
		    case 'myshow'       :
			case 'myxml'        :
			case 'myjobstats'   :
			case 'mynetact'     :
			case 'myjobs'       : 
			case 'myjobstats'   :
			case 'mydeljob'     :			
			case 'myprinter'    :
			default             : 
			                    
        }			
	}
	
    function action($action=null)  {	
	
        switch($action)   {
		
		    /*case 'myaddprinter' : $ret = $this->form_addprinter(null,null,null,null,null,GetReq('indir'));
			                       break;
			case 'mymodprinter' : $ret = $this->form_modprinter();
			                       break;
								
			case 'myremprinter' : break;

			case 'myuseprinter' : $ret = $this->form_useprinter();
			                       break;		
			case 'myconfprinter': $ret = $this->form_configprinter();
			                       break;									
			case 'myinfprinter' : $ret = $this->form_infoprinter();
			                       break;*/									
                                   
			case 'mylogout'  :	  self::_logout();
			                      $ret = self::_login();
			                      break;							
			case 'mylogin'   : 
		    case 'myshow'    :
			case 'myxml'     :
			case 'myjobstats':
			case 'mynetact'  :
			case 'myjobs'    : 
			case 'myjobstats':
			case 'mydeljob'  :
			case 'myprinter' : 
			default          :	$login = self::_login();
								if ($login === true) {
								  $cmd = str_replace('my','',$action);
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
		<input type="hidden" name="FormAction" value="mylogin" />
		
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
	
	
	//PRINTER FORMS........	OVERRIDE	
	
	//ADD NEW PRINTER...overrride
	public function form_addprinter($name=null, $auth=null, $quota=null, $users=null, $indir=null) {
	    $printername = $name ? $name : GetParam('printername');
		$printerauth = $auth ? $auth : GetParam('printerauth');
		$printerquota = $quota ? $quota : 10;//GetParam('printerquota');
		$printerusers = is_array($users) ? $users : null;//GetParam('printerusers');
		$printerdir = $indir ? $indir : (GetParam('indir')?GetParam('indir'):(GetReq('indir') ? GetReq('indir') : $this->defdir));
		
		if (!$printername) {
		  $ret = $this->add_printer_form(null,$name,$auth,$quota,$users,$indir);
		  return ($ret);
		}  
		
        $ok = self::html_add_printer($printername,
			                         $printerauth,
                                     $printerquota,
                                     $printerusers,
                                     $printerdir); 		
		
		$msg = $ok ? 'Success' : 'Failed';
		
		if ($ok) //modify..set conf params		
		  $ret .= $this->mod_printer_form($msg,$printername,$printerauth,$printerquota,$printerusers,$printerdir);
		else 
		  $ret .= $this->add_printer_form($msg,$name,$auth,$quota,$users,$indir);
		
		return ($ret);
	}
	
	//override
	protected function add_printer_form($message=null, $name=null, $auth=null, $quota=null, $users=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;	
		$msg = $message ? '&nbsp;:&nbsp;' . $message : null;
		$basic_check = "checked='checked'"; 
			
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>			
		
	<div id="form_container">

		<form id="form_470441" class="appnitro"  method="post" action="">
					<div class="form_description">
			<h2>Add printer $msg</h2>
			<p>Printer configuration.</p>
		</div>						
			<ul >
			
					<li id="li_1" >
		<label class="description" for="printername">Printer Name</label>
		<div>
			<input id="element_1" name="printername" class="element text medium" type="text" maxlength="13" value=""/> 
		</div><p class="guidelines" id="guide_1"><small>Printer name</small></p> 
		</li>		<li id="li_3" >
		<label class="description" for="printerauth">Authentication Method</label>
		<span>
<input id="element_3_1" name="printerauth" class="element radio" type="radio" value="SIMPLE" />
<label class="choice" for="element_3_1">SIMPLE</label>
<input id="element_3_2" name="printerauth" class="element radio" type="radio" value="BASIC" $basic_check/>
<label class="choice" for="element_3_2">BASIC</label>
<input id="element_3_3" name="printerauth" class="element radio" type="radio" value="OAUTH" />
<label class="choice" for="element_3_3">OAUTH</label>
		</span>
		<p class="guidelines" id="guide_3"><small>Select printer authentiaction method!</small></p> 
		</li>		<li id="li_2" >
		<label class="description" for="printerquota">Quota </label>
		<div>
			<input id="element_2" name="printerquota" class="element text medium" type="text" maxlength="4" value=""/> 
		</div><p class="guidelines" id="guide_2"><small>Printer quota</small></p> 
		</li>
			
					<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="indir" value="$indir" />
				<input type="hidden" name="FormAction" value="myaddprinter" />
			    
				<input id="saveForm" class="button_text" type="submit" name="Submit" value="Create Printer" />
		</li>
			</ul>
		</form>	
		<div id="footer">
		$ver&nbsp;|&nbsp;$this->logout_url
		</div>		
	</div>

EOF;

        return ($form);	
	
	}
	
	//MODIFY PRINTER PARAMS...override
	public function form_modprinter($name=null, $auth=null, $quota=null, $users=null, $indir=null) {
	    $printername = $name ? $name : (GetParam('printername') ? GetParam('printername') : $_SESSION['printer']);//$this->myprintername);
		$printerauth = $auth ? $auth : GetParam('printerauth');
		$printerquota = $quota ? $quota : GetParam('printerquota');
		$printerusers = is_array($users) ? $users : array('admin'=>'admin','myself'=>'me');//null;//GetParam('printerusers');
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
		//echo $printername,'>';
		
        $login = self::_login();
		if ($login !== true) {
		  return ($login);
		}		
		
		if (!$printername)
		  return ('Unknown printer!');
		  
		//$this->myprinter = new UiIPP($printername,null,null,true);
        //$ret = $this->myprinter->html_printer_menu(true);	
        $ret = self::html_printer_menu(true);			
		
		if (GetParam('FormAction')!='mymodprinter') {
		
		  $dir = $printerdir ? $printerdir.'/' : null;
	      $bootstrapfile = $_SERVER['DOCUMENT_ROOT'] .'/'.$dir. str_replace('.printer','.php',$printername);
		
		  $params = self::parse_printer_file(null,null,$bootstrapfile);					
		
		  //print_r($params);
		  if (empty($params))
		    return ('Unknown printer file!');
					
		  
		  $ret .= $this->mod_printer_form(null,$printername,$params['auth'],$params['quota'],$params['users'],$printerdir);
		  return ($ret);
		}		
	
	    //$ret = 'modify printer...'; 		
        $ok = self::html_mod_printer($printername,
		                             $printerauth,
									 $printerquota,
									 $printerusers,
									 $printerdir); 		
		
		$msg = $ok ? 'Success' : 'Failed';
		
		if ($ok) {
		  $ret .= $this->mod_printer_form($msg,$printername,$printerauth,$printerquota,$printerusers,$printerdir);
		}
		else
		  $ret .= $this->mod_printer_form($msg,$printername,$printerauth,$printerquota,$printerusers,$printerdir);
		  
		return ($ret);	
    }	
	
	//override
	protected function mod_printer_form($message=null,$name=null, $auth=null, $quota=null, $users=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;	
	    $msg = $message ? '&nbsp;:&nbsp;' . $message : null;
		$oauth_check = $basic_check = $simple_check = null;
		
		switch (str_replace("'","",$auth)) {
		  case 'OAUTH' : $oauth_check = "checked='checked'"; break;		
		  case 'BASIC' : $basic_check = "checked='checked'"; break;		
		  case 'SIMPLE': 
		  default      : $simple_check = "checked='checked'";
		  
		}
		//echo $auth,'>';
		$menu = self::html_printer_menu(true);			
	
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>			
		
	<div id="form_container">
	    $menu
		<form id="form_470441" class="appnitro"  method="post" action="">
					<div class="form_description">
			<h2>Modify printer : $name $msg</h2>
			<p>Printer configuration.</p>
		</div>						
			<ul >
			
					<li id="li_1" >
		<label class="description" for="printername">Printer Name</label>
		<div>
			<input id="element_1" name="printername" class="element text medium" type="text" maxlength="13" value="$name" 'readonly'/> 
		</div><p class="guidelines" id="guide_1"><small>Printer name</small></p> 
		</li>		<li id="li_3" >
		<label class="description" for="element_3">Authentication Method</label>
		<span>
<input id="element_3_1" name="printerauth" class="element radio" type="radio" value="SIMPLE"  $simple_check/>
<label class="choice" for="element_3_1">SIMPLE</label>
<input id="element_3_2" name="printerauth" class="element radio" type="radio" value="BASIC"  $basic_check/>
<label class="choice" for="element_3_2">BASIC</label>
<input id="element_3_3" name="printerauth" class="element radio" type="radio" value="OAUTH"  $oauth_check/>
<label class="choice" for="element_3_3">OAUTH</label>
		</span>
		<p class="guidelines" id="guide_3"><small>Select printer authentiaction method!</small></p> 
		</li>		<li id="li_2" >
		<label class="description" for="printerquota">Quota </label>
		<div>
			<input id="element_2" name="printerquota" class="element text medium" type="text" maxlength="4" value="$quota"/> 
		</div><p class="guidelines" id="guide_2"><small>Printer quota</small></p> 
		</li>
			
					<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="mymodprinter" />
			    
				<input id="saveForm" class="button_text" type="submit" name="Submit" value="Modify Printer" />
		</li>
			</ul>
		</form>	
		<div id="footer">
		$ver&nbsp;|&nbsp;$this->logout_url		
		</div>		
	</div>
	
EOF;
	
         return ($form);
	}	
	
	//PRINTER USERS ...override
	public function form_useprinter($printername=null, $indir=null) {
	    $printername = $name ? $name : (GetParam('printername') ? GetParam('printername') : $_SESSION['printer']);//$this->myprintername);
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
        $printerusers = array();

        $login = self::_login();
		if ($login !== true) {
		  return ($login);
		}
		
        if (!$printername)
		  return ('Unknown printer!');		
		
		//$this->myprinter = new UiIPP($printername,null,null,true);
		
		$ret = self::html_printer_menu(true);
		
		//read anyway...to save unknown params
		$dir = $printerdir ? $printerdir.'/' : null;
	    $bootstrapfile = $_SERVER['DOCUMENT_ROOT'] .'/'.$dir. str_replace('.printer','.php',$printername);
		$params = self::parse_printer_file(null,null,$bootstrapfile);			
		
		if (GetParam('FormAction')!='myuseprinter') {
		
		  if (empty($params))
		    return ('Unknown printer file!');
		  
		  $ret .= $this->users_printer_form(null,$printername,$params['users'],$printerdir);
		  return ($ret);
		}	

        //get user post data	
        for ($i=1;$i<6;$i++) {
		    $post_user = 'user'.$i.'name';
			$post_pass = 'pass'.$i.'word';
		    if (($u = $_POST[$post_user]) && ($p = $_POST[$post_pass])) {
			   $printerusers[$u] = $p;
			} 
        } 		
		//print_r($printerusers);
		if (!empty($printerusers)) {
		//overwrite.....save unknown params
		$printerauth = GetParam('printerauth') ? GetParam('printerauth') : $params['auth']; 
		$printerquota = GetParam('printerquota') ? GetParam('printerquota') : $params['quota']; 
		//$printerusers = GetParam('printerusers') ? GetParam('printerusers') : $params['users'];		
        $ok = self::html_mod_printer($printername,
		                             $printerauth,//null
					 			     $printerquota,//null
									 $printerusers,
									 $printerdir); 
		}										 
		
		$msg = $ok ? 'modified successfully' : 'Failed to modify!';
		$ret .= $this->users_printer_form($msg,$printername,$printerusers,$printerdir);
		  
		return ($ret);	
    }	
	
	//override
	/*protected function users_printer_form($message=null, $name=null, $users=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;
		$cmd = $this->external_use ? $this->procmd.'useprinter':'useprinter';
		
	    $user_fields = '
        <li id="li_4" >
		<label class="description" for="user<@>">User <@> </label>
		<span>
			<input id="element_<@>_1" name= "user<@>name" class="element text" maxlength="13" size="14" value="[Username]"/>
			<label>Username</label>
		</span>
		<span>
			<input id="element_<@>_2" name= "pass<@>word" class="element text" maxlength="13" size="14" value="[Password]"/>
			<label>Password</label>
		</span><p class="guidelines" id="guide_4"><small>User <@></small></p> 
		</li>		
';		

        $ji=1;
        if (!empty($users)) {
		  foreach ($users as $un=>$up) {
		    $user = str_replace("'","",$un);
			$pass = str_replace("'","",$up);
		    $myuserfields = str_replace('[Username]',$user,str_replace('[Password]',$pass,$user_fields)); 
		    $us_ui .= str_replace('<@>',$ji,$myuserfields);
		    $ji+=1;
		  }
		  
		}
		//+until 5
        for ($i=$ji;$i<=5;$i++) {
		    $myuserfields = str_replace('[Username]','',str_replace('[Password]','',$user_fields));
		    $us_ui .= str_replace('<@>',$i,$myuserfields);
		}

        $menu = self::html_printer_menu(true);		
	
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu 
		<form id="form_470441" class="appnitro"  method="post" action="">
					<div class="form_description">
			<h2>Printer's Users $message</h2>
			<p>Add or modify printer's users.</p>
		</div>						
			<ul >
			
		<li id="li_0" >
		<label class="description" for="element_0">Printer </label>
		<div>
			<input id="element_0" name="element_0" class="element text medium" type="text" maxlength="13" value="$name"/> 
		</div><p class="guidelines" id="guide_1"><small>Printer name</small></p> 
		</li>		

		$us_ui
			
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="myuseprinter" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
		<div id="footer">
		$ver&nbsp;|&nbsp;$this->logout_url
		</div>
	</div>

EOF;
        return ($form);	
	
	}*/	
	
	//CONFIG PRINTER ...override
	public function form_configprinter($printername=null, $indir=null) {
	    $printername = $name ? $name : $_SESSION['printer'];//$this->myprintername;
		$printerdir = $indir ? $indir : $_SESSION['indir'];			
		$handlers = array();
		$params = array();
		
        $login = self::_login();
		if ($login !== true) {
		  return ($login);
		}		

        if (!$printername) 
		  return ('Unknown printer!');	  
		  
		$ret = self::html_printer_menu(true);		
		
        if ($filter=$_POST['filter']) {
		  $code = $_POST['filtercode'];
		  $ret .= $this->config_filter_form($filter,$printername,$code,$printerdir);
		  return ($ret);		
		}			
		
		//read conf file
		$dir = $printerdir ? $printerdir.'/' : null;
	    $cfile = $_SERVER['DOCUMENT_ROOT'] .'/'.$dir. $printername . '.conf';
				
		$pr_config = self::parse_printer_conf(null,null,$cfile);
		//print_r($pr_config);
		
        if ((!empty($pr_config['SERVICES'])) && ($handlers = $pr_config['SERVICES'])) {
		
		    if (is_array($pr_config['PARAMS'])) {
		        $apply_services_method = $pr_config['PARAMS']['services']; 
		        if ($apply_services_method == 'must') {
		            //sort by value =1,2,3,4...
		            asort($handlers);
		        }
				
				$file_output = $pr_config['PARAMS']['foutput'];
				
				$params['method'] = $apply_services_method;
				$params['output'] = $file_output;
            }			
		    //print_r($handlers);
		    foreach ($handlers as $service=>$is_on) {
			
			    if ($is_on>0) 
				   $params['handlers'][] = $service . ':'.$is_on;
                else
				   $params['handlers'][] = $service . ':disabled';
				   				
			}
		}
		
		if (GetParam('FormAction')!='myconfprinter') {
		  
		  $ret .= $this->config_printer_form($msg,$printername,$params,$printerdir);
		  return ($ret);
		}
		
		//read new values while saving...
		$params = array();
		
        //save conf file
		//print_r($_POST);
		$file = "
[SERVICES]";		
        for ($i=1;$i<=10;$i++) {
		   $service = $_POST['handler'.$i]; 
		   $hdval = $_POST['index'.$i]!='disabled' ? $_POST['index'.$i] : null;
		   if ($service) {
		     $srv = $service . ':';
			 $srv .= isset($hdval) ? $hdval : 'disabled'; 
		     $params['handlers'][] = $srv;
		     $file .= "
$service=";
             $file .= ($hdval) ? "$hdval" : ";";
           }
        }	

        $params['method'] = $method = $_POST['filters_method'];		
		$params['output'] = $output = $_POST['filters_output'];
		
		$file .= "
		
[PARAMS]
services=$method
output=$output		
";			
        //echo $file;	
        $msg = 	self::save_printer_conf(null, null, $file, $cfile);		
		
		//print_r($params);		
		$msg = null;//$ok ? 'Saved' : 'Failed to save!';
		$ret .= $this->config_printer_form($msg,$printername,$params,$printerdir);
		  
		return ($ret);	
    }	
	
	//override
	protected function config_printer_form($message=null, $name=null, $params=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;
		$hd_ui = null;
		$filters_method = $params['method'];
		$page = pathinfo($_SERVER['PHP_SELF'],PATHINFO_BASENAME);
		$edit_filter = $page.'?t=myconfprinter&filter=[Handler]';
		
	    $handler_fields = '
        <li id="li_4" >
		<label class="description" for="filter<@>">Filter <@> </label>
		<span>
			<input id="element_<@>_1" name= "handler<@>" class="element text" maxlength="13" size="14" value="[Handler]"/>
			<label>Filter&nbsp;[<a href="' . $edit_filter . '">Edit</a>]</label>
		</span>
		<span>
			<input id="element_<@>_2" name= "index<@>" class="element text" maxlength="13" size="14" value="[Index]"/>
			<label>Value</label>
		</span><p class="guidelines" id="guide_4"><small>Filter <@></small></p> 
		</li>		
';		

        $ji=1;
        if (!empty($params['handlers'])) {
		  foreach ($params['handlers'] as $fi=>$filter) {
		    //echo '>',$filter,'<br>';
		    $fp = explode(':',$filter);
		    $fname = $fp[0];
			$factive = $fp[1];
		    $myhfields = str_replace('[Handler]',$fname,str_replace('[Index]',$factive,$handler_fields)); 
		    $hd_ui .= str_replace('<@>',$ji,$myhfields);
		    $ji+=1;
		  }
		}
		//+until 10
        for ($i=$ji;$i<=10;$i++) {
		    $myhfields = str_replace('[Handler]','',str_replace('[Index]','',$handler_fields));
		    $hd_ui .= str_replace('<@>',$i,$myhfields);
		}	
		
		$menu = self::html_printer_menu(true);
	
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu
		<form id="form_470441" class="appnitro"  method="post" action="">
					<div class="form_description">
			<h2>Printer filters $message</h2>
			<p>Add or modify printer behavior.</p>
		</div>						
			<ul >
			
		<li id="li_0" >
		<label class="description" for="element_0">Filter type </label>
		<div>
			<input id="element_0" name="filters_method" class="element text medium" type="text" maxlength="13" value="$filters_method"/> 
		</div><p class="guidelines" id="guide_1"><small>Filter apply method</small></p> 
		</li>		

		$hd_ui
			
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="myconfprinter" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
		<div id="footer">
		$ver&nbsp;|&nbsp;$this->logout_url
		</div>
	</div>

EOF;
        return ($form);		
	}	

    //PRINTER's FILTER EDIT	..override
	protected function config_filter_form($filter=null, $printername=null, $code=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;
	    $dir = $indir ? $indir.'/' : ($this->defdir ? $this->defdir .'/' : '/');
		$filter = GetParam('filtername') ? GetParam('filtername') : $filter;
	    $file = $_SERVER['DOCUMENT_ROOT'] .'/'.$dir . str_replace('.printer','',$printername).'.'.$filter.'.php';
		//echo $file,'>',$code;
	
	    if ($code = stripslashes($code)) {
           //code testbed
		   $dummy_auth = 'dummy';
		   $dummy_fp = 'fp_';
		   $job_id = 1;
		   $job_filename = $_SERVER['DOCUMENT_ROOT'] .'/'.$dir .'job_test.txt';
		   $job_attr = array('test'=>'test');
		   $testbed = new addhoc($dummy_auth, $dummy_fp,$job_id,$job_filename,$job_attr,$printername);
		   
		   $testbed->dummy_auth = 'dummy';
		   $testbed->dummy_fp = 'fp_';
		   $testbed->job_id = 1;
		   $testbed->job_filename = $_SERVER['DOCUMENT_ROOT'] .'/'.$dir .'job_test.txt';
		   $testbed->job_attr = array('test'=>'test');	

           if (!empty($_FILES['testfile']) && (!$_FILES['testfile']['error'])) {//uploaded file
		   
		     //print_r($_FILES);
			 $tpfile = $_FILES['testfile']['tmp_name'];
			 
		     if (is_readable($tpfile)) {   
			  
			   if ($tp = fopen($tpfile, "r+b")) {  
			     $testbed->import_data = fread($tp, $_FILES['testfile']['size']);
			     fclose($fp);
			   }  
            }			 
           }
           else  		   
             $testbed->import_data = '';		   
		   
		
		   //manipulate code
		   $code = str_replace('echo','$ret .= ',$code);
		   $mycode = str_replace('this','testbed',$code);//local var testbed
		   
		   //eval php
           @trigger_error("");
           $result = eval($mycode);
           $error = error_get_last();		
		   //print_r($error);
		   if ($error['message'])
		     $msg = '<br>'.$error['message'] .' : line '.$error['line'] ;
		   else	 
	         $msg .= '...Saved';	
			 
//           $mycode .= "
//return (\$this->output);";			 
		   

		   try {
		       $result = $testbed->execute($code, true);//not $mycode..thereis no testbed
			   file_put_contents($testbed->job_filename, $result);
			   
			   if ($result) {
		        $preview = '<h2>Result:</h2>';
					  
                if (is_readable($testbed->job_filename)) {
				  $preview .= "<IFRAME SRC=\""."http://".$_ENV["HTTP_HOST"].$dir."job_test.txt\" TITLE=\"$printername\" WIDTH=600 HEIGHT=400>>
                              <!-- Alternate content for non-supporting browsers -->
                              <H2>$printername</H2>
                              <H3>iframe is not suported in your browser!</H3>
                              </IFRAME>";
			    }			 
               }			   
           } 
		   catch (Exception $e) {
                $msg = "Error!";
           }		   
		   
		   //save script
		   file_put_contents($file,"<?php\r\n".$code."\r\n?>");

		}
		else {
		   //load script
		   $code = str_replace("<?php\r\n","",str_replace("\r\n?>","",file_get_contents($file)));
		   $msg = '...Loaded';
		}
		
		$menu = self::html_printer_menu(true);

	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu
		<h2>$msg</h2>
		<form id="form_470441" class="appnitro" enctype="multipart/form-data" method="post" action="">
					<div class="form_description">
			<h2>Edit Printer Filter</h2>
			<p>Edit printer's filter.</p>
		</div>						
			<ul >
			
					<li id="li_1" >
		<label class="description" for="element_1">Filter Name </label>
		<div>
			<input id="element_1" name="filtername" class="element text medium" type="text" maxlength="255" value="$filter"/> 
		</div><p class="guidelines" id="guide_1"><small>Edit filter name.
If filter name exits leave it as is to modify the filter.
If filter name is blank, enter a name to create a new filter.</small></p> 
		</li>		
		
        <li class="section_break">
			<p></p>
		</li>	
		
<label class="description" for="element_2">
<br>&nbsp;class $filter {
<br>&nbsp;&nbsp;function __construct() {
	<br>&nbsp;&nbsp;&nbsp;&nbsp;\$this->printer_name; //string variable that holds the printer's name
	<br>&nbsp;&nbsp;&nbsp;&nbsp;\$this->jid; //integer variable that holds the job id
	<br>&nbsp;&nbsp;&nbsp;&nbsp;\$this->jf; = //string variable holds the name of current file (path included)
	<br>&nbsp;&nbsp;&nbsp;&nbsp;\$this->jattr; // array of current job attributes
	<br>&nbsp;&nbsp;&nbsp;&nbsp;\$this->import_data; // text variable that holds the original data
	<br>&nbsp;&nbsp;&nbsp;&nbsp;// always return the proccesed data using return()	
<br>&nbsp;&nbsp;}	
<br>&nbsp;&nbsp;
<br>&nbsp;&nbsp;protected function execute() {
</label>		
		
		<li id="li_2" >
		<!--label class="description" for="element_2">Filter script </label--!>
		<div>
			<textarea id="element_2" name="filtercode" class="element textarea medium">$code</textarea> 
		</div> 
		</li>
		
<label class="description" for="element_2">	
<br>&nbsp;&nbsp;}
<br>&nbsp}
</label>	
		
        <li class="section_break">
			<h3>Import data</h3>
			<p></p>
		</li>		<li id="li_3" >
		<label class="description" for="element_3">File </label>
		<div>
			<input id="element_3" name="testfile" class="element file" type="file"/> 
		</div>  
		</li>		
        <li class="section_break">
			<p></p>
		</li>		
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="myconfprinter" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
		</ul>
		</form>	
		
		$preview
		
		<div id="footer">
		$ver&nbsp;|&nbsp;$this->logout_url
		</div>
	</div>	
EOF;
        return ($form);		
	}	
	
	
	//FETCH PRINTER INFO FILES...override
	public function form_infoprinter($printername=null, $indir=null) {
	    $printername = $name ? $name : $this->myprintername;
		$printerdir = $indir ? $indir : $_SESSION['indir'];

        $login = self::_login();
		if ($login !== true) {
		  return ($login);
		}		
		
		//$this->myprinter = new ippprinter();
		//$this->myprinter = new UiIPP($printername,null,null,true);
        $ret = self::html_printer_menu(true);
		
        $ok = self::html_info_printer($printername, $printerdir); 				
		$ret .= $ok ? 'End of info' : 'Failed to fetch info!';
		
		return ($ret);	
    }	
	
	//override
	protected function info_printer_form() {
	
	}	
	
	//CHANGE PRINTER QUOTA..override
    public function form_addquota($quota=null, $printername=null, $indir=null) {
	    $printername = $name ? $name : $this->myprintername;
		$printerquota = $quota ? $quota : null;//GetParam('printerquota');
		$printerdir = $indir ? $indir : $_SESSION['indir'];		
   
		//$this->myprinter = new UiIPP($printername,null,null,true);
        $ok = self::html_mod_printer($printername,null,$printerquota,null,$printerdir); 	
		
		if ($ok)										
	      $ret = $jobs .' jobs added!';
		else
          $ret = 'Quota error!';		
		
		return ($ret);   
    } 	

	protected function add_quota_form() {
	
	}		
	
}
};
?>
<?php
$__DPCSEC['NETPANEL_DPC']='1;1;1;1;1;1;1;1;1';

if (!defined("NETPANEL_DPC")) {
define("NETPANEL_DPC",true);

$__DPC['NETPANEL_DPC'] = 'netpanel';

$a = GetGlobal('controller')->require_dpc('networlds/apppanel.dpc.php');
require_once($a);

GetGlobal('controller')->get_parent('APPPANEL_DPC','NETPANEL_DPC');

$__EVENTS['NETPANEL_DPC'][20]='netpanel';
$__EVENTS['NETPANEL_DPC'][21]='modapp';

$__ACTIONS['NETPANEL_DPC'][20]='netpanel';
$__ACTIONS['NETPANEL_DPC'][21]='modapp';

$__DPCATTR['NETPANEL_DPC']['netpanel'] = 'netpanel,1,0,0,0,0,0,0,0,0,0,0,1';

$__LOCALE['NETPANEL_DPC'][0]='NETPANEL_DPC;Add app;Add app';
$__LOCALE['NETPANEL_DPC'][1]='_SHLOGOUT;Logout;Αποσύνδεση';


class netpanel extends apppanel {
	
	var $message;
	var $url_activate, $url_invitate;
	var $test_page, $instrutions_as_var, $instrution_var;
	var $appnameison, $passgenison, $couponison, $couponsubmited ,$coupon_ask, $testmode;
	var $pubkey, $privkey;
	
	function __construct() {   
	   					    
	    parent::__construct();	
	   
	    //override params
		$rmail = remote_paramload('SMTPMAIL','user',$this->prpath);
		$this->mailto = $rmail ? $rmail : 'info@stereobit.com';	

		//application name generator
		$this->appnameison = ($_SESSION['appnameison']=='true') ? $_SESSION['appnameison'] : false;//true;
		if (!$this->appnameison) 
			$this->posted_appname = GetParam('cpappmail') ? $this->newapp_gen_name(GetParam('cpappmail')) : null;
        //else //as posted plus prefix...
		  //  $this->posted_appname = GetParam('cpappmail') ? $this->dir_prefix . $this->posted_appname : null;
	
		//automated user password  generator	
		$this->passgenison = ($_SESSION['passgenison']=='false') ? false: true;			
		if ($this->passgenison)
		    $this->posted_password = $this->posted_password_retyped = $this->newapp_gen_pass();	
        //else as posted		
		
        //get or post theme ..if saved in session get it		
	    $this->posted_theme = $_SESSION['passgenison'] ? $_SESSION['passgenison'] :
		                      (GetParam('initheme') ? GetParam('initheme') : GetParam('theme'));//'mo-music';
		
		if (!$this->appnameison) //use prefix
			$this->app_location = $this->location . '/' . $this->dir_prefix . $this->posted_appname;
		else //without prefix	
		    $this->app_location = $this->location . '/' . $this->posted_appname;
			
		//recaptcha
        $this->pubkey = remote_paramload('RECAPTCHA','pubkey',$this->prpath);
		$this->privkey = remote_paramload('RECAPTCHA','privkey',$this->prpath);		
	   
	    //coupons
	    $this->couponison = true;
		$this->couponsubmited = $_SESSION['couponsubmited'] ? $_SESSION['couponsubmited'] : false;
		$this->coupon_ask = (!empty($_SESSION['coupon_ask'])) ? (array) $_SESSION['coupon_ask'] : array();
		//echo $this->couponsubmited;
		//print_r($_SESSION['coupon_ask']);
		
		//test mode
	    $this->testmode = false;//true;
	}
	
    //overrride 
    function event($event=null) {
	
        switch($event)   {
		
		    case 'modapp'       :  //redirect to app details
			                       if ($addon = GetReq('addon')) { 
		                             $location = $_ENV["HTTP_HOST"] . '/' .$addon. '/';
									 //echo $location;
		                             header('location:http://'.$location);
								   }	 
			                       break;
			
			case 'addapp'       :  //$coupon = $this->set_coupon_actions(); break;
			                       //$this->javascript_loader();
			                       parent::event($event);
								   break;
			case 'netpanel'     :
			default             :  $this->create_slideshow_ini(); //read/update slide show..
			                       parent::event($event);
								   
								   session_destroy();//kill session when in fp...
			                    
        }			
	}
	
	//override
    function action($action=null)  {	
	
        switch($action)   {							
                        
		    case 'modapp'    : $ret = $this->addonapp();  break;						
			
			case 'addapp'    : //$ret = parent::action($action);	break;
			                   $ret = $this->newapp();  break;
			case 'netpanel'  :
			default          : //$ret = parent::action($action);	
		
        }
		
        if ($this->log) {		
			$this->log .= "<br>------------------user------------------<br>" .
			              '<br/>From:' .$from . '<br/>' .
						  '<br/>AppName:' .$this->posted_appname . '<br/>' . 
                          '<br/>Pxass:' .$this->posted_password . '<br/>' .
						  '<br/>Theme:' .$this->posted_theme . '<br/>' .
						  '<br/>Skin:' .$this->posted_skin . '<br/>' .
						  '<br/>isLocal:' .$this->is_local_theme . '<br/>';

			$ndata = $ret . '<br>' . $this->log;
			$notify = $this->app_notifier($ndata, $action, 'Application action:');						  
		}

        return ($ret);		
	}
	
    protected function javascript_loader() {

      if (iniload('JAVASCRIPT')) {

	       $code = '
var ld=(document.all);
var ns4=document.layers;
var ns6=document.getElementById&&!document.all;
var ie4=document.all;
if (ns4)
 	ld=document.loading;
else if (ns6)
 	ld=document.getElementById("loading").style;
else if (ie4)
 	ld=document.all.loading.style;
	
function init() {
 if(ns4){ld.visibility="hidden";}
 else if (ns6||ie4) ld.display="none";
 else ld.display="none";
}
';
 
		   $js = new jscript;
           $js->load_js($code,"",1);			   
		   unset ($js);
	  }   
    } 		
	
	//generate user appname 
	protected function newapp_gen_name($mail=null) {
        if ((empty($_POST)) || (!$mail)) return null; //null if no post		
 
		$m_appname = str_replace('.','-',array_shift(explode('@',$mail)));
		//if (strlen($m_appname)<5)....
		
        $my_app_location  = $this->location . '/' . $this->dir_prefix . $m_appname;		
		
		if (is_dir($my_app_location)) { //name exist change it...
		    
			for ($i=1;$i<100;$i++) { 
		        if (!is_dir($my_app_location . strval($i)))
				     //return name plus number
                     return	($m_appname . strval($i));		   
			}
		}
		else
		    $retname = $m_appname; //as is
		
		return ($retname);
	}	
	
	//generate user strong password
	protected function newapp_gen_pass() {
        if (empty($_POST)) return null; //null if no post	

		$timestamp = time();
		
		$st = $this->posted_appname ? $this->posted_appname . $timestamp  : $timestamp;
		$retpass = hash('crc32',$this->posted_mail . $st);
		
		return ($retpass);
	}
	
	protected function newapp_response($sendmail=false) {
	    if (!$this->posted_appname) return false;	
	
        $appdomain = $this->posted_appname . '.' . $this->rootdomain;	
	    $cplink = $this->get_admin_link($appdomain, true);
		
        //button to go...
		$cplink_noimage = $this->get_admin_link($appdomain);
		$onclick = "top.location.href='" . $cplink_noimage . "'";
        /*$button = '<div class="grid_4 alpha omega aligncenterparent">
				   <br/>
				   <input type="submit" class="call-out grid_2 push_2 alpha omega" alt="Continue" title="Continue"
						  name="Submit" value="Continue" onClick="'.$onclick.'">
				   </input>
				   </div>';*/	
		/*$button = '<div class="grid_12 alpha omega aligncenterparent"> 
					<br/>
					<input type="submit" class="call-out grid_8 push_2 alpha omega" alt="Continue"
						title="Continue" name="Submit" value="Continue" onClick="'.$onclick.'"></input>
					<div class="form-foot" style="clear:both;"></div>
				   </div> '; */				   
			
						  
		$client_message = null;/*'<h5>You are one step behind the final publishing.</h5><br/><p>
							Keep your personal data in a safe place. <br /> <br />
							They will be used every time you need to access your webpage\'s control panel.<br /> <br />
							Thank you. 
							</p>';*/							  

		$client_message.= '<h3 class="grid_3 alpha omega"><strong>Theme:</strong></h3>' .
		                  '<h3 class="grid_3 alpha omega"><strong>'.$this->posted_theme.'</strong></h3>';							  
		/*$client_message.= '<h3 class="grid_3 alpha omega"><strong>Username:</strong></h3>' .
		                  '<h3 class="grid_3 alpha omega"><strong>'.$this->posted_appname.'</strong></h3>';				  
		$client_message.= '<h3 class="grid_3 alpha omega"><strong>Password:</strong></h3>'.
		                  '<h3 class="grid_3 alpha omega"><strong>'.$this->posted_password.'</strong></h3>';*/				  
		$client_message.= '<h3 class="grid_3 alpha omega"><strong>Username:</strong></h3>'.
		                  '<h3 class="grid_3 alpha omega"><strong>'.$this->posted_mail.'</strong></h3>';				  
		$client_message.= '<h3 class="grid_3 alpha omega"><strong>Password:</strong></h3>'.
		                  '<h3 class="grid_3 alpha omega"><strong>'.$this->posted_password.'</strong></h3>';				  		
		$client_message.= '<h3 class="grid_3 alpha omega"><strong>Url:</strong></h3>'.
		                  '<h3 class="grid_3 alpha omega"><strong>http://'.$appdomain.'</strong></h3>';				  		
						  
        //$client_message.= "<div class=\"msg grid_12 alpha omega aligncenterparent\">http://$appdomain</div> ";						  
				
		//$client_message.= $button;//"<div class=\"msg grid_12 alpha omega aligncenterparent\">$cplink</div> ";
	
						  
		if ($sendmail) {	
            $abs_rootdomain = 'http://' . $this->rootdomain; 		
			$client_subject = 'New application ' . $appdomain;
			
			$client_message4mail = "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
	<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\">
	<link href=\"$abs_rootdomain/css/stenet.css\" rel=\"stylesheet\" type=\"text/css\" />
	<script type=\"text/javascript\" src=\"$abs_rootdomain/js/jquery-1.5.1.min.js\"></script>

</head>
<body class=\"sign-in-page\">
<div id=\"site-wrapper\">	
<div id=\"content-wrapper\">
<div id=\"content\" class=\"container_12\">	
<div id=\"content-left\" class=\"grid_8\">	
<div class=\"sign-up-box\">	
" . 
$client_message . 
"
</div><!-- #sign-up-box -->
</div><!-- #content-left -->
</div> <!-- #content -->
</div> <!-- #content-wrapper -->
</div> <!-- #site-wrapper -->
</body>
</html>
";
	        //send html mail		
			$notify_client = $this->_sendmail($this->mailto,$this->posted_mail,$client_subject,$client_message4mail);
		}	
		
		return ($client_message);
	}
	
	protected function newapp_response_form($msg=null) {
	    $action = seturl('t=netpanel');//got start ?? //null;
	    $message = $msg ? $msg : null;		
        $appdomain = $this->posted_appname . '.' . $this->rootdomain;		
		
		$cplink = $this->get_admin_link($appdomain);
		//$onclick = $cplink  ? "top.location.href='" . $cplink . "'" : null;
		$onclick = $cplink  ? "window.open('" . $cplink . "')" : null;
		
		$onclk = $onclick ? 'onClick="'.$onclick.'"' : null;	

		//SEND MAIL.... 
		//$sendmail = $this->testmode ? false : true;
		$message .= $this->newapp_response(true);//$sendmail);		
	
		$form = <<<EOF
							<form name="signup" method="post" class="sign-up-form" action="$action">
								    $message 								 
									<div class="msg grid_12 alpha omega aligncenterparent"></div>      
									<div class="grid_12 alpha omega aligncenterparent"> 
										<br/>
										<input type="submit" class="call-out grid_8 push_2 alpha omega" alt="Continue"
											title="Continue"
											name="Submit" value="Continue" $onclk></input>
									 <div class="form-foot" style="clear:both;"></div>
								    </div>  
							</form>		
EOF;

        return ($form);		
	}	
	
	//override for support simulation
	public function newapp_scenario() {
	
	    if ($this->testmode) {
		    $this->error_log = null;
		    $this->log = 'INSERT INTO users.....';//simulate log...
			$this->posted_theme = $this->posted_theme ? $this->posted_theme : 'testapptheme';
			$this->posted_appname = $this->posted_appname ? $this->posted_appname : 'testappname'; 
			$this->posted_password = $this->posted_password ? $this->posted_password : 'testapppass';
			$this->posted_password_retyped = $this->posted_password_retyped ? $this->posted_password_retyped :'testapppassretype';
            $this->posted_mail == $this->posted_mail ? $this->posted_mail : 'me@testapp.me';
		    $ret = '...test app!';
		}
		else
		    $ret = parent::newapp_scenario();
			 
		return ($ret);	 
    }	
	
	//override..
    public function newapp() {
		
		if ($this->is_valid_app()) {

          if (!empty($this->coupon_ask)) {	//if extra form to submit..show it before
            //extra ask coupon form	
		    $msg = 'Coupon submited';
            $ret .= $this->newapp_form($msg, $this->coupon_ask);		  
		  }
		  else {//do the website cooking...
			try {
				$dataret = $this->newapp_scenario(); 
			} 
			catch (Exception $e) {
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}			
			
			if (strstr($this->log,'INSERT INTO users')) {
			    //that measn that he script has complete all the actions
				//..insert into db is the last? action....

				$ret .= $this->newapp_response_form($msg);
			}
            else {
			    //form
				$error = $this->error_log ? $this->error_log : 'unknown error';
                $msg = "Error during installation ($error), please retry later.";			
				$ret .= $this->newapp_form($msg);
				//error mail notifier..parent event already send
				//$notify = $this->app_notifier($msg, null, 'Application error:');
			}
          }//coupon ask			
		}
        else {
            //form		
		    $msg = $this->get_app_error();
			$ret .= $this->newapp_form($msg);
        }

        return ($ret); 		
    }		
	
	
    //re-design override
	public function newapp_form($msg=null, &$coupon_ask=false) {
        $theme = $this->posted_theme ? $this->posted_theme : 'default';	
		//echo $theme;
		
		//form error messages
		$err_message = $msg ? '<h5>' . $msg . '</h5>' : null;
		           //"<div class=\"msg grid_12 alpha omega aligncenterparent\">$msg</div> " : null;		
		
		//theme selector message
		$theme_message = $this->form_selector_details($theme);

		//extra app name field
        if ($this->appnameison) {		
		    $appname = '<h3 class="grid_3 alpha omega"><strong>Title</strong></h3>'.
					   '<input class="grid_4 alpha omega" name="cpapp" type="text" id="appname" value="'.$this->posted_appname.'"></input>';
		}
		else 
		    $appname = null;
			
		//user set password field
        if (!$this->passgenison) {		
		    $passname = '<h3 class="grid_3 alpha omega"><strong>Password</strong></h3>
						<input class="grid_4 alpha omega" name="upass" type="password" id="password"></input>					
						<h3 class="grid_3 alpha omega"><strong>Password</strong></h3>
						<input class="grid_4 alpha omega" name="upassretype" type="password" id="password2"	value=""></input>
						<small class="grid_1 alpha omega">(retype)</small>';
		}
		else 
		    $passname = null;
			
		//extra coupon name field
        if (($this->couponison) && ($coupon = GetParam('appcoupon')) && (!$this->couponsubmited) ) {
		    //if just coupon param=1 then show field, if has value>strln>1.. is the coupon value
            $coupon_value = strlen($coupon)>1 ? $coupon : null;//GetParam('appcoupon') ? GetParam('appcoupon') : null;		
		    $appcoupon = '<h3 class="grid_3 alpha omega"><strong>Coupon</strong></h3>'.
					     '<input class="grid_4 alpha omega" name="appcoupon" type="text" id="coupon" value="'.$coupon_value.'"></input>';
			
			
			//$appcoupon_hidden = "<input type=\"hidden\" name=\"appcoupon\" value=\"$coupon_value\" /> "			 
		}
		else { 
		    $appcoupon = $this->couponsubmited ? '<h3 class="grid_3 alpha omega"><strong>Coupon</strong></h3>'. 
			                                     '<input class="grid_4 alpha omega" name="appcoupon" type="text" id="coupon" value="'.$this->couponsubmited.'" readonly></input>'
											   : null;	
            //extra coupon fields.... 
            if (!empty($coupon_ask)) {//$coupon_ask)) {
			    echo '>>>>>>>>>>>>>>>couponASK';
			    foreach ($coupon_ask as $coupon_cmd=>$coupon_value) {
				    //$appcoupon .= "<input type=\"hidden\" name=\"$coupon_cmd\" value=\"$coupon_value\" />";
				    $appcoupon .= '<h3 class="grid_3 alpha omega"><strong>'.$coupon_cmd.'</strong></h3>'. 
			                      '<input class="grid_4 alpha omega" name="'.$coupon_cmd.'" type="text" id="coupon" value="'.$coupon_value.'" readonly></input>';
					
					echo '<br>',$coupon_cmd,':',$coupon_value;  
				}
				//reset in mem when extar from question to proced
				//$coupon_ask = null;
				SetSessionParam('coupon_ask',false);
            }			
        }
		
        if (defined('RECAPTCHA_DPC') && ($this->pubkey) && ($this->privkey)) {
		    //echo  'z';
			if (!$coupon_ask) {//check has been done...
				$recaptcha = '<h3 class="grid_3 alpha omega"><strong>Recaptcha</strong></h3>';
				$recaptcha .= '<div class="grid_4 alpha omega">' . recaptcha_get_html($this->pubkey, $this->privkey) . '</div>';
			}
        }			
		
		$form = <<<EOF
							<form name="signup" method="post" class="sign-up-form" action="">
								<input type="hidden" name="FormAction" value="addapp" /> 
								<input type="hidden" name="initheme" value="$theme" /> 
								    $err_message 
								    $appname
									<h3 class="grid_3 alpha omega"><strong>Your Email</strong></h3>
									<input class="grid_4 alpha omega" name="cpappmail" type="text" id="email" value="{$this->posted_mail}">
									</input>
                                    $passname
                                    $appcoupon									
									$recaptcha 									 
									<div class="msg grid_12 alpha omega aligncenterparent"></div>      
                                    $theme_message
									<div class="grid_12 alpha omega aligncenterparent"> 
										<br/>
										<input type="submit" class="call-out grid_8 push_2 alpha omega" alt="Sign Up"
											title="Sign Up"
											name="Submit" value="Sign Up"></input>
									 <div class="form-foot" style="clear:both;"></div>
								    </div>  
							</form>		
EOF;

        return ($form);		
	}	
	
	
	protected function is_valid_app() {
        //check session
        if ($_SESSION['validapp']===true) return true; //check has been done
	
	    //VALID COUPON CHECK
	    $coupon = $this->set_coupon_actions();
		if (!$coupon) {
		    $this->message = "Invalid coupon id!";
			return false;
		}	
		//RECAPTCHA CHECK
        if ((defined('RECAPTCHA_DPC')) /*&& ($_POST["recaptcha_response_field"])*/) {
			//echo 'recaptcah';
			$resp = recaptcha_check_answer ($this->privkey,	$_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
			if (!$resp->is_valid) {
					$this->message = $resp->error;
					return false;
			}
		}		
	    //SUBMIT CHECK
        if (($this->is_valid_email($this->posted_mail)) && 
		    ($this->posted_appname) && 
			($this->posted_password) &&
			(strlen($this->posted_appname)>=5) && 
			(strlen($this->posted_password)>5) && 
		    ($this->posted_password === $this->posted_password_retyped)) { 
			
			    //save valid app..for extra coupon question not to re-ask
			    SetSessionParam('validapp',true); 
                return true;
		}	
			
        return false;			
	}	
	
	protected function get_app_error() {
	
		if (!empty($_POST)) {
		    if (!$this->is_valid_email($this->posted_mail)) {
			    $msg = ' Invalid email.<br/>';
			}
		    elseif (strlen($this->posted_appname)<5) {
			    $msg = $this->posted_appname .' is not a valid name.<br/>';
                $msg .= 'Name must be at least 5 characters long.';	
			}	
			elseif (strlen($this->posted_password)<=5)
                $msg = 'password must be at least 6 characters long.';	
			elseif ($this->posted_password!==$this->posted_password_retyped)
                $msg = 'please re-type the password.';	
			else
                $msg = $this->message ? $this->message : null;				
        }  
        return ($msg);		
	}
	
    protected function is_valid_email($email) {
	
        //php 5.2
        if (filter_var( $email, FILTER_VALIDATE_EMAIL )) {
		    
	        $domain = explode('@', $email);
            if (checkdnsrr($domain[1])) {
			    //echo $domain[1];
			    return true; 
			}	
	    }
		
		//if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.([a-z]){2,4})$",$email)) return true;
		//else 
		return false;
	}		
	
	//pick theme details based on ini
	protected function form_selector_details($theme=null) {
	    if (!$theme) return false;
		//echo $theme;
		$theme_details = $this->get_theme_details($theme, true);
		//print_r($theme_details);
		
		if (!empty($theme_details)) {
		    $theme_image = $theme_details['image']; 
			$theme_text = $theme_details['text'];
		    $theme_title = $theme_details['title'];
			$theme_liveurl = $theme_details['liveurl'];
			$theme_screenshot = $theme_details['screenshot'];
			
            $reselect_link = seturl('t=netpanel'," Isn't so good for you? Pick another.");				
			
			if ($theme_screenshot) {
			    $theme_image = $theme_liveurl ? "<a href='$theme_liveurl' target='_blank'>" . '<img src="'.$theme_screenshot.'" alt="'.$theme_text.'" /></a>'
											  : '<img src="'.$theme_screenshot.'" alt="'.$theme_text.'" />';
			}
			else 
			    $theme_image = $theme_liveurl ? "<a href='$theme_liveurl' target='_blank'>" .'<img src="'.$theme_image.'" alt="'.$theme_text.'" /></a>'
			                                  : '<img src="'.$theme_image.'" alt="'.$theme_text.'" />'; 
			
		
			$ret = "<div class=\"msg grid_12 alpha omega aligncenterparent\">Selected theme: $theme_title $reselect_link</div> ";		
			$ret .= "<div class=\"msg grid_12 alpha omega aligncenterparent\">$theme_image</div> ";	
		}	
		
		return ($ret);
	}	
	
	protected function get_theme_details($theme=null,$asarray=false) {
	    if (!$theme) return false;
		$lan = getlocal() ? getlocal() : 0;
		$ret = null;
		
		if ($tdetails = $this->read_themes_ini($theme)) {
		    //read ini
			//print_r($tdetails);
			//echo $tdetails;
			$tconf = parse_ini_string($tdetails, false, INI_SCANNER_RAW);
			//print_r($tconf);
				
			foreach ($tconf as $k=>$v) {
			    $mv = explode(',',$v);
			    if ($asarray) 
			       $ret[$k] = $mv[$lan];					
				else   
			       $ret .= $k .'=' . $mv[$lan] .'<br/>';
			}			
	    }
		//echo $ret;
		return ($ret);
	}
	
	//read themes data...if theme selected return theme's options
    protected function read_themes_ini($theme=null, $iniret=false){
		$themedir = $this->themes_path;
        if (!is_dir($themedir)) return false;	
        //echo $themeini;
		
        if ($theme) {//read selected theme file
		    $themeini = $themedir.$theme.'/slideshow.ini';
			//echo $themeini;
		    if ($confdata = file_get_contents($themeini)) {
			    //echo 'z';
			    if ($iniret) {
				    $confdata = parse_ini_file($themeini, false, INI_SCANNER_RAW);
					return (array) $confdata;
				}
				else
                    return($confdata);			
			}	
        }
        //else create ini for all... 		
    
        $mydir = dir($themedir);	
		$i=0;
        while ($theme = $mydir->read ()) { 
	
           if (($theme!='.') && ($theme!='..') && (is_dir($themedir.'/'.$theme))) {
			    //echo $theme . '<br>';
				$themeini = $themedir.'/'.$theme.'/slideshow.ini';
				//echo $themeini . '<br>';
				if ((is_readable($themeini)) && ($confdata = @file_get_contents($themeini))) {
				    $i+=1;
				    $ret .= "[SLIDE$i]\r\n";
				    $ret .= $confdata;
					$ret .= "\r\n";
				}	
           }
        }
        $mydir->close();
		
		return $ret;
    } 
  
    //read themes create slideshow
    protected function create_slideshow_ini() { 
       $name = strval(date('Ymd'));
       $ss = $this->prpath . 'slideshow.ini';
	   $data = null;

       if (is_readable($ss)) {
			$data = file_get_contents($ss);
	   }
	   else {
            $data = $this->read_themes_ini();
			if ($data) {
			   //save data  
			   @file_put_contents($ss, $data);
			}   
	   }
	   
	   return ($data);
    }	

	//simulate link as in frontpage dpc
    protected function get_admin_link($domain, $editpoint=null, $query=null) {
	    if (!$domain) return false;
        $editmode_point  = $editpoint ? loadTheme('editmode','') : null;		
		$mynewquery = $query ? $query : null; 
          		
			
		$current_page = pathinfo("http://$domain/index.php");//pathinfo($_SERVER['PHP_SELF']);		
		$target_url = urlencode(encode($current_page['basename'] . "?".$mynewquery));
		
        //$admin_url = $this->infolder . "/".$current_page['basename'] . "?modify=".encode('stereobit')."&turl=".$target_url;		
		
		//editmode icon alternative..
		$icon_file = $this->app_location .'/images/editpage.png';
        if (is_readable($icon_file))
		    $editmode_point2 = "<img src=\"". $domain .'/images/editpage.png' ."\" border=\"0\" alt=\"Edit this page\">";		
		
        //SetSessionParam('authverify',1);		
				
		//$ret = seturl("modify=".urlencode(base64_encode('stereobit'))."&turl=".$target_url.$mynewquery,$editmode_point);
		$modify_url = 'modify='.urlencode(base64_encode('stereobit'))."&turl=".$target_url.$mynewquery;
		$ret = $editpoint ? "<a href='http://". $domain . '/index.php?'. $modify_url ."'>" . $editmode_point . "</a>" . $editmode_point2
				          : 'http://'. $domain . '/index.php?' . $modify_url ;
		
		return ($ret);
    }		
	
	
	
	//override..
	function modify_html_meta() {
	
		$mywizfile = $this->app_location . '/cp/cpwizard.ini';	
		
		$domain = 'http://' . $this->posted_appname . '.' . $this->rootdomain ;
		$email = $this->posted_mail ? $this->posted_mail : $this->posted_appname.'@'.$this->rootdomain ;
		$title = $this->posted_appname;		
		
		//cpwizard preset in cp dir...
		if (is_readable($mywizfile)) {
		    //in case of pre-existing (copied) wizard file..dont create...
	
			//$data = @parse_ini_file($mywizfile ,false, INI_SCANNER_RAW);
		    //...replace or ADD domain,email
			//.......			
			$memdata = @file_get_contents($mywizfile);
			if ($memdata) {
				$memdata1 = str_replace('DOMAIN-NAME=','DOMAIN-NAME='.$domain, $memdata);
			    $memdata2 = str_replace('E-MAIL=','E-MAIL='.$email, $memdata1);
				$data = @parse_ini_string($memdata2 ,false, INI_SCANNER_RAW);
			
				$ok = $this->change_data('cp/html', $data);//...change 
				
				$this->log .= "<br>Wizard file finded!";
				
				//save wizard file with replacements		
		        $ok2 = @ok ? @file_put_contents($mywizfile ,$memdata2) : false;
				return ($ok);
			}
		}
	    //else create wiz file
		
		$data = array('DOMAIN-NAME'=>$domain,'E-MAIL'=>$email,
		              'TITLE'=>$title,'SUBTITLE'=>'#your-subtitle',
		              'META-DESCRIPTION'=>'#your-description','META-KEYWORDS'=>'#your-keywords',
					  'FEEDBURNER'=>'#feedburner-url','TWITTER'=>'#tweeter-url','FACEBOOK'=>'#facebook-url','GOOGLEPLUS'=>'#googleplus-url',
					  'FLICKR'=>'#flickr-url','VIMEO'=>'#viemo-url','LINKEDIN'=>'#linkedin-url','DELICIOUS'=>'#delicious-url',
					  'FBLIKEBOX-PLUGIN'=>'#fb-plugin','FLICKRBADGE-PLUGIN'=>'#flickr-plugin',
					  'LATITUDE'=>'40.690446','LONGITUDE'=>'22.794224',
					  'ADDRESS'=>'Allatini 27 St, 54250, Thessaloniki',
					  'TEL1'=>'T_:+30 2310 950 155', 'TEL2'=>'M_:+30 6937 367 879',
					  'UA-CODE-PLACEHOLDER'=>'UA-XXXXXXXXX',
					  'PROFILE-TITLE'=>'Company','PRODUCTS-TITLE'=>'Products','CONTACT-TITLE'=>'Contact');	
		
				
		$ok = $this->change_data('cp/html', $data);
		
		$this->log .= "<br>Wizard file created!";		
		
		//create wizard ini file
		$myiniwizard = "wizard=1\r\n";//null;		
        foreach ($data as $var=>$val) {
		    $myiniwizard .= $var .'='.$val ."\r\n";
        } 			
		//save wizard file		
		$ok2 = @ok ? @file_put_contents($mywizfile ,$myiniwizard) : false;

        return ($ok2);						  
	}
	
	//not workable if not automated whem cmd change....
	public function get_panel_title() {	
	    $action = null;
		
	    switch ($action) {	
		
		    case 'modapp' : $ret = "Upgrade your website"; break;
		    default       : $ret = "Create your website account";
		}
		
		return ($ret);
	}		
	
	public function get_panel_text($action=null) {

		$submit_coupon_lnk = seturl('t=addapp&theme='.GetReq('theme').'&appcoupon=1', 'here');
		//print_r($_POST);
		
		if (!$action) {
			if ($message = $this->get_app_error()) {
				$action = 'failed-install';
			}	
			elseif ((GetParam('FormAction')=='addapp') && ($message = $this->error_log)) {
				$action = 'failed-install';		
			}			
			elseif ((GetParam('FormAction')=='addapp') && ($this->error_log==null)) {
				$action = 'success-install';
			}	
			else
				$action =  null;//'success-install';//null;	
        }			
			
		//echo $action,'::',$this->error_log;
		
	    switch ($action) {
		
			case 'xyz':   		
						$ret = "if you have any proposal coupon, submit it $submit_coupon_lnk!";
						break;
						
			case 'success-install':  if ($this->coupon_ask)
                                           return ('Coupon accepted. Coupon details submition.');
			
						   $ret = 	'<h5>Congratulations. Your website has been cooked.</h5><br/><p>
							Keep your personal data in a safe place and press the <strong>continue</strong> button to
							complete the installation.<br />
							Your personal data will be asked for security reasons and they will be asked every time you 
							need to access your webpage\'s control panel.<br /> <br />
							Thank you. 
							</p>';	
							
							//coupon submited.. not just getreq coupon fileld =1
							if ($coupon = GetParam('appcoupon') && ((strlen($coupon)>1)))
								$ret .= "Coupon submited:$coupon<br/>";
							else
								$ret .= "<br /> <br />";  
								
                            //button to go...
							/*$appdomain = $this->posted_appname . '.' . $this->rootdomain;
							$cplink = $this->get_admin_link($appdomain);
							$onclick = "top.location.href='" . $cplink . "'";
                            $ret .= '<div class="sign-up-box">
							         <div class="grid_4 alpha omega aligncenterparent">
							         <br/>
										<input type="submit" class="call-out grid_2 push_2 alpha omega" alt="Save"
											title="Continue"
											name="Submit" value="Continue" onClick="'.$onclick.'">
									    </input>
									</div>
									</div><!-- #sign-up-box -->';*/								
						break;		

			case 'failed-install':  
                         $ret = '<h5>'.$message.'</h5><br/><p>
							Please insert your data carefully. <br /> <br />
							They will be used every time you need to access your webpage\'s control panel.<br /> <br />
							Thank you. 
							</p>'; 	
							if (!GetParam('appcoupon'))
								$ret .= "If you have any proposal coupon, press $submit_coupon_lnk to submit it!<br/>";
							else
								$ret .= "<br /> <br />";  								
						break;							
	
			default :
						$ret = "<h5>Welcome, a new web site can be cooking for you.</h5>
				<br/>
				<p>
				Please input your email and let us know how can we contact you. <br /> <br />
				Also we will notify you with the installation details. 
				You will then be able to upgrade to	any premium account anytime.<br /> <br />
				</p>";
				if (!GetParam('appcoupon'))
					$ret .= "If you have any proposal coupon, press $submit_coupon_lnk to submit it!<br/>";
				else
                    $ret .= "<br /> <br />";  				
		}
		
		return ($ret);
	}

	//when coupon posted....change posted params
    protected function set_coupon_actions() {
	    $ret = false;
		
		if (($this->couponison) && ($coupon = GetParam('appcoupon')) && ($this->couponsubmited==false) ) {

            switch ($coupon) {
			
			    case 'theme-music'    :
			    case 'case-name'      :
				case 'theme-name'     : //PRE SELECTED THEME/CASE ..silent coupon selection
				                        $theme = array_pop(explode('-',$coupon));
				                        $this->posted_theme = $theme;
				                        SetSessionParam('theme', $theme);
				                        $ret = true;
				                        break;			
			    case 'select-name'    : 					
			    case 'app-name-is-on' : //ENABLE USER APP NAME SUBMITION..(post with captcha error to enable fields)..silent coupon selection
				                        $this->appnameison = true;
				                        SetSessionParam('appnameison', 'true');
				                        $ret = true;
				                        break;
										
				case 'pass-gen-is-off': //ENABLE USER PASS SUBMITION..(post with captcha error to enable fields)..silent coupon selection
				                        $this->passgenison = false;
				                        SetSessionParam('passgenison', 'false');
				                        $ret = true;
				                        break;
			    case 'free-coupon'    : //TEST SUCCESS..silent coupon selection
				                        $ret = true; 
										break;
			    case 'no-coupon'      : //TEST FAIL..silent coupon selection
				                        $ret = false; 
										break;
										
				default               : //$ret = (strlen($coupon)>1) ? false : true;//false; //default nothing..??
				                        //multi value coupon....NON SILENT COUPON
										$ret = $this->multicoupon_exec($coupon);
            }
            //save coupon ..if exist and is on just 1=enabled
            if ((strlen($coupon)>1) && ($ret==true))	{		
			   //echo 'coupon saved in session:',$coupon;
			   SetSessionParam('couponsubmited', $coupon);
			}   
		}
		else
		    $ret = true; //default true when disabled coupons or no coupons
		
		return ($ret);
    }

	//multicoupon commands
    protected function multicoupon_exec($coupon=null) {	
	    if (!$coupon) return true;//no coupon bypass.. ret=true
		
		$multicoupon = explode('|',$coupon);
		if (array_shift($multicoupon)=='WEBCOOKCOUPON') {
		
			//rest cmds can be encoded....
            //decode...
			//$decoded_multicoupon = explode('::',decode($multicoupon[1]));
			
			while ($cmd = array_shift($multicoupon)) {
												
		 	    $cmdtype = explode(':',$cmd);
			    switch ($cmdtype[0]) {
			        case 'theme' :  $this->posted_theme = $cmdtype[1];
				                    SetSessionParam('theme', $cmdtype[1]);
									$this->coupon_ask['theme'] = $cmdtype[1];
									echo '<br>theme:'.$cmdtype[1];
					                break;
			        case 'addapp' : // $this->posted_theme = $cmdtype[1];
				                    //SetSessionParam('theme', $cmdtype[1]);
									//after standart install action ....continue with app installation...
									$this->coupon_ask['addapp'] = $cmdtype[1];
									echo '<br>addonapp:'.$cmdtype[1];
					                break;
					default       : //do nothing..???
                                    $this->coupon_ask[$cmdtype[0]] = $cmdtype[1];
									echo '<br>'.$cmdtype[0].':'.$cmdtype[1];					
				}
			}
			SetSessionParam('coupon_ask', $this->coupon_ask);//save actions								
			$ret = true;//null= no actions but true anyway
		}
		else
		    $ret = false; //is coupon but invalid coupon
			
		return ($ret);	
    }
	
	//addon actions
    public function addonapp() {	
	    $ret = null;//'addon!';
		
		/*if ($addon = GetReq('addon')) { //moved to events
		   //redirect to app details
		   $location = $_ENV["HTTP_HOST"] . '/' .$addon. '/';
		   header('location:'.$location);
		}*/
		//else return null =page as is...
	    return ($ret);
	}
	
	//called into html as phpdac cmd to get the callback url..
    public function addcallbackurl($addonapp=null) {
	    $cb = GetParam('cb') ? base64_decode(GetParam('cb')) : null;
		$addonapp =  $addonapp ? $addonapp : null;

		if ($cb)
          $url2go = 'http://' . $cb .'/cp/cpupgrade.php?addon=' . $addonapp;//or fill into html
        else
		  $url2go = seturl('t=modapp&addon='.$addonapp);//false; //local details
		
        return ($url2go);		
    }	
}
};
?>
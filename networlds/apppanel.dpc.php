<?php
$__DPCSEC['APPPANEL_DPC']='1;1;1;1;1;1;1;1;1';

if (!defined("APPPANEL_DPC")) {
define("APPPANEL_DPC",true);

$__DPC['APPPANEL_DPC'] = 'apppanel';

$a = GetGlobal('controller')->require_dpc('libs/cpanelx3.lib.php');
require_once($a);

$b = GetGlobal('controller')->require_dpc('libs/htaccess.lib.php');
require_once($b);

$__EVENTS['APPPANEL_DPC'][0]='cpapppanel';
$__EVENTS['APPPANEL_DPC'][1]='apppanel';
$__EVENTS['APPPANEL_DPC'][2]='addapp';
$__EVENTS['APPPANEL_DPC'][3]='delapp';
$__EVENTS['APPPANEL_DPC'][4]='chskin';
$__EVENTS['APPPANEL_DPC'][5]='chskinkeys';

$__ACTIONS['APPPANEL_DPC'][0]='cpapppanel';
$__ACTIONS['APPPANEL_DPC'][1]='apppanel';
$__ACTIONS['APPPANEL_DPC'][2]='addapp';
$__ACTIONS['APPPANEL_DPC'][3]='delapp';
$__ACTIONS['APPPANEL_DPC'][4]='chskin';
$__ACTIONS['APPPANEL_DPC'][5]='chskinkeys';

$__DPCATTR['APPPANEL_DPC']['poprinter'] = 'cpapppanel,1,0,0,0,0,0,0,0,0,0,0,1';

$__LOCALE['APPPANEL_DPC'][0]='CPAPPPANEL_DPC;Panel;Panel';
$__LOCALE['APPPANEL_DPC'][1]='APPPANEL_DPC;Panel;Panel';

class apppanel  {

    var $prpath, $urlpath, $infolder, $url;
    var $encoding, $location;
	var $log, $rootdomain, $mailto;
	var $posted_mail, $posted_theme, $posted_appname, $posted_password, $posted_password_retyped;
	var $db_prefix, $posted_skin, $is_local_theme;	
	
	var $dir_prefix, $app_location, $cpanel_user, $cpanel_pass;
	var $themes_path;
	var $error_log;
	
	function __construct() {   	 
		$this->prpath = paramload('SHELL','prpath'); //cp path of root app
		$this->urlpath = paramload('SHELL','urlpath'); //root path of root app
		$this->infolder = paramload('ID','hostinpath'); //must be null	
		$this->url = paramload('SHELL','urlbase'); //root domain name 

		$this->cpanel_user = '#######';
		$this->cpanel_pass = '#######';

		$char_set  = arrayload('SHELL','char_set');	  
		$charset  = paramload('SHELL','charset');	  		
		if (($charset=='utf-8') || ($charset=='utf8'))
			$encoding = 'utf-8';
		else  
			$encoding = $char_set[getlocal()]; 	
	
		$this->encoding = $_GET['encoding'] ? $_GET['encoding'] : $encoding; 
		$this->location = $_GET['location'] ? $_GET['location'] : $this->urlpath; 	
		
		$this->log = null;
		$this->error_log = null;
		
		$rdomain = remote_paramload('SHELL','rootdomain',$this->prpath);
		$this->rootdomain = $rdomain ? $rdomain : 'stereobit.gr';
		$rmail = remote_paramload('SHELL','rootmail',$this->prpath);
		$this->mailto = $rmail ? $rmail : 'balexiou@stereobit.com';
		
		$this->themes_path = $this->prpath .'/init-app/cp/themes/';
		
		//form submited data 
		$this->posted_mail = GetParam('cpappmail');
		$this->posted_theme = GetParam('initheme');
		$this->posted_appname = GetParam('cpapp');
		$this->posted_password = GetParam('upass');
		$this->posted_password_retyped = GetParam('upassretype');
		
		$this->db_prefix = GetParam('dbprefix') ? GetParam('dbprefix') : 'stereobi_';
		
		$this->posted_skin = GetParam('cpskin');
		$this->is_local_theme = GetParam('localskin');
		
		$dirprefix = remote_paramload('SHELL','dirprefix',$this->prpath);
		$domprefix = strlen($this->rootdomain)>6 ? substr($this->rootdomain,0,6).'_' : $this->rootdomain .'_';
		$this->dir_prefix = $dirprefix ? $dirprefix : $domprefix;
		
		//app dir location
		$this->app_location = $this->location . '/' . $this->dir_prefix . $this->posted_appname;
	}
	
    function event($event) {
	
		switch ($event) {	
		    case 'chskinkeys':
		    case 'chskin' : break;
		    case 'delapp' : break;
		    case 'addapp' :
			default       : 
		}
		
		if (!empty($_POST)) { //init notification
		    /*$subject = 'App event:'.$event.' from app:'.$this->posted_appname;
			$from = $this->posted_mail ? $this->posted_mail : $this->posted_appname .'@'.$this->rootdomain;
			*/
			$text = "<br>------------------new app------------------<br>" .
			        '<br/>From:' .$from . '<br/>' .
					'<br/>AppName:' .$this->posted_appname . '<br/>' . 
                    '<br/>Pxass:' .$this->posted_password . '<br/>' .
					'<br/>Theme:' .$this->posted_theme . '<br/>' .
					'<br/>Skin:' .$this->posted_skin . '<br/>' .
					'<br/>isLocal:' .$this->is_local_theme . '<br/>';
						  
		    //$message = '<html><head></head><body>'.$text.'</body></html>';	
		    //$notify = $this->_sendmail($this->mailto,$this->mailto,$subject,$message);*/
			
			$notify = $this->app_notifier($text, $event, 'Application event:');
		}
    }

    function action($action) {

		switch ($action) {	
		    case 'chskinkeys':
		    case 'chskin' : $ret = $this->chskin(); break;
		    case 'delapp' : $ret = $this->delapp(); break;
		    case 'addapp' :		
			default       : $ret = $this->newapp();
		}
		

		if ($this->log) {
		    
		    /*$subject = 'App action:'.$action.' from app:'.$this->posted_appname;
			$from = $this->posted_mail ? $this->posted_mail : $this->posted_appname .'@'.$this->rootdomain;
			*/
			$this->log .= "<br>------------------user------------------<br>" .
			              '<br/>From:' .$from . '<br/>' .
						  '<br/>AppName:' .$this->posted_appname . '<br/>' . 
                          '<br/>Pxass:' .$this->posted_password . '<br/>' .
						  '<br/>Theme:' .$this->posted_theme . '<br/>' .
						  '<br/>Skin:' .$this->posted_skin . '<br/>' .
						  '<br/>isLocal:' .$this->is_local_theme . '<br/>';
						  
		    //$message = '<html><head></head><body>'. $ret . '<br>' . $this->log . '</body></html>';	
		    //$notify = $this->_sendmail($this->mailto,$this->mailto,$subject,$message);
			
			$ndata = $ret . '<br>' . $this->log;
			$notify = $this->app_notifier($ndata, $action, 'Application action:');
			$ret .= $notify ? '<br>Notification mail send!' : '<br>Notification mail failed!';
			
			$ret .= "<br>------------------log------------------<br>";
			$ret .= $this->log;
        }	
		
        return ($ret);		
	}
	
	protected function app_notifier($ndata=null, $eventoraction=null, $notify_title=null) {
	    $not_title = $notify_title ? $notify_title : 'App action:';
	
		$subject = $not_title.$eventoraction.' from app:'.$this->posted_appname;
		$from = $this->posted_mail ? $this->posted_mail : $this->posted_appname .'@'.$this->rootdomain;
			
		$this->log .= "<br>------------------user------------------<br>" .
		              '<br/>From:' .$from . '<br/>' .
					  '<br/>AppName:' .$this->posted_appname . '<br/>' . 
                      '<br/>Pxass:' .$this->posted_password . '<br/>' .
					  '<br/>Theme:' .$this->posted_theme . '<br/>' .
					  '<br/>Skin:' .$this->posted_skin . '<br/>' .
					  '<br/>isLocal:' .$this->is_local_theme . '<br/>';
						  
		$message = '<html><head></head><body>'. $ndata . '</body></html>';	
			
		$notify = $this->_sendmail($this->mailto,$this->mailto,$subject,$message);	
		return ($notify);
	}
	
    protected function _sendmail($from=null,$to=null,$subject=null,$body=null,$mailfile=null) {
	    ini_set("SMTP","localhost"); 
        ini_set('sendmail_from', $from);
       
	    if (!$to)
            return false;		
	    //$to = $to ? $to : 'b.alexiou@stereobit.gr';
		
		if ($mailfile) 
		    $body = @file_get_contents($mailfile); 
  
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From:' . $from . "\r\n" .
                    'Reply-To: '. $from . "\r\n" .
                    'ImageSave-Printer: 1.0-/' . phpversion();
        //$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
        //$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";					

        // The message
        //$message = "Line 1\nLine 2\nLine 3";
		//...replace br/cr/lf to \n...
		$message = str_replace("\r\n",'',$body);
        // In case any of our lines are larger than 70 characters, we should use wordwrap()
        $message = wordwrap($message, 70);
					
		$ret = mail($to,$subject,$message,$headers);
						
	    return ($ret);					
    }		
	
	function cmds() {

		$cmds[] = seturl('t=addapp','Add Application');
		$cmds[] = seturl('t=chskin','Change Skin');
		$cmds[] = seturl('t=delapp','Del Application');
		
		$ret .= implode('&nbsp;|&nbsp;', $cmds);
        return ($ret);		
	}
	
	function rollback_cpanel($delete_mail_account=false) {
	
	    //$ret .= '<br>ROLLBACK....';
		$mydbuser = (strlen($this->posted_appname)>6) ? substr($this->posted_appname,0,6) : $this->posted_appname;
	
		$cp = new cpanelx3($this->cpanel_user , $this->cpanel_pass); 
					
		$dbuser = $cp->_cpapi1_exec('Mysql', 'deluser', array($this->db_prefix . $mydbuser));		
		$db = $cp->_cpapi1_exec('Mysql', 'deldb', array($this->db_prefix . $this->posted_appname));		

		//remove mail account
		//if (!$this->posted_mail) { //..no need to be common with delapp
		if ($delete_mail_account) {
			$queryArgs1 = array(
			'domain' => $this->rootdomain,
			'email' => $this->posted_appname
			);		
			$addmail = $cp->_cpapi2_exec('Email', 'delpop', $queryArgs1);
			$reason = $addmail['reason'];
			$result = $addmail['result'];		
			$ret .= '<br>Remove pop account' . $result . ':' . $reason;
		}
		
		//remove subdomain
		$queryArgs2 = array(
			'domain' => $this->posted_appname
		);		
		$addsubd = $cp->_cpapi2_exec('SubDomain', 'delsubdomain', $queryArgs2);
		$reason = $addsubd['reason'];
		$result = $addsubd['result'];
		$ret .= '<br>Remove subdomain' . $result . ':' . $reason;	
		
		return ($ret);
	}
	
	//return dbuser for conf modification
    function new_cpanel(&$mydbuser) {
   
		if (is_dir($this->app_location)) { //COPY HAS DONE  ???NOT NEEDED WHEN NEW CPANEL IS BEFORE FILE COPY...
			//setup....
			//MUST BE UNIQUE 7chars -7 strlen ??
			$mydbuser = (strlen($this->posted_appname)>6) ? substr($this->posted_appname,-6) : $this->posted_appname;

			$cp = new cpanelx3($this->cpanel_user, $this->cpanel_pass); 
			
			$db_error = $cp->_cpapi1_exec('Mysql', 'adddb', array($this->posted_appname));	
            $ret .= 'Add Database (' . $this->posted_appname . '):' . $db_error.'<br/>';
            if ($db_error) {
			    $this->error_log = $db_error;
				return false;
            }			
			
			$dbuser_error = $cp->_cpapi1_exec('Mysql', 'adduser', array($mydbuser, $this->posted_password));
			$ret .= 'Add Database user (' . $mydbuser . '):' . $dbuser_error.'<br/>';
            if ($dbuser_error) {
			    $this->error_log = $dbuser_error;
				return false;
            }				
			
			$adduser_error = $cp->_cpapi1_exec('Mysql', 'adduserdb', array($this->posted_appname, $mydbuser,'select create delete insert update alter'));
			$ret .= 'Add Database user (' . $mydbuser . ') to DB:' . $adduser_error.'<br/>';
            if ($adduser_error) {
			    $this->error_log = $adduser_error;
				return false;
            }				
			
			$addadmin_error = $cp->_cpapi1_exec('Mysql', 'adduserdb', array($this->posted_appname,'admin','all'));
			$ret .= 'Add Database Admin user (admin) to DB:' . $addadmin_error.'<br/>';	
            if ($addadmin_error) {
			    $this->error_log = $addadmin_error;
				return false;
            }				

			//$dberror = $db ? ($dbuser ? ($adduser ? ($addadmin ? true : false) : false) : false) : false;
			//if ($dberror) return false; //else continue...				
		
			$mail = $this->posted_mail ? $this->posted_mail : $this->posted_appname.'@'.$this->rootdomain;

			if (!$this->posted_mail) {//$_POST['cpappmail'] ) { 
				//add mail account when not a mail submited...!!!!!!!!
				$queryArgs1 = array(
					'domain' => $this->rootdomain,
					'email' => $this->posted_appname,
					'password' => $this->posted_password,
					'quota' => 50
				);		
				$addmail = $cp->_cpapi2_exec('Email', 'addpop', $queryArgs1);
				$reason = $addmail['reason'];
				$result = $addmail['result'];		
                if (!$result) {
					$this->error_log = $reason;
					return false;
				}					
				
				$ret .= 'Add pop account:' . $result . ':' . $reason.'<br/>';
			}
			
			//add subdomain
			$queryArgs2 = array(
				'domain' => $this->posted_appname,
				'rootdomain' => $this->rootdomain,
				'dir' => $this->app_location, 
				'disallowdot' => 1
			);		
			$addsubd = $cp->_cpapi2_exec('SubDomain', 'addsubdomain', $queryArgs2);
			$reason = $addsubd['reason'];
			$result = $addsubd['result'];
            if (!$result) {
				$this->error_log = $reason;
				return false;
			}			
			
			$ret .= 'Add subdomain:' . $result . ':' . $reason .'<br/>';
						
			return ($ret);
		}
		else
			return false;
    }	

    //NEW APP....
	
	//all files
	function copy_application_files() {
		
		//$my_theme_path = $this->prpath .'/init-app/cp/themes/' . $this->posted_theme;	
		$my_theme_path =  $this->themes_path . $this->posted_theme;	

		if (($theme = $this->posted_theme) && (is_dir($my_theme_path))) {
			$theme_path = 'cp/themes/'.$theme.'/';
			$this->log .=  '<br/>Init theme selected:'.$theme;
			@file_put_contents($this->app_location."/cp/theme.skin",$theme);
		}	   
		else {
			$theme_path = null;
			$this->log .=  '<br/>Init theme selected: default';
		}

		$mydirtheme = $theme  ? $this->themes_path  . $theme .'/' 
		                      : $this->prpath .'/init-app/'; //<<can be a default theme here NOT THE ROOT FILES
								
	    $this->log .=  "<br>mydirtheme:".$mydirtheme."<br>";
		 
	    //create dirs...CHECK EXISTANCE.. 
		if (!is_dir($this->app_location)) { 
		
		    $ret = "<br>APPNAME CREATE DIR:".$this->app_location."<br>";
		    $this->log .=  "<br>APPNAME CREATE DIR:".$this->app_location."<br>";		
			
			$ok = @mkdir($this->app_location);
			
			//.HTACCESS FILE FOR  / 
			$ok1 = $this->create_htaccess_file();	
			$this->log .= $ok1 ? '<br>HTAccess file created successfully!<br>' 
							   : '<br>HTAccess file FAILED to create!<br>'; 				
		}	
		else {
		    $ret = "<br>APPNAME DIR EXIST:".$this->app_location."<br>QUIT!";
		    $this->log .=  "<br>APPNAME DIR EXIST:".$this->app_location."<br>QUIT!";
			return false;//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<test overwrite
        }		
		
        //create app cp dir		
		if (!is_dir($this->app_location.'/cp')) { 
		
			$ok2 = @mkdir($this->app_location .'/cp');
			$this->log .= $ok2 ? '<br>cp dir created!' : '<br>cp dir error!';
			
			if ($ok2) {
				//HTACCESS FILE FOR /CP............. 
				$ok3 = $this->add_cp_htaccess();		 
				$this->log .= $ok3 ? '<br>cp/htaccess succesfully installed' : '<br>cp/htaccess error';			
			}
		}
        else { //if dir exist !!?? overwriet ??
		
		    $this->log .= '<br>cp dir exist!';
			$ok2 = true; //for master ok
			
			//HTACCESS FILE FOR /CP............. 
			$ok3 = $this->add_cp_htaccess();		 
			$this->log .= $ok3 ? '<br>cp/htaccess succesfully installed' : '<br>cp/htaccess error';	
       	}   

		$master_ok = $ok ? ($ok1 ? ($ok2 ? ($ok3 ? $ok3 : false) : false) : false) : false;
		
		if ($master_ok) {  //copy dirs			
		
			$ret .= $this->copy_root_files($mydirtheme, $theme_path);		
			//js
			$ret .= $this->copy_javascript_files($mydirtheme, $theme_path);
			//css - fonts	
			$ret .= $this->copy_css_files($mydirtheme, $theme_path);		
			//pages			
			$ret .= $this->copy_page_files($mydirtheme, $theme_path);
			//cgi-bin			
			$ret .= $this->copy_cgibin_files($mydirtheme, $theme_path);			
			//theme themes
			$ret .= $this->copy_theme_files($mydirtheme, $theme_path);
			//images		
			$ret .= $this->copy_image_files($mydirtheme, $theme_path);		
			//extras..newsletters
			$ret .= $this->copy_newsletter_files($mydirtheme, $theme_path);			
			//cp...
			$ret .= $this->copy_cp_files($mydirtheme, $theme_path);			
			
			//.HTACCESS FILE FOR  / .......................MOVED UP
			/*$ok = $this->create_htaccess_file();	
			$this->log .= $ok ? '<br>HTAccess file created successfully!<br>' 
							  : '<br>HTAccess file FAILED to create!<br>'; */		
		 
			//extras..cp/cgi
			$ret .= $this->copy_awstats_files($mydirtheme, $theme_path);			
		 
			//extras..reports
			$ret .= $this->copy_report_files($mydirtheme, $theme_path);		

			//extras..current cp theme
			if ($theme) 
				$ret .= $this->copy_cptheme_files($mydirtheme, $theme_path, $theme);
		 
			//extras..ckfinder
			$ret .= $this->copy_ckfinder_files($mydirtheme, $theme_path);	
		
			return ($ret);			
		}
         
      	return false;   
	}			

	//root files
	function copy_root_files($mydirtheme=null, $theme_path=null) {
	    if ((!$mydirtheme) || (!$theme_path)) return false;	
		
		$root_path = $theme_path ? $theme_path :'/';
		$f = $this->copy_dir($root_path);
	    $ret .= "<br>[/]$f files copied!"; 	
		
        return ($ret);		
	}			
	
	//page  files
	function copy_page_files($mydirtheme=null, $theme_path=null) {
	    if ((!$mydirtheme) || (!$theme_path)) return false;	
		
		$f9 = $this->copy_dir($theme_path."pages",null,'/');
	    $ret .= "<br>[".$theme_path."pages]$f9 files copied!";
		
        return ($ret);		
	}	
	
	//cgi-bin files
	function copy_cgibin_files($mydirtheme=null, $theme_path=null) {
	    if ((!$mydirtheme) || (!$theme_path)) return false;	
		
		$f1 = $this->copy_dir($theme_path."cgi-bin",null,'/');
	    $ret .= "<br>[".$theme_path."cgi-bin]$f1 files copied!";
		
		//cgi dpc modules
		$cg1 = $this->copy_dir($theme_path. "cgi-bin/shop",null,'cgi-bin/shop');
	    $ret .= "<br>[".$theme_path."cgi-bin/shop]$cg1 files copied!";			
		
        return ($ret);		
	}	

	//image files
	function copy_image_files($mydirtheme=null, $theme_path=null) {
	    if ((!$mydirtheme) || (!$theme_path)) return false;
			
        //images		
        $to = $theme_path ? 'images' : null;		 
		$f7 = $this->copy_dir($theme_path."images",null, $to);
	    $ret .= "<br>[".$theme_path."images]$f7 files copied!";

		//extra images subdirs
		$f8 = $this->copy_dir($theme_path."images/catfiles",null,'images/catfiles');
	    $ret .= "<br>[".$theme_path."images/catfiles]$f8 files copied!";		 
		$f9 = $this->copy_dir($theme_path."images/photo_bg",null,'images/photo_bg');
	    $ret .= "<br>[".$theme_path."images/photo_bg]$f9 files copied!";		
		$f10 = $this->copy_dir($theme_path."images/photo_md",null,'images/photo_md');
	    $ret .= "<br>[".$theme_path."images/photo_md]$f10 files copied!";	
		$f11 = $this->copy_dir($theme_path."images/photo_sm",null,'images/photo_sm');
	    $ret .= "<br>[".$theme_path."images/photo_sm]$f11 files copied!";			 
		$f12 = $this->copy_dir($theme_path."images/uphotos",null,'images/uphotos');
	    $ret .= "<br>[".$theme_path."images/uphotos]$f12 files copied!";			
		
        return ($ret);		
	}	
	
	//newsletter files
	function copy_newsletter_files($mydirtheme=null, $theme_path=null) {
	    if ((!$mydirtheme) || (!$theme_path)) return false;
				
        //extras..newsletters
		$n10 = $this->copy_dir("newsletters");
	    $ret .= "<br>[newsletters]$f10 files copied!";
		$n11 = $this->copy_dir("newsletters/images");
	    $ret .= "<br>[newsletters/images]$f11 files copied!";		 
		$n12 = $this->copy_dir("newsletters/attachments");
	    $ret .= "<br>[newsletters/attachments]$f12 files copied!";
		
        return ($ret);		
	}			
	
	//js /javascripts
	function copy_javascript_files($mydirtheme=null, $theme_path=null) {
	    if ((!$mydirtheme) || (!$theme_path)) return false;
	
		//js /javascripts
		if (is_dir($mydirtheme . "js")) {
		    $to = $theme_path ? 'js' : null;
			$f1 = $this->copy_dir($theme_path."js",null,$to);
			$ret .= "<br>[".$theme_path."js]$f1 files copied!"; 
		}		 
		if (is_dir($mydirtheme . "javascripts")) {
		    $to = $theme_path ? 'javascripts' : null;
			$f2 = $this->copy_dir($theme_path."javascripts",null, $to);
			$ret .= "<br>[".$theme_path."javascripts]$f2 files copied!"; 
		}
		if (is_dir($mydirtheme . "javascripts/swfobject")) {
		    $to = $theme_path ? 'javascripts/swfobject' : null;
			$f3 = $this->copy_dir($theme_path."javascripts/swfobject",null, $to);
			$ret .= "<br>[".$theme_path."javascripts/swfobject]$f3 files copied!";
		}
		if (is_dir($mydirtheme . "javascripts/lightbox")) {
            $to = $theme_path ? 'javascripts/lightbox' : null;		 
			$f4 = $this->copy_dir($theme_path."javascripts/lightbox",null, $to);
			$ret .= "<br>[".$theme_path."javascripts/lightbox]$f4 files copied!";
		}
        return ($ret);		
	}
		
	//css / fonts
	function copy_css_files($mydirtheme=null, $theme_path=null) {
	    if ((!$mydirtheme) || (!$theme_path)) return false;
		
        //css	
        if (is_dir($mydirtheme . "css")) {		
            $to = $theme_path ? 'css' : null;		 
			$f8 = $this->copy_dir($theme_path."css",null, $to);
			$ret .= "<br>[".$theme_path."css]$f8 files copied!";
			//css/ie
            if (is_dir($mydirtheme . "css/ie")) {		
				$to = $theme_path ? 'css/ie' : null;		 
				$f80 = $this->copy_dir($theme_path."css/ie",null, $to);
				$ret .= "<br>[".$theme_path."css/ie]$f80 files copied!";
			}			
        }		
        //fonts	
        if (is_dir($mydirtheme . "fonts")) {		
            $to = $theme_path ? 'fonts' : null;		 
			$f81 = $this->copy_dir($theme_path."fonts",null, $to);
			$ret .= "<br>[".$theme_path."fonts]$f81 files copied!";
        }

        return ($ret);		
	}
	
	//theme
	function copy_theme_files($mydirtheme=null, $theme_path=null) {
	    if ((!$mydirtheme) || (!$theme_path)) return false;
		
		//theme themes
		if (is_dir($mydirtheme . "themes")) {
		    $to = $theme_path ? 'themes' : null;
			$f5 = $this->copy_dir($theme_path."themes",null, $to);
			$ret .= "<br>[".$theme_path."themes]$f5 files copied!";
			
			$to2 = $theme_path ? 'themes/default.theme' : null;
			$f6 = $this->copy_dir($theme_path."themes/default.theme",null, $to2);
			$ret .= "<br>[".$theme_path."themes/default.theme]$f6 files copied!";		
        }
		else {//default theme
		    $df7 = $this->copy_dir("themes");
	        $ret .= "<br>[themes]$df7 files copied!";
			$df8 = $this->copy_dir("themes/default.theme");
			$ret .= "<br>[themes/default.theme]$df8 files copied!";				
		}	
		
        return ($ret);		
	}		
	
	//cp
	function copy_cp_files($mydirtheme=null, $theme_path=null) {
	    if ((!$mydirtheme) || (!$theme_path)) return false;
			
		//cp...
        $f0 = $this->copy_dir("cp");
	    $ret .= "<br>[cp]$f0 files copied!"; 
        $f01 = $this->copy_dir("cp/dpc");
	    $ret .= "<br>[cp/dpc]$f01 files copied!"; 	
        $f00 = $this->copy_dir("cp/dpc/system");
	    $ret .= "<br>[cp/dpc/system]$f00 files copied!"; 			 
        $f02 = $this->copy_dir("cp/images");
	    $ret .= "<br>[cp/images]$f02 files copied!"; 
        //cp/uploads from root ini not theme path..
        $f03 = $this->copy_dir("cp/uploads"); 
        $ret .= "<br>[cp/uploads]$f03 files copied!";		
		
		//cp extra files...override	
        if (is_dir($mydirtheme . "cp")) {			 
			$c8 = $this->copy_dir($theme_path."cp", true, 'cp');
			$ret .= "<br>[".$theme_path."cp]$c8 files copied (OVERWRITED)!";
        }	
		 
		//cp/html
		if ($theme_path) {
			$f1 = $this->copy_dir($theme_path,null, 'cp/html');
			$ret .= "<br>[$theme_path]$f1 files copied!"; 			 
		}
		else {
			$f1 = $this->copy_dir("cp/html");
			$ret .= "<br>[cp/html]$f1 files copied!"; 			 
		}
		
		//cp/uploads
		/*if ($theme_path) {
			$f2 = $this->copy_dir($theme_path.'cp/uploads',null, 'cp/uploads');
			$ret .= "<br>[$theme_path]$f2 files copied!"; 			 
		}
		else {
			$f2 = $this->copy_dir("cp/uploads");
			$ret .= "<br>[cp/uploads]$f2 files copied!"; 			 
		}*/ //moved up...		


		//change cp/html tags
		$ok = $this->modify_html_meta();
        $this->log .= $ok ? '<br>HTML source tags changed successfully!<br>' 
		                  : '<br>HTML source tags failed to change!<br>'; 			
		
		
		//phpdac
		$c0 = $this->modify_config_file("cp/phpdac5.ini");
	    if (!$c0) $ret .= "<br>[cp/phpdac5.ini]Configuration can't saved!";		
		$d0 = $this->modify_config_file("phpdac5.ini");
	    if (!$d0) $ret .= "<br>[/phpdac5.ini]Configuration can't saved!";				 		 
		 
		//config.ini ...local copy!!!!
		$e = $this->modify_config_file("cp/config.ini",'wdemo',$this->posted_appname,null,true);
	    if (!$e) $ret .= "<br>[config.ini]Configuration can't saved!";			 
		$e = $this->modify_config_file("cp/myconfig.txt",'wdemo',$this->posted_appname,null,true);
	    if (!$e) $ret .= "<br>[myconfig.txt]Configuration can't saved!";						  
		
        return ($ret);		
	}	

	//cp cgi-bin awstats
	function copy_awstats_files($mydirtheme=null, $theme_path=null) {
	    if ((!$mydirtheme) || (!$theme_path)) return false;
				
        //extras..cp/cgi
		$c10 = $this->copy_dir("cp/cgi-bin");
	    $ret .= "<br>[cp/cgi-bin]$c10 files copied!";
			
		//awstats.files
		$rd = $this->rootdomain;
		$aw = $this->modify_config_file("cp/cgi-bin/awstats.php");
	    if (!$aw) $ret .= "<br>[cp/cgi-bin/awstats.php]Configuration can't saved!";			 
		$aw = $this->modify_config_file("cp/cgi-bin/awstats.wdemo.{$rd}.conf",null,null,"cp/cgi-bin/awstats.{$this->posted_appname}.{$rd}.conf");
	    if (!$aw) $ret .= "<br>[cp/cgi-bin/awstats.wdemo.{$rd}.conf]Configuration can't saved!";	
		 
		if ($this->file_chmod('cp/cgi-bin/awstats.pl',0744))
            $ret .= "<br>[cp/cgi-bin/awstats.pl] file cmode 0744";	
        else 			
		    $ret .= "<br>[cp/cgi-bin/awstats.pl] file cmode 0744. NOT AFFECTED.";	
			
		$c11 = $this->copy_dir("cp/cgi-bin/lang");
	    $ret .= "<br>[cp/cgi-bin/lang]$c11 files copied!";		 
		$c12 = $this->copy_dir("cp/cgi-bin/lib");
	    $ret .= "<br>[cp/cgi-bin/lib]$c12 files copied!";
		$c13 = $this->copy_dir("cp/cgi-bin/plugins");
	    $ret .= "<br>[cp/cgi-bin/plugins]$c13 files copied!";	
		 
		//..awstats images
        $c14 = $this->copy_dir("cp/images/awstats");
	    $ret .= "<br>[cp/images/awstats]$c14 files copied!"; 		
        $c15 = $this->copy_dir("cp/images/awstats/browser");
	    $ret .= "<br>[cp/images/awstats/browser]$c15 files copied!"; 	 		 
        $c16 = $this->copy_dir("cp/images/awstats/clock");
	    $ret .= "<br>[cp/images/awstats/clock]$c16 files copied!"; 
        $c17 = $this->copy_dir("cp/images/awstats/cpu");
	    $ret .= "<br>[cp/images/awstats/cpu]$c17 files copied!"; 
        $c18 = $this->copy_dir("cp/images/awstats/flags");
	    $ret .= "<br>[cp/images/awstats/flags]$c18 files copied!"; 
        $c19 = $this->copy_dir("cp/images/awstats/mime");
	    $ret .= "<br>[cp/images/awstats/mime]$c19 files copied!"; 
        $c20 = $this->copy_dir("cp/images/awstats/os");
	    $ret .= "<br>[cp/images/awstats/os]$c20 files copied!"; 
        $c21 = $this->copy_dir("cp/images/awstats/other");
	    $ret .= "<br>[cp/images/awstats/other]$c21 files copied!"; 	

        return ($ret);		
	}

	//cp reports
	function copy_report_files($mydirtheme=null, $theme_path=null) {
	    if ((!$mydirtheme) || (!$theme_path)) return false;
					
        //extras..reports
		$r10 = $this->copy_dir("cp/reports");
	    $ret .= "<br>[cp/reports]$r10 files copied!";
		$r11 = $this->copy_dir("cp/charts_library");
	    $ret .= "<br>[cp/charts_library]$r11 files copied!";

        return ($ret);		
	}

	//cp theme files
	function copy_cptheme_files($mydirtheme=null, $theme_path=null, $theme=null) {
	    if ((!$mydirtheme) || (!$theme_path)) return false;
		
        //extras..current cp theme
		if ($theme) {
			$t1 = $this->copy_dir("cp/themes");
			$ret .= "<br>[cp/themes]$t1 files copied!";
			$t2 = $this->copy_dir("cp/themes/$theme");
			$ret .= "<br>[cp/themes/$theme]$t2 files copied!";
			
            if (is_dir($mydirtheme . "css")) {			
			    $t3 = $this->copy_dir("cp/themes/$theme/css");
			    $ret .= "<br>[cp/themes/$theme/css]$t3 files copied!";
			    //css/ie
				if (is_dir($mydirtheme . "css/ie")) {			 
					$t31 = $this->copy_dir("cp/themes/$theme/css/ie");
					$ret .= "<br>[cp/themes/$theme/css/ie]$t31 files copied!";
				}			
			}		
			//fonts	
			if (is_dir($mydirtheme . "fonts")) {			 
				$t32 = $this->copy_dir("cp/themes/$theme/fonts");
				$ret .= "<br>[cp/themes/$theme/fonts]$t32 files copied!";
			}	
			
			$t4 = $this->copy_dir("cp/themes/$theme/images");
			$ret .= "<br>[cp/themes/$theme/images]$t4 files copied!";			 
			$t5 = $this->copy_dir("cp/themes/$theme/pages");
			$ret .= "<br>[cp/themes/$theme/pages]$t5 files copied!";
			
			if (is_dir($mydirtheme . "javascripts")) {
			    $t6 = $this->copy_dir("cp/themes/$theme/javascripts");
			    $ret .= "<br>[cp/themes/$theme/javascripts]$t6 files copied!";
			}
			//cp extra files
			if (is_dir($mydirtheme . "cp")) {			 
				$t7 = $this->copy_dir($theme_path."cp");
				$ret .= "<br>[".$theme_path."cp]$t7 files copied!";
			}	
		    if (is_dir($mydirtheme . "js")) {
			    $t8 = $this->copy_dir($theme_path."js");
			    $ret .= "<br>[".$theme_path."js]$t8 files copied!"; 
		    }				
        }

        return ($ret);		
	}	

    //extras..ckfinder
	function copy_ckfinder_files($mydirtheme=null, $theme_path=null) {
	    if ((!$mydirtheme) || (!$theme_path)) return false;			
		 
		$f10 = $this->copy_dir("cp/ckfinder");
	    $ret .= "<br>[cp/ckfinder]$f10 files copied!";
		$f11 = $this->copy_dir("cp/ckfinder/lang");
	    $ret .= "<br>[cp/ckfinder/lang]$f11 files copied!";		 
		$f12 = $this->copy_dir("cp/ckfinder/skins");
	    $ret .= "<br>[cp/ckfinder/skins]$f12 files copied!";			 
		 
		$f13 = $this->copy_dir("cp/ckfinder/skins/v1");
	    $ret .= "<br>[cp/ckfinder/skins/v1]$f13 files copied!";
		$f14 = $this->copy_dir("cp/ckfinder/skins/v1/images");
	    $ret .= "<br>[cp/ckfinder/skins/v1/images]$f14 files copied!";
		$f15 = $this->copy_dir("cp/ckfinder/skins/v1/images/toolbar");
	    $ret .= "<br>[cp/ckfinder/skins/v1/images/toolbar]$f15 files copied!";		 
		$f16 = $this->copy_dir("cp/ckfinder/skins/v1/images/loaders");
	    $ret .= "<br>[cp/ckfinder/skins/v1/images/loaders]$f16 files copied!";			 
		$f17 = $this->copy_dir("cp/ckfinder/skins/v1/images/icons");
	    $ret .= "<br>[cp/ckfinder/skins/v1/images/icons]$f17 files copied!";		
		$f17 = $this->copy_dir("cp/ckfinder/skins/v1/images/icons/16");
	    $ret .= "<br>[cp/ckfinder/skins/v1/images/icons/16]$f17 files copied!";	
		$f18 = $this->copy_dir("cp/ckfinder/skins/v1/images/icons/32");
	    $ret .= "<br>[cp/ckfinder/skins/v1/images/icons/32]$f18 files copied!";	
		 
		$f13 = $this->copy_dir("cp/ckfinder/skins/kama");
	    $ret .= "<br>[cp/ckfinder/skins/kama]$f13 files copied!";
		$f14 = $this->copy_dir("cp/ckfinder/skins/kama/images");
	    $ret .= "<br>[cp/ckfinder/skins/kama/images]$f14 files copied!";
		$f15 = $this->copy_dir("cp/ckfinder/skins/kama/images/toolbar");
	    $ret .= "<br>[cp/ckfinder/skins/kama/images/toolbar]$f15 files copied!";		 
		$f16 = $this->copy_dir("cp/ckfinder/skins/kama/images/loaders");
	    $ret .= "<br>[cp/ckfinder/skins/kama/images/loaders]$f16 files copied!";			 
		$f17 = $this->copy_dir("cp/ckfinder/skins/kama/images/icons");
	    $ret .= "<br>[cp/ckfinder/skins/kama/images/icons]$f17 files copied!";		
		$f17 = $this->copy_dir("cp/ckfinder/skins/kama/images/icons/16");
	    $ret .= "<br>[cp/ckfinder/skins/kama/images/icons/16]$f17 files copied!";	
		$f18 = $this->copy_dir("cp/ckfinder/skins/kama/images/icons/32");
	    $ret .= "<br>[cp/ckfinder/skins/kama/images/icons/32]$f18 files copied!";		 
		 
		$f18 = $this->copy_dir("cp/ckfinder/help");
	    $ret .= "<br>[cp/ckfinder/help]$f18 files copied!";	
		$f19 = $this->copy_dir("cp/ckfinder/help/en");
	    $ret .= "<br>[cp/ckfinder/help/en]$f19 files copied!";			 
		$f20 = $this->copy_dir("cp/ckfinder/help/en/files");
	    $ret .= "<br>[cp/ckfinder/help/en/files]$f20 files copied!";	
		$f21 = $this->copy_dir("cp/ckfinder/help/files");
	    $ret .= "<br>[cp/ckfinder/help/files]$f21 files copied!";			 
		$f22 = $this->copy_dir("cp/ckfinder/help/files/images");
	    $ret .= "<br>[cp/ckfinder/help/files/images]$f22 files copied!";			 
		$f23 = $this->copy_dir("cp/ckfinder/help/files/other");
	    $ret .= "<br>[cp/ckfinder/help/files/other]$f23 files copied!";
		$f24 = $this->copy_dir("cp/ckfinder/core");
	    $ret .= "<br>[cp/ckfinder/core]$f24 files copied!";	
		$f25 = $this->copy_dir("cp/ckfinder/core/connector");
	    $ret .= "<br>[cp/ckfinder/core/connector]$f25 files copied!";			 
		$f26 = $this->copy_dir("cp/ckfinder/core/connector/php");
	    $ret .= "<br>[cp/ckfinder/core/connector/php]$f26 files copied!";		 
		$f27 = $this->copy_dir("cp/ckfinder/core/connector/php/lang");
	    $ret .= "<br>[cp/ckfinder/core/connector/php/lang]$f27 files copied!";			 
		$f28 = $this->copy_dir("cp/ckfinder/core/connector/php/php5");
	    $ret .= "<br>[cp/ckfinder/core/connector/php/php5]$f28 files copied!";		
		$f29 = $this->copy_dir("cp/ckfinder/core/connector/php/php5/CommandHandler");
	    $ret .= "<br>[cp/ckfinder/core/connector/php/php5/CommandHandler]$f29 files copied!";			 
		$f30 = $this->copy_dir("cp/ckfinder/core/connector/php/php5/Core");
	    $ret .= "<br>[cp/ckfinder/core/connector/php/php5/Core]$f30 files copied!";
		$f31 = $this->copy_dir("cp/ckfinder/core/connector/php/php5/ErrorHandler");
	    $ret .= "<br>[cp/ckfinder/core/connector/php/php5/ErrorHandler]$f31 files copied!";
		$f32 = $this->copy_dir("cp/ckfinder/core/connector/php/php5/Utils");
	    $ret .= "<br>[cp/ckfinder/core/connector/php/php5/Utils]$f32 files copied!";

		$f33 = $this->copy_dir("cp/ckfinder/plugins");
	    $ret .= "<br>[cp/ckfinder/plugins]$f33 files copied!";	
		 
		$f34 = $this->copy_dir("cp/ckfinder/plugins/flashupload");
	    $ret .= "<br>[cp/ckfinder/plugins/flashupload]$f34 files copied!";
		$f35 = $this->copy_dir("cp/ckfinder/plugins/flashupload/flash");
	    $ret .= "<br>[cp/ckfinder/plugins/flashupload/flash]$f35 files copied!";
		 
		$f36 = $this->copy_dir("cp/ckfinder/plugins/imageresize");
	    $ret .= "<br>[cp/ckfinder/plugins/imageresize]$f36 files copied!";		 
		$f37 = $this->copy_dir("cp/ckfinder/plugins/imageresize/images");
	    $ret .= "<br>[cp/ckfinder/plugins/imageresize/images]$f37 files copied!";	
		 
		$f38 = $this->copy_dir("cp/ckfinder/plugins/watermark");
	    $ret .= "<br>[cp/ckfinder/plugins/watermark]$f38 files copied!";
		 
		$f39 = $this->copy_dir("cp/ckfinder/plugins/dummy");
	    $ret .= "<br>[cp/ckfinder/plugins/dummy]$f39 files copied!";	
		$f40 = $this->copy_dir("cp/ckfinder/plugins/dummy/lang");
	    $ret .= "<br>[cp/ckfinder/plugins/dummy/lang]$f40 files copied!";			 
		 

		$ck_appurl_path = '/';
        $ckf = $this->modify_config_file("cp/ckfinder/config.php",'@_PATH_@',$ck_appurl_path);	
	    if (!$ckf) 
		    $ret .= "<br>[ckfinder::config.php]Configuration can't saved!";	
	
        return ($ret);		
	}	

	//change @keywords@...just call modify_data_dir..
 	function change_data($datadir=null,$data=null) {
	    if ((!$datadir) || (!$data)) 
		    return false;
	
	    if (!empty($data)) {

			$this->log .=  $this->location.'<br>';
			$this->log .=  $this->posted_appname.'<br>';
			
		    foreach ($data as $name=>$key) {
			   $this->log .=  $name .':'.$key.'<br>';
			}

			$ret = $this->modify_data_dir($datadir, $data, true);
			return ($ret);
		}
		return false;
	}

	//modify .html files @keywords@
    function modify_data_dir($dirname=null,$data=null, $verbose=null) {
        if (empty($data)) return false;	

		$sourcedir = $this->app_location . '/' . $dirname;
		
	    $fmeter = 0;
		if ($verbose)
		  $this->log .=  '<hr>'.$sourcedir.':<br>';		
		
	    if (!is_dir($sourcedir)) {
		   $this->log .= '<br>MODIFY_DATA_DIR:Error, invalid sourcedir! '.$sourcedir;
		   return false;		 
		}
		  
        $mydir = dir($sourcedir);
        while ($fileread = $mydir->read ()) { 
	
           if (($fileread!='.') && ($fileread!='..') && (!is_dir($sourcedir.'/'.$fileread))) {
			  
			  if (stristr($fileread,'.htm')) {
				$fdata = @file_get_contents("$sourcedir/$fileread");
				
				foreach ($data as $name=>$key) {
				    $myname = '@'.strtoupper($name).'@';
					$fdata = str_replace($myname,$key,$fdata);
				}		
		        @file_put_contents("$sourcedir/$fileread", $fdata);
				
				if ($verbose)
					$this->log .=  'modify:'.$sourcedir.'/'.$fileread.'<br>'; 
				$fmeter+=1; 				
			  }
           }
        }
        $mydir->close ();	
		
		$ret = $fmeter ? $fmeter : '0';
		return ($ret);
	}

	//add cp htaccess security...
    function add_cp_htaccess() {
  
		$htpass_path = $this->prpath;// . '/'; 
		$htaccess_path = $this->app_location . '/cp/'; 
		$this->log .=   '<br>HTACCESS PATH:'. $htpass_path.'>'.	$htaccess_path;	
		
		if (is_dir($htaccess_path)) {
		
			$htpass_file = $htpass_path . 'htpasswd-'.$this->posted_appname;//per app
			$htaccess_file = $htaccess_path . '.htaccess';
		    $this->log .=  '<br>HTACCESS FILE:'. $htpass_file.'>'.	$htaccess_file;	
	    }
		else
		    return false;
			
		// Initializing class htaccess as $ht
		$ht = new htaccess($htaccess_file, $htpass_file);//"/var/www/.htaccess","/var/www/htpasswd");
		// Adding user
		//$ht->addUser($this->posted_appname, $this->posted_password); //<< NOT THE APP NAME AS USERNAME..
		$ht->addUser($this->posted_mail, $this->posted_password); //<<<< THE MAIL..
		
		// Changing password for User
		//$ht->setPasswd("username","newPassword");
		// Getting all usernames from set password file
		$users = $ht->getUsers();
		for($i=0;$i<count($users);$i++){
			$this->log .= $users[$i];
		}
		// Deleting user
		//$ht->delUser("username");
		// Setting authenification type
		// If you don't set, the default type will be "Basic"
		$ht->setAuthType("Basic");
		// Setting authenification area name
		// If you don't set, the default name will be "Internal Area"
		$ht->setAuthName("Control Panel");
		//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		// finally you have to process addLogin()
		// to write out the .htaccess file
		$ht->addLogin();
		// To delete a Login use the delLogin function
		//$ht->delLogin();	
		
		return true;
	}

	//create .haccess fioe for the root dir of app
	function create_htaccess_file() {

		$targetdir = $this->app_location ."/";		
		
	    $htaccess = "
RewriteEngine On

RewriteRule ^addtocart/([^/]*)/([^/]*)/([^/]*)/$ /katalog.php?t=addtocart&a=$1&cat=$2&page=$3 [L]
RewriteRule ^removefromcart/([^/]*)/([^/]*)/([^/]*)/$ /katalog.php?t=removefromcart&a=$1&cat=$2&page=$3 [L]
RewriteRule ^kshow/([^/]*)/([^/]*)/([^/]*)/$ /katalog.php?t=kshow&cat=$1&page=$2&id=$3 [L]
RewriteRule ^kshow/([^/]*)/([^/]*)/$ /katalog.php?t=kshow&cat=$1&id=$2 [L]
RewriteRule ^klist/([^/]*)/([^/]*)/$ /katalog.php?t=klist&cat=$1&page=$2 [L]
RewriteRule ^klist/([^/]*)/$ /katalog.php?t=klist&cat=$1 [L]
RewriteRule ^lan/([^/]*)/$ /katalog.php?t=lang&langsel=$1 [L]

RewriteRule ^search/([^/]*)/([^/]*)/([^/]*)/$ /search.php?t=search&input=$1&stype=$2&page=$3 [L]
RewriteRule ^search/([^/]*)/([^/]*)/$ /search.php?t=search&input=$1&stype=$2 [L]

RewriteRule ^feed/([^/]*)/([^/]*)/([^/]*)/([^/]*)/$ /katalog.php?t=feed&cat=$1&page=$2&id=$3&format=$4 [L]
RewriteRule ^feed/([^/]*)/([^/]*)/([^/]*)/$ /katalog.php?t=feed&cat=$1&page=$2&format=$3 [L]
RewriteRule ^feed/([^/]*)/([^/]*)/$ /katalog.php?t=feed&dpc=$1&format=$2 [L]

RewriteRule ^(viewcart|cart)/?$ katalog.php?t=viewcart    [NC,L] 
RewriteRule ^(shlogin|login)/?$ katalog.php?t=shlogin     [NC,L] 
RewriteRule ^(shlogout|logout)/?$ katalog.php?t=shlogout  [NC,L] 
RewriteRule ^(shlogin|rempwd|clearcart|signup|transview|fastpick|wslist|printcart)/?$ katalog.php?t=$1    [NC,L] 

RewriteRule ^addcart/([^/]*)/([^/]*)/([^/]*)/([^/]*)/$ /katalog.php?t=addtocart&a=$1&cat=$2&page=$3&qty=$4 [L]
RewriteRule ^addcart/([^/]*)/([^/]*)/([^/]*)/$ /katalog.php?t=addtocart&a=$1&cat=$2&page=$3 [L]
RewriteRule ^remcart/([^/]*)/([^/]*)/([^/]*)/([^/]*)/$ /katalog.php?t=removefromcart&a=$1&cat=$2&page=$3&qty=$4 [L]
RewriteRule ^remcart/([^/]*)/([^/]*)/([^/]*)/$ /katalog.php?t=removefromcart&a=$1&cat=$2&page=$3 [L]

RewriteRule ^calc/([^/]*)/([^/]*)/$ /katalog.php?t=calc&$1=$2 [L]
RewriteRule ^(calc|recalc)/?$ katalog.php?t=calc  [NC,L] 

RewriteRule ^signup/([^/]*)/$ /katalog.php?t=signup&invtype=$1 [L]
RewriteRule ^contact/([^/]*)/$ /contact.php?t=contact&branch=$1 [L]
RewriteRule ^contact/$ /contact.php [L]
RewriteRule ^search/$ /search.php [L]
RewriteRule ^subscribe/$ /subscribe.php?t=subscribe [L]
RewriteRule ^unsubscribe/$ /subscribe.php?t=unsubscribe [L]
RewriteRule ^transviewhtml/([^/]*)/$ /katalog.php?t=transviewhtml&tid=$1 [L]

RewriteRule ^trload/([^/]*)/$ /katalog.php?t=loadcart&tid=$1 [L]
RewriteRule ^trview/([^/]*)/$ /katalog.php?t=transviewhtml&tid=$1 [L]
RewriteRule ^trview/$ /katalog.php?t=transview [L]

RewriteRule ^([^/]*)/$ /index.php?t=$1 [L]
";			
	
		if ($htp = fopen($targetdir . ".htaccess",w)) { 
           fputs($htp,$htaccess); 
		   fclose($htp);	
		   return true;
		}
		
		return false;
	}    
	
	//change mode of a file
    function file_chmod($filename=null,$mode=null) {

        if ((!$mode )|| (!$filename)) 
		    return false;	
		$this->log .=  '<br>CHMOD:'.$filename;
		
		$fif = $this->app_location ."/" . $filename;
		
		$ret = chmod($fif, $mode);
		$this->log .=  '<br>:'.$fif.'<br>';
		return ($ret);
    }

	//copy dir 
    function copy_dir($dirname=null,$override=null,$tdirname=null) {
	    $sourcedir = $this->prpath .'/init-app/' . $dirname;
		$targetdir = $this->app_location . "/";
		
		if (($tdirname) && ($tdirname != $dirname))	//other dir name 	
		  $targetdir .= $tdirname;
		else  
		  $targetdir .= $dirname;  
	    $meter = 0;
		
	    if (!is_dir($sourcedir)) {
		    $this->log .= '<br>COPY_DIR:Error, Invalid sourcedir!';
		    return false;		
		}	
		if (!is_dir($targetdir)) 
		    mkdir($targetdir);  
		  
        $mydir = dir($sourcedir);
        while ($fileread = $mydir->read ()) { 
	
           if (($fileread!='.') && ($fileread!='..') && (!is_dir($sourcedir.'/'.$fileread))) {

		      if ($override) { 			  
                $c = copy("$sourcedir/$fileread","$targetdir/$fileread");
				$msg = $c ? 'Overwited' : 'failed to overwrite';
                //$this->log .= '<BR>FILE:'.$targetdir.'/'.$fileread .' '. $msg .'!<br>'; 					
			  }	
			  else {
			    if (!is_readable("$targetdir/$fileread")) {
				  $c = copy("$sourcedir/$fileread","$targetdir/$fileread");
				  $msg = $c ? 'copied' : 'failed to copy';
				}
                else
                  $msg = 'exist';	
                //$this->log .= '<BR>FILE:'.$targetdir.'/'.$fileread .' '. $msg .'!<br>';				  
			  }
              $fmeter+=1; 
           }
        }
        $mydir->close ();	
		$ret = $fmeter ? "[$targetdir]" .$fmeter : '0';
		
		$this->log .= "<br>[$dirname]$fmeter files copied!";
		
		return ($ret);
	}	
	
	//modify conf file..as readed from init-app dir islocalfile
 	function modify_config_file($file,$keyapp=null,$keyval=null,$altname=null,$islocalfile=false) {
		
		$writefile = $altname ? $altname : $file;
		
		$keyapp = $keyapp ? $keyapp : 'wdemo';
		$keyval = $keyval ? $keyval : $this->posted_appname;		
	
	    $readpath = $islocalfile ? $this->app_location . '/' : $this->prpath . '/init-app/';
		$writepath = $this->app_location . '/';
	    $read_filepath = $readpath . $file;
		$write_filepath = $writepath . $writefile;
		
        if (($keyapp) && ($keyval))
		   $out = str_replace($keyapp,$keyval,@file_get_contents($read_filepath));
		else
           $out = @file_get_contents($read_filepath);  		

        $this->log .=  "MODIFY CONFIG FILE:<br>" . $read_filepath.'<br>'.$write_filepath. '<br><br>';	
		
        if ($fp = @fopen ($write_filepath , "w")) {
		
            fwrite ($fp, $out);
            fclose ($fp);	   			   
			return (true);
	    }
	     
		return (false);	    	  	
	}	
	
	//modify existing app config file...as readed from the local app copy
 	function local_modify_config_file($file,$keyapp=null,$keyval=null) {
		$keyapp = $keyapp ? $keyapp : 'wdemo';
		$keyval = $keyval ? $keyval : $this->posted_appname;

	    $readpath = $this->app_location . '/';
		$writepath = $this->app_location . '/';
		
	    $read_filepath = $readpath . $file;
		$write_filepath = $writepath . $file;
		
		$out = str_replace($keyapp,$keyval,@file_get_contents($read_filepath));
        $this->log .=  'SCHEMA MODIFY CONFIG FILE:'.$read_filepath.':'.$write_filepath. '<br>';	
		
        if ($fp = @fopen ($write_filepath , "w")) {
            fwrite ($fp, $out);
            fclose ($fp);   			   
			return (true);
	    }
		return (false);	  	
	}		

	//create db schema...
    function create_database($appurl=null, $appmail=null) {
	    $db = GetGlobal('db');
		$supervisor_email = $appmail ? $appmail : ($appurl ? 'admin@'.$appurl : 'admin@wdemo-url');//default
		$supervisor_password = $this->posted_password ? md5($this->posted_password) : md5('admin');

		if (!$db) {
		    $this->log =  "<br>No db connection!<br><br>";
			return false;
		}

		//application name generator ..if appname is on...
		$appnameison = ($_SESSION['appnameison']=='true') ? $_SESSION['appnameison'] : false;//true;
		if ($appnameison) 
			$app = $this->posted_appname; //without prefix
		else   		
			$app = $this->dir_prefix . $this->posted_appname; //app is the path to conf for reading db user/pass
		
		//return pointer
		$appdb = GetGlobal('controller')->calldpc_method('database.switch_db use '.$app.'++1+1');		 
		$this->log .=  '<br>switch database:'.$this->posted_appname;
		//$db = GetGlobal('db'); 

		if ($appdb) { 
		    
			$this->log .= ' switched succesfuly!';
        
			//..continue creating tables etc.... 
			//READ SCHEMA FORM INIT-APP FOLDER 
			if ($this->encoding) {
				if (is_readable(paramload('SHELL','prpath').'/schema-'.$this->encoding.'.sql'))
					$sql_f = file_get_contents($this->prpath . '/schema-'.$this->encoding.'.sql');
				else
					$sql_f = file_get_contents($this->prpath . '/schema-utf8.sql'); 		  
			}  
			else  
				$sql_f = file_get_contents($this->prpath . '/schema-utf8.sql');  

			$sql_parts = explode(';',$sql_f);
			$schemafile = $this->encoding ? 'file-schema:' . $this->prpath . '/schema-'. $this->encoding . '.sql' 
										  : 'file-schema:' . $this->prpath . '/schema-utf8.sql';		
			$this->log .=  '<br>'.$schemafile.'<br>';
		
			$queries = 0; 
			foreach ($sql_parts as $i=>$sql) {
				$ret = $appdb->Execute($sql,1);	
				$this->log .=  $sql.'<br>'; 		  
				$queries+=1;
			}
		
		
			//insert admin user....for cp 8
			$insSQL = "INSERT INTO users set code2='1',email='$supervisor_email',fname='admin',notes='ACTIVE',seclevid=8,username='$supervisor_email', password='$supervisor_password',vpass='$supervisor_password'";
			$ret = $appdb->Execute($insSQL,1);	
			$this->log .=  '<br>'.$insSQL.'<br>';
			
			//insert super admin user....for cp 9
			$insSQL = "INSERT INTO users set code2='2',email='admin@stereobit.gr',fname='admin',notes='ACTIVE',seclevid=9,username='admin@stereobit.gr', password='".md5('##########')."',vpass='".md5('##########')."'";
			$ret = $appdb->Execute($insSQL,1);	
			//$this->log .=  '<br>'.$insSQL.'<br>';	//silent			
		
			return ($queries);
        }
		else {
		    $this->log .= '<br>database path config file:'.$app;
		    $this->log .= '<br>FAILED!';
			//..rollback
		}
		
        return false;		
	}	
	
	function modify_html_meta() {
	
		$domain = 'http://' . $this->posted_appname . '.' . $this->rootdomain ;
		$email = $this->posted_mail ? $this->posted_mail : $this->posted_appname.'@'.$this->rootdomain ;
		$title = $this->posted_appname;
		
		$data =array('DOMAIN-NAME'=>$domain,'E-MAIL'=>$email,
		              'TITLE'=>$title,'SUBTITLE'=>'Powered by stereobit.networlds',
		              'META-DESCRIPTION'=>'stereobit','META-KEYWORDS'=>'stereobit',
					  'FEEDBURNER'=>'#','TWITTER'=>'#','FACEBOOK'=>'#','GOOGLEPLUS'=>'#',
					  'FLICKR'=>'#','VIMEO'=>'#','LINKEDIN'=>'#','DELICIOUS'=>'#',
					  'FBLIKEBOX-PLUGIN'=>'#','FLICKRBADGE-PLUGIN'=>'#',
					  'LATITUDE'=>'40.690446','LONGITUDE'=>'22.794224',
					  'ADDRESS'=>'Allatini 27 St, 54250, Thessaloniki',
					  'TEL1'=>'T_:+30 2310 950 155', 'TEL2'=>'M_:+30 6937 367 879',
					  'UA-CODE-PLACEHOLDER'=>'UA-XXXXXXXXX',
					  'PROFILE-TITLE'=>'Company','PRODUCTS-TITLE'=>'Products','CONTACT-TITLE'=>'Contact');
					  
		$ok = $this->change_data('cp/html', $data);

        return ($ok);						  
	}
	
	//execute scenario 
	function newapp_scenario() {
        $mydbuser = null; //get the val from new-cpanel...
		
		$acopy = $this->copy_application_files();
		
		if ($acopy) {
		
		  $ret .= $acopy; 

	      if ($cpan = $this->new_cpanel($mydbuser)) {
			$this->log .=  '<br>Cpanel:'.$cpan;
		   
			$this->log .= "<br>Create db schema for application:" . $this->posted_appname;
			$appurl = 'http://' . $this->posted_appname . '.' . $this->rootdomain ;
			$email = $this->posted_mail ? $this->posted_mail : $this->posted_appname.'@'.$this->rootdomain ;	   
			$db_prefix = $this->db_prefix;
			$this->log .= "<br>Prefix:" . $db_prefix;
			$db_encoding = $this->encoding;
			$this->log .= "<br>Encoding:" . $db_encoding;
			
			//copy files
            //$ret .= $this->copy_application_files(); 			
	   
			//modify config.ini..replace user, mail etc into config.ini
			//MUST BE UNIQUE 7chars....GET THE VAL FROM NEW_CPANEL FUNC
			//$mydbuser = (strlen($this->posted_appname)>6) ? substr($this->posted_appname,0,6) : $this->posted_appname;
			
			if (($mydbuser) && ($this->posted_password)) {
				$e = $this->local_modify_config_file("cp/config.ini",$db_prefix.'wduser',$db_prefix.$mydbuser);
				if (!$e) $ret .= "<br>[config.ini]Configuration can't saved!(wduser)";			 
				$e = $this->local_modify_config_file("cp/config.ini",'wdpass',$this->posted_password);
				if (!$e) $ret .= "<br>[config.ini]Configuration can't saved!(wdpass)";	
				//replace email account pass... wdemo-pass...as db pass
				$e = $this->local_modify_config_file("cp/config.ini",$this->posted_appname.'-pass',$this->posted_password);
				if (!$e) $ret .= "<br>[config.ini]Configuration can't saved!('-pass')";			
			}
			
			//application name generator ..if appname is on...
			$appnameison = ($_SESSION['appnameison']=='true') ? $_SESSION['appnameison'] : false;//true;
			$appdirname = ($appnameison) ? $this->posted_appname :
							$this->dir_prefix . $this->posted_appname;
			//...all wdemo-dir s became $appdirname
			$e = $this->local_modify_config_file("cp/config.ini",$this->posted_appname.'-dir',$appdirname);
			if (!$e) $ret .= "<br>[config.ini]Configuration can't saved!(-dir)";
			$e = $this->local_modify_config_file("cp/myconfig.txt",$this->posted_appname.'-dir',$appdirname);
			if (!$e) $ret .= "<br>[myconfig.txt]Configuration can't saved!(-dir)";				
		
			if ($appurl) { //appname has already replace wdemo-url to appname-url
				//...all mails became info@appname-url (=stereobit.gr=post appurl)
				$e = $this->local_modify_config_file("cp/config.ini",$this->posted_appname.'-url',$appurl);
				if (!$e) $ret .= "<br>[config.ini]Configuration can't saved!(-url)";
				$e = $this->local_modify_config_file("cp/myconfig.txt",$this->posted_appname.'-url',$appurl);
				if (!$e) $ret .= "<br>[myconfig.txt]Configuration can't saved!(-url)";			
				//replace title... wdemo-title...
				$e = $this->local_modify_config_file("cp/config.ini",$this->posted_appname.'-title',$appurl);
				if (!$e) $ret .= "<br>[config.ini]Configuration can't saved!(-title)";
				$e = $this->local_modify_config_file("cp/myconfig.txt",$this->posted_appname.'-title',$appurl);
				if (!$e) $ret .= "<br>[myconfig.txt]Configuration can't saved!(-title)";			
				//replace email realm... wdemo-realm...
				$e = $this->local_modify_config_file("cp/config.ini",$this->posted_appname.'-realm',$appurl);
				if (!$e) $ret .= "<br>[config.ini]Configuration can't saved!(-realm)";	
				$e = $this->local_modify_config_file("cp/myconfig.txt",$this->posted_appname.'-realm',$appurl);
				if (!$e) $ret .= "<br>[myconfig.txt]Configuration can't saved!(-realm)";			
			}
		
			if ($email) { //appname has already replace the names above
				//...all mails became from info@appname-url (=stereobit.gr=post appurl) to post appmail (=balexiou@stereobit.gr) 
				$e = $this->local_modify_config_file("cp/config.ini",'info@'.$appurl,$email);
				if (!$e) $ret .= "<br>[config.ini]Configuration can't saved!(email)";
				$e = $this->local_modify_config_file("cp/myconfig.txt",'info@'.$appurl,$email);
				if (!$e) $ret .= "<br>[myconfig.txt]Configuration can't saved!(email)";			
			} 			
		   
            //create db
            $q = $this->create_database($appurl,$email);
            $ret .= "<br>[database]$q queries executed!";		   
		  }   
		  else { 
		    $this->log .=  '<br>Cpanel error!';
			$this->log .=  $this->error_log;
		    /* 
			
			//WHEN A USER SUBMIT THE SAME NAME (DNS EXIST SO ROLLBACK ??????? NO)...
			
		    $this->log .= '<br>ROLLBACK....';
            if (!$this->posted_mail) //no mail submited, a new mail has created
                $this->log .= $this->rollback_cpanel(true);
		    else	//user has post his own mail   
		        $this->log .= $this->rollback_cpanel(false);
			   
			//delete files... 
			$permanent = false;
            $this->remove_application_files($permanent,true);
			*/			
		  }
        }//acopy
        else {
		    $this->error_log .=  'Failed during copy procedure.';
		    $this->log .=  '<br>Copy error!';
			$this->log .=  $this->error_log;
			
		    //rollback delete files... 
			$permanent = false;
            $this->remove_application_files($permanent,true);		
        }		

        return ($ret);		   
    }	
	
	function newapp_form($msg=null) {

		$filename = seturl('t=addapp');
		
		if ($msg)
		   $ret = '<h1>'.$msg.'</h1>';
	  
		$ret .= "<FORM action='". $filename . "' method=post class=\"thin\">" . 
	          "name:<input type=\"text\" name=\"cpapp\" value=\"\" size=\"32\" maxlength=\"64\">" .
			  "<br>app-user-mail:<input type=\"text\" name=\"cpappmail\" value=\"\" size=\"32\" maxlength=\"64\">" .
              "<br>encoding:<input type=\"text\" name=\"encoding\" value=\"{$this->encoding}\" size=\"32\" maxlength=\"64\">";	  
		$ret .= "<br>location:<input type=\"text\" name=\"cploc\" value=\"{$this->location}\" size=\"32\" maxlength=\"64\">";			
		$ret .= "<br>Password:<input type=\"password\" name=\"upass\" value=\"\" size=\"32\" maxlength=\"64\">";	  
		$ret .= "<br>Re-type Password:<input type=\"password\" name=\"upassretype\" value=\"\" size=\"32\" maxlength=\"64\">";
		$ret .= "<br>Init theme:<input type=\"text\" name=\"initheme\" value=\"\" size=\"32\" maxlength=\"64\">";			  
		$ret .= "<br><input type=\"submit\" name=\"Submit\" value=\"Create\">" .
		        "<input type=\"hidden\" name=\"FormAction\" value=\"addapp\" />" .
                "</FORM>";	

        return ($ret);			  
	}	
   
    function newapp() {
	
		if (($this->posted_appname) && ($this->posted_password) &&
			(strlen($this->posted_appname)>=5) && (strlen($this->posted_password)>5) && 
		    ($this->posted_password === $this->posted_password_retyped)) {
            //setup....
            $ret = '<h1>Add Application</h1>';			
			$ret .= $this->newapp_scenario();
		}
        else {
		   
		    if (!empty($_POST)) {
			    if (strlen($this->posted_appname)<5)
                    $msg = 'name must be at least 5 characters long.';	
				elseif (strlen($this->posted_password)<=5)
                    $msg = 'password must be at least 6 characters long.';	
				elseif ($this->posted_password!==$this->posted_password_retyped)
                    $msg = 'please re-type the password.';	
				else
                    $msg = null;				
            }  			
		
		    //form
			$ret = $this->cmds(); 
			$ret .= '<h1>Add Application</h1>';
			$ret .= $this->newapp_form($msg);
        }

        return ($ret); 		
    }
	
	
	
	//REMOVE APP
	
	//remove cp htpass from root cp
    function remove_cp_htaccess() {
  
		$htpass_path = $this->prpath;
		$htpass_file = $htpass_path . 'htpasswd-'.$this->posted_appname;
		$this->log .=   '<br>HTPASSWORD FILE:'. $htpass_file ;	
		
		if (is_readable($htpass_file)) {
		
			$ok = @unlink($htpass_file);
		    $this->log .=  $ok ? ' ok!' : ' error!';
            return ($ok);			
	    }
		else {
		    $this->log .= ' file not exist!';
		    return false;
		}	
    }			
	
    function remove_dir($dirname=null) {
		if (!$dirname) return false;
		$ret = false;	
		
		$targetdir = $this->app_location ."/" . $dirname;
		$this->log .=  '<br>delete:' . $targetdir;
	
		if (is_dir($targetdir)) {
			// the dir must be empty
			$ret = @rmdir($targetdir);
			
		}
		$this->log .=  $ret ? ':1':':0';	
		return ($ret); 	
    }	
	
    function remove_application_files($permanent=false, $verbose=false) {
		
	    $targetdir = $this->app_location . "/";
		
		if ($permanent) {
			//remove app root....
			//release app name
			//...delete ziped backupfiles...
			//....		
			$ret = $this->remove_dir('/');
			//must be empty
		  
			return ($ret);
		}
        else {		
			//zipp files as backup before, if not permanent...
			//.....
		
			$fmeter = 0;		
			if (!is_dir($targetdir)) return false;
		
			$mydir = dir($targetdir);
			while ($fileread = $mydir->read ()) { 

				if (($fileread!='.') && ($fileread!='..')) {

					if (is_dir($targetdir.'/'.$fileread)) {//remove dir
			  
						$this->remove_dir($fileread);
						$fmeter+=1;
					}
					elseif ((is_readable($targetdir.'/'.$fileread)) && //remove file
							(!is_dir($targetdir.'/'.$fileread)) ) {
				  
						@unlink("$targetdir/$fileread");
						$fmeter+=1; 
					}
				}
				if ($verbose)
					$this->log .= '<br>'.$targetdir.'/'.$fileread.':';
			}
			$mydir->close ();	
		}
		
		return ($fmeter);		
	}   

    function removeapp_scenario() {
	    $perm = GetParam('isperm');
		
		//check pass for security reason....
		//....
		
        //$this->log .= '<br>ROLLBACK....';	
        $ret .= $this->rollback_cpanel(true); 		
	   
        $this->log .= '<br>Remove:'. $this->posted_appname . " (".$this->app_location.")"; 
		
		if ($perm) {
		
	        $targetdir = $this->app_location . "/";	
            $this->log .= '<br>Delete permanent directory app:'. $targetdir;		   
		    //$killdir = $cp->_cpapi1_exec('Fileman', 'killdir', array('dir:testme')); //???		

            //$this->remove_cp_htaccess();		   
		}   
		else   
	        $this->remove_application_files($perm,true);
				
		$this->remove_cp_htaccess();		   	

        return ($ret); 		   
    }	
	
	function removeapp_form() {
		$filename = seturl('t=delapp');
		
		$ret = "<FORM action='". $filename . "' method=post class=\"thin\">" . 
	          "Remove app name:<input type=\"text\" name=\"cpapp\" value=\"\" size=\"32\" maxlength=\"64\">";	  
		//$ret .= "location:<input type=\"text\" name=\"cploc\" value=\"$location\" size=\"32\" maxlength=\"64\">";			
		$ret .= "<br>Password:<input type=\"password\" name=\"upass\" value=\"\" size=\"32\" maxlength=\"64\">";	
		$ret .= "<br>is permanent:<input type=\"checkbox\" name=\"isperm\">";		  
		$ret .= "<br><input type=\"submit\" name=\"Submit\" value=\"Remove\">" .
		        "<input type=\"hidden\" name=\"FormAction\" value=\"delapp\" />" .
				"</FORM>";		
				
		return ($ret);		
	}
	
	function delapp() {
	
		if (($this->posted_appname) && ($this->posted_password)) {
            //setup....	
			$ret = '<h1>Delete Application</h1>';
			$ret .= $this->removeapp_scenario();
		}
        else {
		    //form
			$ret = $this->cmds(); 
			$ret .= '<h1>Delete Application</h1>';
			$ret .= $this->removeapp_form();
        }

        return ($ret); 		
	}
	
	
	//CHANGE SKIN
	
    function skin_remove_dir($dirname=null,$tdirname=null, $verbose=null) {
	    $verbose=true;
		$sourcedir = $this->app_location ."/" . $dirname;
		
		if (($tdirname) && ($tdirname != $dirname))	//other dir name 	
		  $targetdir = $this->prpath ."/init-app/" . $tdirname;
		else  
		  $targetdir = $this->prpath ."/init-app/" . $dirname;  
	    $fmeter = 0;	

		if ($verbose) 
		  $this->log .=  '<hr>'.$sourcedir.':'.$targetdir.'<br>';		
		
	    if (!is_dir($targetdir)) {
		   $this->log .=  '<br>Error, no target dir!';
		   return false;
		}   
		
        $mydir = dir($targetdir);
        while ($fileread = $mydir->read ()) { 
	
           if (($fileread!='.') && ($fileread!='..')) {
              //echo $fileread; 
			  if ((is_readable($sourcedir.'/'.$fileread)) && 
			      (!is_dir($sourcedir.'/'.$fileread)) ) {
				  
				unlink("$sourcedir/$fileread");
				
				if ($verbose)
			      $this->log .=  'delete:'; 
                $fmeter+=1; 
			  }
           }
		   if ($verbose)
		     $this->log .=  $sourcedir.'/'.$fileread.'>>>>>>>>'.$targetdir.'/'.$fileread.'<br>'; 
        }
        $mydir->close ();	
		return ($fmeter);		
	}	
	
    function skin_copy_dir($dirname=null,$override=null,$tdirname=null, $verbose=null) {
	    $sourcedir = $this->prpath .'/init-app/' . $dirname;
		$targetdir = $this->app_location .'/';
		
		if (($tdirname) && ($tdirname != $dirname))	//other dir name 	
		  $targetdir .= $tdirname;
		else  
		  $targetdir .= $dirname;
	    $fmeter = 0;
		
		if ($verbose)
		  $this->log .= '<hr>'.$sourcedir.':'.$targetdir.'<br>';		
		
	    if (!is_dir($sourcedir)) {
		    $this->log .= '<br>Error, no source dir '.$sourcedir;
		    return false;		
		}	
		if (!is_dir($targetdir)) 
		    mkdir($targetdir);  	
		  
        $mydir = dir($sourcedir);
        while ($fileread = $mydir->read ()) { 
	
           if (($fileread!='.') && ($fileread!='..') && (!is_dir($sourcedir.'/'.$fileread))) {

		      if ($override) { 			  
                @copy("$sourcedir/$fileread","$targetdir/$fileread");
			  }	
			  else {
			    //if (!is_readable("$targetdir/$fileread"))
				  @copy("$sourcedir/$fileread","$targetdir/$fileread");
			  }
			  if ($verbose)
			    $this->log .= 'skin copy dir:'; 
		        $this->log .= $sourcedir.'/'.$fileread.'>>>>>>>>'.$targetdir.'/'.$fileread.'<br>';				
				
              $fmeter+=1; 
           }
        }
        $mydir->close ();	
		$ret = $fmeter ? "[$targetdir]" . $fmeter : '0';
		return ($ret);
	}	
	
	//local copy skin (into app filesystem)
    function skin_copy_local_dir($dirname=null,$override=null,$tdirname=null, $verbose=null) {

		$sourcedir = $this->app_location ."/" . $dirname;
		$targetdir = $this->app_location ."/";
		
		if (($tdirname) && ($tdirname != $dirname))	//other dir name 	
		  $targetdir .= $tdirname;
		else  
		  $targetdir .= $dirname;  
	    $fmeter = 0;
		
		if ($verbose)
		  $this->log .= '<hr>'.$sourcedir.':'.$targetdir.'<br>';		
		
	    if (!is_dir($sourcedir)) return false;		
		if (!is_dir($targetdir)) mkdir($targetdir);  
		  
        $mydir = dir($sourcedir);
        while ($fileread = $mydir->read ()) { 

           if (($fileread!='.') && ($fileread!='..') && (!is_dir($sourcedir.'/'.$fileread))) {

		      if ($override) { 			  
                copy("$sourcedir/$fileread","$targetdir/$fileread");
			  }	
			  else {
			    //if (!is_readable("$targetdir/$fileread"))
				  copy("$sourcedir/$fileread","$targetdir/$fileread");
			  }
			  if ($verbose) {
			    $this->log .= 'skin copy local dir:'; 
		        $this->log .= $sourcedir.'/'.$fileread.'>>>>>>>>'.$targetdir.'/'.$fileread.'<br>';				
			  }	
              $fmeter+=1; 
           }
        }
        $mydir->close ();	
		$ret = $fmeter ? $fmeter : '0';
		return ($ret);
	}		
	
	function skin_change_data() {
	    if (empty($_POST)) return false;	
	
	    if ($data=$_POST) {
		    $formaction = array_pop($data); //dummy
			$submit = array_pop($data); //dummy
			
			$location = array_pop($data);
			$this->log .= $location.'<br>';
			
		    $appname = array_pop($data);
			$this->log .= "<br>CHANGE SKIN DATA:".$appname.'<br>';
			
		    foreach ($data as $name=>$key) {
			   $this->log .= $name .':'.$key.'<br>';
			}
			$ret = $this->modify_data_dir('cp/html',$data, true);
			$this->log .= $ret;
			
			return ($ret);
		}
		return false;
	}	
	
    function skin_add_data() {
		$filename = seturl('t=chskinkeys');
		
		$app_prpath = $this->app_location."/cp/"; 
		$ret =  $app_prpath.'><br>';
		$url = $this->posted_appname . '.' . $this->rootdomain;
	  
		$ret .= "<FORM action='". $filename . "' method=post class=\"thin\">";
		$ret .= "<br>"; 
		$ret .= "<br>Domain name (or path):<input type=\"text\" name=\"domain-name\" value=\"$url\" size=\"32\" maxlength=\"64\">";	  
		$ret .= "<br>Title:<input type=\"text\" name=\"title\" value=\"\" size=\"32\" maxlength=\"64\">";	
		$ret .= "<br>Subitle:<input type=\"text\" name=\"subtitle\" value=\"\" size=\"32\" maxlength=\"64\">";	
		$ret .= "<br>Description:<input type=\"text\" name=\"meta-description\" value=\"\" size=\"32\" maxlength=\"64\">";	
		$ret .= "<br>Keywords:<input type=\"text\" name=\"meta-keywords\" value=\"\" size=\"32\" maxlength=\"64\">";	
		$ret .= "<br>"; 
		$ret .= "<br>Facebook:<input type=\"text\" name=\"facebook\" value=\"\" size=\"32\" maxlength=\"64\">";
		$ret .= "<br>Facebook likebox plugin:<input type=\"text\" name=\"fblikebox-plugin\" value=\"\" size=\"32\" maxlength=\"254\">";	  
		$ret .= "<br>Twitter:<input type=\"text\" name=\"twitter\" value=\"\" size=\"32\" maxlength=\"64\">";
		$ret .= "<br>Google+:<input type=\"text\" name=\"googleplus\" value=\"\" size=\"32\" maxlength=\"64\">";
		$ret .= "<br>Flickr:<input type=\"text\" name=\"flickr\" value=\"\" size=\"32\" maxlength=\"64\">";
		$ret .= "<br>Flickr badge plugin:<input type=\"text\" name=\"flickrbadge-plugin\" value=\"\" size=\"32\" maxlength=\"64\">";	  
		$ret .= "<br>Vimeo:<input type=\"text\" name=\"vimeo\" value=\"\" size=\"32\" maxlength=\"64\">";
		$ret .= "<br>LinkedIn:<input type=\"text\" name=\"linkedin\" value=\"\" size=\"32\" maxlength=\"64\">";
		$ret .= "<br>Delicious:<input type=\"text\" name=\"delicious\" value=\"\" size=\"32\" maxlength=\"64\">";
		$ret .= "<br>Youtube:<input type=\"text\" name=\"youtube\" value=\"\" size=\"32\" maxlength=\"64\">";	  
		$ret .= "<br>Feed Burner:<input type=\"text\" name=\"feedburner\" value=\"\" size=\"32\" maxlength=\"64\">";	  
	  
		$ret .= "<input type=\"hidden\" name=\"appname\" value=\"{$this->posted_appname}\">"; 
		$ret .= "<input type=\"hidden\" name=\"location\" value=\"{$this->location}\">";	  
		$ret .= "<br><input type=\"submit\" name=\"Submit\" value=\"Change data\">" .
		        "<input type=\"hidden\" name=\"FormAction\" value=\"chskinkeys\" />" .
                "</FORM>";	

		return ($ret);			  
	}	
	
	function chskin_scenario() {
        $erase_skin = true;
        $local_copy_skin = true;
		//check pass for security reason....
		//....	

	    $ret .= "<br>Application:" . $this->posted_appname;
       
	    //echo '>',$_POST['cpskin'];
	    if ($skin = $this->posted_skin) {

			$is_local_theme = $this->is_local_theme;
			$skindir = $this->app_location ."/cp/themes/".$theme;
			
			if (($is_local_theme) && (is_dir($skindir)) ) {
				$this->log .= "<br>Local skin:".$theme;
				$local_copy_skin = true;
				$erase_skin = true;
			}		 
			else {
				$local_copy_skin = false;
				//$erase_skin = false;
			}	
	     
			//erase dirs
			if ($erase_skin) {
			
				$previous_skin = @file_get_contents($this->app_location."/cp/theme.skin");		 
				$this->log .= "<br>Erase skin:" . ($previous_skin ? $previous_skin : 'default');
				
				if ($previous_skin) {
					$f1 = $this->skin_remove_dir("cp/html","cp/themes/$previous_skin");
					$ret .= "<br>[cp/themes/$previous_skin/html]$f1 files removed!"; 	
					$f2 = $this->skin_remove_dir("images","cp/themes/$previous_skin/images");
					$ret .= "<br>[cp/themes/$previous_skin/images]$f2 files removed!"; 		 
					$f3 = $this->skin_remove_dir("javascripts","cp/themes/$previous_skin/javascripts");
					$ret .= "<br>[cp/themes/$previous_skin/javascripts]$f3 files removed!"; 		   
					$f4 = $this->skin_remove_dir("css","cp/themes/$previous_skin/css");
					$ret .= "<br>[cp/themes/$previous_skin/css]$f4 files removed!"; 				   
					$f5 = $this->skin_remove_dir("/","cp/themes/$previous_skin/pages");
					$ret .= "<br>[cp/themes/$previous_skin/pages]$f5 files removed!"; 			   
				}
				else {//default
					$f1 = $this->skin_remove_dir("cp/html");
					$ret .= "<br>[cp/html]$f1 files removed!"; 	
					$f2 = $this->skin_remove_dir("images");
					$ret .= "<br>[images]$f2 files removed!"; 		   
					$f3 = $this->skin_remove_dir("javascripts");
					$ret .= "<br>[javascripts]$f3 files removed!"; 	
					$f4 = $this->skin_remove_dir("css");
					$ret .= "<br>[css]$f4 files removed!"; 		   
					$f5 = $this->skin_remove_dir("/");
					$ret .= "<br>[pages]$f5 files removed!"; 		   
				}
		 }//safe erase
		 
         //copy dirs
         if ($local_copy_skin) {//copy from cp/themes/
			$f1 = $this->skin_copy_local_dir("cp/themes/$skin",true,'cp/html');
			$ret .= "<br>[cp/themes/$skin/html]$f1 files copied!"; 	
			$f2 = $this->skin_copy_local_dir("cp/themes/$skin/images",true,'images');
			$ret .= "<br>[cp/themes/$skin/images]$f2 files copied!"; 
			$f3 = $this->skin_copy_local_dir("cp/themes/$skin/javascripts",true,'javascripts');
			$ret .= "<br>[cp/themes/$skin/javascripts]$f3 files copied!"; 
			$f4 = $this->skin_copy_local_dir("cp/themes/$skin/css",true,'css');
			$ret .= "<br>[cp/themes/$skin/css]$f4 files copied!"; 		 
			$f5 = $this->skin_copy_local_dir("cp/themes/$skin/pages",true,'/');
			$ret .= "<br>[cp/themes/$skin/pages]$f5 files copied!"; 		 
         }
         else {	//retrieve from root app	 
			$f1 = $this->skin_copy_dir("cp/themes/$skin",true,'cp/html');
			$ret .= "<br>[cp/themes/$skin/html]$f1 files copied!"; 	
			$f2 = $this->skin_copy_dir("cp/themes/$skin/images",true,'images');
			$ret .= "<br>[cp/themes/$skin/images]$f2 files copied!"; 
			$f3 = $this->skin_copy_dir("cp/themes/$skin/javascripts",true,'javascripts');
			$ret .= "<br>[cp/themes/$skin/javascripts]$f3 files copied!"; 
			$f4 = $this->skin_copy_dir("cp/themes/$skin/css",true,'css');
			$ret .= "<br>[cp/themes/$skin/css]$f4 files copied!"; 		 
			$f5 = $this->skin_copy_dir("cp/themes/$skin/pages",true,'/');
			$ret .= "<br>[cp/themes/$skin/pages]$f5 files copied!"; 

			//copy local..current cp theme
			$t1 = $this->skin_copy_dir("cp/themes");
			$ret .= "<br>[cp/themes]$t1 files copied!";
			$t2 = $this->skin_copy_dir("cp/themes/$skin");
			$ret .= "<br>[cp/themes/$skin]$t2 files copied!";			 
			$t3 = $this->skin_copy_dir("cp/themes/$skin/css");
			$ret .= "<br>[cp/themes/$skin/css]$t3 files copied!";
			$t4 = $this->skin_copy_dir("cp/themes/$skin/images");
			$ret .= "<br>[cp/themes/$skin/images]$t4 files copied!";			 
			$t5 = $this->skin_copy_dir("cp/themes/$skin/pages");
			$ret .= "<br>[cp/themes/$skin/pages]$t5 files copied!";
			$t6 = $this->skin_copy_dir("cp/themes/$skin/javascripts");
			$ret .= "<br>[cp/themes/$skin/javascripts]$t6 files copied!";						
		 }
		 
         @file_put_contents($this->app_location."/cp/theme.skin",$skin);	 	 
	   }
	   else {//default
	   
         //erase dirs
		 if ($previous_skin = @file_get_contents($this->app_location."/cp/theme.skin")) {
           $f1 = $this->skin_remove_dir("cp/html","cp/themes/$previous_skin");
	       $ret .= "<br>[cp/themes/$previous_skin/html]$f1 files removed!"; 	
           $f2 = $this->skin_remove_dir("images","cp/themes/$previous_skin/images");
	       $ret .= "<br>[cp/themes/$previous_skin/images]$f2 files removed!"; 		 
           $f3 = $this->skin_remove_dir("javascripts","cp/themes/$previous_skin/javascripts");
	       $ret .= "<br>[cp/themes/$previous_skin/javascripts]$f3 files removed!"; 			   
           $f4 = $this->skin_remove_dir("css","cp/themes/$previous_skin/css");
	       $ret .= "<br>[cp/themes/$previous_skin/css]$f4 files removed!"; 		   
           $f5 = $this->skin_remove_dir("/","cp/themes/$previous_skin/pages");
	       $ret .= "<br>[cp/themes/$previous_skin/pages]$f5 files removed!"; 			   
		 }
		 else {//default
           $f1 = $this->skin_remove_dir("cp/html");
	       $ret .= "<br>[cp/html]$f1 files removed!"; 	
           $f2 = $this->skin_remove_dir("images");
	       $ret .= "<br>[images]$f2 files removed!"; 		   
           $f2 = $this->skin_remove_dir("javascripts");
	       $ret .= "<br>[javascripts]$f3 files removed!"; 	
           $f4 = $this->skin_remove_dir("css");
	       $ret .= "<br>[css]$f4 files removed!";		   
           $f5 = $this->skin_remove_dir("/",'pages');
	       $ret .= "<br>[pages]$f5 files removed!";			   
		 }

         //copy default dirs	 
         $f1 = $this->skin_copy_dir("cp/html",true);
	     $ret .= "<br>[cp/html]$f1 files copied!"; 	
         $f2 = $this->skin_copy_dir("images",true);
	     $ret .= "<br>[images]$f2 files copied!"; 
         $f2 = $this->skin_copy_dir("javascripts",true);
	     $ret .= "<br>[javascripts]$f3 files copied!"; 	
         $f4 = $this->skin_copy_dir("css",true);
	     $ret .= "<br>[css]$f4 files copied!"; 		 
         $f5 = $this->skin_copy_dir("pages",true);
	     $ret .= "<br>[pages]$f5 files copied!"; 			 
		 
         @unlink($this->app_location."/cp/theme.skin");		 
	   }
	   
	   
	   $pskin = $previous_skin ? $previous_skin : 'default';
	   $cskin = $skin ? $skin : 'default';
	   $ret .= '<hr><br>Previous Skin:'.$pskin;
	   $ret .= '<br>Current Skin:'.$cskin;
	   
	   //change keys
	   $ret .= $this->skin_add_data();


       return ($ret);	   
	}
	
	function chskin_form() {
	    $location = $this->location;
		$filename = seturl('t=chskin');
	  
		$ret = "<FORM action='". $filename . "' method=post class=\"thin\">" . 
	          "<br>App name:<input type=\"text\" name=\"cpapp\" value=\"\" size=\"32\" maxlength=\"64\">" .
              "<br>App new skin:<input type=\"text\" name=\"cpskin\" value=\"$current_skin\" size=\"32\" maxlength=\"64\">";	  
		$ret .= "<br>Password:<input type=\"password\" name=\"upass\" value=\"\" size=\"32\" maxlength=\"64\">";	  
		$ret .= "<br>location:<input type=\"text\" name=\"cploc\" value=\"$location\" size=\"32\" maxlength=\"64\">";			
		$ret .= "<br>is local skin:<input type=\"checkbox\" name=\"localskin\">";	
		$ret .= "<br><input type=\"submit\" name=\"Submit\" value=\"Change skin\">" .
		        "<input type=\"hidden\" name=\"FormAction\" value=\"chskin\" />" .
                "</FORM>";		
			  
		return ($ret);	  
	}

	function chskin() {
	
		if (($this->posted_appname) && ($this->posted_password)) {
            //setup....	
			$ret = '<h1>Change Skin Application</h1>';
			$ret .= $this->chskin_scenario();
		}
		elseif (($this->posted_appname) && (GetParam('FormAction')=='chskinkeys')) {
            //print_r($_POST);
	        $ret .= $this->skin_change_data();
        }
        else {
		    //form
			$ret = $this->cmds(); 
			$ret .= '<h1>Change Skin Application</h1>';
			$ret .= $this->chskin_form();
        }

        return ($ret); 		
	}	
	
}
}	
?>	
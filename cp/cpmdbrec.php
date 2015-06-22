<?php
if ($cmd=$_GET['t']) {//cmd to execute...
  require_once('dpc/system/pcntl.lib.php'); 
  //echo $cmd;//'z';
  //die();
}  
else //common html ...
  require_once('dpc/system/pcntlhtml.lib.php'); 
//require_once('dpc/system/pcntlhtml.lib.php'); 

//$__EVENTS['FRONTHTMLPAGE_DPC'][2000]='cpmdbrec';
//$__EVENTS['FRONTHTMLPAGE_DPC'][2001]='chpass';
//$__EVENTS['SHLOGIN_DPC'][2002]='chpass';
//$__EVENTS['SHLOGIN_DPC'][2003]='rempwd';

//$__ACTIONS['FRONTHTMLPAGE_DPC'][2000]='cpmdbrec';
//$__ACTIONS['FRONTHTMLPAGE_DPC'][2001]='chpass';
//$__ACTIONS['SHLOGIN_DPC'][2002]='chpass';
//$__ACTIONS['SHLOGIN_DPC'][2003]='rempwd';

//$__LOCALE['FRONTHTMLPAGE_DPC'][1001]='_addpage;Add Page;Νέα σελίδα';
//$__LOCALE['FRONTHTMLPAGE_DPC'][1002]='_editpage;Edit Page;Επεξεργασία σελίδας';
//$__LOCALE['FRONTHTMLPAGE_DPC'][1003]='_previewpage;Preview;Προεπισκόπηση';
$__LOCALE['FRONTHTMLPAGE_DPC'][2001]='_ckfinder;Upload files;Upload αρχείων';
$__LOCALE['FRONTHTMLPAGE_DPC'][2002]='_webmail;Web Mail;Web Mail';
$__LOCALE['FRONTHTMLPAGE_DPC'][2003]='_editpage;Edit Page;Επεξεργασία σελίδας';
$__LOCALE['FRONTHTMLPAGE_DPC'][2004]='_rempass;Forgotten password;Υπενθύμιση κωδικού';
$__LOCALE['FRONTHTMLPAGE_DPC'][2005]='_chpass;Change password;Αλλαγή κωδικού';
$__LOCALE['FRONTHTMLPAGE_DPC'][2006]='_cphelp;Ηelp;Βοήθεια';
$__LOCALE['FRONTHTMLPAGE_DPC'][2007]='_cpupgrade;Upgrade;Αναβάθμιση';
$__LOCALE['FRONTHTMLPAGE_DPC'][2008]='_cpwizard;Enable wizard;Οδηγός εγκατάστασης';
$__LOCALE['FRONTHTMLPAGE_DPC'][2009]='_cpdhtmlon;Windows mode;Πλοήγηση Windows';
$__LOCALE['FRONTHTMLPAGE_DPC'][2010]='_cpdhtmloff;Frames mode;Πλοήγηση Frames';
$__LOCALE['FRONTHTMLPAGE_DPC'][2011]='_cpcropwiz;Crop wizard;Crop wizard';
$__LOCALE['FRONTHTMLPAGE_DPC'][2012]='_OPTIONS;Options;Επιλογές';
$__LOCALE['FRONTHTMLPAGE_DPC'][2013]='_ADD;Add;Προσθήκη';
$__LOCALE['FRONTHTMLPAGE_DPC'][2014]='_CATEGORY;Category;Κατηγορία';
$__LOCALE['FRONTHTMLPAGE_DPC'][2015]='_ITEM;Item;Είδος';
$__LOCALE['FRONTHTMLPAGE_DPC'][2016]='_SETTINGS;Settings;Ρυθμίσεις';
$__LOCALE['FRONTHTMLPAGE_DPC'][2017]='_customers;Customers;Πελάτες';
$__LOCALE['FRONTHTMLPAGE_DPC'][2018]='_EDITHTML;Edit Html;Σελίδες Html';
$__LOCALE['FRONTHTMLPAGE_DPC'][2019]='_SELECTHTML;Select Html;Επιλογή Html';
$__LOCALE['FRONTHTMLPAGE_DPC'][2020]='_ADDFAST;Add item (fast);Εισαγωγή είδους (fast)';
$__LOCALE['FRONTHTMLPAGE_DPC'][2021]='_addtag;Add Tag;Εισαγωγή Ετικέτας';

$page = &new pcntl('
super javascript;
super rcserver.rcssystem;
load_extension adodb refby _ADODB_; 
super database;
use xwindow.window,xwindow.window2,gui.swfcharts;
include networlds.clientdpc;
include mail.smtpmail;
private frontpage.fronthtmlpage /cgi-bin;
public phpdac.shlogin;
private shop.rcitems /cgi-bin;
private shop.rctags /cgi-bin;
',1);

 $lan = getlocal();
 $prpath = paramload('SHELL','prpath');
 $one_attachment = remote_paramload('SHKATALOG','oneattach',$prpath);
 $csep =  remote_paramload('RCITEMS','csep',$prpath);
 $cseparator = $csep ? $csep : '^';
 if ($one_attachment) 
   $slan = null;
 else
   $slan = $lan?$lan:'0';
   
 $template = remote_paramload('FRONTHTMLPAGE','template',$prpath);  
 //echo 'template:',$template;  
 $encoding = $_GET['encoding']?$_GET['encoding']:'utf-8'; 
 $location = '../' . urldecode(base64_decode($_GET['turl'])); //echo $location,'<br>';
 $savelocation = $location . 'editmode=1'; 
 $cp = 'cp.php';  
 $stats = 'cgi-bin/awstats.php';  
 $tools = array('Cube Mail'=>'rcubemail','Lime Survey'=>'limesurvey','XCalendar'=>'xcalendar');
 $useicons = 0;//1;	
 
  //page name..
 $pn = explode('/',$location);
 $pname = array_pop($pn);
 $pnurl = stristr($pname,'?') ? explode('?',$pname) : array('0'=>$pname);
 $pagename = $pnurl[0]; 
 //echo $pagename;
 
 //EXECUTE COMMANDS...
 if ($cmd) {
	switch ($cmd) {
	    case 'chpass' : if (GetReq('sectoken')) //has been send
                          $message = GetGlobal('controller')->calldpc_method('shlogin.html_reset_pass use 1');//change pass  		
		                else    
		                  $message = GetGlobal('controller')->calldpc_method('shlogin.html_remform');//send mail 
		                break;//$message = 'chpass'; break;
		case 'shremember':	GetGlobal('controller')->calldpc_method('shlogin.do_the_job');	
                        $message = localize('ok',getlocal());
                        break;  						
		case 'cphelp' : $message = 'cphelp'; break;
		case 'rempwd' : $message = 'rempwd'; break;
		case 'chpass' : $message = 'chpass'; break;
		
		case 'dologout'://when in cp and reenter pass..exit
		case 'cpexit' : /*$mylocation = str_replace('_&_', '_%26_',$location);
		                $message = "<SCRIPT language=\"JavaScript\">top.location.href=\"" .
		                           urlencode($mylocation) . 
		                           "\";</SCRIPT>";*/
						//$message = 'cpexit'; 		   
						$message = "<h2><a href='#' onclick=\"top.location.href='../'\">Press here to exit</a></h2>";
						break;
		default       : $message = $cmd .'>';//null;
	                    //fetch execution result
	                    //$exeret = $page->render(null,getlocal(),null,'cp_em.html'); 		
	} 	   
 }

 function parse_environment($save_session=false) {

    if ($ret = GetSessionParam('env')) {
	    //echo 'insession';
		//print_r($ret);
	    return ($ret);
	}    

	//$myenvfile = /*$this->prpath .*/ 'cp.ini';
	//$ini = @parse_ini_file($myenvfile ,false, INI_SCANNER_RAW);	
    $ini = @parse_ini_file("cp.ini");
	
	if (!$ini) die('Environment error!');

	$adminsecid = GetSessionParam('ADMINSecID') ? GetSessionParam('ADMINSecID') : $GLOBALS['ADMINSecID'];
	$seclevid = ($adminsecid>1) ? intval($adminsecid)-1 : 1;//...not instant sec level read//9; //test
	//echo GetSessionParam('ADMINSecID'),'>',$adminsecid,':', $seclevid;	
	//print_r($ini); 
	foreach ($ini as $env=>$val) {
	    if (stristr($val,',')) {
		    $uenv = explode(',',$val);
			$ret[$env] = $uenv[$seclevid];  
		}
		else
		    $ret[$env] = $val;
	}

	if (($save_session) && (!GetSessionParam('env'))) //rccontrolpanel also reads and save
		SetSessionParam('env', $ret); 		
	
	return ($ret);
 }
 
 function cpanel($lan=0,$rettokens=null,$editpage=null) {
   global $location, $savelocation, $cp, $stats, $tools, $encoding, $one_attach, $useicons, $message, $exeret;
   global $_logout_url, $_exit_url, $cmd, $template, $cseparator;
   $tokens = array();
   $winhide = 'HIDE';//'SHOW';
   
   //echo '>',getcwd();
   if (($user = $_POST['cpuser']) && ($pass = $_POST['cppass'])) {

     if (defined('RCCONTROLPANEL_DPC'))
	   $login = GetGlobal('controller')->calldpc_method("rccontrolpanel.verify_login");
	 elseif (defined('SHLOGIN_DPC'))
       $login = GetGlobal('controller')->calldpc_method("shlogin.do_login use ".$user.'+'.$pass.'+1');	
     else
       die('Login mechnanism not specified!');	 
   }
   
   if (GetSessionParam('LOGIN')!='yes') { //login
   
        $filename = 'cpmdbrec.php?turl='.urlencode($_GET['turl']).'&encoding='.$encoding;
	    //echo $filename;
	  			    	    
	    //cmd token
        $tokens[] = $exeret ? $exeret : ($message ? $message : null); 	  
        
		if (!$cmd)
			$tokens[] = <<<EOF
							<form name="signup" method="post" class="sign-up-form" action="$filename">
									<h3 class="grid_3 alpha omega"><strong>Username</strong></h3>
									<input class="grid_3 alpha omega" name="cpuser" type="text" id="cpuser" value="">
									</input>
									<h3 class="grid_3 alpha omega"><strong>Passsword</strong></h3>
									<input class="grid_3 alpha omega" name="cppass" type="password" id="cppass" value="">
									</input>
									<div class="msg grid_3 alpha omega "></div>      
									<div class="grid_3 alpha omega "> 
										<br/>
										<input type="submit" class="call-out grid_1 push_2 alpha omega" alt="Sign Up"
										title="Sign Up" name="Submit" value="Ok"></input>
									<div class="form-foot" style="clear:both;">
									</div>
								    </div>  
							</form>	
							<div class="clearfix"></div>							
EOF;

	  //$tokens[] = null;
	  //$tokens[] = '<hr/>';//reset 		  
      //$tokens[] =  "<a href=\"cpmdbrec.php?t=cphelp&editmode=1'\" target='mainFrame'>".localize('_cphelp',$lan)."</a>";
	  
	  if (!$cmd) 
		$tokens[] = "<a href=\"cpmdbrec.php?t=chpass&editmode=1\" target='mainFrame'>".localize('_chpass',$lan)."</a>";
		
	  /*$mylocation = str_replace('_&_', '_%26_',$location);	  
      $onclick = "top.location.href='" . $mylocation . "'";
	  $exithref = '#';//"cpmdbrec.php?t=cpexit";
	  $tokens[] = =  "<a href=\"$exithref\" onClick=\"$onclick\">" . localize('_exit',$lan) . "</a>";*/
   }
   else { //cp
   
	$environment = parse_environment();//true); //@parse_ini_file("cp.ini");
    //print_r($environment);      
    $seclevid = GetSessionParam('ADMINSecID');	
   
     if ($login==true) {
	   $tokens[] = "<SCRIPT language=\"JavaScript\">parent.mainFrame.location=\"cp.php?editmode=1&encoding=$encoding\";</SCRIPT>";
	   $tokens[] = $exeret ? $exeret : ($message ? $message : null); 
	 }
	 else {
	   $tokens[] = $exeret ? $exeret : ($message ? $message : null); 
	 }  
	   
	 /*$content = 'xxxx';  
	 $twin = new window2('test',$content,null,1,null,'HIDE',null,1);
	 $tokens[] = $twin->render("center::100%::0::group_article_selected::left::0::0::");	
	 unset ($twin);	*/	   
	   
	 $mylocation = str_replace('_&_', '_%26_',$location); //echo $mylocation;
 
     //$tokens[] = "<a href=\"javascript:top.location.href='". urlencode($mylocation) ."&editmode=-1'\">".localize('_exit',$lan)."</a>";
	 /*
	 $tokens[] = "<a href=\"javascript:top.location.href='". urlencode($mylocation) ."'\">".localize('_exit',$lan)."</a>";
	 $_icons[] = "<a href=\"javascript:top.location.href='". urlencode($mylocation) ."&editmode=-1'\">".loadicon('/icons/'.'_exit'.'.gif',localize('_mail',$lan),null)."</a>";
	 */ 
   
     if (GetSessionParam('LOGIN')!='yes') {
       $tokens[] = "<a href='$cp?editmode=1' target='mainFrame'>".localize('_login',$lan)."</a>";
	   $_icons[] = "<a href='$cp?editmode=1' target='mainFrame'>".loadicon('/icons/'.'_login'.'.gif',localize('_login',$lan),null)."</a>";
	 }  
     elseif ($environment['DASHBOARD']==1) {
       $otokens[] = "<a href='$cp?editmode=1' target='mainFrame'>".localize('_dashboard',$lan)."</a>"; 	
	   $_icons[] = "<a href='$cp?editmode=1' target='mainFrame'>".loadicon('/icons/'.'_dashboard'.'.gif',localize('_dashboard',$lan),null)."</a>";
	 }  
	 
	 if ($environment['AWSTATS']==1) {
	   $otokens[] = "<a href='$stats' target='mainFrame'>".localize('_webstatistics',$lan)."</a>";	
	   $_icons[] = "<a href='$stats' target='mainFrame'>".loadicon('/icons/'.'_webstatistics'.'.gif',localize('_webstatistics',$lan),null)."</a>";
	 }
	 
	 if ($environment['WEBMAIL']==1) {
       $otokens[] = "<a href='http://www.stereobit.gr/webmail/' target='mainFrame'>".localize('_webmail',$lan)."</a>";
	   $_icons[] = "<a href='http://www.stereobit.gr/webmail/' target='mainFrame'>".loadicon('/icons/'.'_mail'.'.gif',localize('_webmail',$lan),null)."</a>";		 
	 } 
	 
     //if (stristr($editpage,'index.php')) {
	     if ($environment['MENU']==1) {
           $otokens[] = "<a href='cpmenu.php?t=cpmconfig&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_menu',$lan)."</a>";
		   $_icons[] = "<a href='cpmenu.php?t=cpmconfig&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_menu'.'.gif',localize('_menu',$lan),null)."</a>";
		 }  
	     if ($environment['SLIDESHOW']==1) {
           $otokens[] = "<a href='cpslideshow.php?t=cpsconfig&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_slideshow',$lan)."</a>";
		   $_icons[] = "<a href='cpslideshow.php?t=cpsconfig&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_slideshow'.'.gif',localize('_slideshow',$lan),null)."</a>";
		 } 		 
     //}	 
	 
	 if ($environment['CONFIG']==1) {
		//echo $editpage,'>';	 
		//if (stristr($editpage,'index.php')) {
		$pparts = explode('.',$editpage);
		$config_section = strtoupper($pparts[0]);		 
		if ($config_section) {	 
	     //if ($environment['PAGECONFIG']==1) {		 
           $otokens[] = "<a href='cpconfig.php?editmode=1&cpart=$config_section&encoding=$encoding' target='mainFrame'>".localize('_config',$lan)."</a>"; 
	 	   $_icons[] = "<a href='cpconfig.php?editmode=1&cpart=$config_section&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_config'.'.gif',localize('_config',$lan),null)."</a>";	
		 //}  
		}
     }//config...	
	  
	 
		if (stristr($editpage,'contact.php')) {
	     if ($environment['CONTACT_FORM']==1) {
           $otokens[] = "<a href='cpform.php?t=cpform&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_contactform',$lan)."</a>";
		   $_icons[] = "<a href='cpform.php?t=cpform&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_contactform'.'.gif',localize('_contactform',$lan),null)."</a>";
		 }  
		}	
		if (stristr($editpage,'subscribe.php')) {
	     if ($environment['SUBSCRIBERS']==1) {
           $otokens[] = "<a href='cpsubscribers.php?editmode=1&encoding=$encoding' target='mainFrame'>".localize('_subscribers',$lan)."</a>";
		   $_icons[] = "<a href='cpsubscribers.php?editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_subscribers'.'.gif',localize('_subscribers',$lan),null)."</a>";
		 } 
		}	
		if (stristr($editpage,'sitemap.php')) {
	     if ($environment['SITEMAP']==1) {
		 	$pparts = explode('.',$editpage);
		    $config_section = strtoupper($pparts[0]);		 
			if ($config_section) {	 
				$otokens[] = "<a href='cpconfig.php?editmode=1&cpart=$config_section&encoding=$encoding' target='mainFrame'>".localize('_sitemap',$lan)."</a>";
				$_icons[] = "<a href='cpconfig.php?editmode=1&cpart=$config_section&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_sitemap'.'.gif',localize('_sitemap',$lan),null)."</a>";
			}	
		 }   
		}	
		if (stristr($editpage,'search.php')) {
	     if ($environment['SEARCH']==1) {
           $otokens[] = "<a href='cpitems.php?t=cpattach2db&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_search',$lan)."</a>";
		   $_icons[] = "<a href='cpitems.php?t=cpattach2db&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_search'.'.gif',localize('_search',$lan),null)."</a>";
		 }  
		}
	 //}//config
	 if ($environment['EDIT_CATEGORY']==1) { 
	       $cat = null;
           $otokens[] = "<a href='cpkategories.php?t=cpkategories&cat=$cat&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_editcat',$lan)."</a>";
		   $_icons[] = "<a href='cpkategories.php?t=cpkategories&cat=$cat&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_editcat'.'.gif',localize('_editcat',$lan),null)."</a>";		   
	 } 	 
     if ($environment['EDIT_ITEM']==1) {
           $v = null;
		   $otokens[] = "<a href='cpitems.php?t=cpitems&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_edititem',$lan)."</a>";	
		   $_icons[] = "<a href='cpitems.php?t=cpitems&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_edititem'.'.gif',localize('_edititem',$lan),null)."</a>";
	 } 	 
	 if ($environment['USERS']==1) {
           $otokens[] = "<a href='cpusers.php?t=cpusers&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_users',$lan)."</a>"; 
		   $_icons[] = "<a href='cpusers.php?t=cpusers&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_users'.'.gif',localize('_users',$lan),null)."</a>";
	 }  
	 if ($environment['CUSTOMERS']==1) {
           $otokens[] = "<a href='cpcustomers.php?t=cpcustomers&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_customers',$lan)."</a>"; 
		   $_icons[] = "<a href='cpcustomers.php?t=cpcustomers&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_customers'.'.gif',localize('_customers',$lan),null)."</a>";
	 } 
	 if ($environment['TRANSACTIONS']==1) {
		   //old way
           //$otokens[] = "<a href='cptransactions.php?t=cptransview&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_transactions',$lan)."</a>";
		   //$_icons[] = "<a href='cptransactions.php?t=cptransview&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_transactions'.'.gif',localize('_transactions',$lan),null)."</a>";
		   //new way
		   $otokens[] = "<a href='cptransactions.php?t=cptransactions&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_transactions',$lan)."</a>";
		   $_icons[] = "<a href='cptransactions.php?t=cptransactions&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_transactions'.'.gif',localize('_transactions',$lan),null)."</a>";		   
	 }	 
 	 
	 
	 if ($environment['ITEM_SENDMAIL']==1) {
		 $otokens[] = "<a href='cpsubscribers.php?encoding=$encoding&editmode=1' target='mainFrame'>".localize('_senditemmail',$lan)."</a>";	 		 
		 $_icons[] = "<a href='cpsubscribers.php?encoding=$encoding&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_senditemmail'.'.gif',localize('_senditemmail',$lan),null)."</a>";
	 }
	 if ($environment['RESOURCES_UPLOAD']==1) {
		 $otokens[] = "<a href='cpupload.php?editmode=1&encoding=$encoding' target='mainFrame'>".localize('_upload',$lan)."</a>";	 		 
		 $_icons[] = "<a href='cpupload.php?editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_upload'.'.gif',localize('_upload',$lan),null)."</a>";
	 } 	 
	 if ($environment['CKFINDER']==1) {
		 $otokens[] = "<a href='cpmckfinder.php' target='mainFrame'>".localize('_ckfinder',$lan)."</a>";	 		 
		 $_icons[] = "<a href='cpmckfinder.php' target='mainFrame'>".loadicon('/icons/'.'_upload'.'.gif',localize('_ckfinder',$lan),null)."</a>";
	 } 	
	 if ($environment['EDITHTML']==1) {
	    //$turl = array_shift(explode('?',urldecode(base64_decode($_GET['turl']))));//$_GET['turl'];
		$turl_file = str_replace('.php','.html',array_shift(explode('?',urldecode(base64_decode($_GET['turl'])))));
		//echo $turl_file,'..';
		$htmlfile = $_GET['htmlfile'] ? $_GET['htmlfile'] : urlencode(base64_encode($turl_file));
		if ($htmlfile) {
	       $otokens[] = "<a href='cpmhtmleditor.php?cke4=1&encoding=$encoding&editmode=1&htmlfile=$htmlfile' target='mainFrame'>".localize('_editpage',$lan)."</a>";
           $_icons[] = "<a href='cpmhtmleditor.php?cke4=1&encoding=$encoding&editmode=1&htmlfile=$htmlfile' target='mainFrame'>".loadicon('/icons/'.'_editpage'.'.gif',localize('_editpage',$lan),null)."</a>";		
	    }	
	 }  	 

     //win category.......................................................
	 if (!empty($otokens)) {
		$opt_content = implode('<hr/>',$otokens);
		$twinopt = new window2(localize('_OPTIONS',$lan),$opt_content,null,1,null,$winhide,null,1);
		$tokens[] = $twinopt->render("center::100%::0::group_article_selected::left::0::0::");	
		unset ($twinopt);		 
	 
		//$tokens[] = '<hr>';
	 }
	 
	 //........................................................... 
	/* if ($environment['EDITHTML']==1) {
	 
	    //$turl = array_shift(explode('?',urldecode(base64_decode($_GET['turl']))));//$_GET['turl'];
		$htmlfile = $_GET['htmlfile'];//str_replace('.php','.html',urlencode(base64_encode($turl)));
	    $tokens[] = "<a href='cpmhtmleditor.php?cke4=1&encoding=$encoding&editmode=1&htmlfile=$htmlfile' target='mainFrame'>".localize('_editpage',$lan)."</a>";
	 
		$turl = array_shift(explode('?',urldecode(base64_decode($_GET['turl']))));//$_GET['turl'];..exclude ?..editmode=-1
		$tokens[] = "<a href='cpmctrl.php?t=cptnewpage&editmode=1&turl=$turl' target='mainFrame'>".localize('_addpage',$lan)."</a>";	
		//$_icons[] = "<a href='cpmctrl.php?t=cptnewpage&editmode=1&turl=$turl' target='mainFrame'>".loadicon('/icons/'.'_addpage'.'.gif',localize('_addpage',$lan),null)."</a>";
        $tokens[] = "<a href='cpmctrl.php?t=cptnewcopy&editmode=1&turl=$turl' target='mainFrame'>".localize('_copypage',$lan)."</a>";			
	 
		//$tokens[] = dhtml_link('addpage', 'ajax', localize('_addpage',$lan), "cpmctrl.php?t=cptnewpage&editmode=1&turl=$turl"); 
	 
		//$tokens[] = "<a href='cpmhtmleditor.php?t=cptnewpage&editmode=1&turl=$turl' target='mainFrame'>".localize('_editpage',$lan)."</a>";	
		//$tokens[] = "<a href=\"skype:balexiou?chat\">Start chat</a>";
	 
		$htmlfile = GetReq('htmlfile');
		//$phpfile = GetReq('phpfile');//?GetReq('phpfile'):($mylocation?$mylocation:str_replace($lan.'.php','.php',$_GET['turl']));
		$preview = GetReq('turl');//urlencode(base64_encode($location));  	 
		$tokens[] = "<a href='cpmhtmleditor.php?htmlfile=$htmlfile&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_editpage',$lan)."</a>";
		$tokens[] = "<a href='cpmhtmleditor.php?htmlfile=$htmlfile&editmode=1&encoding=$encoding&preview=$preview' target='mainFrame'>".localize('_previewpage',$lan)."</a>";		
		
 	 }*/
	 //............................................................

	 $new_elements = false;
	 
	 $qquery = str_replace('_&_', '_%26_', base64_decode($_GET['turl'])); //echo '>',$qquery,'>'; //& category problem
     $urlquery = parse_url($qquery); /*parse_url(base64_decode($_GET['turl']));*/ //echo $urlquery['query'];
     parse_str($urlquery['query'],$getp); //echo implode('.',$getp);  
	 
     foreach ($getp as $p=>$v) {
	   	 
       if (stristr($p,'cat')) {
	   
         $cat = urlencode($v);
		 
	     /*if ($environment['ADD_CATEGORY']==1) {
           $ntokens[] = "<a href='cpkategories.php?t=cpaddcat&cat=$cat&editmode=1' target='mainFrame'>".localize('_addcat',$lan)."</a>";	
	       $_icons[] = "<a href='cpkategories.php?t=cpaddcat&cat=$cat&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_addcat'.'.gif',localize('_addcat',$lan),null)."</a>";
	     } */ 
	     if ($environment['ADD_ITEM']==1) {  
           //$ntokens[] = "<a href='cpitems.php?t=cpvinput&cat=$cat&editmode=1' target='mainFrame'>".localize('_additem',$lan)."</a>";	
	       //$_icons[] = "<a href='cpitems.php?t=cpvinput&cat=$cat&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_additem'.'.gif',localize('_additem',$lan),null)."</a>";
           $ntokens[] = "<a href='cpmhtmleditor.php?htmlfile=" . "&encoding=$encoding&id=$cat&type=.html&editmode=1&insfast=1' target='mainFrame'>".localize('_ADDFAST',$lan)."</a>";	
	       $_icons[] = "<a href='cpmhtmleditor.php?htmlfile=" . "&encoding=$encoding&id=$cat&type=.html&editmode=1&insfast=1' target='mainFrame'>".loadicon('/icons/'.'_ADDFAST'.'.gif',localize('_ADDFAST',$lan),null)."</a>";		   
	     }   		 
         //$tokens[] = '<hr>'; 
         $new_elements = true; //if cat exist	

	     if ($environment['EDIT_CTAG']==1) { //add cat tags
           $ctokens[] = "<a href='cptags.php?t=cpeditctag&cat=$cat&editmode=1' target='mainFrame'>".localize('_editctag',$lan)."</a>";	
	       $_icons[] = "<a href='cptags.php?t=cpeditctag&cat=$cat&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_editctag'.'.gif',localize('_editctag',$lan),null)."</a>";
	     } 		 
		 if ($environment['EDIT_CATEGORY']==1) { 
           //$ctokens[] = "<a href='cpkategories.php?t=cpeditcat&cat=$cat&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_editcat',$lan)."</a>";
		   //$_icons[] = "<a href='cpkategories.php?t=cpeditcat&cat=$cat&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_editcat'.'.gif',localize('_editcat',$lan),null)."</a>";
           $ctokens[] = "<a href='cpkategories.php?t=cpkategories&cat=$cat&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_editcat',$lan)."</a>";
		   $_icons[] = "<a href='cpkategories.php?t=cpkategories&cat=$cat&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_editcat'.'.gif',localize('_editcat',$lan),null)."</a>";		   
		 }  
		 if ($environment['CATEGORY_UPLOAD']==1) {
		   $ctokens[] = "<a href='cpupload.php?cat=$cat&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_uploadcat',$lan)."</a>";	 		 
		   $_icons[] = "<a href='cpupload.php?cat=$cat&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_uploadcat'.'.gif',localize('_uploadcat',$lan),null)."</a>";
		 } 
	     if ($environment['SYNCPHOTO']==1) {	 
           $ctokens[] = "<a href='cpitems.php?t=cpvrestorephoto&cat=$cat&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_syncphoto',$lan)."</a>"; 
	 	   $_icons[] = "<a href='cpitems.php?t=cpvrestorephoto&cat=$cat&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_syncphoto'.'.gif',localize('_syncphoto',$lan),null)."</a>";
	     }	
	     if ($environment['DBPHOTO']==1) {	 
           $ctokens[] = "<a href='cpitems.php?t=cpvdbphoto&cat=$cat&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_dbphoto',$lan)."</a>"; 
	 	   $_icons[] = "<a href='cpitems.php?t=cpvdbphoto&cat=$cat&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_dbphoto'.'.gif',localize('_syncphoto',$lan),null)."</a>";
	     }		 
		 /*if ($environment['RSS']==1) {//rss for category  
           $ctokens[] = "<a href='cpxmlexp.php?editmode=1&cat=$cat&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_rssfeeds',$lan)."</a>";	
		   $_icons[] = "<a href='cpxmlexp.php?editmode=1&cat=$cat&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_rssfeeds'.'.gif',localize('_rssfeeds',$lan),null)."</a>";
		 } */ 
		 if ($environment['RSS']==1) {//rss for category  
           $ctokens[] = "<a href='cpitems.php?t=cpvitemrss&cat=$cat&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_rssfeeds',$lan)."</a>";	
		   $_icons[] = "<a href='cpitems.php?t=cpvitemrss&cat=$cat&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_rssfeeds'.'.gif',localize('_rssfeeds',$lan),null)."</a>";
		 }  	
		 if ($environment['ITEM_SENDMAIL']==1) {//category send mail..advanced mail template system (rctedit,rctedititems)
		   $ctokens[] = "<a href='cpsubscribers.php?htmlfile=&encoding=$encoding&cat=$cat&editmode=1' target='mainFrame'>".localize('_senditemmail',$lan)."</a>";	 		 
		   $_icons[] = "<a href='cpsubscribers.php?htmlfile=&encoding=$encoding&cat=$cat&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_senditemmail'.'.gif',localize('_senditemmail',$lan),null)."</a>";
	     } 		 
	 
	     $mycurrentcat = explode($cseparator,$v);
	     $vn = array_pop($mycurrentcat);
	     $cat_htm_attachment = "html/". $vn . $lan . '.htm';		
	     $cat_html_attachment = "html/". $vn . $lan . '.html'; 
	   	 
		 if ($environment['CATEGORY_ATTACHMENT']==1) {
	       if (is_readable($cat_htm_attachment)) {
             $ctokens[] = "<a href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($cat_htm_attachment)) . "&encoding=$encoding&id=$vn&type=.htm&editmode=1' target='mainFrame'>".localize('_editcathtml',$lan)."</a>";	  		   
			 $_icons[] = "<a href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($cat_htm_attachment)) . "&encoding=$encoding&id=$vn&type=.htm&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_editcathtml'.'.gif',localize('_editcathtml',$lan),null)."</a>";
		   }	 
           elseif (is_readable($cat_html_attachment)) {
	         $ctokens[] = "<a href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($cat_html_attachment)) . "&encoding=$encoding&id=$vn&type=.html&editmode=1' target='mainFrame'>".localize('_editcathtml',$lan)."</a>";	  		   
			 $_icons[] = "<a href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($cat_html_attachment)) . "&encoding=$encoding&id=$vn&type=.html&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_editcathtml'.'.gif',localize('_editcathtml',$lan),null)."</a>";
		   }	 
	       else {//create nerw file	 
	         $new_attachment = "html/". $vn . $lan . '.html';
             $ctokens[] = "<a href='cpmhtmleditor.php?htmlfile=&encoding=$encoding&id=$vn&type=.html&editmode=1' target='mainFrame'>".localize('_addcathtml',$lan)."</a>";			  
			 $_icons[] = "<a href='cpmhtmleditor.php?htmlfile=&encoding=$encoding&id=$vn&type=.html&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_addcathtml'.'.gif',localize('_addcathtml',$lan),null)."</a>";
	       }	  
		 }//environment
		 
		 //win category.......................................................
		 if (!empty($ntokens)) {
			$new_content = implode('<hr/>',$ntokens);
			//$title = 'Add';//str_replace($cseparator,'<br>',str_replace('_',' ',urldecode($cat)));
			$twincat = new window2(localize('_ADD',$lan),$new_content,null,1,null,$winhide,null,1);
			$tokens[] = $twincat->render("center::100%::0::group_article_selected::left::0::0::");	
			unset ($twincat);	
		 }		 
		 if (!empty($ctokens)) {
			$cat_content = implode('<hr/>',$ctokens);
			$category = str_replace($cseparator,'<br>',str_replace('_',' ',urldecode($cat)));
			$twincat = new window2(localize('_CATEGORY',$lan).':'.$category,$cat_content,null,1,null,$winhide,null,1);
			$tokens[] = $twincat->render("center::100%::0::group_article_selected::left::0::0::");	
			unset ($twincat);	
		 }
       }//if	   
	   	 
       if (stristr($p,'id')) {
	     if ($environment['EDIT_ITAG']==1) { //add id tags
           $itokens[] = "<a href='cptags.php?t=cpedititag&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_edititag',$lan)."</a>";	
	       $_icons[] = "<a href='cptags.php?t=cpedititag&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_edititag'.'.gif',localize('_edititag',$lan),null)."</a>";
	     } 		   
         if ($environment['EDIT_ITEM']==1) {
           //$itokens[] = "<a href='cpitems.php?t=cpvmodify&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_edititem',$lan)."</a>";	
		   //$_icons[] = "<a href='cpitems.php?t=cpvmodify&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_edititem'.'.gif',localize('_edititem',$lan),null)."</a>";
		   $itokens[] = "<a href='cpitems.php?t=cpitems&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_edititem',$lan)."</a>";	
		   $_icons[] = "<a href='cpitems.php?t=cpitems&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_edititem'.'.gif',localize('_edititem',$lan),null)."</a>";
		 }  
		 if ($environment['EDIT_ITEM_PHOTO']==1)  {
	       $itokens[] = "<a href='cpitems.php?t=cpvphoto&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_edititemphoto',$lan)."</a>";
		   $_icons[] = "<a href='cpitems.php?t=cpvphoto&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_edititemphoto'.'.gif',localize('_edititemphoto',$lan),null)."</a>";
		 }  
	     if ($environment['SYNCPHOTO']==1) {	 
           $itokens[] = "<a href='cpitems.php?t=cpvrestorephoto&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_syncphoto',$lan)."</a>"; 
	 	   $_icons[] = "<a href='cpitems.php?t=cpvrestorephoto&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_syncphoto'.'.gif',localize('_syncphoto',$lan),null)."</a>";
	     }	
	     if ($environment['DBPHOTO']==1) {	 
           $itokens[] = "<a href='cpitems.php?t=cpvdbphoto&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_dbphoto',$lan)."</a>"; 
	 	   $_icons[] = "<a href='cpitems.php?t=cpvdbphoto&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_dbphoto'.'.gif',localize('_syncphoto',$lan),null)."</a>";
	     }
		 if ($environment['RSS']==1) {//rss for item  
           $itokens[] = "<a href='cpitems.php?t=cpvitemrss&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_rssfeeds',$lan)."</a>";	
		   $_icons[] = "<a href='cpitems.php?t=cpvitemrss&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_rssfeeds'.'.gif',localize('_rssfeeds',$lan),null)."</a>";
		 }  		 
		 if ($environment['ITEM_UPLOAD']==1) {
		   $itokens[] = "<a href='cpupload.php?id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_uploadid',$lan)."</a>";	 		 
		   $_icons[] = "<a href='cpupload.php?id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_uploadid'.'.gif',localize('_uploadid',$lan),null)."</a>";
		 } 				 

		 if ($environment['ITEM_ATTACHMENT']==1) {
	       $text_attachment = "html/". $v . $lan . '.txt';
	       $htm_attachment = "html/". $v . $lan . '.htm';		
	       $html_attachment = "html/". $v . $lan . '.html'; 
	       //echo $html_attachment,'>';	     
	   
           if ($attachment_type = GetGlobal('controller')->calldpc_method("rcitems.has_attachment2db use $v")) {
	         //echo '>',$attachment-type;
	         switch ($attachment_type) {
		       case '.html' :$itokens[] = "<a href='cpmhtmleditor.php?htmlfile=" . "&encoding=$encoding&id=$v&type=$attachment_type&editmode=1' target='mainFrame'>".localize('_edititemdbhtml',$lan)."</a>"; 
			                 $_icons[] = "<a href='cpmhtmleditor.php?htmlfile=" . "&encoding=$encoding&id=$v&type=$attachment_type&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_edititemdbhtml'.'.gif',localize('_edititemdbhtml',$lan),null)."</a>";
		                     break;
						 
		       case '.htm'  :$itokens[] = "<a href='cpmhtmleditor.php?htmlfile=" . "&encoding=$encoding&id=$v&type=$attachment_type&editmode=1' target='mainFrame'>".localize('_edititemdbhtm',$lan)."</a>";
			                 $_icons[] = "<a href='cpmhtmleditor.php?htmlfile=" . "&encoding=$encoding&id=$v&type=$attachment_type&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_edititemdbhtm'.'.gif',localize('_edititemdbhtm',$lan),null)."</a>";
		                     break;
						 
		       case '.txt ' :$itokens[] = "<a href='cpmhtmleditor.php?htmlfile=" . "&encoding=$encoding&id=$v&type=$attachment_type&editmode=1' target='mainFrame'>".localize('_edititemdbtext',$lan)."</a>";	 
			                 $_icons[] = "<a href='cpmhtmleditor.php?htmlfile=" . "&encoding=$encoding&id=$v&type=$attachment_type&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_edititemdbtext'.'.gif',localize('_edititemdbtext',$lan),null)."</a>";
		                     break;			 
		       default      :$itokens[] = 'Unknown attachment type!<br>'; 					 						 						 
						     //$_icons[] = "";
		     }
			 
			 	 
			 if ($environment['ITEM_SENDMAIL']==1) {
		       $itokens[] = "<a href='cpsubscribers.php?htmlfile=&encoding=$encoding&id=$v&type=$attachment_type&editmode=1' target='mainFrame'>".localize('_senditemmail',$lan)."</a>";	 		 
			   $_icons[] = "<a href='cpsubscribers.php?htmlfile=&encoding=$encoding&id=$v&type=$attachment_type&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_senditemmail'.'.gif',localize('_senditemmail',$lan),null)."</a>";
			 }  
			 if ($environment['ITEM_DELETE_DB_ATTACHMENT']==1)  {
	           $itokens[] = "<a href='cpitems.php?t=cpvdelattach&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_deleteitemattachment',$lan)."</a>";		  
			   $_icons[] = "<a href='cpitems.php?t=cpvdelattach&id=$v&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_deleteitemattachment'.'.gif',localize('_deleteitemattachment',$lan),null)."</a>";
			 }  
	       }
	       //else {	    
	       elseif (is_readable($text_attachment)) {
	           $itokens[] = "<a href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($text_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.txt' target='mainFrame'>".localize('_edititemtext',$lan)."</a>";	
			   $_icons[] = "<a href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($text_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.txt' target='mainFrame'>".loadicon('/icons/'.'_edititemtext'.'.gif',localize('_edititemtext',$lan),null)."</a>";
		       if ($environment['ITEM_SENDMAIL']==1) {			 
					$itokens[] = "<a href='cpsubscribers.php?htmlfile=" . urlencode(base64_encode($text_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.txt' target='mainFrame'>".localize('_senditemmail',$lan)."</a>";	
					$_icons[] = "<a href='cpsubscribers.php?htmlfile=" . urlencode(base64_encode($text_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.txt' target='mainFrame'>".loadicon('/icons/'.'_senditemmail'.'.gif',localize('_senditemmail',$lan),null)."</a>";	
			   }
		   }	 
	       elseif (is_readable($htm_attachment)) {
	           $itokens[] = "<a href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($htm_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.htm' target='mainFrame'>".localize('_edititemhtm',$lan)."</a>";
			   $_icons[] = "<a href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($htm_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.htm' target='mainFrame'>".loadicon('/icons/'.'_edititemhtm'.'.gif',localize('_edititemhtm',$lan),null)."</a>";
		       if ($environment['ITEM_SENDMAIL']==1) {			  
					$itokens[] = "<a href='cpsubscribers.php?htmlfile=" . urlencode(base64_encode($htm_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.htm' target='mainFrame'>".localize('_senditemmail',$lan)."</a>";
					$_icons[] = "<a href='cpsubscribers.php?htmlfile=" . urlencode(base64_encode($htm_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.htm' target='mainFrame'>".loadicon('/icons/'.'_senditemmail'.'.gif',localize('_senditemmail',$lan),null)."</a>";	
			   }		
		   }	 
           elseif (is_readable($html_attachment)) {
	           $itokens[] = "<a href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($html_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.html' target='mainFrame'>".localize('_edititemhtml',$lan)."</a>";	
			   $_icons[] = "<a href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($html_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.html' target='mainFrame'>".loadicon('/icons/'.'_edititemhtml'.'.gif',localize('_edititemhtml',$lan),null)."</a>";
		       if ($environment['ITEM_SENDMAIL']==1) {	  		   
					$itokens[] = "<a href='cpsubscribers.php?htmlfile=" . urlencode(base64_encode($html_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.html' target='mainFrame'>".localize('_senditemmail',$lan)."</a>";	
					$_icons[] = "<a href='cpsubscribers.php?htmlfile=" . urlencode(base64_encode($html_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.html' target='mainFrame'>".loadicon('/icons/'.'_senditemmail'.'.gif',localize('_senditemmail',$lan),null)."</a>";
			   }		
		   }
	       else {//create nerw file	 
	           $new_attachment = "html/". $v . $lan . '.html';
               $itokens[] = "<a href='cpmhtmleditor.php?htmlfile=&encoding=$encoding&id=$v&editmode=1&type=.html' target='mainFrame'>".localize('_additemhtml',$lan)."</a>";			  
			   $_icons[] = "<a href='cpmhtmleditor.php?htmlfile=&encoding=$encoding&id=$v&editmode=1&type=.html' target='mainFrame'>".loadicon('/icons/'.'_additemhtml'.'.gif',localize('_additemhtml',$lan),null)."</a>";
			   if ($environment['ITEM_SENDMAIL']==1) {//mail ..
		           $itokens[] = "<a href='cpsubscribers.php?htmlfile=&encoding=$encoding&id=$v&type=.html&editmode=1' target='mainFrame'>".localize('_senditemmail',$lan)."</a>";	 		 
			       $_icons[] = "<a href='cpsubscribers.php?htmlfile=&encoding=$encoding&id=$v&type=.html&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_senditemmail'.'.gif',localize('_senditemmail',$lan),null)."</a>";			   
			   }				   
	       }
	       //}//else
         }//environment
		 elseif ($environment['ITEM_SENDMAIL']==1) {//only mail ...
		    $itokens[] = "<a href='cpsubscribers.php?htmlfile=&encoding=$encoding&id=$v&type=.html&editmode=1' target='mainFrame'>".localize('_senditemmail',$lan)."</a>";	 		 
			$_icons[] = "<a href='cpsubscribers.php?htmlfile=&encoding=$encoding&id=$v&type=.html&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_senditemmail'.'.gif',localize('_senditemmail',$lan),null)."</a>";			   
		 } 	
		 //win category.......................................................
		 if (!empty($itokens)) {
			$item_content = implode('<hr/>',$itokens);
			$twinitem = new window2(localize('_ITEM',$lan).':'.urldecode($v),$item_content,null,1,null,$winhide,null,1);
			$tokens[] = $twinitem->render("center::100%::0::group_article_selected::left::0::0::");	
			unset ($twinitem);			 
		 }	
       }//if	
	      
       /*if ($v=='viewcart') {	
	     if ($environment['TRANSACTIONS']==1) {
		   //old way
           //$etokens[] = "<a href='cptransactions.php?t=cptransview&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_transactions',$lan)."</a>";
		   //$_icons[] = "<a href='cptransactions.php?t=cptransview&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_transactions'.'.gif',localize('_transactions',$lan),null)."</a>";
		   //new way
		   $etokens[] = "<a href='cptransactions.php?t=cptransactions&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_transactions',$lan)."</a>";
		   $_icons[] = "<a href='cptransactions.php?t=cptransactions&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_transactions'.'.gif',localize('_transactions',$lan),null)."</a>";		   
		 }  
	   }	 
		 
       if (($v=='signup') || ($v=='shlogin')) {	
	     if ($environment['USERS']==1) {
           $etokens[] = "<a href='cpusers.php?t=cpusers&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_users',$lan)."</a>"; 
		   $_icons[] = "<a href='cpusers.php?t=cpusers&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_users'.'.gif',localize('_users',$lan),null)."</a>";
		 }  
	     if ($environment['CUSTOMERS']==1) {
           $etokens[] = "<a href='cpcustomers.php?t=cpcustomers&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_customers',$lan)."</a>"; 
		   $_icons[] = "<a href='cpcustomers.php?t=cpcustomers&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_customers'.'.gif',localize('_customers',$lan),null)."</a>";
		 } 		 
		 if ($environment['SMS']==1) {
		   $etokens[] = "<a href='cpsmsgui.php?t=cpsmsgui&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_sendsms',$lan)."</a>";   
		   $_icons[] = "<a href='cpsmsgui.php?t=cpsmsgui&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_sendsms'.'.gif',localize('_sendsms',$lan),null)."</a>";
		 }  
	   }
	   //extra category.......................................................
	   if (!empty($etokens)) {
	   
		$eitem_content = implode('<hr/>',$etokens);
		$ewinitem = new window2('Extras',$eitem_content,null,1,null,$winhide,null,1);
		$tokens[] = $ewinitem->render("center::100%::0::group_article_selected::left::0::0::");	
		unset ($ewinitem);			 
	   }*/	 //moved to standart options  
     }
 
     //....
	 if ($new_elements===false) { //cat not exist
       if ($environment['ADD_CATEGORY']==1) {
         $ntokens[] = "<a href='cpkategories.php?t=cpaddcat&editmode=1' target='mainFrame'>".localize('_addcat',$lan)."</a>";	
	     $_icons[] = "<a href='cpkategories.php?t=cpaddcat&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_addcat'.'.gif',localize('_addcat',$lan),null)."</a>";
	   }  
	   if ($environment['ADD_ITEM']==1) {  
         $ntokens[] = "<a href='cpitems.php?t=cpvinput&editmode=1' target='mainFrame'>".localize('_additem',$lan)."</a>";	
	     $_icons[] = "<a href='cpitems.php?t=cpvinput&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_additem'.'.gif',localize('_additem',$lan),null)."</a>";
	   }  
	   if (($environment['EDIT_CTAG']==1) || ($environment['EDIT_ITAG']==1)) {  //add tags no cat/id list
         $ntokens[] = "<a href='cptags.php?t=cpeditctag&editmode=1' target='mainFrame'>".localize('_addtag',$lan)."</a>";	
	     $_icons[] = "<a href='cptags.php?t=cpeditctag&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_addtag'.'.gif',localize('_addtag',$lan),null)."</a>";
	   } 	   
       //$tokens[] = '<hr>'; 
	   if (!empty($ntokens)) {
			$new_content = implode('<hr/>',$ntokens);
			//$title = 'Add';//str_replace($cseparator,'<br>',str_replace('_',' ',urldecode($cat)));
			$twincat = new window2(localize('_ADD',$lan),$new_content,null,1,null,$winhide,null,1);
			$tokens[] = $twincat->render("center::100%::0::group_article_selected::left::0::0::");	
			unset ($twincat);	
	   }	   
     }
     //else
       //$tokens[] = '<hr>';


     //if ((stristr($_SERVER['HTTP_REFERER'],'index.php')) || (stristr($_SERVER['HTTP_REFERER'],'katalog.php'))) {	 
	   if ($environment['ATTACH_FILES2DB']==1) {
         $stokens[] = "<a href='cpitems.php?t=cpattach2db&editmode=1' target='mainFrame'>".localize('_itemattachments2db',$lan)."</a>";
		 $_icons[] = "<a href='cpitems.php?t=cpattach2db&editmode=1' target='mainFrame'>".loadicon('/icons/'.'_itemattachments2db'.'.gif',localize('_itemattachments2db',$lan),null)."</a>";
	   }	 
	   /*if ($environment['RSS']==1) {	 
         $stokens[] = "<a href='cpxmlexp.php?editmode=1&cat=$v' target='mainFrame'>".localize('_rssfeeds',$lan)."</a>";
		 $_icons[] = "<a href='cpxmlexp.php?editmode=1&cat=$v' target='mainFrame'>".loadicon('/icons/'.'_rssfeeds'.'.gif',localize('_rssfeeds',$lan),null)."</a>";
	   }*/	
	   if ($environment['RSS']==1) {	 
         $stokens[] = "<a href='cpitems.php?t=cpvitemrss&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_rssfeeds',$lan)."</a>";
		 $_icons[] = "<a href='cpitems.php?t=cpvitemrss&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_rssfeeds'.'.gif',localize('_rssfeeds',$lan),null)."</a>";
	   }	   
	   if ($environment['IMPORTDB']==1) {
         $stokens[] = "<a href='cpimportdb.php?editmode=1&encoding=$encoding' target='mainFrame'>".localize('_importdb',$lan)."</a>";
	 	 $_icons[] = "<a href='cpimportdb.php?editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_importdb'.'.gif',localize('_importdb',$lan),null)."</a>";		 
	   }	
	   if ($environment['SYNCPHOTO']==1) {	 
         $stokens[] = "<a href='cpitems.php?t=cpvrestorephoto&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_syncphoto',$lan)."</a>"; 
	 	 $_icons[] = "<a href='cpitems.php?t=cpvrestorephoto&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_syncphoto'.'.gif',localize('_syncphoto',$lan),null)."</a>";
	   }	
	   if ($environment['DBPHOTO']==1) {	 
         $stokens[] = "<a href='cpitems.php?t=cpvdbphoto&editmode=1&encoding=$encoding' target='mainFrame'>".localize('_dbphoto',$lan)."</a>"; 
	 	 $_icons[] = "<a href='cpitems.php?t=cpvdbphoto&editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_dbphoto'.'.gif',localize('_syncphoto',$lan),null)."</a>";
	   }	   
	   if ($environment['SYNCSQL']==1) {	 
         $stokens[] = "<a href='cpsyncsql.php?editmode=1&encoding=$encoding' target='mainFrame'>".localize('_syncsql',$lan)."</a>"; 
	 	 $_icons[] = "<a href='cpsyncsql.php?editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_syncsql'.'.gif',localize('_syncsql',$lan),null)."</a>";
	   }		   
	   if ($environment['CONFIG']==1) {	 
         $stokens[] = "<a href='cpconfig.php?editmode=1&encoding=$encoding' target='mainFrame'>".localize('_config',$lan)."</a>"; 
	 	 $_icons[] = "<a href='cpconfig.php?editmode=1&encoding=$encoding' target='mainFrame'>".loadicon('/icons/'.'_config'.'.gif',localize('_config',$lan),null)."</a>";
	   }	

     //$stokens[] =  "<a href=\"cpmdbrec.php?t=rempwd&editmode=1\">".localize('_rempass',$lan)."</a>";				    	  
	 //$stokens[] =  "<a href=\"cpmdbrec.php?t=chpass&editmode=1\"".localize('_chpass',$lan)."</a>";
	 $stokens[] =  "<a href=\"cpmdbrec.php?t=chpass&editmode=1\" target='mainFrame'>".localize('_chpass',$lan)."</a>";
     //s$tokens[] =  "<a href=\"cpmdbrec.php?t=cphelp&editmode=1\" target='mainFrame'>".localize('_cphelp',$lan)."</a>";		 	
	 if ($seclevid>=8) {
		$dhtml_var = remote_paramload('FRONTHTMLPAGE','dhtml',paramload('SHELL','prpath'));
		$dhtml_switch = $dhtml_var>0 ? '0' : '1';	 
		$modetitle = $dhtml_var>0 ? localize('_cpdhtmloff',$lan):localize('_cpdhtmlon',$lan);//'Win Mode OFF' : 'Win Mode ON';	
		$stokens[] =  "<a href=\"cpconfig.php?t=cpconfmod&var=fronthtmlpage.dhtml&val=$dhtml_switch&editmode=1\" target='mainFrame'>".$modetitle."</a>";	
	    
	 }
	 if ($seclevid>=9) {
		$stokens[] =  "<a href=\"cpmwiz.php?t=cpwizreinit&editmode=1\" target='mainFrame'>".localize('_cpwizard',$lan)."</a>";		 
		$stokens[] =  "<a href=\"cpmdbrec.php?t=cpupgrade&editmode=1\" target='mainFrame'>".localize('_cpupgrade',$lan)."</a>";	 

		//already into ..._top
		//$turl = urldecode(decode($_GET['turl']));
		//$turl_m = urldecode(decode('AmMMaVlsVHcBPV5mUnUHdQF8U20AcAI9')); 
		//echo GetReq('turl'),']',$turl_m;
		$urlargs = explode('?',$location);
		//print_r($urlargs);
		$turl_m = urldecode(base64_decode($_GET['turl']));// . '&cropwiz=1'; //not reenter cp
		$modify_url = /*$location*/$urlargs[0] . "?modify=".urlencode(base64_encode('stereobit'))."&turl=".urlencode(encode($turl_m)).'&cropwiz=1'; 	
		$stokens[] =  "<a href=\"$modify_url\" target='_top'>".localize('_cpcropwiz',$lan)."</a>";
	 }
	 
     //win settings.......................................................
	 if (!empty($stokens)) {
		$conf_content = implode('<hr/>',$stokens);
		$twinconf = new window2(localize('_SETTINGS',$lan),$conf_content,null,1,null,$winhide,null,1);
		$tokens[] = $twinconf->render("center::100%::0::group_article_selected::left::0::0::");	
		unset ($twinconf);	
	 }
	 
	 /*template based menu*/
	 if (($template) /*&& ($environment['EDIT_HTMLFILES']==1)*/) {
	 
        //$my_current_page = GetGlobal('controller')->calldpc_var("fronthtmlpage.MC_CURRENT_PAGE");	
		$my_current_page = GetGlobal('controller')->calldpc_method("fronthtmlpage.mc_parse_editurl use $location");		
		$mc_current_page = str_replace(array('.php','../'),array('',''),$my_current_page);
		//echo $mc_current_page;
		
	    if ($environment['EDIT_HTMLFILES']==1) {
		  /*edit html*/
	      //$mc_pages = GetGlobal('controller')->calldpc_method("fronthtmlpage.mcPages use 1");
		  $mc_pages = GetGlobal('controller')->calldpc_method("fronthtmlpage.mc_read_files use pages+php++1");
		  foreach ($mc_pages as $mcpage=>$mctitle) {
		    $mc_page = urlencode(base64_encode($mcpage . '.php'));
		    $mc_title = ($mcpage==$mc_current_page) ?
		                 '<b>'.$mctitle.'</b>' : $mctitle;			
			$htokens[] = "<a href='cpmhtmleditor.php?htmlfile=" .$mc_page. "&encoding=$encoding&editmode=1' target='mainFrame'>".$mc_title."</a>";
		  }
	 
		  //win edit html.......................................................
		  if (!empty($htokens)) {
			$conf_content = implode('<hr/>',$htokens);
			$twinconf = new window2(localize('_EDITHTML',$lan) ,$conf_content,null,1,null,$winhide,null,1);
			$tokens[] = $twinconf->render("center::100%::0::group_article_selected::left::0::0::");	
			unset ($twinconf);	
		  }	
        }

		if ($environment['SELECT_HTMLFILES']==1) {
		  /*select html*/					
	      //$mc_pages = GetGlobal('controller')->calldpc_method("fronthtmlpage.mcPages use 1");
		  $mc_pages = GetGlobal('controller')->calldpc_method("fronthtmlpage.mc_read_files use pages+php++1");
		  foreach ($mc_pages as $mcpage=>$mctitle) {
		    $mc_page = urlencode(base64_encode($mcpage . '.php'));
		    $mc_title = ($mcpage==$mc_current_page) ?
		               '<b>'.$mctitle.'</b>' : $mctitle;
		    $qtokens[] = "<a href='cpmhtmleditor.php?htmlfile=" .$mc_page. "&mc_page=$mcpage&turl=".$_GET['turl']."&encoding=$encoding&editmode=1' target='mainFrame'>".$mc_title."</a>";
		  }	
		  //win select html.......................................................
		  if (!empty($qtokens)) {
		    $conf_content = implode('<hr/>',$qtokens);
		    $twinconf = new window2(localize('_SELECTHTML',$lan) ,$conf_content,null,1,null,$winhide,null,1);
		    $tokens[] = $twinconf->render("center::100%::0::group_article_selected::left::0::0::");	
		    unset ($twinconf);	
		  }	
        }		
	 }

     /*if (defined('RCCONTROLPANEL_DPC')) {	 
	   $tokens[]  = "<a href=\"?t=cplogout\" onClick=\"top.location.href='".str_replace('_&_', '_%26_', $location) ."&editmode=-1'\">".localize('_logout',$lan)."</a>";
	   $_icons[] = "<a href=\"?t=cplogout\" onClick=\"top.location.href='".str_replace('_&_', '_%26_', $location) ."&editmode=-1'\">".loadicon('/icons/'.'_logout'.'.gif',localize('_logout',$lan),null)."</a>";
	 }
	 elseif (defined('SHLOGIN_DPC')) {//dologout cmd..
       $tokens[]  = "<a href=\"?t=dologout\" onClick=\"top.location.href='".str_replace('_&_', '_%26_', $location) ."&t=dologout'\">".localize('_logout',$lan)."</a>";
	   $_icons[] = "<a href=\"?t=dologout\" onClick=\"top.location.href='".str_replace('_&_', '_%26_', $location) ."&editmode=-1'\">".loadicon('/icons/'.'_logout'.'.gif',localize('_logout',$lan),null)."</a>";
	 }  
     else {
       //die('Login mechnanism not specified!');	 
	   $tokens[] = "Logout mechnanism not specified!";
	   $_icons[] = "Logout mechnanism not specified!";
	 } */ 
   }
   
   //print_r($tokens);
   if ($rettokens) {
     if (($useicons) && (GetSessionParam('LOGIN')=='yes')) 
	   return ($_icons);
	 else
       return ($tokens);
   }	 
   else {
     $ret = implode('<br>',$tokens);
	 //echo $ret;
	 return ($ret);
   }	 
   
 }
 
 function show_iconstable($type=0,$linemax=4,$icons=null) {

    if (empty($icons))
	  return;
  
    if (is_array($icons)) {
	
	  $itemscount = count($icons);
	  $timestoloop = floor($itemscount/$linemax)+1;
	  $meter = 0;
	  
	  for ($i=0;$i<$timestoloop;$i++) {
	    //echo $i,"---<br>";
	    for ($j=0;$j<$linemax;$j++) {
	       //echo $i*$j,"<br>"; 
           $ret .= $tokens[$i]; 
			 
		   $meter+=1;	 
	    }	  
	  }
	
	  return ($ret);	
    }
 }

 /*if (is_readable('html/cpsidepanel'.$slan.'.html')) {
 
	//echo cpanel($slan,0);
	$tokens = cpanel($slan,1,$pagename);
	$page = file_get_contents('html/cpsidepanel'.$slan.'.html');
	//echo $page;
	//echo GetGlobal('controller')->calldpc_method("fronthtmlpage.combine use $page+...");
	foreach ($tokens as $t=>$tok)
		$page = str_replace('$'.$t.'$',$tok,$page);
	//clean unused tokens	 
	for ($i>$t;$i<=50;$i++)
		$page = str_replace('$'.$i.'$','',$page);	 
	 
	echo $page;	 
 }  
 else {*/ //else no file to draw
    //die('cpsidepanel'.$slan.'.html is missing!');
	
	$mylocation = str_replace('_&_', '_%26_',$location);	  
    $onclick = "top.location.href='" . $mylocation . "'";
	$exithref = '#';//"cpmdbrec.php?t=cpexit";
	$exit_url =  "<a href=\"$exithref\" onClick=\"$onclick\">" . localize('_exit',$lan) . "</a>";	
	$logout_url = "<a href=\"?t=dologout\" onClick=\"top.location.href='".str_replace('_&_', '_%26_', $location) ."&t=dologout'\">".localize('_logout',$lan)."</a>";
	$go_url = (GetSessionParam('LOGIN')!='yes') ? $logout_url : $logout_url;
	$help_url = "<a href=\"cpmdbrec.php?t=cphelp&editmode=1'\" target='mainFrame'>".localize('_cphelp',$lan)."</a>";	
   
	$html = <<<EOF
<html>
<head>
<title>Control Panel</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="html/cpmdbrec.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="html/jquery-1.5.1.min.js"></script>
<LINK REL=StyleSheet HREF="../themes/styles.css">
		<script type="text/javascript">
               //expand/show element
               function expand(listID) {
	             listID = document.getElementById(listID);
                 if (listID.style.display == "none") {
                   listID.style.display = "";
                 }
                 else {
                   listID.style.display = "none";
                 }
               }

               //contract/hide element
               function contract(listID) {
	             listID = document.getElementById(listID);
	             if (listID.style.display == "show") {
		           listID.style.display = "";
	             }
	             else {
		           listID.style.display = "none";
	             }
               }
    </script>	
</head>

<body class="sign-in-page">
<div id="site-wrapper">
<div id="header-wrapper">
	<div id="header-content" class="container_12">
		<div id="glow"></div>
		<div id="top-nav-wrapper" class="container_12">
			<!--div id="header-logo" class="grid_1 alignleft">
				<a target="_top" href="#"><img src="images/logo.png" alt="ste.net" /></a>

		    </div--> 
			<!-- #header-logo -->
		    
		    <div id="top-nav" class="grid_1omega">
				<ul id="menu-top-nav-primary" class="top-nav alignright">
					<li id="menu-item-22" class="menu-item menu-item-type-post_type menu-item-object-page page_item page-item-20 current_page_item menu-item-22">
						$exit_url
					</li>
					<li id="menu-item-26" class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-26">
						$help_url
					</li>
					<li id="menu-item-25" class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-25">
						$go_url
					</li>
				</ul>  
		    </div> <!-- #top-nav -->
		    <div class="clearfix"></div>

	     </div> <!-- #top-nav-wrapper -->
	     
	</div> <!-- #header-content -->
</div><!-- #header-wrapper -->
<div id="content-wrapper">
	<div id="content" class="container_12">
	<div class="sign-up-box">
EOF;
    

	echo $html;
    $tokens = cpanel($slan,1,$pagename);	
    foreach ($tokens as $t=>$tok)	
	    echo $tok,'<br/>';
		
	if (!$cmd)	
		echo '
	    <div class="clearfix"></div>
		</div><!-- #sign-up-box -->
		</div> <!-- #content -->
</div> <!-- #content-wrapper -->
</div>
<script type="text/javascript" src="http://cdn.dev.skype.com/uri/skype-uri.js"></script>
<div id="SkypeButton_Dropdown_Stereobit_1" style="width:100%;background-color:#FFFFFF">
  <script type="text/javascript">
    Skype.ui({
      "name": "chat",
      "element": "SkypeButton_Dropdown_Stereobit_1",
      "participants": ["balexiou"],
      "imageColor": "blue",
      "imageSize": 24
    });
  </script>
</div> <!-- #site-wrapper -->		
</body>
</html>';
 //}  
?>

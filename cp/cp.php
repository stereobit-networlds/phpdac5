<?php
require_once('dpc/system/pcntl.lib.php'); 
$page = &new pcntl('
super javascript;
super rcserver.rcssystem;
load_extension adodb refby _ADODB_; 
super database;
/---------------------------------load and create libs
use xwindow.window,xwindow.window2,browser;
use gui.swfcharts;
/use jqgrid.jqgrid;...output started error
/---------------------------------load not create dpc (internal use)
include networlds.clientdpc;
include gui.form;
/---------------------------------load not create extensions (internal use)		
/load_extension http_class refby _HTTPCL_; 

/---------------------------------load all and create after dpc objects
/public frontpage.fronthtmlpage;
private frontpage.fronthtmlpage /cgi-bin;

#ifdef SES_LOGIN
/public jqgrid.mygrid;
public gui.ajax;
public mail.smtpmail;
public shop.rckategories;
public shop.rcitems;
public shop.rcmaildbqueue;
public shop.rctransactions;
public shop.shusers;
public shop.shcustomers;
public phpdac.rcimportdb;
public phpdac.rcupload;
public phpdac.rcconfig;
#endif
private phpdac.rccontrolpanel /cgi-bin;
public phpdac.shlogin;
',1);

$cptemplate = GetGlobal('controller')->calldpc_method('rcserver.paramload use FRONTHTMLPAGE+cptemplate');
//echo $cptemplate,'>';

if ($cptemplate) {
		
	/*$turl = $_GET['turl'];
	$location = '../' . urldecode(base64_decode($turl));
	$mylocation = str_replace('_&_', '_%26_',$location);
    */
	//echo 'template:',$template;  
	$encoding = $_GET['encoding']?$_GET['encoding']:'utf-8'; 
    $useicons = 0;//1;				
	//page name..
	$pn = explode('/',$location);
	$pname = array_pop($pn);
	$pnurl = stristr($pname,'?') ? explode('?',$pname) : array('0'=>$pname);
	$pagename = $pnurl[0]; 
	//echo $pagename;	

	switch ($_GET['t']) {
	    case 'chpass' : if (GetReq('sectoken')) {//has been send
                          //$message = GetGlobal('controller')->calldpc_method('shlogin.html_reset_pass use 1');//change pass  		
						  $mc_page = 'cp-chpass';
						}  
		                else {    
		                  //$message = GetGlobal('controller')->calldpc_method('shlogin.html_remform');//send mail 
						  $mc_page = 'cp-lock';
						}  
		                break;
		case 'shremember':	//GetGlobal('controller')->calldpc_method('shlogin.do_the_job');	
                        //$message = localize('ok',getlocal());
						$mc_page = 'cp-lock';
                        break;    
        default       :
					   if (($user = $_POST['cpuser']) && ($pass = $_POST['cppass'])) {
						//echo $_POST['cpuser'],'>',$_POST['cppass'];
						/*if (defined('RCCONTROLPANEL_DPC'))
							$login = GetGlobal('controller')->calldpc_method("rccontrolpanel.verify_login");
						elseif (defined('SHLOGIN_DPC'))*/
							$login = GetGlobal('controller')->calldpc_method("shlogin.do_login use ".$user.'+'.$pass.'+1');	
						/*else
							die('Login mechnanism not specified!');*/	 
					   }

					   $mc_page = ((GetSessionParam('LOGIN'))||($login)) ? 'dashboard' : 'cp-login';	  
					   //$mc_page = GetGlobal('controller')->calldpc_method('frontpage.mcSelectPage use index+home++1');
					   //echo $mc_page;
    }
  
	echo $page->render(null,getlocal(), null, $cptemplate.'/index.php');
}
else
	echo $page->render(null,getlocal(),null,'cp_em.html');
?>
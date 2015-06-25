<?php
require_once('dpc/system/pcntl.lib.php'); 
$page = &new pcntl('

super javascript;
super rcserver.rcssystem;

load_extension adodb refby _ADODB_; 
super database;

use xwindow.window;
use gui.swfcharts;
/---------------------------------load not create dpc (internal use)
include networlds.clientdpc;			

/---------------------------------load all and create after dpc objects
private frontpage.fronthtmlpage /cgi-bin;
#ifdef SES_LOGIN
public phpdac.shlogin;
private shop.rcitems /cgi-bin;
public phpdac.rcwizard;
#endif
private phpdac.rccontrolpanel /cgi-bin;
',1);

$cptemplate = GetGlobal('controller')->calldpc_method('rcserver.paramload use FRONTHTMLPAGE+cptemplate');
$lan = getlocal();
if ($cptemplate) {
    $mc_page = (GetSessionParam('LOGIN')) ? 'cp-tags' : 'cp-login';
	echo $page->render(null,getlocal(), null, $cptemplate.'/index.php');
}
else
	echo $page->render(null,getlocal(),null,"cpwizard$lan.html");
?>
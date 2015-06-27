<?php
require_once('dpc/system/pcntlajax.lib.php'); 
$page = &new pcntlajax('

super javascript;
super rcserver.rcssystem;

load_extension adodb refby _ADODB_; 
super database;

/---------------------------------load and create libs
use xwindow.window,xwindow.window2,gui.swfcharts;
use jqgrid.jqgrid;

/---------------------------------load not create dpc (internal use)
include networlds.clientdpc;
include gui.form;
/include gui.datepick;
/include mail.smtpmail;			

/---------------------------------load all and create after dpc objects
private frontpage.fronthtmlpage /cgi-bin;
#ifdef SES_LOGIN
public jqgrid.mygrid;
public gui.ajax;
public database.dataforms;
public phpdac.rcupload;
private shop.rcitems /cgi-bin;
public shop.rcvstats;
public shop.rctransactions;
private cp.cpmhtmleditor /cgi-bin;
#endif
private phpdac.rccontrolpanel /cgi-bin;
',1);

$cptemplate = GetGlobal('controller')->calldpc_method('rcserver.paramload use FRONTHTMLPAGE+cptemplate');

if ($cptemplate) {
    switch ($_GET['ajax']) {
		case 1 : $mc_page = (GetSessionParam('LOGIN')) ? 'cp-blank' : 'cp-login'; break;
		default: $mc_page = (GetSessionParam('LOGIN')) ? 'cp-items' : 'cp-login';
	}	
	echo $page->render(null,getlocal(), null, $cptemplate.'/index.php');
}
else
	echo $page->render(null,getlocal(),null,'cp_em.html');
?>
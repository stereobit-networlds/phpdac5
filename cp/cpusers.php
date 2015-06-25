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
use jqgrid.jqgrid;

/---------------------------------load not create dpc (internal use)
include networlds.clientdpc;
include gui.form;
include gui.datepick;
include mail.smtpmail;

security ACCOUNTMNG_ 1 1:1:1:1:1:1:1:1;
security USERMNG_ 1 1:1:1:1:1:1:1:1;
security USERSMNG_ 1 1:1:1:1:1:1:1:1;
security SIGNUP_ 1 1:1:1:1:1:1:1:1;
security DELETEUSR_ 1 1:1:1:1:1:1:1:1;
security UPDATEUSR_ 1 1:1:1:1:1:1:1:1;
			

/---------------------------------load all and create after dpc objects
private frontpage.fronthtmlpage /cgi-bin;
#ifdef SES_LOGIN
public jqgrid.mygrid;
public gui.ajax;
public database.dataforms;
public shop.shsubscribe;
public phpdac.rcusers;
public phpdac.rccustomers;
public shop.rctransactions;
#endif
private phpdac.rccontrolpanel /cgi-bin;
',1);

$cptemplate = GetGlobal('controller')->calldpc_method('rcserver.paramload use FRONTHTMLPAGE+cptemplate');

if ($cptemplate) {
    $mc_page = (GetSessionParam('LOGIN')) ? 'cp-tags' : 'cp-login';
	echo $page->render(null,getlocal(), null, $cptemplate.'/index.php');
}
else
	echo $page->render(null,getlocal(),null,'cp_em.html');
?>
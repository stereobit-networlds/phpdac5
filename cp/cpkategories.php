<?php
require_once('dpc/system/pcntlajax.lib.php'); 
$page = &new pcntlajax('

super javascript;
super rcserver.rcssystem;

load_extension adodb refby _ADODB_; 
super database;

/---------------------------------load and create libs
use xwindow.window,gui.swfcharts;
use jqgrid.jqgrid;

/---------------------------------load not create dpc (internal use)
include networlds.clientdpc;
include gui.form;

/---------------------------------load all and create after dpc objects
private frontpage.fronthtmlpage /cgi-bin;
#ifdef SES_LOGIN
public jqgrid.mygrid;
public gui.ajax;
public database.dataforms;
private shop.rcitems /cgi-bin;
public gui.ajax;
private shop.rckategories /cgi-bin;
#endif
private phpdac.rccontrolpanel /cgi-bin;
',1);

$cptemplate = GetGlobal('controller')->calldpc_method('rcserver.paramload use FRONTHTMLPAGE+cptemplate');

if ($cptemplate) {

	switch ($_GET['t']) {
		case 'cpkategories'  : $p = $_GET['ajax'] ? 'cp-ajax-ckeditor' : 'cp-tags'; break;
		default              : $p = $_GET['ajax'] ? 'cp-ajax-ckeditor' : 'cp-tags';
	}
	
    $mc_page = (GetSessionParam('LOGIN')) ? $p : 'cp-login';
	echo $page->render(null,getlocal(), null, $cptemplate.'/index.php');
}
else
	echo $page->render(null,getlocal(),null,'cp_em.html');
?>
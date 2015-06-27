<?php
require_once('dpc/system/pcntlajax.lib.php'); 
$page = &new pcntlajax('

#define ESHOP CONF_ESHOP_ENABLE

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
include gui.datepick;

#if ESHOP > 0			
	security CART_DPC 1 1:1:1:1:1:1:1:1;
	security SHCART_DPC 1 1:1:1:1:1:1:1:1;
	security TRANSACTIONS_DPC 1 1:1:1:1:1:1:1:1;
	security SHTRANSACTIONS_DPC 1 1:1:1:1:1:1:1:1;
#endif				

/---------------------------------load all and create after dpc objects
private frontpage.fronthtmlpage /cgi-bin;
#ifdef SES_LOGIN
public jqgrid.mygrid;
public gui.ajax;
public shop.rckategories;
private shop.shkatalogmedia /cgi-bin;
private shop.rcitems /cgi-bin;
private shop.shcustomers /cgi-bin;
private shop.shcart /cgi-bin;
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
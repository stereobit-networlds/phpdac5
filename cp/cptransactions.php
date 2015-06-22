<?php
require_once('dpc/system/pcntlajax.lib.php'); 
$page = &new pcntlajax('

#define SHCART 1

/super cache,log;
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
include frontpage.fronthtmlpage;
include gui.datepick;

#ifdef SHCART			
	security CART_DPC 1 1:1:1:1:1:1:1:1;
	security SHCART_DPC 1 1:1:1:1:1:1:1:1;
	security TRANSACTIONS_DPC 1 1:1:1:1:1:1:1:1;
	security SHTRANSACTIONS_DPC 1 1:1:1:1:1:1:1:1;
#endif				

/---------------------------------load all and create after dpc objects
public jqgrid.mygrid;
public gui.ajax;
public shop.rckategories;
private shop.shkatalogmedia /cgi-bin;
public shop.rcvstats;
private shop.shcustomers /cgi-bin;
#ifdef SHCART
	private shop.shcart /cgi-bin;
	public shop.rctransactions;
#endif
',1);

$lan = getlocal();
echo $page->render(null,$lan,null,'cp_em.html');
?>
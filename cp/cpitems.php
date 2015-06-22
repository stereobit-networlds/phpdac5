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
include frontpage.fronthtmlpage;
include gui.form;
include gui.datepick;
include mail.smtpmail;
			

/---------------------------------load all and create after dpc objects
public jqgrid.mygrid;
public gui.ajax;
public database.dataforms;
public phpdac.rccontrolpanel;
public phpdac.rcupload;
/public mail.abcmail;
/public shop.rcshmail;
/public shop.rcshsubscribers;
/public shop.rckategories;
private shop.rcitems /cgi-bin;
/public phpdac.rccustomers;
/public rc.rcreport;
/public rcserver.rcsidewin;
public shop.rcvstats;
public shop.rctransactions;
',1);

$lan = getlocal();

echo $page->render(null,$lan,null,'cp_em.html');
?>


<?php
require_once('dpc/system/pcntl.lib.php'); 
$page = &new pcntl('
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
public mail.abcmail;
public phpdac.rccustomers;
public shop.rcitems;
public shop.rctransactions;
',1);

$lan = getlocal();
echo $page->render(null,$lan,null,'cp_em.html');
?>

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
include frontpage.fronthtmlpage;
include gui.form;
/include gui.htmlarea;
			

/---------------------------------load all and create after dpc objects
/public phpdac.rccontrolpanel;
public jqgrid.mygrid;
public gui.ajax;
public shop.rcform;
',1);

$lan = getlocal();

echo $page->render(null,$lan,null,'cp_em.html');

?>
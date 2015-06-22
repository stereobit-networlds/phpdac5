<?php
require_once('dpc/system/pcntl.lib.php'); 
$page = &new pcntl('

/super cache,log;
super javascript;
super rcserver.rcssystem;

/---------------------------------load and create libs
use xwindow.window,xwindow.window2,browser;

/---------------------------------load not create dpc (internal use)
/include frontpage.frontpage;
/public cache.supercache;
include frontpage.fronthtmlpage;
/include gui.form;
/include gui.htmlarea;
	
/---------------------------------load not create extensions (internal use)
/load_extension adodb refby _ADODB_; 		

/---------------------------------load all and create after dpc objects
public cpanel.rcpanel;

',1);
echo $page->render(null,0,null,'cp_nocache.html');//'template4.htm');
?>


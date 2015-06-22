<?php
//define ('SENDMAIL_PHPMAILER',null);
//define ('SMTP_PHPMAILER','true');

require_once('dpc/system/pcntl.lib.php'); 
$page = &new pcntl('
super javascript;
super rcserver.rcssystem;

load_extension adodb refby _ADODB_; 
super database;

/---------------------------------load and create libs
use xwindow.window,xwindow.window2,browser;
use jqgrid.jqgrid;

/---------------------------------load not create dpc (internal use)
include networlds.clientdpc;
include frontpage.fronthtmlpage;
include gui.tinyMCE;
include gui.datepick;
include mail.smtpmail;
		

/---------------------------------load all and create after dpc objects
public jqgrid.mygrid;
public gui.ajax;
public phpdac.rccontrolpanel;
public shop.rcshmail;
public shop.rckategories;
public shop.rcitems;
public shop.shtags;
public phpdac.rcfs;
public phpdac.rcupload;
private shop.rcitems /cgi-bin;
private phpdac.rctedit /cgi-bin;
private phpdac.rctedititems /cgi-bin;
private phpdac.rcshsubsqueue /cgi-bin;
',1);
echo $page->render(null,$lan,null,'cp_em.html');
?>
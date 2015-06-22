<?php
require_once('dpc/system/pcntl.lib.php'); 
$page = &new pcntl('
super javascript;
super rcserver.rcssystem;

load_extension adodb refby _ADODB_;
super database;

/---------------------------------load and create libs
use xwindow.window,xwindow.window2,browser;

/---------------------------------load not create dpc (internal use)
include networlds.clientdpc;
include frontpage.fronthtmlpage;
include gui.form;
	
/---------------------------------load all and create after dpc objects
public phpdac.rccontrolpanel;
/public shop.shkategories;
/public shop.shkatalogmedia;
public shop.rckategories;
public shop.rcitems;
public phpdac.rcupload;

',0);
$lan = getlocal();

if (GetReq('editmode')==1)
  echo $page->render(null,$lan,null,'cp_em.html');
else
  echo $page->render(null,$lan,null,'cpgroup.html');
?>
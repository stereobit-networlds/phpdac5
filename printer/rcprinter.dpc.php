<?php


$__DPCSEC['RCPRINTER_DPC']='1;1;1;1;1;1;1;1;1';

if ((!defined("RCPRINTER_DPC")) && (seclevel('RCPRINTER_DPC',decode(GetSessionParam('UserSecID')))) ) {
define("RCPRINTER_DPC",true);

$__DPC['RCPRINTER_DPC'] = 'rcprinter';

//$d = GetGlobal('controller')->require_dpc('printer/ippprinter.lib.php');
//require_once($d); 

$__EVENTS['RCPRINTER_DPC'][0]='rcprinter';
$__EVENTS['RCPRINTER_DPC'][1]='rcaddprinter';
$__EVENTS['RCPRINTER_DPC'][2]='rcmodprinter';
$__EVENTS['RCPRINTER_DPC'][3]='rcremprinter';
$__EVENTS['RCPRINTER_DPC'][4]='rc4';
 
$__ACTIONS['RCPRINTER_DPC'][0]='rcprinter';
$__ACTIONS['RCPRINTER_DPC'][1]='rcaddprinter';
$__ACTIONS['RCPRINTER_DPC'][2]='rcmodprinter';
$__ACTIONS['RCPRINTER_DPC'][3]='rcremprinter';
$__ACTIONS['RCPRINTER_DPC'][4]='rc4';


$__DPCATTR['RCPRINTER_DPC']['rcprinter'] = 'pcprinter,1,0,0,0,0,0,0,0,0,0,0,1';

$__LOCALE['RCPRINTER_DPC'][0]='RCPRINTER_DPC;Printer;Printer';   
 
class rcprinter {

   var $title;
   var $p;
   var $inform_ipn_mail;
   var $prpath;
   var $this_script;
   var $paypal_post;

   function rcprinter() {
    
   }
   
   function event($event=null) {
     switch ($event) {
	 
		case 'rcaddprinter' :
		case 'rcmodprinter' :
		case 'rcremprinter' : break;	 

	    default ://no action
	 }
   }
   
   function action($action=null) {
   
     switch ($action) {
	 
		case 'rcaddprinter' :
		case 'rcmodprinter' :
		case 'rcremprinter' : break;	 

	    default ://no action
	 }
	 
	 return ($ret);
   }
   
   function addquota($jobs=null) {
   
	    $ret = $jobs .' jobs added';
		
		return ($ret);   
   }    
}; 
} 
?>
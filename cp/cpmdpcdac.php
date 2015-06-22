<?php
if ($_GET['t']) //cmd to execute...
  require_once('dpc/system/pcntl.lib.php'); 
else //common html ...
  require_once('dpc/system/pcntlhtml.lib.php'); 
$page = &new pcntl('
super javascript;
super rcserver.rcssystem;

load_extension adodb refby _ADODB_;
super database; 

use xwindow.window;
include networlds.clientdpc;
include gui.form;

public frontpage.fronthtmlpage;
/public rc.rclogo;
public rc.rcfs;
public rc.rcscripts;
public rc.rctedit;
public rc.rcconfig;
',1);
//print_r($_SESSION);
$encoding = $_GET['encoding']?$_GET['encoding']:'utf-8';

$lan = getlocal();

  
if (/*(GetReq('editmode')==1) &&*/ ($cmd = GetReq('t')) && ($renderpage = $page->render(null,$lan,null,'cp_em.html'))) { //in case of t=cmd..execute

  //echo $cmd,'>';
  if (trim($page->data)) {
    //echo $page->data,'>'; 
    echo $renderpage;
    die();
  }
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>php-dpc-dac</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?echo $encoding ?>">
<script language="Javascript" type="text/javascript">
<?php
  //when script to add exists refresh....		
  if ((GetReq('t')=='cpaddcodescript') && ($script = GetParam('script'))) {
     
	  $phpfile = GetReq('phpfile');
	 
	  echo "/*$script,$phpfile*/
	    function edit_phpfile()
	    {
          top.bottomFrame.location='cpmdpceditor.php?encoding=" . GetReq('encoding') . "&turl=" . $phpfile ."';
	    }	  
";	  
  }  
  else {
	  echo "
	    function edit_phpfile()
	    {
			//null function
	    }	  
"; 
  } 

echo "
function show_prompt(say,sayval,goto)
{
var name=prompt(say,sayval);
if (name!=null && name!=\"\")
  {
  //document.write(\"<p>Hello \" + name + \"! How are you today?</p>\");
  top.mainFrame.location=goto+name;
  }
}
function refresh() 
{
  top.rightFrame.location=window.location.href+'&class=".GetReq('script')."';
}
";
?>
</script>
</head>

<body onLoad="edit_phpfile()" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php

 $phpfile = GetReq('phpfile'); 
 //echo GetReq('phpfile'),'>',urldecode(base64_decode(GetReq('phpfile'))),'>';
 
 if (stristr($phpfile,'.php')) {//in case of already decoded name
    $lan = getlocal()?getlocal():'0';
    $phpfile = str_replace($lan.'.php','.php',$phpfile);//extract lan digit
 }	
 else		   
   $phpfile = urldecode(base64_decode(GetReq('phpfile'))); 
 //echo $phpfile,'>';
 
 //echo '<span>';
 //echo "<a href='$location&editmode=1' target='mainFrame'>[Preview]</a>","&nbsp;";
 //echo "<a href='cpmhtmleditor.php?htmlfile=$htmlfile&editmode=1&encoding=$encoding' target='mainFrame'>[Edit]</a>","&nbsp;";
 
 echo  'Dpc class:',GetGlobal('controller')->calldpc_method("rcscripts.show_used_scripts use Select++cp/dpc+cpmdpcdac.php?t=cpusescript&phpfile=$phpfile&script=");
 echo '<br>'; 
 echo  'Inventory:',GetGlobal('controller')->calldpc_method("rcscripts.show_used_scripts use Select+++cpmdpcdac.php?t=cpaddcodescript&phpfile=$phpfile&script=");
 echo '<br>';
 echo  'Dpc inuse:',GetGlobal('controller')->calldpc_method("rcscripts.show_active_scripts use Select+++cpmdpcdac.php?t=cpeditscript&phpfile=$phpfile&script=+mainFrame"); 
 //echo  GetGlobal('controller')->calldpc_method("rctedit.show_image_files use Add++images");  
 //echo "<a href='cpmdbrec.php?turl=".$_GET['turl']."' target='rightFrame'>[Database]</a>","&nbsp;";

 //echo "<a href='?t=cptnew&editmode=1' target='mainFrame'>[New]</a>","&nbsp;";
 //echo "<a href=\"javascript:top.location.href='".$location ."&editmode=-1'\">[Exit]</a>","&nbsp;";
 
 //echo '</span>';
 
 
 echo '<hr/>';

  $cmd0 = seturl('t=cpcheckclass&editmode=1&class='.GetReq('script'));
  echo "<a href='$cmd0' onClick=\"refresh();\" target='bottomFrame'>Check class</a>";
  echo '<br>'; 
  if ($class = GetReq('class')) {
     echo 'class:',$class;
     echo '<br>';	 
	 echo  'Endoscope:',GetGlobal('controller')->calldpc_method("rcscripts.endoscope_class use ".$class); 
     echo '<hr>';	 
  }  
  
  if ($script = GetReq('script')) {
    if (stristr($script,'.')) {
      $sp = explode('.',$script);
	  $dpcpart =  strtoupper($sp[1]);
	}
	else
	  $dpcpart = strtoupper($script);
    $cmd1 = seturl('t=cpconfig&editmode=1&cpart='.$dpcpart);
  }
  else
    $cmd1 = seturl('t=cpconfig&editmode=1');
  echo "<a href='$cmd1' target='bottomFrame'>Configuration</a>";
  echo '<br>';
  //$cmd2 = seturl('t=cpnewdpclass&phpfile='.GetReq('phpfile').'&editmode=1');
  //echo "<a href='$cmd2' target='mainFrame'>New dpc file</a>";
  $cmd2 = seturl('t=cpnewdpclass&phpfile='.GetReq('phpfile').'&editmode=1&script=');  
  echo "<a href='#' onclick=\"show_prompt('Enter dpc class name:','newclass','$cmd2')\">New dpc file</a>";

?>
</body>
</html>



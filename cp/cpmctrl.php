<?php
if ($_GET['t']) //cmd to execute...
  require_once('dpc/system/pcntl.lib.php'); 
else //common html ...
  require_once('dpc/system/pcntlhtml.lib.php'); 
  
$__LOCALE['FRONTHTMLPAGE_DPC'][1001]='_addpage;Add Page;Νέα σελίδα';
$__LOCALE['FRONTHTMLPAGE_DPC'][1002]='_editpage;Edit Page;Επεξεργασία σελίδας';
$__LOCALE['FRONTHTMLPAGE_DPC'][1003]='_previewpage;Preview;Προεπισκόπηση';
$__LOCALE['FRONTHTMLPAGE_DPC'][1004]='_copypage;Copy Page;Αντιγραφή σελίδας';  
  
$page = &new pcntl('
super javascript;
super rcserver.rcssystem;

load_extension adodb refby _ADODB_;
super database; 

use xwindow.window;
include networlds.clientdpc;
include gui.form;

public frontpage.fronthtmlpage;
public rc.rclogo;
public rc.rcfs;
/public rc.rcedittemplates;
public rc.rcscripts;
public rc.rctedit;
',1);
//print_r($_SESSION);
$encoding = $_GET['encoding']?$_GET['encoding']:'utf-8';

$lan = getlocal();

if ((GetReq('editmode')==1) && GetReq('t')) { //in case of t=cmd..execute
  echo $page->render(null,$lan,null,'cp_em.html');
  die();
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Commands</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?echo $encoding ?>">
<script language="Javascript" type="text/javascript" src="http://www.stereobit.gr/javascripts/edit_area/edit_area_full.js"></script>
	<script language="Javascript" type="text/javascript">
		// initialisation
		editAreaLoader.init({
			id: "htmleditor"	// id of the textarea to transform		
			,start_highlight: true	// if start with highlight
			,allow_resize: "both"
			,allow_toggle: false
			,word_wrap: true
			,language: "en"
			,syntax: "php"	
		});
// callback functions
		function my_save(id, content){
			alert("Here is the content of the EditArea '"+ id +"' as received by the save callback function:\n"+content);
		}
		
		function my_load(id){
			editAreaLoader.setValue(id, "The content is loaded from the load_callback function into EditArea");
		}
		
		function test_setSelectionRange(id){
			editAreaLoader.setSelectionRange(id, 100, 150);
		}
		
		function test_getSelectionRange(id){
			var sel =editAreaLoader.getSelectionRange(id);
			alert("start: "+sel["start"]+"\nend: "+sel["end"]); 
		}
		
		function test_setSelectedText(id){
			text= "[REPLACED SELECTION]"; 
			editAreaLoader.setSelectedText(id, text);
		}
		
		function test_getSelectedText(id){
			alert(editAreaLoader.getSelectedText(id)); 
		}
		
		function editAreaLoaded(id){
			if(id=="example_2")
			{
				open_file1();
				open_file2();
			}
		}
		
		function open_file1()
		{
			var new_file= {id: "to\\ &eacute; # &#8364; to", text: "$authors= array();\n$news= array();", syntax: 'php', title: 'beautiful title'};
			editAreaLoader.openFile('example_2', new_file);
		}
		
		function open_file2()
		{
			var new_file= {id: "Filename", text: "<a href=\"toto\">\n\tbouh\n</a>\n<!-- it's a comment -->", syntax: 'html'};
			editAreaLoader.openFile('example_2', new_file);
		}
		
		function close_file1()
		{
			editAreaLoader.closeFile('example_2', "to\\ &eacute; # &#8364; to");
		}
		
		function toogle_editable(id)
		{
			editAreaLoader.execCommand(id, 'set_editable', !editAreaLoader.execCommand(id, 'is_editable'));
		}
	</script>

<SCRIPT language="JavaScript">
function submitform()
{
  document.dpcform.submit();
}
</SCRIPT> 		
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php

    function render($file,$targetfile) {
      //echo '<br>',$file,':',$targetfile;
	  $mydata = file_get_contents($file); 	

	  //find relative html file 
	  $hpages = find_html_pages($mydata, $returnfindedfile);
	  //echo ':::::',$returnfindedfile;
	  //print_r($hpages);
	  if (!empty($hpages)) {
	    //$out .= implode('|',$hpages);
		$pages_included = implode('|',$hpages);
	  }	
	
      //selections	..for auto returned HTML PAGE TO EDIT/PREVIEW
	  $htmlfile = $returnfindedfile ? urlencode(base64_encode($returnfindedfile)): GetReq('htmlfile');
	  //$location = '../'.urldecode(base64_decode($_GET['turl']));  
	  $phpfile = $_POST['selected_phpfile'] ? urlencode(base64_encode($_POST['selected_phpfile'])) : (GetReq('phpfile') ? GetReq('phpfile') : $location) ;
	  $preview = $_POST['selected_phpfile'] ? urlencode(base64_encode($_POST['selected_phpfile'])) :  urlencode(base64_encode($location));  
	  $loadfile_url = "cpmctrl.php?turl=.".$_GET['turl']."encoding=". $encoding;// . '&htmlfile=' . $htmlfile;//..posted
	  
	  //other php files
	  $viewdata[] = GetGlobal('controller')->calldpc_method("rctedit.show_php_files use selected_phpfile+Select file+".$loadfile_url.'+1'); 
	  $viewattr[] = "left;40%";	
      //preview	
	  $location = $phpfile ? '../'. base64_decode(urldecode($phpfile)) : '../' . urldecode(base64_decode($_GET['turl']));  
	  $viewdata[] = "<a href='$location' target='mainFrame'>".localize('_previewpage',$lan)."</a>"."&nbsp;";
	                //"<a href='cpmhtmleditor.php?htmlfile=$htmlfile&editmode=1&encoding=$encoding&phpfile=$phpfile&preview=$preview' target='mainFrame'>".localize('_previewpage',$lan)."</a>"."&nbsp;";
	  $viewattr[] = "left;5%";	
      //edit
	  $edit = "<a href='cpmhtmleditor.php?htmlfile=$htmlfile&editmode=1&encoding=$encoding&phpfile=$phpfile' target='mainFrame'>".localize('_editpage',$lan)."</a>"."&nbsp;";
	  $edit.= $pages_included ? '('.$pages_included.')' : null;
	  $viewdata[] = $edit;
	  $viewattr[] = "left;50%";	

	  //edit html files/templates  
	  $viewdata[] = GetGlobal('controller')->calldpc_method("rctedit.show_template_files use Edit+++cpmhtmleditor.php?encoding=".$encoding."&htmlfile=+mainFrame");
	  $viewattr[] = "left;15%";		

	  $myrec = new window('',$viewdata,$viewattr);
      $out .= $myrec->render("center::100%::0::::left::0::0::");		  
	  //$ret .= $myrec->render();//"center::100%::0::group_article_selected::left::4::4::");
	  unset ($viewdata);
	  unset ($viewattr);	  
	  //selections
	  
	  $out .= "\n<form name=\"dpcform\" action=\"".$_SERVER['PHP_SELF'].'?turl='.$_GET['turl'].'&encoding='.$_GET['encoding']."\" method=\"post\">";  
	 
	  $out .= '<div>'; 
      $out .= "\n <textarea wrap='virtual' id='htmleditor' name='htmltext' style='width: 100%' rows='16' autowrap>$mydata</textarea>";	 
	  $out .= '</div>';
	 
      //$out .=  GetGlobal('controller')->calldpc_method("rctedit.show_php_files use selected_phpfile+1");	
	  
	  $out .= "<input type=\"submit\" name=\"ok\" value=\"  submit  \" />";	  
	  $out .= "<input type=\"hidden\" name=\"file2saveon\" value=\"$targetfile\" />";	  
      $out .= "<input type=\"hidden\" name=\"filetemp\" value=\"" . $file . "\" />";	  


	  $out .= "</form>";
	  
     //$out .= $file.'>'.$targetfile; 
	 return ($out); 
    }	

    function savefile($filename=null,$originalsave=null) {
         //echo $filename;
		 
	     /////////////////////////////////////////////////////////////
	     if (GetSessionParam('LOGIN')!='yes')  {
		   echo 'Not logged in!';
		   return;
		 }
		 //die("Not logged in!");//	
	     /////////////////////////////////////////////////////////////		 

         if ($_POST['ok']) {
            //file_put_contents($filename,$_POST['htmltext']); //save temp
            write2disk($filename,$_POST['htmltext']); //save temp

             
            if ($originalsave)
              //file_put_contents($_POST['file2saveon'],$_POST['htmltext']); //save original
              write2disk($_POST['file2saveon'],$_POST['htmltext']); //save original
 

         }
    }

    function remove_spchars($text=null) {

       $p1 = str_replace("\'","'",$text);
       $p2 = str_replace('\"','"',$p1);

       return $p2;

    }

    function loadcode($code) {

      //do code transformations...remove php etc
      $c = null;
    } 

    function write2disk($file,$data=null) {

            if ($fp = @fopen ($file , "w")) {
	        //echo $file,"<br>";
                 fwrite ($fp, remove_spchars($data));
                 fclose ($fp);

                 return true;
            }
            else {
              echo "File creation error ($file)!<br>";
            }
            return false;

    }
	
	function find_html_pages($phptext=null, &$lastfinded=null) {
	    global $encoding;
	    $lan = getlocal() ? getlocal() : '0';
	
        $hpages = array();
		//preg_match('/email" value="(.*)"/', $phptext, $hpages);
		//preg_match_all('/->render(.*);/', $phptext, $hpages); //find all render->(**);
		preg_match_all('/"(.*)"/', $phptext, $hpages); //find all "**";
		//preg_match_all('/"(.*).html"/', $phptext, $hpages); //find all "**.html";
		
		//return $hpages[0]; //$email[1];	
		//print_r($hpages[0]);
		foreach ($hpages[0] as $p) {
		
		   if (stristr($p,'".$ver."'))//ver argument...
		       $p = str_replace('".$ver."','1', $p);
		
		   if (stristr($p,'.htm')) {//exclude " and index as the file for singularity
		      $hfile =  str_replace('"','',$p);
		      $htmlfile = urlencode(base64_encode($hfile));
		      $ret[$p] = "<a href='cpmhtmleditor.php?cke4=1&encoding=".$encoding."&htmlfile=" . $htmlfile ."' target='mainFrame'>".
			             $hfile . "</a>";
						 
			  $lastfinded = str_replace('"','',$p);			 
		   }	  
		}
		return ($ret);
	}


//$htmlfile = GetReq('htmlfile');

/*if (stristr($_GET['turl'],'.php')) {//in case of already decoded name
  $mylocation = str_replace($lan.'.php','.php',$_GET['turl']);//extract lan digit
  $location = '../'.$mylocation;
}  
else*/  
  $location = '../'.urldecode(base64_decode($_GET['turl']));
  
//$phpfile = GetReq('phpfile')?GetReq('phpfile'):($mylocation?$mylocation:str_replace($lan.'.php','.php',$_GET['turl']));
//$preview = $_POST['selected_phpfile'] ? urlencode(base64_encode($_POST['selected_phpfile'])) :  urlencode(base64_encode($location));  

//location ? correction
/*if (stristr($location,'?')==false)
  $location .= '?'; //extend to ? if not exist

$savelocation = $location . 'editmode=1';
$cp = 'cp.php';
*/ 
 
  //dpc code editor....
  function show_editor() {
	global $encoding;
	
  
	if (($preview = GetReq('preview')) || ($reload = GetReq('reload'))) {
	  //preview mode..show page, 2nd load for cleaning error-log
	  
	  //echo $preview,'>';
	  if ($reload) {//2nd load show cleaned error-log
	    if (is_readable('../error_log'))
	      $ret .= nl2br(file_get_contents('../error_log'));
		else
          $ret .= 'No errors';		
	  }	
	  elseif (is_readable('../error_log')) {
	    $ret .= 'cleaning error-log...';
	    unlink('../error_log');	  
	  }
	  die();
	}	

	if ($turl = $_GET['turl']) {
	  //echo $turl,'>';
  
	  /*if (stristr($turl,'.php')) {//in case of already decoded name
		$lan = getlocal()?getlocal():'0';
		$url = str_replace($lan.'.php','.php',$turl);//extract lan digit
		if (!is_readable('../'.$url))
		die($url .', Not a relative code file.');
	  }	
	  else	*/
		$url = urldecode(base64_decode($turl));
	
	  //echo $url;
	  $mypage = parse_url($url);
    }  
	
    if (!$tempname = $_POST['filetemp']) {
		//print_r($_POST);  
		$pfile = $_POST['selected_phpfile'] ? $_POST['selected_phpfile'] :($mypage ? $mypage['path'] : 'index.php');
	         //$_POST['selected_phpfile'] ? $_POST['selected_phpfile'] : $mypage['path'];

		$htmlfile = getcwd() . '/../'. $pfile;//$mypage['path'];
		//echo $htmlfile,'-------------<br>';
		$ret .= "<h2>$pfile</h2>";
	 
		$p = explode('/',$htmlfile);
		$publichtmlfile = 'html/' . array_pop($p);
		$tempname = '../modify_dpc.tmp';//_'.time().'.tmp';

		copy($htmlfile,$tempname);
		//echo $tempname;
		$ret .= render(getcwd() . '/'. $tempname,$htmlfile);
	
	}
	else {//save
		//echo $tempname;
		$htmlfile = $_POST['file2saveon']; 
		savefile($tempname,1);    

		$ret .= render($tempname,$htmlfile);
	} 
  
	return $ret;
  } 
  
 
 //html.... 
 function show_selections() {
    global $encoding;

	$htmlfile = GetReq('htmlfile');
	$location = '../'.urldecode(base64_decode($_GET['turl']));  
	$phpfile = $_POST['selected_phpfile'] ? urlencode(base64_encode($_POST['selected_phpfile'])) : (GetReq('phpfile') ? GetReq('phpfile') : $location) ;
	$preview = $_POST['selected_phpfile'] ? urlencode(base64_encode($_POST['selected_phpfile'])) :  urlencode(base64_encode($location));  
    //echo '>>>>>',$_POST['selected_phpfile'],base64_decode(urldecode($preview)),base64_decode(urldecode($htmlfile));

	$ret .= '<span>';
	////$ret .= "<a href='$location&editmode=1' target='mainFrame'>[Preview]</a>"."&nbsp;";
	//$ret .= "<a href='cpmhtmleditor.php?htmlfile=$htmlfile&editmode=1&encoding=$encoding&phpfile=$phpfile&preview=$preview' target='mainFrame'>".localize('_previewpage',$lan)."</a>"."&nbsp;";
	//$ret .= "<a href='cpmhtmleditor.php?htmlfile=$htmlfile&editmode=1&encoding=$encoding&phpfile=$phpfile' target='mainFrame'>".localize('_editpage',$lan)."</a>"."&nbsp;";
 
	//$ret .=  GetGlobal('controller')->calldpc_method("rctedit.show_template_files use Edit+++cpmhtmleditor.php?encoding=".$encoding."&htmlfile=+mainFrame");
 
	$ret .= "<a href='?t=cptnew&editmode=1' target='mainFrame'>[New page]</a>"."&nbsp;";
	$ret .= "<a href='cpmctrl.php?t=cptnewpage&editmode=1&turl=$turl' target='mainFrame'>".localize('_addpage',$lan)."</a>"."&nbsp;";	
	$ret .= "<a href='cpmctrl.php?t=cptnewcopy&editmode=1&turl=$turl' target='mainFrame'>".localize('_copypage',$lan)."</a>"."&nbsp;";			 
	$ret .= "<a href='?t=cptnew&editmode=1' target='mainFrame'>[New project]</a>"."&nbsp;";
	$ret .= "<a href=\"javascript:top.location.href='".$location ."&editmode=-1'\">[Exit]</a>"."&nbsp;";
	
	//dpcdac
	//$ret .= "<a href='cpmdpcdac.php?t=cppostscript&encoding=$encoding&phpfile=$phpfile' target='mainFrame'>".localize('_dpcdac',$lan)."</a>"."&nbsp;";
	//....
 
	$ret .= '</span>';
 
    return ($ret);
 }
 
 $phpeditor = show_editor();
 //print_r($_POST);
 $selections =  show_selections();
 
 echo $selections .	'<hr/>'. $phpeditor;
 
 /*
 echo '<hr/>';
 //echo 'POSTKeys:',implode(",",array_keys($_POST)),'<br>'; 
 //echo 'POSTVals:',implode(",",$_POST),'<br>'; 
 echo 'POST:<pre>';
 print_r($_POST);
 echo '</pre>';

 //echo 'GETKeys:',implode(",",array_keys($_GET)),'<br>'; 
 //echo 'GETVals:',implode(",",$_GET),'<br>';
 echo 'GET:<pre>';
 print_r($_GET);
 echo '</pre>'; 

 //echo 'SESKeys:',implode(",",array_keys($_SESSION)),'<br>'; 
 //echo 'SESVals:',implode(",",$_SESSION),'<br>';
 echo 'SESSION:<pre>';
 print_r($_SESSION);
 echo '</pre>';  

 //echo 'COOKeys:',implode(",",array_keys($_COOKIE)),'<br>'; 
 //echo 'COOVals:',implode(",",$_COOKIE),'<br>';
 echo 'COOKIES:<pre>';
 print_r($_COOKIE);
 echo '</pre>';  

 */
  

?>
</body>
</html>



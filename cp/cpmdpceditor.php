<?php
require_once('dpc/system/pcntlhtml.lib.php'); 
$page = &new pcntl('
super rcserver.rcssystem;
super database;
load_extension adodb refby _ADODB_; 
include networlds.clientdpc;
public rc.rclogo;
',1);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Dpc Editor</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
/*		
		editAreaLoader.init({
			id: "example_2"	// id of the textarea to transform	
			,start_highlight: true
			,allow_toggle: false
			,language: "en"
			,syntax: "html"	
			,toolbar: "search, go_to_line, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, |, help"
			,syntax_selection_allow: "css,html,js,php,python,vb,xml,c,cpp,sql,basic,pas,brainfuck"
			,is_multi_files: true
			,EA_load_callback: "editAreaLoaded"
			,show_line_colors: true
		});
		
		editAreaLoader.init({
			id: "example_3"	// id of the textarea to transform	
			,start_highlight: true	
			,font_size: "8"
			,font_family: "verdana, monospace"
			,allow_resize: "y"
			,allow_toggle: false
			,language: "fr"
			,syntax: "css"	
			,toolbar: "new_document, save, load, |, charmap, |, search, go_to_line, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight, |, help"
			,load_callback: "my_load"
			,save_callback: "my_save"
			,plugins: "charmap"
			,charmap_default: "arrows"
				
		});
		
		editAreaLoader.init({
			id: "example_4"	// id of the textarea to transform		
			//,start_highlight: true	// if start with highlight
			//,font_size: "10"	
			,allow_resize: "no"
			,allow_toggle: true
			,language: "de"
			,syntax: "python"
			,load_callback: "my_load"
			,save_callback: "my_save"
			,display: "later"
			,replace_tab_by_spaces: 4
			,min_height: 350
		});
*/		
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
<?php
  if ($preview = GetReq('preview')) {
      $_preview = urldecode(base64_decode($preview));
      if (!stristr($_preview,'?')) 	  
	     $_preview .= '?';
	  
	  echo "
	    function load_dpcdac()
	    {
          top.mainFrame.location='$_preview&editmode=1';
		  //alert('$_preview');		  
		  //self reload
		  location='cpmdpceditor.php?reload=1';
	    }	  
";	

  }		
  elseif ($phpfile2saveon = GetParam('file2saveon')) {  //when post exists....
      $p = explode('/',$phpfile2saveon);
      $phpfile = array_pop($p);	  
	  echo "
	    function load_dpcdac()
	    {
          top.rightFrame.location='cpmdpcdac.php?t=cppostscript&encoding=" . GetReq('encoding') . "&phpfile=" . $phpfile ."';
	    }	  
";	

  }
  else {
	  echo "
	    function load_dpcdac()
	    {
			//null function
	    }	  
"; 
  }  
?>	
	</script>

<SCRIPT language="JavaScript">
function submitform()
{
  document.dpcform.submit();
}
</SCRIPT> 
</head>
<body onLoad="load_dpcdac()" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php

if (GetSessionParam('LOGIN')==='yes') {

  function render($file,$targetfile) {
   
	  $mydata = file_get_contents($file); 	 
	  $out = "\n<form name=\"dpcform\" action=\"".$_SERVER['PHP_SELF'].'?turl='.$_GET['turl'].'&encoding='.$_GET['encoding']."\" method=\"post\">";  
	 
	  $out .= '<div>'; 
          $out .= "\n <textarea wrap='virtual' id='htmleditor' name='htmltext' style='width: 100%' rows='10' autowrap>$mydata</textarea>";	 
	  $out .= '</div>';
	 
	  $out .= "<input type=\"submit\" name=\"ok\" value=\"  submit  \" />";	  
	  $out .= "<input type=\"hidden\" name=\"file2saveon\" value=\"$targetfile\" />";	  
          $out .= "<input type=\"hidden\" name=\"filetemp\" value=\"" . $file . "\" />";	  


	  $out .= "</form>";
	  
     //$out .= $file.'>'.$targetfile; 
	 return ($out); 
    }	
	
	//save backup theme in cp
	function save_theme($filename=null) {
	    $prpath = paramload('SHELL','prpath');
		//global $htmlfile;  
		$filename = $_POST['file2saveon']; //holds realname
		
	    if (!$filename)
		  return null;
		  
		  
		$current_skin = @file_get_contents($prpath."/theme.skin");   
		$themes_dir = $prpath . '/themes';
		$skin_dir = $themes_dir . '/' . $current_skin;
		$pages_dir = $skin_dir . '/pages';
        if (!is_dir($theme_dir)) mkdir($themes_dir);		
		if (!is_dir($skin_dir)) mkdir($skin_dir);
		if (!is_dir($pages_dir)) mkdir($pages_dir);
		
		$file2save = $pages_dir . '/' . array_pop(explode('/',$filename)); //fullpath extract...
		$ret = copy($filename,$file2save);
		
		return ($ret);
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
         //SAVE BACKUP TO LOCAL CP THEME DIR			
         save_theme($filename);		 

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
	
  if (($preview = GetReq('preview')) || ($reload = GetReq('reload'))) {//preview mode..show page, 2nd load for cleaning error-log
	  //echo $preview,'>';
	  if ($reload) {//2nd load show cleaned error-log
	    if (is_readable('../error_log'))
	      echo nl2br(file_get_contents('../error_log'));
		else
          echo 'No errors';		
	  }	
	  elseif (is_readable('../error_log')) {
	    echo 'cleaning error-log...';
	    unlink('../error_log');	  
	  }
	  die();
  }	

  $turl = $_GET['turl'];
  //echo $turl,'>';

  if (stristr($turl,'.php')) {//in case of already decoded name
    $lan = getlocal()?getlocal():'0';
    $url = str_replace($lan.'.php','.php',$turl);//extract lan digit
	if (!is_readable('../'.$url))
	  die($url .', Not a relative code file.');
  }	
  else	
    $url = urldecode(base64_decode($turl));
	
  //echo $url;
  $page = parse_url($url);

  if (!$tempname = $_POST['filetemp']) {

    $htmlfile = getcwd() . '/../'. $page['path'];

    $p = explode('/',$htmlfile);
    $publichtmlfile = 'html/' . array_pop($p);
    $tempname = '../modify_dpc.tmp';//_'.time().'.tmp';

    copy($htmlfile,$tempname);
    //echo $tempname;
    echo render(getcwd() . '/'. $tempname,$htmlfile);
	
  }
  else {//save
    //echo $tempname;
    $htmlfile = $_POST['file2saveon']; 
    savefile($tempname,1);    

    echo render($tempname,$htmlfile);
  }
  
}  
?>
</body>
</html>

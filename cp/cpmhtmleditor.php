<?php
require_once('dpc/system/pcntl.lib.php'); 
$page = &new pcntl('
super javascript;
super rcserver.rcssystem;
load_extension adodb refby _ADODB_; 
super database;
use xwindow.window,xwindow.window2;
include networlds.clientdpc;
use gui.swfcharts;
/use libs.dhtml;
/public rc.rclogo;
private frontpage.fronthtmlpage /cgi-bin;
#ifdef SES_LOGIN
private shop.rckategories /cgi-bin;
private shop.rcitems /cgi-bin;
private shop.rctags /cgi-bin;
private cp.cpmhtmleditor /cgi-bin;
#endif
private phpdac.rccontrolpanel /cgi-bin;
',1);
	
$cptemplate = GetGlobal('controller')->calldpc_method('rcserver.paramload use FRONTHTMLPAGE+cptemplate');
//echo $cptemplate,'>';

if ($cptemplate) {

	switch ($_GET['t']) {
		case 'cpmvphoto' : $p = 'cp-mvphoto'; break;
		case 'cpmvdel'   : $p = 'cp-mvphoto'; break;
		default          : $p = ($_POST['insfast'] ? 'cp-uploadimage' : 'cp-htmleditor');
	}
    $mc_page = (GetSessionParam('LOGIN')) ? $p : 'cp-login';
	echo $page->render(null,getlocal(), null, $cptemplate.'/index.php');
}
else
	echo $page->render(null,getlocal(),null,'cp_em.html');
	
die();	

$encoding = $_GET['encoding']?$_GET['encoding']:'utf-8';
//echo '>',$encoding;
$prpath = paramload('SHELL','prpath');
$tmpl_path = remote_paramload('FRONTHTMLPAGE','template',$prpath);
$template = $tmpl_path ? $tmpl_path .'/' : null;

$one_attachment = remote_paramload('SHKATALOG','oneattach',$prpath);
$lan = getlocal();

if ($one_attachment) 
  $slan = null;
else
  $slan = $lan?$lan:'0';

//save editable file
$htmlfile = urldecode(base64_decode($_GET['htmlfile']));
//echo $htmlfile;
/*if php editable file extend template path to pages path*/
$template .= stristr($htmlfile,'.php') ? 
             (stristr($htmlfile,'index.php') ? null :'pages/') : 
			 null;

//ckeditor 4
//$ckeditor4 = GetReq('cke4') ? GetReq('cke4') : false;
$ckeditor4 = true;//((GetReq('cke4'))||($template)) ? /*true*/false : false; //<<<<
$cke4_inline = $ckeditor4 ? true/*false*/ : false; 
$ckjs = $ckeditor4 ? "ckeditor4/ckeditor.js" : "ckeditor/ckeditor.js";

//$x = new dhtml();
//$x->open_dialog('test',"iframe", "$bottomframe_url", "Console");

//save ck4 partial ajax call
if ((GetReq('savepart')) && ($file=GetParam('file'))) {

    //$p = explode('/',$htmlfile);
	//$fa = array_pop($p);
	
	//$file = GetParam('file');
	
    $mypartialfile = getcwd() . '/html/' . $template . $file;
	$data = GetParam('data') ? unload_spath(GetParam('data')) : '';
	//$old_data = GetParam('olddata') ? unload_spath(GetParam('olddata')) : '';
	
	//$mydfile = getcwd() . '/html/pdata.part';
	$myofile = getcwd() . '/html/'.$template.'podata.part';
     
    /*if ($old_data) {//keep initial data
	
	   //check if data to save exists
	   //if ($odata = @file_get_contents($myofile)) {
	   //}
	   
       @file_put_contents($myofile,$old_data);	   
	} */  
	   
    if ($data) { //save modified data
	   $message = null;
       //@file_put_contents($mydfile,$data);	 

	   //if olddata and dif save...
       if (($odata = @file_get_contents($myofile)) && (strlen($odata)!=strlen($data))) {
	       //keep backup
		   $b = @copy($mypartialfile, str_replace(array('.htm','.php'),array('._htm','._php'),$mypartialfile));  
	       //save it
		   //$str = htmlentities($str, ENT_COMPAT, "UTF-8");
		   $r = @file_put_contents($mypartialfile, str_replace($odata, $data, @file_get_contents($mypartialfile), $counter));
		   
		   //remove compare file
		   @unlink($myofile);
		   
		   $message = $counter ? 'Saved ('.$file .')!' : 'Error: Not Saved ('.$file .')!';
       }
       else //save data to compare it...
	       @file_put_contents($myofile,$data);	
	}   
	
    //$message .= $data ?	$data.' Saved ' : null; //null when olddata
	die($message);
}
elseif (($mc_page=GetReq('mc_page')) /*&& ($file=GetParam('file'))*/) {

	if ($turl=urldecode(base64_decode($_GET['turl']))) {
		$pt = explode('?',$turl);
		parse_str($pt[1], $args);
    }			
	$id = $args['id'] ? $args['id'] : ($args['cat'] ? $args['cat'] : str_replace('.php','',$pt[0]));
    $ret = GetGlobal('controller')->calldpc_method("fronthtmlpage.mcSavePage use $id+$mc_page+");
	//die($ret);
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Html Editor</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?echo $encoding ?>">

<!--link rel="stylesheet" href="dhtmlwindow1.css" type="text/css" />
<script type="text/javascript" src="dhtmlwindow1.js"></script--> 
<script type="text/javascript" src="http://www.stereobit.gr/<?echo $ckjs ?>"></script>	
<script language="Javascript" type="text/javascript">
<?php
  $_url= "cpmdpceditor.php?turl=" . urlencode(base64_encode($turl));

  if ($cke4_inline) {
       echo "
	   
function createRequestObject() {
    var ro;
    var browser = navigator.appName;
    if(browser == \"Microsoft Internet Explorer\"){
        ro = new ActiveXObject(\"Microsoft.XMLHTTP\");
    }else{
        ro = new XMLHttpRequest();
    }
    return ro;
}

var http = createRequestObject();

function sndUrl(url) {
    http.open('get', url+'&ajax=1');
    http.onreadystatechange = handleResponse;
    http.send(null);
}

function sndReqArg(url) {
    var params = url+'&ajax=1';

    http.open('post', params, true);
    http.setRequestHeader(\"Content-Type\", \"text/html; charset=utf-8\");
    //http.setRequestHeader(\"charset\", \"utf-8\");
    http.setRequestHeader(\"encoding\", \"utf-8\");	
    //http.setRequestHeader(\"Content-length\", params.length);	
    //http.setRequestHeader(\"Connection\", \"close\");	
    http.onreadystatechange = handleResponse;	
    http.send(null);
}

function handleResponse() {
    if(http.readyState == 4){
        //var response = http.responseText;
        //trim response
        //response = response.replace( /^\s+/g, \"\" ); // strip leading 
        //response = response.replace( /\s+$/g, \"\" ); // strip trailing
        
        if (response=http.responseText) {		
		
          alert(response); 
		  
		  //reload page after save...
		  window.location.reload();
		}  
    }
}
   
	   
function save_inline_data(data, oldData){
	
	//alert(data+'--'+oldData);
	
	//ajax call to replace part of conetent with the updated partial text submited here...
	//JSON.stringify(data)
	//sndReqArg('cpmhtmleditor.php?savepart=1&data='+escape(data)+'&olddata='+escape(oldData));	
	sndReqArg('cpmhtmleditor.php?savepart=1&file=$htmlfile&data='+escape(data));
}

/*function save_init_data(data){
	
	//alert('INIT:'+data);
	
	//ajax call to replace part of conetent with the updated partial text submited here...
	//JSON.stringify(data)
	sndReqArg('cpmhtmleditor.php?savepart=1&file=$htmlfile&olddata='+escape(data));	
}*/

/*
document.addEventListener('keydown', function (event) {
  var esc = event.which == 27,
      nl = event.which == 13,
      el = event.target,
      input = el.nodeName != 'INPUT' && el.nodeName != 'TEXTAREA',
      data = {};

  if (input) {
    if (esc) {
      // restore state
      document.execCommand('undo');
      el.blur();
    } 
	else if (nl) {
      // save
      //data[el.getAttribute('data-name')] = el.innerHTML;

      // we could send an ajax request to update the field
      //log(JSON.stringify(data));
	  save_inline_data(data);

      el.blur();
      event.preventDefault();
    }
  }
}, true);
*/
";	   
  }  
?>
</script>  
</head>

<body onLoad="" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="">

<?php

  function render_inline($file=null,$tempfile=null,$id=null,$type=null) {
      global $ckeditor4;
	  if (!$ckeditor4) 
	     return (render($file,$tempfile,$id,$type));//default rendering
		 
      $isTemplate = true;
   	  
	  if (isset($_POST['htmltext'])) {//??.....NEVER HERE ..AJAX
        savefile($file,null);		 
		$mydata = unload_spath(file_get_contents($file));//$_POST['htmltext'];		 
	  }
	  else {//load
		if (($file) && is_readable($file)) {
			$mydata = load_spath(file_get_contents($file));
		}  
		else
		    $mydata = 'File not exist!' . " ($file)";
	  }
	  
	  //is template file ?....RETURN TO NO INLINE MODE.....
	  //if (stristr($mydata,'!DOCTYPE html')) {
	  if (substr($mydata,0,8)=='<!DOCTYPE') {
	    $isTemplate = false;
		return (render($file,$tempfile,$id,$type));//default rendering...
	  }	
      //else continue...
	  
	  //html body MUST has editable tags inside else default editing
	  if (!stristr($mydata, 'contenteditable')) {
	      return (render($file,$tempfile,$id,$type));//default rendering...
	  }
	
	  //html body MUST has editable tags inside...enable it..disbale it when save...
	  $out = str_replace('contenteditable="false"','contenteditable="true"',$mydata);
	  
	  //js script
	  $out.= "<script type='text/javascript'>"; 
      $out.= "CKEDITOR.on( 'instanceCreated', function( event ) {
			  var editor = event.editor,
			  element = editor.element;

			  if ( element.is( 'h1', 'h2', 'h3' ) || element.getAttribute( 'id' ) == 'taglist' ) {
				editor.on( 'configLoaded', function() {

					// Remove unnecessary plugins to make the editor simpler.
					editor.config.removePlugins = 'colorbutton,find,flash,font,' +
						'forms,iframe,image,newpage,removeformat,' +
						'smiley,specialchar,stylescombo,templates';

					// Rearrange the layout of the toolbar.
					editor.config.toolbarGroups = [
						{ name: 'editing', groups: [ 'basicstyles', 'links' ] },
						{ name: 'undo' },
						{ name: 'clipboard', groups: [ 'selection', 'clipboard' ] },
						{ name: 'about' }
					];
				});
			  }	
			  
			  editor.on( 'blur', function( event ) {
				  var data = editor.getData();
				  save_inline_data(data);
			  });
			  editor.on( 'focus', function( event ) {
				  var data = editor.getData();
				  //save_init_data(data);
				  save_inline_data(data);
			  });			  
               
			  /*editor.on( 'instanceReady', function( event ) {
			      //var data = editor.getData();
			      //save_init_data(data);
				  //save_inline_data(data);
				  
				  periodicData();
			  });*/			  
			  
	var periodicData = ( function(){
    var data, oldData;

    return function() {
        if ( ( data = editor.getData() ) !== oldData ) {
		
			save_inline_data(data, oldData);
			
            oldData = data;
            //console.log( data );
        }

        setTimeout( periodicData, 1000 );
    };
})();
			  
		});			
"; 		     
	  $out .= "</script>"; 
	  
      return ($out);
  }

  function render($file=null,$tempfile=null,$id=null,$type=null) {
      global $ckeditor4;
      $isTemplate = true; //$_GET['istemplate']?$_GET['istemplate']:false;
	  $insfast = GetReq('insfast');
      
	  if (isset($_POST['insfast'])) { //fast item insert
	    //echo $_POST['title'],$_POST['tags'];
		$title = GetParam('title');
		
		if (($id) && ($type) && ($title)) { 
			$code = str_replace(' ','-',$title);
			$tags = GetParam('tags') ;//as come from post ...str_replace(' ',',',GetParam('tags'));
			$text = GetParam('htmltext');
			$descr = substr(trim(strip_tags($text)),0,250).'...';
		    $category = $id;
			
			$save_attachment = GetGlobal('controller')->calldpc_method("rcitems.add_attachment_data use ".$code."+". $type."+".$text."+1");		
			$save_tags = GetGlobal('controller')->calldpc_method("rctags.add_tags_data use ".$code."+". $title."+".$descr."+".$tags);		
			$save_cat = GetGlobal('controller')->calldpc_method("rckategories.add_kategory_data use ".$category);		
			$save_item = GetGlobal('controller')->calldpc_method("rcitems.add_item_data use ".$code."+". $title."+".$descr."+".$category);		
			
			if (isset($_POST['htmltext'])) {
			    $mytext = str_replace(' use','&nbsp;use',str_replace('+','<SYN>',unload_spath(str_replace("'","\'",$_POST['htmltext'])))); //!!!!!!!!!!!!!!
				$save = GetGlobal('controller')->calldpc_method("rcitems.add_attachment_data use ".$code ."+". $type."+".$mytext);		 
				$mydata = GetGlobal('controller')->calldpc_method("rcitems.has_attachment2db use " . $code ."+$type+1"); 			
			}
		}	
	  }
	  elseif (isset($_POST['htmltext'])) {
         if (($id) && ($type)) { //db
		    //echo 'post load from db';		 
	        $mytext = str_replace(' use','&nbsp;use',str_replace('+','<SYN>',unload_spath(str_replace("'","\'",$_POST['htmltext'])))); //!!!!!!!!!!!!!!
	        $save = GetGlobal('controller')->calldpc_method("rcitems.add_attachment_data use ".$id ."+". $type."+".$mytext);		 
		    $mydata = GetGlobal('controller')->calldpc_method("rcitems.has_attachment2db use " . $id ."+$type+1"); 
         }
         else {//text
		    //echo 'post load from post';
            savefile($file,null);		 
		    $mydata = file_get_contents($file);//$_POST['htmltext'];
         }		 
	  }
	  else {//load
         if (($id) && ($type)) { //db
		    //echo 'load from db:',$id,$type;
		    $mydata = GetGlobal('controller')->calldpc_method("rcitems.has_attachment2db use " . $id ."+$type+1"); 
			//echo '>',$mydata;
         }
         else {//text
		    //echo 'load from file'; 
			if (($file) && is_readable($file)) {
				$mydata = file_get_contents($file);
			}  
			else
				$mydata = 'File not exist!' . " ($file)";			
         }	  
	  }
	  
	  //is template file ?....
	  //if (stristr($mydata,'!DOCTYPE html')) //not <html> due to extra html defintions
	  if (substr($mydata,0,8)=='<!DOCTYPE') 
	    //echo substr($mydata,0,8);
	    $isTemplate = false;
	  
	  $purl = $_SERVER['PHP_SELF'].'?encoding='.$_GET['encoding'].'&htmlfile='.$_GET['htmlfile'];
	  //echo $purl;
	  if (!$_POST['insfast']) {	//hide when post fast
		$out = "<form name=\"htmlform\" action=\"".$purl."\" method=\"post\">";  
	  }	
	  
	  $ckattr = $ckeditor4 ?
	           "fullpage : true, 
               filebrowserBrowseUrl : '/cp/ckfinder/ckfinder.html',
	           filebrowserImageBrowseUrl : '/cp/ckfinder/ckfinder.html?type=Images',
	           filebrowserFlashBrowseUrl : '/cp/ckfinder/ckfinder.html?type=Flash',
	           filebrowserUploadUrl : '/cp/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
	           filebrowserImageUploadUrl : '/cp/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
	           filebrowserFlashUploadUrl : '/cp/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash',
	           filebrowserWindowWidth : '1000',
 	           filebrowserWindowHeight : '700'"	  
	           : 
	           "skin : 'office2003', 
			   fullpage : true, 
			   extraPlugins :'docprops',
               filebrowserBrowseUrl : '/cp/ckfinder/ckfinder.html',
	           filebrowserImageBrowseUrl : '/cp/ckfinder/ckfinder.html?type=Images',
	           filebrowserFlashBrowseUrl : '/cp/ckfinder/ckfinder.html?type=Flash',
	           filebrowserUploadUrl : '/cp/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
	           filebrowserImageUploadUrl : '/cp/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
	           filebrowserFlashUploadUrl : '/cp/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash',
	           filebrowserWindowWidth : '1000',
 	           filebrowserWindowHeight : '700'";
	 
	  $out .= '<div>'; 
      $out .= "<textarea id='htmltext' name='htmltext'>".load_spath($mydata)."</textarea>";	
	  $out .= "<script type='text/javascript'> 
	           CKEDITOR.replace('htmltext',
			   {
               $ckattr		   
			   }		   
			   );";
	
      if ($isTemplate==false)	
	  $out .= "			   
	           CKEDITOR.config.fullPage=true;";		   
		
      $maximize = $insfast ? ($_POST['insfast'] ? 'maximize' : 'minimize' ) : 'maximize';		
	  $out .= "		   
               CKEDITOR.config.entities = false;
               CKEDITOR.config.entities_greek = false;
               CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;			   
               CKEDITOR.on('instanceReady',
               function( evt )
               {
                  var editor = evt.editor;
                  editor.execCommand('$maximize');
               });
			   </script>"; 
	  $out .= '</div>';
      //extraPlugins : 'stylesheetparser',enterMode : CKEDITOR.ENTER_DIV/ENTER_BR/ENTER_P
	
      if (!$_POST['insfast']) {	//hide when post fast
		$mytempfile = GetParam('tempfile')?	GetParam('tempfile') : $tempfile;	   
		$myfile = GetParam('file')?	GetParam('file') : $file;	
		$myid = GetParam('id')?	GetParam('id') : $id;	
		$mytype = GetParam('type')?	GetParam('type') : $type;	
	 
		$out .= "<input type=\"submit\" name=\"ok\" value=\"  submit  \" />";	  
		$out .= "<input type=\"hidden\" name=\"file2saveon\" value=\"" . $myfile . "\" />";	  
		$out .= "<input type=\"hidden\" name=\"filetemp\" value=\"" . $mytempfile . "\" />";	 
		$out .= "<input type=\"hidden\" name=\"id\" value=\"" . $myid . "\" />";	 
		$out .= "<input type=\"hidden\" name=\"type\" value=\"" . $mytype . "\" />";		   

		//insert item fast
		if ($insfast) {
			$out .= "Title:<input type=\"text\" name=\"title\" value=\"my title\" />";
			$out .= "Tags:<input type=\"text\" name=\"tags\" value=\"".str_replace(array(' ','_','-'),array(',',' ',' '),$myid)."\" />";
			$out .= "<input type=\"hidden\" name=\"insfast\" value=\"" . $insfast . "\" />";		   	  
		}	  
		
		$out .= "</form>";
	  }//post fast hide
	  elseif ($_POST['insfast']) { //post fast seccond step, add photo
	    //echo 'add_photo:'.$code.'>'.$category;
		if (defined('RCITEMS_DPC') && (($code)||($category))) {	
			$out .= GetGlobal('controller')->calldpc_method('rcitems.form_photo use 1+'.$category.'+'.$code.'+cpitems');
		}		
	  }

      //$out .= $file.':'.$targetfile;
      
	  return ($out); 
    }	

    function savefile($filename=null,$tempfile=null) {
         //echo $filename;
		 
	     /////////////////////////////////////////////////////////////
		 
	     if (GetSessionParam('LOGIN')!='yes') 
		   die("Not logged in!");
		   
	     /////////////////////////////////////////////////////////////			 

         //if ($_POST['ok']) { //CKEDITOR FULL SCREEN SAVE BUTTON ....NO NEED
			
            write2disk($filename,$_POST['htmltext']); //save temp
             
            if ($tempfile)
              write2disk($tempfile,$_POST['htmltext']); //save original
			  

         //}
    }

    function remove_spchars($text=null) {
	   //if ckfinder
	   //return ($text);	

       $p1 = str_replace("\'","'",$text);
       $p2 = str_replace('\"','"',$p1);

       return $p2;

    }

    function handle_phpdac_tags($text,$savemode=null) {

      if ($savemode==null) {//load
       $p1 = str_replace("<?","<phpdac5>",$text);
       $p2 = str_replace('?>','</phpdac5>',$p1);
      }
      else {//save mode
       $p1 = str_replace("<phpdac5>","<?",$text);
       $p2 = str_replace('</phpdac5>','?>',$p1);
      }

      return $p2;
    }

    function load_spath($text=null) {
	   //if ckfinder
	   //return ($text);

       $p1 = str_replace("images/","../images/",$text);

       $ret = handle_phpdac_tags($p1);
       return ($ret);

    }

    function unload_spath($text=null) {
	   //if ckfinder
	   //return ($text);	

       $p1 = str_replace("../images/","images/",$text);

       $ret = handle_phpdac_tags($p1,1);
       return ($ret);

    }

    function write2disk($file,$data=null) {

	    $indata = remove_spchars(unload_spath($data));
		//keep a backup
		@copy($file, str_replace(array('.htm','.php'),array('._htm','._php'),$file));
		
        if ($fp = @fopen ($file , "w")) {
	        //echo $file,"<br>";
            fwrite ($fp, $indata);
            fclose ($fp);

            return true;
        }
        else {
            echo "File creation error ($file)!<br>";
        }
        return false;

    }

 	
	
	if (!empty($_POST)) {
      //echo 'post....';	
      if (($id = GetParam('id')) && ($type = GetParam('type'))) {	
	     echo render(null,null,$id,$type);
      }
	  elseif ($myfile = GetParam('file2saveon')) {
	     //if ($cke4_inline)
		   // echo render_inline($myfile,null);	  	  
		 //else	  
	        echo render($myfile,null);
	  }
	}
	else {
      //echo 'load....';	
      if (($id = GetReq('id')) && ($type = GetReq('type'))) {	
	     echo render(null,null,$id,$type);
      }
	  elseif ($htmlfile) {
        $p = explode('/',$htmlfile);
	    $fa = array_pop($p);
        $myfile = getcwd() . '/html/' . $template .  $fa;
        //$tempname = getcwd() . '/modify_html.tmp';
	  
	    if ($cke4_inline)
		   echo render_inline($myfile,null);	  	  
		else
           echo render($myfile,null);	  	  
      } 
    }
   

?>
</body>
</html>
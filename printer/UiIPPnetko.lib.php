<?php

require_once("UiIPP.php");

class UiIPPnetko extends UiIPP {
	
	function __construct($printer=null, $auth=null, $printers_url=null, $externaluse=null, $procmd=null) {   	   
							    
	    parent::__construct($printer,$auth,$printers_url,$externaluse,$procmd);
	   
	}
	
	//override
	public function form_configprinter($printername=null, $indir=null) {
	    $printername = $name ? $name : $this->printer_name;
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
        $cmd = $this->external_use ? $this->procmd.'confprinter':'confprinter';		
		$handlers = array();
		$params = array();
		//echo $printername,'...',$indir,'...';
		
	    if ($this->username!=$this->get_printer_admin()) 
		   return ('Not allowed!');		

        if (!$printername) 
		  return ('Unknown printer!');	  
		  
		//$ret = $this->html_printer_menu(true);		
		
        if (($filter=$_POST['filter']) || ($filter=$_GET['filter'])) {
		  $code = $_POST['filtercode'];
		  $ret .= $this->config_filter_select_form($filter,$printername,$code,$printerdir);
		  return ($ret);		
		}			
		
		//read conf file
		$pr_config = $this->parse_printer_conf($printername,$printerdir);
		//print_r($pr_config);
		if (empty($pr_config))
		  return ('Invalid configuration!');		

        if ((!empty($pr_config['SERVICES'])) && ($handlers = $pr_config['SERVICES'])) {
		
		    if (is_array($pr_config['PARAMS'])) {
		        $apply_services_method = $pr_config['PARAMS']['services']; 
		        if ($apply_services_method == 'must') {
		            //sort by value =1,2,3,4...
		            asort($handlers);
		        }
				
				$file_output = $pr_config['PARAMS']['foutput'];
				
				$params['method'] = $apply_services_method;
				$params['output'] = $file_output;
            }			
		    //print_r($handlers);
		    foreach ($handlers as $service=>$is_on) {
			
			    if ($is_on>0) 
				   $params['handlers'][] = $service . ':'.$is_on;
                else
				   $params['handlers'][] = $service . ':disabled';
				   				
			}
		}
		
		if ($_POST['FormAction']!=$cmd) {
		  
		  $ret .= $this->config_printer_form($msg,$printername,$params,$printerdir);
		  return ($ret);
		}
		
		//read new values while saving...
		$params = array();
		
        //save conf file
		//print_r($_POST);
		$file = "
[SERVICES]";		
        for ($i=1;$i<=10;$i++) {
		   $service = $_POST['handler'.$i]; 
		   $hdval = $_POST['index'.$i]!='disabled' ? $_POST['index'.$i] : null;
		   if ($service) {
		     $srv = $service . ':';
			 $srv .= isset($hdval) ? $hdval : 'disabled'; 
		     $params['handlers'][] = $srv;
		     $file .= "
$service=";
             $file .= ($hdval) ? "$hdval" : ";";
           }
        }	

        $params['method'] = $method = $_POST['filters_method'];		
		$params['output'] = $output = $_POST['filters_output'];
		
		$file .= "
		
[PARAMS]
services=$method
output=$output		
";			
        //echo $file;	
        $msg = 	$this->save_printer_conf($printername,$printerdir,$file);	
		
		//print_r($params);		
		$msg = null;//$ok ? 'Saved' : 'Failed to save!';
		$ret .= $this->config_printer_form($msg,$printername,$params,$printerdir);
		  
		return ($ret);	
    }		

    //override
	protected function config_printer_form($message=null, $name=null, $params=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;
		$hd_ui = null;
		$filters_method = $params['method'];
		$page = pathinfo($_SERVER['PHP_SELF'],PATHINFO_BASENAME);
		$edit_filter = $page.'?'.$this->cmd.'confprinter&filter=[Handler]';
		$cmd = $this->external_use ? $this->procmd.'confprinter':'confprinter';
		
	    $handler_fields = '
        <li id="li_4" >
		<!--label class="description" for="filter<@>">Filter <@> </label-->
		<span>
			<!--input id="element_<@>_1" name= "handler<@>" class="element text" maxlength="13" size="14" value="[Handler]"/-->
			<h2>Filter&nbsp;<a href="' . $edit_filter . '">[Handler]</a></h2>
			<!--label>Filter&nbsp;<a href="' . $edit_filter . '">Edit</a></label-->
		</span>
		<span>
			<!--input id="element_<@>_2" name= "index<@>" class="element text" maxlength="13" size="14" value="[Index]"/-->
			<h2>:[Index]</h2>
			<!--label>Value</label-->
		</span><p class="guidelines" id="guide_4"><small>Filter <@></small></p> 
		</li>		
';		

        $ji=1;
        if (!empty($params['handlers'])) {
		  foreach ($params['handlers'] as $fi=>$filter) {
		    //echo '>',$filter,'<br>';
		    $fp = explode(':',$filter);
		    $fname = $fp[0];
			$factive = $fp[1];
		    $myhfields = str_replace('[Handler]',$fname,str_replace('[Index]',$factive,$handler_fields)); 
		    $hd_ui .= str_replace('<@>',$ji,$myhfields);
		    $ji+=1;
		  }
		}
		//+until 3
        /*for ($i=$ji;$i<=3;$i++) {
		    $myhfields = str_replace('[Handler]','',str_replace('[Index]','',$handler_fields));
		    $hd_ui .= str_replace('<@>',$i,$myhfields);
		}*/	
	
	    $menu = $this->html_printer_menu(true);
	
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu
		<form id="form_470441" class="appnitro" enctype="multipart/form-data" method="post" action="">
					<div class="form_description">
			<h2>Printer filters $message</h2>
			<p>Add or modify printer behavior.</p>
		</div>						
			<ul >
			
		<!--li id="li_0" >
		<label class="description" for="element_0">Filter type </label>
		<div>
			<input id="element_0" name="filters_method" class="element text medium" type="text" maxlength="13" value="$filters_method"/> 
		</div><p class="guidelines" id="guide_1"><small>Filter apply method</small></p> 
		</li-->		

		$hd_ui
			
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="$cmd" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
		<div id="footer">
		$ver&nbsp;|&nbsp;$this->logout_url
		</div>
	</div>
	<br/>

EOF;
        return ($form);		
	}	

    //dropbox filter form
	protected function config_filter_form_dropbox($filter=null, $printername=null, $code=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;
	    $dir = $indir ? $indir.'/' : ($_SESSION['indir'] ? $_SESSION['indir'] .'/' : '/');
		$filter = $_POST['filtername'] ? $_POST['filtername'] : $filter;
		$cmd = $this->external_use ? $this->procmd.'confprinter':'confprinter';
		
		//$file = $_SERVER['DOCUMENT_ROOT'] .'/'.$dir . str_replace('.printer','',$printername).'.'.$filter.'.php';
		$file = self::get_printer_path() . str_replace('.printer','',$printername).'.'.$filter.'.php'; 
		//echo $file,'>',$code;
		
		$dropbox_ui = '
        <li id="li_1" >
		<label class="description" for="dropbox">Dropbox account</label>
		<span>
			<input id="element_1_1" name= "dbusername" class="element text" maxlength="50" size="20" value="[Username]"/>
			<label>Dropbox Username</label>
		</span>
		<span>
			<input id="element_1_2" name= "dbpassword" class="element text" maxlength="50" size="20" value="[Password]"/>
			<label>Dropbox Password</label>
		</span><p class="guidelines" id="guide_4"><small>Please provide your dropbox username and password</small></p> 
		</li>		
';			
        //echo 'code:',$code;
		//echo '>file:',$file;
        if (($user=$_POST['dbusername']) && ($pass=$_POST['dbpassword'])) {				

		  $db_code = "<?php\r\nrequire_once('dropbox/DropboxUploader.php');
\$myfile = \$this->jf . '.pdf';		 
\$uploader = new DropboxUploader('$user','$pass');
\$ret = \$uploader->upload(\$this->jf, '/printQueue', \$myfile);
if (\$ret) return true; 
?>\r\n		  
";
		  //save file...		  
		  @file_put_contents($file, $db_code);
		  
		  $dp_ui = str_replace('[Password]',$pass,str_replace('[Username]',$user,$dropbox_ui));
		}
		else {
		  //load file
		  //$cnt = file_get_contents($file);
		  if (is_readable($file)) {
		    $cnt = file($file,FILE_SKIP_EMPTY_LINES);
		    //scan for user pass...
		    $parts = explode("'",$cnt[3]);
		    $user = $parts[1];
		    $pass = $parts[3];
			
			$dp_ui = str_replace('[Password]',$pass,str_replace('[Username]',$user,$dropbox_ui));
		  }
		  else
		    $dp_ui = str_replace('[Password]','',str_replace('[Username]','',$dropbox_ui));
		}
		  
		$menu = $this->html_printer_menu(true);  

	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu 
		<form id="form_470441" class="appnitro" enctype="multipart/form-data" method="post" action="">
					<div class="form_description">
			<h2>Dropbox configuration $message</h2>
			<p>Modify dropbox configuration.</p>
		</div>						
			<ul >		

        $dp_ui	
			
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="$cmd" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
		<div id="footer">
		$ver&nbsp;|&nbsp;$this->logout_url
		</div>
	</div>
	<br/>

EOF;
        return ($form);		
	}
	
	//override
	protected function config_filter_form($filter=null, $printername=null, $code=null, $indir=null) {
	   $ver = $this->server_name . $this->server_version;	
	
       $menu = $this->html_printer_menu(true); 	
	
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu
		<h2>Undefined form</h2>
		<div id="footer">
		$ver&nbsp;|&nbsp;$this->logout_url
		</div>
	</div>	
	<br/>		
EOF;
		
	   return ($form);
	}

    //CUSTOM FORM PER FILTER	
	protected function config_filter_select_form($filter=null, $printername=null, $code=null, $indir=null) {	

	    if ($filter=='dropbox') {
		    //$form = 'dropbox';
		    $form = self::config_filter_form_dropbox($filter, $printername, $code, $indir);
	    }
	    else
		    //$form = parent::config_filter_form($filter, $printername, $code, $indir);
	        $form = self::config_filter_form($filter, $printername, $code, $indir);
	   
	   return ($form);
	}
}
?>
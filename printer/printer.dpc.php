<?php
$__DPCSEC['PRINTER_DPC']='1;1;1;1;1;1;1;1;1';

if ((!defined("PRINTER_DPC")) && (seclevel('PRINTER_DPC',decode(GetSessionParam('UserSecID')))) ) {
define("PRINTER_DPC",true);

$__DPC['PRINTER_DPC'] = 'printer';

$a = GetGlobal('controller')->require_dpc('printer/ippprinter.lib.php');
require_once($a);

$b = GetGlobal('controller')->require_dpc('printer/addhoc.lib.php');
require_once($b);

$__EVENTS['PRINTER_DPC'][0]='printer';
$__EVENTS['PRINTER_DPC'][1]='ippshow';
$__EVENTS['PRINTER_DPC'][2]='ippxml';
$__EVENTS['PRINTER_DPC'][3]='ippjobstats';
$__EVENTS['PRINTER_DPC'][4]='ippnetact';
$__EVENTS['PRINTER_DPC'][5]='ipplogout';
$__EVENTS['PRINTER_DPC'][6]='ippjobs';
$__EVENTS['PRINTER_DPC'][7]='ippjobstats';
$__EVENTS['PRINTER_DPC'][8]='ippdeljobs';
$__EVENTS['PRINTER_DPC'][9]='addprinter';
$__EVENTS['PRINTER_DPC'][10]='modprinter';
$__EVENTS['PRINTER_DPC'][11]='remprinter';
$__EVENTS['PRINTER_DPC'][12]='infprinter';
$__EVENTS['PRINTER_DPC'][13]='ipplogin';
$__EVENTS['PRINTER_DPC'][14]='useprinter';
$__EVENTS['PRINTER_DPC'][15]='confprinter';

$__ACTIONS['PRINTER_DPC'][0]='printer';
$__ACTIONS['PRINTER_DPC'][1]='ippshow';
$__ACTIONS['PRINTER_DPC'][2]='ippxml';
$__ACTIONS['PRINTER_DPC'][3]='ippjobstats';
$__ACTIONS['PRINTER_DPC'][4]='ippnetact';
$__ACTIONS['PRINTER_DPC'][5]='ipplogout';
$__ACTIONS['PRINTER_DPC'][6]='ippjobs';
$__ACTIONS['PRINTER_DPC'][7]='ippjobstats';
$__ACTIONS['PRINTER_DPC'][8]='ippdeljob';
$__ACTIONS['PRINTER_DPC'][9]='addprinter';
$__ACTIONS['PRINTER_DPC'][10]='modprinter';
$__ACTIONS['PRINTER_DPC'][11]='remprinter';
$__ACTIONS['PRINTER_DPC'][12]='infprinter';
$__ACTIONS['PRINTER_DPC'][13]='ipplogin';
$__ACTIONS['PRINTER_DPC'][14]='useprinter';
$__ACTIONS['PRINTER_DPC'][15]='confprinter';

$__DPCATTR['PRINTER_DPC']['printer'] = 'printer,1,0,0,0,0,0,0,0,0,0,0,1';

$__LOCALE['PRINTER_DPC'][0]='PRINTER_DPC;Printer;Εκτυπωτής';
$__LOCALE['PRINTER_DPC'][1]='_SHLOGOUT;Logout;Αποσύνδεση';



class printer {
   
    var $username, $userid, $userLevelID;
	var $urlpath, $inpath;
	var $path, $auth;
	
	var $myprinter, $myprintername, $defdir;
	var $message;
	
	function printer() {
	   $sFormErr = GetGlobal('sFormErr');
	   $UserName = GetGlobal('UserName');
	   $UserSecID = GetGlobal('UserSecID');
	   $GRX = GetGlobal('GRX');
	   $this->username = decode($UserName);
	   $this->userid = decode($UserID);
	   $this->userLevelID = (((decode($UserSecID))) ? (decode($UserSecID)) : 0);	   
	   
	   $this->path = paramload('SHELL','prpath');
	   $this->urlpath = paramload('SHELL','urlpath');
	   $this->inpath = paramload('ID','hostinpath');	   
	   
	   $this->myprinter = null;
	   $this->message = null;
	   $this->defdir = null;//'printers';
	   
	   $this->myprintername = $_SESSION['printer'] ?
	                          str_replace('.printer','',$_SESSION['printer']) :
							  GetReq('printername');
	   
	}

    function event($event=null) {
	
	    self::get_printer_auth('NONE');
		
        switch($event)   {
		
		    case 'addprinter' : 
			case 'modprinter' :
			case 'remprinter' : 
			case 'infprinter' : 
			case 'confprinter': 
			case 'useprinter' : //$this->myprinter = new ippprinter(null,null,null);
			                    break;
								
			case 'ipplogin'   : $this->myprinter = new ippprinter(null,$this->auth,null);
			                    $this->message = $this->get_printer_user(); 
			                    break;
			
			case 'ipplogout'  :	
		    case 'ippprinter' :
		    case 'ippshow'    :
			case 'ippxml'     :
			case 'ippjobstats':
			case 'ippnetact'  :
			case 'ippjobs'    : 
			case 'ippjobstats':
			case 'ippdeljob'  :			
			case 'printer'    :
			default           :
			                    $this->myprinter = new ippprinter(null,$this->auth,null);
			                    break;
        }			
	}
	
    function action($action=null)  {	
	
        switch($action)   {
		
		    case 'addprinter' : $ret = $this->addprinter(null,null,null,null,null,GetReq('indir'));
			                    break;
			case 'modprinter' : $ret = $this->modprinter();
								
			                    break;
								
			case 'remprinter' : break;

			case 'useprinter' : $ret = $this->useprinter();
			                    break;		
			case 'confprinter': $ret = $this->configprinter();
			                    break;									
			case 'infprinter' : $ret = $this->infoprinter();
			                    break;									

			case 'ipplogout'  :	$ret = $this->logout();
			                    $ret .= $this->myprinter->html_get_printers(GetReq('indir'));
			                    break;								
			case 'ipplogin'   : 
		    case 'ippprinter' :
		    case 'ippshow'    :
			case 'ippxml'     :
			case 'ippjobstats':
			case 'ippnetact'  :
			case 'ippjobs'    : 
			case 'ippjobstats':
			case 'ippdeljob'  :
			case 'printer'    : 
			                    $noauth = ($this->auth=='NONE' ? true : false);
								$cmd = str_replace('ipp','',$action);
								if ($noauth) {
                                    $log = $this->loginprinter($this->message);
									if ($log===true) {
									  $ret = $this->myprinter->html_printer_menu(true); 
								      $ret .= $this->myprinter->printer_console($cmd, $noauth);
									}  
									elseif (GetReq('printer'))
                                      $ret .= $log;				
                                    else
                                      $ret .= $this->myprinter->html_get_printers(); 									
								}
                                else  								
								    $ret .= $this->myprinter->printer_console($cmd, $noauth);	
									
								//$ret .= '-'.GetReq('printer').'>' . $_SESSION['printer'] .'-User:'.$_SESSION['user'];	
								break;	
								
			default           : //echo '>',GetReq('indir');
			                    //$ret = $this->myprinter->html_get_printers();//GetReq('indir'));//$ret ='printer';//none 					
        }

		//$ret .= '>'.$_SESSION['printer'];
        return ($ret);		
	}
	
	//ADD NEW PRINTER
	public function addprinter($name=null, $auth=null, $quota=null, $users=null, $indir=null) {
	    $printername = $name ? $name : GetParam('printername');
		$printerauth = $auth ? $auth : GetParam('printerauth');
		$printerquota = $quota ? $quota : 10;//GetParam('printerquota');
		$printerusers = is_array($users) ? $users : null;//GetParam('printerusers');
		$printerdir = $indir ? $indir : (GetParam('indir')?GetParam('indir'):(GetReq('indir') ? GetReq('indir') : $this->defdir));
		
		if (!$printername) {
		  $ret = $this->add_printer_form(null,$name,$auth,$quota,$users,$indir);
		  return ($ret);
		}  
	
	    //$ret = 'add printer...';
		
		$this->myprinter = new ippprinter();
        $ok = $this->myprinter->html_add_printer($printername,
			                                     $printerauth,
                                                 $printerquota,
                                                 $printerusers,
                                                 $printerdir); 		
		
		$msg = $ok ? 'Success' : 'Failed';
		
		if ($ok) {//modify..set conf params		
		  //$ret .=  seturl('t=modprinter','..Config..');
		  $ret .= $this->mod_printer_form($msg,$printername,$printerauth,$printerquota,$printerusers,$printerdir);
		}
		else 
		  $ret .= $this->add_printer_form($msg,$name,$auth,$quota,$users,$indir);
		
		return ($ret);
	}
	
	protected function add_printer_form($message=null, $name=null, $auth=null, $quota=null, $users=null, $indir=null) {
	
		$msg = $message ? '&nbsp;:&nbsp;' . $message : null;
		$basic_check = "checked='checked'"; 
			
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>			
		
	<img id="top" src="images/pf_top.png" alt="">
	<div id="form_container">
	
		<h1><a>Add Printer $msg</a></h1>
		<form id="form_470441" class="appnitro"  method="post" action="">
					<div class="form_description">
			<h2>Add printer $msg</h2>
			<p>Printer configuration.</p>
		</div>						
			<ul >
			
					<li id="li_1" >
		<label class="description" for="printername">Printer Name</label>
		<div>
			<input id="element_1" name="printername" class="element text medium" type="text" maxlength="13" value=""/> 
		</div><p class="guidelines" id="guide_1"><small>Printer name</small></p> 
		</li>		<li id="li_3" >
		<label class="description" for="printerauth">Authentication Method</label>
		<span>
<input id="element_3_1" name="printerauth" class="element radio" type="radio" value="SIMPLE" />
<label class="choice" for="element_3_1">SIMPLE</label>
<input id="element_3_2" name="printerauth" class="element radio" type="radio" value="BASIC" $basic_check/>
<label class="choice" for="element_3_2">BASIC</label>
<input id="element_3_3" name="printerauth" class="element radio" type="radio" value="OAUTH" />
<label class="choice" for="element_3_3">OAUTH</label>
		</span>
		<p class="guidelines" id="guide_3"><small>Select printer authentiaction method!</small></p> 
		</li>		<li id="li_2" >
		<label class="description" for="printerquota">Quota </label>
		<div>
			<input id="element_2" name="printerquota" class="element text medium" type="text" maxlength="4" value=""/> 
		</div><p class="guidelines" id="guide_2"><small>Printer quota</small></p> 
		</li>
			
					<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="indir" value="$indir" />
				<input type="hidden" name="FormAction" value="addprinter" />
			    
				<input id="saveForm" class="button_text" type="submit" name="Submit" value="Create Printer" />
		</li>
			</ul>
		</form>	
		<div id="footer">
		</div>		
	</div>
	<img id="bottom" src="images/pf_bottom.png" alt="">

EOF;

        return ($form);	
	
	}
	
	//MODIFY PRINTER PARAMS
	public function modprinter($name=null, $auth=null, $quota=null, $users=null, $indir=null) {
	    $printername = $name ? $name : (GetParam('printername')?GetParam('printername'):$this->myprintername);
		$printerauth = $auth ? $auth : GetParam('printerauth');
		$printerquota = $quota ? $quota : GetParam('printerquota');
		$printerusers = is_array($users) ? $users : array('admin'=>'admin','myself'=>'me');//null;//GetParam('printerusers');
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
		
		if (!$printername)
		  return ('Unknown printer!');
		  
		$this->myprinter = new ippprinter();
        $ret = $this->myprinter->html_printer_menu(true);		
		
		if (GetParam('FormAction')!='modprinter') {
		
		  $params = $this->myprinter->parse_printer_file($printername, $printerdir);
		  //print_r($params);
		  if (empty($params))
		    return ('Unknown printer!');
		  
		  $ret .= $this->mod_printer_form(null,$printername,$params['auth'],$params['quota'],$params['users'],$printerdir);
		  return ($ret);
		}		
	
	    //$ret = 'modify printer...';
		
        $ok = $this->myprinter->html_mod_printer($printername,
		                                         $printerauth,
												 $printerquota,
												 $printerusers,
												 $printerdir); 		
		
		$msg = $ok ? 'Success' : 'Failed';
		
		if ($ok) {
		  $ret .= $this->mod_printer_form($msg,$printername,$printerauth,$printerquota,$printerusers,$printerdir);
		}
		else
		  $ret .= $this->mod_printer_form($msg,$printername,$printerauth,$printerquota,$printerusers,$printerdir);
		  
		return ($ret);	
    }	
	
	protected function mod_printer_form($message=null,$name=null, $auth=null, $quota=null, $users=null, $indir=null) {
	
	    $msg = $message ? '&nbsp;:&nbsp;' . $message : null;
		$oauth_check = $basic_check = $simple_check = null;
		
		switch (str_replace("'","",$auth)) {
		  case 'OAUTH' : $oauth_check = "checked='checked'"; break;		
		  case 'BASIC' : $basic_check = "checked='checked'"; break;		
		  case 'SIMPLE': 
		  default      : $simple_check = "checked='checked'";
		  
		}
		//echo $auth,'>';
	
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>			
		
	<img id="top" src="images/pf_top.png" alt="">
	<div id="form_container">
	
		<h1><a>Modify Printer : $name $msg</a></h1>
		<form id="form_470441" class="appnitro"  method="post" action="">
					<div class="form_description">
			<h2>Modify printer : $name $msg</h2>
			<p>Printer configuration.</p>
		</div>						
			<ul >
			
					<li id="li_1" >
		<label class="description" for="printername">Printer Name</label>
		<div>
			<input id="element_1" name="printername" class="element text medium" type="text" maxlength="13" value="$name" 'readonly'/> 
		</div><p class="guidelines" id="guide_1"><small>Printer name</small></p> 
		</li>		<li id="li_3" >
		<label class="description" for="element_3">Authentication Method</label>
		<span>
<input id="element_3_1" name="printerauth" class="element radio" type="radio" value="SIMPLE"  $simple_check/>
<label class="choice" for="element_3_1">SIMPLE</label>
<input id="element_3_2" name="printerauth" class="element radio" type="radio" value="BASIC"  $basic_check/>
<label class="choice" for="element_3_2">BASIC</label>
<input id="element_3_3" name="printerauth" class="element radio" type="radio" value="OAUTH"  $oauth_check/>
<label class="choice" for="element_3_3">OAUTH</label>
		</span>
		<p class="guidelines" id="guide_3"><small>Select printer authentiaction method!</small></p> 
		</li>		<li id="li_2" >
		<label class="description" for="printerquota">Quota </label>
		<div>
			<input id="element_2" name="printerquota" class="element text medium" type="text" maxlength="4" value="$quota"/> 
		</div><p class="guidelines" id="guide_2"><small>Printer quota</small></p> 
		</li>
			
					<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="modprinter" />
			    
				<input id="saveForm" class="button_text" type="submit" name="Submit" value="Modify Printer" />
		</li>
			</ul>
		</form>	
		<div id="footer">
		</div>		
	</div>
	<img id="bottom" src="images/pf_bottom.png" alt="">
	
EOF;
	
         return ($form);
	}	
	
	//PRINTER USERS
	public function useprinter($printername=null, $indir=null) {
	    $printername = $name ? $name : (GetParam('printername') ? GetParam('printername') : $this->myprintername);
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
        $printerusers = array();		
		
        if (!$printername)
		  return ('Unknown printer!');		
		
		$this->myprinter = new ippprinter();
		
		$ret = $this->myprinter->html_printer_menu(true);
		
		if (GetParam('FormAction')!='useprinter') {
		
		  $params = $this->myprinter->parse_printer_file($printername, $printerdir);
		  //print_r($params);
		  if (empty($params))
		    return ('Unknown printer!');
		  
		  $ret .= $this->users_printer_form(null,$printername,$params['users'],$printerdir);
		  return ($ret);
		}	

        //get user post data	
        for ($i=1;$i<6;$i++) {
		    $post_user = 'user'.$i.'name';
			$post_pass = 'pass'.$i.'word';
		    if (($u = $_POST[$post_user]) && ($p = $_POST[$post_pass])) {
			   $printerusers[$u] = $p;
			} 
        } 		
		//print_r($printerusers);
		if (!empty($printerusers)) {
		
        $ok = $this->myprinter->html_mod_printer($printername,
		                                         null,
												 null,
												 $printerusers,
												 $printerdir); 
		}										 
		
		$msg = $ok ? 'modified successfully' : 'Failed to modify!';
		$ret .= $this->users_printer_form($msg,$printername,$printerusers,$printerdir);
		  
		return ($ret);	
    }	
	
	protected function users_printer_form($message=null, $name=null, $users=null, $indir=null) {
	
	    $user_fields = '
        <li id="li_4" >
		<label class="description" for="user<@>">User <@> </label>
		<span>
			<input id="element_<@>_1" name= "user<@>name" class="element text" maxlength="13" size="14" value="[Username]"/>
			<label>Username</label>
		</span>
		<span>
			<input id="element_<@>_2" name= "pass<@>word" class="element text" maxlength="13" size="14" value="[Password]"/>
			<label>Password</label>
		</span><p class="guidelines" id="guide_4"><small>User <@></small></p> 
		</li>		
';		

        $ji=1;
        if (!empty($users)) {
		  foreach ($users as $un=>$up) {
		    $user = str_replace("'","",$un);
			$pass = str_replace("'","",$up);
		    $myuserfields = str_replace('[Username]',$user,str_replace('[Password]',$pass,$user_fields)); 
		    $us_ui .= str_replace('<@>',$ji,$myuserfields);
		    $ji+=1;
		  }
		  
		}
		//+until 5
        for ($i=$ji;$i<=5;$i++) {
		    $myuserfields = str_replace('[Username]','',str_replace('[Password]','',$user_fields));
		    $us_ui .= str_replace('<@>',$i,$myuserfields);
		}	   
	
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<img id="top" src="images/pf_top.png" alt="">
	<div id="form_container">
	
		<h1><a>Printer's Users $message</a></h1>
		<form id="form_470441" class="appnitro"  method="post" action="">
					<div class="form_description">
			<h2>Printer's Users $message</h2>
			<p>Add or modify printer's users.</p>
		</div>						
			<ul >
			
		<li id="li_0" >
		<label class="description" for="element_0">Printer </label>
		<div>
			<input id="element_0" name="element_0" class="element text medium" type="text" maxlength="13" value="$name"/> 
		</div><p class="guidelines" id="guide_1"><small>Printer name</small></p> 
		</li>		

		$us_ui
			
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="useprinter" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
		<div id="footer">
		</div>
	</div>
	<img id="bottom" src="images/pf_bottom.png" alt="">

EOF;
        return ($form);	
	
	}	
	
	//FETCH PRINTER INFO FILES
	public function infoprinter($printername=null, $indir=null) {
	    $printername = $name ? $name : $this->myprintername;
		$printerdir = $indir ? $indir : $_SESSION['indir'];		
		
		$this->myprinter = new ippprinter();
        $ret = $this->myprinter->html_printer_menu(true);
		
        $ok = $this->myprinter->html_info_printer($printername, $printerdir); 				
		$ret .= $ok ? 'End of info' : 'Failed to fetch info!';
		
		return ($ret);	
    }	
	
	protected function info_printer_form() {
	
	}	
	
	//CHANGE PRINTER QUOTA
    public function addquota($quota=null, $printername=null, $indir=null) {
	    $printername = $name ? $name : $this->myprintername;
		$printerquota = $quota ? $quota : null;//GetParam('printerquota');
		$printerdir = $indir ? $indir : $_SESSION['indir'];		
   
		$this->myprinter = new ippprinter();
        $ok = $this->myprinter->html_mod_printer($printername,null,$printerquota,null,$printerdir); 	
		
		if ($ok)										
	      $ret = $jobs .' jobs added!';
		else
          $ret = 'Quota error!';		
		
		return ($ret);   
    } 	

	protected function add_quota_form() {
	
	}	
	
	//CONFIG PRINTER
	public function configprinter($printername=null, $indir=null) {
	    $printername = $name ? $name : $this->myprintername;
		$printerdir = $indir ? $indir : $_SESSION['indir'];			
		$handlers = array();
		$params = array();

        if (!$printername) 
		  return ('Unknown printer!');	  
		  
		$this->myprinter = new ippprinter();
		$ret = $this->myprinter->html_printer_menu(true);		
		
        if ($filter=GetParam('filter')) {
		  $code = GetParam('filtercode');
		  $ret .= $this->config_filter_form($filter,$printername,$code,$printerdir);
		  return ($ret);		
		}			
		
		//read conf file
		$pr_config = $this->myprinter->parse_printer_conf($printername,$printerdir);
		//print_r($pr_config);
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
		
		if (GetParam('FormAction')!='confprinter') {
		  
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
        $msg = 	$this->myprinter->save_printer_conf($printername,$printerdir,$file);	
		
		//print_r($params);		
		$msg = null;//$ok ? 'Saved' : 'Failed to save!';
		$ret .= $this->config_printer_form($msg,$printername,$params,$printerdir);
		  
		return ($ret);	
    }	
	
	protected function config_printer_form($message=null, $name=null, $params=null, $indir=null) {
	    //print_r($params);
		$hd_ui = null;
		$filters_method = $params['method'];
		$page = pathinfo($_SERVER['PHP_SELF'],PATHINFO_BASENAME);
		$edit_filter = $page.'?t=confprinter&filter=[Handler]';//seturl('t=confprinter&filter=[Handler]');
		
	    $handler_fields = '
        <li id="li_4" >
		<label class="description" for="filter<@>">Filter <@> </label>
		<span>
			<input id="element_<@>_1" name= "handler<@>" class="element text" maxlength="13" size="14" value="[Handler]"/>
			<label>Filter&nbsp;[<a href="' . $edit_filter . '">Edit</a>]</label>
		</span>
		<span>
			<input id="element_<@>_2" name= "index<@>" class="element text" maxlength="13" size="14" value="[Index]"/>
			<label>Value</label>
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
		//+until 10
        for ($i=$ji;$i<=10;$i++) {
		    $myhfields = str_replace('[Handler]','',str_replace('[Index]','',$handler_fields));
		    $hd_ui .= str_replace('<@>',$i,$myhfields);
		}	
	
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<img id="top" src="images/pf_top.png" alt="">
	<div id="form_container">
	
		<h1><a>Printer filters $message</a></h1>
		<form id="form_470441" class="appnitro"  method="post" action="">
					<div class="form_description">
			<h2>Printer filters $message</h2>
			<p>Add or modify printer behavior.</p>
		</div>						
			<ul >
			
		<li id="li_0" >
		<label class="description" for="element_0">Filter type </label>
		<div>
			<input id="element_0" name="filters_method" class="element text medium" type="text" maxlength="13" value="$filters_method"/> 
		</div><p class="guidelines" id="guide_1"><small>Filter apply method</small></p> 
		</li>		

		$hd_ui
			
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="confprinter" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
		<div id="footer">
		</div>
	</div>
	<img id="bottom" src="images/pf_bottom.png" alt="">

EOF;
        return ($form);		
	}	

    //PRINTER's FILTER EDIT	
	protected function config_filter_form($filter=null, $printername=null, $code=null, $indir=null) {
	
	    $dir = $indir ? $indir.'/' : ($this->defdir ? $this->defdir .'/' : null);
		$filter = GetParam('filtername') ? GetParam('filtername') : $filter;
	    $file = $_SERVER['DOCUMENT_ROOT'] .'/'.$dir . $printername.'.'.$filter.'.php';
		//echo $file,'>',$code;
	
	    if ($code) {
           //code testbed
		   $dummy_auth = 'dummy';
		   $dummy_fp = 'fp_';
		   $job_id = 1;
		   $job_filename = $_SERVER['DOCUMENT_ROOT'] .'/'.$dir .'job_test.txt';
		   $job_attr = array('test'=>'test');
		   $testbed = new addhoc($dummy_auth, $dummy_fp,$job_id,$job_filename,$job_attr,$printername);
		   
		   $testbed->dummy_auth = 'dummy';
		   $testbed->dummy_fp = 'fp_';
		   $testbed->job_id = 1;
		   $testbed->job_filename = $_SERVER['DOCUMENT_ROOT'] .'/'.$dir .'job_test.txt';
		   $testbed->job_attr = array('test'=>'test');	

           if (!empty($_FILES['testfile']) && (!$_FILES['testfile']['error'])) {//uploaded file
		   
		     //print_r($_FILES);
			 $tpfile = $_FILES['testfile']['tmp_name'];
			 
		     if (is_readable($tpfile)) {   
			  
			   if ($tp = fopen($tpfile, "r+b")) {  
			     $testbed->import_data = fread($tp, $_FILES['testfile']['size']);
			     fclose($fp);
			   }  
            }			 
           }
           else  		   
             $testbed->import_data = '';		   
		   
		
		   //manipulate code
		   $code = str_replace('echo','$ret .= ',$code);
		   $mycode = str_replace('this','testbed',$code);//local var testbed
		   
		   //eval php
           @trigger_error("");
           $result = eval($mycode);
           $error = error_get_last();		
		   //print_r($error);
		   if ($error['message'])
		     $msg = '<br>'.$error['message'] .' : line '.$error['line'] ;
		   else	 
	         $msg .= '...Saved';	
			 
//           $mycode .= "
//return (\$this->output);";			 
		   

		   try {
		       $result = $testbed->execute($code, true);//not $mycode..thereis no testbed
			   file_put_contents($testbed->job_filename, $result);
			   
			   if ($result) {
		        $preview = '<li class="section_break"><h3>Result:</h3><p></p></li>';
					  
                if (is_readable($testbed->job_filename)) {
				  $preview .= "<IFRAME SRC=\"".$dir."job_test.txt\" TITLE=\"$printername\" WIDTH=600 HEIGHT=400>>
                              <!-- Alternate content for non-supporting browsers -->
                              <H2>$printername</H2>
                              <H3>iframe is not suported in your browser!</H3>
                              </IFRAME>";
			    }			 
               }			   
           } 
		   catch (Exception $e) {
                $msg = "Error!";
           }		   
		   
		   //save script
		   file_put_contents($file,"<?php\r\n".$code."\r\n?>");

		}
		else {
		   //load script
		   $code = str_replace("<?php\r\n","",str_replace("\r\n?>","",file_get_contents($file)));
		   $msg = '...Loaded';
		}
		
		//if ($preview)
		  //return ($preview);

	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<img id="top" src="images/pf_top.png" alt="">
	<div id="form_container">
	
		<h1>$msg</h1>
		<form id="form_470441" class="appnitro" enctype="multipart/form-data" method="post" action="">
					<div class="form_description">
			<h2>Edit Printer Filter</h2>
			<p>Edit printer's filter.</p>
		</div>						
			<ul >
			
					<li id="li_1" >
		<label class="description" for="element_1">Filter Name </label>
		<div>
			<input id="element_1" name="filtername" class="element text medium" type="text" maxlength="255" value="$filter"/> 
		</div><p class="guidelines" id="guide_1"><small>Edit filter name.
If filter name exits leave it as is to modify the filter.
If filter name is blank, enter a name to create a new filter.</small></p> 
		</li>		
		
        <li class="section_break">
			<p></p>
		</li>	
		
<label class="description" for="element_2">
<br>&nbsp;class $filter {
<br>&nbsp;&nbsp;function __construct() {
	<br>&nbsp;&nbsp;&nbsp;&nbsp;\$this->printer_name; //string variable that holds the printer's name
	<br>&nbsp;&nbsp;&nbsp;&nbsp;\$this->jid; //integer variable that holds the job id
	<br>&nbsp;&nbsp;&nbsp;&nbsp;\$this->jf; = //string variable holds the name of current file (path included)
	<br>&nbsp;&nbsp;&nbsp;&nbsp;\$this->jattr; // array of current job attributes
	<br>&nbsp;&nbsp;&nbsp;&nbsp;\$this->import_data; // text variable that holds the original data
	<br>&nbsp;&nbsp;&nbsp;&nbsp;// always return the proccesed data using return()	
<br>&nbsp;&nbsp;}	
<br>&nbsp;&nbsp;
<br>&nbsp;&nbsp;protected function execute() {
</label>		
		
		<li id="li_2" >
		<!--label class="description" for="element_2">Filter script </label--!>
		<div>
			<textarea id="element_2" name="filtercode" class="element textarea medium">$code</textarea> 
		</div> 
		</li>
		
<label class="description" for="element_2">	
<br>&nbsp;&nbsp;}
<br>&nbsp}
</label>	
		
        <li class="section_break">
			<h3>Import data</h3>
			<p></p>
		</li>		<li id="li_3" >
		<label class="description" for="element_3">File </label>
		<div>
			<input id="element_3" name="testfile" class="element file" type="file"/> 
		</div>  
		</li>		
        <li class="section_break">
			<p></p>
		</li>		
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="confprinter" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
		<div id="footer">
		$preview
		</div>
	</div>	
	<img id="bottom" src="images/pf_bottom.png" alt="">		
EOF;
        return ($form);		
	}	
	
	
	//PRINTER AUTH
	protected function get_printer_auth($auth=null) {
	   
	   if ($auth) {
	      $this->auth =	$auth; 
	   }
       elseif ($printer = $_SESSION['printer']?$_SESSION['printer']:$_GET['printer']) {	 
	      $bootfile = $this->printers_path . str_replace('.printer','.php',$printer);
		  //echo $bootfile;
		  $this->auth = $this->get_bootstrap_auth($bootfile);	   
	   }
       else	
          $this->auth =	'BASIC';   
		  
	   //echo '-',$this->auth,'<br>';
       return ($this->auth);	   
	}
	
	
	protected function get_bootstrap_auth($bootsrapfile=null) {
	    if (!$bootsrapfile)
		  return;
		
		$auth = 'BASIC'; //default
		$printers_path = $_SERVER['DOCUMENT_ROOT'] .'/printers/';
        $bootdata = @file_get_contents($printers_path . $bootsrapfile);
		
		if (strstr($bootdata,"IPPListener")) {
		
          if (strstr($bootdata,",'OAUTH'"))
           $auth = 'OAUTH'; 		
          elseif (strstr($bootdata,",'DIGEST'"))
           $auth = 'DIGEST'; 
		  elseif (strstr($bootdata,",'BASIC'"))
           $auth = 'BASIC';    
		  	  
	      return ($auth);
		}
		else //invalid bootstrap file
		  return null;
	}	
	
	protected function get_printer_user() {
        $printername = GetParam('printername');
		$printerdir = GetParam('indir');
		$printeruser = GetParam('username');
		$printerpass = GetParam('password');
		$allowed_users = array();
	
		$params = $this->myprinter->parse_printer_file(str_replace('.printer','',$printername), $printerdir);
		//print_r($params);
		
		if (empty($params))
		   $message = 'Invalid printer';
		
		$allowed_users = (array) $params['users']; 
        //print_r($allowed_users);		
		   
		if (($printeruser) && ($printerpass)) {
		
		   if ((array_key_exists($printeruser, $allowed_users)) &&
		    ($allowed_users[$printeruser]== $printerpass)) {
			
		        $_SESSION['user'] = $printeruser;
				$_SESSION['printer'] = $printername;//str_replace('.printer','',$printername);
				$_SESSION['indir'] = $printerdir;
				return true;
		   }
        }		
	
	    $message = 'Invalid user';
		
	    return ($message);
	}

   	protected function loginprinter($message=null) {
	    $current_printer = GetReq('printer');
	    $login_printer = $_SESSION['printer'];
		$printer_user = $_SESSION['user'];
		
		//echo $current_printer ,'>' , $login_printer ,'User:',$printer_user;
		
	    if ($message === true) {
		   return true; //successful login by get_printer_user() event
		}
	    elseif (!$login_printer) {
		   //login form
		   $ret .= $this->login_form($message.'...');
		}
		elseif ($login_printer!=$current_printer){ 
		   //login form
		   $ret .= $this->login_form($message);		
		}
		else {
		   //$ret .= '..logged in';	 
		   //$ret .= 'User:'.$printer_user;
		   $ret = true;
		}   
        return ($ret);		
    }	
	
	protected function login_form($message=null) {
	
	    $printername = GetParam('printername') ? GetParam('printername') : GetReq('printer');
		$indir = GetParam('indir') ? GetParam('indir') : (GetReq('indir') ? GetReq('indir') : $this->defdir);
	
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>			
		
	<img id="top" src="images/pf_top.png" alt="">
	<div id="form_container">
	
		<h1><a>Login $message</a></h1>
		<form id="form_470441" class="appnitro"  method="post" action="">
					<div class="form_description">
			<h2>Login $message ($printername)</h2>
			<p>Please enter your details to access the printer.</p>
		</div>						
			<ul >
			
					<li id="li_4" >
		<label class="description" for="element_4">User </label>
		<span>
			<input id="element_4_1" name= "username" class="element text" maxlength="20" size="14" value=""/>
			<label>Username</label>
		</span>
		<span>
			<input id="element_4_2" type= "password" name= "password" class="element text" maxlength="20" size="14" value=""/>
			<label>Password</label>
		</span><p class="guidelines" id="guide_4"><small>Printer credentials</small></p> 
		</li>
			
					<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
			    <input type="hidden" name="printername" value="$printername" />
				<input type="hidden" name="indir" value="$indir" />
				<input type="hidden" name="FormAction" value="ipplogin" />
				
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
		<div id="footer">
			Green Printers by <a href="http://www.stereobit.com">stereobit</a>
		</div>
	</div>
	<img id="bottom" src="images/pf_bottom.png" alt="">

EOF;
	
        return ($form);	
	}	
	
	//overrite ipp logout 
   	protected function logout() {

       //session_destroy();
	   
       if (isset($_SESSION['user'])) {
          $_SESSION['user'] = null; 
		  $_SESSION['printer'] = null;
		  $_SESSION['indir'] = null;

          $ret .=  "Successfully logged out<br>";
          //echo '<p><a href="?action=logIn">LogIn</a></p>';
       }

       return ($ret);	   
    }	
}
};
?>
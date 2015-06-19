<?php

require_once("UiIPP.php");

class UiIPPdropbox extends UiIPP {

    var $newuser;
	
	function __construct($printer=null, $auth=null, $printers_url=null, $externaluse=null, $procmd=null) {   	   
							    
	    parent::__construct($printer,$auth,$printers_url,$externaluse,$procmd);
	    
		$this->newuser = $_SESSION['new_user'] ? $_SESSION['new_user'] : false;
		//echo $this->newuser,'>';
	}
	
    protected function _sendmail($from=null,$to=null,$subject=null,$body=null,$mailfile=null) {
	    ini_set("SMTP","localhost");//"smtp.example.com" ); 
        ini_set('sendmail_from', $from);//'user@example.com'); 
       
	    if (!$to)
            return false;		
	    //$to = $to ? $to : 'b.alexiou@stereobit.gr';
		
		if ($mailfile) 
		    $body = file_get_contents($mailfile); 
  
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From:' . $from . "\r\n" .
                    'Reply-To: '. $from . "\r\n" .
                    'Dropbox-Printer: 1.0-/' . phpversion();
        //$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
        //$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";					

        // The message
        //$message = "Line 1\nLine 2\nLine 3";
		//...replace br/cr/lf to \n...
		$message = str_replace("\r\n",'',$body);
        // In case any of our lines are larger than 70 characters, we should use wordwrap()
        $message = wordwrap($message, 70);
					
		$ret = mail($to,$subject,$message,$headers);
						
	    return ($ret);					
    }	
	
    protected function _save_mail_relation($childmail,$child,$parent) {
	    $pname = str_replace('.printer','',$this->printer_name); 
		//$path = $_SERVER['DOCUMENT_ROOT'] . '/';

		$file = $this->admin_path."$pname-listmail.php";
        $data = "\$".$pname."_listmail['$childmail'] = '".$child.'<'.$parent."';"; //save referer and email
				
		    if ($fp = @fopen ($file , "a+")) {
                fwrite ($fp, "\r\n<?php " . $data . "?>");
                fclose ($fp);
                return true;
            }
            else 
			    return false;	
    }

    protected function _get_userlist() {
	    $pname = str_replace('.printer','',$this->printer_name);
		//$path = $_SERVER['DOCUMENT_ROOT'] . '/';
		$listvar = $pname."_listmail";	
		
		include($this->admin_path."$pname-listmail.php");				
		
		if (empty($listvar))
		    return null;
			
		foreach ($$listvar as $mail=>$userp) {
		    $childparent = explode('<',$userp);
			$child = $childparent[0];
			$parent = $childparent[1];
			if ($parent==$this->username)
			    $ret[$child] = $mail;
		}
		
		return ($ret);
    }	
	
    protected function _get_maillist() {
	    $pname = str_replace('.printer','',$this->printer_name);
		//$path = $_SERVER['DOCUMENT_ROOT'] .'/';
		$listvar = $pname."_listmail";			
		
		include($this->admin_path."$pname-listmail.php");
		
		if (empty($listvar))
		    return null;
		
		if ($this->username!=$this->get_printer_admin()) {
		    $ret = $this->_get_userlist();
		}
		else //all
            $ret = array_keys($listvar);	

        return ($ret);			
    }		
	
	//override ..for startup screen when login
	public function printer_console($action=null, $noauth=false) {
	    $action = $action ? $action : $_GET['action'];
		
		//echo self::get_printer_path(),'>';
	
		//print_r($_SESSION);
        //echo $this->external_use,'>';		
		if ($this->external_use==false) {//($noauth==false) {//html dpc auth

		  if ($this->authenticate_user()==false) {
		  
		    if ($this->authentication_mechanism==='OAUTH') {
		  
		        //////////////////////////////////////////////////////////////////// OAUTH
				//already directed to twitter login screen			
		    }
			elseif ($this->authentication_mechanism==='NONE') {
			
		        //////////////////////////////////////////////////////////////////// OAUTH
				//anonymous user no auth..				
			}
			else {  ////////////////////////////////////////////////////////////////// BASIC
                self::write2disk('network.log',":$this->username(login-failed):");		  		  
			
	            header("WWW-Authenticate: Basic realm=\"$this->printer_name\",stale=FALSE");
                header('HTTP/1.0 401 Unauthorized');
				
				return (self::invalid_login());
			}	
		  }
		  
		}//noauth	
        else {
          $this->username = $_SESSION['user']; //external dpc auth
     	  if (!$this->username) 
		    return ('Exit'); 		  
		}  
		  
        //echo 'CONSOLE', 'printername:',$this->printer_name,'>';		  
			
		self::write2disk('network.log',":$this->username:");
           
        switch ($action) {
              case 'show'    : if ($iframe = $_GET['iframe']) 
			                     $ret .= $this->html_header(); 
			                   break;	//else no ifamerow data
              case 'xml'     : break;	//xml row data	
			  case 'logout'  :	
			  case 'delete'  : 
			  case 'jobs'    : 
			  case 'jobstats': $ret .= self::html_header(); break;
              case 'netact'  : $ret .= self::html_header(null,15); break;			  
		      default        : $ret .= self::html_header();
		}

		if (($this->printer_name) && ($jid = $_GET['job'])) {
		    //echo 'c';
		    switch ($action) { 
			   
			   case 'proceed' :	$ret .= self::html_proceed_printer_job($jid); break;			   
			   case 'delete': $ret .= self::html_delete_printer_job($jid); break;
			   case 'show'  : $iframe = $_GET['iframe']?true:false;
			                  $ret .= self::html_show_printer_job($jid, $iframe); break;
			   case 'xml'   : break;
			   case 'logout': break;
			   case 'netact': $ret .= self::html_get_network_activity(); break;
			   default      : $ret .= self::html_show_printer_job($jid);
			}
		}	
		elseif ($this->printer_name) { 
		    //echo 'b', 'printername:',$this->printer_name,'>';
		    switch ($action) {
		      case 'addprinter' : $ret .= $this->form_addprinter(); break;
			  case 'modprinter' : $ret .= $this->form_modprinter(); break;
			  case 'remprinter' : 
			  case 'infprinter' : $ret .= $this->form_infoprinter(); break;
			  case 'confprinter': $ret .= $this->form_configprinter(); break;
			  case 'useprinter' : $ret .= $this->form_useprinter(); break;	
			  case 'uploadjob'  : $ret .= $this->form_upload_job(); break;			  
			                       
			  case 'xml'     : $ret .= self::xml_get_printer_jobs(); break;
			  case 'logout'  : break;
			  case 'jobstats': $ret .= self::html_get_printer_stats(); break;
			  case 'jobs'    : $ret .= self::html_get_printer_jobs(); break;
			  case 'netact'  : $ret .= self::html_get_network_activity(); break;
			  default        : //$ret .= self::html_get_printer_menu();	
			                   //$ret .= self::html_get_printer_jobs();
							   $ret .= $this->form_useprinter();
							   //$ret .= $this->form_infoprinter();
            }			
		}	
		else {
		    //echo 'a', 'printername:',$this->printer_name,'>';
		    switch ($action) {
			  case 'logout': break;
			  //case 'jobs'  : $ret .= self::html_get_printer_jobs($printer); break;
			  case 'netact': $ret .= self::html_get_network_activity(); break;
			  default      : //$ret .= self::html_get_printers();
			                 //$ret .= self::html_get_printer_jobs();//no printer yet...
							 //$ret .= self::html_printer_menu(true);
							 $ret .= $this->form_useprinter();
			}  
		}	
			
        switch ($action) {
              case 'show'    : if ($iframe = $_GET['iframe'])
			                     $ret .= self::html_footer();
			                   break;	//else no ifamerow data
			  case 'xml'     : break;	//xml row data	
              case 'logout'  : $ret .= $this->logout();
                               //$ret .= self::html_get_printers();
                               break;							   
              case 'netact'  :
              case 'jobstats':	
              case 'jobs'    : 			  
		      default        : 	
			                   //$ret .= '<hr>$this->server_name . $this->server_version . "&nbsp;|&nbsp;".$this->logout_url;  
		                       $ret .= self::html_footer();	
		}			
		 
		return $ret; 
	}	
	
	//override
    protected function logout($html=null) {

       //session_destroy();
	   
       if (isset($_SESSION['user'])) {
          $_SESSION['user'] = null; 
		  $_SESSION['printer'] = null;
          $_SESSION['indir'] = null;
		  
		  $_SESSION['new_user'] = null;
		  
          $ret .=  "Exit<br>";
          //echo '<p><a href="?action=logIn">LogIn</a></p>';
       }

       return ($ret);	   
    }	
	
	//override
	public function form_modprinter($name=null, $auth=null, $quota=null, $users=null, $indir=null) {
	    $printername = $name ? $name : ($_POST['printername']?$_POST['printername']:$this->printer_name);
		$printerauth = $auth ? $auth : $_POST['printerauth'];
		$printerquota = $quota ? $quota : $_POST['printerquota'];
		$printerusers = is_array($users) ? $users : array('admin'=>'admin','user1'=>'test123');
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
		$cmd = $this->external_use ? $this->procmd.'modprinter':'modprinter';
		
	    if ($this->username!=$this->get_printer_admin()) {
		   //return ('Not allowed!');		
		   $ret = self::html_window(null, 'Not allowed!');
		   return ($ret);
		}   
		
		$ret = parent::form_modprinter($name,$auth,$quota,$users,$indir);	
		return ($ret);
	}
	
	//override
	public function form_useprinter($printername=null, $indir=null) {
	    $printername = $name ? $name : ($_POST['printername']?$_POST['printername']:$this->printer_name);
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
		$cmd = $this->external_use ? $this->procmd.'useprinter':'useprinter';
        $printerusers = array();
		$ok = false;

	    if ($this->username!=$this->get_printer_admin()) {
		    //return ('Not allowed!');
            if (!$printername)
		        return ('Unknown printer!');				
			   
		    $params = $this->parse_printer_file($printername, $printerdir);
		    //print_r($params);
		    if (empty($params))
		        return ('Unknown printer file!');
				
		    $printerusers = (array) $params['users'];
		   
		    if ($_POST['FormAction']!=$cmd) {
			    if ($this->newuser) {
				    //$ret .= $this->html_show_instruction_page('user-defined');
				    //$ret  = $this->html_show_instruction_page('user-post');
					
                    $ret .= self::html_window(null, 'User ('.$this->newuser.') defined.', $this->printer_name);			
				}	
				else 
		          $ret .= $this->add_user_printer_form(null,$printername,$params['users'],$printerdir);
				  
		        return ($ret); 
		    }
			
 		
		    if (!empty($printerusers)) {
                //get user post data	
		        $post_user = 'username';
			    $post_pass = 'password';			
				
		        if (($u = addslashes($_POST[$post_user])) && ($p = addslashes($_POST[$post_pass]))) {
			        //not allowing double entries
			        if (!array_key_exists($u, $printerusers)) {
			            $printerusers[$u] = hash('crc32',$p);//$p; //hash 
			    			
                        $ok = $this->html_mod_printer($printername,
		                                              null,
					 				                  null,
									                  $printerusers,
									                  $printerdir); 
						//CHANGE USER							  
						if ($ok) {
						    $this->newuser = $u;
							$_SESSION['new_user'] = $u;
						    $addusermail = $this->_save_mail_relation($_POST['email'],$this->newuser,$this->username);							
                        }						
					}								  
				}					  
		    }										 
		
		    $msg = $ok ? " $u user added successfully" : ' Dublicate entry, failed to add user!';
		    //$ret .= $this->add_user_printer_form($msg,$printername,$printerusers,$printerdir);			
			$ret .= self::html_window(null, $msg, $this->printer_name);			
			
			return ($ret);
		}   
		//else
		$ret = parent::form_useprinter($printername, $indir);
		return ($ret);
		
		//////////////////////////////////////////////////////////
        if (!$printername)
		  return ('Unknown printer!');		
		
		//$ret = $this->html_printer_menu(true);
		
		if ($_POST['FormAction']!=$cmd) {
		
		  $params = $this->parse_printer_file($printername, $printerdir);
		  //print_r($params);
		  if (empty($params))
		    return ('Unknown printer file!');
		  
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
        $ok = $this->html_mod_printer($printername,
		                              null,
					 				  null,
									  $printerusers,
									  $printerdir); 
		}										 
		
		$msg = $ok ? 'modified successfully' : 'Failed to modify!';
		$ret .= $this->users_printer_form($msg,$printername,$printerusers,$printerdir);
		  
		return ($ret);	
    }	
	
	public function add_user_printer_form($message=null, $name=null, $users=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;
		$cmd = $this->external_use ? $this->procmd.'useprinter':'useprinter';	
	
	    $menu = $this->html_printer_menu(true);
		
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu 
		<form id="form_470441" class="appnitro" enctype="multipart/form-data" method="post" action="">
					<div class="form_description">
			<h2>Add User $message</h2>
			<p>Add a printer account.</p>
		</div>						
			<ul >		

        <li id="li_4" >
		<label class="description" for="user">Account details</label>
		<span>
			<input id="element_1_1" name= "username" class="element text" maxlength="13" size="14" value=""/>
			<label>Username</label>
		</span>
		<span>
			<input id="element_1_2" type= "password" name= "password" class="element text" maxlength="13" size="14" value=""/>
			<label>Password</label>
		</span><p class="guidelines" id="guide_4"><small>Add user account details</small></p> 
		</li>
		
		<li id="li_0" >
		<label class="description" for="element_0">Your e-mail</label>
		<div>
			<input id="element_0" name="email" class="element text medium" type="text" maxlength="30" value=""/> 
		</div><p class="guidelines" id="guide_1"><small>Please specify your email to send you the activation details</small></p> 
		</li>			
			
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="$cmd" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
		<div id="footer">
        $this->printer_name
		</div>
	</div>
	<br/>

EOF;
        return ($form);	
	
	}		
	
	//override
	public function form_configprinter($printername=null, $indir=null) {
	    $printername = $printername ? $printername : $this->printer_name;
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
        $cmd = $this->external_use ? $this->procmd.'confprinter':'confprinter';		
		$handlers = array();
		$params = array();
		//echo $printername,'...',$indir,'...';
		
	    if ($this->username!=$this->get_printer_admin()) {
		    //return ('Not allowed!');		
		   
            if (!$printername) 
		        return ('Unknown printer!');			   
				
		    $ret = self::config_filter_form_dropbox('dboxsave', $printername, $code, $indir);
			return ($ret);
		}   

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
/*		
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
*/		
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
		$this->printer_name
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
		if ($this->username!=$this->get_printer_admin()) {
		    $myuser = $this->newuser ? $this->newuser : $this->username;
		    $userstr = '-'.$myuser;
		}	
		else {
		    $myuser = $this->username;
           	$userstr = null;	
		}	
			
		//$path = self::get_printer_path();	
		//NOT A FILTER FILE ANYMORE
		//$file = $this->admin_path . str_replace('.printer','',$printername).'.'.$filter.$userstr.'.php';
		//..JUST SETTING SAVING
		$file = $this->admin_path . $filter.$userstr.'-conf'.'.php';
		//load file		
		if (is_readable($file)) { //load before post
		
		    include ($file);
			//make any param mods any...
			//....
			//$dbfolder will be read asis inside the file
		}
		
		$dp_ui = null;
		
		//if ($dbfolder = $_POST['dbfolder']) {
		if ($filtername = $_POST['filtername']) {
		   
		    $dbfolder  = $_POST['dbfolder'];
		
		    $db_code = "<?php
\$dbfolder = '$dbfolder';			
?>		  
";
		    //save file...		  
		    @file_put_contents($file, $db_code);

            if ($this->newuser) {
			    //go back yo native user
			    $this->newuser = null;
                $_SESSION['new_user'] = null;	
				
				$form = $this->form_infoprinter(); 
		        return ($form);		
			}			
		}	
        /*else { //MOVED UP....	
            //load file		
		    if (is_readable($file)) {
			    $cnt = file($file,FILE_SKIP_EMPTY_LINES);
			    //scan for dropbox save folder
			    $parts = explode("=",$cnt[1]);
			    $dbfolder = trim(str_replace(';','',$parts[1]));				
            }	   
		}*/	
		
		$menu = $this->html_printer_menu(true);  

	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu 
		<form id="form_470441" class="appnitro" enctype="multipart/form-data" method="post" action="">
					<div class="form_description">
			<h2>Set Dropbox account data. $message</h2>
			<p>Dropbox configuration.</p>
		</div>						
			<ul >		

        $dp_ui	
		
		<li id="li_0" >
		<label class="description" for="element_0">Dropbox folder </label>
		<div>
			<input id="element_0" name="dbfolder" class="element text medium" type="text" maxlength="20" value="$dbfolder"/> 
		</div><p class="guidelines" id="guide_1"><small>Please specify a dropbox folder to save outputs</small></p> 
		</li>			
			
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="FormAction" value="$cmd" />	
				<input type="hidden" name="filtername" value="$filter" />				
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
		<div id="footer">
		$this->printer_name
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

	    //if ($filter=='dropbox') { //renamed due to dropbox.printer conflict !
		if ($filter=='dboxsave') {
		    //$form = 'dropbox';
		    $form = self::config_filter_form_dropbox($filter, $printername, $code, $indir);
	    }
	    else
		    //$form = parent::config_filter_form($filter, $printername, $code, $indir);
	        $form = self::config_filter_form($filter, $printername, $code, $indir);
	   
	   return ($form);
	}
	
	//override
	public function form_infoprinter($printername=null, $indir=null) {
	    $printername = $printernamename ? $printername : $this->printer_name;
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
		
		if ($this->username!=$this->get_printer_admin()) {
		    $ret = self::html_get_printer_jobs_info();
		}	
		else {
		    $ret = self::info_printer_form();
			$ok = self::html_info_printer($printername, $printerdir); 		
		    $ret .= $ok ? $ok : 'Failed to fetch info!';
		}	
		
		return (self::html_window(null, $ret, 'dropbox.printer'));	
    }	
	
	protected function html_get_printer_jobs_info() {
	    $user = $this->newuser ? $this->newuser : ($this->username ? $this->username : $_SESSION['user']);	
		$jstate = array(); 
		
        if (!is_dir($this->jobs_path))
		  return null; 

        $printer_state = null;	
        $mydir = dir($this->jobs_path);	
		
        while ($fileread = $mydir->read ()) { 
		
		    if (substr($fileread,0,4)=='job'.FILE_DELIMITER) {
				//echo $fileread,'<br>';
			    $pf = explode(FILE_DELIMITER,$fileread);
				$jid = $pf[1];//sort	
                $job_owner = $pf[3];
				
			    if (($user==$this->get_printer_admin()) || ($job_owner==$user) || (!defined('AUTH_USER'))) {				
			
                    $jobs[intval($jid)] = $fileread;
					
				    if (stristr($fileread,FILE_DELIMITER.'completed'))
					    $jstate[intval($jid)] = 'completed';
					elseif (stristr($fileread,FILE_DELIMITER.'processing'))
					    $jstate[intval($jid)] = 'processing';
					elseif (stristr($fileread,FILE_DELIMITER.'pending'))
					    $jstate[intval($jid)] = 'pending';
					else
					    $jstate[intval($jid)] = 'pending';										  
				}
			}	
		}	
		$mydir->close();
		
		$ret = '<h2>' . $user . '&nbsp;Jobs</h2>';		
		$ret .= self::printline(array('No','Job-Id','Ip','User','Job-Name','Status'),
		                        array('left;5%','left;5%','left;20%','left;20%','left;40%','left;10%'),
		 					    1,
			                    "center::100%::0::group_article_body::left::0::0::");	
								
		if (is_array($jobs)) {

			krsort($jobs);
		    $i=1;
		    foreach ($jobs as $jid=>$fileread) {
			   $fp = explode(FILE_DELIMITER,$fileread);
	           $job['id'] = $fp[1];
	           $job['remote-ip'] = str_replace('~',':',$fp[2]);
	           $job['user-name'] = $fp[3];
		       $job['job-name'] = $fp[4];

			   //FORCE PROCESS.......not in dpc wraping
			   if (($jstate[$job['id']]!='completed') && (!$_GET['t']) && (!$_GET['printer'])) 
                 $proceed_state = "<a href='$this->urlpath?".$this->cmd."proceed&job=".$job['id']."'>" . $jstate[$job['id']]  ."</a>";				   			   
			   else
			     $proceed_state = $jstate[$job['id']];
		   			   
		   
               $ret .= self::printline(array($i++,$job['id'],$job['remote-ip'],$job['user-name'],$job['job-name'],/*$jstate[$job['id']]*/$proceed_state),
			                           array('left;5%','left;5%','left;20%','left;20%','left;40%','left;10%'),
                                       0,
			                           "center::100%::0::group_article_body::left::0::0::");									   
			}
	    }
		else {
		   $ret .= 'No Jobs';
		}	

        return ($ret);			
	}

	//override  
	protected function html_get_printer_menu($iconsview=null, $p=null) {
		$urlicons = 'icons/';	
        $icons = array();		
		$user = $this->username ? $this->username : $_SESSION['user'];
		$indir = $_SESSION['indir'] ? $_SESSION['indir'] : $_GET['indir'];
		
		if ($this->username!=$this->get_printer_admin()) {
		  
		    if ($this->newuser) {
		        $icons[] = $this->urlpath."?".$this->cmd."useprinter:one";
			    $icons[] = $this->urlpath."?".$this->cmd."confprinter:two";
                $icons[] = $this->urlpath."?".$this->cmd."infprinter:three";		
			    //$icons[] = $this->urlpath."?".$this->cmd."logout:logout";
			}
			else {
		        $icons[] = $this->urlpath."?".$this->cmd."useprinter:Printer Users";
			    $icons[] = $this->urlpath."?".$this->cmd."confprinter:Printer Configuration";
                $icons[] = $this->urlpath."?".$this->cmd."infprinter:Printer Info";		
			    //$icons[] = $this->urlpath."?".$this->cmd."logout:logout";			
			}			
			
		    //RENDER ICONS
		    if ($iconsview) {
		        //print_r($icons);
		        foreach ($icons as $icon) { 
			
			    $icondata = explode(':',$icon);
			
			    if (is_file($this->icons_path.$icondata[1].'.png'))
			      $ifile = $urlicons.$icondata[1].'.png';
			    else
			      $ifile = $urlicons.'index.printer.png';
			   
			    $icco[] = "<a href='".$icondata[0]."'><img src='" . $ifile."' border=0 alt='".$icondata[1]."'></a>";
			    //$link = "<a href='".$icondata[0]."'>" . $icondata[1]  ."</a>";
			    $px = $p ? $p : '33%';
	            $attr[] = 'left;'.$px;
			    }	
                //print_r($icco);			
			    $ret = self::printline($icco,$attr,0,"center::100%::0::group_article_body::left::0::0::");			
		    }
		
		    return ($ret);			
		}
		
		$ret = parent::html_get_printer_menu($iconsview,$p);
		return($ret);
    }	


    ///////////////////////////////////////////////////////////////////////////
	
	//send a file in queue
    protected function send_test_page($file=null, $printername=null, $indir=null, $user=null) {
	    $printername = $printername ? $printername : $this->printer_name;	
	    $dir = $indir ? $indir.'/' : ($_SESSION['indir'] ? $_SESSION['indir'] .'/' : '/');
        $username = $user ? $user : $this->username;
		
        if ((!$username) || (!$printername))		
		    return false;
	
		$job_id = self::_get_job_id(); 
	    $name = $file ? $file : 'testpage.txt';  
		
        $jobname = str_replace(FILE_DELIMITER,'_',$name);
				   
		$jobtitle = 'job'.FILE_DELIMITER.
		            $job_id.FILE_DELIMITER.
		            str_replace(':','~',$_SERVER['REMOTE_ADDR']).FILE_DELIMITER.
					$username.FILE_DELIMITER.
					$jobname;
				
        if (is_readable($this->admin_path . $name)) { //copy it
		    $ok = @copy($this->admin_path . $name, $this->jobs_path . $jobtitle);
			//echo 'Copy file:'. $this->admin_path . $file;
		}   
		elseif ($fp = fopen($this->jobs_path . $jobtitle, "w")) {//create it
		    $data = "test page";
		    $ok = fwrite($fp, $data, strlen($data));
            fclose($fp);
			//echo 'Create file:'.$this->jobs_path . $jobtitle;
		}
		//else
		  // echo "Error:".$file;
		
		//add quota
		if ($ok) {
		  //echo ':Send Test Page... Success<br>';
		  self::set_user_quota(1,$username,$printername, $dir);
		  return ($job_id); //return id to execute in 2nd step
		}  

	    return false;
    }

	//process queue 
    protected function process_job($job_id=null, $printername=null, $indir=null) {
	    $printername = $printername ? $printername : $this->printer_name;	
	    $dir = $indir ? $indir.'/' : ($_SESSION['indir'] ? $_SESSION['indir'] .'/' : '/');
        $username = $user ? $user : $this->username;
		$job_attr = array();
		$ret = false;	
		
		if ((!$job_id) || (!$printername))		
		    return false; 		

		$mydir = dir($this->jobs_path);
        while ($fileread = $mydir->read ()) { 		
		    if (substr($fileread,0,4)=='job'.FILE_DELIMITER) {
			    $pf = explode(FILE_DELIMITER,$fileread);
				$jid = $pf[1];
				$job_owner = $pf[3];
				
				if ($jid==$job_id) {
				
	                $job_attr['job-id'] = $pf[1];
	                $job_attr['remote-ip'] = str_replace('~',':',$pf[2]);
	                $job_attr['user-name'] = $pf[3];
		            $job_attr['job-name'] = $pf[4];					
				
                    if (is_readable($this->jobs_path . $fileread)) { 
					
                        if (($username==$this->get_printer_admin()) || ($job_owner==$username) || (!defined('AUTH_USER'))) {
						    $file_name = $this->jobs_path . $fileread;
						}
                    } 						
						
					break;	
				}
			}
		}	
		$mydir->close();

		//echo ">FILE_NAME:".$file_name.'<br>'; 
		
		if ($file_name) {		
		
	    //execute test page for allow app callback dropbox ...CALL AGENT ..HANDLERS ???
		/*
        $callback_function = null;
	    $callback_param = null;	
        
			
		    if ((class_exists('AgentIPP', true)) && ($username)) {
		        $srv = new AgentIPP($this->authentication,
			                    $printername,
			                    $username,
			                    $callback_function,
							    $callback_param,
							    true, true);//<<<manual run...
			   
		        $ret .= "<br>Print agent initialized!";
				
				$ret .= $srv->process_job($job_id, $file_name, $job_attr);
		    } 
            else 
              $ret .= "<br>Print agent failed to initialized!";
		*/
		
		//execute test page for allow app callback dropbox ..directly call api
        $app_key = "geuq6gm2b5glofq";
	    $app_secret = "5s9jvk2zd5oc0hq";

        try {
            // Check whether to use HTTPS and set the callback URL
            $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
            $callback = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];	
	
	        // Instantiate the Encrypter and storage objects
            // $key is a 32-byte encryption key (secret)
            $key = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
            $encrypter = new \Dropbox\OAuth\Storage\Encrypter($key);

            // User ID assigned by your auth system (used by persistent storage handlers)
            $userID = $username;
		  
            // Create the storage object, passing it the Encrypter object
            //$storage = new \Dropbox\OAuth\Storage\Session($encrypter); 
		    $storage = new \Dropbox\OAuth\Storage\Filesystem($encrypter, $userID);
	        $storage->setDirectory($this->admin_path);
 
            $OAuth = new \Dropbox\OAuth\Consumer\Curl($app_key, $app_secret, $storage, $callback);
            $dropbox = new \Dropbox\API($OAuth);
 
		    //$ret .= "<br>DBOXSAVE FILENAME ($userID):". $file_name ."<br>";
	
            // Upload the file with an alternative filename
            $ret = $dropbox->putFile($file_name,$job_attr['job-name'],null,true); //alt name,path,override
	        
        } 
	    catch(\Dropbox\Exception $e) {
	        $ret = $e->getMessage() . PHP_EOL;
	        //exit('Setup failed! Please try running setup again.');
        }			
		
		}//if file_name
		
		//in case of true file_read has no renamed to complete..always pending..
        return ($ret);		
    } 	
}
?>
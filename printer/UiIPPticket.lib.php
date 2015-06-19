<?php

require_once("UiIPP.php");

class UiIPPticket extends UiIPP {

    var $newuser;
	
	function __construct($printer=null, $auth=null, $printers_url=null, $externaluse=null, $procmd=null) {   	   
	
        spl_autoload_register(array($this, 'loader')); //call dropbox api..process_job		
							    
	    parent::__construct($printer,$auth,$printers_url,$externaluse,$procmd);
	    
		$this->newuser = $_SESSION['new_user'] ? $_SESSION['new_user'] : false;
		//echo $this->newuser,'>';
			
	}
	
    function loader($class){
	   $class = str_replace('\\', '/', $class);
	   require_once('handlers/'.$class . '.php');
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
                    'ImageSave-Printer: 1.0-/' . phpversion();
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
			
		foreach ($listvar as $mail=>$userp) {
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
			  case 'jobstats': $ret .= $this->html_header(); break;
              case 'netact'  : $ret .= $this->html_header(null,15); break;			  
		      default        : $ret .= $this->html_header();
		}

		if (($this->printer_name) && ($jid = $_GET['job'])) {
		    //echo 'c';
		    switch ($action) { 
			   
			   case 'proceed' :	$ret .= $this->html_proceed_printer_job($jid, true); break;//this override, true for silent exec		   
			   case 'delete': $ret .= $this->html_delete_printer_job($jid); break;
			   case 'show'  : $iframe = $_GET['iframe']?true:false;
			                  $ret .= $this->html_show_printer_job($jid, $iframe); break;
			   case 'xml'   : break;
			   case 'logout': break;
			   case 'netact': $ret .= $this->html_get_network_activity(); break;
			   default      : $ret .= $this->html_show_printer_job($jid);
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
			                       
			  case 'xml'     : $ret .= $this->xml_get_printer_jobs(); break;
			  case 'logout'  : break;
			  case 'jobstats': $ret .= $this->html_get_printer_stats(); break;
			  case 'jobs'    : $ret .= $this->html_get_printer_jobs(); break;
			  case 'netact'  : $ret .= $this->html_get_network_activity(); break;
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
			  case 'netact': $ret .= $this->html_get_network_activity(); break;
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
		                       $ret .= $this->html_footer();	
		}			
		 
		return $ret; 
	}	
	
	//override..add css for html5-dnd
	protected function html_header($encoding=null, $reload=null) {
	
	  if ($this->external_use) 
	    return null;
	  
	  //no need
	  $encoding = $encoding?$encoding:'utf-8';//'iso-8859-7';
	
	  $ret = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset='.$encoding.'" />
			';
	
      if ($reload)	
	    $ret .= '<meta http-equiv="refresh" content="'.$reload.'"/>';
			
      $ret .= '<title>IPP Server '.$this->server_version.' | stereobit.networlds</title>
               <link href="imagesave-printer.css" rel="stylesheet" type="text/css" />	  
            </head>
            <body>';
			
	  return ($ret);		
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
			            $printerusers[$u] = hash('crc32',$p);//$p;
			    			
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
		
        if (!$printername) 
		    return ('Unknown printer!');		
		
	    if ($this->username!=$this->get_printer_admin()) {
		    //return ('Not allowed!');					   
				
		    $ret = self::config_filter_form_ticketsave('ticketsave', $printername, $code, $indir);
			return ($ret);
		}     
		  
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

    //imagesave filter form
	protected function config_filter_form_ticketsave($filter=null, $printername=null, $code=null, $indir=null) {
	    $printername = $printername ? $printername : $this->printer_name;
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
			
		$file = $this->admin_path . $filter.$userstr.'-conf'.'.php';
		//echo $file,'>';
        //read file	args	
		if (is_readable($file)) {

            include($file);	
			
			$iwfile_src = $iwfile ? 'admin/'.$printername.'/'.$iwfile : null;

			$iautoresize = implode(',',$iautoresize);//set as string
            //$iftp_pathpersize = implode(',',$iftp_pathpersize);//set as string	
			if (!empty($iftp_pathpersize))
		  	    //$iftp_pathpersize = "'" . implode("','",$iftp_pathpersize). "'";//set as string	..keep ' in load
			    $iftp_pathpersize = implode(',',$iftp_pathpersize);
			else  
			    $iftp_pathpersize = ''; 
				
            //keep previous state			
			$dropbox_was_enabled = ($idropbox>0) ? true : false;					
        }			
		
		//change args if post
		if ($filtername = $_POST['filtername']) {
		    $iaction = $_POST['iaction'] ? stripslashes($_POST['iaction']) : 'null';
		    $ifiletype = $_POST['ifiletype'] ? stripslashes($_POST['ifiletype']) : '';
		    $icompression = $_POST['icompression'] ? stripslashes($_POST['icompression']) : 75;
			$ixframe = (($xf = intval($_POST['ixframe'])) && ($xf<2000)) ? $xf : '0';
			$iyframe = (($yf = intval($_POST['iyframe'])) && ($yf<2000)) ? $yf : '0';
			$iwopacity = $_POST['iwopacity'] ? ($_POST['iwopacity']<=100 ? stripslashes($_POST['iwopacity']):100) : 100;
			$iwalpha = $_POST['iwalpha'] ? 1 : 0; 
			$iwposition = $_POST['iwposition'] ? $_POST['iwposition'] : 'null';
			$ioptimize = $_POST['ioptimize'] ? $_POST['ioptimize'] : '0';
			//print_r($_FILES['iwfile']);
			if (!empty($_FILES['iwfile']) && (!$_FILES['iwfile']['error'])) {//uploaded file
	            
		        $ufile = $_FILES['iwfile']['tmp_name'];	
				$rfile = $_FILES['iwfile']['name'];//str_replace(FILE_DELIMITER,'_',$_FILES['iwfile']['name']);				
				
				if ((stristr($rfile,'.jpg')) || (stristr($rfile,'.gif')) || (stristr($rfile,'.png')) ) {
				
					$iwfilename = $this->username . FILE_DELIMITER . 'watermark' . '.' . array_pop(explode('.',$rfile));			
	                //echo '>'. $iwfilename;
					
                    if (move_uploaded_file($ufile, $this->admin_path . $iwfilename)) {
					
					    $iwfile = $iwfilename;
						$iwfile_src = 'admin/'.$printername.'/'.$iwfilename;
						//echo '>'. $iwfilename . '>' . $iwfile;
					}	
					else	
                        $message = "Can not upload the file." . $_FILES['iwfile']['error']; 			
				}	
				else	
                    $message = "Invalid image type."; 					
			}
				
			if (stristr($_POST['iautoresize'],',')) {
			  $iautoresize = explode(',',stripslashes($_POST['iautoresize']));
			  foreach ($iautoresize as $i=>$size)
			    $cp_size .= $size . ',';			
			}
			else
			    $cp_size = stripslashes($_POST['iautoresize']);
				
			$iftp_server = stripslashes($_POST['iftp_server']);
			$iftp_username = stripslashes($_POST['iftp_username']);
			$iftp_password = stripslashes($_POST['iftp_password']);
			$iftp_path = stripslashes($_POST['iftp_path']);
			
			if (stristr($_POST['iftp_pathpersize'],',')) {
			  $iftp_pathpersize = explode(',',stripslashes($_POST['iftp_pathpersize']));
			  foreach ($iftp_pathpersize as $i=>$sp)
			    $sp_path .= "'" . $sp ."',";
			}  
			else
			    $sp_path = "'" . stripslashes($_POST['iftp_pathpersize']) . "'";
				
			$idropbox = $_POST['idropbox'] ? 1 : 0; 
            $idbfolder = $_POST['idbfolder'] ? stripslashes($_POST['idbfolder']) : null; 			
			
		    //echo 'post'.$iwfile;
		    $db_code = "<?php
\$iaction = $iaction;			
\$ifiletype = '$ifiletype';			
\$icompression = $icompression;
\$ixframe = $ixframe;
\$iyframe = $iyframe;
\$iwopacity = $iwopacity;
\$iwalpha = $iwalpha;
\$iwposition = $iwposition;
\$iwfile = '$iwfile';
\$iautoresize = array($cp_size);
\$ioptimize = $ioptimize;
\$iftp_server = '$iftp_server';
\$iftp_username = '$iftp_username';
\$iftp_password = '$iftp_password';
\$iftp_path = '$iftp_path';
\$iftp_pathpersize = array($sp_path);
\$idropbox = $idropbox;
\$idbfolder = '$idbfolder';
?>";
		    //save file...		  
		    $x = @file_put_contents($file, $db_code);			
			//echo $x.'>'.$db_code;
			
			//override arrays to view as string
		    $iautoresize = implode(',',$iautoresize) ;//set as string 
            $iftp_pathpersize = implode(',',$iftp_pathpersize);//set as string dont keep ' at save
			
            if ($this->newuser) {
			    //go back to native user
			    $this->newuser = null;
                $_SESSION['new_user'] = null;	
				
				$form = $this->form_infoprinter(); 
		        return ($form);		
			}
            //else....
			
            //if dropbox gonna be enabled...
            if ((!$dropbox_was_enabled) && ($idropbox)) {
			    //echo 'GOTO DROPBOX PAGE:',$this->username,'>',$printername,'<br>';		  
				//goto dropbox page...
				$this->enable_dropbox_jobs(null,$printername, $indir);    
            }

			//if dropbox gonna be disabled...
			if (($dropbox_was_enabled) && (!$idropbox)) { 
			    //echo 'DISABLE DROPBOX:',$this->username,'>',$printername,'<br>'; 
                $this->disable_dropbox_jobs($printername, $indir); 			  
			}	
			
		}
        elseif ($_GET['oauth_token']) {	//..returing from allow app procedure
            //echo 'RETURN FROM DROPBOX:',$this->username,'>',$printername,'<br>';	

			if ($testpage_id = $this->send_test_page('testpage.jpg',$printername, $indir, $this->username)) {
              //return from dropbox page...save token...
			  $this->enable_dropbox_jobs($testpage_id,$printername, $indir);
            }				
		}
		elseif ($_GET['not_approved']==='true') {//in case of deny app
			//echo 'deny';
			$idropbox = 0; //override setting		
		}		
		
		switch ($ifiletype) {
		    case 'png' : $ifiletype_select = "<option value='0' >Source</option><option value='jpg' >jpg</option><option value='png' selected='selected'>png</option><option value='gif' >gif</option>";
			             break;
		    case 'gif' : $ifiletype_select = "<option value='0' >Source</option><option value='jpg' >jpg</option><option value='png' >png</option><option value='gif' selected='selected'>gif</option>";
			             break;
		    case 'jpg' : $ifiletype_select = "<option value='0' >Source</option><option value='jpg' selected='selected'>jpg</option><option value='png' >png</option><option value='gif' >gif</option>";
			             break;  
			default    : $ifiletype_select = "<option value='0' selected='selected'>Source</option><option value='jpg'>jpg</option><option value='png' >png</option><option value='gif' >gif</option>";
		}
		
		switch ($iaction) {
			case 1 : $iact_select_1 = "selected='selected'"; break; 
			case 2 : $iact_select_2 = "selected='selected'"; break; 
			case 3 : $iact_select_3 = "selected='selected'"; break; 
            case 0 :			
			default: $iact_select_0 = "selected='selected'"; 
		}		
		
        $iwfile_image = $iwfile_src ? "<img src='$iwfile_src' width='128'>" : null;		
		$iwalpha_check = $iwalpha ? "checked='checked'": null;		
		
		switch ($iwposition) {
		    case 1 : $iwpos_select_1 = "selected='selected'"; break; 
			case 2 : $iwpos_select_2 = "selected='selected'"; break; 
			case 3 : $iwpos_select_3 = "selected='selected'"; break; 
			case 4 : $iwpos_select_4 = "selected='selected'"; break; 
			case 5 : $iwpos_select_5 = "selected='selected'"; break; 
			case 0 :
			default: $iwpos_select_0 = "selected='selected'"; 
		}	

		switch ($ioptimize) { 
			case 1 : $iopt_select_1 = "selected='selected'"; break; 
            case 0 :			
			default: $iopt_select_0 = "selected='selected'"; 
		}	

        $idropbox_check = $idropbox ? "checked='checked'": null;				

		$menu = $this->html_printer_menu(true);  

		//dropbox form addon when no new user ........????
        if (!$this->newuser) {
		    $form_dropbox = '
        <li class="section_break"><h2>Dropbox integration</h2><p>Enable dropbox integration.
		This service requires a Dropbox service to be installed on your system. If you don\'t have a Dropbox account, 
		<a href="http://db.tt/Pd430oY0" target=\'_blank\'>create a dropbox account.</a></p></li>
        <li id="li_8" >
		<label class="description" for="element_8">Enable Dropbox</label>
		<span>
		<input id="element_8" name="idropbox" class="element checkbox" type="checkbox" value="1" '.$idropbox_check.'/>
        <label class="choice" for="element_8">Dropbox</label>
		</span><p class="guidelines" id="guide_8"><small>Dropbox integration.</small></p> 
		</li>
		<li id="li_9" >
		<label class="description" for="element_9">Dropbox inbox name </label>
		<div>
			<input id="element_9" name="idbfolder" class="element text medium" type="text" maxlength="20" value="'.$idbfolder.'"/> 
		</div><p class="guidelines" id="guide_9"><small>Please specify a dropbox folder to save outputs</small></p> 
		</li>			
';			
        }
        else
            $form_dropbox = null;		
		
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu 
		<form id="form_470441" class="appnitro" enctype="multipart/form-data" method="post" action="">
		<div class="form_description">
			<h2>Printer settings. $message</h2>
			<p>Printer configuration.</p>
		</div>						
		<ul >		
		
        <li class="section_break"><h2>Image manipulation</h2><p>Task when one job received.</p></li>
			
		<li id="li_01" >
		<label class="description" for="element_01">Action </label>
		<div>
		<select class="element select medium" id="element_01" name="iaction"> 
		<option value="0" $iact_select_0 >No action</option>
        <option value="1" $iact_select_1 >Rotate 90 left</option>		
        <option value="2" $iact_select_2 >Rotate 90 right</option>		
        <option value="3" $iact_select_3 >Rotate 180</option>
		</select>
		</div><p class="guidelines" id="guide_01"><small>Select an action.</small></p> 
		</li>			
		<li id="li_0" >
		<label class="description" for="element_0">Image export </label>
		<div>
		<select class="element select medium" id="element_0" name="ifiletype"> 
        $ifiletype_select
		</select>
		</div><p class="guidelines" id="guide_0"><small>Select an image type for output</small></p> 
		</li>
		<li id="li_1" >
		<label class="description" for="element_1">Compression </label>
		<div>
			<input id="element_1" name="icompression" class="element text medium" type="text" maxlength="3" value="$icompression"/> 
		</div><p class="guidelines" id="guide_1"><small>Specify a JPEG compression rate (1..100)</small></p> 
		</li>
		<li id="li_11" >
		<label class="description" for="element_11">Frame</label>
		<span>
			<input id="element_11" name= "ixframe" class="element text" maxlength="4" size="8" value="$ixframe"/>
			<label>X dimension</label>
		</span>
		<span>
			<input id="element_12" name= "iyframe" class="element text" maxlength="4" size="8" value="$iyframe"/>
			<label>Y dimension</label>
		</span><p class="guidelines" id="guide_11"><small>Place in frame</small></p> 
		</li>		
		
		<li class="section_break"><h2>Watermark</h2><p>Add watermark.</p></li>
		
		<li id="li_13" >
		<label class="description" for="element_13">Watermark position </label>
		<div>
		<select class="element select medium" id="element_13" name="iwposition"> 
		<option value="0" $iwpos_select_0 ">No watermark</option>
        <option value="1" $iwpos_select_1 >Up left</option>
        <option value="2" $iwpos_select_2 >Up right</option>
        <option value="3" $iwpos_select_3 >Down left</option>
        <option value="4" $iwpos_select_4 >Down right</option>
        <option value="5" $iwpos_select_5 >Center</option>
		</select>
		</div><p class="guidelines" id="guide_13"><small>Select where to place the watermark</small></p> 
		</li>
        <li id="li_12" >
		<label class="description" for="element_12">Upload a watermark file </label>
		<div>
		<input id="element_12" name="iwfile" class="element file" type="file"/> 
		</div> <p class="guidelines" id="guide_12"><small>Select a watermark image file to merge inito your source image. Please upload a smaller image than can fit into your source images.</small></p> 
		</li>
		<li id="li_13" >
		<!--label class="description" for="element_13">Watermark file:$iwfile </label-->
		$iwfile_image
		</li>				
		<li id="li_12" >
		<label class="description" for="element_12">Opacity </label>
		<div>
			<input id="element_12" name="iwopacity" class="element text medium" type="text" maxlength="3" value="$iwopacity"/> 
		</div><p class="guidelines" id="guide_12"><small>Specify watermark opacity rate (0..100)</small></p> 
		</li>
        <li id="li_11" >
		<label class="description" for="element_11">Alpha transparency</label>
		<span>
		<input id="element_11" name="iwalpha" class="element checkbox" type="checkbox" value="1" $iwalpha_check/>
        <label class="choice" for="element_11">Alpha transparency</label>
		</span><p class="guidelines" id="guide_11"><small>Use Alpha transparency (Ignore opacity).</small></p> 
		</li>		

        <li class="section_break"><h2>Image resizing</h2><p>Automate image resizing.</p></li>
		
		<li id="li_2" >
		<label class="description" for="element_2">Resize </label>
		<div>
		<input id="element_2" name="iautoresize" class="element text medium" type="text" maxlength="20" value="$iautoresize"/> 
		</div><p class="guidelines" id="guide_2"><small>Please specify autoresize x value separated by commas (500,200,100)</small></p> 
		</li>
		<li id="li_22" >
		<label class="description" for="element_22">Dimension </label>
		<div>
		<select class="element select medium" id="element_22" name="ioptimize"> 
        <option value="0" $iopt_select_0 >is width</option>		
        <option value="1" $iopt_select_1 >is height</option>		
		</select>
		</div><p class="guidelines" id="guide_22"><small>Specify the dimension type.</small></p> 
		</li>		
		
        <li class="section_break"><h2>Ftp Account</h2><p>Enable ftp auto uploading.</p></li>
		
		<li id="li_3" >
		<label class="description" for="element_3">Ftp Server</label>
		<div>
			<input id="element_3" name="iftp_server" class="element text medium" type="text" maxlength="20" value="$iftp_server"/> 
		</div><p class="guidelines" id="guide_3"><small>Please specify ftp server address to automatically upload the files. Otherwise leave it blank</small></p> 
		</li>	
		<li id="li_4" >
		<label class="description" for="element_4">Ftp Username</label>
		<div>
			<input id="element_4" name="iftp_username" class="element text medium" type="text" maxlength="50" value="$iftp_username"/> 
		</div><p class="guidelines" id="guide_4"><small>Please specify ftp account username</small></p> 
		</li>
		<li id="li_5" >
		<label class="description" for="element_5">Ftp Password</label>
		<div>
			<input id="element_5" name="iftp_password" class="element text medium" type="text" maxlength="50" value="$iftp_password"/> 
		</div><p class="guidelines" id="guide_5"><small>Please specify ftp account password</small></p> 
		</li>
		<li id="li_6" >
		<label class="description" for="element_6">Ftp Path</label>
		<div>
			<input id="element_6" name="iftp_path" class="element text medium" type="text" maxlength="50" value="$iftp_path"/> 
		</div><p class="guidelines" id="guide_6"><small>Please specify ftp path</small></p> 
		</li>	
		<li id="li_7" >
		<label class="description" for="element_7">Ftp subpath per size</label>
		<div>
			<input id="element_7" name="iftp_pathpersize" class="element text medium" type="text" maxlength="50" value="$iftp_pathpersize"/> 
		</div><p class="guidelines" id="guide_7"><small>Please specify ftp path per size separated by commas (large,medium,small)</small></p> 
		</li>	

        $form_dropbox			
					
		
        <li class="section_break"></li>
		
		<li class="buttons">
		        <input type="hidden" name="MAX_FILE_SIZE" value="500000" />
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
		if ($filter=='ticketsave') {
		    //$form = 'dropbox';
		    $form = self::config_filter_form_ticketsave($filter, $printername, $code, $indir);
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
		
		return (self::html_window(null, $ret, 'po.printer'));	
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
				
                    //..return from image edit
                    if (($savedid = $_GET['imagesaved']) && ($savedid == $jid)) {

                        //change job status...to re-process..	
					    if ($new_state_fileread = $this->process_job($jid))
						    $fileread = $new_state_fileread;
                    }					
					
				    if (stristr($fileread,FILE_DELIMITER.'completed'))
					    $jstate = 'completed';
					elseif (stristr($fileread,FILE_DELIMITER.'processing'))
					    $jstate = 'processing';
					elseif (stristr($fileread,FILE_DELIMITER.'pending'))
					    $jstate = 'pending';
					else
					    $jstate = 'pending';	
						
					$jtime = date ("F d Y H:i:s.", filemtime($this->jobs_path . $fileread));	
					$jsize = filesize($this->jobs_path . $fileread);	//bytes
						
				    $jobs[intval($jid)] = array('name'=>$fileread, 'job'=>$pf, 'state'=>$jstate, 
					                            'date'=>$jtime, 'size'=>$jsize);							
				}
			}	
		}	
		$mydir->close();
		
		$ret = '<h2>' . $user . '&nbsp;Jobs</h2>';	

        //header line..
		//$ret .= '<h2>' . $this->printer_name . '&nbsp;Jobs'./* $this->printer_state.*/'</h2>';
		$ret .= "<a href='".$this->urlpath."?".$this->cmd."jobs&which=all'>" . 'All'  ."</a>";	
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."jobs&which=pending'>" . 'Pending'  ."</a>";	   
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."jobs&which=processing'>" . 'Processing'  ."</a>";				   
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."jobs&which=completed'>" . 'Completed'  ."</a>";
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."jobstats'>" . 'Statistics'  ."</a>";
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."uploadjob'>" . 'Add/Upload'  ."</a>";	
        $ret .= '<hr/>';
		
		$ret .= self::printline(array('No','Date','Size','Name','Status','Edit'),
		                        array('left;5%','left;30%','left;10%','left;45%','left;5%','left;5%'),
		 					    1,
			                    "center::100%::0::group_article_body::left::0::0::");	
								
		if (is_array($jobs)) {

			krsort($jobs);
		    $i=1;
			foreach ($jobs as $jid=>$fileattr) {			   
			   $job_file = $fileattr['name'];
	           $job_id = $fileattr['job'][1];
	           $job_remote_ip = str_replace('~',':',$fileattr['job'][2]);
	           $job_user_name = $fileattr['job'][3];
		       $job_name = $fileattr['job'][4];	
               $job_status = $fileattr['state'];			   
			   $job_time = $fileattr['date'];
			   $job_size = self::bytesToSize1024($fileattr['size'], 1);
			   
			   //FORCE PROCESS....
			   if ($job_status!='completed') { 
			   
			     if ((!$_GET['t']) && (!$_GET['printer']))//...not in dpc wraping
                   $proceed_state = "<a href='$this->urlpath?".$this->cmd."proceed&job=".$job_id."'>" . $job_status  ."</a>";
				 else
				   $proceed_state = $job_status;	
				   
                 //edit image..not allowed
                 $edit = 'Edit';
               } 				 
			   else { //completed
			     $proceed_state = $job_status;			 
		   	
			     //edit image...
                 //$image = '../jobs/'.$this->printer_name.'/'.$job_file;	
                 $callback = base64_encode('..'.$_SERVER["REQUEST_URI"].'&imagesaved='); 
				 //echo $callback.'>';				 
                 $edit = "<a href='imageeditor/index.php?callback=$callback&printer=".$this->printer_name."&imagesrc=".$job_id."'>Edit</a>";  				
			   }	 
		   
               $ret .= self::printline(array($i++,$job_time,$job_size,$job_name,$proceed_state,$edit),
			                           array('left;5%','left;30%','left;10%','left;45%','left;5%','left;5%'),
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
		//$urlicons = 'icons/';	
	    //if custom printer dir icon... 
		$urlicons = strstr($this->icons_path, $this->printer_name) ? 'icons/'.$this->printer_name.'/' : 'icons/';	
		
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

	//override
    protected function html_proceed_printer_job($job_id=null, $silentmode=false) {
        $user = $this->username ? $this->username : $_SESSION['user'];
	    $job_id = $_GET['job']?$_GET['job']:$job_id;
	    $job_attr = null;		

		if (!$job_id)
		  return null;
		
		if (!$silentmode) //ret is a string else a boolean
          $ret = "";		
		
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
					
                        if (($user==$this->get_printer_admin()) || ($job_owner==$user) || (!defined('AUTH_USER'))) {
						    $file_name = $this->jobs_path . $fileread;
						}
                    } 						
						
					break;	
				}
			}
		}	
		$mydir->close();
		
		if ($file_name) {
		
		  if (is_readable($file_name)) { 
		    if (!$silentmode)
		      $ret .= '<br><br>'.$fileread . "-------------------------<br>";
            //$ret .= nl2br(file_get_contents($file_name));

		    if ((class_exists('AgentIPP', true)) && ($this->username)) {//ONLY IF USERNAME..GET JOBS PER USER
		        $srv = new AgentIPP($this->authentication,
			                    self::get_printer_name(),
			                    $this->username,//??? when called what is the name ??
			                    $callback_function,//must be inside pragent class
							    $callback_param,
							    true, true);//<<<manual run...
			   
			    if (!$silentmode) {
		            $ret .= "<br>Print agent initialized!";
				    $ret .= $srv->process_job($job_id, $fileread, $job_attr);
				}
                else //boolean
				    $ret = $srv->process_job($job_id, $fileread, $job_attr, $silentmode);
		    } 
            else 
              $ret .= "<br>Print agent failed to initialized!";
			
		  } 		
			
          if (!$silentmode) {		  
            /*
		    if (is_readable($this->printer_name.'.log')) { 
		      $ret .= '<br><br>'.$this->printer_name.'.log' . "-------------------------<br>";
              $ret .= nl2br(file_get_contents($this->printer_name.'.log'));		  
		    }  
		    */
		    if (is_readable($this->jobs_path.'job'.$job_id.'.state')) { 
		      $ret .= '<br><br>'.'job'.$job_id.'.state' . "-------------------------<br>";
              $ret .= nl2br(file_get_contents($this->jobs_path.'job'.$job_id.'.state'));		  
		    }  
            /*
		    if (is_readable($this->jobs_path.'job'.$job_id.'.mystate')) { 
		      $ret .= '<br><br>'.'job'.$job_id.'.mystate' . "-------------------------<br>";
              $ret .= nl2br(file_get_contents($this->jobs_path.'job'.$job_id.'.mystate'));		  
		    } 
            */	
          }	  
		}//if file_name
		else
		  $ret = "Invalid job id.";
		
		if ((!$silentmode) || ($ret!==true)) //if silent or error...
		    return (self::html_window(null, $ret));
		else
            return ($this->form_infoprinter()); //job list		
    }		

	//override ..CUSTOM HTML-5 DRUG n DROP UPLOAD FORM
	protected function upload_job_form($printername=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;
	    $dir = $indir ? $indir.'/' : ($_SESSION['indir'] ? $_SESSION['indir'] .'/' : '/');
		$cmd = $this->external_use ? $this->procmd.'uploadjob':'uploadjob';
	
		//if ($this->username==$this->get_printer_admin()) {

           if (!empty($_FILES['jobfile']) && (!$_FILES['jobfile']['error'])) {//uploaded file
		   
		        $sFileName = $_FILES['jobfile']['name'];
                $sFileType = $_FILES['jobfile']['type'];
                $sFileSize = self::bytesToSize1024($_FILES['jobfile']['size'], 1);

		   
		        //print_r($_FILES);
			    $tpfile = $_FILES['jobfile']['tmp_name'];
			 
		        if (is_readable($tpfile)) {   
			  
				    $job_id = self::_get_job_id(); 
                    $jobname = str_replace(FILE_DELIMITER,'_',$_FILES['jobfile']['name']);
				   
		            $jobtitle = 'job'.FILE_DELIMITER.
		            $job_id.FILE_DELIMITER.
		            str_replace(':','~',$_SERVER['REMOTE_ADDR']).FILE_DELIMITER.
					$this->username.FILE_DELIMITER.
					$jobname;
					
                    //echo $this->jobs_path . $jobtitle;
                    //if (!copy($tpfile, $this->jobs_path . $jobtitle)) 	
                    if (move_uploaded_file($tpfile, $this->jobs_path . $jobtitle)) {
					    //add quota
					    self::set_user_quota(1,$this->username,$this->printer_name, $dir);
						
                        $ret = <<<EOF
<div class="s">
    <p>Your file: {$sFileName} has been successfully received.</p>
    <p>Type: {$sFileType}</p>
    <p>Size: {$sFileSize}</p>
</div>
EOF;
        						
					}	
					else	
                        //$msg = "Can not upload the job."; 					
						$ret = '<div class="f">An error occurred</div>';
                }
				//ajax call by html5-dnd
                die($ret);
				
                /*if (!$msg) //true on upload...				
			      $ret = $this->html_get_printer_jobs();	
				else //error..
				  $ret = self::html_window(null, $msg);
				  
				return ($ret);*/				
           }   	   

		//}

		  
		$menu = $this->html_printer_menu(true);  
		
		$url = $_SERVER["REQUEST_URI"];//"http://www.stereobit.gr/html5-dnd/upload.php";
		//echo $cmd.'?'.$_SERVER["REQUEST_URI"];
		
		$drugndrop = <<<EOF
        <!--div class="container"-->
            <div class="contr"><h2>Add Jobs. Drag and Drop your images to 'Drop Area'</h2>(up to 5 files at a time, size - under 2Mb)</div>		
            <div class="upload_form_cont">
                <div id="dropArea">Drop Area</div>

                <div class="info">
                    <div>Files left: <span id="count">0</span></div>
                    <!--div>Destination url: <input type="hidden" id="url" value="$url"/></div-->
                    <h2>Result:</h2>
                    <div id="result"></div>
                    <canvas width="340" height="20"></canvas>
                </div>
            </div>
	    <!--/div-->
		<input type="hidden" id="url" value="$url"/>
		<script src="html5-dnd-script.js"></script>
EOF;
			
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu
		$drugndrop		
				
		<div id="footer">
		$ver&nbsp;|&nbsp;$this->logout_url
		</div>
	</div>	
	<br/>		
EOF;
        return ($form);		
	}		
	
	//override
    /*protected function html_show_printer_job($job_id=null, $iframe=null) {
	
	    $job_id = $_GET['job']?$_GET['job']:$job_id;

		if (!$job_id)
		  return null;
		  	
		$mydir = @dir($this->jobs_path);
		
		//$ret .= '<h1>' . $printer_name . '&nbsp;Jobs'.'</h1>';	
		$ret .= "<a href='$this->urlpath?".$this->cmd."jobs&which=all'>" . 'All'  ."</a>";	
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."jobs&which=pending'>" . 'Pending'  ."</a>";	   
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."jobs&which=processing'>" . 'Processing'  ."</a>";				   
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."jobs&which=completed'>" . 'Completed'  ."</a>";
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."jobstats'>" . 'Statistics'  ."</a>";
        $ret .= '<hr/>';		
		
        while ($fileread = $mydir->read ()) { 
		    if (substr($fileread,0,4)=='job'.FILE_DELIMITER) {
			    $pf = explode(FILE_DELIMITER,$fileread);
				$jid = $pf[1];
				if ($jid==$job_id) {
				
				    if ($iframe) {
		              $ret .= '<h1>' . $this->printer_name . '&nbsp;Job&nbsp;'. $jid . '</h1>';
					  
                      if (is_readable($this->jobs_path . $fileread)) {

					    $ret .= "<IFRAME SRC=\"$this->urlpath?".$this->cmd."show&job=$jid\" 
						      TITLE=\"$this->printer_name / Job $jid\" WIDTH=800 HEIGHT=600>
                              <!-- Alternate content for non-supporting browsers -->
                              <H2>$this->printer_nameJob $jid</H2>
                              <H3>iframe is not suported in your browser!</H3>
                              </IFRAME>";
					  }
                    }
					else {
					
         			  //$out = file_get_contents($this->jobs_path . $fileread);
					  $out = self::html_job_viewer($fileread);
					  die($out);//iframe
					}
					
					break;	
				}
			}
		}	
		$mydir->close();
		
        return (self::html_window(null, $ret));		
    }	
    */
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

	//process queue ..change job status to re-process 
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
						    $file_name = $fileread;
						}
                    } 						
						
					break;	
				}
			}
		}	
		$mydir->close();

		//echo ">FILE_NAME:".$file_name.'<br>'; 
		
		if (stristr($file_name, '-completed')!==FALSE) {	//only on completed
		
            $new_state_file = str_replace('-completed','-pending',$file_name); 
            $ret = @rename($this->jobs_path . $file_name, $this->jobs_path . $new_state_file); 		
		    //echo $file_name.'>'.$new_state_file ;
			
            return ($new_state_file); //job new name				
		
		}//if file_name
		
        return false;
    }

    //enable dropbox..save test page to dropbox
    protected function enable_dropbox_jobs($job_id=null, $printername=null, $indir=null) {
	    $printername = $printername ? $printername : $this->printer_name;	
	    $dir = $indir ? $indir.'/' : ($_SESSION['indir'] ? $_SESSION['indir'] .'/' : '/');
        $username = $user ? $user : $this->username;
		$job_attr = array();
		$ret = false;	
		
		if (!$printername)		
		    return false; 	

        if ($job_id) {			

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
		}
				
		
		//execute test page for allow app callback dropbox ..directly call api
        $app_key = "geuq6gm2b5glofq";//..dropbox.printer
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
		    $storage = new \Dropbox\OAuth\Storage\Filesystem($encrypter, $userID);
	        $storage->setDirectory($this->admin_path);
 
            $OAuth = new \Dropbox\OAuth\Consumer\Curl($app_key, $app_secret, $storage, $callback);
            $dropbox = new \Dropbox\API($OAuth);
 
            if ($file_name) {
		      //$ret .= "<br>DBOXSAVE FILENAME ($userID):". $file_name ."<br>";
	        
              // Upload the file with an alternative filename
              $ret = $dropbox->putFile($file_name,$job_attr['job-name'],null,true); //alt name,path,override
			}
	        
        } 
	    catch(\Dropbox\Exception $e) {
	        //$ret = $e->getMessage() . PHP_EOL;
			self::write2disk('dropboxapi'.$printername.'.log',"ERROR:".$e->getMessage() . PHP_EOL);
        }			
		
		
		//in case of true file_read has no renamed to complete..always pending..
        return ($ret);		
    } 	

    protected function disable_dropbox_jobs($printername=null, $indir=null) {
	    $printername = $printername ? $printername : $this->printer_name;	
	    $dir = $indir ? $indir.'/' : ($_SESSION['indir'] ? $_SESSION['indir'] .'/' : '/');
        $username = $user ? $user : $this->username;
		$ret = false;	
		
		if ((!$username) || (!$printername))		
		    return false; 
			
		//remove token file
		try {
	        // Instantiate the Encrypter and storage objects
            // $key is a 32-byte encryption key (secret)
            $key = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
            $encrypter = new \Dropbox\OAuth\Storage\Encrypter($key);

            // User ID assigned by your auth system (used by persistent storage handlers)
            $userID = $username;
            // Create the storage object, passing it the Encrypter object
		    $storage = new \Dropbox\OAuth\Storage\Filesystem($encrypter, $userID);
			$storage->setDirectory($this->admin_path);
			
			$ret = $storage->delete();
        } 
	    catch(\Dropbox\Exception $e) {
	        //$ret = $e->getMessage() . PHP_EOL;
			self::write2disk('dropboxapi'.$printername.'.log',"ERROR:".$e->getMessage() . PHP_EOL);
        }

        return ($ret);		
    } 	
	
}
?>
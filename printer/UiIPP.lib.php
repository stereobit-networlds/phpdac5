<?php
/* 
 * Class UiIPP - process printer queue.
 *
 *   Copyright (C) 2012  Alexiou Vassilis, ste.net
 *
 *   This library is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU Library General Public
 *   License as published by the Free Software Foundation; either
 *   version 2 of the License, or (at your option) any later version.
 *
 *   This library is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *   Library General Public License for more details.
 *
 *   You should have received a copy of the GNU Library General Public
 *   License along with this library; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *   mailto:balexiou@stereobit.com
 *   stereobit.networlds, 27 Allatini st., 54 250 THESSALONIKI -- HELLAS
 *
 */   
 
require_once('shell/pxml.lib.php');
require_once('handlers/addhoc.php');


define("AUTH_USER", true);
define("FILE_DELIMITER", '-'); 

//define("USE_DATABASE", false);
//define("SERVER_LOG", true);


 class UiIPP  {
 
    const AUTH_USER_METHOD = 'BASIC';//''DIGEST';//BASIC//'OAUTH'
	
	protected $authentication_mechanism, $allowed_users, $server_version, $server_name;
	protected $printers_path, $jobs_path, $icons_path, $admin_path, $printers_url, $urlpath;
	protected $printer_name;
	
	protected $external_use, $cmd, $logout_url, $procmd;

    function __construct($printer=null, $auth=null, $printers_url=null, $externaluse=null, $procmd=null) {
	
       session_start(); //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< session start	
	   
	   $this->server_name = 'IPP Server';
	   $this->server_version = '1.0';
	   $this->external_use = $externaluse ? $externaluse : false;
       $this->procmd = $procmd ? $procmd : 'ipp';	   
	   $this->cmd = $externaluse ? 't='.$this->procmd : 'action='; //ipp* is the cmd extension in external dpc use
       $this->printer_name = $printer ? $printer : ($_SESSION['printer'] ? $_SESSION['printer'] : $_GET['printer']);	   
	   
	   //init
       $this->authentication_mechanism = $auth ? $auth : constant('self::AUTH_USER_METHOD');
	   $this->allowed_users = null;//..loaded in auth func
	   
	   $this->printers_url = $printers_url ? "/$printers_url/" : '/';//'/printers/';	   
	   $this->printers_path = $_SERVER['DOCUMENT_ROOT'] . $this->printers_url;//'/printers/';
	   
       $this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/';

	   if (self::is_named_printer()) {
	     $this->jobs_path .= $this->printer_name . '/'; 
		 $this->allowed_users = $this->get_printer_users($this->printer_name, $_GET['indir']);
	   }	 
	   else {
	     $indir = $_GET['indir'] ? '/' . $_GET['indir'].'/' : '/';
	     $printerfile = $_SERVER['DOCUMENT_ROOT'] . $indir . str_replace('.printer','.php',$_GET['printer']);	
		 $this->authentication_mechanism = $this->get_printer_auth(null,null,$printerfile);
         $this->allowed_users = $this->get_printer_users(null,null,$printerfile);	   
		 $this->jobs_path .= $_GET['printer'] . '/'; 
		 $this->printer_name = $_GET['printer'];
		 //echo $this->username,'>',$printerfile;
		 //echo $this->authentication_mechanism,'>';
		 //print_r($this->allowed_users);
       }	   
		 
	   $this->icons_path = $_SERVER['DOCUMENT_ROOT'] .'/icons/';
	   if (@is_dir($this->icons_path . $this->printer_name . '/')) 
	     $this->icons_path .= $this->printer_name . '/';
	   
	   $this->admin_path = $_SERVER['DOCUMENT_ROOT'] .'/admin/';
       if (self::is_named_printer()) 
	     $this->admin_path .= $this->printer_name . '/';	   
	   
	   $this->urlpath = pathinfo($_SERVER['PHP_SELF'],PATHINFO_BASENAME);

	   $this->username = $_SESSION['user'] ? $_SESSION['user'] : null;//'anonymoys';
	   //$this->printer_use_quota = $quota ? $quota : 500; //dynamic printer quota read

       $this->logout_url = "<a href='$this->urlpath?".$this->cmd."logout'>" . 'Logout'  ."</a>";	   
    }

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

          //logout...when press button????
     	  //if ((!$_SESSION['indir']) || (!$this->username)) 
		    //return ('Exit'); 	  
		  
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
			                     $ret .= self::html_header(); 
			                   break;	//else no ifamerow data
              case 'xml'     : break;	//xml row data	
			  case 'logout'  :	
			  case 'delete'  : 
			  case 'proceed' : 			  
			  case 'jobs'    : 
			  case 'jobstats': $ret .= self::html_header(); break;
              case 'netact'  : $ret .= self::html_header(null,15); break;			  
		      default        : $ret .= self::html_header();
		}
        /* //NOT HERE ERROR IN RSS (JOB DIR)
		if (intval(self::get_user_quota($this->username)) > intval(self::get_printer_quota())) {
		    //die('overquota');
			switch ($action) {
              case 'show'    : $ret .= self::expired(); break;
              case 'xml'     : die(self::expired()); break;
			  case 'logout'  : break;
			  case 'jobstats': break;
			  case 'jobs'    : break;
              case 'netact'  : 
		      default        : $ret .= self::expired();
		    }
		}		  
		*/  
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
			                   $ret .= self::html_get_printer_jobs();
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
							 $ret .= self::html_printer_menu(true);
			}  
		}	
			
        switch ($action) {
              case 'show'    : if ($iframe = $_GET['iframe'])
			                     $ret .= self::html_footer();
			                   break;	//else no ifamerow data
			  case 'xml'     : break;	//xml row data	
              case 'logout'  : $ret .= self::logout();
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
	
	protected function html_window($title=null, $data=null, $footer_title=null) {
	    $ver = $this->server_name . $this->server_version;	
		$footer_title = $footer_title ? $footer_title :	$ver.'&nbsp;|&nbsp;'.$this->logout_url;
		
	    $menu = $this->html_printer_menu(true);		
	
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu
		<div class="form_description">
			<h2>$title</h2>
			<p></p>
		</div>						

		$data
		<hr/>	
		<div id="footer">
        $footer_title
		</div>
	</div>
	<br/>

EOF;

        return ($form); 	
	}
	
    protected function html_job_viewer($file=null, $data=null, $extension=null, $encoding=null) {		

		//case image..
		$image_info = getimagesize($this->jobs_path . $file);
		
		if (!empty($image_info)) {
		    //print_r($image_info);
			//die('xxx');	
			
		    $data = @file_get_contents($this->jobs_path . $file);
			if (!$data)
                die('_EMPTY_FILE_');				
			
			header("Content-type: {$image_info['mime']}");
			die($data);	
		   
		}//case document...
		elseif ($data = @file_get_contents($this->jobs_path . $file)) {
		
		    $data = @file_get_contents($this->jobs_path . $file);
			if (!$data)
                die('_EMPTY_FILE_');			
				
		    //......check contents
			
			if ($extension) { //force view type
                $ext = $extension ? $extension : null;	
                $enc = $encoding ? $encoding : 'utf-8';					
			}
			elseif (substr($data,0,2)=='PK') {//xps pk ziped file
			    $ext = '.xps';
			}
		    elseif (substr($data,0,4)=='%!PS') {//postscript	  
		        $ext = '.ps';
			}	
		    elseif (substr($data,0,4)=='%PDF') {//pdf 
		        $ext = '.pdf';
			}	
            elseif (mb_detect_encoding($data)=="UTF-8") {
			    $ext = '.txt';//txt utf-8		
				$enc = 'utf-8';//mb_detect_encoding($data); //set encoding...
            }				
            else {
                $ext = $extension ? $extension : null;	
                $enc = $encoding ? $encoding : 'utf-8';				
			}  
			
            switch ($ext) {
			    case '.xps' : //header("Content-type: application/oxps"); break;
				              header("Content-type: application/vnd.ms-xpsdocument"); break;
			    case '.txt' : header("Content-type: text/plain; charset=$enc"); break;
		        case '.docx':
		        case '.doc' : header("Content-Type: application/vnd.ms-word"); break;		
		        case '.xlsx':
		        case '.xls' : header("Content-Type: application/vnd.ms-excel"); break;
		        case '.pdf' : header("Content-type: application/pdf"); break;
		        case '.ps'  : header("Content-type: application/postscript"); break;		   
		        default     : //$data .= $file;//header("Content-type: application/pdf");//text 
				              header("Content-type: text/plain");
							  //convert ansi to utf-8 for view
							  //header("Content-type: text/plain; charset=utf-8");
							  //die(utf8_encode($data));
            }

            die($data);			
		
		}
		else //case unknown
		   die('_UKNOWN_FILE_');			
			
	}
	
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
            </head>
            <body>';
			
	  return ($ret);		
	}	
	
	protected function html_footer() {
	
	  if ($this->external_use)
	    return null;
	
	  //no need
	  $ret = '</body></html>';
	  return ($ret);
	}		
	
    protected function html_show_printer_job($job_id=null, $iframe=null) {
	
	    $job_id = $_GET['job']?$_GET['job']:$job_id;

		if (!$job_id)
		  return null;
		  	
		$mydir = @dir($this->jobs_path);
		
		//$ret .= '<h1>' . $printer_name . '&nbsp;Jobs'./* $this->printer_state.*/'</h1>';	
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

    protected function html_proceed_printer_job($job_id=null) {
        $user = $this->username ? $this->username : $_SESSION['user'];
	    $job_id = $_GET['job']?$_GET['job']:$job_id;
	    $job_attr = null;		

		if (!$job_id)
		  return null;
		
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
		    $ret .= '<br><br>'.$fileread . "-------------------------<br>";
            //$ret .= nl2br(file_get_contents($file_name));

		    if ((class_exists('AgentIPP', true)) && ($this->username)) {//ONLY IF USERNAME..GET JOBS PER USER
		        $srv = new AgentIPP($this->authentication,
			                    self::get_printer_name(),
			                    $this->username,//??? when called what is the name ??
			                    $callback_function,//must be inside pragent class
							    $callback_param,
							    true, true);//<<<manual run...
			   
		        $ret .= "<br>Print agent initialized!";
				
				$ret .= $srv->process_job($job_id, $fileread, $job_attr);
		    } 
            else 
              $ret .= "<br>Print agent failed to initialized!";
			
		  } 		
			

		  if (is_readable($this->printer_name.'.log')) { 
		    $ret .= '<br><br>'.$this->printer_name.'.log' . "-------------------------<br>";
            $ret .= nl2br(file_get_contents($this->printer_name.'.log'));		  
		  }  
		  
		  if (is_readable($this->jobs_path.'job'.$job_id.'.state')) { 
		    $ret .= '<br><br>'.'job'.$job_id.'.state' . "-------------------------<br>";
            $ret .= nl2br(file_get_contents($this->jobs_path.'job'.$job_id.'.state'));		  
		  }  

		  if (is_readable($this->jobs_path.'job'.$job_id.'.mystate')) { 
		    $ret .= '<br><br>'.'job'.$job_id.'.mystate' . "-------------------------<br>";
            $ret .= nl2br(file_get_contents($this->jobs_path.'job'.$job_id.'.mystate'));		  
		  }  		  	
		}//if file_name
		else
		  $ret = "Invalid job id.";
		
		return (self::html_window(null, $ret));
    }		
		
    protected function html_delete_printer_job($job_id=null) {
	
        $user = $this->username ? $this->username : $_SESSION['user'];	
	    $job_id = $_GET['job']?$_GET['job']:$job_id;

		if (!$job_id)
		  return null;		  
		  	
		$mydir = dir($this->jobs_path);
		
        while ($fileread = $mydir->read ()) { 
		    if (substr($fileread,0,4)=='job'.FILE_DELIMITER) {
			    $pf = explode(FILE_DELIMITER,$fileread);
				$jid = $pf[1];
				$job_owner = $pf[3];
				
				if ($jid==$job_id) {
				
                    if (is_readable($this->jobs_path . $fileread)) {
					
					    if (($user==$this->get_printer_admin()) || ($job_owner==$user) || (!defined('AUTH_USER'))) {
                            @unlink($this->jobs_path . $fileread);		
							@unlink($this->jobs_path . 'job'.$job_id.'.state');		
							@unlink($this->jobs_path . 'job'.$job_id.'.mystate');		
					    }	
					}	
						
					break;	
				}
			}
		}	
		$mydir->close();

        $ret = $this->html_get_printer_jobs();		
		
		return ($ret);//self::html_window(null, $ret));
    }	
	
	protected function html_get_printer_jobs($which_jobs=null) {
		$wjobs = $_GET['which'] ? $_GET['which'] : $which_jobs;
		//echo $wjobs.'>';
		$user = $this->username ? $this->username : $_SESSION['user'];	
		/*$indir = $_GET['indir'] ? 
		         "&indir=".$_GET['indir'] : 
		         ($_SESSION['indir'] ? "&indir=".$_SESSION['indir'] : null);	*/	
        $jstate = array(); 		
		
        //echo $this->printers_path,'|',$this->jobs_path,'>'; //>>>>
        if (!is_dir($this->jobs_path))
		  return null; 
				  
		$printer_state = null;//self::_get_printer_state(); //file not in jobs dir... 
		
        $mydir = dir($this->jobs_path);	        		
		
        //header line		
		$ret .= '<h2>' . $this->printer_name . '&nbsp;Jobs'./* $this->printer_state.*/'</h2>';
		$ret .= "<a href='".$this->urlpath."?".$this->cmd."jobs&which=all'>" . 'All'  ."</a>";	
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."jobs&which=pending'>" . 'Pending'  ."</a>";	   
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."jobs&which=processing'>" . 'Processing'  ."</a>";				   
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."jobs&which=completed'>" . 'Completed'  ."</a>";
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."jobstats'>" . 'Statistics'  ."</a>";
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."uploadjob'>" . 'Add/Upload'  ."</a>";		
        $ret .= '<hr/>';
		
        while ($fileread = $mydir->read ()) { 
		
		    if (substr($fileread,0,4)=='job'.FILE_DELIMITER) {
				//echo $fileread,'<br>';
			    $pf = explode(FILE_DELIMITER,$fileread);
				$jid = $pf[1];//sort	
                $job_owner = $pf[3];
				
			    if (($user==$this->get_printer_admin()) || ($job_owner==$user) || (!defined('AUTH_USER'))) {				
			
			        switch ($wjobs) {
				
				        case 'completed' :if (stristr($fileread,FILE_DELIMITER.'completed')) {
				                            $jobs[intval($jid)] = $fileread;
											$jstate[intval($jid)] = 'completed';
										  }	
                                          break;
				        case 'processing':if (stristr($fileread,FILE_DELIMITER.'processing')) {
				                            $jobs[intval($jid)] = $fileread;
											$jstate[intval($jid)] = 'processing';
										  }	
                                          break;			   
				        case 'pending'   :if ((stristr($fileread,FILE_DELIMITER.'pending')) || 
						                      ((stristr($fileread,FILE_DELIMITER.'completed')==false) &&
											  (stristr($fileread,FILE_DELIMITER.'processing')==false))) { 
				                            $jobs[intval($jid)] = $fileread;
											$jstate[intval($jid)] = 'pending';
										  }	
                                          break;				
				        case 'all'       :
				        default          :$jobs[intval($jid)] = $fileread;
						                  /*$s = array_pop($pf);//due to .ext is not work!!!!
						                  if (in_array($s, array('completed','processing','pending')))
										    $jstate[intval($jid)] = $s;
										  else	
						                    $jstate[intval($jid)] = 'pending';
										  */
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
		}	
		$mydir->close();
		
		$ret .= self::printline(array('No','Job-Id','Ip','User','Job-Name','Status','&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;|&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;|&nbsp;-&nbsp;-&nbsp;-&nbsp;-&nbsp;'),
		                        array('left;5%','left;10%','left;10%','left;10%','left;35%','left;10%','left;20%'),
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
			   
			   //$ret .= $fileread . '&nbsp';
			   //../../ see job dir htaccess directives
		       $links = "<a href='$this->urlpath?".$this->cmd."show&job=".$job['id']."&iframe=1'>" . 'View'  ."</a>";	
			   $links .= "&nbsp;|&nbsp;<a href='/jobs/queue/$this->printer_name/".$job['id']."/'>" . 'Show'  ."</a>";
			   
		       $links .= "&nbsp;|&nbsp;<a href='$this->urlpath?".$this->cmd."delete&job=".$job['id']."'>" . 'Delete'  ."</a>";				   
               //$ret .= '<br/>';	
			   
			   //FORCE PROCESS.......
			   if ($jstate[$job['id']]!='completed')
                 $proceed_state = "<a href='$this->urlpath?".$this->cmd."proceed&job=".$job['id']."'>" . $jstate[$job['id']]  ."</a>";				   			   
			   else
			     $proceed_state = $jstate[$job['id']];
		   
               $ret .= self::printline(array($i++,$job['id'],$job['remote-ip'],$job['user-name'],$job['job-name'],/*$jstate[$job['id']]*/$proceed_state,$links),
			                           array('left;5%','left;10%','left;10%','left;10%','left;35%','left;10%','left;20%'),
                                       0,
			                           "center::100%::0::group_article_body::left::0::0::");									   
			}
	    }
		else {
		   $ret .= 'Empty<br>';
		}
		
		//footer line
		$ret .= '<hr>';
		$ret .= "<a href='jobs/rss/".$this->printer_name."/'>" . 'Printer RSS'  ."</a>";
		//if ($this->username==$this->get_printer_admin()) {
		  //$pname = str_replace('.printer','',$this->printer_name);
		  $ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."modprinter'>" . 'Printer properties'  ."</a>";
		  $ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."useprinter'>" . 'Printer users'  ."</a>";
		  $ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."confprinter'>" . 'Printer configuration'  ."</a>";
		//}
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."infprinter'>" . 'Printer info'  ."</a>";
		//$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."uploadjob'>" . 'Add/Upload'  ."</a>";
		
		return (self::html_window(null, $ret));			
	}

	protected function xml_get_printer_jobs($encoding=null) {
	
        //echo $this->printers_path,'|',$this->jobs_path,'>';
        if (!is_dir($this->jobs_path))
		  return null; 	

		$user = $this->username ? $this->username : null;
		  
		$mydir = dir($this->jobs_path);	

        while ($fileread = $mydir->read ()) { 
		    if (substr($fileread,0,4)=='job'.FILE_DELIMITER) {
			    $pf = explode(FILE_DELIMITER,$fileread);
				$jid = $pf[1];//sort
				$job_owner = $pf[3];
				
				//if (($user=='admin') || ($job_owner==$user) || (!defined('AUTH_USER')))
				//  $jobs[intval($jid)] = (array) $pf ; 
			    if (($user==$this->get_printer_admin()) || ($job_owner==$user) || (!defined('AUTH_USER'))) {
				
				    if (stristr($fileread,FILE_DELIMITER.'completed'))
					    $jstate = 'completed';
					elseif (stristr($fileread,FILE_DELIMITER.'processing'))
					    $jstate = 'processing';
					elseif (stristr($fileread,FILE_DELIMITER.'pending'))
					    $jstate = 'pending';
					else
					    $jstate = 'pending';
						
					$jtime = date (DATE_RFC822, filemtime($this->jobs_path . $fileread));	
					$jsize = filesize($this->jobs_path . $fileread);	//bytes
						
				    $jobs[intval($jid)] = array('name'=>$fileread, 'job'=>$pf, 'state'=>$jstate, 
					                            'date'=>$jtime, 'size'=>$jsize);
				}				
			}
		}	
		$mydir->close();
		
		if (is_array($jobs)) {		
		    
			krsort($jobs);
			
		    $xml = new pxml();
            $xml->encoding = $encoding?$encoding:'utf-8';
		    $xml->addtag('rss',null,null,"version=2.0");							
	        $xml->addtag('channel','rss',$this->printer_name.' Jobs',null);
	        $xml->addtag('title','channel','Printer Jobs Monitor',null);								
	        $xml->addtag('link','channel','http://'.$_ENV["HTTP_HOST"].'/jobs/',null);									
	        $xml->addtag('description','channel','Printer Jobs Monitor, stereobit.networlds',null);									
	        $xml->addtag('language','channel','en',null);									
	        $xml->addtag('pubDate','channel',date(DATE_RFC822),null);									
	        $xml->addtag('lastBuildDate','channel',date(DATE_RFC822),null);	
	        $xml->addtag('docs','channel',null,null);																	
	        $xml->addtag('generator','channel','IPP Server '.$this->server_version.' | stereobit.networlds',null);									
	        $xml->addtag('managingEditor','channel',$this->printer_name,null);									
	        $xml->addtag('webMaster','channel',null,null);									
	        $xml->addtag('ttl','channel','15',null);				
		
		    foreach ($jobs as $jid=>$params) {	
			   $job_file = $params['name'];
	           $job_id = $params['job'][1];
	           $job_remote_ip = str_replace('~',':',$params['job'][2]);
	           $job_user_name = $params['job'][3];
		       $job_name = $params['job'][4];	
               $job_status = $params['state'];			   
			   $job_time = $params['date'];
			   $job_size = $params['size'];			

               $link = "http://".$_ENV["HTTP_HOST"]."/jobs/queue/".$this->printer_name."/".$job_id."/";//."&action=xml";			
		
			   $xml->addtag('item','channel',null,null);				   
			   
               $xml->addtag('title','item',$xml->cdata($job_name),null);				   
               $xml->addtag('link','item',$link,null);
               $xml->addtag('description','item',$xml->cdata($job_status),null);				   			   				   			   
			   $xml->addtag('author','item',$job_user_name,null);
			   $xml->addtag('category','item',$this->printer_name,null);
			   $xml->addtag('comments','item',$job_status,null);
               $xml->addtag('pubDate','item',$job_time,null);				   			   
               $xml->addtag('guid','item',$job_id,null);	
			}
			
		    echo $xml->getxml(1);
			
			return true;
	    }
 
        return false; 		
	}	
	
	protected function html_get_printer_stats($summary=false, $set_quota=false) {

		$user = $this->username ? $this->username : $_SESSION['user'];
		$indir = $_SESSION['indir'] ? $_SESSION['indir'] : $_GET['indir'];		
		
        if (!is_dir($this->jobs_path))
		  return null; 	
		  
		$printer_state = null;//self::_get_printer_state(); //file not in jobs dir... 
		
        $mydir = dir($this->jobs_path);	        		
		
        //header line		
		if (!$summary) {
		$ret .= '<h2>' . $this->printer_name . '&nbsp;Jobs'/* .$this->printer_state*/.'</h2>';	
		$ret .= "<a href='".$this->urlpath."?".$this->cmd."jobs&which=all'>" . 'All'  ."</a>";	
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."jobs&which=pending'>" . 'Pending'  ."</a>";	   
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."jobs&which=processing'>" . 'Processing'  ."</a>";				   
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."jobs&which=completed'>" . 'Completed'  ."</a>";
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."jobstats'>" . 'Statistics'  ."</a>";
        $ret .= '<hr/>';
        }		

        while ($fileread = $mydir->read ()) { 
		
		    if (substr($fileread,0,4)=='job'.FILE_DELIMITER) {
				
			    $pf = explode(FILE_DELIMITER,$fileread);
				$jid = $pf[1];//sort	
                $job_owner = $pf[3];
				
			    if (($user==$this->get_printer_admin()) || ($job_owner==$user) || (!defined('AUTH_USER'))) {
				
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

		if (is_array($jobs)) {
		    
			if (!$summary)
		      $ret .= self::printline(array('No','Date','Ip','Name','Size','Status'),
			                          array('left;5%','left;25%','left;20%','left;30%','left;10%','left;10%'),
									  1,
			                          "center::100%::0::group_article_body::left::0::0::");		
		    
			$jobs_sum = array();
			
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
			   
			   //$ret .= sprintf('%s %s %d bytes %s<br>',$job_time,$job_name,$job_size,$job_status);
			   if (!$summary)
			     $ret .= self::printline(array($i++,$job_time,$job_remote_ip,$job_name,$job_size,$job_status),
			                             array('left;5%','left;25%','left;20%','left;30%','left;10%','left;10%'),
									     0,
			                             "center::100%::0::group_article_body::left::0::0::");

			   $jobs_sum['total-jobs'] += 1;
               $jobs_sum[$job_status] += 1;
               $jobs_sum['total-size'] += $fileattr['size'];//$job_size = kb convertion; 			   
			}
			$ret .= '<hr>';
			$ret .= sprintf ('Completed Jobs:%d <br>',$jobs_sum['completed']);
			$ret .= sprintf ('Prosessing Jobs:%d <br>',$jobs_sum['prosessing']);
			$ret .= sprintf ('Pending Jobs:%d <br>',$jobs_sum['pending']);
			$ret .= sprintf ('Total Jobs:%d <br>',$jobs_sum['total-jobs']);
			//$ret .= sprintf ('Total Size:%d kb<br>',floatval($jobs_sum['total-size']/1024));
			$ret .= sprintf ('Total Size:%s<br>',self::bytesToSize1024($jobs_sum['total-size'],1));
	    }	
		else {
		   if (!$summary)
		     $ret .= 'Empty<br>';
		}
		
		$current_bytes = intval(@file_get_contents($this->printers_path . $this->printer_name.'.counter'));
		//$ret .= sprintf ('Total Process Size:%d kb<br>',floatval($current_bytes/1024));
		$ret .= sprintf ('Total Size:%s<br>',self::bytesToSize1024($current_bytes,1));
		
        if ($set_quota) {
            //reset quota
			self::set_user_quota($jobs_sum['completed'],$user,$this->printer_name, $indir, true);
        }	
		
		//footer line
		if (!$summary) {
		$ret .= '<hr>';
		$ret .= "<a href='jobs/rss/".$this->printer_name."/'>" . 'Printer RSS'  ."</a>";
		//if ($this->username==$this->get_printer_admin()) {
		  //$pname = str_replace('.printer','',$this->printer_name);
		  $ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."modprinter'>" . 'Printer properties'  ."</a>";
		  $ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."useprinter'>" . 'Printer users'  ."</a>";
		  $ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."confprinter'>" . 'Printer configuration'  ."</a>";
		//}
		$ret .= "&nbsp;|&nbsp;<a href='".$this->urlpath."?".$this->cmd."infprinter'>" . 'Printer info'  ."</a>";		     		
        }
		
		if ($summary)
		  return ($ret);
		else  
          return (self::html_window(null, $ret));  		
	}

	protected function html_get_printer_menu($iconsview=null, $p=null) {
	    //if custom printer dir icon... 
		$urlicons = strstr($this->icons_path, $this->printer_name) ? 'icons/'.$this->printer_name.'/' : 'icons/';	

        $icons = array();		
		$user = $this->username ? $this->username : $_SESSION['user'];
		$indir = $_SESSION['indir'] ? $_SESSION['indir'] : $_GET['indir'];
		
	    $ret .= '<h2>' . $this->printer_name /*.' - '. $this->printer_state*/.'</h2>';
		//echo "<a href='jobs/?printer=".$printer_name,"'>" . 'Show jobs'  ."</a>";
		//$ret .= "<a href='jobs/queue/".$printer_name."/'>" . 'Show printer jobs'  ."</a><br/>";
		
		if ($iconsview) {
		  $icons[] = $this->urlpath."?".$this->cmd."jobs:Printer Jobs";
		  $icons[] = "jobs/rss/".$this->printer_name.'/:Printer RSS';
		}
		else {
		  $ret .= "<a href='".$this->urlpath."?".$this->cmd."jobs'>" . 'Show printer jobs'  ."</a><br/>";
		  $ret .= "<a href='jobs/rss/".$this->printer_name."/'>" . 'Printer RSS'  ."</a><br/>";
		}
		
		//if ($user == $this->get_printer_admin()) {
		   //$ret .= '<hr>';
		   
           $pname = str_replace('.printer','',$this->printer_name);
		   
		   if ($iconsview) {
		     $icons[] = $this->urlpath."?".$this->cmd."modprinter:Printer Properties";
		     $icons[] = $this->urlpath."?".$this->cmd."useprinter:Printer Users";
			 $icons[] = $this->urlpath."?".$this->cmd."confprinter:Printer Configuration";
		   }
		   else {		   
		     $ret .= "<a href='".$this->urlpath."?".$this->cmd."modprinter'>" . 'Printer properties'  ."</a><br/>";
		     $ret .= "<a href='".$this->urlpath."?".$this->cmd."useprinter'>" . 'Printer users'  ."</a><br/>";
		     $ret .= "<a href='".$this->urlpath."?".$this->cmd."confprinter'>" . 'Printer configuration'  ."</a><br/>";
		   }
		   
		   //admin quota (must sum quota of all users of this printer!)
           $user_quota = self::get_user_quota($user);
		   $printer_quota = self::get_printer_quota();
		   //echo $user,':',$user_quota,'>',$printer_quota;
		   if (intval($user_quota) > intval($printer_quota)) {
             if ($pd = str_replace('/','',$indir))		   
               $item = $indir.'-'.$pname;
			 else
               $item = $pname;  		   
			 $ret .= "<a href='download.php?g=$item'>" . 'Feed the Printer'. "($user : $user_quota > $printer_quota)"  ."</a><br/>";
		   }    		   
		//}
		
		if ($iconsview) {
		  $icons[] = $this->urlpath."?".$this->cmd."infprinter:Printer Info";		
		}  
		else {	 
          $ret .= "<a href='".$this->urlpath."?".$this->cmd."infprinter'>" . 'Printer info'  ."</a><br/>";		
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
			   $px = $p ? $p : '18%';
	           $attr[] = 'left;'.$px;
			}	
            //print_r($icco);			
			$ret = self::printline($icco,$attr,0,"center::100%::0::group_article_body::left::0::0::");			
		}
		
		return ($ret);
	}

	//alias
	public function html_printer_menu() {
	
	    $ret = $this->html_get_printer_menu(true);
		return ($ret);
	}

	public function html_get_printers($indir=null) {
		$urlicons = 'icons/';	
		$indir = $indir ? $indir : ($_GET['indir'] ? $_GET['indir'] : $_SESSION['indir']);
		$nd = null;
		//echo $this->printers_path,'|',$this->jobs_path,'>';
		/*
	    if ($indir) {
		  $mydir = @dir($_SERVER['DOCUMENT_ROOT'] ."/$indir/");
		  $nd = "&indir=$indir";
		}  
	    else
          $mydir = @dir($this->printers_path); //default dir 'printers'
		*/
		$nd = "&indir=$indir";
        $mydir = @dir(self::get_printer_path()); 		
		
        if (!$mydir)
		  return;
		
		$ret = '<h1>' . 'Printers' . '</h1>';	
		
		$data = array();
		$attr = array();
        while ($fileread = $mydir->read ()) { 
		    
			if (stristr($fileread,'.php')) {
			
			   if ((!$this->get_printer_auth(null,null,$fileread/*, $indir*/)) ||
			      (strstr($fileread,'index'))) //no index files
			      continue;
			   
			   $printer_name = str_replace('.php','.printer',$fileread);
		       //$ret .= "<a href='".$this->printers_url . $printer_name."'>" . $printer_name  ."</a><br/>";
			   //echo $this->icons_path.$printer_name.'.png';
			   if (is_file($this->icons_path.$printer_name.'.png'))
			     $ifile = $urlicons.$printer_name.'.png';
			   else
			     $ifile = $urlicons.'index.printer.png';
			   
			   $icon = "<a href='".$this->urlpath."?".$this->cmd."printer&printer=".$printer_name.$nd."'><img src='" . $ifile."' border=0 alt='$printer_name'></a>";
			   $link = "<a href='".$this->urlpath."?".$this->cmd."printer&printer=".$printer_name.$nd."'>" . $printer_name  ."</a>";

			   $ret .= self::printline(array($icon,$link),array('left;1%','left;99%'),
			                           0,"center::100%::0::group_article_body::left::0::0::");
			   $ret .= '<hr>';						   
			}
			unset ($data);
			unset ($attr);
	    }
		
        $mydir->close();

        return ($ret);		
	}

	protected function html_get_network_activity($indir=null) {
	    $indir = $indir ? $indir : ($_SESSION['indir'] ? $_SESSION['indir'] : $_GET['indir']);
        
		$ret .= '<h1>' . 'Network activity' . '</h1>';
		
		/*
	    if ($indir)	
		   $netfile = $_SERVER['DOCUMENT_ROOT'] ."/$indir/" . 'network.log';		
        else
           $netfile = $this->printers_path . 'network.log';	
		*/   
        $netfile = self::get_printer_path() . 'network.log'; 		   
		  
		if ($data = @file_get_contents($netfile)) {	
		 
          $ndata = nl2br($data);
          $ret .= $ndata;		
		}	

        return ($ret);		
	}
	
	protected function get_user_quota($user=null, $printer=null, $indir=null) {
	    $pname = $printer ? $printer : $this->printer_name;
		$puser = $user ? FILE_DELIMITER.$user : FILE_DELIMITER.$this->username;
		$indir = $indir ? $indir : ($_SESSION['indir'] ? $_SESSION['indir'] : null);
		$qfile = $pname.$puser.'.quota';
	    
        /*		
	    if ($indir)
		  $myqfile = $_SERVER['DOCUMENT_ROOT'] ."/$indir/" . $qfile;
        else		  
	      //$myqfile = $this->printers_path . $qfile;
		  $myqfile = $this->printers_path . str_replace('.php','.printer',$_SERVER['PHP_SELF']).$puser.'.quota';
		*/  
		$myqfile = self::get_printer_path() . $qfile;  
	    //echo $indir,'>',$myqfile,'<br>';
		
        if ($quota = @file_get_contents($myqfile)) {
		    return (intval($quota)); 
        }
		
		//.....if admin count all users
		//..............................
        
        return false; 		
	}		
	
	//check if it is main (index.php) printer
	protected function get_printer_name() {
	   
	    if (($this->printer_name!='index.php') && ($this->printer_name!='AUTO'))
		    return ($this->printer_name);
		
        return false;		
	}	
	
	protected function is_named_printer() {
	  
	    //return ($this->printer_name);
		return (self::get_printer_name()); 
	}	
	
	protected function invalid_login() {
	  
	    //$ret = $this->html_get_printers();
		$ret = "Invalid operation";
	  
	    return ($ret);
	}		
	
	protected function expired() {
	  
	    //$ret = $this->html_get_printers();
	
		$ret= '<h2>You are overlimit. Please feed your <a href="http://stereobit.gr/download.php?g=art">printer</a>.</h2>';
		
		return ($ret);
	}	
    
    protected function get_printer_admin() {
	
	    if (is_object($this->authentication)) {
	   
	      $this->user_admin = $this->authentication->get_user_admin();
	    }
	    else
	      $this->user_admin = 'admin';
		  
	   	return ($this->user_admin);  
    }	
	
	public function get_printer_users($printer=null,$indir=null,$bootstrapfile=null) {
	   
		$params = $this->parse_printer_file($printer, $indir, $bootstrapfile);
		  
		return ($params['users']);
	   
	}	
	
	public function get_printer_auth($printer=null,$indir=null,$bootstrapfile=null) {
	   
		$params = $this->parse_printer_file($printer, $indir, $bootstrapfile);
		  
		return ($params['auth']);

	}

	public function get_printer_path($printer=null,$indir=null) {
	    $pname = $printer ? $printer : $this->printer_name; 
		$indir = $indir ? $indir : ($_SESSION['indir'] ? $_SESSION['indir'] : '/');
	    //echo $indir,'>',$pname,'>',$_SERVER['PHP_SELF'],'>';
		
		$dparts = explode('/',$_SERVER['PHP_SELF']);
		$pname_self = array_pop($dparts);
		//echo $pname_self,'>';

		//if printername = self container file (=local ui run)
        if ($pname==str_replace('.php','.printer',$pname_self)) {
            //echo 'A>';		
			$printer_path = $this->printers_path . implode('/',$dparts) .'/';
			//str_replace('.php','.printer',$_SERVER['PHP_SELF']);			
		}
		elseif ($indir) {
		    //echo 'B>';
			$printer_path = $this->printers_path . "/$indir/";
		}
		else {
		    //echo 'C>'; 
		    $printer_path = $this->printers_path;//$_SERVER['DOCUMENT_ROOT'];
		}
       
        //echo $printer_path;
		$ret = str_replace('//','/',$printer_path);
        return ($ret);		
	}	
	
	public function get_printer_quota($printer=null,$indir=null,$bootstrapfile=null) {
	   
		$params = $this->parse_printer_file($printer, $indir, $bootstrapfile);
		  
		return (intval($params['quota']));

	}	
	
	public function set_user_quota($quota=1,$user=null,$printer=null,$indir=null,$clear=null) {	
		$pname = $printer ? $printer : $this->printer_name; 
		$puser = $user ? FILE_DELIMITER.$user : ($this->username ? FILE_DELIMITER.$this->username : null);
        $qfile = $pname.$puser.'.quota';		
		$quota = $quota ? $quota : 1;
		$ret = false;
		/*
        if ($indir) 
		  $file = $_SERVER['DOCUMENT_ROOT'] . "/$indir/" . $qfile;
        else
          $file = $this->printers_path . $qfile;		
	    */
		$file = self::get_printer_path() . $qfile;
		
        if ($clear) {
		    $ret = @file_put_contents($file, $quota, LOCK_EX);
		}
		elseif ($prev_quota = @file_get_contents($file)) {
		
		    $new_quota = intval($prev_quota) + intval($quota);
		    $ret = file_put_contents($file, $new_quota , LOCK_EX);
		
		    return ($new_quota); 
        }
        else
		    $ret = @file_put_contents($file, $quota, LOCK_EX);	
			
		//$ret = @file_put_contents($this->printers_path . $pname.$puser.'.quota', $jobs_sum['completed'], LOCK_EX);		
		
	    return ($ret);
	}
	
	//PARSE
	public function parse_printer_file($name=null,$indir=null,$bootstrapfile=null) {
	    $name = $name ? $name : $this->printer_name;
		$indir = $indir ? $indir : ($_SESSION['indir'] ? $_SESSION['indir'] : '/');	
	
	    if (!$file = $bootstrapfile) {

		  $name = str_replace('.printer','',$name);
          $file = self::get_printer_path($name, $indir) . $name .'.php';		
		  
		}//bootstrapfile
		
		//echo '<br>',$bootstrapfile,'>',$file,'>',$indir;
		
		
		//$data = file_get_contents($file);
        //self::write2disk('myauth.log',"\r\n>".$_SERVER['PHP_SELF'].'|'.$file.'>'.$data."\r\n");	
		
		if (@is_readable($file)) { 		

        $lines = explode(';',file_get_contents($file));
        foreach ($lines as $i=>$ln) {
		   if ($i==1) {
		      $lex = explode(',',$ln);
			  foreach ($lex as $l=>$lx) {
			    if (stristr($lx,'array')) {
				   $u = explode('=>',str_replace('array(','',$lx));
				   $params['users'][str_replace("'","",$u[0])] = str_replace("'","",$u[1]);
				}
				elseif (stristr($lx,'=>')) {
				   $u = explode('=>',$lx);
				   $params['users'][str_replace("'","",$u[0])] = str_replace("'","",$u[1]);
				}
				elseif ( (stristr($lx,'BASIC')) || (stristr($lx,'DIGEST')) || 
				         (stristr($lx,'SIMPLE')) || (stristr($lx,'OAUTH')) ) {
				   $params['auth'] = str_replace("'","",trim($lx));
				}
				elseif ($lx>1) {
				   $params['quota'] = trim($lx);
				}				
			  }
			  //echo '<pre>'; print_r($lex); echo '</pre>';
			  //$p = var_export($params,1);
			  //self::write2disk('myauth.log',"\r\n>".$p."\r\n");
			  return ($params);
		   }
        } 
        }		
	}

	public function parse_printer_conf($name=null, $indir=null, $confile=null) {
	    $name = str_replace('.printer','',$name);
	    
	    if (!$file = $confile) {
          /*if ($indir) 
		    $file = $_SERVER['DOCUMENT_ROOT'] . "/$indir/" . $name .'.printer.conf';
          else
            //$file = $this->printers_path . $name .'.printer.conf';
		    $file = $this->printers_path . str_replace('.php','',$_SERVER['PHP_SELF']).'.printer.conf';
		  */
          $file = self::get_printer_path() . $name .'.printer.conf';			  
		}  
        //echo $file;
        $params = @parse_ini_file($file, true);	
        return ($params);		
	}

	public function save_printer_conf($name=null, $indir=null, $conf=null, $confile=null) {
	    $name = str_replace('.printer','',$name);
	
	    if (!$conf)
		  return;
	    
		if (!$file = $confile) {
          /*if ($indir) 
		    $file = $_SERVER['DOCUMENT_ROOT'] . "/$indir/" . $name .'.printer.conf';
          else
            //$file = $this->printers_path . $name .'.printer.conf';
		    $file = $this->printers_path . str_replace('.php','',$_SERVER['PHP_SELF']).'.printer.conf';
		  */
          $file = self::get_printer_path() . $name .'.printer.conf'; 		  
        }
		
        $ret = file_put_contents($file,$conf);
		  
        return (true);		
	}	
	
	
	//AUTH
	protected function authenticate_user() {
	  
	    //if ((!$_GET['t']) || (!defined('AUTH_USER')))
	      //return true;
	    if (!defined('AUTH_USER')) {
			$_SESSION['printer'] = $this->printer_name;	
			$_SESSION['user'] = 'anonymoys';//$this->username;			
	        return true;		  
		}  

        if ($this->authentication_mechanism) {
		
		    if ($this->authentication_mechanism=='NONE') {
			    $_SESSION['printer'] = $this->printer_name;	
				$_SESSION['user'] = 'anonymoys';//$this->username;	
			    return true;		
			}	
		    
		    $this->authentication = new AuthIPP($this->authentication_mechanism, $this->allowed_users, 'crc32');		

            $this->username = $this->authentication->http_auth();
            //register printer
			if ($this->username) {
                $_SESSION['printer'] = $this->printer_name;	
				$_SESSION['user'] = $this->username;	
			}	
		}		
		
		if ($this->username) {   
		  self::write2disk('login.log',"\r\n".$this->server_time.":".$this->printer_name.":".
		                               $_SERVER['REMOTE_ADDR'].":".$this->username);

          return true;									   
		}							 

	    return false;
	}
	
	
    protected function logout($html=null) {

       //session_destroy();
	   
       if (isset($_SESSION['user'])) {
          $_SESSION['user'] = null; 
		  $_SESSION['printer'] = null;
          $_SESSION['indir'] = null;
		  
          $ret .=  "Exit<br>";
          //echo '<p><a href="?action=logIn">LogIn</a></p>';
       }

       return ($ret);	   
    }	
	
	
	//PRINTER CONF
	public function html_add_printer($name=null, $auth=null, $quota=null, $users=null, $indir=null) {	
	    $name = str_replace('.printer','',$name);
	    $fname = $name ? $name : 'index';
		$pname = $name ? $name . '.printer' : null;
		$pauth = $auth ? ",'$auth'" : ",'BASIC'";
		$pquota = $quota ? ",$quota" : ",10";
		if (!empty($users)) {
		  $pusers = ",array(";
		  foreach ($users as $username=>$password)
		    $pusers .= "'" . $username ."'=>'".$password."',";
		  $pusers .= ")";	
        }
		else
		  $pusers = ",array('admin'=>'admin',)"; 
	
	    $code0 = "<?php	
include ('../printers/ippserver/ListenerIPP.php');
\$listener = new IPPListener('$pname' $pauth $pquota $pusers);
\$listener->ipp_send_reply(); 
?>";

	    $code1 = "<?php
include ('../printers/ippserver/ListenerIPP.php');
\$listener = new IPPListener('$pname' $pauth $pquota $pusers);
\$listener->ipp_send_reply(); 
?>";
     
	    $htaccess = "
RewriteEngine On

RewriteRule ^(.*)\.prn$ $1.php [L] 
RewriteRule ^(.*)\.printer$ $1.php [L] 

RewriteRule .* - [E=DEVMD_AUTHORIZATION:%{HTTP:Authorization}]		
";	

        $printer_config = "
[SERVICES]
test=true

[PARAMS]
services=all ;any/all/null
             ;depend on how services are applied, 
			 ;any will produce true=complete job if one of services is true
             ;all will produce true=complete job if all services is true
			 ;null = any		
";		
        
        if ($indir) {
		  $myprinter_path = self::get_printer_path();//$_SERVER['DOCUMENT_ROOT'] ."/$indir/"; 
		  
		  if (!is_dir($myprinter_path)) {
			    @mkdir($myprinter_path, 0755);
				//copy/make files ...
				$htp = fopen($myprinter_path . ".htaccess",w); 
                fputs($htp,$htaccess); 
				fclose($htp);
		  }		
				
		  //user directory		
		  $file = $myprinter_path . $fname .'.php';
          $conf = $myprinter_path . $fname .'.printer.conf';		  

		  if (!is_readable($file)) {//not overwrite
		    $ret = file_put_contents($file, $code1);
            $ret = file_put_contents($conf, $printer_config);  			
		  }	
        }
        else {// printer directory		
          $file = /*$this->printers_path*/self::get_printer_path() . $fname .'.php';
		  $conf = /*$this->printers_path*/self::get_printer_path() . $fname .'.printer.conf';
		
		  if (!is_readable($file)) {//not overwrite
		    $ret = file_put_contents($file, $code0);
			$ret = file_put_contents($conf, $printer_config);			
		  }	
		}  
		//echo $file;
		return ($ret);
	}	
	
    public function html_mod_printer($name=null, $auth=null, $quota=null, $users=null, $indir=null) {
		$printername = $name ? $name : ($_POST['printername']?$_POST['printername']:$this->printer_name);
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
		$name = str_replace('.printer','',$printername);
	 
        if (!$name)
            return false;	

        $params = $this->parse_printer_file($name, $printerdir);		
        //print_r($params);	
		if ($quota>1)
          $quota = $params['quota'] + $quota; //addon		
	    //else reset to 1
		
        $fname = $name ? $name : null;	
	    $pname = $name ? $name . '.printer' : null;
		
        $pauth = $auth ? ",'$auth'" : ",'".$params['auth']."'"; //as is
		$pquota = $quota ? ",$quota" : ','.$params['quota']; //as is
		
		if (empty($users)) 
		  $users = (array) $params['users'];
		
		$pusers = ",array(";
		foreach ($users as $username=>$password)
		  $pusers .= "'" . $username ."'=>'".$password."',";
		$pusers .= ")";	

	    $code0 = "<?php	
include ('../ippserver/ListenerIPP.php');
\$listener = new IPPListener('$pname' $pauth $pquota $pusers);
\$listener->ipp_send_reply(); 
?>";

	    $code1 = "<?php
include ('../printers/ippserver/ListenerIPP.php');
\$listener = new IPPListener('$pname' $pauth $pquota $pusers);
\$listener->ipp_send_reply(); 
?>";		  

  
        /*if ($indir) {
		  $myprinter_path = $_SERVER['DOCUMENT_ROOT'] ."/$indir/"; 

          $file = $myprinter_path . $fname .'.php';
          $ret = file_put_contents($file, $code1);   		  
        }
        else {
		
          $file = $this->printers_path . $fname .'.php';
          $ret = file_put_contents($file, $code1); 		  
        }*/	
		$myprinter_path = self::get_printer_path();
		$file = $myprinter_path . $fname .'.php';
		$ret = file_put_contents($file, $code1);
		
		//echo $file,'>',$indir;
		//$ret .=  seturl('t=modprinter','..Config..');
		
        return ($ret);		
	}

    public function html_info_printer($name=null, $indir=null) {
		$printername = $name ? $name : $this->printer_name;
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
		$name = str_replace('.printer','',$printername);	
    
        if (!$name)
            return false;
		
        $ret = self::html_get_printer_menu();			
			
        return ($ret);			
    }	
	
	//PRINTER FORMS........		
	
	//ADD NEW PRINTER
	public function form_addprinter($name=null, $auth=null, $quota=null, $users=null, $indir=null) {
	    $printername = $name ? $name : $_POST['printername'];
		$printerauth = $auth ? $auth : $_POST['printerauth'];
		$printerquota = $quota ? $quota : $_POST['printerquota'];//10
		$printerusers = is_array($users) ? $users : null;
		$printerdir = $indir ? $indir : ($_POST['indir']?$_POST['indir']:($_GET['indir'] ? $_GET['indir'] : '/'));
		
	    if ($this->username!=$this->get_printer_admin())	
		   return (self::html_window(null, 'Not allowed!'));		
		
		if (!$printername) {
		  $ret = self::add_printer_form(null,$name,$auth,$quota,$users,$indir);
		  return ($ret);
		}  
		
        $ok = self::html_add_printer($printername,
			                         $printerauth,
                                     $printerquota,
                                     $printerusers,
                                     $printerdir); 		
		
		$msg = $ok ? 'Success' : 'Failed';
		
		if ($ok) //modify..set conf params		
		  $ret .= self::mod_printer_form($msg,$printername,$printerauth,$printerquota,$printerusers,$printerdir);
		else 
		  $ret .= self::add_printer_form($msg,$name,$auth,$quota,$users,$indir);
		
		return ($ret);
	}
		
	protected function add_printer_form($message=null, $name=null, $auth=null, $quota=null, $users=null, $indir=null) {
		$ver = $this->server_name . $this->server_version;
		$indir = $indir ? $indir : $_SESSION['indir'];		
		$msg = $message ? '&nbsp;:&nbsp;' . $message : null;
		$basic_check = "checked='checked'"; 
		$cmd = $this->external_use ? $this->procmd.'addprinter':'addprinter';		
			
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>			
		
	<div id="form_container">

		<form id="form_470441" class="appnitro" enctype="multipart/form-data" method="post" action="">
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
		</li>		
		<li id="li_4" >
		<label class="description" for="indir">Printer Path</label>
		<div>
			<input id="element_4" name="indir" class="element text medium" type="text" maxlength="13" value="$indir"/> 
		</div><p class="guidelines" id="guide_4"><small>Printer path</small></p> 
		</li>		
		<li id="li_3" >
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
				<!--input type="hidden" name="indir" value="$indir" /-->
				<input type="hidden" name="FormAction" value="$cmd" />
			    
				<input id="saveForm" class="button_text" type="submit" name="Submit" value="Create Printer" />
		</li>
			</ul>
		</form>	
		<div id="footer">
		$ver&nbsp;|&nbsp;$this->logout_url
		</div>		
	</div>

EOF;

        return ($form);	
	
	}	
	
	//MODIFY PRINTER PARAMS
	public function form_modprinter($name=null, $auth=null, $quota=null, $users=null, $indir=null) {
	    $printername = $name ? $name : ($_POST['printername']?$_POST['printername']:$this->printer_name);
		$printerauth = $auth ? $auth : $_POST['printerauth'];
		$printerquota = $quota ? $quota : $_POST['printerquota'];
		$printerusers = is_array($users) ? $users : array('admin'=>'admin','user1'=>'test123');
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
		$cmd = $this->external_use ? $this->procmd.'modprinter':'modprinter';
		
	    if ($this->username!=$this->get_printer_admin()) 
		   return (self::html_window(null, 'Not allowed!'));		
		
		if (!$printername)
		  return ('Unknown printer!');
		  
        //$ret = $this->html_printer_menu(true);		
		$params = $this->parse_printer_file($printername, $printerdir);
		//print_r($params);		
		if (empty($params))
		  return ('Unknown printer file!');		
		
		if ($_POST['FormAction']!=$cmd) {
		  
		  $ret .= $this->mod_printer_form(null,$printername,$params['auth'],$params['quota'],$params['users'],$printerdir);
		  return ($ret);
		}		
	
	    //$ret = 'modify printer...';
		
		//override users
		$printerusers = (!empty($params['users'])) ? $params['users'] : $printerusers;
		
        $ok = $this->html_mod_printer($printername,
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
	    $ver = $this->server_name . $this->server_version;
	    $msg = $message ? '&nbsp;:&nbsp;' . $message : null;
		$oauth_check = $basic_check = $simple_check = null;
		$cmd = $this->external_use ? $this->procmd.'modprinter':'modprinter';
		
		switch (str_replace("'","",$auth)) {
		  case 'OAUTH' : $oauth_check = "checked='checked'"; break;		
		  case 'BASIC' : $basic_check = "checked='checked'"; break;		
		  case 'SIMPLE': 
		  default      : $simple_check = "checked='checked'";
		  
		}
		//echo $auth,'>';
		$menu = $this->html_printer_menu(true);	
	
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>			
		
	<div id="form_container">
	
		$menu
		<form id="form_470441" class="appnitro" enctype="multipart/form-data" method="post" action="">
					<div class="form_description">
			<h2>Modify printer : $name $msg</h2>
			<p>Printer configuration.</p>
		</div>						
			<ul >
			
		<li id="li_3" >
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
				<input type="hidden" name="FormAction" value="$cmd" />
			    
				<input id="saveForm" class="button_text" type="submit" name="Submit" value="Modify Printer" />
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


	//PRINTER USERS
	public function form_useprinter($printername=null, $indir=null) {
	    $printername = $name ? $name : ($_POST['printername']?$_POST['printername']:$this->printer_name);
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
		$cmd = $this->external_use ? $this->procmd.'useprinter':'useprinter';
        $printerusers = array();

	    if ($this->username!=$this->get_printer_admin()) 
		   return (self::html_window(null, 'Not allowed!'));		
		
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
        
		$existing_users = $this->get_printer_users($printername, $printerdir);
		$existed = count($existing_users);
		//echo $existed;
        //get user post data	
        for ($i=1;$i<6;$i++) {
		    $post_user = 'user'.$i.'name';
			$post_pass = 'pass'.$i.'word';
		    if (($u = $_POST[$post_user]) && ($p = $_POST[$post_pass])) {
			   
			    if ($i<=$existed)
			        $printerusers[$u] = $p; //existed hashed users...
			    else
			        $printerusers[$u] = hash('crc32',$p); //<hash new entries
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
	
	protected function users_printer_form($message=null, $name=null, $users=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;
		$cmd = $this->external_use ? $this->procmd.'useprinter':'useprinter';
	
	    $user_fields = '
        <li id="li_4" >
		<label class="description" for="user<@>">User <@> </label>
		<span>
			<input id="element_<@>_1" name="user<@>name" class="element text" maxlength="13" size="14" value="[Username]"/>
			<label>Username</label>
		</span>
		<span>
			<input id="element_<@>_2" type= "password" name="pass<@>word" class="element text" maxlength="13" size="14" value="[Password]"/>
			<label>Password</label>
		</span><p class="guidelines" id="guide_4"><small>User <@@></small></p> 
		</li>		
';		

        $ji=1;
        if (!empty($users)) {
		  foreach ($users as $un=>$up) {
		    $user = str_replace("'","",$un);
			$pass = str_replace("'","",$up);
		    $myuserfields = str_replace('[Username]',$user,str_replace('[Password]',$pass,$user_fields)); 
			
			$user_quota = self::get_user_quota($un);
			$uq = $user_quota ? $user_quota : '0';
		    $ui = str_replace('<@>',$ji,$myuserfields);
			$say = $ji . "&nbsp;/&nbsp;Usage:" . $uq . "&nbsp; jobs";
			$us_ui .= str_replace('<@@>',$say,$ui);
			
		    $ji+=1;
		  }
		  
		}
		//+until 5
        for ($i=$ji;$i<=5;$i++) {
		    $myuserfields = str_replace('[Username]','',str_replace('[Password]','',$user_fields));
		    $us_ui .= str_replace('<@>',$i,$myuserfields);
		}	   
	
	    $menu = $this->html_printer_menu(true);
		
	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu 
		<form id="form_470441" class="appnitro" enctype="multipart/form-data" method="post" action="">
					<div class="form_description">
			<h2>Printer's Users $message</h2>
			<p>Add or modify printer's users.</p>
		</div>						
			<ul >		

		$us_ui
			
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
	
	//CONFIG PRINTER 
	public function form_configprinter($printername=null, $indir=null) {
	    $printername = $printername ? $printername : $this->printer_name;
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
        $cmd = $this->external_use ? $this->procmd.'confprinter':'confprinter';		
		$handlers = array();
		$params = array();
		//echo $printername,'...',$indir,'...';
		
	    if ($this->username!=$this->get_printer_admin()) 
		   return (self::html_window(null, 'Not allowed!'));		

        if (!$printername) 
		  return ('Unknown printer!');	  
		  
		//$ret = $this->html_printer_menu(true);		
		
        if (($filter=$_POST['filter']) || ($filter=$_GET['filter'])) {
		  $code = $_POST['filtercode'];
		  $ret .= $this->config_filter_form($filter,$printername,$code,$printerdir);
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
	
	protected function config_printer_form($message=null, $name=null, $params=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;
		$hd_ui = null;
		$filters_method = $params['method'];
		$page = pathinfo($_SERVER['PHP_SELF'],PATHINFO_BASENAME);
		$edit_filter = $page.'?'.$this->cmd.'confprinter&filter=[Handler]';
		$cmd = $this->external_use ? $this->procmd.'confprinter':'confprinter';
		
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
		//+until 3
        for ($i=$ji;$i<=3;$i++) {
		    $myhfields = str_replace('[Handler]','',str_replace('[Index]','',$handler_fields));
		    $hd_ui .= str_replace('<@>',$i,$myhfields);
		}	
	
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
			
		<li id="li_0" >
		<label class="description" for="element_0">Filter type </label>
		<div>
			<input id="element_0" name="filters_method" class="element text medium" type="text" maxlength="13" value="$filters_method"/> 
		</div><p class="guidelines" id="guide_1"><small>Filter apply method</small></p> 
		</li>		

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

    //PRINTER's FILTER EDIT	
	protected function config_filter_form($filter=null, $printername=null, $code=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;
	    $dir = $indir ? $indir.'/' : ($_SESSION['indir'] ? $_SESSION['indir'] .'/' : '/');
		$filter = $_POST['filtername'] ? $_POST['filtername'] : $filter;
		$cmd = $this->external_use ? $this->procmd.'confprinter':'confprinter';
		
	    //$file = $_SERVER['DOCUMENT_ROOT'] .'/'.$dir . str_replace('.printer','',$printername).'.'.$filter.'.php';		
        $file = self::get_printer_path() . str_replace('.printer','',$printername).'.'.$filter.'.php'; 		
		//echo $file,'>',$code;
	
	    if ($code = stripslashes($code)) {
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
		   

		   try {
		       $result = $testbed->execute($code, true);//not $mycode..thereis no testbed
			   file_put_contents($testbed->job_filename, $result);
			   
			   if ($result) {
		        $preview = '<h2>Result:</h2>';
					  
                if (is_readable($testbed->job_filename)) {
				  $preview .= "<IFRAME SRC=\""."http://".$_ENV["HTTP_HOST"].$dir."job_test.txt\" TITLE=\"$printername\" WIDTH=600 HEIGHT=400>
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
		  
		$menu = $this->html_printer_menu(true);  

	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu
		<h2>$msg</h2>
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
				<input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
				<input type="hidden" name="FormAction" value="$cmd" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
			</ul>
		</form>	
		
		$preview
				
		<div id="footer">
		$ver&nbsp;|&nbsp;$this->logout_url
		</div>
	</div>	
	<br/>		
EOF;
        return ($form);		
	}		
	
	//FETCH PRINTER INFO FILES
	public function form_infoprinter($printername=null, $indir=null) {
	    $printername = $printername ? $printername : $this->printer_name;
		$printerdir = $indir ? $indir : $_SESSION['indir'];	
		
        $ret = self::info_printer_form();
		
        $ok = self::html_info_printer($printername, $printerdir); 
		
		$ret .= $ok ? $ok : 'Failed to fetch info!';
		
		return (self::html_window(null, $ret));	
    }	
	
	protected function info_printer_form($printername=null, $indir=null) {
	
	    if ($this->username==$this->get_printer_admin()) 
		    $ret = self::html_get_printer_stats(true);
			
	    return ($ret);
	}	
	
	
    //PRINTER's ADD/UPLOAD JOB	
	protected function form_upload_job() {
	
        $user_quota = self::get_user_quota($this->username);
		$printer_quota = self::get_printer_quota();
		//echo $user,':',$user_quota,'>',$printer_quota;
		
		if (intval($user_quota) < intval($printer_quota)) {
	       $ret = $this->upload_job_form();
		}   
		else 
		   $ret = self::html_window(null, "User limit has expired.");
 		
	    return ($ret);
	}
	
	protected function upload_job_form($printername=null, $indir=null) {
	    $ver = $this->server_name . $this->server_version;
	    $dir = $indir ? $indir.'/' : ($_SESSION['indir'] ? $_SESSION['indir'] .'/' : '/');
		$cmd = $this->external_use ? $this->procmd.'uploadjob':'uploadjob';
	
		//if ($this->username==$this->get_printer_admin()) {

           if (!empty($_FILES['jobfile']) && (!$_FILES['jobfile']['error'])) {//uploaded file
		   
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
					}	
					else	
                        $msg = "Can not upload the job."; 					
                }
                
                if (!$msg) //true on upload...				
			      $ret = $this->html_get_printer_jobs();	
				else //error..
				  $ret = self::html_window(null, $msg);
				  
				return ($ret);				
           }	   	   

		//}

		  
		$menu = $this->html_printer_menu(true);  

	    $form = <<<EOF
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>	
		
	<div id="form_container">
	    $menu
		<h2>$msg</h2>
		<form id="form_470441" class="appnitro" enctype="multipart/form-data" method="post" action="">
		<div class="form_description">
			<h2>Add Job</h2>
			<p>Upload a job.</p>
		</div>						
		<ul >						
		
		<li id="li_3" >
		<label class="description" for="element_3">File </label>
		<div>
			<input id="element_3" name="jobfile" class="element file" type="file"/> 
		</div>  
		</li>		
        <li class="section_break">
			<p></p>
		</li>		
		<li class="buttons">
			    <input type="hidden" name="form_id" value="470441" />
				<input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
				<input type="hidden" name="FormAction" value="$cmd" />			    
				<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		</li>
		</ul>
		</form>	
		
		$preview
				
		<div id="footer">
		$ver&nbsp;|&nbsp;$this->logout_url
		</div>
	</div>	
	<br/>		
EOF;
        return ($form);		
	}		
	
	//CHANGE PRINTER QUOTA
    public function form_addquota($quota=null, $printername=null, $indir=null) {
	    $printername = $name ? $name : $this->printer_name;
		$printerquota = $quota ? $quota : null;//$_POST['printerquota'];
		$printerdir = $indir ? $indir : $_SESSION['indir'];		
   
		//$this->myprinter = new UiIPP($printername,null,null,true);
        $ok = self::html_mod_printer($printername,null,$printerquota,null,$printerdir); 	
		
		if ($ok)										
	      $ret = $jobs .' jobs added!';
		else
          $ret = 'Quota error!';		
		
		return ($ret);   
    } 	

	protected function add_quota_form() {
	
	}	


	//generate next job's id for uploading job file
	protected function _get_job_id() {
	
	    //in case of no folder yet..//DROPBOX ENABLE before printer added
	    if (!is_dir($this->jobs_path)) {
	        @mkdir($this->jobs_path, 0755);
           	return 1; //first job	   
		}   
	
        $mydir = dir($this->jobs_path);
		
		$i=0;		
        while ($fileread = $mydir->read ()) { 
	
           if (substr($fileread,0,4)=='job'.FILE_DELIMITER) {
              $i+=1;
			  
			  //get job number...same job id (create-job with multiple docs)
			  $pf = explode(FILE_DELIMITER,$fileread);
			  $jid[] = intval($pf[1]);
           }
        }
        $mydir->close ();

        //return ($i+1);	
		
		//print_r($jid); 
		if (!empty($jid))
		  return (max($jid)+1);	
		else
          return(1); //first job		
		  
	}	
	
    //LIB	
    protected function bytesToSize1024($bytes, $precision = 2) {
        $unit = array('B','KB','MB');
        return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision).' '.$unit[$i];
    }
	
    protected function printline($dat=null,$att=null,$isbold=false,$render=null) {
	    $ret = null;
		$isarray = is_array($att);
		
	    if (is_array($dat)) {
		
		   foreach ($dat as $i=>$f) {
		   
			   $data[$i] = $isbold ? '<strong>'.$f.'</strong>':$f; 
	           $attr[$i] = $isarray ? $att[$i] : $att;			      
		   }
		   
	       //$win = new window('',$data,$attr);
		   //$ret = $win->render($render);
		   $ret = "\r\n<table><tr>";
		   foreach ($data as $t=>$title) {
		     $attribute = explode(';',$attr[$t]);
		     $ret .= '<td align="'.$attribute[0].'" width="'.$attribute[1].'" valign="top">'.$title.'</td>';
		   }	 
		   $ret .= "</tr></table>";
		}
		
		return ($ret);
    }		
	
    function write2disk($file,$data=null) {
	        if (!defined('SERVER_LOG'))
			    return null; 	

            if ($fp = @fopen ($file , "a+")) {
	        //echo $file,"<br>";
                 fwrite ($fp, $data);
                 fclose ($fp);

                 return true;
            }
            else {
              echo "File creation error ($file)!<br>";
            }
            return false;

    }  		
 
 };
?>
<?php
/* 
 * Class ippprinter.lib - html frontend for receive Basic IPP requests, Get and parses IPP requests.
 *
 *   Copyright (C) 2012  Alexiou Vassilis
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
/*

    This class is intended to implement Internet Printing Protocol on -SERVER- side.

*/

require_once("AuthIPP.php");

define("AUTH_USER", true);
define("FILE_DELIMITER", '-');

class ippprinter {
    
    const AUTH_USER_METHOD = 'BASIC';//''DIGEST';//BASIC//'OAUTH'
	
	protected $authentication_mechanism, $server_version;
	protected $printers_path, $jobs_path, $icons_path, $admin_path, $printers_url, $urlpath;
	protected $printer_name;
	
    function __construct($printer=null, $auth=null, $printers_url=null) {
	
	   $this->server_version = '1.0';
       
       $this->authentication_mechanism = $auth ? $auth : constant('self::AUTH_USER_METHOD');
	   
	   $this->printer_name = $_SESSION['printer'] ? $_SESSION['printer'] : $_GET['printer'];
	   
	   $this->printers_url = $printers_url ? "/$printers_url/" : '/';//'/printers/';	   
	   $this->printers_path = $_SERVER['DOCUMENT_ROOT'] . $this->printers_url;//'/printers/';
	   
       $this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/';
	   if ($this->printer_name)
	     $this->jobs_path .= $this->printer_name . '/'; 
		 
	   $this->icons_path = $_SERVER['DOCUMENT_ROOT'] .'/icons/';
	   $this->admin_path = $_SERVER['DOCUMENT_ROOT'] .'/admin/';
	   $this->urlpath = pathinfo($_SERVER['PHP_SELF'],PATHINFO_BASENAME);	  
    }
	
	public function printer_console($action=null, $noauth=false) {
	      $action = $action ? $action : $_GET['action'];
		  
		  //print_r($_SESSION);	  
		  if ($noauth==false) {//html dpc auth
		  if ($this->authenticate_user()==false) {
		  
		    if ($this->authentication_mechanism==='OAUTH') {
		  
		        //////////////////////////////////////////////////////////////////// OAUTH
				//already directed to twitter login screen			
		    }
			else {  ////////////////////////////////////////////////////////////////// BASIC
                self::write2disk('network.log',":$this->username(login-failed):");		  		  
			
	            header("WWW-Authenticate: Basic realm=\"$this->printer_name\",stale=FALSE");
                header('HTTP/1.0 401 Unauthorized');
			    //header('WWW-Authenticate: Digest realm="'.$realm.'" qop="auth" nonce="'.$uniqid.'" opaque="'.md5($realm).'"');		 

			    //$ss = var_export($_SESSION,1);
			    //print_r($_SESSION);
						
                //die('not-logged-in');	
				
				return (self::invalid_login());
			}	
		  }	
          //else//if (!$_SESSION['user']) { //logged out...		  
		    //echo 'a',$_SESSION['user'];
		  //}
		  }//noauth	
          else 
            $this->username = $_SESSION['user']; //external dpc auth
			
		  self::write2disk('network.log',":$this->username:");
           
          switch ($action) {
              case 'show'    : break;	//row data
              case 'xml'     : break;	//xml row data	
			  case 'logout'  :	
			  case 'delete'  : 
			  case 'jobs'    : 
			  case 'jobstats': $ret .= self::html_header(); break;
              case 'netact'  : $ret .= self::html_header(null,15); break;			  
		      default        : $ret .= self::html_header();
		  }
		  
		  $quota = self::get_printer_quota($this->username);

		  if (intval($quota) > $this->printer_use_quota) {
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
		  
		  if (($this->printer_name) && ($jid = $_GET['job'])) {
		    //echo 'c';
		    switch ($action) { 
			   
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
		    //echo 'b';
		    switch ($action) {
			  case 'xml'     : $ret .= self::xml_get_printer_jobs(); break;
			  case 'logout'  : break;
			  case 'jobstats': $ret .= self::html_get_printer_stats(true); break;
			  case 'jobs'    : $ret .= self::html_get_printer_jobs(); break;
			  case 'netact'  : $ret .= self::html_get_network_activity(); break;
			  default        : $ret .= self::html_get_printer_info();	
            }			
		  }	
		  else {
		    //echo 'a';
		    switch ($action) {
			  case 'logout': break;
			  //case 'jobs'  : $ret .= self::html_get_printer_jobs($printer); break;
			  case 'netact': $ret .= self::html_get_network_activity(); break;
			  default      : $ret .= self::html_get_printers();
			}  
		  }	
			
          switch ($action) {
              case 'show'    : break;	//row data	
			  case 'xml'     : break;	//xml row data	
              case 'logout'  : $ret .= self::logout();
                               $ret .= self::html_get_printers();
                               break;							   
              case 'netact'  :
              case 'jobstats':	
              case 'jobs'    : 			  
		      default        : $logout = "<a href='$this->urlpath?t=ipplogout'>" . 'Logout'  ."</a>";	
			                   $ret .= '<hr>IPP server '.$this->server_version. "&nbsp;|&nbsp;".$logout;  
		                       $ret .= self::html_footer();	
		  }			
		 
		  return $ret; 
	}
	
	protected function html_header($encoding=null, $reload=null) {
	
	  
	  return null;
	  
	  //no need
	  $encoding = $encoding?$encoding:'utf-8';//'iso-8859-7';
	
	  echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=',$encoding,'" />
			';
	
      if ($reload)	
	    echo '<meta http-equiv="refresh" content="'.$reload.'"/>';
			
      echo '<title>IPP Server ',$this->server_version,' | stereobit.networlds</title>
            </head>
            <body>';
	}
	
	protected function html_footer() {
	
	  return null;
	
	  //no need
	  echo '</body></html>';
	}	
	
    protected function html_show_printer_job($job_id=null, $dataonly=null) {
	
	    $job_id = $_GET['job']?$_GET['job']:$job_id;

		if (!$job_id)
		  return null;
		  	
		$mydir = @dir($this->jobs_path);
		
		//$ret .= '<h1>' . $printer_name . '&nbsp;Jobs'./* $this->printer_state.*/'</h1>';	
		$ret .= "<a href='$this->urlpath?t=ippjobs&which=all&printer=".$this->printer_name."'>" . 'All'  ."</a>";	
		$ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=ippjobs&which=pending&printer=".$this->printer_name."'>" . 'Pending'  ."</a>";	   
		$ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=ippjobs&which=processing&printer=".$this->printer_name."'>" . 'Processing'  ."</a>";				   
		$ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=ippjobs&which=completed&printer=".$this->printer_name."'>" . 'Completed'  ."</a>";
		$ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=ippjobstats&printer=".$this->printer_name."'>" . 'Statistics'  ."</a>";
        $ret .= '<hr/>';		
		
        while ($fileread = $mydir->read ()) { 
		    if (substr($fileread,0,4)=='job'.FILE_DELIMITER) {
			    $pf = explode(FILE_DELIMITER,$fileread);
				$jid = $pf[1];
				if ($jid==$job_id) {
				
				    if ($dataonly) {
					  $out = file_get_contents($this->jobs_path . $fileread);
					  die($out);//iframe
					}
					else {
		              $ret .= '<h1>' . $this->printer_name . '&nbsp;Job&nbsp;'. $jid . '</h1>';
					  
                      if (is_readable($this->jobs_path . $fileread)) {
					    $ret .= "<IFRAME SRC=\"$this->urlpath?t=ippshow&job=$jid&printer=$this->printer_name&iframe=1\" TITLE=\"$this->printer_name / Job $jid\" WIDTH=800 HEIGHT=600>>
                              <!-- Alternate content for non-supporting browsers -->
                              <H2>$this->printer_nameJob $jid</H2>
                              <H3>iframe is not suported in your browser!</H3>
                              </IFRAME>";
					  }
                    } 					
					break;	
				}
			}
		}	
		$mydir->close();
        return ($ret);		
    }	
	
    protected function html_delete_printer_job($job_id=null) {
	
	    $job_id = $_GET['job']?$_GET['job']:$job_id;

		if (!$job_id)
		  return null;		  
		  	
		$mydir = dir($this->jobs_path);
		
        while ($fileread = $mydir->read ()) { 
		    if (substr($fileread,0,4)=='job'.FILE_DELIMITER) {
			    $pf = explode(FILE_DELIMITER,$fileread);
				$jid = $pf[1];
				if ($jid==$job_id) {
				
                    if (is_readable($this->jobs_path . $fileread)) 
                        unlink($this->jobs_path . $fileread);					
						
					break;	
				}
			}
		}	
		$mydir->close();

        $ret = $this->html_get_printer_jobs();		
		return ($ret);
    }	

	protected function html_get_printer_jobs($which_jobs=null) {
		$wjobs = $_GET['which'] ? $_GET['which'] : $which_jobs;
		$user = $this->username ? $this->username : $_SESSION['user'];	
		$indir = GetReq('indir') ? 
		         "&indir=".GetReq('indir') : 
		         ($_SESSION['indir'] ? "&indir=".$_SESSION['indir'] : null);		
        $jstate = array(); 		
		
 
        if (!is_dir($this->jobs_path))
		  return null; 
				  
		$printer_state = null;//self::_get_printer_state(); //file not in jobs dir... 
		
        $mydir = dir($this->jobs_path);	        		
		
        //header line		
		$ret .= '<h1>' . $this->printer_name . '&nbsp;Jobs'./* $this->printer_state.*/'</h1>';	
		$ret .= "<a href='$this->urlpath?t=ippjobs&which=all&printer=".$this->printer_name."'>" . 'All'  ."</a>";	
		$ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=ippjobs&which=pending&printer=".$this->printer_name."'>" . 'Pending'  ."</a>";	   
		$ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=ippjobs&which=processing&printer=".$this->printer_name."'>" . 'Processing'  ."</a>";				   
		$ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=ippjobs&which=completed&printer=".$this->printer_name."'>" . 'Completed'  ."</a>";
		$ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=ippjobstats&printer=".$this->printer_name."'>" . 'Statistics'  ."</a>";
        $ret .= '<hr/>';
		
        while ($fileread = $mydir->read ()) { 
		
		    if (substr($fileread,0,4)=='job'.FILE_DELIMITER) {
				
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
				        case 'pending'   :if (stristr($fileread,FILE_DELIMITER.'pending')) { 
				                            $jobs[intval($jid)] = $fileread;
											$jstate[intval($jid)] = 'pending';
										  }	
                                          break;				
				        case 'all'       :
				        default          :$jobs[intval($jid)] = $fileread;
						                  $s = array_pop($pf);
						                  if (in_array($s, array('completed','processing','pending')))
										    $jstate[intval($jid)] = $s;
										  else	
						                    $jstate[intval($jid)] = 'pending';
			        }
				}
			}	
		}	
		$mydir->close();
		
		$ret .= self::printline(array('No','Job-Id','Ip','User','Job-Name','Status','--------|----------|--------'),
		                        array('left;5%','left;5%','left;15%','left;10%','left;35%','left;10%','left;20%'),
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
		       $links = "<a href='$this->urlpath?t=ippshow&job=".$job['id']."&printer=".$this->printer_name."'>" . 'View'  ."</a>";	
			   $links .= "&nbsp;|&nbsp;<a href='/jobs/queue/$this->printer_name/".$job['id']."/'>" . 'Show'  ."</a>";
			   
		       $links .= "&nbsp;|&nbsp;<a href='$this->urlpath?action=ippdeljob&job=".$job['id']."&printer=".$this->printer_name."'>" . 'Delete'  ."</a>";				   
               //$ret .= '<br/>';	
		   
               $ret .= self::printline(array($i++,$job['id'],$job['remote-ip'],$job['user-name'],$job['job-name'],$jstate[$job['id']],$links),
			                           array('left;5%','left;5%','left;15%','left;10%','left;35%','left;10%','left;20%'),
                                       0,
			                           "center::100%::0::group_article_body::left::0::0::");									   
			}
	    }
		else {
		   $ret .= 'Empty';
		}
		
		//footer line
		$ret .= '<hr>';
		$ret .= "<a href='jobs/rss/".$this->printer_name."/'>" . 'Printer RSS'  ."</a>";
		if ($this->username==$this->get_printer_admin()) {
		  $pname = str_replace('.printer','',$this->printer_name);
		  $ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=modprinter&printername=".$pname.$indir."'>" . 'Printer properties'  ."</a>";
		  $ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=useprinter&printername=".$pname.$indir."'>" . 'Printer users'  ."</a>";
		  $ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=confprinter&printername=".$pname.$indir."'>" . 'Printer configuration'  ."</a>";
		}
		$ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=infprinter&printername=".$pname.$indir."'>" . 'Printer info'  ."</a>";
		
		return ($ret);			
	}
	
	protected function html_get_printer_stats($set_quota=false) {

		$user = $this->username ? $this->username : $_SESSION['user'];
		$indir = GetReq('indir') ? 
		         "&indir=".GetReq('indir') : 
		         ($_SESSION['indir'] ? "&indir=".$_SESSION['indir'] : null);		
		
        if (!is_dir($this->jobs_path))
		  return null; 	
		  
		$printer_state = null;//self::_get_printer_state(); //file not in jobs dir... 
		
        $mydir = dir($this->jobs_path);	        		
		
        //header line		
		$ret .= '<h1>' . $this->printer_name . '&nbsp;Jobs'/* .$this->printer_state*/.'</h1>';	
		$ret .= "<a href='$this->urlpath?t=ippjobs&which=all&printer=".$this->printer_name."'>" . 'All'  ."</a>";	
		$ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=ippjobs&which=pending&printer=".$this->printer_name."'>" . 'Pending'  ."</a>";	   
		$ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=ippjobs&which=processing&printer=".$this->printer_name."'>" . 'Processing'  ."</a>";				   
		$ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=ippjobs&which=completed&printer=".$this->printer_name."'>" . 'Completed'  ."</a>";
		$ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=ippjobstats&printer=".$this->printer_name."'>" . 'Statistics'  ."</a>";
        $ret .= '<hr/>';		

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
			   $job_size = $fileattr['size'];
			   
			   //$ret .= sprintf('%s %s %d bytes %s<br>',$job_time,$job_name,$job_size,$job_status);
			   $ret .= self::printline(array($i++,$job_time,$job_remote_ip,$job_name,$job_size,$job_status),
			                           array('left;5%','left;25%','left;20%','left;30%','left;10%','left;10%'),
									   0,
			                           "center::100%::0::group_article_body::left::0::0::");

			   $jobs_sum['total-jobs'] += 1;
               $jobs_sum[$job_status] += 1;
               $jobs_sum['total-size'] += $job_size; 			   
			}
			$ret .= '<hr>';
			$ret .= sprintf ('Completed Jobs:%d <br>',$jobs_sum['completed']);
			$ret .= sprintf ('Prosessing Jobs:%d <br>',$jobs_sum['prosessing']);
			$ret .= sprintf ('Pending Jobs:%d <br>',$jobs_sum['pending']);
			$ret .= sprintf ('Total Jobs:%d <br>',$jobs_sum['total-jobs']);
			$ret .= sprintf ('Total Size:%d kb<br>',floatval($jobs_sum['total-size']/1024));
	    }	
		else {
		   $ret .= 'Empty';
		}
		
		$current_bytes = intval(@file_get_contents($this->printers_path . $this->printer_name.'.counter'));
		$ret .= sprintf ('Total Process Size:%d kb<br>',floatval($current_bytes/1024));
		
        if ($set_quota) {
		    $pname = $_GET['printer'];
			$puser = $this->username ? FILE_DELIMITER.$this->username : null;
			@file_put_contents($this->printers_path . $pname.$puser.'.quota', $jobs_sum['completed'], LOCK_EX);
        }	
		
		//footer line
		$ret .= '<hr>';
		$ret .= "<a href='jobs/rss/".$this->printer_name."/'>" . 'Printer RSS'  ."</a>";
		if ($this->username==$this->get_printer_admin()) {
		  $pname = str_replace('.printer','',$this->printer_name);
		  $ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=modprinter&printername=".$pname.$indir."'>" . 'Printer properties'  ."</a>";
		  $ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=useprinter&printername=".$pname.$indir."'>" . 'Printer users'  ."</a>";
		  $ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=confprinter&printername=".$pname.$indir."'>" . 'Printer configuration'  ."</a>";
		}
		$ret .= "&nbsp;|&nbsp;<a href='$this->urlpath?t=infprinter&printername=".$pname.$indir."'>" . 'Printer info'  ."</a>";		     		

        return ($ret);  		
	}
	
	protected function html_get_printer_info($iconsview=null) {
		$urlicons = 'icons/';	
        $icons = array();		
		$user = $this->username ? $this->username : $_SESSION['user'];
		$indir = GetReq('indir') ? 
		         "&indir=".GetReq('indir') : 
		         ($_SESSION['indir'] ? "&indir=".$_SESSION['indir'] : null);
		
	    $ret .= '<h1>' . $this->printer_name /*.' - '. $this->printer_state*/.'</h1>';
		//echo "<a href='jobs/?printer=".$printer_name,"'>" . 'Show jobs'  ."</a>";
		//$ret .= "<a href='jobs/queue/".$printer_name."/'>" . 'Show printer jobs'  ."</a><br/>";
		
		if ($iconsview) {
		  $icons[] = "$this->urlpath?t=ippjobs&printer=".$this->printer_name.$indir.":Printer Jobs";
		  $icons[] = "jobs/rss/".$this->printer_name.'/:Printer RSS';
		}
		else {
		  $ret .= "<a href='$this->urlpath?t=ippjobs&printer=".$this->printer_name.$indir."'>" . 'Show printer jobs'  ."</a><br/>";
		  $ret .= "<a href='jobs/rss/".$this->printer_name."/'>" . 'Printer RSS'  ."</a><br/>";
		}
		
		if ($user == $this->get_printer_admin()) {
		   //$ret .= '<hr>';
		   
           $pname = str_replace('.printer','',$this->printer_name);
		   
		   if ($iconsview) {
		     $icons[] = "$this->urlpath?t=modprinter&printername=".$pname.$indir.":Printer Properties";
		     $icons[] = "$this->urlpath?t=useprinter&printername=".$pname.$indir.':Printer Users';
			 $icons[] = "$this->urlpath?t=confprinter&printername=".$pname.$indir.':Printer Configuration';
		   }
		   else {		   
		     $ret .= "<a href='$this->urlpath?t=modprinter&printername=".$pname.$indir."'>" . 'Printer properties'  ."</a><br/>";
		     $ret .= "<a href='$this->urlpath?t=useprinter&printername=".$pname.$indir."'>" . 'Printer users'  ."</a><br/>";
		     $ret .= "<a href='$this->urlpath?t=confprinter&printername=".$pname.$indir."'>" . 'Printer configuration'  ."</a><br/>";
		   }
		   //admin quota (must sum quota of all users of this printer!)
           $quota = self::get_printer_quota($user);

		   if (intval($quota) > $this->printer_use_quota) {	
             $item = $indir ? GetReq('indir').'-'.$pname : $pname;  		   
			 $ret .= "<a href='download.php?g=$item'>" . 'Feed the Printer'  ."</a><br/>";
		   }    		   
		}
		
		if ($iconsview) {
		  $icons[] = "$this->urlpath?t=infprinter&printername=".$pname.$indir.":Printer Info";		
		}  
		else {	 
          $ret .= "<a href='$this->urlpath?t=infprinter&printername=".$pname.$indir."'>" . 'Printer info'  ."</a><br/>";		
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
	           $attr[] = 'left;25%';
			}	
            //print_r($icco);			
			$ret = self::printline($icco,$attr,0,"center::100%::0::group_article_body::left::0::0::");			
		}
		
		return ($ret);
	}
	
	//alias
	public function html_printer_menu() {
	
	    $ret = self::html_get_printer_info(true);
		return ($ret);
	}
	
	public function html_get_printers($indir=null) {
		$urlicons = 'icons/';	
		$indir = $indir ? $indir : (GetReq('indir') ? GetReq('indir') : $_SESSION['indir']);
		$nd = null;
		
	    if ($indir) {
		  $mydir = @dir($_SERVER['DOCUMENT_ROOT'] ."/$indir/");
		  $nd = "&indir=$indir";
		}  
	    else
          $mydir = @dir($this->printers_path); //default dir 'printers'
		
        if (!$mydir)
		  return;
		
		$ret = '<h1>' . 'Printers' . '</h1>';	
		
		$data = array();
		$attr = array();
        while ($fileread = $mydir->read ()) { 
		    
			if (stristr($fileread,'.php')) {
			
			   if ((!$this->get_printer_auth($fileread, $indir)) ||
			      (strstr($fileread,'index'))) //no index files
			      continue;
			   
			   $printer_name = str_replace('.php','.printer',$fileread);
		       //$ret .= "<a href='".$this->printers_url . $printer_name."'>" . $printer_name  ."</a><br/>";
			   //echo $this->icons_path.$printer_name.'.png';
			   if (is_file($this->icons_path.$printer_name.'.png'))
			     $ifile = $urlicons.$printer_name.'.png';
			   else
			     $ifile = $urlicons.'index.printer.png';
			   
			   $icon = "<a href='$this->urlpath?t=printer&printer=".$printer_name.$nd."'><img src='" . $ifile."' border=0 alt='$printer_name'></a>";
			   $link = "<a href='$this->urlpath?t=printer&printer=".$printer_name.$nd."'>" . $printer_name  ."</a>";

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
	    $indir = $indir ? $indir : (GetReq('indir') ? GetReq('indir') : $_SESSION['indir']);
        
		$ret .= '<h1>' . 'Network activity' . '</h1>';
		
	    if ($indir)	
		   $netfile = $_SERVER['DOCUMENT_ROOT'] ."/$indir/" . 'network.log';		
        else
           $netfile = $this->printers_path . 'network.log';		
		  
		if ($data = @file_get_contents($netfile)) {	
		 
          $ndata = nl2br($data);
          $ret .= $ndata;		
		}	

        return ($ret);		
	}
	
	protected function get_printer_quota($user=null, $printer=null, $indir=null) {
	    $pname = $printer ? $printer : $_GET['printer'];
		$puser = $user ? FILE_DELIMITER.$user : null;
		$qfile = $pname.$puser.'.quota';
		$indir = $indir ? $indir : (GetReq('indir') ? GetReq('indir') : $_SESSION['indir']);
	
	    if ($indir)
		  $myqfile = $_SERVER['DOCUMENT_ROOT'] ."/$indir/" . $qfile;
        else		  
	      $myqfile = $this->printers_path . $qfile;
	 
        if ($quota = @file_get_contents($myqfile)) {
		    return (intval($quota)); 
        }
        
        return false; 		
	}

	public function html_add_printer($name=null, $auth=null, $quota=null, $users=null, $indir=null) {
	
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
		  $myprinter_path = $_SERVER['DOCUMENT_ROOT'] ."/$indir/"; 
		  
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
          $file = $this->printers_path . $fname .'.php';
		  $conf = $this->printers_path . $fname .'.printer.conf';
		
		  if (!is_readable($file)) {//not overwrite
		    $ret = file_put_contents($file, $code0);
			$ret = file_put_contents($conf, $printer_config);			
		  }	
		}  
		
		return ($ret);
	}	
	
    public function html_mod_printer($name=null, $auth=null, $quota=null, $users=null, $indir=null) {
    
        if (!$name)
            return false;	

        $params = $this->parse_printer_file($name, $indir);		
        //print_r($params);	
		if ($quota>1)
          $quota = $params['quota'] + $quota; //addon		
	    //else reset to 1
		
        $fname = $name ? $name : null;	
	    $pname = $name ? $name . '.printer' : null;
		
        $pauth = $auth ? ",'$auth'" : ",".$params['auth']; //as is
		$pquota = $quota ? ",$quota" : ','.$params['quota']; //as is
		
		if (empty($users)) 
		  $users = (array) $params['users'];
		
		$pusers = ",array(";
		foreach ($users as $username=>$password)
		  $pusers .= "'" . $username ."'=>'".$password."',";
		$pusers .= ")";	

	    $code0 = "<?php	
include ('ippserver/ListenerIPP.php');
\$listener = new IPPListener('$pname' $pauth $pquota $pusers);
\$listener->ipp_send_reply(); 
?>";

	    $code1 = "<?php
include ('../printers/ippserver/ListenerIPP.php');
\$listener = new IPPListener('$pname' $pauth $pquota $pusers);
\$listener->ipp_send_reply(); 
?>";		  

        if ($indir) {
		  $myprinter_path = $_SERVER['DOCUMENT_ROOT'] ."/$indir/"; 

          $file = $myprinter_path . $fname .'.php';
          $ret = file_put_contents($file, $code1);   		  
        }
        else {
		
          $file = $this->printers_path . $fname .'.php';
          $ret = file_put_contents($file, $code1); 		  
        }	
		
		//$ret .=  seturl('t=modprinter','..Config..');
		
        return ($ret);		
	}
	
	public function parse_printer_file($name,$indir=null) {
	
        if ($indir) 
		  $file = $_SERVER['DOCUMENT_ROOT'] . "/$indir/" . $name .'.php';
        else
          $file = $this->printers_path . $name .'.php';
        //echo $file;
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
			  return ($params);
		   }
        } 
        }		
	}
	
    public function html_info_printer($name=null, $indir=null) {
    
        if (!$name)
            return false;
			
			

        return ($ret);			
    }			
	
	public function parse_printer_conf($name=null, $indir=null) {
	
        if ($indir) 
		  $file = $_SERVER['DOCUMENT_ROOT'] . "/$indir/" . $name .'.printer.conf';
        else
          $file = $this->printers_path . $name .'.printer.conf';
        //echo $file;
        $params = @parse_ini_file($file, true);	
        return ($params);		
	}
	
	public function save_printer_conf($name=null, $indir=null, $conf=null) {
	
	    if (!$conf)
		  return;
	
        if ($indir) 
		  $file = $_SERVER['DOCUMENT_ROOT'] . "/$indir/" . $name .'.printer.conf';
        else
          $file = $this->printers_path . $name .'.printer.conf';

        $ret = file_put_contents($file,$conf);
		  
        return (true);		
	}	
	
	
	//AUTH
	protected function authenticate_user($ipp_call=false) {
	  
	    if ((!$_GET['t']) || (!defined('AUTH_USER')))
	      return true;
		  
		//if  ($_SESSION['printer']!=$this->printer_name) 
		  //return false;
		
        if ($this->authentication_mechanism) {		
		    $this->authentication = new AuthIPP($this->authentication_mechanism);

           	if ($ipp_call) 
			    $this->username = $this->authentication->ipp_auth();
            else {  
                $this->username = $this->authentication->http_auth();
                //register printer
				if ($this->username)
                  $_SESSION['printer'] = $this->printer_name;			
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
          $_SESSION['user'] = null; //???
		  $_SESSION['printer'] = null;

          $ret .=  "You've successfully logged out<br>";
          //echo '<p><a href="?action=logIn">LogIn</a></p>';
       }

       return ($ret);	   
    }	
	
	protected function is_named_printer() {
	  
	    return ($this->printer_name);
	}	
	
	protected function invalid_login() {
	  
	    $ret = $this->html_get_printers();
	  
	    return ($ret);//'Invalid operation!');
	}	
	
	protected function expired() {
	  
	    //$ret = $this->html_get_printers();
	
		$ret= '<h2>You are overlimit. Please feed your <a href="http://stereobit.gr/download.php?g=art">printer</a>.</h2>';
		
		return ($ret);
	}

	protected function get_printer_auth($bootsrapfile=null, $indir=null) {
	    if (!$bootsrapfile)
		  return;
		
		$auth = 'BASIC'; //default
		
	    if ($indir)
		  $bs = $_SERVER['DOCUMENT_ROOT'] ."/$indir/" . $bootsrapfile;
	    else	
          $bs = $this->printers_path . $bootsrapfile;		
		
        $bootdata = @file_get_contents($bs);
		
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
	
    protected function get_printer_admin() {
	
	    if (is_object($this->authentication)) {
	   
	      $this->user_admin = $this->authentication->get_user_admin();
	    }
	    else
	      $this->user_admin = 'admin';
		  
	   	return ($this->user_admin);  
    }	
	
	public function get_printer_users($printer=null,$indir=null) {
	   
	   if ($printer) {
	      $myprinter = str_replace('.printer','',$printer);
		  $params = $this->parse_printer_file($myprinter, $indir);
		  
		  return ($params['users']);
	   }
	   
	   return null;
	}		

    protected function printline($dat=null,$att=null,$isbold=false,$render=null) {
	    $ret = null;
		$isarray = is_array($att);
	
	    if (is_array($dat)) {
		
		   foreach ($dat as $i=>$f) {
		   
			   $data[] = $isbold ? '<strong>'.$f.'</strong>':$f; 
	           $attr[] = $isarray ? $att[$i] : $att;			      
		   }
		   
	       $win = new window('',$data,$attr);
		   $ret = $win->render($render);
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
}         


 
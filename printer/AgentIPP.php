<?php
/* 
 * Class AgentIPP - process printer queue.
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


define("USE_DATABASE", false);
define("SERVER_LOG", true);

if (USE_DATABASE==true) {
    require_once("DBStream.php");
    stream_register_wrapper('db', 'DBStream');
} 

 class AgentIPP  {
 
     protected $agent_version, $logfile;
	 protected $printer_name;
	 protected $jobs_path, $icon_path, $admin_path;
	 
	 protected $job_owner;
	 protected $network_log, $netfile, $countfile;
     protected $indir, $printer_path;	 
	 
	 public $auth_obj;
 
     public function __construct(&$auth,$printer_name=null, $job_owner= null, $callback_function=null, $callback_param=null, $log=null, $noexec=null) {		
	 
	    spl_autoload_register(array($this, 'loader'));
		set_error_handler(array($this,'handleError'));
		
		$this->agent_version = '1.0';   //IPP server version
		$this->network_log = $log ? true : false;
		//in case of subdir printer...
		//ONLY OF READING SUBDIR CONF FILE WHEN EXEC MANUALY FROM OUTER DIR (DPC), 
		//LOG PATH, NETWORK LOG, AFFECTED OTHER PATHS REMAIN AS IS..
	    $this->indir = isset($_SESSION['indir']) ? $_SESSION['indir'].'/' : null; //'/';
        $this->printer_path = isset($this->indir) ? $_SERVER['DOCUMENT_ROOT'].'/'.$this->indir : null;//NULL=IN THIS DIR.. 	
		
		$this->logfile = $printer_name ? $this->printer_path . $printer_name . '.log' : $this->printer_path . 'pragent.log';
		$this->countfile = $printer_name ? $this->printer_path . $printer_name . '.counter' : $this->printer_path . 'index.printer.counter';//!!!				
		$this->netfile = $this->printer_path . 'network.log';

		$this->printer_name = $printer_name ? $printer_name : 'root-printer';
		$this->job_owner = $job_owner; //BEWARE is the agent owner!!!!..NOW CALLED BY NAME ONLY...

        $this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs';//$_SERVER['DOCUMENT_ROOT'] . pathinfo($_SERVER['PHP_SELF'],PATHINFO_DIRNAME); 		
		if ($printer_name) 
		  $this->jobs_path .= '/' . $printer_name;
		  
	    $this->icons_path = $_SERVER['DOCUMENT_ROOT'] .'/icons/';
	   
	    $this->admin_path = $_SERVER['DOCUMENT_ROOT'] .'/admin/';
        if ($printer_name) 
	      $this->admin_path .= $this->printer_name . '/';		  
		
		if (!$noexec) { //manual run..............
          if (is_dir($this->jobs_path)) {
		  
		   self::write2disk($this->logfile,"\r\n".date(DATE_RFC822)."\r\n");		  
		  
		   if ($this->network_log)
		       self::write2disk($this->netfile,":READ JOBS:");
			   
		   if ($callback_function) {//specific agent task
		       self::_read_jobs(null,null,$callback_function,$callback_param);// 'set_jobs_status','processing');//null);     
		   }
		   else //common agent task
		       self::_read_jobs(null,null,null);//'set_jobs_status','processing');//null);   
          }	
          else {
		   self::write2disk($this->logfile,"\r\n".date(DATE_RFC822)."\r\n".$this->jobs_path . ' not exist!');
		   
		   if ($this->network_log)
		       self::write2disk($this->netfile,":JOB PATH NOT EXIST:");
          }
		}
		$this->auth_obj = (object) $auth;
		
    }
	
    private function loader($className) {
	
	    //echo 'Trying to load ', $className, ' via ', __METHOD__, "()\n"; 
        self::write2disk($this->logfile,"\r\nAgentIPP:Trying to load ". $className. ' via '. __METHOD__ . "()\r\n");
		
		try {
		    $class = str_replace('\\', '/', $className);
            require_once('handlers/'. $class . '.php');  //handlers as handlers/xxx
			
			$ok = "\r\n Class $className loaded!";
			self::write2disk($this->logfile,$ok);
			
			//if ($this->network_log)
		      //self::write2disk($this->netfile,":$className:");
		} 
		catch (Exception $e) {
            $err = "\r\n File $className not exist!";
			self::write2disk($this->logfile,$err);
			//debug_print_backtrace();
			
			//if ($this->network_log)
		      //self::write2disk($this->netfile,":$className:ERROR");			
        }
		
        self::write2disk($this->logfile,"\r\nAgentIPP:End of load ". $className. ' via '. __METHOD__. "()\r\n");		
    }	
	
	//read jobs directory
	private function _read_jobs($my_jobs=false, $which_jobs=false, $callback_function=null, $callback_param=null) {
	    $jobs = null;
		
        $mydir = dir($this->jobs_path);	
		self::write2disk($this->logfile,"\r\nJOBS PATH:".$this->jobs_path."\r\n");	
		
        /*
        $which_jobs = self::read_request_attribute('which-jobs');		
		$limit = self::read_request_attribute('limit');
		$my_jobs = $my_jobs ? $my_jobs : self::read_request_attribute('my-jobs');
		$user = self::read_request_attribute('requesting-user-name');
		*/
		
		//READ ALL JOBS......
		//...................
		$user = $this->job_owner;
		
		$i=0;		
        while ($fileread = $mydir->read()) { 
		    if (substr($fileread,0,4)=='job'.FILE_DELIMITER) {
			
                $i+=1;
				$pf = explode(FILE_DELIMITER,$fileread);
				$jid = $pf[1];//sort			
				$jowner = $pf[3];
				
				//PER USER CALL...
			    if (/*($user==$this->get_printer_admin()) ||*/ ($jowner==$user) || (!defined('AUTH_USER'))) {
								
				
			    //switch depending on request attr
			    switch ($which_jobs) {
				
				    case 'completed'     : if (($my_jobs) && ($user) && ($jowner!=$this->job_owner))
					                         break;
                                           elseif (stristr($fileread,FILE_DELIMITER.'completed')) {
				                             $jobs[intval($jid)] = $fileread;		
                                           } 					
					                       break;
										   
				    case 'processing'    : if (($my_jobs) && ($user) && ($jowner!=$this->job_owner))
					                         break;
                                           elseif (stristr($fileread,FILE_DELIMITER.'processing')) {
				                             $jobs[intval($jid)] = $fileread;		
                                           } 					
					                       break;
					
                    //usually error at handler call. Otherwise not a status = pending					
				    case 'pending'       : if (($my_jobs) && ($user) && ($jowner!=$this->job_owner))
					                         break;
                                           elseif (stristr($fileread,FILE_DELIMITER.'pending')) {
				                             $jobs[intval($jid)] = $fileread;		
                                           } 					
					                       break;										   

				    case 'not-completed' : if (($my_jobs) && ($user) && ($jowner!=$this->job_owner))
					                         break;
                                           elseif (stristr($fileread,FILE_DELIMITER.'completed')==false) { 
				                             $jobs[intval($jid)] = $fileread;
				                           } 										   
								  
				    case 'all'           :				    
				    default              : if (($my_jobs) && ($user) && ($jowner!=$this->job_owner))
					                         break;
										   elseif ((stristr($fileread,FILE_DELIMITER.'completed')==false) &&
           										   (stristr($fileread,FILE_DELIMITER.'deleted')==false) &&
												   (stristr($fileread,FILE_DELIMITER.'canceled')==false)
												   ){ 
											 //only pending & processing
				                             $jobs[intval($jid)] = $fileread;
				                           } 
				}
                }//PER USER CALL...				
			}
            
            //if (($limit) && ($limit>$i)) 			
			  //break;
	    }
		
        $mydir->close ();	
		
		if ($this->network_log)
		    self::write2disk($this->netfile,":COUNT:".count($jobs).':');		

		if (is_array($jobs)) {
		    
			//krsort($jobs);
			ksort($jobs);
		
		    foreach ($jobs as $jid=>$fileread) {
			   $fp = explode(FILE_DELIMITER,$fileread);
	           $job['job-id'] = $fp[1];
	           $job['remote-ip'] = str_replace('~',':',$fp[2]);
	           $job['user-name'] = $fp[3];
		       $job['job-name'] = $fp[4];			   
			   
			   if ($callback_function) {
			     $ret = call_user_func_array(array($this,$callback_function),array($job['job-id'], $fileread, $job, $callback_param));
			   }	 
			   else {
			     $ret = self::_process_job($job['job-id'], $fileread, $job);
			   }
			   
		       self::write2disk($this->logfile,"Process Job $jid (auto)\r\n");			   
			   
			   $status = $ret ? 'completed' : 'processing';
			   //echo $fileread;			   
			   self::write2disk($this->logfile,"\r\n".$fileread.'->'.$status);
			   
		       if ($this->network_log) 
		         self::write2disk($this->netfile,":JOB $jid:$status:");
			}
	    }		
	}
	
	//MANUAL PROCESS ...public func..called by UiIPP
	public function process_job($job_id=null, $job_file=null, $job_attr=null, $silentmode=false) {
	
		self::write2disk($this->logfile,"Process Job $job_id (manual)\r\n");
		
	    $ret = self::_process_job($job_id, $job_file, $job_attr, true, $silentmode);
		return ($ret);
	}
	
	private function _process_job($job_id=null, $job_file=null, $job_attr=null, $standalone=false, $silentmode=false) {
	    if (!$job_id)
		    return null;
		
		$jf = $this->jobs_path . '/' . $job_file;
		
		if (is_readable($jf)) {   
		
            self::write2disk($this->logfile,"Call handler\r\n");
		    if ($this->network_log) 
		        self::write2disk($this->netfile,":CALL_HANDLER:");				
			
			$ret = self::_call_handler($job_id, $job_file, $job_attr, $standalone, $silentmode);			
        }
        else {
		
		    if ($this->network_log) 
		        self::write2disk($this->netfile,":CALL_HANDLER:FILE_ERROR:");		
		
		    $message = "File ($jf) not exist.\r\n"; 
			if ($standalone) {
			  $ret = $message;
			}  
			else {			
			  $ret = false; 
			  self::write2disk($this->logfile,$message);
			}  
        } 		

		//if ($standalone)
		return ($ret);
		
        //return ($ret?$job_id:false);		
	}
	
	private function _call_handler($job_id=null, $job_file=null, $job_attr=null, $standalone=false, $silentmode=false) {

		$data = null;	
        $ret = false;	
		$state_message = null;
		$handlers = array();
		$musttrue = true; //init, hold prev state
		$current_job_attr = $job_attr; //init
		$current_job_file = $this->jobs_path.'/'.$job_file; //init
		
		//custom class
        $classpath = $_SERVER['DOCUMENT_ROOT'] .pathinfo($_SERVER['PHP_SELF'],PATHINFO_DIRNAME).'/';
        $_printer_name = str_replace('.printer','',$this->printer_name);
        $_user = $this->job_owner ? '-'.$this->job_owner : ($job_attr['user-name'] ? '-'.$job_attr['user-name'] : null);		
	    //self::write2disk($classpath.'extraclass.txt',"\r\n".$classpath);
			
		//get printer attributes..subsciption to services
		//echo $this->printer_path; //<<<<<<<<<<<<<< WHEN FIRE-UP MANUALY FROM OUTER DIR
        $pr_config = @parse_ini_file($this->printer_path . $this->printer_name.'.conf', true);
		
		//$srvs = var_export($pr_config,1); 
		//self::write2disk($this->jobs_path.'/job'.$job_id.'.conf',$srvs);			
		
		//get job state..executed services
		if (is_readable($this->jobs_path.'/job'.$job_id.'.state'))
          $jb_config = @parse_ini_file($this->jobs_path.'/job'.$job_id.'.state');
		else
		  $jb_config = array();//empty
		
		//PROCESSING....................
		if ((!empty($pr_config['SERVICES'])) && ($handlers = $pr_config['SERVICES'])) {
		
		    if ($this->network_log) 
		        self::write2disk($this->netfile,":PROCESSING:");		
		
		    if (is_array($pr_config['PARAMS'])) {
		        $apply_services_method = $pr_config['PARAMS']['services']; 
		        if ($apply_services_method == 'must') {
		            //sort by value =1,2,3,4...
		            asort($handlers);
		        }
				
				//final out...
				$job_file_output = $pr_config['PARAMS']['foutput'];
            }
			
			//extension to mod file per service
            if (!empty($pr_config['EXTOUT'])) {
			   $file_mod_extensions = (array) $pr_config['EXTOUT'];
            }			
			else
			   $file_mod_extensions = array(); //empty
		
		    //self::write2disk($this->logfile,"\r\n".var_dump($pr_config));
			
			//multiple service subscription
		    foreach ($handlers as $service=>$is_on) {	

		        if ($this->network_log) 
		          self::write2disk($this->netfile,":$service:$is_on:");			
			
			    $extension = null; //init per service 
			    $state_message .= $service.'=';		

                if (($standalone) && (!$silentmode)) 
                    $ret .= "<br><b>$service :</b>"; 				
			   
                if ($is_on) {
				
				    if (($standalone) && (!$silentmode))  
                        $ret .= " ON."; 	
				   
				    /*if ((is_array($file_mod_extensions)) && ($file_mod_extensions[$service])) {
			            $extension = $file_mod_extensions[$service];
						if ($extension) {
						    $new_job_file = self::_add_extension($job_file, $extension, true);
						
						    //$c = @copy($this->jobs_path.'/'.$job_file, $this->jobs_path.'/'.$new_job_file);
						    $c = @rename($this->jobs_path.'/'.$job_file, $this->jobs_path.'/'.$new_job_file);
						    if ($c===true) {
						        $current_job_file = $this->jobs_path.'/'.$new_job_file; 
						        $job_file = $new_job_file;						   
						        self::write2disk($this->logfile,"\r\nNEW_EXTENSION:".$new_job_file."\r\n");
						        if (($standalone) && (!$silentmode)) 
                                    $ret .= "<br>Add extension: $new_job_file";
						    }
                        }						
				    }*/
					
					
				    try { //prevent from printer hanging....
                        //@self::write2disk($classpath.'extraclass.txt',"\r\n".$classpath.$_printer_name.'.'.$service.'.php');
						
						//$adhoc2load = 'handlers\\addhoc';
						//$class2load = 'handlers\\'.$service;
						
				        if ((class_exists('addhoc', true)) && 
						    (is_readable($classpath.$_printer_name.'.'.$service.'.php'))) {
						
						  //if user service code file
                          if (is_readable($classpath.$_printer_name.'.'.$service.$_user.'.php')) 
						    $addhoc_code = @file_get_contents($classpath.$_printer_name.'.'.$service.$_user.'.php');	
                          else //standart admin-nouser service code file						  
						    $addhoc_code = @file_get_contents($classpath.$_printer_name.'.'.$service.'.php');	
							
						  //@self::write2disk($classpath.$_printer_name.'-addhoc.log',$service.'>'.$_user."\r\n");
						  if (($standalone) && (!$silentmode)) 
                            $ret .= "<br>Class addhoc: $_printer_name.$service.php";
							
					      switch ($apply_services_method) {
						                 //if prev service is false
			                case 'must': if ($musttrue===false) {
							                $state_message .= "_FALSE\r\n";
									        //go out of loop...
										    goto outloop; 
											//break;
										 } 										 
			                case 'all' : 
                            case 'any' : 
				            default    : //bypass already executed services
			                             if ((!$standalone) && ($jb_config[$service]=='_TRUE')) {
				                           $state_message .= "_TRUE\r\n"; //rewrite..
										   $musttrue = true; //never here..
										   
										   //if (!$standalone) //if standalone..repeat
				                           continue;//next loop
				                         }
						    //continue...loading class
						    $srv = new addhoc($this->job_owner,//$this->auth_obj,
						                        $data,//IS NULL 
											    $job_id, 
						                        $current_job_file, 
											    $current_job_attr, 
												$this->printer_name);
												
							if ($bytes = $srv->execute($addhoc_code,false,$standalone)) {
								$state_message .= "_TRUE\r\n";
								$musttrue = true;
								
								//get service job atributes
								if (is_array($current_job_attr))
								  $current_job_attr = array_merge($current_job_attr, $srv->_get_attr());
								else
								  $current_job_attr = $srv->_get_attr();								
								
								//bytes meter per job/loops
							    self::_add_job_process_count(intval($bytes));
								
								if (($standalone) && (!$silentmode)) 
                                   $ret .= "<br>Execute: $bytes";					
                            }
							else {
							    $state_message .= "_FALSE\r\n";
								$musttrue = false;
								
								if (($standalone) && (!$silentmode))  
                                   $ret .= "<br>Execute: 0";
							}								
                          }//switch										 
						}
						elseif (class_exists($service, true)) { 
						   /* if (is_object($srv = new $service($this->job_owner,//$this->auth_obj,
						                        $data,//IS NULL 
											    $job_id, 
						                        $current_job_file, 
											    $current_job_attr, 
												$this->printer_name))) {*/
						
						  //@self::write2disk($classpath.$_printer_name.'-agent.log',$service.'>'.$_user."\r\n");
						  if (($standalone) && (!$silentmode)) 
                            $ret .= "<br>Class exist: true";
							
						  switch ($apply_services_method) {
						                 //if prev service is false
			                case 'must': if ($musttrue===false) {
							                $state_message .= "_FALSE\r\n";
									        //go out of loop...
										    goto outloop; 
											//break;
										 } 	
										 
			                case 'all' : 
                            case 'any' : 
				            default    : //bypass already executed services
			                             if ((!$standalone) && ($jb_config[$service]=='_TRUE')) {
				                           $state_message .= "_TRUE\r\n"; //rewrite..
										   $musttrue = true; //never here..
										   
										   //if (!$standalone) //if standalone..repeat
				                           continue;//next loop
				                         }	
                            
				            $srv = new $service($this->job_owner,//$this->auth_obj,
						                        $data,//IS NULL 
											    $job_id, 
						                        $current_job_file, 
											    $current_job_attr, 
												$this->printer_name);					  
						    
			                if (method_exists($srv, 'execute')) {
						  
				              if ($bytes = $srv->execute($standalone)) {		  
							
							    $state_message .= "_TRUE\r\n";
								$musttrue = true;
								
								//get service job atributes
								if (is_array($current_job_attr))
								  $current_job_attr = array_merge($current_job_attr, $srv->_get_attr());
								else
								  $current_job_attr = $srv->_get_attr();
								
								//bytes meter per job/loops ..//..bytes returned not the whole data
							    self::_add_job_process_count(intval($bytes));//strlen($data));
								
								if (($standalone) && (!$silentmode)) 
                                   $ret .= "<br>Execute: $bytes";									
							  }
							  else {
							    $state_message .= "_FALSE\r\n";
								$musttrue = false;
								
								if (($standalone) && (!$silentmode))  
                                   $ret .= "<br>Execute: 0";
							  }	
				            }
                            else { 
                              $state_message .= "_INIT\r\n"; //or TRUE ? 
							  $musttrue = true;
							}  
							  
						  }//switch	  
						} 
                        else 	
                          $state_message .= "_ERROR\r\n";

                        if ($this->network_log) 
		                    self::write2disk($this->netfile,":STATE:$state_message:");						  
                    } 
		            catch (Exception $e) {
                        $err = "\r\n Class $service not exist or error in service class!\r\n";
						$backtrace = $e->getTraceAsString();
						self::write2disk($this->logfile,$err.$backtrace."\r\n\r\n");
						$state_message .= "_ERROR\r\n";
						
						//self::write2disk('a-error.log',$err.$backtrace."\r\n\r\n");
						if (($standalone) && (!$silentmode)) 
                            $ret .= "<br>Error: $err<br>" . $backtrace;
							
		                if ($this->network_log) 
		                    self::write2disk($this->netfile,":ERROR:\r\n".$backtrace."\r\n");							
                    }
                }
                else {
					if (($standalone) && (!$silentmode))  
                        $ret .= " OFF."; 
						
                    $state_message .= "_OFF\r\n";	
				}	

				//CHANGE JOB STATE, FILE NAME INTO SERVICE LOOP..TO HANDLE NEW job_file NAME
                /*if ($standalone)  				
                   $ret .= self::_set_job_state($job_id, $job_file, $apply_services_method);					
				else   
                   $ret = self::_set_job_state($job_id, $job_file, $apply_services_method);
                */				   
			}
			outloop: 
            //self::write2disk($this->jobs_path.'/job'.$job_id.'.state',$state_message);			
			$write_state = @file_put_contents($this->jobs_path.'/job'.$job_id.'.state', $state_message, LOCK_EX);
        }
		
		//return ($ret); 
		 		 
        if (($standalone) && (!$silentmode))  { 				
		    //comments added
            $ret .= self::_set_job_state($job_id, $job_file, $apply_services_method);					
		}	
		else  //plain true false 
            $ret = self::_set_job_state($job_id, $job_file, $apply_services_method);
			
        return ($ret);	
	}
	
	
    private function _set_job_state($job_id, $job_file, $apply_services_method=null) {	
	    $newfilename = null;
		
		$status = self::_get_job_state($job_id, $apply_services_method);
		//self::write2disk($this->logfile,"\r\nNFILE->".$status."|\r\n");		
				
		
		switch ($status) {
		  
		  case 'processing': self::_set_file_status($job_file, $status);
		                     $out = false;  
                             break;
 							 
		  case 'completed' : self::_set_file_status($job_file, $status);
		                     $out = true;   
							 break;
		  case 'pending'   : 
		  default          : self::_set_file_status($job_file, 'pending');
		                     $out = false; 
		}	
		
        if ($this->network_log)
		    self::write2disk($this->netfile,"\r\nJob $job_id=$status");		
		
		//return ($out);
		//return ($status);
		//self::write2disk($this->logfile,"\r\n->".$job_id.'->'.$status.'->|'.$newfilename."|\r\n");
		//$ret = array('job_file'=>$newfilename,'job_status'=>$status);
		//return ($newfilename);
		//return ($ret);
		return ($out);
	}
	
	private function _get_job_state($job_id,$apply_services_method=null) {
	
	    $jb_config = @parse_ini_file($this->jobs_path.'/job'.$job_id.'.state');
		//self::write2disk($this->jobs_path.'/job'.$job_id.'.mystate',$this->jobs_path.'/job'.$job_id.'.ini');
		
		if (!empty($jb_config)) {
		   //self::write2disk($this->jobs_path.'/job'.$job_id.'.mystate',implode(':',$jb_config));
		   
		   foreach($jb_config as $srv=>$st) { 		   
		   
		      self::write2disk($this->jobs_path.'/job'.$job_id.'.mystate',
			                   $apply_services_method.'>'.$srv.'='.$st."\r\n"); 			
							   
              $state = trim($st);							   
			
		      if ($state!='_OFF') {
		      
			    switch ($apply_services_method) {
			  
			    case 'must': 
			    case 'all' :  if ($state!='_TRUE') {
				                 self::write2disk($this->jobs_path.'/job'.$job_id.'.mystate',
			                                      "\r\n------PROCESSING---------\r\n"); 	
				                return 'processing';
							  }	
			                  break;
							  
                case 'any' : //self::write2disk($this->jobs_path.'/job'.$job_id.'.mystate',$srv.'='.$state."\r\n"); 
				default    : if ($state=='_TRUE') {
				                @unlink($this->jobs_path.'/job'.$job_id.'.state'); 
								@unlink($this->jobs_path.'/job'.$job_id.'.mystate'); 
                                return 'completed';
							 }	
			    }
              }//_OFF
		   }
		   
           switch ($apply_services_method) {
		     case 'must':
		     case 'all' : @unlink($this->jobs_path.'/job'.$job_id.'.state');
			              @unlink($this->jobs_path.'/job'.$job_id.'.mystate');
			              return 'completed';//all TRUE 
			              break;
			 case 'any' :  
			 default    : self::write2disk($this->jobs_path.'/job'.$job_id.'.mystate',
			                               "\r\n------PROCESSING---------\r\n"); 
			              return 'processing';//all FALSE
           }
		}
		
		self::write2disk($this->jobs_path.'/job'.$job_id.'.mystate',
		                 "\r\n------PENDING---------\r\n"); 		
		return 'pending';
	}
	
	private function _set_file_status($file=null,$status=null) {
	    if (!$file)
		  return;
		$ret = false; 	
		  
		$file_elements = explode(FILE_DELIMITER,$file);  

		if ((stristr($file,FILE_DELIMITER.'completed')) ||
            (stristr($file,FILE_DELIMITER.'processing')) ||
            (stristr($file,FILE_DELIMITER.'canceled')) ||
			(stristr($file,FILE_DELIMITER.'pending')) ||
            (stristr($file,FILE_DELIMITER.'deleted')))  			
		    $current_status = array_pop($file_elements);
		else
            $current_status = null; //default		
		
        $nfile    = $file; //init		
		$myfile   = $this->jobs_path . '/' . $file;			
		//filename without current status (pop)	
        $filename = implode(FILE_DELIMITER,$file_elements);	
		
        //self::write2disk($this->logfile,"FILENAME:".$file.'->'.$nfile.'->'.$current_status."\r\n");		
		  
		switch ($status) {
		
		    case 'completed' :  if ($current_status!='completed') {
			                      $nfile = $filename . FILE_DELIMITER . 'completed';
			                      $ret = @rename($myfile, $this->jobs_path . '/' . $nfile); 
								}								
                                break; 
			case 'processing':  if ($current_status!='processing') {
			                      $nfile = $filename . FILE_DELIMITER . 'processing'; 
			                      $ret = @rename($myfile, $this->jobs_path . '/' . $nfile);  
								}  
                                break;			
			case 'canceled'  :  if ($current_status!='canceled') {
			                      $nfile = $filename . FILE_DELIMITER . 'canceled';
			                      $ret = @rename($myfile, $this->jobs_path . '/' . $nfile);
								}  
                                break;
			case 'pending'   :  if ($current_status!='pending') {
			                      $nfile = $filename . FILE_DELIMITER . 'pending';
			                      $ret = @rename($myfile, $this->jobs_path . '/' . $nfile);
								}  
                                break;								
			case 'deleted'   :  if ($current_status!='deleted') {
			                      $nfile = $filename . FILE_DELIMITER . 'deleted';
			                      $ret = @rename($myfile, $this->jobs_path . '/' . $nfile);
								}  
                                break;								
			default          :  //no status....pending
			                    if ($current_status) { 
								  $nfile = $filename; //remove any status 
			                      $ret = @rename($myfile, $this->jobs_path . '/' . $nfile);
								}  
        }

        return $ret;		
		
		//return new name -with status-plus extension if any..
        //self::write2disk($this->logfile,"\r\nNFILE->".$nfile."|\r\n");		
		//$out = $ret ? $nfile : false;
		//return ($out);
		//return ($nfile);
	}	
	
	//meter for recurrent loop of using bytes per service
	private function _add_job_process_count($bytes=null) {
	
	    if (is_readable($this->countfile)) //$this->printer_name.'.counter'))
	      $current_bytes = intval(@file_get_contents($this->countfile));//$this->printer_name.'.counter'));
		else
          $current_bytes = 0;
		  
		$current_bytes += $bytes;
		
		$write_state = @file_put_contents($this->countfile, $current_bytes, LOCK_EX);//$this->printer_name.'.counter'
		return ($write_state);
	}	

	private function _add_extension($file, $ext=null, $remove_old_ext=false) {
	
	    if (!$ext)
		   return ($file); //as is

        $file_elements = explode(FILE_DELIMITER,$file);
        if (count($file_elements)>5)
          $current_status = array_pop($file_elements); 		
		else
    	  $current_status = null;//'pending'; //default 

        if ($remove_old_ext) {
		   if (strstr($file_elements[4],'.')) {
		     $n = array_shift(explode('.',$file_elements[4]));
		     $file_elements[4] = $n . '.' . $ext;
		   }
        }
        else //append		
		   $file_elements[4] = $file_elements[4] . '.' . $ext;

		$status = $current_status ? FILE_DELIMITER . $current_status : null;   
        $ret_name = implode(FILE_DELIMITER, $file_elements) . $status;
		
        return ($ret_name);		
	}	
	
	//callbacks.....!!!!????
	private function set_jobs_status($job_id, $job_file, $job_attr, $callback_param=null) {
	
	    $ret = self::_set_file_status($job_file,$callback_param);//'canceled');
		return $ret;
	}
	
	//empty log files
	public function flush_log_files() {
	
		//agent logs
		@unlink('addhoc.log');
		@unlink('pragent.log');
		@unlink($this->logfile);
		
		return true;
	}	
	
    function handleError($errno, $errstr, $errfile, $errline) {
        //no difference between excpetions and E_WARNING
        //$err = "\r\nuser error handler: e_warning=".E_WARNING."  num=".$errno." msg=".$errstr." line=".$errline."";
		
        error_log(sprintf("PHP %s:  %s in %s on line %d", $errors, $errstr, $errfile, $errline));
        $err = "ERROR: [$errno] $errstr\r\n"."$errors on line $errline in file $errfile\r\n";		
		
		self::write2disk($this->logfile,$err);
		//debug_print_backtrace();
        return true;
        //change to return false to make the "catch" block execute;
		//return false;
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
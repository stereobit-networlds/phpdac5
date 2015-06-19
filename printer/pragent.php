<?php
/* 
 * Class PRagent - process printer queue.
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

 class pragent  {
 
     protected $jobs_path, $agent_version, $logfile;
	 protected $printer_name;
	 
	 protected $job_owner;
	 protected $network_log;
	 
 
     public function __construct($printer_name=null, $job_owner= null, $callback_function=null, $callback_param=null, $log=null) {		
	 
	    spl_autoload_register(array($this, 'loader'));
		set_error_handler(array($this,'handleError'));
		
		$this->agent_version = '1.0';   //IPP server version
		$this->network_log = $log ? true : false;

		$this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs';//$_SERVER['DOCUMENT_ROOT'] . pathinfo($_SERVER['PHP_SELF'],PATHINFO_DIRNAME); 
		$this->logfile = $printer_name ? $printer_name . '.log' : 'pragent.log';
		$this->printer_name = $printer_name ? $printer_name : 'root-printer';
		$this->job_owner = $job_owner; 
		
		if ($printer_name) 
		  $this->jobs_path .= '/' . $printer_name;
		
        if (is_dir($this->jobs_path)) {
		   if ($callback_function) {//specific agent task
		       self::_read_jobs(null,null,$callback_function,$callback_param);// 'set_jobs_status','processing');//null);     
		   }
		   else //common agent task
		       self::_read_jobs(null,null,null);//'set_jobs_status','processing');//null);   
        }	
        else {
		   self::write2disk($this->logfile,"\r\n".$this->jobs_path . ' not exist!');
        }
    }
	
    private function loader($className) {
	
	    //echo 'Trying to load ', $className, ' via ', __METHOD__, "()\n"; 
        self::write2disk($this->logfile,"\r\nTrying to load ". $className. ' via '. __METHOD__. "()\r\n");
		
		try {
            include 'handlers/'. $className . '.php';
			
			if ($this->network_log)
		      self::write2disk('network.log',":$className:");
		} 
		catch (Exception $e) {
            $err = "\r\n File $className not exist!";
			self::write2disk($this->logfile,$err);
        }
    }	
	
	//read jobs directory
	private function _read_jobs($my_jobs=false, $which_jobs=false, $callback_function=null, $callback_param=null) {
	    $jobs = null;
		
        $mydir = dir($this->jobs_path);	
		self::write2disk($this->logfile,"\r\n".date(DATE_RFC822).$this->jobs_path);	
		
        /*
        $which_jobs = self::read_request_attribute('which-jobs');		
		$limit = self::read_request_attribute('limit');
		$my_jobs = $my_jobs ? $my_jobs : self::read_request_attribute('my-jobs');
		$user = self::read_request_attribute('requesting-user-name');
		*/
		$i=0;		
        while ($fileread = $mydir->read()) { 
		    if (substr($fileread,0,4)=='job-') {
			
                $i+=1;
				$pf = explode('-',$fileread);
				$jid = $pf[1];//sort			
				$jowner = $pf[3];
				
			    //switch depending on request attr
			    switch ($which_jobs) {
				
				    case 'completed'     : if (($my_jobs) && ($user) && ($jowner!=$this->job_owner))
					                         break;
                                           elseif (stristr($fileread,'-completed')) {
				                             $jobs[intval($jid)] = $fileread;		
                                           } 					
					                       break;
										   
				    case 'processing'    : if (($my_jobs) && ($user) && ($jowner!=$this->job_owner))
					                         break;
                                           elseif (stristr($fileread,'-processing')) {
				                             $jobs[intval($jid)] = $fileread;		
                                           } 					
					                       break;
					
                    //usually error at handler call. Otherwise not a status = pending					
				    case 'pending'       : if (($my_jobs) && ($user) && ($jowner!=$this->job_owner))
					                         break;
                                           elseif (stristr($fileread,'-pending')) {
				                             $jobs[intval($jid)] = $fileread;		
                                           } 					
					                       break;										   

				    case 'not-completed' : if (($my_jobs) && ($user) && ($jowner!=$this->job_owner))
					                         break;
                                           elseif (stristr($fileread,'-completed')==false) { 
				                             $jobs[intval($jid)] = $fileread;
				                           } 										   
								  
				    case 'all'           :				    
				    default              : if (($my_jobs) && ($user) && ($jowner!=$this->job_owner))
					                         break;
                                           //elseif (stristr($fileread,'-completed')==false) { 
										   elseif ((stristr($fileread,'-completed')==false) &&
           										   (stristr($fileread,'-deleted')==false) &&
												   (stristr($fileread,'-canceled')==false)
												   ){ //only pending & processing
				                             $jobs[intval($jid)] = $fileread;
				                           } 
				}						   
			}
            
            //if (($limit) && ($limit>$i)) 			
			  //break;
	    }
		
        $mydir->close ();	

		if (is_array($jobs)) {
		    
			//krsort($jobs);
			ksort($jobs);
		
		    foreach ($jobs as $jid=>$fileread) {
			   $fp = explode('-',$fileread);
	           $job['id'] = $fp[1];
	           $job['remote-ip'] = str_replace('~',':',$fp[2]);
	           $job['user-name'] = $fp[3];
		       $job['job-name'] = $fp[4];			   
			   
			   if ($callback_function) {
			     $ret = call_user_func_array(array($this,$callback_function),array($job["id"], $fileread, $job, $callback_param));
			   }	 
			   else
			     $ret = self::_process_job($job['id'], $fileread, $job);
			   
			   $status = $ret?'completed':'processing';
			   //echo $fileread;			   
			   self::write2disk($this->logfile,"\r\n".$fileread.'->'.$status);
			}
	    }		
	}
	
	private function _process_job($job_id=null, $job_file=null, $job_attr=null) {
	    if (!$job_id)
		    return null;
		
		$jf = $this->jobs_path . '/' . $job_file;
		
		if (is_readable($jf)) {   

			//call handler, do the job
			if ($data = file_get_contents($jf))
			  $ret = self::_call_handler($data, $job_id, $job_file, $job_attr);
        }

        return ($ret?$job_id:false);		
	}
	
	private function _call_handler(&$data=null, $job_id=null, $job_file=null, $job_attr=null) {
	    if (!$data)
		    return false;
		
        $ret = false;
        $status = 'pending';	
			
		//get printer attributes..subsciption to services
        $pr_config = @parse_ini_file($this->printer_name.'.conf', true);
		//get job state..executed services
        $jb_config = @parse_ini_file($this->jobs_path.'/job'.$job_id.'.state');
		
		if (is_array($pr_config['PARAMS'])) {
		   $apply_services_method = $pr_config['PARAMS']['services']; 
        }		
		
		//PROCESSING....................
		if (!empty($pr_config['SERVICES'])) {
		
		    //self::write2disk($this->logfile,"\r\n".var_dump($pr_config));
			
			//multiple service subscription
		    foreach ($pr_config['SERVICES'] as $service=>$is_on) {
			
			    $state_message .= $service.'=';			
			
			    //bypass already executed services
			    if ($jb_config[$service]=='_TRUE') {
				  $state_message .= "_TRUE\r\n"; //rewrite..
				  continue;
				}  
			   
                if ($is_on) {
				    try {	    
						if (class_exists($service, true)) {
						
				          $srv = new $service($data, $job_id, 
						                      $this->jobs_path.'/'.$job_file, 
											  $job_attr, $this->printer_name);					  
						  
			              if (method_exists($srv, 'execute')) {
						  
				            if ($export_data = $srv->execute()) {		  
							
		                      $w = @file_put_contents($this->jobs_path.'/'.$job_file, $export_data);
							  if ($w) 
							    $state_message .= "_TRUE\r\n";
							  else
                                $state_message .= "_WRITE_ERROR\r\n";							  
							}
							else
							  $state_message .= "_FALSE\r\n";
							
				          }
                          else 
                              $state_message .= "_INIT\r\n"; 						  
						} 
                        else {	
                          $state_message .= "_ERROR\r\n";						  
						}
                    } 
		            catch (Exception $e) {
                        $err = "\r\n Class $service not exist!";
						self::write2disk($this->logfile,$err);
                    }					
                }
                else 
                    $state_message .= "_OFF\r\n";				
				
			}
            //self::write2disk($this->jobs_path.'/job'.$job_id.'.state',$state_message);			
			$write_state = @file_put_contents($this->jobs_path.'/job'.$job_id.'.state', $state_message, LOCK_EX);
        }
		//.....................PROCESSING
		//PENDING IF NOT SERVICES........

		/*if ($export_data) {
		  //save file...
		  self::_set_file_status($job_file, 'saving'); //test status		  
		  $out = @file_put_contents($this->jobs_path.'/'.$job_file, $export_data);
		}*/
		
		//GET PROCESSING STATE.........
		$status = self::_get_job_state($job_id, $apply_services_method);
		
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
		    self::write2disk('network.log',"\r\nJob $job_id=$status");		 
		
        return ($out);		
	}
	
	function _get_job_state($job_id,$apply_services_method=null) {
	
	    $jb_config = @parse_ini_file($this->jobs_path.'/job'.$job_id.'.state');
		//self::write2disk($this->jobs_path.'/job'.$job_id.'.mystate',$this->jobs_path.'/job'.$job_id.'.ini');
		
		if (!empty($jb_config)) {
		   //self::write2disk($this->jobs_path.'/job'.$job_id.'.mystate',implode(':',$jb_config));
		   
		   foreach($jb_config as $srv=>$state) {
		   
		      //self::write2disk($this->jobs_path.'/job'.$job_id.'.mystate',
			  //                 $apply_services_method.'>'.$srv.'='.$state."\r\n"); 
							   
		      if ($state=='_OFF') continue;
		      
			  switch ($apply_services_method) {
			  
			    case 'all' :  if ($state!='_TRUE') 
				                return 'processing';
			                  break;
							  
                case 'any' : //self::write2disk($this->jobs_path.'/job'.$job_id.'.mystate',$srv.'='.$state."\r\n"); 
				default    : if ($state=='_TRUE') {
				                @unlink($this->jobs_path.'/job'.$job_id.'.state'); 
                                return 'completed';
							 }	
			  }
		   }
		   
           switch ($apply_services_method) {
		    
		     case 'all' : @unlink($this->jobs_path.'/job'.$job_id.'.state');
			              return 'completed';//all TRUE 
			              break;
			 case 'any' :  
			 default    : return 'processing';//all FALSE
           }
		}
		
		return 'pending';
	}
	
	function _set_file_status($file=null,$status=null) {
	    if (!$file)
		  return;
		$ret = false;  
		  
		$file_elements = explode('-',$file);  
		
		if ((stristr($file,'-completed')) ||
            (stristr($file,'-processing')) ||
            (stristr($file,'-canceled')) ||
			(stristr($file,'-pending')) ||
            (stristr($file,'-deleted')))  			
		    $current_status = array_pop($file_elements);
		else
            $current_status = null; //default		
			
        self::write2disk($this->logfile,$file.'->'.$current_status);
		
		$myfile   = $this->jobs_path . '/' . $file;
        $filename = $this->jobs_path . '/' . implode('-',$file_elements);//filename without current status (pop)		
		  
		switch ($status) {
		
		    case 'completed' :  if ($current_status!='completed')
			                      $ret = @rename($myfile, $filename . '-completed'); 
                                break;
			case 'processing':  if ($current_status!='processing')
			                      $ret = @rename($myfile, $filename . '-processing');  
                                break;			
			case 'canceled'  :  if ($current_status!='canceled')
			                      $ret = @rename($myfile, $filename . '-canceled');
                                break;
			case 'pending'   :  if ($current_status!='pending')
			                      $ret = @rename($myfile, $filename . '-pending');
                                break;								
			case 'deleted'   :  if ($current_status!='deleted')
			                      $ret = @rename($myfile, $filename . '-deleted');
                                break;								
			default          :  //no status....pending
			                    if ($current_status) 
			                      $ret = @rename($myfile, $filename);//remove any status 
        }

        return $ret;		
	}	
	
	
	
	
	//callbacks.....
	private function set_jobs_status($job_id, $job_file, $job_attr, $callback_param=null) {
	
	    $ret = self::_set_file_status($job_file,$callback_param);//'canceled');
		return $ret;
	}
	
	//empty log files
	public function flush_log_files() {
	
		//agent logs
		@unlink('pragent.log');
		@unlink($this->printer_name . '.log');
		
		return true;
	}	
	
    function handleError($n, $m, $f, $l) {
        //no difference between excpetions and E_WARNING
        $err = "\r\nuser error handler: e_warning=".E_WARNING."  num=".$n." msg=".$m." line=".$l."\n";
		
		self::write2disk($this->logfile,$err);
		
        return true;
        //change to return false to make the "catch" block execute;
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
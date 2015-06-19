<?php
$__DPCSEC['RCUWIZARD_DPC']='1;1;1;1;1;1;2;2;9';

if ( (!defined("RCUWIZARD_DPC")) && (seclevel('RCUWIZARD_DPC',decode(GetSessionParam('UserSecID')))) ) {
define("RCUWIZARD_DPC",true);

$__DPC['RCUWIZARD_DPC'] = 'rcuwizard';

$x = GetGlobal('controller')->require_dpc('libs/cpanelx3.lib.php');
require_once($x);

$a = GetGlobal('controller')->require_dpc('phpdac/rcwizard.dpc.php');
require_once($a);

$b = GetGlobal('controller')->require_dpc('libs/htaccess.lib.php');
require_once($b);

$c = GetGlobal('controller')->require_dpc('libs/appkey.lib.php');
require_once($c);

GetGlobal('controller')->get_parent('RCWIZARD_DPC','RCUWIZARD_DPC');

$__EVENTS['RCUWIZARD_DPC'][20]='cpupgrade';
$__EVENTS['RCUWIZARD_DPC'][21]='cpwizcancel';
$__EVENTS['RCUWIZARD_DPC'][22]='cpupgcancel';
$__EVENTS['RCUWIZARD_DPC'][23]='cpwupdate';
$__EVENTS['RCUWIZARD_DPC'][24]='cpwbackup';

$__ACTIONS['RCUWIZARD_DPC'][20]='cpupgrade';
$__ACTIONS['RCUWIZARD_DPC'][21]='cpwizcancel';
$__ACTIONS['RCUWIZARD_DPC'][22]='cpupgcancel';
$__ACTIONS['RCUWIZARD_DPC'][23]='cpwupdate';
$__ACTIONS['RCUWIZARD_DPC'][24]='cpwbackup';

$__LOCALE['RCUWIZARD_DPC'][0]='RCUWIZARD_DPC;Upgrade Wizard;Upgrade Wizard';
$__LOCALE['RCUWIZARD_DPC'][1]='_backup;Backup;Αποθήκευση δεδομένων';
$__LOCALE['RCUWIZARD_DPC'][2]='_addspace;Limited space, add space;Πρόσθεσε χωρητικότητα';
$__LOCALE['RCUWIZARD_DPC'][3]='_ago;after expiration;που έληξε';
$__LOCALE['RCUWIZARD_DPC'][4]='_fromnow;before expire;πρίν τη λήξη';
$__LOCALE['RCUWIZARD_DPC'][5]='_modified;modified;Ενημερώθηκε';
$__LOCALE['RCUWIZARD_DPC'][6]='_ago2;ago;πρίν';
$__LOCALE['RCUWIZARD_DPC'][7]='_fromnow2;from now;μετά';

define('DS', DIRECTORY_SEPARATOR); 

class rcuwizard extends rcwizard {
  
    var $upgrade_root_path;
	var $log, $wf, $error;
	var $instaled, $installit, $reinstall_question;
	var $url, $murl, $prpath;
	var $appkey;

	function __construct() {

	    parent::__construct();
		
		$this->murl = arrayload('SHELL','ip');
        $this->url = $this->murl[0];
		$this->prpath = paramload('SHELL','prpath');	
		
		$this->upgrade_root_path = $this->prpath . '../../cp/upgrade-app/';	
		
        $this_log = null;	
		$this->error = false;
        $this->instaled = false;	
		$this->reinstall_question = false;
		
		//upgrade wizard...
		//$this->wf = $_SESSION['wf'] ? $_SESSION['wf'] : (GetReq('wf') ? GetReq('wf') : null);
		if ($addon = (GetParam('wf'))) {
		    //print_r($_POST);
			if ($this->instalit = $this->call_upgrade_ini($addon)) {//copy from upgrade root dir
				$this->wf = $addon;
				SetSessionParam('wf',$this->wf);//save to session
			}	
			else
                $this->instalit = false;					
		}	
		else {
		    $this->wf = $_SESSION['wf'];
		    $this->instalit = $_SESSION['wf'] ? $_SESSION['wf'] : false; //true			
		}	
		//echo $this->instalit,'>',$_SESSION['wf'];		
		$this->wizardfile = $this->wf ? "cpwizard-".$this->wf.".ini" : 'cpwizard.ini';
		
		//re-read params
        $this->environment = $_SESSION['env'] ? $_SESSION['env'] : $this->read_env_file(true);//session or read..; 				
        $this->wdata = $_SESSION['wdata'] ? $_SESSION['wdata'] : $this->read_wizard_file(true);//session or read..; 		
	    $this->wstep = $_SESSION['wstep'] ? $_SESSION['wstep'] : 0;
		
		$this->appkey = new appkey();
	}
	
	function event($event=null) {
	
	    //$this->pre_check_installation();
	
	    switch ($event) {
		
	      case 'cpwbackup' : break;		

          case 'cpwizcancel' : //just ask for cancel
		                       $this->log = 'Cancel installation.'; 
                               break;			
		  case 'cpupgcancel' :  if ($this->instalit) { 
									//just exit if procedure canceled
									$this->log = '<br>clear installation details!';
									$this->wdata = $_SESSION['wdata'] = null; 
									$this->wstep = $_SESSION['wstep'] = null;
									$this->wf = $this->instalit = $_SESSION['wf'] = null;
									
									$this->log = '<br>delete installation files!';
									if (is_readable($this->prpath . $this->wizardfile))
										@unlink($this->prpath . $this->wizardfile);
								}	   
							   //echo 'return to dashboard!'; //<<<<<<
							   header('location:cp.php?editmode=1'); //return to dashboard...
		                     break; 	  

		  case 'cpwizexit' : //if app installed reredirect to app
		                     //if ($this->instalit) {
							    //session_destroy();//kill session ..into redirect
								
								//just exit if procedure completed
								$this->log = '<br>clear installation details!';
								$this->wdata = $_SESSION['wdata'] = null; 
								$this->wstep = $_SESSION['wstep'] = null;
								$this->wf = $this->instalit = $_SESSION['wf'] = null;								
								
		                        $this->redirect_to_app();		
							 /*}
							 else {
							    //just exit if procedure canceled
								//echo 'return to dashboard!'; //<<<<<<
								header('location:cp.php?editmode=1'); //return to dashboard...
							 }*/	
		                     break; 
							 
		  case 'cpwizsave' : if ($this->instalit) { // && ($this->instaled = $this->install_siwapp())) { //<<<<<<<
		                        //echo 'save';
								
								//save wiz file ..
								$ok = $this->write_wizard_file(false);//true //..to enable/disable wiz next time..
								//save env file updated with addon
			                    $ok2 = $this->write_env_file($this->wf, 1, true);
								
								$this->instaled = $ok ? ($ok2 ? true : false) : false;
							 }
                             break;		  

		  case 'cpwiznext' : if ($this->instalit) $this->inc_wizard_step(); break;
		  case 'cpwizprev' : if ($this->instalit) $this->dec_wizard_step(); break;
		  case 'cpwizskip' : if ($this->instalit) $this->inc_wizard_step(); break;
		  
		  case 'cpwupdate' :
          case 'cpupgrade' : 
		  default          : 
		                     
        }			
    }
	
	function action($action=null) {	
	    //echo $this->wstep;	
	
	    switch ($action) {	
		
		  case 'cpwbackup' :   $out = $this->backup_directory(GetReq('zpath'),GetReq('zname'));
			                   break;		

		  case 'cpupgcancel' : //never here redirect...					
		  case 'cpwizcancel' : //just exit if procedure canceled
		                       //if ($this->instalit) 
								  $out = $this->cancel_step($this->log);
                               break;			
		
		  case 'cpwizsave' :   if ($this->instalit) {
		                          if ($this->instaled)
									$out = $this->completed_step($this->log);
								  else	  
									$out = $this->error_step($this->log);
							   }		
		                       break; 
		  case 'cpwiznext' :
		  case 'cpwizprev' :
		  case 'cpwizskip' :
		  											   
		  case 'cpwizexit' : //just exit if procedure canceled or completed....
		                     //break;
		  case 'cpwupdate' :					 	
          case 'cpupgrade' :
          default          : if (!$this->instalit) { //pre-install checks
                               		  
		                        if ($this->log) {//pre-install warning-error
								    //echo 'log:'.$this->log;
								    if (GetParam('Submit')==='No') //question reinstall answered No (if yes never here...=wizard_step)
										$out = $this->final_step(false); //wizard last step canceled procedure
									else //pre-install checks	
										$out = $this->error_step($this->log);
								}	
							    else {	//setup link	
								    //if addon not defined lets choose ....
									//$msg = '<br>upgrade..' . seturl('t=cpwizard&wf=siwapp', 'Siwapp');
                                    $addon = GetParam('addon') ? GetParam('addon') : null;//'siwapp';	
                                    //echo $addon; 	
                                    if ($addon) {									
										$out = $this->welcome_step($msg, $addon);
									}	
									else {
                                        //$out = $this->error_step('Undefined add-on. Repeat the procedure.');
										
										//addon screen
										//$cb = base64_encode($_ENV["HTTP_HOST"]);//str_replace('www.','',$_ENV["HTTP_HOST"]));
										//header('location:http://stereobit.com/netpanel.php?t=modapp&cb='.$cb);
										
										$out = $this->update_page();//true);
									}	
								}	
	                         }
							 else //execute wizard step scenario
		                       $out = $this->wizard_step();
		 						  
        }
		
        return ($out);

    }
	
	/*protected function pre_check_installation() {
	}*/
	
	protected function update_page($gotourl=false) {
	
	   if ($gotourl) {
	    //addon screen
        $cb = base64_encode($_ENV["HTTP_HOST"]);//str_replace('www.','',$_ENV["HTTP_HOST"]));
		header('location:http://stereobit.com/netpanel.php?t=modapp&cb='.$cb);
		return;
	   }
	
	   //$ret = GetGlobal('controller')->calldpc_method('rccontrolpanel._show_update_tools');
	   $ret = $this->_show_update_tools();
	   return ($ret);
	}
	
	//override
	protected function wizard_step() {
		
		if (!empty($this->wdata)) {
		  //echo 'current step:'.$this->wstep;
		  $step_index = 0; //0 is welcome screen
		  foreach ($this->wdata as $stepname=>$stepaction) {
			//echo $stepname,':',$stepaction,'>';
  
			if (($stepaction) && ($step_index==$this->wstep)) {//if step val exist
			    //echo $stepname,':',$stepaction,'>';
				$ret = $this->render_step($stepname, $stepaction);
				if ($ret) 
				    return ($ret); //else continue....other steps...	
			}
			else
			    $step_index += 1;
		  }
		}
		
		//finilizing wizard exist screen...
		//echo $this->wstep,'>';
		if ($this->wstep) //has do the wiz cycle...
		   $ret = $this->final_step(true); //no final step....
		else   
		   $ret = $this->completed_step();
		   
		return ($ret);
	}
	
	//ovwerride render wizard step
	protected function render_step($name=null, $value=null) {
		if (!$name) return ('no named step!');
		//echo $name;
		switch ($name) {
		
			case 'dnload5' :
			case 'dnload4' :
			case 'dnload3' :
			case 'dnload2' :
			case 'dnload1' :
		    case 'dnload'  :  $msg = $this->install_addon_step($name,$value);
                              $ret = $this->command_step($msg);	
			                  break;		

			case 'goturl5' :
			case 'goturl4' :
			case 'goturl3' :
			case 'goturl2' :
			case 'goturl1' :
		    case 'goturl'  : $msg = $this->install_addon_step($name,$value);
                             $ret = $this->command_step($msg);	
			                 break;
							 
			case 'copyfn5'  :							 
			case 'copyfn4' :							 
			case 'copyfn3' :
			case 'copyfn2' :				 
			case 'copyfn1' :				 
			case 'copyfn'  :  $msg = $this->install_addon_step($name,$value);
                              $ret = $this->command_step($msg);	
			                  break;							 
		
			case 'copyrc5' :
			case 'copyrc4' :
			case 'copyrc3' :
			case 'copyrc2' :
			case 'copyrc1' :
		    case 'copyrc'  :  $msg = $this->install_addon_step($name,$value);
                              $ret = $this->command_step($msg);	
			                  break;			
			
			case 'cpanel5' :
			case 'cpanel4' :
			case 'cpanel3' :
			case 'cpanel2' :
			case 'cpanel1' :
		    case 'cpanel'  :  							  
			                  $msg = $this->install_addon_step($name,$value);
                              $ret = $this->command_step($msg);	
                              break;	
		    case 'phpdac0' :  								 
		    case 'phpdac9' :  								 
		    case 'phpdac8' :  								 
		    case 'phpdac7' :  								 
		    case 'phpdac6' :  							 
		    case 'phpdac5' :  								 
		    case 'phpdac4' :  								 
		    case 'phpdac3' :  								 
		    case 'phpdac2' :  								 
		    case 'phpdac1' :  							  
		    case 'phpdac'  :  //remote dpc calls
			                  //$msg = $this->install_addon_step($name,$value);
							  //if (defined('RCCONFIG_DPC')) echo 'rxxx'; else echo 'no';
							  if (stristr($value,',')) { //has args 
								$p = explode(',',$value);
								$dpccmd = array_shift($p);//first part
								//$test .= implode(',',$p) . $dpccmd.'>';
								foreach ($p as $pi=>$pn) {
								  if (substr($pn,0,1)=='@')  //is wizarg arg
		                             $arguments[] = $this->get_wizard_arg(substr($pn,1));
								  else
								     $arguments[] = $pn; //as is
								}
								$args = implode('+',$arguments);
								$linker = (stristr($dpccmd,' use ')) ? '+':' use ';
								$istrue = GetGlobal('controller')->calldpc_method($dpccmd.$linker.$args);
								$error = $dpccmd.$linker.$args;//.':'.$test;
							  }
							  else {
			                    $istrue = GetGlobal('controller')->calldpc_method($value);
								$error = 'internal func logical error';
							  } 	
							  //$msg .= implode(',',array_keys($this->wdata));
                              //$test .= $istrue;							  
                              $msg .= $istrue ? (strlen($istrue)>1?$istrue:'Done'):'ERROR:'.$error;							
							
                              $ret = $this->command_step($msg, true);			
			                  break;			
							
			case 'execmd5' :
		    case 'execmd4' :							  
		    case 'execmd3' :
		    case 'execmd2' :							  
		    case 'execmd1' :							  
		    case 'execmd'  :  //local func call
			                  $params = explode(',',$value);
			                  if (method_exists($this, $params[0])) {
							     $function = $params[0];
                                 if (substr($params[1],0,1)=='@') //is wizarg arg
 							        $argument = $this->get_wizard_arg(substr($params[1],1)); 							  
							     //else as exist/entered
								 
			                     $msg = $this->$function($argument);
								 $ret = $this->command_step($msg, true);
							  }	 
							  else
                                 $ret = $this->command_step('method not exist ('.$function.')'); //..continue steps 							  
			                  break;
							  
		
		    case 'wizard':	 //always step 0
							if ($value) //welcome screen
								$ret = $this->welcome_step('Pre install check are valid!'); //<<has been shown ???????????	  
							else //wizard is off (completed)
								$ret = $this->completed_step();	
							break; 							  
						   
			default      :  //if (($value) && ($value!='disabled')) //..problem bypass counters
								$ret = $this->default_step($name, $value);
							//else
                              //  $ret = false;	 //..continue steps 							
		}
		
		return ($ret);
	}
	
	//override	
	protected function default_step($name=null, $value=null) {

	    $ret  = 'Press '.seturl('t=cpwizcancel','here').' to cancel';		
		
	    $ret .= parent::default_step($name, $value);
		return ($ret);
	}		
	
	protected function command_step($msg=false, $nocancel=false) {
		$cmd = 'cpwiznext'; 
		$message = $msg ? $msg : 'command has no ret value.';

	    //cmd commands can't cancel..?
		if (!$nocancel)
	        $cancel = 'Press '.seturl('t=cpwizcancel','here').' to cancel';	
		
		$ret .= <<<EOF
							<form name="wizdefstep" method="post" class="sign-up-form" action="">
								<input type="hidden" name="FormAction" value="$cmd" /> 
                                    $message 
									<br/><br/>
									$cancel
									<div class="msg grid_4 alpha omega aligncenterparent"></div>      
									<div class="grid_4 alpha omega aligncenterparent">
									<br/>
									<input type="submit" class="call-out grid_2 push_2 alpha omega" alt="Save"	title="Next" name="Submit" value="Next">
									</input>
								    </div>
							</form>
<script type="text/javascript">
	$(document).ready(function() {
		$("#stepfield").focus();
	});
</script>							
EOF;
		
 	    return ($ret);
	}	
	
	//override
	protected function welcome_step($msg=null,$addon=false) {	
	    $message = $msg;
	    $addon = $addon ? $addon : GetParam('wf');
		
	    $ret = $message; //'Welcome step:' . $msg;
		
		if (!$addon) {
		    session_destroy();//kill session 
		    return ($ret . "Addon not defined! ($addon)");
		}	
		
		$cmd = $this->instalit ? 'cpwiznext' : 'cpupgrade';
		
		$ret .= '<br><br>Install:<strong>'. str_replace('_','&nbsp;',$addon) . '</strong>';
		$ret .= '<br>Press '.seturl('t=cpwizcancel','here').' to cancel this installation';
		
		$ret .= '<form name="wizwelcomestep" method="post" class="sign-up-form" action="">
								<input type="hidden" name="FormAction" value="'.$cmd.'" />     
								<input type="hidden" name="wf" value="'.$addon.'" />  
									<div class="grid_4 alpha omega aligncenterparent">
										<br/>
										<input type="submit" class="call-out grid_2 push_2 alpha omega" alt="start"
											title="Start"
											name="Submit" value="Start">
										</input>
								    </div>
							</form>';	
							
		return ($ret);	
	}	
	
	//override..
	protected function completed_step($msg=null) { 	
	
	    $message = $msg;//'Completed step:' . $msg;

		//cpwizexit also do the redirection.......
		$goto = $this->redirect_to_app(true); //get redirect link....   
		$onclick = $this->instaled ? "onClick=\"$goto\"" : null;
	    $completed = '<br>Installation completed.'; //. $goto;		
		
		$ret .= '<form name="wizcompletestep" method="post" class="sign-up-form" action="">
		                        '.$message.' 
								<br/><br/>
								'.$completed.'		
								<input type="hidden" name="FormAction" value="cpwizexit" />     
									<div class="grid_4 alpha omega aligncenterparent">
										<br/>
										<input type="submit" class="call-out grid_2 push_2 alpha omega" alt="exit"
											title="exit"
											name="Submit" value="Exit" '.$onclick.'>
										</input>
								    </div>
							</form>';			
		
		return ($ret);	
	}	
	
	//override
	protected function final_step($finished=false, $goto=null) {
	
	    if ($finished) {//installed ok..gor save step...	
			$formaction = 'cpwizsave' ? '<input type="hidden" name="FormAction" value="cpwizsave" />' : null;
			$action = null;
		}	
		else {//cancel procedure return...
			$formaction = null;
            $action = $goto ? $goto : 'cp.php?editmode=1';
		}	

		/*
	    $ret = 'Final step:<br>';
		if (!empty($this->wdata))
		    $ret .= implode('<br>',$this->wdata);
        */ 
		//form... when submit cpwizsave... 
		$ret .= '<form name="wizlaststep" method="post" class="sign-up-form" action="'.$action.'">
								'.$formaction.'     
									<div class="grid_4 alpha omega aligncenterparent">
										<br/>
										<input type="submit" class="call-out grid_2 push_2 alpha omega" alt="finish"
											title="Finish"
											name="Submit" value="Finish">
										</input>
								    </div>
							</form>';			
		
		return ($ret);
	}
	
	protected function error_step($msg=null) { 	
	    $addon = GetParam('wf');
	    $message = $msg;//'Error step:' . $msg;
		
		$this->error = true; //toggge error var
		
		if (!$addon) {
		    session_destroy();//kill session 		
		    return ($message . "<br>Addon ($addon) not defined!");
		}	
		
		if ($this->reinstall_question) {
		
		    $cancel = 'Press '.seturl('t=cpwizcancel','here').' to cancel';		
		
		    $ret .= '<form name="wizreinstallquestion" method="post" class="sign-up-form" action="">
								<input type="hidden" name="FormAction" value="cpupgrade" /> 
                                <input type="hidden" name="wf" value="'.$addon.'" />
		                        '.$message.' 
								<br/><br/>
								'.$cancel.'									
									<div class="grid_4 alpha omega aligncenterparent">
										<br/>
										<input type="submit" class="call-out grid_1 push_1 alpha omega" alt="no"
											title="no"
											name="Submit" value="No">
										</input>
										&nbsp;
										<input type="submit" class="call-out grid_1 push_1 alpha omega" alt="yes"
											title="yes"
											name="Submit" value="Yes">
										</input>										
								    </div>
							</form>';		
		}
		else {
		    $ret .= '<form name="wizerror" method="post" class="sign-up-form" action="">
								<input type="hidden" name="FormAction" value="cpupgrade" /> 
                                <input type="hidden" name="wf" value="'.$addon.'" />								
									<div class="grid_4 alpha omega aligncenterparent">
										<br/>
										<input type="submit" class="call-out grid_2 push_2 alpha omega" alt="retry"
											title="retry"
											name="Submit" value="Retry">
										</input>
								    </div>
							</form>';
        }							
		
		return ($ret);	
	}	

	protected function cancel_step($msg=null) { 	
	
	    $message = $msg;	
	    $continue = 'Press '.seturl('t=cpupgrade','here').' to continue';	
			
		$ret .= '<form name="wizcancel" method="post" class="sign-up-form" action="">
		                        '.$message.' 
								<br/><br/>
								'.$continue.'
								<input type="hidden" name="FormAction" value="cpupgcancel" />     
									<div class="grid_4 alpha omega aligncenterparent">
										<br/>
										<input type="submit" class="call-out grid_2 push_2 alpha omega" alt="cancel"
											title="cancel"
											name="Submit" value="Cancel">
										</input>
								    </div>
							</form>';			
		
		return ($ret);	
	}	

	protected function redirect_to_app($retonclick=false, $kill_session=false) {
	
	    switch ($this->wf) {
	    
		    case 'siwapp' : session_destroy();//kill session anyway 
			
			                $go = $this->url .'/siwapp/installer.php'; 
			                $onclick = "location.href='$go'";
							if ($retonclick) 
								$ret = "top.location.href='" . $go . "'";
			                break;
							
			case 'google_analytics' :
			                $kill_session=false; //override to not kill the session 
			
                            $go = 'ganalytics.html';//'cp.php?editmode=1'; //return to cp
			                $onclick = "location.href='$go'";
							if ($retonclick) //not.. top.location
								$ret = "location.href='" . $go . "'";
								
                            break;
            
			case 'ieditor'        :
			case 'jqgrid'         :
			case 'ckfinder'       :
			case 'awstats'        :
			case 'cpimages'       :
			case 'dacpage'        :
			case 'dpcmod'         :
			case 'validatekey'    :
			case 'genkey'         :			
			case 'addkey'         :					
			case 'addspace'       :					 
			case 'edit_htmlfiles' :				 
			case 'uninstall_maildbqueue':					
            case 'maildbqueue'    :							
            case 'backup'         :
			case 'add_recaptcha'  :				 
			case 'upload_logo'    :				  
			case 'add_products'   :
			case 'add_categories' :
			case 'add_domainname' :
			case 'eshop'          :
			case 'uninstalleshop' :
			                $kill_session=false; //override to not kill the session 
			
                            $go = 'cp.php?editmode=1'; //return to cp
			                $onclick = "location.href='$go'";
							if ($retonclick) //not.. top.location
								$ret = "location.href='" . $go . "'";
								
                            break;								
							
		    default       : //goto uprgade screen...
			                $go = 'cpupgrade.php'; 
			                $onclick = "location.href='$go'";
							if ($retonclick) //on top. !!!???
								$ret = "top.location.href='" . $go . "'";							
		}	
		
		if ($kill_session) {
		    echo 'Re-enter cp';
			session_destroy();//kill session 		
		}	
		
		if ($ret)
		    return ($ret);
		
		//redirect
		header('location:'.$go);
		//die();
	}
	
	protected function call_upgrade_ini($addon) {
	   // $addon = GetParam('wf') ? : $this->wf;
	    if (!$addon) return false;
	    $inifile = $this->upgrade_root_path . "/cpwizard-".$addon.".ini";
		$target_inifile = $this->prpath . "/cpwizard-".$addon.".ini";
		$installed_inifile = str_replace('.ini','._ni',$target_inifile);
		$reinstall_answered_yes = (GetParam('Submit')=='Yes') ? true : false;

		if (is_readable($target_inifile)) {//already copied
		    //..answer has submited..
			//echo 'z';
		    return true;
		}
		
		if (is_readable($inifile)) {
		   
		   if ($this->environtment[strtoupper($addon)]==false) {
		   
		      if ((!is_readable($installed_inifile)) || ($reinstall_answered_yes)) {
			  
				$ret = @copy($inifile, $target_inifile);
				return $ret ? $addon : false;
			  }
			  else {
			    $this->log = 'This addon seems to be installed. Re-install?';
				$this->reinstall_question = true;
			  }	
		   }
		   else
		      $this->log = 'Already installed!';
		}
		else
		   $this->log = 'Unknown installation!';
		   
		return false;
	}
	
	//override
	public function get_step_title($title=null, $pretext=null, $posttext=null) {
        $mypath = $this->upgrade_root_path;		
	
        if ($this->error) 	
		    return ('Installation');
	
	    $default_ret = $title ? $title : 'Welcome';		
		$step_index = $this->wstep ? $this->wstep : '0';
		$step_file = str_replace('.ini','.text'.$step_index, $this->wizardfile);
		
		//fetch step title =first line
		$ret = (is_readable($mypath . $step_file)) 
		     ? array_shift(@file($mypath . $step_file)) 
			 : $default_ret;		
		
		return ($pretext . $ret . $posttext);	
    }	
	
	//override
	public function get_step_text($textpath=null) {
	    $mypath = $textpath ? $textpath : $this->upgrade_root_path;	
	
        if ($this->error) {	
		    $console = $this->log;
	        $ret = "<h5>Installation details</h5>
				<br/>
				<p>
				$console
				</p><br /> <br />";		
	    }
	    elseif (GetReq('t')!='cpwizcancel')  
            $ret = parent::get_step_text($mypath);
		else	
	        $ret = "<h5>Cancel installation?</h5>
				<br/>
				<p>
				You choose to cancel the installation. <br /> <br />
				You can continue by pressing the continue link 
				or press <strong>cancel</strong> to stop this installation.<br /> <br />
				</p><br /> <br />";
				
		return ($ret);
	}	
	
	//override
	protected function write_env_file($module,$mvalue=1,$reload_env_session=false) {
	    if (!$module) return false;
        $myenvfile = $this->prpath . 'cp.ini';
        $newmodule = strtoupper($module);

		if ($mvalue>0) //post as diff secid else as current secid
			$adminsecid = ($mvalue>1) ? $mvalue : GetSessionParam('ADMINSecID');	
		else
  		    $adminsecid = -1; //reset vals to 0
		
		if ($adminsecid>0) { //multiple csv value 
		    //down to current secid has no right = 0 
			//up to cuurent secid has right =1
	        $seclevid = ($adminsecid>1) ? intval($adminsecid)-1 : 1;		
		    for ($iu=1;$iu<=9;$iu++) {
			   $mv[] = ($iu<=$seclevid) ? '0' : '1'; 
			}   
		    $multiple_value = implode(',',$mv);
		}
		elseif ($adminsecid<0) //-1 reset come here set =0
		    $multiple_value = '0';//single value
		else 
		    $multiple_value = $mvalue;//single value
			
		$append_string = $newmodule.'='.$multiple_value;
   
        //backup cp file
		@copy($myenvfile, str_replace('.ini','._ni',$myenvfile));
		
		//check for existing string
		$initext = @file($myenvfile);
		$savetext = null;
		$existed = false;
		foreach ($initext as $i=>$line) {
		    if (trim($line)) {
		       $p = explode('=',$line);
			   
			   if ($p[0]==$newmodule) {
			      
                    switch ($p[1]) {//value
					   case '1' :  
					   default  : $savetext .= $newmodule . "=" . $multiple_value . PHP_EOL; 
                    } 
                    $existed = true; 					
			   }
			   else
			       $savetext .= $line . PHP_EOL; 
			}  
		}
		
		
		if (!$existed)
		   $savetext .= $newmodule . "=" . $multiple_value . PHP_EOL;  
		   
		//overwrite file   
		$ret = @file_put_contents($myenvfile, $savetext);	
		
		
		if ($reload_env_session) //reload environment in session	
            $this->environment = $this->read_env_file(true);
			
        return ($ret);			
    }	
	
	
	//step by step install actions based on ini scenario
	protected function install_addon_step($name=null, $cmd=null) {	
	    if ((!$name)||(!$cmd)) return false;
		
        //$str = 'In My Cart : 11 items';
        //$mygroupcmd = filter_var($name, FILTER_SANITIZE_NUMBER_INT);		
		$mygroupcmd = substr($name,0,6);//extract numbers
		
		//cmd filetrs
		if (stristr($cmd,'DBNAME@')) {
		  $dbname = paramload('DATABASE','dbname');
		  $cmd = str_replace('DBNAME@',$dbname,$cmd);
		}
		if (stristr($cmd,'DBUSER@')) { 
		  $dbuser = paramload('DATABASE','dbuser');
		  $cmd = str_replace('DBUSER@',$dbuser,$cmd);
		}
		/*if (substr($cmd,0,1)=='@') { //is wizarg arg
		  $argument = $this->get_wizard_arg(substr($cmd,1));
		}*/		
		//if...
		
		if (stristr($cmd,',')) 
			$params = explode(',',$cmd); 
        else
		    $params = array(0=>$cmd); 
			
		//test	
		//print_r($params);
        //echo $mygroupcmd,'>';
		/*$cp = null;
	    $this->cpanel_login($cp);
		if (is_object($cp)) 
			$ret = $mygroupcmd . '['.implode('>',$params).']';
		else
		    $ret = 'Invlid cpanel login';
        return ($ret);	*/	
		
		switch ($mygroupcmd) {
		
		    case 'phpdac' : if ($cmd)   
			                    $msg = GetGlobal('controller')->calldpc_method($cmd);

							echo $name.':'.$cmd;	
			                break;
		
		    case 'dnload' : //download file
			                $file = $this->urlpath .'/'. $params[0];
							$title = $params[1] ? $params[1] : 'press here';
							//$link = $title ? "<a href='$url' target='_blank'>$title</a>" : $url;	

							//$downloadfile = new DOWNLOADFILE($file);
							//if ($downloadfile->df_download()) //no header allowed here
							
							$ip = paramload('SHELL','ip');//$_SERVER['HTTP_HOST'];
							$pr = paramload('SHELL','protocol');		   			 
							$download_link = $pr . $ip . "/" . $params[0];
							if ((is_readable($file)) && ($download_link)) {
							    $link = $title ? "<a href='$download_link' target='_blank'>$title</a>" : $download_link;	
								$ret = $link; //$this->get_step_title();
							}	
							else
							    $ret = " file ($params[0]) not exist!";
			                break;		
		
		    case 'goturl' : //wait for user url goto and act
			                $url = $params[0];
							$title = $params[1] ? $params[1] : 'press here';
							$link = $title ? "<a href='$url' target='_blank'>$title</a>" : $url;	
							$ret = $this->get_step_title() . $link;
			                break;
		  
            case 'cpanel' : $cp = null;
			                $this->cpanel_login($cp);
							
			                if (is_object($cp)) {
							  //$mydbname = paramload('DATABASE','dbname') . 'siwapp';
							
			                  if ($params[0]=='v1') {
							    //for($i=3;$i<count($params);$i++) {
								foreach ($params as $i=>$v) {
								   if (($i>=3) && ($v))
                                      $queryArgs[] = $v;					
								}   
			                    $exec = $cp->_cpapi1_exec($params[1],$params[2], $queryArgs);
								$ret = implode('<br>',$params);//$exec ? '' : '';
							  }	
                              else {//v2	
							    $queryArgs = array($params[2]=>$params[3],
								                   $params[4]=>$params[5],
												   $params[6]=>$params[7],
												   $params[8]=>$params[9],
								                   );
			                    $exec = $cp->_cpapi2_exec($params[0],$params[1], $queryArgs);
			                    $reason = $exec['reason'];
			                    $result = $exec['result'];
                                if (!$result) 
				                   $ret = $reason;							
								else
								   $ret = $ret = implode('<br>',$params);
                              }
							}
							else
							   $ret = 'Invalid cpanel login';
                            break;  	

            case 'copyrc' : 
					        $source_path = $this->upgrade_root_path . $params[0];	
		                    $target_path = $this->urlpath . '/' . $params[1];	
			                $cf = $this->copy_r($source_path, $target_path, false);
                            $ret = 'Directory created ' . $params[1];//$target_path;							
                            break;		

            case 'copyfn' : //...
					        $source_path = $this->upgrade_root_path . $params[0];	
		                    $target_path = $this->urlpath . '/' . $params[1];	
			                $cf = @copy($source_path, $target_path);
                            $ret = $cf ? 'File copied ': "File not copied.";						
                            break;								
			               
			
			default : //do nothing....
			          $ret = null; 
		}

        return ($ret);		
	}
	
    //as call_upgrade_ini into rcuwizard dpc
    //in case of update must be also here for wizard to play..
    //in case of update is only for read and history reasons
    protected function call_wizard_url($addon=null,$isupdate=false) {
        if (!$addon) return false;
		
		$upgrade_root_path = $this->prpath . '/../../cp/upgrade-app/';	
		$update_root_path = $this->prpath . '/../../cp/update-app/'; 
		
		$r_path = $isupdate ? $update_root_path : $upgrade_root_path;
		
	    $inifile = $r_path . "/cpwizard-".$addon.".ini";
		$target_inifile = $this->prpath . "/cpwizard-".$addon.".ini";
		$installed_inifile = str_replace('.ini','._ni',$target_inifile);
		//echo $inifile;
		
		if ((is_readable($target_inifile)) || ((is_readable($inifile)))){//already copied or fetch from root app

            /*$url = $isupdate ? seturl('t=cpwupdate&wf='.$addon) : 
			                   seturl('t=cpupgrade&wf='.$addon);*/
			//in case of update url is the same...as upgrade
            $url = seturl('t=cpupgrade&wf='.$addon);			
		}
        else
            $url = false; 		
			
        return ($url);		
    }		
	
	protected function cpanel_login(&$cp) {
		$cpanel_user = 'stereobi';
		$cpanel_pass = 'tCW}3aDN}3';//'Yi>~O,h/';	
        //$this->db_prefix = GetParam('dbprefix') ? GetParam('dbprefix') : 'stereobi_';		
		
        $cp = new cpanelx3($cpanel_user , $cpanel_pass);
	}
	
	//RECURSIVE COPY...
    public function copy_r($path, $dest, $verbose=false)  {
        if( is_dir($path) )  {
		
            @mkdir( $dest );
            $objects = scandir($path);
			
            if( sizeof($objects) > 0 )  {
                foreach( $objects as $file ) {
                    if( $file == "." || $file == ".." )
                        continue;
                    // go on
                    if( is_dir( $path.DS.$file ) ) {
                        $this->copy_r( $path.DS.$file, $dest.DS.$file);// , $verbose);//NOT VERBOSE IN SUB DIR FOR FAST..
                    }
                    else {
                        copy( $path.DS.$file, $dest.DS.$file );
						if ($verbose) 
							echo '<br>copy from:'. $path.DS.$file. ' to '. $dest.DS.$file;
                    }
                }
            }
            return true;
        }
        elseif( is_file($path) ) {
		    if ($verbose) 
				echo '<br>copy from:'.$path. ' to '.$dest;
            return copy($path, $dest);
        }
        else {
            return false;
        }
    }		
	
	//RECURSIVE DELETE...
    public function delete_r($path, $dest, $verbose=false)  {
        if( is_dir($path) )  {
		
            //@mkdir( $dest );
            $objects = scandir($path);
			
            if( sizeof($objects) > 0 )  {
                foreach( $objects as $file ) {
                    if( $file == "." || $file == ".." )
                        continue;
                    // go on
                    if( is_dir( $path.DS.$file ) ) {
                        $this->delete_r( $path.DS.$file, $dest.DS.$file);// , $verbose);//NOT VERBOSE IN SUB DIR FOR FAST..
                    }
                    elseif (is_readable($dest)) {
                        @unlink( $dest.DS.$file );
						if ($verbose) 
							echo '<br>delete from:'. $path.DS.$file. ' to '. $dest.DS.$file;
                    }
                }
            }
            return true;
        }
        elseif( is_file($path) ) {//source exist
		    if ($verbose) 
				echo '<br>delete from:'.$path. ' to '.$dest;
			if (is_readable($dest))	
			    $ret = @unlink($dest);
            return $ret;
        }
        else {
            return false;
        }
    }		
	
	//GOOGLE ANALYTICS
	//modify .html files add google analytics..
	//except if google-analytics.html exist, recreate it
    public function put_ua_code_inhtml_dir($uacode=null) {
	    $ua = $uacode ? $uacode : 'UA-XXXXXXXX'; 
	
		$sourcedir = $this->prpath . '/html/';// . $dirname;
        $uascript = "
<script type=\"text/javascript\">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '$ua']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' :
'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(ga, s);
  })();
</script>		
";		

        // if google-analytics.html exist ...
		$gfile = $sourcedir .'/google-analytics.html';
        if (is_readable($gfile)) {
		    //echo 'zzz';
			$fm = @file_put_contents($gfile, $uascript);
			$ret = $fm ? '1 file affected.' : '0 files affected';
			return ($ret);			
        }		
 
	    $fmeter = 0;
		$ret .=  '<hr>'.$sourcedir.':<br>';		
		
	    if (!is_dir($sourcedir)) {
		   $ret .= '<br>Error, invalid sourcedir! '.$sourcedir;
		   return ($ret);//false		 
		}
		  
        $mydir = dir($sourcedir);
        while ($fileread = $mydir->read ()) { 
	
           if (($fileread!='.') && ($fileread!='..') && (!is_dir($sourcedir.'/'.$fileread)) ) { 
			  
			  if ((stristr($fileread,'.htm')) && (substr($fileread,0,2)!='cp'))  {//<<cpfilename restiction
				$fdata = @file_get_contents("$sourcedir/$fileread");
				
				if (stristr($fdata, "_gaq.push(['_setAccount',")) { 
				    //code already injected	
					//replace uacode only....
				    //$ret .= '<br>Replace UA-XXXXXXXX='.$ua;
					
					//find ua code inserted...
					$eua = array();
		            //preg_match_all('/<option value="(.*)">(.*)<\/option>/', $fdata, $eua);
					preg_match('/_gaq.push\(\[\'\_setAccount\', \'(.*)\'\]/', $fdata, $eua);
					//print_r($eua);
		            //echo $eua[1],'..<br>';					
					$existed_ua = $eua[1] ? $eua[1] : 'UA-XXXXXXXX';
					
					$fdata = str_replace($existed_ua,$ua,$fdata);  
				}
                else { //inject script
				    $ret .= '<br>Add Google code';//.$uascript;
					$fdata = str_replace('</head>',$uascript.'</head>',$fdata);				
                }    		
				
		        @file_put_contents("$sourcedir/$fileread", $fdata);
				//$ret .=  ' to:'.$sourcedir.'/'.$fileread.'<br>'; 
				$fmeter+=1; 				
			  }
           }
        }
        $mydir->close ();	
		
		$ret = $fmeter ? $fmeter . ' files affected.' : '0 files affeceted.';
		return ($ret);
	}
	
	//GOOGLE ANALYTICS
	//ganalytics html file
    public function put_gatable_inhtml_file($infile=null, $gatable=null) {
	    if ((!$infile) || (!$gatable))
			return false;
	
	    if (is_readable($this->prpath . '/' . $infile)) {
		   $fdata = @file_get_contents($this->prpath . '/' . $infile);
		   $ndata = str_replace('@GA-TABLE@',$gatable,$fdata);
		   $x = @file_put_contents($this->prpath . '/' . $infile, $ndata);
		   
		   $ret = $x ? true : false;
		   return ($ret);
		}
		
		return false;
	}
	
   //zip directory without recursion
    public function backup_directory_norec($path=null, $name=null, $download=false, $ziprecur=false,$subpath=null) {

        //override to get app dir else root of all apps is selected 
        $app_urlpath = remote_paramload('SHELL','urlpath', $this->prpath);	
        $app_path = $app_urlpath . '/';		
	
        $d = date('Ymd-Hi');
		$zpath = $path ? $path : '';
        $zname = $name ? $d.'-'.$name : $d.'-'.'backup.zip';
		$dirname = $app_path . '/' . $zpath;
		
	    if (is_dir($dirname)) {
		   
          $mydir = dir($dirname);
		  
		  $zip = new ZipArchive();
		  $zfilename = $this->prpath . "/uploads/" . $zname; //to save into
         
		  if ($zip->open($zfilename, ZipArchive::CREATE)!==TRUE) {
              return "cannot open $zfilename";//false;
          }		  

          while ($fileread = $mydir->read ()) {
		   if (($fileread!='.') && ($fileread!='..'))  {
                 //echo "<br>",$fileread;	   
  	             if (!is_dir($fileread))  {   
                    $zip->addFile($dirname."/".$fileread, $fileread); //or basename of full path
                    //$zip->addFromString($fileread,  file_get_contents($dirname."/".$fileread));					
				 }

		   } 
	      }
	      $mydir->close ();
		  
          $ret = "numfiles: " . $zip->numFiles . "<br/>";
          $ret .= "status:" . $zip->status . "<br/>";
		  
		  $zip->close();		  
        }
		
		if ($download==true) {
		    $dn = new DOWNLOADFILE($zfilename);
			$ret = $dn->df_download();
		}
		

		return ($ret);
    }    	
	
    public function backup_directory($path=null, $name=null, $download=false, $ziprecur=false) {
	
        //override to get app dir else root of all apps is selected 
        $app_urlpath = remote_paramload('SHELL','urlpath', $this->prpath);	
        $app_path = $app_urlpath . '/';			
	
	    static $dirname;
		$dirname .= $path .'/'; //goto next dir
		static $zip; 
		$zip = new ZipArchive();
		
        $d = date('Ymd-Hi');
        $zname = $name ? $d.'-'.$name : $d.'-'.'backup.zip';			
		$zfilename = $this->prpath . "/uploads/" . $zname; //to save into
         
		if ($zip->open($zfilename, ZipArchive::CREATE)!==TRUE) {
            die("cannot open $zfilename");//false;
        }			
		
		
        //$ret = null;		
        //echo 'DIRNAME:'.$dirname. '<br/>';
	    if (is_dir($app_path . $dirname)) {
		
		  //if (!$ziprecur)
		    $zip->addEmptyDir(str_replace('../','',$dirname));//clean ..
		   
          $mydir = dir($app_path . $dirname);
		 
			
          while ($fileread = $mydir->read ()) {
		   if (($fileread!='.') && ($fileread!='..'))  {
                 //echo "<br>",$fileread;	   
  	             if (!is_dir($app_path . $dirname."/".$fileread))  {   
					$zip->addFile($app_path . $dirname."/".$fileread, str_replace('../','',$dirname).$fileread); //to sub path
					//echo 'addfile:'.$app_path . $dirname.$fileread.'<br/>';
				 }
				 else //recursion
				    $x = $this->backup_directory($fileread, $name, $download, true);
		   } 
	      }
	      $mydir->close ();
		  //goto prev dir
		  $dirname = str_replace($path .'/','',$dirname);
		  
		  //echo "directory: " . $dirname . "<br/>";
		  if (!$ziprecur) {
			$ret .= "numfiles: " . $zip->numFiles . "<br/>";
			$ret .= "status:" . $zip->status . "<br/>";
		  }
		  
		  if (!$ziprecur)
			$zip->close();		  
        }
		
		if ((!$ziprecur) && ($download==true)) {
		    $dn = new DOWNLOADFILE($zfilename);
			$ret = $dn->df_download();
		}
		
        //echo $ret;
		return ($ret);
    }	
	
    public function backup_dbtable($table=null, $name=null, $download=false) {
		$db = GetGlobal('db');  
		$d = date('Ymd-Hi');
	    $meter = 0;
		$m = 0;
		$zname = $name ? $d.'-'.$name : $d.'-'.'dbasecsv.zip';
		if (!$table) return false;
		
		$con = mysql_connect(remote_paramload('DATABASE','dbhost', $this->prpath), 
		                      remote_paramload('DATABASE','dbuser', $this->prpath),
							  remote_paramload('DATABASE','dbpwd', $this->prpath));
		if ($con) {	

  	      $zip = new ZipArchive();		
		  $zfilename = $this->prpath . "/uploads/" . $zname; //to save into
         
		  if ($zip->open($zfilename, ZipArchive::CREATE)!==TRUE) {
              $download=false;//die("cannot open $zfilename");//false;
			  $ret = 'Zip error!';
          }				
							  
		  mysql_select_db(remote_paramload('DATABASE','dbname', $this->prpath));	
		  //mysql_query("SET NAMES utf8");
		  $charset = remote_paramload('DATABASE','charset', $this->prpath);
		  mysql_query("SET NAMES ".$charset);//'utf8'");
		  
	      if (stristr($table,'|')) //multitable
		    $tables = explode('|',$table);
		  else
			$tables[] = $table; //one table

          foreach ($tables as $t=>$tbl) {	
            $ztablename = $this->prpath . "/uploads/" . $d.'-'.$tbl . '.csv';		
            
			$result = mysql_query("SELECT * FROM " . $tbl);
            if ($result) {	
			
			  $fh = fopen($ztablename, 'w');
			  
			  // Now UTF-8 - Add byte order mark / UTF8 BOM
			  //if (($this->encoding=='utf-8') || ($this->encoding=='utf8'))
				fwrite($fh, pack("CCC",0xef,0xbb,0xbf)); 
			  
			  /* insert field values into data.txt */			
			  while ($row = mysql_fetch_array($result)) {          
				$last = end($row);          
				$num = mysql_num_fields($result) ;    
				for($i = 0; $i < $num; $i++) {
				
				    //NULL VALUES..0 zeros ???
				    //$cell = $row[$i] ? $row[$i] : 'null';
					
					fwrite($fh, $row[$i]);                      
					//fwrite($fh, mb_convert_encoding($row[$i], 'UTF-8', 'GREEK'));
					//fwrite($fh, iconv("UTF-8", "ISO-8859-7", $row[$i]));
					
					if ($row[$i] != $last)
						fwrite($fh, "; "); //,
				}                                                                 
				fwrite($fh, PHP_EOL);//"\n");
			  }
			  @fclose($fh);	
			  
			  $zip->addFile($ztablename, $tbl . '.csv');
				
              $meter+=1;
			}	
	
			/*// MUST BE ADMIN TO EXPORT TEXT
			  $sSQL = "SELECT itmname,itmactive INTO OUTFILE '".$ztablename[$t]."'
  FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
  LINES TERMINATED BY '\\n'
  FROM " . $tbl;
            //echo $sSQL;  
			$result = $db->Execute($sSQL,2);*/
						 
          }
		  $ret .= $meter . ' tables exported.';
		  $zip->close();	
		  
		  if ($download==true) {
		  
		    $dn = new DOWNLOADFILE($zfilename);
			$ret = $dn->df_download();
		  }	  
		}  
        else 
		  $ret = 'No connection';		  
		  
        return ($ret);		
    }
	
	function writeUTF8File($filename,$content) { 
        $f=fopen($filename,"w"); 
        # Now UTF-8 - Add byte order mark 
        fwrite($f, pack("CCC",0xef,0xbb,0xbf)); 
        fwrite($f,$content); 
        fclose($f); 
	} 	
	
	//enable maildbqueue for app
	public function install_maildbqueue($dummy=true, $timekey=null) {
	    $app_path = $this->prpath;
	    $root_app_path = $this->prpath . "/../../cp/";
		$mailappfile = 'mailqueue-apps.ini';
		$app_name = paramload('ID','instancename');//,$this->prpath);
		
		if (is_readable($root_app_path . $mailappfile)) {
		
		    $appqueuelist = @file_get_contents($root_app_path . $mailappfile);
			
		    if (!stristr($appqueuelist, $app_name)) {
			   //backup
			   @copy($root_app_path . $mailappfile, $root_app_path . str_replace('.ini','._ni',$mailappfile));
			   //add app
			   $x = @file_put_contents($root_app_path . $mailappfile, $appqueuelist .','. $app_name);
			   $ret = $x ? "Application ($app_name) installed." : 'Error installing application.';
			}
			else //exist
			   $ret = "Application ($app_name) is already installed.";
		}
		else {//create new
		    $x = @file_put_contents($root_app_path . $mailappfile, $app_name);
			$ret = $x ? "Application ($app_name) installed." : 'Error installing application.';
		}	
		
		//put timekey
		if ($timekey) {
		    $ret .= $this->addkey(true, $timekey);
		}
			
		return ($ret);	
	}
	
	//disable maildbqueue for app
	public function uninstall_maildbqueue($cpinikey=null) {//, $newcpinikey=null) {
	    $app_path = $this->prpath;
	    $root_app_path = $this->prpath . "/../../cp/";
		$mailappfile = 'mailqueue-apps.ini';
		$app_name = paramload('ID','instancename');//,$this->prpath);
		
		if (is_readable($root_app_path . $mailappfile)) {
		
		    $appqueuelist = @file_get_contents($root_app_path . $mailappfile);
			
		    if (stristr($appqueuelist, $app_name)) {
			   //backup
			   @copy($root_app_path . $mailappfile, $root_app_path . str_replace('.ini','._ni',$mailappfile));
			   //del app
			   if (stristr($appqueuelist, $app_name.',')) //is in middle of , separated values
                   $rem_appqueuelist = str_replace($app_name.',','',$appqueuelist);			   
			   elseif (stristr($appqueuelist, ','.$app_name)) //remove last value
				   $rem_appqueuelist = str_replace(','.$app_name,'',$appqueuelist);
			   
			   $x = @file_put_contents($root_app_path . $mailappfile, $rem_appqueuelist);
			   $ret = $x ? "Application ($app_name) uninstalled." : 'Error uninstalling application.';
			}
			else // not exist
			   $ret = "Application ($app_name) is not installed.";
		}
		
		//must remove cp.ini variable
		if ($cpinikey) {
			$myenvfile = $app_path . 'cp.ini';
			if (is_readable($myenvfile)) {
			    
				//if ($newcpinikey) {
					//$newini = str_replace($cpinikey,$newcpinikey,@file_get_contents($myenvfile));
					$newini = str_replace($cpinikey.'=1',$cpinikey.'=0',@file_get_contents($myenvfile));
					//backup
					@copy($myenvfile, str_replace('.ini','._ni',$myenvfile));
					//overwrite
					$x = @file_put_contents($myenvfile, $newini);
					$ret .= $x ? '<br/>ini updated' : '<br/>ini not updated.';
			    //}		
				//$ret = @parse_ini_file($myenvfile ,false, INI_SCANNER_RAW);	

				//if ($save_session)
					//SetSessionParam('env', $ret); 		
			}	
		}
			
		return ($ret);	
	}	
	
	//add-on html and php files ..rename olds
    protected function copy_eshop_files($path, $dest, $rollback=null)  {
        if( is_dir($path) )  {
		
            //@mkdir( $dest );
            $objects = scandir($path);
			
            if( sizeof($objects) > 0 )  {
                foreach( $objects as $file ) {
                    if( $file == "." || $file == ".." )
                        continue;
                    
					//search for ._ files when rollback else . to copy
					$dfile = ($rollback) ? str_replace('.','._', $file) : $file;
					
                    if( file_exists( $dest.DS.$dfile ) ) {
                       
					    if ($rollback) {//xx.htm became _xx.htm, rename xx._htm=>xx.htm
						    //backup
							//@copy( $dest.DS.$dfile, $dest.DS.'_'.$dfile);
							//delete
							@unlink($dest.DS.$file);
							//rollback backup file
							@rename( $dest.DS.$dfile, $dest.DS.$file);						   
						}
						else {//xx.htm became xx._tm, copy new
							//backup file
							@rename( $dest.DS.$file, $dest.DS.str_replace('.','._', $file));
							//copy new
							@copy( $path.DS.$file, $dest.DS.$file);
							$this->modify_tags_inhtml($dest.DS.$file);
						}					
                    }
                    else {
					    if ($rollback) {//delete eshop htm file xx.htm 
						    @unlink($dest.DS.$file);
							//do nothing
						}	
						else {//copy eshop new htm file xx.htm
							@copy( $path.DS.$file, $dest.DS.$file );
							$this->modify_tags_inhtml($dest.DS.$file);
						}	
                    }
                }
            }
            return true;
        }

        return false;
    }	
	
	function modify_awstats_config() {
	
	    $awstats_source_file = '/home/stereobi/tmp/awstats/awstats.' . str_replace('www.','',$this->url) . '.conf';
		$awstats_target_file = $this->prpath.'/cgi-bin/awstats.'.str_replace('www.','',$this->url).'.conf';
	    $awstats_altsource_file = $this->upgrade_root_path.'/awstats/awstats.app.stereobit.gr.conf';
		
	    if (is_readable($awstats_source_file)) {//has generated from awststs
		
			$aw = str_replace('DirIcons="/','DirIcons="../',@file_get_contents($awstats_source_file));	
			$ret = @file_put_contents($awstats_target_file ,$aw);
			return ($ret);
		}
		elseif (is_readable($awstats_altsource_file)) {
		    
			$aw = str_replace('@NAME@',str_replace('www.','',$this->url),@file_get_contents($awstats_altsource_file));	
			$ret = @file_put_contents($awstats_target_file ,$aw);
			return ($ret);		
		}
		else //just empty file
			$ret = @file_put_contents($awstats_target_file ,'0');
			
        return false; //awstats conf file is not exist..yet		
	}	
	
	function modify_ckfinder_config() {
	
	    $ckfinder_conf_file = $this->prpath.'/ckfinder/config.php';
	
	    if (is_readable($ckfinder_conf_file)) {
			$ck_appurl_path = '/';
			//$ckf = $this->modify_config_file("cp/ckfinder/config.php",'@_PATH_@',$ck_appurl_path);	
			//if (!$ckf) 
				//$ret .= "<br>[ckfinder::config.php]Configuration can't saved!";		
			$cf = str_replace('@_PATH_@',$ck_appurl_path,@file_get_contents($ckfinder_conf_file));	
			$ret = @file_put_contents($ckfinder_conf_file ,$cf);
			return ($ret);
		}

        return false; //ckfinder is not installed		
	}
	
	//modify new html files if there are tags
	//replacing ...parent func $this->modify_tags_inhtml_dir();
	protected function modify_tags_inhtml($sourcefile=null){
	    if ((!$sourcefile)||(!is_readable($sourcefile)))
			return false;
		
        //wizard file is disabled so read for ._ni file		
		$mywizfile = $this->prpath . 'cpwizard._ni';
		
		if (is_readable($mywizfile))	
			$wdata = @parse_ini_file($mywizfile ,false, INI_SCANNER_RAW);			
		//print_r($wdata);
		
		if (empty($wdata)) return false;
		
	    $fdata = @file_get_contents($sourcefile);
	
		foreach ($wdata as $name=>$key) {
		  $fdata = str_replace('@'.$name.'@',$key,$fdata);
		  //echo '<br/>',$name,'=',$key;
		}
		
		$ret = @file_put_contents($sourcefile, $fdata);
		return ($ret);
	}	

	//modify .php internal dac code
    protected function modify_php_file($file, $path=null, $rollback=false, $modules=null, $security=null) {
	
	    $mypath = $path ? '/' . $path . '/' : '/';
		$mods = $modules ? explode(',',$modules) : 
		                   array(0=>'private shop.shcart',
						         1=>'private shop.shusers',
								 2=>'private shop.shcustomers',
								 3=>'private shop.shtransactions',
								 4=>'twig.twigengine',
								 );
		//plus ..
		//security CART_DPC 1 0:0:0:0:0:0:0:0;
		//security SHCART_DPC 1 0:0:0:0:0:0:0:0;
		//security TRANSACTIONS_DPC 1 0:0:0:0:0:0:0:0;
		//security SHTRANSACTIONS_DPC 1 0:0:0:0:0:0:0:0;								 
		$secs = $security ? explode(',',$security) : 
							array(0=>'cart',
						         1=>'shcart',
								 2=>'transactions',
								 3=>'shtransactions',
								 );		
		
	    if (is_readable($this->urlpath .$mypath.$file)) {
	        //backup
			@copy($this->urlpath .$mypath.$file,
			      $this->urlpath .$mypath.str_replace('.','._',$file)); 
				  
	        //modify
			$pdata = file_get_contents($this->urlpath.$mypath.$file);
			
			//security
			$sln_on = '1:1:1:1:1:1:1:1'; 
			$sln_off= '0:0:0:0:0:0:0:0';
			foreach ($secs as $i=>$s) {
			  
			  $sline_on = 'security ' . strtoupper($s).'_DPC 1 '. $sln_on;
			  $sline_off= 'security ' . strtoupper($s).'_DPC 1 '. $sln_off;
			  $sline_exist = ($rollback) ? $sline_on : $sline_off;
			  //echo $sline_exist,'----';
			  //echo ($rollback) ? $sline_off : $sline_on;
			  //echo '<br/>';
			  //toggle sec line from on to off and off to on
			  if (stristr($pdata, $sline_exist)) {					
			    if ($rollback) 
					$pdata = str_replace($sline_exist, $sline_off, $pdata);
				else 
					$pdata = str_replace($sline_exist, $sline_on, $pdata);				
			  }	
			}
			
			//dpc modules
			foreach ($mods as $i=>$m) {
			  if (stristr($pdata, $m)) { 
			    if ($rollback)
					//comment mods
					$pdata = str_replace($m, '#'.$m, $pdata);
				else
					//uncomment mods
					$pdata = str_replace('#'.$m, $m, $pdata);
			  }		
			}
			
			//write to file
			$ret = file_put_contents($this->urlpath.$mypath.$file, $pdata);
			
			if ($ret) {//delete backed up file ??
				@unlink($this->urlpath .$mypath.str_replace('.','._',$file));
				return true;
			}
			//return ($ret ? true : false);
	    }
	   
	   return false;
	}
	
	//install eshop features
	public function install_eshop($dummy=true, $timekey=null) {
	
	    //step 1: create backup html dir,cp-config files, .php root files
		//handled by wizard
		
		//step 2: copy files cgi-bin private dpc files,html files, .php root files 
        $source_path = $this->upgrade_root_path . 'eshop/transactions';//$params[0];	
		$target_path = $this->urlpath . '/' . 'cp/transactions';//$params[1];	
		$cfa = $this->copy_r($source_path, $target_path, false);
        $ret .= "directory transactions ";
		$ret .= $cfa ? "created, " : "NOT created, ";

		//copy cgi-bin dir
		//copyrc1=eshop/cgi-bin/shop,/cgi-bin/shop	
        $source_path = $this->upgrade_root_path . 'eshop/cgi-bin/shop';//$params[0];	
		$target_path = $this->urlpath . '/' . 'cgi-bin/shop';//$params[1];	
		$cfb = $this->copy_r($source_path, $target_path, false);
        $ret .= "dpc modules ";
		$ret .= $cfb ? "copied, " : "NOT copied, ";
		
		//js copy
        $source_path = $this->upgrade_root_path . 'eshop/js';//$params[0];	
		$target_path = $this->urlpath . '/' . 'js';//$params[1];	
		$cfb = $this->copy_r($source_path, $target_path, false);
        $ret .= "javascripts ";
		$ret .= $cfb ? "copied, " : "NOT copied, ";		
		
		//copy html dir
		//copyrc1=eshop/transactions,/cp/transactions
        $source_path = $this->upgrade_root_path . 'eshop/html';
		$target_path = $this->urlpath . '/' . 'cp/html';	
		$cfc = $this->copy_eshop_files($source_path, $target_path, false);
        $ret .= "html files ";
		$ret .= $cfc ? "copied, " : "NOT copied, ";				
		
		//step 3: modify myconfig
		$pager = 21;
		$a = GetGlobal('controller')->calldpc_method('rcconfig.setconf use shkatalog.pager+'.$pager);		
		$itemsperline = 3; //was 4 fullscreen, now has left column
		$b = GetGlobal('controller')->calldpc_method('rcconfig.setconf use shkatalog.itemsperline+'.$itemsperline);		
        $oneitemlist = 1; //must already be..
		$c = GetGlobal('controller')->calldpc_method('rcconfig.setconf use shkatalogmedia.oneitemlist+'.$oneitemlist);				
        $resultintable = 3; //was 4 fullscreen, now has left column
		$d = GetGlobal('controller')->calldpc_method('rcconfig.setconf use shkategories.resultintable+'.$resultintable);				
		$ret .= (($a)&&($b)&&($c)&&($d)) ? 
		         "myconfig modified, " : "myconfig NOT modified";
		
		//step 4: save cp.ini
		$mod1 = $this->write_env_file('users',8);//as 8 user
		$mod2 = $this->write_env_file('customers',8);//as 8 user
		$mod3 = $this->write_env_file('subscribers',8);//as 8 user
		$mod4 = $this->write_env_file('transactions',8);//as 8 user
        $ret .= (($mod1)&&($mod2)&&($mod3)&&($mod4)) ?
		        "cp.ini modified, " : "cp.ini NOT modified,";		

		//step 5 : enable #dac modules into cp.php
		$modules2enable = 'shop.rctransactions,shop.shusers,shop.shcustomers';
		$x1 = $this->modify_php_file('cp.php','cp',false,$modules2enable);
        $ret .= ($x1) ? "cp.php modified, " : "cp.php NOT modified,";
				
		//step 6 : enable #dac modules into root php files
		$y1 = $this->modify_php_file('index.php');
		$y2 = $this->modify_php_file('katalog.php');
		$y3 = $this->modify_php_file('contact.php');
		$y4 = $this->modify_php_file('search.php');
		$y5 = $this->modify_php_file('sitemap.php');
		$y6 = $this->modify_php_file('subscribe.php');
        $ret .= (($y1)&&($y2)&&($y3)&&($y4)&&($y5)&&($y6)) ?
		        "root files modified, " : "root files NOT modified,";	

		//step 7,copy-override pages dir
        $source_path = $this->upgrade_root_path . 'eshop/pages';
		$target_path = $this->urlpath;// . '/' . 'cp/html';	
		$cfc = $this->copy_eshop_files($source_path, $target_path, false);
        $ret .= "root files ";
		$ret .= $cfc ? "copied, " : "NOT copied, ";					
		
		//put timekey
		if ($timekey) {
		    $ret .= $this->addkey(true, $timekey);
		}		
        //wizard self write 'eshop' module
		
        return ($ret);		
	}
	
	//rollback eshop
	public function uninstall_eshop() {
	    //step 1: create backup html dir,cp-config files, .php root files
		//handled by wizard
		
		//step 2: copy files cgi-bin private dpc files,html files, .php root files 
        $source_path = $this->upgrade_root_path . 'eshop/transactions';//$params[0];	
		$target_path = $this->urlpath . '/' . 'cp/transactions';//$params[1];	
		$cfa = $this->delete_r($source_path, $target_path, false);
        $ret .= "directory transactions ";
		$ret .= $cfa ? "deleted, " : "NOT deleted, ";

		//js copy
        $source_path = $this->upgrade_root_path . 'eshop/js';//$params[0];	
		$target_path = $this->urlpath . '/' . 'js';//$params[1];	
		$cfb = $this->delete_r($source_path, $target_path, false);
        $ret .= "javascripts ";
		$ret .= $cfb ? "deleted, " : "NOT deleted, ";			
		
		//copy cgi-bin dir
		//copyrc1=eshop/cgi-bin/shop,/cgi-bin/shop	
        $source_path = $this->upgrade_root_path . 'eshop/cgi-bin/shop';
		$target_path = $this->urlpath . '/' . 'cgi-bin/shop';	
		$cfb = $this->delete_r($source_path, $target_path, false);
        $ret .= "dpc modules ";
		$ret .= $cfb ? "deleted, " : "NOT deleted, ";
		
		//copy html dir
		//copyrc1=eshop/transactions,/cp/transactions
        $source_path = $this->upgrade_root_path . 'eshop/html';
		$target_path = $this->urlpath . '/' . 'cp/html';	
		$cfc = $this->copy_eshop_files($source_path, $target_path, true);
        $ret .= "html files ";
		$ret .= $cfc ? "copied, " : "NOT copied, ";			
		
		//step 3: modify myconfig
		$pager = 20; //was 21
		$a = GetGlobal('controller')->calldpc_method('rcconfig.setconf use shkatalog.pager+'.$pager);		
		$itemsperline = 4; //was 3
		$b = GetGlobal('controller')->calldpc_method('rcconfig.setconf use shkatalog.itemsperline+'.$itemsperline);		
        $oneitemlist = 1; //must already be..
		$c = GetGlobal('controller')->calldpc_method('rcconfig.setconf use shkatalogmedia.oneitemlist+'.$oneitemlist);				
        $resultintable = 4; //was 3
		$d = GetGlobal('controller')->calldpc_method('rcconfig.setconf use shkategories.resultintable+'.$resultintable);				
		$ret .= (($a)&&($b)&&($c)&&($d)) ? 
		         "myconfig modified, " : "myconfig NOT modified";
		
		//step 4: save cp.ini
		$mod1 = $this->write_env_file('users',-1);//reset
		$mod2 = $this->write_env_file('customers',-1);//reset
		$mod3 = $this->write_env_file('subscribers',-1);//reset
		$mod4 = $this->write_env_file('transactions',-1);//reset
        $ret .= (($mod1)&&($mod2)&&($mod3)&&($mod4)) ?
		        "cp.ini modified, " : "cp.ini NOT modified,";		

		//step 5 : enable #dac modules into cp.php
		$modules2disable = 'shop.rctransactions,shop.shusers,shop.shcustomers';
		$x1 = $this->modify_php_file('cp.php','cp', true, $modules2disable);
        $ret .= ($x1) ? "cp.php modified, " : "cp.php NOT modified,";
				
		//step 7=pre 6 ....rollback overrided pages dir..delete files ?
        $source_path = $this->upgrade_root_path . 'eshop/pages';
		$target_path = $this->urlpath;// . '/' . 'cp/html';	
		$cfc = $this->copy_eshop_files($source_path, $target_path, true);
        $ret .= "root files ";
		$ret .= $cfc ? "copied, " : "NOT copied, ";						
				
		//step 6 : enable #dac modules into root php files
		$y1 = $this->modify_php_file('index.php', null,true);
		$y2 = $this->modify_php_file('katalog.php', null,true);
		$y3 = $this->modify_php_file('contact.php', null,true);
		$y4 = $this->modify_php_file('search.php', null,true);
		$y5 = $this->modify_php_file('sitemap.php', null,true);
		$y6 = $this->modify_php_file('subscribe.php', null,true);
        $ret .= (($y1)&&($y2)&&($y3)&&($y4)&&($y5)&&($y6)) ?
		        "root files modified, " : "root files NOT modified,";
			
        //wizard self write 'uninstall_eshop' module
        return ($ret);		
	}	
	
	public function genkey($dummy=true,$days=null,$product=null) {
	    $days= ($days ? $days : 1); //demo test
		
	    if ((is_numeric($days)) && ($product)) {
		    $division = $product;
			$param = 'expires';
			
			$exp = time() + ($days * 24 * 60 * 60);
			$edate = date('Y-m-d H:i', $exp);
			$edate_p = explode(' ',$edate);
			$d1 = $edate_p[0];
			$d2 = $edate_p[1];
			
			$gk = $this->appkey->create_key($division, $param, $d1, $d2);
			return ($gk);
		}
		
		return false;
	}
	
	public function addkey($dummy=true, $key=null) {
		if (!$key) return false;
		
		//..check key to extract division.param.value
		$keymap= explode('-',$this->appkey->sp_char($key));
		if ((empty($keymap)) || (count($keymap)!=4)) 
		     return false;
			 
		//standart encoding-decoding (may other methods preloaded)
		$crypt = new AzDGCrypt('1234567890abcdefdgklm#$%^&');			 
			 
		$division = $crypt->decrypt($this->appkey->to_base256($keymap[0]));
		$param = $crypt->decrypt($this->appkey->to_base256($keymap[1]));
		$value = (($keymap[2]) && ($keymap[3])) ?
		          $crypt->decrypt($this->appkey->to_base256($keymap[2])).' '.$crypt->decrypt($this->appkey->to_base256($keymap[3])) :
				  null;
		
		if (($division) && ($param) && ($value)) {		
			
			$value_date = strtotime($value);
			$now = time();
			//$date = "2009-03-04 17:45";		
			//$unix_date = strtotime($date);
			//if ($now > $unix_date) {   		
			if ($now < $value_date) { //date must be in the future
				
				$timeleft = $this->getinstalledkey($division, $param ,true);
				if ($timeleft>0) {
				    //echo 'newkey:';
					//create a new key added with remaing days
					$newdate = ($value_date + $timeleft);
					$edate = date('Y-m-d H:i', $newdate);
					$edate_p = explode(' ',$edate);
					$d1 = $edate_p[0];
					$d2 = $edate_p[1];
			        //create new key
					$key = $this->appkey->create_key($division, $param, $d1, $d2);
					//echo $key; 
				}
				//echo 'timeleft:',$timeleft;
                //else key as is  
				$k = GetGlobal('controller')->calldpc_method('rcconfig.setconf use '.$division.'.'.$param.'+'.$key);				
				$ret .= ($k) ? "Key installed" : "Key is NOT installed";	
				return ($ret);
			}//else past=invalid date key	
		}
		
        return false;		
	}
	
	public function validatekey($dummy=true, $key=null) {
		if (!$key) return false;	

		$keymap = explode('-',$this->appkey->sp_char($key));
		//print_r($keymap);
		
		if ((empty($keymap)) || (count($keymap)!=4)) 
		     return false;	

		//standart encoding-decoding (may other methods preloaded)
		$crypt = new AzDGCrypt('1234567890abcdefdgklm#$%^&');			 
			 	
		$division = $crypt->decrypt($this->appkey->to_base256($keymap[0])); 
		//echo '<br>d:'.$division;
		$param = $crypt->decrypt($this->appkey->to_base256($keymap[1])); 
		//echo '<br>p:'.$param;
		$value = (($keymap[2]) && ($keymap[3])) ?
		          $crypt->decrypt($this->appkey->to_base256($keymap[2])).' '.$crypt->decrypt($this->appkey->to_base256($keymap[3])) :
				  null;
		
		if (($division) && ($param) && ($value)) {
			//echo '<br>v:'.$value;
			$value_date = strtotime($value);
			//echo '<br>v1:'.$value_date;		
		
			$now = time();		
			//echo '<br>now:'.$now;
			if ($now <= $value_date) { //date must be in the future
				//echo 'ok';
				//return ($division.'.'.$param.'.'.$value);
				return true;
			}
		}  
        return false;			
	}
	
	protected function getinstalledkey($division=null, $param=null, $timeleft=false) {
	    //echo 'section:',$division,'.',$param;
	    if (($division) && ($param)) {
		    //read conf
		    //$conf = GetGlobal('controller')->calldpc_method('rcconfig.read_config');
			//print_r($conf);
			//get key
			//$key = GetGlobal('controller')->calldpc_method('rcconfig.getconf use '.$division.'.'.$param);
			//$key = $conf[strtoupper($division)][$param];
			$key = remote_paramload(strtoupper($division), $param, $this->path);
			
            //echo 'installed key:',$key;
			if (!$key) return false;
			
			$keymap = explode('-',$this->appkey->sp_char($key));
			if ((empty($keymap)) || (count($keymap)!=4)) 
				return false;	
				
			//standart encoding-decoding (may other methods preloaded)
			$crypt = new AzDGCrypt('1234567890abcdefdgklm#$%^&');					

			$keydivision = $crypt->decrypt($this->appkey->to_base256($keymap[0])); 
			//echo '<br>d:'.$division;
			$keyparam = $crypt->decrypt($this->appkey->to_base256($keymap[1])); 
			//echo '<br>p:'.$param;
			$value = (($keymap[2]) && ($keymap[3])) ?
					 $crypt->decrypt($this->appkey->to_base256($keymap[2])).' '.$crypt->decrypt($this->appkey->to_base256($keymap[3])) :
				     null;				
			
			if (($keydivision==$division) && ($keyparam=='expires') && ($value)) { 
			    //..is valid
				$value_date = strtotime($value);
				$now = time();
				if ($now <= $value_date) { //date must be in the future
				    $ret = $timeleft ? ($value_date-$now) : true;
					return ($ret);
				}
            }//valid key			
		}	
		
		return false;
	}	
	
	//add space
	public function addspace($add=false, $space_in_mb=null) {
	    $payment = true;//check payment...in session
		$free_space = 200;
	    $spacefile = $this->prpath . '/maxsize.conf.php';
		$spacenow = @file_get_contents($spacefile);		
	
	    $myaddspace = is_numeric($space_in_mb) ? ($space_in_mb*1024*1024) : null;
		
		if (!$myaddspace) 
			return false;
			
		if ($add) { 
		    if (intval($myaddspace+$spacenow)>($free_space*1024*1024)) //cut ...
				$myaddspace = ($free_space*1024*1024) - intval($spacenow);			
		
		    $space = intval($spacenow) + $myaddspace;		   
		}   
		else
		   $space = $myaddspace;
		   
		//check if up to free space	
		if (($space>($free_space*1024*1024)) && ($payment))	    
			$ok = @file_put_contents($spacefile ,$space);
		elseif ($space<=($free_space*1024*1024)) //.. free...
            $ok = @file_put_contents($spacefile ,$space);
        else //no action just 1 byte..send message			
		    $ok = @file_put_contents($spacefile ,$spacenow+1);
			
		return ($ok);
	}
	
	//get the free space
	protected function free_space() {
	    $date = strval(date('Ymd'));
		//read size files of today...must exist!!!!!
		$msize = intval(@file_get_contents($this->prpath . $date . '-msize.size'));
		$tsize = intval(@file_get_contents($this->prpath . $date . '-tsize.size'));
        $dsize = intval(@file_get_contents($this->prpath . $date . '-dsize.size'));	
		$total_size = $tsize + $dsize + $msize;
		//echo "Size total ($tsize + $dsize + $msize):",$total_size;
		//alowed size
		$rtotal = intval(@file_get_contents($this->prpath .'maxsize.conf.php'));
        //echo 'Size allowed:',$rtotal;
		//remaind size
		$remain_size = intval($rtotal - $total_size);
        //echo 'Size remain:',$remain_size;
	    return ($remain_size);		
	}		
	
	//update 
    protected function _show_update_tools() {   
        $text = 'update';
		$u = null;
		//check for time limited services
		if (($codeexpires = $this->get_code_expirations()) && (!empty($codeexpires))) {
		    foreach ($codeexpires as $section=>$exp_text) {
                $module = $section .'_DPC';
				$mod = localize($module, getlocal());
				$update_key_url = $this->call_wizard_url('addkey');//, true);	//is upgrade			
				$u .= "<p><a href='$update_key_url'>" . date("F d Y H:i:s.") . "&nbsp;".$mod."&nbsp;".$exp_text."</a></p>";
			}
		}		
		
		//check for dpc upgrade
		if (($dpc2copy = $this->get_dpc_modules()) && (!empty($dpc2copy))) {
		    foreach ($dpc2copy as $d=>$dpc) {
				//automated dpc update
				$update_dpc_url = $this->call_wizard_url('dpcmod');//, true);	//is upgrade			
				$u .= "<p><a href='$update_dpc_url'>" . date("F d Y H:i:s.") . "&nbsp;".$dpc."</a></p>";
			}	
		}
		
		//check for dac pages to upgrade
		if (($phpdac2copy = $this->get_dac_pages()) && (!empty($phpdac2copy))) {
		    foreach ($phpdac2copy as $p=>$dac) {
				//automated dpc update
				$update_dac_url = $this->call_wizard_url('dacpage');//, true);	//is upgrade			
				$u .= "<p><a href='$update_dac_url'>" . date("F d Y H:i:s.") . "&nbsp;".$dac."</a></p>";
			}	
		}		
		
		//check for free space
		if ($this->free_space() < (100*1024*1024)) { //get more in MB..100MB
		    //automated add space
		    $update_url = $this->call_wizard_url('addspace');//, true);	//is upgrade			
			$u .= "<p><a href='$update_url'>" . date("F d Y H:i:s.") . "&nbsp;".localize('_addspace', getlocal())."</a></p>";
		}		
		
	    $updates = $this->read_update_directory();
		if (!empty($updates)) {
		    arsort($updates);
		    foreach ($updates as $update=>$udatecreated) {
			    //$u .= $udatecreated . '&nbsp;';
				
                $update_url = $this->call_wizard_url($update, true);				
				$u .= "<p><a href='$update_url'>" . date("F d Y H:i:s.", $udatecreated) . "&nbsp;".str_replace('_',' ',strtoupper($update))."</a></p>";
			}	
		}
	   
	    return $u;
    }
	
	//return dpc array for update
	protected function get_dpc_modules() {
	    //read priv dpc dir
		//compare with root dir
		//return array of newer
		$dirname = $this->urlpath . '/cgi-bin/shop/';
		$diffdir = $this->prpath . '/../../cp/upgrade-app/cgi-bin/shop/';
		
		if (is_dir($dirname)) {
			$mydir = dir($dirname);
			while ($fileread = $mydir->read ()) {
				if (($fileread!='.') && ($fileread!='..') && (!is_dir($fileread)))  {
				
				    $sourcetime = @filemtime($dirname.$fileread);
					$targettime = @filemtime($diffdir.$fileread);
					
					if ((is_readable($diffdir.$fileread)) && 
					    ($sourcetime<$targettime)) {
						
						$datemod = date('Y-m-d H:i', $targettime);
						$ret[$fileread] = str_replace('.php','.mod',$fileread) .
						                  '&nbsp;' . localize('_modified',getlocal()) . 
						                  '&nbsp;' . $this->appkey->nicetime($datemod, localize('_ago2',getlocal()));
					}		
				}
			}
            return (!empty($ret) ? $ret : null);			
		}   
		return false;
	}
	
	public function update_dpc_module($dummy=true, $module=null) {
	    if (!$module) return false;
		$dirname = $this->urlpath . '/cgi-bin/shop/';
		$diffdir = $this->prpath . '/../../cp/upgrade-app/cgi-bin/shop/';
		
		//backup and copy
		if (@copy($dirname.$module, $dirname.'_'.$module))
		    $ret = @copy($diffdir.$module,$dirname.$module);
		
		return ($ret);		
	}
	
	//return dac pages array for update
	protected function get_dac_pages() {
	    //read priv dpc dir
		//compare with root dir
		//return array of newer
		$dirname = $this->prpath . '/';
		$diffdir = $this->prpath . '/../../cp/upgrade-app/cp/';
		
		if (is_dir($dirname)) {
			$mydir = dir($dirname);
			while ($fileread = $mydir->read ()) {
				if (($fileread!='.') && ($fileread!='..') && (!is_dir($fileread)))  {
				
				    $sourcetime = @filemtime($dirname.$fileread);
					$targettime = @filemtime($diffdir.$fileread);
					
					if ((is_readable($diffdir.$fileread)) && 
					    ($sourcetime<$targettime)) {
						
						$datemod = date('Y-m-d H:i', $targettime);
						$ret[$fileread] = str_replace('.php','.dac',$fileread) .
						                  '&nbsp;' . localize('_modified',getlocal()) . 
						                  '&nbsp;' . $this->appkey->nicetime($datemod, localize('_ago2',getlocal()));
					}				
				}
			}
            return (!empty($ret) ? $ret : null);  			
		}   
		return false;
	}	
	
	public function update_phpdac_page($dummy=true, $dacpage=null) {
	    if (!$dacpage) return false;
		$dirname = $this->prpath . '/';
		$diffdir = $this->prpath . '/../../cp/upgrade-app/cp/';
		
		//backup and copy
		if (@copy($dirname.$dacpage, $dirname.'_'.$dacpage))
		    $ret = @copy($diffdir.$dacpage,$dirname.$dacpage);
		
		return ($ret);	
	}	
	
	protected function get_code_expirations() {
	    //read myconfig specific expiration codes
		//compare dates
		//return array of expirations
		if (!defined('RCCONFIG_DPC')) return false;
		
		$exps = GetGlobal('controller')->calldpc_method("rcconfig.get_expirations");
		$uexps = unserialize($exps);
		if (!empty($uexps)) {
			foreach ($uexps as $section=>$key) {
			    $date = $this->appkey->decode_key($key, $section);
				//echo $section,'--->',$key,'--->',$date,'--->','2013-12-14 10:36';
				if ($date) {
				    $now = time();
				    $diff = strtotime($date) - $now;
					$daystosay = 30 * 24 * 60 *60; //30 days
					if ($diff<($daystosay)) //x days or negative=expired
						$e[$section] = $this->appkey->nicetime($date); 
				}	
			}
			//print_r($e);
			return ($e);
		}
		
		return false;	
	}

    //read update ini files
    function read_update_directory() {
  
		$dirname = $this->prpath . '/../../cp/update-app/';
  
        //echo $dirname;
	    if (is_dir($dirname)) {
          $mydir = dir($dirname);
		 
          while ($fileread = $mydir->read ()) {
	        
		   if (($fileread!='.') && ($fileread!='..'))  {
		   
                 //echo "<br>",$fileread;	   
	             //read cpwizard- files
  	             if ((stristr ($fileread,".ini")) &&
				     ((substr($fileread,0,9))=='cpwizard-') && //not already updated  	   
					 (!is_readable($this->prpath.'/'.str_replace('.ini','._ni',$fileread)))) { 
                      
					  $p = explode('-',$fileread);
					  $update_name = str_replace('.ini','',$p[1]);
		              $ddir[$update_name] = filectime($dirname . $fileread);						
					}
		   } 
	      }
	      $mydir->close ();
        }

		return ($ddir);
    } 	
	
	//modify conf files to change domain and mail account
	public function change_domainname($dummy=null, $dname=null, $email=null, $password=null) {
	    $db = GetGlobal('db'); 
	    if (!$dname) return false;		
		
		$instance_name = paramload('ID','instancename'); 
		$oldmail = 'info@'.$this->url;//not in session //GetGlobal('UserName') ? GetGlobal('UserName') : null;		
		$ret = $dname . ' '.$email . ' '. $oldmail;
		//return ($ret);
		
		$cp =null;
		$this->cpanel_login($cp);
			
		//add domain to cpanel				
		$queryArgs2 = array(
			'newdomain' => $dname,
			'dir' => $this->urlpath, 
			'subdomain' => str_replace('.','-',$dname)//$instance_name 
			//This function will also create a subdomain
			//..exist so add renamed dname!!!!
			//or leave the same subdomain name..error not critical?
		);		
		//$addsubd = $cp->_cpapi2_exec('SubDomain', 'addsubdomain', $queryArgs2);
		$adddomain = $cp->_cpapi2_exec('AddonDomain', 'addaddondomain', $queryArgs2);
		$reason = $adddomain['reason'];
		$result = $adddomain['result'];
        if (!$result) {
			$log .= '<br/>CP add domain failed:' . $result . ':' . $reason;
            return ($log); //critical ..not a valid domain //remark for test mode !!!!			
		}	
		
		//else continue to config...
		$log .= '<br/>CP add domain:' . $result . ':' . $reason;		
		
		$confini = @file_get_contents($this->prpath . "config.ini");
		//backup conf file
		$c1 = @copy($this->prpath ."config.ini",$this->prpath ."_config-".$this->url.".ini");
		if ($c1) {
		    //email first
			if (($email) && ($oldmail))
                $ret_a = str_replace($oldmail,$email,$confini);			
			else
			    $ret_a = $confini;
				
			$ret_b = str_replace($this->url,$dname,$ret_a);
			
			//add murl array
			$murl = $dname . ',';
			//$log .= '<'.implode(',',$this->murl).'>';
			$murl .= is_array($this->murl) ? implode(',',$this->murl) : $this->murl;
			//writen to myconfig.txt !!!!!!
			//$x = GetGlobal('controller')->calldpc_method('rcconfig.setconf use shell.ip+'.$murl);
			$ret_c = str_replace('ip='.$dname,'ip='.$murl,$ret_b);

			$out1 = @file_put_contents($this->prpath ."config.ini", $ret_c);
			$log .= $out1 ? "<br/>Config modified, ($murl) added." : "<br/>Config failed.";			
		}	
		
        $confmy = @file_get_contents($this->prpath . "myconfig.txt");
        //backup myconf file		
		$c2 = @copy($this->prpath ."myconfig.txt",$this->prpath ."_myconfig-".$this->url.".txt");
		if ($c2) { 
		
		    //email first
			if (($email) && ($oldmail))
                $ret_a = str_replace($oldmail,$email,$confmy);
			else
			    $ret_a = $confmy;
				
			$ret_b = str_replace($this->url,$dname,$ret_a);
			$out2 = @file_put_contents($this->prpath ."myconfig.txt", $ret_b);
			$log .= $out2 ? "<br/>myConfig modified." : "<br/>myConfig failed.";
		
		}	
				
     
	    if (($out2) && ($out1)) {						
			
			$log .= "<br/>Domain name ($dname) installed.";
			
		    if (($email) && ($password)) {
				//insert new admin user 2....for cp
				$mypass =md5($password);
				$insSQL = "INSERT INTO users set code2='2',email='$email',fname='$dname',notes='ACTIVE',seclevid=9,username='$email', password='$mypass',vpass='$mypass'";
				$ret = $db->Execute($insSQL,1);	

				//add mail to cpanel
				$queryArgs1 = array(
					'domain' => $dname,
					'email' => $email,
					'password' => $password,
					'quota' => 150
				);		
				$addmail = $cp->_cpapi2_exec('Email', 'addpop', $queryArgs1);
				
				$reason = $addmail['reason'];
				$result = $addmail['result'];
				
                if (!$result) 
					$log .= '<br/>Add pop account failed:' . $result . ':' . $reason;
		        else				
					$log .= '<br/>Add pop account:' . $result . ':' . $reason;
							
				//add dir htaccess user/pass..
				//LAST beware instant re-login	
				//$ht = new htaccess($this->prpath. '.htaccess', '../../cp/htpasswd-'.$instance_name);
				//new file with -extension name...
				$ht = new htaccess($this->prpath. '.htaccess', '../../cp/htpasswd-'.str_replace('.','-',$dname));
				$ht->addUser($email, $password);					
					
				$log .= "<br/>CP user installed.";				
			}			
			
			return ($log);
		}
		
		$log .= "<br/>Error installing domain name ($dname)."; 
		return ($log);
	}
	
	//add user to users table
	public function change_admin_user($dummy=null, $email=null, $password=null) {
	    $log = null;
	    $db = GetGlobal('db'); 
	    if (!$password) {
			$log =  '<br>User pass failed.';
			return ($log);			
		}
        //else
        $mypass = md5($password); 		
		
		if ($email) {
			//insert new admin user 2....for cp
			$insSQL = "INSERT INTO users set code2='2',email='$email',fname='$dname',notes='ACTIVE',seclevid=9,username='$email', password='$mypass',vpass='$mypass'";
			$ret = $db->Execute($insSQL,1);	
			$log =  '<br>User added.';
		}
        else		
			$log =  '<br>User add failed.';
			
		return ($log);
	}	

	//disabled
	protected function install_siwapp() {
	    //return true;//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
		
		$symfony_path = $this->prpath . '/symfony';	
		$siwapp_path = $this->urlpath . '/siwapp';	
	
		//copy files...
		//copy symfony framework into cp directory
		$this->copy_r($this->upgrade_root_path . 'cp/symfony', $symfony_path, true);	
		//copy siwapp files into app root (can't be into cp rewrite engine error!!)
		$this->copy_r($this->upgrade_root_path . 'siwapp', $siwapp_path, true);	
		
		//chmode folders
		//... no need
		
		//override config.php..NO NEED..FIXED INSIDE FILE..?????
		/*$new_conf_contents = "
<?php
\$sw_installed = false;
\$options['sf_web_dir'] = dirname(__FILE__);
\$options['sf_root_dir'] = realpath('$symfony_path');		
";
        //save a copy
        @rename($siwapp_path . '/config.php',$siwapp_path.'/_config.php');
        //override		
        @file_put_contents($siwapp_path.'/config.php', $new_conf_contents);  
        */
		/*
        //override index.php NO NEED..FIXED INSIDE FILE..
        $find = "'siwapp', 'prod', false";
		$replace = "'siwapp', 'prod', true";
        $new_index_contents = str_replace($find, $replace, file_get_contents($siwapp_path . '/index.php'));				
        //save a copy
        @rename($siwapp_path . '/index.php',$siwapp_path.'/_index.php');
        //override		
        @file_put_contents($siwapp_path.'/index.php', $new_index_contents); 		
		*/
		//create db for siwapp
		$cpanel_user = 'stereobi';
		$cpanel_pass = 'tCW}3aDN}3';//'Yi>~O,h/';	
        //$this->db_prefix = GetParam('dbprefix') ? GetParam('dbprefix') : 'stereobi_';		
		
        $cp = new cpanelx3($cpanel_user , $cpanel_pass);

		if ($cp) {
		    //read local config
			$mydbname = paramload('DATABASE','dbname') . 'siwapp'; //create db as exiting name plus siwapp word
		    $mydbuser = paramload('DATABASE','dbuser');
			$mydbpass = paramload('DATABASE','dbpwd');
		
			$db_error = $cp->_cpapi1_exec('Mysql', 'adddb', array($mydbname));	
            $this_log .= 'Add Database (' . $mydbname . '):' . $db_error.'<br/>';
            if ($db_error) {
			    $this->log = $db_error;
				return false;
            }			
			
			//user exist...
			/*$dbuser_error = $cp->_cpapi1_exec('Mysql', 'adduser', array($mydbuser, $mydbpass));
			$this_log .= 'Add Database user (' . $mydbuser . '):' . $dbuser_error.'<br/>';
            if ($dbuser_error) {
			    $this->log = $dbuser_error;
				return false;
            }*/				
			
			$adduser_error = $cp->_cpapi1_exec('Mysql', 'adduserdb', array($mydbname, $mydbuser,'all'));//select create delete insert update alter drop'));
			$this_log .= 'Add Database user (' . $mydbuser . ') to DB:' . $adduser_error.'<br/>';
            if ($adduser_error) {
			    $this->log = $adduser_error;
				return false;
            }				
			
			$addadmin_error = $cp->_cpapi1_exec('Mysql', 'adduserdb', array($mydbname,'admin','all'));
			$this_log .= 'Add Database Admin user (admin) to DB:' . $addadmin_error.'<br/>';	
            if ($addadmin_error) {
			    $this->log = $addadmin_error;
				return false;
            }			
		}
		else
		    $this->log .= "Can't access cp!";
		
		return true;
	}	
  
};
}
?>
<?php
$__DPCSEC['RCWIZARD_DPC']='1;1;1;1;1;1;2;2;9';

if ( (!defined("RCWIZARD_DPC")) && (seclevel('RCWIZARD_DPC',decode(GetSessionParam('UserSecID')))) ) {
define("RCWIZARD_DPC",true);

$__DPC['RCWIZARD_DPC'] = 'rcwizard';

$__EVENTS['RCWIZARD_DPC'][0]='cpmwiz';
$__EVENTS['RCWIZARD_DPC'][1]='cpwizard';
$__EVENTS['RCWIZARD_DPC'][2]='cpwiznext';
$__EVENTS['RCWIZARD_DPC'][3]='cpwizprev';
$__EVENTS['RCWIZARD_DPC'][4]='cpwizskip';
$__EVENTS['RCWIZARD_DPC'][5]='cpwizexit';
$__EVENTS['RCWIZARD_DPC'][6]='cpwizsave';
$__EVENTS['RCWIZARD_DPC'][7]='cpwizlogin';
$__EVENTS['RCWIZARD_DPC'][8]='cpwizreinit';
//$__EVENTS['RCWIZARD_DPC'][9]='cpupgrade';

$__ACTIONS['RCWIZARD_DPC'][0]='cpmwiz';
$__ACTIONS['RCWIZARD_DPC'][1]='cpwizard';
$__ACTIONS['RCWIZARD_DPC'][2]='cpwiznext';
$__ACTIONS['RCWIZARD_DPC'][3]='cpwizprev';
$__ACTIONS['RCWIZARD_DPC'][4]='cpwizskip';
$__ACTIONS['RCWIZARD_DPC'][5]='cpwizexit';
$__ACTIONS['RCWIZARD_DPC'][6]='cpwizsave';
$__ACTIONS['RCWIZARD_DPC'][7]='cpwizlogin';
$__ACTIONS['RCWIZARD_DPC'][8]='cpwizreinit';
//$__ACTIONS['RCWIZARD_DPC'][9]='cpupgrade';

$__LOCALE['RCWIZARD_DPC'][0]='RCWIZARD_DPC;Wizard;Wizard';

class rcwizard {
  
    var $title, $prpath, $urlpath, $url, $encoding;
	var $post, $msg;
	var $browser;
	var $editmode;
	var $wdata, $wstep, $weditfiles, $environment, $wizardfile;

	function __construct() {
		$this->prpath = paramload('SHELL','prpath'); //cp path of root app
		$this->urlpath = paramload('SHELL','urlpath'); //root path of root app
		$this->infolder = paramload('ID','hostinpath'); //must be null	
		$this->url = paramload('SHELL','urlbase'); //root domain name 	
	
		$char_set  = arrayload('SHELL','char_set');	  
		$charset  = paramload('SHELL','charset');	  		
		if (($charset=='utf-8') || ($charset=='utf8'))
			$encoding = 'utf-8';
		else  
			$encoding = $char_set[getlocal()]; 	
	
		$this->encoding = $_GET['encoding'] ? $_GET['encoding'] : $encoding; 
		$this->editmode = GetReq('editmode');	  
		$this->title = localize('RCWIZARD_DPC',getlocal());	
		$this->post = false;  
		$this->msg = null;
		
		$this->install_root_path = $this->prpath . '../../cp/init-app/';
		
		//standart wizard file ...
		$this->wizardfile = 'cpwizard.ini';
		
        $this->environment = $_SESSION['env'] ? $_SESSION['env'] : $this->read_env_file(true);//session or read..; 				
        $this->wdata = $_SESSION['wdata'] ? $_SESSION['wdata'] : $this->read_wizard_file(true);//session or read..; 		
	    $this->wstep = $_SESSION['wstep'] ? $_SESSION['wstep'] : 0;
		$this->weditfiles = $_SESSION['weditfiles'] ? $_SESSION['weditfiles'] : null;//has init initialized into edit...$this->read_html_files(true);	
	}
	
	function event($event=null) {
	
	   /////////////////////////////////////////////////////////////
	   /*if (GetSessionParam('LOGIN')!='yes') {//die("Not logged in!");//	
	     if (!GetReq('editmode'))		 
	       die("Not logged in!");//	
		 else
     	   //header("Location: cp.php?editmode=1&encoding=" . GetReq('encoding'));  
   	       die("Not logged in! <A href='../cp.php?editmode=1&encoding=".GetReq('encoding')."'>LOGIN</A>");//
	   }*/		
	   /////////////////////////////////////////////////////////////   
	
	    switch ($event) {
		
          //case 'cpupgrade'  : break;		
		  case 'cpwizreinit' : $this->reinit_wizard();
                                break;		  
		
		  case 'cpwizlogin' :if ($ok = $this->login_wizard_step()) { 
		                        //goto dashboard after login... 
		                        $this->javascript_goto('cp.php?editmode=1&encoding='.$this->encoding);
		                        $this->msg = null;//true;
							 }	
							 else	
		                        $this->msg = "Invalid username or password."; 
		                     break;		
		
		  case 'cpwizsave' : //change html tags
		                     //$this->msg = $this->change_html_tags();	
							 $this->msg = $this->modify_tags_inhtml_dir();
							 //save wiz file ..
							 $ok = $this->write_wizard_file(false);//true //..to enable/disable wiz next time..
							 
							 if ($ok=true) {
							    session_destroy();//kill session
								//..can be redirected...
								//header("Location:".$this->url);
								//..exit to self win...!!!
							 }
                             break;		  

		  case 'cpwiznext' : $this->inc_wizard_step(); break;
		  case 'cpwizprev' : $this->dec_wizard_step(); break;
		  case 'cpwizskip' : $this->inc_wizard_step(); break;
		  
		  case 'cpwizard'  :	
          case 'cpmwiz'    :
		  default          :
		                     
        }			
    }
	
	function action($action=null) {	
	    //echo $this->wstep;	
	
	    switch ($action) {	
          /*case 'cpupgrade'  :  $out = 'upgrade..'; 
		                       $out.= seturl('t=cpwizard&wf=siwapp', 'Siwapp');
		                       break;*/	

		  case 'cpwizreinit' :  $out .= 'Please exit and re-enter the control panel!';
		                        //$out .= $this->wizard_step();//..if already in session done, goto finish
                                break;	
		
		  case 'cpwizlogin':   $out .= $this->wizard_step(); //current step reload... 
		                       break;
		
		  case 'cpwizsave' :   //if ($this->msg) //some kind of error
		                       $out .= $this->completed_step($this->msg);
		                       break; 
		  case 'cpwiznext' :
		  case 'cpwizprev' :
		  case 'cpwizskip' :
		  
		  case 'cpwizard'  :	
          case 'cpmwiz'    :
		  default          :   //print_r($this->wdata);//$this->read_wizard_file());
		                       $out .= $this->wizard_step();//'wizard!';
		 						  
        }
		
        return ($out);
		/*$win2 = new window($title,$out);
		$winout .= $win2->render("center::100%::0::group_dir_headtitle::left::0::0::");
		unset ($win2);
		return ($winout);*/
    }
	
    protected function javascript_goto($goto) {
   
      if (iniload('JAVASCRIPT')) {

	       $code = "parent.mainFrame.location=\"$goto\"";
		   $js = new jscript;
           $js->load_js($code,"",1);			   
		   unset ($js);
	  }   
    } 	

    //..as in cpmdbrec.php	
	protected function login_wizard_step() {

	     if (($user = $_POST['cpuser']) && ($pass = $_POST['cppass'])) {
	
			if (defined('RCCONTROLPANEL_DPC'))
				$login = GetGlobal('controller')->calldpc_method("rccontrolpanel.verify_login");
			elseif (defined('SHLOGIN_DPC'))
				$login = GetGlobal('controller')->calldpc_method("shlogin.do_login use ".$user.'+'.$pass.'+1');	
			else
				//die('Login mechnanism not specified!');	
				return false;
			
            return ($login);			
		}
		
		return false;
	}	
	
	protected function inc_wizard_step() {
	    //echo $this->wstep;
	    if ($this->wstep < count($this->wdata)) {
			$this->wstep+=1;
			SetSessionParam('wstep', $this->wstep);
			
			if ($_POST) {
				$step_var_name = null;
				$step_post_value = $this->get_step_post($step_var_name);
				//echo $step_var_name;
				//save post value into mem wiz array
				if (($step_var_name) && (array_key_exists($step_var_name, $this->wdata))){
                    //echo ':true';
                    $this->write_wizard_element($step_var_name, $step_post_value, true);
				}
			}
		}
	}
	
	protected function dec_wizard_step() {
	
	    if ($this->wstep>0) {
			$this->wstep-=1;
			SetSessionParam('wstep', $this->wstep);
		}
	}	
	
	protected function wizard_step() {
	   
	    //..change posted app (old array)
		/*$domain = 'http://' . $this->posted_appname . '.' . $this->rootdomain ;
		$email = $this->posted_mail ? $this->posted_mail : $this->posted_appname.'@'.$this->rootdomain ;
		$title = $this->posted_appname;			
		
		$data = array('DOMAIN-NAME'=>$domain,'E-MAIL'=>$email,
		              'TITLE'=>$title,'SUBTITLE'=>'#your-subtitle',
		              'META-DESCRIPTION'=>'#your-description','META-KEYWORDS'=>'#your-keywords',
					  'FEEDBURNER'=>'#feedburner-url','TWITTER'=>'#tweeter-url','FACEBOOK'=>'#facebook-url','GOOGLEPLUS'=>'#googleplus-url',
					  'FLICKR'=>'#flickr-url','VIMEO'=>'#viemo-url','LINKEDIN'=>'#linkedin-url','DELICIOUS'=>'#delicious-url',
					  'FBLIKEBOX-PLUGIN'=>'#fb-plugin','FLICKRBADGE-PLUGIN'=>'#flickr-plugin');	
		
				
		//$ok = $this->change_data('cp/html', $data);	   
		*/
		
		//..change domain ? (www.name.gr can be bind here...)..or skip 
		//...change mail ? (mailform can be changed here ?...)..or skip
		//..change logo (search for logo.png in images folder?)..or skip.....<<<<<<<<
		//..change title subttile ..or skip
		//..change/add meta keyword if not exist as html tag...)..or skip
		//..change/add meta description if not exist as html tag...)..or skip
		//add social media..one by one..explaining and add... or skip...
		
		//....
		
		//html file by html file...edit ....
		//......
		
		//image upload....
		//.....
		
		//cp commands win explain step by step...
		//.....dashboard....
		
		//echo 'current step:'.$this->wstep;
		$step_index = 0; //0 is welcome screen
		//print_r($this->wdata);
		foreach ($this->wdata as $stepname=>$stepaction) {
			//echo $stepname,':',$stepaction,'>';
  
			if (($stepaction) && ($step_index==$this->wstep)) {//if step val exist
			    //echo $stepname,':',$stepaction,'>';
				$ret = $this->render_step($stepname, $stepaction);
				if ($ret) 
				    return ($ret); //else continue....other steps...
				//else
				    //$this->wstep += 1;//virt inc..
                    //$step_index += 1;				
			}
			else
			    $step_index += 1;
		}
		
		//finilizing wizard exist screen...
		$ret = $this->final_step();
		return ($ret);
	}
	
	//render wizard step
	protected function render_step($name=null, $value=null) {
		if (!$name) return ('no named step!');
		
		switch ($name) {
		    case 'dashboard' : //explain dashboard..if permited
			                  $myvalue = $this->environment['DASHBOARD'] ? $value : false;
			                  if ($myvalue) 
			                     $ret = $this->dashboard_step();
							  else
                                 $ret = false; //..continue steps 							  
			                  break;		
		
		    case 'awstats' : //explain awstats..if permited
			                  $myvalue = $this->environment['AWSTATS'] ? $value : false;
			                  if ($myvalue) 
			                     $ret = $this->awstats_step();
							  else
                                 $ret = false; //..continue steps 							  
			                  break;			
		
		    case 'upload'  : //upload files/pics..if permited
			                  $myvalue = $this->environment['CKFINDER'] ? $value : false;
			                  if ($myvalue) 
			                     $ret = $this->upload_step();
							  else
                                 $ret = false; //..continue steps 							  
			                  break;		
		
		    case 'edithtml' : //edit html files..if permited
			                  $myvalue = $this->environment['EDITHTML'] ? $value : false;
			                  if ($myvalue) 
			                     $ret = $this->edit_step();
							  else
                                 $ret = false; //..continue steps 							  
			                  break;
		
		    case 'wizard':	 //always step 0
							if ($value) //welcome screen
								$ret = $this->welcome_step();	  
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
	
	//dashboard step
	protected function dashboard_step() {	
	    $site_url = $this->url;
		$sitecp_url = 'cp.php?editmode=1';
		$message = $this->msg ? $this->msg .'<br/>' : null;
	
	    if (GetSessionParam('LOGIN')!='yes') {
		    
            /*$rightframe_url	= "cpmdbrec.php";//?turl=" . urlencode(base64_encode($turl)) . '&encoding='.$this->encoding;// . '&htmlfile=' . $htmlfile;				
            $onclick = null;//"dhtmlwindow.open(\"rightframe\", \"iframe\", \"$rightframe_url\", \"Menu\", \"width=250px,height=450px,resize=1,scrolling=1,center=1\", \"recal\")";//"top.location.href='" . $this->url . "'";		
			$link = "<a href='$site_url' target='mainFrame' onClick='$onclick'>".localize('_login',$lan)."</a>";
			$message = '<br/>Press here to login:' . $link;	*/		
			
			//login form
			$username = $this->read_wizard_element('E-MAIL');
			$ret =  <<<FORMEOF
							<form name="wizdefstep" method="post" class="sign-up-form" action="">
								<input type="hidden" name="FormAction" value="cpwizlogin" />
                                <h5>$message</h5> 								
                                <h3 class="grid_3 alpha omega"><strong>Username</strong></h3>
					            <input class="grid_4 alpha omega" name="cpuser" type="text" id="username" value="$username"></input>
					            <h3 class="grid_3 alpha omega"><strong>Password</strong></h3>
					            <input class="grid_4 alpha omega" name="cppass" type="password" id="password" value=""></input>
									<div class="msg grid_4 alpha omega aligncenterparent"></div>      
									<div class="grid_4 alpha omega aligncenterparent">
									<br/>
									<input type="submit" class="call-out grid_2 push_2 alpha omega" alt="Save"	title="Next" name="Submit" value="Next">
									</input>
								    </div>
							</form>
<script type="text/javascript">
	$(document).ready(function() {
		$("#username").focus();
	});
</script>							
FORMEOF;
		}
	    else {
			$link = "<a href='$sitecp_url' target='mainFrame'>".localize('_dashboard',$lan)."</a>";
			$message = '<br/>Press here to login:' . $link;			
		
	
			$ret = '<form name="wizdashboardstep" method="post" class="sign-up-form" action="">
								<input type="hidden" name="FormAction" value="cpwiznext" />  
                                    '.$message.' 								
									<div class="grid_4 alpha omega aligncenterparent">
									<br/>
									<input type="submit" class="call-out grid_2 push_2 alpha omega" alt="Save" title="Next"	name="Submit" value="Next">
									</input>
								    </div>
							</form>';	
		}					
				
		return ($ret);				
	}		
	
	//awstats step
	protected function awstats_step() {	
		$link = "<a href='cgi-bin/awstats.php' target='mainFrame'>".localize('_webstatistics',$lan)."</a>";
		$message = '<br/>Press here to view stats:' . $link;	
	
		$ret = '<form name="wizawstatsstep" method="post" class="sign-up-form" action="">
								<input type="hidden" name="FormAction" value="cpwiznext" />  
                                    '.$message.' 								
									<div class="grid_4 alpha omega aligncenterparent">
									<br/>
									<input type="submit" class="call-out grid_2 push_2 alpha omega" alt="Save" title="Next"	name="Submit" value="Next">
									</input>
								    </div>
							</form>';	
				
		return ($ret);				
	}	
	
	//upload step
	protected function upload_step() {	
		$link = "<a href='cpmckfinder.php' target='mainFrame'>".localize('_ckfinder',$lan)."</a>";
		$message = '<br/>Press here to upload files:' . $link;	
	
		$ret = '<form name="wizuploadstep" method="post" class="sign-up-form" action="">
								<input type="hidden" name="FormAction" value="cpwiznext" />  
                                    '.$message.' 								
									<div class="grid_4 alpha omega aligncenterparent">
									<br/>
									<input type="submit" class="call-out grid_2 push_2 alpha omega" alt="Save" title="Next"	name="Submit" value="Next">
									</input>
								    </div>
							</form>';	
				
		return ($ret);				
	}
	
	//edit html step
	protected function edit_step() {	
		$message = null;
        $encoding = $this->encoding;		
		
	    //initialize.. read files..retutrn count of files...
        $hfiles = $_SESSION['weditfiles'] ? count($_SESSION['weditfiles']) : $this->read_html_files(true);
		
		$ret = 'edit:';
		
	    if ($hfiles) {	
		  foreach ($this->weditfiles as $i=>$file) {

            $plainfile	= str_replace('.html','', $file);	
		    $phpfile = str_replace('.html','.php', $file);
		    $htmlfile = urlencode(base64_encode($file));
		
		    $htmleditlink = "<a href='cpmhtmleditor.php?cke4=1&encoding=$encoding&editmode=1&htmlfile=$htmlfile' target='mainFrame'>".
			                $plainfile .
							"</a>"; 
		   	$message .= '<br/>edit:' . $htmleditlink;
		  }
		
		  $ret .= '<form name="wizeditstep" method="post" class="sign-up-form" action="">
								<input type="hidden" name="FormAction" value="cpwiznext" />  
                                    '.$message.' 								
									<div class="grid_4 alpha omega aligncenterparent">
									<br/>
									<input type="submit" class="call-out grid_2 push_2 alpha omega" alt="Save" title="Next"	name="Submit" value="Next">
									</input>
								    </div>
							</form>';
        }
        else
          $ret .= 'no files to edit...';		
		  
		return ($ret);
    }	
	
	//default step read array value and change it
	protected function default_step($name=null, $value=null) {
		$message = null;
		$loname = strtolower($name);
		$cmd = 'cpwiznext'; //goto next step..check post
		
		//..id stepfield to focus (jquery) when next step....
		$input_field = '<h3 class="grid_3 alpha omega"><strong>'.$loname.'</strong></h3>'.
					   '<input class="grid_4 alpha omega" name="'.$name.'" type="text" id="stepfield" value="'.$value.'"></input>';		
		
		$form = <<<EOF
							<form name="wizdefstep" method="post" class="sign-up-form" action="">
								<input type="hidden" name="FormAction" value="$cmd" /> 
								    $input_field
                                    $message 
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
		
		return $form;
	    //$ret = $name.'='.$value;	
	    //$ret = 'default step:';		
		//return ($ret);		
	}
	
	//render wizard final step
	protected function final_step() {
	    //..collect data adn save.... 
		/*
		$domain = $this->read_wizard_element('DOMAIN-NAME');//  $this->url;
		$email = $this->read_wizard_element('E-MAIL');//paramload from SMTPMAIL,user..
		$title = $this->read_wizard_element('TITLE');			
		$title = $this->read_wizard_element('SUBTITLE');
		//basic array
		$data = array('DOMAIN-NAME'=>$domain,'E-MAIL'=>$email,
		              'TITLE'=>$title,'SUBTITLE'=>$subtitle);
		//extended (optional array)			  
		$sdata = array(			  
		              'META-DESCRIPTION'=>'#your-description','META-KEYWORDS'=>'#your-keywords',
					  'FEEDBURNER'=>'#feedburner-url','TWITTER'=>'#tweeter-url','FACEBOOK'=>'#facebook-url','GOOGLEPLUS'=>'#googleplus-url',
					  'FLICKR'=>'#flickr-url','VIMEO'=>'#viemo-url','LINKEDIN'=>'#linkedin-url','DELICIOUS'=>'#delicious-url',
					  'FBLIKEBOX-PLUGIN'=>'#fb-plugin','FLICKRBADGE-PLUGIN'=>'#flickr-plugin');	
			
		//$ok = $this->change_data('cp/html', $data);		
	    */
		
		/*
	    $ret = 'Final step:<br>';
		$ret .= implode('<br>',$this->wdata);	
        */
		//form... when submit cpwizsave... 
		$ret .= '<form name="wizlaststep" method="post" class="sign-up-form" action="">
								<input type="hidden" name="FormAction" value="cpwizsave" />     
									<div class="grid_4 alpha omega aligncenterparent">
										<br/>
										<input type="submit" class="call-out grid_2 push_2 alpha omega" alt="Save"
											title="Finish"
											name="Submit" value="Finish">
										</input>
								    </div>
							</form>';			
		
		return ($ret);
	}	

	//render wizard welcome step
	protected function welcome_step() {	
	
	    //$ret = 'Welcome step:';
		
		$ret .= '<form name="wizlaststep" method="post" class="sign-up-form" action="">
								<input type="hidden" name="FormAction" value="cpwiznext" />     
									<div class="grid_4 alpha omega aligncenterparent">
										<br/>
										<input type="submit" class="call-out grid_2 push_2 alpha omega" alt="Save"
											title="Start"
											name="Submit" value="Start">
										</input>
								    </div>
							</form>';	
							
		return ($ret);	
	}
	
	//render wizard completed step
	protected function completed_step($msg=null) { 
	    $message = $msg ? $msg : null;
        $onclick = "top.location.href='" . $this->url . "'";
	    $jlink =  "<a href=\"#\" onClick=\"$onclick\">" . localize('_exit',$lan) . "</a>";	
	
	    //$ret = 'Completed step:' . $msg;
		//$ret .= $jlink;
		
		$ret = $message;
		$ret .= '<form name="wizlaststep" method="post" class="sign-up-form" action="">  
									<div class="grid_4 alpha omega aligncenterparent">
										<br/>
										<input type="submit" class="call-out grid_2 push_2 alpha omega" alt="Save"
											title="exit"
											name="Submit" value="Exit" onClick="'.$onclick.'">
										</input>
								    </div>
							</form>';			
		
		return ($ret);	
	}
	
	//read the post and return the data enetered as ret, 
	//array field-name as step-var when one element else ret= array
    protected function get_step_post(&$step_var) {
	    if (empty($_POST)) return false;
		
		//extract submit and FormAction
		array_shift($_POST);//FormAction
		array_pop($_POST); //Submit
		
		if (count($_POST)>1) {//if multiple values
			$ret = (array) $_POST; //return array
			//print_r($_POST);
		}	
		else {//return last element
		    $step_var = key($_POST); //save field name..
		    $ret = array_pop($_POST);
			//echo $ret;
		}	
        		
		return ($ret);	
    }

	public function get_step_title($textpath=null) {
	    $mypath = $textpath ? $textpath : $this->install_root_path;	
	    $default_ret = 'Welcome';		
		$step_index = $this->wstep ? $this->wstep : '0';
		$step_file = str_replace('.ini','.text'.$step_index, $this->wizardfile);
		
		//fetch step title =first line
		$ret = (is_readable($mypath . $step_file)) 
		     ? array_shift(@file($mypath . $step_file)) 
			 : $default_ret;		
		
		return ($ret);	
    }
	
	public function get_step_text($textpath=null) {
	    $mypath = $textpath ? $textpath : $this->install_root_path;
	
	    $default_ret = "<h5>Welcome</h5>
				<br/>
				<p>
				</p><br /> <br />";
				
		$step_index = $this->wstep ? $this->wstep : '0';
		$step_file = str_replace('.ini','.text'.$step_index, $this->wizardfile);
		
		//echo $mypath . $step_file;
		
		//fetch step text...
		if (is_readable($mypath . $step_file)) {
		
		    //$ret = file_get_contents($mypath . $step_file) ;
			$text = @file($mypath . $step_file);
			$header = "<h5>" . array_shift($text) . "</h5>";
			$body = "<p>" . implode('<br/>',$text). "</p><br/><br/>";
			$ret = $header . $body;
		}
		else
		    $ret = $default_ret;	
	
				
		return ($ret);
	}	
	
	//read a prev input arg from memory and return the arg asked, 	
    public function get_wizard_arg($arg=null) {
	    if (!$arg) return false;
		
		//print_r($_POST); //no post...
		//echo $arg,':',$_POST[$arg];
		//$ret = $_POST[$arg];
		
		$ret = $this->read_wizard_element($arg);
		//echo $ret;
	    return ($ret);
    }		

	//read wiz file
	protected function read_wizard_file($save_session=false) {
	
		$mywizfile = $this->prpath . $this->wizardfile;
		
		if (is_readable($mywizfile)) {	
			$ret = @parse_ini_file($mywizfile ,false, INI_SCANNER_RAW);	
			//echo 'read wfile:';
			//if (!empty($ret) //to not re-read at constuct..
			if ($save_session)
				SetSessionParam('wdata', $ret); 
		}
		//else
		  //echo "not a w file!($mywizfile)";	
		return ($ret);
    }	
	
	//write wiz file, DISABLE WIZARD IF TRUE..
	protected function write_wizard_file($status=false) {
	    $ok = false;
		$mywizfile = $this->prpath . $this->wizardfile;	   
		
		if (is_readable($mywizfile)) {			
			$onoff = $status ? 1 : 0;		
		
			//final set of wiz status
			$this->write_wizard_element('wizard',$onoff);
			//save wizard file			
			foreach ($this->wdata as $var=>$val) {
				$myiniwizard .= $var .'='.$val ."\r\n";
			} 
			$ok = @file_put_contents($mywizfile ,$myiniwizard);		
		
			//rename file to not re-show when 2nd time in cp
			if ($status==false) {
				$ok = @rename($mywizfile, str_replace('.ini','._ni',$mywizfile));
			}
		
		}
		//else
		  //echo "not a w file!($mywizfile)";		

        return ($ok);	
    }	
	
	
	//modify wiz array in memory
	protected function write_wizard_element($var=null, $val=null, $saveit=false) {
	    if (!$var) return false;
	    if (!$val) $val = '0';
		
		$this->wdata[$var] = $val;
		
		if ($saveit) //save next wiz step...
			SetSessionParam('wdata', $this->wdata); 
		
		return ($val);
	}
	
	//read wiz element
	protected function read_wizard_element($var=null) {
	    if (!$var) return false;
		
		$ret = $this->wdata[$var];
		return ($ret);
	}	
	
	//DISABLED, CALL modify_tags_inhtml_dir directly
	//change @keywords@...just call modify_data_dir..
	//OLD::change_data
 	function change_html_tags() {
		
		//$data = $this->wdata;
		//pick old ini file before save...
		$old_data = $this->read_wizard_file(false);
	    //print_r($old_data);
		
	    if (!empty($this->wdata)) {
			
		    foreach ($this->wdata as $name=>$key) {
			
			   $ret .=  '>>>'.$name .'='.$key;
			   
			   if (is_array($old_data)) {//(isset($old_data[$name]))
			     $old_value = isset($old_data[$name]) ? $old_data[$name] : null; 
			     $ret .= $old_value ? " replacing $old_value<br>" : "<br>";
			   }	 
			   else
			     $ret .= '<br>';
			}
            //ENABLE TAG REPLACEMENTS...CALIT DIRECTLY
			//$ret = $this->modify_tags_inhtml_dir();//$data, $old_data, true);
			return ($ret);
		}
		return false;
	}

	//modify .html files @keywords@
    function modify_tags_inhtml_dir() {
		//pick old ini file before save...
		$old_data = $this->read_wizard_file(false);		
		$sourcedir = $this->prpath . '/html/';// . $dirname;
		
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
				
				foreach ($this->wdata as $name=>$key) {
				  /*  if (isset($old_data[$name])) //prev modified value exist
						$myname = $old_data[$name]//'@'.strtoupper($name).'@';
					else
						$myname = '@'.strtoupper($name).'@';	*/				
											
					
					$myname = '@'.strtoupper($name).'@';
					//$fdata = str_replace($myname,$key,$fdata);
					
					if (stristr($fdata, $myname)) {//init values
					    //$ret .= '<br>'.$name.'='.$key;
						$fdata = str_replace($myname,$key,$fdata);
					}
					/*else*/
					if ((is_array($old_data)) && ($old_value = $old_data[$name])) { 
					    //$ret .= '<br>'.$old_value.'='.$key;
					    $fdata = str_replace($old_value,$key,$fdata);
					} 					
				}		
		        @file_put_contents("$sourcedir/$fileread", $fdata);
				//$ret .=  '<br>modify:'.$sourcedir.'/'.$fileread.'<br>'; 
				
				//read html files to edit (weditfiles array).....automated at end..
				//$this->weditfiles[] = $fileread;
				
				$fmeter+=1; 				
			  }
           }
        }
        $mydir->close ();	
		//.=
		$ret = $fmeter ? $fmeter . ' files affected' : '0 files affcted';
		return ($ret);
	}
	
	//read html files to edit (weditfiles array).....
    function read_html_files($save_session=false) {
		$sourcedir = $this->prpath . '/html/';
	    $fmeter = 0;	
		
	    if (!is_dir($sourcedir)) 
		   return (false);		 

		  
        $mydir = dir($sourcedir);
        while ($fileread = $mydir->read ()) { 
	
           if (($fileread!='.') && ($fileread!='..') && (!is_dir($sourcedir.'/'.$fileread)) ) { 
			  if ((stristr($fileread,'.htm')) && (substr($fileread,0,2)!='cp'))  {//<<cpfilename restiction

				$this->weditfiles[] = $fileread;
				$fmeter+=1; 				
			  }
           }
        }
        $mydir->close ();
		
		if ($save_session)
		    SetSessionParam('weditfiles', $this->weditfiles); 		
		
		$ret = $fmeter ? $fmeter : null;
		return ($ret);
	}	

	//read environment cp file
	protected function read_env_file($save_session=false) {
		$myenvfile = $this->prpath . 'cp.ini';
		$ret = @parse_ini_file($myenvfile ,false, INI_SCANNER_RAW);	

		if ($save_session)
		    SetSessionParam('env', $ret); 
		
		return ($ret);
    }	

	//write environment cp file
	protected function write_env_file($module,$mvalue=1,$reload_env_session=false) {
	    if (!$module) return false;
        $myenvfile = $this->prpath . 'cp.ini';
        $newmodule = strtoupper($module);
		$append_string = $newmodule.'='.$mvalue;
   
        //backup cp file
		@copy($myenvfile, str_replace('.ini','._ni',$myenvfile));
		
		//check for existing string
		$initext = @file_get_contents($myenvfile);
		
		if (stristr($initext,$newmodule.'=1')) {//is enabled
		    //replace cp.ini
			$ret = @file_put_contents($myenvfile, str_replace($newmodule.'=1',$newmodule.'='.$mvalue,$initext));
		}
		elseif (stristr($initext,$newmodule.'=0')) {//is disabled
		    //replace cp.ini
			$ret = @file_put_contents($myenvfile, str_replace($newmodule.'=1',$newmodule.'='.$mvalue,$initext));		
		}
		elseif (stristr($initext,$newmodule.'=')) {//is disabled ..has no value
		    //replace cp.ini
			$ret = @file_put_contents($myenvfile, str_replace($newmodule.'=1',$newmodule.'='.$mvalue,$initext));		
		}		
		else
			//append cp.ini
			$ret = @file_put_contents($myenvfile, "\r\n" . $append_string, FILE_APPEND | LOCK_EX);
			
		if ($reload_env_session) //reload environment in session	
            $this->environment = $this->read_env_file(true);
			
        return ($ret);			
    }
	
	//change / add htuser ......
    function add_cp_htaccess() {
  
		$htpass_path = $this->prpath;// . '/'; 
		$htaccess_path = $this->app_location . '/cp/'; 
		$this->log .=   '<br>HTACCESS PATH:'. $htpass_path.'>'.	$htaccess_path;	
		
		if (is_dir($htaccess_path)) {
		
			$htpass_file = $htpass_path . 'htpasswd-'.$this->posted_appname;//per app
			$htaccess_file = $htaccess_path . '.htaccess';
		    $this->log .=  '<br>HTACCESS FILE:'. $htpass_file.'>'.	$htaccess_file;	
	    }
		else
		    return false;
			
		// Initializing class htaccess as $ht
		$ht = new htaccess($htaccess_file, $htpass_file);//"/var/www/.htaccess","/var/www/htpasswd");
		// Adding user
		$ht->addUser($this->posted_appname, $this->posted_password);
		
		// Changing password for User
		//$ht->setPasswd("username","newPassword");
		// Getting all usernames from set password file
		$users = $ht->getUsers();
		for($i=0;$i<count($users);$i++){
			$this->log .= $users[$i];
		}
		// Deleting user
		//$ht->delUser("username");
		// Setting authenification type
		// If you don't set, the default type will be "Basic"
		$ht->setAuthType("Basic");
		// Setting authenification area name
		// If you don't set, the default name will be "Internal Area"
		$ht->setAuthName("Control Panel");
		//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		// finally you have to process addLogin()
		// to write out the .htaccess file
		$ht->addLogin();
		// To delete a Login use the delLogin function
		//$ht->delLogin();	
		
		return true;
	}	
	
	//write wiz file, DISABLE WIZARD IF TRUE..
	protected function reinit_wizard($status=false) {
	    $ok = false;
		$mywizfile = $this->prpath . $this->wizardfile;	   
		$mywizfile_disabled = $this->prpath . str_replace('.ini','._ni',$this->wizardfile);

		if (is_readable($mywizfile_disabled)) {
		    
			//set wizard=1
			$wfd = str_replace('wizard=0','wizard=1',@file_get_contents($mywizfile_disabled));
			$setw = @file_put_contents($mywizfile_disabled, $wfd);
			
			//rename ._ni => .ini
			$ok = @rename($mywizfile_disabled, $mywizfile);		
			
		    if (($setw) && ($ok)) {
				//final set of wiz status
				//$this->write_wizard_element('wizard',$onoff);
			    //reset vars
				$this->environment = $_SESSION['env'] = null;
				$this->wdata = $_SESSION['wdata'] = null; 		
				$this->wstep = $_SESSION['wstep'] = 1;//null ;
				$this->weditfiles = $_SESSION['weditfiles'] = null;
				
				//kill session and re-enter
				//session_destroy();
			}
		
		}
		//else
		  //echo "not a w file!($mywizfile)";		

        return ($ok);	
    }	
  
};
}
?>
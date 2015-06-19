<?php

$__DPCSEC['RCUSERS_DPC']='1;1;1;1;1;1;1;1;1';

if ((!defined("RCUSERS_DPC")) && (seclevel('RCUSERS_DPC',decode(GetSessionParam('UserSecID')))) ) {
define("RCUSERS_DPC",true);

$__DPC['RCUSERS_DPC'] = 'rcusers';

//$a = GetGlobal('controller')->require_dpc('nitobi/nitobi.lib.php');
//require_once($a);

$b = GetGlobal('controller')->require_dpc('phpdac/shusers.dpc.php');
require_once($b);


$__EVENTS['RCUSERS_DPC'][0]='cpusers';
$__EVENTS['RCUSERS_DPC'][1]='deluser';
$__EVENTS['RCUSERS_DPC'][2]='reguser';
$__EVENTS['RCUSERS_DPC'][3]='cpcusmail';
$__EVENTS['RCUSERS_DPC'][4]='cpcusmsend';
$__EVENTS['RCUSERS_DPC'][5]='insuser';
$__EVENTS['RCUSERS_DPC'][6]='upduser';
$__EVENTS['RCUSERS_DPC'][7]='saveupduser';
$__EVENTS['RCUSERS_DPC'][8]='cpupdate';
$__EVENTS['RCUSERS_DPC'][9]='cpupdateadv';
$__EVENTS['RCUSERS_DPC'][10]='cpusractiv';
$__EVENTS['RCUSERS_DPC'][11]='searchtopic';
$__EVENTS['RCUSERS_DPC'][12]='regusercus';

$__ACTIONS['RCUSERS_DPC'][0]='cpusers';
$__ACTIONS['RCUSERS_DPC'][1]='deluser';
$__ACTIONS['RCUSERS_DPC'][2]='reguser';
$__ACTIONS['RCUSERS_DPC'][3]='cpcusmail';
$__ACTIONS['RCUSERS_DPC'][4]='cpcusmsend';
$__ACTIONS['RCUSERS_DPC'][5]='insuser';
$__ACTIONS['RCUSERS_DPC'][6]='upduser';
$__ACTIONS['RCUSERS_DPC'][7]='saveupduser';
$__ACTIONS['RCUSERS_DPC'][8]='cpupdate';
$__ACTIONS['RCUSERS_DPC'][9]='cpupdateadv';
$__ACTIONS['RCUSERS_DPC'][10]='cpusractiv';
$__ACTIONS['RCUSERS_DPC'][11]='searchtopic';
$__ACTIONS['RCUSERS_DPC'][12]='regusercus';

$__DPCATTR['RCUSERS_DPC']['cpusers'] = 'cpusers,1,0,0,0,0,0,0,0,0,0,0,1';

$__LOCALE['RCUSERS_DPC'][0]='RCUSERS_DPC;Users;Χρήστες';
$__LOCALE['RCUSERS_DPC'][1]='_reason;Reason;Αιτία';
$__LOCALE['RCUSERS_DPC'][2]='_cdate;Date in;Ημ/νία εισοδου';
$__LOCALE['RCUSERS_DPC'][3]='_price;Price;Τιμή';
$__LOCALE['RCUSERS_DPC'][4]='_ftype;Pay;Πληρωμή';
$__LOCALE['RCUSERS_DPC'][5]='_name1;First Name;Ονομα';
$__LOCALE['RCUSERS_DPC'][6]='_name2;Last Name;Επώνυμο';
$__LOCALE['RCUSERS_DPC'][7]='_kybismos;Kyb.;Κυβικα';
$__LOCALE['RCUSERS_DPC'][8]='_color;Color;Χρώμα';
$__LOCALE['RCUSERS_DPC'][9]='_extras;Extras;Εχτρα';
$__LOCALE['RCUSERS_DPC'][10]='_address;Address;Διεύθυνση';
$__LOCALE['RCUSERS_DPC'][11]='_tel;Tel.;Τηλέφωνο';
$__LOCALE['RCUSERS_DPC'][12]='_mob;Mobile;Κινητό';
$__LOCALE['RCUSERS_DPC'][13]='_email;e-mail;e-mail';
$__LOCALE['RCUSERS_DPC'][14]='_fax;Fax;Fax';
$__LOCALE['RCUSERS_DPC'][15]='_TIMEZONE;Timezone;Ζωνη ωρας';
$__LOCALE['RCUSERS_DPC'][16]='_fname;Contact person;Υπεύθυνος επικοινωνίας';
$__LOCALE['RCUSERS_DPC'][17]='_lname;Title;Επωνυμια';
$__LOCALE['RCUSERS_DPC'][18]='_username;Username;Χρήστης';
$__LOCALE['RCUSERS_DPC'][19]='_password;Password;Κωδικός';
$__LOCALE['RCUSERS_DPC'][20]='_notes;Notes;Σημειωσεις';
$__LOCALE['RCUSERS_DPC'][21]='_subscribe;Subscriber;Συνδρομητης';
$__LOCALE['RCUSERS_DPC'][22]='_seclevid;seclevid;seclevid';
$__LOCALE['RCUSERS_DPC'][23]='_secparam;Param;Param';
$__LOCALE['RCUSERS_DPC'][24]='_active;Active;Ενεργός';
$__LOCALE['RCUSERS_DPC'][25]='_newuser;Add user;Προσθήκη χρήστη';
$__LOCALE['RCUSERS_DPC'][26]='_newcus;Add customer;Προσθήκη πελάτη';
$__LOCALE['RCUSERS_DPC'][27]='_newcususer;Add new;Προσθήκη συναλλασόμενου';

class rcusers extends shusers {

    var $title;
	var $carr;
	var $msg;
	var $path;
	var $post;
	var $maillink;

	var $_grids;
	//var $urlpath, $inpath;
	
	var $tell_activate, $tell_deactivate;
	var $subj_activate, $subj_deactivate;
	var $body_activate, $body_deactivate;

	function rcusers() {
	
	  shusers::shusers();
	
	  $GRX = GetGlobal('GRX');
	  $this->title = localize('RCUSERS_DPC',getlocal());
	  $this->carr = null;
	  $this->msg = null;

	  $this->path = paramload('SHELL','prpath');
	  //$this->urlpath = paramload('SHELL','urlpath');
	  //$this->inpath = paramload('ID','hostinpath');		  


	  $this->maillink = seturl('t=cpcusmail&<@>');

      if ($GRX) {
          $this->delete = loadTheme('ditem',localize('_delete',getlocal()));
          $this->edit = loadTheme('eitem',localize('_edit',getlocal()));
          //$this->import = loadTheme('ivehicle',localize('_import',getlocal()));
          //$this->recode = loadTheme('rvehicle',localize('_recode',getlocal()));
          $this->add = loadTheme('aitem',localize('_add',getlocal()));
          $this->mail = loadTheme('mailitem',localize('_mail',getlocal()));

		  $this->sep = "&nbsp;";//loadTheme('lsep');
      }
      else {
          $this->delete = localize('_delete',getlocal());
          $this->edit = localize('_edit',getlocal());
          //$this->import = localize('_import',getlocal());
          //$this->recode = loadTheme('rvehicle','show help');
          $this->add = localize('_add',getlocal());
          $this->mail = localize('_mail',getlocal());

		  $this->sep = "|";
      }
	  
	  $this->tell_activate = remote_paramload('RCUSERS','mail_on_activate',$this->path);
	  $this->tell_deactivate = remote_paramload('RCUSERS','mail_on_deactivate',$this->path);	
	  $this->subj_activate = remote_paramload('RCUSERS','subject_on_activate',$this->path);
	  $this->subj_deactivate = remote_paramload('RCUSERS','subject_on_deactivate',$this->path);
	  $this->body_activate = remote_paramload('RCUSERS','text_on_activate',$this->path);
	  $this->body_deactivate = remote_paramload('RCUSERS','text_on_deactivate',$this->path);	  
	}

    function event($event=null) {

	   /////////////////////////////////////////////////////////////
	   if (GetSessionParam('LOGIN')!='yes') die("Not logged in!");//
	   /////////////////////////////////////////////////////////////

	   switch ($event) {
	     case 'cpusractiv'    : $this->activate_deactivate();
	                            break;
	     case 'cpupdateadv'   :
		                        break;
	     case 'cpupdate'      : if (!$this->checkFields(null,$this->checkuseasterisk)) {
		 
									//auto subscribe
									if ( (defined('SHSUBSCRIBE_DPC')) /*&& (seclevel('SHSUBSCRIBE_DPC',$this->userLevelID))*/ ) {
										if (trim(GetParam('autosub'))=='on')
											GetGlobal('controller')->calldpc_method('shsubscribe.dosubscribe use '.GetParam("eml"));//.'++-1');
										else
											GetGlobal('controller')->calldpc_method('shsubscribe.dounsubscribe use '.GetParam("eml"));//.'+-1');
									}
							  		 
		                            $this->update();
							   }	 
							   break;
		 
	     case 'cpcusmsend'  : $this->send_mail();
		                      //$this->carr = $this->select_customers('all',null,GetReq('alpha'));//dummy param
		                      break;
	     case 'cpcusmail'   :
		                      break;
 	     case 'regusercus'  :
		                      break;
	     case 'reguser' :
		                      break;
		 case 'insuser' :     //$this->insert();
                              $this->insert_user_customer();  
		                      break;
							  
	     case 'upduser' :     //if (!GetReq('editmode'))
		                        //$this->updrec = $this->getuserdata(null,GetReq('rec'),$this->actcode);
							  //else
							    //$this->updrec = $this->getuser(GetReq('rec'),null,0,1);	
							
                              $this->grid_javascript();							
		                      break;
							  
		 case 'saveupduser' : $this->update();
		                      break;
							  
	     case 'deluser' :    $this->_delete(GetReq('rec'),'id');
							  break;
							 
		 case 'searchtopic' : 					 
	     case 'cpusers'     : 
		 default            : //if (!GetReq('editmode'))
		                        //$this->nitobi_javascript();
		                      //$this->carr = $this->select_customers('all',null,GetReq('alpha'));//dummy param
	   }

    }

    function action($action=null) {

	  /*if (GetSessionParam('REMOTELOGIN'))
	     $out = setNavigator(seturl("t=cpremotepanel","Remote Panel"),$this->title);
	  elseif (!GetReq('editmode'))
         $out = setNavigator(seturl("t=cp","Control Panel"),$this->title);
      */  
	  switch ($action) {
	     case 'cpupdateadv' : $out .= $this->user_form();
		                      break;
	     case 'cpcusmsend'  : $out .= $this->show_users();
		                      break;
	     case 'cpcusmail'   : $out .= $this->show_mail();
		                      break;
	     case 'deluser'     : //if (GetReq('editmode'))
		                        $out .= $this->viewUsers2();
							  /*else	
		                        $out .= $this->show_users();*/
		                      break;
	     case 'regusercus' :  $out .= $this->regform(null,'insuser');//this cmd
		                      break;							  
		 case 'reguser'     : //$out .= $this->form();
		                      //$out .= $this->show_users();
							  $out .= $this->regform();//null,'insuser');//this cmd ?
							  break;
		 case 'cpupdate'  :	  //$out .= $this->register();
		                      /*if (!GetReq('editmode'))
		                        $out .= $this->show_users();
							  else*/
							    $out .= $this->viewUsers2();	
		                      break;	
							  			  
	     case 'upduser' :     //$out .= $this->updrec.'>';
		 
		                      //if (!GetReq('editmode'))
		                        //$out .= $this->regform($this->updrec,'saveupduser',1);
							  //else
							  
							  //$out .= $this->register(GetReq('rec'),'id','rec','cpupdate');
 							  	
							  $out .= $this->update_user_form();   
		                      break;
		 case 'saveupduser' :
         case 'insuser'     :
	     case 'cpusers'     :
		 case 'cpusractiv'  :
		 case 'searchtopic' :			 
		 default            : /*if (!GetReq('editmode'))
		                        $out .= $this->show_users();
							  else*/
							    $out .= $this->viewUsers2();	
	 }

	 return ($out);
    }
	
	function grid_javascript() {
      if (iniload('JAVASCRIPT')) {		      
		   
	       $code = $this->init_grids();			
		   $js = new jscript;	   
           $js->load_js($code,"",1);			   
		   unset ($js);
	  }		
	}		
	
	function init_grids() {

	    //$bodyurl = seturl("t=cptranslink&tid=");	
		$bodyurl = seturl("t=cploadframe&tid=");	
	
        //disable alert !!!!!!!!!!!!		
		$out = "
function alert() {}\r\n 

function update_stats_id() {
  var str = arguments[0];
  var str1 = arguments[1];
  var str2 = arguments[2];
  
  
  statsid.value = str;
  //alert(statsid.value);
  sndReqArg('$this->ajaxLink'+statsid.value,'stats');
  
  return str1+' '+str2;
}

function show_body() {
  var str = arguments[0];
  var str1 = arguments[1];
  var str2 = arguments[2];  
  var taskid;
  var custid;
  
  taskid = str;  
  sndReqArg('$bodyurl'+taskid,'trans');
}			
";

        $out .= "\r\n";
        return ($out);
	}	
	
	protected function update_user_form() {
	
	   //update form
	   $form = $this->register(GetReq('rec'),'id','rec','cpupdate');
	   
	   //if (defined('RCTRANSACTIONS_DPC')) 
		//$form .= GetGlobal('controller')->calldpc_method("rctransactions.show_grid");	   
	   
	   if (defined('RCTRANSACTIONS_DPC')) {
	   
		$datattr[] = $form;
		$viewattr[] = "left;50%";	   

		//user transactions
		$trans = GetGlobal('controller')->calldpc_method("rctransactions.show_grid use 800+150+1");	   
		$datattr[] = $trans;
		$viewattr[] = "left;50%";	   
	   
		$myw = new window('',$datattr,$viewattr);
		$ret = $myw->render();//"center::100%::0::group_article_selected::left::3::3::");
		unset ($datattr);
		unset ($viewattr);
		
		$ret .= GetGlobal('controller')->calldpc_method("ajax.setajaxdiv use trans");	   
      
		return ($ret);	   
	   }
       else
        return ($form); 	   
	}
	
	//when post
	protected function insert_user_customer() {
	
        if ($this->includecusform) {
			//RCCUSTOMERS...extends shcaustomers
			if ( (defined('SHCUSTOMERS_DPC')) ) {// && (seclevel('SHCUSTOMERS_DPC',$this->userLevelID)) ) {
				//echo 'a>';
			    if ($this->check_existing_customer) {
				    //echo 'b>';
                    if ($cid = GetGlobal('controller')->calldpc_method('shcustomers.customer_exist use 1')) {
		                if ($cid<>-1) {//not mapped customer	
								     //echo 'c1>';
								     $checkcuserr = null;
									 $this->map_customer = true;						 
									 $this->customer_exist_id = $cid;
						}
						else {//already maped customer
								     //echo 'c2>';
								     $checkcuserr = localize('_CUSTEXISTS',getlocal());//'Customer exist!';
									 $this->map_customer = false;	
									 $this->customer_exist_id = null;
									 SetGlobal('sFormErr',$checkcuserr);
						}
					}
					else  {//new customer
					    //echo 'c>';
					    $checkcuserr = GetGlobal('controller')->calldpc_method('shcustomers.checkFields use +'.$this->checkuseasterisk);   
				 	    $this->map_customer = null; //new customer	
				    } 
			    }
			    else {//new customer
			        $checkcuserr = GetGlobal('controller')->calldpc_method('shcustomers.checkFields use +'.$this->checkuseasterisk);
			   	    //SetGlobal('sFormErr',$checkcuserr);
			    }
		    }
							   
			//user check  
			$checkusrerr = $this->checkFields(null,$this->checkuseasterisk);
			//echo 'errors:',$checkusrerr,'|',$checkcuserr;
							 
			if ((!$checkusrerr) && (!$checkcuserr))  {		
			    //echo 'e>';
				$this->insert_with_customer();							  					 
			} 
							 
	    }//not include cus form
	    else {
	        if (!$this->checkFields(null,$this->checkuseasterisk)) {	
	            $this->insert();
            }
        }	
	}
	
	function form($action=null) {

     $myaction = seturl("t=reguser");

     if ($this->post==true) {

	   SetSessionParam('REGISTERED_CUSTOMER',1);
	 }
	 else { //show the form plus error if any

       //if (!$action) $out = setNavigator($this->title);

       $out .= setError($sFormErr . $this->msg);


	   $form = new form(localize('_ADDEVENT',getlocal()), "reguser", FORM_METHOD_POST, $myaction, true);

	   $form->addGroup			("personal",			"Tell us about your self.");
	   //$form->addGroup			("technical",			"Tell us about your technology.");
	   $form->addGroup			("subscribe",			"Subscribe.");

	   $form->addElement		("personal",			new form_element_text		(localize('_COMP',getlocal())."*",     "company",		GetParam("company"),				"forminput",	        50,				255,	0));
	   $form->addElement		("personal",			new form_element_text		(localize('_CPER',getlocal()),     "cperson",		GetParam("cperson"),				"forminput",	        20,				255,	0));
	   $form->addElement		("personal",			new form_element_text		(localize('_ACTV',getlocal()),     "activities",	GetParam("activities"),				"forminput",	        30,				255,	0));
	   $form->addElement		("personal",			new form_element_text		(localize('_ADDR',getlocal()),     "address",	    GetParam("address"),				"forminput",	        30,				255,	0));
	   $form->addElement		("personal",			new form_element_text		(localize('_TOWN',getlocal()),     "town",	        GetParam("town"),				"forminput",	        20,				255,	0));
//	   $form->addElement		("personal",			new form_element_greekmap	(localize('_NOMOS',getlocal()),     "amail","nomos",GetParam("nomos"),"forminput",20,20,1));
	   $form->addElement		("personal",			new form_element_text		(localize('_ZIP',getlocal()),      "zip",	        GetParam("zip"),				"forminput",	        20,				255,	0));
	   $form->addElement		("personal",			new form_element_text		(localize('_CNTR',getlocal()),     "country",	    GetParam("country"),				"forminput",	        20,				255,	0));
	   //$form->addElement		("personal",			new form_element_combo_file (localize('_CNTR',getlocal()),     "country",	    $this->get_country_from_ip(),				"forminput",	        1,				0,	'country'));
	   $form->addElement		("personal",			new form_element_text		(localize('_TEL',getlocal()),      "tel",	        GetParam("tel"),				"forminput",	        20,				255,	0));
	   $form->addElement		("personal",			new form_element_text		(localize('_FAX',getlocal()),      "fax",	        GetParam("fax"),				"forminput",	        20,				255,	0));
	   $form->addElement		("personal",			new form_element_text		(localize('_MAIL',getlocal())."*",     "email",			GetParam("email"),				"forminput",	        30,				255,	0));
	   $form->addElement		("personal",			new form_element_text		(localize('_WEB',getlocal()),      "web",			"http://",		"forminput",		    20,				255,	0));

	   //$form->addElement		("technical",			new form_element_combo_file (localize('_PLAN',getlocal()),     "proglan",	    GetParam("proglan"),				"forminput",	        5,				0,	'proglan'));
	   //$form->addElement		("technical",			new form_element_combo_file (localize('_OSYS',getlocal()),     "opersys",	    GetParam("opersys"),				"forminput",	        5,				0,	'opersys'));
	   //$form->addElement		("technical",			new form_element_combo_file (localize('_USERI',getlocal()),     "userint",	    GetParam("userint"),				"forminput",	        5,				0,	'userint'));
	   //$form->addElement		("technical",			new form_element_combo_file (localize('_DBENV',getlocal()),     "dbenv",	    GetParam("dbenv"),				"forminput",	        5,				0,	'dbenv'));

	   //$form->addElement		("subscribe",			new form_element_text		(localize('_SYBSCR',getlocal()),   "subscribe",		"",				"forminput",	        20,				255,	0));
	   $form->addElement		("subscribe",			new form_element_radio		(localize('_RCSUBSE',getlocal()),   "subscribe",      1,             "",   2, array ("0" => localize('_OXI',getlocal()), "1" => localize('_NAI',getlocal()))));
	   //$form->addElement		("thema",			    new form_element_text		(localize('_SUBJECT',getlocal())."*",  "subject",		GetParam("subject"),				"forminput",			60,				255,	0));
	   //$form->addElement		("thema",			    new form_element_textarea   (localize('_MESSAGE',getlocal()),  "mail_text",		GetParam("mail_text"),				"formtextarea",			60,				9));

	   //$form->addElement		("warning",			    new form_element_onlytext	(localize('_WARNING',getlocal()),  localize('_FORMWARN',getlocal()),""));

	   //if ($this->info_message)
	     //$form->addElement		("info",			    new form_element_onlytext	("",  $this->info_message,""));

	   // Adding a hidden field
	   if ($action)
	     $form->addElement		(FORM_GROUP_HIDDEN,		new form_element_hidden ("FormAction", $action));
	   else
	     $form->addElement		(FORM_GROUP_HIDDEN,		new form_element_hidden ("FormAction", "reguser"));

	   // Showing the form
	   $fout = $form->getform ();

	   //$fwin = new window(localize('AMAIL_DPC',getlocal()),$fout);
	   //$out .= $fwin->render();
	   //unset ($fwin);

	   $out .= $fout;

	   //$form->checkform();
	 }

     return ($out);
	}


    function get_country_from_ip() {

     $mycountry = GetGlobal('controller')->calldpc_method("country.find_country");
	 //return "Greece";
	 return ($mycountry);
    }

	
	function getUsersList() {
       $db = GetGlobal('db');
       $UserName = GetGlobal('UserName');	
	   //$name = $UserName?decode($UserName):null;		   
       //echo GetReq('col');
	   	   
	     $sSQL = "select id,notes,fname,lname,email,username,code1,code2 from users ";// .//where cid=" . $db->qstr($name) . 
		 
		 if ($s = GetParam('searcht')) {//SEARCH TOPIC   
		   $sSQL .= "where fname like '%$s%' or lname like '%$s%' or email like '%$s%' or username like '%$s%' ";
		 }  		 
		 
		 if ($col = GetReq('col'))
		   $sSQL .= "order by " . $col;
		 else
		   $sSQL .= "order by id"; 
		   
		 if (GetReq('sort')<0)
		   $sSQL .= ' DESC';
		   
		 //echo $sSQL;		 
				 
				 
	     $res = $db->Execute($sSQL,2);
	     //print_r ($res);
		 $i=0;
	     if (!empty($res)) { 
	       foreach ($res as $n=>$rec) {
		    $i+=1;
				
			
            $transtbl[] = $i . ";" . 
                         $rec[0] . ";" . $rec[1] . ";" . $rec[2] . ";" . $rec[3] . ";" .
						 $rec[4] . ";" . $rec[5] . ";" . $rec[6] . ";" . $rec[7];						 					 	   
		   }
		   
           //browse
		   //print_r($transtbl); 
		   $ppager = GetReq('pl')?GetReq('pl'):100;
           $browser = new browse($transtbl,null,$this->getpage($transtbl,$this->searchtext));
	       $out .= $browser->render("cpusers",$ppager,$this,1,1,0,0,1,1,1,0);
	       unset ($browser);	
		      
	     }
		 else {
           //empty message
	       $w = new window(null,localize('_EMPTY',getlocal()));
	       $out .= $w->render("center::40%::0::group_win_body::left::0::0::");//" ::100%::0::group_form_headtitle::center;100%;::");
	       unset($w);

		 }		 	
	   
	   return ($out);
	} 
	
	
	function viewUsers2() {
	
	    if (!defined('MYGRID_DPC')) 
		   return ($this->viewUsers());	
		   
        $sFormErr = GetGlobal('sFormErr');
	    if (($msg = $this->msg) || ($msg = $sFormErr)) 
			$out = $msg;//$this->msg;
			
	    /*//..add user-customer ??? goto frontend
	    $links = seturl("t=reguser",localize('_newuser',getlocal())) . '|'.
	            seturl("t=regcustomer",localize('_newcus',getlocal()));
		if ($this->includecusform) //!!!! to be continued
            $links .= '|'.seturl("t=regusercus",localize('_newcususer',getlocal()));		
	   
	    $myadd = new window('',$links);
	    $out .= $myadd->render("center::100%::0::group_article_selected::right::0::0::");
	    unset ($myadd);	
        */
	    if (defined('MYGRID_DPC')) {
		
           if ( (defined('SHCUSTOMERS_DPC')) ) {
			//$xsSQL = "select id,notes,fname,lname,email,name address,area from users ";
			$xsSQL2 = "SELECT * FROM (SELECT i.id,i.notes,i.fname,i.lname,i.email,i.username,i.code2,c.name,c.address,c.area FROM users i";
			$xsSQL2.= " INNER JOIN customers c ON c.code2 = i.code2 AND c.active>0) x";
			//$out.= $xsSQL2;
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+id|".localize('id',getlocal())."|5|0|||1");
			//GetGlobal('controller')->calldpc_method("mygrid.column use grid2+notes|".localize('_active',getlocal())."|boolean|1|ACTIVE:DELETED");
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+notes|".localize('_active',getlocal())."|link|0|".seturl('t=cpusractiv&rec={id}').'||1');
		    //GetGlobal('controller')->calldpc_method("mygrid.column use grid2+fname|".localize('_fname',getlocal())."|20|1|");
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+fname|".localize('_fname',getlocal())."|link|20|".seturl('t=upduser&editmode=1&rec={id}&cusmail={username}').'||');
		    //GetGlobal('controller')->calldpc_method("mygrid.column use grid2+lname|".localize('_lname',getlocal())."|20|1|");
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+lname|".localize('_lname',getlocal())."|link|20|".seturl('t=cptransactions&editmode=1&cusmail={username}').'||');
		    GetGlobal('controller')->calldpc_method("mygrid.column use grid2+username|".localize('_username',getlocal())."|20|0|");				
			//GetGlobal('controller')->calldpc_method("mygrid.column use grid2+name|".localize('_name',getlocal())."|20|0|");
			//GetGlobal('controller')->calldpc_method("mygrid.column use grid2+name|".localize('_name',getlocal())."|link|20|".seturl('t=cpupdateadv&editmode=1&rec={id}').'||');	
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+address|".localize('_address',getlocal())."|20|0|");
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+area|".localize('_area',getlocal())."|10|0|");
			$out .= GetGlobal('controller')->calldpc_method("mygrid.grid use grid2+users+$xsSQL2+r+".localize('RCUSERS_DPC',getlocal())."+id+1+1+36+800");
		   }
		   else {
		    //due to sql conflict, sql must be subselect of previous sql when multiple sql statements exec at once
            $xsSQL = "SELECT * from (select x.id,x.notes,x.fname,x.lname,x.email,x.username from users x) o ";		   
		   
		    GetGlobal('controller')->calldpc_method("mygrid.column use grid1+id|".localize('id',getlocal())."|5|0|||1");
		    //GetGlobal('controller')->calldpc_method("mygrid.column use grid1+notes|".localize('_active',getlocal())."|boolean|1|ACTIVE:DELETED");		   
		    GetGlobal('controller')->calldpc_method("mygrid.column use grid1+notes|".localize('_active',getlocal())."|link|0|".seturl('t=cpusractiv&editmode=1&rec={id}').'||');
		    //GetGlobal('controller')->calldpc_method("mygrid.column use grid1+fname|".localize('_fname',getlocal())."|20|1|");
		    GetGlobal('controller')->calldpc_method("mygrid.column use grid1+fname|".localize('_fname',getlocal())."|link|20|".seturl('t=upduser&editmode=1&rec={id}').'||');
		    GetGlobal('controller')->calldpc_method("mygrid.column use grid1+lname|".localize('_lname',getlocal())."|20|1|");
		    GetGlobal('controller')->calldpc_method("mygrid.column use grid1+username|".localize('_username',getlocal())."|20|0|");
		   
		    $out .= GetGlobal('controller')->calldpc_method("mygrid.grid use grid1+users+$xsSQL+r+".localize('RCUSERS_DPC',getlocal())."+id+1+1+36+800");
		   }
	    }
        return ($out); 		
	}	
	
	function viewUsers() {
       $db = GetGlobal('db');
	   $a = GetReq('a');
       $UserName = GetGlobal('UserName');
       $sFormErr = GetGlobal('sFormErr');
	   if (($msg = $this->msg) || ($msg = $sFormErr)) 
		   $out = $msg;//$this->msg;
	   /* //..add user-customer ??? //goto frontend
	   $links = seturl("t=reguser",localize('_newuser',getlocal())) . '|'.
	            seturl("t=regcustomer",localize('_newcus',getlocal()));
	   if ($this->includecusform) //!!!! to be continued
            $links .= '|'.seturl("t=regusercus",localize('_newcususer',getlocal()));		
	   */
	   
	   $myadd = new window('',$links);
	   $out .= $myadd->render("center::100%::0::group_article_selected::right::0::0::");
	   unset ($myadd);
	   	   
	   
	  /* $apo = GetParam('apo'); //echo $apo;
	   $eos = GetParam('eos');	//echo $eos;   

       $myaction = seturl("t=cpusers&editmode=".GetReq('editmode'));	   
	   
       $out .= "<form method=\"POST\" action=\"";
       $out .= "$myaction";
       $out .= "\" name=\"Transview\">";		   
      */ 
	 
	   $out .= 	$this->getUsersList();	 
		 
	  /*		 
       $out .= "<input type=\"hidden\" name=\"FormName\" value=\"Userview\">";
       $out .= "</FORM>";			 		   
		*/	

	   /*if (defined('MYGRID_DPC')) {
		
           if ( (defined('SHCUSTOMERS_DPC')) ) {
			//$xsSQL = "select id,notes,fname,lname,email,name address,area from users ";
			$xsSQL2 = "SELECT * FROM (SELECT i.id,i.notes,i.fname,i.lname,i.email,i.username,i.code2,c.name,c.address,c.area FROM users i";
			$xsSQL2.= " INNER JOIN customers c ON c.code2 = i.code2) o";
			//echo $xsSQL2;
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+id|".localize('id',getlocal())."|5|0|||1");
			//GetGlobal('controller')->calldpc_method("mygrid.column use grid2+notes|".localize('_active',getlocal())."|boolean|1|ACTIVE:DELETED");
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+notes|".localize('_active',getlocal())."|link|0|".seturl('t=cpusractiv&rec={id}').'||1');
		    //GetGlobal('controller')->calldpc_method("mygrid.column use grid2+fname|".localize('_fname',getlocal())."|20|1|");
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+fname|".localize('_fname',getlocal())."|link|20|".seturl('t=upduser&editmode=1&rec={id}').'||');
		    GetGlobal('controller')->calldpc_method("mygrid.column use grid2+lname|".localize('_lname',getlocal())."|20|1|");
		    GetGlobal('controller')->calldpc_method("mygrid.column use grid2+username|".localize('_username',getlocal())."|20|0|");		
			//GetGlobal('controller')->calldpc_method("mygrid.column use grid2+name|".localize('_name',getlocal())."|20|0|");
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+name|".localize('_name',getlocal())."|link|20|".seturl('t=cpupdateadv&editmode=1&rec={id}').'||');	
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+address|".localize('_address',getlocal())."|20|0|");
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+area|".localize('_area',getlocal())."|10|0|");
			$out .= GetGlobal('controller')->calldpc_method("mygrid.grid use grid2+users+$xsSQL2+r+".localize('RCUSERS_DPC',getlocal())."+id+1+1+20+400");
		   }
		   //else {
		    //due to sql conflict, sql must be subselect of previous sql when multiple sql statements exec at once
            $xsSQL = "SELECT * from (select x.id,x.notes,x.fname,x.lname,x.email,x.username,x.code2 from users x) o ";
			
		    //GetGlobal('controller')->calldpc_method("mygrid.column use grid1+code2|".localize('code',getlocal())."|5|0|||1");
		    GetGlobal('controller')->calldpc_method("mygrid.column use grid1+id|".localize('id',getlocal())."|5|0|||1");
		    //GetGlobal('controller')->calldpc_method("mygrid.column use grid1+notes|".localize('_active',getlocal())."|boolean|1|ACTIVE:DELETED");		   
		    GetGlobal('controller')->calldpc_method("mygrid.column use grid1+notes|".localize('_active',getlocal())."|link|0|".seturl('t=cpusractiv&editmode=1&rec={id}').'||');
		    //GetGlobal('controller')->calldpc_method("mygrid.column use grid1+fname|".localize('_fname',getlocal())."|20|1|");
		    GetGlobal('controller')->calldpc_method("mygrid.column use grid1+fname|".localize('_fname',getlocal())."|link|20|".seturl('t=upduser&editmode=1&rec={id}').'||');
		    GetGlobal('controller')->calldpc_method("mygrid.column use grid1+lname|".localize('_lname',getlocal())."|20|1|");
		    GetGlobal('controller')->calldpc_method("mygrid.column use grid1+username|".localize('_username',getlocal())."|20|0|");
		   
		    $out .= GetGlobal('controller')->calldpc_method("mygrid.grid use grid1+users+$xsSQL+r+".localize('RCUSERSa_DPC',getlocal())."+id+1+1+20+400");
		   //}
	   }*/			
	   
	   return ($out);	
	
	}		

	function show_mail() {
       $sFormErr = GetGlobal('sFormErr');
	   $sendto = GetReq('m');

	   if (defined('ABCMAIL_DPC')) {
	     $ret = $sFormErr;
	     $ret .= GetGlobal('controller')->calldpc_method('abcmail.create_mail use cpcusmsend+'.$sendto);
	   }

	   return ($ret);
	}

	function send_mail($from=null, $to=null, $subject=null, $body=null) {

	   if (!defined('RCSSYSTEM_DPC')) return;

	   $from = $from ? $from : GetParam('from');
	   $to = $to ? $to : GetParam('to');
	   $subject = $subject ? $subject : GetParam('subject');
	   $body = $body ? $body : GetParam('mail_text');

	   if ($res = GetGlobal('controller')->calldpc_method('rcssystem.sendit use '.$from.'+'.$to.'+'.$subject.'+'.$body)) {
	     $this->mailmsg = "Send successfull";
		 return true;
	   }	 
	   else {
	     $this->mailmsg = "Send failed";
		 return false;
	   }
	}

    function searchinbrowser() {
            $ret = "
           <form name=\"searchinbrowser\" method=\"post\" action=\"\">
           <input name=\"filter\" type=\"Text\" value=\"\" size=\"56\" maxlength=\"64\">
           <input name=\"Image\" type=\"Image\" src=\"../images/b_go.gif\" alt=\"\"    align=\"absmiddle\" width=\"22\" height=\"28\" hspace=\"10\" border=\"0\">
           </form>";

          $ret .= "<br>Last search: " . GetParam('filter');

          return ($ret);
    }
		
	function getpage($array,$id){
	
	   if (count($array)>0) {
         //while(list ($num, $data) = each ($array)) {
         foreach ($array as $num => $data) {
		    $msplit = explode(";",$data);
			if ($msplit[1]==$id) return floor(($num+1) / $this->pagenum)+1;
		 }	  
		 
		 return 1;
	   }	 
	}		
		
    function browse($packdata,$view) {
	
	   $data = explode("||",$packdata); //print_r($data);
	
       $out = $this->viewusrs($data[0],$data[1],$data[2],$data[3],$data[4],$data[5],$data[6],$data[7],$data[8],$data[9]);

	   return ($out);
	}				
		
    function viewusrs($i,$id,$notes,$fname,$lname,$email,$username,$code1,$code2) {
	   $p = GetReq('p');
	   $a = GetReq('a');
	   
	   $del_link = seturl("t=deluser&rec=$id&editmode=1" , $i);
	   $name_link = seturl("t=upduser&rec=$id&editmode=1" , $fname. '&nbsp;'.$lname);								  	   
	   $email_link = seturl("t=cpupdateadv&rec=$id&editmode=1" , $email);	
	   $activ_link = seturl("t=cpusractiv&rec=$id&editmode=1" , $notes);	   			  
	   	   
	   $data[] = $del_link?$del_link:'&nbsp;';   
	   $attr[] = "left;10%";	   
	   
	   $data[] = $activ_link?$activ_link:'&nbsp;';   
	   $attr[] = "left;15%";   
	   
	   $data[] = $name_link?$name_link:'&nbsp;';   
	   $attr[] = "left;25%";	      
	   
	   $data[] = $email_link?$email_link:'&nbsp;'; /*. '/' . $dtime*/;   
	   $attr[] = "left;25%";	
	   
	   $data[] = $username?$username:'&nbsp;';   
	   $attr[] = "left;25%";		   
	   
	   
	   $myarticle = new window('',$data,$attr);
       $line = $myarticle->render();//"center::100%::0::group_dir_body::left::0::0::");
	   unset ($data);
	   unset ($attr);
	   
       if ($this->details) {//disable cancel and delete form buttons due to form elements in details????
	     $mydata = $line . '<br/>' . $this->details($id);
	     $cartwin = new window2($id . '/' . $status,$mydata,null,1,null,'HIDE',null,1);
	     $out = $cartwin->render();//"center::100%::0::group_article_body::left::0::0::"
	     unset ($cartwin);		   
	   }	
	   else {   
		 $out .= $line ;//. '<hr>';
	   }	   
	   

	   return ($out);
	}			
		
	function headtitle() {
	   $p = GetReq('p');
	   $t = GetReq('t')?GetReq('t'):'cpusers';
	   $sort = GetReq('sort')>0?-1:1; 
	   
	   if (GetReq('editmode'))
	     $edmode = '&editmode=1';
	   else
	     $edmode = null; 
	
       $data[] = seturl("t=$t&a=&g=1&p=$p&sort=$sort&col=id".$edmode ,  "A/A" );
	   $attr[] = "left;10%";							  
	   $data[] = seturl("t=$t&a=&g=2&p=$p&sort=$sort&col=notes".$edmode , localize('_active',getlocal()) );
	   $attr[] = "left;15%";
	   $data[] = seturl("t=$t&a=&g=3&p=$p&sort=$sort&col=fname".$edmode , localize('_fname',getlocal()) );
	   $attr[] = "left;25%";
	   $data[] = seturl("t=$t&a=&g=4&p=$p&sort=$sort&col=email".$edmode , localize('_email',getlocal()) );
	   $attr[] = "left;25%";
	   $data[] = seturl("t=$t&a=&g=5&p=$p&sort=$sort&col=username".$edmode , localize('_username',getlocal()) );
	   $attr[] = "left;25%";	   

  	   $mytitle = new window('',$data,$attr);
	   $out = $mytitle->render(" ::100%::0::group_form_headtitle::center;100%;::");
	   unset ($data);
	   unset ($attr);	
	   
	   return ($out);
	}
	
	function user_form() {
	  global $config;	
      $db = GetGlobal('db');	
	
	   if (GetReq('editmode')) {//default form colors	

	     $config['FORM']['element_bgcolor1'] = 'EEEEEE';
	     $config['FORM']['element_bgcolor2'] = 'DDDDDD';	
		   
         $sSQL = "select id from users ";
	     $sSQL .= " WHERE id='" . GetReq('rec') . "'";	
	     //echo $sSQL;
	  
	     $resultset = $db->Execute($sSQL,2);	
		 //print_r($resultset->fields);
		 $id = $resultset->fields['id']	;  
		 
	     GetGlobal('controller')->calldpc_method('dataforms.setform use myform+myform+5+5+50+100+0+0');
	     GetGlobal('controller')->calldpc_method('dataforms.setformadv use 0+0+50+10');
		 GetGlobal('controller')->calldpc_method('dataforms.setformgoto use DPCLINK:cpusers:OK');
	     GetGlobal('controller')->calldpc_method('dataforms.setformtemplate use cpupdateadvok');	   
	   
         $fields = "code1,code2,ageid,clogon,cntryid,email,fname,genid,lanid,lastlogon,lname,notes,seclevid,sesdata" .
                   ",startdate,subscribe,username,password,vpass,timezone";		   
				 
	     $farr = explode(',',$fields);
	     foreach ($farr as $t)
	       $title[] = localize($t,getlocal());
	       $titles = implode(',',$title);			 	 					
	     }	 	
		 
	     $out .= GetGlobal('controller')->calldpc_method("dataforms.getform use update.users+dataformsupdate+Post+Clear+$fields+$titles++id=$id+dummy");	  
	   
         return ($out);		 
	}
	
	function activate_user() {
	     $db = GetGlobal('db');	
		 $id = GetReq('rec');
		 
		 $sSQL = "update users set notes='ACTIVE' where id = " . $id;
		 //echo $sSQL;		 
         $db->Execute($sSQL);
         if($db->Affected_Rows()) {
		   SetGlobal('sFormErr',"ok");
		   return ($id);
		 }  
	     else {
		   SetGlobal('sFormErr',localize('_MSG18',getlocal()));			 
		   return false;
		 }  
	}
	
	function deactivate_user() {
	     $db = GetGlobal('db');	
		 $id = GetReq('rec');
		 
		 $sSQL = "update users set notes='DELETED' where id = " . $id;
		 //echo $sSQL;		 
         $db->Execute($sSQL);
         if($db->Affected_Rows()) {
		   SetGlobal('sFormErr',"ok");
		   return ($id);
		 }		 
	     else {
		   SetGlobal('sFormErr',localize('_MSG18',getlocal()));			 
		   return false;
		 }  
	}		
	
	function is_activated_user() {
	     $db = GetGlobal('db');	
		 $id = GetReq('rec');
		 
		 $sSQL = "select notes from users where id = " . $id;
		 //echo $sSQL;		 
         $result = $db->Execute($sSQL,2);
		 
		 $notes = $result->fields['notes'];
		 if (substr($notes,0,7)=='DELETED')
		   return false;
		 else
		   return true;  
	
	}
	
	function fetch_user_data($id, $fields=null) {
	     $db = GetGlobal('db');	
		 if ((!$id) || (!$fields)) return false;
		 
		 if (stristr($fields,'::')) {
		   $mfa = explode('::',$fields);//array of fields
		   $mf = str_replace('::',',',$fields);
		 }  
		 else {
           $mfa = $fields; //one element		 
		   $mf = $fields;
		 }
		 
		 
		 $sSQL = "select $mf from users where id = " . $id;
		 //echo $sSQL;		 
         $result = $db->Execute($sSQL,2);
		 
		 if (is_array($mfa)) {
		   foreach ($mfa as $i=>$f)
		     $ret[$f] = $result->fields[$f];
		 }
		 else
		   $ret = $result->fields[$mfa];
		 
         //echo $ret;
         //echo print_r($ret);
		 
		 return ($ret);  
	
	}	
	
	function activate_deactivate() {
	
	   if ($this->is_activated_user()) {
	   
	     $uid = $this->deactivate_user();
		 
		 if (($uid) && ($this->tell_deactivate)) {	 
		    $user_email = $this->fetch_user_data($uid,'email');
			$this->send_mail($this->tell_it, $user_email,$this->subj_deactivate,$this->body_deactivate);
		 }		 
	   }	 
	   else {
	   
	     $uid = $this->activate_user();	 
		 
		 if (($uid) && ($this->tell_activate)) {
		    $user_email = $this->fetch_user_data($uid,'email');
			$this->send_mail($this->tell_it, $user_email,$this->subj_activate,$this->body_activate);		 
		 }
	   }	 
	}
	
};
}
?>
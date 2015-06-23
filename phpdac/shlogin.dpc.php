<?php
$__DPCSEC['SHLOGIN_DPC']='1;1;1;1;1;1;1;1;1';

if ((!defined("SHLOGIN_DPC")) && (seclevel('SHLOGIN_DPC',decode(GetSessionParam('UserSecID')))) ) {
define("SHLOGIN_DPC",true);

$__DPC['SHLOGIN_DPC'] = 'shlogin';

$a = GetGlobal('controller')->require_dpc('nitobi/nitobi.lib.php');
require_once($a);

$__EVENTS['SHLOGIN_DPC'][0]='shlogin';
$__EVENTS['SHLOGIN_DPC'][1]='dologin';
$__EVENTS['SHLOGIN_DPC'][2]='dologout';
$__EVENTS['SHLOGIN_DPC'][3]='rempwd';
$__EVENTS['SHLOGIN_DPC'][4]='shremember';
$__EVENTS['SHLOGIN_DPC'][5]='shcaptcha';
$__EVENTS['SHLOGIN_DPC'][6]='chpass';

$__ACTIONS['SHLOGIN_DPC'][0]='shlogin';
$__ACTIONS['SHLOGIN_DPC'][1]='dologin';
$__ACTIONS['SHLOGIN_DPC'][2]='dologout';
$__ACTIONS['SHLOGIN_DPC'][3]='rempwd';
$__ACTIONS['SHLOGIN_DPC'][4]='shremember';
$__ACTIONS['SHLOGIN_DPC'][5]='shcaptcha';
$__ACTIONS['SHLOGIN_DPC'][6]='chpass';

$__DPCATTR['SHLOGIN_DPC']['shlogin'] = 'shlogin,1,0,0,0,0,0,0,0,0,0,0,1';

$__LOCALE['SHLOGIN_DPC'][0]='SHLOGIN_DPC;Login;Εισαγωγή';
$__LOCALE['SHLOGIN_DPC'][1]='_SHLOGOUT;Logout;Αποσύνδεση';
$__LOCALE['SHLOGIN_DPC'][2]='SHLOGIN_CNF;Logout;Αποσύνδεση';
$__LOCALE['SHLOGIN_DPC'][3]='SHLOGIN_UNK;Login;Εισαγωγή';
$__LOCALE['SHLOGIN_DPC'][4]='_USERNAME;Username;Χρήστης';
$__LOCALE['SHLOGIN_DPC'][5]='_PASSWORD;Password;Κωδικός';
$__LOCALE['SHLOGIN_DPC'][6]='_WELLCOME;Wellcome;Καλωσήρθες';
$__LOCALE['SHLOGIN_DPC'][7]='_SEEYOU;See you next time;Τα λέμε';
$__LOCALE['SHLOGIN_DPC'][8]='_MSG1;Incorect data!;Λάθος στοιχεία!';
$__LOCALE['SHLOGIN_DPC'][9]='_VERPASS;Verify password;Επαληθευση κωδικου';
$__LOCALE['SHLOGIN_DPC'][10]='_PASSREMINDER;Please change your password!;Παρακαλω αλλαξτε τον κωδικό σας!';
$__LOCALE['SHLOGIN_DPC'][11]='_VERPASSUCCESS;Password changed!;Επιτυχης αλλαγη κωδικου';
$__LOCALE['SHLOGIN_DPC'][12]='_HERE;here;εδώ';
$__LOCALE['SHLOGIN_DPC'][13]="_IFORGET;If you dont remember your password click ;Αν δεν θυμαστε τον κωδικο σας πατηστε";
$__LOCALE['SHLOGIN_DPC'][14]='_PRESSHERE;Click here;Πατήστε εδώ';
$__LOCALE['SHLOGIN_DPC'][15]='_MSG2;Username and Password send at your mail account!;Το όνομα χρήστη και ο κωδικός στάλθηκαν στο ηλεκτρονικό σας ταχυδρομείο!';
$__LOCALE['SHLOGIN_DPC'][16]='_SENDCRE;Username and Password send at your mail account!;Το όνομα χρήστη και ο κωδικός στάλθηκαν στο ηλεκτρονικό σας ταχυδρομείο!';
$__LOCALE['SHLOGIN_DPC'][17]='_UMAILREMSUBC;Account reset;Αλλαγή κωδικού χρήστη';
$__LOCALE['SHLOGIN_DPC'][18]='_OK;Success;Επιτυχης εργασια';
$__LOCALE['SHLOGIN_DPC'][19]='_OKREMINDER;Your account details has been send by e-mail.;Τα στοιχεία του λογαριασμού σας σταλθηκσν με e-mail.';
$__LOCALE['SHLOGIN_DPC'][20]='_RESET;Reset;Αλλαγή';
$__LOCALE['SHLOGIN_DPC'][21]='_RESETPASS;Reset password;Αλλαγή κωδικού';
$__LOCALE['SHLOGIN_DPC'][22]="_MSG21;Password and verify password doesn't match!;Η επιβαιβαιωση κωδικου δεν συμφωνει με τον κωδικο σας.";
$__LOCALE['SHLOGIN_DPC'][23]='_MSGPWD;Invalid password format, 8 characters required;Μη αποδεκτός κωδικός, 8 χαρακτήρες τουλάχιστον είναι απαραίτητοι';
$__LOCALE['SHLOGIN_DPC'][24]='ok;An mail send to you. Follow the instruction in order to complete the process;Ένα email σταλθηκε στον λογαριασμό ηλ. ταχυδρομείου που δηλώσατε. Ακολουθήστε τις οδηγίες για την ολοκληρωση της διαδικασίας';
$__LOCALE['SHLOGIN_DPC'][25]='_ok;Submit;Εντάξει';
$__LOCALE['SHLOGIN_DPC'][26]='ok2;Password changed;Ο κωδικός άλλαξε';
$__LOCALE['SHLOGIN_DPC'][27]='_ERRSECTOKEN;Invalid token;Λάνθασμένο στοιχείο';
$__LOCALE['SHLOGIN_DPC'][28]='_NOTAFFECTED;Record not affected;Δεν έγινε η αλλαγή';

//cpmdbrec commands
$__LOCALE['SHLOGIN_DPC'][80]='_exit;Exit;Έξοδος';
$__LOCALE['SHLOGIN_DPC'][81]='_dashboard;Dashboard;Πίνακας ελέγχου';
$__LOCALE['SHLOGIN_DPC'][82]='_logout;Logout;Αποσύνδεση';
$__LOCALE['SHLOGIN_DPC'][83]='_rssfeeds;RSS Feeds;RSS Feeds';
$__LOCALE['SHLOGIN_DPC'][84]='_edititemtext;Edit Item Text;Μεταβολή κειμένου';// (text) αντικειμένου';
$__LOCALE['SHLOGIN_DPC'][85]='_deleteitemattachment;Delete Item Attachment;Διαγραφή συνημμένου';// είδους';
$__LOCALE['SHLOGIN_DPC'][90]='_editcat;Edit Category;Μεταβολή κατηγορίας';
$__LOCALE['SHLOGIN_DPC'][91]='_addcat;Add Category;Νέα Κατηγορία';
$__LOCALE['SHLOGIN_DPC'][92]='_additem;Add Item;Νέο Είδος';
$__LOCALE['SHLOGIN_DPC'][93]='_webstatistics;Statistics;Στατιστικά';
$__LOCALE['SHLOGIN_DPC'][94]='_addcathtml;Add Category Html;Προσθήκη κειμένου';// κατηγορίας';
$__LOCALE['SHLOGIN_DPC'][95]='_editcathtml;Edit Category Html;Μεταβολή κειμένου';// κατηγορίας';
$__LOCALE['SHLOGIN_DPC'][96]='_edititem;Edit Item;Μεταβολή είδους';// αντικειμένου';
$__LOCALE['SHLOGIN_DPC'][97]='_edititemphoto;Edit Photo;Μεταβολή φωτογραφίας';// αντικειμένου';
$__LOCALE['SHLOGIN_DPC'][98]='_edititemdbhtm;Edit Item Htm;Μεταβολή κειμένου';// (htm) αντικειμένου (db)';
$__LOCALE['SHLOGIN_DPC'][99]='_edititemdbhtml;Edit Item Html;Μεταβολή κειμένου';// (html) αντικειμένου (db)';
$__LOCALE['SHLOGIN_DPC'][100]='_edititemdbtext;Edit Item Text;Μεταβολή κειμένου';// (text) αντικειμένου (db)';
$__LOCALE['SHLOGIN_DPC'][101]='_senditemmail;Send Text/Html e-mail;Αποστολή e-mail';
$__LOCALE['SHLOGIN_DPC'][102]='_delitemattachment;Delete Text;Διαγραφή κειμένου';// (db)';
$__LOCALE['SHLOGIN_DPC'][103]='_edititemtext;Edit Item Text;Μεταβολή κειμένου';// (text) αντικειμένου';
$__LOCALE['SHLOGIN_DPC'][104]='_edititemhtm;Edit Item Htm;Μεταβολή κειμένου';// (htm) αντικειμένου';
$__LOCALE['SHLOGIN_DPC'][105]='_edititemhtml;Edit Item Html;Μεταβολή κειμένου';// (html) αντικειμένου';
$__LOCALE['SHLOGIN_DPC'][106]='_additemhtml;Add Item Html;Εισαγωγή κειμένου';// στο αντικείμενο';
$__LOCALE['SHLOGIN_DPC'][107]='_transactions;Transactions;Συναλλαγές';
$__LOCALE['SHLOGIN_DPC'][108]='_users;Users;Χρήστες';
$__LOCALE['SHLOGIN_DPC'][109]='_itemattachments2db;Add Item(s) to Database;Μεταφορά κειμένων στην Β.Δ.';//βάση δεδομένων';
$__LOCALE['SHLOGIN_DPC'][110]='_importdb;Import Database;Εισαγωγή βάσης δεδομένων';
$__LOCALE['SHLOGIN_DPC'][111]='_config;Configuration;Ρυθμίσεις';
$__LOCALE['SHLOGIN_DPC'][112]='_contactform;Contact Form;Φόρμα επικοινωνίας';
$__LOCALE['SHLOGIN_DPC'][113]='_subscribers;Subscribers;Συνδρομητές';
$__LOCALE['SHLOGIN_DPC'][114]='_sitemap;Sitemap;Χάρτης πλοήγησης';// αντικειμένων';
$__LOCALE['SHLOGIN_DPC'][115]='_search;Search;Φόρμα Αναζήτησης';
$__LOCALE['SHLOGIN_DPC'][116]='_upload;Upload files;Ανέβασμα αρχείων';
$__LOCALE['SHLOGIN_DPC'][117]='_uploadid;Upload item files;Ανέβασμα αρχείων';// αντικειμένου';
$__LOCALE['SHLOGIN_DPC'][118]='_uploadcat;Upload category files;Ανέβασμα αρχείων';// κατηγορίας';
$__LOCALE['SHLOGIN_DPC'][119]='_syncphoto;Sync photos;Συγχρονισμός φωτογραφιών';
$__LOCALE['SHLOGIN_DPC'][120]='_syncsql;Sync data;Συγχρονισμός δεδομένων';
$__LOCALE['SHLOGIN_DPC'][121]='_dbphoto;Image in DB;Εικόνα στην βάση δεδομένων';
$__LOCALE['SHLOGIN_DPC'][122]='_editctag;Category Tags;Tags κατηγορίας';
$__LOCALE['SHLOGIN_DPC'][123]='_edititag;Item Tags;Tags είδους';
$__LOCALE['SHLOGIN_DPC'][124]='_menu;Menu;Επιλογές Menu';
$__LOCALE['SHLOGIN_DPC'][125]='_slideshow;Slideshow;Επιλογές Slideshow';
$__LOCALE['SHLOGIN_DPC'][126]='_ckfinder;Upload files;Upload αρχείων';
$__LOCALE['SHLOGIN_DPC'][127]='_webmail;Web Mail;Web Mail';
$__LOCALE['SHLOGIN_DPC'][128]='_editpage;Edit Page;Επεξεργασία σελίδας';
$__LOCALE['SHLOGIN_DPC'][129]='_rempass;Forgotten password;Υπενθύμιση κωδικού';
$__LOCALE['SHLOGIN_DPC'][130]='_chpass;Change password;Αλλαγή κωδικού';
$__LOCALE['SHLOGIN_DPC'][131]='_cphelp;Ηelp;Βοήθεια';
$__LOCALE['SHLOGIN_DPC'][132]='_cpupgrade;Upgrade;Αναβάθμιση';
$__LOCALE['SHLOGIN_DPC'][133]='_cpwizard;Enable wizard;Οδηγός εγκατάστασης';
$__LOCALE['SHLOGIN_DPC'][134]='_cpdhtmlon;Windows mode;Πλοήγηση Windows';
$__LOCALE['SHLOGIN_DPC'][135]='_cpdhtmloff;Frames mode;Πλοήγηση Frames';
$__LOCALE['SHLOGIN_DPC'][136]='_cpcropwiz;Crop wizard;Crop wizard';
$__LOCALE['SHLOGIN_DPC'][137]='_OPTIONS;Options;Επιλογές';
$__LOCALE['SHLOGIN_DPC'][138]='_ADD;Add;Προσθήκη';
$__LOCALE['SHLOGIN_DPC'][139]='_CATEGORY;Category;Κατηγορία';
$__LOCALE['SHLOGIN_DPC'][140]='_ITEM;Item;Είδος';
$__LOCALE['SHLOGIN_DPC'][141]='_SETTINGS;Settings;Ρυθμίσεις';
$__LOCALE['SHLOGIN_DPC'][142]='_customers;Customers;Πελάτες';
$__LOCALE['SHLOGIN_DPC'][143]='_EDITHTML;Edit Html;Σελίδες Html';
$__LOCALE['SHLOGIN_DPC'][144]='_SELECTHTML;Select Html;Επιλογή Html';
$__LOCALE['SHLOGIN_DPC'][145]='_ADDFAST;Add item;Εισαγωγή είδους';
$__LOCALE['SHLOGIN_DPC'][146]='_addtag;Add Tag;Εισαγωγή Ετικέτας';
$__LOCALE['SHLOGIN_DPC'][147]='_back;Back;Επιστροφή';

class shlogin {

	var $userLevelID;
	var $msg;
	var $outpoint;
	var $sslscript;
	var $ses_prothema;
	var $ses_path;
	var $ssl;
    var $time_of_session;
	var $reseller_attr, $path;
	var $username, $userid;
    var $actcode;
    var $iname, $load_sesssion;
    var $after_goto, $dpc_after_goto,$login_successfull;
	var $login_goto,$logout_goto;  
	var $urlpath, $inpath;
	var $recaptcha_public_key, $recaptcha_private_key;	
	
	static $staticpath;

	function shlogin() {
	   $sFormErr = GetGlobal('sFormErr');
	   $UserName = GetGlobal('UserName');
	   $UserSecID = GetGlobal('UserSecID');
	   $GRX = GetGlobal('GRX');
	   $this->username = decode($UserName);
	   $this->userid = decode($UserID);

       $this->userLevelID = (((decode($UserSecID))) ? (decode($UserSecID)) : 0);
	   $this->msg = $sFormErr;
	   $this->ssl = paramload('SHELL','ssl');
	   $this->outpoint = "|";
	   
	   self::$staticpath = paramload('SHELL','urlpath');

	   if ($remoteuser=GetSessionParam('REMOTELOGIN'))
	     $this->path = paramload('SHELL','prpath')."instances/$remoteuser/";
	   else
	     $this->path = paramload('SHELL','prpath');
		 
	   $this->urlpath = paramload('SHELL','urlpath');
	   $this->inpath = paramload('ID','hostinpath');			 

	   $this->sslscript = "<script src=\"".paramload('SHLOGIN','sslscript')."\"></script>";
	   $this->ses_prothema = paramload('SHLOGIN','sespro');
	   $this->ses_path = paramload('SHELL','sespath');

       $this->must_reenter_password	= null;

	   $this->link = seturl("t=rempwd",localize("_HERE",getlocal()));
	   $this->message = localize("_IFORGET",getlocal());
	   $this->formerror = null;

	   $this->title = localize('SHLOGIN_DPC',getlocal());
       $logintime = remote_paramload('SHLOGIN','logintime',$this->path);
	   $this->time_of_session = $logintime?$logintime:3600;//1 hour is default
	   
	   $this->reseller_attr = remote_paramload('SHLOGIN','reseller',$this->path); //DISABLED
	   
	   $this->accountmailfrom = remote_paramload('SHLOGIN','accountmailfrom',$this->path);

	   $acode = remote_paramload('RCCUSTOMERS','activecode',$this->path);
	   $this->actcode = $acode?$acode:'code2';

	   $this->iname = paramload('ID','instancename');
	   $this->load_session = remote_paramload('SHLOGIN','loadsession',$this->path);

	   $this->after_goto = remote_paramload('SHLOGIN','aftergoto',$this->path);
	   $this->dpc_after_goto = GetSessionParam('afterlogingoto')?GetSessionParam('afterlogingoto'):$this->after_goto;
	   $this->login_successfull = false;
	   
	   $this->logout_goto = remote_paramload('SHLOGIN','logout_goto',$this->path);
	   $this->login_goto = remote_paramload('SHLOGIN','login_goto',$this->path);	
	   
	   $this->recaptcha_public_key = remote_paramload('RECAPTCHA','pubkey',$this->path);							  
	   $this->recaptcha_private_key = remote_paramload('RECAPTCHA','privkey',$this->path);	      
	   
       //timezone	   
       date_default_timezone_set('Europe/Athens');		   
	}

    function event($sAction) {
	   $__USERAGENT = GetGlobal('__USERAGENT');
	   $UserName = GetGlobal('UserName');
	   $param1 = GetGlobal('param1');
	   $param2 = GetGlobal('param2');
	   

       if (!$this->msg) {

         switch($sAction)   {
		    case 'rempwd'       : break;
		    case 'shremember'  : $this->do_the_job(); break;
		    case 'shcaptcha'   : $this->do_the_captcha(); break;
            case 'shlogin'     : break;

            case "dologin":  
			               switch ($__USERAGENT) {
	                          case 'HTML' :  $this->login_successfull = $this->do_login();
                                             if (($this->login_goto)&& ($this->login_successfull)) {
							                    if (!$this->dpc_after_goto)// inside code command
			                                      $this->refresh_page_js($this->login_goto);	
											 }	
																		  
							                 if (defined('UONLINE_DPC'))
											   GetGlobal('controller')->calldpc_method('uonline.isOnline');
											 break;
	                          case 'XML'  :
	                          case 'GTK'  :
							  case 'XUL'  :
		                      case 'CLI'  :
	                          case 'TEXT' :  $this->do_login($param1,$param2);
							                 break;
						   }
						   //reset database connection
						   $db = null;
                           break;

            case "dologout":  
			                switch ($__USERAGENT) {
	                          case 'HTML' : if (defined('UONLINE_DPC'))
    	                                      GetGlobal('controller')->calldpc_method('uonline.isOffline');
											  
                                            $this->do_logout();
											
                                            if ($this->logout_goto)
			                                  $this->refresh_page_js($this->logout_goto); 											
                                            break;
	                          case 'XML'  :
	                          case 'GTK'  :
							  case 'XUL'  :
		                      case 'CLI'  :
	                          case 'TEXT' : $this->do_logout();
							                break;
						   }
						   //reset database connection
						   //$db = null;
                           break;

			 case "chpass" : //$this->js_password_meter();
			                 $this->do_reenter_pasword(); 
			                 break;
          }
       }
	}

    function action($action=null)  {
	    $__USERAGENT = GetGlobal('__USERAGENT');
	    $sFormErr = GetGlobal('sFormErr');

        switch($action)   {

		    case 'rempwd'       :
		    case 'shremember'  : //$out = $this->title();
			                     if ($this->formerror != localize("ok",getlocal())) {
								   $out .= $this->html_remform();	 
								 }	 
								 else {//login
								   //$tmpl = 'login.htm'; 
								   $out = $this->html_form();
								 }  
								 break;
		    case 'shcaptcha'   : $out .= $this->show_the_captcha(); break;
            case "shlogin": $out = $this->form(); break;

            case "dologin":
               //goto after login with ses param
               if (($this->dpc_after_goto) && ($this->login_successfull)) {
                 //echo $this->dpc_after_goto,'>';
			     $mydpc = explode('.',$this->dpc_after_goto);//check security
			     $mydpcname = strtoupper($mydpc[0]).'_DPC';				 
				 if (seclevel($mydpcname,$this->userLevelID)) 
                   $out .= GetGlobal('controller')->calldpc_method($this->dpc_after_goto);
				 else
				   $out .= $this->form();//default action  
                 $this->dpc_after_goto = null;
                 SetSessionParam('afterlogingoto','');
               }
               else 
			     $out = $this->form(); 
		       break;

            case "dologout": $out = $this->form(); 
			                 break;
			
		    case "chpass" :  $out = $this->html_reset_pass();
			                 break;
	           
		       
		}

		return ($out);
	}
	
    function js_password_meter() {
   
      if (iniload('JAVASCRIPT')) {
		 
		   $js = new jscript;
		   $js->load_css('javascripts/passwordmeter/ext-all.css');
		   $js->load_js('passwordmeter/ext-base.js');
		   $js->load_js('passwordmeter/ext-all.js');
		   $js->load_js('passwordmeter/Ext.ux.PasswordMeter.js');
		   $js->load_js('passwordmeter/passwordmeter.js');
		   $js->load_css('javascripts/passwordmeter/passwordmeter.css');
           $js->load_js($code,"",1);			   
		   unset ($js);
	  }   
    } 		
	
    function refresh_page_js($goto) {
   
      if (iniload('JAVASCRIPT')) {

	       $code = $this->javascript($goto);
	   
		   $js = new jscript;
           $js->load_js($code,"",1);			   
		   unset ($js);
	  }   
    } 
   
    //after login/logout goto...
    function javascript($goto) {
   
     $ret = "
function neu()
{	
	top.frames.location.href = \"$goto\"
}
window.setTimeout(\"neu()\",10);
";
	 
	 return ($ret);
    }   	

	function title() {
	   $UserID = GetGlobal('UserID');

       $uid = decode($UserID);

       //navigation status
       if ($uid == "")
  	     $out = setNavigator(localize('SHLOGIN_DPC',getlocal()));
	   else
         $out = setNavigator(localize('_SHLOGOUT',getlocal()));

	   return ($out);
	}

	function html_form($tokensout=null) {
	     $sFormErr = GetGlobal('sFormErr');
	     $UserID = GetGlobal('UserID');
         $logonurl = seturl("t=dologin",0,1);	
		 
		 if ($UserID) {
		 
           $t = $this->urlpath .'/' . $this->inpath . '/cp/html/'. str_replace('.',getlocal().'.','logout.htm') ; 
		   //echo $t;
	       if (is_readable($t)) {
		     $mytemplate = file_get_contents($t);
		     $tokensout = 1;
           }	
		   
           if ($tokensout) {
		     $tokens[] = seturl("t=dologout"); //link
			 $tokens[] = localize('_SHLOGOUT',getlocal());
			 $tokens[] = $this->myf_button(localize('_SHLOGOUT',getlocal()), seturl("t=dologout"));
			 
			 $ret = $this->combine_tokens($mytemplate,$tokens);

		   }
		   else	{	   		 
		     //$ret = '<h2><p align=center>' . seturl("t=dologout",localize('_SHLOGOUT',getlocal())) . '</p></h2>';
			 $ret = $this->myf_button(localize('_SHLOGOUT',getlocal()), seturl("t=dologout"));
		   }	 
			 	
		   return ($ret);
		 }
		 
		 //NOT FOR PASSWORDS FORM
	     /*if (defined('RECAPTCHA_DPC')) {
	         $recaptcha = recaptcha_get_html($this->recaptcha_public_key, $this->recaptcha_private_key);	   
			 //echo $recaptcha;
	     }	*/		 
		 
         $t = $this->urlpath .'/' . $this->inpath . '/cp/html/'. str_replace('.',getlocal().'.','login.htm') ; 
		 //echo $t;
	     if (is_readable($t)) {
		   $mytemplate = file_get_contents($t);
		   $tokensout = 1;
         }		 	 

	     //SSL tm
	     if ($this->ssl) {
		                      //$loginform .= $this->sslscript;
	                          $sslwin = new window("",$this->sslscript);
	                          $loginform .= $sslwin->render("center::100%::0::group_article_selected::right::0::0::");
	                          unset ($sslwin);
	     }

         if ($tokensout) {
		    if (($sFormErr=='ok')||($sFormErr=='Ok')||($sFormErr=='OK')||($sFormErr=='OKREMINDER')) {//fix ok global msg
			  if (stristr($sFormErr,'ok'))
			    $tokens[] = localize('_OK', getlocal());
			  else
			    $tokens[] = localize('_OKREMINDER', getlocal());
			}  
			else
		      $tokens[] = $sFormErr;
		 }
		 else {	 
		 
		    $loginform .= $toprint = $this->title();
						   		 
		    if (($sFormErr=='ok')||($sFormErr=='Ok')||($sFormErr=='OK')||($sFormErr=='OKREMINDER')) {//fix ok global msg
			  if (stristr($sFormErr,'ok'))
			    $loginform .= localize('_OK', getlocal());
			  else
			    $loginform .= localize('_OKREMINDER', getlocal());			
			}
			else  //error message
	          $loginform .= setError($sFormErr);
		 }				   
						   //$loginform .= new captcha;
        
         if ($tokensout) {
		 
		   $tokens[] = "<form action=\"$logonurl\" method=\"POST\">";
		   $tokens[] = "<input type=\"text\" name=\"Username\" maxlenght=\"20\" class=\"myf_input\"  onfocus=\"this.style.backgroundColor='#F5F5F5'\" onblur=\"this.style.backgroundColor='#FFFFFF'\" style=\"background-color: rgb(255, 255, 255); \">" ;
		 }
		 else { 
                           //login form
                           $loginform .= "<form action=\"$logonurl\" method=\"POST\">";
						   		 
                           $lf1[] = localize('_USERNAME',getlocal());
		                   $lfat1[] = "right;20%;";
		                   $lf1[] = "&nbsp;<input type=\"text\" name=\"Username\" maxlenght=\"20\">";
		                   $lfat1[] = "left;80%";
		                   $w1 = new window('',$lf1,$lfat1);  $loginform .= $w1->render("center::100%::0::group_article_selected::left::0::0::");   unset ($w1);
         } 		 
		 
         if ($tokensout) { 
		 
		   $tokens[] = "<input type=\"password\" name=\"Password\" maxlenght=\"20\" class=\"myf_input\"  onfocus=\"this.style.backgroundColor='#F5F5F5'\" onblur=\"this.style.backgroundColor='#FFFFFF'\" style=\"background-color: rgb(255, 255, 255); \">"; 	   
		   $tokens[] = "<input type=\"submit\" class=\"myf_button\" value=\"" . localize('SHLOGIN_DPC',getlocal()) . "\">";
		   
		   $tokens[] =	//$recaptcha . NOT FOR PASSWORDS FORM 
                       "<input type=\"hidden\" name=\"FormName\" value=\"Login\">" .					   
		               "<input type=\"hidden\" name=\"FormAction\" value=\"dologin\">" .
		               "</form>";	
		 }
		 else { 		 
                           $lf2[] = localize('_PASSWORD',getlocal());
		                   $lfat2[] = "right;20%";
		                   $lf2[] = "&nbsp;<input type=\"password\" name=\"Password\" maxlenght=\"20\">";
		                   $lfat2[] = "left;80%";
		                   $w2 = new window('',$lf2,$lfat2); $loginform .= $w2->render("center::100%::0::group_article_selected::left::0::0::"); unset ($w2);
						   
						   $loginform .= $recaptcha;
                           $loginform .= "<input type=\"hidden\" name=\"FormName\" value=\"Login\">";         
                           $loginform .= "<input type=\"hidden\" name=\"FormAction\" value=\"dologin\">";
                           $loginform .= "<input type=\"submit\" value=\"" . localize('SHLOGIN_DPC',getlocal()) . "\">";
                           $loginform .= "</form>";
		 } 				   

         if ($tokensout) {
		 
		   //i  forget my password....extension
		   $tokens[] = $this->iforgotmypassword();
		   
		   //return ($tokens);
		   $ret = $this->combine_tokens($mytemplate,$tokens);
		   return ($ret);
		 }
		 else {  		 
						   //i  forget my password....extension
						   $loginform .= $this->iforgotmypassword();

                           $data1[] = $loginform;
                           $attr1[] = "center";

	                       $swin = new window(localize('_SENLOGIN',getlocal()),$data1,$attr1);
	                       $toprint .= $swin->render("center::40%::0::group_win_body::left::0::0::");
	                       unset ($swin);

						   return ($toprint);
		 }				   
	}

    function form($agent='HTML') {
	   $sFormErr = GetGlobal('sFormErr');

       $uid = decode($UserID);

       $sFileName = seturl("t=dologin");
       $logonurl = seturl("",0,1); //seturl("t=&a=&g=",0,1);
       $logoffurl = seturl();
	   
	   //in case of preint form after login
       if ($this->login_successfull) {
	   
         $t = $this->urlpath .'/' . $this->inpath . '/cp/html/'. str_replace('.',getlocal().'.','welcome.htm') ; 
	     //echo $t;
	     if (is_readable($t)) {
		   $mytemplate = file_get_contents($t);
           return ($mytemplate);
         }
		 else		   
	       return (localize('_WELLCOME',getlocal()) .' '. decode(GetSessionParam('UserName')));
	   }	 

       //if ((!$this->userid) && (!$this->username)) {//!!!!!!!!!!!!!!!!!!???
	   if ($uid=="") {

	      switch ($agent) {
	         case 'HTML' : //template form
			               //$toprint = $this->title();
						   
			               $toprint .= $this->html_form();
		                   break;

		     case 'XML'  : $xml = new pxml();
			               $xml->addtag('TEXT',null,'/',"value=$sFormErr");
					       $xml->addtag('FORM',null,null,"action=".$xml->xstr($logonurl)."|method=POST|class=thin");
					       $xml->addtag('LABEL','FORM','/',"for=cmd|accesskey=C");
					       $xml->addtag('INPUT','FORM','/',"type=text|name=docomm|maxlength=255|value=|size=15|id=cmd");
					       $xml->addtag('INPUT','FORM','/',"type=submit|value=Ok");
					       $xml->addtag('HIDDEN','FORM','/',"name=FormAction|value=command");
					       $toprint = $xml->getxml();
					       unset($xml);
			               break;
			 case 'XUL'  :
		     case 'GTK'  : $xml = new pxml('XUL');
			               $xml->addtag('GTKDIALOG',null,null,'id=dialog1|label=Login|modal=true|winx=400|winy=150|border=10');
						   $xml->addtag('GTKLABEL','GTKDIALOG','/',"value=".$sFormErr);
						   $xml->addtag('GTKFORM','GTKDIALOG',null,null);
						   //$xml->addtag('description','GTKFORM','/',"value=".$sFormErr);
			               $xml->addtag('label','GTKFORM','/',"control=usr|value=".localize('_USERNAME',getlocal()));
					       $xml->addtag('textbox','GTKFORM','/',"id=Username|maxlength=64");
			               $xml->addtag('label','GTKFORM','/',"control=pwd|value=".localize('_PASSWORD',getlocal()));
					       $xml->addtag('textbox','GTKFORM','/',"id=Password|type=password|maxlength=64");
					       $xml->addtag('hidden','GTKFORM','/',"id=action|value=login");
					       $toprint = $xml->getxml();
					       unset($xml);
						   //die($toprint)
			               break;
		   }
        }
		else {

          //never in here ???????
		  if ($goto = GetPreSessionParam('afterlogingoto')) {
			 $mydpc = explode('.',$goto);//check security
			 $mydpcname = strtoupper($mydpc[0]).'_DPC';				 
			 if (seclevel($mydpcname,$this->userLevelID)) 	  
		       $toprint .= GetGlobal('controller')->calldpc_method($goto);
		  }
		}


        return ($toprint);
    }

    function quickform($attr=null,$after_goto=null,$dpc_after_goto=null,$param_name=null,$param=null) {
       $winattr = $attr?$attr:"center::100%::0::group_win_body::left::0::0::";
	   $UserID = GetGlobal('UserID');
       $uid = decode($UserID);
	   
       $t = $this->urlpath .'/' . $this->inpath . '/cp/html/'. str_replace('.',getlocal().'.','qlogin.htm') ; 
	   //echo $t;
	   if (is_readable($t)) {
		 $mytemplate = file_get_contents($t);
		 $tokensout = 1;
       }	   

       $this->after_goto = $after_goto?$after_goto:null;

       //$this->dpc_after_goto = $dpc_after_goto?str_replace('>','.',$dpc_after_goto):null;
       if ($dpc_after_goto) {
       	  SetSessionParam('afterlogingoto',str_replace('>','.',$dpc_after_goto));
       }
        //echo $dpc_after_goto;

        if ($this->after_goto) {
               	  $logonurl = seturl("t=".$this->after_goto."&$param_name=".$param,0,1);
               	  $this->after_goto = null;
        }
        else
                  $logonurl = seturl("",0,1); //seturl("t=$a=&g=",0,1);
				  
       $logoffurl = seturl('t=dologout',localize('_SHLOGOUT',getlocal()));

	   if (isset($UserID)) {
	     if ($tokensout) {
		   return null;
		 }
		 else {
	       $myw = new window($logoffurl,null);
	       $ret = $myw->render("center::100%::0::group_win_body::left::0::0::");
	       unset ($myw);
		   return($ret);
	       //return($logoffurl);
		 }
	   }

       if ($tokensout) {
	   
	     $tokens[] = "<form action=\"$logonurl\" method=\"POST\">";
		 $tokens[] = "<input type=\"text\" name=\"Username\" maxlenght=\"50\" size=\"20\" class=\"myf_input_front\"  onfocus=\"this.style.backgroundColor='#F5F5F5'\" onblur=\"this.style.backgroundColor='#FFFFFF'\" style=\"background-color: rgb(255, 255, 255); \">";		 
		 $tokens[] = "<input type=\"password\" name=\"Password\" maxlenght=\"50\" size=\"20\" class=\"myf_input_front\"  onfocus=\"this.style.backgroundColor='#F5F5F5'\" onblur=\"this.style.backgroundColor='#FFFFFF'\" style=\"background-color: rgb(255, 255, 255); \">";
		 $tokens[] = "<input type=\"submit\" class=\"myf_button\" value=\"" . localize('SHLOGIN_DPC',getlocal()) . "\">";
         $tokens[] = "<input type=\"hidden\" name=\"FormAction\" value=\"dologin\">" .
					 "<input type=\"hidden\" name=\"FormName\" value=\"Login\">" .
		             "</form>";
					 
		 $ret = $this->combine_tokens($mytemplate,$tokens);
		 return ($ret);					 
		             
	   }
	   else {
         $loginform = "<table>".
                    "<form action=\"$logonurl\" method=\"POST\">" .
                    "<tr><td>" .
                    localize('_USERNAME',getlocal()) . " :<br>" .
                    localize('_PASSWORD',getlocal()) . " :";

         if ( (defined('SHUSERS_DPC')) && (seclevel('SHUSERS_DPC',decode(GetSessionParam('UserSecID')))) ) {
           $loginform .= '<br>'.seturl('t=signup',localize('_SIGNUP',getlocal()));//GetGlobal('controller')->calldpc_method('shcustomers.register');
	     }
         $loginform .= "</td><td>" .
                       "<input type=\"hidden\" name=\"FormName\" value=\"Login\"><br>" .
                       "<input type=\"text\" name=\"Username\" maxlenght=\"50\" size=\"20\" ><br>".
                       "<input type=\"password\" name=\"Password\" maxlenght=\"50\" size=\"20\" ><br>" .
                       "<input type=\"hidden\" name=\"FormAction\" value=\"dologin\">" .
                       "<input type=\"submit\" value=\"" . localize('SHLOGIN_DPC',getlocal()) . "\">" .
                       "</td></tr></form></table>";
					   
         $toprint .= $loginform;

	     $myw = new window($this->title,$toprint);
	     $out = $myw->render("$winattr");
	     unset ($myw);

         return ($out);
	   }	 
    }
	
	function isoldpass($username) {
	   $db = GetGlobal('db');
	   $sSQL = "select password from users where username='".$username."'";
	   $result = $db->Execute($sSQL,2);
	   $p = $result->fields['password'];
	   
	   if (($p) && (strlen($p)<=10)) //user exist and pass <=10 chars
	      return true;
	
       return false;	
	}	

    function do_login($user='',$pwd='',$editmode=null) {
	   $db = GetGlobal('db');
	   $sFormErr = GetGlobal('sFormErr');
	   $UserName = GetGlobal('UserName');
	   
	   //recaptcha NOT FOR PASSWORDS FORM 
       //if ($this->valid_recaptcha()) {		   
	   //if ($db) {   

       if (!$user) $sUsername = GetParam("Username", adText);
	          else $sUsername = $user;
       if (!$pwd) $sPassword = GetParam("Password", adText);
	         else $sPassword = $pwd;

	   if (($sUsername) && ($sPassword)) {

	      if ($this->isoldpass($sUsername)) {//old way, old users with unencoded pass
	        $sSQL = "SELECT ".$this->actcode.", sesid, notes, seclevid, clogon FROM users" . " WHERE username ='" .
		            addslashes($sUsername) . "' AND password='" . addslashes($sPassword) . "'";
		  }
          else {
			$sSQL = "SELECT ".$this->actcode.", sesid, notes, seclevid, clogon FROM users" . " WHERE username ='" .
					addslashes($sUsername) . "' AND password='" . md5(addslashes($sPassword)) . "'";		  
          }
		  
		  if ($editmode)
            //$sSQL .= " and seclevid>=9";
			$sSQL .= " and seclevid>=5"; //9 only replaced by 5>
          else 			
            $sSQL .= " and seclevid<=1"; //NOT ALLOW BACKEND CP USER TO LOGIN INTO FRONTEND
		  //echo "login:",$sSQL;

          $result = $db->Execute($sSQL,2);
		  //print_r($result->fields);

          if (($result->fields[$this->actcode]) && (strcmp(trim($result->fields['notes']),"DELETED")!=0)) {
		  
		                     if ($editmode) {
		                       SetSessionParam('LOGIN','yes');	
		                       //SetSessionParam('USER',encode($sUsername));
		                       SetSessionParam('ADMIN','yes');	
							   SetSessionParam('ADMINSecID',$result->fields['seclevid']);
							   SetSessionParam("UserName", $sUsername);
							   //post for instant value for cpmdbrec login
                               //$_POST['ADMINSecID'] = $result->fields['seclevid'];
							   //$GLOBALS['ADMINSecID']= $result->fields['seclevid'];
                               return true;							   
							 }  

                             if ($this->load_session)
							  $this->loadSession($sUsername);

                              SetSessionParam("UserName", encode($sUsername));
							  SetSessionParam("Password", encode($sPassword));//!!!!!
                              SetSessionParam("UserID", encode($result->fields[$this->actcode]));
							  $GLOBALS['UserID']=encode($result->fields[$this->actcode]);
                              SetSessionParam("UserSecID", encode($result->fields['seclevid']));
							  
							  //$this->is_reseller(); //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
							  if ((defined('SHCUSTOMERS_DPC'))) {	  
	                              //GetGlobal('controller')->calldpc_var('shcustomers.reseller');							  
	                              GetGlobal('controller')->calldpc_method('shcustomers.is_reseller');	
	                          }	

			                  //re-enter password flag
			                  if ($result->fields['clogon']==1) {
							    $this->must_reenter_password=1;
								$chpass = seturl("t=chpass",localize('_PASSREMINDER',getlocal()),1,'',1);
								setInfo($chpass);
							  }
							  else
  		                        setInfo(localize('_WELLCOME',getlocal()) . " " . $sUsername);

							  //set cookie
							  if (paramload('SHELL','cookies')) {
							    setcookie("cuser",$UserName,time()+$this->time_of_session);//,time()+3600,"","panikidis.gr",0);
								setcookie("csess",session_id(),time()+$this->time_of_session);
							  }
							  //echo "login:",$sSQL;
							  return true;
           }
           else {
		                      //setInfo(localize('_MSG1',getlocal()));
                              //$sFormErr = localize('_MSG1',getlocal());
							  SetGlobal('sFormErr',localize('_MSG1',getlocal()));
							  return false;
           }
	   }
	   //}//db	   
	   //}//recaptcha
	   
	   return false;
	}

    function do_logout() {
      $UserName = GetGlobal('UserName');

      $un  = decode($UserName);
      $this->saveSession();
      //print "save session";

      setInfo(localize('_SEEYOU',getlocal()) . " $un ...");

	  //zero cookie
	  if (paramload('SHELL','cookies')) {
	    setcookie("cuser","");//,time()+3600,"",$this->iname,0);
		setcookie("csess","");
	  }
	}
	
	function getUserName() {
	  $UserName = GetGlobal('UserName');	
	  
	  $ret = decode($UserName);
	  
	  return ($ret);
	}

    function do_reenter_pasword($myusername=null) {
	   $db = GetGlobal('db');
	   $sFormErr = GetGlobal('sFormErr');
	   $UserName = GetGlobal('UserName');

	   if ($id = GetReq('sectoken')) {//by mail link
		   $toks = explode('|',base64_decode(urldecode($id)));
		   $currentuser = $toks[0]; 
	   } 			   
	   else
	       $currentuser = $myusername ? $myusername : decode($UserName);
	   
	   if (!$currentuser) return false;
	   
	   $pwd = GetParam("Password");
	   $pwd2 = GetParam("vPassword");
       
	   if ($this->valid_recaptcha()) {
	   if (($pwd!=null) && ($pwd2!=null)) {

	     if  ((strcmp($pwd,$pwd2)==0)) {
		 
		   //extra checks
	       if ((!is_numeric($pwd)) && (strlen($pwd)>=8)) {

            $sSQL = "UPDATE users set " .
                  "password='" . md5(addslashes($pwd))  . "'," .
                  "vpass='" . md5(addslashes($pwd2))  . "'," .
				  "clogon=0";

	        if (!$a) 
		     $sSQL .= " WHERE username ='" . $currentuser . "'";
	        else 
		     $sSQL .= " WHERE ".$this->actcode." =" . $a;

	        //echo $sSQL;

            $db->Execute($sSQL,1);
            if($db->Affected_Rows()) 
		     $this->formerror = localize('ok2',getlocal());//"ok2";
	        else 
		     $this->formerror = localize('_NOTAFFECTED',getlocal());
			 
            SetGlobal('sFormErr',$this->formerror);
		   }
		   else {
		    $this->formerror = localize('_MSGPWD',getlocal());
		    SetGlobal('sFormErr',$this->formerror);	 	   
		   } 		   
         }
		 else {
		   $this->formerror = localize('_MSG21',getlocal());
		   SetGlobal('sFormErr',$this->formerror);
		 }  
	   }
	   }//recaptcha
	}
	
    function form_reset_pass($tokensout=null,$username=null) {
	   $sectoken = GetReq('sectoken') ? '&sectoken='.GetReq('sectoken') : null;
       $url = seturl("t=chpass".$sectoken,0,1);
		  
	   //if ((!$currentuser) || ($this->formerror!=localize('ok2',getlocal()))) {
	   
	     if (defined('RECAPTCHA_DPC')) {
	         $recaptcha = recaptcha_get_html($this->recaptcha_public_key, $this->recaptcha_private_key);	   
	     }	
		 if ($tokensout) {
		   $tokens[] = $this->formerror;
		 }
		 else
           $loginform .= $this->formerror;
		   
		 if ($tokensout) {
		   $tokens[] = "<form action=\"$url\" method=\"POST\">";
		   $tokens[] = "<input type=\"password\" autocomplete=\"off\" name=\"Password\" maxlenght=\"50\" size=\"20\" class=\"myf_input\"  onfocus=\"this.style.backgroundColor='#F5F5F5'\" onblur=\"this.style.backgroundColor='#FFFFFF'\" style=\"background-color: rgb(255, 255, 255); \">";
		   $tokens[] = "<input type=\"password\" autocomplete=\"off\" name=\"vPassword\" maxlenght=\"50\" size=\"20\" class=\"myf_input\"  onfocus=\"this.style.backgroundColor='#F5F5F5'\" onblur=\"this.style.backgroundColor='#FFFFFF'\" style=\"background-color: rgb(255, 255, 255); \">";		   
		   $tokens[] = $recaptcha;		   
		   $tokens[] = "<input type=\"submit\" class=\"myf_button\" value=\"" . localize('_RESET',getlocal()) . "\">";

		   $tokens[] = "<input type=\"hidden\" name=\"FormAction\" value=\"chpass\">" .
		               "<input type=\"hidden\" name=\"username\" value=\"$username\">" .
		               "<input type=\"hidden\" name=\"FormName\" value=\"UserChPass\">".
					   "</form>";				   
		 }
		 else {		   
           $loginform .= "<form action=\"$url\" method=\"POST\">";
           $loginform .= "<input type=\"hidden\" name=\"FormName\" value=\"RemLogin\">";
           //$loginform .= localize('_USERNAME',getlocal()) . " :<br><input type=\"text\" name=\"myusername\" maxlenght=\"50\" size=\"12\" ><br>";
           $lf0[] = localize('_PASSWORD',getlocal());
		   $lfat0[] = "right;40%;";
		   $lf0[] = "&nbsp;<input type=\"password\" autocomplete=\"off\" name=\"password\" maxlenght=\"20\" size=\"20\" >";
		   $lfat0[] = "left;60%";
		   $w0 = new window('',$lf0,$lfat0);  $loginform .= $w0->render("center::100%::0::group_article_selected::left::0::0::");   unset ($w0);
		  
           //$loginform .= localize('_MAIL',getlocal()) . " :<br><input type=\"text\" name=\"myemail\" maxlenght=\"50\" size=\"12\" ><br>";
           $lf1[] = localize('_VERPASS',getlocal());
	       $lfat1[] = "right;40%;";
		   $lf1[] = "&nbsp;<input type=\"password\" autocomplete=\"off\" name=\"VPassword\" maxlenght=\"50\" size=\"20\" >";
		   $lfat1[] = "left;60%";
		   $w1 = new window('',$lf1,$lfat1);  $loginform .= $w1->render("center::100%::0::group_article_selected::left::0::0::");   unset ($w1);

		   $loginform .= $recaptcha;
		  
           $loginform .= "<input type=\"hidden\" name=\"FormAction\" value=\"chpass\">";
		   $loginform .= "<input type=\"hidden\" name=\"username\" value=\"$username\">";
           $loginform .= "<input type=\"submit\" value=\"" . localize('_RESET',getlocal()) . "\">";
           $loginform .= "</form>";
      
           $toprint .= $loginform;

           $swin = new window(localize('_RESETPASS',getlocal()),$toprint);
           $out .= $swin->render();//"center::40%::0::group_win_body::left::0::0::");
	       unset ($swin);
		 }//tokens		   
		 
	     if ($tokensout) 
	       return ($tokens); 		 
       //}
		  
	   return ($out);
    }	
	
	function html_reset_pass($editmode=null) {
	   $UserName = GetGlobal('UserName');
	   
	   if ($id = base64_decode(urldecode(GetReq('sectoken')))) {//by mail link
		   $toks = explode('|',$id);
		   $timestamp = time(); 
		   if (is_numeric($toks[1]) && (($timestamp-(intval($toks[1])))<3600)) {//timestamp < 1 hour
		     $currentuser = $toks[0]; 
		   } 
		   else {//timestamp is invalid	 
		     $currentuser = null;
			 $this->formerror = localize('_ERRSECTOKEN',getlocal());
			 SetGlobal('sFormErr',$this->formerror);
		   }	 
		   //echo $timestamp,intval($toks[1]),'>',$currentuser;	   
	   } 			   
	   elseif ($UserName) {	   
	      $currentuser = decode($UserName);	
	   }	  
	   else
          $currentuser = null;	   

	   if (($currentuser) && ($this->formerror!=localize('ok2',getlocal()))) {
   		$toks = $this->form_reset_pass(1, $currentuser);
		
		$mydata = str_replace('+','<@>',implode('<TOKENS>',$toks));
		
		if (!$ret = GetGlobal('controller')->calldpc_method("fronthtmlpage.subpage use userchpass.htm+".$mydata)) 
		  $ret = $this->form_reset_pass(null,$currentuser);
		  
	   }	  
	   else {//login
	      if (!$editmode)
			$ret = $this->html_form(); 
	   }	  
		  
	   return ($ret);  
	}			

	function saveSession() {
	   $db = GetGlobal('db');
	   $UserName = GetGlobal('UserName');
	   //$db = GetGlobal('db');
	   
	   if ($db) {

		$currentses = session_id();
		$currentuser = decode($UserName);
		$session_data = str_replace("\"","<@>",session_encode());
		//echo '>',$session_data;
		//save ses id
		$sSQL = "UPDATE users set sesid='" . $currentses .
                    "',sesdata='" . $session_data .
		       "' WHERE username ='" . $currentuser . "'" ;

		//echo $sSQL;

		$db->Execute($sSQL,1);
       }
	   
	   //unregister all selected session params
	   ResetSessionParams();


       //session_write_close();

       session_unset();

	   //session_destroy();
	}

	function loadSession($uname) {
	 $db = GetGlobal('db');


	   $sSQL = "select sesdata from users where username='" . $uname ."'" ;
       $res = $db->Execute($sSQL,2);//null,1);
	   //print_r($res->fields);
	   //echo $sSQL;
	   //echo '<',$db->model,'>';

	  /* if ($db->model=='ADODB')
	     $ret = $res->fields[0];
	   else
	     $ret = $res[0];	*/

	 //if ($ret) {
	   //echo '>',$res->fields[0];
	   session_decode(str_replace("<@>","\"",$res->fields[0]));
	 //}
     //echo '>';
     //print_r(GetSessionParam('storebuffer'));
    }

	function is_reseller($leeid=null) {
	   $db = GetGlobal('db');

	   if ($leeid!=null)
	     $id = $leeid;
	   else
	     $id = decode(GetSessionParam('UserID'));

	   //if is in cuatomers table then....
	   if ($id) {

	     $sSQL = "select attr1 from customers where ".$this->actcode."=" . $id;
		 //echo $sSQL;
         $result = $db->Execute($sSQL,2);
		 //echo $result->fields[0];

		 if ($result->fields[0]==$this->reseller_attr) {//'ΧΟΝΔΡΙΚΗ') {
		   //echo 'yes';
		   SetSessionParam('RESELLER','true');
		   return true;
		 }
	   }

	   return false;
	}

	function iforgotmypassword() {

	    $ret = $this->message . "&nbsp;" . $this->link;

		return ($ret);
	}

    function remform($tokensout=null) {

       $url = seturl("t=shremember",0,1);

  	   if ($this->formerror!=localize("ok", getlocal())) {//"ok") {
	   
	     if (defined('RECAPTCHA_DPC')) {
	         $recaptcha = recaptcha_get_html($this->recaptcha_public_key, $this->recaptcha_private_key);	   
	     }		   

		 if ($this->ssl) {
		    //$loginform .= $this->sslscript;
	        $sslwin = new window("",$this->sslscript);
	        $loginform .= $sslwin->render("center::100%::0::group_article_selected::right::0::0::");
	        unset ($sslwin);
		 }
         
		 if ($tokensout) {
		   $tokens[] = $this->formerror;
		 }
		 else
           $loginform .= $this->formerror;

		 if ($tokensout) {
		   $tokens[] = "<form action=\"$url\" method=\"POST\">";
		   $tokens[] = "<input type=\"text\" autocomplete=\"off\" name=\"myemail\" maxlenght=\"50\" size=\"20\" class=\"myf_input\"  onfocus=\"this.style.backgroundColor='#F5F5F5'\" onblur=\"this.style.backgroundColor='#FFFFFF'\" style=\"background-color: rgb(255, 255, 255); \">";
		   $tokens[] = $recaptcha;		   
		   $tokens[] = "<input type=\"submit\" class=\"myf_button\" value=\"" . localize("_ok",getlocal()) . "\">";

		   $tokens[] = "<input type=\"hidden\" name=\"FormAction\" value=\"shremember\">" .
		               "<input type=\"hidden\" name=\"FormName\" value=\"RemLogin\">".
					   "</form>";
		 }
		 else {		   
         $loginform .= "<form action=\"$url\" method=\"POST\">";
         $loginform .= "<input type=\"hidden\" name=\"FormName\" value=\"RemLogin\">";

         //$loginform .= localize('_USERNAME',getlocal()) . " :<br><input type=\"text\" name=\"myusername\" maxlenght=\"50\" size=\"12\" ><br>";
        /* $lf0[] = localize('_USERNAME',getlocal());
		 $lfat0[] = "right;40%;";
		 $lf0[] = "&nbsp;<input type=\"text\" name=\"myusername\" maxlenght=\"20\" size=\"20\" >";
		 $lfat0[] = "left;60%";
		 $w0 = new window('',$lf0,$lfat0);  $loginform .= $w0->render("center::100%::0::group_article_selected::left::0::0::");   unset ($w0);
		*/
         //$loginform .= localize('_MAIL',getlocal()) . " :<br><input type=\"text\" name=\"myemail\" maxlenght=\"50\" size=\"12\" ><br>";
         $lf1[] = localize('_MAIL',getlocal());
	     $lfat1[] = "right;40%;";
		 $lf1[] = "&nbsp;<input type=\"text\" autocomplete=\"off\" name=\"myemail\" maxlenght=\"50\" size=\"20\" >";
		 $lfat1[] = "left;60%";
		 $w1 = new window('',$lf1,$lfat1);  $loginform .= $w1->render("center::100%::0::group_article_selected::left::0::0::");   unset ($w1);

		 $loginform .= $recaptcha;
		  
         $loginform .= "<input type=\"hidden\" name=\"FormAction\" value=\"shremember\">";
         $loginform .= "<input type=\"submit\" value=\"" . localize("_ok",getlocal()) . "\">";
         $loginform .= "</form>";
      
         $toprint .= $loginform;

         $swin = new window(localize('SHLOGIN_DPC',getlocal()),$toprint);
         $out .= $swin->render("center::40%::0::group_win_body::left::0::0::");
	     unset ($swin);
		 }//tokens
		 
	     if ($tokensout) 
	       return ($tokens); 
	     else	 
           return ($out);		 
	   }
	   else {
	     //echo 'OK';
		 if (defined(_CAPTCHA_)) {

           /*if (iniload('JAVASCRIPT')) {
  	            $plink = "<A href=\"" . seturl("t=slogin",'',1) . "\"";
	            //call javascript for opening a new browser win for the img
	            $params = seturl("t=sencaptcha&c=".encode($this->result),0,1) . ";captcha;scrollbars=yes,width=320,height=340;";
				$js = new jscript;
	            $plink .= calldpc_method('javascript.JS_function use js_openwin+'.$params);
                unset ($js);

	            $plink .= ">";
	       }
	       else
              $plink = "<A href=\"" . seturl("t=sencaptcha&c=".encode($this->result)) . ">";

		   $winout = $plink . localize('_PRESSHERE',getlocal()) . "</A>";
		   */
		   //no press here...just showit in the page.
		   //call the image
		   $uc = seturl("t=sencaptcha&c=".encode($this->result));
           $winout .= "<img src=\"$uc\">";

           $swin = new window(localize('SHLOGIN_DPC',getlocal()),$winout);
           $out .= $swin->render("center::40%::0::group_win_body::left::0::0::");
	       unset ($swin);
		 }
		 elseif (defined('SMTPMAIL_DPC')) {
		   //echo 'mailOK';		 
		   $msg = localize('_SENDCRE',getlocal());		   
		   
		   if ($tokensout) {
		     $tokens[] = $msg;
			 
			 //login tokens
		     $tokens[] = "<form action=\"$logonurl\" method=\"POST\">".
		               "<input type=\"text\" name=\"Username\" maxlenght=\"20\">".
					   "<input type=\"hidden\" name=\"FormName\" value=\"Login\">";	
		     $tokens[] = "<input type=\"password\" name=\"Password\" maxlenght=\"20\">".
		                 "<input type=\"hidden\" name=\"FormAction\" value=\"dologin\">";
					   
		     $tokens[] = "<input type=\"submit\" value=\"" . localize('SHLOGIN_DPC',getlocal()) . "\">".
					      "</form>";;					   		 
		   }
		   else {
             $swin = new window(localize('SHLOGIN_DPC',getlocal()),$msg);
             $out .= $swin->render("center::40%::0::group_win_body::left::0::0::");
	         unset ($swin);
		   }	 
		 }
	     else {
		   //echo 'textOK';
		   //text ver
		   $r = $this->result;
		   if ($tokensout) {
		     $tokens[] = $r;
		   }
		   else {		   
             $swin = new window(localize('SHLOGIN_DPC',getlocal()),$r);
             $out .= $swin->render("center::40%::0::group_win_body::left::0::0::");
	         unset ($swin);
		   }	 
		 }
		 
	     if ($tokensout) 
	       return ($tokens); 
	     else	 
           return ($out);			 
	   }

       //return ($out);
    }
	
	function html_remform() {
	
   		$toks = $this->remform(1);
		
		$mydata = str_replace('+','<@>',implode('<TOKENS>',$toks));
		
		if (!$ret = GetGlobal('controller')->calldpc_method("fronthtmlpage.subpage use remlogin.htm+".$mydata)) 
		  $ret = $this->remform();
		  
		return ($ret);  
	}

	function do_the_job() {
	   $db = GetGlobal('db');
	   $u = GetParam("myusername");
	   $m = GetParam("myemail");
	
       if ($this->valid_recaptcha($norecaptcha)) {		 

		if (($m)) {// && (!$u)) {//mail only -> send username and password
	       $sSQL = "SELECT username, password, notes FROM users WHERE " .
		           "email='" . addslashes($m) . "' and username is not null";//not for subscribers// and seclevid>0";

	       //echo "remember:",$sSQL;

           $result = $db->Execute($sSQL,2);
		   //print_r($result->fields);
           if (($u=$result->fields['username']) && ($p=$result->fields['password'])
		   /* &&
		       (strcmp(trim($result->fields['notes']),"DELETED")!=0)*/) {

	           //if ($this->tellit) {
			   $tokens[] = $u;
			   /*if ($this->isoldpass($sUsername)) //old way
			     $tokens[] = $p;
			   else	
				$tokens[] = base64_decode($p);*/
			   //md5 pass can't decoded, just send link to reset
               $tokens[] = null; 			   
				
			   $timestamp = time(); 
			   $sectoken = urlencode(base64_encode($u.'|'.$timestamp));
	           $reset_url = seturl('t=chpass&sectoken='.$sectoken);	
			   $tokens[] = $reset_url;	
				
		       $sd = str_replace('+','<@>',implode('<TOKENS>',$tokens));
		       if (!$mailbody = GetGlobal('controller')->calldpc_method("fronthtmlpage.subpage use userremind.htm+".$sd."+1")) {
		
			   
	             $mailcontent = "Account info:<br>" . $u . '/' . $p;

		         $template = paramload('SHELL','prpath') . "insertusrusr.tpl";
		         $mailbody = str_replace("##_LINK_##",$mailcontent,file_get_contents($template));
				 $this->result = $mailbody;
               }
			   
		       $from = $this->accountmailfrom;
	           $this->mailto($from,$m,localize('_UMAILREMSUBC',getlocal()),$mailbody);

			   $this->formerror = localize("ok", getlocal());//localize('_MSG2',getlocal()); //"ok";
		   }
		   else $this->formerror = localize('_MSG1',getlocal());
		 }
		 else
		   $this->formerror = localize('_MSG1',getlocal());
		   
		   
		 if ($this->formerror!='ok') {
		   SetGlobal('sFormErr',$this->formerror);		   
		 }
		 else {
		   //$msg = localize('_SENDCRE',getlocal());	
		   SetGlobal('sFormErr',"OKREMINDER");////$msg);
		 }
		 
	   }//recaptcha	    
		   
	}

	function do_the_captcha() {

		//$this->captcha = new captcha();//strlen($this->result),'jpeg',$this->result);
		//$captcha->sent_header();
		//echo $captcha;
	}

	function show_the_captcha() {

	       $result = decode(GetReq('c'));
		   $captcha = new captcha(strlen($result),'jpeg',$result);

		   die();
	}

	function is_logged_in() {

	      if (GetSessionParam('UserID'))
		    return true;

		  return false;
	}

	function mailto($from,$to,$subject=null,$body=null) {

		     if ((defined('SMTPMAIL_DPC')) &&
				 (seclevel('SMTPMAIL_DPC',$this->UserLevelID)) ) {
		       $smtpm = new smtpmail;
		       $smtpm->to = $to;
			   $smtpm->from = $from;
			   $smtpm->subject = $subject;
			   $smtpm->body = $body ;

			   $mailerror = $smtpm->smtpsend();
			   //echo '>',$mailerror;

			   unset($smtpm);

			   return ($mailerror);
			 }
	}
	
	function login_with_key($key=null,$code=null,$ischar=null) {
	  $db = GetGlobal('db');
		 	
	  if ($key) {
	  
	      $pk = explode("~",$key);

	      $sSQL = "SELECT ".$this->actcode.", sesid, notes, seclevid, clogon, username, password FROM users WHERE ";
		  $sSQL.= $code?$code:$this->actcode;
		  $sSQL.= "=";
		  $sSQL.= $ischar?"'".$pk[0]."'":$pk[0];
		  //echo $sSQL;

		  //echo "login:",$sSQL;

          $result = $db->Execute($sSQL,2);
		  //print_r($result->fields);
		  $hash = $pk[1];
		  $data = trim($result->fields['username']).":".trim($result->fields['password']);
		  $hash2cmp = md5($data);
		  
		  if (strcmp($hash,$hash2cmp)) {//echo 'zzzz2';
		  
          
             if (/*($result->fields[$this->actcode]>0) &&*////PROBLEM
		         (strcmp(trim($result->fields['notes']),"DELETED")!=0)) {//echo 'zzzz';

                             if ($this->load_session)
							  $this->loadSession($sUsername);

							  $_POST['UserName'] = encode($sUsername);
							  
                              SetSessionParam("UserName", encode($sUsername));
							  SetSessionParam("Password", encode($sPassword));//!!!!!
                              SetSessionParam("UserID", encode($result->fields[$this->actcode]));
							  $GLOBALS['UserID']=encode($result->fields[$this->actcode]);
                              SetSessionParam("UserSecID", encode($result->fields['seclevid']));
							  $this->is_reseller(); //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			                  //re-enter password flag
			                  if ($result->fields['clogon']==1) {
							    $this->must_reenter_password=1;
								$chpass = seturl("t=chpass",localize('_PASSREMINDER',getlocal()),1,'',1);
								setInfo($chpass);
							  }
							  else
  		                        setInfo(localize('_WELLCOME',getlocal()) . " " . $sUsername);

							  //set cookie
							  if (paramload('SHELL','cookies')) {
							    setcookie("cuser",$UserName,time()+$this->time_of_session);//,time()+3600,"","panikidis.gr",0);
								setcookie("csess",session_id(),time()+$this->time_of_session);
							  }
							  //echo "login:",$sSQL;
							  return (encode($sUsername));
             }
             else {
		                      //setInfo(localize('_MSG1',getlocal()));
                              //$sFormErr = localize('_MSG1',getlocal());
							  SetGlobal('sFormErr',localize('_MSG1',getlocal()));
							  return false;
             }  
		  }//hash validation	 
	  
	  }//is key
	  else
	    return false;
   }
   
	 function valid_recaptcha() {
	 
	      if (!defined('RECAPTCHA_DPC')) return true;
		  
		  //print_r($_POST);
		  
          if ($_POST["recaptcha_response_field"]) {
            $resp = recaptcha_check_answer ($this->recaptcha_private_key,
                                            $_SERVER["REMOTE_ADDR"],
                                            $_POST["recaptcha_challenge_field"],
                                            $_POST["recaptcha_response_field"]);
											
            //print_r($resp);
            if ($resp->is_valid) {
                $error = null;//echo "You got it!";
				$ret = true;
            } 
			else {
                # set the error code so that we can display it
                $error = $resp->error;
				$ret = false;
		        $msg = '<br>' . "Incorrect recaptcha entry!";				
            }
		  }
		  else {
		    $ret = false;
		    $msg = '<br>' . "Recaptcha entry required!";			  
		  }
		  //$sFormErr = $error;
		  //echo '>',$error,'-',$ret;
		  //SetGlobal('sFormErr',$this->msg);
		  
		  $this->formerror = $msg;//'recaptcha error';
		  SetGlobal('sFormErr',$msg);
		  
		  return ($ret);												
									 
     }     
   
	function combine_tokens($template_contents,$tokens) {
	
	    if (!is_array($tokens)) return;
		
		if (defined('FRONTHTMLPAGE_DPC')) {
		  $fp = new fronthtmlpage(null);
		  $ret = $fp->process_commands($template_contents);
		  unset ($fp);
          //$ret = GetGlobal('controller')->calldpc_method("fronthtmlpage.process_commands use ".$template_contents);		  		
		}		  		
		else
		  $ret = $template_contents;
		  
		//echo $ret;
	    foreach ($tokens as $i=>$tok) {
            //echo $tok,'<br>';
		    $ret = str_replace("$".$i,$tok,$ret);
	    }
		//clean unused token marks
		for ($x=$i;$x<10;$x++)
		  $ret = str_replace("$".$x,'',$ret);
		//echo $ret;
		return ($ret);
	}   

	public static function myf_button($title,$link=null,$image=null) {
	   //$browser = get_browser(null, true);
       //print_r($browser);	
       //echo $_SERVER['HTTP_USER_AGENT']; 
	   //echo '1';
	   $path = self::$staticpath;//$this->urlpath;//
	   
	   if (($image) && (is_readable($path."/images/".$image.".png"))) {
	      //echo 'a';
	      $imglink = "<a href=\"$link\" title='$title'><img src='images/".$image.".png'/></a>";
	   }
	   
	   if (preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT'])) { 
	      //echo 'ie';
		  $_b = $imglink ? $imglink : "[$title]";
		  $ret = "&nbsp;<a href=\"$link\">$_b</a>&nbsp;";
		  return ($ret);
	   }	
	   
	   if ($imglink)
	       return ($imglink);
	
       //else button	
	   if ($link)
	      $ret = "<a href=\"$link\">";
		  
	   $ret .= "<input type=\"button\" class=\"myf_button\" value=\"".$title."\" />";
	   
	   if ($link)
          $ret .= "</a>";	   
		  
	   return ($ret);
	}
	
	
 function parse_environment($save_session=false) {

    /*if ($ret = GetSessionParam('env')) {//<<<<<<<<<<<<<<<<<
	    return ($ret);
	} */   

	//$myenvfile = /*$this->prpath .*/ 'cp.ini';
	//$ini = @parse_ini_file($myenvfile ,false, INI_SCANNER_RAW);	
    $ini = @parse_ini_file("cp.ini");

	if (!$ini) die('Env error!');

	$adminsecid = GetSessionParam('ADMINSecID') ? GetSessionParam('ADMINSecID') : $GLOBALS['ADMINSecID'];
	$seclevid = ($adminsecid>1) ? intval($adminsecid)-1 : 1;//...not instant sec level read//9; //test
	//echo GetSessionParam('ADMINSecID'),'>',$adminsecid,':', $seclevid;	
	//print_r($ini); 
	foreach ($ini as $env=>$val) {
	    if (stristr($val,',')) {
		    $uenv = explode(',',$val);
			$ret[$env] = $uenv[$seclevid];  
		}
		else
		    $ret[$env] = $val;
	}

	if (($save_session) && (!GetSessionParam('env'))) //rccontrolpanel also reads and save
		SetSessionParam('env', $ret); 		
	
	return ($ret);
 }
 
 function panelmenu($rettokens=null) {
    global $template, $editpage;
	$lan = getlocal();
	$turl = $_GET['turl'];
	$location = '../' . urldecode(base64_decode($turl));
	$mylocation = str_replace('_&_', '_%26_',$location);
    $tokens = array();
	$stats = 'cgi-bin/awstats.php';
    $class = 'class=""';
	$csep =  remote_paramload('RCITEMS','csep',$this->path);
	$cseparator = $csep ? $csep : '^'; 
    $encoding = 'utf-8';

 	$environment = $this->parse_environment();//true); //@parse_ini_file("cp.ini");
    //print_r($environment);      
    $seclevid = GetSessionParam('ADMINSecID');
   
    if ($environment['DASHBOARD']==1) 
       $otokens[] = "<li><a $class href='cp.php?editmode=1'>".localize('_dashboard',$lan)."</a></li>"; 	
	 
	 if ($environment['AWSTATS']==1) 
	   $otokens[] = "<li><a $class href='$stats'>".localize('_webstatistics',$lan)."</a></li>";	

	 if ($environment['WEBMAIL']==1) {
	   $urlbase = remote_paramload('SHELL','urlbase',paramload('SHELL','prpath'));
       $otokens[] = "<li><a $class href='$urlbase/webmail/'>".localize('_webmail',$lan)."</a></li>";
	 }  
	 
     //if (stristr($editpage,'index.php')) {
	     if ($environment['MENU']==1) 
           $otokens[] = "<li><a $class href='cpmenu.php?t=cpmconfig&editmode=1&encoding=$encoding'>".localize('_menu',$lan)."</a></li>";

	     if ($environment['SLIDESHOW']==1) 
           $otokens[] = "<li><a $class href='cpslideshow.php?t=cpsconfig&editmode=1&encoding=$encoding'>".localize('_slideshow',$lan)."</a></li>";	 
     //}	 
	 
	 if ($environment['CONFIG']==1) {
		//echo $editpage,'>';	 
		//if (stristr($editpage,'index.php')) {
		$pparts = explode('.',$editpage);
		$config_section = strtoupper($pparts[0]);		 
		if ($config_section) {	  
           $otokens[] = "<li><a $class href='cpconfig.php?editmode=1&cpart=$config_section&encoding=$encoding'>".localize('_config',$lan)."</a></li>"; 
		}
     }//config...	
	  
	 
	 if (stristr($editpage,'contact.php')) {
	    if ($environment['CONTACT_FORM']==1) 
           $otokens[] = "<li><a $class href='cpform.php?t=cpform&editmode=1&encoding=$encoding'>".localize('_contactform',$lan)."</a></li>";
	 }	
	 if (stristr($editpage,'subscribe.php')) 
	    if ($environment['SUBSCRIBERS']==1) {
           $otokens[] = "<li><a $class href='cpsubscribers.php?editmode=1&encoding=$encoding'>".localize('_subscribers',$lan)."</a></li>";
	 }	
	 if (stristr($editpage,'sitemap.php')) {
	    if ($environment['SITEMAP']==1) {
		 	$pparts = explode('.',$editpage);
		    $config_section = strtoupper($pparts[0]);		 
			if ($config_section)  
				$otokens[] = "<li><a $class href='cpconfig.php?editmode=1&cpart=$config_section&encoding=$encoding'>".localize('_sitemap',$lan)."</a></li>";

		}   
	 }	
	 if (stristr($editpage,'search.php')) {
	    if ($environment['SEARCH']==1) 
           $otokens[] = "<li><a $class href='cpitems.php?t=cpattach2db&editmode=1&encoding=$encoding'>".localize('_search',$lan)."</a></li>";
	 }
	 //}//config
	 if ($environment['EDIT_CATEGORY']==1) { 
	       $cat = null;
           $otokens[] = "<li><a $class href='cpkategories.php?t=cpkategories&cat=$cat&editmode=1&encoding=$encoding'>".localize('_editcat',$lan)."</a></li>";
	 } 	 
     if ($environment['EDIT_ITEM']==1) {
           $v = null;
		   $otokens[] = "<li><a $class href='cpitems.php?t=cpitems&id=$v&editmode=1&encoding=$encoding'>".localize('_edititem',$lan)."</a></li>";
	 } 	 
	 if ($environment['USERS']==1) 
           $otokens[] = "<li><a $class href='cpusers.php?t=cpusers&editmode=1&encoding=$encoding'>".localize('_users',$lan)."</a></li>"; 

	 if ($environment['CUSTOMERS']==1) 
           $otokens[] = "<li><a $class href='cpcustomers.php?t=cpcustomers&editmode=1&encoding=$encoding'>".localize('_customers',$lan)."</a></li>"; 

	 if ($environment['TRANSACTIONS']==1) 
		   $otokens[] = "<li><a $class href='cptransactions.php?t=cptransactions&editmode=1&encoding=$encoding'>".localize('_transactions',$lan)."</a></li>"; 
 	 
	 if ($environment['ITEM_SENDMAIL']==1) 
		 $otokens[] = "<li><a $class href='cpsubscribers.php?encoding=$encoding&editmode=1'>".localize('_senditemmail',$lan)."</a></li>";	 		 

	 if ($environment['RESOURCES_UPLOAD']==1) 
		 $otokens[] = "<li><a $class href='cpupload.php?editmode=1&encoding=$encoding'>".localize('_upload',$lan)."</a></li>";	 		 

	 if ($environment['CKFINDER']==1) 
		 $otokens[] = "<li><a $class href='cpmckfinder.php'>".localize('_ckfinder',$lan)."</a></li>";	 		 
	
	 if ($environment['EDITHTML']==1) {
	    //$turl = array_shift(explode('?',urldecode(base64_decode($_GET['turl']))));//$_GET['turl'];
		$turl_file = str_replace('.php','.html',array_shift(explode('?',urldecode(base64_decode($_GET['turl'])))));
		//echo $turl_file,'..';
		$htmlfile = $_GET['htmlfile'] ? $_GET['htmlfile'] : urlencode(base64_encode($turl_file));
		if ($htmlfile) 
	       $otokens[] = "<li><a $class href='cpmhtmleditor.php?cke4=1&encoding=$encoding&editmode=1&htmlfile=$htmlfile'>".localize('_editpage',$lan)."</a></li>";
	 }  	 

     //win category.......................................................
	 if (!empty($otokens)) {
		    $li0 = '<li class="sub-menu">
			          <a href="javascript:;" class="">
                          <i class="icon-list"></i>
                          <span>'.localize('_OPTIONS',$lan).'</span>
                          <span class="arrow"></span>
                      </a>';
			$li1 = '</li>';
		    $tokens[] = $li0 . '<ul class="sub">'.implode('',$otokens).'</ul>' . $li1;	
	 }
	 
	 //........................................................... 
	/* if ($environment['EDITHTML']==1) {
	 
	    //$turl = array_shift(explode('?',urldecode(base64_decode($_GET['turl']))));//$_GET['turl'];
		$htmlfile = $_GET['htmlfile'];//str_replace('.php','.html',urlencode(base64_encode($turl)));
	    $tokens[] = "<a href='cpmhtmleditor.php?cke4=1&encoding=$encoding&editmode=1&htmlfile=$htmlfile'>".localize('_editpage',$lan)."</a></li>";
	 
		$turl = array_shift(explode('?',urldecode(base64_decode($_GET['turl']))));//$_GET['turl'];..exclude ?..editmode=-1
		$tokens[] = "<a href='cpmctrl.php?t=cptnewpage&editmode=1&turl=$turl'>".localize('_addpage',$lan)."</a></li>";	
		//$_icons[] = "<a href='cpmctrl.php?t=cptnewpage&editmode=1&turl=$turl'>".loadicon('/icons/'.'_addpage'.'.gif',localize('_addpage',$lan),null)."</a></li>";
        $tokens[] = "<a href='cpmctrl.php?t=cptnewcopy&editmode=1&turl=$turl'>".localize('_copypage',$lan)."</a></li>";			
	 
		//$tokens[] = dhtml_link('addpage', 'ajax', localize('_addpage',$lan), "cpmctrl.php?t=cptnewpage&editmode=1&turl=$turl"); 
	 
		//$tokens[] = "<a href='cpmhtmleditor.php?t=cptnewpage&editmode=1&turl=$turl'>".localize('_editpage',$lan)."</a></li>";	
		//$tokens[] = "<a href=\"skype:balexiou?chat\">Start chat</a></li>";
	 
		$htmlfile = GetReq('htmlfile');
		//$phpfile = GetReq('phpfile');//?GetReq('phpfile'):($mylocation?$mylocation:str_replace($lan.'.php','.php',$_GET['turl']));
		$preview = GetReq('turl');//urlencode(base64_encode($location));  	 
		$tokens[] = "<a href='cpmhtmleditor.php?htmlfile=$htmlfile&editmode=1&encoding=$encoding'>".localize('_editpage',$lan)."</a></li>";
		$tokens[] = "<a href='cpmhtmleditor.php?htmlfile=$htmlfile&editmode=1&encoding=$encoding&preview=$preview'>".localize('_previewpage',$lan)."</a></li>";		
		
 	 }*/
	 //............................................................

	 $new_elements = false;
	 
	 $qquery = str_replace('_&_', '_%26_', base64_decode($_GET['turl'])); //echo '>',$qquery,'>'; //& category problem
     $urlquery = parse_url($qquery); /*parse_url(base64_decode($_GET['turl']));*/ //echo $urlquery['query'];
     parse_str($urlquery['query'],$getp); //echo implode('.',$getp);  
	 
     foreach ($getp as $p=>$v) {
	   	 
       if (stristr($p,'cat')) {
	   
         $cat = urlencode($v);
		 
	     /*if ($environment['ADD_CATEGORY']==1) 
           $ntokens[] = "<li><a $class href='cpkategories.php?t=cpaddcat&cat=$cat&editmode=1'>".localize('_addcat',$lan)."</a></li>";	
         */
	     if ($environment['ADD_ITEM']==1) 
           $ntokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . "&encoding=$encoding&id=$cat&type=.html&editmode=1&insfast=1'>".localize('_ADDFAST',$lan)."</a></li>";	

         $new_elements = true; //if cat exist	

	     if ($environment['EDIT_CTAG']==1)  //add cat tags
           $ctokens[] = "<li><a $class href='cptags.php?t=cpeditctag&cat=$cat&editmode=1'>".localize('_editctag',$lan)."</a></li>";	
		 
		 if ($environment['EDIT_CATEGORY']==1) 
           $ctokens[] = "<li><a $class href='cpkategories.php?t=cpkategories&cat=$cat&editmode=1&encoding=$encoding'>".localize('_editcat',$lan)."</a></li>";

		 if ($environment['CATEGORY_UPLOAD']==1) 
		   $ctokens[] = "<li><a $class href='cpupload.php?cat=$cat&editmode=1&encoding=$encoding'>".localize('_uploadcat',$lan)."</a></li>";	 		 
 
	     if ($environment['SYNCPHOTO']==1) 	 
           $ctokens[] = "<li><a $class href='cpitems.php?t=cpvrestorephoto&cat=$cat&editmode=1&encoding=$encoding'>".localize('_syncphoto',$lan)."</a></li>"; 

	     if ($environment['DBPHOTO']==1) 	 
           $ctokens[] = "<li><a $class href='cpitems.php?t=cpvdbphoto&cat=$cat&editmode=1&encoding=$encoding'>".localize('_dbphoto',$lan)."</a></li>"; 
		 
		 /*if ($environment['RSS']==1) //rss for category  
           $ctokens[] = "<li><a $class href='cpxmlexp.php?editmode=1&cat=$cat&editmode=1&encoding=$encoding'>".localize('_rssfeeds',$lan)."</a></li>";	
		  */ 
		 if ($environment['RSS']==1) //rss for category  
           $ctokens[] = "<li><a $class href='cpitems.php?t=cpvitemrss&cat=$cat&editmode=1&encoding=$encoding'>".localize('_rssfeeds',$lan)."</a></li>";	
 	
		 if ($environment['ITEM_SENDMAIL']==1) //category send mail..advanced mail template system (rctedit,rctedititems)
		   $ctokens[] = "<li><a $class href='cpsubscribers.php?htmlfile=&encoding=$encoding&cat=$cat&editmode=1'>".localize('_senditemmail',$lan)."</a></li>";	 		 
 		 
	 
	     $mycurrentcat = explode($cseparator,$v);
	     $vn = array_pop($mycurrentcat);
	     $cat_htm_attachment = "html/". $vn . $lan . '.htm';		
	     $cat_html_attachment = "html/". $vn . $lan . '.html'; 
	   	 
		 if ($environment['CATEGORY_ATTACHMENT']==1) {
	       if (is_readable($cat_htm_attachment)) 
             $ctokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($cat_htm_attachment)) . "&encoding=$encoding&id=$vn&type=.htm&editmode=1'>".localize('_editcathtml',$lan)."</a></li>";	  		   
           elseif (is_readable($cat_html_attachment)) 
	         $ctokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($cat_html_attachment)) . "&encoding=$encoding&id=$vn&type=.html&editmode=1'>".localize('_editcathtml',$lan)."</a></li>";	  		   
	       else 
             $ctokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=&encoding=$encoding&id=$vn&type=.html&editmode=1'>".localize('_addcathtml',$lan)."</a></li>";			    
		 }//environment
		 
		 //win category.......................................................
		 if (!empty($ntokens)) {
		 	        $active = null;//$active_id ? null : ' active';
					$li0 = '<li class="sub-menu'.$active.'">
			          <a href="javascript:;" class="">
                          <i class="icon-plus"></i>
                          <span>'.localize('_ADD',$lan).'</span>
                          <span class="arrow"></span>
                      </a>';
			$li1 = '</li>';
		    $tokens[] = $li0 . '<ul class="sub">'.implode('',$ntokens).'</ul>' . $li1;		
		 }		 
		 if (!empty($ctokens)) {
            //$active = 'sub-menu active';//GetReq('id') ? null : (GetReq('cat') ? 'sub-menu active' : 'sub-menu');		 
		    $li0 = '<li class="sub-menu">
			          <a href="javascript:;" class="">
                          <i class="icon-stackexchange"></i>
                          <span>'.localize('_CATEGORY',$lan).'</span>
                          <span class="arrow"></span>
                      </a>';
			$li1 = '</li>';
		    $tokens[] = $li0 . '<ul class="sub">'.implode('',$ctokens).'</ul>' . $li1;	
		 }
       }//if	   
	   	 
       if (stristr($p,'id')) {
	     if ($environment['EDIT_ITAG']==1)  //add id tags
           $itokens[] = "<li><a $class href='cptags.php?t=cpedititag&id=$v&editmode=1&encoding=$encoding'>".localize('_edititag',$lan)."</a></li>";	
	   
         if ($environment['EDIT_ITEM']==1) 
		   $itokens[] = "<li><a $class href='cpitems.php?t=cpitems&id=$v&editmode=1&encoding=$encoding'>".localize('_edititem',$lan)."</a></li>";	
  
		 if ($environment['EDIT_ITEM_PHOTO']==1) 
	       $itokens[] = "<li><a $class href='cpitems.php?t=cpvphoto&id=$v&editmode=1&encoding=$encoding'>".localize('_edititemphoto',$lan)."</a></li>";
 
	     if ($environment['SYNCPHOTO']==1) 	 
           $itokens[] = "<li><a $class href='cpitems.php?t=cpvrestorephoto&id=$v&editmode=1&encoding=$encoding'>".localize('_syncphoto',$lan)."</a></li>"; 

	     if ($environment['DBPHOTO']==1) 	 
           $itokens[] = "<li><a $class href='cpitems.php?t=cpvdbphoto&id=$v&editmode=1&encoding=$encoding'>".localize('_dbphoto',$lan)."</a></li>"; 

		 if ($environment['RSS']==1) //rss for item  
           $itokens[] = "<li><a $class href='cpitems.php?t=cpvitemrss&id=$v&editmode=1&encoding=$encoding'>".localize('_rssfeeds',$lan)."</a></li>";	
		 
		 if ($environment['ITEM_UPLOAD']==1) 
		   $itokens[] = "<li><a $class href='cpupload.php?id=$v&editmode=1&encoding=$encoding'>".localize('_uploadid',$lan)."</a></li>";	 		 
			 

		 if ($environment['ITEM_ATTACHMENT']==1) {
	       $text_attachment = "html/". $v . $lan . '.txt';
	       $htm_attachment = "html/". $v . $lan . '.htm';		
	       $html_attachment = "html/". $v . $lan . '.html'; 
	       //echo $html_attachment,'>';	     
	   
           if ($attachment_type = GetGlobal('controller')->calldpc_method("rcitems.has_attachment2db use $v")) {
	         //echo '>',$attachment-type;
	         switch ($attachment_type) {
		       case '.html' :$itokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . "&encoding=$encoding&id=$v&type=$attachment_type&editmode=1'>".localize('_edititemdbhtml',$lan)."</a></li>"; 
		                     break;
						 
		       case '.htm'  :$itokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . "&encoding=$encoding&id=$v&type=$attachment_type&editmode=1'>".localize('_edititemdbhtm',$lan)."</a></li>";
			                 break;
						 
		       case '.txt ' :$itokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . "&encoding=$encoding&id=$v&type=$attachment_type&editmode=1'>".localize('_edititemdbtext',$lan)."</a></li>";	 
			                 break;			 
		       default      :$itokens[] = 'Unknown attachment type!<br>'; 					 						 						 
						     //$_icons[] = "";
		     }
			 
			 	 
			 if ($environment['ITEM_SENDMAIL']==1) 
		       $itokens[] = "<li><a $class href='cpsubscribers.php?htmlfile=&encoding=$encoding&id=$v&type=$attachment_type&editmode=1'>".localize('_senditemmail',$lan)."</a></li>";	 		 
 
			 if ($environment['ITEM_DELETE_DB_ATTACHMENT']==1)  
	           $itokens[] = "<li><a $class href='cpitems.php?t=cpvdelattach&id=$v&editmode=1&encoding=$encoding'>".localize('_deleteitemattachment',$lan)."</a></li>";		  
 
	       }
	       //else {	    
	       elseif (is_readable($text_attachment)) {
	           $itokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($text_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.txt'>".localize('_edititemtext',$lan)."</a></li>";	
			   if ($environment['ITEM_SENDMAIL']==1) 			 
					$itokens[] = "<li><a $class href='cpsubscribers.php?htmlfile=" . urlencode(base64_encode($text_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.txt'>".localize('_senditemmail',$lan)."</a></li>";	
		   }	 
	       elseif (is_readable($htm_attachment)) {
	           $itokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($htm_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.htm'>".localize('_edititemhtm',$lan)."</a></li>";
			   if ($environment['ITEM_SENDMAIL']==1) 			  
					$itokens[] = "<li><a $class href='cpsubscribers.php?htmlfile=" . urlencode(base64_encode($htm_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.htm'>".localize('_senditemmail',$lan)."</a></li>";		
		   }	 
           elseif (is_readable($html_attachment)) {
	           $itokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($html_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.html'>".localize('_edititemhtml',$lan)."</a></li>";	
			   if ($environment['ITEM_SENDMAIL']==1) 	  		   
					$itokens[] = "<li><a $class href='cpsubscribers.php?htmlfile=" . urlencode(base64_encode($html_attachment)) . "&encoding=$encoding&id=$v&editmode=1&type=.html'>".localize('_senditemmail',$lan)."</a></li>";			
		   }
	       else {//create nerw file	 
	           $new_attachment = "html/". $v . $lan . '.html';
               $itokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=&encoding=$encoding&id=$v&editmode=1&type=.html'>".localize('_additemhtml',$lan)."</a></li>";			  
			   if ($environment['ITEM_SENDMAIL']==1) //mail ..
		           $itokens[] = "<li><a $class href='cpsubscribers.php?htmlfile=&encoding=$encoding&id=$v&type=.html&editmode=1'>".localize('_senditemmail',$lan)."</a></li>";	 		 				   
	       }
	       //}//else
         }//environment
		 elseif ($environment['ITEM_SENDMAIL']==1) //only mail ...
		    $itokens[] = "<a href='cpsubscribers.php?htmlfile=&encoding=$encoding&id=$v&type=.html&editmode=1'>".localize('_senditemmail',$lan)."</a></li>";	 		 
 	
		 //win category.......................................................
		 if (!empty($itokens)) {
		    //$active = GetReq('id') ? 'sub-menu active' : 'sub-menu';
		    $li0 = '<li class="sub-menu active">
			          <a href="javascript:;" class="">
                          <i class="icon-file"></i>
                          <span>'.localize('_ITEM',$lan).'</span>
                          <span class="arrow"></span>
                      </a>';
			$li1 = '</li>';
		    $tokens[] = $li0 . '<ul class="sub">'.implode('',$itokens).'</ul>' . $li1;				 
		 }	
       }//if	
	      
       /*if ($v=='viewcart') {	
	     if ($environment['TRANSACTIONS']==1) {
		   //old way
           //$etokens[] = "<a href='cptransactions.php?t=cptransview&editmode=1&encoding=$encoding'>".localize('_transactions',$lan)."</a></li>";
		   //$_icons[] = "<a href='cptransactions.php?t=cptransview&editmode=1&encoding=$encoding'>".loadicon('/icons/'.'_transactions'.'.gif',localize('_transactions',$lan),null)."</a></li>";
		   //new way
		   $etokens[] = "<a href='cptransactions.php?t=cptransactions&editmode=1&encoding=$encoding'>".localize('_transactions',$lan)."</a></li>";
		   $_icons[] = "<a href='cptransactions.php?t=cptransactions&editmode=1&encoding=$encoding'>".loadicon('/icons/'.'_transactions'.'.gif',localize('_transactions',$lan),null)."</a></li>";		   
		 }  
	   }	 
		 
       if (($v=='signup') || ($v=='shlogin')) {	
	     if ($environment['USERS']==1) {
           $etokens[] = "<a href='cpusers.php?t=cpusers&editmode=1&encoding=$encoding'>".localize('_users',$lan)."</a></li>"; 
		   $_icons[] = "<a href='cpusers.php?t=cpusers&editmode=1&encoding=$encoding'>".loadicon('/icons/'.'_users'.'.gif',localize('_users',$lan),null)."</a></li>";
		 }  
	     if ($environment['CUSTOMERS']==1) {
           $etokens[] = "<a href='cpcustomers.php?t=cpcustomers&editmode=1&encoding=$encoding'>".localize('_customers',$lan)."</a></li>"; 
		   $_icons[] = "<a href='cpcustomers.php?t=cpcustomers&editmode=1&encoding=$encoding'>".loadicon('/icons/'.'_customers'.'.gif',localize('_customers',$lan),null)."</a></li>";
		 } 		 
		 if ($environment['SMS']==1) {
		   $etokens[] = "<a href='cpsmsgui.php?t=cpsmsgui&editmode=1&encoding=$encoding'>".localize('_sendsms',$lan)."</a></li>";   
		   $_icons[] = "<a href='cpsmsgui.php?t=cpsmsgui&editmode=1&encoding=$encoding'>".loadicon('/icons/'.'_sendsms'.'.gif',localize('_sendsms',$lan),null)."</a></li>";
		 }  
	   }
	   //extra category.......................................................
	   if (!empty($etokens)) {
	   
		$eitem_content = implode('<hr/>',$etokens);
		$ewinitem = new window2('Extras',$eitem_content,null,1,null,$winhide,null,1);
		$tokens[] = $ewinitem->render("center::100%::0::group_article_selected::left::0::0::");	
		unset ($ewinitem);			 
	   }*/	 //moved to standart options  
     }
 
     //....
	 if ($new_elements===false) { //cat not exist
       if ($environment['ADD_CATEGORY']==1) 
         $ntokens[] = "<li><a $class href='cpkategories.php?t=cpaddcat&editmode=1'>".localize('_addcat',$lan)."</a></li>";	
 
	   if ($environment['ADD_ITEM']==1)  
         $ntokens[] = "<li><a $class href='cpitems.php?t=cpvinput&editmode=1'>".localize('_additem',$lan)."</a></li>";	
 
	   if (($environment['EDIT_CTAG']==1) || ($environment['EDIT_ITAG']==1))   //add tags no cat/id list
         $ntokens[] = "<li><a $class href='cptags.php?t=cpeditctag&editmode=1'>".localize('_addtag',$lan)."</a></li>";	
	   
       //$tokens[] = '<hr>'; 
	   if (!empty($ntokens)) {
		    $li0 = '<li class="sub-menu">
			          <a href="javascript:;" class="">
                          <i class="icon-plus"></i>
                          <span>'.localize('_ADD',$lan).'</span>
                          <span class="arrow"></span>
                      </a>';
			$li1 = '</li>';
		    $tokens[] = $li0 . '<ul class="sub">'.implode('',$ntokens).'</ul>' . $li1;	
	   }	   
     }


     //if ((stristr($_SERVER['HTTP_REFERER'],'index.php')) || (stristr($_SERVER['HTTP_REFERER'],'katalog.php'))) {	 
	   if ($environment['ATTACH_FILES2DB']==1) 
         $stokens[] = "<li><a $class href='cpitems.php?t=cpattach2db&editmode=1'>".localize('_itemattachments2db',$lan)."</a></li>";
	 
	   /*if ($environment['RSS']==1) 	 
         $stokens[] = "<li><a $class href='cpxmlexp.php?editmode=1&cat=$v'>".localize('_rssfeeds',$lan)."</a></li>";
		*/	
	   if ($environment['RSS']==1) 	 
         $stokens[] = "<li><a $class href='cpitems.php?t=cpvitemrss&editmode=1&encoding=$encoding'>".localize('_rssfeeds',$lan)."</a></li>";
	   
	   if ($environment['IMPORTDB']==1) 
         $stokens[] = "<li><a $class href='cpimportdb.php?editmode=1&encoding=$encoding'>".localize('_importdb',$lan)."</a></li>";
	
	   if ($environment['SYNCPHOTO']==1) 	 
         $stokens[] = "<li><a $class href='cpitems.php?t=cpvrestorephoto&editmode=1&encoding=$encoding'>".localize('_syncphoto',$lan)."</a></li>"; 
	
	   if ($environment['DBPHOTO']==1) 	 
         $stokens[] = "<li><a $class href='cpitems.php?t=cpvdbphoto&editmode=1&encoding=$encoding'>".localize('_dbphoto',$lan)."</a></li>"; 
	   
	   if ($environment['SYNCSQL']==1) 	 
         $stokens[] = "<li><a $class href='cpsyncsql.php?editmode=1&encoding=$encoding'>".localize('_syncsql',$lan)."</a></li>"; 
		   
	   if ($environment['CONFIG']==1) 	 
         $stokens[] = "<li><a $class href='cpconfig.php?editmode=1&encoding=$encoding'>".localize('_config',$lan)."</a></li>"; 
	

     //$stokens[] =  "<li><a $class href=\"cpmdbrec.php?t=rempwd&editmode=1\">".localize('_rempass',$lan)."</a></li>";				    	  
	 //$stokens[] =  "<li><a $class href=\"cpmdbrec.php?t=chpass&editmode=1\"".localize('_chpass',$lan)."</a></li>";
	 $stokens[] =  "<li><a $class href=\"cpmdbrec.php?t=chpass&editmode=1\">".localize('_chpass',$lan)."</a></li>";
     //s$tokens[] =  "<li><a $class href=\"cpmdbrec.php?t=cphelp&editmode=1\">".localize('_cphelp',$lan)."</a></li>";		 	
	 /*if ($seclevid>=8) {
		$dhtml_var = remote_paramload('FRONTHTMLPAGE','dhtml',paramload('SHELL','prpath'));
		$dhtml_switch = $dhtml_var>0 ? '0' : '1';	 
		$modetitle = $dhtml_var>0 ? localize('_cpdhtmloff',$lan):localize('_cpdhtmlon',$lan);//'Win Mode OFF' : 'Win Mode ON';	
		$stokens[] =  "<li><a $class href=\"cpconfig.php?t=cpconfmod&var=fronthtmlpage.dhtml&val=$dhtml_switch&editmode=1\">".$modetitle."</a></li>";	
	    
	 }*/
	 if ($seclevid>=9) {
		$stokens[] =  "<li><a $class href=\"cpmwiz.php?t=cpwizreinit&editmode=1\">".localize('_cpwizard',$lan)."</a></li>";		 
		$stokens[] =  "<li><a $class href=\"cpmdbrec.php?t=cpupgrade&editmode=1\">".localize('_cpupgrade',$lan)."</a></li>";	 

		//already into ..._top
		//$turl = urldecode(decode($_GET['turl']));
		//$turl_m = urldecode(decode('AmMMaVlsVHcBPV5mUnUHdQF8U20AcAI9')); 
		//echo GetReq('turl'),']',$turl_m;
		$urlargs = explode('?',$location);
		//print_r($urlargs);
		$turl_m = urldecode(base64_decode($_GET['turl']));// . '&cropwiz=1'; //not reenter cp
		$modify_url = /*$location*/$urlargs[0] . "?modify=".urlencode(base64_encode('stereobit'))."&turl=".urlencode(encode($turl_m)).'&cropwiz=1'; 	
		$stokens[] =  "<li><a $class href=\"$modify_url\" target='_top'>".localize('_cpcropwiz',$lan)."</a></li>";
	 }
	 
     //win settings.......................................................
	 if (!empty($stokens)) {
		    $li0 = '<li class="sub-menu">
			          <a href="javascript:;" class="">
                          <i class="icon-cogs"></i>
                          <span>'.localize('_SETTINGS',$lan).'</span>
                          <span class="arrow"></span>
                      </a>';
			$li1 = '</li>';
		    $tokens[] = $li0 . '<ul class="sub">'.implode('',$stokens).'</ul>' . $li1;	
	 }
	 
	 /*template based menu*/
	 if (($template) /*&& ($environment['EDIT_HTMLFILES']==1)*/) {
	 
        //$my_current_page = GetGlobal('controller')->calldpc_var("fronthtmlpage.MC_CURRENT_PAGE");	
		$my_current_page = GetGlobal('controller')->calldpc_method("fronthtmlpage.mc_parse_editurl use $location");		
		$mc_current_page = str_replace(array('.php','../'),array('',''),$my_current_page);
		//echo $mc_current_page;
		
	    if ($environment['EDIT_HTMLFILES']==1) {
		  /*edit html*/
	      //$mc_pages = GetGlobal('controller')->calldpc_method("fronthtmlpage.mcPages use 1");
		  $mc_pages = GetGlobal('controller')->calldpc_method("fronthtmlpage.mc_read_files use pages+php++1");
		  foreach ($mc_pages as $mcpage=>$mctitle) {
		    $mc_page = urlencode(base64_encode($mcpage . '.php'));
		    $mc_title = ($mcpage==$mc_current_page) ?
		                 '<b>'.$mctitle.'</b>' : $mctitle;			
			$htokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" .$mc_page. "&encoding=$encoding&editmode=1'>".$mc_title."</a></li>";
		  }
	 
		  //win edit html.......................................................
		  if (!empty($htokens)) {
		    $li0 = '<li class="sub-menu">
			          <a href="javascript:;" class="">
                          <i class="icon-th"></i>
                          <span>'.localize('_EDITHTML',$lan).'</span>
                          <span class="arrow"></span>
                      </a>';
			$li1 = '</li>';
		    $tokens[] = $li0 . '<ul class="sub">'.implode('',$htokens).'</ul>' . $li1;	
		  }	
        }

		if ($environment['SELECT_HTMLFILES']==1) {
		  /*select html*/					
	      //$mc_pages = GetGlobal('controller')->calldpc_method("fronthtmlpage.mcPages use 1");
		  $mc_pages = GetGlobal('controller')->calldpc_method("fronthtmlpage.mc_read_files use pages+php++1");
		  foreach ($mc_pages as $mcpage=>$mctitle) {
		    $mc_page = urlencode(base64_encode($mcpage . '.php'));
		    $mc_title = ($mcpage==$mc_current_page) ?
		               '<b>'.$mctitle.'</b>' : $mctitle;
		    $qtokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" .$mc_page. "&mc_page=$mcpage&turl=".$_GET['turl']."&encoding=$encoding&editmode=1'>".$mc_title."</a></li>";
		  }	
		  //win select html.......................................................
		  if (!empty($qtokens)) {
		    $li0 = '<li class="sub-menu">
			          <a href="javascript:;" class="">
                          <i class="icon-th"></i>
                          <span>'.localize('_SELECTHTML',$lan).'</span>
                          <span class="arrow"></span>
                      </a>';
			$li1 = '</li>';
		    $tokens[] = $li0 . '<ul class="sub">'.implode('',$qtokens).'</ul>' . $li1;		
		  }	
        }		
	 }
   
     //print_r($tokens);
     if ($rettokens) 
		return ($tokens);
     else 
		return (implode('',$tokens));
 }	
 
};
}
?>
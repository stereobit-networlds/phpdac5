<?php

$__DPCSEC['RCCONTROLPANEL_DPC']='1;1;1;1;1;1;1;1;1';

if ((!defined("RCCONTROLPANEL_DPC")) && (seclevel('RCCONTROLPANEL_DPC',decode(GetSessionParam('UserSecID')))) ) {
define("RCCONTROLPANEL_DPC",true);

$__DPC['RCCONTROLPANEL_DPC'] = 'rccontrolpanel';

/*$ajx = GetGlobal('controller')->require_dpc('gui/ajax.dpc.php');
require_once($ajx);*/

$a = GetGlobal('controller')->require_dpc('libs/appkey.lib.php');
require_once($a);
 
$__EVENTS['RCCONTROLPANEL_DPC'][0]='cp';
$__EVENTS['RCCONTROLPANEL_DPC'][1]='cplogout';
$__EVENTS['RCCONTROLPANEL_DPC'][2]='cplogin';
$__EVENTS['RCCONTROLPANEL_DPC'][3]='cpnitobi';
$__EVENTS['RCCONTROLPANEL_DPC'][4]='cpchartshow';
$__EVENTS['RCCONTROLPANEL_DPC'][5]='cpmenushow';
$__EVENTS['RCCONTROLPANEL_DPC'][6]='cpgaugeshow';
$__EVENTS['RCCONTROLPANEL_DPC'][7]='cpzbackup';

$__ACTIONS['RCCONTROLPANEL_DPC'][0]='cp';
$__ACTIONS['RCCONTROLPANEL_DPC'][1]='cplogout';
$__ACTIONS['RCCONTROLPANEL_DPC'][2]='cplogin';
$__ACTIONS['RCCONTROLPANEL_DPC'][3]='cpnitobi';
$__ACTIONS['RCCONTROLPANEL_DPC'][4]='cpchartshow';
$__ACTIONS['RCCONTROLPANEL_DPC'][5]='cpmenushow';
$__ACTIONS['RCCONTROLPANEL_DPC'][6]='cpgaugeshow';
$__ACTIONS['RCCONTROLPANEL_DPC'][7]='cpzbackup';

$__DPCATTR['RCCONTROLPANEL_DPC']['cp'] = 'cp,1,0,0,0,0,0,0,0,0,0,0,1';

$__LOCALE['RCCONTROLPANEL_DPC'][0]='RCCONTROLPANEL_DPC;Control Panel;Control Panel';
$__LOCALE['RCCONTROLPANEL_DPC'][1]='_BACKCP;Back;Επιστροφή';
$__LOCALE['RCCONTROLPANEL_DPC'][2]='_DASHBOARD;CP Dashboard;Πινακας Ελεγχου';
$__LOCALE['RCCONTROLPANEL_DPC'][3]='_MENU;General info;Βασικές πληροφορίες';
$__LOCALE['RCCONTROLPANEL_DPC'][4]='_statisticscat;Category Viewed/Month;Επισκεψιμότητα κατηγοριών';
$__LOCALE['RCCONTROLPANEL_DPC'][5]='_statistics;Items Viewed/Month;Επισκεψιμότητα ειδών';
$__LOCALE['RCCONTROLPANEL_DPC'][6]='_transactions;Transaction/Month;Συναλλαγές ανα μήνα';
$__LOCALE['RCCONTROLPANEL_DPC'][7]='_applications;Applications Birth/Month;Νεές εφαρμογές ανα μήνα';
$__LOCALE['RCCONTROLPANEL_DPC'][8]='_appexpires;Applications Expires/Month;Ληξεις εφαρμογών ανα μήνα';
$__LOCALE['RCCONTROLPANEL_DPC'][9]='_mailqueue;Mail send/Month;Σταλθέντα e-mail ανα μήνα';
$__LOCALE['RCCONTROLPANEL_DPC'][10]='_mailsendok;Mail Received/Month;Παρεληφθέντα e-mail ανα μήνα';
$__LOCALE['RCCONTROLPANEL_DPC'][11]='_income;Income;Εισόδημα';
$__LOCALE['RCCONTROLPANEL_DPC'][12]='_moretrans;All transactions;Όλες οι συναλλαγές';

//cpmdbrec commands
$__LOCALE['RCCONTROLPANEL_DPC'][13]='_awstats;Web statistics;Στατιστικά';
$__LOCALE['RCCONTROLPANEL_DPC'][14]='_google_analytics;Google Analytics;Στατιστικά Google';
$__LOCALE['RCCONTROLPANEL_DPC'][15]='_siwapp;Siwapp;Siwapp τιμολόγηση';
$__LOCALE['RCCONTROLPANEL_DPC'][16]='_MENU1;Size;Μέγεθος';
$__LOCALE['RCCONTROLPANEL_DPC'][17]='_MENU2;People;Συναλλασόμενοι';
$__LOCALE['RCCONTROLPANEL_DPC'][18]='_MENU3;Photos & attachments;Φωτογραφίες και έγγραφα';
$__LOCALE['RCCONTROLPANEL_DPC'][19]='_MENU4;Inventory;Αποθήκη';
$__LOCALE['RCCONTROLPANEL_DPC'][20]='_MENU5;Synchronize;Συγχρονισμοί';
$__LOCALE['RCCONTROLPANEL_DPC'][21]='_MENU6;Newsletters;Αποστολές';
$__LOCALE['RCCONTROLPANEL_DPC'][22]='_MENU7;Orders;Κινήσεις';
$__LOCALE['RCCONTROLPANEL_DPC'][23]='_add_categories;Upload Categories;Εισαγωγή κατηγοριών';
$__LOCALE['RCCONTROLPANEL_DPC'][24]='_add_products;Upload Products;Εισαγωγή ειδών';

$__LOCALE['RCCONTROLPANEL_DPC'][25]='_google_addwords;Google Adwords;Google Adwords';
$__LOCALE['RCCONTROLPANEL_DPC'][26]='_upload_logo;Upload logo;Αλλαγή λογοτύπου';
$__LOCALE['RCCONTROLPANEL_DPC'][27]='_add_recaptcha;ReCaptcha;ReCaptcha';
$__LOCALE['RCCONTROLPANEL_DPC'][28]='_update;Update;Αναβάθμιση';
$__LOCALE['RCCONTROLPANEL_DPC'][29]='_backup;Backup;Αποθήκευση δεδομένων';
$__LOCALE['RCCONTROLPANEL_DPC'][30]='_backup_content;Backup contents;Αποθήκευση στοιχείων';
$__LOCALE['RCCONTROLPANEL_DPC'][31]='_maildbqueue;Newsletters & mailing lists;Μαζικές αποστολές e-mails';
$__LOCALE['RCCONTROLPANEL_DPC'][32]='_sendnewsletters;Enable newsletter mailing list feature;Ενεργοποίηση μαζικών αποστολών e-mails';
$__LOCALE['RCCONTROLPANEL_DPC'][33]='_TWEETSRSS;Feeds & tweets;Ενημέρωση';
$__LOCALE['RCCONTROLPANEL_DPC'][34]='_add_domainname;Domain name;Domain name';
$__LOCALE['RCCONTROLPANEL_DPC'][35]='_customers;Customers;Πελάτες';
$__LOCALE['RCCONTROLPANEL_DPC'][36]='_installeshop;Install e-shop;Εγκατάσταση e-shop';
$__LOCALE['RCCONTROLPANEL_DPC'][37]='_uninstalleshop;Uninstall e-shop;Κατάργηση e-shop';
$__LOCALE['RCCONTROLPANEL_DPC'][38]='_eshop;e-shop module;e-shop πρόσθετο';
$__LOCALE['RCCONTROLPANEL_DPC'][39]='_install;Install;Εγκατάσταση';
$__LOCALE['RCCONTROLPANEL_DPC'][40]='_ckfinder;CKfinder;CKfinder';
$__LOCALE['RCCONTROLPANEL_DPC'][41]='_jqgrid;JQgrid;JQgrid';
$__LOCALE['RCCONTROLPANEL_DPC'][42]='_ieditor;IEditor;IEditor';
$__LOCALE['RCCONTROLPANEL_DPC'][43]='_addons;Addons;Πρόσθετα';
$__LOCALE['RCCONTROLPANEL_DPC'][44]='_edit_htmlfiles;Edit system files;Επεξεργασία αρχείων συστήματος';
$__LOCALE['RCCONTROLPANEL_DPC'][45]='_addspace;Limited space, add space;Πρόσθεσε χωρητικότητα';
$__LOCALE['RCCONTROLPANEL_DPC'][46]='_ago;after expiration;που έληξε';
$__LOCALE['RCCONTROLPANEL_DPC'][47]='_fromnow;before expire;πρίν τη λήξη';
$__LOCALE['RCCONTROLPANEL_DPC'][48]='_modified;modified;Ενημερώθηκε';
$__LOCALE['RCCONTROLPANEL_DPC'][49]='_ago2;ago;πρίν';
$__LOCALE['RCCONTROLPANEL_DPC'][50]='_fromnow2;from now;μετά';
$__LOCALE['RCCONTROLPANEL_DPC'][51]='_cpimages;Update icons;Ενημέρωση εικονιδίων';
$__LOCALE['RCCONTROLPANEL_DPC'][52]='_addkey;Add key;Εισαγωγή κλειδιού';
$__LOCALE['RCCONTROLPANEL_DPC'][53]='_genkey;Gen key;Δημιουργία κλειδιού';
$__LOCALE['RCCONTROLPANEL_DPC'][54]='_validatekey;Validate key;Έλεγχος κλειδιού';
$__LOCALE['RCCONTROLPANEL_DPC'][55]='_desendnewsletters;Uninstall newsletter feature;Απεγκατάσταση μαζικών αποστολών e-mails';
$__LOCALE['RCCONTROLPANEL_DPC'][56]='_newsletters;Newsletter feature installed;Αποστολή e-mails εγκατεστημένο';
$__LOCALE['RCCONTROLPANEL_DPC'][57]='_year;Year;Έτος';
$__LOCALE['RCCONTROLPANEL_DPC'][58]='_month;Month;Μήνας';
$__LOCALE['RCCONTROLPANEL_DPC'][59]='_more;More...;Περισσότερα...';

$__LOCALE['RCCONTROLPANEL_DPC'][60]='_exit;Exit;Έξοδος';
$__LOCALE['RCCONTROLPANEL_DPC'][61]='_dashboard;Dashboard;Πίνακας ελέγχου';
$__LOCALE['RCCONTROLPANEL_DPC'][62]='_logout;Logout;Αποσύνδεση';
$__LOCALE['RCCONTROLPANEL_DPC'][63]='_rssfeeds;RSS Feeds;RSS Feeds';
$__LOCALE['RCCONTROLPANEL_DPC'][64]='_edititemtext;Edit Item Text;Μεταβολή κειμένου';// (text) αντικειμένου';
$__LOCALE['RCCONTROLPANEL_DPC'][65]='_deleteitemattachment;Delete Item Attachment;Διαγραφή συνημμένου';// είδους';
$__LOCALE['RCCONTROLPANEL_DPC'][66]='_editcat;Edit Category;Μεταβολή κατηγορίας';
$__LOCALE['RCCONTROLPANEL_DPC'][67]='_addcat;Add Category;Νέα Κατηγορία';
$__LOCALE['RCCONTROLPANEL_DPC'][68]='_additem;Add Item;Νέο Είδος';
$__LOCALE['RCCONTROLPANEL_DPC'][69]='_webstatistics;Statistics;Στατιστικά';
$__LOCALE['RCCONTROLPANEL_DPC'][70]='_addcathtml;Add Category Html;Προσθήκη κειμένου';// κατηγορίας';
$__LOCALE['RCCONTROLPANEL_DPC'][71]='_editcathtml;Edit Category Html;Μεταβολή κειμένου';// κατηγορίας';
$__LOCALE['RCCONTROLPANEL_DPC'][72]='_edititem;Edit Item;Μεταβολή είδους';// αντικειμένου';
$__LOCALE['RCCONTROLPANEL_DPC'][73]='_edititemphoto;Edit Photo;Μεταβολή φωτογραφίας';// αντικειμένου';
$__LOCALE['RCCONTROLPANEL_DPC'][74]='_edititemdbhtm;Edit Item Htm;Μεταβολή κειμένου';// (htm) αντικειμένου (db)';
$__LOCALE['RCCONTROLPANEL_DPC'][75]='_edititemdbhtml;Edit Item Html;Μεταβολή κειμένου';// (html) αντικειμένου (db)';
$__LOCALE['RCCONTROLPANEL_DPC'][76]='_edititemdbtext;Edit Item Text;Μεταβολή κειμένου';// (text) αντικειμένου (db)';
$__LOCALE['RCCONTROLPANEL_DPC'][77]='_senditemmail;Send e-mail;Αποστολή e-mail';
$__LOCALE['RCCONTROLPANEL_DPC'][78]='_delitemattachment;Delete Text;Διαγραφή κειμένου';// (db)';
$__LOCALE['RCCONTROLPANEL_DPC'][79]='_edititemtext;Edit Item Text;Μεταβολή κειμένου';// (text) αντικειμένου';
$__LOCALE['RCCONTROLPANEL_DPC'][80]='_edititemhtm;Edit Item Htm;Μεταβολή κειμένου';// (htm) αντικειμένου';
$__LOCALE['RCCONTROLPANEL_DPC'][81]='_edititemhtml;Edit Item Html;Μεταβολή κειμένου';// (html) αντικειμένου';
$__LOCALE['RCCONTROLPANEL_DPC'][82]='_additemhtml;Add Item Html;Εισαγωγή κειμένου';// στο αντικείμενο';
$__LOCALE['RCCONTROLPANEL_DPC'][83]='_transactions;Transactions;Συναλλαγές';
$__LOCALE['RCCONTROLPANEL_DPC'][84]='_users;Users;Χρήστες';
$__LOCALE['RCCONTROLPANEL_DPC'][85]='_itemattachments2db;Add Items to DB;Μεταφορά κειμένων στην Β.Δ.';//βάση δεδομένων';
$__LOCALE['RCCONTROLPANEL_DPC'][86]='_importdb;Import Database;Εισαγωγή βάσης δεδομένων';
$__LOCALE['RCCONTROLPANEL_DPC'][87]='_config;Configuration;Ρυθμίσεις';
$__LOCALE['RCCONTROLPANEL_DPC'][88]='_contactform;Contact Form;Φόρμα επικοινωνίας';
$__LOCALE['RCCONTROLPANEL_DPC'][89]='_subscribers;Subscribers;Συνδρομητές';
$__LOCALE['RCCONTROLPANEL_DPC'][90]='_sitemap;Sitemap;Χάρτης πλοήγησης';// αντικειμένων';
$__LOCALE['RCCONTROLPANEL_DPC'][91]='_search;Search;Φόρμα Αναζήτησης';
$__LOCALE['RCCONTROLPANEL_DPC'][92]='_upload;Upload files;Ανέβασμα αρχείων';
$__LOCALE['RCCONTROLPANEL_DPC'][93]='_uploadid;Upload item files;Ανέβασμα αρχείων';// αντικειμένου';
$__LOCALE['RCCONTROLPANEL_DPC'][94]='_uploadcat;Upload category files;Ανέβασμα αρχείων';// κατηγορίας';
$__LOCALE['RCCONTROLPANEL_DPC'][95]='_syncphoto;Sync photos;Συγχρονισμός φωτογραφιών';
$__LOCALE['RCCONTROLPANEL_DPC'][96]='_syncsql;Sync data;Συγχρονισμός δεδομένων';
$__LOCALE['RCCONTROLPANEL_DPC'][97]='_dbphoto;Image in DB;Εικόνα στην βάση δεδομένων';
$__LOCALE['RCCONTROLPANEL_DPC'][98]='_editctag;Category Tags;Tags κατηγορίας';
$__LOCALE['RCCONTROLPANEL_DPC'][99]='_edititag;Item Tags;Tags είδους';
$__LOCALE['RCCONTROLPANEL_DPC'][100]='_menu;Menu;Επιλογές';
$__LOCALE['RCCONTROLPANEL_DPC'][101]='_slideshow;Slideshow;Επιλογές Slideshow';
$__LOCALE['RCCONTROLPANEL_DPC'][102]='_ckfinder;Upload files;Upload αρχείων';
$__LOCALE['RCCONTROLPANEL_DPC'][103]='_webmail;Web Mail;Web Mail';
$__LOCALE['RCCONTROLPANEL_DPC'][104]='_editpage;Edit Page;Επεξεργασία σελίδας';
$__LOCALE['RCCONTROLPANEL_DPC'][105]='_rempass;Forgotten password;Υπενθύμιση κωδικού';
$__LOCALE['RCCONTROLPANEL_DPC'][106]='_chpass;Change password;Αλλαγή κωδικού';
$__LOCALE['RCCONTROLPANEL_DPC'][107]='_cphelp;Ηelp;Βοήθεια';
$__LOCALE['RCCONTROLPANEL_DPC'][108]='_cpupgrade;Upgrade;Αναβάθμιση';
$__LOCALE['RCCONTROLPANEL_DPC'][109]='_cpwizard;Enable wizard;Οδηγός εγκατάστασης';
$__LOCALE['RCCONTROLPANEL_DPC'][110]='_cpdhtmlon;Windows mode;Πλοήγηση Windows';
$__LOCALE['RCCONTROLPANEL_DPC'][111]='_cpdhtmloff;Frames mode;Πλοήγηση Frames';
$__LOCALE['RCCONTROLPANEL_DPC'][112]='_cpcropwiz;Crop wizard;Crop wizard';
$__LOCALE['RCCONTROLPANEL_DPC'][113]='_OPTIONS;Options;Επιλογές';
$__LOCALE['RCCONTROLPANEL_DPC'][114]='_ADD;Add;Προσθήκη';
$__LOCALE['RCCONTROLPANEL_DPC'][115]='_CATEGORY;Category;Κατηγορία';
$__LOCALE['RCCONTROLPANEL_DPC'][116]='_ITEM;Item;Είδος';
$__LOCALE['RCCONTROLPANEL_DPC'][117]='_SETTINGS;Settings;Ρυθμίσεις';
$__LOCALE['RCCONTROLPANEL_DPC'][118]='_customers;Customers;Πελάτες';
$__LOCALE['RCCONTROLPANEL_DPC'][119]='_EDITHTML;Edit Html;Σελίδες Html';
$__LOCALE['RCCONTROLPANEL_DPC'][120]='_SELECTHTML;Select Html;Επιλογή Html';
$__LOCALE['RCCONTROLPANEL_DPC'][121]='_ADDFAST;Add item;Εισαγωγή είδους';
$__LOCALE['RCCONTROLPANEL_DPC'][122]='_addtag;Add Tag;Εισαγωγή Ετικέτας';
$__LOCALE['RCCONTROLPANEL_DPC'][123]='_back;Back;Επιστροφή';

class rccontrolpanel {

	var $title,$cmd,$subpath,$path,$dbpath,$prpath;
	var $_ctree,$_ctabstrip, $dashboard, $cp0_tabtype, $cpn_tabtype;
	
    static $firstrun = 0;
	var $charts, $hasgraph, $goto, $ajaxgraph, $refresh, $objcall, $objgauge, $hasgauge;
	var $charset;
	var $editmode;
	var $application_path;	
	var $environment, $url, $murl, $urlpath;
	var $dhtml, $tools;
	var $has_eshop, $htmlfolder, $htmlext;
	var $appkey, $awstats_url;
	var $cptemplate, $stats, $cpStats;
		
	function rccontrolpanel() {
		
	    $this->title = localize('RCCONTROLPANEL_DPC',getlocal());
        $this->urlpath = paramload('SHELL','urlpath');		
		$this->subpath = $this->urlpath . "/cp";
		//$this->prpath = paramload('SHELL','urlpath') . $this->subpath;//??		
		$this->path = paramload('SHELL','urlpath') . $this->subpath;   		
		//echo $this->path; global $config; print_r($config);
		$this->dbpath = paramload('SHELL','dbgpath');
		
		$this->prpath = paramload('SHELL','prpath');	
        $this->application_path = paramload('SHELL','urlpath');			
		//echo $this->prpath;
		
		$this->murl = arrayload('SHELL','ip');
        $this->url = $this->murl[0]; 			
		$this->editmode = GetReq('editmode');
		
        //choose encoding
        $char_set  = arrayload('SHELL','char_set');	  
        $charset  = paramload('SHELL','charset');	  		
		if (($charset=='utf-8') || ($charset=='utf8'))
		  $this->charset = 'utf-8';
		else  
	      $this->charset = $char_set[getlocal()]; 		
		
		
		$au = remote_paramload('RCCONTROLPANEL','autoupdate',$this->prpath);
        $this->autoupdate = $au?$au:3600;
		$this->dashboard = null;
		
		$this->cp0_tabtype = 'dom';//'iframe'; //first tab
		$this->cpn_tabtype = 'iframe'; //all others
		
		$this->ajaxgraph=1;
		$this->refresh = GetReq('refresh')?GetReq('refresh'):60;//0
		$this->goto = seturl('t=cp&group='.GetReq('group'));//handle graph selections with no ajax
		
        //READ ENVIRONMENT ATTR
		$this->environment = $_SESSION['env'] ? $_SESSION['env'] : $this->read_env_file(true);		
		//print_r($this->environment);
		
		$this->load_graph_objects();
		
		//dynamic html loader
	    $this->dhtml = remote_paramload('FRONTHTMLPAGE','dhtml',$this->prpath);		  
		
		//installed eshop code
		//$this->has_eshop = remote_paramload('ESHOP','code',$this->prpath);		
		
		//edit html files
		$htmltemplate  = remote_paramload('FRONTHTMLPAGE','template',$this->prpath);
		$this->htmlext  = remote_paramload('FRONTHTMLPAGE','htmlext',$this->prpath);
		$this->htmlfolder  = remote_paramload('FRONTHTMLPAGE','path',$this->prpath) .
		                     ($htmltemplate ? '/'. $htmltemplate : null);
		//awstats cp window
        //$url = "cgi-bin/awstats.pl?config=".str_replace('www.','',$_ENV["HTTP_HOST"])."&framename=mainright#top";			   
		//get last murl element = site.stereobit.gr 
		$awurl = remote_paramload('RCAWSTATS','file',$this->prpath);
		$this->awstats_url = $awurl ? $awurl :
		                     ((!empty($this->murl)) ? array_pop($this->murl) : str_replace('www.','',$_ENV["HTTP_HOST"]));		
		
		$this->appkey = new appkey();
		
		$this->cptemplate = remote_paramload('FRONTHTMLPAGE','cptemplate',$this->prpath);
		$this->stats = array();
		$this->cpStats = false;
		if ($this->cptemplate) {
			$this->templatepanel(true); //always
			$this->cpStats = $this->isStats();
		}		
	}
	 	
    function event($sAction) {    	  
	   
	   //if a remote user is in do not allow /cp actions
	   /////////////////////////////////////////////////////////////
	   if (GetSessionParam('REMOTELOGIN')!='') die("Not allowed!");//	
	   /////////////////////////////////////////////////////////////	
	   
	   $this->autoupdate();	   	  		  			      
  
	   switch ($sAction) {
	   
	     case 'cpzbackup' : break;
	   
		 case 'cpmenushow': //if ($menu = GetReq('group')) {//ajax call
		                       //header("Content-Type", "text/html; charset=$this->charset");
		                       $this->hasmenu = $this->read_directory($this->path);
							   //$this->gotomenu = seturl('t=cpmenushow&group='.GetReq('group').'&ai=1&&statsid=');
							 //}
							 break; 
							 
		 case 'cpchartshow': if ($report = GetReq('report')) {//ajax call
		                       //$this->load_graph_objects();
		                       $this->hasgraph = GetGlobal('controller')->calldpc_method("swfcharts.create_chart_data use $report");
							   $this->goto = seturl('t=cpchartshow&group='.GetReq('group').'&ai=1&report='.$report.'&statsid=');
							 }
							 break;
							 							 	   
		 case 'cpgaugeshow': if ($report = GetReq('report')) {//ajax call
		                       //$this->load_graph_objects();
		                       $this->hasgauge = GetGlobal('controller')->calldpc_method("swfcharts.create_gauge_data use $report+where cid=0++1+400+300+meter");
							   $this->goto = seturl('t=cpgaugeshow&group='.GetReq('group').'&ai=1&report='.$report.'&statsid=');
							 }
							 break;	   
	   	
         case "cplogout"   : $this->logout();
		                     break;
		 case "cplogin"     :$valid = $this->verify_login();
		                     $this->javascript();							  
							
		 case "cpnitobi"    :if (GetSessionParam('LOGIN')=='yes')
		                      $this->read_directory($this->path);//echo $this->path;
		                     break;	
							 
		 case "cp"          :
		 default         	:if ($this->cptemplate) {
								$this->set_addons_list();
		                     }
                             elseif (GetSessionParam('LOGIN')=='yes') { 
								$this->javascript();	
								$this->read_directory($this->path);//echo $this->path;
							  
								//$this->load_graph_objects();
								$this->set_addons_list();
							 }
		                     break;				 
       } 
    }
  
    function action($sAction) {
		   
	  //echo GetSessionParam('LOGIN'),"...";
	  if (GetSessionParam('LOGIN')=='yes') {
	    
		$tmpl = 'cpdashboard.htm';  
		$t = $this->path . '/html/'. str_replace('.',getlocal().'.',$tmpl) ; 
		//echo $t;
		if (is_readable($t)) 
			$this->dashboard = file_get_contents($t);
				  
	  
	    switch ($sAction) {
		    
			case 'cpzbackup' : $out = $this->zip_directory(GetReq('zname'),GetReq('zpath'));
			                   break;
		  
		    case 'cpmenushow': if ($this->hasmenu) //ajax call
		                          //$out = $this->show_directory_iconstable(4,3);
								  $out = $this->ajax_menu(4,3);//,null,1); //xmlout
							    else
							      $out = "<h3>".localize('_GNAVAL',0)."</h3>";	

							    die('menu|'.$out); //ajax return
								break;		  
		  
		    case 'cpchartshow': if ($this->hasgraph) {//ajax call
		                          $out = GetGlobal('controller')->calldpc_method("swfcharts.show_chart use " . GetReq('report') ."+500+240+$this->goto");								  
								}  
							    else
							      $out = "<h3>".localize('_GNAVAL',0)."</h3>";	

							    die(GetReq('report').'|'.$out); //ajax return
								break;
								
		    case 'cpgaugeshow': if ($this->hasgauge) {//ajax call
		                          $out = GetGlobal('controller')->calldpc_method("swfcharts.show_gauge use ". GetReq('report') ."+400+300");								  
								}  
							    else
							      $out = "<h3>".localize('_GNAVAL',0)."</h3>";	

							    die(GetReq('report').'|'.$out); //ajax return
								break;										  
		  	    
			case "cp"          :	
			default            : if ($this->cptemplate) {
			                        //echo 'a'; //not always executed but ony ehwn in dashboard
									if ((GetReq('t')=='cp')||(!GetReq('t'))) 
										$this->templatepanel(); 
									$this->cpStats = $this->isStats();
								 }	
			                     else
									$out .= $this->controlpanel(4,3,$this->editmode); 
			  
		} 		 		  
	  }  
	
	  return ($out);
    } 
	
	//load graphs urls to call
	function load_graph_objects() {
	
        if (defined('RCKATEGORIES_DPC'))		  
          $this->objcall['statisticscat'] = seturl('t=cpchartshow&group='.GetReq('group').'&ai=1&report=statisticscat&statsid=');
        if (defined('RCITEMS_DPC'))		  
          $this->objcall['statistics'] = seturl('t=cpchartshow&group='.GetReq('group').'&ai=1&report=statistics&statsid=');
        if (defined('RCMAILDBQUEUE_DPC')) {		  
          $this->objcall['mailqueue'] = seturl('t=cpchartshow&group='.GetReq('group').'&ai=1&report=mailqueue&statsid=');
		  //$this->objcall['mailsendok'] = seturl('t=cpchartshow&group='.GetReq('group').'&ai=1&report=mailsendok&statsid=');
		}  
		if (defined('RCTRANSACTIONS_DPC')) {
          $this->objcall['transactions'] = seturl('t=cpchartshow&group='.GetReq('group').'&ai=1&report=transactions&statsid=');  		  
		  //$this->objcall['users'] = seturl('t=cpchartshow&group='.GetReq('group').'&ai=1&report=users&statsid=');  		  
		  //$this->objcall['subscribers'] = seturl('t=cpchartshow&group='.GetReq('group').'&ai=1&report=subscribers&statsid=');  		  
		}  
		if (defined('RCUSERS_DPC')) {
		  $this->objcall['users'] = seturl('t=cpchartshow&group='.GetReq('group').'&ai=1&report=users&statsid=');  		  
		  $this->objcall['subscribers'] = seturl('t=cpchartshow&group='.GetReq('group').'&ai=1&report=subscribers&statsid='); 		
		}
        if (defined('RCSAPPVIEW_DPC')) {		  
          $this->objcall['applications'] = seturl('t=cpchartshow&group='.GetReq('group').'&ai=1&report=applications&statsid=');
          $this->objcall['appexpires'] = seturl('t=cpchartshow&group='.GetReq('group').'&ai=1&report=appexpires&statsid=');		  		  		  
		} 
		 
		if (defined('RCTRANSACTIONS_DPC'))
          $this->objgauge['income'] = seturl('t=cpgaugeshow&group='.GetReq('group').'&ai=1&report=income&statsid=');						    		
		  	
	}
	
	function javascript() {
      if (iniload('JAVASCRIPT')) {
	  
		   $js = new jscript;	  
	        
		   //auto refresh
	       if ($refresh = $this->refresh)
             $code = $this->javascript_refresh(seturl('t=cp&refresh='.$refresh),$refresh*1000);  

           if ($this->ajaxgraph) {		   
	         $code .= $this->ajaxinitjscall();
		     $js->setloadparams("init();");//call menu ajax		   		   		   		   
		   }
		   
           $js->load_js($code,"",1);			   
		   unset ($js);		   
	  }	
	}
	
	function ajaxinitjscall() {
        $gotomenu = seturl('t=cpmenushow&group='.GetReq('group').'&ai=1&&statsid=');				
	
		$out = "
function init()
{		 
  sndReqArg('$gotomenu'+statsid.value,'menu'); 		
}		
";					
        return ($out);
	}
	
    function javascript_refresh($page,$timeout=null) {	 
	  
     $mytimeout=$timeout?$timeout:5000;//5 sec
     $mytimeout2=$timeout?$timeout+2000:7000;//5 sec
     
     if ($this->ajaxgraph) {
	   
	   if (!empty($this->objcall)) {
	     $i = 0;
	     foreach ($this->objcall as $report=>$goto) {
	       $timeout = $mytimeout + (++$i*1000); //set delay 
           $ret .= "window.setInterval(\"sndReqArg('$goto'+statsid.value,'$report')\",$timeout);
";	 
         }
	   }
	   if (!empty($this->objgauge)) {
	     $j = 0;
	     foreach ($this->objgauge as $report=>$goto) {
	       $timeout = $mytimeout + (++$j*1500); //set delay 
           $ret .= "window.setInterval(\"sndReqArg('$goto'+statsid.value,'$report')\",$timeout);
";	 
         }	 
	   }  
	 }
	 else {
       $ret = "
function neu()
{	
	top.frames.location.href = \"$page\"
}
window.setTimeout(\"neu()\",$mytimeout);
";
	 }
	 return ($ret);
    }			
	
	function nitobi_javascript() {
      if (iniload('JAVASCRIPT')) {

		   //$template = $this->set_template();   		      
		   
	       //$code = $this->init_tabstrip();
	       $code = $this->_ctabstrip->tabstrip_init($this->cpn_tabtype);//'ajax');//null=iframe		   			
	   
		   $js = new jscript;
		   $js->setloadparams("init()");
           $js->load_js('nitobi.toolkit.js');		   
           $js->load_js('nitobi.tabstrip.js');
           $js->load_js('nitobi.tree.js');		   		   
           $js->load_js($code,"",1);			   
		   unset ($js);
	  }		
	}	
	
    protected function tabstrip() {
		$mytitle = (isset($this->dashboard))?localize('_DASHBOARD',getlocal()):'Control Panel';
  
		if ($this->cp0_tabtype=='dom')
			$ret = $this->_ctabstrip->set_tabstrip($mytitle,'cp0',$this->cp0_tabtype,'nitobi','100%','700px','left','fade');
		else
			$ret = $this->_ctabstrip->set_tabstrip($mytitle,'cp.php?t=cpnitobi',$this->cp0_tabtype,'nitobi','100%','700px','left','fade');
  
		return ($ret);
    }		  
	  
    protected function controlpanel($type=4,$linemax=3,$nav_off=null,$win_off=null) {
     //echo '>',$this->editmode;
	 
	 if (!$nav_off) {
	     if ($gr=GetReq('group'))
	       $out = setNavigator(seturl("t=cp",$this->title),substr($gr,2));	 
	     else	 
           $out = setNavigator($this->title);//,$t);	   
	 }
	 
	 if (isset($this->dashboard)) {
			//all icons one token
			if ($this->editmode) 
			  $tokens[] = $this->site_stats();
		    else
			  $tokens[] = $this->show_directory_iconstable($type,$linemax);  	
			  
		    $out .= $this->combine_tokens($this->dashboard,$tokens);
	 }
	 else { 	  

	   if ($this->ajaxgraph) { //ajax
	     if ($this->editmode) {
		   //$panel = ....;
		 }  
		 else
		   $panel = GetGlobal('controller')->calldpc_method("ajax.setajaxdiv use menu");
	   }	 
	   else	
	     $panel = $this->show_directory_iconstable($type,$linemax);
	   
	   if ($win_off) {
	     $out .= $panel;
	   }
	   else {	 		 		 
	     if ($this->editmode) {
		   $site_panel = $this->site_stats();
		   
		   $win1 = new window2(localize("_MENU",getlocal()),$site_panel,null,1,null,'SHOW',null,1);
	       $panel = $win1->render();
		   unset ($win1);		   
		 }
		 else {
		   $win1 = new window2(localize("_MENU",getlocal()),$panel,null,1,null,'SHOW',null,1);
	       $panel = $win1->render();
		   unset ($win1);
	     }

		 //multicolumn view	
		 
         //middle panel	..if dhtml show middle column else add in left panel					
		 $middle_panel =  ($this->dhtml) ?
		                  $this->_show_charts() . 
		                  $this->_show_gauges() :
						  null;
		 
		 //right panel
         $right_panel = $this->_show_addon_tools(true);
		 $right_panel .= ($this->dhtml) ? null : $this->_show_charts() . $this->_show_gauges();			   				
		 
		 //left panel
		 $left_panel = $this->_show_update_tools() . $panel;
		 //$left_panel .= ($this->dhtml) ? null : $this->_show_charts() . $this->_show_gauges();			   
		 $left_panel .= $this->_show_addons();
		 $left_panel .= $this->_show_tweets_rss();
		 
	     $data1[] = $left_panel;
         $attr1[] = isset($right_panel) ? (isset($middle_panel) ? "left;35%" : "left;50%") : "left;100%";	    
		
		 if (isset($middle_panel)) { 
			$data1[] = $middle_panel;
			$attr1[] = isset($right_panel) ? "left;30%" : "left;50%";
		 }
		
		 if (isset($right_panel)) { 
			$data1[] = $right_panel;//$stats;//ts			
			$attr1[] = isset($middle_panel) ? "left;35%" : "left;50%";
		 }
		 
	     $swin = new window(localize("_DASHBOARD",getlocal()),$data1,$attr1);
	     $out .= $swin->render("center::100%::0::::center::0::2::");		 
	     //$swin = new window("Control Panel",$panel);
	     //$out .= $swin->render("center::50%::0::group_win_body::center::0::0::");	
	     unset ($swin);
		 
	     //HIDDEN FIELD TO HOLD STATS ID FOR AJAX HANDLE
	     $out .= "<INPUT TYPE= \"hidden\" ID= \"statsid\" VALUE=\"0\" >";	  		 			   		 
       }
	 }  
	 
     return ($out);	 
    } 
  
    protected function _show_charts() {
		//$stats = $this->_show_addon_tools(); //tools to enable
	    if (!empty($this->objcall)) {  
		 
 		    foreach ($this->objcall as $report=>$goto) {//goto not used in this case
                $title = localize("_$report",getlocal()); //title
		        if ($this->ajaxgraph)  {//ajax
			        $ts = GetGlobal('controller')->calldpc_method("ajax.setajaxdiv use $report");
			    }
		        elseif ($transtats = GetGlobal('controller')->calldpc_method("swfcharts.create_chart_data use transactions")) {
			        $ts = GetGlobal('controller')->calldpc_method("swfcharts.show_chart use $report+500+240+$this->goto");
			    }						
			    $win1 = new window2($title,$ts,null,1,null,'SHOW',null,1);
	            $stats .= $win1->render();
		        unset ($win1);								 	   
			}
		}

		return ($stats);		 
    }
  
    protected function _show_gauges() {
  
      if (!empty($this->objgauge)) {  
	
	    foreach ($this->objgauge as $report=>$goto) {
           $title = localize("_$report",getlocal()); //title

		   if ($this->ajaxgraph)  {//ajax
			 $ts = GetGlobal('controller')->calldpc_method("ajax.setajaxdiv use $report");
		   }
		   elseif ($transtats = calldpc_method("swfcharts.create_gauge_data use $report+where cid=0++1+400+300+meter")) {
		     $ts = GetGlobal('controller')->calldpc_method("swfcharts.show_gauge use $report+400+300");
		   }
		      
		   $win1 = new window2($title,$ts,null,1,null,'SHOW',null,1);
           $chart .= $win1->render();
           unset ($win1);		 
	   }
      }
      return ($chart);	
    }
  
    protected function _show_addons($template=false) {  
      $winh = 'SHOW';
	
      if (!empty($this->environment)) {    
      foreach ($this->environment as $mod=>$val) {
	    
		if ($val) {//enabled
		   $module = strtolower($mod);
		   switch ($module) {
		       case 'dashboard' : $text=null; break; //bypass
			   case 'ckfinder'  : $text=null; break; //bypass
			   
			   case 'edithtml'  : $text = $this->edit_html_files(false);//true); //cke4
			                      $winh = 'HIDE';
			                      break; 
			   
			   case 'menu'      : $text=null; break; //bypass

		       case 'awstats'   : //$text = "<a href='cgi-bin/awstats.php'>Awstats</a>";
							   $url = "cgi-bin/awstats.pl?config=". $this->awstats_url ."&framename=mainright#top";
					           $text .= "<IFRAME SRC=\"$url\" TITLE=\"awstats\" WIDTH=100% HEIGHT=400>
										<!-- Alternate content for non-supporting browsers -->
										<H2>Awstats</H2>
										<H3>iframe is not suported in your browser!</H3>
										</IFRAME>";	
                               $winh = 'SHOW';										
			                   break;			   
		       case 'siwapp' : $text = "<a href='../siwapp/'>Siwapp</a>"; 
			                   /*$url = "http://".str_replace('www.','',$_ENV["HTTP_HOST"])."/siwapp/";			   
					           $text .= "<IFRAME SRC=\"$url\" TITLE=\"siwapp\" WIDTH=100% HEIGHT=400>
										<!-- Alternate content for non-supporting browsers -->
										<H2>Siwapp</H2>
										<H3>iframe is not suported in your browser!</H3>
										</IFRAME>";	*/
							   $winh = 'SHOW';			
			                   break;
		       default       : $text = null;//$val;
			                   $winh = 'SHOW';
		   }
		  
		   if ($text) {
		     $mtitle = localize('_'.$module, getlocal());
		     $win1 = new window2($mtitle,$text,null,1,null,$winh,null,1);
             $addons .= $win1->render();
             unset ($win1);
		   }
		}  
      }		
	  }//if

      return ($addons);	
    }
  
    protected function _show_addon_tools($icons=null, $template=false) {

      $seclevid = GetSessionParam('ADMINSecID');   
      //print_r($this->environment);
      //print_r($this->tools);echo $seclevelid;
	
      if (!empty($this->tools)) {    
      foreach ($this->tools as $tool=>$u_ison) {
	    $peruser_ison = explode(',',$u_ison);
		$ison = $peruser_ison[$seclevid-1];
		
        $text = null;
		$mytool = strtolower($tool);
		//echo $tool,'<br/>';		   
		
	    $icon_on = $icons ? true : false; //init pre tool
		$e1 = null;//init pre tool
		
		//if (($ison>0) && ($this->environment[strtoupper($tool)]>0)) {//(isset($this->environment[strtoupper($tool)]))) {//enabled
		if ($this->environment[strtoupper($tool)]>0) { //enabled tool
	       //echo $tool,'<br/>';	
		   switch ($mytool) {
		       case 'google_addwords'  : $text = "<a href='../analyr/'>Go to addwords</a>"; 
			                             break;		   
										 
		       case 'google_analytics' : if (is_readable($this->prpath.'ganalytics.html')) 
											$url = "ganalytics.html";
										 else 
											$url = "http://analytics.google.com";	   
					                     $text .= "<IFRAME SRC=\"$url\" TITLE=\"analytics\" WIDTH=100% HEIGHT=400>
										<!-- Alternate content for non-supporting browsers -->
										<H2>Google analytics</H2>
							   			<H3>iframe is not suported in your browser!</H3>
										</IFRAME>";	
                                         $icon_on =false; 										
			                             break;	
			   case 'add_categories':   if (defined('RCIMPORTDB_DPC')) {
			                                $text = GetGlobal('controller')->calldpc_method('rcimportdb.upload_database_form use +++1');
											$icon_on =false;
										}	
			                            else
											$text = "<a href='cpimportdb.php?editmode=1&encoding=".$this->charset."'>Upload categories</a>"; 
			                            break;
               case 'add_products'  :	if (defined('RCIMPORTDB_DPC')) {
			                                $text = GetGlobal('controller')->calldpc_method('rcimportdb.upload_database_form use +++1');
											$icon_on =false;
										}	
			                            else
											$text = "<a href='cpimportdb.php?editmode=1&encoding=".$this->charset."'>Upload products</a>"; 
			                            break;	
			   case 'upload_logo'   :	if (defined('RCUPLOAD_DPC')) {
			                                $text = GetGlobal('controller')->calldpc_method('rcupload.advanced_uploadform use +logo.png++images+');
											$text .= GetGlobal('controller')->calldpc_method('rcupload.advanced_uploadform use +pointer.png++images+');
											$text .= GetGlobal('controller')->calldpc_method('rcupload.advanced_uploadform use +favicon.ico');
											$icon_on =false;
										}	
			                            else
											$text = "<a href='cpupload.php?editmode=1&encoding=utf-8'><img src='../images/logo.png'/></a>"; 
			                            break;	
               case 'add_recaptcha' :  	//$text = "<a href='cpupload.php?editmode=1&encoding=utf-8'>reCAPTCHA ON!</a>";
			                            $text = "Recaptcha feature installed";
                                        break;			   
               case 'backup'        :  	//always repeat...
			                            if ($e1 = $this->call_wizard_url('backup')) 
											$text = "<a href='$e1'>".localize('_backup_content',getlocal())."</a>"; 	
										 else
 										    $text = "Unknown tool.";
                                        break;	
			   //case 'uninstall_maildbqueue'   :							
               case 'maildbqueue'   :   if ($valid = $this->is_valid_newsletter()) {
										    $text = localize('_newsletters' ,getlocal());//"Newsletter feature installed"; 
											$text .= ' ('.$valid.')';
										}
                                        else {//uninstall
											if ($e1 = $this->call_wizard_url('uninstall_maildbqueue')) 
												$text = "<a href='$e1'>".localize('_desendnewsletters',getlocal())."</a>"; 	
											else
												$text = "Unknown tool.";										
                                        }										
                                        break;	
               case 'add_domainname':  	
			                            $text = "Domain name ($this->url) installed. ";
										//if ($e1 = $this->call_wizard_url('add_domainname'))
                                          //  $text .= "<a href='$e1'>Re-change.</a>"; 										
                                        break;

			   //case 'uninstalleshop'   :							
               case 'eshop'   :         //$text = "e-shop feature installed"; break;
			                            if ($valid = $this->is_valid_eshop()) {//uninstall
										 
										    $message = localize('_uninstalleshop',getlocal());
											$message .= ' ('.$valid.')';
											
										    //you can unistall before expired
											if ($e1 = $this->call_wizard_url('uninstalleshop')) 
											  $text = "<a href='$e1'>".$message."</a>"; 	
											else
 										      $text = "Unknown tool.";
										}
                                        /*else {//re-install
											if ($e1 = $this->call_wizard_url('eshop')) 
											  $text = "<a href='$e1'>".localize('_installeshop',getlocal())."</a>"; 	
											else
 										      $text = "Unknown tool.";										
                                        }*/
                                        else {//uninstall
										    if ($e1 = $this->call_wizard_url('uninstalleshop')) 
											  $text = "<a href='$e1'>".$message."</a>"; 	
											else
 										      $text = "Unknown tool.";
                                        }										
                                        break;
               case 'ckfinder':         $text = "CKfinder installed"; break;
               case 'ieditor' :         $text = "IEditor installed"; break;
               case 'jqgrid'  :         $text = "JQgrid installed"; break;
			   case 'awstats' :         $text = "AWStats installed"; break;	
			   
			   case 'edit_htmlfiles':   $text = $this->edit_html_files(false, true, true);
			                            $icon_on =false;
			                            break; 
               case 'addkey'        :  	//always repeat...
			                            if ($e1 = $this->call_wizard_url('addkey')) 
											$text = "<a href='$e1'>".localize('_addkey',getlocal())."</a>"; 	
										 else
 										    $text = "Unknown tool.";
                                        break;	
			   case 'genkey'        :  	//always repeat...
			                            if ($e1 = $this->call_wizard_url('genkey')) 
											$text = "<a href='$e1'>".localize('_genkey',getlocal())."</a>"; 	
										 else
 										    $text = "Unknown tool.";
                                        break;	
               case 'validatekey'  :  	//always repeat...
			                            if ($e1 = $this->call_wizard_url('validatekey')) 
											$text = "<a href='$e1'>".localize('_validatekey',getlocal())."</a>"; 	
										 else
 										    $text = "Unknown tool.";
                                        break;	
               case 'cpimages'     :  	//always repeat...
			                            if ($e1 = $this->call_wizard_url('cpimages')) 
											$text = "<a href='$e1'>".localize('_cpimages',getlocal())."</a>"; 	
										 else
 										    $text = "Unknown tool.";
                                        break;											
		       default              :   //nothing
			                            $text = null;
		   }
		}
		//elseif (($ison>0) && (array_key_exists(strtoupper($tool),$this->environment))) {//($this->environment[strtoupper($tool)]==0)) {
		elseif ((array_key_exists(strtoupper($tool),$this->environment)) && 
		        ($this->environment[strtoupper($tool)]==0)) {//installed tool no privilege
		   //no priviledge
		   //do nothing...
		   //echo '<br/>',$tool,$seclevid,'>';
		}
        elseif ($ison>0) {//disabled tool..enable it, if local privilege is on
		   //echo $mytool.'<br/>';
		   switch ($mytool) {
		       case 'google_addwords'  : if ($e1 = $this->call_wizard_url('google_addwords'))
											$text = "<a href='$e1'>Enable addwords</a>"; 
										 else
 										    $text = "Unknown tool."; 
			                             break;		   
										 
		       case 'google_analytics' : if ($e1 = $this->call_wizard_url('google_analytics'))
											$text = "<a href='$e1'>Enable analytics</a>"; 
										 else
 										    $text = "Unknown tool.";
			                             break;
			   							 
			   case 'add_categories':    if ($e1 = $this->call_wizard_url('add_categories'))
											$text = "<a href='$e1'>Upload categories</a>"; 
										 else
 										    $text = "Unknown tool.";
			                             break;
               case 'add_products'  :    if ($e1 = $this->call_wizard_url('add_products'))
											$text = "<a href='$e1'>Upload products</a>"; 
										 else
 										    $text = "Unknown tool.";
			                             break;
               case 'upload_logo'   :    if ($e1 = $this->call_wizard_url('upload_logo')) {
											//$text = "<a href='$e1'>Change logo</a>"; 
											$text = "<a href='$e1'>Upload logo</a>";//<img src='../images/logo.png'/></a>"; 												
										 }	
										 else
 										    $text = "Unknown tool.";
			                             break;									
               case 'add_recaptcha'  :	 if ($e1 = $this->call_wizard_url('add_recaptcha')) 
											$text = "<a href='$e1'>Add recaptcha entry feature</a>"; 	
										 else
 										    $text = "Unknown tool.";									 
										 break;
               case 'backup'         :	 if ($e1 = $this->call_wizard_url('backup')) 
											$text = "<a href='$e1'>".localize('_backup_content',getlocal())."</a>"; 	
										 else
 										    $text = "Unknown tool.";									 
										 break;	
			   case 'maildbqueue'    :	if ($e1 = $this->call_wizard_url('maildbqueue')) 
											$text = "<a href='$e1'>".localize('_sendnewsletters',getlocal())."</a>"; 	
										 else
 										    $text = "Unknown tool.";									 
										 break;	
               case 'add_domainname' :	 if ($e1 = $this->call_wizard_url('add_domainname')) 
											$text = "<a href='$e1'>Change domain name</a>"; 	
										 else
 										    $text = "Unknown tool.";									 
										 break;	
			   case 'eshop'          :	if ($e1 = $this->call_wizard_url('eshop')) 
											$text = "<a href='$e1'>".localize('_installeshop',getlocal())."</a>"; 	
										 else
 										    $text = "Unknown tool.";									 
										 break;	
               case 'ckfinder':          if ((!is_dir($this->prpath.'/ckfinder')) && 
			                                 ($e1 = $this->call_wizard_url('ckfinder'))) 
											$text = "<a href='$e1'>".localize('_install',getlocal())."</a>"; 	
										 else
 										    $text = null;									 
										 break;	
               case 'ieditor' :          if ((!is_dir($this->prpath.'/ieditor')) &&
			                                ($e1 = $this->call_wizard_url('ieditor'))) 
											$text = "<a href='$e1'>".localize('_install',getlocal())."</a>"; 	
										 else
 										    $text = null;									 
										 break;	
               case 'jqgrid'  :          //echo $this->urlpath.'/javascripts/jqgrid';
			                             if ((!is_dir($this->urlpath.'/javascripts/jqgrid')) &&
			                                ($e1 = $this->call_wizard_url('jqgrid'))) 
											$text = "<a href='$e1'>".localize('_install',getlocal())."</a>"; 	
										 else
 										    $text = null;									 
										 break;	
               case 'awstats' :          if ($e1 = $this->call_wizard_url('awstats')) 
											$text = "<a href='$e1'>Enable AWStats</a>"; 	
										 else
 										    $text = "Unknown tool.";
										 break;	
										 
               case 'edit_htmlfiles'   : if ($e1 = $this->call_wizard_url('edit_htmlfiles')) 
											$text = "<a href='$e1'>Edit html files</a>"; 	
										 else
 										    $text = "Unknown tool.";									 
										 break;	
			   case 'addkey'        :  	//always repeat...
			                            if ($e1 = $this->call_wizard_url('addkey')) 
											$text = "<a href='$e1'>".localize('_addkey',getlocal())."</a>"; 	
										 else
 										    $text = "Unknown tool.";
                                        break;	
			   case 'genkey'        :  	//always repeat...
			                            if ($e1 = $this->call_wizard_url('genkey')) 
											$text = "<a href='$e1'>".localize('_genkey',getlocal())."</a>"; 	
										 else
 										    $text = "Unknown tool.";
                                        break;	
               case 'validatekey'  :  	//always repeat...
			                            if ($e1 = $this->call_wizard_url('validatekey')) 
											$text = "<a href='$e1'>".localize('_validatekey',getlocal())."</a>"; 	
										 else
 										    $text = "Unknown tool.";
                                        break;	
               case 'cpimages'     :  	//always repeat...
			                            if ($e1 = $this->call_wizard_url('cpimages')) 
											$text = "<a href='$e1'>".localize('_cpimages',getlocal())."</a>"; 	
										 else
 										    $text = "Unknown tool.";
                                        break;											
		       default                 : //nothing
			                             $text = null;
		   }		
        }
        //else disabled tool...		
		
		if ($text) {
		  if ($template) {
		    //echo $text,'<br/>';
		    $tool_url = $text ? ($e1 ? $e1 : "help/$mytool/") : null;
			$this->stats['Addons']['url'][] = $tool_url;
			$this->stats['Addons']['href'][] = $text;
			$_more = localize('_more',getlocal());
		    $ao = '<div class="msg-time-chat">
                        <a class="message-img" href="'.$tool_url.'"><img alt="" src="images/'.$mytool.'.png" class="avatar"></a>
                        <div class="message-body msg-in">
                            <span class="arrow"></span>
                            <div class="text">
                                <p>'.$text.'</p>
								<!--p class="attribution"><a href="/help/'.$mytool.'/">'.$_more.'</a> at 1:55pm, 13th April 2013</p-->
                            </div>
                        </div>
                   </div>';
			$this->stats['Addons']['html'] .= $ao;
          }
          else {		  
			if (($icons) && ($icon_on == true)) {
				$tool_url = $text ? ($e1 ? $e1 : "help/$mytool/") : null;
				$ic[] = $this->icon("images/$mytool.png",$tool_url,$text,4);
			}
			else {
				$mtitle = localize('_'.$mytool, getlocal());
				$win1 = new window2($mtitle,$text,null,1,null,'HIDE',null,1);
				$wtools .= $win1->render();
				unset ($win1);
			}	
		  }	
		}		
      }	//foreach	
	  }//if
	
	  if ($template) {
		return true;
	  }
	  else {
		if (($icons)&&(!empty($ic))) {
			$wtools .= $this->show_iconstable($ic,localize('_addons', getlocal()),4); 
		}
		//edit photo if id or cat is selected
		$wtools .= $this->change_photo_shortcut();
      }
      return ($wtools);	
    }	
  
    //check if eshop exist and is valid
    protected function is_valid_eshop() {
   	  
		$timekey = remote_paramload('SHCART','expires',$this->prpath);
		//echo $timekey;
		if ($timekey) {
			//$timeleft = $this->appkey->decode_key($timekey, 'SHCART', true);
			$date = $this->appkey->decode_key($timekey, 'SHCART');

			//if ($timeleft>0) {
			if ($date) {
				$daystosay = 30 * 24 * 60 *60; //30 days			
			    //if ($timeleft<($daystosay))//x days or negative=expired
			        //return true;
					
				$now = time();
				$diff = strtotime($date) - $now;
				//if ($diff<($daystosay)) {//x days or negative=expired					
					$ret = $this->appkey->nicetime($date); 
					//echo $ret;
					return ($ret);//true with text
				//}	
			}		
		}
	  
	    return false;
    } 

    //check if newsletter feature is valid
    protected function is_valid_newsletter() {
   	  
		//installed mailqueue key
		$timekey = remote_paramload('RCSHSUBSQUEUE','expires',$this->prpath);

		if ($timekey) {
			$date = $this->appkey->decode_key($timekey, 'RCSHSUBSQUEUE');

			if ($date) {
				$daystosay = 30 * 24 * 60 *60; //30 days			
					
				$now = time();
				$diff = strtotime($date) - $now;
				//if ($diff<($daystosay)) {//x days or negative=expired					
					$ret = $this->appkey->nicetime($date); 
					//echo $ret;
					return ($ret);//true with text
				//}	
			}		
		}

	    return false;
    }	
  
    //edit photo if id or cat is selected
    protected function change_photo_shortcut() {
		$qquery = str_replace('_&_', '_%26_', base64_decode($_GET['turl'])); //echo '>',$qquery,'>'; //& category problem
		$urlquery = parse_url($qquery); /*parse_url(base64_decode($_GET['turl']));*/ //echo $urlquery['query'];
		parse_str($urlquery['query'],$getp); //echo implode('.',$getp);  
	
		foreach ($getp as $p=>$v) {
			if (stristr($p,'cat'))
				$cat = $v;//urlencode($v);
			if (stristr($p,'id'))
				$id = $v;//urlencode($v);	
		}
		//print_r($getp);
		//extras
		//if (GetReq('id')) {
		if (defined('RCITEMS_DPC') && (($id)||($cat))) {	
			$out = GetGlobal('controller')->calldpc_method('rcitems.form_photo use 1+'.$cat.'+'.$id);
		}
		
		return ($out);	
    }
  
    protected function _show_update_tools($template=false) {   
        $text = 'update';
		$u = null;
		//check for time limited services
		$index = 0;
		if (($codeexpires = $this->get_code_expirations()) && (!empty($codeexpires))) {
		    foreach ($codeexpires as $section=>$exp_text) {
                $module = $section .'_DPC';
				$mod = localize($module, getlocal());
				$update_key_url = $this->call_wizard_url('addkey');//, true);	//is upgrade			
				if ($template) {
					$this->stats['Update']['value'] = ++$index;
				    $this->stats['Update']['url'][] = $update_key_url;
					$this->stats['Update']['href'][] = $exp_text;
					$html = '<li>
                                <span class="label label-warning"><i class="icon-bell"></i></span>
                                    <span><a href="'.$update_key_url.'">'.$exp_text.'</a></span>
                                    <div class="pull-right">
                                        <span class="small italic ">'.date("F d Y H:i:s.").'</span>
                                    </div>
                             </li>';
					if ($index<9) $this->stats['Update']['html'] .= $html;	
                    $notify = ' <li>
                                   <a href="#">
                                       <div class="task-info">
                                         <div class="desc">'.$exp_text.'</div>
                                         <div class="percent">44%</div>
                                       </div>
                                       <div class="progress progress-striped active no-margin-bot">
                                           <div class="bar" style="width: 44%;"></div>
                                       </div>
                                   </a>
                               </li>'; 
                    if ($index<5) $this->stats['Update']['notify'] .= $notify;							   
				}
				else
				  $u .= "<h3><a href='$update_key_url'>" . date("F d Y H:i:s.") . "&nbsp;".$mod."&nbsp;".$exp_text."</a></h3>";
			}
		}		
		
		//check for dpc upgrade
		if (($dpc2copy = $this->get_dpc_modules()) && (!empty($dpc2copy))) {
		    foreach ($dpc2copy as $d=>$dpc) {
				//automated dpc update
				$update_dpc_url = $this->call_wizard_url('dpcmod');//, true);	//is upgrade			
				if ($template) {
				    $this->stats['Update']['value'] = ++$index;
					$this->stats['Update']['url'][] = $update_dpc_url;
					$this->stats['Update']['href'][] = $dpc;
					$html = '<li>
                                <span class="label label-success"><i class="icon-bullhorn"></i></span>
                                    <span><a href="'.$update_dpc_url.'">'.$dpc.'</a></span>
                                    <div class="pull-right">
                                        <span class="small italic ">'.date("F d Y H:i:s.").'</span>
                                    </div>
                             </li>';
					if ($index<9) $this->stats['Update']['html'] .= $html;
                    $notify = ' <li>
                                   <a href="#">
                                       <div class="task-info">
                                         <div class="desc">'.$dpc.'</div>
                                         <div class="percent">44%</div>
                                       </div>
                                       <div class="progress progress-striped active no-margin-bot">
                                           <div class="bar" style="width: 44%;"></div>
                                       </div>
                                   </a>
                               </li>'; 
                    if ($index<5) $this->stats['Update']['notify'] .= $notify;					
				}
				else
					$u .= "<h3><a href='$update_dpc_url'>" . date("F d Y H:i:s.") . "&nbsp;".$dpc."</a></h3>";
			}	
		}
		
		//check for dac pages to upgrade
		if (($phpdac2copy = $this->get_dac_pages()) && (!empty($phpdac2copy))) {
		    foreach ($phpdac2copy as $p=>$dac) {
				//automated dpc update
				$update_dac_url = $this->call_wizard_url('dacpage');//, true);	//is upgrade			
				if ($template) {
				    $this->stats['Update']['value'] = ++$index;
					$this->stats['Update']['url'][] = $update_dac_url;
					$this->stats['Update']['href'][] = $dac;
					$html = '<li>
                                <span class="label label-important"><i class="icon-bullhorn"></i></span>
                                    <span><a href="'.$update_dac_url.'">'.$dac.'</a></span>
                                    <div class="pull-right">
                                        <span class="small italic ">'.date("F d Y H:i:s.").'</span>
                                    </div>
                             </li>';
					if ($index<9) $this->stats['Update']['html'] .= $html;
                    $notify = ' <li>
                                   <a href="#">
                                       <div class="task-info">
                                         <div class="desc">'.$dac.'</div>
                                         <div class="percent">44%</div>
                                       </div>
                                       <div class="progress progress-striped active no-margin-bot">
                                           <div class="bar" style="width: 44%;"></div>
                                       </div>
                                   </a>
                               </li>'; 
                    if ($index<5) $this->stats['Update']['notify'] .= $notify;					
				}
				else
					$u .= "<h3><a href='$update_dac_url'>" . date("F d Y H:i:s.") . "&nbsp;".$dac."</a></h3>";
			}	
		}	
		
		//check for free space
		if ($this->free_space() < (100*1024*1024)) { //get more in MB..100MB
		    //automated add space
		    $update_url = $this->call_wizard_url('addspace');//, true);	//is upgrade			
			if ($template) {
			    $this->stats['Update']['value'] = ++$index;
				$this->stats['Update']['url'][] = localize('_addspace', getlocal());
				$this->stats['Update']['href'][] = $update_url;
				$html = '<li>
                            <span class="label label-important"><i class=" icon-bug"></i></span>
                            <span><a href="'.$update_url.'">'.localize('_addspace', getlocal()).'</a></span>
                            <div class="pull-right">
                                <span class="small italic ">'.date("F d Y H:i:s.").'</span>
                            </div>
                        </li>';
				if ($index<9) $this->stats['Update']['html'] .= $html;
                $notify = ' <li>
                                <a href="#">
                                    <div class="task-info">
                                        <div class="desc">'.localize('_addspace', getlocal()).'</div>
                                        <div class="percent">44%</div>
                                    </div>
                                    <div class="progress progress-striped active no-margin-bot">
                                        <div class="bar" style="width: 44%;"></div>
                                    </div>
                                </a>
                            </li>'; 
                if ($index<5) $this->stats['Update']['notify'] .= $notify;
			}
			else
				$u .= "<h3><a href='$update_url'>" . date("F d Y H:i:s.") . "&nbsp;".localize('_addspace', getlocal())."</a></h3>";
		}
		
	    $updates = $this->read_update_directory();
		if (!empty($updates)) {
		    arsort($updates);
		    foreach ($updates as $update=>$udatecreated) {
			    //$u .= $udatecreated . '&nbsp;';
				
                $update_url = $this->call_wizard_url($update, true);
				if ($template) {
				    $this->stats['Update']['value'] = ++$index;
					$this->stats['Update']['url'][] = str_replace('_',' ',strtoupper($update));
					$this->stats['Update']['href'][] = $update_url;
					$html = '<li>
                                <span class="label label-warning"><i class="icon-bullhorn"></i></span>
                                    <span><a href="'.$update_url.'">'.str_replace('_',' ',strtoupper($update)).'</a></span>
                                    <div class="pull-right">
                                        <span class="small italic ">'.date("F d Y H:i:s.", $udatecreated).'</span>
                                    </div>
                             </li>';
					if ($index<9) $this->stats['Update']['html'] .= $html;
					$notify = ' <li>
                                <a href="#">
                                    <div class="task-info">
                                        <div class="desc">'.str_replace('_',' ',strtoupper($update)).'</div>
                                        <div class="percent">44%</div>
                                    </div>
                                    <div class="progress progress-striped active no-margin-bot">
                                        <div class="bar" style="width: 44%;"></div>
                                    </div>
                                </a>
                            </li>'; 
					if ($index<5) $this->stats['Update']['notify'] .= $notify;					
				}
				else				
					$u .= "<h3><a href='$update_url'>" . date("F d Y H:i:s.", $udatecreated) . "&nbsp;".str_replace('_',' ',strtoupper($update))."</a></h3>";
			}	
		}

		if ($template) {
			return true;
		}
		else {		
			if ($u) {
				$mtitle = localize('_update', getlocal());
				$win1 = new window2($mtitle,$u,null,1,null,'SHOW',null,1);
				$utools .= $win1->render();
				unset ($win1);
			}		   
			return ($utools);
		}	
    }
   
    protected function _show_tweets_rss($username=null) {
		$tuser = $username ? $username : 'balexiou';
   
		$ret = <<<EOF
<script charset="utf-8" src="http://widgets.twimg.com/j/2/widget.js"></script>
<script>
new TWTR.Widget({
  version: 2,
  type: 'profile',
  rpp: 4,
  interval: 30000,
  width: 225,
  height: 300,
  theme: {
    shell: {
      background: '#3B8EB6',
      color: '#000000'
    },
    tweets: {
      background: '#d4edf7',
      color: '#000000',
      links: '#f28a0b'
    }
  },
  features: {
    scrollbar: true,
    loop: false,
    live: false,
    behavior: 'default'
  }
}).render().setUser('balexiou').start();
</script>		 
EOF;
 
		$tweets = "<a class=\"twitter-timeline\" href=\"https://twitter.com/$tuser\" data-widget-id=\"247970993891573760\">Tweets by @$tuser</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\"://platform.twitter.com/widgets.js\";fjs.parentNode.insertBefore(js,fjs);}}(document,\"script\",\"twitter-wjs\");</script>";
		//www.rssinclude.com widget balexiou/basilVk7dP 
		$rss1 = " 
<div id=\"rssincl-box-container-786722\"></div>
<script type=\"text/javascript\">
(function() {
  var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true;
  s.src = 'http://output72.rssinclude.com/output?type=asyncjs&id=786722&hash=fa1d433a49b4c11a1e56dc3ccd22240f';
  document.getElementsByTagName('head')[0].appendChild(s);
})();
</script>
";
		//return ($tweets . $rss1);	
	
		$data1[] = $tweets;
		$attr1[] = "left;70%";
		$data1[] = $rss1;
		$attr1[] = "left;30%";
		$swin = new window(null,$data1,$attr1);
		$out = $swin->render();//"center::100%::0::::center::0::2::");		 
		unset ($swin);
	
		$win = new window2(localize("_TWEETSRSS",getlocal()),$out,null,1,null,'SHOW',null,1);
		$wout .= $win->render();
		unset($win);
		
		return ($wout);	 
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
  
    //zip directory
    function zip_directory($path=null, $name=null) {
        $d = date('Ymd-Hi');
		$zpath = $path ? $path : '';
        $zname = $name ? $d.'-'.$name : $d.'-'.'backup.zip'; 
		$dirname = $this->prpath . '/' . $zpath;
  
        //echo $dirname;
	    if (is_dir($dirname)) {
          $mydir = dir($dirname);
		  
		  $zip = new ZipArchive();
		  $zfilename = $this->prpath . "/uploads/" . $zname; //to save into

		  if ($zip->open($zfilename, ZipArchive::CREATE)!==TRUE) {
            return false;
          }		  
		 
          while ($fileread = $mydir->read ()) {
	        
		   if (($fileread!='.') && ($fileread!='..'))  {
		   
                 //echo "<br>",$fileread;	   
	             //read cpwizard- files
  	             if (!is_dir($fileread))  { 
                      
                    $zip->addFile($dirname."/".$fileread, $fileread);						
				 }
		   } 
	      }
	      $mydir->close ();
		  
          $ret = "numfiles: " . $zip->numFiles . "\n";
          $ret .= "status:" . $zip->status . "\n";
          $zip->close();		  
        }

		return ($ret);
    }    
  
    function read_directory($dirname) {
  
        if ($gr=GetReq('group'))
		  $dirname .= "/" . $gr;
  
        //echo $dirname;
	    if (is_dir($dirname)) {
          $mydir = dir($dirname);
		 
          while ($fileread = $mydir->read ()) {
	        
		   if (($fileread!='.') && ($fileread!='..'))  {
		   
                 //echo "<br>",$fileread;
				 		   
		         //read cp dirs
		         if ((is_dir($dirname."/".$fileread)) &&
		   		     ($fileread{0}==='c') &&
					 ($fileread{1}==='p')) {
					 
			         $ddir[] = $fileread;		 
		         } 		   
	             //read cp files
  	             if ((stristr ($fileread,".php")) &&
				     ($fileread{0}==='c') &&
					 ($fileread{1}==='p') &&
					 ($fileread != "cp.php")) {		   

		              $ddir[] = $fileread;						
					}
		   } 
	      }
	      $mydir->close ();
        }

		$this->cmds = $ddir;
		return ($ddir);
    }
  
    function show_directory()  {
  
	  //if (defined('SMSGUI_DPC')) {
	    //$ret = GetGlobal('controller')->calldpc_method('smsgui.sendsms use aekara2+306936550848+1');
	    //echo $ret;
	  //}	  

      if (is_array($this->cmds)) {
	
		$actions = GetGlobal('__ACTIONS');	//print_r($actions); 
	
		foreach ($this->cmds as $id=>$name) {
	
			$parts = explode(".",$name);
		
			if (isset($parts[1])) {//it means it is a file '.php'
				$obj = strtoupper(str_replace("cp","rc",$parts[0])."_DPC");
		
				if (defined($obj)) {
					$cmd = $actions[$obj][0];
					$title = localize($obj,getlocal());
	        
					$ret .= $id.". " .seturl("t=".$cmd,$title) ."<br>"; 
				}
			}
			else {//it is a dir with other .php files
				$t = GetReq('t');	
				$group = $parts[0];
				$title = substr($group,2); //exclude cp chars
				$ret .= $id.". " .seturl("t=$t&group=".$group,$title) ."<br>";
			}
		}	
	  }
	
	  //$ret .= seturl("t=cplogout","Logout");		
	  if (GetSessionParam('REMOTELOGIN')) {
		//$ret .= seturl("t=cpremotelogout","Logout");	
		if ($gr=GetReq('group')) {
			$t = GetReq('t'); 
			$ret .= seturl("t=$t","Back");		  
		}	
		else {
			$ret .= seturl("t=cpremotelogout","Logout");  
		}			  
	  }  
	  else {
		$ret .= "<hr>";
		$ret .= seturl("t=senticodes","Send invitation codes");
		$ret .= "<br>";
		$ret .= seturl("t=exportdb","Export database schema");
		$ret .= "<br>";
		$ret .= seturl("t=crackermail","Web mail");	
		$ret .= "<br>";	
	  	
		//$ret .= seturl("t=cplogout","Logout"); 
		if ($gr=GetReq('group')) {
			$t = GetReq('t'); 
			$ret .= seturl("t=$t","Back");		  
		}	
		else {
			$ret .= seturl("t=cpremotelogout","Logout");  
		}			   	
	  }  
	
	  return ($ret);
    }
  
  
    function show_directory_icons($type=0) {
	  $t = GetReq('t');  
      $mygroup = GetReq('group');  
      $cpfile = @file_get_contents($this->prpath.'cpmenu.txt');
      $menu_direct_file = explode(',',$cpfile);  
  
      if (is_array($this->cmds)) {
		$actions = GetGlobal('__ACTIONS');	//print_r($actions); 
	
		foreach ($this->cmds as $id=>$name) {
	
			$parts = explode(".",$name);

			if (isset($parts[1])) {//it means it is a file '.php'		
			  $obj = strtoupper(str_replace("cp","rc",$parts[0])."_DPC");
			  $obj2 = strtolower($obj);
		  
			  if (defined($obj)) {
				$cmd = $actions[$obj][0];
				$url = ($mygroup ? $mygroup.'/'.$name:$name); //as php file			
				$title = localize($obj,getlocal());
	
				if (in_array($parts[0],$menu_direct_file)) 
					$ret .= icon2("/icons/$obj2.gif",$url,$title,$type);  
				else 
					$ret .= icon("/icons/$obj2.gif","t=".$cmd,$title,$type); 
				 
			  }
			}
			else {//it is a dir with other .php files
		      $group = $parts[0];
		      $title = substr($group,2); //exclude cp chars
		      if ($this->ajaxgraph) {
				$goto = seturl('t=cpmenushow&group='.$group.'&ai=1&statsid=');
				$js = "onclick=\"sndReqArg('".$goto."'+statsid.value,'menu')\""; 
				$ret .= "<a href=\"#\">". loadicon('/icons/'.$group.'.gif',$title,$js)."<br>$title</a>"; 			  
			  }			   
		      else 		   
				$ret .= icon("/icons/$group.gif","t=$t&group=".$group,$title,$type);
			}		  
		}
	  }
	
	  //$ret .= icon("/icons/cplogout.gif","t=cplogout","Logout",$type);	
	  if (GetSessionParam('REMOTELOGIN')) {
	    //$ret .= icon("/icons/cplogout.gif","t=cpremotelogout","Logout",$type);	
	    if ($gr=GetReq('group')) {
		  if ($this->ajaxgraph) {
		    $goto = seturl('t=cpmenushow&group=&ai=1&&statsid=');
   	        $js = "onclick=\"sndReqArg('".$goto."'+statsid.value,'menu')\""; 
		    $items[] = "<a href=\"#\">". loadicon('/icons/cplogout.gif',localize('_BACKCP',getlocal()),$js)."<br>".localize('_BACKCP',getlocal())."</a>"; 			  
		  }			   
		  else
            $ret .= icon("/icons/cplogout.gif","t=$t",localize('_BACKCP',getlocal()),$type);		  
	    }	
	    else {
          $ret .= icon("/icons/cplogout.gif","t=cpremotelogout","Logout",$type);	  
	    }		
	  }  
	  else {
	
	    $ret .= "<hr>";
	    $ret .= icon("/icons/rcsenticodes_dpc.gif","t=senticodes","Send invitation codes",$type);
	    $ret .= icon("/icons/rcexportdb_dpc.gif","t=exportdb","Export database schema",$type);
	    //$ret .= icon("/icons/rcmailcracker_dpc.gif","t=crackermail","Web mail",$type);	    
	
	    //$ret .= icon("/icons/cplogout.gif","t=cplogout","Logout",$type);		
	    if ($gr=GetReq('group')) { 
		  if ($this->ajaxgraph) {
		    $goto = seturl('t=cpmenushow&group=&ai=1&&statsid=');
   	        $js = "onclick=\"sndReqArg('".$goto."'+statsid.value,'menu')\""; 
		    $items[] = "<a href=\"#\">". loadicon('/icons/cplogout.gif',localize('_BACKCP',getlocal()),$js)."<br>".localize('_BACKCP',getlocal())."</a>"; 			  
		  }			   
		  else		
            $ret .= icon("/icons/cplogout.gif","t=$t",localize('_BACKCP',getlocal()),$type);		  
	    }	
	    else {
          $ret .= icon("/icons/cplogout.gif","t=cplogout","Logout",$type);	  
	    }			  
	  }  
	
	  return ($ret);	
    }

    protected function icon($iconpath='',$url,$title,$vtype=0,$ssl=0) {
        $comment = null;
		$jscript = null;
	    $loadicon = $out = "<img src=\"$iconpath\" border=\"0\" alt=\"$comment\" $jscript>";
		$iconimg = $url ? "<a href='$url'>". $loadicon . '</a>' : $loadicon; 
		$icontitle = $title;
	
        //icon image and title at bottom
	    if ($iconpath) {
	        $iwin1 = new window('',$iconimg);
	        $out = $iwin1->render("center::100%::0::group_icons::center::0::0::");
	        unset ($iwin1);
	    }
	    $iwin2 = new window('',$icontitle);
	    $out .= $iwin2->render("center::100%::0::group_icons::center::0::0::");
	    unset ($iwin2);
		
		return ($out);
    }				
	
	protected function show_iconstable($items=null,$title=null,$linemax=4) {
	  if (empty($items)) return;
	
	  $itemscount = count($items);
	  $timestoloop = floor($itemscount/$linemax)+1;
	  $meter = 0;
	  for ($i=0;$i<$timestoloop;$i++) {
		//echo $i,"---<br>";
		for ($j=0;$j<$linemax;$j++) {
			//echo $i*$j,"<br>";
			
			//$viewdata[] = (isset($items[$meter])? $items[$meter] : "&nbsp");
			$item = array_shift($items);//get first from list
			$viewdata[] = (isset($item)? $item : "&nbsp");
			$viewattr[] = "center;10%";	
			$meter+=1;	 
	    }
	  
	    $myrec = new window('',$viewdata,$viewattr);
	    $ret .= $myrec->render("center::100%::0::group_article_selected::left::0::0::");
	    unset ($viewdata);
	    unset ($viewattr);		  
	  }
	  
	  if ($ret) {
	    $mytitle = $title ? $title  : '';
		if ($mytitle) {
			$myrec = new window2($mytitle,$ret,null,1,null,'HIDE',null,1);
			$winret = $myrec->render();
		}
		else
		    $winret = $ret;
	  }

	  return ($winret);	
	}
  
    function show_directory_iconstable($type=0,$linemax=4,$cmds=null) {
      $t = GetReq('t');     
      $mygroup = GetReq('group');
      $cpfile = @file_get_contents($this->prpath.'cpmenu.txt');
      $menu_direct_file = explode(',',$cpfile);
      //print_r($menu_direct_file);

      if ($cmds)
	    $this->cmds = unserialize($cmds);     
  
      if (is_array($this->cmds)) {
	
	    $actions = GetGlobal('__ACTIONS');	//print_r($actions); 
	
        foreach ($this->cmds as $id=>$name) {	
			$parts = explode(".",$name);
		
			if (isset($parts[1])) {//it means it is a file '.php'				
				$obj = strtoupper(str_replace("cp","rc",$parts[0])."_DPC");
				$obj2 = strtolower($obj);
				//echo $obj,"<br>";
				if (defined($obj)) {
					$cmd = $actions[$obj][0];//from cp as t=xxx
					$url = ($mygroup ? $mygroup.'/'.$name:$name); //as php file
					$title = localize($obj,getlocal());// . "|<a href=\"#\" onclick=\"remove()\">X</a>";
	
					if (in_array($parts[0],$menu_direct_file)) {

						if ($this->ajaxgraph) {		  			  
							$items[] = icon2("/icons/$obj2.gif",$url,$title,$type); 
						} 
						else 			
							$items[] = icon2("/icons/$obj2.gif",$url,$title,$type); 
					}  
					else {
			
						if ($this->ajaxgraph) {
							$items[] = icon("/icons/$obj2.gif","t=".$cmd,$title,$type); 			  
						}			   
						else 			
							$items[] = icon("/icons/$obj2.gif","t=".$cmd,$title,$type); 	
					}  
				}
			}
			else {//it is a dir with other .php files
				$group = $parts[0];
				$title = substr($group,2); //exclude cp chars
		   
				if ($this->ajaxgraph) {
					$goto = seturl('t=cpmenushow&group='.$group.'&ai=1&statsid=');
					$js = "onclick=\"sndReqArg('".$goto."'+statsid.value,'menu')\""; 
					$items[] = "<a href=\"#\">". loadicon('/icons/'.$group.'.gif',$title,$js)."<br>$title</a>"; 			  
				}			   
				else 		   
					$items[] = icon("/icons/$group.gif","t=$t&group=".$group,$title,$type);
			}		  
		}
	  }
	
	
	  if (GetSessionParam('REMOTELOGIN')) {
		//$items[] = icon("/icons/cplogout.gif","t=cpremotelogout","Logout",$type);		
		if ($gr=GetReq('group')) {
			if ($this->ajaxgraph) {
				$goto = seturl('t=cpmenushow&group=&ai=1&statsid=');
				$js = "onclick=\"sndReqArg('".$goto."'+statsid.value,'menu')\""; 
				$items[] = "<a href=\"#\">". loadicon('/icons/cplogout.gif',localize('_BACKCP',getlocal()),$js)."<br>".localize('_BACKCP',getlocal())."</a>"; 			  
			}			   
			else
				$items[] = icon("/icons/cplogout.gif","t=$t",localize('_BACKCP',getlocal()),$type);		  
		}	
		else {
			$items[] = icon("/icons/cplogout.gif","t=cpremotelogout","Logout",$type);	  
		}			  
	  }  
	  else {
		//$items[] = icon("/icons/rcsenticodes_dpc.gif","t=senticodes","Send invitation codes",$type);
		//$items[] = icon("/icons/rcexportdb_dpc.gif","t=exportdb","Export database schema",$type);
		//$items[] = icon("/icons/rcmailcracker_dpc.gif","t=crackermail","Web mail",$type);	    	
	
		//$items[] = icon("/icons/cplogout.gif","t=cplogout","Logout",$type);		
	    if ($gr=GetReq('group')) {
			if ($this->ajaxgraph) {
				$goto = seturl('t=cpmenushow&group=&ai=1&statsid=');
				$js = "onclick=\"sndReqArg('".$goto."'+statsid.value,'menu')\""; 
				$items[] = "<a href=\"#\">". loadicon('/icons/cplogout.gif',localize('_BACKCP',getlocal()),$js)."<br>".localize('_BACKCP',getlocal())."</a>"; 			  
			}			   
			else 
				$items[] = icon("/icons/cplogout.gif","t=$t",localize('_BACKCP',getlocal()),$type);		  
		}	
		else {
			$items[] = icon("/icons/cplogout.gif","t=cplogout","Logout",$type);	  
		}			  
	  }
	
	  $itemscount = count($items);
	  $timestoloop = floor($itemscount/$linemax)+1;
	  $meter = 0;
	  for ($i=0;$i<$timestoloop;$i++) {
		//echo $i,"---<br>";
		for ($j=0;$j<$linemax;$j++) {
			//echo $i*$j,"<br>";
			$viewdata[] = (isset($items[$meter])? $items[$meter] : "&nbsp");
			$viewattr[] = "center;10%";	
			$meter+=1;	 
	    }
	  
	    $myrec = new window('',$viewdata,$viewattr);
	    $ret .= $myrec->render("center::100%::0::group_article_selected::left::0::0::");
	    unset ($viewdata);
	    unset ($viewattr);		  
	  }

	  return ($ret);	
    } 
  
    function ajax_menu($type=0,$linemax=4,$cmds=null,$xmlout=null) {
	  $t = GetReq('t');     
      $mygroup = GetReq('group');
      $cpfile = @file_get_contents($this->prpath.'cpmenu.txt');
      $menu_direct_file = explode(',',$cpfile);
      //print_r($menu_direct_file);

      if ($cmds)
	    $this->cmds = unserialize($cmds);     
  
      if (is_array($this->cmds)) {
	
	   $actions = GetGlobal('__ACTIONS');	//print_r($actions); 
	
       foreach ($this->cmds as $id=>$name) {	
	    $parts = explode(".",$name);
		
		if (isset($parts[1])) {//it means it is a file '.php'				
		    $obj = strtoupper(str_replace("cp","rc",$parts[0])."_DPC");
		    $obj2 = strtolower($obj);
		    //echo $obj,"<br>";
		    if (defined($obj)) {
				$cmd = $actions[$obj][0];//from cp as t=xxx
				$url = ($mygroup ? $mygroup.'/'.$name:$name); //as php file
				$title = localize($obj,getlocal());// . "|<a href=\"#\" onclick=\"remove()\">X</a>";
	
				//if (in_array($parts[0],$menu_direct_file)) {	//disable CPMENU.TXT	  			  
				if (is_readable($this->path.'/'.$url)) {
					//echo $this->path . '/'. $url,'<br>';			
					$items[] = icon2("/icons/$obj2.gif",$url,$title,$type); 
				}  
				else {
					//echo $cmd,'<br>';
					$items[] = icon("/icons/$obj2.gif","t=".$cmd,$title,$type); 			  
				}  
			}
	    }
		else {//it is a dir with other .php files
		   $group = $parts[0];
		   $title = substr($group,2); //exclude cp chars
		   $goto = seturl('t=cpmenushow&group='.$group.'&ai=1&statsid=');
		   $js = "onclick=\"sndReqArg('".$goto."'+statsid.value,'menu')\""; 
		   $items[] = "<a href=\"#\">". loadicon('/icons/'.$group.'.gif',$title,$js)."<br>$title</a>"; 			  
		}		  
	   }
	  }
	
	  if (GetSessionParam('REMOTELOGIN')) {
		//$items[] = icon("/icons/cplogout.gif","t=cpremotelogout","Logout",$type);		
		if ($gr=GetReq('group')) {
			$goto = seturl('t=cpmenushow&group=&ai=1&statsid=');
			$js = "onclick=\"sndReqArg('".$goto."'+statsid.value,'menu')\""; 
			$items[] = "<a href=\"#\">". loadicon('/icons/cplogout.gif',localize('_BACKCP',getlocal()),$js)."<br>".localize('_BACKCP',getlocal())."</a>"; 			  
		}	
		else {
			$items[] = icon("/icons/cplogout.gif","t=cpremotelogout","Logout",$type);	  
		}			  
	  }  
	  else {	
		if ($gr=GetReq('group')) {
			$goto = seturl('t=cpmenushow&group=&ai=1&statsid=');
			$js = "onclick=\"sndReqArg('".$goto."'+statsid.value,'menu')\""; 
			$items[] = "<a href=\"#\">". loadicon('/icons/cplogout.gif',localize('_BACKCP',getlocal()),$js)."<br>".localize('_BACKCP',getlocal())."</a>"; 			  		  
		}	
		else {
			$items[] = icon("/icons/cplogout.gif","t=cplogout","Logout",$type);	  
		}			  
	  }
	
	  if ($xmlout) {
		$xml = new pxml(null,$this->charset);//'test');
		$xml->encoding = $this->charset;  
		$xml->addtag('menu',null,null,"version=2.0");
	  
		foreach ($items as $i=>$item)
			$xml->addtag($i,'menu',$item,null);
		
		$ret = $xml->getxml(1);	
	  }
	  else {
		$itemscount = count($items);
		$timestoloop = floor($itemscount/$linemax)+1;
		$meter = 0;
		for ($i=0;$i<$timestoloop;$i++) {
			//echo $i,"---<br>";
			for ($j=0;$j<$linemax;$j++) {
				//echo $i*$j,"<br>";
				$viewdata[] = (isset($items[$meter])? $items[$meter] : "&nbsp");
				$viewattr[] = "center;10%";	
				$meter+=1;	 
			}
	  
			$myrec = new window('',$viewdata,$viewattr);
			$ret .= $myrec->render("center::100%::0::group_article_selected::left::0::0::");
			unset ($viewdata);
			unset ($viewattr);		  
		}
	  }
	
	  return ($ret);	
    }   

  
    function nitobi_menu($cp=0,$cs=0,$linemax=null,$cmds=null) {
      $linemax = $linemax?$linemax:4;
      $mygroup = GetReq('group');
      $cpfile = str_replace('\r\n','',@file_get_contents($this->path.'/cpmenu.txt'));
      $cdetail = @parse_ini_file($this->path.'/control_details.ini',1);
      $menu_group_file = explode(',',trim($cpfile));
	
	  foreach ($menu_group_file as $i=>$args) {
		//echo $args,'<br>';
		$x = explode(':',trim(str_replace('\r\n','',$args)));
		//[dpc]=group
		//echo $x[0],'>',$x[1],'<br>';
		$g[strtoupper($x[0]).'_DPC'] = $x[1];
	  }  
	  $a = count(g);
      //print_r($g);
	  //echo $a;
	
	  //VIEW BY LOCALES (DPC LOADED)
	  $l = GetGlobal('__LOCALE'); //print_r($l);
	  foreach ($l as $dpc=>$dpcdata) {
	   //echo $dpcdata[0],'<br>';
	   if ((substr($dpc,0,2)=='RC') && (substr($dpc,-4)=='_DPC')) {
	    //echo $dpc,'<br>';
		if ($group = $g[$dpc])
		  $menu[$group][] = $dpc;
		else    
          $menu[] = $dpc; 
	   } 	
	  }  
	  //echo '<pre>';
      //print_r($menu);
	  //echo '</pre>';
	  $actions = GetGlobal('__ACTIONS');
      //$details = $cdetail[$titles[$id]]['detail'];//???			
	
	  if (is_array($menu)) {
        foreach ($menu as $id=>$dpcname) {
  	     if (is_array($dpcname)) {
		   foreach ($dpcname as $i=>$dpcn) {
		     $items[$id][] = $this->make_icon($dpcn,$actions,$details);
		   }	 
		   $items[$id][] = $this->make_icon('back');	 		   
		 }
		 else {
		   $items[] = $this->make_icon($dpcname,$actions,$details);
		 }
        } 
	    $items[] = $this->make_icon('logout');				
	  }
	
	  //print_r($items);
	
	  foreach ($items as $it=>$item) {
  	     if (is_array($item)) {
		   $mgroup = null; 
		   foreach ($item as $i=>$groupicon) {		 
		     $mgroup .= $groupicon; 
		   }
	       //$win = new window2($it,$mgroup,null,1,null,'SHOW',null,1);
	       //$ret .= $win->render("center::100%::0::group_article_selected::left::0::0::");
	       //unset ($win);
		   $ret .= '<br>'.$it.'<hr>'.$mgroup;		   
		 }
		 else {
           $mroot.= $item;		 
		 }		 		  	
	  }
	  //$win = new window2('Menu',$mroot,null,1,null,'SHOW',null,1);
	  //$ret .= $win->render("center::100%::0::group_article_selected::left::0::0::");
	  //unset ($win);
	  $ret .= '<br>'.'<hr>'.$mroot;		
	
	  return ($ret);
    }
  
    function make_icon($dpcname,$actions=null,$details=null) {
  
	    if ($dpcname=='back') { 
		  $js = "onclick=\"add('".$title."','cp.php?t=".$cmd."')\""; 
		  $ret = "<a href=\"#\">".loadicon('/icons/cplogout.gif',localize('_BACKCP',getlocal()),$js)."</img>".localize('_BACKCP',getlocal())."</a>";		  		  
	    }	
	    elseif ($dpcname=='logout') { 
		  $js = "onclick=\"add('".$title."','cp.php?t=cplogout')\""; 
		  $ret = "<a href=\"cp.php?t=cplogout\">".loadicon('/icons/cplogout.gif','Logout')."</img>Logout</a>";		  	  
	    }  
		elseif (defined($dpcname)) {

		  $title = localize($dpcname,getlocal());			
	      $parts = explode("_",$dpcname);
		  $obj = strtolower($dpcname);		  
						
		  $file = strtolower(str_replace("RC","CP",$parts[0]).".php");
		  $cmdfile = $this->path .'/'. $file; //echo $cmdfile,'<br>';
		  
		  if (is_readable($cmdfile)) {//.php exist
		      $url = $file;

		      $js = "onclick=\"add('".$title."','".$url."')\""; 
			  $ret = "<a href=\"#\" alt=\"$title\">".loadicon('/icons/'.$obj.'.gif',$title,$js)."</img>$title</a>";
			  //echo $js."<br>";

		  }  
		  else {//.php doesn.t exist
			    $cmd = $actions[$dpcname][0];
			    //$nurl = seturl("t=".$cmd);
			    $js = "onclick=\"add('".$title."','cp.php?t=".$cmd."')\""; 
			    $ret = "<a href=\"#\">".loadicon('/icons/'.$obj.'.gif',$title,$js)."</img>$title</a>";
			    //echo $js."<br>";	
		  } 
		}  

		return ($ret);  
    }
  	    
    function show_directory_details($type=2,$cmds=null) {
		$t = GetReq('t');   
		$mygroup = GetReq('group');  
		$cpfile = @file_get_contents($this->prpath.'cpmenu.txt');
		$menu_direct_file = explode(',',$cpfile);
	  
		if ($cmds)
			$this->cmds = unserialize($cmds);
  
		$type=2;
		$cdetail = @parse_ini_file($this->prpath.'control_details.ini',1);	
		//echo $this->prpath;
		//print_r($cdetail);  
  
		if (is_array($this->cmds)) {
	  
			$actions = GetGlobal('__ACTIONS');	//print_r($actions); 
	
			foreach ($this->cmds as $id=>$name) {
	
				$parts = explode(".",$name);
		
				if (isset($parts[1])) {//it means it is a file '.php'
					$obj = strtoupper(str_replace("cp","rc",$parts[0])."_DPC");
					$obj2 = strtolower($obj);
					//echo $obj,"<br>";
					if (defined($obj)) {
						$cmd = $actions[$obj][0]; //from cp as t=xxx
						$url = $url = ($mygroup ? $mygroup.'/'.$name:$name);  //as php file			
						$title = localize($obj,getlocal());
						$titles[] = localize($obj,getlocal());
						$cmds[] = seturl("t=".$cmd,$title);
	
						if (in_array($parts[0],$menu_direct_file)) {	
		
							$items[] = icon2("/icons/$obj2.gif",$url,$title,$type); 						
						}  
						else {  			
							$items[] = icon("/icons/$obj2.gif","t=".$cmd,$title,$type); 
						}  
					}
				}
				else {//it is a dir with other .php files
					$group = $parts[0];
					$title = substr($group,2); //exclude cp chars
					$titles[] = $title;
					$cmds[] = seturl("t=$t&group=".$group,$title);		
					if ($this->ajaxgraph) {
						$goto = seturl('t=cpmenushow&group='.$group.'&ai=1&statsid=');
						$js = "onclick=\"sndReqArg('".$goto."'+statsid.value,'menu')\""; 
						$items[] = "<a href=\"#\">". loadicon('/icons/'.$group.'.gif',$title,$js)."<br>$title</a>"; 			  
					}			   
					else		   
						$items[] = icon("/icons/$group.gif","t=cp&group=".$group,$title,$type);
				}		  
			}
		}
	
		if (GetSessionParam('REMOTELOGIN')) {
			$items[] = icon("/icons/cplogout.gif","t=cpremotelogout","Logout",$type);		
	  
		  if ($gr=GetReq('group')) {
			$title = localize('_BACKCP',getlocal());
			$titles[] = localize('_BACKCP',getlocal());
			if ($this->ajaxgraph) {
				$goto = seturl('t=cpmenushow&group=&ai=1&statsid=');
				$js = "onclick=\"sndReqArg('".$goto."'+statsid.value,'menu')\""; 
				$cmds[] = "<a href=\"#\">". loadicon('/icons/cplogout.gif',localize('_BACKCP',getlocal()),$js)."<br>".localize('_BACKCP',getlocal())."</a>"; 			  
			}			   
			else				
				$cmds[] = seturl("t=$t",$title);	  
		  }	
		  else {
			$title = "Exit";
			$titles[] = "Exit";	  
			$cmds[] = seturl("t=cpremotelogout",$title);	  
		  }	
		}  
		else {
	
		  $items[] = icon("/icons/rcsenticodes_dpc.gif","t=senticodes","Send invitation codes",$type);
		  $items[] = icon("/icons/rcexportdb_dpc.gif","t=exportdb","Export database schema",$type);
	      //$items[] = icon("/icons/rcmailcracker_dpc.gif","t=crackermail","Web mail",$type);	  //...IMPORETD AS REGULAR RC component
	
	      //$items[] = icon("/icons/cplogout.gif","t=cplogout","Logout",$type);		
	      if ($gr=GetReq('group')) {
		    if ($this->ajaxgraph) {
				$goto = seturl('t=cpmenushow&group=&ai=1&statsid=');
				$js = "onclick=\"sndReqArg('".$goto."'+statsid.value,'menu')\""; 
				$items[] = "<a href=\"#\">". loadicon('/icons/cplogout.gif',localize('_BACKCP',getlocal()),$js)."<br>".localize('_BACKCP',getlocal())."</a>"; 			  
			}			   
			else 
				$items[] = icon("/icons/cplogout.gif","t=$t",localize('_BACKCP',getlocal()),$type);		  
		  }	
		  else {
			$items[] = icon("/icons/cplogout.gif","t=cplogout","Logout",$type);	  
		  }			
		} 
		
		//print_r($items);
		foreach ($items as $id=>$myitem) {
	
			$details = $cdetail[$titles[$id]]['detail'];
	
			$viewdata[] = $myitem;
			$viewattr[] = "center;10%";
	  
			$viewdata[] = "<B>" . $cmds[$id] . "</B><br>" . $details;
			$viewattr[] = "left;90%";	  
	
			$myrec = new window('',$viewdata,$viewattr);
			$ret .= $myrec->render("center::100%::0::group_article_selected::left::0::0::");
			unset ($viewdata);
			unset ($viewattr);		  
		}	
	
		return ($ret);
    }     
  
    function logincp_form($nav_off=null,$tokens=null) {
	
	    if (!$nav_off) 
		  $out = setNavigator($this->title);
	 
	    if ($this->editmode)
		  $filename = seturl("t=cp&editmode=1",0,1);
		else
          $filename = seturl("t=cp",0,1);
	  	
		if ($tokens) {
		  $token[] = "<FORM action=". $filename . " method=post class=\"thin\">" . 
                      "<input type=\"text\" name=\"cpuser\" value=\"\" size=\"32\" maxlength=\"128\">";		  
		}  
		else {
          $toprint  = "<FORM action=". $filename . " method=post class=\"thin\">";
	      $toprint .= "<STRONG>Username:</STRONG>"; 
          $toprint .= "<input type=\"text\" name=\"cpuser\" value=\"\" size=\"32\" maxlength=\"128\"><br>";  		
		}
		
		if ($tokens) {
          $token[] = "<input type=\"password\" name=\"cppass\" value=\"\" size=\"32\" maxlength=\"128\">";		
		}
		else {
          $toprint .= "<STRONG>Password:</STRONG>"; 
	      $toprint .= "<input type=\"password\" name=\"cppass\" value=\"\" size=\"32\" maxlength=\"128\"><br>";
        }
		
		if ($tokens) {
	      $token[] = "<input type=\"submit\" name=\"Submit\" value=\"Ok\">" .
                     "<input type=\"hidden\" name=\"FormAction\" value=\"cplogin\">" .
		             "<input type=\"hidden\" name=\"AUTHENTICATE\" value=\"Login\">" .	
                     "</FORM>";	
				   
		  return ($token);		   		
		}
		else {
	      $toprint .= "<input type=\"submit\" name=\"Submit\" value=\"Ok\">"; 
          $toprint .= "<input type=\"hidden\" name=\"FormAction\" value=\"cplogin\">";
		
		  //enable AUTH
		  $toprint .= "<input type=\"hidden\" name=\"AUTHENTICATE\" value=\"Login\">";
				
          $toprint .= "</FORM>";	   
		
		
	      $swin = new window("Login",$toprint);
	      $out .= $swin->render("center::50%::0::group_dir_body::left::0::0::");	
	      unset ($swin);

          return ($out);
		}
    } 
  
    function verify_login() {
		//in case of instance app login goto root db
		$mydb = & GetGlobal('controller')->calldpc_method('database.switch_db use +1+1');  
	
		$db = GetGlobal('db');  
  
		if (($user=GetParam('cpuser')) && ($pwd=GetParam('cppass'))) {
	
			//get running application info
			$is_instance_app = paramload('ID','isinstance');
			//echo $is_instance_app; 
			$appname = paramload('ID','instancename');
			//echo '>',$appname;
	  
			//INSERT ROOT USER
			//$sins = "insert into dpcmodules (user,pwd,appname) values ('root','rootvk7dp','root')";
			//$result = $db->Execute($sins,1);	  
	   
			$sSQL .= "select user,pwd,appname from dpcmodules where user='$user' and pwd='$pwd' and active=1";
			$result = $mydb->Execute($sSQL,2);	
			//echo $sSQL;
			//print_r($result);
	  
			//if username & password exists
			if (($result->fields[0]==$user) && ($result->fields[1]==$pwd)) {
	  
				//restore app db
				GetGlobal('controller')->calldpc_method('database.switch_db');//null = this app// use '.$appname); 	  

				//must be instance and appname be correct
				if (($is_instance_app) && ($result->fields[2]==$appname)) {
				  SetSessionParam('LOGIN','yes');	
				  SetSessionParam('USER',$user);	
				  return true;
				}//else is no instance (root app) appname=root  
				elseif ((!$is_instance_app) && ($result->fields[2]=='root')) {
				  SetSessionParam('LOGIN','yes');	
				  SetSessionParam('USER',$user);	
				  SetSessionParam('ADMIN','yes');
				  return true;
				}  
				else
				  return false;
			}	
		}	
		return false;
    } 
  
	function get_user_name($nopro=0) {
	
	  //return (GetSessionParam('USER'));
	  if (!$nopro)
	    $pro = "User:";
	  
	  if (GetSessionParam('LOGIN')) {
	  
	    if ($user=GetSessionParam('USER'))
	      $ret = $pro . $user;
	    else
	      $ret = null;
		
	    //echo $ret;		
	  }
	  
	  return $ret;	  
	}

    function show_tree($param=null) {
  
          ////////////////////////////////////////////////////////////////// tree test			
	      $out .= $this->_ctree->set_tree('sthandler.php?key='.GetReq('key'),'folders',300,100);
		  
		  return ('a'.$out);  
    }	  
	
    function sidewin() {
	  if (GetSessionParam('LOGIN')=='yes') {          

          $ret = GetGlobal('controller')->calldpc_method('rcsidewin.set_show_calldpc use rccontrolpanel.show_tree PARAMS cpitems');		  
		  
		  return ($ret);
	  }	  
    }
  
    function combine_tokens($template_contents,$tokens=null) {
	
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
  
    function autoupdate() {
     //echo $_SERVER['PHP_SELF'];
	 /*$rf = file_get_contents('http://www.stereobit.com/cp/cp.php');
	 $hf = file_get_contents($this->path .'/cp.php');
	 if (strlen($rf)!=strlen($hf)) {
	   echo 'must update...';
	 }*/
    }
  
	
    function getencoding() {

	  return ($this->charset);
    }
  
    function logout() {
  
		SetSessionParam('LOGIN',null);
		SetSessionParam('USER',null);
		//SetSessionParam('ADMIN',null); //to not propagated!?just close navigator window
    }
  
    protected function bytesToSize1024($bytes, $precision = 2) {
        $unit = array('B','KB','MB','GB');
        return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision).' '.$unit[$i];
    }  
 
 
    function filesize_r($path){
		if (!file_exists($path)) 
			return 0;
		
	    if (is_file($path)) 
			return filesize($path);
			
		$ret = 0;
		
		$glob = glob($path."/*");
		
		if (is_array($glob)) {
			foreach(glob($path."/*") as $fn)
				$ret += $this->filesize_r($fn);
		}	
		return $ret;

    } 
	
    function cached_mail_size($year=null, $month=null) {
	   // /home/stereobi/mail/domain
	   $path = '/home/stereobi/mail/' . str_replace('www.','',$this->url);
  	   //$path = $this->prpath . '../../../../mail/' . str_replace('www.','',$this->url); // ./mail/domainname
	   //echo $path,'>>>';
       $name = strval(date('Ymd'));
       $msize = $this->prpath . $name . '-msize.size';
	   $size = 0;
	   
	   if (($year) && ($month)) {
			$selected_name = sprintf('%04d',$year) . sprintf('%02d',$month);
			for ($d=31;$d>0;$d--) {
				$search_selected_name = $selected_name . sprintf('%02d',$d);
				if (is_readable($this->prpath . $search_selected_name . '-msize.size'))
					$msize = $this->prpath . $search_selected_name . '-msize.size';
			}
	   }
	   //else msize of today...
       if (is_readable($msize)) {
	        //echo $msize;
			$size = @file_get_contents($msize);

	   }
	   else {
            $size = $this->filesize_r($path);
			@file_put_contents($msize, $size);
	   }
	   
	   return ($size);
    }	
  
    function cached_disk_size($year=null, $month=null) {
  	   $path = $this->application_path; 
       $name = strval(date('Ymd'));
       $tsize = $this->prpath . $name . '-tsize.size';
	   $size = 0;
	   
	   if (($year) && ($month)) {	
			$selected_name = sprintf('%04d',$year) . sprintf('%02d',$month);
			for ($d=31;$d>0;$d--) {
				$search_selected_name = $selected_name . sprintf('%02d',$d);
				if (is_readable($this->prpath . $search_selected_name . '-tsize.size'))
						$tsize = $this->prpath . $search_selected_name . '-tsize.size';
			}
	   }		
	   //else tsize of today...
       if (is_readable($tsize)) {
	        //echo $tsize;
			$size = @file_get_contents($tsize);

	   }
	   else {
            $size = $this->filesize_r($path);
			@file_put_contents($tsize, $size);
	   }
	   
	   return ($size);
    }
  
    function cached_database_filesize($year=null, $month=null) {
      $db = GetGlobal('db'); 
      $name = strval(date('Ymd'));
      $dsize = $this->prpath . $name . '-dsize.size';	
	
	  if (($year) && ($month)) {
			$selected_name = sprintf('%04d',$year) . sprintf('%02d',$month);
			for ($d=1;$d<31;$d++) {
				$search_selected_name = $selected_name . sprintf('%02d',$d);
				if (is_readable($this->prpath . $search_selected_name . '-dsize.size'))
					$tsize = $this->prpath . $search_selected_name . '-dsize.size';
			}	
      }
	  //else tsize of today...
      if (is_readable($dsize)) {
	    //echo $dsize;
		$size = @file_get_contents($dsize);

	  }
	  else {
		//$result = mysql_query( “SHOW TABLE STATUS” );
		$sSQL = "SHOW TABLE STATUS";
		$res = $db->Execute($sSQL,2);		
		//print_r($res);
		$size = 0;
		/*while( $row = mysql_fetch_array( $result ) ) {  
        $dbsize += $row[ "Data_length" ] + $row[ "Index_length" ];
		} */
		if (!empty($res)) { 
			foreach ($res as $n=>$rec) {
				$size += $rec[ "Data_length" ] + $rec[ "Index_length" ];					
			}
		}
		@file_put_contents($dsize, $size);
	  }	
		
	  return ($size);
    }
  
    public function select_timeline($year=null,$month=null,$nodropdown=false,$template=false) {
	
	  if ($template) {
	  
		for ($y=2010;$y<=intval(date('Y'));$y++) {
		    $yearsli .= '<li>'. seturl('t=cp&month='.$month.'&year='.$y.'&editmode=1&turl='.GetReq('turl'),$y) .'</li>';
		}
		
		for ($m=1;$m<=12;$m++) {
		    $mm = sprintf('%02d',$m);
		    $monthsli .= '<li>' . seturl('t=cp&month='.$mm.'&year='.$year.'&editmode=1&turl='.GetReq('turl'),$mm) .'</li>';
		}	  
	  
	    $_dashboard = localize('_dashboard',getlocal()); 
		$_year = localize('_year',getlocal());
		$_month = localize('_month',getlocal());
	    $navbar = ' <div class="bs-docs-example">
                                <h4>'.$_dashboard.'</h4>
                                <div class="navbar">
                                    <div class="navbar-inner">
                                        <div class="container">
                                            <a data-target=".navbar-responsive-collapse" data-toggle="collapse" class="btn btn-navbar">
                                                <span class="icon-bar"></span>
                                                <span class="icon-bar"></span>
                                                <span class="icon-bar"></span>
                                            </a>
                                            <a href="#" class="brand">'.$year.' '.$month.'</a>
                                            <div class="nav-collapse collapse navbar-responsive-collapse">
                                                <ul class="nav">
                                                    <!--li class="active"><a href="#">Home</a></li>
                                                    <li><a href="#">Link</a></li>
                                                    <li><a href="#">Link</a></li-->													
                                                </ul>
                                                <!--form action="" class="navbar-search pull-left">
                                                    <input type="text" placeholder="Search" class="search-query input-medium">
                                                </form-->
                                                <ul class="nav pull-right">
                                                    <li class="dropdown">
                                                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">'.$_year.' <b class="caret"></b></a>
                                                        <ul class="dropdown-menu">'.
                                                            $yearsli .
                                                        '</ul>
                                                    </li>
													<li class="divider-vertical"></li>
                                                    <li class="dropdown">
                                                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">'.$_month.' <b class="caret"></b></a>
                                                        <ul class="dropdown-menu">'.
                                                            $monthsli .
                                                        '</ul>
                                                    </li>												
                                                    <!--li><a href="#">Link</a></li-->
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>';
		$this->stats['Timeline']['value'] = $navbar;					
		return true;
	  }
	  else {
	  if ($nodropdown) {
        $ret = "<p><b>Year ($year):|";
		
		for ($y=2010;$y<=intval(date('Y'));$y++) {
		    $ret .= seturl('t=cp&month='.$month.'&year='.$y.'&editmode=1',$y) .'|';
		}
		$ret .= "<hr>Month ($month):|";	
		
		for ($m=1;$m<=12;$m++) {
		    $mm = sprintf('%02d',$m);
		    $ret .= seturl('t=cp&month='.$mm.'&year='.$year.'&editmode=1',$mm) .'|';
		}
		$ret .= "</b>";
		$ret .= '</p>';
        return ($ret);		
	  }
	
	  for ($mo=1;$mo<=12;$mo++) {
	    $id = sprintf('%02d',$mo);
        $s{$id} = ($id==$month) ? "selected=\"true\"" : null;
	    //echo "<br>s$id",'-',$mo,'-',$month,'>',$id,'>',$s{$id};
	  }
  
      $form = "Selected period:
<form method=POST action=\"cp.php?t=cp&editmode=1\">
<select name=\"month\">
<option $s01 value=\"01\">Jan</option>
<option $s02 value=\"02\">Feb</option>
<option $s03 value=\"03\">Mar</option>
<option $s04 value=\"04\">Apr</option>
<option $s05 value=\"05\">May</option>
<option $s06 value=\"06\">Jun</option>
<option $s07 value=\"07\">Jul</option>
<option $s08 value=\"08\">Aug</option>
<option $s09 value=\"09\">Sep</option>
<option $s10 value=\"10\">Oct</option>
<option $s11 value=\"11\">Nov</option>
<option $s12 value=\"12\">Dec</option>
</select>
<select name=\"year\">
";

      for ($y=2010;$y<=intval(date('Y'));$y++) {

	   if ($year==$y)
	      $form .= "<option selected=\"true\" value=\"$y\">$y</option>";
	   else
		  $form .= "<option value=\"$y\">$y</option>";
      }
      $form .= "
</select>	
<!--input type=\"hidden\" name=\"FormAction\" value=\"cp\" /-->
<input type=\"submit\" value=\" OK \" />
</form>
";	
  
      return ($form);
	  }
    }
  
    function site_stats($tokensout=false) {
		$db = GetGlobal('db'); 
        $year = GetParam('year') ? GetParam('year') : date('Y'); //null;
	    $month = GetParam('month') ? GetParam('month') : date('m');//null;		
		$path = $this->application_path;
				
		$tok = $tokensout ? true : ($this->cptemplate ? true : false); 
		
		if (GetReq('id')) //item selected..bypass ????
		    return null; 
		
        if (!$tok)		
			$ret .= $this->select_timeline($year,$month,1);
        else 
		    $this->select_timeline($year,$month,1,true);
			
		if (!$tok) {
			$ret_a = $this->free_space(true, $year, $month);
			$win1 = new window2(localize("_MENU1",getlocal()),$ret_a,null,1,null,'HIDE',null,1);
			$ret .= $win1->render();
			unset ($win1);				
		}	
		else	
			$this->free_space(true, $year, $month, true);	
		
		//if $total_size > ..50MB ...goto upgrade.....
		
        if (defined('RCKATEGORIES_DPC')) { //???	
		    $tbl_b = array();
            $sSQL = "select count(id) from users";
			$res = $db->Execute($sSQL,2);
            if (!$tok) {			
				$ret_b .= '<br>Total users:'.$res->fields[0];
				$tbl_b[] = $this->icon("images/file_icon.png",'cpuser.php','Users:'.$res->fields[0],4);			
			}	
			else
                $this->stats['Users']['value'] = $res->fields[0];			
			
            $sSQL = "select count(id) from users where subscribe=1";
			$res = $db->Execute($sSQL,2);	
			if (!$tok) {			
				$ret_b .= '<br>Total subscribers:'.$res->fields[0];	
				$tbl_b[] = $this->icon("images/file_icon.png",'cpsubscribers.php','Subscribers:'.$res->fields[0],4);				
			}	
			else
                $this->stats['Users']['Subscribers'] = $res->fields[0];
			
            $sSQL = "select count(id) from customers";
			$res = $db->Execute($sSQL,2);	
			if (!$tok) {			
				$ret_b .= '<br>Total customers:'.$res->fields[0];
				$tbl_b[] = $this->icon("images/file_icon.png",'cpcustomers.php','Customers:'.$res->fields[0],4);				
            }
			else
                $this->stats['Users']['custromers'] = $res->fields[0];			
			
            $sSQL = "select count(id) from ulists";
			$res = $db->Execute($sSQL,2);	
			if (!$tok) {			
				$ret_b .= '<br>Total mails in lists:'.$res->fields[0];
				$tbl_b[] = $this->icon("images/file_icon.png",'cpsubscibers.php?t=cpviewsubsqueue','Mail queue:'.$res->fields[0],4);				
			}
			else
                $this->stats['Mail']['maillist'] = $res->fields[0];			
			
			if (!$tok) {
				if (!empty($tbl_b)) 
					$ret_b = $this->show_iconstable($tbl_b,null,4);
			
				$win1 = new window2(localize("_MENU2",getlocal()),$ret_b,null,1,null,'HIDE',null,1);
				$ret .= $win1->render();
				unset ($win1);				
			}
			
			$tbl_c = array();
            $sSQL = "select count(id) from pphotos where stype='SMALL'";
			$res = $db->Execute($sSQL,2);
			if (!$tok) {			
				$ret_c .= '<br>Total photos in database (small):'.$res->fields[0];	
				$tbl_c[] = $this->icon("images/file_icon.png",'#','Photos in DB (small):'. $res->fields[0],4);					
			}
			else
                $this->stats['Items']['DbPicSmall'] = $res->fields[0];			
			
            $sSQL = "select count(id) from pphotos where stype='MEDIUM'";
			$res = $db->Execute($sSQL,2);
			if (!$tok) {			
				$ret_c .= '<br>Total photos in database (medium):'.$res->fields[0];
				$tbl_c[] = $this->icon("images/file_icon.png",'#','Photos in DB (medium):'. $res->fields[0],4);
			}
			else
                $this->stats['Items']['DbPicMedium'] = $res->fields[0];			
			
            $sSQL = "select count(id) from pphotos where stype='LARGE'";
			$res = $db->Execute($sSQL,2);
			if (!$tok) {			
				$ret_c .= '<br>Total photos in database (large):'.$res->fields[0];
				$tbl_c[] = $this->icon("images/file_icon.png",'#','Photos in DB (large):'. $res->fields[0],4);
			}
			else
                $this->stats['Items']['DbPicLarge'] = $res->fields[0];
			
            $sSQL = "select count(id) from pattachments";
			$res = $db->Execute($sSQL,2);	
			if (!$tok) {			
				$ret_c .= '<br>Total item attachments:'.$res->fields[0];			
				$tbl_c[] = $this->icon("images/file_icon.png",'#','Files in DB '. $res->fields[0],4);
			}
			else
                $this->stats['Items']['Attachments'] = $res->fields[0];			
			
			if (!$tok) {
				if (!empty($tbl_c)) 
					$ret_c = $this->show_iconstable($tbl_c,null,4);
			
				$win1 = new window2(localize("_MENU3",getlocal()),$ret_c,null,1,null,'HIDE',null,1);
				$ret .= $win1->render();
				unset ($win1);				
			}	
		}  
        if (defined('RCITEMS_DPC')) {
            $tbl_d = array();		
		    $sSQL = "select id,substr(sysins,1,4) as year,substr(sysins,6,2) as month from products where substr(sysins,1,4)='$year' and substr(sysins,6,2)='$month'";
			$res = $db->Execute($sSQL,2);
			$i=0;
			if (!empty($res)) { 
				foreach ($res as $n=>$rec) {
				    $i+=1;					
				}
			}

			if (!$tok) {			
				$ret_d .= '<br>Items inserted this month:'.$i;
				$tbl_d[] = $this->icon("images/file_icon.png",'#','Inserts:'.$i,4);
			}
			else
                $this->stats['Items']['insert'] = $res->fields[0];			
						
		    $sSQL = "select id,substr(sysupd,1,4) as year,substr(sysupd,6,2) as month from products where substr(sysupd,1,4)='$year' and substr(sysupd,6,2)='$month'";
			$res = $db->Execute($sSQL,2);
			$i=0;
			if (!empty($res)) { 
				foreach ($res as $n=>$rec) {
				    $i+=1;				
				}
			}
			
			if (!$tok) {			
				$ret_d .= '<br>Items updated this month:'.$i;
				$tbl_d[] = $this->icon("images/file_icon.png",'cpitems.php','Updates:'.$i,4);
			}
			else
                $this->stats['Items']['update'] = $res->fields[0];			
						
            $sSQL = "select count(id) from products where itmactive=1 and active=101";
			$res = $db->Execute($sSQL,2);	
			if (!$tok) {			
				$ret_d .= '<br>Total active items:'.$res->fields[0];	
				$tbl_d[] = $this->icon("images/file_icon.png",'cpitems.php','Active:'.$res->fields[0],4);
			}
			else
                $this->stats['Items']['active'] = $res->fields[0];			
						
            $sSQL = "select count(id) from products where (itmactive=0 and active=0) or (itmactive is null and active is null)";//or...
			$res = $db->Execute($sSQL,2);	
			if (!$tok) {			
				$ret_d .= '<br>Total inactive items:'.$res->fields[0];	
				$tbl_d[] = $this->icon("images/file_icon.png",'cpitems.php','Inactive:'.$res->fields[0],4);
 			}
			else
                $this->stats['Items']['inactive'] = $res->fields[0];			
						
            $sSQL = "select count(id) from products";
			$res = $db->Execute($sSQL,2);	
			if (!$tok) {			
				$ret_d .= '<br>Total items:'.$res->fields[0];
				$tbl_d[] = $this->icon("images/file_icon.png",'cpitems.php','Total:'.$res->fields[0],4);
			}
			else
                $this->stats['Items']['value'] = $res->fields[0];			
						
			if (!$tok) {
				if (!empty($tbl_d)) 
					$ret_d = $this->show_iconstable($tbl_d,null,4);
				
				$win1 = new window2(localize("_MENU4",getlocal()),$ret_d,null,1,null,'HIDE',null,1);
				$ret .= $win1->render();
				unset ($win1);				
			}	
		} 
        if (defined('RCITEMS_DPC')) {//???????SYNC DPC
		    $tbl_e = array();
		    $sSQL = "select id,status,sqlres,sqlquery,substr(date,1,4) as year,substr(date,6,2) as month from syncsql where substr(date,1,4)='$year' and substr(date,6,2)='$month'";
			$res = $db->Execute($sSQL,2);
			$i=0;
			$chars_send = 0;
			$noexec_syncs = 0;
			if (!empty($res)) { 
				foreach ($res as $n=>$rec) {
				    $i+=1;
                    $chars_send += strlen($rec['sqlquery']);
                    if (!$rec['status']) 
                        $noexec_syncs+=1;					
				}
			}
			
			if (!$tok) {			
				$ret_e .= '<br>Syncs send this month:'.$i;
				$tbl_e[] = $this->icon("images/file_icon.png",'cpitems.php','Syncs:'.$i,4);
				$ret_e .= '<br>Syncs not executed this month:'.$noexec_syncs;
				$tbl_e[] = $this->icon("images/file_icon.png",'cpitems.php','Sync errors:'.$noexec_syncs,4);			
				$ret_e .= '<br>Syncs data send this month:'.$this->bytesToSize1024($chars_send,1);
				$tbl_e[] = $this->icon("images/file_icon.png",'cpitems.php','Traffic:'.$this->bytesToSize1024($chars_send,1),4);
			}
			else {
                $this->stats['Sync']['subtotal'] = $i;			
				$this->stats['Sync']['noexec'] = $noexec_syncs;
				$this->stats['Sync']['bytes'] = $this->bytesToSize1024($chars_send,1);
			}	
			
		    $sSQL = "select count(id) from syncsql where substr(date,1,4)='$year'";
			$res = $db->Execute($sSQL,2);
			if (!$tok) {			
				$ret_e .= '<br>Total syncs :'.$res->fields[0]; 	
				$tbl_e[] = $this->icon("images/file_icon.png",'cpitems.php','Total syncs:'.$res->fields[0],4);			
            }
			else
                $this->stats['Sync']['value'] = $res->fields[0];
			
			if (!$tok) {
				if (!empty($tbl_e)) 
					$ret_e = $this->show_iconstable($tbl_e,null,4);
				
				$win1 = new window2(localize("_MENU5",getlocal()),$ret_e,null,1,null,'HIDE',null,1);
				$ret .= $win1->render();
				unset ($win1);				
			}	
		}  		
        if (defined('RCMAILDBQUEUE_DPC')) {
		    $tbl_f = array();
		    $sSQL = "select id,body,active,status,mailstatus,sender,receiver,substr(timeout,1,4) as year,substr(timeout,6,2) as month from mailqueue where substr(timeout,1,4)='$year' and substr(timeout,6,2)='$month'";
			$sSQL .= " and active=0";
			$res = $db->Execute($sSQL,2);
			$i=0;
			$chars_send = 0;
			if (!empty($res)) { 
				foreach ($res as $n=>$rec) {
				    $i+=1;
                    $chars_send += strlen($rec['body']);				
				}
			}
			if (!$tok) {			
				$ret_f .= '<br>Mails sent this month:'.$i;
				$tbl_f[] = $this->icon("images/file_icon.png",'cpsubscribers.php?t=cpviewsubsqueue','Mails sent:'.$i,4);
				$ret_f .= '<br>Mails data sent this month:'.$this->bytesToSize1024($chars_send,1);
				$tbl_f[] = $this->icon("images/file_icon.png",'cpsubscribers.php?t=cpviewsubsqueue','Traffic sent:'.$this->bytesToSize1024($chars_send,1),4);
			}
			else {
				$this->stats['Mail']['subtotal'] = $i;
				$this->stats['Mail']['bytes'] = $this->bytesToSize1024($chars_send,1);
			}	
			
		    $sSQL = "select count(id) from mailqueue where active=1";
			$res = $db->Execute($sSQL,2);
			if (!$tok) {
				$ret_f .= '<br>Total mails in queue:'.$res->fields[0]; 
				$tbl_f[] = $this->icon("images/file_icon.png",'cpsubscribers.php?t=cpviewsubsqueue','Active mails:'.$res->fields[0],4);			
			}
			else
                $this->stats['Mail']['value'] = $res->fields[0];
						
		    $sSQL = "select count(id) from mailqueue where substr(timeout,1,4)='$year' and active=0";
			$res = $db->Execute($sSQL,2);
			if (!$tok) {			
				$ret_f .= '<br>Total mails send:'.$res->fields[0]; 	          				
				$tbl_f[] = $this->icon("images/file_icon.png",'cpsubscribers.php?t=cpviewsubsqueue','Total mails:'.$res->fields[0],4);
			}
			else
                $this->stats['Mail']['send'] = $res->fields[0];
						
			if (!$tok) {
				if (!empty($tbl_f)) 
					$ret_f = $this->show_iconstable($tbl_f,null,4);
				
				$win1 = new window2(localize("_MENU6",getlocal()),$ret_f,null,1,null,'HIDE',null,1);
				$ret .= $win1->render();
				unset ($win1);				
			}	
		}  
		if (defined('RCTRANSACTIONS_DPC')) {
		    $tbl_g = array();
            //trans list, last 5 		
			$ret_g = GetGlobal('controller')->calldpc_method("rctransactions.getTransactionsList use 5");
			//more...
			//$tbutton = seturl('t=cptransview&editmode=1',localize("_moretrans",getlocal()));  
			//$tbutton = seturl('t=cptransactions&editmode=1',localize("_moretrans",getlocal()));  
			if (!$tok) {			
				$tbutton = "<a href='cptransactions.php'>".localize("_moretrans",getlocal())."</a>"; //error in jqgid.lib ..output strarted...!!!
				$wint = new window(localize(null,getlocal()),$tbutton);
				$ret_g .= $wint->render();
				unset ($wint);
				$tbl_g[] = $this->icon("images/file_icon.png",'cptransactions.php',localize("_moretrans",getlocal()),4);
			}
            //summary per month 		
		    $sSQL = "select tid,cid,tstatus,cost,costpt,payway,roadway,substr(tdate,1,4) as year,substr(tdate,6,2) as month from transactions where substr(tdate,1,4)='$year' and substr(tdate,6,2)='$month'";
			$res = $db->Execute($sSQL,2);
			$i=0;
			$pay_send = 0;
			$paynet_send = 0;
			if (!empty($res)) { 
				foreach ($res as $n=>$rec) {
				    $i+=1;
                    $paynet_send += floatval($rec['cost']);
                    $pay_send += floatval($rec['costpt']);					
				}
			}	
			
			if (!$tok) {			
				$ret_g .= '<br>Transactions this month:'.$i;
				$tbl_g[] = $this->icon("images/file_icon.png",'cptransactions.php','Transactions:'.$i,4);
				$ret_g .= '<br>Monthly revenue (net):'.sprintf("%01.2f", $paynet_send); 	          
				$tbl_g[] = $this->icon("images/file_icon.png",'cptransactions.php','Revenue (net):'.sprintf("%01.2f", $paynet_send),4);
				$ret_g .= '<br>Monthly revenue:'.sprintf("%01.2f", $pay_send);
				$tbl_g[] = $this->icon("images/file_icon.png",'cptransactions.php','Revenue:'.sprintf("%01.2f", $pay_send),4);			
			}
			else {
				$this->stats['Transactions']['subtotal'] = $i;
				$this->stats['Transactions']['revenue'] = sprintf("%01.2f", $pay_send);
				$this->stats['Transactions']['revenuenet'] = sprintf("%01.2f", $paynet_send);
			}				
			
		    $sSQL = "select sum(cost),sum(costpt) from transactions where substr(tdate,1,4)='$year'";
			$res = $db->Execute($sSQL,2);
			if (!$tok) {			
				$ret_g .= '<br>Total revenue (net):'.sprintf("%01.2f", $res->fields[0]); 	          
				$tbl_g[] = $this->icon("images/file_icon.png",'cptransactions.php','Total (net):'.sprintf("%01.2f", $res->fields[0]),4);
				$ret_g .= '<br>Total revenue (taxes included):'.sprintf("%01.2f", $res->fields[1]);			
				$tbl_g[] = $this->icon("images/file_icon.png",'cptransactions.php','Total:'.sprintf("%01.2f", $res->fields[1]),4);
			}
			else {
				$this->stats['Transactions']['value'] = sprintf("%01.2f", $res->fields[0]);
				$this->stats['Transactions']['taxvalue'] = sprintf("%01.2f", $res->fields[1]);
			}				
			
			if (!$tok) {
				if (!empty($tbl_g)) 
					$ret_g = $this->show_iconstable($tbl_g,null,4);
			
				$win1 = new window2(localize("_MENU7",getlocal()),$ret_g,null,1,null,'HIDE',null,1);
				$ret .= $win1->render();
				unset ($win1);				
			}	
		}  
        if (defined('RCSAPPVIEW_DPC')) {		  
            //....
		}

        return ($ret);     	
    }
	
	//get the free space
	protected function free_space($verbose=false, $year=null, $month=null, $template=false) {
	    $ret = '';
		$tbl = array();
		
		$msize = $this->cached_mail_size($year, $month);
		$msize2 = $this->bytesToSize1024($msize,1);
		if (!$template) {		
			$ret .= "<br>Mailbox size ". $msize2;
			$tbl[] = $this->icon("images/file_icon.png",'#','Mailbox size '. $msize2,4);							
	    }
		else
			$this->stats['Diskspace']['mailbox'] = $msize2;	
				
		$tsize = $this->cached_disk_size($year, $month);
		$tsize2 = $this->bytesToSize1024($tsize,1);
		if (!$template) {		
			$ret .= "<br>Folder size ". $tsize2;
			$tbl[] = $this->icon("images/file_icon.png",'#','Folder size '. $tsize2,4);					
        }
		else
			$this->stats['Diskspace']['value'] = $tsize2;
			
        $dsize = $this->cached_database_filesize($year, $month);	
		$dsize2 = $this->bytesToSize1024($dsize,1);	
		if (!$template) {
			$ret .= "<br>Database size ". $dsize2;
			$tbl[] = $this->icon("images/file_icon.png",'#','Database size '. $dsize2,4);					
        }
		else
			$this->stats['Diskspace']['database'] = $dsize2;
			
		$total_size = $tsize + $dsize + $msize;
		//echo "Size total ($tsize + $dsize + $msize):",$total_size;
		$stotal = $this->bytesToSize1024($total_size,1);
		if (!$template) {		
			$ret .= "<br>Total used size ". $stotal;
			$tbl[] = $this->icon("images/file_icon.png",'#','Size used '. $stotal,4);					
        }
		else
			$this->stats['Diskspace']['value'] = $stotal;
			
		//alowed size
		$rtotal = intval(@file_get_contents($this->prpath .'maxsize.conf.php'));
		//echo 'Size allowed:',$rtotal;
		$allowed_size = $this->bytesToSize1024($rtotal,1);
		if (!$template) {
			$ret .= "<br>Total alowed size ". $allowed_size;
			$tbl[] = $this->icon("images/file_icon.png",'#','Allowed size '. $allowed_size,4);					
		}
		else
			$this->stats['Diskspace']['size'] = $allowed_size;
			
		//remaind size
		$remain_size = intval($rtotal - $total_size);
		$rmtotal = $this->bytesToSize1024($remain_size,1);
		if (!$template) {
			$ret .= "<br>Remaining size ". $rmtotal;
			$tbl[] = $this->icon("images/file_icon.png",'#','Remaining size '. $rmtotal,4);					
		}
		else
			$this->stats['Diskspace']['remain'] = $rmtotal;		
		//echo 'Size remain:',$remain_size;
		
		if (!$template) {
			if ($verbose) {
				if (!empty($tbl)) 
					$ret = $this->show_iconstable($tbl,null,4);	
				
				return ($ret); 			
			}
			else
				return ($remain_size);
		}
        else
			return true;
        //return ($verbose ? $ret : $remain_size); 		
	}
	
	protected function set_space($space_in_mb) {
	    $spacefile = $this->prpath . '/maxsize.conf.php';
	    
	    $space = intval($space_in_mb * 1024 * 1024);
		$ok = file_put_contents($spacefile ,$space);
		
		return ($ok);
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
  
	protected function set_addons_list() {
	
		$this->tools['google_analytics'] = '0,0,0,0,0,0,0,1,1';
		$this->tools['add_recaptcha'] = '0,0,0,0,0,0,0,1,1';
		$this->tools['upload_logo'] = '0,0,0,0,0,0,0,1,1';
		$this->tools['add_domainname'] = '0,0,0,0,0,0,0,1,1';
		$this->tools['maildbqueue'] = '0,0,0,0,0,0,0,0,1';
		$this->tools['item_photo'] = '0,0,0,0,0,0,0,1,1';
		//$this->tools['uninstall_maildbqueue'] = '0,0,0,0,0,0,0,1,1';
		//$this->tools['add_addwords'] = '0,0,0,0,0,0,0,1,1';					 
		if ($this->environment['IMPORTDB']>0) {					 
			$this->tools['add_categories']='0,0,0,0,0,0,0,1,1';
			$this->tools['add_products']='0,0,0,0,0,0,0,1,1';
			//print_r($this->tools);
		}
		
		$this->tools['jqgrid'] = '0,0,0,0,0,0,0,0,1';//priv for setup
		$this->tools['ieditor'] = '0,0,0,0,0,0,0,0,1';//priv for setup
		$this->tools['ckfinder'] = '0,0,0,0,0,0,0,0,1';//priv for setup

		$this->tools['edit_htmlfiles'] = '0,0,0,0,0,0,0,0,1';//priv for setup
		$this->tools['cpimages'] = '0,0,0,0,0,0,0,1,1';
		$this->tools['awstats'] = '0,0,0,0,0,0,0,1,1';
		
		//keys
		$this->tools['addkey'] = '0,0,0,0,0,0,0,1,1';//no priv for setup
		$this->tools['genkey'] = '0,0,0,0,0,0,0,0,1';//priv for setup
		$this->tools['validatekey'] = '0,0,0,0,0,0,0,0,1';//priv for setup
		
		/*if ($this->environment['BACKUP']) //has been installed..read string cp.ini???
		  $this->tools['backup'] = '0,0,0,0,0,0,1,1,1'; 
		else //when has no installed the right to install is up to 9 secid*/
		//handled by installed secid if installed
		$this->tools['backup'] = '0,0,0,0,0,0,0,0,1';//priv for setup
		
		$this->tools['eshop'] = '0,0,0,0,0,0,0,0,1';//priv for setup
		//$this->tools['uninstalleshop'] = '0,0,0,0,0,0,0,0,1';//priv for setup		
			
	}  
  
    function parse_environment() {
		$myenvfile = $this->prpath . 'cp.ini';
		$ini = @parse_ini_file($myenvfile,false, INI_SCANNER_RAW);	
		//$ini = @parse_ini_file("cp.ini");
		if (!$ini) die('Environment error!');
		//print_r($ini);
		$adminsecid = GetSessionParam('ADMINSecID');
		//session sec level not defined at first login...???	
		$seclevid = ($adminsecid>1) ? intval($adminsecid)-1 : 1;//9; //test
		//echo GetSessionParam('ADMINSecID'),'>',$adminsecid,':', $seclevid;	

		foreach ($ini as $env=>$val) {
			if (stristr($val,',')) {
				$uenv = explode(',',$val);
				$ret[$env] = $uenv[$seclevid];  
			}
			else
				$ret[$env] = $val;
		}
		//print_r($ret);
		return ($ret);
    }  

	//read environment cp file
	protected function read_env_file($save_session=false) {
		$myenvfile = $this->prpath . 'cp.ini';
		$ret = $this->parse_environment();//@parse_ini_file($myenvfile ,false, INI_SCANNER_RAW);	

		if ($save_session)
		    SetSessionParam('env', $ret); 
		
		return ($ret);
    } 

    protected function edit_html_files($cke4=false, $uselan=false, $table=false) {
	    $tbl = $table ? (GetSessionParam('editfiles') ? 
		                 unserialize(GetSessionParam('editfiles')) : null) : //no session tbl..read dir 
						 true;//in case of no table just get in..//null; 
						 
		if (!$tbl) {				 
			$lan = getlocal();
			$filter = $uselan ? ($lan ? $lan.'.htm' : '0.htm') : null;
			$sourcedir = $this->prpath . '/'.$this->htmlfolder.'/';
			$encoding = $this->charset;	
		
			if (!is_dir($sourcedir)) 
				return (false);		 

			//$ret .= 'Filter:'.$filter;  
			//echo $sourcedir;
			$mydir = dir($sourcedir);
			while ($fileread = $mydir->read ()) { 
	
				if (($fileread!='.') && ($fileread!='..') && (!is_dir($sourcedir.'/'.$fileread)) ) { 
					if ((stristr($fileread,'.htm')) && 
					    (substr($fileread,0,2)!='cp') && //<<cpfilename restiction
						(substr($fileread,0,1)!='_'))  {//<<manual backup files

						if ($filter) { 
							if (stristr($fileread,$filter))
								$weditfiles[] = $fileread;	//per lan			
						}	
						else //all	
							$weditfiles[] = $fileread;				
					}
				}
			}
			$mydir->close ();
		
			if (!empty($weditfiles)) {
		
				foreach ($weditfiles as $i=>$file) {

					$plainfile	= (stristr($file,'.html')) ? 
			                      ($filter ? str_replace($filter.'l','', $file) : str_replace('.html','', $file)):
                                  ($filter ? str_replace($filter,'', $file) : str_replace('.htm','', $file));						   
					$phpfile = str_replace('.html','.php', $file);
					$htmlfile = urlencode(base64_encode($file));
		
					$flink = "cpmhtmleditor.php?cke4=$cke4&encoding=$encoding&editmode=1&htmlfile=$htmlfile";
					$htmleditlink = "<a href='$flink' target='mainFrame'>".
			                        $plainfile .
							        "</a>";
					if ($table)							
						$tbl[$plainfile] = $this->icon("images/file_icon.png",$flink,$htmleditlink,4);//$flink;
					else	
						$ret .= '<br/>Edit:' . $htmleditlink;
			
				}
			}
			//else
				//$ret = 'no files to edit...';
			if (($table) && (!empty($tbl))) {//save 4 faster load			
                SetSessionParam('editfiles', serialize($tbl));
			}	
		}  
		
		if (($table) && (!empty($tbl)))  {
		    ksort($tbl);
			$ret = $this->show_iconstable($tbl,null,4);
			//$ret .= GetSessionParam('editfiles');//test
		}

		return ($ret);  		
    }	
	
    function panelmenu($rettokens=null) {
    $turl = urlencode(GetReq('turl'));
	$passturl = $turl ? 'editmode=1&turl='.$turl : 'editmode=1';
    $template = remote_paramload('FRONTHTMLPAGE','template',$this->path); 
	$editpage = GetGlobal('pagename');
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
       $otokens[] = "<li><a $class href='cp.php?$passturl'>".localize('_dashboard',$lan)."</a></li>"; 	
	 
	 if ($environment['AWSTATS']==1) 
	   $otokens[] = "<li><a $class href='cp.php?mc_page=cp-webstats&$passturl&encoding=$encoding'>".localize('_webstatistics',$lan)."</a></li>";	

	 if ($environment['WEBMAIL']==1) {
	   $urlbase = remote_paramload('SHELL','urlbase',paramload('SHELL','prpath'));
       $otokens[] = "<li><a $class href='cp.php?mc_page=cp-webmail&$passturl&encoding=$encoding'>".localize('_webmail',$lan)."</a></li>";
	 }  
	 
     //if (stristr($editpage,'index.php')) {
	     if ($environment['MENU']==1) 
           $otokens[] = "<li><a $class href='cpmenu.php?t=cpmconfig&$passturl&encoding=$encoding'>".localize('_menu',$lan)."</a></li>";

	     if ($environment['SLIDESHOW']==1) 
           $otokens[] = "<li><a $class href='cpslideshow.php?t=cpsconfig&$passturl&encoding=$encoding'>".localize('_slideshow',$lan)."</a></li>";	 
     //}	 
	 
	 if ($environment['CONFIG']==1) {
		//echo $editpage,'>';	 
		//if (stristr($editpage,'index.php')) {
		$pparts = explode('.',$editpage);
		$config_section = strtoupper($pparts[0]);		 
		if ($config_section) {	  
           $otokens[] = "<li><a $class href='cpconfig.php?cpart=$config_section&$passturl&encoding=$encoding'>".localize('_config',$lan)."</a></li>"; 
		}
     }//config...	
	  
	 
	 if (stristr($editpage,'contact.php')) {
	    if ($environment['CONTACT_FORM']==1) 
           $otokens[] = "<li><a $class href='cpform.php?t=cpform&$passturl&encoding=$encoding'>".localize('_contactform',$lan)."</a></li>";
	 }	
	 if (stristr($editpage,'subscribe.php')) 
	    if ($environment['SUBSCRIBERS']==1) {
           $otokens[] = "<li><a $class href='cpsubscribers.php?$passturl&encoding=$encoding'>".localize('_subscribers',$lan)."</a></li>";
	 }	
	 if (stristr($editpage,'sitemap.php')) {
	    if ($environment['SITEMAP']==1) {
		 	$pparts = explode('.',$editpage);
		    $config_section = strtoupper($pparts[0]);		 
			if ($config_section)  
				$otokens[] = "<li><a $class href='cpconfig.php?cpart=$config_section&$passturl&encoding=$encoding'>".localize('_sitemap',$lan)."</a></li>";

		}   
	 }	
	 if (stristr($editpage,'search.php')) {
	    if ($environment['SEARCH']==1) 
           $otokens[] = "<li><a $class href='cpitems.php?t=cpattach2db&$passturl&encoding=$encoding'>".localize('_search',$lan)."</a></li>";
	 }
	 //}//config
	 if ($environment['EDIT_CATEGORY']==1) { 
	       $cat = null;
           $otokens[] = "<li><a $class href='cpkategories.php?t=cpkategories&cat=$cat&$passturl&encoding=$encoding'>".localize('_editcat',$lan)."</a></li>";
	 } 	 
     if ($environment['EDIT_ITEM']==1) {
           $v = null;
		   $otokens[] = "<li><a $class href='cpitems.php?t=cpitems&id=$v&$passturl&encoding=$encoding'>".localize('_edititem',$lan)."</a></li>";
	 } 	 
	 if ($environment['USERS']==1) 
           $otokens[] = "<li><a $class href='cpusers.php?t=cpusers&$passturl&encoding=$encoding'>".localize('_users',$lan)."</a></li>"; 

	 if ($environment['CUSTOMERS']==1) 
           $otokens[] = "<li><a $class href='cpcustomers.php?t=cpcustomers&$passturl&encoding=$encoding'>".localize('_customers',$lan)."</a></li>"; 

	 if ($environment['TRANSACTIONS']==1) 
		   $otokens[] = "<li><a $class href='cptransactions.php?t=cptransactions&$passturl&encoding=$encoding'>".localize('_transactions',$lan)."</a></li>"; 
 	 
	 if ($environment['ITEM_SENDMAIL']==1) 
		 $otokens[] = "<li><a $class href='cpsubscribers.php?encoding=$encoding&$passturl'>".localize('_senditemmail',$lan)."</a></li>";	 		 

	 if ($environment['RESOURCES_UPLOAD']==1) 
		 $otokens[] = "<li><a $class href='cpupload.php?$passturl&encoding=$encoding'>".localize('_upload',$lan)."</a></li>";	 		 

	 if ($environment['CKFINDER']==1) 
		 $otokens[] = "<li><a $class href='cpmckfinder.php?&$passturl&encoding=$encoding'>".localize('_ckfinder',$lan)."</a></li>";	 		 
	
	 if ($environment['EDITHTML']==1) {
	    //$turl = array_shift(explode('?',urldecode(base64_decode($_GET['turl']))));//$_GET['turl'];
		$turl_file = str_replace('.php','.html',array_shift(explode('?',urldecode(base64_decode($_GET['turl'])))));
		//echo $turl_file,'..';
		$htmlfile = $_GET['htmlfile'] ? $_GET['htmlfile'] : urlencode(base64_encode($turl_file));
		if ($htmlfile) 
	       $otokens[] = "<li><a $class href='cpmhtmleditor.php?cke4=1&encoding=$encoding&$passturl&htmlfile=$htmlfile'>".localize('_editpage',$lan)."</a></li>";
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
	 
	 $new_elements = false;
	 
	 $qquery = str_replace('_&_', '_%26_', base64_decode($_GET['turl'])); //echo '>',$qquery,'>'; //& category problem
     $urlquery = parse_url($qquery); /*parse_url(base64_decode($_GET['turl']));*/ //echo $urlquery['query'];
     parse_str($urlquery['query'],$getp); //echo implode('.',$getp);  
	 
     foreach ($getp as $p=>$v) {
	   	 
       if (stristr($p,'cat')) {
	   
         $cat = urlencode($v);
		 
	     /*if ($environment['ADD_CATEGORY']==1) 
           $ntokens[] = "<li><a $class href='cpkategories.php?t=cpaddcat&cat=$cat&$passturl'>".localize('_addcat',$lan)."</a></li>";	
         */
	     if ($environment['ADD_ITEM']==1) 
           $ntokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . "&encoding=$encoding&id=$cat&type=.html&$passturl&insfast=1'>".localize('_ADDFAST',$lan)."</a></li>";	

         $new_elements = true; //if cat exist	

	     if ($environment['EDIT_CTAG']==1)  //add cat tags
           $ctokens[] = "<li><a $class href='cptags.php?t=cpeditctag&cat=$cat&$passturl'>".localize('_editctag',$lan)."</a></li>";	
		 
		 if ($environment['EDIT_CATEGORY']==1) 
           $ctokens[] = "<li><a $class href='cpkategories.php?t=cpkategories&cat=$cat&$passturl&encoding=$encoding'>".localize('_editcat',$lan)."</a></li>";

		 if ($environment['CATEGORY_UPLOAD']==1) 
		   $ctokens[] = "<li><a $class href='cpupload.php?cat=$cat&$passturl&encoding=$encoding'>".localize('_uploadcat',$lan)."</a></li>";	 		 
 
	     if ($environment['SYNCPHOTO']==1) 	 
           $ctokens[] = "<li><a $class href='cpitems.php?t=cpvrestorephoto&cat=$cat&$passturl&encoding=$encoding'>".localize('_syncphoto',$lan)."</a></li>"; 

	     if ($environment['DBPHOTO']==1) 	 
           $ctokens[] = "<li><a $class href='cpitems.php?t=cpvdbphoto&cat=$cat&$passturl&encoding=$encoding'>".localize('_dbphoto',$lan)."</a></li>"; 
		 
		 /*if ($environment['RSS']==1) //rss for category  
           $ctokens[] = "<li><a $class href='cpxmlexp.php?editmode=1&cat=$cat&$passturl&encoding=$encoding'>".localize('_rssfeeds',$lan)."</a></li>";	
		  */ 
		 if ($environment['RSS']==1) //rss for category  
           $ctokens[] = "<li><a $class href='cpitems.php?t=cpvitemrss&cat=$cat&$passturl&encoding=$encoding'>".localize('_rssfeeds',$lan)."</a></li>";	
 	
		 if ($environment['ITEM_SENDMAIL']==1) //category send mail..advanced mail template system (rctedit,rctedititems)
		   $ctokens[] = "<li><a $class href='cpsubscribers.php?htmlfile=&encoding=$encoding&cat=$cat&$passturl'>".localize('_senditemmail',$lan)."</a></li>";	 		 
 		 
	 
	     $mycurrentcat = explode($cseparator,$v);
	     $vn = array_pop($mycurrentcat);
	     $cat_htm_attachment = "html/". $vn . $lan . '.htm';		
	     $cat_html_attachment = "html/". $vn . $lan . '.html'; 
	   	 
		 if ($environment['CATEGORY_ATTACHMENT']==1) {
	       if (is_readable($cat_htm_attachment)) 
             $ctokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($cat_htm_attachment)) . "&encoding=$encoding&id=$vn&type=.htm&$passturl'>".localize('_editcathtml',$lan)."</a></li>";	  		   
           elseif (is_readable($cat_html_attachment)) 
	         $ctokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($cat_html_attachment)) . "&encoding=$encoding&id=$vn&type=.html&$passturl'>".localize('_editcathtml',$lan)."</a></li>";	  		   
	       else 
             $ctokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=&encoding=$encoding&id=$vn&type=.html&$passturl'>".localize('_addcathtml',$lan)."</a></li>";			    
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
           $itokens[] = "<li><a $class href='cptags.php?t=cpedititag&id=$v&$passturl&encoding=$encoding'>".localize('_edititag',$lan)."</a></li>";	
	   
         if ($environment['EDIT_ITEM']==1) 
		   $itokens[] = "<li><a $class href='cpitems.php?t=cpitems&id=$v&$passturl&encoding=$encoding'>".localize('_edititem',$lan)."</a></li>";	
  
		 if ($environment['EDIT_ITEM_PHOTO']==1) 
	       $itokens[] = "<li><a $class href='cpitems.php?t=cpvphoto&id=$v&$passturl&encoding=$encoding'>".localize('_edititemphoto',$lan)."</a></li>";
 
	     if ($environment['SYNCPHOTO']==1) 	 
           $itokens[] = "<li><a $class href='cpitems.php?t=cpvrestorephoto&id=$v&$passturl&encoding=$encoding'>".localize('_syncphoto',$lan)."</a></li>"; 

	     if ($environment['DBPHOTO']==1) 	 
           $itokens[] = "<li><a $class href='cpitems.php?t=cpvdbphoto&id=$v&$passturl&encoding=$encoding'>".localize('_dbphoto',$lan)."</a></li>"; 

		 if ($environment['RSS']==1) //rss for item  
           $itokens[] = "<li><a $class href='cpitems.php?t=cpvitemrss&id=$v&$passturl&encoding=$encoding'>".localize('_rssfeeds',$lan)."</a></li>";	
		 
		 if ($environment['ITEM_UPLOAD']==1) 
		   $itokens[] = "<li><a $class href='cpupload.php?id=$v&$passturl&encoding=$encoding'>".localize('_uploadid',$lan)."</a></li>";	 		 
			 

		 if ($environment['ITEM_ATTACHMENT']==1) {
	       $text_attachment = "html/". $v . $lan . '.txt';
	       $htm_attachment = "html/". $v . $lan . '.htm';		
	       $html_attachment = "html/". $v . $lan . '.html'; 
	       //echo $html_attachment,'>';	     
	   
           if ($attachment_type = GetGlobal('controller')->calldpc_method("rcitems.has_attachment2db use $v")) {
	         //echo '>',$attachment-type;
	         switch ($attachment_type) {
		       case '.html' :$itokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . "&encoding=$encoding&id=$v&type=$attachment_type&$passturl'>".localize('_edititemdbhtml',$lan)."</a></li>"; 
		                     break;
						 
		       case '.htm'  :$itokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . "&encoding=$encoding&id=$v&type=$attachment_type&$passturl'>".localize('_edititemdbhtm',$lan)."</a></li>";
			                 break;
						 
		       case '.txt ' :$itokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . "&encoding=$encoding&id=$v&type=$attachment_type&$passturl'>".localize('_edititemdbtext',$lan)."</a></li>";	 
			                 break;			 
		       default      :$itokens[] = 'Unknown attachment type!<br>'; 					 						 						 
						     //$_icons[] = "";
		     }
			 
			 	 
			 if ($environment['ITEM_SENDMAIL']==1) 
		       $itokens[] = "<li><a $class href='cpsubscribers.php?htmlfile=&encoding=$encoding&id=$v&type=$attachment_type&$passturl'>".localize('_senditemmail',$lan)."</a></li>";	 		 
 
			 if ($environment['ITEM_DELETE_DB_ATTACHMENT']==1)  
	           $itokens[] = "<li><a $class href='cpitems.php?t=cpvdelattach&id=$v&$passturl&encoding=$encoding'>".localize('_deleteitemattachment',$lan)."</a></li>";		  
 
	       }
	       //else {	    
	       elseif (is_readable($text_attachment)) {
	           $itokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($text_attachment)) . "&encoding=$encoding&id=$v&$passturl&type=.txt'>".localize('_edititemtext',$lan)."</a></li>";	
			   if ($environment['ITEM_SENDMAIL']==1) 			 
					$itokens[] = "<li><a $class href='cpsubscribers.php?htmlfile=" . urlencode(base64_encode($text_attachment)) . "&encoding=$encoding&id=$v&$passturl&type=.txt'>".localize('_senditemmail',$lan)."</a></li>";	
		   }	 
	       elseif (is_readable($htm_attachment)) {
	           $itokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($htm_attachment)) . "&encoding=$encoding&id=$v&$passturl&type=.htm'>".localize('_edititemhtm',$lan)."</a></li>";
			   if ($environment['ITEM_SENDMAIL']==1) 			  
					$itokens[] = "<li><a $class href='cpsubscribers.php?htmlfile=" . urlencode(base64_encode($htm_attachment)) . "&encoding=$encoding&id=$v&$passturl&type=.htm'>".localize('_senditemmail',$lan)."</a></li>";		
		   }	 
           elseif (is_readable($html_attachment)) {
	           $itokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" . urlencode(base64_encode($html_attachment)) . "&encoding=$encoding&id=$v&$passturl&type=.html'>".localize('_edititemhtml',$lan)."</a></li>";	
			   if ($environment['ITEM_SENDMAIL']==1) 	  		   
					$itokens[] = "<li><a $class href='cpsubscribers.php?htmlfile=" . urlencode(base64_encode($html_attachment)) . "&encoding=$encoding&id=$v&$passturl&type=.html'>".localize('_senditemmail',$lan)."</a></li>";			
		   }
	       else {//create nerw file	 
	           $new_attachment = "html/". $v . $lan . '.html';
               $itokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=&encoding=$encoding&id=$v&$passturl&type=.html'>".localize('_additemhtml',$lan)."</a></li>";			  
			   if ($environment['ITEM_SENDMAIL']==1) //mail ..
		           $itokens[] = "<li><a $class href='cpsubscribers.php?htmlfile=&encoding=$encoding&id=$v&type=.html&$passturl'>".localize('_senditemmail',$lan)."</a></li>";	 		 				   
	       }
	       //}//else
         }//environment
		 elseif ($environment['ITEM_SENDMAIL']==1) //only mail ...
		    $itokens[] = "<a href='cpsubscribers.php?htmlfile=&encoding=$encoding&id=$v&type=.html&$passturl'>".localize('_senditemmail',$lan)."</a></li>";	 		 
 	
		 //win category.......................................................
		 if (!empty($itokens)) {
		    //$active = GetReq('id') ? 'sub-menu active' : 'sub-menu';
		    $li0 = '<li class="sub-menu">
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
           //$etokens[] = "<a href='cptransactions.php?t=cptransview&$passturl&encoding=$encoding'>".localize('_transactions',$lan)."</a></li>";
		   //$_icons[] = "<a href='cptransactions.php?t=cptransview&$passturl&encoding=$encoding'>".loadicon('/icons/'.'_transactions'.'.gif',localize('_transactions',$lan),null)."</a></li>";
		   //new way
		   $etokens[] = "<a href='cptransactions.php?t=cptransactions&$passturl&encoding=$encoding'>".localize('_transactions',$lan)."</a></li>";
		   $_icons[] = "<a href='cptransactions.php?t=cptransactions&$passturl&encoding=$encoding'>".loadicon('/icons/'.'_transactions'.'.gif',localize('_transactions',$lan),null)."</a></li>";		   
		 }  
	   }	 
		 
       if (($v=='signup') || ($v=='RCCONTROLPANEL')) {	
	     if ($environment['USERS']==1) {
           $etokens[] = "<a href='cpusers.php?t=cpusers&$passturl&encoding=$encoding'>".localize('_users',$lan)."</a></li>"; 
		   $_icons[] = "<a href='cpusers.php?t=cpusers&$passturl&encoding=$encoding'>".loadicon('/icons/'.'_users'.'.gif',localize('_users',$lan),null)."</a></li>";
		 }  
	     if ($environment['CUSTOMERS']==1) {
           $etokens[] = "<a href='cpcustomers.php?t=cpcustomers&$passturl&encoding=$encoding'>".localize('_customers',$lan)."</a></li>"; 
		   $_icons[] = "<a href='cpcustomers.php?t=cpcustomers&$passturl&encoding=$encoding'>".loadicon('/icons/'.'_customers'.'.gif',localize('_customers',$lan),null)."</a></li>";
		 } 		 
		 if ($environment['SMS']==1) {
		   $etokens[] = "<a href='cpsmsgui.php?t=cpsmsgui&$passturl&encoding=$encoding'>".localize('_sendsms',$lan)."</a></li>";   
		   $_icons[] = "<a href='cpsmsgui.php?t=cpsmsgui&$passturl&encoding=$encoding'>".loadicon('/icons/'.'_sendsms'.'.gif',localize('_sendsms',$lan),null)."</a></li>";
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
         $ntokens[] = "<li><a $class href='cpkategories.php?t=cpaddcat&$passturl'>".localize('_addcat',$lan)."</a></li>";	
 
	   if ($environment['ADD_ITEM']==1)  
         $ntokens[] = "<li><a $class href='cpitems.php?t=cpvinput&$passturl'>".localize('_additem',$lan)."</a></li>";	
 
	   if (($environment['EDIT_CTAG']==1) || ($environment['EDIT_ITAG']==1))   //add tags no cat/id list
         $ntokens[] = "<li><a $class href='cptags.php?t=cpeditctag&$passturl'>".localize('_addtag',$lan)."</a></li>";	
	   
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
         $stokens[] = "<li><a $class href='cpitems.php?t=cpattach2db&$passturl'>".localize('_itemattachments2db',$lan)."</a></li>";
	 
	   /*if ($environment['RSS']==1) 	 
         $stokens[] = "<li><a $class href='cpxmlexp.php?$passturl&cat=$v'>".localize('_rssfeeds',$lan)."</a></li>";
		*/	
	   if ($environment['RSS']==1) 	 
         $stokens[] = "<li><a $class href='cpitems.php?t=cpvitemrss&$passturl&encoding=$encoding'>".localize('_rssfeeds',$lan)."</a></li>";
	   
	   if ($environment['IMPORTDB']==1) 
         $stokens[] = "<li><a $class href='cpimportdb.php?$passturl&encoding=$encoding'>".localize('_importdb',$lan)."</a></li>";
	
	   if ($environment['SYNCPHOTO']==1) 	 
         $stokens[] = "<li><a $class href='cpitems.php?t=cpvrestorephoto&$passturl&encoding=$encoding'>".localize('_syncphoto',$lan)."</a></li>"; 
	
	   if ($environment['DBPHOTO']==1) 	 
         $stokens[] = "<li><a $class href='cpitems.php?t=cpvdbphoto&$passturl&encoding=$encoding'>".localize('_dbphoto',$lan)."</a></li>"; 
	   
	   if ($environment['SYNCSQL']==1) 	 
         $stokens[] = "<li><a $class href='cpsyncsql.php?$passturl&encoding=$encoding'>".localize('_syncsql',$lan)."</a></li>"; 
		   
	   if ($environment['CONFIG']==1) 	 
         $stokens[] = "<li><a $class href='cpconfig.php?$passturl&encoding=$encoding'>".localize('_config',$lan)."</a></li>"; 
	

     //$stokens[] =  "<li><a $class href=\"cpmdbrec.php?t=rempwd&$passturl\">".localize('_rempass',$lan)."</a></li>";				    	  
	 //$stokens[] =  "<li><a $class href=\"cpmdbrec.php?t=chpass&$passturl\"".localize('_chpass',$lan)."</a></li>";
	 $stokens[] =  "<li><a $class href=\"cpmdbrec.php?t=chpass&$passturl\">".localize('_chpass',$lan)."</a></li>";
     //s$tokens[] =  "<li><a $class href=\"cpmdbrec.php?t=cphelp&$passturl\">".localize('_cphelp',$lan)."</a></li>";		 	
	 /*if ($seclevid>=8) {
		$dhtml_var = remote_paramload('FRONTHTMLPAGE','dhtml',paramload('SHELL','prpath'));
		$dhtml_switch = $dhtml_var>0 ? '0' : '1';	 
		$modetitle = $dhtml_var>0 ? localize('_cpdhtmloff',$lan):localize('_cpdhtmlon',$lan);//'Win Mode OFF' : 'Win Mode ON';	
		$stokens[] =  "<li><a $class href=\"cpconfig.php?t=cpconfmod&var=fronthtmlpage.dhtml&val=$dhtml_switch&$passturl\">".$modetitle."</a></li>";	
	    
	 }*/
	 if ($seclevid>=9) {
		$stokens[] =  "<li><a $class href=\"cpmwiz.php?t=cpwizreinit&$passturl\">".localize('_cpwizard',$lan)."</a></li>";		 
		$stokens[] =  "<li><a $class href=\"cpmdbrec.php?t=cpupgrade&$passturl\">".localize('_cpupgrade',$lan)."</a></li>";	 

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
			$htokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" .$mc_page. "&encoding=$encoding&$passturl'>".$mc_title."</a></li>";
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
		    $qtokens[] = "<li><a $class href='cpmhtmleditor.php?htmlfile=" .$mc_page. "&mc_page=$mcpage&turl=".$_GET['turl']."&encoding=$encoding&$passturl'>".$mc_title."</a></li>";
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
	
	public function templatepanel($init=false) {
		//echo 'z';
		if ($init)
			$this->_show_update_tools(true);		
		else {	
			$this->site_stats(true);
			//$this->_show_update_tools(true);
			$this->_show_addon_tools(1, true);		
		}
	}
	
	public function getStats($section=null, $subsection=null) {
		if (!$section) return 0;
		//print_r($this->stats);//[$section]);
		//echo $section,'>',$subsection;
		$sb = $subsection ? $subsection : 'value';
		return ($this->stats[$section][$sb]);
	}	
	
	public function isStats() {
		return (!empty($this->stats) ? true : false);
	}
	
};
}
?>
<?php

$__DPCSEC['RCCUSTOMERS_DPC']='1;1;1;1;1;1;1;1;1';

if ((!defined("RCCUSTOMERS_DPC")) && (seclevel('RCCUSTOMERS_DPC',decode(GetSessionParam('UserSecID')))) ) {
define("RCCUSTOMERS_DPC",true);

$__DPC['RCCUSTOMERS_DPC'] = 'rccustomers';

//$a = GetGlobal('controller')->require_dpc('nitobi/nitobi.lib.php');
//require_once($a);
$b = GetGlobal('controller')->require_dpc('phpdac/shcustomers.dpc.php');
require_once($b);

$__EVENTS['RCCUSTOMERS_DPC'][0]='cpcustomers';
$__EVENTS['RCCUSTOMERS_DPC'][1]='delcustomer';
$__EVENTS['RCCUSTOMERS_DPC'][2]='regcustomer';
$__EVENTS['RCCUSTOMERS_DPC'][3]='cpcusmail';
$__EVENTS['RCCUSTOMERS_DPC'][4]='cpcusmsend';
$__EVENTS['RCCUSTOMERS_DPC'][5]='cpctype';
$__EVENTS['RCCUSTOMERS_DPC'][6]='insert2';
$__EVENTS['RCCUSTOMERS_DPC'][7]='signup3';
$__EVENTS['RCCUSTOMERS_DPC'][8]='updcustomer';
$__EVENTS['RCCUSTOMERS_DPC'][9]='saveupdcus';

$__ACTIONS['RCCUSTOMERS_DPC'][0]='cpcustomers';
$__ACTIONS['RCCUSTOMERS_DPC'][1]='delcustomer';
$__ACTIONS['RCCUSTOMERS_DPC'][2]='regcustomer';
$__ACTIONS['RCCUSTOMERS_DPC'][3]='cpcusmail';
$__ACTIONS['RCCUSTOMERS_DPC'][4]='cpcusmsend';
$__ACTIONS['RCCUSTOMERS_DPC'][5]='cpctype';
$__ACTIONS['RCCUSTOMERS_DPC'][6]='insert2';
$__ACTIONS['RCCUSTOMERS_DPC'][7]='signup3';
$__ACTIONS['RCCUSTOMERS_DPC'][8]='updcustomer';
$__ACTIONS['RCCUSTOMERS_DPC'][9]='saveupdcus';

$__DPCATTR['RCCUSTOMERS_DPC']['cpcustomers'] = 'cpcustomers,1,0,0,0,0,0,0,0,0,0,0,1';

$__LOCALE['RCCUSTOMERS_DPC'][0]='RCCUSTOMERS_DPC;Customers;Customers';
$__LOCALE['RCCUSTOMERS_DPC'][1]='_reason;Reason;Αιτία';
$__LOCALE['RCCUSTOMERS_DPC'][2]='_cdate;Date in;Ημ/νία εισοδου';
$__LOCALE['RCCUSTOMERS_DPC'][3]='_price;Price;Τιμή';
$__LOCALE['RCCUSTOMERS_DPC'][4]='_ftype;Pay;Πληρωμή';
$__LOCALE['RCCUSTOMERS_DPC'][5]='_name1;First Name;Ονομα';
$__LOCALE['RCCUSTOMERS_DPC'][6]='_name2;Last Name;Επώνυμο';
$__LOCALE['RCCUSTOMERS_DPC'][7]='_kybismos;Kyb.;Κυβικα';
$__LOCALE['RCCUSTOMERS_DPC'][8]='_color;Color;Χρώμα';
$__LOCALE['RCCUSTOMERS_DPC'][9]='_extras;Extras;Εχτρα';
$__LOCALE['RCCUSTOMERS_DPC'][10]='_address;Address;Διεύθυνση';
$__LOCALE['RCCUSTOMERS_DPC'][11]='_tel;Tel.;Τηλέφωνο';
$__LOCALE['RCCUSTOMERS_DPC'][12]='_mob;Mobile;Κινητό';
$__LOCALE['RCCUSTOMERS_DPC'][13]='_mail;e-mail;e-mail';
$__LOCALE['RCCUSTOMERS_DPC'][14]='_fax;Fax;Fax';
$__LOCALE['RCCUSTOMERS_DPC'][15]='_ptype;Price type;Τύπος Τιμών';
$__LOCALE['RCCUSTOMERS_DPC'][16]='_name;Name;Όνομα';
$__LOCALE['RCCUSTOMERS_DPC'][17]='_afm;Vat ID;ΑΦΜ';
$__LOCALE['RCCUSTOMERS_DPC'][18]='_area;Area;Περιοχή';
$__LOCALE['RCCUSTOMERS_DPC'][19]='_prfdescr;Occupation;Επάγγελμα';

class rccustomers extends shcustomers {

    var $title;
	var $carr;
	var $msg;
	var $path, $urlpath, $inpath;
	var $post;
	var $maillink;

	var $_grids;
	var $actcode;
	var $updrec;
	var $usemailasusername;

	function rccustomers() {
	  $GRX = GetGlobal('GRX');
	  $this->title = localize('RCCUSTOMERS_DPC',getlocal());
	  $this->carr = null;
	  $this->msg = null;
	  
	  shcustomers::__construct();

	  $this->path = paramload('SHELL','prpath');
	  //shcustomers construct is not inherit ?? reload params
	  //$this->urlpath = paramload('SHELL','urlpath');
	  //$this->inpath = paramload('ID','hostinpath');	  

	  $this->maillink = seturl('t=cpcusmail&<@>');

      if ($GRX) {

          $this->delete = loadTheme('ditem',localize('_delete',getlocal()));
          $this->edit = loadTheme('eitem',localize('_edit',getlocal()));
          //$this->import = loadTheme('iitem',localize('_import',getlocal()));
          //$this->recode = loadTheme('ritem',localize('_recode',getlocal()));
          $this->add = loadTheme('aitem',localize('_add',getlocal()));
          $this->mail = loadTheme('mailitem',localize('_mail',getlocal()));
		  $this->type = loadTheme('iitem',localize('_ptype',getlocal()));

		  $this->sep = "&nbsp;";//loadTheme('lsep');
      }
      else {
          $this->delete = localize('_delete',getlocal());
          $this->edit = localize('_edit',getlocal());
          //$this->import = localize('_import',getlocal());
          //$this->recode = loadTheme('rvehicle','show help');
          $this->add = localize('_add',getlocal());
          $this->mail = localize('_mail',getlocal());
          $this->type = localize('_ptype',getlocal());

		  $this->sep = "|";
      }

	  $acode = remote_paramload('RCCUSTOMERS','activecode',$this->path);

	  $this->actcode = 'id';//$acode?$acode:'code2';
	  //echo '>',$this->usemailasusername;
	  
	  //shcustomers construct is not inherit ?? reload params
	  //$this->usemailasusername = remote_paramload('SHCUSTOMERS','usemailasusername',$this->path);
	}

    function event($event=null) {

	   /////////////////////////////////////////////////////////////
	   if (GetSessionParam('LOGIN')!='yes') die("Not logged in!");//
	   /////////////////////////////////////////////////////////////

	   switch ($event) {

         case "signup3"    :  if (!$this->checkFields())
		                        $this->insert();
							  //$this->nitobi_javascript();
 			                  break;

		 case 'cpctype'    :  $this->make_cus_type();
			                       //$this->read_list();
			                       break;
	     case 'cpcusmsend'  : $this->send_mail();
		                      //$this->carr = $this->select_customers('all',null,GetReq('alpha'));//dummy param
		                      break;
	     case 'cpcusmail'   :
		                      break;

	     case 'regcustomer' :
		                      break;
         case 'updcustomer' : $this->updrec = $this->getcustomer(GetReq('rec'),$this->actcode);
		                      $this->grid_javascript();
		                      break;
	     case 'saveupdcus'  : $this->update(GetReq('rec'),$this->actcode);
		                      break;
	     case 'delcustomer' : $this->delete_customer(GetReq('rec'),$this->actcode);
		                      //$this->carr = $this->select_customers('all',null,GetReq('alpha'));
							  break;
	     case 'cpcustomers' :
		 default            : 
		                      //$this->carr = $this->select_customers('all',null,GetReq('alpha'));//dummy param
	   }

    }

    function action($action=null) {

	  /*if (GetSessionParam('REMOTELOGIN'))
	     $out = setNavigator(seturl("t=cpremotepanel","Remote Panel"),$this->title);
	  else
         $out = setNavigator(seturl("t=cp","Control Panel"),$this->title);
      */
	  switch ($action) {
	     case 'cpcusmsend'  : $out .= $this->show_customers();
		                      break;
	     case 'cpcusmail'   : $out .= $this->show_mail();
		                      break;
	     case 'delcustomer' : $out .= $this->show_customers();
		                      break;
		 case 'regcustomer' : //$out .= $this->form();
		                      //$out .= $this->show_customers();
							  $out .= $this->makeform(null,1,'signup3');
							  break;
		 case 'updcustomer' : //$goto = 't=cpcustomers&rec='.GetReq('rec').'&editmode=1&encoding='.GetReq('encoding');
							  //$out .= $this->makeform($this->updrec,1,'saveupdcus',1,$goto,1);
							  $out .= $this->update_customer_form();
							  break;
		 case 'saveupdcus'  :
		 case 'signup3'     :
		 case 'cpctype'     :
	     case 'cpcustomers' :
		 default            :
		                      $out .= $this->show_customers();
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
	
	protected function update_customer_form() {
	
	   //update form
       $goto = 't=cpcustomers&rec='.GetReq('rec').'&editmode=1&encoding='.GetReq('encoding');
	   $form = $this->makeform($this->updrec,1,'saveupdcus',1,$goto,1);	   
	   
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

	function delete_customer($id,$key=null) {
        $db = GetGlobal('db');

		$sSQL = "delete from customers where ";
		if ($key)
		  $sSQL .= $key . "=" . $id;//'' must added to param
		else
		  $sSQL .= "email = " . $db->qstr($id);

        $db->Execute($sSQL,1);
	    //echo $sSQL;

		$this->msg = "Customer with $key=$id deleted!";
	}


	function show_customers() {
     	$sFormErr = GetGlobal('sFormErr');

	    if ($this->msg) 
			$out = $this->msg;
	    /*
	    $myadd = new window('',seturl("t=regcustomer","Register a new customer!"));
	    $out .= $myadd->render("center::100%::0::group_article_selected::right::0::0::");
	    unset ($myadd);
	    */
        $out .= $sFormErr;

	    if (defined('MYGRID_DPC')) {
		
			$xsSQL2 = "SELECT * FROM (SELECT i.id,i.name,i.afm,i.eforia,i.prfdescr,i.mail,i.address,i.area,c.email,c.notes,c.username FROM customers i";
			$xsSQL2.= " INNER JOIN users c ON c.code2 = i.code2 AND i.active>0) x";
			//$out.= $xsSQL2;
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+id|".localize('id',getlocal())."|5|0|||1");
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+notes|".localize('_active',getlocal())."|boolean|1|ACTIVE:DELETED");
		    //GetGlobal('controller')->calldpc_method("mygrid.column use grid2+email|".localize('_mail',getlocal())."|20|1|");	
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+email|".localize('_mail',getlocal())."|link|20|".seturl('t=cptransactions&editmode=1&cusmail={email}').'||');
		    //GetGlobal('controller')->calldpc_method("mygrid.column use grid2+name|".localize('_name',getlocal())."|20|1|");
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+name|".localize('_name',getlocal())."|link|20|".seturl('t=updcustomer&editmode=1&rec={id}&cusmail={username}').'||');
		    GetGlobal('controller')->calldpc_method("mygrid.column use grid2+prfdescr|".localize('_prfdescr',getlocal())."|20|1|");			
		    //GetGlobal('controller')->calldpc_method("mygrid.column use grid2+afm|".localize('_afm',getlocal())."|10|1|");
	        //GetGlobal('controller')->calldpc_method("mygrid.column use grid2+eforia|".localize('_doy',getlocal())."|20|1|");				
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+address|".localize('_address',getlocal())."|20|1|");
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+area|".localize('_area',getlocal())."|10|1|");
			GetGlobal('controller')->calldpc_method("mygrid.column use grid2+username|".localize('_mail',getlocal())."|5|0|||1|1");
			$out .= GetGlobal('controller')->calldpc_method("mygrid.grid use grid2+customers+$xsSQL2+r+".localize('RCCUSTOMERS_DPC',getlocal())."+id+1+1+36+800");

	    }
		else 
		   $out .= 'Initialize jqgrid.';
		   
        return ($out); 
	}

	function form($action=null) {

     $myaction = seturl("t=regcustomer");

     if ($this->post==true) {

	   SetSessionParam('REGISTERED_CUSTOMER',1);
	 }
	 else { //show the form plus error if any

       //if (!$action) $out = setNavigator($this->title);

       $out .= setError($sFormErr . $this->msg);


	   $form = new form(localize('_ADDEVENT',getlocal()), "regcustomer", FORM_METHOD_POST, $myaction, true);

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
	   //$form->addElement		("personal",			new form_element_text		(localize('_CNTR',getlocal()),     "country",	    GetParam("country"),				"forminput",	        20,				255,	0));
	   $form->addElement		("personal",			new form_element_combo_file (localize('_CNTR',getlocal()),     "country",	    $this->get_country_from_ip(),				"forminput",	        1,				0,	'country'));
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
	     $form->addElement		(FORM_GROUP_HIDDEN,		new form_element_hidden ("FormAction", "regcustomer"));

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

	function show_mail() {
       $sFormErr = GetGlobal('sFormErr');
	   $sendto = GetReq('m');

	   if (defined('ABCMAIL_DPC')) {
	     $ret = $sFormErr;
	     $ret .= GetGlobal('controller')->calldpc_method('abcmail.create_mail use cpcusmsend+'.$sendto);
	   }

	   return ($ret);
	}

	function send_mail() {

	   if (!defined('ABCMAIL_DPC')) return;

	   $from = GetParam('from');
	   $to = GetParam('to');
	   $subject = GetParam('subject');
	   $body = GetParam('mail_text');

	   if ($res = GetGlobal('controller')->calldpc_method('abcmail.sendit use '.$from.'+'.$to.'+'.$subject.'+'.$body))
	     $this->mailmsg = "Send successfull";
	   else
	     $this->mailmsg = "Send failed";
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

	function make_cus_type() {
        $db = GetGlobal('db');
		$mycode = $this->actcode;

	    $sSQL = "select attr1 from customers where $mycode=".GetReq('rec');
		$ret = $db->Execute($sSQL,2);

		switch ($ret->fields[0]) {
		  case $this->reseller_attr  : $sw = ''; break;
		  default                    : $sw = $this->reseller_attr ;
		}
		//echo $sSQL,$sw,'>',$ret->fields[0];

	    $sSQL = "update customers set attr1="."'$sw' where $mycode=".GetReq('rec');
		$db->Execute($sSQL,1);
		//reset session of user
		$sSQL = "update users set sesdata='' where $mycode=".GetReq('rec');
		$db->Execute($sSQL,1);
        //echo $sSQL;
		$this->msg = "Job completed!(Customer type: $sw)";
	}
	
	//override
	function update($id=null,$fkey=null) {
	   $db = GetGlobal('db');
	   //$myfkey = $fkey?$fkey:$this->fkey;
	   //$key = decode(GetGlobal('UserName'));//security..foreign to user
	   
	   if ($error = $this->checkFields(null,$this->checkuseasterisk)) {
	       SetGlobal('sFormErr',$error);
	       return ($error);//false;//($error);
	   }		   

       if ($this->usemailasusername) {
	     if (GetParam('uname')) //= mail
		   $recfields = array('name','afm','eforia','prfdescr','address','area','zip','voice1','voice2','fax');
		 else
	       $recfields = array('name','afm','eforia','prfdescr','address','area','zip','voice1','voice2','fax','mail');
	   }
	   else
	     $recfields = array('code2','name','afm','eforia','prfdescr','address','area','zip','voice1','voice2','fax','mail');

	   if (!$id) {
	     //return (false);
		 SetGlobal('sFormErr',localize('_MSG20',getlocal()));
	   }	 

       $sSQL = "update customers set ";
	   $sSQL.= /*'code2='.$db->qstr(GetParam('code2')) . ',' .*/
	           'name='.$db->qstr(addslashes(GetParam('name'))) . ',' .
	           'afm='.$db->qstr(addslashes(GetParam('afm'))) . ',' .
	           'eforia='.$db->qstr(addslashes(GetParam('eforia'))) . ',' .
	           'prfdescr='.$db->qstr(addslashes(GetParam('prfdescr'))) . ',' .
	           'address='.$db->qstr(addslashes(GetParam('address'))) . ',' .
	           'area='.$db->qstr(addslashes(GetParam('area'))) . ',' .
	           'zip='.$db->qstr(addslashes(GetParam('zip'))) . ',' .
	           'voice1='.$db->qstr(addslashes(GetParam('voice1'))) . ',' .
	           'voice2='.$db->qstr(addslashes(GetParam('voice2'))) . ',' .
	           'fax='.$db->qstr(addslashes(GetParam('fax'))) . ',' .
	           'mail='.$db->qstr(addslashes(GetParam('mail')))  .
	           " where id=".$id;// . " and " . "code2=" . $db->qstr($key);

       //echo $sSQL;
	   //SetGlobal('sFormErr',$sSQL);
       $result = $db->Execute($sSQL,1);
	   //print_r($result->fields);
       if ($db->Affected_Rows()) {	   
         SetGlobal('sFormErr',"ok");
		 return true;
	   }
	   else {
		 echo $db->ErrorMsg();
		 SetGlobal('sFormErr',localize('_MSG20',getlocal()));
	   }	 

	   return false;//($result);
	}
	

};
}
?>
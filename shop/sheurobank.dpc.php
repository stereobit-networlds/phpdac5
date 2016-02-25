<?php

$__DPCSEC['SHEUROBANK_DPC']='1;1;1;1;1;1;1;1;1';

if ((!defined("SHEUROBANK_DPC")) && (seclevel('SHEUROBANK_DPC',decode(GetSessionParam('UserSecID')))) ) {
define("SHEUROBANK_DPC",true);

$__DPC['SHEUROBANK_DPC'] = 'sheurobank';

$d = GetGlobal('controller')->require_dpc('libs/sha256.lib.php');
require_once($d); 

$__EVENTS['SHEUROBANK_DPC'][0]='eurobank';
$__EVENTS['SHEUROBANK_DPC'][1]='process';
$__EVENTS['SHEUROBANK_DPC'][2]='payreturn';
$__EVENTS['SHEUROBANK_DPC'][3]='paycancel';
$__EVENTS['SHEUROBANK_DPC'][4]='payticket';
 
$__ACTIONS['SHEUROBANK_DPC'][0]='eurobank';
$__ACTIONS['SHEUROBANK_DPC'][1]='process';
$__ACTIONS['SHEUROBANK_DPC'][2]='payreturn';
$__ACTIONS['SHEUROBANK_DPC'][3]='paycancel';
$__ACTIONS['SHEUROBANK_DPC'][4]='payticket';


$__DPCATTR['SHEUROBANK_DPC']['sheurobank'] = 'eurobank,1,0,0,0,0,0,0,0,0,0,0,1';

$__LOCALE['SHEUROBANK_DPC'][0]='SHEUROBANK_DPC;Eurobank;Eurobank'; 
$__LOCALE['SHEUROBANK_DPC'][1]='_PLEASEWAIT;Please wait, your order is being processed...;Παρακαλώ περιμένετε...';  
$__LOCALE['SHEUROBANK_DPC'][2]='_PAYCANCEL;Transaction canceled.;Ακυρωση συναλλαγης.';  
$__LOCALE['SHEUROBANK_DPC'][3]='_PAYERROR;Error during payment. Please try later.;Λανθασμένη συναλαγή. Παρακαλώ δοκιμάστε αργότερα.';  
$__LOCALE['SHEUROBANK_DPC'][4]='_SRVERROR;Unknown error. Please try later.;Άγνωστο σφάλμα. Παρακαλώ δοκιμαστε αργότερα.';  
$__LOCALE['SHEUROBANK_DPC'][5]='_EUROBANK;Credit card;Πιστωτική κάρτα'; 
$__LOCALE['SHEUROBANK_DPC'][6]='_mailSubject;Transaction No ;Παραστατικό αγοράς Νο '; 
 
class sheurobank  {

   var $title;
   var $transaction, $amount;
   var $error;
   var $path, $title2show;
   var $fields;   
   var $eurobank_url, $eurobank_payment;   
   
   //pay form
   var $merchantid, $eurobank_user, $eurobank_pass, $lancode, $parambacklink;   
   var $return_post_args;
   
   var $msg, $debug, $debug_return;
   var $tmpl_path, $tmpl_name;   
   

   function __construct() {
	  $UserSecID = GetGlobal('UserSecID');
	  $UserName = GetGlobal('UserName');
	  $UserID = GetGlobal('UserID');

      $this->userLevelID = (((decode($UserSecID))) ? (decode($UserSecID)) : 0);
	  $this->username = decode($UserName);
	  $this->userid = decode($UserID);   
	 
	  $this->path = paramload('SHELL','prpath'); 
	  $inpath = paramload('ID','hostinpath');	 	 
 	  $this->title = localize('SHEUROBANK_DPC',getlocal());
	  
      $this->msg = null;
      $this->debug = 0;//1;	  
      $this->debug_return = 0;//1;	  		  
	  
      $this->title2show =  remote_paramload('SHEUROBANK','title',$this->path);	  
	  //$this->amount = GetSessionParam('amount') ? GetSessionParam('amount') : 0.10;	//cart value
      $pay = GetSessionParam('amount') ? GetSessionParam('amount') : 0.10;	//cart value
	  //echo $pay;
      $this->amount = strval(number_format($pay,2,',','.'));	  
	  
	  /*if (($this->debug) || ($this->debug_return))
	       //test transaction (already in trnas table)
	    $this->transaction = 1073;
	  else*/ //normal cart process
	  	$this->transaction = GetSessionParam('eurobankID') ? GetSessionParam('eurobankID') : GetReq('tid');	 

	  
	  $sandbox = remote_paramload('SHEUROBANK','sandbox',$this->path);
	  //echo '>',$sandbox;
	  if ($sandbox)
       $this->eurobank_url = "https://euro.test.modirum.com/vpos/shophandlermpi";
	  else  
       $this->eurobank_url = "https://vpos.eurocommerce.gr/vpos/shophandlermpi"; //"https://euro.test.modirum.com/vpos/shophandlermpi";
           
      $this->eurobank_payment = false; //init	   
	  $this->fields = array();//empty data to send
	  	
      $this->merchantid =  remote_paramload('SHEUROBANK','merchantid',$this->path);		
      $this->eurobank_user =  null;//remote_paramload('SHEUROBANK','user',$this->path);	
      $this->eurobank_pass =  remote_paramload('SHEUROBANK','pass',$this->path);		  	
	  	  
	  switch (getlocal()) {
        case 1  : $this->lancode =  'el_GR'; break;
		case 0  : 
		default : $this->lancode =  'en_US'; 
	  } 	  		  
	  
      $this->parambacklink = 't=paycancel&tid='.$this->transaction;//remote_paramload('SHEUROBANK','parambacklink',$this->path);       
	  $this->return_post_args = array('mid','orderid','status','orderAmount','currency','paymentTotal','message','riskScore','payMethod','txId','paymentRef','digest');
	  
      //$page = $this->parambacklink?$this->parambacklink:$_SERVER['PHP_SELF'].'?t=paycancel&tid='.$this->transaction;
      //$this->this_script = 'http://'.$_SERVER['HTTP_HOST'].$inpath.'/'.$page;	  	
	  
	  $this->tmpl_path = remote_paramload('FRONTHTMLPAGE','path',$this->path);
	  $this->tmpl_name = remote_paramload('FRONTHTMLPAGE','template',$this->path);	   	   
	  		
   }
   
   function event($event=null) {
   
     switch ($event) {  
	 
       case 'payreturn':$this->eurobank_payment = $this->eurobank_receive_post(); 
	   
	                    if ($this->eurobank_payment) {
	                       // Order was successful...
	                       $this->handle_Transaction('success');
						   
						   if (defined('SHCART_DPC')) {
							   
						     $tid = $this->eurobank_get_post_params('orderid');
							 $subject = localize('_mailSubject', getlocal()) . $tid;
							 
					         GetGlobal('controller')->calldpc_method("shcart.goto_mailer use $tid++invoice.htm+".$subject);
					       }
						}
						else 
						   $this->handle_Transaction('error');
					   
	                     break;
						 
       case 'paycancel' : //$this->savelog("PIRAEUS PAYMENT:CANCELED");	
	     	           $this->handle_Transaction('cancel');
					   
					   if (defined('SHCART_DPC')) {
					     GetGlobal('controller')->calldpc_method("shcart.cancel_order");
					   }
					   
	                   break;
					   
       case 'payticket' : //not used ...procedure executes on process/default                 
	                   break;
					   
	   case 'eurobank':
	   case 'process' :
       default        : if ($this->amount>0) {
						    //$this->handle_Transaction('ticket'); //init	
						    $this->eurobank_create_payment();	 
                            die();
					    }   
	 }
   }
   
   function action($action=null) {
   
     switch ($action) {
		case 'payreturn' : if ($this->eurobank_payment) {
			
			                 $tid = $this->eurobank_get_post_params('orderid');
		                     //$ret = $this->set_message('success',null);
							 
		                     if (defined('SHCART_DPC')) {
								  
		                        //$ret = $this->set_message('success'); //NO
							    //NO NEED...called into cartview tid+3
		                        //$ret .= GetGlobal('controller')->calldpc_method("shcart.finalize_cart use ".$tid);
							   
							    //$ok = GetGlobal('controller')->calldpc_method("shcart.loadcart use ".$tid);
							    // if ($ok) {
		                          $ret .= GetGlobal('controller')->calldpc_method("shcart.cartview use $tid+3");
								  GetGlobal('controller')->calldpc_method("shcart.clear");
								//}  
						     }	 
						   }
						   else {
							   
						      //$ret = $this->set_message('error','_PAYERROR');
							  
		                      if (defined('SHCART_DPC')) {							  
							    //reload cart from error transaction
							    //$ok = GetGlobal('controller')->calldpc_method("shcart.loadcart use ".$tid);//GetReq('tid'));
							    //if ($ok)
		                          $ret .= GetGlobal('controller')->calldpc_method("shcart.cartview use ".$tid);//GetReq('tid'));
							  }	  
						   }	 
		                   break;
		case 'paycancel'  : 
		                   //$ret = $this->set_message('cancel','_PAYCANCEL');   
						   if (defined('SHCART_DPC')) {
							 //reload cart from canceled transaction
							 //$ok = GetGlobal('controller')->calldpc_method("shcart.loadcart use ".GetReq('tid'));
							 //if ($ok)
		                       $ret .= GetGlobal('controller')->calldpc_method("shcart.cartview use ".GetReq('tid')."+2");
							 //else goto fp  
						   }	 
		                   break;
		case 'payticket' : $ret = null;
		                   break; 
		case 'eurobank':				 
	    case 'process' : //$ret = $this->error;//null;//must not have action, if error goto frontpage'Please wait processing...'; 
	    default        : //in case of no amount (error?)  or ticket error goto cart 		
		                 //$ret = $this->set_message('error','_SRVERROR');   
						 if (defined('SHCART_DPC')) {
							 //reload cart from error ... no need
							 //GetGlobal('controller')->calldpc_method("shcart.loadcart use ".GetReq('tid'));
				             if (GetGlobal('controller')->calldpc_var("shcart.qtytotal")>0)			 
		                       $ret .= GetGlobal('controller')->calldpc_method("shcart.cartview use ".GetReq('tid'));
		                       //else goto fp  
						 }	 
	 }
	 
	 return ($ret);
   }

   protected function eurobank_create_payment() {
	  
	  $confirmUrl = 't=payreturn&tid='.$this->transaction;
	  $cancelUrl = 't=paycancel&tid='.$this->transaction;
	  $description = localize('_mailSubject', getlocal()) . $this->transaction;
	  
	  $this->add_field('mid', $this->merchantid);
	  $this->add_field('orderid', $this->transaction);
	  $this->add_field('orderDesc', $description); //$this->title2show);
	  $this->add_field('orderAmount', $this->amount);
	  $this->add_field('currency', 'EUR');
	  $this->add_field('payerEmail', $this->username);
	  $this->add_field('confirmUrl', setUrl($confirmUrl));
	  $this->add_field('cancelUrl', setUrl($cancelUrl));
	  
	  $form_data = implode("", $this->fields) . $this->eurobank_pass;
	  $digest = base64_encode(sha1($form_data, true));
	  $this->add_field('digest', $digest);
	  
	  //echo $form_data . "<br/>" . $digest;
	  //die();
	  
      $this->submit_data(); // submit the payment fields to piraeus		  	   
   }
   
   protected function eurobank_receive_post() {
      $ret = false;
	  
	  if ($this->debug_return)
	    print_r($_POST);
	
	  $post = array();
	  foreach ($this->return_post_args as $arg) {	  
		${$arg} = $_POST[$arg];
        if ($arg!='digest') //exclude digest param from post array (exist as $(arg))
			$post[$arg] = $_POST[$arg]; 		
	  }  
	  
	  if ($this->verify_eurobank_payment($post, $digest)) {	

	    //save post to file
        if ($f = fopen($this->path."eurobank_last_pay.txt",'a+')) {
		  	    
	        foreach ($_POST as $id=>$val)
				$pstr .= "[".$id."]=".$val."\r";
	        fwrite($f,$pstr,strlen($pstr));
	        fclose($f);	  
	    }	  

	    if ((strcmp($post['status'], 'AUTHORIZED')==0) || (strcmp($post['status'], 'CAPTURED')==0)) {
			
			$ret = true;						   					      	   	 
	    }
	    elseif (strcmp($post['status'], 'CANCELED')==0) 
			$ret = false;
	    elseif (strcmp($post['status'], 'REFUSED')==0) 
			$ret = false;
	    elseif (strcmp($post['status'], 'ERROR')==0) 
			$ret = false;
	    else //unknown error
			$ret = false;
	    
		
      }//verify_eurobank_payment		
	  
	  return ($ret);				   
     
   }
   
   //check the posted data digest param by replicate it
   protected function verify_eurobank_payment($post_data=null, $post_digest=null) {
     //echo 'Verify:';
     if (is_array($post_data) && ($post_digest)) { 
	   
	   $form_data = implode("",$post_data) . $this->eurobank_pass;  
	   $digest = base64_encode(sha1($form_data, true));
	   //echo '>',$digest,'-',$post_digest;
	   if (strcmp($digest, $post_digest)==0) {
		 
	       $this->savetransaction($post_data);
		   
		   if ($this->debug_return)
	         echo '<br>Message:'.$post_data['message'];
			 		   		 
	       return true;	  
	   }	 
	 }
	 
	 if ($this->debug_return)
	   echo '<br>Message:'.$post_data['message'];
   
	 return false;
   } 
   
   
   protected function mybin2hex($str) { 
   
     $strl = strlen($str); 
     $fin = ''; 
     for($i =0; $i < $strl; $i++) {
		 
         $fin .= dechex(ord($str[$i])); 
		 
         if ($this->debug_return) {
		   echo '<br>'.$str[$i].'<<<>>>'.ord($str[$i]).'<<<>>>'.dechex(ord($str[$i])); 
		 }
     } 
     return $fin; 
     
   }  

   protected function eurobank_get_post_params($field=null) {
      if ($field==null) return null;
	  
	  return ($_POST[$field]);
   }     
   
   protected function handle_Transaction($status,$ticket=null) {

           if ( (defined('SHTRANSACTIONS_DPC')) && (seclevel('SHTRANSACTIONS_DPC',decode(GetSessionParam('UserSecID')))) ) {
                
                $id = $this->transaction ? $this->transaction : GetReq('tid');

                switch ($status) {
                    case 'ticket' : GetGlobal('controller')->calldpc_method("shtransactions.setTransactionStatus use $id+1");
                                    break;
                    case 'success': GetGlobal('controller')->calldpc_method("shtransactions.setTransactionStatus use $id+2");
                                    break;													
                    case 'cancel' : GetGlobal('controller')->calldpc_method("shtransactions.setTransactionStatus use $id+-1");
                                    break;
                    case 'error'  : GetGlobal('controller')->calldpc_method("shtransactions.setTransactionStatus use $id+-2");
                                    break;									
                }
           }
   }    
   
   protected function add_field($field, $value) {
      
      // adds a key=>value pair to the fields array, which is what will be 
      // sent to paypal as POST variables.  If the value is already in the 
      // array, it will be overwritten.
      
      $this->fields["$field"] = $value;
   }
   
   protected function submit_data() {
 
      // this function actually generates an entire HTML page consisting of
      // a form with hidden elements which is submitted to paypal via the 
      // BODY element's onLoad attribute.  We do this so that you can validate
      // any POST vars from you custom form before submitting to paypal.  So 
      // basically, you'll have your own form which is submitted to your script
      // to validate the data, which in turn calls this function to create
      // another hidden form and submit to paypal.
 
      // The user will briefly see a message on the screen that reads:
      // "Please wait, your order is being processed..." and then immediately
      // is redirected to paypal.
	  

      echo "<html>";
      echo "<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
	  echo "<title>Processing Payment...</title>";
	  echo "<script type=\"text/javascript\">
		function send_tran() {
			var frm = document.getElementById('form');
			frm.style.visibility=\"hidden\";
			frm.submit();
		}
		</script>";
	  echo "</head>";
	  if ($this->debug) {//dont send the request just print form
	    echo "<body>";
		echo '<br>' . $this->eurobank_url;
	  }
	  else //goto bank  	  
        //echo "<body onLoad=\"document.form.submit();\">";
		echo "<body onLoad=\"send_tran();\">";
	  
      echo "<center><h3>".localize('_PLEASEWAIT',getlocal())."</h3></center>";
      echo "<form method=\"post\" id=\"form\" name=\"form\" action=\"".$this->eurobank_url."\" accept-charset=\"UTF-8\">";

      foreach ($this->fields as $name => $value) {
         echo "<input type=\"hidden\" name=\"$name\" value=\"$value\">";
 	     if ($this->debug) {
		   echo '<br>'. $name . '=>'.$value;
		 }
      }
 
      echo "</form>";
      echo "</body></html>";
    
   } 
   
   protected function set_message($case,$errorcode=null) {

	    $template = remote_paramload('SHEUROBANK',$case,$this->path) . '.htm'; //'fpcartline.htm';
	    $t = $this->path . $this->tmpl_path .'/'. $this->tmpl_name .'/'. str_replace('.',getlocal().'.',$template) ;
	    //echo $t,'>';
	    if (is_readable($t)) 
		  $ret = file_get_contents($t);
	    else
	      $ret = $m?$m:localize($errorcode,getlocal()); //plain text	
		  
		//if ($errorcode)
		  //$ret .= "[ERRORCODE:".$errorcode."]";
		 
		//if ($this->msg)
		  //$ret .= "<br/>( $this->msg )"; 
		  
		$ret .= "<h2>";  
		
		//PIRAEUS response code
		switch ($this->responsecode) {
		
		  case '12' : $ret .= "Transaction Denied! Please check your credit card."; break;
		  case '11' : if ($this->authstatus=='03') {
		                $ret .= "Approved Transaction.";//"<br>Recharge Attempt!"; //...allowed by piraeus just send a info mail..above
					  }	
		              break;		
		  case '12' : if ($this->authstatus=='02')
		                $ret .= "Transaction Declined by the bank! Please use another credit card!"; 
		              break;
		  case '00' : if ($this->authstatus=='01')
		                $ret .= "Approved Transaction.";//VISA 
					  elseif ($this->authstatus=='02')
					    $ret .= "Approved Transaction.";//MASTERCARD 
					  else
					    $ret .= "Approved Transaction.";//anyway		
					  break;
		  default   : if ($this->authstatus=='01') {
		                if ($this->resultcode =='1072')
						  $ret .= "Package is closing! Please try later.";
						elseif ($this->resultcode =='501')
		                  $ret .= "Communication Error! Please try later.";
					  }	
					  elseif ($this->authstatus=='02')	
					    $ret .= "Invalid Card Number! Please check your credit card details or use a different card.";
                      elseif ($this->authstatus=='03')	
					    $ret .= "Under procees transaction was re-send! Please try later.";						
		}
		
		$ret .= "</h2>";    
		  
		return ($ret);    	   
   }
   
   protected function tell_by_mail($subject,$from,$to,$body) {
         

         $smtpm = new smtpmail;
         $smtpm->to = $to; 
         $smtpm->from = $from; 
         $smtpm->subject = $subject;
         $smtpm->body = $body;
         $mailerror = $smtpm->smtpsend();
         unset($smtpm);	
		 
		 if ($mailerror) echo "Error sending mail:",$mailerror;
		 return ($mailerror);   
   }  
   
   protected function savetransaction($status) {
   
     $actfile = paramload('SHELL','prpath') . "transactions" . ".txt";							
	 //echo $actfile;
	 if ((is_file($actfile)) && (is_writable($actfile))) 
	   $mode='a+';
	 else 
	   $mode='w';
	 
	 switch ($status) {
	   case 'RESULTCODE' : $data = implode(",", $status); break;
	   case 'STATUSFLAG' : $data = implode(",", $status); break;
       case 'REPAY'      : $data = implode(",", $status); break;   
       case 'SUCCESS'    : $data = implode(",", $status); break;
	   default           : $data = implode(",", $status);
	 }
	   
     $record = date('Y-m-d h:m:s') .','. $data."\n";
	 
	 //save in db
	 $id = $this->transaction ? $this->transaction : $status['orderid']; 
	 GetGlobal('controller')->calldpc_method("shtransactions.setTransactionStoreData use $id+type1+".$status['txId']);
     GetGlobal('controller')->calldpc_method("shtransactions.setTransactionStoreData use $id+type2+".$record);	 	
				 	 
     if ($fp = @fopen ($actfile , $mode)) {	 
                 fwrite ($fp,$record);
                 fclose ($fp);
     }
     else 
		 echo "File creation error!(".$record.')';
   }              
      	
}; 
} 
?>
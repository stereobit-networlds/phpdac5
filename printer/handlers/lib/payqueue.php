<?php

class payqueue {
  
    protected $paypal_post, $p, $this_script, $sandbox;
	protected $user, $printer_name, $admin_path;

    function __construct($user, $printer_name, $admin_path=null) {
	    $this->user = $user;
        $this->printer_name = $printer_name;
        $this->admin_path = $admin_path ? $admin_path : $_SERVER['DOCUMENT_ROOT'] .'/admin/'.$this->printer_name;	
		
		$this->sandbox = true;
		$this->paypal_post = array();		
		
		// setup a variable for this script (ie: 'http://www.micahcarrick.com/paypal.php')
		$this->this_script = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];		

		// if there is not action variable, set the default action of 'process'
		if (empty($_GET['action'])) $_GET['action'] = 'process'; 			
	}
	
	//form
	public function paybutton($iname=null, $value=null, $sellermail=null, $cur=null) {
	    if (!$sellermail) return false;
		$currency = $cur ? $cur : 'EUR'; //'USD'
		$item_name = $iname ? $iname : 'Digital Download';
		$amount = $value ? $value : 0.1;
		
		$protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
		$return_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; //THIS URL
		$ipn_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/ipn.php?u='.$this->user.'&printer='.$this->printer_name;//THE URL TO YOUR ipn.php SCRIPT
	
		$ret = "<form name=\"_xclick\" action=\"https://www.sandbox.paypal.com/cgi-bin/webscr\" 
    method=\"post\">
    <input type=\"hidden\" name=\"cmd\" value=\"_xclick\">
    <input type=\"hidden\" name=\"business\" value=\"$sellermail\">
    <input type=\"hidden\" name=\"currency_code\" value=\"$currency\">
    <input type=\"hidden\" name=\"item_name\" value=\"$item_name\">
    <input type=\"hidden\" name=\"amount\" value=\"$amount\">
    <input type=\"hidden\" name=\"return\" value=\"$return_url\">
    <input type=\"hidden\" name=\"notify_url\" value=\"$ipn_url\">
    <input type=\"hidden\" name=\"invoice\" value=\"$this->printer_name\">
    <input type=\"hidden\" name=\"custom\" value=\"$this->user\">	
    <input type=\"image\" src=\"http://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif\" 
        border=\"0\" name=\"submit\" alt=\"Make payments with PayPal - it's fast, free and secure!\">
</form>";

		return ($ret);
	}	
	
	public function paypal($iname=null, $value=null, $sellermail=null, $cur=null) {
	    if (!$sellermail) return false;
		$currency = $cur ? $cur : 'EUR'; //'USD'
		$item_name = $iname ? $iname : 'Digital Download';
		$amount = $value ? $value : 0.1;	
	
		switch ($_GET['action']) {	
		    case 'process':      // Process and order...
			$this->p = new paypal_class;  // initiate an instance of the class		
			if ($this->sandbox)
				$this->p->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';   // testing paypal url
			else  
				$this->p->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';     // paypal url			
			$this->p->add_field('business', $this->paypal_mail);//'YOUR PAYPAL (OR SANDBOX) EMAIL ADDRESS HERE!');
			$this->p->add_field('return', $this->this_script.'?action=success&'.SID);
			$this->p->add_field('cancel_return', $this->this_script.'?action=cancel&'.SID);
			$this->p->add_field('notify_url', $this->this_script.'?action=ipn&'.SID);
			$this->p->add_field('item_name', $item_name);
			$this->p->add_field('amount', $amount);  
			$this->p->add_field('first_name', $this->printer_name);
			$this->p->add_field('last_name', $this->user);	
            $this->p->submit_paypal_post();
            die();
            break;

			case 'success':      // Order was successful... 			
			$this->get_paypal_posts(); //HAS NOT CONFIRMED BY IPN YET!!!!			
			break;
			
			case 'cancel':       // Order was canceled...
			$this->savelog("PAYPAL PAYMENT:CANCELED");
            break;

			case 'ipn':          // Paypal is calling page for IPN validation...
			$this->p = new paypal_class;  // initiate an instance of the class		
			if ($this->sandbox)
				$this->p->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';   // testing paypal url
			else  
				$this->p->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';     // paypal url			
			if ($this->p->validate_ipn()) {			
				$this->procced_on_payment();//backend operations
  
				// For this example, we'll just email ourselves ALL the data.
				$subject = 'Instant Payment Notification - Recieved Payment';
				$to = $this->inform_ipn_mail;//'YOUR EMAIL ADDRESS HERE';    //  your email
				$body =  "An instant payment notification was successfully recieved\n";
				$body .= "from ".$this->p->ipn_data['payer_email']." on ".date('m/d/Y');
				$body .= " at ".date('g:i A')."\n\nDetails:\n";
         
				foreach ($this->p->ipn_data as $key => $value) 
					$body .= "\n\r$key: $value"; 
				//$this->tell_by_mail($subject,$this->p->ipn_data['payer_email'],$to,$body);
		 		 
				$this->savelog("PAYPAL IPN:SUCCESS");
				die();
			}
			else 
				$this->savelog("PAYPAL IPN:FAILED");	  
			break;
		}
	}
	
    function get_paypal_posts() {
   
     foreach ($_POST as $key => $value) { 
	   echo "$key: $value<br>"; 
	   
	   $this->paypal_post[$key] = $value;
	 }   
    }

    function savelog($data) {
   
     $newdata = date("F j, Y, g:i a") . " " . $data;
   
     $actfile = $this->admin_path . "/paypal.txt";							
	 
     if ($fp = @fopen ($actfile , 'a+')) {
                 fwrite ($fp, $newdata."\n");
                 fclose ($fp);
     }
     else 
		 echo "File creation error!";
        
    } 

   function callback() {
   
        if (empty($this->paypal_post))
			return false;
   
        //else proceed...
	    /*$product_id = $this->paypal_post['item_name'];
	    $link = seturl("t=pickit&g=$product_id",$product_id); 
	    $w = new window("Download",$link);
	    $ret .= $w->render();
	    unset($w);	*/
		
		return true;   
   }	

}

/*******************************************************************************
 *                      PHP Paypal IPN Integration Class
 *******************************************************************************
 *      Author:     Micah Carrick
 *      Email:      email@micahcarrick.com
 *      Website:    http://www.micahcarrick.com
 *
 *      File:       paypal.class.php
 *      Version:    1.00
 *      Copyright:  (c) 2005 - Micah Carrick 
 *                  You are free to use, distribute, and modify this software 
 *                  under the terms of the GNU General Public License.  See the
 *                  included license.txt file.
 *      
 *******************************************************************************
 *  VERION HISTORY:
 *  
 *      v1.0.0 [04.16.2005] - Initial Version
 *
 *******************************************************************************
 *  DESCRIPTION:
 *
 *      This file provides a neat and simple method to interface with paypal and
 *      The paypal Instant Payment Notification (IPN) interface.  This file is
 *      NOT intended to make the paypal integration "plug 'n' play". It still
 *      requires the developer (that should be you) to understand the paypal
 *      process and know the variables you want/need to pass to paypal to
 *      achieve what you want.  
 *
 *      This class handles the submission of an order to paypal aswell as the
 *      processing an Instant Payment Notification.
 *  
 *      This code is based on that of the php-toolkit from paypal.  I've taken
 *      the basic principals and put it in to a class so that it is a little
 *      easier--at least for me--to use.  The php-toolkit can be downloaded from
 *      http://sourceforge.net/projects/paypal.
 *      
 *      To submit an order to paypal, have your order form POST to a file with:
 *
 *          $p = new paypal_class;
 *          $p->add_field('business', 'somebody@domain.com');
 *          $p->add_field('first_name', $_POST['first_name']);
 *          ... (add all your fields in the same manor)
 *          $p->submit_paypal_post();
 *
 *      To process an IPN, have your IPN processing file contain:
 *
 *          $p = new paypal_class;
 *          if ($p->validate_ipn()) {
 *          ... (IPN is verified.  Details are in the ipn_data() array)
 *          }
 *
 *
 *      In case you are new to paypal, here is some information to help you:
 *
 *      1. Download and read the Merchant User Manual and Integration Guide from
 *         http://www.paypal.com/en_US/pdf/integration_guide.pdf.  This gives 
 *         you all the information you need including the fields you can pass to
 *         paypal (using add_field() with this class) aswell as all the fields
 *         that are returned in an IPN post (stored in the ipn_data() array in
 *         this class).  It also diagrams the entire transaction process.
 *
 *      2. Create a "sandbox" account for a buyer and a seller.  This is just
 *         a test account(s) that allow you to test your site from both the 
 *         seller and buyer perspective.  The instructions for this is available
 *         at https://developer.paypal.com/ as well as a great forum where you
 *         can ask all your paypal integration questions.  Make sure you follow
 *         all the directions in setting up a sandbox test environment, including
 *         the addition of fake bank accounts and credit cards.
 * 
 *******************************************************************************
*/

class paypal_class {
    
   var $last_error;                 // holds the last error encountered
   
   var $ipn_log;                    // bool: log IPN results to text file?
   var $ipn_log_file;               // filename of the IPN log
   var $ipn_response;               // holds the IPN response from paypal   
   var $ipn_data = array();         // array contains the POST values for IPN
   
   var $fields = array();           // array holds the fields to submit to paypal

   
   function paypal_class() {
       
      // initialization constructor.  Called when class is created.
      
      $this->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
      
      $this->last_error = '';
      
      $this->ipn_log_file = paramload('SHELL','prpath') . 'ipn_log.txt';
      $this->ipn_log = true;
      $this->ipn_response = '';
      
      // populate $fields array with a few default values.  See the paypal
      // documentation for a list of fields and their data types. These defaul
      // values can be overwritten by the calling script.

      $this->add_field('rm','2');           // Return method = POST
      $this->add_field('cmd','_xclick'); 
      
   }
   
   function add_field($field, $value) {
      
      // adds a key=>value pair to the fields array, which is what will be 
      // sent to paypal as POST variables.  If the value is already in the 
      // array, it will be overwritten.
      
      $this->fields["$field"] = $value;
   }

   function submit_paypal_post() {
 
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

      echo "<html>\n";
      echo "<head><title>Processing Payment...</title></head>\n";
      echo "<body onLoad=\"document.form.submit();\">\n";
      echo "<center><h3>Please wait, your order is being processed...</h3></center>\n";
      echo "<form method=\"post\" name=\"form\" action=\"".$this->paypal_url."\">\n";

      foreach ($this->fields as $name => $value) {
         echo "<input type=\"hidden\" name=\"$name\" value=\"$value\">";
      }
 
      echo "</form>\n";
      echo "</body></html>\n";
    
   }
   
   function validate_ipn() {

      // parse the paypal URL
      $url_parsed=parse_url($this->paypal_url);        

      // generate the post string from the _POST vars aswell as load the
      // _POST vars into an arry so we can play with them from the calling
      // script.
      $post_string = '';    
      foreach ($_POST as $field=>$value) { 
         $this->ipn_data["$field"] = $value;
         $post_string .= $field.'='.urlencode($value).'&'; 
      }
      $post_string.="cmd=_notify-validate"; // append ipn command

      // open the connection to paypal
      $fp = fsockopen($url_parsed[host],"80",$err_num,$err_str,30); 
      if(!$fp) {
          
         // could not open the connection.  If loggin is on, the error message
         // will be in the log.
         $this->last_error = "fsockopen error no. $errnum: $errstr";
         $this->log_ipn_results(false);       
         return false;
         
      } else { 
 
         // Post the data back to paypal
         fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n"); 
         fputs($fp, "Host: $url_parsed[host]\r\n"); 
         fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
         fputs($fp, "Content-length: ".strlen($post_string)."\r\n"); 
         fputs($fp, "Connection: close\r\n\r\n"); 
         fputs($fp, $post_string . "\r\n\r\n"); 

         // loop through the response from the server and append to variable
         while(!feof($fp)) { 
            $this->ipn_response .= fgets($fp, 1024); 
         } 

         fclose($fp); // close connection

      }
      
      if (eregi("VERIFIED",$this->ipn_response)) {
  
         // Valid IPN transaction.
         $this->log_ipn_results(true);
         return true;       
         
      } else {
  
         // Invalid IPN transaction.  Check the log for details.
         $this->last_error = 'IPN Validation Failed.';
         $this->log_ipn_results(false);   
         return false;
         
      }
      
   }
   
   function log_ipn_results($success) {
       
      if (!$this->ipn_log) return;  // is logging turned off?
      
      // Timestamp
      $text = '['.date('m/d/Y g:i A').'] - '; 
      
      // Success or failure being logged?
      if ($success) $text .= "SUCCESS!\n";
      else $text .= 'FAIL: '.$this->last_error."\n";
      
      // Log the POST variables
      $text .= "IPN POST Vars from Paypal:\n";
      foreach ($this->ipn_data as $key=>$value) {
         $text .= "$key=$value, ";
      }
 
      // Log the response from the paypal server
      $text .= "\nIPN Response from Paypal Server:\n ".$this->ipn_response;
      
      // Write to log
      $fp=fopen($this->ipn_log_file,'a');
      fwrite($fp, $text . "\n\n"); 

      fclose($fp);  // close file
   }

   function dump_fields() {
 
      // Used for debugging, this function will output all the field/value pairs
      // that are currently defined in the instance of the class using the
      // add_field() function.
      
      echo "<h3>paypal_class->dump_fields() Output:</h3>";
      echo "<table width=\"95%\" border=\"1\" cellpadding=\"2\" cellspacing=\"0\">
            <tr>
               <td bgcolor=\"black\"><b><font color=\"white\">Field Name</font></b></td>
               <td bgcolor=\"black\"><b><font color=\"white\">Value</font></b></td>
            </tr>"; 
      
      ksort($this->fields);
      foreach ($this->fields as $key => $value) {
         echo "<tr><td>$key</td><td>".urldecode($value)."&nbsp;</td></tr>";
      }
 
      echo "</table><br>"; 
   }
}         
<?php

require_once('skeleton.php');

class sslencode extends skeleton {
 
 protected $sslpath, $printer_name;
 protected $ssl_priv_key;
 
 public function __construct($user,$data=null, $job_id=null, $job_file=null, $job_attr=null, $printer_name=null) {
    parent::__construct();
	
    //$this->import_data = $import_data;
	$this->jid = $job_id;
	$this->jf = $job_file;
	$this->jattr = (array) $job_attr;
	
	$this->printer_name = $printer_name;
	
	$this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/'.$this->printer_name;		
  
    if (is_object($auth)) {
	   self::write2disk('sslencode.log','yes');
	}
	
	$this->sslpath = $this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/printers/ssl/';
	$this->ssl_private_key = $this->sslpath . $this->printer_name .'.prk';
	$this->ssl_public_key = $this->sslpath . $this->printer_name .'.puk';
	
	if (is_readable($this->ssl_private_key)) {
	  $privateKey = file_get_contents($this->ssl_private_key);
	}
	else {
	  if (function_exists('openssl_pkey_new')) {
	    // generate a 1024 bit rsa private key, returns a php resource, save to file
        $privateKey = openssl_pkey_new(array(
	      'private_key_bits' => 1024,
	      'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ));
        openssl_pkey_export_to_file($privateKey, $this->ssl_private_key, '1234567890');
      }
	}	
	
	if (($privateKey) && (!is_readable($this->ssl_public_key))) {
        // get the public key $keyDetails['key'] from the private key;
        $keyDetails = openssl_pkey_get_details($privateKey);
		if (is_array($keyDetails))
          file_put_contents($this->ssl_public_key, $keyDetails['key']);
    }	 
 }
 
 //override
 public function execute() {
 
    $this->export_data = null;
    //return true;
    //return $this->import_data;
 
    if (is_readable($this->ssl_public_key)) {
	
	  if (function_exists('openssl_pkey_get_public')) {

        //$pubKey = openssl_pkey_get_public('file://'.$this->ssl_public_key);
		$priKey = openssl_pkey_get_private('file://'.$this->ssl_private_key, '1234567890');
		//if ($pubKey) {
		if ($priKey) {
		  //return (':'.$pubKey);
          //if ($ret = openssl_public_encrypt($this->import_data, $this->export_data, $pubKey)) {
		  if ($ret = openssl_private_encrypt($this->import_data, $this->export_data, $priKey)) {
		    //return ($this->export_data);
			return ($ret);
          }		  
		  else
		    return false;
		}  
		else
          return false;		
	  }
	  
	  //....decrypt
      // retrieve $encryptedData from storage ...
 
      // load the private key and decrypt the encrypted data
      //$privateKey = openssl_pkey_get_private('file:///path/to/privatekey', $passphrase);
      //openssl_private_decrypt($encryptedData, $sensitiveData, $privateKey);	  
	   	  
	} 
    return false;	
 }
}


?>
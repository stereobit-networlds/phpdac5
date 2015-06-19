<?php

require_once('skeleton.php');
//require_once('dropbox/DropboxUploader.php');


if ($_GET['test']) {
spl_autoload_register(function($class){
	$class = str_replace('\\', '/', $class);
	require_once($class . '.php');
});
}

/*else {
require_once('Dropbox/OAuth/Storage/Encrypter.php');
require_once('Dropbox/OAuth/Storage/Session.php');
require_once('Dropbox/OAuth/Consumer/Curl.php');
require_once('Dropbox/API.php');
require_once('Dropbox/Exception.php');
}*/

class dboxsave extends skeleton {

 protected $key, $secret;
 
 public function __construct($user,$data=null, $job_id=null, $job_file=null, $job_attr=null, $printer_name=null) {
 
    parent::__construct($user,$data,$job_id,$job_file,$job_attr,$printer_name);
	
    //$this->import_data = $import_data;
	$this->jid = $job_id;
	$this->jf = $job_file;
	$this->jattr = (array) $job_attr;
	
	$this->jowner = $user;
	
	$this->printer_name = $printer_name;
	
	$this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/'.$this->printer_name;	
	$this->admin_path = $_SERVER['DOCUMENT_ROOT'] .'/admin/'.$this->printer_name;
  

	self::write2disk('dboxsave.log',date(DATE_RFC822)."\r\n");

	//self::write2disk($this->jobs_path.'/job'.$job_id.'.dropbox',"a\r\n");

    $this->key = "geuq6gm2b5glofq";
	$this->secret = "5s9jvk2zd5oc0hq";	
 }
 
 function loader($class){
	$class = str_replace('\\', '/', $class);
	require_once($class . '.php');
 } 
 
 public function execute($debug_mode=false) {
    //$uid = $_GET['uid']; //when allow app callback return
	//$oauth_token = $_GET['oauth_token']; //when allow app callback return
    //if ($debug_mode)	
	  //  session_start();	

	//spl_autoload_register(array($this, 'loader')); 
	
    //$test_jf = $_SERVER['DOCUMENT_ROOT'] .'/jobs/' . 'test_dropbox2.txt';
    //self::write2disk('dboxsave.log',">".$this->jf.':'.$test_jf."\r\n");
    //return true;		
	
    try {
        // Check whether to use HTTPS and set the callback URL
        $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
        $callback = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];	
	
	    // Instantiate the Encrypter and storage objects
        // $key is a 32-byte encryption key (secret)
        $key = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
        $encrypter = new \Dropbox\OAuth\Storage\Encrypter($key);

        // User ID assigned by your auth system (used by persistent storage handlers)
		if ($u = $_GET['userid']) //only in test manual mode
          $userID = $u;//$_GET['userid'];		
		else 
          $userID = $this->jowner;//$_SESSION['user'] ? $_SESSION['user'] : $this->jowner;//'anonymoys';//?? when printer ?
		  
        // Create the storage object, passing it the Encrypter object
        //$storage = new \Dropbox\OAuth\Storage\Session($encrypter); 
		$storage = new \Dropbox\OAuth\Storage\Filesystem($encrypter, $userID);//Session;//Filesystem
		/*if ($debug_mode)
		  $storage->setDirectory(getcwd() . '/Dropbox'); 
		else*/
	    $storage->setDirectory($this->admin_path);
 
        $OAuth = new \Dropbox\OAuth\Consumer\Curl($this->key, $this->secret, $storage, $callback);
        $dropbox = new \Dropbox\API($OAuth);
 
        if ($debug_mode)
		  echo "<br>DBOXSAVE FILENAME ($userID):". $this->jf ."<br>";
		else
          self::write2disk('dboxsave.log',"FILE ($userID):".$this->jf."\r\n");
	    //return true;
	
        // Upload the file with an alternative filename
        $put = $dropbox->putFile($this->jf,/*'api_upload_test.txt'*/null,null,true); //alt name,path,override
	    //$put = $dropbox->putFile($test_jf,null,null,true); //alt name,path,override
	
    } 
	catch(\Dropbox\Exception $e) {
	    //echo $e->getMessage() . PHP_EOL;
	    //exit('Setup failed! Please try running setup again.');
	    if ($debug_mode)
		  echo $e->getMessage();
		else
	      self::write2disk('dboxsave.log',"ERROR:".$e->getMessage() . PHP_EOL);
    }	
	
    self::write2disk('dboxsave.log',"PUT:$put\r\n");
	
	//$bytes = self::_write($response);
	//return ($bytes);	  


	if ($put)
	  return true; 	

	  
    return false;	
 }
 
 function setup($authorize=false) {
    
    try {
	  // Set up the OAuth consumer
	  $storage = new \Dropbox\OAuth\Storage\Filesystem;//Session;//Filesystem
	  $storage->setDirectory(getcwd() . '/Dropbox');//filesystem
	  
	  $OAuth = new \Dropbox\OAuth\Consumer\Curl($this->key, $this->secret, $storage);
	
	  if ($authorize) {
	    // Generate the authorisation URL and prompt user
	    echo "Generating Authorisation URL...\r\n\r\n";
	    echo "===== Begin Authorisation URL =====\r\n";
	    echo $OAuth->getAuthoriseUrl() . PHP_EOL;
	    echo "===== End Authorisation URL =====\r\n\r\n";
	    echo "Visit the URL above and allow the SDK to connect to your account\r\n";
	    echo "Press any key once you have completed this step...";
	    //fgets(STDIN);
	  }
	  else {
	    // Acquire the access token
	    echo "Acquiring access token...\r\n";
	
	    $OAuth->getAccessToken();
	    $token = serialize(array(
		'token' => $storage->get('access_token'),
		'consumerKey' => $this->key,
		'consumerSecret' => $this->secret,
	    ));
	
	    // Write the access token to disk
	    if(@file_put_contents(getcwd() . '/Dropbox/oauth.token', $token) === false){
		  throw new \Dropbox\Exception('Unable to write token to file');
	    } 
	    else {
		  exit('Setup complete! You can now run the test suite.');
	    }
	  }//else
    } 
	catch(\Dropbox\Exception $e) {
	  echo $e->getMessage() . PHP_EOL;
	  exit('Setup failed! Please try running setup again.');
    } 
 }
 
 //override
 /*public function execute() {
 
 
    //send the job file to dropbox
 
	$myfile = $this->jf . '.pdf';
	
 
    // Upload
    $uploader = new DropboxUploader('balexiou@stereobit.com', 'basilvk7dp');
    $ret = $uploader->upload($this->jf, '/printQueue', $myfile);
	
    //self::write2disk($this->jobs_path.'/job'.$this->jid.'.dropbox',"b\r\n");
	
	if ($ret)
	  return true; 
	  //return ($this->import_data); //as is
	
    return false;	
 }*/
}


/********************************************************************************/
//test
if ($_GET['test']) {

$test_file = getcwd() . '/test.txt';
$my_test_file = getcwd() . '/test'.$_GET['userid'].'.txt';
$test_attr = array('job-name'=>'text'.$_GET['userid'].'.txt');

$c = copy($test_file,$my_test_file);
//echo 'test_file:', $test_file,'<br>';

if (($c) && ($fp = fopen($my_test_file, "r+b"))) { 

  $auth_obj = new StdClass();//dummy
  
  $test_dropbox = new dboxsave($auth_obj,$fp,1,$my_test_file,$test_attr,'test.printer');

  if ($_GET['setup']) {
    //SETUP APP
    $ret = $test_dropbox->setup($_GET['authorize']);
  }
  else {
    //echo 'my_test_file:', $my_test_file,'<br>';//no when redirect to dropbox allow procedure..
    echo $test_dropbox->execute(true);
  
  }
  //echo 'result:',$ret,'<br>';
  
  //read data
  //$this->fp = $fp;
  //$this->import_data = fread($this->fp, filesize($job_file)); 
  
  fclose($fp);
}
else
  echo 'file is not readable<br>';
}  
/****************************************************************************/
?>
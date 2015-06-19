<?php

require_once('skeleton.php');

/*
http://www.ocrwebservice.com/Default.aspx
*/


class ocrscan extends skeleton {
 
 protected $client;
 public $fp;
	
 public function __construct($user,$data=null, $job_id=null, $job_file=null, $job_attr=null, $printer_name=null) {
 
    parent::__construct($user,$data,$job_id,$job_file,$job_attr,$printer_name);
	
    //$this->import_data = $import_data;
	$this->jid = $job_id;
	$this->jf = $job_file;
	$this->jattr = (array) $job_attr;
	
	$this->printer_name = $printer_name;
	
	$this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/'.$this->printer_name;	
	
	//$this->fp = $fp;

    // Turn off WSDL caching
    ini_set ('soap.wsdl_cache_enabled', 0);
     
    $this->client = new SoapClient("http://www.ocrwebservice.com/services/OCRWebService.asmx?WSDL"
                             , array("trace"=>1, "exceptions"=>1)

                            );
								 							
 
    self::write2disk('ocrscan.log',"INIT\r\n");	
 }
 
 //override
 public function execute($debug_mode=false, $test=false) {
 	
	if ($bytes=self::ocr($debug_mode,$test))
	  return ($bytes);
	
    return false;	
 }
 

 protected function ocr($debug_mode=false, $test=false) {
 
    $params = new StdClass();
    $params->user_name = "balexiou";//"<username>";
    $params->license_code = "381CE5F0-4074-4A64-B593-A28EBD1AB968";//"<license_code>";
		 
 
    $inimage = new StdClass();
 
    if ($debug_mode) 
      $out = '<br>JOB NAME:'.$this->jf;
	else  
	  self::write2disk('ocrscan.log',$this->jf."\r\n");	
	
    $inimage->fileName = $this->jattr['job-name'];//$filename;//"sample_image.jpg";
	
	if ($debug_mode)  
	  $out .= '<br>SET NAME:'. $inimage->fileName;
	else  
	  self::write2disk('ocrscan.log',$inimage->fileName."\r\n");
	
    if ($test)	{//web-test
	  echo $this->jf,'<br>';
	  $my_file = $this->jf;// . '.jpg';
      $handle = fopen($my_file ,'r+b');//"C:\\sample_image.jpg", 'r');
      $data_image = fread($handle, filesize($my_file));//"C:\\sample_image.jpg"));
      fclose($handle);	
	
      $inimage->fileData = $data_image; 
	}  
	else
      $inimage->fileData = self::_read();//$data_image;//$this->import_data;
	

    $params->OCRWSInputImage = $inimage;	
	
    $settings = new StdClass();
    $settings->ocrLanguages = array("ENGLISH", "GREEK");
    $settings->outputDocumentFormat  = "TXT";
    $settings->convertToBW = FALSE;
    $settings->getOCRText = TRUE;
    $settings->createOutputDocument = FALSE;
    $settings->multiPageDoc = FALSE;
    $settings->ocrWords = FALSE;

    $params->OCRWSSetting = $settings;

	
	$ocrdata = null;
    $bytes = 0;	
	
    try 
    {
        $result = (object) $this->client->OCRWebServiceRecognize($params);		
	
    }  
    catch (SoapFault $fault) 
    {
        //print($client->__getLastRequest());
        //print($client->__getLastRequestHeaders());
		$lreq = $this->client->__getLastRequest();
        $lreq_headers = $this->client->__getLastRequestHeaders();
		
		if ($debug_mode) 
		  echo '<br>ERROR:',$lreq."<br>",$lreq_headers,"--------<br>";
		else 
		  self::write2disk('ocrscan.log',$lreq."\r\n".$lreq_headers."--------\r\n");
    }
	
	/*$ocrdata = isset($result->OCRWSResponse->errorMessage) ?
                      $result->OCRWSResponse->errorMessage  :    		
	                 $result->OCRWSResponse->ocrText; //????*/
	
	if (!$ocrdata = $result->OCRWSResponse->errorMessage) {
		    
			//the output document
			if ($filedata = $result->OCRWSResponse->fileData) {
               //get filename
			   $filename = $this->jobs_path .'/' . $result->OCRWSResponse->fileName;
			   //save...			   
               $handle = fopen($filename ,'w');
               $data_image = fwrite($handle, strlen($filedata));
               fclose($handle);			   
			}
			
			//save the ocr text
			/*for ($z=0; $z<count($result->OCRWSResponse->ocrText); $z++) {
			
			    for ($p=0; $p<count($result->OCRWSResponse->ocrText[$z]); $p++)
				     $ocrdata .= $result->OCRWSResponse->ocrText[$z][$p];
			}*/
			if ($string = $result->OCRWSResponse->ocrText->ArrayOfString->string) {
			    //foreach ($result->OCRWSResponse->ocrText->ArrayOfString as $i=>$s)
			      //  foreach ($s as $string)
			            $ocrdata .= $string;//$result->OCRWSResponse->ocrText;
			}
            else
                $ocrdata = "Empty"; 			
	} 	
	
	
	//$bytes = self::_write($ocrdata);	
	
	//UTF-8 encode
    //iconv("ANSI", "UTF-8", $ocrdata); 
	//iconv("Windows-1252", "UTF-8", $ocrdata);
    //iconv("ISO-8859-7", "UTF-8",$ocrdata));		
	$bytes = self::_writeutf8($ocrdata);
	
	if ($test) { //web test
	    //echo '<br>';
        //var_dump($result);
		print_r($result);
        print("Done");	
	}
    elseif ($debug_mode) {	
	    $out .= var_export($result, true);
	    //echo "<br>is-object:", is_object($result);
		$out .= "<br>errorMessage:".$result->OCRWSResponse->errorMessage;
		$out .= "<br>ocrText:".$result->OCRWSResponse->ocrdata;
		$out .= "<br>ocrBytes:".$bytes;
	}
	else {
	    //self::write2disk('ocrscan.log',"ocrText:".$result->OCRWSResponse->ocrText."\r\n");
		self::write2disk('ocrscan.log',"ocrBytes:".$bytes."\r\n");
		self::write2disk('ocrscan.log',"fileName:".$result->OCRWSResponse->fileName."\r\n");
        //self::write2disk('ocrscan.log',"fileData".$result->OCRWSResponse->fileData."\r\n");		
		self::write2disk('ocrscan.log',"errorMessage:".$result->OCRWSResponse->errorMessage."\r\n");
	}	
	
	//if false try to re-send to web service..excceed daily limit
	if ($debug_mode)
	  return ($out); 
	else   
	  return ($bytes ? $bytes : true); //error..complete job request..not retry..
	
 }
 
 function writeUTF8File($filename,$content) { 
        $f=fopen($filename,"w"); 
        # Now UTF-8 - Add byte order mark 
        fwrite($f, pack("CCC",0xef,0xbb,0xbf)); 
        fwrite($f,$content); 
        fclose($f); 
 } 
 
}

/********************************************************************************/
//test
if ((!empty($_GET)) && ($test = $_GET['test'])) {
$test_file = getcwd() . '/text.jpg';
$test_attr = array('job-name'=>'text.jpg');
//echo 'test_file:', $test_file,'<br>';
if ($fp = fopen($test_file, "r+b")) {  

  $auth_obj = new StdClass();//dummy
  $test_ocr = new ocrscan($auth_obj,$fp,1,$test_file,$test_attr,'test.printer');

  $ret = $test_ocr->execute($test, true);
  
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
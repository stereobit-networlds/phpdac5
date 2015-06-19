<?php

require_once('skeleton.php');

//http://ps2pdf.com/convert.cgi
//http://www.stuffedcow.net/ps2pdf ..http://www.stuffedcow.net/images/ps2pdf/convert.php

class ps2pdf extends skeleton {
 
 public function __construct($user,$data=null, $job_id=null, $job_file=null, $job_attr=null, $printer_name=null) {
 
    parent::__construct($user,$data,$job_id,$job_file,$job_attr,$printer_name);
    //$this->import_data = $import_data;
	
	$this->jid = $job_id;
	$this->jf = $job_file;
	$this->jattr = (array) $job_attr;	

	self::write2disk('ps2pdf.log','yes');

 }
 
 //override
 public function execute() {

    $ret = $this->ps2pdf_2();
 
    //$ret = $this->ps2pdf_1();
	return ($ret);
 } 
 
 public function ps2pdf_2() {
 
    //$ret = "";
	//$ret .= $this->jf."\r\n";
 
    //if (substr($this->import_data,0,4)=='%!PS') {//postscript 
	
	
	  $ch = curl_init();
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
      curl_setopt($ch, CURLOPT_URL, "http://www.stuffedcow.net/images/ps2pdf/convert.php");
      curl_setopt($ch, CURLOPT_POST, true);
      // same as <input type="file" name="file_box">
      $post = array(
        "source"=>'@'.$this->jf
		//,"name"=>'source'
      );
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
      $response = curl_exec($ch);
	  
	  /*if ($this->export_data = $response)
	     return $this->export_data;
	  else
         return false; 	 */
	
	  //TEST..................
      if ($_GET['test']) { 
	     //echo '>',$this->jf;
	     header("Content-type: application/pdf");
	     echo $response;
	  }	 
	  
	  $bytes = self::_write($response);
	  return ($bytes);
	//}
    
	//return false;	
 } 
 
 public function ps2pdf_1() {
 
 
    //return $this->import_data;
 
    $ret = "";
	$ret .= $this->jf."\r\n";
 
    if (substr($this->import_data,0,4)=='%!PS') {//postscript
	
	  if (!function_exists('curl_init')) {
        $this->export_data = $ret . ' is postscript';
	    return $this->export_data;	  
	  }
      //do the translation ...	  
	  //...

	  //.....................using curl
	  $ch = curl_init();
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
      curl_setopt($ch, CURLOPT_URL, "http://ps2pdf.com/convert.cgi");
      curl_setopt($ch, CURLOPT_POST, true);
      // same as <input type="file" name="file_box">
      $post = array(
        "inputfile"=>'@'.$this->jf
      );
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
      $response = curl_exec($ch);
	  
	  if ($this->export_data = $response)
	     return $this->export_data;
	  else
         return false; 	  
	  
	  //....................using http client..error from 2nd http header set...
	  set_time_limit(0);
	  
	  $http=new http_client;
	  $http->timeout=0;
	  $http->data_timeout=0;
	  $http->debug=0;
	  $http->html_debug=1;	  
	  
	  $url="http://ps2pdf.com/convert.cgi";
	  $error=$http->GetRequestArguments($url,$arguments);
	  $arguments["RequestMethod"]="POST";
	  /*$arguments["PostValues"]=array(
		"somefield"=>"Upload forms",
		"MAX_FILE_SIZE"=>"1000000"
	  );*/
	  $arguments["PostFiles"]=array(
		"inputfile"=>array(
			"filename"=>$this->jf,
			"Name"=>"inputfile",
			"Content-Type"=>"automatic/name",
		)/*,
		"anotherfile"=>array(
			"FileName"=>"test_http_post.php",
			"Content-Type"=>"automatic/name",
		)*/
	  );
	  $arguments["Referer"]="http://www.alltheweb.com/";
	  $ret .= "<H2><LI>Opening connection to:</H2>\n<PRE>".HtmlEntities($arguments["HostName"])."</PRE>\n";
	  flush();
	  $error=$http->Open($arguments);	  
	  
	  if($error=="")
	  {
		$error=$http->SendRequest($arguments);
		if($error=="")
		{
			$ret .= "<H2><LI>Request:</LI</H2>\n<PRE>\n".HtmlEntities($http->request)."</PRE>\n";
			$ret .= "<H2><LI>Request headers:</LI</H2>\n<PRE>\n";
			for(Reset($http->request_headers),$header=0;$header<count($http->request_headers);Next($http->request_headers),$header++)
			{
				$header_name=Key($http->request_headers);
				if(GetType($http->request_headers[$header_name])=="array")
				{
					for($header_value=0;$header_value<count($http->request_headers[$header_name]);$header_value++)
						$ret .= $header_name.": ".$http->request_headers[$header_name][$header_value]."\r\n";
				}
				else
					$ret .= $header_name.": ".$http->request_headers[$header_name]."\r\n";
			}
			$ret .= "</PRE>\n";
			$ret .= "<H2><LI>Request body:</LI</H2>\n<PRE>\n".HtmlEntities($http->request_body)."</PRE>\n";
			flush();

			$headers=array();
			$error=$http->ReadReplyHeaders($headers);
			if($error=="")
			{
				$ret .= "<H2><LI>Response headers:</LI</H2>\n<PRE>\n";
				for(Reset($headers),$header=0;$header<count($headers);Next($headers),$header++)
				{
					$header_name=Key($headers);
					if(GetType($headers[$header_name])=="array")
					{
						for($header_value=0;$header_value<count($headers[$header_name]);$header_value++)
							$ret .= $header_name.": ".$headers[$header_name][$header_value]."\r\n";
					}
					else
						$ret .= $header_name.": ".$headers[$header_name]."\r\n";
				}
				$ret .= "</PRE>\n";
				flush();

				$ret .= "<H2><LI>Response body:</LI</H2>\n<PRE>\n";
				for(;;)
				{
					$error=$http->ReadReplyBody($body,1000);
					if($error!=""
					|| strlen($body)==0)
						break;
					$ret .= HtmlSpecialChars($body);
				}
				$ret .= "</PRE>\n";
				flush();
			}
		}
		$http->Close();
	  }
	  if(strlen($error))
		$ret .= "<CENTER><H2>Error: ".$error."</H2><CENTER>\n";	  
	  
	  //$this->export_data = $ret;
	  //return $this->export_data;
	  $bytes = self::_write($ret);
	  return ($bytes);
	}
    
	return false;
 }
}

/********************************************************************************/
//test
if ($_GET['test']) {
$test_file = getcwd() . '/test.ps';
//echo 'test_file:', $test_file,'<br>';
if ($fp = fopen($test_file, "r+b")) {  

  $auth_obj = new StdClass();//dummy
  $testbed = new ps2pdf($auth_obj,$fp,1,$test_file,null,'test.printer');
  $ret = $testbed->execute();
  
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
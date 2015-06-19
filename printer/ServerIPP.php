<?php
/* 
 * Class ServerIPP - Receive Basic IPP requests, Get and parses IPP requests.
 *
 *   Copyright (C) 2012  Alexiou Vassilis
 *   Parts Copyright (C) 2005-2006 Thomas Harding, Manuel Lemos
 *
 *   This library is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU Library General Public
 *   License as published by the Free Software Foundation; either
 *   version 2 of the License, or (at your option) any later version.
 *
 *   This library is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *   Library General Public License for more details.
 *
 *   You should have received a copy of the GNU Library General Public
 *   License along with this library; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *   mailto:balexiou@stereobit.com
 *   stereobit.networlds, 27 Allatini st., 54 250 THESSALONIKI -- HELLAS
 *
 */    
/*

    This class is intended to implement Internet Printing Protocol on -SERVER- side.

    References needed to debug / add functionnalities:
        - RFC 2910
        - RFC 2911
        - RFC 3380
        - RFC 3382
*/


            
require_once("BasicIPP.php");
//status codes RFC2566 13.1
define ('successful-ok',chr(0)); //0x0000
define ('successful-ok-ignored-or-substituted-attributes',chr(1)); //0x0001
define ('successful-ok-conflicting-attributes',chr(2)); //0x0002  
//client error codes RFC2566 13.1.4   
//1.0       
define ('client-error-bad-request',chr(0x04) . chr(0x00)); //0x0400
define ('client-error-bad-forbidden',chr(0x04) . chr(0x01)); //0x0401
define ('client-error-not-authenticated',chr(0x04) . chr(0x02)); //0x0402
define ('client-error-not-authorized',chr(0x04) . chr(0x03)); //0x0403
define ('client-error-not-possible',chr(0x04) . chr(0x04)); //0x0404
define ('client-error-timeout',chr(0x04) . chr(0x05)); //0x0405
define ('client-error-not-found',chr(0x04) . chr(0x06)); //0x0406
define ('client-error-gone',chr(0x04) . chr(0x07)); //0x0407
define ('client-error-request-entity-too-large',chr(0x04) . chr(0x08)); //0x0408
define ('client-error-request-value-too-long',chr(0x04) . chr(0x09)); //0x0409
define ('client-error-document-format-not-supported',chr(0x04) . chr(0x0A)); //0x040A
define ('client-error-attributes-or-values-not-supported',chr(0x04) . chr(0x0B)); //0x040B
define ('client-error-uri-scheme-not-supported',chr(0x04) . chr(0x0C)); //0x040C
define ('client-error-charset-not-supported',chr(0x04) . chr(0x0D)); //0x040D
define ('client-error-conflicting-attributes',chr(0x04) . chr(0x0E)); //0x040E
//1.1
define ('client-error-compression-not-supported',chr(0x04) . chr(0x0F)); //0x040F
define ('client-error-compression-error',chr(0x04) . chr(0x10)); //0x0410
define ('client-error-document-format-error',chr(0x04) . chr(0x11)); //0x0411
define ('client-error-document-access-error',chr(0x04) . chr(0x12)); //0x0412
//server error codes RFC2566 13.1.5
//1.0
define ('server-error-internal-error',chr(0x05) . chr(0x00)); //0x0500
define ('server-error-operation-not-supported',chr(0x05) . chr(0x01)); //0x0501
define ('server-error-service-unavailable',chr(0x05) . chr(0x02)); //0x0502
define ('server-error-version-not-supported',chr(0x05) . chr(0x03)); //0x0503
define ('server-error-device-error',chr(0x05) . chr(0x04)); //0x0504
define ('server-error-temporary-error',chr(0x05) . chr(0x05)); //0x0505
define ('server-error-not-accepting-jobs',chr(0x05) . chr(0x06)); //0x0506
define ('server-error-busy',chr(0x05) . chr(0x07)); //0x0507
define ('server-error-job-canceled',chr(0x05) . chr(0x08)); //0x0508
//1.1
define ('server-error-multiple-document-jobs-not-supported',chr(0x05) . chr(0x09)); //0x0509

//object printer attributes RFC2566 4, 4.4 
define ("printer-uri-supported","ipp://localhost:80/printers/printer1|ipp://localhost:80/printers/printer2|ipp://localhost:80/printers/printer3");//required
define ('printer-security-supported',"none|none|ssl3"); //required
define ('printer-name',"IPPprinter");//required
define ('printer-location',"stereobit");
define ('printer-info',"stereobit.networlds");
define ('printer-more-info',"http://localhost:80/printers/info.php");
define ('printer-driver-installer',"http://localhost:80/printers/install.php");
define ('printer-make-and-model',"Model 1");
define ('printer-more-info-manufacturer',"http://stereobit.com");
define ('printer-state',"idle"); //required
define ('printer-state-reasons',"none"); 
define ('operations-supported',"Print-Job|Print-URI|Validate-Job|Create-Job|Send-Document|Send-URI|Cancel-Job|Get-Job-Attributes|Get-Jobs|Get-Printer-Attributes");//required
define ('charset-configured',"en-us");//required
define ('charset-supported',"us-ascii|iso-8859-1|iso-8859-7|utf-8");//required
define ('natural-language-configured',"en-us");//required
define ('generated-natural-language-supported',"en-us|utf-8");//required
define ('document-format-default',"text/plain");//required
define ('document-format-supported',"text/plain|text/plain,charset=iso-8859-1|text/plain,charset=iso-8859-7|application/postscript|application/vnd.hp-PCL|application/octet-stream");//required
define ('printer-is-accepting-jobs','true');//optional
define ('queued-job-count',0);//recommended
define ('printer-message-from-operator',"Hello"); //optional
define ('color-supported',"true"); //optional
define ('reference-uri-schemes-supported',"http|https|ftp");//optional
define ('pdl-override-supported','not-attempted');//required
define ('printer-up-time',1);//required


define('CRLF', "\r\n");
define('BUFFER_LENGTH', 8192);

/*
//For large files, consider using stream_get_line rather than fgets - it can make a significant difference.

//$ time yes "This is a test line" | head -1000000 | php -r '
//$fp=fopen("php://input","r"); 
//while($line=stream_get_line($fp,65535,"\n")) 
//{ 1; } 
//fclose($fp);'


define('CRLF', "\r\n");
define('BUFFER_LENGTH', 8192);
$headers = '';
$body = '';
$length = 0;

//$fp = fsockopen($host, $port, $errno, $errstr, $timeout);

// get headers FIRST
//--do
while(!feof(STDIN)) {
{
    // use fgets() not fread(), fgets stops reading at first newline
    // or buffer which ever one is reached first
    $data = fgets($fp, BUFFER_LENGTH);
    // a sincle CRLF indicates end of headers
    if ($data === false || $data == CRLF || feof($fp)) {
        // break BEFORE OUTPUT
        break;
    }
    $headers .= $data;
}
//--while (true);
// end of headers

// read from chunked stream
// loop though the stream
//--do
while(!feof(STDIN)) {
{
    // NOTE: for chunked encoding to work properly make sure
    // there is NOTHING (besides newlines) before the first hexlength

    // get the line which has the length of this chunk (use fgets here)
    $line = fgets($fp, BUFFER_LENGTH);

    // if it's only a newline this normally means it's read
    // the total amount of data requested minus the newline
    // continue to next loop to make sure we're done
    if ($line == CRLF) {
        continue;
    }

    // the length of the block is sent in hex decode it then loop through
    // that much data get the length
    // NOTE: hexdec() ignores all non hexadecimal chars it finds
    $length = hexdec($line);

    if (!is_int($length)) {
        trigger_error('Most likely not chunked encoding', E_USER_ERROR);
    }

    // zero is sent when at the end of the chunks
    // or the end of the stream or error
    if ($line === false || $length < 1 || feof($fp)) {
        // break out of the streams loop
        break;
    }

    // loop though the chunk
    do
    {
        // read $length amount of data
        // (use fread here)
        $data = fread($fp, $length);

        // remove the amount received from the total length on the next loop
        // it'll attempt to read that much less data
        $length -= strlen($data);

        // PRINT out directly
        #print $data;
        #flush();
        // you could also save it directly to a file here

        // store in string for later use
        $body .= $data;

        // zero or less or end of connection break
        if ($length <= 0 || feof($fp))
        {
            // break out of the chunk loop
            break;
        }
    }
    while (true);
    // end of chunk loop
}
//--while (true);
// end of stream loop

// $body and $headers should contain your stream data
*/


class ServerIPP extends BasicIPP {

    protected $ipp_version, $ipp_version_x8, $ipp_version_auto;
	protected $server_version;
    protected $clientoutput;
	
    public $client_attributes; // object you can read: all attributes	
	public $client_job_attributes; // object you can read: job attributes
    public $client_operation_attributes; // object you can read: operation attributes
	public $client_printer_attributes; // object you can read: printer attributes
	 
    public function __construct() {
	
        parent::__construct();
		
        self::_initTags(); //extend ipp server tags			
		
		$this->server_version = '1.0';   //IPP server version
		$this->ipp_version = '1.0';      //1.1 or 1.0
		$this->ipp_version_auto = true;  //auto change ipp version depending on ipp client
		
		switch ($this->ipp_version) {
		  case '1.1' : $this->ipp_version_x8 = chr(0x01) . chr(0x01); 
		               break;
		  case '1.0' :
		  default    :
		               $this->ipp_version_x8 = chr(0x01) . chr(0x00);//'1.0';//NOT 1.1 ..WILL NOT WORK IN WINDOWS
		}
		
		$this->clientoutput = new stdClass();		
    }
	
	protected function receiveHttp() {
	
	   set_time_limit(50); //chunk...
	
       //unset($this->clientoutput);
       self::_putDebug(self::_dummy("Processing HTTP response") , 2);
       $this->clientoutput->headers = array(); 	
	   $this->clientoutput->headers = self::get_client_headers();	   
       $this->clientoutput->body = "";	
	   
       //$body = file_get_contents("php://input");
	   $this->clientoutput->body = file_get_contents("php://input");
	   
	   /*
	   $fp=fopen("php://input","r"); //rb?
       //while($line=stream_get_line($fp,65535,"\n")) {//error....
	   while(!feof($fp)) {
          //$body .= $line;	   
		  //$body .= fgets($fp,8192);
		  $this->clientoutput->body .= fread($fp,8192);//fgets
	   } 
       fclose($fp);
	   */
	   //self::_parseClientRequestStream();
	   
	   if (strlen($this->clientoutput->body) == 0) {//break;//exit(0);//
	     return false;
	   }	 
	   /*
       if (strlen($body) == 0) {//break;//exit(0);//
	   
	     //chunked data
	     if (self::c_parseHttpHeaders('Content-Encoding','chunked'))
	       die('chunk data received completely');
	      
	     return false;
		 //..check for end chunked data..length 0
		 //....
		 //php Bug #60826 fastcgi
		 //....
		 //Finally, if you do get this working, beware that you may need to "dechunk" 
		 //(decode the transfer-encoding: chunked body) the request input even though you didn't have to do this before. 
		 //You can use it like so:
		 
         //$fp = fopen('php://input', 'rb');
         //stream_filter_append($fp, 'dechunk', STREAM_FILTER_READ);
         //$HTTP_RAW_POST_DATA = stream_get_contents($fp);
		 
		 //...
		 //http_chunked_decode ..... see php function
	   }   
	   */
	   //chunked data
	   if (self::c_parseHttpHeaders('Content-Encoding','chunked'))
	     die('chunk data received');
       
	   
       //$this->clientoutput->body .= $body;	
       //echo $body;	

	   $ip = $_SERVER['REMOTE_ADDR'];
       self::write2disk('printer.log',"\r\nREQUEST['$ip']:".$this->clientoutput->body."\r\n");
	   
	   self::_parseClientRequest();
	   
	   self::write2disk('ippserver.log',$this->clientoutput->ipp_version.'|'.
	                                    $this->clientoutput->operation_id.'|'.
										$this->clientoutput->request_id."\r\n");
										
	   $cout = var_export($this->clientoutput,1) . "\r\n";
	   self::write2disk('ippserver.log',"\r\n".$cout."\r\n");
	   
	   $cattr = var_export($this->client_attributes,1) . "\r\n";
	   $oattr = var_export($this->client_operation_attributes,1) . "\r\n";
	   $jattr = var_export($this->client_job_attributes,1) . "\r\n";
	   $pattr = var_export($this->client_printer_attributes,1) . "\r\n";
	   self::write2disk('ippserver.log',"\r\n".$cattr.$oattr.$jattr.$pattr."\r\n");	   
	   return true;
	}
	
	protected function get_client_headers() {
	
        if (!function_exists('getallheaders')) {
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                   $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
          }
        }
        else {
		    //apache request headers alia.....
		    $headers = getallheaders();  
        }	

        return $headers;		
	}
	
	protected function support_ipp_version() {
	  
	   if (!$this->clientoutput) 
	     return false;
		 
	   if ($this->ipp_version_auto==true)
	     return true;
		 
	   if (floatval($this->ipp_version) >= floatval($this->clientoutput->ipp_version))
	     return true;
		 
	   return false;	 
	}
	
	protected function build_ipp_version() {	
	
	   if (($this->ipp_version_auto==true) && ($cipp = $this->clientoutput->ipp_version)) 
	     $ver = chr(dechex(substr($cipp,0,1))) . chr(dechex(substr($cipp,2,1)));
	   else 
	     $ver = chr(dechex(substr($this->ipp_version,0,1))) . chr(dechex(substr($this->ipp_version,2,1))); 
	   
	   //$ver = $this->ipp_version_x8;
	   
	   return ($ver);
	}
	
	//used to build job list..........................
	protected function build_ipp_jobs_response($jobs=null) {
	}
	
	protected function build_ipp_response($status_code=null, $operationattributes=null, $jobattributes=null, $printerattributes=null, $unsupported_attributes=null) {
	
	    $status_code = isset($status_code) ?
		               $status_code :
                       chr(0x00) . chr (0x00);	//status-code = succesfull-ok
					   
	    //self::write2disk('status-codes.log',$status_code."\r\n");
			   
        $req_id = $this->clientoutput->request_id ? 
		          self::_integerBuild(intval($this->clientoutput->request_id)) :
				  chr(0x00) . chr (0x00) . chr(0x00) . chr (0x00); //rfc2911 3.1.2
				  
        $ret =  $this->build_ipp_version();//$this->ipp_version_x8; //chr(0x01) . chr(0x00) // 1.0  | version-number
		
        $ret .=  //$this->ipp_version_x8 //chr(0x01) . chr(0x00) // 1.0  | version-number
                $status_code // status-code
              . $req_id // request-id;
			  . chr(0x01)  // start-operation-attributes
              . $this->meta->charset			  
			  . $this->meta->language
              . $operationattributes; //added opp attributes except charset,language=required
			  	  
        $ret.= $jobattributes ?			  
			    chr(0x02)  // start job-attributes	
              . $jobattributes :	
			   null;
			  
		$ret.= $printerattributes ?
                chr(0x04)  // start printer-attributes		
			  . $printerattributes :
			   null;
			
		$ret.= $unsupported_attributes ?
                chr(0x05)  // start unsupported-attributes		
			  . $unsupported_attributes :
			   null;

        $ret .= chr(0x03); // end-of-attributes | end-of-attributes-tag			   

        return ($ret); 			  
	}
	
	//depending on client request
	protected function build_request_attributes(&$unsupported_attributes) {
	  $unsupported_attributes = null;//init
	
	  //self::write2disk('ippserver.log',"\r\n".$this->printer_attributes->requested_attributes->_value0."AAAA\r\n");
	  //return;
	
	  if (!empty($this->printer_attributes->requested_attributes)) {
	    $s = 0;
	    foreach ($this->printer_attributes->requested_attributes as $p=>$pop) {
		
		  //self::write2disk('ippserver.log',"\r\n".$p.'=>'.$pop."\r\n");
		  
		  switch ($p) {
		    case '_type'     : $type = $pop; break;
			case '_range'    : $range = $pop; break;
			case '_value'.$s : $values[] = $pop; $s+=1; break;
			default          :
		  }
		}
        
		if (!empty($values)) {
          foreach ($values as $i=>$v) {
		  
			$val = str_replace('-','_',$v);
					  
            //self::write2disk('ippserver.log',"setAttribute('$v','123');\r\n");		
            
			//if ((!$this->$val) || ($this->setAttribute($v,$this->$val)==false))
			  //self::write2disk('ippserver.log',"unsupported\r\n");
			 
			//unset attribute if exist.... 
			if (($this->$val) && ($this->unsetAttribute($v)==true)) {
			  $this->setAttribute($v,$this->$val);
			  self::write2disk('ippserver.log',"setAttribute($v,".$this->$val.")\r\n");
			}
            else {
			  $unsupported_attributes .= chr(0x10) 
                                      . self::_giveMeStringLength($v) 
	                                  . $v
                                      . chr(0x00) . chr(0x00);									  
              self::write2disk('ippserver.log',"$v is unsupported or value is not defined\r\n");			
			}  
		  }	
		}
        return true;		
	  }
	  else {
	    self::write2disk('ippserver.log',"\r\n..............................\r\n");
		return false;
	  }
	}
		
	//read client request values	
	protected function read_request_attribute($attribute) {
	  $attr = str_replace('-','_',$attribute);//value attr as my-attr
	  
	  $i=0;
	  while (($index = '_value'.$i) && ($value = $this->client_attributes->$attr->$index)) { 
	    $ret[$i] = $value;
	    $i++;
	  }	
	  
	  //$cout = var_export($ret[0],1) . "\r\n";
	  //self::write2disk('read_request_attr.log',$attribute.'='.$cout."\r\n");	  
	  
	  if (is_array($ret)) {
        if (count($ret)>1) //multiple values
          return ($ret); //array of values
	    else
		  return ($ret[0]); //single value
      }
 
      return false; 
	}
	
	//read client job request values	
	protected function read_request_job_attribute($attribute, $job_index=0) {
	  $attr = str_replace('-','_',$attribute);//value attr as my-attr
	  $j_index = 'job_'.$job_index;
	  
	  $i=0;
	  while (($index = '_value'.$i) && ($value = $this->client_job_attributes->$j_index->$attr->$index)) { 
	    $ret[$i] = $value;
	    $i++;
	  }	
	  
	  if (is_array($ret)) {
        if (count($ret)>1) //multiple values
          return ($ret); //array of values
	    else
		  return ($ret[0]); //single value
      }
 
      return false; 
	}	

    protected function setStatusCode($status_code=null)  {

        switch ($status_code) {

            case "successful-ok-ignored-or-substituted-attributes" : $ret = chr(0x00) . chr(0x01);  break;
            case "successful-ok-conflicting-attributes" :  $ret = chr(0x00) . chr(0x02);   break;
            case "client-error-bad-request" :   $ret = chr(0x04) . chr(0x00);   break;
            case "client-error-forbidden" :  $ret = chr(0x04) . chr(0x01);    break;
            case "client-error-not-authenticated" : $ret = chr(0x04) . chr(0x02);  break;
            case "client-error-not-authorized" : $ret = chr(0x04) . chr(0x03);  break;
            case "client-error-not-possible" :  $ret = chr(0x04) . chr(0x04);  break;
            case "client-error-timeout" : $ret = chr(0x04) . chr(0x05);  break;
            case "client-error-not-found" :  $ret = chr(0x04) . chr(0x06);  break;
            case "client-error-gone" :  $ret = chr(0x04) . chr(0x07);  break;
            case "client-error-request-entity-too-large" : $ret = chr(0x04) . chr(0x08);  break;
            case "client-error-request-value-too-long" :  $ret = chr(0x04) . chr(0x09);  break;
            case "client-error-document-format-not-supported" :  $ret = chr(0x04) . chr(0x0A);   break;
            case "client-error-attributes-or-values-not-supported" : $ret = chr(0x04) . chr(0x0B);  break;
            case "client-error-uri-scheme-not-supported" : $ret = chr(0x04) . chr(0x0C); break;
            case "client-error-charset-not-supported" : $ret = chr(0x04) . chr(0x0D);  break;
            case "client-error-conflicting-attributes" : $ret = chr(0x04) . chr(0x0E); break;
            case "client-error-compression-not-supported" : $ret = chr(0x04) . chr(0x0F);  break;
            case "client-error-compression-error" : $ret = chr(0x04) . chr(0x10);  break;
            case "client-error-document-format-error" : $ret = chr(0x04) . chr(0x11);  break;
            case "client-error-document-access-error" : $ret = chr(0x04) . chr(0x12);  break;
            case "client-error-attributes-not-settable" : $ret = $ret = chr(0x04) . chr(0x13); break; // RFC3380
            case "server-error-internal-error" : $ret = $ret = chr(0x05) . chr(0x00); break;
            case "server-error-operation-not-supported" : $ret = $ret = chr(0x05) . chr(0x01); break;
            case "server-error-service-unavailable" : $ret = $ret = chr(0x05) . chr(0x02);  break;
            case "server-error-version-not-supported" : $ret = chr(0x05) . chr(0x03);  break;
            case "server-error-device-error" : $ret = $ret = chr(0x05) . chr(0x04);  break;
            case "server-error-temporary-error" : $ret = $ret = chr(0x05) . chr(0x05);  break;
            case "server-error-not-accepting-jobs": $ret = $ret = chr(0x05) . chr(0x06);  break;
            case "server-error-busy" : $ret = $ret = chr(0x05) . chr(0x07); break;
            case "server-error-job-canceled" : $ret = $ret = chr(0x05) . chr(0x08); break;
            case "server-error-multiple-document-jobs-not-supported" :  $ret = chr(0x05) . chr(0x09); break;
            case "successful-ok" :        
			default :  $ret = chr(0x00) . chr(0x00);

        }

		return ($ret);
    }
	

    //
    // REQUEST PARSING
    //

	//use chuncked data...
	protected function _parseClientRequestStream() {
	    $this->clientoutput->response = array();
		$this->_parsing->offset = 0;
		
        $body = '';
        $length = 0;		
		
	    $fp = fopen("php://input","r"); //rb?

	    //while(!feof($fp)) {
		do {
		
            // NOTE: for chunked encoding to work properly make sure
            // there is NOTHING (besides newlines) before the first hexlength

            // get the line which has the length of this chunk (use fgets here)
            $line = fgets($fp, BUFFER_LENGTH);

            // if it's only a newline this normally means it's read
            // the total amount of data requested minus the newline
            // continue to next loop to make sure we're done
            if ($line == CRLF) {
              continue;
            }

            // the length of the block is sent in hex decode it then loop through
            // that much data get the length
            // NOTE: hexdec() ignores all non hexadecimal chars it finds
            $length = hexdec($line);

            if (!is_int($length)) {
              //trigger_error('Most likely not chunked encoding', E_USER_ERROR);
			  $this->clientoutput->body .= $line;
			  continue;
            }

            // zero is sent when at the end of the chunks
            // or the end of the stream or error
            if ($line === false || $length < 1 || feof($fp)) {
              // break out of the streams loop
              break;
            }	

            // loop though the chunk
            do
            {
                // read $length amount of data
                // (use fread here)
                $data = fread($fp, $length);

                // remove the amount received from the total length on the next loop
                // it'll attempt to read that much less data
                $length -= strlen($data);

                // PRINT out directly
                #print $data;
                #flush();
                // you could also save it directly to a file here

                // store in string for later use
                $this->clientoutput->body .= $data;

                // zero or less or end of connection break
                if ($length <= 0 || feof($fp)) {
                   // break out of the chunk loop
                   break;
                }
            }
            while (true);
            // end of chunk loop
        }
        while (true);
        // end of stream loop
		
	    //} 
        fclose($fp);		
	}
	
    protected function _parseClientRequest() {
        $this->clientoutput->response = array();
		
        //if (!self::_parseHttpHeaders()) return FALSE;
		//self::c_parseHttpHeaders();
		
        $this->_parsing->offset = 0;
  
        self::c_parseIppVersion();
        self::c_parseOperationID();
        self::c_parseRequestID();
  
        self::c_parseRequest();
        //var_dump ($this->clientoutput->response);
		
		self::c_parseClientAttributes();
		
        self::c_parsePrinterAttributes();
        self::c_parseJobAttributes();
        self::c_parseOperationAttributes();
  
        /*/devel
        self::_putDebug(
        sprintf("***** IPP STATUS: %s ******", $this->clientoutput->operation_id),
        4);
        self::_putDebug("****** END OF OPERATION ****");*/
  
        return true;
    }

	//check if http request header exist and set as contition value
    protected function c_parseHttpHeaders($cont_header_name=null, $cont_header_value=null) {
        $response = "";
		
		//$headers = var_export($this->clientoutput->headers,1);
		
		foreach ($this->clientoutput->headers as $header_name=>$header_value) {
		    $headers .= $header_name."\r\n".$header_value."\r\n";
		
		    if (($cont_header_name) && ($cont_header_name==$header_name)) {
			    if ($cont_header_value == $header_value)
				  return true;
			}
        }
		
		//self::write2disk('client-headers.log',"\r\n".var_export($headers,1));		
		
		return false;
		
    }	
 
    protected function c_parseIppVersion() {
	
        $ippversion = (ord($this->clientoutput->body[$this->_parsing->offset]) * 256)
                     + ord($this->clientoutput->body[$this->_parsing->offset + 1]);
   
        switch ($ippversion) {
		
            case 0x0101: $this->clientoutput->ipp_version = "1.1"; break;
            case 0x0100: $this->clientoutput->ipp_version = "1.0"; break;	
            default:
                    $this->clientoutput->ipp_version =  
					sprintf("%u.%u (Unknown)", ord($this->clientoutput->body[$this->_parsing->offset]) * 256,
                                               ord($this->clientoutput->body[$this->_parsing->offset + 1]));
                    break;
        }
  
        self::_putDebug("I P P    R E S P O N S E :\n\n");
        self::_putDebug(sprintf("IPP version %s%s: %s",ord($this->clientoutput->body[$this->_parsing->offset]),
                                                       ord($this->clientoutput->body[$this->_parsing->offset + 1]),
                                                       $this->clientoutput->ipp_version));
	 
        $this->_parsing->offset+= 2;
        return;
    }
 

    protected function c_parseOperationID() {
        $operation_id =(ord($this->clientoutput->body[$this->_parsing->offset]) * 256)
                     + ord($this->clientoutput->body[$this->_parsing->offset + 1]);
        $this->clientoutput->operation_id = "NOT PARSED";
        $this->_parsing->offset+= 2;
        if (strlen($this->clientoutput->body) < $this->_parsing->offset)  {
          return false;
        }

        switch ($operation_id) {

          case 0x0002: $this->clientoutput->operation_id = "Print-Job";   break;
          case 0x0003: $this->clientoutput->operation_id = "Print-Uri";   break;	
          case 0x0004: $this->clientoutput->operation_id = "Validate-Job";  break;
          case 0x0005: $this->clientoutput->operation_id = "Create-Job";    break;	
          case 0x0006: $this->clientoutput->operation_id = "Send-Document";   break;
          case 0x0007: $this->clientoutput->operation_id = "Send-Uri";   break;	
          case 0x0008: $this->clientoutput->operation_id = "Cancel-Job";  break;	
          case 0x0009: $this->clientoutput->operation_id = "Get-Job-Attributes";   break;
          case 0x000A: $this->clientoutput->operation_id = "Get-Jobs";  break;
          case 0x000B: $this->clientoutput->operation_id = "Get-Printer-Attributes";  break;	
          case 0x000C: $this->clientoutput->operation_id = "Hold-Job";  break;	
          case 0x000D: $this->clientoutput->operation_id = "Release-Job";  break;	
          case 0x000E: $this->clientoutput->operation_id = "Restart-Job";  break;	
          case 0x0010: $this->clientoutput->operation_id = "Pause-Printer";  break;	
          case 0x0011: $this->clientoutput->operation_id = "Resume-Printer";   break;	
          case 0x0012: $this->clientoutput->operation_id = "Purge-Jobs";  break;	
          case 0x0013: $this->clientoutput->operation_id = "Set-Printer-Attributes";  break;	
          case 0x0014:  $this->clientoutput->operation_id = "Set-Job-Attributes";   break;		
          //CUPS	
          case 0x4001: $this->clientoutput->operation_id = "cups vendor extension: get-defaults";   break;	
          case 0x4002: $this->clientoutput->operation_id = "cups vendor extension: get-printers";  break;		
          case 0x4008: $this->clientoutput->operation_id = "cups vendor extension: accept-jobs";  break;	
          case 0x4009:  $this->clientoutput->operation_id = "cups vendor extension: reject-jobs";   break;		
          default:   break;
        }
		
        self::_putDebug(
	    sprintf("operation-id: %s%s: %s ",$this->clientoutput->body[$this->_parsing->offset],
                                          $this->clientoutput->body[$this->_parsing->offset + 1],
                                          $this->clientoutput->operation_id),4);
        return;
    }

    protected function c_parseRequestID()  {
	
       $this->clientoutput->request_id =
       self::_interpretInteger(substr($this->clientoutput->body, $this->_parsing->offset, 4));
	   
       self::_putDebug("request-id " . $this->clientoutput->request_id, 2);
       $this->_parsing->offset+= 4;
     
       return;
    } 
 
    //store all client request attributes
    protected function c_parseClientAttributes() {

        //if (!preg_match('#successful#',$this->clientoutput->status))
        //   return false;

        $k = -1;
        for ($i = 0 ; $i < count($this->clientoutput->response) ; $i++) {	
		
            for ($j = 0 ; $j < (count($this->clientoutput->response[$i]) - 1) ; $j ++)
                if (!empty($this->clientoutput->response[$i][$j]['name'])) {
                    $k++;
                    $l = 0;
                    $this->parsed[$k]['range'] = $this->clientoutput->response[$i]['attributes'];
                    $this->parsed[$k]['name'] = $this->clientoutput->response[$i][$j]['name'];
                    $this->parsed[$k]['type'] = $this->clientoutput->response[$i][$j]['type'];
                    $this->parsed[$k][$l] = $this->clientoutput->response[$i][$j]['value'];
                } else {
                    $l ++;
                    $this->parsed[$k][$l] = $this->clientoutput->response[$i][$j]['value'];
                }
		}			
        //$this->clientoutput->response = array();
        
        $this->client_attributes = new stdClass();
        for ($i = 0 ; $i < count($this->parsed) ; $i ++) {
                    $name = $this->parsed[$i]['name'];
                    $php_name = str_replace('-','_',$name);
                    $type = $this->parsed[$i]['type'];
                    $range = $this->parsed[$i]['range'];
                    $this->client_attributes->$php_name = new stdClass();
                    $this->client_attributes->$php_name->_type = $type;
                    $this->client_attributes->$php_name->_range = $range;
                    for ($j = 0 ; $j < (count($this->parsed[$i]) - 3) ; $j ++) {
                        $value = $this->parsed[$i][$j];
                        $index = '_value'.$j;
                        $this->client_attributes->$php_name->$index = $value;
                        }
                    }
                    
        $this->parsed = array();
                
         
    }
	
	//store all client request printer attributes
    protected function c_parsePrinterAttributes() {

        //if ($this->clientoutput->status != "successfull-ok")
        //    return false;
        
        $pr = -1;
        for ($i = 0 ; $i < count($this->clientoutput->response) ; $i++) {
		
            if ($this->clientoutput->response[$i]['attributes'] == "printer-attributes")
                $pr ++;
				
            $k = -1; 
            for ($j = 0 ; $j < (count($this->clientoutput->response[$i]) - 1) ; $j ++)
                if (!empty($this->clientoutput->response[$i][$j]['name'])) {
                    $k++;
                    $l = 0;
                    $this->parsed[$pr][$k]['range'] = $this->clientoutput->response[$i]['attributes'];
                    $this->parsed[$pr][$k]['name'] = $this->clientoutput->response[$i][$j]['name'];
                    $this->parsed[$pr][$k]['type'] = $this->clientoutput->response[$i][$j]['type'];
                    $this->parsed[$pr][$k][$l] = $this->clientoutput->response[$i][$j]['value'];
                } else {
                    $l ++;
                    $this->parsed[$pr][$k][$l] = $this->clientoutput->response[$i][$j]['value'];
                }
        }
        
        //$this->clientoutput->response = array();

        $this->client_printer_attributes = new stdClass();
		
        for ($pr_nbr = 0 ; $pr_nbr <= $pr ; $pr_nbr ++) {
		
            $pr_index = "pr_".$pr_nbr;
            $this->client_printer_attributes->$pr_index = new stdClass();
			
            for ($i = 0 ; $i < count($this->parsed[$pr_nbr]) ; $i ++) {
                    $name = $this->parsed[$pr_nbr][$i]['name'];
                    $php_name = str_replace('-','_',$name);
                    $type = $this->parsed[$pr_nbr][$i]['type'];
                    $range = $this->parsed[$pr_nbr][$i]['range'];
                    $this->client_printer_attributes->$pr_index->$php_name = new stdClass();
                    $this->client_printer_attributes->$pr_index->$php_name->_type = $type;
                    $this->client_printer_attributes->$pr_index->$php_name->_range = $range;
                    for ($j = 0 ; $j < (count($this->parsed[$pr_nbr][$i]) - 3) ; $j ++) {
                        # This causes incorrect parsing of integer job attributes.
                        # 2010-08-16
                        # bpkroth
                        #$value = self::_interpretAttribute($name,$type,$this->parsed[$job_nbr][$i][$j]);
                        $value = $this->parsed[$pr_nbr][$i][$j];
                        $index = '_value'.$j;
                        $this->client_printer_attributes->$pr_index->$php_name->$index = $value;
                    }
            }
        }
            
        $this->parsed = array(); 
    }		
	
	//store all client request operation attributes
    protected function c_parseOperationAttributes() {

        //if ($this->clientoutput->status != "successfull-ok")
        //    return false;
        
        $op = -1;
        for ($i = 0 ; $i < count($this->clientoutput->response) ; $i++) {
		
            if ($this->clientoutput->response[$i]['attributes'] == "operation-attributes")
                $op ++;
				
            $k = -1; 
            for ($j = 0 ; $j < (count($this->clientoutput->response[$i]) - 1) ; $j ++)
                if (!empty($this->clientoutput->response[$i][$j]['name'])) {
                    $k++;
                    $l = 0;
                    $this->parsed[$op][$k]['range'] = $this->clientoutput->response[$i]['attributes'];
                    $this->parsed[$op][$k]['name'] = $this->clientoutput->response[$i][$j]['name'];
                    $this->parsed[$op][$k]['type'] = $this->clientoutput->response[$i][$j]['type'];
                    $this->parsed[$op][$k][$l] = $this->clientoutput->response[$i][$j]['value'];
                } else {
                    $l ++;
                    $this->parsed[$op][$k][$l] = $this->clientoutput->response[$i][$j]['value'];
                }
        }
        
        //$this->clientoutput->response = array();

        $this->client_operation_attributes = new stdClass();
		
        for ($op_nbr = 0 ; $op_nbr <= $op ; $op_nbr ++) {
		
            $op_index = "op_".$op_nbr;
            $this->client_operation_attributes->$op_index = new stdClass();
			
            for ($i = 0 ; $i < count($this->parsed[$op_nbr]) ; $i ++) {
                    $name = $this->parsed[$op_nbr][$i]['name'];
                    $php_name = str_replace('-','_',$name);
                    $type = $this->parsed[$op_nbr][$i]['type'];
                    $range = $this->parsed[$op_nbr][$i]['range'];
                    $this->client_operation_attributes->$op_index->$php_name = new stdClass();
                    $this->client_operation_attributes->$op_index->$php_name->_type = $type;
                    $this->client_operation_attributes->$op_index->$php_name->_range = $range;
                    for ($j = 0 ; $j < (count($this->parsed[$op_nbr][$i]) - 3) ; $j ++) {
                        # This causes incorrect parsing of integer job attributes.
                        # 2010-08-16
                        # bpkroth
                        #$value = self::_interpretAttribute($name,$type,$this->parsed[$job_nbr][$i][$j]);
                        $value = $this->parsed[$op_nbr][$i][$j];
                        $index = '_value'.$j;
                        $this->client_operation_attributes->$op_index->$php_name->$index = $value;
                    }
            }
        }
            
        $this->parsed = array();
    }	
 
    //store all client request job attributes
    protected function c_parseJobAttributes() {

        //if ($this->clientoutput->status != "successfull-ok")
        //    return false;
        
        $job = -1;
        for ($i = 0 ; $i < count($this->clientoutput->response) ; $i++) {
		
            if ($this->clientoutput->response[$i]['attributes'] == "job-attributes")
                $job ++;
				
            $k = -1; 
            for ($j = 0 ; $j < (count($this->clientoutput->response[$i]) - 1) ; $j ++)
                if (!empty($this->clientoutput->response[$i][$j]['name'])) {
                    $k++;
                    $l = 0;
                    $this->parsed[$job][$k]['range'] = $this->clientoutput->response[$i]['attributes'];
                    $this->parsed[$job][$k]['name'] = $this->clientoutput->response[$i][$j]['name'];
                    $this->parsed[$job][$k]['type'] = $this->clientoutput->response[$i][$j]['type'];
                    $this->parsed[$job][$k][$l] = $this->clientoutput->response[$i][$j]['value'];
                } else {
                    $l ++;
                    $this->parsed[$job][$k][$l] = $this->clientoutput->response[$i][$j]['value'];
                }
        }
        
        //$this->clientoutput->response = array();

        $this->client_job_attributes = new stdClass();
		
        for ($job_nbr = 0 ; $job_nbr <= $job ; $job_nbr ++) {
		
            $job_index = "job_".$job_nbr;
            $this->client_job_attributes->$job_index = new stdClass();
			
            for ($i = 0 ; $i < count($this->parsed[$job_nbr]) ; $i ++) {
                    $name = $this->parsed[$job_nbr][$i]['name'];
                    $php_name = str_replace('-','_',$name);
                    $type = $this->parsed[$job_nbr][$i]['type'];
                    $range = $this->parsed[$job_nbr][$i]['range'];
                    $this->client_job_attributes->$job_index->$php_name = new stdClass();
                    $this->client_job_attributes->$job_index->$php_name->_type = $type;
                    $this->client_job_attributes->$job_index->$php_name->_range = $range;
                    for ($j = 0 ; $j < (count($this->parsed[$job_nbr][$i]) - 3) ; $j ++) {
                        # This causes incorrect parsing of integer job attributes.
                        # 2010-08-16
                        # bpkroth
                        #$value = self::_interpretAttribute($name,$type,$this->parsed[$job_nbr][$i][$j]);
                        $value = $this->parsed[$job_nbr][$i][$j];
                        $index = '_value'.$j;
                        $this->client_job_attributes->$job_index->$php_name->$index = $value;
                    }
            }
        }
            
        $this->parsed = array(); 
    }
 
 
    protected function c_parseRequest () {
        $j = -1;
        $this->index = 0;
        for ($i = $this->_parsing->offset; $i < strlen($this->clientoutput->body) ; $i = $this->_parsing->offset) {
        
        
            $tag = ord($this->clientoutput->body[$this->_parsing->offset]);
            
            
            if ($tag > 0x0F) {
                self::_readAttribute($j);
                $this->index ++;
                continue;
            }

            switch ($tag) {
                case 0x01:
                    $j += 1;
                    $this->clientoutput->response[$j]['attributes'] = "operation-attributes";
                    $this->index = 0;
                    $this->_parsing->offset += 1;
                    break;
                case 0x02:
                    $j += 1;
                    $this->clientoutput->response[$j]['attributes'] = "job-attributes";
                    $this->index = 0;
                    $this->_parsing->offset += 1;
                    break;
                case 0x03:
                    $j +=1;
                    $this->clientoutput->response[$j]['attributes'] = "end-of-attributes";
                    self::_putDebug( "tag is: ".$this->clientoutput->response[$j]['attributes']."\n");
                    if ($this->alert_on_end_tag === 1)
                        echo "END tag OK<br />";
                    $this->response_completed[(count($this->response_completed) -1)] = "completed";
                    return;
                case 0x04:
                    $j += 1;
                    $this->clientoutput->response[$j]['attributes'] = "printer-attributes";
                    $this->index = 0;
                    $this->_parsing->offset += 1;
                    break;
                case 0x05:
                    $j += 1;
                    $this->clientoutput->response[$j]['attributes'] = "unsupported-attributes";
                    $this->index = 0;
                    $this->_parsing->offset += 1;
                    break;
                default:
                    $j += 1;
                    $this->clientoutput->response[$j]['attributes'] = sprintf(self::_dummy("0x%x (%u) : attributes tag Unknown (reserved for future versions of IPP"),$tag,$tag);
                    $this->index = 0;
                    $this->_parsing->offset += 1;
                    break;
                }
        
         self::_putDebug( "tag is: ".$this->clientoutput->response[$j]['attributes']."\n\n\n");
    
        }  
        return;        
    } 
 
    protected function _readAttribute($attributes_type) {
        
            $tag = ord($this->clientoutput->body[$this->_parsing->offset]);
            
            $this->_parsing->offset += 1;
            $j = $this->index;
            
            $tag = self::_readTag($tag);

            switch ($tag) {
                 case "begCollection": //RFC3382 (BLIND CODE)
                    if ($this->end_collection)
                        $this->index --;
                    $this->end_collection = false;
                    $this->clientoutput->response[$attributes_type][$j]['type'] = "collection";
                    self::_putDebug( "tag is: begCollection\n");
                    self::_readAttributeName ($attributes_type,$j);
                    if (!$this->clientoutput->response[$attributes_type][$j]['name']) { // it is a multi-valued collection
                        $this->collection_depth ++;
                        $this->index --;
                        $this->collection_nbr[$this->collection_depth] ++;
                    } else {
                        $this->collection_depth ++;
                        if ($this->collection_depth == 0)
                            $this->collection = (object) 'collection';
                        if (array_key_exists($this->collection_depth,$this->collection_nbr))
                            $this->collection_nbr[$this->collection_depth] ++;
                        else
                            $this->collection_nbr[$this->collection_depth] = 0;
                        unset($this->end_collection);
                        
                        }
                    self::_readValue ("begCollection",$attributes_type,$j);
                    break;
                 case "endCollection": //RFC3382 (BLIND CODE)
                    $this->clientoutput->response[$attributes_type][$j]['type'] = "collection";
                    self::_putDebug( "tag is: endCollection\n");
                    self::_readAttributeName ($attributes_type,$j,0);
                    self::_readValue ('name',$attributes_type,$j,0);
                    $this->collection_depth --;
                    $this->collection_key[$this->collection_depth] = 0;
                    $this->end_collection = true;
                    break;
                 case "memberAttrName": // RFC3382 (BLIND CODE)
                    $this->clientoutput->response[$attributes_type][$j]['type'] = "memberAttrName";
                    $this->index -- ;
                    self::_putDebug( "tag is: memberAttrName\n");
                    self::_readCollection ($attributes_type,$j);
                    break;
 
                default:
                    $this->collection_depth = -1;
                    $this->collection_key = array();
                    $this->collection_nbr = array();
                    $this->clientoutput->response[$attributes_type][$j]['type'] = $tag;
                    self::_putDebug( "tag is: $tag\n");
                    $attribute_name = self::_readAttributeName ($attributes_type,$j);
                    if (!$attribute_name)
                        $attribute_name = $this->attribute_name;
                    else
                        $this->attribute_name = $attribute_name;
                    $value = self::_readValue ($tag,$attributes_type,$j);
                    $this->clientoutput->response[$attributes_type][$j]['value'] = 
                        self::_interpretAttribute($attribute_name,$tag,$this->clientoutput->response[$attributes_type][$j]['value']);
                    break;
                
            }
    return;
    } 
 
    protected function _readTag($tag) {

            switch ($tag) {
                case 0x10:
                    $tag = "unsupported";
                    break;
                case 0x11:
                    $tag = "reserved for 'default'";
                    break;
                case 0x12:
                    $tag = "unknown";
                    break;
                case 0x13:
                    $tag = "no-value";
                    break;
                case 0x15: // RFC 3380
                    $tag = "not-settable";
                    break;
                case 0x16: // RFC 3380
                    $tag = "delete-attribute";
                    break;
                case 0x17: // RFC 3380
                    $tag = "admin-define";
                    break;
                case 0x20:
                    $tag = "IETF reserved (generic integer)";
                    break;
                case 0x21:
                    $tag = "integer";
                    break;
                case 0x22:
                    $tag = "boolean";
                    break;
                case 0x23:
                    $tag = "enum";
                    break;
                case 0x30:
                    $tag = "octetString";
                    break;
                case 0x31:
                    $tag = "datetime";
                    break;
                 case 0x32:
                    $tag = "resolution";
                    break;
                 case 0x33:
                    $tag = "rangeOfInteger";
                    break;
                 case 0x34: //RFC3382 (BLIND CODE)
                    $tag = "begCollection";
                    break;
                 case 0x35:
                    $tag = "textWithLanguage";
                    break;
                 case 0x36:
                    $tag = "nameWithLanguage";
                    break;
                 case 0x37: //RFC3382 (BLIND CODE)
                    $tag = "endCollection";
                    break;
                 case 0x40:
                    $tag = "IETF reserved text string";
                    break;
                 case 0x41:
                    $tag = "textWithoutLanguage";
                    break;
                 case 0x42:
                    $tag = "nameWithoutLanguage";
                    break;
                 case 0x43:
                    $tag = "IETF reserved for future";
                    break;
                 case 0x44:
                    $tag = "keyword";
                    break;
                 case 0x45:
                    $tag = "uri";
                    break;
                 case 0x46:
                    $tag = "uriScheme";
                    break;
                 case 0x47:
                    $tag = "charset";
                    break;
                 case 0x48:
                    $tag = "naturalLanguage";
                    break;
                 case 0x49:
                    $tag = "mimeMediaType";
                    break;
                 case 0x4A: // RFC3382 (BLIND CODE)
                    $tag = "memberAttrName";
                    break;
                  case 0x7F:
                    $tag = "extended type";
                    break;
                 default:
                 
                    if ($tag >= 0x14 && $tag < 0x15 && $tag > 0x17 && $tag <= 0x1f) 
                        $tag = "out-of-band";
                    elseif (0x24 <= $tag && $tag <= 0x2f) 
                        $tag = "new integer type";
                    elseif (0x38 <= $tag && $tag <= 0x3F) 
                        $tag = "new octet-stream type";
                    elseif (0x4B <= $tag && $tag <= 0x5F) 
                        $tag = "new character string type";
                    elseif ((0x60 <= $tag && $tag < 0x7f) || $tag >= 0x80 )
                        $tag = "IETF reserved for future";
                    else
                        $tag = sprintf("UNKNOWN: 0x%x (%u)",$tag,$tag);
                        
                    break;                                                            
                }
            return $tag; 
    }
	
    protected function _readCollection($attributes_type,$j) {
       
        $name_length = ord($this->clientoutput->body[$this->_parsing->offset]) *  256
                     +  ord($this->clientoutput->body[$this->_parsing->offset + 1]);
        
        $this->_parsing->offset += 2;
        
        self::_putDebug( "Collection name_length ". $name_length ."\n");
        
        $name = '';
        for ($i = 0; $i < $name_length; $i++) {
            $name .= $this->clientoutput->body[$this->_parsing->offset];
            $this->_parsing->offset += 1;
            if ($this->_parsing->offset > strlen($this->clientoutput->body))
                return;
            }
        
        $collection_name = $name;
        
        $name_length = ord($this->clientoutput->body[$this->_parsing->offset]) *  256
                     +  ord($this->clientoutput->body[$this->_parsing->offset + 1]);
        $this->_parsing->offset += 2;
        
        self::_putDebug( "Attribute name_length ". $name_length ."\n");
        
        $name = '';
        for ($i = 0; $i < $name_length; $i++) {
            $name .= $this->clientoutput->body[$this->_parsing->offset];
            $this->_parsing->offset += 1;
            if ($this->_parsing->offset > strlen($this->clientoutput->body))
                return;
            }
        
        $attribute_name = $name;
        if ($attribute_name == "") {
            $attribute_name = $this->last_attribute_name;
            $this->collection_key[$this->collection_depth] ++;
        } else {
            $this->collection_key[$this->collection_depth] = 0;
        }
        $this->last_attribute_name = $attribute_name;
        
        self::_putDebug( "Attribute name ".$name."\n");
        
        $tag = self::_readTag(ord($this->serveroutput->body[$this->_parsing->offset]));
        $this->_parsing->offset ++;
        
        $type = $tag;
        
        $name_length = ord($this->serveroutput->body[$this->_parsing->offset]) *  256
                     +  ord($this->serveroutput->body[$this->_parsing->offset + 1]);
        $this->_parsing->offset += 2;
        
        self::_putDebug( "Collection2 name_length ". $name_length ."\n");
        
        $name = '';
        for ($i = 0; $i < $name_length; $i++) {
            $name .= $this->serveroutput->body[$this->_parsing->offset];
            $this->_parsing->offset += 1;
            if ($this->_parsing->offset > strlen($this->serveroutput->body))
                return;
            }
        
        $collection_value = $name;
        $value_length = ord($this->serveroutput->body[$this->_parsing->offset]) *  256
                      +  ord($this->serveroutput->body[$this->_parsing->offset + 1]);
        
        self::_putDebug( "Collection value_length ".$this->serveroutput->body[ $this->_parsing->offset]
                                       . $this->serveroutput->body[$this->_parsing->offset + 1]
                                       .": "
                                       . $value_length
                                       . " ");
        
        $this->_parsing->offset += 2;
        
        $value = '';
        for ($i = 0; $i < $value_length; $i++) {
            
            if ($this->_parsing->offset >= strlen($this->serveroutput->body))
                return;
            $value .= $this->serveroutput->body[$this->_parsing->offset];
            $this->_parsing->offset += 1;
            
            }
        
        $object = &$this->collection;
        for ($i = 0 ; $i <= $this->collection_depth ; $i ++) {
            $indice = "_indice".$this->collection_nbr[$i];
            if (!isset($object->$indice))
                $object->$indice = (object) 'indice';
            $object = &$object->$indice;
            }
         
        $value_key = "_value".$this->collection_key[$this->collection_depth];
        $col_name_key = "_collection_name".$this->collection_key[$this->collection_depth];
        $col_val_key = "_collection_value".$this->collection_key[$this->collection_depth];
        
        $attribute_value = self::_interpretAttribute($attribute_name,$tag,$value);
        $attribute_name = str_replace('-','_',$attribute_name);
        
        
        self::_putDebug( sprintf("Value: %s\n",$value));
        $object->$attribute_name->_type = $type;
        $object->$attribute_name->$value_key = $attribute_value;
        $object->$attribute_name->$col_name_key = $collection_name;
        $object->$attribute_name->$col_val_key = $collection_value;
        
        $this->clientoutput->response[$attributes_type][$j]['value'] = $this->collection;
    }	
 
    protected function _readAttributeName ($attributes_type,$j,$write=1) {

        $name_length = ord($this->clientoutput->body[ $this->_parsing->offset]) *  256
                     +  ord($this->clientoutput->body[$this->_parsing->offset + 1]);
        $this->_parsing->offset += 2;
        
        self::_putDebug( "name_length ". $name_length ."\n");
        
        $name = '';
        for ($i = 0; $i < $name_length; $i++) {
            if ($this->_parsing->offset >= strlen($this->clientoutput->body))
                return;
            $name .= $this->clientoutput->body[$this->_parsing->offset];
            $this->_parsing->offset += 1;
            }
        
        if($write)
        $this->clientoutput->response[$attributes_type][$j]['name'] = $name;

        self::_putDebug( "name " . $name . "\n");
    
    return $name;   
    }
 
    protected function _readValue ($type,$attributes_type,$j,$write=1) {
	    //echo '\n>>>>>>>>>>>>>>>>readValue:',$type,'|',$attributes_type;	

        $value_length = ord($this->clientoutput->body[$this->_parsing->offset]) *  256
                      +  ord($this->clientoutput->body[$this->_parsing->offset + 1]);
        
        self::_putDebug( "value_length ".$this->clientoutput->body[ $this->_parsing->offset]
                                       . $this->clientoutput->body[$this->_parsing->offset + 1]
                                       .": "
                                       . $value_length
                                       . " ");
        
        $this->_parsing->offset += 2;
        
        $value = '';
        for ($i = 0; $i < $value_length; $i++) {
            
            if ($this->_parsing->offset >= strlen($this->clientoutput->body))
                return;
            $value .= $this->clientoutput->body[$this->_parsing->offset];
            $this->_parsing->offset += 1;
            
            }
            
        self::_putDebug( sprintf("Value: %s\n",$value));
        
        if ($write)
        $this->clientoutput->response[$attributes_type][$j]['value'] = $value;

    return $value;
    } 
 
     protected function _interpretAttribute($attribute_name,$type,$value) {
        
        switch ($type) {
            case "integer":
                $value = self::_interpretInteger($value);
                break;
            case "rangeOfInteger":
                $value = self::_interpretRangeOfInteger($value);
                break;
            case 'boolean':
                $value = ord($value);
                if ($value == 0x00)
                    $value = 'false';
                else
                    $value = 'true';
                break;
            case 'datetime':
                $value = self::_interpretDateTime($value);
                break;
            case 'enum':
                $value = $this->_interpretEnum($attribute_name,$value); // must be overwritten by children
                break;
            case 'resolution':
                $unit = $value[8];
                $value = self::_interpretRangeOfInteger(substr($value,0,8));
                switch($unit) {
                    case chr(0x03):
                        $unit = "dpi";
                        break;
                    case chr(0x04):
                        $unit = "dpc";
                        break;
                    }
                $value = $value." ".$unit;
                break;
            default:
                break;
                }
    return $value;
    }
 
    protected function _interpretRangeOfInteger($value) {
        
        $value_parsed = 0;
        $integer1 = $integer2 = 0;
        
        $halfsize = strlen($value) / 2;
       
        $integer1 = self::_interpretInteger(substr($value,0,$halfsize));
        $integer2 = self::_interpretInteger(substr($value,$halfsize,$halfsize));
         
        $value_parsed = sprintf('%s-%s',$integer1,$integer2);
        
        
    return $value_parsed;
    }
 
    protected function _interpretDateTime($date) {
        $year = self::_interpretInteger(substr($date,0,2));
        $month =  self::_interpretInteger(substr($date,2,1));
        $day =  self::_interpretInteger(substr($date,3,1));
        $hour =  self::_interpretInteger(substr($date,4,1));
        $minute =  self::_interpretInteger(substr($date,5,1));
        $second =  self::_interpretInteger(substr($date,6,1));
        $direction = substr($date,8,1);
        $hours_from_utc = self::_interpretInteger(substr($date,9,1));
        $minutes_from_utc = self::_interpretInteger(substr($date,10,1));
        
        $date = sprintf('%s-%s-%s %s:%s:%s %s%s:%s',$year,$month,$day,$hour,$minute,$second,$direction,$hours_from_utc,$minutes_from_utc);

    return $date;
    }  

    protected function _interpretEnum($attribute_name,$value) {
        
        $value_parsed = self::_interpretInteger($value);
        
        switch ($attribute_name) {
            case 'job-state':
                switch ($value_parsed) {
                    case 0x03:
                        $value = 'pending';
                        break;
                    case 0x04:
                        $value = 'pending-held';
                        break;
                    case 0x05:
                        $value = 'processing';
                        break;
                    case 0x06:
                        $value = 'processing-stopped';
                        break;
                    case 0x07:
                        $value = 'canceled';
                        break;
                    case 0x08:
                        $value = 'aborted';
                        break;
                    case 0x09:
                        $value = 'completed';
                        break;
                    }
                if ($value_parsed > 0x09)
                    $value = sprintf('Unknown(IETF standards track "job-state" reserved): 0x%x',$value_parsed);
                break;
            case 'print-quality':
            case 'print-quality-supported':
            case 'print-quality-default':
                switch ($value_parsed) {
                    case 0x03:
                        $value = 'draft';
                        break;
                    case 0x04:
                        $value = 'normal';
                        break;
                    case 0x05:
                        $value = 'high';
                        break;
                    }
                break;
            case 'printer-state':
                switch ($value_parsed) {
                    case 0x03:
                        $value = 'idle';
                        break;
                    case 0x04:
                        $value = 'processing';
                        break;
                    case 0x05:
                        $value = 'stopped';
                        break;
                    }
                if ($value_parsed > 0x05)
                    $value = sprintf('Unknown(IETF standards track "printer-state" reserved): 0x%x',$value_parsed);
                break;
            
            case 'operations-supported':
                switch($value_parsed) {
                    case 0x0000:
                    case 0x0001:
                        $value = sprintf('Unknown(reserved) : %s',ord($value));
                        break;
                    case 0x0002:
                        $value = 'Print-Job';
                        break;
                    case 0x0003:
                        $value = 'Print-URI';
                        break;
                    case 0x0004:
                        $value = 'Validate-Job';
                        break;
                    case 0x0005:
                        $value = 'Create-Job';
                        break;
                    case 0x0006:
                        $value = 'Send-Document';
                        break;
                    case 0x0007:
                        $value = 'Send-URI';
                        break;
                    case 0x0008:
                        $value = 'Cancel-Job';
                        break;
                    case 0x0009:
                        $value = 'Get-Job-Attributes';
                        break;
                    case 0x000A:
                        $value = 'Get-Jobs';
                        break;
                    case 0x000B:
                        $value = 'Get-Printer-Attributes';
                        break;
                    case 0x000C:
                        $value = 'Hold-Job';
                        break;
                    case 0x000D:
                        $value = 'Release-Job';
                        break;
                    case 0x000E:
                        $value = 'Restart-Job';
                        break;
                    case 0x000F:
                        $value = 'Unknown(reserved for a future operation)';
                        break;
                    case 0x0010:
                        $value = 'Pause-Printer';
                        break;
                    case 0x0011:
                        $value = 'Resume-Printer';
                        break;
                    case 0x0012:
                        $value = 'Purge-Jobs';
                        break;
                    case 0x0013:
                        $value = 'Set-Printer-Attributes'; // RFC3380
                        break;
                    case 0x0014:
                        $value = 'Set-Job-Attributes'; // RFC3380
                        break;
                    case 0x0015:
                        $value = 'Get-Printer-Supported-Values'; // RFC3380
                        break;
                    case 0x0016:
                        $value = 'Create-Printer-Subscriptions';
                        break;
                    case 0x0017:
                        $value = 'Create-Job-Subscriptions';
                        break;
                    case 0x0018:
                        $value = 'Get-Subscription-Attributes';
                        break;
                    case 0x0019:
                        $value = 'Get-Subscriptions';
                        break;
                    case 0x001A:
                        $value = 'Renew-Subscription';
                        break;
                    case 0x001B:
                        $value = 'Cancel-Subscription';
                        break;
                    case 0x001C:
                        $value = 'Get-Notifications';
                        break;
                    case 0x001D:
                        $value = sprintf('Unknown (reserved IETF "operations"): 0x%x',ord($value));
                        break;
                    case 0x001E:
                        $value = sprintf('Unknown (reserved IETF "operations"): 0x%x',ord($value));
                        break;
                    case 0x001F:
                        $value = sprintf('Unknown (reserved IETF "operations"): 0x%x',ord($value));
                        break;
                    case 0x0020:
                        $value = sprintf('Unknown (reserved IETF "operations"): 0x%x',ord($value));
                        break;
                    case 0x0021:
                        $value = sprintf('Unknown (reserved IETF "operations"): 0x%x',ord($value));
                        break;
                    case 0x0022: 
                        $value = 'Enable-Printer';
                        break;
                    case 0x0023: 
                        $value = 'Disable-Printer';
                        break;
                    case 0x0024: 
                        $value = 'Pause-Printer-After-Current-Job';
                        break;
                    case 0x0025: 
                        $value = 'Hold-New-Jobs';
                        break;
                    case 0x0026: 
                        $value = 'Release-Held-New-Jobs';
                        break;
                    case 0x0027: 
                        $value = 'Deactivate-Printer';
                        break;
                    case 0x0028: 
                        $value = 'Activate-Printer';
                        break;
                    case 0x0029: 
                        $value = 'Restart-Printer';
                        break;
                    case 0x002A: 
                        $value = 'Shutdown-Printer';
                        break;
                    case 0x002B: 
                        $value = 'Startup-Printer';
                        break;
                }
                if ($value_parsed > 0x002B && $value_parsed <= 0x3FFF)
                    $value = sprintf('Unknown(IETF standards track operations reserved): 0x%x',$value_parsed);
                elseif ($value_parsed >= 0x4000 && $value_parsed <= 0x8FFF) {
                  switch ($value_parsed) {
                    case 0x4002:
                        $value = 'Get-Availables-Printers';
                        break;
                    default:
                        $value = sprintf('Unknown(Cups extension for operations): 0x%x',$value_parsed);
                        break;
                  }
                } 
				elseif ($value_parsed > 0x8FFF)
                    $value = sprintf('Unknown operation (should not exists): 0x%x',$value_parsed);
                
                break;
            case 'finishings':
            case 'finishings-default':
            case 'finishings-supported':
                switch ($value_parsed) {
                    case 0x03:
                        $value = 'none';
                        break;
                    case 0x04:
                        $value = 'staple';
                        break;
                    case 0x05:
                        $value = 'punch';
                        break;
                    case 0x06:
                        $value = 'cover';
                        break;
                    case 0x07:
                        $value = 'bind';
                        break;
                    case 0x08:
                        $value = 'saddle-stitch';
                        break;
                    case 0x09:
                        $value = 'edge-stitch';
                        break;
                    case 0x14://20:
                        $value = 'staple-top-left';
                        break;
                    case 0x15://21:
                        $value = 'staple-bottom-left';
                        break;
                    case 0x16://22:
                        $value = 'staple-top-right';
                        break;
                    case 0x17://23:
                        $value = 'staple-bottom-right';
                        break;
                    case 0x18://24:
                        $value = 'edge-stitch-left';
                        break;
                    case 0x19://25:
                        $value = 'edge-stitch-top';
                        break;
                    case 0x1A://26:
                        $value = 'edge-stitch-right';
                        break;
                    case 0x1B://27:
                        $value = 'edge-stitch-bottom';
                        break;
                    case 0x1C://28:
                        $value = 'staple-dual-left';
                        break;
                    case 0x1D://29:
                        $value = 'staple-dual-top';
                        break;
                    case 0x1E://30:
                        $value = 'staple-dual-right';
                        break;
                    case 0x1F://31:
                        $value = 'staple-dual-bottom';
                        break;
                    }
                if ($value_parsed > 31)
                    $value = sprintf('Unknown(IETF standards track "finishing" reserved): 0x%x',$value_parsed);
                break;
            
            case 'orientation-requested':
            case 'orientation-requested-supported':
            case 'orientation-requested-default':
                switch ($value_parsed) {
                    case 0x03:
                        $value = 'portrait';
                        break;
                    case 0x04:
                        $value = 'landscape';
                        break;
                    case 0x05:
                        $value = 'reverse-landscape';
                        break;
                    case 0x06:
                        $value = 'reverse-portrait';
                        break;
                    }
                if ($value_parsed > 0x06)
                    $value = sprintf('Unknown(IETF standards track "orientation" reserved): 0x%x',$value_parsed);
                break;
				
			case "cpi":     
                switch ($value_parsed) {
                    case 0x0A://chr(10):
                        $value = '10';
                        break;
                    case 0x0C://chr(12):
                        $value = '12';
                        break;
                    case 0x11://chr(17):
                        $value = '17';
                        break;
                    default:
                        $value = '10';
                }
            break;
			
            case "lpi":
                switch ($value_parsed) {
                    case 0x06://chr(6):
                        $value = '6';
                        break;
                    case 0x08://chr(8):
                        $value = '8';
                        break;
                    default:
                        $value = '6';
                }
            break;	
                
            default:
                break;
        }
		
        return $value;
    }	
 
 
    //
    //override tags
    // 
    protected function _initTags () {
        
        // override parent with specific cups attributes
        $tags_types = array ("uri-type" => array("tag" => chr(0x45) , "build" => "string"),//already defined as uri..
		                     ); 
        $this->tags_types = array_merge ($this->tags_types, $tags_types);								 
        
		//operation tags
        $operation_tags = array ("status-message" => array("tag" => "textWithoutLanguage"),
								 "ipp-attribute-fidelity" => array("tag" => "boolean"),		
		                         "attributes-charset" => array("tag" => "textWithoutLanguage"),//??
								 "printer-message-from-operator" => array("tag" => "textWithoutLanguage"),//text
								 
                                 "printer-uri" => array("tag" => "uri"),
								 "last-document" => array("tag" => "boolean"),
								 
                                 //"which-jobs" => array("tag" => "keyword"),//keyword, which jobs completed...								 
								 //"limit" => array("tag" => "integer"),//integer, jobs limit
								 //"requested-attributes" => array("tag" => "keyword"),//keyword, requested attr
		                         ); 
        $this->operation_tags = array_merge ($this->operation_tags, $operation_tags);
        
        //job tags		
        $job_tags = array ( //job description attributes....
		                    "job-id" => array("tag" => "integer") ,
                            "job-uri" => array("tag" => "uri-type") ,
							"job-printer-uri" => array("tag" => "uri") ,
							"job-more-info" => array("tag" => "uri") ,
							"job-name" => array("tag" => "nameWithoutLanguage") ,//name
							"job-originating-user-name" => array("tag" => "nameWithoutLanguage") ,//name
                            "job-state" => array("tag" => "enum"),			
							"job-state-reasons" => array("tag" => "keyword"),//1setof type2 keyword
                            "job-state-message" => array("tag" => "textWithoutLanguage"),//text
							"number-of-documents" => array("tag" => "integer"),//integer
							"output-device-assigned" => array("tag" => "nameWithoutLanguage"),//name
							"time-at-creation" => array("tag" => "integer"),//integer
							"time-at-processing" => array("tag" => "integer"),//integer
							"time-at-completed" => array("tag" => "integer"),//integer
							"number-of-interventing-jobs" => array("tag" => "integer"),//integer
                            //job-message-from-operator...derived by this->job_tags					
                            //job-k-octets...derived by this->job_tags	 
                            //job-impressions...derived by this->job_tags	  
                            //job-media-sheets...derived by this->job_tags
							"job-k-limit" => array("tag" => "integer"),//integer kbytes to print
                            "job-k-octets-prossesed" => array("tag" => "integer"),//integer							
							"job-impressions-completed" => array("tag" => "integer"),//integer
							"job-media-sheets-completed" => array("tag" => "integer"),//integer
							
							//job template attributes.....
							"copies" => array("tag" => "integer"),//integer
							"sides" => array("tag" => "integer"),//integer
							"page-ranges" => array("tag" => "rangeOfInteger"),//range of integer
							"finishings-suppoted" => array("tag" => "enum"),//enum
							"finishings-default" => array("tag" => "enum"),//enum
							"orientation-requested-supported" => array("tag" => "enum"),//enum
							"orientation-requested-default" => array("tag" => "enum"),//enum
	                        //cups...
                            "job-billing" => array("tag" => "textWithoutLanguage"),
                            "blackplot" => array("tag" => "boolean"),
                            "brightness" => array("tag" => "integer"),
                            "columns" => array("tag" => "integer"),
                            "cpi" => array("tag" => "enum"),
                            "fitplot" => array("tag" => "boolean"),
                            "gamma" => array("tag" => "integer"),
                            "hue" => array("tag" => "integer"),
                            "lpi" => array("tag" => "enum"),
                            "mirror" => array("tag","boolean"),
                            "natural-scaling" => array("tag" => "integer"),
                            "number-up-layout" => array("tag" => "keyword"),
                            "page-border" => array("tag" => "keyword"),
                            "page-bottom" => array("tag" => "integer"),
                            "page-label" => array("tag" => "textWithoutLanguage"),
                            "page-left" => array("tag" => "integer"),
                            "page-right" => array("tag" => "integer"),
                            "page-set" => array("tag" => "keyword"),
                            "page-top" => array("tag" => "integer"),
                            "penwidth" => array("tag" => "integer"),
                            "position" => array("tag" => "keyword"),
                            "ppi" => array("tag" => "integer"),
                            "prettyprint" => array("tag","boolean"),
                            "saturation" => array("tag" => "integer"),
                            "scaling" => array("tag" => "integer"),
                            "wrap" => array("tag","boolean"),							
                            );
        $this->job_tags = array_merge ($this->job_tags, $job_tags);
		
		//printer tags
        $printer_tags = array ( "printer-uri-supported" => array("tag" => "uri") ,//1setOf uri
                                "uri-security-supported" => array("tag" => "keyword") ,//1setOf type2 keyword
								"uri-authentication-supported" => array("tag" => "keyword") ,//1setOf type2 keyword
                                "printer-name" => array("tag" => "nameWithoutLanguage"),//name
                                "printer-location" => array("tag" => "textWithoutLanguage"),//text
								"printer-info" => array("tag" => "textWithoutLanguage"),//text 
								"printer-more-info" => array("tag" => "uri"),
								"printer-driver-installer" => array("tag" => "uri"), 
								"printer-make-and-model" => array("tag" => "textWithoutLanguage"),//text 
								"printer-more-info-manufacturer" => array("tag" => "uri"), 
								"printer-state" => array("tag" => "enum"),// type1 enum
								"printer-state-reasons" => array("tag" => "keyword"),//1setOf type2 keyword
								"printer-state-message" => array("tag" => "textWithoutLanguage"),//text
								"operations-supported" => array("tag" => "enum"),//1setOf type2 enum
								"charset-configured" => array("tag" => "charset"),
								"charset-supported" => array("tag" => "charset"),//1setOf charset
								"natural-language-configured" => array("tag" => "naturalLanguage"),
								"generated-natural-language-supported" => array("tag" => "naturalLanguage"),//1setOf naturallanguage
								"document-format-default" => array("tag" => "mimeMediaType"),
								"document-format-supported" => array("tag" => "mimeMediaType"),//1setOf mimemediatype								
								"printer-is-accepting-jobs" => array("tag" => "boolean"),
								"queued-job-count" => array("tag" => "integer"),
								"printer-message-from-operator" => array("tag" => "text"),//??
								"color-supported" => array("tag" => "boolean"),
								"reference-uri-schemes-supported" => array("tag" => "uriScheme"),//1setOf uriScheme
								"pdl-override-supported" => array("tag" => "keyword"),//type2 keyword
								"printer-up-time" => array("tag" => "integer"),
								"printer-current-time" => array("tag" => "datetime"),
								"multiple-operation-time-out" => array("tag" => "integer"),
								"compression-supported" => array("tag" => "keyword"),//1setOf type3 keyword
								"job-k-octets-supported" => array("tag" => "rangeOfInteger"), 
								"job-impressions-supported" => array("tag" => "rangeOfInteger"),
								"job-media-sheets-supported" => array("tag" => "rangeOfInteger"),
								"ipp-versions-supported" => array("tag" => "keyword"),//1.1 1setOf type2 keyword
								"compression-supported" => array("tag" => "keyword"),//1.1 1setof type3 keyword
								//CUPS....
								"printer-is-shared" => array("tag" => "boolean"),
								"server-is-sharing-printers" => array("tag" => "boolean"),
								"requesting-user-name-allowed" => array("tag" => "textWithoutLanguage"),//text
								"requesting-user-name-denied" => array("tag" => "textWithoutLanguage"),//text
								"auth-info-required" => array("tag" => "keyword"),
								"port-monitor" => array("tag" => "textWithoutLanguage"),
								"port-monitor-supported" => array("tag" => "textWithoutLanguage"),
								"auth-info-required" => array("tag" => "keyword"),//domain,none,username,password
								//.....
								"printer-op-policy" => array("tag" => "nameWithoutLanguage"),
								//.....
								"media-color" => array("tag" => "keyword"),
								"media-size" => array("tag" => "keyword"),
								"x-dimension" => array("tag" => "integer"),
								"y-dimension" => array("tag" => "integer"),						
                              );
        $this->printer_tags = array_merge ($this->printer_tags, $printer_tags);		
    }
	
	//override
    protected function _enumBuild ($tag,$value) {
        
        $value_built = parent::_enumBuild($tag,$value);
       

        switch ($tag) {
          case "job-state":
                           switch ($value) {
                             case 'pending':
                             $value_built = chr(3);//chr(0x00) . chr (0x03)
                             break;   
                             case 'pending-held':
                             $value_built = chr(4);
                             break;  
                             case 'proccesing':
                             $value_built = chr(5);
                             break;  							 
                             case 'proccesing-stopped':
                             $value_built = chr(6);
                             break; 
                             case 'canceled':
                             $value_built = chr(7);
                             break; 
                             case 'aborted':
                             $value_built = chr(8);
                             break;	
                             case 'completed':
                             $value_built = chr(9);
                             break;							 
                           }
                           break;
						   
          case "printer-state":
                           switch ($value) {
                             case 'idle':
                             $value_built = chr(0x00) . chr(0x03);//chr(3);
                             break;   
                             case 'proccesing':
                             $value_built = chr(0x00) . chr(0x04);//chr(4);
                             break;  
                             case 'stopped':
                             $value_built = chr(0x00) . chr(0x05);//chr(5);
                             break;  							 					 
                           }
                           break;	


          case "operations-supported":
                           switch ($value) {
                             case 'Print-Job':
                             $value_built = chr(0x00) . chr(0x02);
                             break;   
                             case 'Print-URI':
                             $value_built = chr(0x00) . chr(0x03);
                             break;  
                             case 'Validate-Job':
                             $value_built = chr(0x00) . chr(0x04);
                             break; 							 
                             case 'Create-Job':
                             $value_built = chr(0x00) . chr(0x05);
                             break;
                             case 'Send-Document':
                             $value_built = chr(0x00) . chr(0x06);
                             break;
                             case 'Send-URI':
                             $value_built = chr(0x00) . chr(0x07);
                             break;	
                             case 'Cancel-Job':
                             $value_built = chr(0x00) . chr(0x08);
                             break;		
                             case 'Get-Job-Attributes':
                             $value_built = chr(0x00) . chr(0x09);
                             break;		
                             case 'Get-Jobs':
                             $value_built = chr(0x00) . chr(0x0A);
                             break;		
                             case 'Get-Printer-Attributes':
                             $value_built = chr(0x00) . chr(0x0B);
                             break;									 
                           }
                           break;						   
						   	
          case "cpi":     
                           switch ($value) {
                             case '10':
                             $value_built = chr(10);
                             break;
                             case '12':
                             $value_built = chr(12);
                             break;
                             case '17':
                             $value_built = chr(17);
                             break;
                             default:
                             $value_built = chr(10);
                           }
                           break;
          case "lpi":
                           switch ($value) {
                             case '6':
                             $value_built = chr(6);
                             break;
                             case '8':
                             $value_built = chr(8);
                             break;
                             default:
                             $value_built = chr(6);
                           }
                           break;						
        }

        $prepend = '';
        while ((strlen($value_built) + strlen($prepend)) < 4)
            $prepend .= chr(0);
    return $prepend.$value_built;
    }	

 //
 //collections
 //
 
 function  setCollectionAttribute($collection, $attribute, $values) {
  $operation_attributes_tags = array_keys($this->operation_tags);
  $job_attributes_tags = array_keys($this->job_tags);
  $printer_attributes_tags = array_keys($this->printer_tags);
  self::unsetCollectionAttribute($attribute);
  
  if (in_array($attribute, $operation_attributes_tags)) 
  {
    self::_setOperationCollectionAttribute($collection, $attribute, $values);
  }
  elseif (in_array($attribute, $job_attributes_tags)) 
  {
    self::_setJobCollectionAttribute($collection, $attribute, $values);	
  }
  elseif (in_array($attribute, $printer_attributes_tags)) 
  {
    self::_setPrinterCollectionAttribute($collection, $attribute, $values);
  }
  else
  {
   return FALSE;
  }
 }
 
 protected function _setJobCollectionAttribute($collection, $attribute, $value) 
 {
  //used by setCollectionAttribute
  
  self::_setJobAttribute($attribute, $value);
  
  $this->job_tags[$attribute]['collection'] = $collection;
 } 
 
 protected function _setOperationCollectionAttribute($collection, $attribute, $value) 
 {
  //used by setCollectionAttribute
  
  self::_setOperationAttribute($attribute, $value);
  
  $this->operation_tags[$attribute]['collection'] = $collection;  
 } 
 
 protected function _setPrinterCollectionAttribute($collection, $attribute, $value) 
 {
  //used by setCollectionAttribute

  self::_setPrinterAttribute($attribute, $value);
  
  $this->printer_tags[$attribute]['collection'] = $collection;  
 }
 
 protected function _buildCollectionValues(&$operationattributes, &$jobattributes, &$printerattributes) 
 {
  $operationattributes = array();
  foreach($this->operation_tags as $key => $values) 
  {
   //build if collection tag
   if ( (array_key_exists('value', $values)) && (array_key_exists('collection', $values)))
   {
    $collection = $values['collection']; 
    foreach($values['value'] as $item_value) 
    {
	 if (is_array($item_value)) {

        foreach ($item_value as $subvalue) 
		{
          if (array_key_exists($subvalue, $this->operation_tags))
          {		  
            $operationattributes[$collection][$key][$subvalue] =
	        $this->printer_tags[$subvalue]['systag']
	        . self::_giveMeStringLength('')
	        . self::_giveMeStringLength($this->operation_tags[$subvalue]['value'][0]) 
	        . $this->operation_tags[$subvalue]['value'][0];

			//unset attribute when copy 
            self::unsetCollectionAttribute($subvalue);	//unset tag values*			
		  }	
		}  
     }	
     elseif (array_key_exists('value', $this->printer_tags[$key])) 
	 { //if values are unset*(not double entries)	 	 
	   $operationattributes[$collection][$key] =
	   $values['systag']
	   . self::_giveMeStringLength('')
	   . self::_giveMeStringLength($item_value) 
	   . $item_value;
	 }  
    }
   }
  }
  $jobattributes = array();
  foreach($this->job_tags as $key => $values) 
  {
   //build if collection tag
   if ((array_key_exists('value', $values)) && (array_key_exists('collection', $values)))
   {
    $collection = $values['collection']; 
    foreach($values['value'] as $item_value) 
    {
	 if (is_array($item_value)) {

        foreach ($item_value as $subvalue) 
		{
          if (array_key_exists($subvalue, $this->job_tags))
          {		  
            $jobattributes[$collection][$key][$subvalue] =
	        $this->job_tags[$subvalue]['systag']
	        . self::_giveMeStringLength('')
	        . self::_giveMeStringLength($this->job_tags[$subvalue]['value'][0]) 
	        . $this->job_tags[$subvalue]['value'][0];

			//unset attribute when copy 
            self::unsetCollectionAttribute($subvalue);	//unset tag values*			
		  }	
		}  
     }
     elseif (array_key_exists('value', $this->job_tags[$key])) 
	 { //if values are unset*(not double entries)	 	 
	   $jobattributes[$collection][$key] =
	   $values['systag']
	   . self::_giveMeStringLength('')
	   . self::_giveMeStringLength($item_value) 
	   . $item_value;
	 } 
    }
   }
  }
  $printerattributes = array();
  foreach($this->printer_tags as $key => $values) 
  {
   //build if collection tag
   if ((array_key_exists('value', $values)) && (array_key_exists('collection', $values))) 
   {
    $collection = $values['collection']; 
    foreach($values['value'] as $item_value) 
    {
	 if (is_array($item_value)) {

        foreach ($item_value as $subvalue) 
		{
          if (array_key_exists($subvalue, $this->printer_tags))
          {		  
            $printerattributes[$collection][$key][$subvalue] =
	        $this->printer_tags[$subvalue]['systag']
	        . self::_giveMeStringLength('')
	        . self::_giveMeStringLength($this->printer_tags[$subvalue]['value'][0]) 
	        . $this->printer_tags[$subvalue]['value'][0];

			//unset attribute when copy 
            self::unsetCollectionAttribute($subvalue);	//unset tag values*			
		  }	
		}  
     }
     elseif (array_key_exists('value', $this->printer_tags[$key])) 
	 { //if values are unset*(not double entries)	 
	   $printerattributes[$collection][$key] =
	   $values['systag']
	   . self::_giveMeStringLength('')
	   . self::_giveMeStringLength($item_value) 
	   . $item_value;
	 }
    }
   }
  }
  reset($this->job_tags);
  reset($this->operation_tags);
  reset($this->printer_tags);
  return true;
 } 

 protected function _buildCollections(&$o_collections, &$j_collections, &$p_collections) 
 {
  $o_collections = '';
  $j_collections = '';
  $p_collections = '';
  
  self::_buildCollectionValues($operationcollections, $jobcollections, $printercollections);
  $ocollections = var_export($operationcollections,1) . "\r\n";
  $jcollections = var_export($jobcollections,1) . "\r\n";
  $pcollections = var_export($printercollections,1) . "\r\n";  
  self::write2disk('collection.log',"\r\n".$ocollections . $jcollections . $pcollections);	 
  
  foreach ($operationcollections as $collection_name=>$collection_value) 
  {
    //begCollection
    $o_collections .= chr(0x34) .
	                  self::_giveMeStringLength($collection_name) . $collection_name . chr(0x00) . chr(0x00);
	

	//memberAttrName
	foreach ($collection_value as $memberAttrName=>$value) 
	{
	  if (is_array($value)) //infold collection
	  { 
	    //sub begCollection
        $o_collections .= chr(0x4A) 
		                  . chr(0x00) . chr(0x00)
						  . self::_giveMeStringLength($memberAttrName) 
						  . $memberAttrName
		                  . chr(0x34)
	                      . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
	    //sub memberAttrName					   
	    foreach ($value as $sub_memberAttrName=>$sub_value)
		{
	      $o_collections .= chr(0x4A)
	  	                 . self::_giveMeStringLength('')
	                     . self::_giveMeStringLength($sub_memberAttrName) 
	                     . $sub_memberAttrName
	                     . $sub_value;		
		}
		//sub endCollection
		$o_collections .= chr(0x37) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);	
	  }
	  else { //
	    $o_collections .= chr(0x4A)
	  	               . self::_giveMeStringLength('')
	                   . self::_giveMeStringLength($memberAttrName) 
	                   . $memberAttrName
	                   . $value;			   
      }
	}  
	//endCollection
    $o_collections .= chr(0x37) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);	
  }
  
  foreach ($jobcollections as $collection_name=>$collection_value) {
    //begCollection
    $j_collections .= chr(0x34) .
	                  self::_giveMeStringLength($collection_name) . $collection_name . chr(0x00) . chr(0x00);
	

	//memberAttrName
	foreach ($collection_value as $memberAttrName=>$value)
	{
	  if (is_array($value)) //infold collection
	  { 
	    //sub begCollection
        $j_collections .= chr(0x4A) 
		                  . chr(0x00) . chr(0x00)
						  . self::_giveMeStringLength($memberAttrName) 
						  . $memberAttrName
		                  . chr(0x34)
	                      . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
	    //sub memberAttrName					   
	    foreach ($value as $sub_memberAttrName=>$sub_value)
		{
	      $j_collections .= chr(0x4A)
	  	                 . self::_giveMeStringLength('')
	                     . self::_giveMeStringLength($sub_memberAttrName) 
	                     . $sub_memberAttrName
	                     . $sub_value;		
		}
		//sub endCollection
		$j_collections .= chr(0x37) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);	
	  }
	  else 
	  { //one memberAttrName		
	    $j_collections .= chr(0x4A)
	  	               . self::_giveMeStringLength('')
	                   . self::_giveMeStringLength($memberAttrName) 
	                   . $memberAttrName
	                   . $value;	
      }						
    }
	//endCollection
    $j_collections .= chr(0x37) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);	
  }

  foreach ($printercollections as $collection_name=>$collection_value) {
    //begCollection
    $p_collections .= chr(0x34) .
	                  self::_giveMeStringLength($collection_name) . $collection_name . chr(0x00) . chr(0x00);
	

	//memberAttrName
	foreach ($collection_value as $memberAttrName=>$value)
	{
	  if (is_array($value)) //infold collection
	  { 
	    //sub begCollection
        $p_collections .= chr(0x4A) 
		                  . chr(0x00) . chr(0x00)
						  . self::_giveMeStringLength($memberAttrName) 
						  . $memberAttrName
		                  . chr(0x34)
	                      . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);
	    //sub memberAttrName					   
	    foreach ($value as $sub_memberAttrName=>$sub_value)
		{
	      $p_collections .= chr(0x4A)
	  	                 . self::_giveMeStringLength('')
	                     . self::_giveMeStringLength($sub_memberAttrName) 
	                     . $sub_memberAttrName
	                     . $sub_value;		
		}
		//sub endCollection
		$p_collections .= chr(0x37) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);	
	  }
	  else 
	  { //one memberAttrName	
	    $p_collections .= chr(0x4A)
	  	               . self::_giveMeStringLength('')
	                   . self::_giveMeStringLength($memberAttrName) 
	                   . $memberAttrName
	                   . $value;
      }						
    }
	//endCollection
    $p_collections .= chr(0x37) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00);	
  }  
  
  return true;  
 } 
 
 
 public function unsetCollectionAttribute($attribute) 
 {
  $operation_attributes_tags = array_keys($this->operation_tags);
  $job_attributes_tags = array_keys($this->job_tags);
  $printer_attributes_tags = array_keys($this->printer_tags);
  if (in_array($attribute, $operation_attributes_tags)) 
  {
   unset( 
     $this->operation_tags[$attribute]['value'],
     $this->operation_tags[$attribute]['systag'],
     $this->operation_tags[$attribute]['collection']	 
        );
  }
  elseif (in_array($attribute, $job_attributes_tags))
  {
   unset(
     $this->job_tags[$attribute]['value'],
     $this->job_tags[$attribute]['systag'],
     $this->job_tags[$attribute]['collection']
        );
  }
  elseif (in_array($attribute, $printer_attributes_tags))
  {
   unset(
     $this->printer_tags[$attribute]['value'],
     $this->printer_tags[$attribute]['systag'],
     $this->printer_tags[$attribute]['collection']
        );
  }
  else 
  {
   trigger_error(
     sprintf(self::_dummy('unsetCollectionAttribute: Tag "%s" is not a printer or a job attribute'),
      $attribute) , E_USER_NOTICE);
   self::_putDebug(
     sprintf(self::_dummy('unsetCollectionAttribute: Tag "%s" is not a printer or a job attribute'),
      $attribute) , 3);
   self::_errorLog(
     sprintf(self::_dummy('unsetCollectionAttribute: Tag "%s" is not a printer or a job attribute'),
      $attribute) , 2);
   return FALSE;
  }
  return true;
 }

	
};
?>
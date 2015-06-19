<?php

require_once('skeleton.php');

/*
http://www.beyrent.net/category/categories/web-services
*/


class livedocx extends skeleton {
 
 protected $soap;
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
	/*if ($this->fp = fopen($this->jf, "r+b")) {
	  $this->import_data = fread($this->fp, filesize($job_file));
	  //fclose($this->fp);
	} */
    //$this->import_data = 'Alexiou Vassilis'; //'Αλεξιου Βασίλης', //must be utf-8 (php file encode) 	
  
    // Turn off WSDL caching
    ini_set ('soap.wsdl_cache_enabled', 0);
     
    // Define credentials for LD
    $credentials = array(
      'username' => 'balexiou',
      'password' => 'basil!72',
    );
     
    // SOAP WSDL endpoint
    $endpoint = 'https://api.livedocx.com/1.2/mailmerge.asmx?WSDL';
     
    // Define timezone
    date_default_timezone_set('Europe/Athens');

    // Create a new instance of the SoapClient object
    $this->soap = new SoapClient($endpoint);
    $this->soap->LogIn(
      array(
        'username' => $credentials['username'],
        'password' => $credentials['password']
      )
    );
 
    self::write2disk('livedocx.log',"INIT\r\n");	
 }
 
 //override
 public function execute() {
 
    //get data
	$farray = array();
	
	$pparts = explode('blank',$this->import_data);
	
	$fparts = explode(' ', $pparts[0]);//$this->import_data);
	$xi = 0;
	foreach ($fparts as $i=>$f) {
	    if (trim($f)) {
	        if (strstr($f,':')) {
	            $ff = explode(':',$f);
				if ($field = trim($ff[1])) {
	              $farray[$ff[0]] = $field;//$ff[1];
				  $last_element = $ff[0];
				  //save also by id
				  $farray[$xi] = $field;//$ff[1];
				  $last_element_id = $xi;
				  $xi+=1;
				}  
	        }
	        else {
	            //$farray[] = $f; 
			    $farray[$last_element] .= ' '.$f; //prev rec field if no :
				//save also by id
				$farray[$last_element_id] .= ' '.$f;
			}	
	    }	 
	}   
	$farray[] = $pparts[1];
	//process
	//.....
 	
	//if ($bytes=self::example1())	
	if ($bytes=self::example0($farray))
	  return ($bytes);
    //else	  
	  //return ($this->import_data); //as is
	
    return false;	
 }
 
 protected function example0($mapfields=null) {
 
    $test = 'START>>>' . implode('>>>',$mapfields) . '>>>EXPORT:'. var_export($mapfields,true);
 
    // Upload template
    $path_to_template = $this->jobs_path . '/text_template.docx';
	
    $data = file_get_contents($path_to_template);
	
    if(empty($data)) {
      return false;
    }
	self::write2disk('livedocx.log',$path_to_template."\r\n");
	
    $this->soap->SetLocalTemplate(
    array(
        'template' => base64_encode($data),
        'format'   => 'docx'
    ));
	
	
    //retrieve from $this->import_data.....   
    // Assign data to template
 
    $fieldValues = array (
	'product01' => iconv("ISO-8859-7", "UTF-8",array_pop($mapfields)),
	'cuscode' => iconv("ISO-8859-7", "UTF-8",$mapfields[0]),
	'cusname' => iconv("ISO-8859-7", "UTF-8",$mapfields[1].' '.$mapfields[2]),
	/*'cusaddress' => iconv("ISO-8859-7", "UTF-8",$mapfields[3].' '.$mapfields[4]),
	'cusps' => iconv("ISO-8859-7", "UTF-8",$mapfields[5].' '.$mapfields[6]),
	'cusmail' => iconv("ISO-8859-7", "UTF-8",$mapfields[7].' '.$mapfields[8]),
	'custel' => iconv("ISO-8859-7", "UTF-8",$mapfields[9].' '.$mapfields[10]),
	'cusfax' => iconv("ISO-8859-7", "UTF-8",$mapfields[11].' '.$mapfields[12]),
	'cusmob' => iconv("ISO-8859-7", "UTF-8",$mapfields[13]),*/
    /*'product01' => iconv("ISO-8859-7", "UTF-8",$mapfields[0]),
    'product02' => iconv("ISO-8859-7", "UTF-8",$mapfields[1]),*/	
    'company'   => iconv("ISO-8859-7", "UTF-8", $test),
	'licensee' => iconv("ISO-8859-7", "UTF-8", $this->import_data),
    'date'     => date('F d, Y'),
    'time'     => date('H:i:s'),
    'city'     => 'Thessaloniki',
    'country'  => 'Hellas'
    );
 
	
    $this->soap->SetFieldValues(
    array (
        'fieldValues' => self::assocArrayToArrayOfArrayOfString($fieldValues)
    )
    );	
	
    // Build the document
 
    $this->soap->CreateDocument();
 
    // Get document as PDF
    $result = $this->soap->RetrieveDocument(
    array(
        'format' => 'pdf'
    ));
	
    // Fetch the document
    $data = $result->RetrieveDocumentResult;	
	
	/*fseek($this->fp, 0);	
	$bytes = fwrite($this->fp, base64_decode($data));//, $length);
	fclose($this->fp);	*/
	self::_write(base64_decode($data));
	
    $this->soap->LogOut();	
	
    return ($bytes);		
 }
 
 //
 // SAMPLE #1 - License Agreement
 //
 protected function example1() {
 
    // Upload template
    $path_to_template = $this->jobs_path . '/text_template.docx';//'/license-agreement-template.docx';
	
    $data = file_get_contents($path_to_template);
	
    if(empty($data)) {
      return false;
    }
	
	self::write2disk('livedocx.log',$path_to_template."\r\n");
	
    $this->soap->SetLocalTemplate(
    array(
        'template' => base64_encode($data),
        'format'   => 'docx'
    ));
   
    //retrieve from $this->import_data.....   
    // Assign data to template
 
    $fieldValues = array (
    /*'software' => 'Magic Graphical Compression Suite v2.5',
    'licensee' => 'Henry Doner-Meyer',
    'company'  => 'Megasoft Co-Operation',*/
	'licensee' => iconv("ISO-8859-7", "UTF-8", $this->import_data),
    'date'     => date('F d, Y'),
    'time'     => date('H:i:s'),
    'city'     => 'Thessaloniki',
    'country'  => 'Hellas'
    );
 
	
    $this->soap->SetFieldValues(
    array (
        'fieldValues' => self::assocArrayToArrayOfArrayOfString($fieldValues)
    )
    );
 
    // Build the document
 
    $this->soap->CreateDocument();
 
    // Get document as PDF
    $result = $this->soap->RetrieveDocument(
    array(
        'format' => 'pdf'
    ));
	
    // Fetch the document
    $data = $result->RetrieveDocumentResult;
	
	//save data as pdf 
	//$bytes = self::_write(base64_decode($data));
	//$bytes = $this->_write(base64_decode($data));
	
	//fseek($this->fp, 0);
	//this->fp ????
	//if ($this->fp = fopen($this->jf, "r+b")) //already open
	//append pdf data to text...if fseek o start from beginning of file
	//$bytes = fwrite($this->fp, base64_decode($data));//, $length);
	self::_write(base64_decode($data));
	
	//ftruncate($this->fp, $length);
	//fclose($this->fp);
	
	//self::write2disk('livedocx.log',base64_decode($data));
	
	//self::aaaa_bbbb();		
	
    // Log out (closes connection to backend server)
    $this->soap->LogOut();	
	
    return ($bytes);	
 }
 
 //
 // SAMPLE #2 - Telephone Bill
 //
 protected function example2() {
 
    // Upload template
    $data = file_get_contents($this->jobs_path . '/telephone-bill-template.doc');
 
    // Assign field values data to template
    $this->soap->SetLocalTemplate(
    array(
        'template' => base64_encode($data),
        'format'   => 'doc'
    )
    );
 
    $fieldValues = array (
    'customer_number' => sprintf("#%'10s",  rand(0,1000000000)),
    'invoice_number'  => sprintf("#%'10s",  rand(0,1000000000)),
    'account_number'  => sprintf("#%'10s",  rand(0,1000000000)),
    'phone'           => '+49 421 335 9000',
    'date'            => date('F d, Y'),
    'name'            => 'James Henry Brown',
    'service_phone'   => '+49 421 335 910',
    'service_fax'     => '+49 421 335 9180',
    'month'           => date('F Y'),
    'monthly_fee'     =>  '€ 15.00',
    'total_net'       => '€ 100.00',
    'tax'             =>      '19%',
    'tax_value'       =>  '€ 15.00',
    'total'           => '€ 130.00'
    );
 
    $this->soap->SetFieldValues(
    array (
        'fieldValues' => assocArrayToArrayOfArrayOfString($fieldValues)
    )
    );
 
    // Assign block field values data to template
 
    $blockFieldValues = array (
    array ('connection_number' => '+49 421 335 912', 'connection_duration' => '00:00:07', 'fee' => '€ 0.03'),
    array ('connection_number' => '+49 421 335 913', 'connection_duration' => '00:00:07', 'fee' => '€ 0.03'),
    array ('connection_number' => '+49 421 335 914', 'connection_duration' => '00:00:07', 'fee' => '€ 0.03'),
    array ('connection_number' => '+49 421 335 916', 'connection_duration' => '00:00:07', 'fee' => '€ 0.03')
    );
 
    $this->soap->SetBlockFieldValues(
    array (
        'blockName'        => 'connection',
        'blockFieldValues' => multiAssocArrayToArrayOfArrayOfString($blockFieldValues)
    )
    );
 
    // Build the document
    $this->soap->CreateDocument();
 
    // Get document as PDF
    $result = $this->soap->RetrieveDocument(
    array(
        'format' => 'pdf'
    )
    );
 
    $data = $result->RetrieveDocumentResult;
 
    //file_put_contents('./telephone-bill-document.pdf', base64_decode($data));
	$bytes = self::_write(base64_decode($data));
 
    // Log out (closes connection to backend server)
    $this->soap->LogOut();
	
    return ($bytes);  
 }
 
 //
 // SAMPLE #3 - Supported Formats
 //
 protected function example3 () {
 
    $ret = 'Starting sample #3 (supported-formats)...' . PHP_EOL;
 
    // Get an object containing an array of supported template formats
    $result = $this->soap->GetTemplateFormats();
 
    $ret .= sprint(PHP_EOL . 'Template format (input):' . PHP_EOL);
 
    foreach ($result->GetTemplateFormatsResult->string as $format) {
       $ret .= sprintf('- %s%s', $format, PHP_EOL);
    }
 
    // Get an object containing an array of supported document formats
    $ret .= sprint(PHP_EOL . 'Document format (output):' . PHP_EOL);
 
    $result = $this->soap->GetDocumentFormats();
 
    foreach ($result->GetDocumentFormatsResult->string as $format) {
       $ret .= sprintf('- %s%s', $format, PHP_EOL);
    }
 
    // Get an object containing an array of supported image formats
    $ret .= sprint(PHP_EOL . 'Image format (output):' . PHP_EOL);
 
    $result = $this->soap->GetImageFormats();
 
    foreach ($result->GetImageFormatsResult->string as $format) {
       $ret .= sprintf('- %s%s', $format, PHP_EOL);
    }
 
    $ret .= sprint(PHP_EOL . 'DONE.' . PHP_EOL);
	
	self::write2disk('livedocx.log',$ret);	
 
    // Log out (closes connection to backend server)
    $this->soap->LogOut();
  
 }
 
 //
 // SAMPLE #4 - Supported Formats
 // 
 protected function example4() {
 
    $ret = 'Starting sample #4 (supported-fonts)...' . PHP_EOL; 
 
    // Get an object containing an array of supported fonts 
    $result = $this->soap->GetFontNames();
 
    foreach ($result->GetFontNamesResult->string as $format) {
      $ret .= sprintf('- %s%s', $format, PHP_EOL);
    }
 
    $ret .= sprint(PHP_EOL . 'DONE.' . PHP_EOL);
	
	self::write2disk('livedocx.log',$ret);		
 
    // Log out (closes connection to backend server)
    $this->soap->LogOut();
 }
 
 protected function aaaa_bbbb() {
    // Get document as bitmaps (one per page)
 
    $result = $soap->GetAllBitmaps(
    array(
        'zoomFactor' => 100,
        'format'     => 'png'
    )
    );
 
    $data = array();
 
    if (isset($result->GetAllBitmapsResult->string)) {
    $pageCounter = 1;
    if (is_array($result->GetAllBitmapsResult->string)) {
        foreach ($result->GetAllBitmapsResult->string as $string) {
            $data[$pageCounter] = base64_decode($string);
            $pageCounter++;
        }
    } else {
       $data[$pageCounter] = base64_decode($result->GetAllBitmapsResult->string);
    }
    }
 
    foreach ($data as $pageCounter => $pageData) {
    $pageFilename = sprintf('./license-agreement-document-page-%s.png', $pageCounter);
    file_put_contents($pageFilename, $pageData);    
    }
 
    // Get document as Windows metafiles (one per page)
 
    $result = $soap->GetAllMetafiles();
 
    $data = array();
 
    if (isset($result->GetAllMetafilesResult->string)) {
    $pageCounter = 1;
    if (is_array($result->GetAllMetafilesResult->string)) {
        foreach ($result->GetAllMetafilesResult->string as $string) {
            $data[$pageCounter] = base64_decode($string);
            $pageCounter++;
        }
    } else {
       $data[$pageCounter] = base64_decode($result->GetAllMetafilesResult->string);
    }
    }
 
    foreach ($data as $pageCounter => $pageData) {
    $pageFilename = sprintf('./license-agreement-document-page-%s.wmf', $pageCounter);
    file_put_contents($pageFilename, $pageData);    
    }
  
 }
 
 

/**
 * Convert a PHP assoc array to a SOAP array of array of string
 *
 * @param array $assoc
 * @return array
 */
function assocArrayToArrayOfArrayOfString ($assoc) {
  $arrayKeys   = array_keys($assoc);
  $arrayValues = array_values($assoc);
  return array ($arrayKeys, $arrayValues);
}
 
/**
 * Convert a PHP multi-depth assoc array to a SOAP array of array of array of string
 *
 * @param array $multi
 * @return array
 */
function multiAssocArrayToArrayOfArrayOfString ($multi){
    $arrayKeys   = array_keys($multi[0]);
    $arrayValues = array();
 
    foreach ($multi as $v) {
      $arrayValues[] = array_values($v);
    }
 
    $_arrayKeys = array();
    $_arrayKeys[0] = $arrayKeys;
 
    return array_merge($_arrayKeys, $arrayValues);
} 
}
?>
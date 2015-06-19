<?php

define('PDF_MAGIC', "\\x25\\x50\\x44\\x46\\x2D"); 

class pdffactory {

    protected $pdf_data, $pdffile, $x_echo;
	protected $pdf_passthrough_driver;
	protected $qrcode_image_file;

    function __construct($pdfdrv=false, $qrcodefile=null) {
	
	    $this->pdf_data = null;
		$this->pdffile = null;
		$this->x_echo = true;//false; //log..
		
        $this->pdf_passthrough_driver = $pdfdrv ? $pdfdrv : false;	
        $this->qrcode_image_file = $qrcodefile ? $qrcodefile : null;			
	}
	
	private function pdf_echo($message=null) {	
	
	    if (!$this->x_echo) return false;
	
	    $file = $this->pdffile ? $this->pdffile : 'con.pdf';
	    $msg = $message ? $message : $this->pdf_data;
	
		if ($fp = fopen(str_replace('.pdf','.log',$file), "a+")) {
						
		    $ok = fwrite($fp, $msg, strlen($msg));
            fclose($fp);
		    return $ok ? true : false;						
        }	
        return false;		  
	}	
	
    protected function is_pdf($filename) { 
        return (file_get_contents($filename, false, null, 0, strlen(PDF_MAGIC)) === PDF_MAGIC) ? true : false; 
    } 	
	

	
	
	
// Function    : pdf2txt()
	
//there is some more fix from luc pdf2text function. It really works at my tasks.

//Two fixes:
//1) Different platforms set different characters after start "stream" text, for example: "stream\n", "stream\r", "stream\r\n". So, we detect it first.
//2) Some non-text blocks are detected as text, so we added a function "FilterNonText".

protected function FilterNonText($data) {
  for($i=1;$i<9;$i++) {
      if(strpos($data, chr($i)) !== false) {
         return ""; // not text, something strange
      }
  }
  return $data;
}

	
//I am trying to extract the text from PDF files and use it to feed a search engine (Intranet tool). 
//I tried several functions "PDF2TXT" posted below, but not they do not produce the expected result. 
//At least, all words need to be separated by spaces (then used as keywords), and the "junk" codes removed 
//(for example: binary data, pictures...). I start modifying the interesting function posted by Swen, 
//and here is the my current version that starts to work quite well (with PDF version 1.2). 
//Sorry for having a quite different style of programming. Luc	
	
// New function, replacing old "pd2txt" function
protected function PS2Text_New($PS_Data)
{
//global $TCodeReplace;
// Global table for codes replacement 
$TCodeReplace = array ('\(' => '(', '\)' => ')');

// Catch up some codes
if (ord($PS_Data[0]) < 10) return ''; 
if (substr($PS_Data, 0, 8) == '/CIDInit') return '';

// Some text inside (...) can be found outside the [...] sets, then ignored 
// => disable the processing of [...] is the easiest solution

$Result = $this->ExtractPSTextElement($PS_Data);

// echo "Code=$PS_Data\nRES=$Result\n\n";

// Remove/translate some codes
return strtr($Result, $TCodeReplace);
}	
	
// New function - Extract text from PS codes
protected function ExtractPSTextElement($SourceString)
{
$CurStartPos = 0;
while (($CurStartText = strpos($SourceString, '(', $CurStartPos)) !== FALSE)
    {
    // New text element found
    if ($CurStartText - $CurStartPos > 8) $Spacing = ' ';
    else    {
        $SpacingSize = substr($SourceString, $CurStartPos, $CurStartText - $CurStartPos);
        if ($SpacingSize < -25) $Spacing = ' '; else $Spacing = '';
        }
    $CurStartText++;

    $StartSearchEnd = $CurStartText;
    while (($CurStartPos = strpos($SourceString, ')', $StartSearchEnd)) !== FALSE)
        {
        if (substr($SourceString, $CurStartPos - 1, 1) != '\\') break;
        $StartSearchEnd = $CurStartPos + 1;
        }
    if ($CurStartPos === FALSE) break; // something wrong happened
    
    // Remove ending '-'
    if (substr($Result, -1, 1) == '-')
        {
        $Spacing = '';
        $Result = substr($Result, 0, -1);
        }

    // Add to result
    $Result .= $Spacing . substr($SourceString, $CurStartText, $CurStartPos - $CurStartText);
    $CurStartPos++;
    }
// Add line breaks (otherwise, result is one big line...)
return $Result . "\n";
}	
	
	
// Function    : pdf2txt()
// Arguments   : $filename - Filename of the PDF you want to extract
// Description : Reads a pdf file, extracts data streams, and manages
//               their translation to plain text - returning the plain
//               text at the end
// Authors      : Jonathan Beckett, 2005-05-02
//              : Sven Schuberth, 2007-03-29

protected function pdf2txt($filename){

    $data = $this->getFileData($filename);
    
    $s=strpos($data,"%")+1;
    
    $version=substr($data,$s,strpos($data,"%",$s)-1);
    if(substr_count($version,"PDF-1.2")==0)
        return $this->handleV3($data);
    else
        return $this->handleV2($data);

    
}
// handles the verson 1.2
protected function handleV2($data){
        
    // grab objects and then grab their contents (chunks)
    $a_obj = getDataArray($data,"obj","endobj");
    
    foreach($a_obj as $obj){
        
        $a_filter = $this->getDataArray($obj,"<<",">>");
    
        if (is_array($a_filter)){
            $j++;
            $a_chunks[$j]["filter"] = $a_filter[0];

            $a_data = $this->getDataArray($obj,"stream\r\n","endstream");
            if (is_array($a_data)){
                $a_chunks[$j]["data"] = substr($a_data[0],
strlen("stream\r\n"),
strlen($a_data[0])-strlen("stream\r\n")-strlen("endstream"));
            }
        }
    }

    // decode the chunks
    foreach($a_chunks as $chunk){

        // look at each chunk and decide how to decode it - by looking at the contents of the filter
        $a_filter = split("/",$chunk["filter"]);
        
        if ($chunk["data"]!=""){
            // look at the filter to find out which encoding has been used            
            if (substr($chunk["filter"],"FlateDecode")!==false){
                $data =@ gzuncompress($chunk["data"]);
                if (trim($data)!=""){
                    $result_data .= $this->ps2txt($data);
					// CHANGED HERE PATCH #1, 
                    //$result_data .= $this->PS2Text_New($data);
					// CHANGED HERE PATCH #2, 
                    //$result_data .= $this->FilterNonText(PS2Text_New($data));
                } else {
                
                    //$result_data .= "x";
                }
            }
        }
    }
    
    return $result_data;
}

//handles versions >1.2
protected function handleV3($data){
    // grab objects and then grab their contents (chunks)
    $a_obj = $this->getDataArray($data,"obj","endobj");
    $result_data="";
    foreach($a_obj as $obj){
        //check if it a string
        if(substr_count($obj,"/GS1")>0){
            //the strings are between ( and )
            preg_match_all("|\((.*?)\)|",$obj,$field,PREG_SET_ORDER);
            if(is_array($field))
                foreach($field as $data)
                    $result_data.=$data[1];
        }
    }
    return $result_data;
}

protected function ps2txt($ps_data){
    $result = "";
    $a_data = $this->getDataArray($ps_data,"[","]");
    if (is_array($a_data)){
        foreach ($a_data as $ps_text){
            $a_text = $this->getDataArray($ps_text,"(",")");
            if (is_array($a_text)){
                foreach ($a_text as $text){
                    $result .= substr($text,1,strlen($text)-2);
                }
            }
        }
    } else {
        // the data may just be in raw format (outside of [] tags)
        $a_text = $this->getDataArray($ps_data,"(",")");
        if (is_array($a_text)){
            foreach ($a_text as $text){
                $result .= substr($text,1,strlen($text)-2);
            }
        }
    }
    return $result;
}

protected function getFileData($filename){
    $handle = fopen($filename,"rb");
    $data = fread($handle, filesize($filename));
    fclose($handle);
    return $data;
}

protected function getDataArray($data,$start_word,$end_word){

    $start = 0;
    $end = 0;
    unset($a_result);
    
    while ($start!==false && $end!==false){
        $start = strpos($data,$start_word,$end);
        if ($start!==false){
            $end = strpos($data,$end_word,$start);
            if ($end!==false){
                // data is between start and end
                $a_result[] = substr($data,$start,$end-$start+strlen($end_word));
            }
        }
    }
    return $a_result;
}	
	
}
?>	
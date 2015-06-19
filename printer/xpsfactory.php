<?php

class xpsfactory {

    protected $xps_data, $xpsfile, $x_echo;
	protected $xps_passthrough_driver;
	protected $qrcode_image_file;

    function __construct($xpsdrv=false, $qrcodefile=null) {
	
	    $this->xps_data = null;
		$this->xpsfile = null;
		$this->x_echo = true;//false; //log..
		
        $this->xps_passthrough_driver = $xpsdrv ? $xpsdrv : false;	
        $this->qrcode_image_file = $qrcodefile ? $qrcodefile : null;		
	}
	
	private function xps_echo($message=null) {	
	
	    if (!$this->x_echo) return false;
	
	    $file = $this->xpsfile ? $this->xpsfile : 'con.xps';
	    $msg = $message ? $message : $this->xps_data;
	
		if ($fp = fopen(str_replace('.xps','.log',$file), "a+")) {
						
		    $ok = fwrite($fp, $msg, strlen($msg));
            fclose($fp);
		    return $ok ? true : false;						
        }	
        return false;		  
	}
	
	public function xps_load($pathname, $resource) {

        if (@copy($resource, $pathname)) {//get a copy of sample.xps
		    
            $this->xpsfile = $pathname;
            $this->xps_echo("LOAD:".$pathname.PHP_EOL);		
			
            return true; 			
		}
		$this->xps_echo("LOAD_ERROR:".$pathname.PHP_EOL);	 
		return false;
	}
	
	//RESOURCES, images ...
	public function xps_resource_sign($signtext=null, $rf=null, $rt=null) {
	    if (!$signtext)
		    return false;
	
	    $this->xps_echo("START".PHP_EOL);
		
	    //read image data into file
		$ext = null; //return extension
	    $imagedata = $this->xps_get_resource($ext);
	    if ($imagedata) {
		    //$this->xps_echo("IMAGE DATA:".$imagedata.PHP_EOL);
	        $this->xps_text2img($signtext, $imagedata, $ext, 5);
		    $ret = $this->xps_zip_resource();
		}
		$this->xps_echo("END".PHP_EOL);
		return ($ret ? $ret : false);
    } 

	protected function xps_text2img($text=null, $string_img=null, $restype=null, $font_size=null) {
        $string = $text ? $text : '--';
		$restype = $restype ? $restype : '.JPG';			
		$filename = str_replace('.xps', $restype, $this->xpsfile);
		$fsize = $font_size ? $font_size : 3;
		
	    //$new_image = imagecreatetruecolor($xframe, $yframe);
		//$white = imagecolorallocate($new_image, 255, 255, 255);
		//imagefill($new_image, 0, 0, $white);		
		
		//$orange = imagecolorallocate($im, 220, 210, 60);
		$background_color = imagecolorallocate($im, 255, 255, 255);//white		
		
        //$im = imagecreatefrompng("images/button1.png");
		$im = imagecreatefromstring($string_img);

		//..add qr code image		
		if (is_file($this->qrcode_image_file)) {
		
			//list($width, $height) = getimagesize($this->qrcode_image_file);//source img width, height
			
            //get image resource
			$qrc_image = imagecreatefrompng($this->qrcode_image_file);
			
			//resize image according to y value of source xps image
			$qrc_resized = imagecreatetruecolor(imagesy($im), imagesy($im));
			imagecopyresampled($qrc_resized, $qrc_image, 0, 0, 0, 0, imagesy($im), imagesy($im), imagesx($qrc_image), imagesy($qrc_image));
			//put on source at left
			imagecopy($im, $qrc_resized, 0, 0, 0, 0, imagesx($qrc_resized), imagesy($qrc_resized));
			
			//put string next to qrc image
            $px = imagesx($qrc_resized);//(imagesx($im) - 7.5 * strlen($string)) / 2;
            imagestring($im, $fsize, $px, 9, $string, $background_color);			
		}
		else {//just put string at center
            $px = (imagesx($im) - 7.5 * strlen($string)) / 2;
            imagestring($im, $fsize, $px, 9, $string, $background_color);		
		}
		
		//save file
		switch ($restype) {
		  case '.GIF': imagegif($im, $filename); break;		
		  case '.PNG': imagepng($im, $filename); break;
		  case '.JPG':
          default    : imagejpeg($im, $filename);
		}
		
        imagedestroy($im);

		//get image contents 
		if ($data = @file_get_contents($filename)) {
            $this->xps_data = $data;		
			return true;
		}
		return false;
	}	
	
	protected function xps_get_resource(&$ext=null,$resfolder=null, $restype=null) {
	    $resfolder = $resfolder ? $resfolder : 'Images'; //Fonts
		$restype = $restype ? $restype : '.JPG';//upper letters !!!!
        $resarray = array('.JPG','.GIF','.PNG');		
		
        $zip = new ZipArchive; 
        $res = $zip->open($this->xpsfile); 	
		
        if (!$res) return false;
		
        $this->xps_echo("GET_RESOURCE".PHP_EOL);
		
		//10 image resource max get the last from 10 resource files..
		$p = 10;
		do {
			//$zfile = '/Documents/1/Resources/Images/2.JPG';
			//$zfile = 'Documents/1/Resources/'.$resfolder.'/'.$p.$restype;
			//$this->xps_echo($zfile.PHP_EOL);
			
			foreach ($resarray as $res) {
			    $zfile = 'Documents/1/Resources/'.$resfolder.'/'.$p.$res;
				if ($contents = $zip->getFromName($zfile)) {
				    $this->xps_echo('RESOURCE FILE:'.$zfile.PHP_EOL);
					$ext = $res; //return value
                    $zip->close();					
					return $contents; 
				}
			}
			
			$p--;
        }
        while ($p);	
		
        $zip->close();
		
        return false;		
	}
	
    protected function xps_zip_resource($resfolder=null, $restype=null) {
	    $resfolder = $resfolder ? $resfolder : 'Images'; //Fonts
		$restype = $restype ? $restype : '.JPG';
		$resarray = array('.JPG','.GIF','.PNG');
	
        $zip = new ZipArchive; 
        $res = $zip->open($this->xpsfile); 	
		
        if (!$res) return false;		
		
		//10 image resource max get the last..
		$p = 10;
		do {
			//$zfile = 'Documents/1/Resources/'.$resfolder.'/'.$p.$restype;
			//$this->xps_echo($zfile.PHP_EOL);
				
			foreach ($resarray as $res) {
			  $zfile = 'Documents/1/Resources/'.$resfolder.'/'.$p.$res;				
			  if ($g = $zip->getFromName($zfile)) {
			
                $this->xps_echo('MODIFY:'.$zfile.PHP_EOL); 
                //Delete the old...
                $zip->deleteName($zfile);
				
			    //$this->xps_echo('WRITE:'.$zfile.PHP_EOL); 
                //Write the new...
                $zip->addFromString($zfile, $this->xps_data);	
                $zip->close(); 

                return true;	
			  }
			}
			$p--;
        }
        while ($p);	
        $zip->close(); 

        return false;				
	}	
	
	//TEXT save text files ...
	public function xps_save($textfile) {
	
        if (($this->xpsfile) && is_readable($textfile)) { 
		
		    $this->xps_echo($textfile.PHP_EOL);
			
		    if ($this->xps_create_text_page($textfile)) {
			
			    $this->xps_echo('SAVE'.PHP_EOL);
				
		        return ($this->xps_zip_save());
		    }	
	    }
        return false;	
	}
	
	protected function xps_zip_save($page=null) {	
	    $p = $page ? $page : 1; //....
        $zip = new ZipArchive; 
        $res = $zip->open($this->xpsfile); 
	
        if (!$res) return false;

		if ($this->xps_passthrough_driver) {
		    //xps passthrough driver def..
		    $zfile = 'Documents/1/Pages/1.fpage/[0].piece';
		}	
		else //native ms xps def..
		    $zfile = 'Documents/1/Pages/1.fpage';
	
		    $this->xps_echo($zfile.PHP_EOL);
			
            //$this->xps_echo('DELETE:'.$zfile.PHP_EOL); 
            //Delete the old...
            $zip->deleteName($zfile);
				
			//$this->xps_echo('WRITE:'.$zfile.PHP_EOL); 
            //Write the new...
            $zip->addFromString($zfile, $this->xps_data);		


        //$zip->extractTo($extract_to, $extfiles); 
        $zip->close(); 

        return true;
		
	}	
	
	protected function xps_text_extract($page=null) {
	    $p = $page ? $page : 1; 

        $zip = new ZipArchive; 
        $res = $zip->open($this->xpsfile); 
	
        if (!$res) return false;

		do {
		    if ($this->xps_passthrough_driver) {
			    //xps passthrough driver de..
		        $zfile = "Documents/1/Pages/$p.fpage/[0].piece";
			}	
		    else //native ms xps def..
		        $zfile = "Documents/1/Pages/$p.fpage";
			
			//Read xml contents into memory
            $contents = $zip->getFromName($zfile);
			
			//search and replace
                //Delete the old...
                //$zip->deleteName($zname);
                //Write the new...
                //$zip->addFromString($zname, $mydata);			
			//
			//dec page
			$p--;
        }
        while ($p);		
	}
	
	protected function xps_create_text_page($textfile, $sx=null, $sy=null, $size=null) {
        if (!$textfile) return false; 
	    $lines = file($textfile);//, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if (empty($lines)) return false;
		
		$x = $sx ? $sx : 20;
		$y = $sy ? $sy : 20;
		$s = $size ? $size : 7;//9
		$spline = $s ? ($s+2) : 12;
		
		foreach ($lines as $i=>$line) {
		  
		    //$wl = $line ? iconv("ISO-8859-7", "UTF-8", str_replace(PHP_EOL,'',$line)) : ' '; 
			if (trim($line)) {
		        $wl = iconv("ISO-8859-7", "UTF-8", str_replace(PHP_EOL,'',$line));
			
		        $glyphs .= $this->xps_glyph('name'.$i, $x, $y, $s, $wl);
                $y+=$spline;			
			}
            else
                $y+=$spline;			
		}
		$this->xps_echo($glyphs.PHP_EOL);
		$this->xps_data = $this->xps_page($glyphs);
		
		return true;
	}		
	
	private function xps_page($glyphs) {
	
	    $xg = '<?xml version="1.0"?>
<FixedPage xmlns="http://schemas.microsoft.com/xps/2005/06" xmlns:x="http://schemas.microsoft.com/xps/2005/06/resourcedictionary-key" Height="386" Width="288" xml:lang="en-US">
<!-- Generated by: Microsoft XPS Object Model, Version: 1.0, Build: 6.1.7084.0 -->
<FixedPage.Resources>
<ResourceDictionary/>
</FixedPage.Resources>
<Canvas Name="textArea">'.
$glyphs .
'</Canvas>
</FixedPage>';

        $this->xps_echo($xg.PHP_EOL);
		
        return ($xg);		
	}
	
	private function xps_glyph($name,$xpos,$ypos,$size,$ustring=null) {	
	
	    $g ='<Glyphs FontUri="/Resources/Fonts/9D419573-5DF4-450A-9F07-A2A249FC2D5E.odttf" UnicodeString="'.$ustring.'" FontRenderingEmSize="'.$size.'" OriginX="'.$xpos.'" OriginY="'.$ypos.'" Name="'.$name.'" Fill="#000000"/>';
		return $g;
	}
	
 /*private function xps_read_xml($file=null) {
 
    $reader = new XMLReader(); 
	
    $reader->open($file);
    $odt_meta = array();
    while ($reader->read()) {
    if ($reader->nodeType == XMLREADER::ELEMENT) {
        $elm = $reader->name;
    } else {
        if ($reader->nodeType == XMLREADER::END_ELEMENT && $reader->name == 'office:meta') {
            break;
        }
        if (!trim($reader->value)) {
            continue;
        }
        $odt_meta[$elm] = $reader->value;
    }
    }
    print_r($odt_meta);	
 }*/	
}
?>
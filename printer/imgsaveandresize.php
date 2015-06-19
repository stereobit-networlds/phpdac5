<?php

//require_once('skeleton.php');
//require_once('ImageResize/SimpleImage.php');
/* POSTCRIPT PAGES
   Paper Size                      Dimension (in points)
   ------------------              ---------------------
   Comm #10 Envelope               297 x 684
   C5 Envelope                     461 x 648
   DL Envelope                     312 x 624
   Folio                           595 x 935
   Executive                       522 x 756
   Letter                          612 x 792
   Legal                           612 x 1008
   Ledger                          1224 x 792
   Tabloid                         792 x 1224
   A0                              2384 x 3370
   A1                              1684 x 2384
   A2                              1191 x 1684
   A3                              842 x 1191
   A4                              595 x 842
   A5                              420 x 595
   A6                              297 x 420
   A7                              210 x 297
   A8                              148 x 210
   A9                              105 x 148
   B0                              2920 x 4127
   B1                              2064 x 2920
   B2                              1460 x 2064
   B3                              1032 x 1460
   B4                              729 x 1032
   B5                              516 x 729
   B6                              363 x 516
   B7                              258 x 363
   B8                              181 x 258
   B9                              127 x 181
   B10                             91 x 127
*/

class imgsaveandresize extends skeleton {
 
 var $filename;
 var $admin_path;
 var $dropbox;
 
 var $iaction, $ifiletype, $icompression, $ixframe, $iyframe, $iwopacity, $iwalpha,
     $iwposition, $iwfile, $iautoresize, $ioptimize, $iftp_server, $iftp_username,
     $iftp_password, $iftp_path, $iftp_pathpersize, $idropbox, $idbfolder; 
	
 public function __construct($user,$data=null, $job_id=null, $job_file=null, $job_attr=null, $printer_name=null) {
 
    parent::__construct($user,$data,$job_id,$job_file,$job_attr,$printer_name);
	
	$this->jid = $job_id;
	$this->jf = $job_file;
	$this->jattr = (array) $job_attr;
	$this->printer_name = $printer_name;
	$this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/'.$this->printer_name;							
	
    self::write2disk('imgsaveandresize.log',date(DATE_RFC822)."INIT\r\n");	
	
	$this->filename = $this->jattr['job-name'];	
	
	$this->admin_path = $_SERVER['DOCUMENT_ROOT'] .'/admin/';
    if ($printer_name) //must be..
	    $this->admin_path .= $printer_name . '/';	
 }
 
 //override 
 public function execute($test=false) {
	
	$filter = 'imgsaveandresize';
	
	if ($this->jowner!='admin')
	  $conf_file = $this->admin_path . $filter.'-'.$this->jowner.'-conf'.'.php';
	else //admin
      $conf_file = $this->admin_path . $filter.'-conf'.'.php';	
	
	if (is_readable($conf_file)) {
	    //if ($test)
		  //  echo 'Conf:'.$conf_file.'<br>';
			
	    include($conf_file);
		
		$this->iaction = $iaction;	
        $this->ifiletype = $ifiletype;			
        $this->icompression = $icompression;
		$this->ixframe = $ixframe;
		$this->iyframe = $iyframe;
		$this->iwopacity = $iwopacity;
		$this->iwalpha = $iwalpha;
		$this->iwposition = $iwposition;
        $this->iwfile = $iwfile;
        $this->iautoresize = (array) $iautoresize;
		$this->ioptimize = $ioptimize;
        $this->iftp_server = $iftp_server;
        $this->iftp_username = $iftp_username;
        $this->iftp_password = $iftp_password;
        $this->iftp_path = $iftp_path;
        $this->iftp_pathpersize = (array) $iftp_pathpersize;
        $this->idropbox = $idropbox; 
        $this->idbfolder = $idbfolder; 	

	    $bytes = $this->save_and_resize($test);
	    return ($bytes);		
	}
	else {	
	    //if ($test)
		  //  echo 'Conf:default<br>';
		/*$this->iaction = null;	
        $this->ifiletype = 'jpg';			
        $this->icompression = 90;
		$this->ixframe = 0;
		$this->iyframe = 0;
		$this->iwopacity = 50;
		$this->iwalpha = 0;
		$this->iwposition = null;
        $this->iwfile = '';
        $this->iautoresize = array(500,200,95,);
		$this->ioptimize = 0;
        $this->iftp_server = 'stereobit.gr';
        $this->iftp_username = 'balexiou@stereobit.gr';
        $this->iftp_password = 'basil!72';
        $this->iftp_path = 'images/';
        $this->iftp_pathpersize = array('photo_bg/','photo_md/','photo_sm/',);
        $this->idropbox = 0; //......
        $this->idbfolder = ''; //........	
        */
        
		//save as error txt file
		$this->js .= '.txt';
        $bytes = self::_write('Not a valid configuration file.'); 
        return ($bytes);		
	}
	
 }
 
 public function save_and_resize($test=false) {
								 
	$autoresize = (!empty($this->iautoresize)) ? $this->iautoresize : null;//array('500'); 
	$filetype = $this->ifiletype ? $this->ifiletype : 'jpg';
	$compression = $this->icompression ? $this->icompression : 75;
	$wopacity = $this->iwopacity  ? $this->iwopacity  : 100;
	$walpha = $this->iwalpha ? 1 : 0;
    //echo '>...'.$walpha; 
	 
    //......check contents
	$data = @file_get_contents($this->jf);
	//if ($test)
	  //  echo '>'.$data; 
		
	if (substr($data,0,4)=='%!PS') {//postscript	
	
	    //self::write2disk('imgsaveandresize.log',"PS-RESPONSE:".$data."\r\n");
	
   	    //echo 'Convert PS2PDF';
        if ($convert = $this->ps2pdf()) {
		    if ($test)
		        echo 'Convert postscript to pdf';
			else 
                self::write2disk('imgsaveandresize.log',"Convert postscript to pdf.\r\n");

			//2nd step..convert to image...NO NEED RESIZE RETURN FALSE...NEXT STEP...
            /*if ($convert = $this->pdf2image($filetype)) {
		        if ($test)
		            echo 'Convert pdf to image';
			    else 
                    self::write2disk('imgsaveandresize.log',"Convert pdf to image.\r\n");
            }
            else 
                self::write2disk('imgsaveandresize.log',"FAILED:Convert pdf to image.\r\n"); 			
            */
		    //change job-name from .ps to .pdf...
            //$this->filename = str_replace('.ps','.pdf',$this->jattr['job-name']);				
			/*
            if ($bytes=self::resize($this->iaction, $autoresize, $filetype, $compression, $this->ixframe, $this->iyframe,
			                        $wopacity, $walpha, $this->iwposition, $this->iwfile, $this->ioptimize,
                                    $this->iftp_server, $this->iftp_user_name, $this->iftp_user_pass,
							        $this->iftp_path, $this->iftp_path_per_size,			
			                        $test))
	            return ($bytes);	
			*/
            //just return nothing...false			
        } 
        else 
            self::write2disk('imgsaveandresize.log',"FAILED:Convert postscript to pdf.\r\n");		
			
		return false;	
	}	
	elseif (substr($data,0,4)=='%PDF') {//pdf 
	    //return true;//SAVE AS PDF...
		
   	    //echo 'Convert PDF2IMAGE';
        if ($convert = $this->pdf2image($filetype)) {
		    if ($test)
		        echo 'Convert pdf to image';
			else 
                self::write2disk('imgsaveandresize.log',"Convert pdf to image.\r\n");
				
			//change job-name from .pdf to .imagetype...	
 		    //$this->filename = str_replace('.pdf','.'.$filetype,$this->jattr['job-name']);				

			/* 
            if ($bytes=self::resize($this->iaction, $autoresize, $filetype, $compression, $this->ixframe, $this->iyframe,
			                        $wopacity, $walpha, $this->iwposition, $this->iwfile, $this->ioptimize,
                                    $this->iftp_server, $this->iftp_user_name, $this->iftp_user_pass,
							        $this->iftp_path, $this->iftp_path_per_size,			
			                        $test))
	            return ($bytes);
			*/	
			//just return nothing...false
        }
		else 
            self::write2disk('imgsaveandresize.log',"FAILED:Convert pdf to image.\r\n");
			
		return false;	
	}
    elseif (substr($data,0,2)=='PK') {//xps ziped file  

        return true;	
	}
 	else { //must be jpg image..resize..ELSE ?
	  //return true;//SAVE AS PDF...
	  
	  if ($bytes=self::resize($this->iaction, $autoresize, $filetype, $compression, $this->ixframe, $this->iyframe,
			                  $wopacity, $walpha, $this->iwposition, $this->iwfile, $this->ioptimize,
                              $this->iftp_server, $this->iftp_user_name, $this->iftp_user_pass,
							  $this->iftp_path, $this->iftp_path_per_size,			
			                  $test))
	    return ($bytes);
	}  
	
    return false;	
 }
 
 protected function resize($action=null, $autoresize=null, $filetype=null, $compression=null, $xframe=null, $yframe=null,
                           $wopacity=null, $walpha=null, $wposition=null, $wfile=null, $optimize=null,
                           $ftp_server=null, $ftp_user_name=null, $ftp_user_pass=null,
						   $ftp_path=null, $ftp_path_per_size=null,			
                           $debug_mode=false) {
    //if ($debug_mode)
	  // print_r($this->jattr);  
	  
	$id   = $this->jattr['job-id'];
	$f = explode('.',$this->jattr['job-name']);   
	$name = $f[0] . '.' . $filetype;
	$source_filetype = $f[1];
	
    $image = new SimpleImage();	
    $image->load($this->jf);
	
	switch ($filetype) {
	    case 'png': $target_filetype = IMAGETYPE_PNG; break;
	    case 'gif': $target_filetype = IMAGETYPE_GIF; break;
	    case 'jpg': $target_filetype = IMAGETYPE_JPEG; break;
	    default   : $target_filetype = $image->image_type;
	}
	//echo '>'.$filetype.'>'.$target_filetype;
	if ($action)
	  $image->do_action($action);
	
	if (($xframe) || ($yframe)) 
	    $image->place_in_frame($xframe, $yframe);
	
    if ($wposition) {//$wfile may exists //add watermark

	    $watermark_filename = $this->admin_path . $wfile;
	    $image->add_watermark($watermark_filename, $wopacity, $wposition, $walpha);
	    //echo '>'.$watermark_filename;
    }
	
	//save  
	$image->save($this->jf,$target_filetype,$compression);	
	
    if ($ftp_server) {
	    //connect 
        $conn_id = ftp_connect($ftp_server);	
        // login with username and password
        $login = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
	}	
	
    //resize large, medium and small and save at once	
    if (!empty($autoresize)) {	
						
		if ($image->image_type) { //is image

			//for any size in array
			foreach ($autoresize as $i=>$size) {
			
			    if ($size) {
				   				
				    if ($optimize)//is height
					   $image->resizeToHeight($size);
					else //is width  
                       $image->resizeToWidth($size);
					
			  	    $file = $this->jobs_path .'/job'. $id .'_'.$size .'_'.$name;
                    $image->save($file,$target_filetype,$compression);
					
					if ($login) {//move to ftp
					    $remote_dir = isset($ftp_path_per_size[$i]) ? $ftp_path_per_size[$i] : null;
						$remote_file = $ftp_path . $remote_dir . $name;
						
						//if ($debug_mode)
						//    echo "remotefile:".$remote_file.'<br>';
						
						if (ftp_put($conn_id, $remote_file, $file, FTP_BINARY)) {
                            @unlink($file);							
                            self::write2disk('imgsaveandresize.log',date(DATE_RFC822)."$file transfered\r\n");									
						}
                        else {	    
						    if ($debug_mode)
                               echo "FTP:File not found:".$file.'<br>';						
                            self::write2disk('imgsaveandresize.log',date(DATE_RFC822)."$file NOT transfered\r\n");																
						}	
					}
					
					//save to dropbox..in ftp folder... 
                    if ($this->idropbox) 
					    $this->save2dropbox($file,$ftp_path_per_size[$i] ,null, $debug_mode);				
						
			    }	   
			}//foreach
			
            // close the ftp connection		
		    if ($conn_id) 
                ftp_close($conn_id);
						
			return true;			
 		}	
	}
	else {//saved.. just put a copy on ftp..if
	
        if ($image->image_type) { //is image
			

			if ($login) {//copy to ftp
			    $remote_dir = null;
				$remote_file = $ftp_path . $remote_dir . $name;
				
				if ($debug_mode)
				    echo "remotefile:".$remote_file.'<br>';			
				
				if (ftp_put($conn_id, $remote_file, $this->jf, FTP_BINARY)) {
                    //@unlink($file);							
                    self::write2disk('imgsaveandresize.log',date(DATE_RFC822). $this->jf." transfered\r\n");									
			    }	
			}		
		
            // close the ftp connection		
		    if ($conn_id) 
                ftp_close($conn_id);
				
            //save to dropbox..in dbfolder...			
            if ($this->idropbox) 
			    $this->save2dropbox($this->jf, $this->idbfolder, /*$this->filename*/null, $debug_mode);		
				
		    return true;
        }		
    }
	
    // close the ftp connection		
	if ($conn_id) 
        ftp_close($conn_id);	
	
	unset($image);
    return false;//not an image	 	
 }

 
 public function ps2pdf($sourcefile=null) {
 
	  $source = $sourcefile ? $sourcefile : $this->jf;   
	  //echo 'ps2pdf:'.$source; 
	
	  $ch = curl_init();
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
      curl_setopt($ch, CURLOPT_URL, "http://www.stuffedcow.net/images/ps2pdf/convert.php");
      curl_setopt($ch, CURLOPT_POST, true);
      // same as <input type="file" name="file_box">
      $post = array(
        "source"=>'@'.$source
		//,"name"=>'source'
      );
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
      $response = curl_exec($ch);
	  //self::write2disk('imgsaveandresize.log',"PS2PDF-RESPONSE:".$response."\r\n");
	  
	  //echo 'zzzz';
	  //if (substr($response,0,4)=='%PDF') {//if response is pdf
	    $bytes = self::_write($response);
	    return ($bytes);
	  //}
	  
	  //return (false);
 }  
 
 public function pdf2image($type=null, $sourcefile=null) {
 
      $type = $type ? $type : 'jpg';
	  $source = $sourcefile ? $sourcefile : $this->jf;   
	  //echo 'ps2pdf:'.$source; 
	
	  $ch = curl_init();
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
      curl_setopt($ch, CURLOPT_URL, "http://service.coolutils.com/PDF-Converter.php");
      curl_setopt($ch, CURLOPT_POST, true);
      // same as <input type="file" name="file_box">
      $post = array(
        "filename"=>'@'.$source
		//,"name"=>'source'
      );
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
      $response = curl_exec($ch);
	  //self::write2disk('imgsaveandresize.log',"PDF2JPG-RESPONSE:".$response."\r\n");
	  //echo 'zzzz';
	  
	  //if (substr($response,0,4)=='%xxxPDF') {//if response is jpg
	    $bytes = self::_write($response);
	    return ($bytes);
	  //}
	  
	  //return (false);
 }   
 
 protected function save2dropbox($file=null, $infolder=null, $altname=null, $debug_mode=false) {
    if (!$file) return;
	$altname = $altname ? $altname : $this->jattr['job-name'];//str_replace('.ps','.jpg',$altname);

	$myfolder = $infolder ? $infolder : null;	
    $app_key = "45mi3sxld19333f";//po printer ..//"geuq6gm2b5glofq";//..dropbox.printer
	$app_secret = "424ewjtnrtgwpud";//"5s9jvk2zd5oc0hq";	
	
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
          $userID = $u;		
		else 
          $userID = $this->jowner;
		  
        // Create the storage object, passing it the Encrypter object
		$storage = new \Dropbox\OAuth\Storage\Filesystem($encrypter, $userID);
	    $storage->setDirectory($this->admin_path);
 
        $OAuth = new \Dropbox\OAuth\Consumer\Curl($app_key, $app_secret, $storage, $callback);
        $this->dropbox = new \Dropbox\API($OAuth);
 
        /*if ($debug_mode) {
		  echo "<br>DBOXSAVE FILENAME ($userID):". $this->jf ;
		  echo "<br>DBOXSAVE FOLDER ($userID):". $myfolder ."<br>";
		}  
		else {*/
          self::write2disk('imgsaveandresize.log',"FILE ($userID):".$this->jf. PHP_EOL);
		  self::write2disk('imgsaveandresize.log',"FOLDER ($userID):".$myfolder. PHP_EOL);
		//}  
	    //return true;
		 
		 
        // Upload the file with an alternative filename
        $put = $this->dropbox->putFile($file,$altname,$myfolder,true); //alt name,path,override
    } 
	catch(\Dropbox\Exception $e) {
	    //echo $e->getMessage() . PHP_EOL;
	    //exit('Setup failed! Please try running setup again.');
	    if ($debug_mode)
		  echo $e->getMessage();
		else
	      self::write2disk('imgsaveandresize.log',"ERROR:".$e->getMessage() . PHP_EOL);
		  
        //create directory
        self::write2disk('imgsaveandresize.log',"CREATE DIR:".$myfolder . PHP_EOL); 		
		$this->create_dropbox_directory($myfolder);  		  
    }	
	
    self::write2disk('imgsaveandresize.log',"PUT:$put". PHP_EOL);
	
	//$bytes = self::_write($response);
	//return ($bytes);	  


	if ($put)
	  return true; 	

	  
    return false;	
 }

 function create_dropbox_directory($path=null) {
    if (!$path) 
	  return false;
 
    $metaData = $this->dropbox->create($path); 
	return ($metaData);
 }  
 
}

/********************************************************************************/
//test
if ($test = $_GET['test']) {
$test_file = getcwd() . '/text.jpg';
//echo 'test_file:', $test_file,'<br>';
if ($fp = fopen($test_file, "r+b")) {  

  $auth_obj = new StdClass();//dummy
  $test_image = new imgsaveandresize($auth_obj,$fp,1,$test_file,null,'imagesave.printer');  

  $ret = $test_image->execute($test);
  
  //echo 'result:',$ret,'<br>';
  
  fclose($fp);
}
else
  echo 'file is not readable<br>';
}  
/****************************************************************************/
?>
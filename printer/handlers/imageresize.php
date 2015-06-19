<?php

require_once('skeleton.php');
require_once('ImageResize/SimpleImage.php');


class imageresize extends skeleton {

 var $autoresize, $filetype, $compression;
 var $ftp_server, $ftp_user_name, $ftp_user_pass, $ftp_path, $ftp_path_per_size;
	
 public function __construct($user,$data=null, $job_id=null, $job_file=null, $job_attr=null, $printer_name=null) {
 
    parent::__construct($user,$data,$job_id,$job_file,$job_attr,$printer_name);
	
	$this->jid = $job_id;
	$this->jf = $job_file;
	$this->jattr = (array) $job_attr;
	
	$this->printer_name = $printer_name;
	
	$this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/'.$this->printer_name;							
 
    $this->autoresize = array('95','200','500'); //pixels width resize
	$this->filetype = '.jpg';
	$this->compression = 99;
	
    $this->ftp_server = "networlds.org";	
    $this->ftp_user_name = "networld";
    $this->ftp_user_pass = "Vk7dp@pd7kV";	
	$this->ftp_path = 'www/images/';
	$this->ftp_path_per_size = array('small/','medium/','large/');
 
    self::write2disk('imageresize.log',"INIT\r\n");	
 }
 
 //override
 public function execute($test=false) {
 	
	if ($bytes=self::resize($test))
	  return ($bytes);
	
    return false;	
 }
 
 protected function resize($debug_mode=false) {
    if ($debug_mode)
	   print_r($this->jattr);
	   
	$id   = $this->jattr['job-id'];
	$name = $this->jattr['job-name'];
	
    //resize large, medium and small and save at once	
    if (!empty($this->autoresize)) {	

        // set up basic connection
        $conn_id = ftp_connect($this->ftp_server);	
		
        // login with username and password
        $login = ftp_login($conn_id, $this->ftp_user_name, $this->ftp_user_pass);

        if ($login) {	
		
                        $image = new SimpleImage();
                        $image->load($this->jf);
						if  ($image->image_type) { //is image
							   
							   if ($dim_large = $this->autoresize[2]) {
                                 $image->resizeToWidth($dim_large);
								 $file = $this->jobs_path .'/job'. $id .'_large_' .$name;
                                 $image->save($file);//,null,$this->compression);
								 
								 //move to ftp
								 $remote_dir = isset($this->ftp_path_per_size[2]) ? $this->ftp_path_per_size[2] : null;
								 $remote_file = $this->ftp_path . $remote_dir . $name;
								 if (ftp_put($conn_id, $remote_file, $file, FTP_BINARY))
                                    @unlink($file);							
                                    //echo ''; 									
							   }								   
							   if ($dim_medium = $this->autoresize[1]) {
                                 $image->resizeToWidth($dim_medium);
								 $file = $this->jobs_path .'/job'. $id .'_medium_' .$name;								 
                                 $image->save($file);//,null,$this->compression);
								 
								 //move to ftp
								 $remote_dir = isset($this->ftp_path_per_size[1]) ? $this->ftp_path_per_size[1] : null;
								 $remote_file = $this->ftp_path . $remote_dir . $name;
								 if (ftp_put($conn_id, $remote_file, $file, FTP_BINARY))
                                    @unlink($file);	
                                    //echo ''; 									
							   }
							   if ($dim_small = $this->autoresize[0]) {
                                 $image->resizeToWidth($dim_small);
								 $file = $this->jobs_path .'/job'. $id .'_small_' .$name;								 
                                 $image->save($file);//,null,$this->compression);
								 
								 //move to ftp
								 $remote_dir = isset($this->ftp_path_per_size[0]) ? $this->ftp_path_per_size[0] : null;
								 $remote_file = $this->ftp_path . $remote_dir . $name;
								 if (ftp_put($conn_id, $remote_file, $file, FTP_BINARY))
                                    @unlink($file);	
									//echo '';  
							   }
						}	   
						unset($image);
                        return 1;
 		}
        // close the connection
        ftp_close($conn_id); 		
	}

    return false; 	
 }
 
 protected function ftp_example() {
 
    $file = 'somefile.txt';
    $remote_file = 'readme.txt';

    // set up basic connection
    $conn_id = ftp_connect($ftp_server);

    // login with username and password
    $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

    // upload a file
    if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
       echo "successfully uploaded $file\n";
    } 
	else {
       echo "There was a problem while uploading $file\n";
    }

    // close the connection
    ftp_close($conn_id); 
 }
 
}

/********************************************************************************/
//test
if ($test = $_GET['test']) {
$test_file = getcwd() . '/text.jpg';
//echo 'test_file:', $test_file,'<br>';
if ($fp = fopen($test_file, "r+b")) {  

  $auth_obj = new StdClass();//dummy
  $test_image = new imageresize($auth_obj,$fp,1,$test_file,null,'test.printer');

  $ret = $test_image->execute($test);
  
  //echo 'result:',$ret,'<br>';
  
  fclose($fp);
}
else
  echo 'file is not readable<br>';
}  
/****************************************************************************/
?>
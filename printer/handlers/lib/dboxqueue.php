<?php


class dboxqueue {

    protected $idboxusers, $idboxto_files;
	protected $printer_name, $queued_file, $admin_path;

    function __construct($printer_name=null, $queued_file=null, $admin_path=null) {
	   
	    $this->idboxusers = array();
		$this->idboxto_files = array();
		
		$this->printer_name = $printer_name;
		$this->queued_file = $queued_file;
		$this->admin_path = $admin_path;
	}
	
	public function add_dbox_user($user, $id=null) {

		if (!$this->dropbox_receiver_exist($user)) 
            return false;		
	
	    if ($id)
			$this->idboxusers[$id] = $user;	
		else
			$this->idboxusers[$user] = $user;	
			
		return ($user);	
	}

	public function remove_dbox_user($user, $id=null) {	
	
		if (!$this->dropbox_receiver_exist($user))  
            return false;		
	
	    if ($id)
			$this->idboxusers[$id] = null;	
		else
			$this->idboxusers[$user] = null;	
			
		return ($user);	
	}	
	
	public function add_dbox_attachment($file, $id=null) {

		if (!is_readable($file)) 
            return false;		
	
	    if ($id)
			$this->idboxto_files[$id] = $file;	
		else
			$this->idboxto_files[$file] = $file;	
			
		return ($file);	
	}

	public function remove_dbox_attachment($file, $id=null) {	
	
		if (!is_readable($file)) 
            return false;			
	
	    if ($id)
			$this->idboxto_files[$id] = null;	
		else
			$this->idboxto_files[$file] = null;	
			
		return ($file);	
	}	
	
	protected function save2dropbox($file=null, $infolder=null, $altname=null, $jowner=false) {
		if (!$file) return;

		$myfolder = $infolder ? $infolder : null;	
		$app_key = "geuq6gm2b5glofq";//..dropbox.printer
		$app_secret = "5s9jvk2zd5oc0hq";	
	
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
				$userID = $jowner; //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
		  
			// Create the storage object, passing it the Encrypter object
			$storage = new \Dropbox\OAuth\Storage\Filesystem($encrypter, $userID);
			$storage->setDirectory($this->admin_path);
 
			$OAuth = new \Dropbox\OAuth\Consumer\Curl($app_key, $app_secret, $storage, $callback);
			$this->dropbox = new \Dropbox\API($OAuth);
		 
		    // Upload the file with an alternative filename
			$put = $this->dropbox->putFile($file,$altname,$myfolder,false); //alt name,path,override	
		} 
		catch(\Dropbox\Exception $e) {

			//if ($debug_mode)
				//echo $e->getMessage();
		  
			//create directory		
				$this->create_dropbox_directory($myfolder);  		  
		}	
	

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
 
	function dropbox_receiver_exist($id=null) {
		if (!$id) 
			return false;
 
		if (is_readable($this->admin_path.'/'.md5($id).'.token'))
			return true;
		
		return false;
	} 	 
	
	function dropbox_queue_attachments() {
	
		// Upload pre-selected files
		if (!empty($this->idboxto_files)) {
		
			foreach ($this->idboxto_files as $id=>$file) {
			
			    foreach ($this->idboxusers as $u=>$user) {
					$put = $this->save2dropbox($file,null,null,$user); //alt name,path,user
					//if ($put)..add tracker
				}	
			}	
				
			return true;	
		}	
		
		return false;
	}
}	
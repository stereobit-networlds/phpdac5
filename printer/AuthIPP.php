<?php
require 'handlers/tweet/tmhOAuth.php';
require 'handlers/tweet/tmhUtilities.php';

class AuthIPP {

	protected $_auth;
	protected $auth_type;
	protected $name, $pass;
	
	public $allowed_users;
	protected $hash_schema;

    public function __construct($auth_method=null, $users=null, $hash_schema=null) {
	
	    $this->auth_type = $auth_method;
		if (is_array($users)) 
		  $this->allowed_users = (array) $users;
		else		
		  $this->allowed_users = array('billy'=>'bf4ab1b1','admin'=>'e681fac9',
		                               'test'=>'bf4ab1b1','guest'=>'191abe91',
			  						   'root'=>'b6536850',
									   'Stelios'=>'b6536850');//basic auth

        $this->hash_schema = $hash_schema;									   
		
		//.htaccess for cgi php last rule = RewriteRule .* - [E=DEVMD_AUTHORIZATION:%{HTTP:Authorization}] 
		if ((isset($_SERVER['DEVMD_AUTHORIZATION']) && preg_match('/Basic\s+(.*)$/i', $_SERVER['DEVMD_AUTHORIZATION'], $matches)) ||
            (isset($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/Basic\s+(.*)$/i', $_SERVER['HTTP_AUTHORIZATION'], $matches))) 
		{ 
		    //get account data from printer
            list($this->name, $this->pass) = explode(':', base64_decode($matches[1]));
            $_SERVER['PHP_AUTH_USER'] = strip_tags($this->name);
            $_SERVER['PHP_AUTH_PW'] = strip_tags($this->pass);
		    //self::write2disk('prompt.log',$_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW']."\r\n");
		
		}		
	
		if ($this->auth_type==='OAUTH') {
		  //$pin_call = true;//false;
		  $this->_auth = new tmhOAuth(array('consumer_key'    => 'NwjTDX9Xb8s5MHsU9Pfj9Q',
                                            'consumer_secret' => 'ZbZSyvSJbjPbFzDKV5MIikO1wTann8jAHZX8RVb2w'));
        }	
    }  

    public function ipp_auth() {
	
	
	    switch ($this->auth_type) {
		  
		    case 'OAUTH':  $username = self::oAuth_ipp();
			               return ($username);
                           break;
			case 'DIGEST': break;
			case 'BASIC' : $username = self::http_basic();
			               return ($username);
                           break;	
			case 'SIMPLE': $username = self::http_simple();
			               return ($username);
                           break;						   
			default      :
		}//switch	
		return false;	
    }	
	
    public function http_auth() {
	
	    switch ($this->auth_type) {
		  
		    case 'OAUTH' : $username = self::oAuth_http();
			               return ($username);
                           break;
			case 'DIGEST': $username = self::digest_http();
			               return ($username);
                           break;
			case 'BASIC' : $username = self::http_basic();
			               return ($username);
                           break;
			case 'SIMPLE': $username = self::http_simple();
			               return ($username);
                           break;							   
			default      :
		}//switch	
		return false;
    }
	
	public function get_user_admin() {
	   
	   
	   return ('admin');
	}
	
	protected function digest_http() {
	
		if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
	 	        preg_match('/digest\susername="(?P<username>.*)"' .
                       ',\s*realm="(?P<realm>.*)"' . 
                       ',\s*nonce="(?P<nonce>.*)"' .
                       ',\s*uri="(?P<uri>.*)"' .
                       ',\s*response="(?P<response>.*)"' . 
                       ',\s*opaque="(?P<opaque>.*)"' . 
                       ',\s*qop=(?P<qop>.*)' . 
                       ',\s*nc=(?P<nc>.*)' . 
                       ',\s*cnonce="(?P<cnonce>.*)"/i', stripslashes($_SERVER['PHP_AUTH_DIGEST']), $digest);
                // Sometimes ISAPI uses qop="auth", and sometimes it uses qop=auth
                $digest['qop'] = str_replace("\"", "", $digest['qop']);
			
                // Analyze the PHP_AUTH_DIGEST variable
                //if (!($digest = self::http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) || 
			    //    !array_key_exists($digest['username'], $this->allowed_users)){			
			    if (array_key_exists($digest['username'], $this->allowed_users)) {
			
                    // This is the valid response expected
                    $A1 = md5($digest['username'] . ':' . $realm . ':' . $users[$digest['username']]);
                    $A2 = md5($_SERVER['REQUEST_METHOD'].':'.$digest['uri']);
                    $valid_response = md5($A1.':'.$digest['nonce'].':'.$digest['nc'].':'.
                                      $digest['cnonce'].':'.$digest['qop'].':'.$A2);

                    if ($digest['response'] == $valid_response) {
		                //ipp access
                        $username = $digest['username']; 
		
		                //http access...save user/pass
		                $_SESSION['user'] = $digest['username'];	
					
                        return ($username);
                    }						
                }				
		}	
		return false;
	}

    // Function to parse the http auth header
    protected function http_digest_parse($txt){
        // Protect against missing data
        $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
        $data = array();
        $keys = implode('|', array_keys($needed_parts));
 
        preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);
 
        foreach ($matches as $m) {
            $data[$m[1]] = $m[3] ? $m[3] : $m[4];
            unset($needed_parts[$m[1]]);
        }
 
        return $needed_parts ? false : $data;
    }

	protected function http_basic() {
	    /*
		if (isset($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/Basic\s+(.*)$/i', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
          list($name, $password) = explode(':', base64_decode($matches[1]));
          $_SERVER['PHP_AUTH_USER'] = strip_tags($name);
          $_SERVER['PHP_AUTH_PW'] = strip_tags($password);	  
		}//BASIC AUTH MY DEVMD _SERVER PARAM
	    //.htaccess for cgi php last rule = RewriteRule .* - [E=DEVMD_AUTHORIZATION:%{HTTP:Authorization}] 
		elseif (isset($_SERVER['DEVMD_AUTHORIZATION']) && preg_match('/Basic\s+(.*)$/i', $_SERVER['DEVMD_AUTHORIZATION'], $matches)) {
          list($name, $password) = explode(':', base64_decode($matches[1]));
          $_SERVER['PHP_AUTH_USER'] = strip_tags($name);
          $_SERVER['PHP_AUTH_PW'] = strip_tags($password);
        }	
	    */
		
		if ($this->hash_schema)
		    $p_pass = hash($this->hash_schema,$this->pass); //hash comparison
		else
            $p_pass = $this->pass; 		
		
		if ((array_key_exists($this->name, $this->allowed_users)) &&
		    ($this->allowed_users[$this->name]== $p_pass)) {
          
		  //ipp access
          $username = $this->name; 
		
		  //http access...save user/pass
		  $_SESSION['user'] = $this->name;
		  //$_SESSION['pass'] = $_SERVER['PHP_AUTH_PW']; //no need
          		  
		  return ($username);
		} 	
		return false;
	}
	
	protected function http_simple() {
	    //based only on names
	    //$user = self::read_request_attribute('requesting-user-name');
        $name = $this->name ? $this->name : 'anonymous';
		//$job_owner = self::read_request_attribute('job-originating-user-name');//..must be set by printer object ?	    
		
		if (in_array($name, $this->allowed_users)) {
		   $this->username = $name;
		   $_SESSION['user'] = $name;
		   return ($uername);
		}  	
	}
	
	//implements pin request
	protected function oAuth_ipp() {
	
		$name = $this->name;
		$pass = $this->pass;
	
		if ((isset($name)) && (isset($pass))) { 

		        self::write2disk('auth.log',$name.':'.$pass."\r\n");
			
		        //if already logged in from web
		        if (is_readable($name.'.php')) {
		            include($name.'.php'); //load user array of data
			        $this->_auth->config['user_token']  = $user['user_token'];
                    $this->_auth->config['user_secret'] = $user['user_secret'];

		            if ($this->_auth->request('GET',$this->_auth->url('1/account/verify_credentials'))==200) {
		                $resp = json_decode($this->_auth->response['response']);
			            $username = $resp->screen_name;
			            return ($username);
		            }			
		        }
                else { 
					
				    $code = $this->_auth->request('POST',$this->_auth->url('oauth/access_token', ''),
            	  	                        array('oauth_verifier' => $pass)); 
                    if ($code == 200) {
						$oauth_creds = $this->_auth->extract_params($this->_auth->response['response']);
				        $resp = json_decode($this->_auth->response['response']);
						$username = $resp->screen_name;
						//save user file
				        @file_put_contents($username.'.php',"<?php\r\n \$user = array('user_token'=>'".$oauth_creds['oauth_token']."','user_secret'=>'".$oauth_creds['oauth_token_secret']."');\r\n?>");
		                return ($username);
                    }
                    else {
			            //$oauth = var_export($this->_auth, 1);
			            $text = 'There was an error: ' . $this->_auth->response['response'] . PHP_EOL . $oauth;
				        self::write2disk('auth.log',$text);
						
						$params['x_auth_access_type'] = 'read';
				        $params['x_auth_access_type'] = 'write';	
                        $code = $this->_auth->request('POST',$this->_auth->url('oauth/request_token', ''), $params);
						
						$oauth_creds = $this->_auth->extract_params($this->_auth->response['response']);
						
						// update with the temporary token and secret
                        $this->_auth->config['user_token']  = $oauth_creds['oauth_token'];
                        $this->_auth->config['user_secret'] = $oauth_creds['oauth_token_secret'];
						$url = $this->_auth->url('oauth/authorize', '') . "?oauth_token={$oauth_creds['oauth_token']}";
						$prompt = "$name : copy and paste this URL to get a pin code:\r\n".$url;
						self::write2disk('auth.log',$prompt."\r\n");
						/*
                        $headers  = 'MIME-Version: 1.0' . "\r\n";
                        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                        $headers .= 'From: balexiou@stereobit.com' . "\r\n" .
                                    'Reply-To: balexiou@stereobit.com' . "\r\n" .
                                    'IPP-Printer: 1.0-/' . phpversion();						
						mail ('b.alexiou@stereobit.gr','oauth prompt', $prompt, $headers);
						*/
                        return false;			  
			        }
				}	
        }	
	}
	
	protected function oAuth_http() {
	
        if (isset($_SESSION['access_token'])) {
                $this->_auth->config['user_token']  = $_SESSION['access_token']['oauth_token'];
                $this->_auth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

                $code = $this->_auth->request('GET',$this->_auth->url('1/account/verify_credentials'));

                if ($code == 200) {
                    $resp = json_decode($this->_auth->response['response']);
				
                    //echo '<h1>Hello ' . $resp->screen_name . '</h1>';
                    //echo '<p>The access level of this token is: ' . $this->_auth->response['headers']['x_access_level'] . '</p>';
				
		            //ipp access
                    $username = $resp->screen_name; 
				    //for ipp requests...store
				    @file_put_contents($username.'.php',"<?php\r\n \$user = array('user_token'=>'".$this->_auth->config['user_token'].
					                                    "','user_secret'=>'".$this->_auth->config['user_secret']."');\r\n?>");
		
		            //http access...
		            $_SESSION['user'] = $resp->screen_name;
				
			        return ($username);
                } 
			    else {
                   //echo 'There was an error: ' . $this->tmhOAuth->response['response'] . PHP_EOL;
			       return false;
                }
        }//access-token / we're being called back by Twitter 
        elseif (isset($_REQUEST['oauth_verifier'])) { 
	
			    $this->_auth->config['user_token']  = $_SESSION['oauth']['oauth_token'];
                $this->_auth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];
                $code = $this->_auth->request('POST',$this->_auth->url('oauth/access_token', ''),
            	  	                  array('oauth_verifier' => $_REQUEST['oauth_verifier']));
			    if ($code == 200) {
			  
			        $_SESSION['access_token'] = $this->_auth->extract_params($this->_auth->response['response']);
				
                    unset($_SESSION['oauth']);
                    header('Location: ' . tmhUtilities::php_self());
				    die();
                }
                else {
			        //echo 'There was an error: ' . $tmhOAuth->response['response'] . PHP_EOL;
                    return false;   			
			    }		  
        }
        else { //request-token
		  
                $params = array('oauth_callback' => tmhUtilities::php_self());
				$params['x_auth_access_type'] = 'read';
				$params['x_auth_access_type'] = 'write';	
                $code = $this->_auth->request('POST',$this->_auth->url('oauth/request_token', ''), $params);

                if ($code == 200) {
				        $_SESSION['oauth'] = $this->_auth->extract_params($this->_auth->response['response']);
                        $method = 'authenticate';//isset($_REQUEST['authenticate']) ? 'authenticate' : 'authorize';
                        $force  = '&force_login=1';//isset($_REQUEST['force']) ? '&force_login=1' : '';
                        $authurl = $this->_auth->url("oauth/{$method}", '') .  "?oauth_token={$_SESSION['oauth']['oauth_token']}{$force}";
                        //echo '<p>To complete the OAuth flow follow this URL: <a href="'. $authurl . '">' . $authurl . '</a></p>';
			        
				        //redirect
                        header("Location: {$authurl}");
				        die();
                } 
        } 		
	}
	
	
    function write2disk($file,$data=null) {
	        if (!defined('SERVER_LOG'))
			    return null; 	

            if ($fp = @fopen ($file , "a+")) {
	        //echo $file,"<br>";
                 fwrite ($fp, $data);
                 fclose ($fp);

                 return true;
            }
            else {
              echo "File creation error ($file)!<br>";
            }
            return false;

    }  	
}
?>
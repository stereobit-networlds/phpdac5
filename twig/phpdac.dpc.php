<?php

	   
class phpdac {

    var $prpath, $tpath, $tcache;
    var $title;
	var $phpdacarray;
   
    function __construct() {
   
        $this->prpath = paramload('SHELL','prpath');
        $this->tpath = paramload('FRONTHTMLPAGE','path');
		$this->tcache = paramload('SHELL','cachepath');
        //echo 'aaaaa';
		
		$this->title = 'aaaa';  
        $this->phpdacarray = array();		
    }
	
	public function mytitle() {
	
	    return ($this->title);
	}
	
	public function _func($func=null) {
	    if (!$func) return ('no func');
	    return (GetGlobal('controller')->calldpc_method($func));
	}
	
	public function _var($var=null) {
	    if (!$var) return ('no var');
	    return (GetGlobal('controller')->calldpc_var($var));
	}

	public function _svar($svar=null) {
	    if (!$svar) return ('no session var');
	    return (GetSessionParam($svar));
	}	
	
	public function _global($var=null) {
	    if (!$var) return ('no global var');
	    return (GetGlobal($var));
	}	
	
	public function _param($var=null) {
	    if (!$var) return ('no param var');
	    return (GetParam($var));
	}	

	public function _conf($confvar=null) {
	    $p = explode('.',$confvar); 
		$sec = $p[0];
		$var = $p[1];
	    if (!$sec) return ('no section');
	    if (!$var) return ('no var');
	    return (remote_paramload($sec,$var,$this->prpath));
	}	
	
	//filter
	public function _nformat($n=null) {
	   if (!$n) return;
	   return (number_format(floatval($n),2,',','.'));
	}
	
	public function _sexplode($n=null, $nret=null, $sep=null) {
	   if (!$n) return;
	   echo '|||||',$nret,$sep,'>>>>';
	   $sep = $sep ? $sep : ';';
	   $p = is_numeric($nret) ? $nret : 0;
	   $parts = explode($sep, $n);
	   //print_r($parts);
	   return ($parts[$p]);
	}	
	
	public function _dacarray($n=null) {
	   if (!$n) return;
	   $this->phpdacarray = array(); //reset
	   $this->phpdacarray = explode(';',$n);
	   return ($this->phpdacarray[0]);
	}	

    public function _dacelement($no=null) {
	   if (!$no) $n = 0;
	   return $this->phpdacarray[$n];
    }

    public function _1($n) {
	   $parts = explode(';', $n);
	   return ($parts[0]);
    }	
	
    public function _2($n) {
	   $parts = explode(';', $n);
	   return ($parts[1]);
    }	
	
    public function _3($n) {
	   $parts = explode(';', $n);
	   return ($parts[2]);
    }		
	
    public function _4($n) {
	   $parts = explode(';', $n);
	   return ($parts[3]);
    }	

    public function _5($n) {
	   $parts = explode(';', $n);
	   return ($parts[4]);
    }	
	
    public function _6($n) {
	   $parts = explode(';', $n);
	   return ($parts[5]);
    }	
	
    public function _7($n) {
	   $parts = explode(';', $n);
	   return ($parts[6]);
    }		
	
    public function _8($n) {
	   $parts = explode(';', $n);
	   return ($parts[7]);
    }	
	
    public function _9($n) {
	   $parts = explode(';', $n);
	   return ($parts[8]);
    }		
	
    public function _10($n) {
	   $parts = explode(';', $n);
	   return ($parts[9]);
    }		
	
    public function _11($n) {
	   $parts = explode(';', $n);
	   return ($parts[10]);
    }	

    public function _12($n) {
	   $parts = explode(';', $n);
	   return ($parts[11]);
    }	

    public function _brstrstr($s) {
	
	    if (strstr($s, '<br>'))
			return (explode('<br>',$s));
		
        return (array($s)); 		
    }	
   
	/*public function __get($name) {
		if ('title' == $name) {
			return 'The title';
		}
		
		// throw some kind of error
		$trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
			
        return null;		
	}
	
	public function __isset($name) {
		if ('title' == $name) {
			return true;
		}
		return false;
    }*/
}
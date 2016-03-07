<?php
if (!defined("TWIGENGINE_DPC")) {
define("TWIGENGINE_DPC",true);

$__DPC['TWIGENGINE_DPC'] = 'twigengine';

$a = GetGlobal('controller')->require_dpc('twig/twigengine.lib.php');
require_once($a);
//Twig_Autoloader::register();

//require_once('phpdac.dpc.php');
//$phpdac = new phpdac();
$b = GetGlobal('controller')->require_dpc('twig/phpdac.dpc.php');
require_once($b);
	   
class twigengine extends Twig_Autoloader {

   var $prpath, $tpath, $tcache;
   var $twig;
   var $tmpl_path, $tmpl_name;
   
   var $pdac;
   
   function __construct($cache=false) {
   
        $this->prpath = paramload('SHELL','prpath');
		$this->tcache = paramload('SHELL','cachepath');
        //echo 'aaaaa';
		
        $this->tpath = remote_paramload('FRONTHTMLPAGE','path',$this->prpath);		
	    $this->tmpl_path = remote_paramload('FRONTHTMLPAGE','path',$this->prpath);
	    $this->tmpl_name = remote_paramload('FRONTHTMLPAGE','template',$this->prpath);	 		
		
		parent::register();
		
		$mypath = $this->tmpl_name ? 
		          $this->prpath . $this->tmpl_path .'/'. $this->tmpl_name .'/'. str_replace('.',getlocal().'.',$template) :
		          $this->prpath . $this->tpath;
		//echo $mypath;
		$loader = new Twig_Loader_Filesystem($mypath);//'/path/to/templates');
		$c = ($cache) ? array('cache' => $this->tcache) : array();
		$this->twig = new Twig_Environment($loader, $c);
						   
						   
		$this->pdac = new phpdac();				   
		$this->twig->addGlobal('phpdac', $this->pdac);
		$this->twig->addFilter('nformat', new Twig_Filter_Function('phpdac::_nformat'));
		//$this->twig->addFilter('sexplode', new Twig_Filter_Function('phpdac::_sexplode'));
		$this->twig->addFilter('dacarray', new Twig_Filter_Function('phpdac::_dacarray'));
		//$this->twig->addFilter('dacelement', new Twig_Filter_Function('phpdac::_dacelement'));
		//$function = new Twig_SimpleFunction("dacelement", function ($id=0) { return (phpdac::_dacelement($id));});
        //$this->twig->addFunction($function);
		for ($xi=1;$xi<13;$xi++) 
		   $this->twig->addFilter('_'.$xi, new Twig_Filter_Function('phpdac::_'.$xi));
		
		$this->twig->addFilter('brstrstr', new Twig_Filter_Function('phpdac::_brstrstr'));
        //$this->twig->addFilter('lower', new Twig_Filter_Function('strtolower'));		
		//$this->twig->addFilter('monfiltre', new Twig_Filter_Function('MaClasse::MaMethode'));
		/*$function = new Twig_SimpleFunction("form_text", function ($name, $id, $value = "", $class = "form_text") {
        echo '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" class="'.$class.'">";
});
$twig->addFunction($function);*/
/*
$syntaxe = new Twig_Lexer($twig, array(
    'tag_comment'  => array('#', '#'),
    'tag_block'    => array('<%', '%>'),
    'tag_variable' => array('<%=', '%>')
));
$twig->setLexer($syntaxe);
*/
   }
   
   public function render($htmlpage=null, $cache=false, $mytokens) {
        if (!$htmlpage) return;
		//echo $htmlpage;
		
		//put here for autoloader..
		//to not load classes when include as dpc!
		//load when needed
		/*$loader = new Twig_Loader_Filesystem($this->prpath.$this->tpath);//'/path/to/templates');
		$c = ($cache) ? array('cache' => $this->tcache) : array();
		$this->twig = new Twig_Environment($loader,$c);*/ //, array(
							/*'cache' => $this->tcache,
						   ));*/	
						   
        $tokens = unserialize($mytokens);//explode('<twig|twig>', $mytokens);
		//print_r($tokens);
		
		$this->pdac->addTokens($tokens);
		
		$ret = $this->twig->render($htmlpage, $tokens);   
		
		return ($ret);
   }
   
   public static function tokenize($mytokens) {
       $tokens = unserialize($mytokens);
       if (empty($tokens)) return;
   
       return (implode('<twig|twig>',$tokens));
   }

};
}
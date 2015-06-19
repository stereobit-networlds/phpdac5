<?php
$__DPCSEC['TAXCLOUDINDEX_DPC']='1;1;1;1;1;1;1;1;1';

if (!defined("TAXCLOUDINDEX_DPC")) {
define("TAXCLOUDINDEX_DPC",true);

$__DPC['TAXCLOUDINDEX_DPC'] = 'taxcloudindex';

$a = GetGlobal('controller')->require_dpc('printer/UiIPPtaxcloud.lib.php');
require_once($a);


$__EVENTS['TAXCLOUDINDEX_DPC'][0]='index';
$__EVENTS['TAXCLOUDINDEX_DPC'][1]='taxindex';
$__EVENTS['TAXCLOUDINDEX_DPC'][2]='taxqrscan';

$__ACTIONS['TAXCLOUDINDEX_DPC'][0]='index';
$__ACTIONS['TAXCLOUDINDEX_DPC'][1]='taxindex';
$__ACTIONS['TAXCLOUDINDEX_DPC'][2]='taxqrscan';

$__DPCATTR['TAXCLOUDINDEX_DPC']['taxindex'] = 'taxindex,1,0,0,0,0,0,0,0,0,0,0,1';

$__LOCALE['TAXCLOUDINDEX_DPC'][0]='TAXCLOUDINDEX_DPC;index;index';


class taxcloudindex  {

	function __construct() {  
	
	    $this->printer_name = $_SESSION['printer'] ? $_SESSION['printer'] : null;//'taxcloud.printer';
		$this->defdir = $_SESSION['indir'] ? $_SESSION['indir'] : '/';//null//'printers';
		$this->username = $_SESSION['user'] ? $_SESSION['user'] : null;//'anonymoys';	
		$this->tax_user_email = $_SESSION['itaxusermail'] ? $_SESSION['itaxusermail'] : null;
		$this->notify_mail = 'info@smart-printers.net';
		
		$this->jobs_path = $_SERVER['DOCUMENT_ROOT'] .'/jobs/';
		$this->icons_path = $_SERVER['DOCUMENT_ROOT'] .'/icons/';
	    $this->admin_path = $_SERVER['DOCUMENT_ROOT'] .'/admin/';
        if ($this->printer_name) 
	      $this->admin_path .= $this->printer_name . '/';			
	}
	
    function event($event=null) {
	
	    $this->_setup_user();
	
        switch($event)   {
			                     		
        }			
	}
	
    function action($action=null)  {	
	
        switch($action)   {	
		
			default           :	$ret  = $this->html_window($this->printer_name, null, $this->printer_name);//, true);
								
        }

        return ($ret);		
	}	
	
	protected function _setup_user() {
	
	    //refresh conf file
	    //if ($this->username!=$this->get_printer_admin()) 
		    $this->config_file = $this->admin_path . 'taxcalc-'.$this->username.'-conf'.'.php';
	    //else
        //    $this->config_file = $this->admin_path . 'taxcalc-conf'.'.php';		
	
	    //read conf	   
        if (is_readable($this->config_file))
            include($this->config_file);	   

        $this->signer_service = $itaxsigner ? true : false; //enable eafdss
	    $this->fiscal_service = $itaxsigner ? ($itaxfiscal ? true : false) : false; //enable taxfiscal pos only if eafdss is active
        $this->cprint_service = $itaxcprint ? true : false; //enable consumer services		   

	    //timezone	   
        date_default_timezone_set($itaxctimezone);	
	}	
	
	protected function html_window($title=null, $data=null, $footer_title=null, $nomenu=false, $tab_id=false) {
	    $ver = null;//$this->server_name . $this->server_version;	
		$footer_title = $footer_title ? $footer_title :	null;//$ver.'&nbsp;|&nbsp;'.$this->logout_url;
		$header_title = $title ? '<div class="contr"><h2>'.$title.'</h2></div>' : null;
		
	    $menu = $nomenu ? null : $this->html_printer_menu(true);		
	
	    $form = <<<EOF
<!--link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script-->	
	
    <div class="container">
    $header_title
    <div class="upload_form_cont">	
	<div id="form_container">
	    $menu					
		$data
		<hr/>	
		<div id="footer">
        $footer_title
		</div>
	</div>
	<br/>
	</div>
	</div>
EOF;

        if (($this->tabs) && ($tabid = $this->tab_id++)) {
		   $ts = '<div class="tabpage" id="tabpage_'.$tabid.'">';
		   $te = '</div>';
		   return ($ts.$form.$te);
		}
		else
           return ($form); 	
	}		
	
	protected function html_printer_menu($iconsview=null, $p=null) {
		$urlicons = 'icons/';	
        $icons = array();		
		$user = $this->username ? $this->username : $_SESSION['user'];
		$indir = $_SESSION['indir'] ? $_SESSION['indir'] : $_GET['indir'];
		$cmd = 'taxprinter.php?t=tax';
		
		if (!$this->username)
		    return false;
		
		//if ($this->username!=$this->get_printer_admin()) {

			    //if ($this->signer_service)//eafdss service enabled
		           $icons[] = $cmd."useprinter:Printer Users";
				   
			    $icons[] = $cmd ."confprinter:Printer Configuration";
                $icons[] = $cmd ."infprinter:Printer Info";	
				
				if ($this->fiscal_service)//tacfiscal service enabled..toggle button
				   $icons[] = 'taxfiscal.php:Tax fiscal';
				if ($this->cprint_service)
				   $icons[] = $cmd ."services:Tax services";   
				   
			    $icons[] = $cmd ."logout:logout";			
			
		    //RENDER ICONS
		    if ($iconsview) {
		        //print_r($icons);
		        foreach ($icons as $icon) { 
			
			        $icondata = explode(':',$icon);
			
			        if (is_file($this->icons_path.$icondata[1].'.png'))
			          $ifile = $urlicons.$icondata[1].'.png';
			        else
			          $ifile = $urlicons.'index.printer.png';
			   
			        $icco[] = "<a href='".$icondata[0]."'><img src='" . $ifile."' border=0 alt='".$icondata[1]."'></a>";
			        //$link = "<a href='".$icondata[0]."'>" . $icondata[1]  ."</a>";
			        $px = $p ? $p : '25%';
	                $attr[] = 'left;'.$px;
			    }	
                //print_r($icco);			
			    $ret = self::printline($icco,$attr,0,"center::100%::0::group_article_body::left::0::0::");			
		    }
		
		    return ($ret);			
		/*}
		
		$ret = parent::html_get_printer_menu($iconsview,$p);
		return($ret);*/
    }

    protected function printline($dat=null,$att=null,$isbold=false,$render=null) {
	    $ret = null;
		$isarray = is_array($att);
		
	    if (is_array($dat)) {
		
		   foreach ($dat as $i=>$f) {
		   
			   $data[$i] = $isbold ? '<strong>'.$f.'</strong>':$f; 
	           $attr[$i] = $isarray ? $att[$i] : $att;			      
		   }
		   
	       //$win = new window('',$data,$attr);
		   //$ret = $win->render($render);
		   $ret = "\r\n<table><tr>";
		   foreach ($data as $t=>$title) {
		     $attribute = explode(';',$attr[$t]);
		     $ret .= '<td align="'.$attribute[0].'" width="'.$attribute[1].'" valign="top">'.$title.'</td>';
		   }	 
		   $ret .= "</tr></table>";
		}
		
		return ($ret);
    }	
}
}
?>
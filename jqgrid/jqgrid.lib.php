<?php
if (!defined("JQGRID_DPC")) {
define("JQGRID_DPC",true);

$__DPC['JQGRID_DPC'] = 'jqgrid';

require_once("inc/jqgrid_dist.php");
//test******************************************************
/*		 $conn = mysql_connect(paramload('DATABASE','dbhost'), 
		                      paramload('DATABASE','dbuser'),
							  paramload('DATABASE','dbpwd'));

		  mysql_select_db(paramload('DATABASE','dbname'));
		
		  $charset = paramload('DATABASE','charset');
		  mysql_query("SET NAMES ".$charset);//'utf8'");
		  
          $sSQL = "select * from (select sysins,itmname,active," .
	            "cat1,cat2,cat3,cat4" .
				" from products) as o";		  
		  
		  $my_jgrid = new jqgrid();
		  $my_jgrid->select_command = $sSQL;
		  $my_jgrid->table = 'products';
		  $mygrid = $my_jgrid->render('xxx');
		  SetGlobal('mygrid',$mygrid);*/
//************************************************************
class jq_grid {

    var $_jgrid;

    function __construct($table,$sql=null,$gridcols=null,$gridparams=null,$gridactions=null){

		$conn = mysql_connect(paramload('DATABASE','dbhost'), 
		                      paramload('DATABASE','dbuser'),
							  paramload('DATABASE','dbpwd'));
		if ($conn) {					  
							  
		  mysql_select_db(paramload('DATABASE','dbname'));
		
		  $charset = paramload('DATABASE','charset');
		  mysql_query("SET NAMES ".$charset);//'utf8'");
		
		  //no need
	      //SetGlobal('conn',&$conn);//&$db);////global for jqgrid
	
          $this->_jgrid = new jqgrid();

		  // set few params
		  if (!empty($gridparams)) {
			//$grid["caption"] = "Sample Grid";
			//$grid["multiselect"] = true;
			
			// $grid["url"] = ""; // your paramterized URL -- defaults to REQUEST_URI
			//$grid["rowNum"] = 10; // by default 20
			//$grid["sortname"] = 'id'; // by default sort grid by this field
			//$grid["sortorder"] = "desc"; // ASC or DESC
			//$grid["caption"] = "Invoice Data"; // caption of grid
			//$grid["autowidth"] = true; // expand grid to screen width
			//$grid["multiselect"] = false; // allow you to multi-select through checkboxes
			// export XLS file
			// export to excel parameters - range could be "all" or "filtered"
			//$grid["export"] = array("format"=>"xlsx", "filename"=>"my-file", "sheetname"=>"test");
			// export PDF file
			// export to excel parameters
			//$grid["export"] = array("format"=>"pdf", "filename"=>"my-file", "heading"=>"Invoice Details", "orientation"=>"landscape");
			// export filtered data or all data
			//$grid["export"]["range"] = "filtered"; // or "all"

			//$grid["grouping"] = true; // 
			//$grid["groupingView"] = array();
			//$grid["groupingView"]["groupField"] = array("closed"); // specify column name to group listing
			//$grid["groupingView"]["groupColumnShow"] = array(false); // either show grouped column in list or not (default: true)
			//$grid["groupingView"]["groupText"] = array("<b>{0} - {1} Item(s)</b>"); // {0} is grouped value, {1} is count in group
			//$grid["groupingView"]["groupOrder"] = array("asc"); // show group in asc or desc order
			//$grid["groupingView"]["groupDataSorted"] = array(true); // show sorted data within group
			//$grid["groupingView"]["groupSummary"] = array(true); // work with summaryType, summaryTpl, see column: $col["name"] = "total";
			//$grid["groupingView"]["groupCollapse"] = false; // Turn true to show group collapse (default: false) 
			//$grid["groupingView"]["showSummaryOnHide"] = true; // show summary row even if group collapsed (hide) 
			//cols..see...example mygrid
			
			$this->_jgrid->set_options($gridparams);//$grid);
		  }
		
		  if (!empty($gridactions)) {
			/*$g->set_actions(array(	
						"add"=>true, // allow/disallow add
						"edit"=>true, // allow/disallow edit
						"delete"=>true, // allow/disallow delete
						"rowactions"=>true, // show/hide row wise edit/del/save option
						"search" => "advance", // show single/multi field search condition (e.g. simple or advance)
						"export"=>true, // show/hide export to excel option
						"autofilter" => true // show/hide autofilter for search					
							) 
						);*/
			$this->_jgrid->set_actions($gridactions);			
		  }				

		  if ($sql) {
			// you can provide custom SQL query to display data
			$this->_jgrid->select_command = $sql;
			
			// subqueries are also supported now (v1.2)
			// $g->select_command = "select * from (select * from invheader) as o";		
		  }
        		
		  // set database table for CRUD operations
		  $this->_jgrid->table = $table;//"clients";

		  if (!empty($gridcols)) { 
			// pass the cooked columns to grid
			$this->_jgrid->set_columns($gridcols);		
		  }
		
		  //$themes = array("redmond","smoothness","start","dot-luv","excite-bike","flick","ui-darkness","ui-lightness","cupertino","dark-hive");
		  //$i = rand(0,8);
		
		  $this->javascript();//<<<<<load js
        }//if connection
		
        //$this->javascript();	//in case of test	
	}
	
	function javascript() {
        $jsapi = "http://www.google.com/jsapi";
   
        $jquery = "google.load(\"jquery\", \"1.6.2\");";   
   
 	    $jscode = "";  	
 
		if (iniload('JAVASCRIPT')) {
			$js = new jscript;		   
		    //$js->load_js('http://www.google.com/jsapi',"",null,null,1);
		    //$js->load_js($jquery,"",1);
		    //$js->load_js('fpss.packed.js');
            //$js->load_js($jscode,"",1);			   
			
			//$js->load_css('xxx.css'); 
			
			//put into javascript root dir
			$js->load_css('javascripts/themes/redmond/jquery-ui.custom.css'); 
			$js->load_css('javascripts/jqgrid/css/ui.jqgrid.css');
            
			//put into javascript root dir
            $js->load_js('jquery.min.js');
			$js->load_js('jqgrid/js/i18n/grid.locale-en.js');			
			$js->load_js('jqgrid/js/jquery.jqGrid.min.js');
			$js->load_js('themes/jquery-ui.custom.min.js');
			unset ($js);
		}
			
	}
	
	function showgrid($name=null,$cols=null) {
	    //$myjgrid = GetGlobal('mygrid'); //test global
	    if (!$this->_jgrid) 
		    return ('Grid init error');
			//return ($myjgrid);
			
        // pass the cooked columns to grid
		if (!empty($cols))
			$this->_jgrid->set_columns($cols);			
	
		// generate grid output, with unique grid name as 'list1'
		$gname = $name ? $name : 'list1';
		$out = $this->_jgrid->render($name);//"list1");
		
		return ($out);
	}	

    function lookup($sql=null) {
	    if (!$sql) return;
		
		$str = $this->_jgrid->get_dropdown_values($sql);//"select distinct client_id as k, name as v from clients");
		return ($str);
    }	
	
};
}
?> 
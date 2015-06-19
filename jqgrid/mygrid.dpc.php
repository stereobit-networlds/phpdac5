<?php
if (!defined("MYGRID_DPC")) {
define("MYGRID_DPC",true);

$__DPC['MYGRID_DPC'] = 'mygrid';

//$a = GetGlobal('controller')->require_dpc('jqgrid/jqgrid.lib.php');
//require_once($a);

//extends functionality of jqgrid lib
//lib must be loaded into dpc file 
class mygrid {

    var $_grid, $_cols;
	var $paid_version;

    function __construct() {
			
		$this->_cols = array();
		
		$paid = remote_paramload('MYGRID','paid',$this->path);
		$this->paid_version = $paid ? true : false;
	}
	
	public function column($grid_id,$attr=false) {
	  if (!$grid_id) $grid_id ='grid0';//return;
	
	  if ($attr) {   
	    $at = explode('|',$attr);
		$col = array();
		$col["title"] = $at[1]?$at[1]:$at[0];//"Total";
		$col["name"] = $at[0];//"total";
		$col["editable"] = ($at[3]>0) ? true : false;		
		$col["search"] = ($at[6]>0) ? false : true; //no search
		$col["hidden"] = ($at[7]>0) ? true : false;
		$col["align"] = isset($at[8]) ? $at[8] : "left";
		
		if (is_numeric($at[2])) {
			
			if ($at[2]>20) { //big field..keep small edit textarea
			   $col["edittype"] = "textarea";
			   $col["width"] = "20";
			}
			else
			   $col["width"] = $at[2]?$at[2]:"10";
		}	
		elseif ($at[2]=='date') {
		    $col["formatter"] = "date"; // format as date
		    $col["formatoptions"] = array("srcformat"=>'Y-m-d',"newformat"=>'d/m/Y');  
			$col["width"] = '10';
	    }		
		elseif ($at[2]=='boolean') {
		    $col["edittype"] = "checkbox";
		    if ($at[4])
		        $col["editoptions"] = array("value"=>$at[4]);//"1:0"); //'10:$10;20:$20;30:$30;40:$40;50:$50'
			else		
				$col["editoptions"] = array("value"=>"1:0");
			$col["width"] = '5';
		}	
		elseif ($at[2]=='select') {
		    //echo 'a',$at[4];
			if ($this->paid_version) {
			
		    if (stristr($at[4],'select')) {//is a lookup sql query
			    //echo 'select query';
				// fetch data from database, with alias k for key, v for value
                //$str = $g->get_dropdown_values("select distinct client_id as k, name as v from clients");
				$str = $this->lookup($at[4]);
				if ($str) {
				    $col["formatter"] = "select"; // format as select
					$col["editoptions"] = array("value"=>$str); 
				}
				else
                    $col["editoptions"] = array("value"=>'lookup null'); 			
			}
			elseif (stristr($at[4],';')) {//only if many options exist
			    //echo 'select values';
			    $col["formatter"] = "select"; // format as select
		        $col["editoptions"] = array("value"=>$at[4], "multiple" => true);
			}	
			elseif ($at[4]) {//formater is just a value...
			    //echo 'just a value';
                $col["editoptions"] = array("value"=>$at[4]);			
			}	
			//else
				//$col["editoptions"] = array("value"=>$col["name"]);//the value per se ?
			
			$col["width"] = '10';
			}//paid
			else //not paid, edit as text
			    $col["width"] = '10';
	    }
        elseif ($at[2]=='link') {
            $col["link"] = $at[4];//"http://localhost/?id={id}"; // e.g. http://domain.com?id={id} given that, there is a column with $col["name"] = "id" exist
            $col["linkoptions"] = $at[5];//"target='_blank'"; // extra params with <a> tag
		    $col["width"] = $at[3] ? $at[3] : "10";//use editable attr for width
			$col["editable"] = false;//editable always false for link
        }		
		else {
		  $col["width"] = '10';
		  //if ($at[4]) //predefined text...for inputs???
			//$col["editoptions"] = array("value"=>$at[4]);
		}  
		  
		
		//search
        //$col["search"] = false;
		
		//$col["sortable"] = false; // this column is not sortable
		//$col["search"] = false; // this column is not searchable
		//$col["align"] = "center";
	    //$col["edittype"] = "checkbox";
		//$col["edittype"] = "textarea"; // render as textarea on edit
		//$col["editoptions"] = array("rows"=>2, "cols"=>20); // with these attributes		
		// To mask password field, apply following attribs
		# $col["edittype"] = "password";
		# $col["formatter"] = "password";

		// default render is textbox
		// $col["editoptions"] = array("value"=>'10');

		// can be switched to select (dropdown)
		# $col["edittype"] = "select"; // render as select
		# $col["editoptions"] = array("value"=>'10:$10;20:$20;30:$30;40:$40;50:$50'); // with these values "key:value;key:value;key:value"
		//$col["editoptions"] = array("value"=>"1:0"); // with these values "checked_value:unchecked_value"
		#$col["editoptions"] = array("value"=>'No:Not Booked eg. N, I,E;Yes:Yes it is Booked eg. N, I,E'); // with these values "key:value;key:value;key:value"
		//$col["editoptions"] = array("size"=>20); // with default display of textbox with size 20
		//$col["editrules"] = array("required"=>true, "edithidden"=>true); // and is required
		//$col["hidden"] = true;	
		//$col["formatter"] = "date"; // format as date
		// $col["formatoptions"] = array("srcformat"=>'Y-m-d',"newformat"=>'d/m/Y'); // @todo: format as date, not working with editing
		
		// default render is textbox
		//$col["editoptions"] = array("value"=>'10');
		//select
		// can be switched to select (dropdown)
		# $col["edittype"] = "select"; // render as select
		# $col["editoptions"] = array("value"=>'10:$10;20:$20;30:$30;40:$40;50:$50'); // with these values "key:value;key:value;key:value"
		
		//export
		//$col["export"] = false; // this column will not be exported
		//links
		//$col["link"] = "http://localhost/?id={id}"; // e.g. http://domain.com?id={id} given that, there is a column with $col["name"] = "id" exist
		//$col["linkoptions"] = "target='_blank'"; // extra params with <a> tag		
		//group
		//$col["summaryType"] = "sum"; // available grouping fx: sum, count, min, max
        //$col["summaryTpl"] = '<b>Total: ${0}</b>'; // display html for summary row - work when "groupSummary" is set true. search below

		
		$this->_cols[$grid_id][] = $col;
		return true;
	  }	
	  return false;
	}
	
	//..to be continued
	public function lookup($sql) {
	    //_grid,_jgrid is null before grid func..
		if (!$this->_grid) {
		  echo 'Init error';
		  return;
		}  
		
	    //$str = $this->_grid->_jgrid->get_dropdown_values($sql);
		//"select distinct client_id as k, name as v from clients");
        //$col["editoptions"] = array("value"=>$str);
		
		$str = $this->_grid->lookup($sql);
		return ($str);
	}
	
	public function mylookup($sql=null) {
	    if (!$sql) return;
		$db = GetGlobal('db');
		if ($db) {
		
			echo $sSQL; 
			$resultset = $db->Execute($sql,1);
            //$ret = $db->fetch_array_all($resultset);
            //print_r($ret);  	
            foreach ($resultset as $n=>$rec) {			
			   //echo $rec[0],'<br/>';
			   if ($rec[0])
				$ret[] = $rec[0].':'.$rec[0];
			}
			$str = implode(';',$ret);
			//echo $str;
			return $str;
		}
		return false;
	}
	
	public function printcolumns() {
	  print_r($this->_cols);
	}
	
	//http://stackoverflow.com/questions/5272850/is-there-an-api-in-jqgrid-to-add-advanced-filters-to-post-data/5273385#5273385
	public function jquery() {
	
	}
	
	public function grid($grid_id,$table,$sql=null,$mode=null,$caption=null,$sortname=false,$rowactions=false, $searchadv=false,$rows=false,$height=null,$width=null,$nofilter=false,$export=false,$desc=false) {
	    if (!$table) return 'table undefined';
		if (!$grid_id) $grid_id = 'grid0';
		$caption = $caption ? $caption : $grid_id;
		//echo $sql;
	    if (defined('JQGRID_DPC')) {
		
		  $gridparams["caption"] = $caption;
		  $gridparams["multiselect"] = false;//true;
          $gridparams["autowidth"] = $width ? false :true;
          if ($width) {
			$gridparams["width"] = $width;
          }		  
		  $gridparams["rowNum"] = $rows ? $rows : 20; 
		  $gridparams["sortname"] = $sortname ? $sortname : 'id'; // by default sort grid by this field
          $gridparams["sortorder"] = $desc ? "desc" : 'asc'; // ASC or DESC

		  if ($height) 
			$gridparams["height"] = $height;
			
		  $gridparams["add_options"] = array('width'=>'620');
		  $gridparams["edit_options"] = array('width'=>'620');			
		  
          //add
          if (stristr($mode,'+')) //not valid when getglobal call
            $actions['add'] = true;		  
	
	      switch ($mode) {
		    
			//case 'ed': $actions['edit'] = true;			
			case 'd': $actions['delete'] = true;
			case 'a': $actions['add'] = true;
			case 'e': $actions['edit'] = true;
		    case 'r': 
			default : if (!$actions['delete']) $actions['delete'] = false;
                      if (!$actions['edit']) $actions['edit'] = false;
                      if (!$actions['add']) $actions['add'] = false;					  
			          $actions['rowactions'] = $rowactions?true:false;
			          $actions['search'] = $searchadv?'advance':'simple';
					  $actions["export"] = $export?true:false; 
					  $actions["autofilter"] = $nofilter?false:true;
		  }
		  
		  $this->_grid = new jq_grid($table, $sql, null, $gridparams, $actions);
		  
		  if (is_array($this->_cols[$grid_id]))
			$ret = $this->_grid->showgrid($caption, $this->_cols[$grid_id]);
		  else
            $ret = $this->_grid->showgrid($caption);		  
		}
        else 
		  $ret = 'load jqgrid lib.';
		
	    return ($ret);
	}
};
}	
?>
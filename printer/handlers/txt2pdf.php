<?php

class txt2pdf
{
 protected $export_data, $import_data;
 
 public function __construct($import_data=null) {
    $this->import_data = $import_data;
 }
 
 public function execute() {
 
    
    $this->export_data = '123';	
    
    return $this->export_data;
 }
}


?>
<?php
$__DPCSEC['CPMDBREC_DPC']='1;1;1;1;1;1;1;1;1';

if ((!defined("CPMDBREC_DPC")) && (seclevel('CPMDBREC_DPC',decode(GetSessionParam('UserSecID')))) ) {
define("CPMDBREC_DPC",true);

$__DPC['CPMDBREC_DPC'] = 'cpmdbrec';

class cpmdbrec {

};
}
?>
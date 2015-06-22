<?php
$location = '../'.urldecode(base64_decode($_GET['turl'])).'&editmode=1';
header('location:'.$location);
?>


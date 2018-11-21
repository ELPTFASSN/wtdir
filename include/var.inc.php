<?php


/* connection vars */
/*
$server='10.1.1.3';
$port='3306';
$username='root';
$password='1199322426';
$database='misdir_dev';
$BusinessUnit="Waffletime Inc.,";
$ParentPath = '/misdir/';
*/
$timezone = 'Asia/Manila';
if(function_exists('date_default_timezone_set')) {date_default_timezone_set($timezone);}
	
include 'function.inc.php';
?>
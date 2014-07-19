<?php
require_once('config.php');
if(!defined('MONGO_USER')||!defined('MONGO_PASS')||!defined('MONGO_DB')||!defined('MONGO_HOST')){
	die("MongoDB Config not found !!!\n");
}
global $oMAConn, $oMADB;
$strConnString = sprintf('mongodb://%s', MONGO_HOST);
$oMAConn = new Mongo($strConnString, array(
	'username' => MONGO_USER,
    'password' => MONGO_PASS,
    'db'       => MONGO_DB
));
$oMADB   = $oMAConn->selectDB(MONGO_DB);
?>
<?php
    /** config for main database **/
	$config['dbtype']="mysql";
	$config['dbhost']="10.30.15.9";
	$config['dbuname']="hieutt";
	$config['dbpass']="123";
	$config['dbname']="mzd";
	$config['table_prefix']="";
    /** end **/

    /** configuration for zabbix database **/
    $config['dbtype_zabbix']="mysql";
	$config['dbhost_zabbix']="10.30.15.19";
	$config['dbuname_zabbix']="hieutt";
	$config['dbpass_zabbix']="hieutt123";
	$config['dbname_zabbix']="zabbix_prod";
	$config['table_prefix_zabbix']="";
    /** end **/
    define("ZABBIX_LOCATION","QTSC");
    define("ZABBIX_VERSION", "1.8");
    define("BACKEND_ID", 2);
	/** MA Mongodb **/
    define("MONGO_HOST","10.30.15.8");
    define("MONGO_USER", "u_ma");
    define("MONGO_PASS", "Ma20!3");
    define("MONGO_DB", "monitoring_assistant");
	/** end **/
	

?>

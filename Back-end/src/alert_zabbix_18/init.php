<?php
	/**
	 * @author Quang Minh
	 * @copyright VNG corp
     * @build date 19-04-2011
     * @version 1.0
     * @description: Init connection database, configuration ... for website
     * @PHPVersion > 5.0
	 */
?>
<?php
	ini_set('display_errors',0);
	ini_set("default_charset", "utf8");
	error_reporting(0);
	ob_start();

	/** include php file **/
    include_once "include/config.php";
    include_once "include/constants.php";
    /** end **/
    $dbconn_zabbix = null;
    $dbconn = null;
	/********************* Connect to DB SERVER main **********************/
	// $dbconn = mysql_connect($config['dbhost'],$config['dbuname'],$config['dbpass'],false);
	// if (!$dbconn) {
		// raise_DBMsgError($dbconn, __FILE__ , __LINE__, "Error connecting to db ".$config['dbname']);
	// }
    // mysql_select_db($config['dbname'],$dbconn);
	/********************* END ***************************************/

    /********************* Connect to DB SERVER zabbix **********************/
	$dbconn_zabbix = mysql_connect($config['dbhost_zabbix'],$config['dbuname_zabbix'],$config['dbpass_zabbix'],true);
	if (!$dbconn_zabbix) {
		raise_DBMsgError($dbconn_zabbix, __FILE__ , __LINE__, "Error connecting to db ".$config['dbname_zabbix']);
	}
    mysql_select_db($config['dbname_zabbix'],$dbconn_zabbix);
	/********************* END ***************************************/

	$current_alert_table = _get_current_alert_table();
	if($current_alert_table){
		define('ZABBIX_MONITOR_ALERT_TABLE', $current_alert_table['table']);
    }
?>

<?php
    /**
     * Raise error connection to db
     **/
    function raise_DBMsgError($db='',$prg='',$line=0,$message='Error accesing to the database') {
		global $config;
		$lcmessage = $message . "<br>" .
								"Program: " . $prg . " - " . "Line N.: " . $line . "<br>" .
								"Database: " . $db . "<br> ";
		$lcmessage .= "Error (" . mysql_error() . ")<br>";
		die($lcmessage);
	}

	function _get_current_alert_table(){
		global $dbconn;
		$sql = "SELECT id,`type`,`table`,`previous_table`,`status`,`table_created_date`,`created_date`" .
				" FROM " . MZD_PARTITION_MASTER_TABLE . ' WHERE `type`=' .
				PARTITION_TABLE_TYPE_ALERT .
				" AND `status`=" . PARTITION_TABLE_STATUS_ON . " AND deleted='0' LIMIT 1";
		if(MODE_DEBUG){
			echo "$sql\n";
		}
		$result = mysql_query($sql, $dbconn);
		if($result){
			if(mysql_num_rows($result) > 0) return mysql_fetch_assoc($result);
		}
		return null;
	}
?>
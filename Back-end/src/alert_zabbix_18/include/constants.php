<?php
    define("MODE_DEBUG",false);
    define("MODE_DEBUG_TO_FILE",false);
    /* DEFINE PATH CORE */
    define("PATH_CORE","core/");
    define("NUM_RECORD_PROGRESS",10000);
    /* DEFINE LINK CONNECT TO XML FILE */
    define("LINK_FILE","http://10.30.23.23/nagios_stat.xml");
    define("TIMELINE_STORE_DATA",2592000); //1 Thang

    /* DEFINE NUMBER OF TRIGGER WHEN GET IN HOST */
    define("NUMBER_GET_TRIGGER_IN_HOST",50);
    define('ZBX_PREG_NUMBER', '([\-+]?[0-9]+[.]{0,1}[0-9]*[A-Z]{0,1})');
    define('ZBX_PREG_PRINT', '^\x00-\x1F');
    define("ZBX_FLAG_TRIGGER",0);
    define('ZBX_FLAG_EVENT',1);
    define('ITEM_VALUE_TYPE_LOG',2);
    define('ITEM_VALUE_TYPE_FLOAT',		0);
	define('ITEM_VALUE_TYPE_STR',		1);
	define('ITEM_VALUE_TYPE_LOG',		2);
	define('ITEM_VALUE_TYPE_UINT64',	3);
	define('ITEM_VALUE_TYPE_TEXT',		4);
    define('HIDE',0);
    define('SHOW',1);
    /* DEFINE STATUS FOR ALERT */
    define("STATUS_OK",0);
    define("STATUS_WARNING",1);
    define("STATUS_CRITICAL",2);

    define("STATUS_UNKOWN",3);

    define("Z_STATUS_DISATER",5);
    define("Z_STATUS_HIGH",4);
    define("Z_STATUS_AVERAGE",3);
    define("Z_STATUS_WARNING",2);
    define("Z_STATUS_INFORMATION",1);
    define("Z_STATUS_NOT_CLASSFIED",0);

    /* DEFINE SEND TO ZABBIX INFO */
    define("Z_MSG_WARNING","[Warning]");
    define("Z_MSG_CRITICAL","[Disaster]");

    /* DEFINE MAX NUMBER RESEND TO ZABBIX */
    define("MAX_RESEND_TO_ZABBIX",10);

    define("EXISTED",1);
    define("NOT_EXISTED",0);
    define("SRC_FRM_ZABBIX","Zabbix");
    define("SRC_FRM_G2","G2");
    define("LIMIT_TIME",24*60*60);
    /* DEFINE COMMAND SEND TO ZABBIX */
    define("CMD_EXEC","/etc/zabbix/bin/zabbix_sender -z10.30.15.16 -sTOM_Zabbix_Master -kG2_Monitor -o \"{value}\" -v");

    /* DEFINE TABLE */
    define("ZABBIX_MONITOR_ALERT_TABLE","monitoring_assistant_alerts");
    define("ZABBIX_MONITOR_ALERT_MAINTEN_TABLE","zabbix_monitor_alerts_maintenances");
    define("ZABBIX_EVENTS_TABLE","events");
    define("ZABBIX_TRIGGERS_TABLE","triggers");
    define("ZABBIX_FUNCTION_TABLE","functions");
    define("ZABBIX_HOSTS_TABLE","hosts");
    define("ZABBIX_ITEMS_TABLE","items");
	define("ZABBIX_PRODUCTS_TABLE", "products");
	define("ZABBIX_DEPARTMENTS_TABLE", "departments");

    /* DEFINE ZABBIX MARCO */
    define("MACRO_ZABBIX_HOST","{HOSTNAME}");
    define("MACRO_ZABBIX_LASTVALUE1","{ITEM.LASTVALUE1}");
    define("MACRO_ZABBIX_LASTVALUE","{ITEM.LASTVALUE}");
    define("MACRO_ZABBIX_LASTVALUE_","$1");

	/* DEFINE FOR PARTITIONING TABLE */
    define("MZD_ORIGINAL_ALERT_TABLE", 'zabbix_monitor_alerts');
    define("MZD_ORIGINAL_CCU_DASHBOARD_TABLE", 'mzd_ccu_dashboard');
    define("MZD_PARTITION_MASTER_TABLE", 'mzd_partition_master');
    define("PARTITION_TABLE_STATUS_OFF", 0);
    define("PARTITION_TABLE_STATUS_ON", 1);
    define("PARTITION_TABLE_TYPE_ALERT", 1);
    define("PARTITION_TABLE_TYPE_CCU_DASHBOARD", 2);
?>

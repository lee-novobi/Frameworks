

//LogParser
#define RECORD_ID "_id"
#define EVENT_ID "eventid"
#define STATUS "status"
#define ITSM_STATUS "itsm_status"
#define ITSM_STATUS_NOTI "itsm_status_notified"
#define ITEM_ID "itemid"
#define HOST_ID "hostid"
#define HOST_NAME "host"
#define NAME "name"
#define APP_NAME "app_name"
#define CLOCK "clock"
#define ZBX_SERVER_ID "zabbix_server_id"
#define SERVER_ID "serverid"
#define ZBX_SERVERID "zbx_serverid"
#define TRIGGER_ID "triggerid"
#define EXPRESSION "expression"
#define DESCRIPTION "description"
#define PARA_VALUE "value"
#define LAST_VALUE "last_value"
#define PRE_VALUE "pre_value"
#define PRIORITY "priority"
#define ALERT_ID "alertid"
#define KEY_ "key_"
#define VALUE_TYPE "value_type"
#define UNITS "units"
#define FUNCTION_ID "functionid"
#define FUNCTION_NAME "function"
#define PARAMETER "parameter"
#define VALUE_CHANGED "value_changed"
#define IS_AVAILABLE "available"
#define MAINTENANCE "maintenance"
#define MAINTENANCE_FROM "maintenance_from"
#define PRIVATE_INTERFACE "private_interfaces"
#define PUBLIC_INTERFACE "public_interfaces"
#define SERVER_NAME "server_name"
#define SERVER_KEY "server_key"
#define LAST_UPDATED "last_updated"
#define VID "vid"
#define IS_SHOW "is_show"
#define INTERNAL_STATUS "internal_status"
#define EXTERNAL_STATUS "external_status"
#define SOURCE_FROM "source_from"
#define INC_CASE_FROM "from"
#define INC_CASE_TO "to"
#define IMPACT_LEVEL "level"
#define ZBX_SOURCE_FROM_VAL "Zabbix"
#define SOURCE_ID "source_id"
#define TITLE "title"
#define DEPARTMENT "department"
#define DEPARTMENT_ALIAS "department_alias"
#define DEPARTMENT_CODE "department_code"
#define PRODUCT "product"
#define PRODUCT_ALIAS "product_alias"
#define PRODUCT_CODE "product_code"
#define ATTACHMENTS "attachments"
#define NUM_OF_CASE "num_of_case"
#define AFFECTED_DEALS "affected_deals"
#define IS_ACK "is_ack"
#define ACK_MSG "ack_msg"
#define ALERT_MSG "alert_message"
#define ITSM_INC_ID "itsm_incident_id"
#define CREATE_DATE "create_date"
#define UPDATE_DATE "update_date"
#define OUTAGE_START "outage_start"
#define ZBX_LOCATION "zabbix_location"
#define ZBX_HOST_ID "zabbix_hostid"
#define ZBX_NAME "zbx_name"
#define ZBX_HOST "zbx_host"
#define ZBX_GROUP_ID "zabbix_groupid"
#define ZBX_GROUP_NAME "zabbix_groupname"
#define ZBX_ITEM_ID "zabbix_itemid"
#define ZBX_ITEM_NAME "zabbix_itemname"
#define ZBX_EVENT_ID "zabbix_eventid"
#define ZBX_TRIGGER_ID "zabbix_triggerid"
#define ZBX_TRIGGER_PRI "zabbix_trigger_priority"
#define ZBX_TRIGGER_DES "zabbix_trigger_description"
#define CMDB_DEPT_ALIAS "cmdb_department_alias"
#define CMDB_PROD_ALIAS "cmdb_product_alias"
#define IS_SYNC "is_sync"
#define MAP_SRC_PRODUCT "src_product"
#define MAP_ITSM_PRODUCT "itsm_product"
#define MAP_SOURCE "source"
#define WEB_TEST "web.test"
#define WEB_TEST_IN "web.test.in"
#define WEB_TEST_FAIL "web.test.fail"
#define WEB_TEST_TIME "web.test.time"
#define WEB_TEST_RSPCODE "web.test.rspcode"
#define WEB_TEST_ERROR "web.test.error"
#define FAIL_NUM "fail"
#define RESPONSE_CODE "response_code"
#define SPEED "speed"
#define STEP_SPEED "step_speed"
#define STEP_NAME "step_name"
#define RESPONSE_TIME "response_time"
#define WEB_KEY "web_key"
#define ERR_MGS "err_msg"

#define RAW_ALERT_STATUS_INIT        	0
#define RAW_ALERT_STATUS_CENTRALIZED    1
#define RAW_ALERT_STATUS_ITSM_OPENNED   2
#define RAW_ALERT_STATUS_ITSM_CLOSED    3
#define RAW_ALERT_STATUS_ITSM_REOPEN    4
#define RAW_ALERT_STATUS_ITSM_REJECTED  5
#define RAW_ALERT_STATUS_ITSM_RESOLVED  6



// G8 FIELD
#define G8_SOURCE_FROM_VAL "G8"

// CS FIELD
#define CS_SOURCE_FROM_VAL "CS"
#define CS_ERROR_MSG "error_message"
#define CS_SERVER_NAME "server_name"

//Workflow
#define ACTION_TREE "action_tree"
#define NODE_VALUE "par"
#define CHILD_ARRAY "child"
#define METHOD_ID "method"
#define CONDITION_ID "condition"
#define ROOT_VALUE "root"
#define WF_NAME "name"


//Log file
#define EVENTGROUP "EVENT"
#define TRIGGERGROUP "TRIGGER"
#define FUNCTIONGROUP "FUNCTION"
#define ITEMGROUP "ITEM"
#define ALERTGROUP "ALERT"
#define HOST_WEB_GROUP "HOST_WEB"
#define HOSTGROUP "HOST"
#define HISTORYGROUP "HISTORY"
#define ZBX_ALERT_SYNC_GROUP "ZBX_ALERT_SYNC"
#define G8_ALERT_SYNC_GROUP "G8_ALERT_SYNC"
#define CS_ALERT_SYNC_GROUP "CS_ALERT_SYNC"
#define UPDATE_STT_ALERT_GROUP "UPDATE_STT_ALERT"

#define MONGODB_MA "MONGODB_MA"
#define MONGODB_ODA "MONGODB_ODA"
#define MYSQL_MDR "MYSQL_MDR"
#define MYSQL_MA "MYSQL_MA"
#define INFO "INFO"
#define LAST "LAST"
#define TAIL "Tail"
#define POS "Position"
#define LOGPATH "LogPath"
#define INFOPATH "InfoPath"
#define PARTITION "IsPart"
#define PERIOD "Period"
#define DATETIME "Datetime"
#define HOST "Host"
#define USER "User"
#define PASS "Password"
#define SRC "Source"
#define PORT "Port"
#define DB "DBName"
#define STOPPARSING "Stop"
#define PARTITION_DAY "PartitionDay"
#define ALERT_NUM_OF_CASE "AlertNumOfCase"
#define iParseSleepTime 1

//=============

#define SYSTEM_INFO "system.info"
#define VB_SYSTEM_INFO "vb.system.info"
#define INVALID_MAC_ADDRESS "00:00:00:00:00:00"
#define SPEC_MAC_ADDRESS "00:50:56"

//===Struct===

struct ConnectInfo
{
	string strHost;
	string strUser;
	string strPass;
	string strSource;
	string strPort;
	string strDBName;
};


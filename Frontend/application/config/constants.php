<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
|--------------------------------------------------------------------------
| Icon types and links
|--------------------------------------------------------------------------
|
| These modes are used when working with icon template library
|
*/

define('ICON_PATH',		'asset/images/metro/');
define('ICON_BG_DARK',	'dark/'); 
define('ICON_BG_LIGHT',	'light/'); 
define('ICON_DEFAULT_WIDTH', '24px');

define('ICON_TYPE_EDIT', 	'Edit');
define('ICON_TYPE_CALL', 	'Call');
define('ICON_TYPE_DELETE', 	'Delete');
define('ICON_TYPE_ADD', 	'Add');
define('ICON_TYPE_ACK', 	'Ack');
define('ICON_TYPE_INC', 	'Incident');

define('ICON_IMG_EDIT', 	'appbar.edit.png');
define('ICON_IMG_CALL', 	'appbar.phone.png');
define('ICON_IMG_DELETE', 	'appbar.delete.png');
define('ICON_IMG_ADD', 		'appbar.add.png');
define('ICON_IMG_ACK', 		'appbar.checkmark.pencil.png');
define('ICON_IMG_INC', 		'appbar.alert.png');
define('ICON_IMG_HISTORY', 		'appbar.book.contact.png');


define('PAGER_SIZE', 10);

define('SECONDS_1_HOUR',  3600);
define('SECONDS_2_HOUR',  7200);
define('SECONDS_3_HOUR',  10800);
define('SECONDS_6_HOUR',  21600);
define('SECONDS_12_HOUR', 43200);
define('SECONDS_1_DAY',   86400);
define('SECONDS_2_DAY',   172800);
define('SECONDS_3_DAY',   259200);
define('SECONDS_1_WEEK',  604800);
define('SECONDS_2_WEEK',  1209600);
define('SECONDS_1_MONTH', 2592000);
define('SECONDS_3_MONTH', 7776000);

define('DEFAULT_HISTORY_LENGTH', SECONDS_1_WEEK);
define('DEFAULT_HISTORY_LENGTH_CHART', SECONDS_1_DAY);

define('COOKIE_SESSION_KEY', 'monitoring_assistant_session');

define('ITEM_VALUE_INT', 3);
define('ITEM_VALUE_FLT', 0);
/* End of file constants.php */
/* Location: ./application/config/constants.php */
define('SEPARATOR', ';');

define('CLT_PREFIX',            	'');
define('CLT_ALERTS',            	CLT_PREFIX . 'alerts');
define('CLT_CS_ALERTS',         	CLT_PREFIX . 'cs_alerts');
define('CLT_MA_ALERTS',         	CLT_PREFIX . 'monitoring_assistant_alerts');
define('CLT_MA_ALERTS_ACK',     	CLT_PREFIX . 'alerts_ack');
define('CLT_MA_IGNORED_TRIGGER',	CLT_PREFIX . 'ignored_trigger');
define('CLT_ATTACHMENT',			CLT_PREFIX . 'external_attachments');
define('CLT_G8_ALERTS',				CLT_PREFIX . 'g8_alerts');
define('CLT_FLAGS',					CLT_PREFIX . 'flags');
define('CLT_TOP_MAINTENANCES', 		CLT_PREFIX . 'top_maintenances');

//define('TBL_PREFIX',            'z_');
define('TBL_PREFIX',            '');

define('TBL_ACTIVE_TABLE',                    	TBL_PREFIX . 'monitoring_assistant_changes');

define('TBL_INCIDENT_CREATE_HISTORY',          	TBL_PREFIX . 'incident_create_history');
define('TBL_INCIDENT_UPDATE_HISTORY',          	TBL_PREFIX . 'incident_update_history');
define('TBL_INCIDENT_FOLLOW',               	TBL_PREFIX . 'incident_follow');
define('TBL_CHANGE_FOLLOW',               		TBL_PREFIX . 'change_follow');
define('TBL_CHANGE_HISTORY',               		TBL_PREFIX . 'change_history');
define('TBL_INCIDENT_HISTORY',               	TBL_PREFIX . 'incident_history');
define('TBL_AREA',                    		  	TBL_PREFIX . 'area');
define('TBL_SUBAREA',                    	  	TBL_PREFIX . 'subarea');
define('TBL_ASSIGNEE',                 		  	TBL_PREFIX . 'assignee');
define('TBL_ASSIGNMENT_GROUP',					TBL_PREFIX . 'assignment_group');
define('TBL_DEPARTMENT',                 		TBL_PREFIX . 'department');
define('TBL_PRODUCT',               			TBL_PREFIX . 'product');
define('TBL_BUG_CATEGORY',               	  	TBL_PREFIX . 'bug_category');
define('TBL_UNIT',               	  	  	  	TBL_PREFIX . 'unit');
define('TBL_USER', 								TBL_PREFIX . 'user');
define('TBL_AVAYA', 							TBL_PREFIX . 'avaya');
define('TBL_ACTION_HISTORY', 					TBL_PREFIX . 'action_history');
define('TBL_VNG_STAFF_LIST', 					TBL_PREFIX . 'vng_staff_list');
define('TBL_SHIFT_TRANSFER_INFO', 				TBL_PREFIX . 'shift_transfer_info');
define('TBL_SHIFT_SCHEDULE_ASSIGN', 			TBL_PREFIX . 'shift_schedule_assign');
define('TBL_CRITICAL_ASSET',					TBL_PREFIX . 'critical_asset');
define('TBL_NEW_ROOT_CAUSE',					TBL_PREFIX . 'new_root_cause_category');
define('TBL_DETECTOR',							TBL_PREFIX . 'detector_category');
define('TBL_CAUSED_EXTERNAL_DEPT',				TBL_PREFIX . 'cause_external_category');
define('TBL_LOCATION',							TBL_PREFIX . 'location_category');
define('TBL_AFFECTED_CI',						TBL_PREFIX . 'affected_ci');
define('TBL_INCIDENT_TRACKING_CHANGES',			TBL_PREFIX . 'incident_tracking_changes');
define('TBL_USER_ROLE_PRODUCT',					TBL_PREFIX . 'user_role_product');
define('TBL_ROLE',								TBL_PREFIX . 'role');
define('TBL_INCIDENT_ESCALATION',				TBL_PREFIX . 'incident_escalation');
define('TBL_SDK_FOLLOW_INCIDENT_NOTIFICATION',	TBL_PREFIX . 'sdk_follow_incident_notification');
define('TBL_SE_REPORT_INCIDENT_NOTIFICATION',	TBL_PREFIX . 'se_report_incident_notification');
define('TBL_SHIFTSCHEDULE',						TBL_PREFIX . 'shiftschedule');
define('TBL_SDK_SERVICE_SUPPORT',				TBL_PREFIX . 'sdk_service_support');

define('SERVER_ICON_PATH',  'asset/images/icons/servers/');
define('SERVICE_ICON_PATH', 'asset/images/icons/services/');
define('DB_ICON_PATH',      'asset/images/icons/dbs/');

define('ZABBIX_SERVER_ID_QUANG_TRUNG', 	2);
define('ZABBIX_SERVER_ID_HOA_LAC', 		3);

define('LOCATION_QUANG_TRUNG', 'Quang Trung');
define('LOCATION_HOA_LAC', 'Hòa Lạc');

define('COLOR_RED', 	'#FF0000');
define('COLOR_GREEN', 	'#00FF00');
define('COLOR_BLUE', 	'#0000FF');

define('ACTIVE_YES', '1');
define('ACTIVE_NO', '2');

define('YES', 1);
define('NO', 0);

define('DATA_TYPE_INTEGER', 1);
define('DATA_TYPE_STRING', 2);
define('DATA_TYPE_FLOAT', 3);
define('DATA_TYPE_DATETIME', 4);

define('INCIDENT_STATUS_INITIALIZE', 1);

define('INCIDENT_UPDATE_STATUS_INITIALIZE', 0);
define('INCIDENT_UPDATE_STATUS_FAIL', 2);
/* contact point */
define('IS_ITSM_DEPARTMENT', 1);
define('IS_NOT_ITSM_DEPARTMENT', 0);
define('IS_ITSM_PRODUCT', 1);
define('IS_NOT_ITSM_PRODUCT', 0);
define('STRING_LIST', 'List...');
define('NO_ROLE', 'No role');
define('NOT_HR', 'USER\'S DEPARTMENT INFO NOT FOUND IN HR DATABASE.');
define('USER_SDK', 'USER\'S DEPARTMENT INFO IN SDK DB (');
define('USER_HR', ') IS DIFFERENT FROM USER\'S DEPARTMENT INFO IN HR DB (');
define('COLOR_USER_NOT_MATCH_HR_DB', '#FF7575');
define('COLOR_USER_NOT_MATCH_SDK_VS_HR', '#FFFF94');
define('LEVEL_ESCALATION_PREFIX', 'Level_Escalation_');

/* call mobile and send sms */
define('FORMAT_MYSQL_DATE', 'Y-m-d');
define('FORMAT_MYSQL_TIME', 'H:i:s');
define('FORMAT_MYSQL_DATETIME', FORMAT_MYSQL_DATE . ' ' . FORMAT_MYSQL_TIME);
define('CONTACT_ACTION_TYPE_SMS', 'CONTACT_ACTION_TYPE_SMS');
define('NOTIFY_ACTION_TYPE_CALL', 'NOTIFY_ACTION_TYPE_CALL');
define('CONTACT_ACTION_TYPE_CALL_SUCCESS', 'CONTACT_ACTION_TYPE_CALL_SUCCESS');
define('CONTACT_ACTION_TYPE_CALL_FAIL', 'CONTACT_ACTION_TYPE_CALL_FAIL');
define('TIMEZONE_HCM', 'Asia/Ho_Chi_Minh');
define('PHONE_NUMBER_INVALID_MESSAGE', "Phone number is invalid: ");
define('LENGTH_MORE_THAN_160_CHARS_MESSAGE', "Length of message > 160 characters: ");
define('OPERATION_INVALID_MESSAGE', "Operation invalid: ");
define('INVALID_SERVICEID_AND_COMMAND_CODE_MESSAGE', "Invalid ServiceID and Command Code: ");
define('DUPLICATE_MT_MESSAGE', "Duplicate MT: ");
define('SYSTEM_BUSY_MESSAGE', "System is busy: ");
define('INVALID_SIG_PARTNER_MESSAGE', "Invalid Sig partner: ");
define('INVALID_REQUESTID_MESSAGE', "RequestID is invalid: ");
define('LENGTH_PHONE_NUMBER_MORE_THAN_20_CHARS', "Length of PhoneNumber more than 20 character(s): ");
define('NULL_PHONE_NUMBER_MESSAGE', "PhoneNumber is null: ");
define('NULL_MESSAGE_SENDED', "Message is null: ");
define('INVALID_PHONE_NUMBER', "Invalid phone number: ");
define('SYSTEM_ERROR_MESSAGE', "System error: ");
define('STRING_NOTE', 'ok');
define('MESSAGE_TYPE_SUCCESS', "success");
define('MESSAGE_TYPE_ERROR', "error");
define('AFFECTED_CODE', 1);
define('NO_AFFECTED_CODE', 2);
define('API_CALL_AVAYA', 'http://10.30.15.247/autodialer/Telephony.aspx?ext=');
define('API_CALL_AVAYA_SUFFIX', "&phone=9");
define('API_SEND_SMS', 'http://10.30.15.16/services/SendSMS.php?phonenumber=');
define('API_SEND_SMS_SUFFIX', "&msg=");
define('SDK_DEPARTMENT_NAME', 'sdk');

define('NO_PHONE', 'No phone');
define('NO_EMAIL', 'No email');
define('NO_ACTION', 'No action');
define('NO_NAME', 'No name');
define('NO_DEPT', 'No department');
define('EXIST_STRING', 'exist');
define('NOT_EXIST_STRING', 'not_exist');
define('INSERTDB_ERROR', 'insert_error');
define('ALREADY', 'already');
define('NULL_STRING', 'NULL');
define('NOT_ENOUGH', 'not_enough');
define('NO_EXT', 'no_ext');
define('INVALID_EMAIL', 'invalid_email');
define('UNKNOWN', 'Unknown');
define('OTHERS', 'Others');
define('ALL', 'All');
//define('LOST_DATA', 'lost_data');
define('ALERT_HIDE',                  0);
define('ALERT_SHOW',                  1);
define('ALERT_STOP_BY_EXT',           2);
define('ALERT_STOP_BY_SDK_ACK_NOINC', 3);
define('ALERT_STOP_BY_SDK_ACK_INC',   4);

define('IS_ACKED',    1);
define('IS_NO_ACKED', 0);
define('ACK_NO_INC', 'NO INC');
define('ACK_INC', 'INC');
define('UNLIMITED', 1000000000);

define('STATUS_TRANSFER', 1);
define('STATUS_ACCEPTED', 2);
define('STATUS_REJECTED', 3);
define('STATUS_INIT', 4);
define('STATUS_TRANSFER_REJECTED', 5);

define('SDK', 'sdk');
define('HR_VNG', 'hr_vng');
define('INCIDENT_STATUS_CLOSED', 'closed');
define('INCIDENT_CLOSED_COLOR', '#FF80C0');

define('DEFAULT_ASSIGMENT_GROUP_LEVEL', '_L1');

/* define type issue - add ack alert */
define('ISSUE_TYPE_NO_INC', 'NO_INCIDENT');
define('ISSUE_TYPE_INC', 'INCIDENT');
define('ISSUE_TYPE_NEW_SERVER', 'NEW_SERVER');
define('ISSUE_TYPE_IN_MAINTENANCE', 'IN_MAINTENANCE');
define('ISSUE_TYPE_BEING_CONFIGURED', 'BEING_CONFIGURED');

define('SOURCE_FROM_CS',     'cs');
define('SOURCE_FROM_G8',     'g8');
define('SOURCE_FROM_ZABBIX', 'zabbix');
define('SOURCE_FROM_SO6',    'so6');
define('SOURCE_FROM_PROMOTION', 'promotion');
define('SOURCE_FROM_DC', 	'dc');
define('ATTACHMENT_WEB_PATH', 'https://sdk.vng.com.vn/monitor/attachments/');
define('ATTACHMENT_CHECK_INTERVAL', 3000);

/* message information for call action */
define('SUCCESS', 'success');
define('CALL_TIME_OUT', 'call_time_out');
define('NO_FOUND_AVAYA', 'no_found_avaya');

define('CALL_SUCCESS', 'Call success');
define('CALL_FAIL', 'Call fail');
define('FORMAT_DATE', 'd-m-Y');

define('INCIDENT_NOTI_TYPE', 1);
define('SE_REPORT_NOTI_TYPE', 2);

define('INC_PREFIX', 'IM');
/* production link */
define('ODA_HOST_DETAIL_URL', 'https://monitor.vng.com.vn/hostgroup/host_info/');
/* dev link */ 
// define('ODA_HOST_DETAIL_URL', 'http://oda.sdk-dev.vng.com.vn/hostgroup/host_info/');

define('CHANGE_STATUS_INITIAL', 'initial');

define('UPDATED_STATUS', 'updated');
define('NOT_UPDATED_STATUS', 'not_updated');
?>
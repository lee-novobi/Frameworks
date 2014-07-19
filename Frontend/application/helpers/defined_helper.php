<?php
global $arrDefined;
$arrDefined['location'][]               = 'HL';
$arrDefined['location'][]               = 'QT';

$arrDefined['unuse_ip'] = array('0.0.0.0', '10.72.0.218');
$arrDefined['department_special'] = array('TGM', 'GMT', 'GDM');

$arrDefined['auto_refresh_page']['alert'] = array('alert_list' => 30, 'alert_list_history' => 30 /* Refesh Interval (seconds) */);
$arrDefined['source_allow_show_numof_case'] = array('cs', '123pay', 'g8', 'promotion');
$arrDefined['raw_alert_tbl_map'] = array('cs' => CLT_CS_ALERTS, 'g8' => CLT_G8_ALERTS, 'zabbix' => CLT_ALERTS);
$arrDefined['source_from'] = array(SOURCE_FROM_CS => 'CS', SOURCE_FROM_DC => 'DC', SOURCE_FROM_G8 => 'G8', SOURCE_FROM_ZABBIX => 'Zabbix', SOURCE_FROM_SO6 => 'SO6', SOURCE_FROM_PROMOTION => 'Promotion');
$arrDefined['source_has_attachment'] = array('g8');
$arrDefined['critical_asset_require'] = array('sdk','sns','dc','ird');
$arrDefined['change_view'] = array('N'=>'new', 'F'=>'follow', 'A'=>'all');
// $arrDefined['change_function'] = array('new'=>'new_change', 'follow' => 'follow_change', 'all' => 'all_changes');
?>
<?php
    require_once ("init.php");
    require_once "include/f_index.php";
    require_once (PATH_CORE."class.xmlparse.php");
    require_once (PATH_CORE."class.zabbix.alerts.php");
    require_once (PATH_CORE."class.zabbix.triggers.php");

	if(!defined('ZABBIX_MONITOR_ALERT_TABLE')) exit("Cannot get current alert table\n");
	ini_set('display_errors', 1);
	ini_set('mongo.native_long', 1);
	error_reporting(E_ALL);
    $oCZabbixAlerts     = new CZabbixAlerts();
    $oCZabbixTriggers   = new CZabbixTriggers();

    // Lay thong tin triggers
    $triggers = $oCZabbixAlerts->_getExistedTrigger(5);

    // Lay thong tin host
    $count_triggers = 0;
    $list_triggerid = "";
    $tmp_hosts = array();
    $hosts = array();
    $hostsid = array();
    $total_triggers = count($triggers);
    $count = 0;
    foreach($triggers as $key => $value){
        $list_triggerid .= $key.",";
        $count_triggers++;
        if($count_triggers == NUMBER_GET_TRIGGER_IN_HOST || $count == ($total_triggers-1)){
			echo $count_triggers."\n";
            $list_triggerid = substr($list_triggerid,0,-1);
            $tmp_hosts[] = $oCZabbixAlerts->_getExistedHost($list_triggerid);
            $count_triggers = 0;
            $list_triggerid = "";
        }
        $count++;
    }
    // Xu ly thong tin host
    $hosts = $oCZabbixAlerts->_progressHostInfo($tmp_hosts);
    // Tao thong tin hostid
    foreach($hosts as $key => $value){
        $hostsid[] = $key;
    }
    $arrEventid = "";
    foreach($triggers as $tnum => $trigger){
        $data = $oCZabbixAlerts->_getInfoEvent($trigger['triggerid']);
        if((int)$data['eventid'] == 0){
            continue;
        }
        $found = false;
        foreach($hosts as $v_hosts){
            if(in_array($trigger['triggerid'],$v_hosts['triggers'])){
                $trigger['host']   = $v_hosts['host'];
                $trigger['hostid'] = $v_hosts['hostid'];
                $trigger['ip']     = $v_hosts['ip'];
                $trigger['port']   = $v_hosts['port'];
                $found = true;
            }
        }
        if($found == false){
            continue;
        }
        $msg = $oCZabbixTriggers->_expand_trigger_description_by_data($oCZabbixTriggers->_zbx_array_merge($trigger, array('clock'=>$data['clock'])) ,ZBX_FLAG_EVENT);
        $trigger['msg'] = $msg;
        $trigger['eventid'] = $data['eventid'];
        $trigger['acknowledged'] = $data['acknowledged'];
        $trigger['value'] = $data['value'];
        $trigger['clock'] = $data['clock'];
        $arrEventid[] = $data['eventid'];
        if($oCZabbixAlerts->_isExistedAlert($data['eventid']) == EXISTED){
            $oCZabbixAlerts->_update_alertbyeventid($trigger);
        }else{
            $oCZabbixAlerts->_insert_alert($trigger);
        }
    }
    $oCZabbixAlerts->_updateAll2Hide($arrEventid);
?>
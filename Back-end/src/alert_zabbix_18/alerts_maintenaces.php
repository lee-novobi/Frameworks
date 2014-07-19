<?php
    require_once ("init.php");
    require_once "include/f_index.php";
    require_once (PATH_CORE."class.xmlparse.php");
    require_once (PATH_CORE."class.zabbix.alerts.mainten.php");
    require_once (PATH_CORE."class.zabbix.triggers.php");

    $oCZabbixAlerts     = new CZabbixAlerts();
    $oCZabbixTriggers   = new CZabbixTriggers();
    // Lay thong tin triggers
    $triggers = $oCZabbixAlerts->_getExistedTrigger(3);
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
    $sListEventid = "";
    foreach($triggers as $tnum => $trigger){
        $data = $oCZabbixAlerts->_getInfoEvent($trigger['triggerid']);
        if((int)$data['eventid'] == 0){
            continue;
        }
        $found = false;
        foreach($hosts as $v_hosts){
            if(in_array($trigger['triggerid'],$v_hosts['triggers'])){
                $trigger['host'] 	= $v_hosts['host'];
                $trigger['hostid']  = $v_hosts['hostid'];
                $trigger['ip'] 		= $v_hosts['ip'];
                $trigger['port'] 	= $v_hosts['port'];
                $trigger['prod_code']   		= $v_hosts['prod_code'];
                $trigger['cmdb_product_code']   = $v_hosts['cmdb_product_code'];
                $trigger['prod_alias']   		= $v_hosts['prod_alias'];
                $trigger['dep_code']   			= $v_hosts['dep_code'];
                $trigger['dep_alias']   		= $v_hosts['dep_alias'];
                $trigger['dep_division']   		= $v_hosts['dep_division'];
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
        $sListEventid .= $data['eventid'].",";
        if($oCZabbixAlerts->_isExistedAlert($data['eventid']) == EXISTED){
            $oCZabbixAlerts->_update_alertbyeventid($trigger);
        }else{
            $oCZabbixAlerts->_insert_alert($trigger);
        }
    }
    if($sListEventid != ""){
        $sListEventid = substr($sListEventid,0,-1);
        $oCZabbixAlerts->_updateAll2Hide($sListEventid);
    }else{
        $oCZabbixAlerts->_updateAll2Hide("");
    }
?>
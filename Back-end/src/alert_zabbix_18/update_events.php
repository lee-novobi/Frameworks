<?php
    set_time_limit(50);
    require_once ("init.php");
    require_once "include/f_index.php";       
    require_once (PATH_CORE."class.xmlparse.php");
    require_once (PATH_CORE."class.zabbix.php");
    require_once (PATH_CORE."HttpClient.class.php");
    require_once "collect_events.inc.php";
    
    $oCZabbix = new CZabbix();
    $host = $oCZabbix->_get_allhost();
    if(MODE_DEBUG_TO_FILE == true){
        $fp = fopen("debug.txt","w");
        fwrite($fp,"START: ".date("d.m.Y H:i:s",time())."\n");
    } 
    $data = $oCZabbix->_getEvents_toUpdate();
    $i = 0;
    while($i < count($data)){
        $oCZabbix->_update_alertbyeventid($data[$i]);
        $i++;
    }     
    if(MODE_DEBUG_TO_FILE == true){
        fwrite($fp,"END : ".date("d.m.Y H:i:s",time())."\n");
        fclose($fp);
    }
?>
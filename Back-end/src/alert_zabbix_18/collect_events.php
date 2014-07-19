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
    $last_time_event = $oCZabbix->_getLastEvents(); 
    $data = $oCZabbix->_getEvents($last_time_event);
    _progress_data($data);
    if(MODE_DEBUG_TO_FILE == true){
        fwrite($fp,"END : ".date("d.m.Y H:i:s",time())."\n");
        fclose($fp);
    }
    $oCZabbix->_cleanup_old_data(); // thuc hien clean data cu
?>
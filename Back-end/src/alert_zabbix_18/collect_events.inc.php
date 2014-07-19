<?php
    function _progress_data($data){
        global $host,$oCZabbix;
        /* Tong hop lai cac triggerID */
        $l_trigger_id = $oCZabbix->_sum_list_triggerID($data);  
        /* Lay danh sach cac functionc cua triggers */
        $functions = $oCZabbix->_get_list_function($l_trigger_id);        
        $i = 0;
        while($i < count($data)){
            if($functions[$data[$i]['triggerid']]['itemid'] > 0){
                $data[$i]['itemid']     = $functions[$data[$i]['triggerid']]['itemid'];
                $data[$i]['lastvalue']  = $functions[$data[$i]['triggerid']]['lastvalue'];
                $data[$i]['function']   = $functions[$data[$i]['triggerid']]['function'];
                $data[$i]['parameter']  = $functions[$data[$i]['triggerid']]['parameter'];
                $info_item              = $oCZabbix->_get_itemsbyid($data[$i]['itemid']);
                /** So sanh gia tri cuoi cung trong bang item voi event **/
                $_Y_init_item = date("Y",$info_item['lastclock']);
                $_m_init_item = date("m",$info_item['lastclock']);
                $_d_init_item = date("d",$info_item['lastclock']);
                $str_item = $_Y_init_item.$_m_init_item.$_d_init_item;
                
                $_Y_init_event = date("Y",$data[$i]['clock']);
                $_m_init_event = date("m",$data[$i]['clock']);
                $_d_init_event = date("d",$data[$i]['clock']);
                $str_src = $_Y_init_event.$_m_init_event.$_d_init_event;
                
                if($str_src != $str_item){
                    // empty                    
                }else{
                    $data[$i]['lastvalue_of_item']  = $info_item['lastvalue'];
                    $data[$i]['hostname_of_item']   = $host[$info_item['hostid']]['host'];
                    $data[$i]['hostip_of_item']     = $host[$info_item['hostid']]['ip'];
                    $data[$i]['hostport_of_item']   = $host[$info_item['hostid']]['port'];
                    $data[$i]['msg']        = _go_replace($data[$i]['description'],$data[$i]);
                    $isExisted = $oCZabbix->_isExistedAlert($data[$i]['hostname_of_item'],$data[$i]['triggerid']);
                    if($isExisted == EXISTED){
                        $oCZabbix->_update_alert($data[$i]);
                        $i++;
                        continue;
                    }
                    $oCZabbix->_insert_alert($data[$i]);    
                }
            }
            $i++;
        } 
    }
    
    /**
     * Thuc hien thay the cac macro
     * @param String $msg
     * @return String
     **/ 
    function _go_replace($msg,$info){
       $msg = str_replace(MACRO_ZABBIX_HOST,$info['hostname_of_item'],$msg);
       $msg = str_replace(MACRO_ZABBIX_LASTVALUE,$info['lastvalue_of_item'],$msg); 
       $msg = str_replace(MACRO_ZABBIX_LASTVALUE1,$info['lastvalue_of_item'],$msg); 
       $msg = str_replace(MACRO_ZABBIX_LASTVALUE_,$info['lastvalue_of_item'],$msg); 
       return $msg;
    }
?>
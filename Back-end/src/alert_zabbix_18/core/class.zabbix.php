<?php
    class CZabbix{

        /**
         * Lay thoi gian tao event cuoi cung
         * @return Array
         **/
        public function _getLastEvents(){
           global $dbconn;
           $rest = 0;
           $sql = "SELECT clock FROM ".ZABBIX_MONITOR_ALERT_TABLE." WHERE eventid>0 ORDER BY clock DESC LIMIT 1";
           if(MODE_DEBUG == true){
                echo $sql;
                echo "<br>";
           }
           $result = mysql_query($sql,$dbconn);
           while($row = mysql_fetch_assoc($result)){
                $rest = $row['clock'];
                break;
           }
           mysql_free_result($result);
           return $rest;
        }

        /**
         * Lay danh sach events
         * @return Array
         **/
        public function _getEvents_toUpdate(){
            global $dbconn_zabbix;
            $rows = array();
            $start_record = 0;
            $end_record = NUM_RECORD_PROGRESS;
            $sql = "SELECT eventid,events.value,acknowledged,triggerid FROM ".ZABBIX_EVENTS_TABLE." events INNER JOIN ".ZABBIX_TRIGGERS_TABLE." triggers ON events.objectid = triggers.triggerid ORDER BY clock DESC LIMIT {$start_record}, {$end_record}";
            if(MODE_DEBUG == true){
                echo $sql;
                echo "<br>";
            }
            $result = mysql_query($sql,$dbconn_zabbix);
            while($row = mysql_fetch_assoc($result)){
                $rows[] = $row;
            }
            mysql_free_result($result);
            return $rows;
        }

        /**
         * Lay danh sach events
         * @return Array
         **/
        public function _getEvents($ftime){
            global $dbconn_zabbix;
            $rows = array();
            $start_record = 0;
            $end_record = NUM_RECORD_PROGRESS;
            $sql = "SELECT eventid,objectid,clock,events.value,acknowledged,triggerid,expression,description,priority,FROM_UNIXTIME(clock) as date_init FROM ".ZABBIX_EVENTS_TABLE." events INNER JOIN ".ZABBIX_TRIGGERS_TABLE." triggers ON events.objectid = triggers.triggerid AND clock>='{$ftime}' ORDER BY clock ASC LIMIT {$start_record}, {$end_record}";
            if(MODE_DEBUG == true){
                echo $sql;
                echo "<br>";
            }
            $result = mysql_query($sql,$dbconn_zabbix);
            while($row = mysql_fetch_assoc($result)){
                $rows[] = $row;
            }
            mysql_free_result($result);
            return $rows;
        }

        /**
         * Summary danh sach triggerID
         * @param Array $data
         * @return String
         **/
        public function _sum_list_triggerID($data){
            $l_trigger_id = '';
            foreach($data as $v_data){
                $l_trigger_id .= $v_data['objectid'].",";
            }
            if($l_trigger_id!=""){
                $l_trigger_id = substr($l_trigger_id,0,-1);
            }
            return $l_trigger_id;
        }

        /**
         * Lay danh sach cac function zabbix
         * @param String $l_trigger_id
         * @return Array
         **/
         public function _get_list_function($l_trigger_id){
            global $dbconn_zabbix;
            $rows = array();
            $tmp = array();
            if($l_trigger_id!=""){
                $sql = "SELECT * FROM ".ZABBIX_FUNCTION_TABLE." WHERE triggerid IN ({$l_trigger_id})";
            }else{
                $sql = "SELECT * FROM ".ZABBIX_FUNCTION_TABLE;
            }
            if(MODE_DEBUG == true){
                echo $sql;
                echo "<br>";
            }
            $result = mysql_query($sql,$dbconn_zabbix);
            while($row = mysql_fetch_assoc($result)){
                if(!in_array($row['triggerid'],$tmp)){
                    $rows[$row['triggerid']] = $row;
                    $tmp[] = $row['triggerid'];
                }
            }
            mysql_free_result($result);
            return $rows;
         }

         /** Lay danh sach hostname
          * @return Array
          **/
          public function _get_allhost(){
            global $dbconn_zabbix;
            $sql = "SELECT hostid,host,ip,port,status FROM ".ZABBIX_HOSTS_TABLE;
            if(MODE_DEBUG == true){
                echo $sql;
                echo "<br>";
            }
            $result = mysql_query($sql,$dbconn_zabbix);
            while($row = mysql_fetch_assoc($result)){
                $rows[$row['hostid']] = $row;
            }
            mysql_free_result($result);
            return $rows;
          }

          /**
           * Lay so lieu cua item
           * @param integer $itemid
           * @return Array
           **/
          public function _get_itemsbyid($itemid){
            global $dbconn_zabbix;
            $sql = "SELECT itemid,hostid,description,key_,lastvalue,lastclock  FROM ".ZABBIX_ITEMS_TABLE." WHERE itemid='{$itemid}' LIMIT 1";
            if(MODE_DEBUG == true){
                echo $sql;
                echo "<br>";
            }
            $result = mysql_query($sql,$dbconn_zabbix);
            while($row = mysql_fetch_assoc($result)){
                $rows[] = $row;
            }
            mysql_free_result($result);
            if(is_array($rows)){
                return $rows[0];
            }
            return array();
          }

          /**
           * Them vao danh sach alert
           * @param Array $info
           **/
           public function _insert_alert($info){
            global $dbconn;
            $data = array(  "eventid"   =>  $info['eventid'],
                            "triggerid" =>  $info['triggerid'],
                            "hostname"  =>  $info['hostname_of_item'],
                            "host_ip"   =>  $info['hostip_of_item'],
                            "host_port" =>  $info['hostport_of_item'],
                            "msg"       =>  $info['msg'],
                            "type"      =>  $info['priority'],
                            "clock"     =>  $info['clock'],
                            "ack"       =>  $info['acknowledged'],
                            "status"    =>  $info['value'],
                            "src_from"  =>  SRC_FRM_ZABBIX
                         );
            if(defined('ZABBIX_LOCATION') && ZABBIX_LOCATION != ''){
             	$data['location'] = ZABBIX_LOCATION;
             }
            InsertAll($data,ZABBIX_MONITOR_ALERT_TABLE,$dbconn);
           }

           /**
            * Thuc hien update thong tin alert
            * @param Array $info
            **/
            public function _update_alert($info){
                global $dbconn;
                $hostname = $info['hostname_of_item'];
                $triggerid = $info['triggerid'];
                $data = array(
                                "clock"     =>  $info['clock'],
                                "ack"       =>  $info['acknowledged'],
                                "status"    =>  $info['value'],
                                "src_from"  =>  SRC_FRM_ZABBIX
                            );
                if(defined('ZABBIX_LOCATION') && ZABBIX_LOCATION != ''){
             		$data['location'] = ZABBIX_LOCATION;
             	}
                $where =  "hostname='{$hostname}' AND triggerid='{$triggerid}'";
                UpdateAll($data,ZABBIX_MONITOR_ALERT_TABLE,$where,$dbconn);
            }

            /**
            * Thuc hien update thong tin alert
            * @param Array $info
            **/
            public function _update_alertbyeventid($info){
                global $dbconn;
                $data = array(
                                "ack"       =>  $info['acknowledged'],
                                "status"    =>  $info['value']
                            );
                $where =  "eventid='".$info['eventid']."'";
                if(defined('ZABBIX_LOCATION') && ZABBIX_LOCATION != ''){
             		$where .= " AND location='" . ZABBIX_LOCATION . "'";
             	}
                UpdateAll($data,ZABBIX_MONITOR_ALERT_TABLE,$where,$dbconn);
            }

           /**
            * Kiem tra thong tin co trong bang alert hay chua
            * @param integer $eventid
            * @return boolean
            **/
            public function _isExistedAlert($hostname,$triggerid){
               global $dbconn;
               $rest  = NOT_EXISTED;
               $where = "hostname='{$hostname}' AND triggerid='{$triggerid}'";
               if(defined('ZABBIX_LOCATION') && ZABBIX_LOCATION != ''){
             	   $where .= " AND location='" . ZABBIX_LOCATION . "'";
               }
               $sql   = "SELECT alert_id FROM ".ZABBIX_MONITOR_ALERT_TABLE." WHERE {$where}";
               if(MODE_DEBUG == true){
                    echo $sql;
                    echo "<br>";
               }
               $result = mysql_query($sql,$dbconn);
               while($row = mysql_fetch_assoc($result)){
                    $rest = EXISTED;
                    break;
               }
               mysql_free_result($result);
               return $rest;
            }

            /**
             * Xoa cac data cu
             **/
            public function _cleanup_old_data(){
                global $dbconn;
                $time_del = time() - TIMELINE_STORE_DATA;
                $where_clause = "clock <".$time_del;
                DeleteAll(ZABBIX_MONITOR_ALERT_TABLE,$where_clause,$dbconn);
            }
    }
?>
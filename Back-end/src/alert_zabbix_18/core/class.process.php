<?php
    class CProcess{
        /**
         * Xu ly data cho host status
         * @param Array $aData
         * @return Array
         **/ 
        public function _process_host_status($aData){
            $a_host_data = $aData['nagios']['hosts'];
            $i = 0;
            $a_host_status = array();
            while($i < count($a_host_data['hoststatus'])){
                $a_host_status[] = $a_host_data['hoststatus'][$i]['attr'];
                $i++;
            }
            return $a_host_status;
        }
        
        /**
         * Xu ly data cho host status service
         * @param Array $aData
         * @return Array
         **/ 
        public function _process_host_status_service($aData){
            $a_host_data = $aData['nagios']['hosts'];
            $i = 0;
            $a_host_status_service = array();
            while($i < count($a_host_data['hoststatus'])){
                $tmp = $a_host_data['hoststatus'][$i]['servicestatus'];
                foreach($tmp as $key=>$value){
                    $a_host_status_service[] = $value['attr'];
                }
                $i++;
            }
            return $a_host_status_service;
        }
        
        /**
         * Thuc hien send thong tin alert den zabbix
         * @param String $msg
         **/
         public function _send2zabbix($msg){
            $command = str_replace("{value}",$msg,CMD_EXEC);
            $resend = 0;
            /** run command **/
            while(true){
                /* Neu so lan resend vuot qua quy dinh thi thoat chuong trinh */
                if($resend > MAX_RESEND_TO_ZABBIX){
                    break;
                }
                /* end */
                exec($command, $results,$return_val); // Thuc hien send command
                echo $command."<br>";
                echo $result_exec = $results[0];
                echo "<br>";
                $is_failed = strpos($result_exec,"Failed 0"); // doc ket qua
                if($is_failed !== false){
                    break;     // thoat khoi loop
                }else{
                    $resend++; // dem so lan resend
                }
            }
            /** end **/
         } 
         
         /**
          * Insert vao bang alert
          * @param Array $info
          **/
          public function _insert2DB($info){
            global $dbconn;
            if($info['current_state'] == STATUS_CRITICAL){
                $type = Z_STATUS_DISATER;
                $status = 1;
            }else if($info['current_state'] == STATUS_WARNING){
                $type = Z_STATUS_WARNING;
            }
            $eventid = time() ."".rand(11111,99999);
            $data = array(
                            "hostname"      =>  $info['host_name'],
                            "eventid"       =>  $eventid,
                            "msg"           =>  $info['service_description'],
                            "type"          =>  $type,
                            "is_show"       =>  SHOW,
                            "status"        =>  $status,
                            "clock"         =>  $info['last_check'],
                            "lasted_update" =>  date("Y-m-d H:i:s",time()),
                            "src_from"      =>  SRC_FRM_G2
                         );
            InsertAll($data,ZABBIX_MONITOR_ALERT_TABLE,$dbconn);
          } 
          
          /**
           * Update vao bang alert
           * @param Array $info
           **/
           public function _update2DB($info){
            global $dbconn;
            if($info['current_state'] == STATUS_CRITICAL){
                $type = Z_STATUS_DISATER;
                $status = 1;
            }else if($info['current_state'] == STATUS_WARNING){
                $type = Z_STATUS_WARNING;
            }
            $data = array(
                            "type"          =>  $type,
                            "status"        =>  $status,
                            "is_show"       =>  SHOW,
                            "clock"         =>  $info['last_check'],
                            "lasted_update" =>  date("Y-m-d H:i:s",time()),
                            "src_from"      =>  SRC_FRM_G2
                         );
            $where =  "hostname='".addslashes($info['host_name'])."' AND msg='".addslashes($info['service_description'])."'";
            UpdateAll($data,ZABBIX_MONITOR_ALERT_TABLE,$where,$dbconn);
           }
           
           /**
            * Kiem tra thong tin alert co trong db chua
            * @param Array $info
            * @return integer
            **/
            public function _isExistedInDB($info){
               global $dbconn;
               $rest = NOT_EXISTED;
               $where =  "hostname='".addslashes($info['host_name'])."' AND msg='".addslashes($info['service_description'])."' AND src_from ='".SRC_FRM_G2."'";
               $sql = "SELECT alert_id FROM ".ZABBIX_MONITOR_ALERT_TABLE." WHERE {$where}";
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
             * Update toan bo danh sach thanh hidden
             **/ 
            public function _updateAll2Hide(){
                global $dbconn;
                $data = array(  
                                "is_show"   =>  HIDE,
                                "status"    =>  STATUS_OK,
                            ); 
                $where =  "src_from='".SRC_FRM_G2."' AND extent = ''";
                UpdateAll($data,ZABBIX_MONITOR_ALERT_TABLE,$where,$dbconn);
            } 
    }
?>
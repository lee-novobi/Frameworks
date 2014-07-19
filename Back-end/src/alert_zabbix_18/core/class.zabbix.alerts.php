<?php
    class CZabbixAlerts{
        /**
         * Lay toan bo thong tin triggers
         * @return Array
         **/
        public function _getExistedTrigger($gtLevel){
            global $dbconn_zabbix;
            $rows = array();
            $sql = "SELECT DISTINCT t.* FROM triggers t,functions f,items i,hosts h WHERE i.hostid=h.hostid AND f.triggerid=t.triggerid AND f.itemid=i.itemid AND i.status=0 AND t.status=0 AND h.status=0 AND t.priority>={$gtLevel} AND h.maintenance_status = 0 AND t.value=1 ORDER BY t.lastchange DESC";
            if(MODE_DEBUG == true){
                echo $sql;
                echo "<br>";
            }
            $result = mysql_query($sql,$dbconn_zabbix);
            while($row = mysql_fetch_assoc($result)){
                $rows[$row['triggerid']] = $row;
            }
            mysql_free_result($result);
            return $rows;
        }

        /**
         * Lay danh sach thong tin host
         * @param String $list_triggerid
         * @return Array
         **/
        public function _getExistedHost($list_triggerid){
            global $dbconn_zabbix;
            $rows = array();
			$sql = "SELECT DISTINCT h.*,f.triggerid FROM 
			hosts h ,functions f,items i 
			WHERE (f.triggerid IN ({$list_triggerid})) AND h.hostid=i.hostid AND f.itemid=i.itemid AND h.status IN (0,1,3)";
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
          * Xu ly thong tin host
          * @param Array $info
          * @return Array
          **/
         public function _progressHostInfo($info){
            $i = 0;
            $k = 0;
            while($i < count($info)){
                foreach($info[$i] as $value){
                    $hosts[] = $value;
                    $k++;
                }
                $i++;
            }
            $tmp = array();
            $i = 0;
			$nOriginalHostsCount = count($hosts);
            while($i < $nOriginalHostsCount){
            	if(!isset($hosts[$i])){
					$i++;
					continue;
				}
                $hostid = $hosts[$i]['hostid'];
                if((int)$hostid==0){
                    $i++;
                    continue;
                }
                $tmp[$hostid]['hostid']             = $hosts[$i]['hostid'];
                $tmp[$hostid]['host']               = $hosts[$i]['host'];
                $tmp[$hostid]['ip']                 = $hosts[$i]['ip'];
                $tmp[$hostid]['port']               = $hosts[$i]['port'];
                $tmp[$hostid]['status']             = $hosts[$i]['status'];
                $tmp[$hostid]['error']              = $hosts[$i]['error'];
                $tmp[$hostid]['maintenanceid']      = $hosts[$i]['maintenanceid'];
                $tmp[$hostid]['maintenance_status'] = $hosts[$i]['maintenance_status'];
                $tmp[$hostid]['maintenance_type']   = $hosts[$i]['maintenance_type'];
                $tmp[$hostid]['maintenance_from']   = $hosts[$i]['maintenance_from'];
                $triggers_in_host[] = $hosts[$i]['triggerid'];
                $j = $i+1;
                $total_host = count($hosts);
                while($j < $total_host){
                    if(isset($hosts[$i]) && isset($hosts[$j]))
                    {
						if($hosts[$i]['hostid'] == $hosts[$j]['hostid'] && $hosts[$j]['hostid']>0){
							$triggers_in_host[] = $hosts[$j]['triggerid'];
							unset($hosts[$j]);
						}
					}
                    $j++;
                }
                $tmp[$hostid]['triggers'] = $triggers_in_host;
                unset($triggers_in_host);
                $i++;
            }
            return $tmp;
         }

         /**
          * Lay thong tin event
          * @param integer $objectid
          * @return Array
          **/
          public function _getInfoEvent($objectid=0){
            global $dbconn_zabbix;
            if($objectid == 0){
                return array();
            }
            $select  = "SELECT e.eventid, e.value, e.clock, e.objectid as triggerid, e.acknowledged FROM events e ";
            $where   = "WHERE e.object=0 AND e.objectid={$objectid} AND (e.value=1 OR e.value=0 OR e.value=2) ";
            $orderby = "ORDER by e.object DESC, e.objectid DESC, e.eventid DESC LIMIT 1";
            $sql     = $select . $where . $orderby;

            if(MODE_DEBUG == true){
                echo $sql;
                echo "<br>";
            }
            $result = mysql_query($sql,$dbconn_zabbix);
            $rows = array();
            while($row = mysql_fetch_assoc($result)){
                $rows[] = $row;
            }
            mysql_free_result($result);
            return (count($rows) > 0) ? $rows[0] : array('eventid' => 0);
          }

          /**
           * Them vao danh sach alert
           * @param Array $info
           **/
           public function _insert_alert($info){
            global $dbconn, $config;
            $data = array(  "zabbix_eventid"   =>  intval($info['eventid']),
							"is_ack"       =>  new MongoInt32($info['acknowledged']),
							"zbx_host"  =>  $info['host'],
							"zabbix_hostid"  =>  intval($info['hostid']),
							"zabbix_server_id"  =>  (((intval($info['hostid']) - 10000) * 256) + 2),
							"alert_message"       =>  $info['msg'],
							"zabbix_trigger_description"       =>  $info['msg'],
                            "is_show"   =>  new MongoInt32(SHOW),
							"clock"     =>  intval($info['clock']),
							"create_date"     =>  intval($info['clock']),
							"priority"       =>  new MongoInt32($info['priority']),
							"zabbix_trigger_priority"       =>  new MongoInt32($info['priority']),
							"source_from"  =>  SRC_FRM_ZABBIX,
							"source_id"       =>  $info['triggerid'],
							"zbx_zabbix_server_id" => new MongoInt32(2),
							"zbx_maintenance"       =>  new MongoInt32(0),
							"level"       =>  new MongoInt32(0),
							"num_of_case"       =>  new MongoInt32(0),
							"zabbix_version"       =>  "1.8",
							"ack_msg"       =>  "",
							"affected_deals"       =>  "",
							"attachments"       =>  "",
							"description"       =>  "",
							"external_status"       =>  "",
							"internal_status"       =>  "",
							"itsm_incident_id"       =>  "",
							"ticket_id"       =>  "",
							"title"       =>  "",
							"update_date"       =>  ""
                         );
             InsertAll($data,ZABBIX_MONITOR_ALERT_TABLE);
           }


            /**
            * Thuc hien update thong tin alert
            * @param Array $info
            **/
            public function _update_alertbyeventid($info){
                global $dbconn, $config;
                $data = array('$set' => array(
                                "is_ack"       =>  new MongoInt32($info['acknowledged']),
                                "alert_message"       =>  $info['msg'],
								"clock"     =>  intval($info['clock']),
								"update_date"     =>  intval($info['clock']),
							"priority"       =>  new MongoInt32($info['priority'])
                            ));
                $where =  array("zabbix_eventid" => intval($info['eventid']));
                UpdateAll($data,ZABBIX_MONITOR_ALERT_TABLE,$where);
            }

            /**
             * Update toan bo danh sach thanh hidden
             * @param String $arrEventid
             **/
            public function _updateAll2Hide($arrEventid){
                global $dbconn, $config;
                $data = array(
                                '$set' => array("is_show"  =>  HIDE)
                            );
				$where = array('$nin' => $arrEventid, "zbx_zabbix_server_id" => 2);
                UpdateAll($data,ZABBIX_MONITOR_ALERT_TABLE,$where);
            }

            /**
            * Kiem tra thong tin co trong bang alert hay chua
            * @param integer $eventid
            * @return boolean
            **/
            public function _isExistedAlert($eventid){
				global $dbconn, $config;
				$rest = NOT_EXISTED;
				$where =  array("source_from" => "Zabbix", "zabbix_eventid" => intval($eventid), "zbx_zabbix_server_id" => 2);
				if(CountAll(ZABBIX_MONITOR_ALERT_TABLE,$where) > 0)
					$rest = EXISTED;
				return $rest;
            }
    }
?>
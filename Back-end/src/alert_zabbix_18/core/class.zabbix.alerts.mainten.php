<?php
    class CZabbixAlerts{
        /**
         * Lay toan bo thong tin triggers
         * @return Array
         **/
        public function _getExistedTrigger($gtLevel){
            global $dbconn_zabbix;
            $rows = array();
            $sql = "SELECT DISTINCT t.* FROM triggers t,functions f,items i,hosts h WHERE i.hostid=h.hostid AND f.triggerid=t.triggerid AND f.itemid=i.itemid AND i.status=0 AND t.status=0 AND h.status=0 AND t.priority>={$gtLevel} AND h.maintenance_status = 1 AND t.value=1 ORDER BY t.lastchange DESC";
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
            if(!defined('ZABBIX_VERSION') || ZABBIX_VERSION=="1.8"){
            	$sql = "SELECT DISTINCT idep.code dep_code,idep.alias dep_alias,idep.division dep_division,iprod.code prod_code,iprod.cmdb_product_code cmdb_product_code,iprod.alias prod_alias,h.*,f.triggerid 
				FROM hosts h LEFT JOIN `".
				ZABBIX_PRODUCTS_TABLE."` iprod ON( h.productid = iprod.productid ) LEFT JOIN `".
				ZABBIX_DEPARTMENTS_TABLE."` idep ON ( iprod.departmentid = idep.departmentid )
				,functions f,items i 
				WHERE (f.triggerid IN ({$list_triggerid})) AND h.hostid=i.hostid AND f.itemid=i.itemid AND h.status IN (0,1,3)";
            } elseif(ZABBIX_VERSION=="2.0"){
				$sql = "SELECT DISTINCT idep.code dep_code,idep.alias dep_alias,idep.division dep_division,iprod.code prod_code,iprod.cmdb_product_code cmdb_product_code,iprod.alias prod_alias,h.*,hi.ip,hi.port,f.triggerid 
				FROM `hosts` h INNER JOIN `items` i ON(h.hostid=i.hostid) INNER JOIN `functions` f ON(i.itemid=f.itemid) INNER JOIN interface hi ON(h.hostid=hi.hostid) LEFT JOIN `".
				ZABBIX_PRODUCTS_TABLE."` iprod ON( h.productid = iprod.productid ) LEFT JOIN `".
				ZABBIX_DEPARTMENTS_TABLE."` idep ON ( iprod.departmentid = idep.departmentid ) 
				WHERE (f.triggerid IN ({$list_triggerid})) AND h.status IN (0,1,3)";
            }
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
                $hostid = $hosts[$i]['hostid'];
                if((int)$hostid==0){
                    $i++;
                    continue;
                }
                $tmp[$hostid]['hostid']             = $hosts[$i]['hostid'];
                $tmp[$hostid]['host']               = $hosts[$i]['host'];
                $tmp[$hostid]['dns']                = $hosts[$i]['dns'];
                $tmp[$hostid]['ip']                 = $hosts[$i]['ip'];
                $tmp[$hostid]['port']               = $hosts[$i]['port'];
                $tmp[$hostid]['status']             = $hosts[$i]['status'];
                $tmp[$hostid]['error']              = $hosts[$i]['error'];
                $tmp[$hostid]['maintenanceid']      = $hosts[$i]['maintenanceid'];
                $tmp[$hostid]['maintenance_status'] = $hosts[$i]['maintenance_status'];
                $tmp[$hostid]['maintenance_type']   = $hosts[$i]['maintenance_type'];
                $tmp[$hostid]['maintenance_from']   = $hosts[$i]['maintenance_from'];
                $tmp[$hostid]['prod_code']   		= $hosts[$i]['prod_code'];
                $tmp[$hostid]['cmdb_product_code']  = $hosts[$i]['cmdb_product_code'];
                $tmp[$hostid]['prod_alias']   		= $hosts[$i]['prod_alias'];
                $tmp[$hostid]['dep_code']   		= $hosts[$i]['dep_code'];
                $tmp[$hostid]['dep_alias']   		= $hosts[$i]['dep_alias'];
                $tmp[$hostid]['dep_division']   	= $hosts[$i]['dep_division'];
                $triggers_in_host[] = $hosts[$i]['triggerid'];
                $j = $i+1;
                $total_host = count($hosts);
                while($j < $total_host){
                    if($hosts[$i]['hostid'] == $hosts[$j]['hostid'] && $hosts[$j]['hostid']>0){
                        $triggers_in_host[] = $hosts[$j]['triggerid'];
                        unset($hosts[$j]);
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
            $sql = "SELECT e.eventid, e.value, e.clock, e.objectid as triggerid, e.acknowledged FROM events e WHERE e.object=0 AND e.objectid={$objectid} AND (e.value=1 OR e.value=0 OR e.value=2) ORDER by e.object DESC, e.objectid DESC, e.eventid DESC LIMIT 1";
            if(MODE_DEBUG == true){
                echo $sql;
                echo "<br>";
            }
            $result = mysql_query($sql,$dbconn_zabbix);
            while($row = mysql_fetch_assoc($result)){
                $rows[] = $row;
            }
            mysql_free_result($result);
            return $rows[0];
        }

        /**
         * Them vao danh sach alert
         * @param Array $info
         **/
        public function _insert_alert($info){
            global $dbconn, $config;
            $data = array(  "eventid"   =>  $info['eventid'],
                            "hostname"  =>  $info['host'],
                            "host_ip"   =>  $info['ip'],
                            "host_port" =>  $info['port'],
                            "msg"       =>  $info['msg'],
                            "type"      =>  $info['priority'],
                            "clock"     =>  $info['clock'],
                            "ack"       =>  $info['acknowledged'],
                            "status"    =>  $info['value'],
                            "maintenance_status"    =>  1,
                            "src_from"  =>  SRC_FRM_ZABBIX,
							"zbx_prod_code"     	 => $info['prod_code'],
							"zbx_cmdb_product_code"  => $info['cmdb_product_code'],
							"zbx_prod_alias"    => $info['prod_alias'],
							"zbx_dep_code"      => $info['dep_code'],
							"zbx_dep_alias"     => $info['dep_alias'],
							"zbx_dep_division"  => $info['dep_division']
            );
            if(defined('ZABBIX_LOCATION') && ZABBIX_LOCATION != ''){
                $data['location'] = ZABBIX_LOCATION;
            }
            if(defined('BACKEND_ID')){
             	$data['backend_id'] = BACKEND_ID;
            }
            InsertAll($data,ZABBIX_MONITOR_ALERT_MAINTEN_TABLE,$dbconn);
        }


        /**
         * Thuc hien update thong tin alert
         * @param Array $info
         **/
        public function _update_alertbyeventid($info){
            global $dbconn, $config;
            $data = array(
                "ack"       =>  $info['acknowledged'],
                "status"    =>  $info['value'],
                "is_show"   =>  SHOW,
				"zbx_prod_code"     	 => $info['prod_code'],
				"zbx_cmdb_product_code"  => $info['cmdb_product_code'],
				"zbx_prod_alias"    => $info['prod_alias'],
				"zbx_dep_code"      => $info['dep_code'],
				"zbx_dep_alias"     => $info['dep_alias'],
				"zbx_dep_division"  => $info['dep_division']
            );
            $where =  "eventid='".$info['eventid']."' AND maintenance_status=1";
            if(defined('ZABBIX_LOCATION') && ZABBIX_LOCATION != ''){
                $where .= " AND location='" . ZABBIX_LOCATION . "'";
            }
         	if(defined('BACKEND_ID')){
         		$where .= " AND backend_id='" . BACKEND_ID ."'";
         	}
            UpdateAll($data,ZABBIX_MONITOR_ALERT_MAINTEN_TABLE,$where,$dbconn);
        }

        /**
         * Update toan bo danh sach thanh hidden
         * @param String $sListEventid
         **/
        public function _updateAll2Hide($sListEventid=""){
            global $dbconn, $config;;
            $data = array(
                "is_show"  =>  HIDE
            );
            $where =  " src_from='".SRC_FRM_ZABBIX."'";
            if(defined('ZABBIX_LOCATION') && ZABBIX_LOCATION != ''){
                $where .= " AND location='" . ZABBIX_LOCATION . "'";
            }
         	if(defined('BACKEND_ID')){
         		$where .= " AND backend_id='" . BACKEND_ID ."'";
         	}
            if($sListEventid != ""){
                $where .= " AND eventid NOT IN ({$sListEventid}) AND maintenance_status=1";
            }
            UpdateAll($data,ZABBIX_MONITOR_ALERT_MAINTEN_TABLE,$where,$dbconn);
        }

        /**
         * Kiem tra thong tin co trong bang alert hay chua
         * @param integer $eventid
         * @return boolean
         **/
        public function _isExistedAlert($eventid){
            global $dbconn,$config;
            $rest = NOT_EXISTED;
            $where =  "eventid='{$eventid}'";
            if(defined('ZABBIX_LOCATION') && ZABBIX_LOCATION != ''){
                $where .= " AND location='" . ZABBIX_LOCATION . "'";
            }

            if(defined('BACKEND_ID')){
         	   $where .= " AND backend_id='" . BACKEND_ID ."'";
            }
            $sql = "SELECT alert_id FROM ".ZABBIX_MONITOR_ALERT_MAINTEN_TABLE." WHERE {$where}";
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
    }
?>
<?php
    class CZabbixTriggers{
        /**
         * Merge array
         * @return Array
         */ 
        public function _zbx_array_merge(){
        	$args = func_get_args();
        	$result = array();
        	foreach($args as &$array){
        		if(!is_array($array)) return false;
        
        		foreach($array as $key => $value){
        			$result[$key] = $value;
        		}
        	}
            return $result;
        }
        /** 
           * Phan tich expression cua function
           * @param String  $expression
           * @param String $function
           * @return integer
           **/ 
          public function _trigger_get_N_functionid($expression, $function){
        	$result = NULL;
        	$arr = preg_split('/[\{\}]/', $expression);
        	$num = 1;
        	foreach($arr as $id){
        		if(is_numeric($id)){
        			if($num == $function){
        				$result = $id;
        				break;
        			}
        			$num++;
        		}
        	}
            return $result;
        }
        
        /**
         * Phan tich so trong chuoi
         * @param String $str
         * @return $number;
         **/ 
    	public function _extract_numbers($str){
            $numbers = array();
            while(preg_match('/'.ZBX_PREG_NUMBER.'(['.ZBX_PREG_PRINT.']*)/', $str, $arr)){
            	$numbers[] = $arr[1];
            	$str = $arr[2];
            }
            return $numbers;
    	}
        
        
        /**
         * Phan tich chuoi description cua trigger
         * @param String $description
         * @param String $row
         * @reurn String
         **/ 
        public function _expand_trigger_description_constants($description, $row){
    		if($row && isset($row['expression'])){
    			$numbers = self::_extract_numbers(preg_replace('/(\{[0-9]+\})/', 'function', $row['expression']));
    
    			$description = $row['description'];
    
    			for ( $i = 0; $i < 9; $i++ ){
    				$description = str_replace(
    									'$'.($i+1),
    									isset($numbers[$i])?$numbers[$i]:'',
    									$description
    								);
    			}
    		}
    
    		return $description;
    	}
        
        /**
         * Tim vi tri xuat hien trong chuoi
         * @param String $haystack
         * @param String $needle
         * @reurn Integer
         **/ 
        public function _zbx_strstr($haystack,$needle){
        	$pos = strpos($haystack,$needle);
        	if($pos !== FALSE){
        		$pos = substr($haystack,$pos);
        	}
            return $pos;
        }
        
        
        /**
         * Lay thong tin function trong zabbix
         * @param integer $functionid
         * @return array
         **/
         public function _getInfoFunctionByID($functionid){
            global $dbconn_zabbix;
            $rows = array();
            $sql = "SELECT DISTINCT h.host FROM functions f,items i,hosts h WHERE f.itemid=i.itemid AND i.hostid=h.hostid AND f.functionid={$functionid} LIMIT 1";
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
         * Lay thong tin value function trong zabbix
         * @param integer $functionid
         * @return array
         **/ 
        public function _getInfoValueFunctionByID($functionid){
            global $dbconn_zabbix;
            $rows = array();
            $sql = "SELECT i.lastvalue, i.value_type, i.itemid FROM items i, functions f WHERE i.itemid=f.itemid AND f.functionid={$functionid} LIMIT 1";
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
         * Lay gia tri co time lon nhat tu history log
         * @param integer $itemid
         * @return integer
         **/
         public function _getMaxClockFromHistoryLogByItemID($itemid){
            global $dbconn_zabbix;
            $rows = array();
            $sql = "SELECT MAX(clock) as max FROM history_log WHERE itemid={$itemid} LIMIT 1";
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
         * Lay gia tri tu history log
         * @param integer $itemid
         * @return integer
         **/
         public function _getValueFromHistoryLogByItemID($itemid,$maxclock){
            global $dbconn_zabbix;
            $rows = array();
            $sql = "SELECT value FROM history_log WHERE itemid={$itemid} AND clock={$maxclock} LIMIT 1";
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
         * Lay gia tri tu bang item dua vao function id
         * @param integer $functionid
         * @return Array
         **/ 
        public function _getValueFromItemByFunctionID($functionid){
            global $dbconn_zabbix;
            $rows = array();
            $sql = "select i.* from items i, functions f where i.itemid=f.itemid and f.functionid={$functionid} LIMIT 1";
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
         * Lay gia tri tu bang history
         **/ 
        public function _getValueItemFromHistory($table,$itemid,$clock){
            global $dbconn_zabbix;
            $rows = array();
            $sql = "select value from {$table} where itemid={$itemid} and clock<={$clock} order by itemid,clock desc LIMIT 1";
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
         * Lay gia tri tu bang history
         **/ 
        public function _getMaxClockFromHistoryByItemId($table,$itemid){
            global $dbconn_zabbix;
            $rows = array();
            $sql = "select max(clock) as clock from {$table} where itemid={$itemid} LIMIT 1";
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
         * Lay gia tri tu bang history
         **/ 
        public function _getValueItemFromHistoryByClockAndItemId($table,$itemid,$clock){
            global $dbconn_zabbix;
            $rows = array();
            $sql = "select value from {$table} where itemid={$itemid} and clock={$clock} LIMIT 1";
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
         * Lay gia tri item tu history
         **/ 
        function item_get_history($db_item, $last = 1, $clock = 0){
    		$value = NULL;
    
    		switch($db_item["value_type"]){
    			case ITEM_VALUE_TYPE_FLOAT:
    				$table = "history";
    				break;
    			case ITEM_VALUE_TYPE_UINT64:
    				$table = "history_uint";
    				break;
    			case ITEM_VALUE_TYPE_TEXT:
    				$table = "history_text";
    				break;
    			case ITEM_VALUE_TYPE_STR:
    				$table = "history_str";
    				break;
    			case ITEM_VALUE_TYPE_LOG:
    			default:
    				$table = "history_log";
    				break;
    		}
    
    		if($last == 0){
    			$row = self::_getValueItemFromHistory($table,$db_item['itemid'],$clock);
    			if($row){
    				$value = $row["value"];
                }
    		}
    		else{
    			$row = self::_getMaxClockFromHistoryByItemId($table,$db_item["itemid"]);
    			if($row && !is_null($row["clock"])){
    				$clock = $row["clock"];
    				$row = self::_getValueItemFromHistoryByClockAndItemId($table,$db_item["itemid"],$clock);
    				if($row){
    					$value = $row["value"];
                    }
    			}
    		}
    		return $value;
    	}
        
        /**
         * Gan gia tri vao function
         **/
        public function _trigger_get_func_value($expression, $flag, $function, $param){
    		$result = NULL;
    		$functionid=self::_trigger_get_N_functionid($expression,$function);
    		if(isset($functionid)){
    			$row=self::_getValueFromItemByFunctionID($functionid);
    			if($row){
    				$result=($flag == ZBX_FLAG_TRIGGER)?self::item_get_history($row, $param):self::item_get_history($row, 0, $param);
    			}
    		}
    		return $result;
    	}
        
        /**
         * Xu ly description
         **/ 
        public function _expand_trigger_description_by_data($row, $flag = ZBX_FLAG_TRIGGER){            
    		if($row){
    			$description = self::_expand_trigger_description_constants($row['description'], $row);
    			for($i=0; $i<10; $i++){
    				$macro = '{HOSTNAME'.($i ? $i : '').'}';
    				if(self::_zbx_strstr($description, $macro)) {
    					$functionid = self::_trigger_get_N_functionid($row['expression'], $i ? $i : 1);
    
    					if(isset($functionid)) {
    						$host = self::_getInfoFunctionByID($functionid);
    						if(is_null($host['host'])){
    							$host['host'] = $macro;
                            }
    						$description = str_replace($macro, $host['host'], $description);
    					}
    				}
    			}
    
    			for($i=0; $i<10; $i++){
    				$macro = '{ITEM.LASTVALUE'.($i ? $i : '').'}';
    				if(self::_zbx_strstr($description, $macro)) {
    					$functionid = self::_trigger_get_N_functionid($row['expression'], $i ? $i : 1);
    
    					if(isset($functionid)){    						
    						$row2=self::_getInfoValueFunctionByID($functionid);
    						if($row2['value_type']!=ITEM_VALUE_TYPE_LOG){
    							$description = str_replace($macro, $row2['lastvalue'], $description);
    						}
    						else{
    							$row3=self::_getMaxClockFromHistoryLogByItemID($row2['itemid']);
    							if($row3 && !is_null($row3['max'])){
    								$row4=self::_getValueFromHistoryLogByItemID($row2['itemid'],$row3['max']);
    								$description = str_replace($macro, $row4['value'], $description);
    							}
    						}
    					}
    				}
    			}
    
    			for($i=0; $i<10; $i++){
    				$macro = '{ITEM.VALUE'.($i ? $i : '').'}';
    				if(self::_zbx_strstr($description, $macro)){
    					$value=($flag==ZBX_FLAG_TRIGGER)?
    							self::_trigger_get_func_value($row['expression'],ZBX_FLAG_TRIGGER,$i ? $i : 1, 1):
    							self::_trigger_get_func_value($row['expression'],ZBX_FLAG_EVENT,$i ? $i : 1, $row['clock']);
    
    					$description = str_replace($macro, $value, $description);
    				}
    
    			}
    		}
    		else{
    			$description = '*ERROR*';
    		}
    	   return $description;
    	}
    }
?>
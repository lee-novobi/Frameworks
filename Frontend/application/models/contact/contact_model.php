<?php 
require_once "application/models/mysql_base_model.php";

class Contact_model extends mysql_base_model {
	function __construct() {
		parent::__construct();
	}

	function getUsersByDepartmentProduct($noProductId) {
		// $sql = "SELECT u.*, r.`name` AS role, d.`name` AS sdk_dept FROM `user_role_product` urp  
				// INNER JOIN `user` u
				// ON u.`userid` = urp.`user_id`
				// INNER JOIN `role` r
				// ON r.`roleid` = urp.`role_id`
				// INNER JOIN `department` d
				// ON u.`department_id` = d.`departmentid` AND d.`is_itsm_department` = 0 AND d.`deleted` = '0'
				// WHERE urp.`product_id` = ? AND urp.`deleted` = '0'
				// ORDER BY role";
		$sql = "SELECT u.*, r.`name` AS role, d.`name` AS sdk_dept FROM `user` u
				LEFT JOIN `user_role_product` urp ON urp.user_id = u.`userid`
				LEFT JOIN `department` d
				ON u.`department_id` = d.`departmentid` AND d.`is_itsm_department` = 0 AND d.`deleted` = '0'
				LEFT JOIN `role` r ON urp.`role_id` = r.`roleid`
				LEFT JOIN `incident_escalation` ie ON ie.`product_id` = urp.`product_id`
				WHERE urp.product_id = ? AND urp.`deleted` = '0' 
				GROUP BY u.`userid`, urp.`role_id` ORDER BY role";
		$query = $this->db_ma->query($sql, array($noProductId));
		return $query->result_array();
	}

	function findUserViaEmail($strEmail) {
		$strEmail = substr($strEmail, 0, strpos($strEmail, '@'));
        $sql = "SELECT `department` AS vng_dept FROM `vng_staff_list` WHERE SUBSTRING_INDEX(`email`, '@', 1) LIKE '". $this->db_ma->escape_like_str($strEmail) . "' LIMIT 1";
        $query = $this->db_ma->query($sql, array($strEmail));
		$result = $query->row_array();
		return $result;	

	}

	function getEscalationUserEachLevel($noProductId) {
		$sql = "SELECT ie.`id` AS ieid, ieu.`id` AS ieuid, ie.`level_incident_id`, ie.`level_escalation_id`, ie.`duration`, ie.`product_id`, `user`.`full_name`, `user`.`email` , `user`.`mobile`, `user`.`ext`, d.`name` AS sdk_dept, `user`.`userid`, urp.`role_id` FROM `incident_escalation` ie 
			LEFT JOIN `incident_escalation_user` ieu ON incident_escalation_id = ie.`id` 
			LEFT JOIN `user` ON ieu.`user_id` = `user`.`userid`
			LEFT JOIN `department` d ON d.`departmentid` = `user`.`department_id` AND d.`is_itsm_department` = 0 AND d.`deleted` = '0'
			LEFT JOIN `user_role_product` urp ON ie.`product_id` = urp.`product_id` AND ieu.`user_id` = urp.`user_id` AND urp.`deleted` = '0'
			WHERE ie.`product_id` = ? GROUP BY ie.`level_incident_id`, ie.`level_escalation_id`,`user`.`userid`";
		$query = $this->db_ma->query($sql, array($noProductId));
		$result = $query->result_array();
		return $result;
	}

	function getUsersByProduct($strProductName) {
		$sql = "SELECT u.*, r.`name` AS role, d.`name` AS sdk_dept, p.`name` AS product
				FROM user_role_product urp 
				INNER JOIN product p
				ON urp.`product_id` = p.`productid` AND p.`deleted` = '0' AND p.`is_itsm_product` = 0
				LEFT JOIN department d
				ON p.`department_id` = d.`departmentid` AND d.`is_itsm_department` = 0 AND d.`deleted` = '0'
				INNER JOIN `user` u 
				ON u.`userid` = urp.`user_id`
				INNER JOIN role r
				ON r.`roleid` = urp.`role_id`
				WHERE p.`name` LIKE '". $this->db_ma->escape_like_str($strProductName) . "%' AND urp.`deleted` = '0' ORDER BY sdk_dept";
		$query = $this->db_ma->query($sql);
		return $query->result_array();
	}

	function getEscalationUserEachLevelByProduct($strProductName) {
		$sql = "SELECT ie.`id` AS ieid, ieu.`id` AS ieuid, ie.`level_incident_id`, ie.`level_escalation_id`, ie.`duration`, ie.`product_id`, `user`.`full_name`, `user`.`email` , `user`.`mobile`, `user`.`ext`, d.`name` AS sdk_dept, `user`.`userid`, urp.`role_id` FROM `incident_escalation` ie 
			LEFT JOIN `incident_escalation_user` ieu ON incident_escalation_id = ie.`id` 
			LEFT JOIN `user` ON ieu.`user_id` = `user`.`userid`
			LEFT JOIN `department` d ON d.`departmentid` = `user`.`department_id` AND d.`is_itsm_department` = 0 AND d.`deleted` = '0'
			LEFT JOIN `user_role_product` urp ON ie.`product_id` = urp.`product_id` AND ieu.`user_id` = urp.`user_id` AND urp.`deleted` = '0'
			INNER JOIN `product` p ON urp.`product_id` = p.`productid` AND p.`deleted` = '0' AND p.`is_itsm_product` = 0
			WHERE p.`name` = ? GROUP BY ie.`level_incident_id`, ie.`level_escalation_id`,`user`.`userid`";
		$query = $this->db_ma->query($sql, array($strProductName));
		$result = $query->result_array();
		return $result;
	}

	function getProductListByKeySearch($strKeySearch) {
		$sql = "SELECT `productid`, `name` FROM `product` WHERE `name` LIKE '". $this->db_ma->escape_like_str($strKeySearch). "%' AND `is_itsm_product` = 0 AND `deleted` = '0' ORDER BY `name`";
            $query = $this->db_ma->query($sql);
            $result = $query->result_array();
            return $result;
	}

	//============================== WRITE LOG ================================//
    function insert_action_history($log_message_data) {
        $this->db_ma->insert($this->tblActionHistory, $log_message_data);
		if($this->db_ma->affected_rows() > 0) {
			return AFFECTED_CODE; //insert success
		}
		else {
			return NO_AFFECTED_CODE; //fail
		} 
    }

    function getEmailListByKeySearch($strKeySearch) {
    	$sql = "SELECT u.`userid`, SUBSTRING_INDEX(u.`email`, '@', 1) AS strfound, u.`department_id` FROM `user` u
                INNER JOIN `department` d
                WHERE u.`department_id` = d.`departmentid` AND d.`deleted` = '0' AND d.`is_itsm_department` = 0 AND
                SUBSTRING_INDEX(email, '@', 1) LIKE '". $this->db_ma->escape_like_str($strKeySearch) . "%' ORDER BY strfound ASC";
        $query = $this->db_ma->query($sql);
        $result = $query->result_array();
        return $result;
    }

    function getListUsersInfoByUserDomain($strUserDomain) {
    	$sql = "SELECT u.*, d.`name` AS sdk_dept, p.`name` AS product, r.`name` AS role 
				FROM `user_role_product` urp
				INNER JOIN `product` p ON p.`productid` = urp.`product_id` AND p.`is_itsm_product` = 0 AND p.`deleted` = '0' 
				INNER JOIN `department` d ON p.`department_id` = d.`departmentid` AND d.`deleted` = '0' AND d.`is_itsm_department` = 0
				INNER JOIN `user` u  ON urp.`user_id` = u.`userid`
				INNER JOIN `role` r ON r.`roleid` = urp.`role_id`
				WHERE SUBSTRING_INDEX(u.`email`, '@', 1) LIKE '". $this->db_ma->escape_like_str($strUserDomain) . "%' ORDER BY u.`email`";
    	$query = $this->db_ma->query($sql);
    	$result = $query->result_array();
    	return $result;
    }

    function getVNGStaffListByUserDomain($strUserDomain) {
    	$sql = "SELECT `id`, `fullname`, `department`, `email`, `cellphone`, `title`, `extension` FROM vng_staff_list WHERE SUBSTRING_INDEX(`email`, '@', 1) LIKE '". $this->db_ma->escape_like_str($strUserDomain) . "%' ORDER BY `email`";
    	$query = $this->db_ma->query($sql);
    	$result = $query->result();
    	return $result;
    }

    function getUserEmailFromVNGStaffList($strKeySearch) {
    	$sql = "SELECT id, SUBSTRING_INDEX(`email`, '@', 1) AS strfound
				FROM vng_staff_list WHERE SUBSTRING_INDEX(email, '@', 1) LIKE '" . $this->db_ma->escape_like_str($strKeySearch) . "%' ORDER BY strfound ASC";
    	$query = $this->db_ma->query($sql);
        $result = $query->result_array();
        return $result;
    }

    function insertUserFromVNGStaffList($strFullName, $strMobile, $strEmail, $strExt, $iDepartmentId) {
    	$arrData = array('full_name' => $strFullName,
    					 'mobile' => $strMobile,
    					 'email' => $strEmail,
    					 'ext' => $strExt,
    					 'department_id' => $iDepartmentId);
    	$this->db_ma->insert($this->tblUser, $arrData);
    	$iAffectedRows = $this->db_ma->affected_rows();
    	return $iAffectedRows;
    }

    function getStaffVNGByUserId($iUserId) {
    	$this->db_ma->where('id', $iUserId);
    	$oQuery = $this->db_ma->get($this->tblVNGStaffList);
    	if($oQuery->num_rows() > 0) {
    		return $oQuery->row();
    	} else {
    		return null;
    	}
    }
    
    function getProductCodeByHostIdServerID($hostID, $serverID) {
        
        $this->db_mdr = $this->load->database('mdr', TRUE);
        
        $sql = "SELECT cmdb_product_code FROM host_mdr WHERE zbx_hostid = '".$hostID."' AND zbx_zabbix_server_id = '".$serverID."' LIMIT 1";
        
        $query = $this->db_mdr->query($sql);
        
        $result = $query->result_array();
        
        $this->db_mdr->close();
        
        if (count($result) == 0) {
            return array();
        }
        
        return $result[0];
    }
    
    function getProductIDByProductCode($productCode) {
        
        $strSql = "SELECT productid, department_id FROM product WHERE product_code = '".$productCode."' AND is_itsm_product = 0 LIMIT 1";
        
        $oQuery = $this->db_ma->query($strSql);
        
        // $result = $query->row_array();
        
        if($oQuery->num_rows() > 0) {
        	return $oQuery->row_array();
        }
        
        return null;
    }
    
    function getUserInfoByProductId($productID) {
        
        $sql = "SELECT U.userid, U.full_name, U.email, U.mobile, U.ext, R.name AS role
                FROM user_role_product AS URP
                LEFT JOIN user AS U ON U.userid = URP.user_id
                LEFT JOIN role AS R ON R.roleid = URP.role_id 
                WHERE URP.product_id = '".$productID."' 
                ORDER BY R.name ASC";
        
        $query = $this->db_ma->query($sql);
        
        $result = $query->result_array();
        
        if (count($result) == 0) {
            return array();
        }
        
        return $result;
    }
    
    function getTotalUserByProductId($productID) {
        
        $sql = "SELECT U.userid
                FROM user_role_product AS URP
                LEFT JOIN user AS U ON U.userid = URP.user_id
                LEFT JOIN role AS R ON R.roleid = URP.role_id 
                WHERE URP.product_id = '".$productID."'
                GROUP BY U.userid 
                ORDER BY R.name ASC";
    
        $query = $this->db_ma->query($sql);
        
        $result = $query->result_array();
        
        if (count($result) == 0) {
            return array();
        }
        
        return $result;
    }
    //----------------------------------------------------------------------------------------- //
    function getProductFromITSMProduct($strProduct) {
    	$strSQL = "SELECT productid, department_id 
				FROM product 
				WHERE product_code = (SELECT product_code FROM product WHERE is_itsm_product = 1 AND deleted = '0' AND lower(name) = ?)
				AND is_itsm_product = 0 AND deleted = '0'";
		$oQuery = $this->db_ma->query($strSQL, array($strProduct));
		if($oQuery->num_rows() > 0) {
			return $oQuery->result();
		} 
		return null;
    }
    //----------------------------------------------------------------------------------------- //
    function getContactByProduct($arrProductId) {
		$this->db_ma->select('u.*, r.name AS role, d.name AS sdk_dept');
		$this->db_ma->from($this->tblUser . ' AS u');
		$this->db_ma->join($this->tblUserRoleProduct . ' AS urp', 'urp.user_id = u.userid', 'left');
		$this->db_ma->join($this->tblDeparment . ' AS d', 'u.department_id = d.departmentid AND d.is_itsm_department = 0 AND d.deleted = \'0\'', 'left');
		$this->db_ma->join($this->tblRole . ' AS r', 'urp.role_id = r.roleid', 'left');
		$this->db_ma->join($this->tblIncidentEscalation . ' AS ie', 'ie.product_id = urp.product_id', 'left');
		$this->db_ma->where_in('urp.product_id', $arrProductId);
		$this->db_ma->where('urp.deleted', '0');
		$this->db_ma->group_by(array('u.userid', 'urp.role_id'));
		$this->db_ma->order_by('role');
		$oQuery = $this->db_ma->get();
		// pd($this->db_ma->last_query());
		if($oQuery->num_rows() > 0) {
			return $oQuery->result_array();
		} 
		return null;
    }
	//----------------------------------------------------------------------------------------- //
	function getNumofCallFromInc($strIncidentId, $strCallStatus) {
		$this->db_ma->where(array('action_type' => $strCallStatus, 'incident_id' => $strIncidentId));
		$oQuery = $this->db_ma->get($this->tblActionHistory);
		if($oQuery->num_rows() > 0) {
			return $oQuery->num_rows();
		} 
		return null;
	}
	//----------------------------------------------------------------------------------------- //
	function getNumofCallFromAlert($strAlertId) {
		$strCondition = 'alert_id = \''. $strAlertId . '\' AND (action_type = \''. CONTACT_ACTION_TYPE_CALL_SUCCESS . '\' OR action_type = \'' . CONTACT_ACTION_TYPE_CALL_FAIL . '\')';
		$this->db_ma->select('COUNT(1) AS noof_call, action_type');
		$this->db_ma->where($strCondition);
		$this->db_ma->group_by('action_type');
		$oQuery = $this->db_ma->get($this->tblActionHistory);
		// pd($this->db_ma->last_query());
		if($oQuery->num_rows() > 0) {
			return $oQuery->result();
		} 
		return null;
	}
	//----------------------------------------------------------------------------------------- //
}
?>
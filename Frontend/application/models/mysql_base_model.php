<?php
class Mysql_base_model extends CI_Model {

	var $tblArea;
	var $tblSubarea;
	var $tblAssignee;
	var $tblAssignmentGroup;
	var $tblDeparment;
	var $tblProduct;
	var $tblBugCategory;
	var $tblBugUnit;
	var $tblIncidentFollow;
	var $tblActionHistory;
	var $tblUser;
	var $tblAvaya;
	var $tblIncidentHistory;
	var $tblChangeFollow;
	var $tblChangeHistory;
	var $tblShiftTransferInfo;
	var $tblShiftScheduleAssign;
	var $tblVNGStaffList;
	var $tblCriticalAsset;
	var $tblNewRootCause;
	var $tblDetector;
	var $tblCauseExternalDept;
	var $tblLocation;
	var $tblAffectedCI;
	var $tblIncidentTrackingChanges;
	var $tblUserRoleProduct;
	var $tblIncidentEscalation;
	var $tblRole;
	var $tblSdkFollowIncidentNotification;
	var $tblSEReportIncidentNotification;
	var $tblShiftSchedule;
	var $tblSdkServiceSupport;
	
	function __construct()
	{
		// Call the Model constructor
		parent :: __construct();
		$this->db_ma = $this->load->database('monitoring_assistant', TRUE);

		$this->tblArea 							= TBL_AREA;
		$this->tblSubarea 						= TBL_SUBAREA;
		$this->tblAssignee 						= TBL_ASSIGNEE;
		$this->tblAssignmentGroup 				= TBL_ASSIGNMENT_GROUP;
		$this->tblDeparment 					= TBL_DEPARTMENT;
		$this->tblProduct 						= TBL_PRODUCT;
		$this->tblBugCategory					= TBL_BUG_CATEGORY;
		$this->tblBugUnit						= TBL_UNIT;
		$this->tblIncidentFollow				= TBL_INCIDENT_FOLLOW;
		$this->tblIncidentUpdateHistory 		= TBL_INCIDENT_UPDATE_HISTORY;
		$this->tblIncidentCreateHistory	 		= TBL_INCIDENT_CREATE_HISTORY;
		$this->tblUser							= TBL_USER;
		$this->tblAvaya							= TBL_AVAYA;
		$this->tblActionHistory					= TBL_ACTION_HISTORY;
 		$this->tblIncidentFollow				= TBL_INCIDENT_FOLLOW;
 		$this->tblChangeFollow					= TBL_CHANGE_FOLLOW;
 		$this->tblChangeHistory					= TBL_CHANGE_HISTORY;
 		$this->tblIncidentHistory				= TBL_INCIDENT_HISTORY;
 		$this->tblShiftTransferInfo				= TBL_SHIFT_TRANSFER_INFO;
 		$this->tblShiftScheduleAssign			= TBL_SHIFT_SCHEDULE_ASSIGN;
		$this->tblVNGStaffList					= TBL_VNG_STAFF_LIST;
		$this->tblCriticalAsset					= TBL_CRITICAL_ASSET;
		$this->tblNewRootCause					= TBL_NEW_ROOT_CAUSE;
		$this->tblDetector						= TBL_DETECTOR;
		$this->tblCauseExternalDept				= TBL_CAUSED_EXTERNAL_DEPT;
		$this->tblLocation						= TBL_LOCATION;
		$this->tblAffectedCI					= TBL_AFFECTED_CI;
		$this->tblIncidentTrackingChanges 		= TBL_INCIDENT_TRACKING_CHANGES;
		$this->tblUserRoleProduct				= TBL_USER_ROLE_PRODUCT;
		$this->tblRole							= TBL_ROLE;
		$this->tblIncidentEscalation			= TBL_INCIDENT_ESCALATION;
		$this->tblSdkFollowIncidentNotification	= TBL_SDK_FOLLOW_INCIDENT_NOTIFICATION; 
		$this->tblSEReportIncidentNotification	= TBL_SE_REPORT_INCIDENT_NOTIFICATION; 
		$this->tblShiftSchedule					= TBL_SHIFTSCHEDULE;
		$this->tblSdkServiceSupport				= TBL_SDK_SERVICE_SUPPORT;
	}

	// ------------------------------------------------------------------------------------------ //
	public function CloseNotiById($iNotiType,$iNotiId)
	{
		$this->db_ma->where('id',$iNotiId);
		if($iNotiType == INCIDENT_NOTI_TYPE)
			$this->db_ma->update($this->tblSdkFollowIncidentNotification,array('status'=>'CLOSE'));
		else if($iNotiType == SE_REPORT_NOTI_TYPE)
			$this->db_ma->update($this->tblSEReportIncidentNotification,array('status'=>'CLOSE'));
		if($this->db_ma->affected_rows() == 0)
			return false;
		return true;
	}
	
	// ------------------------------------------------------------------------------------------ //
	public function GetOpenNotiSEReport()
	{
		$this->db_ma->select('*');
		$this->db_ma->from($this->tblSEReportIncidentNotification);
		$this->db_ma->where(array('status'=> 'OPEN'));
		$this->db_ma->order_by('created_date', 'DESC');
		$query = $this->db_ma->get();
		if ( $query->num_rows() > 0 )
		{
			$result = $query->result_array();
			return $result;
		}
		else
			return null;
	}
	
	// ------------------------------------------------------------------------------------------ //
	public function GetOpenNotiIncident()
	{
		$this->db_ma->select('*');
		$this->db_ma->from($this->tblSdkFollowIncidentNotification);
		$this->db_ma->where(array('status'=> 'OPEN'));
		$this->db_ma->order_by('created_date', 'DESC');
		$query = $this->db_ma->get();
		if ( $query->num_rows() > 0 )
		{
			$result = $query->result_array();
			return $result;
		}
		else
			return null;
	}
	
	// ------------------------------------------------------------------------------------------ //
	public function GetUniqueITSMProduct()
	{
		$this->db_ma->select('*');
		$this->db_ma->from($this->tblProduct);
		$this->db_ma->where(array('is_itsm_product'=> 1));
		$this->db_ma->order_by('name', 'ASC');
		$query = $this->db_ma->get();
		//vd($this->db_ma->last_query());
		if ( $query->num_rows() > 0 )
		{
			$result = $query->result();
			return $result;
		}
		else
			return null;
	}

	// ------------------------------------------------------------------------------------------ //
	public function GetUniqueITSMDepartment()
	{
		$this->db_ma->distinct();
		$this->db_ma->select('*');
		$this->db_ma->from($this->tblDeparment);
		$this->db_ma->where(array('is_itsm_department'=> 1));
		$this->db_ma->order_by('name', 'ASC');
		$query = $this->db_ma->get();

		if ( $query->num_rows() > 0 )
		{
			$result = $query->result();
			return $result;
		}
		else
			return null;
	}

	// ------------------------------------------------------------------------------------------ //
	function GetProductByDepartment($strDepartment) {
		$this->db_ma->select('*');
		$this->db_ma->from($this->tblProduct);
		$this->db_ma->where(array(
								'is_itsm_product' => 1
								, 'UPPER(department_name)' => strtoupper($strDepartment)
								, 'deleted' => '0'));
		$query = $this->db_ma->get();

		if ( $query->num_rows() > 0 )
		{
			$result = $query->result();
			return $result;
		}
		else
			return null;
	}

	// ------------------------------------------------------------------------------------------ //
	function GetDepartmentOfProduct($strProduct) {
		$sql = "SELECT department_name FROM " . $this->tblProduct . " WHERE name LIKE '" . $this->db_ma->escape_like_str($strProduct);
		$sql .= "' AND deleted='0' AND is_itsm_product=1 LIMIT 1";

		$query = $this->db_ma->query($sql);

		if ( $query->num_rows() > 0 )
		{
			$result = $query->result();
			return $result[0];
		}
		else
			return null;
	}

	// ------------------------------------------------------------------------------------------ //
	function GetCurrentShift() {
		$sql    = 'SELECT f_get_current_shift() as current_shift';
		$query  = $this->db_ma->query($sql);
		$result = $query->result();
		if (count($result) > 0) {
			return $result[0];
		}
		else {
			return null;
		}
	}

	// ------------------------------------------------------------------------------------------ //
	function GetDepartmentListForContact() {
		$this->db_ma->where(array('is_itsm_department' => IS_NOT_ITSM_DEPARTMENT, 'deleted' => '0'));
		$this->db_ma->order_by('name', "asc");
		$query = $this->db_ma->get($this->tblDeparment);
		$res = $query->result_array();
		if($query->num_rows() > 0) {
			return $res;
		} else {
			return null;
		}

	}

	// ------------------------------------------------------------------------------------------ //
	function GetProductListByDepartmentIdForContact($nDepartmentId) {
		$this->db_ma->where(array('department_id' => $nDepartmentId, 'is_itsm_product' => IS_NOT_ITSM_PRODUCT, 'deleted' => '0'));
		$query = $this->db_ma->get($this->tblProduct);
		$res = $query->result_array();
		if($query->num_rows() > 0) {
			return $res;
		} else {
			return null;
		}

	}
	// ------------------------------------------------------------------------------------------ //
	function GetUsersByDepartmentId($noDepartmentId) {
		$this->db_ma->select('u.*, d.name as sdk_dept ');
		$this->db_ma->from('user AS u ');
		$this->db_ma->join('department AS d', 'u.department_id = d.departmentid', 'left');
		$this->db_ma->where(array('u.department_id' => $noDepartmentId, 'd.deleted' => '0', 'd.is_itsm_department' => IS_NOT_ITSM_DEPARTMENT));
		$query = $this->db_ma->get();
		//$sql = "";
		$res = $query->result_array();
		if($query->num_rows() > 0) {
			return $res;
		} else {
			return null;
		}
	}

	// ------------------------------------------------------------------------------------------ //
	function getUserById($iUserId) {
		$this->db_ma->where('userid', intval($iUserId));
		$query = $this->db_ma->get($this->tblUser);
		$res = $query->row_array();
		return $res;
	}

	// ------------------------------------------------------------------------------------------ //
	function get_avaya_info_by_ip($strIpAddress) {
		$this->db_ma->where('ip_address', $strIpAddress);
		$query = $this->db_ma->get($this->tblAvaya);
		return $query->row();
	}

	// ------------------------------------------------------------------------------------------ //
	function LoadUserByUserName($strUserName) {
		if(!empty($strUserName)){
			$strSQL = "select user.* from user INNER JOIN department d ON(d.departmentid=user.department_id)
					where email like '". $this->db_ma->escape_like_str($strUserName) ."@%' AND d.name LIKE ? AND d.is_itsm_department=0 LIMIT 1";
			$oQuery = $this->db_ma->query($strSQL, SDK_DEPARTMENT_NAME);

			if ($oQuery->num_rows() > 0) {
				return $oQuery->row_array();
			}
		}

		return null;
	}
	// ------------------------------------------------------------------------------------------ //
	function getDepartmentIdByDeptName($strDepartmentName) {
		$this->db_ma->where(array('name' => $strDepartmentName, 'deleted' => '0', 'is_itsm_department' => 0));
		$oQuery = $this->db_ma->get($this->tblDeparment);
		if($oQuery->num_rows() > 0) {
			return $oQuery->row();
		} else {
			return null;
		}
	}
	// ------------------------------------------------------------------------------------------ //
	function checkEmailExisted($strEmail) {
		$strSQL = "SELECT `userid` FROM `user` WHERE SUBSTRING_INDEX(`email`, '@', 1) LIKE '". $this->db_ma->escape_like_str($strEmail) . "'";
		$oQuery = $this->db_ma->query($strSQL);
		//pd($this->db_ma->last_query());
		return $oQuery->num_rows();
	}

	// ------------------------------------------------------------------------------------------ //
	function getStaffById($iUserId) {
		$this->db_ma->where('id', intval($iUserId));
		$query = $this->db_ma->get($this->tblVNGStaffList);
		$res = $query->row_array();
		return $res;
	}
	// ------------------------------------------------------------------------------------------ //
	function getProductByDepartmentAndProductId($iProductId, $strDepartment) {
		$oResult = null;
		$this->db_ma->where(array(
							'is_itsm_product' => 1,
							'deleted' => '0',
							'productid' => $iProductId,
							'department_name' => $strDepartment
		));
		$this->db_ma->limit(1);
		$oQuery = $this->db_ma->get($this->tblProduct);
		if($oQuery->num_rows() > 0) {
			$oResult = $oQuery->row();
		}
		return $oResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function getActionHistoryByIncidentId($strIncidentId) {
		$oResult = null;
		$this->db_ma->select('ah.*, u.full_name');
		$this->db_ma->from($this->tblActionHistory . ' ah');
		$this->db_ma->join($this->tblUser . ' u', 'ah.user_action_id = u.userid', 'left');
		$this->db_ma->where('ah.incident_id', $strIncidentId);
		$oQuery = $this->db_ma->get();
		if($oQuery->num_rows() > 0) {
			$oResult = $oQuery->result();
		}
		return $oResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function getShiftList() {
		$this->db_ma->select("id, concat(name,':',time_begin,':00 -> ',time_end, ':00') as name", FALSE);
		$oQuery = $this->db_ma->get($this->tblShiftSchedule);
		// pd($this->db_ma->last_query());
		if ( $oQuery->num_rows() > 0 )
		{
			return $oQuery->result();
		}
		return null;
	}
	// ------------------------------------------------------------------------------------------ //
	function getActionHistoryByRefId($strRefId, $strCurrentTime) {
		// $strCurrentTime = '2014-02-12';
		$oResult = null;
		$strCondition = "ah.ref_id = " . $strRefId . " AND ah.created_date <= '" . $strCurrentTime . "' AND ah.created_date >= DATE_SUB('". $strCurrentTime . "', INTERVAL 24 HOUR)";
		$this->db_ma->select('ah.*, u.full_name');
		$this->db_ma->from($this->tblActionHistory . ' ah');
		$this->db_ma->join($this->tblUser . ' u', 'ah.user_action_id = u.userid', 'left');
		$this->db_ma->where($strCondition);
		$oQuery = $this->db_ma->get();
		// pd($this->db_ma->last_query());
		if($oQuery->num_rows() > 0) {
			$oResult = $oQuery->result();
		}
		return $oResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function getExtraActionHistoryTypeNotifyCall($strRefId, $strAlertMsg=null, $strTimeAlert=null, $strIncidentId=null, $strChangeId=null, $strTimeMarker) {
		$arrCondition = array();
		$oResult = null;
		if(isset($strAlertMsg)) {
			$arrCondition['ah.alert_message'] = $strAlertMsg;
		}
		if(isset($strTimeAlert)) {
			$arrCondition['ah.time_alert'] = $strTimeAlert;
		}
		if(isset($strIncidentId)) {
			$arrCondition['ah.incident_id'] = $strIncidentId;
		}
		if(isset($strChangeId)) {
			$arrCondition['ah.change_id'] = $strChangeId;
		}
		$arrCondition['ah.action_type'] = NOTIFY_ACTION_TYPE_CALL;
		$arrCondition['ah.created_date <='] = $strTimeMarker;
		$arrCondition['ah.ref_id'] = $strRefId;
		
		$this->db_ma->select('ah.*, u.full_name');
		$this->db_ma->from($this->tblActionHistory . ' ah');
		$this->db_ma->join($this->tblUser . ' u', 'ah.user_action_id = u.userid', 'left');
		$this->db_ma->where($arrCondition);
		$this->db_ma->order_by('ah.created_date', 'desc');
		$this->db_ma->limit(1);
		$oQuery = $this->db_ma->get();
		// pd($this->db_ma->last_query());
		if($oQuery->num_rows() > 0) {
			$oResult = $oQuery->row();
		}
		return $oResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function getProductIdByProductName($strProduct) {
		$arrCondition = array();
		$arrCondition['deleted'] = '0';
		$arrCondition['is_itsm_product'] = 1;
		$arrCondition['name'] = $strProduct;
		$this->db_ma->where($arrCondition);
		$oQuery = $this->db_ma->get($this->tblProduct);
		if($oQuery->num_rows() > 0) {
			return $oQuery->row();
		}
		return null;
	}
	// ------------------------------------------------------------------------------------------ //
	function getActionHistoryByAlertId($strAlertId) {
		$oResult = null;
		$this->db_ma->select('ah.*, u.full_name');
		$this->db_ma->from($this->tblActionHistory . ' ah');
		$this->db_ma->join($this->tblUser . ' u', 'ah.user_action_id = u.userid', 'left');
		$this->db_ma->where('ah.alert_id', $strAlertId);
		$oQuery = $this->db_ma->get();
		if($oQuery->num_rows() > 0) {
			$oResult = $oQuery->result();
		}
		return $oResult;
	}
	// ------------------------------------------------------------------------------------------ //
	function getProductContactPointByPName($strDepartment, $strProduct) {
		$oResult = null;
		if(!empty($strProduct)) {
			$arrCondition = array();
			$arrCondition['p.deleted'] = '0';
			$arrCondition['p.is_itsm_product'] = 0;
			$arrCondition['d.name'] = $strDepartment;
			$arrCondition['d.deleted'] = '0';
			$this->db_ma->select('productid, department_id');
			$this->db_ma->from($this->tblProduct . ' p');
			$this->db_ma->join($this->tblDeparment . ' d', 'd.departmentid = p.department_id', 'inner');
			$this->db_ma->like('p.name', $strProduct);
			$this->db_ma->where($arrCondition)->limit(1);
			$oQuery = $this->db_ma->get();
			// pd($this->db_ma->last_query());
			if($oQuery->num_rows() > 0) {
				$oResult = $oQuery->row_array();
			}	
		} 
		
		return $oResult;
	}
}
?>

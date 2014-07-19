<?php 
require_once "application/models/mysql_base_model.php";

class Change_model extends Mysql_base_model {
	function __construct() {
		parent::__construct();
	}
	
	// --------------------------------------------------------------------------------------------- //
	public function GetChangeFollow($arrFilter, $arrPagination=null, $iFollowed = null, $iMovedAllChanges=null) {
		if(!empty($arrFilter)) {
			$this->db_ma->like('service', $arrFilter['service']);
		}
		if(isset($iFollowed)) {
			$this->db_ma->where('is_followed', $iFollowed);
		}
		if(!isset($iMovedAllChanges)) {
			$this->db_ma->where('moved_all_changes IS NULL', NULL, false);
		} else {
			$this->db_ma->order_by('down_start', 'desc');
		}
		if (isset($arrPagination['offset']) && isset($arrPagination['limit'])) {
			$this->db_ma->limit($arrPagination['limit'], $arrPagination['offset']);
		}
		$this->db_ma->where('status', CHANGE_STATUS_INITIAL);
		$this->db_ma->where('planned_end >=', 'NOW()', FALSE);
		$oQuery = $this->db_ma->get($this->tblChangeFollow);
		// pd($this->db_ma->last_query());
		if($oQuery->num_rows() > 0) {
			return $oQuery->result();
		}
		return null;
	}
	// --------------------------------------------------------------------------------------------- //
	public function GetChangeDetailByChangeId($strChangeId) {
		$this->db_ma->where('itsm_change_id', $strChangeId)->limit(1);
		$oQuery = $this->db_ma->get($this->tblChangeFollow);
		if($oQuery->num_rows() > 0) {
			return $oQuery->row();
		}
		return null;
	}
	// --------------------------------------------------------------------------------------------- //
	public function UpdateChangeIsFollowed($strChangeId, $strChangeView) {
		global $arrDefined;
		$this->db_ma->where('itsm_change_id', $strChangeId)->limit(1);
		/* $arrData = array('is_followed'=>1);
		$this->db_ma->update($this->tblChangeFollow, $arrData); */
		$this->db_ma->set('is_followed', 1);
		if(!empty($strChangeView) && $strChangeView === $arrDefined['change_view']['A']) {
			$this->db_ma->set('moved_all_changes', NULL);
		}
		$this->db_ma->update($this->tblChangeFollow);
		if($this->db_ma->affected_rows() > 0) {
			return AFFECTED_CODE;
		} else {
			return NO_AFFECTED_CODE;
		}
		
	}
	// --------------------------------------------------------------------------------------------- //
	public function MoveChangeToAllChanges($strChangeId) {
		$this->db_ma->where('itsm_change_id', $strChangeId)->limit(1);
		$arrData = array('moved_all_changes'=>1);
		$this->db_ma->update($this->tblChangeFollow, $arrData);
		if($this->db_ma->affected_rows() > 0) {
			return AFFECTED_CODE;
		} else {
			return NO_AFFECTED_CODE;
		}
	}
	// --------------------------------------------------------------------------------------------- //
	public function CountChangesByCondition($arrFilter, $iFollowed = null, $iMovedAllChanges=null) {
		if(!empty($arrFilter)) {
			$this->db_ma->like('service', $arrFilter['service']);
		}
		if(isset($iFollowed)) {
			$this->db_ma->where('is_followed', $iFollowed);
		}
		if(!isset($iMovedAllChanges)) {
			$this->db_ma->where('moved_all_changes IS NULL', NULL, false);
		}
		$this->db_ma->where('status', CHANGE_STATUS_INITIAL);
		$this->db_ma->where('planned_end >=', 'NOW()', FALSE);
		return $this->db_ma->count_all_results($this->tblChangeFollow);
	}
	// --------------------------------------------------------------------------------------------- //
}
?>
<?php
/*
 * Created on Feb 27, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once 'application/models/mongo_base_model.php';

class Alert_model extends Mongo_base_model
{
	// ----------------------------------------------------------------------------------------- //
	function __construct($right=null)
 	{
		parent :: __construct();
 	}
	
	// ----------------------------------------------------------------------------------------- //
	function GetIsChangedFlag($arrCondition)
	{
	  //  vd($strAlertId);
		$arrResult = $this->mongo_db->limit(1)
									->where($arrCondition)
									->get($this->cltFlags);
                                    
		if ($arrResult && count($arrResult))
		{
			if (isset($arrResult[0]['change']))
				return $arrResult[0]['change'];
			else
				return 0;
		}
		return 0;
	}
	
	// ----------------------------------------------------------------------------------------- //
 	public function UpdateIsChangedFlag($arrCondition, $arrData){
		$bRes = $this->UpdateMongoDB($arrCondition, $arrData, $this->cltFlags);
		return $bRes;
	}
	
 	// ----------------------------------------------------------------------------------------- //
 	function GetAlerts($arrCondition=null, $arrPagination=null, $arrSort=null){
		$arrAlerts = array();
		if(is_null($arrCondition) || !is_array($arrCondition)) $arrCondition = array();
		if(is_null($arrSort) || !is_array($arrSort)) $arrSort = array('clock' => -1);
		$this->BuildQueryConditions($arrCondition, $arrPagination, $arrSort);

 		$arrResults = $this->mongo_db->get($this->cltMAAlerts);

		if ($arrResults && count($arrResults))
		{
			foreach ($arrResults as $oAlert) {
				$arrAlerts[] = $oAlert;
			}
		}
		return $arrAlerts;
 	}
	// ----------------------------------------------------------------------------------------- //
 	function CountAlerts($arrCondition=null){
		$arrAlerts = array();
		$this->BuildQueryConditions($arrCondition, null, null);
		
 		$arrResults = $this->mongo_db->count($this->cltMAAlerts);
		return $arrResults;
 	}

	// ----------------------------------------------------------------------------------------- //
	function GetAlertById($strAlertId)
	{
		$arrResult = $this->mongo_db->limit(1)
									 ->where(array('_id' => new MongoId($strAlertId)))
									 ->get($this->cltMAAlerts);
		if ($arrResult && count($arrResult))
		{
			return $arrResult[0];
		}
		return null;
	}
	// ----------------------------------------------------------------------------------------- //
	function GetCSAlertById($strAlertId)
	{
	  //  vd($strAlertId);
		$arrResult = $this->mongo_db->limit(1)
									 ->where(array('_id' => new MongoId($strAlertId)))
									 ->get($this->cltCSAlerts);
                                    
		if ($arrResult && count($arrResult))
		{
			return $arrResult[0];
		}
		return null;
	}
 	// ----------------------------------------------------------------------------------------- //
 	function ListAlertDetail($arrGroupCondition, $offset=0, $limit=15)
 	{
 		if($this->rawMongoDBConnection){
	 		global $arrDefined;
	 		$arr_alertDetail = array();
	 		$db = $this->rawMongoDBConnection->selectDB($this->mongo_config_default['mongo_database']);
	 		$alertCollection = new MongoCollection($db, $this->tblAlert);

	 		$arrCondition = $this->BuildAlertDetailCondition($arrGroupCondition);
	 		if($this->CheckValidCondition($arrCondition)){
		 		$alertCursor  = $alertCollection->find($arrCondition)
		 						->limit($limit)->skip($offset)->sort(array('created_date' => -1));
		 		foreach ($alertCursor as $doc) {
		 			$arr_alertDetail[] = $doc;
		 		}
		 		return $arr_alertDetail;
	 		}
 		}
 		return array();
 	}
 	// ----------------------------------------------------------------------------------------- //
 	function BuildAlertDetailCondition($arrGroupCondition)
 	{
 		global $arrDefined;
 		$arrCondition = array();
 		if(!isset($arrGroupCondition['hostid']))
 		{
 			$arrCondition = array('$or' => array());
 			foreach($this->userRightHost as $serverId=>$arrHost)
 			{
 				$arrTmpCon = $arrGroupCondition;
 				$arrTmpCon['value_changed'] = 1;
	 			$arrTmpCon['host_zb_ip'] = array('$nin' => $arrDefined['unuse_ip']);
 				$arrTmpCon['zabbix_server_id'] = $serverId;
 				$arrTmpCon['hostid'] = array('$in' => $arrHost);
 				$arrCondition['$or'][] = $arrTmpCon;
 			}
 		}
 		else
 		{
 			$arrCondition = $arrGroupCondition;
			$arrCondition['value_changed'] = 1;
 		}

 		return $arrCondition;
 	}
 	// ----------------------------------------------------------------------------------------- //
 	function ACKAlertNO_INC($strAlertID, $strUser){
 		$this->UpdateMongoDB(
 			array('_id' => new MongoId($strAlertID)),
 			array(/*'is_show' => ALERT_STOP_BY_SDK_ACK_NOINC,*/ 'is_acked' => 1),
 			$this->cltMAAlerts
 		);
 		$this->InsertMongoDB(
 			array(
				'alert_id'     => new MongoId($strAlertID),
				'msg'          => ACK_NO_INC,
				'unixtime'     => time(),
				'created_date' => date('Y-m-d H:i:s'),
				'ack_by'       => $strUser
			), $this->cltMAAlertsACK
		);
 	}
 	// ----------------------------------------------------------------------------------------- //
 	function ACKAlertINC($strAlertID, $strUser){
 		$this->UpdateMongoDB(array('_id' => new MongoId($strAlertID)), array(/*'is_show' => ALERT_STOP_BY_SDK_ACK, */'is_acked' => 1), $this->cltMAAlerts);
 		$this->InsertMongoDB(
 			array(
				'alert_id'     => new MongoId($strAlertID),
				'msg'          => ACK_INC,
				'unixtime'     => time(),
				'created_date' => date('Y-m-d H:i:s'),
				'ack_by'       => $strUser
			), $this->cltMAAlertsACK);
 	}
 	// ----------------------------------------------------------------------------------------- //
 	function AddACKAlert($strAlertID, $arrMoreAlertIds, $strUser, $strMsg, $strTypeIssue) {
		$arrMAAlerts = array();

 		if(!empty($strAlertID)){
 			$oMAAlertOri = $this->SelectOneMongoDB(
 				array('_id' => new MongoId($strAlertID)),
 				$this->cltMAAlerts
 			);

			$this->InsertACKAlerts($strAlertID, $oMAAlertOri['source_from'], $oMAAlertOri['source_id'],
											$strMsg, $strUser, $strTypeIssue);

			$oNewestAlertOri = $this->GetNewestAlert($oMAAlertOri);
			if((string)$oNewestAlertOri['_id'] != $strAlertID) {
				$this->InsertACKAlerts($oNewestAlertOri['_id'], $oMAAlertOri['source_from'], $oMAAlertOri['source_id'],
											$strMsg, $strUser, $strTypeIssue);
			}

			if(!empty($arrMoreAlertIds)) {
				foreach ($arrMoreAlertIds as $key => $iAlertId) {
					$arrMAAlerts[] = $this->SelectOneMongoDB(
							 				array('_id' => new MongoId($iAlertId)),
							 				$this->cltMAAlerts
							 			);
				}

				foreach($arrMAAlerts as $key => $oMAAlert) {
					$this->InsertACKAlerts($oMAAlert['_id'], $oMAAlert['source_from'], $oMAAlert['source_id'],
											$strMsg, $strUser, $strTypeIssue);

					$oNewestAlert = $this->GetNewestAlert($oMAAlert);

					if((string)$oNewestAlert['_id'] != (string)$oMAAlert['_id']) {
						$this->InsertACKAlerts($oNewestAlert['_id'], $oMAAlert['source_from'], $oMAAlert['source_id'],
											$strMsg, $strUser, $strTypeIssue);
					}
				}
			}
			#pd($arrACKAlerts);

 		}
 	}
 	// ----------------------------------------------------------------------------------------- //
	function RejectCSAlert($strAlertId, $arrMoreAlertIds, $strMsg) {
		$arrError = array();
		$arrMAAlerts = array();
		if(!empty($strAlertId)){
			$arrMAAlerts[] = $this->SelectOneMongoDB(
				array('_id' => new MongoId($strAlertId)),
				$this->cltMAAlerts
			);
		}
		if(!empty($arrMoreAlertIds)) {
			foreach ($arrMoreAlertIds as $key => $iAlertId) {

				$arrMAAlerts[] = $this->SelectOneMongoDB(
										array('_id' => new MongoId($iAlertId)),
										$this->cltMAAlerts
									);
			}
		}

		foreach($arrMAAlerts as $key => $oMAAlert) {
			if($oMAAlert['source_from'] != "CS")
				continue;
			$bUpdRes = $this->UpdateCSAlert(array('_id' => new MongoId($oMAAlert['source_id']), 'itsm_id' => NULL), array('itsm_status' => 'rejected', 'itsm_status_notified' => 0, 'msg' => $strMsg, 'itsm_id' => NULL));
			if($bUpdRes == false)
			{
				$arrError[] = $oMAAlert['title'];
			}
		}
		return $arrError;
	}
 	// ----------------------------------------------------------------------------------------- //
	function LinkCSAlert($strAlertId, $arrMoreAlertIds, $strITSMId)
	{
		$arrLinkedAlert = array();
		$arrMAAlerts = array();
		if(!empty($strAlertId)){
			$arrMAAlerts[] = $this->SelectOneMongoDB(
				array('_id' => new MongoId($strAlertId)),
				$this->cltMAAlerts
			);
		}
		if(!empty($arrMoreAlertIds)) {
			foreach ($arrMoreAlertIds as $key => $iAlertId) {
				$arrMAAlerts[] = $this->SelectOneMongoDB(
										array('_id' => new MongoId($iAlertId)),
										$this->cltMAAlerts
									);
			}
		}
		foreach($arrMAAlerts as $key => $oMAAlert) {
			if($oMAAlert['source_from'] != "CS")
				continue;
			$this->UpdateCSAlert(array('_id' => new MongoId($oMAAlert['source_id'])), array('itsm_id' => $strITSMId, 'itsm_status_notified' => 0, 'is_linked' => 1));
			$arrLinkedAlert[] = array('src_from' => $oMAAlert['source_from'], 'src_id' => $oMAAlert['source_id']);
		}

		return $arrLinkedAlert;
	}
 	// ----------------------------------------------------------------------------------------- //
 	public function GetNewestAlert($oOldAlert){
		if(!empty($oOldAlert['source_from']) && !empty($oOldAlert['source_id'])){
			$arrAlerts = $this->SelectMongoDB(
				array(
					'source_from'    => $oOldAlert['source_from'],
					'source_id'      => $oOldAlert['source_id']
				),
				$this->cltMAAlerts,
				0, 1 /* offset, limit */,
				array('clock' => -1) //sort desc order
			);
			if(!empty($arrAlerts)) return $arrAlerts[0];
		}
		return $oOldAlert;
	}
 	// ----------------------------------------------------------------------------------------- //
 	public function UpdateMAAlert($arrCondition, $arrData){
		$this->UpdateMongoDB($arrCondition, $arrData, $this->cltMAAlerts);
	}

	// ----------------------------------------------------------------------------------------- //
 	public function UpdateCSAlert($arrCondition, $arrData){
		$bRes = $this->UpdateMongoDB($arrCondition, $arrData, $this->cltCSAlerts);
		return $bRes;
	}

	// ----------------------------------------------------------------------------------------- //
	public function UpdateMAIgnoredTrigger($arrCondition ,$arrData){
		$bRes = $this->UpdateMongoDB($arrCondition, $arrData, $this->cltMAIgnoredTrigger, array('upsert' => true));
		return $bRes;
	}

	// ----------------------------------------------------------------------------------------- //
	public function GetIgnoredTriggers() {
		$arrIgnoredTriggers = $this->SelectMongoDB(
			array(
				'is_ignored'    => YES
			),
			$this->cltMAIgnoredTrigger
		);
		if(!empty($arrIgnoredTriggers)) return $arrIgnoredTriggers;
	}

	// ----------------------------------------------------------------------------------------- //
 	public function ListAckOfAlert($strAlertID, $arrPagination){
		return $this->SelectMongoDB(array('alert_id' => new MongoId($strAlertID)),
			$this->cltMAAlertsACK
			, $arrPagination['offset']
			, $arrPagination['limit']
			, array('unixtime' => -1) /* Sort */
			, array('msg' => true, 'created_date' => true, 'ack_by' => true));
	}
	// ----------------------------------------------------------------------------------------- //
 	function CountACK($strAlertID){
		return $this->CountMongoDB(array('alert_id' => new MongoId($strAlertID)), $this->cltMAAlertsACK);
 	}
	// ----------------------------------------------------------------------------------------- //
	function InsertACKAlerts($oAlertId, $oSourceFrom, $oSourceId, $oAlertMessage, $strUser, $strTypeIssue) {
		$this->InsertMongoDB(
	 			array(
					'alert_id'     => new MongoId($oAlertId),
					'source_from'  => $oSourceFrom,
					'source_id'    => new MongoId($oSourceId),
					'msg'          => $oAlertMessage,
					'unixtime'     => time(),
					'created_date' => date('Y-m-d H:i:s'),
					'ack_by'       => $strUser,
					'type_issue'   => $strTypeIssue
				), $this->cltMAAlertsACK);
	}
	// ----------------------------------------------------------------------------------------- //
	public function ProcessMAIgnoredTrigger($arrCondition, $arrData) {
		if($arrData['is_ignored'] == 1) { /* ignored alert */
			$bRes = $this->UpdateMongoDB($arrCondition, $arrData, $this->cltMAIgnoredTrigger, array('upsert' => true));
		} else { /* Stop Ignore */
			$bRes = $this->RemoveMongoDB($arrCondition, $this->cltMAIgnoredTrigger);
		}
		return $bRes;
	}
	// ----------------------------------------------------------------------------------------- //
	public function ListAlertAttachment($oAlert) {
		$arrResult = $this->SelectMongoDB(
			array('source_from' => new MongoRegex('/^' . $oAlert['source_from'] . '$/i'), 'source_id' => $oAlert['ticket_id']),
			$this->cltAttachments, 0, UNLIMITED, array(), array('filename_alias', 'is_file_saved')
		);

		return $arrResult;
	}
	// ----------------------------------------------------------------------------------------- //
	public function GetRawAlert($oAlert){
		global $arrDefined;

		$oResult    = NULL;
		$strTBL     = '';
		$strSrcFrom = strtolower(@$oAlert['source_from']);

		if(!empty($strSrcFrom)){
			if(array_key_exists($strSrcFrom, $arrDefined['raw_alert_tbl_map'])){
				$strTBL = $arrDefined['raw_alert_tbl_map'][$strSrcFrom];
				$oResult = $this->SelectOneMongoDB(
					array('_id' => new MongoId($oAlert['source_id'])),
					$strTBL
				);
			}
		}

		return $oResult;
	}
	// ----------------------------------------------------------------------------------------- //
	/**
	 * function::GetHostsMaintenanceByClock
	 * description: check host is being alert which is maintained or not?
	 * author: ThaoDT 
	 */
	public function GetHostsMaintenanceByClock($iClock, $iZbxServerId, $arrSort) {
		$arrResult = $this->mongo_db->limit(1)
									->order_by($arrSort)
					   				->where_lte('clock', $iClock)
					   				->where_in('hostid', array($iZbxServerId))
					   				->get($this->cltTopMaintenance);
		// vd($this->mongo_db->last_query());
		if ($arrResult && count($arrResult))
		{
			return $arrResult[0];
		}
		return null;
	}
	// ----------------------------------------------------------------------------------------- //
	/**
	 * function::GetMaintenanceHostsByClock
	 * description: get hosts is maintained at current time when alert list func is called
	 * author: ThaoDT 
	 */
	public function GetMaintenanceHostsByClock($iClock, $arrSort) {
		$arrResult = $this->mongo_db->limit(1)
									->order_by($arrSort)
					   				->where_lte('clock', $iClock)
					   				->get($this->cltTopMaintenance);
		// vd($this->mongo_db->last_query());
		if ($arrResult && count($arrResult))
		{
			return $arrResult[0];
		}
		return null;
	}
}
?>

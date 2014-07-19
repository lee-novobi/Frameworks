<?php
/*
 * Created on Feb 27, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class Mongo_base_model extends CI_Model
{
	var $mongo_config_default          = NULL;

	// Cac table co su dung co che master/backup
	// Name cua cac table nay duoc load len tu db luc runtime
 	var $cltAlerts                      = '';
	var $cltMAAlerts                    = '';
	var $cltMAAlertsACK                 = '';
	var $cltMAIgnoredTrigger			= '';
	var $cltTopMaintenance				= '';

	// Cac table khong su dung co che master/backup
	// Name cua cac table nay duoc gan truc tiep trong constructor
 	var $tblActiveTable      = '';
	var $tblDepartments      = '';
	var $tblProducts         = '';
 	var $tblSession          = '';

	// ------------------------------------------------------------------------------------------ //
 	function __construct()
 	{
		parent :: __construct();

		if(!class_exists('Mongo_db'))
		{
			$this->load->library('mongo_db');
		}
		$this->mongo_db = new Mongo_db();
		$this->mongo_config_default  = $this->config->item('default');

		$this->cltAlerts      			= CLT_ALERTS;
		$this->cltCSAlerts    			= CLT_CS_ALERTS;
		$this->cltMAAlerts    			= CLT_MA_ALERTS;
		$this->cltMAAlertsACK 			= CLT_MA_ALERTS_ACK;
		$this->cltMAIgnoredTrigger	  	= CLT_MA_IGNORED_TRIGGER;
		$this->cltAttachments			= CLT_ATTACHMENT;
		$this->cltFlags					= CLT_FLAGS;
		$this->cltTopMaintenance		= CLT_TOP_MAINTENANCES;


		//$this->tblActiveTable                = TBL_ACTIVE_TABLE;
		//$this->GetActiveTables();
	}
	// ------------------------------------------------------------------------------------------ //
	protected function BuildQueryConditions($arrCondition=null, $arrPagination=null, $arrSort=null)
	{
		if (!is_null($arrCondition) && is_array($arrCondition) && count($arrCondition) > 0)
		{
			$this->mongo_db->where($arrCondition);
		}
		if (!is_null($arrSort) && is_array($arrSort) && count($arrSort) > 0)
		{
			$this->mongo_db->order_by($arrSort);
		}
		if (isset($arrPagination['limit']))
		{
			$this->mongo_db->limit($arrPagination['limit']);
		}
		if (isset($arrPagination['offset']))
		{
			$this->mongo_db->offset($arrPagination['offset']);
		}
	}
	// ------------------------------------------------------------------------------------------ //
	function GetActiveTables()
	{
		$arr_activeTables = $this->mongo_db->get($this->tblActiveTable);
		$arr_tmp = array();
		foreach($arr_activeTables as &$tbl)
		{
			$arr_tmp[$tbl['name']] = $tbl;
		}
		$arr_activeTables = $arr_tmp;
		ksort($arr_activeTables);


		$this->tblHosts        = $arr_activeTables[TBL_HOSTS]['active'];
		$this->tblItems        = $arr_activeTables[TBL_ITEMS]['active'];
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListProducts()
	{
		$raw = $this->mongo_db->select(array('productid', 'code'))->order_by(array('code'=> 1))
				->get($this->tblProducts);

		return $raw;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ListOSName()
	{
		$result = array();
		$raw = $this->mongo_db->select(array('os_type'), array('_id'))
				->order_by(array('os_type'=> 1))
				->get($this->tblHosts);
		foreach($raw as $rs)
		{
			if(isset($rs['os_type']) && $rs['os_type'] != '')
			{
				if(@!in_array($rs['os_type'], $result)) $result[] = @$rs['os_type'];
			}
		}
		$result[] = 'Unknown';
		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	public function IsCollectionExists($strCollectionName)
	{
		$rs = $this->mongo_db->where(
        	array(
				'name' => $this->mongo_config_default['mongo_database'] . '.' . $strCollectionName
			)
		)->get('system.namespaces');
		if (count($rs) > 0)
        	return true;
		else
			return false;
    }
	// ------------------------------------------------------------------------------------------ //
	protected function CheckValidCondition($arrCondition){
		$result = true;
		if(isset($arrCondition['$or']) && count($arrCondition['$or']) <= 0) $result = false;

		return $result;
	}
	// ------------------------------------------------------------------------------------------ //
	public function CountMongoDB($arrCondition, $strCollectionName){
		$nResult = 0;

		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_default['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				$nResult = $oCollection->find($arrCondition)->count();
			}
		}

		return $nResult;
	}
	// ------------------------------------------------------------------------------------------ //
	public function SelectOneMongoDB($arrCondition, $strCollectionName, $arrSelectField=array()){
		$oResult = null;

		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_default['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				if(empty($arrSelectField)){
					$oResult = $oCollection->findOne($arrCondition);
				} else {
					$oResult = $oCollection->findOne($arrCondition, $arrSelectField);
				}
			}
		}

		return $oResult;
	}
	// ------------------------------------------------------------------------------------------ //
	public function SelectMongoDB($arrCondition, $strCollectionName, $offset=0, $limit=UNLIMITED, $arrSort=array(), $arrSelectField=array()){
		$arrResult = array();

		$oMgConn = $this->mongo_db->get_connection();
		if(!is_array($arrSort)) $arrSort = array();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_default['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				if(empty($arrSelectField)){
					$oCursor = $oCollection->find($arrCondition)->skip($offset)->limit($limit)->sort($arrSort);
				} else {
					$oCursor = $oCollection->find($arrCondition, $arrSelectField)->skip($offset)->limit($limit)->sort($arrSort);
				}
				foreach($oCursor as $oDoc){
					$arrResult[] = $oDoc;
				}
			}
		}

		return $arrResult;
	}

	/* -------------------------------------------------------------------------
	 * UpdateMongoDB
	 * -------------------------------------------------------------------------
	 * Function to update MongoDB
	 *
	 * @param: $arrCondition, $arrNewData, $strCollectionName, $arrOptions
	 * @return: boolean (true if success, false if error)
	 *
	 * Note: $arrRes contains:
	 * 	'updatedExisting' => boolean false
	 * 	'n' => int 1
	 * 	'connectionId' => int 1299857
	 * 	'err' => null
	 * 	'ok' => float 1
	 * 	'upserted' =>
	 * 		object(MongoId)[28]
	 * 		public '$id' => string '5269e6bab10d137e80248d45' (length=24)
	 *
	 */


	public function UpdateMongoDB($arrCondition, $arrNewData, $strCollectionName, $arrOptions=array('multiple' => true)){
		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_default['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				$arrRes = $oCollection->update($arrCondition, array('$set' => $arrNewData), $arrOptions);
				if (isset($arrRes['ok']) && intval($arrRes['ok']) == 1 &&  intval($arrRes['n']) != 0)
				{
					return true;
				}
				else
				{
					$errMsg = (!is_null($arrRes['err'] )) ? $arrRes['err'] : 'Error update MongoDB';
					log_message('error', sprintf('Error Message: %s', $errMsg));
					return false;
				}
			}
		}
		return false;
	}


	/* -------------------------------------------------------------------------
	 * InsertMongoDB
	 * -------------------------------------------------------------------------
	 * Function to insert data into MongoDB
	 *
	 * @param: $arrNewData, $strCollectionName
	 * @return: boolean (true if success, false if error)
	 *
	 * Note: $arrRes contains:
	 * 	'n' => int 0
	 * 	'connectionId' => int 1299857
	 * 	'err' => null
	 * 	'ok' => float 1
	 *
	 */

	public function InsertMongoDB($arrNewData, $strCollectionName){
		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_default['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				$arrRes = $oCollection->insert($arrNewData);
				if (isset($arrRes['ok']) && intval($arrRes['ok']) == 1)
				{
					return true;
				}
				else
				{
					$errMsg = (!is_null($arrRes['err'] )) ? $arrRes['err'] : 'Error insert MongoDB';
					log_message('error', sprintf('Error Message: %s', $errMsg));
					return false;
				}
			}
		}
		return false;
	}
	// ------------------------------------------------------------------------------------------ //

	/* -------------------------------------------------------------------------
	 * BatchInsertMongoDB
	 * -------------------------------------------------------------------------
	 * Function to insert multiple documents into MongoDB
	 *
	 *
	 */
	public function BatchInsertMongoDB($arrData, $strCollectionName) {
		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_default['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				$arrRes = $oCollection->batchInsert($arrData);
				if (isset($arrRes['ok']) && intval($arrRes['ok']) == 1)
				{
					return true;
				}
				else
				{
					$errMsg = (!is_null($arrRes['err'] )) ? $arrRes['err'] : 'Error batchinsert MongoDB';
					log_message('error', sprintf('Error Message: %s', $errMsg));
					return false;
				}
			}
		}
		return false;
	}
	/* -------------------------------------------------------------------------
	 * RemoveMongoDB
	 * -------------------------------------------------------------------------
	 * Function to remove documents that matches conditions in MongoDB
	 *
	 *
	 */
	public function RemoveMongoDB($arrConditions, $strCollectionName) {
		$oMgConn = $this->mongo_db->get_connection();
		if($oMgConn != null){
			$oCollection = $oMgConn->selectCollection($this->mongo_config_default['mongo_database'],
								$strCollectionName);
			if($oCollection != null){
				$arrRes = $oCollection->remove($arrConditions);
				#var_dump($arrRes);
				if (isset($arrRes['ok']) && intval($arrRes['ok']) == 1)
				{
					return true;
				}
				else
				{
					$errMsg = (!is_null($arrRes['err'] )) ? $arrRes['err'] : 'Error batchinsert MongoDB';
					log_message('error', sprintf('Error Message: %s', $errMsg));
					return false;
				}
			}
		}
		return false;
	}
}
?>

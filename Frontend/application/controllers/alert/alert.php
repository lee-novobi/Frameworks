<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_controller.php';

/**
 *
 * alert.php: This file contains alert Class, the controller for Alerts of Monitor_Assistant
 *
 **/

/**
 * Alert Class
 *
 *
 **/

class Alert extends Base_controller {
/**
 *
 * Constructor: extends from Base controller Class
 *
 */
	public function __construct(){
		parent::__construct();
		$this->load->model('alert/alert_model', 'alert_model');
		$this->load->model('incident/mysql_incident_model', 'mysql_incident_model');
        $this->load->model('contact/contact_model', 'contact_model');
		$this->db4log = null;
	}

/**
 * Index
 *
 * Default page
 *
 */
	public function index()
	{
		header('Location: '. $base_url.'alert/alert/alert_list');
		exit;
	}
    
/**
 * ajax_is_changed
 *
 *
 */	
	public function sse_get_is_changed_flag()
	{
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');

		$arrCondition = array('code' => 'maintenance');
		$oResult = $this->alert_model->GetIsChangedFlag($arrCondition);
		if ($oResult == 0) 
		{
			echo "data: ".NO."\n\n";
		}
		else 
		{
			echo "data: ".YES."\n\n";
			sleep(30);
			$arrData = array('change' => NO);	
			$this->alert_model->UpdateIsChangedFlag($arrCondition, $arrData);
		}

		flush();
	}
	
/**
 * ajax_get_alert_groups
 *
 *
 */
    public function ajax_get_alert_groups()
    {
        $arrGroupBySrc = array();
		$arrCondition = $this->GetAlertListFilter();
		
        $arrConditionNoACK = $arrCondition;
        $arrConditionNoACK['is_show']         = ALERT_SHOW;
		$arrConditionNoACK['is_acked']        = IS_NO_ACKED;
		/* $arrConditionNoACK['$or'] = array(
			array('zbx_maintenance' => array('$exists' => false)),
			array('zbx_maintenance' => NO),
		); */
		$arrSort = array('clock' => -1);
		$arrMaintenanceHosts = $this->alert_model->GetMaintenanceHostsByClock(time(), $arrSort);
		if(!empty($arrMaintenanceHosts)) {
			$arrMaintenanceHosts = $arrMaintenanceHosts['hostid'];
			$arrConditionNoACK['zabbix_server_id'] = array('$nin' => $arrMaintenanceHosts);
		}
		
        $arrAlertNoACK = $this->alert_model->GetAlerts($arrConditionNoACK);
        $arrProducts = array();
		$arrProducts['All'] = count($arrAlertNoACK);
        foreach ($arrAlertNoACK as $oAlert) 
        {
            if (isset($oAlert['product']) && (trim($oAlert['product'])!=''))
            {
                $strUpperProduct = strtoupper($oAlert['product']);
                isset($arrProducts[$strUpperProduct]) ? $arrProducts[$strUpperProduct] += 1 
                                                      : $arrProducts[$strUpperProduct] = 1;
            }
            else 
            {
                isset($arrProducts[UNKNOWN]) ? $arrProducts[UNKNOWN] += 1 
                                             : $arrProducts[UNKNOWN] = 1;
            }
        }
    	$strHTML = $this->load->view('alert/ajax_alert_group',array(
		                                            'arrProducts' => $arrProducts
	                                           ), true);
		echo $strHTML;
		exit;
    }

/**
 * Alert List
 *
 * Show list of alerts sort by clock
 * NOTES: shift chief -> show full | Members -> only show tasks followed
 *
 */
	public function alert_list()
	{
		$arrSort = array('clock' => -1);
		// $oCheckMaintenance = null;
		$arrMaintenanceHosts = array();
		$arrCondition = $this->GetAlertListFilter();
		$arrConditionNoACK = $arrCondition;
		$arrConditionNoACK['is_show']         = ALERT_SHOW;
		$arrConditionNoACK['is_acked']        = IS_NO_ACKED;
		#$iCallSuccess = $iCallFail = 0;
		
		/* Edited by: ThaoDT */
		$arrMaintenanceHosts = $this->alert_model->GetMaintenanceHostsByClock(time(), $arrSort);
		if(!empty($arrMaintenanceHosts)) {
			$arrMaintenanceHosts = $arrMaintenanceHosts['hostid'];
			$arrConditionNoACK['zabbix_server_id'] = array('$nin' => $arrMaintenanceHosts);
		}
		// pd($arrMaintenanceHosts);
		/* End [ThaoDT]-edited */
		// pd($arrConditionNoACK);
		/* if (isset($arrConditionNoACK['$and'])) { 
			$arrConditionNoACK['$and'][] = array( 
												'$or' => array(
																	array('zbx_maintenance' => array('$exists' => false)),
																	array('zbx_maintenance' => NO),
																));
		} else {
			$arrConditionNoACK['$or'] = array(
				array('zbx_maintenance' => array('$exists' => false)),
				array('zbx_maintenance' => NO),
			);
		} */
		
		$arrPaginationNoACK = array();
		$arrPaginationNoACK = $this->GetPaginationRequest('limit_no_acked', 'page_no_acked');

		$iTotalRowNoACK     = $this->alert_model->CountAlerts($arrConditionNoACK);
		// p($iTotalRowNoACK);
		if((($arrPaginationNoACK['page']-1)*$arrPaginationNoACK['limit']) >= $iTotalRowNoACK){
			$arrPaginationNoACK['page']   = 1;
			$arrPaginationNoACK['offset'] = 0;
		}
		$arrIgnoredTriggers = $this->GetIgnoredTriggers();
		$arrAlertsNoACK     = $this->alert_model->GetAlerts($arrConditionNoACK, $arrPaginationNoACK, $arrSort);
		
		$arrAlertsNoACK = $this->CalNumOfCallFromAlert($arrAlertsNoACK);
		
       	// pd($arrAlertsNoACK);
		$strAlertViewNoACK  = $this->load->view('alert/ajax_alert_list',
				array(
					'arrAlerts'				=> $arrAlertsNoACK
					, 'arrIgnoredTriggers'  => $arrIgnoredTriggers
					, 'row_alternate'		=> false
					, 'base_url'			=> base_url()
					, 'bIsAjax'				=> false
				), true
		);


		$arrConditionACK = $arrCondition;
		$arrConditionACK['is_show']         = ALERT_SHOW;
		$arrConditionACK['is_acked']        = IS_ACKED;
		/* Edited by: ThaoDT */
		if(!empty($arrMaintenanceHosts)) {
			$arrConditionACK['zabbix_server_id'] = array('$nin' => $arrMaintenanceHosts);
		}
		/* End [ThaoDT]-edited */
		// $oCheckMaintenance = null;
		/* $arrConditionACK['$or'] = array(
			array('zbx_maintenance' => array('$exists' => false)),
			array('zbx_maintenance' => NO),
		); */
		
		$arrPaginationACK = array();
		$arrPaginationACK = $this->GetPaginationRequest('limit_acked', 'page_acked');

		$iTotalRowACK     = $this->alert_model->CountAlerts($arrConditionACK);
		// p($iTotalRowACK);
		if((($arrPaginationACK['page']-1)*$arrPaginationACK['limit']) >= $iTotalRowACK){
			$arrPaginationACK['page']   = 1;
			$arrPaginationACK['offset'] = 0;
		}

		$arrAlertsACK     = $this->alert_model->GetAlerts($arrConditionACK, $arrPaginationACK, $arrSort);
		
		$arrAlertsACK = $this->CalNumOfCallFromAlert($arrAlertsACK);
		// pd($arrAlertsACK);
		$strAlertViewACK  = $this->load->view('alert/ajax_alert_list',

				array(
					'arrAlerts'				=> $arrAlertsACK
					, 'arrIgnoredTriggers'  => $arrIgnoredTriggers
					, 'row_alternate'		=> false
					, 'base_url'			=> base_url()
					, 'bIsAjax'				=> false
				), true
		);
		
		# build alert list view

		$strQueryString = $this->ParseAlertListQueryString();
		if ($this->input->get('layout') == "dashboard") {
			$this->loadview('alert/alert_list',
					array(
						'strAlertViewNoACK'	=> $strAlertViewNoACK
						, 'iTotalRowNoACK'	=> $iTotalRowNoACK
						, 'iPageNoACK'		=> $arrPaginationNoACK['page']
						, 'iPageSizeNoACK'	=> $arrPaginationNoACK['limit']
						, 'strAlertViewACK'	=> $strAlertViewACK
						, 'iTotalRowACK'	=> $iTotalRowACK
						, 'iPageACK'		=> $arrPaginationACK['page']
						, 'iPageSizeACK'	=> $arrPaginationACK['limit']
						, 'strQueryString'  => $strQueryString
						, 'iTotalRowHistory'	=> $iTotalRowNoACK
						, 'iPageHistory'		=> $arrPaginationNoACK['page']
						, 'iPageSizeHistory'	=> $arrPaginationNoACK['limit']
					), 'layout_dashboard'
			);
		}
		else {
			$this->loadview('alert/alert_list'
				, array(
					'strAlertViewNoACK'	=> $strAlertViewNoACK
					, 'iTotalRowNoACK'	=> $iTotalRowNoACK
					, 'iPageNoACK'		=> $arrPaginationNoACK['page']
					, 'iPageSizeNoACK'	=> $arrPaginationNoACK['limit']
					, 'strAlertViewACK'	=> $strAlertViewACK
					, 'iTotalRowACK'	=> $iTotalRowACK
					, 'iPageACK'		=> $arrPaginationACK['page']
					, 'iPageSizeACK'	=> $arrPaginationACK['limit']
					, 'strQueryString'  => $strQueryString
					, 'iTotalRowHistory'	=> $iTotalRowNoACK
					, 'iPageHistory'		=> $arrPaginationNoACK['page']
					, 'iPageSizeHistory'	=> $arrPaginationNoACK['limit']
				)
			);
		}
	}

/**
 * Alert List History
 *
 * Show list of alerts sort by clock
 * NOTES: shift chief -> show full | Members -> only show tasks followed
 *
 */
	public function alert_list_history()
	{
		$arrSort = array('clock' => -1);
		$arrCondition = $this->GetAlertListFilter();
		$arrConditionHistory = $arrCondition;
		$arrPaginationHistory = array();
		$arrPaginationHistory = $this->GetPaginationRequest('limit_no_acked', 'page_no_acked');

		$iTotalRowHistory     = $this->alert_model->CountAlerts($arrConditionHistory);
		if((($arrPaginationHistory['page']-1)*$arrPaginationHistory['limit']) >= $iTotalRowHistory){
			$arrPaginationHistory['page']   = 1;
			$arrPaginationHistory['offset'] = 0;
		}
		$arrIgnoredTriggers = $this->GetIgnoredTriggers();
		$arrAlertsHistory     = $this->alert_model->GetAlerts($arrConditionHistory, $arrPaginationHistory, $arrSort);
		$arrAlertsHistory = $this->CalNumOfCallFromAlert($arrAlertsHistory);
		$strAlertViewHistory  = $this->load->view('alert/ajax_alert_list',
				array(
					'arrAlerts'				=> $arrAlertsHistory
					, 'arrIgnoredTriggers'  => $arrIgnoredTriggers
					, 'row_alternate'		=> false
					, 'base_url'			=> base_url()
					, 'bIsAjax'				=> false
				), true
		);

		# build alert list view
		
		// var_dump($iTotalRowHistory); exit;
		$strQueryString = $this->ParseAlertListQueryString();
		$this->loadview('alert/alert_list_history'
				, array(
					'strAlertViewHistory'	=> $strAlertViewHistory
					, 'iTotalRowHistory'	=> $iTotalRowHistory
					, 'iPageHistory'		=> $arrPaginationHistory['page']
					, 'iPageSizeHistory'	=> $arrPaginationHistory['limit']
					, 'iTotalRowACK'	=> $iTotalRowHistory
					, 'iPageACK'		=> $arrPaginationHistory['page']
					, 'iPageSizeACK'	=> $arrPaginationHistory['limit']
					, 'strQueryString'  => $strQueryString
					, 'iTotalRowNoACK'	=> $iTotalRowHistory
					, 'iPageNoACK'		=> $arrPaginationHistory['page']
					, 'iPageSizeNoACK'	=> $arrPaginationHistory['limit']
				)
			);
	}
	
	// ------------------------------------------------------------------------------------------ //
	private function ParseAlertListQueryString(){
		$arrParam = array();
		parse_str($_SERVER['QUERY_STRING'], $arrParam);

		unset($arrParam['page_no_acked']);
		unset($arrParam['page_acked']);
		unset($arrParam['limit_no_acked']);
		unset($arrParam['limit_acked']);

		return http_build_query($arrParam);
	}
	// ------------------------------------------------------------------------------------------ //
	private function GetAlertListFilter(){
		$arrResult = array();

		if(!empty($_REQUEST['source_from'])){
			$arrResult['source_from'] = new MongoRegex('/^' . $this->input->get_post('source_from') . '$/i');
		}
		if(!empty($_REQUEST['zbx_host'])){
			$arrResult['zbx_host'] = new MongoRegex('/' . $this->input->get_post('zbx_host') . '/i');
		}
		if(!empty($_REQUEST['product']) && $_REQUEST['product'] != UNKNOWN && $_REQUEST['product'] != ALL){
			$strProduct = $this->input->get_post('product');
			$strProduct = str_replace(array('[', ']'), array('\[', '\]'), $strProduct);
			$strProduct = str_replace(array('(', ')'), array('[(]', '[)]'), $strProduct);
			$arrResult['product'] = new MongoRegex('/^' . $strProduct . '$/i');
		}
		elseif (@$_REQUEST['product'] == UNKNOWN) {
			$arrResult['$and'] = array( 
									array ('$or' => array(
										array('product' => array('$exists' => false)),
										array('product' => ""),
										array('product' => NULL)
									))
								); 
		}
		return $arrResult;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ajax_get_alerts()
	{
		$strACKType     = @$_REQUEST['is_acked'];
		$strACKTypeName = empty($strACKType)?'_no_acked':'_acked';

		$arrCondition = array();
		$arrCondition['is_show']  = ALERT_SHOW;
		$arrCondition['is_acked'] = intval($strACKType);
		/* $arrCondition['$or'] = array(
			array('zbx_maintenance' => array('$exists' => false)),
			array('zbx_maintenance' => NO),
		); */
		$arrSort = array('clock' => -1);
		$arrMaintenanceHosts = $this->alert_model->GetMaintenanceHostsByClock(time(), $arrSort);
		if(!empty($arrMaintenanceHosts)) {
			$arrMaintenanceHosts = $arrMaintenanceHosts['hostid'];
			$arrCondition['zabbix_server_id'] = array('$nin' => $arrMaintenanceHosts);
		}

		$arrPagination = $this->GetPaginationRequest('limit'.$strACKTypeName, 'page'.$strACKTypeName);
		$arrAlerts = $this->alert_model->GetAlerts($arrCondition, $arrPagination);
		$arrAlerts = $this->CalNumOfCallFromAlert($arrAlerts);
		$arrIgnoredTriggers = $this->GetIgnoredTriggers();
		$this->loadview('alert/ajax_alert_list',
				array(
					'arrAlerts'				=> $arrAlerts
					, 'arrIgnoredTriggers'  => $arrIgnoredTriggers
					, 'row_alternate'		=> false
					, 'base_url'			=> base_url()
					, 'bIsAjax'				=> true
				), 'layout_ajax'
		);
	}

	// ------------------------------------------------------------------------------------------ //
	public function ajax_ack_no_inc()
	{
		$strAlertId  = $_REQUEST['aid'];

		if(!empty($strAlertId)){
			$strUser = $this->session->userdata('username');
			$this->alert_model->ACKAlertNO_INC($strAlertId, $strUser);
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function ajax_ack_list(){
		$arrResult     = array();

		$arrPagination = $this->GetPaginationRequest('limit', 'page');
		$strAlertId    = $_REQUEST['alertid'];

		if(!empty($strAlertId)){
			$arrResult = $this->alert_model->ListAckOfAlert($strAlertId, $arrPagination);
		}

		$strHTML = $this->load->view('alert/ajax_ack_alert_body',array(
			'arrACK' => $arrResult
		), true);
		echo $strHTML;
		exit;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ack_alert() {
		$nTotalACK   = 0;
		$strAlertId  = $_REQUEST['alertid'];
		$arrPagination = $this->GetPaginationRequest('limit', 'page');
		$arrIncFollow = $this->mysql_incident_model->GetIncFollowList();
		
		if(!empty($strAlertId)) {
			$nTotalACK = $this->alert_model->CountACK($strAlertId);
			$oAlert = $this->alert_model->GetAlertById($strAlertId);
		}

		$arrSort = array('created_date' => '1');
		$arrCondition = $this->GetAlertListFilter();

		$arrConditionNoACK = $arrCondition;
		$arrConditionNoACK['is_show']   = ALERT_SHOW;
		$arrConditionNoACK['is_acked']  = IS_NO_ACKED;
		$arrConditionNoACK['_id'] = array('$ne' => new MongoId($strAlertId));
		/* $arrConditionNoACK['$or'] = array(
			array('zbx_maintenance' => array('$exists' => false)),
			array('zbx_maintenance' => NO),
		); */
		$arrMaintenanceHosts = $this->alert_model->GetMaintenanceHostsByClock(time(), array('clock' => -1));
		if(!empty($arrMaintenanceHosts)) {
			$arrMaintenanceHosts = $arrMaintenanceHosts['hostid'];
			$arrConditionNoACK['zabbix_server_id'] = array('$nin' => $arrMaintenanceHosts);
		}

		$arrIgnoredTriggers = $this->GetIgnoredTriggers();
		$arrAlertsNoACK     = $this->alert_model->GetAlerts($arrConditionNoACK, null, $arrSort);
		$arrAlertsNoACK = $this->CalNumOfCallFromAlert($arrAlertsNoACK);
		
		$this->loadview('alert/ajax_ack_alert'
			, array(
				'arrIncFollow'			=> $arrIncFollow,
				'oAlert'				=> $oAlert,
				'arrAlertsNoACK'     	=> $arrAlertsNoACK,
				'arrIgnoredTriggers' 	=> $arrIgnoredTriggers,
				'nTotalACK'  			=> $nTotalACK,
				'nPage'      			=> $arrPagination['page'],
				'nPageSize'  			=> $arrPagination['limit'],
				'row_alternate'			=> false
			), 'layout_popup');
	}

	// ------------------------------------------------------------------------------------------ //
	public function add_alert_ack(){
		$strAlertId  = $_REQUEST['alertid'];
		$strACKMsg   = $_REQUEST['msg'];
		$arrMoreAlertIds = $_REQUEST['arrMoreAlerts'];
		$strTypeIssue = $_REQUEST['type_issue'];
		if(!empty($strAlertId) && !empty($strACKMsg) && !empty($strTypeIssue)) {
			$strUser = $this->session->userdata('username');
			$arrACK  = $this->alert_model->AddACKAlert($strAlertId, $arrMoreAlertIds, $strUser, $strACKMsg, $strTypeIssue);
			if(!empty($arrMoreAlertIds)) {
				foreach($arrMoreAlertIds as $key=>$oAlertId) {
					$this->alert_model->UpdateMAAlert(array('_id' => new MongoId($oAlertId)), array('is_acked' => 1));
				}
			}
			$this->alert_model->UpdateMAAlert(array('_id' => new MongoId($strAlertId)), array(
				'is_acked' => 1
			));
		}
	}

	// ------------------------------------------------------------------------------------------ //
	public function reject_cs_alert(){
		$arrError = array();
		//===============Init==================
		$arrMoreAlertIds = array();
		$strAlertId  = $_REQUEST['alertid'];
		if(isset($_REQUEST['msg']))
			$strMsg   = 'Reject Alert - '.$_REQUEST['msg'];
		else
			$strMsg   = 'Reject Alert';
		if(isset($_REQUEST['arrMoreAlerts']))
			$arrMoreAlertIds = $_REQUEST['arrMoreAlerts'];
		$strTypeIssue = $_REQUEST['type_issue'];
		//=====================================
		if(!empty($strAlertId) && !empty($strMsg) && $strTypeIssue == ISSUE_TYPE_NO_INC) {
			$arrError = $this->alert_model->RejectCSAlert($strAlertId, $arrMoreAlertIds, $strMsg);
			//====================ACK======================
			$strUser = $this->session->userdata('username');
			$arrACK  = $this->alert_model->AddACKAlert($strAlertId, $arrMoreAlertIds, $strUser, $strMsg, $strTypeIssue);
			if(!empty($arrMoreAlertIds)) {
				foreach($arrMoreAlertIds as $key=>$oAlertId) {
					$this->alert_model->UpdateMAAlert(array('_id' => new MongoId($oAlertId)), array('is_acked' => 1));
				}
			}
			$this->alert_model->UpdateMAAlert(array('_id' => new MongoId($strAlertId)), array(
				'is_acked' => 1
			));
			//=============================================
			if(empty($arrError))
			{
				$arrResult['msg'] = 'Success! Rejected alerts.';
				$arrResult['result'] = true;
				echo (json_encode($arrResult));
				exit;
			}
			else
			{
				$arrResult['arrError'] = $arrError;
				$arrResult['msg'] = 'Fail! Can not reject alerts: ';
				$arrResult['result'] = false;
				echo (json_encode($arrResult));
				exit;
			}
		}
		else {
			$arrResult['msg'] = 'Error! Empty reject message.';
			$arrResult['result'] = false;
			echo (json_encode($arrResult));
			exit;
		}
	}
	
	// ------------------------------------------------------------------------------------------ //
	public function link_cs_alert(){
		//===============Init==================
		$arrLinkedAlert = array();
		$arrMoreAlertIds = array();
		$strAlertId  = $_REQUEST['alertid'];
		if(isset($_REQUEST['arrMoreAlerts']))
			$arrMoreAlertIds = $_REQUEST['arrMoreAlerts'];
		$strTypeIssue = $_REQUEST['type_issue'];
		$strITSMInc   = $_REQUEST['strITSMId'];
		$arrITSMId = explode("-", $strITSMInc);
		$strITSMId = $arrITSMId[0];
		if(empty($_REQUEST['msg']))
			$strMsg   = 'Link to : '.$strITSMId.' - '.$_REQUEST['msg'];
		else
			$strMsg   = 'Link to : '.$strITSMId;
		//=====================================
		$bResult = $this->mysql_incident_model->IsIncFollowExist($strITSMId);
		if($bResult){
			if(!empty($strAlertId) && !empty($strITSMId)) {
				$arrLinkedAlert = $this->alert_model->LinkCSAlert($strAlertId, $arrMoreAlertIds, $strITSMId);
				$this->mysql_incident_model->UpdateIncFollowLinkedAlerts($strITSMId, $arrLinkedAlert);
				//====================ACK======================
				$strUser = $this->session->userdata('username');
				$arrACK  = $this->alert_model->AddACKAlert($strAlertId, $arrMoreAlertIds, $strUser, $strMsg, $strTypeIssue);
				if(!empty($arrMoreAlertIds)) {
					foreach($arrMoreAlertIds as $key=>$oAlertId) {
						$this->alert_model->UpdateMAAlert(array('_id' => new MongoId($oAlertId)), array('is_acked' => 1));
					}
				}
				$this->alert_model->UpdateMAAlert(array('_id' => new MongoId($strAlertId)), array(
					'is_acked' => 1
				));
				//=============================================
				$arrResult['msg'] = 'Success! Linked alert to incident.';
				$arrResult['result'] = true;
				echo (json_encode($arrResult));
				exit;
			}
			else {
				$arrResult['msg'] = 'Error! Empty ITSM incident ID.';
				$arrResult['result'] = false;
				echo (json_encode($arrResult));
				exit;
			}
		}
		else
		{
			$arrResult['msg'] = 'Error! Incident ID does not exist.';
			$arrResult['result'] = false;
			echo (json_encode($arrResult));
			exit;
		}
	}
	
	// ------------------------------------------------------------------------------------------ //
	private function GetIgnoredTriggers(){
		$arrIgnoredTriggers = array();
		$arrRs = $this->alert_model->GetIgnoredTriggers();
		#vd($arrRs);

		if(!empty($arrRs)) {
			foreach ($arrRs as $oTrigger)
			{
				if (isset($oTrigger['triggerid']) && intval($oTrigger['triggerid']) != 0)
					$arrIgnoredTriggers[] = $oTrigger['triggerid'];
				elseif(intval($oTrigger['triggerid']) == 0 && (string)@$oTrigger['alertid'] !== '') {
					$arrIgnoredTriggers[] = @$oTrigger['alertid'];
				}
			}

		}

		return $arrIgnoredTriggers;
	}

	// ------------------------------------------------------------------------------------------ //
	public function ignore_alert(){
		//error_reporting(E_ALL);
		$strTriggerId  = $_REQUEST['triggerid'];
		$iIsIgnore = $_REQUEST['is_ignored'];
		$strSourceFrom = $_REQUEST['source_from'];
		$strSourceId = $_REQUEST['source_id'];
		$strMsg = $_REQUEST['msg'];
		$bRes = false;
		$iIsIgnore = intval($iIsIgnore);
		$iTriggerId = 0;
		$strAlertId = '';

		if(!empty($strTriggerId)) {
			$strUser = $this->session->userdata('username');
			if(strtolower($strSourceFrom) != 'zabbix') {
				$strAlertId = $strTriggerId;
			} else {
				$iTriggerId = intval($strTriggerId);
			}
			// v($iTriggerId);
			// vd($strAlertId);
			$bRes = $this->alert_model->ProcessMAIgnoredTrigger(array('triggerid' => $iTriggerId, 'alertid' => $strAlertId), array(
																	'is_ignored' => $iIsIgnore, 'updated_by' => $strUser,
																	'source_from' => $strSourceFrom, 'source_id' => $strSourceId,
																	'message' => $strMsg
																));
		}
		if ($bRes) {
			if ($iIsIgnore == YES) {
				$this->session->set_flashdata('msg','Success! Alerts by this trigger will be ignored!');
			} else {
				$this->session->set_flashdata('msg','Success! Stop ignoring this alert!');
			}
			$this->session->set_flashdata('type_msg', 'success');
		}
		else {
			$this->session->set_flashdata('msg','Error! Cannot ignore this alert!');
			$this->session->set_flashdata('type_msg', 'error');
		}
	}
    // ------------------------------------------------------------------------------------------ //
    public function contact_point_alert() {
    	global $arrDefined;
    	$arrUserInfo = array(); //contact result found
		$arrProducts = array();
		$arrProductId = array();
		$iDepartmentSelected = null;
		$iProductIdSelected = null;
		$arrDepartments = $this->contact_model->GetDepartmentListForContact();
		$strAlertId = $strZbxHostName = $strAlertMsg = $strTimeAlert = null;
		
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$arrProductTemp = array();
			$strZbxServerID = $_GET['zbxServerID'];
        	$strZbxHostId = $_GET['zbxHostID'];
        	$strZbxHostName = $_GET['zbxHostName'];
			$strAlertId = $_GET['alert_id'];
			$strAlertMsg = $_GET['alert_msg'];
			$strTimeAlert = $_GET['time_alert'];
			$strSourceFrom = $_GET['source_from'];
			$strProduct = $_GET['product'];
			$strDepartment = $_GET['department'];
			// pd($strAlertId);
			if(strtolower($strSourceFrom) !== strtolower(SOURCE_FROM_SO6)) {
				$arrProductCode = $this->contact_model->getProductCodeByHostIdServerID($strZbxHostId, $strZbxServerID);
	        	if (!empty($arrProductCode)) {
		            $arrProductTemp = $this->contact_model->getProductIdByProductCode($arrProductCode['cmdb_product_code']);  
		        }
			} else {
				$arrProductTemp = $this->contact_model->getProductContactPointByPName(trim($strDepartment), trim($strProduct));
			}
			// pd($arrProductId);
			if(!empty($arrProductTemp)) {
				$iProductIdSelected = intval($arrProductTemp['productid']);
	            $iDepartmentSelected = intval($arrProductTemp['department_id']);
				$arrProductId[] = intval($arrProductTemp['productid']);
				$arrUserInfo = $this->contact_model->getContactByProduct($arrProductId);
				// pd($arrUserInfo);
				if(!empty($arrUserInfo)) {
					if(count($arrUserInfo) == 1) {
						$arrOrders = array('; ', ';', '/ ','/');
    					$arrChildOrders = array('. ', '- ', '.', '-');
						$arrUserInfo[0]['mobile'] = str_replace($arrOrders, ', ', $arrUserInfo[0]['mobile']);
			    		$arrUserInfo[0]['mobile'] = str_replace($arrChildOrders, '', $arrUserInfo[0]['mobile']);
			        	$arrUserInfo[0]['mobile'] = explode(', ', $arrUserInfo[0]['mobile']);
						$this->loadview('alert/alert_popup_call_user', array(
																	'arrUserInfo' => $arrUserInfo[0],
																	'strAlertId' => $strAlertId,
																	'strAlertMsg' => $strAlertMsg,
																	'strTimeAlert' => $strTimeAlert
																	), 'layout_popup');
						return;
					} else {
						foreach($arrUserInfo as $index=>$oneUser) {
							$arrHRDept = $this->contact_model->findUserViaEmail(trim($oneUser['email']));
			            	@$arrUserInfo[$index]['vng_dept'] = $arrHRDept['vng_dept']; 
						}	
					}
					
				}
			}
			if(!empty($iDepartmentSelected)) {
				$arrProducts = $this->contact_model->GetProductListByDepartmentIdForContact($iDepartmentSelected);
			}
		} else { //do post
			$iDepartmentSelected = $_POST['department'];
			$iProductIdSelected = $_POST['product'];
			$strAlertId = $_POST['alert_id'];
			// $strZbxHostName = $_POST['zbx_host_name'];
			$strAlertMsg = $_POST['alert_message'];
			$strTimeAlert = $_POST['time_alert'];
			
			//p($_POST);
			if(isset($_POST['btnFilter'])) {
				$arrProducts = $this->contact_model->GetProductListByDepartmentIdForContact($iDepartmentSelected);
				if($iDepartmentSelected != -1) {
					$iDepartmentSelected = intval($iDepartmentSelected);
					$iProductIdSelected = intval($iProductIdSelected);
					if(!empty($iProductIdSelected)) {
						$arrUserInfo = $this->contact_model->getUsersByDepartmentProduct($iProductIdSelected);
						if(!empty($arrUserInfo)) {
							foreach($arrUserInfo as $index=>$oneUser) {
								$arrHRDept = $this->contact_model->findUserViaEmail(trim($oneUser['email']));
				            	@$arrUserInfo[$index]['vng_dept'] = $arrHRDept['vng_dept']; 
							}
						}

					}
				}
			} 
		}
		
		$strContactView = $this->load->view('contact/tbl_contact_content', array('arrUsers' => $arrUserInfo, 'base_url' => $this->getBaseUrl()), true);
		
		$this->loadview('contact/contact_of_alert', array(
												'arrDepartments' => $arrDepartments,
												'iDepartmentSelected' => $iDepartmentSelected,
												'arrProducts' => $arrProducts,
												'iProductIdSelected' => $iProductIdSelected,
												'strContactView' => $strContactView,
												'strAlertId' => $strAlertId,
												'strZbxHostName' => $strZbxHostName,
												'strAlertMsg' => $strAlertMsg,
												'strTimeAlert' => $strTimeAlert
						), 'layout_popup');
    }
	// ------------------------------------------------------------------------------------------ //
	/* private function CalNumOfCallFromAlert($arrAlerts) {
		// calculate number of call fail/success of each alert
		if(!empty($arrAlerts)) {
			foreach($arrAlerts as $idx=>$oAlert) {
				$arrAlerts[$idx]['noof_call_success'] = $arrAlerts[$idx]['noof_call_fail'] = 0;
				$oNoofCallFromAlert = $this->contact_model->getNumofCallFromAlert((string)$oAlert['_id']);
				if(!empty($oNoofCallFromAlert)) {
					foreach ($oNoofCallFromAlert as $oCallFromAlert) {
						if($oCallFromAlert->action_type === CONTACT_ACTION_TYPE_CALL_SUCCESS) {
							$arrAlerts[$idx]['noof_call_success'] = intval($oCallFromAlert->noof_call);
						} elseif($oCallFromAlert->action_type === CONTACT_ACTION_TYPE_CALL_FAIL) {
							$arrAlerts[$idx]['noof_call_fail'] = intval($oCallFromAlert->noof_call);
						}
					}
				}
				
				//if alert opened INC, I sum noof_call_fail/call_success of that INC
				$oCheckAlertOpenedInc = $this->mysql_incident_model->getIncidentIdByAlert((string)$oAlert['_id']);
				if(!empty($oCheckAlertOpenedInc)) {
					if(!empty($oCheckAlertOpenedInc->itsm_incident_id)) {
						$oInc = $this->mysql_incident_model->GetIncidentById($oCheckAlertOpenedInc->itsm_incident_id);
						if(!empty($oInc)) {
							$arrAlerts[$idx]['noof_call_success'] += $oInc['number_of_call_success'];
							$arrAlerts[$idx]['noof_call_fail'] += $oInc['number_of_call_fail']; 
						}
					}
				}
			}
		}
		return $arrAlerts;
	} */
	
	// ------------------------------------------------------------------------------------------ //
	private function CalNumOfCallFromAlert($arrAlerts) {
		$oActionHistoryAlert = array();
		// calculate number of call fail/success of each alert
		if(!empty($arrAlerts)) {
			foreach($arrAlerts as $idx=>$oAlert) {
				$arrAlerts[$idx]['noof_call_success'] = $arrAlerts[$idx]['noof_call_fail'] = 0;
				$oActionHistoryAlert = $this->contact_model->getActionHistoryByAlertId((string)$oAlert['_id']);
				if(!empty($oActionHistoryAlert)) {
					foreach ($oActionHistoryAlert as $j=>$oCallFromAlert) {
						if($oCallFromAlert->action_type === CONTACT_ACTION_TYPE_CALL_SUCCESS) {
							if($oActionHistoryAlert[$j-1]->action_type === CONTACT_ACTION_TYPE_CALL_SUCCESS && $oActionHistoryAlert[$j-1]->ip_action === $oCallFromAlert->ip_action && $oActionHistoryAlert[$j-1]->ref_id === $oCallFromAlert->ref_id) {
									
							} else {
								$arrAlerts[$idx]['noof_call_success'] += 1; 
							}
						} elseif($oCallFromAlert->action_type === CONTACT_ACTION_TYPE_CALL_FAIL) {
							if($oActionHistoryAlert[$j-1]->action_type === CONTACT_ACTION_TYPE_CALL_FAIL && $oActionHistoryAlert[$j-1]->ip_action === $oCallFromAlert->ip_action && $oActionHistoryAlert[$j-1]->ref_id === $oCallFromAlert->ref_id) {
							} else {
								$arrAlerts[$idx]['noof_call_fail'] += 1; 
							}
						}
					}
				}
				//if alert opened INC, I sum noof_call_fail/call_success of that INC
				$oCheckAlertOpenedInc = $this->mysql_incident_model->getIncidentIdByAlert((string)$oAlert['_id']);
				if(!empty($oCheckAlertOpenedInc)) {
					if(!empty($oCheckAlertOpenedInc->itsm_incident_id)) {
						$oInc = $this->mysql_incident_model->GetIncidentById($oCheckAlertOpenedInc->itsm_incident_id);
						if(!empty($oInc)) {
							$arrAlerts[$idx]['noof_call_success'] += $oInc['number_of_call_success'];
							$arrAlerts[$idx]['noof_call_fail'] += $oInc['number_of_call_fail']; 
						}
					}
				}
			}
		}
		return $arrAlerts;
	}
	// ------------------------------------------------------------------------------------------ //
	public function action_history_alert() {
		$strAlertId = $_REQUEST['alertid'];
		$strZbxHostName = $_REQUEST['zbx_host'];
		$strAlertMsg = $_REQUEST['alert_msg'];
		$strTimeAlert = $_REQUEST['time_alert'];
		$strSource = $_REQUEST['source'];
		$oActHistoryTemp = $oActHistoryRecords = array();
		$strStartTimeTemp = null;
		if(!empty($strAlertId)) {
			$strAlertId = trim($strAlertId);
			$oActHistoryTemp = $this->contact_model->getActionHistoryByAlertId($strAlertId);	
		}
		if(!empty($oActHistoryTemp)) {
			foreach($oActHistoryTemp as $idx=>$oRecord) {
				if($oRecord->action_type === NOTIFY_ACTION_TYPE_CALL) {
					$strStartTimeTemp = $oRecord->created_date;
				} else {
					if($oRecord->action_type !== CONTACT_ACTION_TYPE_SMS) {
						if($oActHistoryTemp[$idx-1]->action_type !== CONTACT_ACTION_TYPE_SMS && $oActHistoryTemp[$idx-1]->action_type !== NOTIFY_ACTION_TYPE_CALL) {
							if($oRecord->alert_id === $oActHistoryTemp[$idx-1]->alert_id && $oActHistoryTemp[$idx-1]->ip_action === $oRecord->ip_action) {
								unset($oActHistoryRecords[$idx-1]);
							}
						}
						$oActHistoryTemp[$idx]->connect_start = $strStartTimeTemp;
					} else {
						$oActHistoryTemp[$idx]->connect_start = $oRecord->created_date;
					}
					$oActHistoryRecords[$idx] = $oActHistoryTemp[$idx];
				}
			}
			
		}
		// vd($oActHistoryRecords);
		$this->loadview('contact/action_history_of_alert', array('oHistory' => $oActHistoryRecords
																,'strZbxHost' => $strZbxHostName
																,'strSource' => $strSource
																,'strAlertMsg' => $strAlertMsg
																,'strTimeAlert' => $strTimeAlert
														), 'layout_popup');
	}
	
	// ------------------------------------------------------------------------------------------ //
}

?>
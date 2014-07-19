<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_controller.php';

/**
 *
 * incident.php: This file contains Incident Class, the controller for Incident of Monitor_Assistant
 *
 **/

/**
 * Incident Class
 *
 *
 **/

class Incident extends Base_controller {
/**
 *
 * Constructor: extends from Base controller Class
 *
 */
	public function __construct(){
		parent::__construct();
		$this->load->model('incident/mysql_incident_model', 'model');
		$this->load->model('incident/mongo_incident_model', 'mongo_model');
		$this->load->model('alert/alert_model', 'alert_model');
		$this->load->model('contact/contact_model', 'contact_model');
		
		$this->db4log = &$this->model->db_ma;
	}

/**
 * Index
 *
 * Default page
 *
 */
// 	public function index()
// 	{
// 		$this->changed_list();
// 	}

/**
 * Incident List
 *
 * Show list of incidents
 * NOTES:
 *
 */
	public function inc_list() {
		$strIncListPagingType = 'ajax';
		$arrIncWithoutSubarea = array();
		$arrIncJustClosedBySE = array();
		$nPage2               = 0;
		$nPage3               = 0;
		$nPageSize2           = 0;
		$nPageSize3           = 0;

		$arrCurrentShiftInfo  = $this->model->GetCurrentShiftInfo();

		$arrSelectedShiftInfo = array(
			'shift_date'       => empty($_REQUEST['shift_date']) ? @$arrCurrentShiftInfo[0]['shift_date']:$_REQUEST['shift_date'],
			'shift_id'         => empty($_REQUEST['shift_id']) ? @$arrCurrentShiftInfo[0]['shift_id']:$_REQUEST['shift_id'],
			'arrPagination'    => $this->GetPaginationRequest('limit1', 'page1')
		);
		$arrSelectedShiftInfo['is_current_shift'] = (@$arrCurrentShiftInfo[0]['shift_date']==$arrSelectedShiftInfo['shift_date'] && @$arrCurrentShiftInfo['0']['shift_id']==$arrSelectedShiftInfo['shift_id']);

		$nPage1     = $arrSelectedShiftInfo['arrPagination']['page'];
		$nPageSize1 = $arrSelectedShiftInfo['arrPagination']['limit'];

		if($arrSelectedShiftInfo['is_current_shift']){
			// pd($arrSelectedShiftInfo);
			$arrInc = $this->model->ListOpenIncident(
				$arrSelectedShiftInfo
			);

			$arrSelectedShiftInfo['arrPagination'] = $this->GetPaginationRequest('limit2', 'page2');
			$nPage2     = $arrSelectedShiftInfo['arrPagination']['page'];
			$nPageSize2 = $arrSelectedShiftInfo['arrPagination']['limit'];
			$arrIncWithoutSubarea = $this->model->ListClosedIncidentWithoutSubarea(
				$arrSelectedShiftInfo
			);
			$arrSelectedShiftInfo['arrPagination'] = $this->GetPaginationRequest('limit3', 'page3');
			$nPage3     = $arrSelectedShiftInfo['arrPagination']['page'];
			$nPageSize3 = $arrSelectedShiftInfo['arrPagination']['limit'];
			$arrIncJustClosedBySE = $this->model->ListIncidentJustClosedBySE(
				$arrSelectedShiftInfo
			);
		} else {
			$arrInc = $this->model->ListIncidentByShift(
				$arrSelectedShiftInfo
			);
		}
        
        $arrRs = $this->model->ListIncidentUpdateFailed();
        $arrUpdateFailInc = array();
        if (!empty($arrRs)) {
           foreach ($arrRs as $oIncId) {
                $arrUpdateFailInc[] = $oIncId->itsm_incident_id;
            }  
        }
       
       // vd($arrUnsuccessUpdateInc);

		$strQueryString = $this->ParseIncidentListQueryString();
		$this->loadview('incident/inc_list',
			array(
				'arrInc'               => $arrInc,
                'arrUpdateFailInc'     => $arrUpdateFailInc,
				'arrIncWithoutSubarea' => $arrIncWithoutSubarea,
				'arrIncJustClosedBySE' => $arrIncJustClosedBySE,
				'arrSelectedShiftInfo' => $arrSelectedShiftInfo,
				'arrCurrentShiftInfo'  => $arrCurrentShiftInfo,
				'nPage1'               => $nPage1,
				'nPage2'               => $nPage2,
				'nPage3'               => $nPage3,
				'nPageSize1'           => $nPageSize1,
				'nPageSize2'           => $nPageSize2,
				'nPageSize3'           => $nPageSize3,
				'strIncListPagingType' => $strIncListPagingType,
				'strQueryString'       => (empty($strQueryString)?'':$strQueryString)
			)
		);
	}
    
    // ------------------------------------------------------------------------------------------ //
    public function view_incident_detail($strIncidentId) 
    {
        if ($strIncidentId != '' && !is_null($strIncidentId)) 
        {
            $oIncident     = $this->model->GetIncidentById($strIncidentId);
			$strIssueName = null;
			if(!empty($oIncident['kb_id'])) {
				$strIssueName = $this->model->getKBLinkByKbId(intval($oIncident['kb_id']));
				if(!empty($strIssueName)) {
					$strIssueName = $strIssueName->issue_name;
				}
			}
           # vd($oIncident);
            $this->loadview('incident/incident_detail', array('oIncident' => $oIncident, 'strIssueName' => $strIssueName), 'layout_popup');
        }
    }
	
	// ------------------------------------------------------------------------------------------ //
	public function update_incident_status(){
		$strIncidentId = $this->input->get('incidentid');
		$oIncident     = $this->model->GetIncidentById($strIncidentId);
		
    	$arrArea 					= $this->model->GetAllArea();
		$arrSubarea					= array();
		if (isset($oIncident['area']) && $oIncident['area'] != '' ) {
			$arrSubarea	= $this->model->GetSubareaByArea($oIncident['area']);
		}
		else {
			$arrSubarea = $this->model->GetAllSubarea();
		}
#vd($oIncident);
		if ($this->input->get('layout') === 'popup') {
			$this->loadview('incident/update_status',
				array(
					'oIncident' => $oIncident
                    , 'arrArea' => 	$arrArea
                    , 'arrSubarea' => $arrSubarea
				), 'layout_popup'
			);
		} else {
			$this->loadview('incident/update_status',
				array(
					'oIncident' => $oIncident
                    , 'arrArea' => 	$arrArea
                    , 'arrSubarea' => $arrSubarea
				));
		}
	}
	
	// ------------------------------------------------------------------------------------------ //
	public function update_incident_status_submit() {
		$arrParamKeys = array('itsm_incident_id', 'incident_status'
						, 'outage_end', 'solution', 'closurecode', 'area', 'subarea'
						, 'resolvedby', 'sdk_note');

		$arrParams = $this->GetParameter($arrParamKeys);
#pd($arrParams);
		if (isset($arrParams['solution'])) $arrParams['solution'] = str_replace("\r\n", "\r", $arrParams['solution']);
		if (isset($arrParams['sdk_note'])) $arrParams['sdk_note'] = str_replace("\r\n", "\r", $arrParams['sdk_note']);
		$arrParams['updated_time'] = date('Y-m-d H:i:s');
        $arrParams['updated_by'] = $this->session->userdata('username');
		$arrParams['sdk_update_to_itsm_status'] = 0;
        $arrParams['sdk_update_to_itsm_count'] = 0;
		
		$oIncident     = $this->model->GetIncidentById($arrParams['itsm_incident_id']);
#vd($oIncident); exit;
		
		if (!empty($oIncident)) {
			$oRs = false;
			if ($arrParams['incident_status'] == 'Resolved' ) {
				$oRs = $this->model->UpdateIncident($arrParams);
                 if ($arrParams['outage_end']!="" && $arrParams['outage_end'] != NULL) { 
                    $arrParams['internal_status'] = 'closed'; 
                 }
			}
			elseif ($arrParams['incident_status'] == 'Reopen') {
				$arrParams['reopened_by'] = $this->session->userdata('username');
				$arrParams['reopen_time'] = date('Y-m-d H:i:s');            
#vd($arrParams);
				$oRs = $this->model->UpdateIncident($arrParams);
                $arrParams['internal_status'] = NULL;  
			}
			if ($oRs) {
                
				$this->model->UpdateIncidentFollow($arrParams);
				$this->session->set_flashdata('msg','Success! Incident status has been updated!');
				$this->session->set_flashdata('type_msg', 'success');
			}
		}
		else {
			$this->session->set_flashdata('msg','Error! No incident id found!');
			$this->session->set_flashdata('type_msg', 'error');
		}
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit;
	}
	
	// ------------------------------------------------------------------------------------------ //
	private function ParseIncidentListQueryString(){
		$arrParam = array();
		parse_str($_SERVER['QUERY_STRING'], $arrParam);

		unset($arrParam['page1']);
		unset($arrParam['page2']);
		unset($arrParam['page3']);
		unset($arrParam['limit1']);
		unset($arrParam['limit2']);
		unset($arrParam['limit3']);

		return http_build_query($arrParam);
	}
	// ------------------------------------------------------------------------------------------ //
	public function ajax_list_incident($nPage, $nPageSize){
		$arrCurrentShiftInfo  = $this->model->GetCurrentShiftInfo();

		$arrSelectedShiftInfo = array(
			'shift_date'       => empty($_REQUEST['shift_date']) ? @$arrCurrentShiftInfo[0]['shift_date']:$_REQUEST['shift_date'],
			'shift_id'         => empty($_REQUEST['shift_id']) ? @$arrCurrentShiftInfo[0]['shift_id']:$_REQUEST['shift_id'],
			'arrPagination'    => array('offset' => ($nPage-1)*$nPageSize, 'limit' => $nPageSize)
		);
		$arrSelectedShiftInfo['is_current_shift'] = (@$arrCurrentShiftInfo[0]['shift_date']==$arrSelectedShiftInfo['shift_date'] && @$arrCurrentShiftInfo['0']['shift_id']==$arrSelectedShiftInfo['shift_id']);

		if($arrSelectedShiftInfo['is_current_shift']){
			$arrInc = $this->model->ListOpenIncident(
				$arrSelectedShiftInfo
			);
		} else {
			$arrInc = $this->model->ListIncidentByShift(
				$arrSelectedShiftInfo
			);
		}
        
        $arrRs = $this->model->ListIncidentUpdateFailed();
        $arrUpdateFailInc = array();
        if (!empty($arrRs)) {
           foreach ($arrRs as $oIncId) {
                $arrUpdateFailInc[] = $oIncId->itsm_incident_id;
            }  
        }
        

		$this->loadview('incident/ajax_inc_list',
			array(
				'arrInc' => $arrInc,
                'arrUpdateFailInc' => $arrUpdateFailInc
			)
			, 'layout_ajax'
		);
	}
	// ------------------------------------------------------------------------------------------ //
	public function ajax_list_incident_just_closed_by_se($nPage, $nPageSize){
		$arrIncJustClosedBySE = $this->model->ListIncidentJustClosedBySE(
			array('arrPagination' => array('offset' => ($nPage-1)*$nPageSize, 'limit' => $nPageSize))
		);
		$arrRs = $this->model->ListIncidentUpdateFailed();
        $arrUpdateFailInc = array();
        if (!empty($arrRs)) {
           foreach ($arrRs as $oIncId) {
                $arrUpdateFailInc[] = $oIncId->itsm_incident_id;
            }  
        }
		$this->loadview('incident/ajax_inc_list_incident_just_closed_by_se',
			array(
				'arrIncJustClosedBySE' => $arrIncJustClosedBySE,
				'arrUpdateFailInc' => $arrUpdateFailInc
			)
			, 'layout_ajax'
		);
	}
	// ------------------------------------------------------------------------------------------ //
	public function ajax_list_closed_incident_without_subarea($nPage, $nPageSize){
		$arrIncWithoutSubarea = $this->model->ListClosedIncidentWithoutSubarea(
			array('arrPagination' => array('offset' => ($nPage-1)*$nPageSize, 'limit' => $nPageSize))
		);
		$arrRs = $this->model->ListIncidentUpdateFailed();
        $arrUpdateFailInc = array();
        if (!empty($arrRs)) {
           foreach ($arrRs as $oIncId) {
                $arrUpdateFailInc[] = $oIncId->itsm_incident_id;
            }  
        }
		$this->loadview('incident/ajax_inc_list_closed_incident_without_subarea',
			array(
				'arrIncWithoutSubarea' => $arrIncWithoutSubarea,
				'arrUpdateFailInc' => $arrUpdateFailInc
			)
			, 'layout_ajax'
		);
	}
	// ------------------------------------------------------------------------------------------ //
	public function review(){
		$arrChg = array();
		$arrTsk = array();
		$nPage2 = 1;
		$nPage3 = 1;
		$nPageSize2 = PAGER_SIZE;
		$nPageSize3 = PAGER_SIZE;
		$strIncListPagingType = 'ajax';
		$strQueryString = '';

		$arrCurrentShiftInfo  = $this->model->GetCurrentShiftInfo();
		$arrSelectedShiftInfo = array(
			'shift_date'       => empty($_REQUEST['shift_date']) ? $arrCurrentShiftInfo[0]['shift_date']:$_REQUEST['shift_date'],
			'shift_id'         => empty($_REQUEST['shift_id']) ? $arrCurrentShiftInfo[0]['shift_id']:$_REQUEST['shift_id'],
			'arrPagination'    => $this->GetPaginationRequest('limit1', 'page1')
		);
		$arrSelectedShiftInfo['is_current_shift'] = ($arrCurrentShiftInfo['shift_date']==$arrSelectedShiftInfo['shift_date'] && $arrCurrentShiftInfo['shift_id']==$arrSelectedShiftInfo['shift_id']);

		$arrInc = $this->model->ListReviewIncidentByShift(
			$arrSelectedShiftInfo
		);

		$strQueryString = $this->ParseIncidentListQueryString();
		$this->loadview('incident/review_list',
			array(
				'arrInc'               => $arrInc,
				'arrChg'               => $arrChg,
				'arrTsk'               => $arrTsk,
				'arrSelectedShiftInfo' => $arrSelectedShiftInfo,
				'nPage1'               => $arrSelectedShiftInfo['arrPagination']['page'],
				'nPage2'               => $nPage2,
				'nPage3'               => $nPage3,
				'nPageSize1'           => $arrSelectedShiftInfo['arrPagination']['limit'],
				'nPageSize2'           => $nPageSize2,
				'nPageSize3'           => $nPageSize3,
				'strIncListPagingType' => $strIncListPagingType,
				'strQueryString'       => (empty($strQueryString)?'':$strQueryString)
			)
		);
	}
	// ------------------------------------------------------------------------------------------ //
	public function ajax_list_incident_review($nPage, $nPageSize){
		$arrCurrentShiftInfo  = $this->model->GetCurrentShiftInfo();
		$arrSelectedShiftInfo = array(
			'shift_date'       => empty($_REQUEST['shift_date']) ? $arrCurrentShiftInfo[0]['shift_date']:$_REQUEST['shift_date'],
			'shift_id'         => empty($_REQUEST['shift_id']) ? $arrCurrentShiftInfo[0]['shift_id']:$_REQUEST['shift_id'],
			'arrPagination'    => array('offset' => ($nPage-1)*$nPageSize, 'limit' => $nPageSize)
		);
		$arrSelectedShiftInfo['is_current_shift'] = ($arrCurrentShiftInfo['shift_date']==$arrSelectedShiftInfo['shift_date'] && $arrCurrentShiftInfo['shift_id']==$arrSelectedShiftInfo['shift_id']);
		$arrInc = $this->model->ListReviewIncidentByShift(
			$arrSelectedShiftInfo
		);

		$this->loadview('incident/ajax_list_incident_review',
			array(
				'arrInc' => $arrInc
			)
			, 'layout_ajax'
		);
	}
	// ------------------------------------------------------------------------------------------ //
	public function create_incident()
	{
		$arrArea 					= $this->model->GetAllArea();
		$arrSubarea 				= $this->model->GetAllSubarea();
		$arrDepartment				= $this->model->GetUniqueITSMDepartment();
		$arrBugCategory				= $this->model->GetAllBugCategory();
		$arrBugUnit					= $this->model->GetAllBugUnit();
		$arrService					= $this->model->GetUniqueITSMProduct();
		$arrLocation				= $this->model->GetAllLocation();
		$arrCausedByDept			= $this->model->GetAllCauseExternalDept();
		$arrNewRootCause			= $this->model->GetAllNewRootCause();
		$arrDetector				= $this->model->GetAllDetector();
		$arrProductKB				= $this->get_kb_product_list();
		//vd($arrService);
		$this->loadview('incident/create_new'
			, array(
				'arrArea' 			=> $arrArea,
				'arrSubarea' 		=> $arrSubarea,
				'arrDepartment' 	=> $arrDepartment,
				'arrBugCategory' 	=> $arrBugCategory,
				'arrBugUnit'		=> $arrBugUnit,
				'arrService'		=> $arrService,
				'arrLocation'		=> $arrLocation,
				'arrCausedByDept'	=> $arrCausedByDept,
				'arrNewRootCause'	=> $arrNewRootCause,
				'arrDetector'		=> $arrDetector,
				'arrProductKB'		=> $arrProductKB
		), 'layout_popup');
	}

	// ------------------------------------------------------------------------------------------ //
	public function create_incident_from_alert()
	{
		global $arrDefined;
		$strDepartment      = '';
		$strProduct         = '';
		$iProductId 		= null;
		$strDescription     = '';
		$isIncompleteAttach = FALSE;
		$arrIncompleteAttach = array();
		$arrAttment          = array();
		$arrAttachLink       = array();
		$arrAttachFilename   = array();
		$arrKnowledgeBase 	 = array();
		$strAlertId = $this->input->get('alertid');
		$oAlert     = $this->alert_model->GetAlertById($strAlertId);
#vd($oAlert);
		if (!is_null($oAlert)) {
			if (isset($oAlert['department']) && $oAlert['department'] != '')
			{
				$strDepartment = $oAlert['department'];
			}
			if (isset($oAlert['product']) && $oAlert['product'] != '')
			{
				$strProduct = $oAlert['product'];
				$iProductId = $this->model->getProductIdByProductName($strProduct);
				if(!empty($iProductId)) {
					$iProductId = $iProductId->productid;
					$arrProductIdKB = array();
					$arrProductKBTemp = $this->model->GetListProductKB();
					if(!empty($arrProductKBTemp)) {
						foreach($arrProductKBTemp as $oKB) {
							$arrProductIdKB[] = $oKB->product_id; 
						}
					}
					if(!empty($arrProductIdKB) && in_array($iProductId, $arrProductIdKB)) {
						$arrKnowledgeBase = $this->model->GetKnowledgeBaseByProduct($iProductId);
					}
				}
			}
			if (isset($oAlert['alert_message']) && $oAlert['alert_message'] != '')
			{
				// $strDescription = $oAlert['alert_message'];
				$strDescription = $oAlert['description'];
			}
			if(in_array(strtolower(@$oAlert['source_from']), $arrDefined['source_has_attachment'])){
				$oRawAlert = $this->alert_model->GetRawAlert($oAlert);

				if(!empty($oRawAlert)){
					$oRawAlert['source_from'] = $oAlert['source_from'];
					$arrAttment = $this->alert_model->ListAlertAttachment($oRawAlert);

					foreach($arrAttment as &$oAttachment){
						$oAttachment['link'] = ATTACHMENT_WEB_PATH . $oAttachment['filename_alias'];
						$oAttachment['base64_link']  = base64_encode(ATTACHMENT_WEB_PATH . $oAttachment['filename_alias']);
						$oAttachment['downloaded']   = 1;
						$oAttachment['warning'] = 0;
						if($oAttachment['is_file_saved'] == 0){
							$oAttachment['downloaded']   = 0;
							$oAttachment['warning'] = 1;
							$arrIncompleteAttach[] = $oAttachment;
						}
						$arrAttachLink[] = $oAttachment['link'];
						$arrAttachFilename[] = $oAttachment['filename_alias'];
					}
				}
			}
		}
#pd($arrAttment);
		$arrArea 					= $this->model->GetAllArea();
		$arrSubarea 				= $this->model->GetAllSubarea();
		$arrDepartment				= $this->model->GetUniqueITSMDepartment();
		$arrBugCategory				= $this->model->GetAllBugCategory();
		$arrBugUnit					= $this->model->GetAllBugUnit();
		$arrNewRootCause			= $this->model->GetAllNewRootCause();
		$arrDetector				= $this->model->GetAllDetector();
		$arrCausedByDept			= $this->model->GetAllCauseExternalDept();
		$arrLocation				= $this->model->GetAllLocation();
		$arrService					= $this->model->GetUniqueITSMProduct();
		$arrProductKB 				= $this->get_kb_product_list();
       // vd($oAlert);
		$arrCSAlert					= $this->alert_model->GetCSAlertById($oAlert['source_id']);
		if($oAlert['source_from'] == 'CS')
			$iAlertImpactLevel			= @$arrCSAlert['level'];
		else
			$iAlertImpactLevel			= $oAlert['level'];

#pd($arrNewRootCause);
#pd($arrDetector);
#pd($arrCausedByDept);
		//vd($oAlert);
		//vd($arrService);
		$this->loadview('incident/create_new_from_alert'
			, array(
				'oAlert'			=> $oAlert,
				'strDepartment'		=> $strDepartment,
				'strProduct'		=> $strProduct,
				'strDescription'	=> $strDescription,
				'arrArea' 			=> $arrArea,
				'arrSubarea' 		=> $arrSubarea,
				'arrDepartment' 	=> $arrDepartment,
				'arrBugCategory' 	=> $arrBugCategory,
				'arrBugUnit'		=> $arrBugUnit,
				'arrService'		=> $arrService,
				'arrNewRootCause'	=> $arrNewRootCause,
				'arrDetector'		=> $arrDetector,
				'arrCausedByDept'	=> $arrCausedByDept,
				'arrLocation'		=> $arrLocation,
				'iAlertImpactLevel'	  => $iAlertImpactLevel,
				'isIncompleteAttach'  => $isIncompleteAttach,
				'arrIncompleteAttach' => $arrIncompleteAttach,
				'arrAttachment'       => $arrAttment,
				'arrAttachLink'       => $arrAttachLink,
				'arrAttachFilename'   => $arrAttachFilename,
				'arrProductKB'		  => $arrProductKB,
				'arrKnowledgeBase'    => $arrKnowledgeBase
		), 'layout_popup');
	}

	// ------------------------------------------------------------------------------------------ //
	public function create_incident_submit()
	{
 		$arrParamKeys = array('auto_update_impact_level', 'alert_id', 'src_from', 'src_id', 'customer_case'
						, 'area', 'subarea', 'department'
						, 'product', 'outage_start', 'downtime_start'
						, 'bugcategory', 'assignment_group', 'bugunit'
						, 'assignee', 'impact_level', 'location'
						, 'urgency_level', 'related_id', 'related_id_change'
						, 'ccu_time', 'user_impacted', 'knowledge_base'
						, 'is_cause_by_ext', 'cause_by_ext', 'title', 'description'
						, 'root_cause_category', 'is_downtime', 'detector'
						, 'attachments', 'link' , 'sdk_note', 'critical_asset');

		$arrParams = $this->GetParameter($arrParamKeys);
		// vd($arrParams);
		$arrParams['description'] = str_replace("\r\n", "\r", $arrParams['description']);
		
		$arrParams['is_cause_by_ext'] = isset($arrParams['is_cause_by_ext']) ? 'true' : 'false';
        if ($arrParams['is_downtime']=="")
        {
            $arrParams['is_downtime'] = NULL;
        } 
       # vd($arrParams);
		//$arrParams['is_downtime'] = isset($arrParams['is_downtime']) ? 'true' : 'false';
		$arrParams['status'] = INCIDENT_STATUS_INITIALIZE;
		if(!isset($arrParams['auto_update_impact_level'])) {	$arrParams['auto_update_impact_level'] = 0;  }
		# get current shift
		$oCurrentShift = $this->model->GetCurrentShift();
		$iCurrentShift = $oCurrentShift->current_shift;
		$arrParams['shift_id'] = $iCurrentShift;
		$arrParams['created_by'] = $this->session->userdata('username');
		$arrParams['created_date'] = date('Y-m-d H:i:s');
		$arrParams['product_code'] = $arrParams['product'];
		$arrParams['product_alias'] = $arrParams['product'];
		$arrParams['department_code'] = $arrParams['department'];
		
		$arrParams['knowledge_base'] = isset($arrParams['knowledge_base']) && intval($arrParams['knowledge_base']) !== -1 ? intval($arrParams['knowledge_base']) : 0; 
		// vd($arrParams);

		$this->alert_model->UpdateCSAlert(array('_id' => new MongoId($arrParams['src_id'])), array('auto_update_impact_level' => intval($arrParams['auto_update_impact_level'])));
		$bRes = $this->model->InsertIncident($arrParams);
		if ($bRes) {
			$this->session->set_flashdata('msg','Success! New incident has been created!');
			$this->session->set_flashdata('type_msg', 'success');

			$strUser = $this->session->userdata('username');
			$this->alert_model->ACKAlertINC($arrParams['alert_id'], $strUser);
			$this->alert_model->UpdateMAAlert(array('_id' => new MongoId($arrParams['alert_id'])), array(
				'is_acked' => 1
			));
		}
		else {
			$this->session->set_flashdata('msg','Error! Can\'t create incident due to database error!');
			$this->session->set_flashdata('type_msg', 'error');
		}
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit;
	}
		
		// ------------------------------------------------------------------------------------------ //
		public function incident_detail()
		{
			global $arrDefined;
			$strDepartment      = $strKbLink = '';
			$strProduct         = '';
			$strDescription     = '';
			$isIncompleteAttach = FALSE;
			$arrIncompleteAttach = array();
			$arrAttment          = array();
			$arrAttachLink       = array();
			$arrKnowledgeBase 	 = array();
			$strIncidentId = $this->input->get('incidentid');
			$oIncident     = $this->model->GetIncidentById($strIncidentId);
			$arrProductKB  = $this->get_kb_product_list();
			
	#vd($oIncident);
			
			if (!is_null($oIncident)) {
				$downtime_start = DateTime::createFromFormat("Y-m-d H:i:s", $oIncident['downtime_start']);
				if($downtime_start != null){
					date_add($downtime_start, date_interval_create_from_date_string('7 hours'));
					$oIncident['downtime_start'] = $downtime_start->format('Y-m-d H:i:s');
				}
				if (isset($oIncident['department']) && $oIncident['department'] != '')
				{
					$strDepartment = $oIncident['department'];
				}
				if (isset($oIncident['product']) && $oIncident['product'] != '')
				{
					$strProduct = $oIncident['product'];
					$iProductId = $this->model->getProductIdByProductName($strProduct);
					if(!empty($iProductId)) {
						$iProductId = $iProductId->productid;
						$arrProductIdKB = array();
						$arrProductKBTemp = $this->model->GetListProductKB();
						if(!empty($arrProductKBTemp)) {
							foreach($arrProductKBTemp as $oKB) {
								$arrProductIdKB[] = $oKB->product_id; 
							}
						}
						if(!empty($arrProductIdKB) && in_array($iProductId, $arrProductIdKB)) {
							$arrKnowledgeBase = $this->model->GetKnowledgeBaseByProduct($iProductId);
						}
					}
				}
				if (isset($oIncident['title']) && $oIncident['title'] != '')
				{
					// $strDescription = $oIncident['alert_message'];
					$strDescription = $oIncident['description'];
				}
				if(in_array(strtolower(@$oIncident['source_from']), $arrDefined['source_has_attachment'])){
					$oRawAlert = $this->alert_model->GetRawAlert($oIncident);
	
					if(!empty($oRawAlert)){
						$oRawAlert['source_from'] = $oIncident['source_from'];
						$arrAttment = $this->alert_model->ListAlertAttachment($oRawAlert);
	
						foreach($arrAttment as &$oAttachment){
							$oAttachment['link'] = ATTACHMENT_WEB_PATH . $oAttachment['filename_alias'];
							$oAttachment['base64_link']  = base64_encode(ATTACHMENT_WEB_PATH . $oAttachment['filename_alias']);
							$oAttachment['downloaded']   = 1;
							$oAttachment['warning'] = 0;
							if($oAttachment['is_file_saved'] == 0){
								$oAttachment['downloaded']   = 0;
								$oAttachment['warning'] = 1;
								$arrIncompleteAttach[] = $oAttachment;
							}
							$arrAttachLink[] = $oAttachment['link'];
						}
					}
				}
				if(!empty($oIncident['kb_id'])) { 
					$strKbLink = $this->model->getKBLinkByKbId($oIncident['kb_id']);
					if(!empty($strKbLink)) {
						$strKbLink = $strKbLink->knowledgebase;
					}
				}
			}
	#pd($arrAttment);
			$arrArea 					= $this->model->GetAllArea();
			$arrSubarea					= array();
			if (isset($oIncident['area']) && $oIncident['area'] != '' ) {
				$arrSubarea	= $this->model->GetSubareaByArea($oIncident['area']);
			}
			else {
				$arrSubarea = $this->model->GetAllSubarea();
			}
			#vd ($arrSubarea);
			$arrDepartment				= $this->model->GetUniqueITSMDepartment();
			$arrService = array();
			$arrCriticalAsset = array();
			if (isset($oIncident['department']) && $oIncident['department'] != '') {
				$arrService	= $this->model->GetProductByDepartment($strDepartment);
				$arrCriticalAsset	= $this->model->GetCriticalAssetByDepartment($strDepartment);
			}
			else {
				$arrService			= $this->model->GetUniqueITSMProduct();
			}
			#vd ($arrCriticalAsset );
			
			$arrAffectedCI = array();
			if (isset($oIncident['product']) && $oIncident['product'] != '') {
				$arrAffectedCI	= $this->model->GetAffectedCIByProduct($oIncident['product']);
			} else {
				$arrAffectedCI  = $this->model->GetAffectedCIByProduct();
			}
			#vd($arrAffectedCI); 
			$arrAssignmentGroup	= $this->model->GetAssignmentGroupByProductAndDepartment();
			$arrAssignee = array();
			if (isset($oIncident['assignment']) && $oIncident['assignment'] != '') {
				$arrAssignee    = $this->model->GetAssigneeByAssignmentGroup($oIncident['assignment']);
			}
			
			$arrBugUnit	= array();
			$arrBugCategory				= $this->model->GetAllBugCategory();
			if (isset($oIncident['bug_category']) && $oIncident['bug_category'] != '') {
				$arrBugUnit	= $this->model->GetBugUnitByCategory($oIncident['bug_category']);
			}
			#vd($arrBugUnit);
			#$arrBugUnit					= $this->model->GetAllBugUnit();
			$arrNewRootCause			= $this->model->GetAllNewRootCause();
			$arrDetector				= $this->model->GetAllDetector();
			$arrCausedByDept			= $this->model->GetAllCauseExternalDept();
			$arrLocation				= $this->model->GetAllLocation();
			
		  #vd($oIncident);
			if($oIncident['source_from'] == 'CS'){
				$arrCSAlert	= $this->alert_model->GetCSAlertById($oIncident['source_id']);
				$oIncident['auto_update_impact_level'] = $arrCSAlert['auto_update_impact_level'];
			}
	#pd($arrNewRootCause);
	#pd($arrDetector);
	#pd($arrCausedByDept);
			# vd($oIncident);
			# vd($arrService);
			$arrData = array(
						'oIncident'			=> $oIncident,
						'strDepartment'		=> $strDepartment,
						'strProduct'		=> $strProduct,
						'strDescription'	=> $strDescription,
						'arrArea' 			=> $arrArea,
						'arrSubarea' 		=> $arrSubarea,
						'arrDepartment' 	=> $arrDepartment,
						'arrBugCategory' 	=> $arrBugCategory,
						'arrBugUnit'		=> $arrBugUnit,
						'arrService'		=> $arrService,
						'arrAffectedCI'		=> $arrAffectedCI,
						'arrAssignmentGroup' => $arrAssignmentGroup,
						'arrAssignee'		=> $arrAssignee,
						'arrCriticalAsset'	=> $arrCriticalAsset,
						'arrNewRootCause'	=> $arrNewRootCause,
						'arrDetector'		=> $arrDetector,
						'arrCausedByDept'	=> $arrCausedByDept,
						'arrLocation'		=> $arrLocation,
						'isIncompleteAttach'  => $isIncompleteAttach,
						'arrIncompleteAttach' => $arrIncompleteAttach,
						'arrAttachment'       => $arrAttment,
						'arrAttachLink'       => $arrAttachLink,
						'arrProductKB'		  => $arrProductKB,
						'arrKnowledgeBase'    => $arrKnowledgeBase,
						'strKbLink'			  => $strKbLink 
				);
			if ($this->input->get('layout') === 'popup') {
				$this->loadview('incident/update_incident', $arrData, 'layout_popup');
			} else {
				$this->loadview('incident/update_incident', $arrData);
			}
		}

	// ------------------------------------------------------------------------------------------ //
	public function update_incident_submit()
	{
 		$arrParamKeys = array('sdk_update_to_itsm_status', 'auto_update_impact_level'
						, 'itsm_incident_id','alert_id', 'src_from', 'src_id', 'customer_case'
						, 'area', 'subarea', 'department', 'affected_ci'
						, 'product', 'outage_start',  'outage_end', 'downtime_start'
						, 'bugcategory', 'assignment_group', 'bugunit'
						, 'assignee', 'impact_level', 'location'
						, 'urgency_level', 'related_id', 'related_id_change'
						, 'ccu_time', 'user_impacted', 'knowledge_base'
						, 'is_cause_by_ext', 'cause_by_ext', 'title', 'description'
						, 'root_cause_category', 'is_downtime', 'detector', 'resolvedby'
						, 'attachments' , 'sdk_note', 'critical_asset');
						
		$arrParams = $this->GetParameter($arrParamKeys);
		
		$oIncident = $this->model->GetIncidentById($arrParams['itsm_incident_id']);
		if ($oIncident) {
			#pd($arrParams);
			$arrParams['description'] = str_replace("\r\n", "\r", $arrParams['description']);
	
			$arrParams['is_cause_by_ext'] = isset($arrParams['is_cause_by_ext']) ? 'true' : 'false';
            if ($arrParams['is_downtime']=="")
            {
                $arrParams['is_downtime'] = NULL;
            } 
		#	pd($arrParams);
            
			// $arrParams['status'] = INCIDENT_STATUS_INITIALIZE;
			$arrParams['sdk_update_to_itsm_status'] = 0;
            $arrParams['sdk_update_to_itsm_count'] = 0;
			if(!isset($arrParams['auto_update_impact_level']))
				$arrParams['auto_update_impact_level'] = 0;
	
			# get current shift
			$oCurrentShift = $this->model->GetCurrentShift();
			$iCurrentShift = $oCurrentShift->current_shift;
			$arrParams['shift_id'] = $iCurrentShift;
			$arrParams['updated_by'] = $this->session->userdata('username');
			$arrParams['updated_time'] = date('Y-m-d H:i:s');
			$arrParams['product_code'] = $arrParams['product'];
			$arrParams['product_alias'] = $arrParams['product'];
			$arrParams['department_code'] = $arrParams['department'];
			$arrParams['knowledge_base'] = isset($arrParams['knowledge_base']) && intval($arrParams['knowledge_base']) !== -1 ? intval($arrParams['knowledge_base']) : 0;
			// vd($arrParams);
			// pd($arrParams['auto_update_impact_level']);
			if(@$arrParams['src_from'] == 'CS') {
				$this->alert_model->UpdateCSAlert(array('_id' => new MongoId($arrParams['src_id'])), array('auto_update_impact_level' => intval($arrParams['auto_update_impact_level'])));
			}
			$bRes = $this->model->UpdateIncident($arrParams);
            // vd($bRes);
			if ($bRes) {
				$this->model->UpdateIncidentFollow($arrParams);
				
				$this->session->set_flashdata('msg','Success! Incident has been updated!');
				$this->session->set_flashdata('type_msg', 'success');
	
				$strUser = $this->session->userdata('username');
				$this->alert_model->ACKAlertINC($arrParams['alert_id'], $strUser);
				$this->alert_model->UpdateMAAlert(array('_id' => new MongoId($arrParams['alert_id'])), array(
					'is_acked' => 1
				));
			}
			else {
				$this->session->set_flashdata('msg','Error! Can\'t update incident due to database error!');
				$this->session->set_flashdata('type_msg', 'error');
			}
		}
		else {
			$this->session->set_flashdata('msg','Error! Incident\'s not found!');
			$this->session->set_flashdata('type_msg', 'error');
		}
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit;
	}
	
	

	// ------------------------------------------------------------------------------------------ //
	public function ajax_get_subarea_of_area($strTagName, $strTagId, $strTagClass)
	{
		$strArea = $this->input->get('area');
		if ($strArea != 'all')
		{
			$arrSubarea	= $this->model->GetSubareaByArea($strArea);
		}
		else
		{
			$arrSubarea	= $this->model->GetAllSubarea();
		}
		$strHTML = '';
		$arrData = array(
			'arrSubarea' 		=> $arrSubarea
			, 'strTagName' 		=> $strTagName
			, 'strTagId' 		=> $strTagId
			, 'strTagClass' 	=> $strTagClass
			, 'base_url'		=> $this->config->item('base_url')
		);

		$strHTML = $this->parser->parse('incident/ajax_templates/slt_subarea', $arrData, TRUE);
		echo $strHTML;
		exit;
	}

	// ------------------------------------------------------------------------------------------ //
	public function ajax_get_bug_unit_by_category($strTagName, $strTagId, $strTagClass)
	{
		$strBugCategory = $this->input->get('bug_category');
		$arrBugUnit	= $this->model->GetBugUnitByCategory($strBugCategory);
		if ($strBugCategory == "" || !$strBugCategory ) {
			$arrBugUnit = array();
		}
		#vd ($arrBugUnit);
		$strHTML = '';
		$arrData = array(
			'arrBugUnit' 		=> $arrBugUnit
			, 'strTagName' 		=> $strTagName
			, 'strTagId' 		=> $strTagId
			, 'strTagClass' 	=> $strTagClass
			, 'base_url'		=> $this->config->item('base_url')
		);

		$strHTML = $this->parser->parse('incident/ajax_templates/slt_bugunit', $arrData, TRUE);
		echo $strHTML;
		exit;
	}

	// ------------------------------------------------------------------------------------------ //
	public function ajax_get_assignee_of_assignment_group($strTagName, $strTagId, $strTagClass)
	{
		$strProductId       = $this->input->get('product');
		$strAssignmentGroup = $this->input->get('assignment_grp');
		$strImpactLevel     = $this->input->get('impact_level');

		$arrAssignee    = $this->model->GetAssigneeByAssignmentGroup($strAssignmentGroup);
		#vd($arrAssignmentGroup);
		$strHTML = '';
		$arrData = array(
			  'arrAssignee' 	=> $arrAssignee
			, 'strTagName' 		=> $strTagName
			, 'strTagId' 		=> $strTagId
			, 'strTagClass' 	=> $strTagClass
			, 'base_url'		=> $this->config->item('base_url')
		);

		$strHTML = $this->parser->parse('incident/ajax_templates/slt_assignee', $arrData, TRUE);
		echo $strHTML;
		exit;
	}
	
	// ------------------------------------------------------------------------------------------ //
	public function ajax_get_affected_ci_of_product($strTagName, $strTagId, $strTagClass) {
		$strProduct    = trim($this->input->get('product'));
		if ($strProduct != "" && $strProduct != false) {
			$arrAffectedCI = $this->model->GetAffectedCIByProduct($strProduct);
		} else {
			$arrAffectedCI = $this->model->GetAffectedCIByProduct();
		}
		
		$strHTML = '';
		$arrData = array(
			  'arrAffectedCI' 	=> $arrAffectedCI
			, 'strTagName' 		=> $strTagName
			, 'strTagId' 		=> $strTagId
			, 'strTagClass' 	=> $strTagClass
			, 'base_url'		=> $this->config->item('base_url')
		);
		
		$strHTML = $this->parser->parse('incident/ajax_templates/slt_affected_ci', $arrData, TRUE);;
		echo $strHTML;
		exit;
	}

	// ------------------------------------------------------------------------------------------ //
	public function ajax_get_assignment_group_of_product_and_department($strTagName, $strTagId, $strTagClass)
	{
		error_reporting(0);
		$strDepartment = trim($this->input->get('department'));
		$strProduct    = trim($this->input->get('product'));
		$arrAllAssignmentGroup = array();
		$oSelectedAssignmentGroupL1 = null;

		if(!empty($strDepartment) && !empty($strProduct)){
			$arrAllAssignmentGroup      = $this->model->GetAssignmentGroupByProductAndDepartment();
			$arrSelectedAssignmentGroup	= $this->model->GetAssignmentGroupByProductAndDepartment($strDepartment, $strProduct);
			if ( count($arrSelectedAssignmentGroup) > 0) {
				foreach($arrSelectedAssignmentGroup as $oSelectedAssignmentGroup){
					// if(IEndWith($oSelectedAssignmentGroup->name, DEFAULT_ASSIGMENT_GROUP_LEVEL)){
						$oSelectedAssignmentGroupL1 = $oSelectedAssignmentGroup;
						break;
					// }
				} 
				// $oSelectedAssignmentGroupL1 = $arrSelectedAssignmentGroup[0];
			} 
		}
		// vd ($oSelectedAssignmentGroupL1);
		#vd($arrAllAssignmentGroup);
		$strHTML = '';
		$arrData = array(
			'arrAllAssignmentGroup'        => $arrAllAssignmentGroup
			, 'oSelectedAssignmentGroupL1' => $oSelectedAssignmentGroupL1
			, 'strTagName'                 => $strTagName
			, 'strTagId'                   => $strTagId
			, 'strTagClass'                => $strTagClass
			, 'base_url'                   => $this->config->item('base_url')
		);

		$strHTML = $this->parser->parse('incident/ajax_templates/slt_assignment_group', $arrData, TRUE);
		echo $strHTML;
		exit;
	}

	// ------------------------------------------------------------------------------------------ //
	public function ajax_get_product_of_department($strTagName, $strTagId, $strTagClass)
	{
		$arrProduct = array();
		$strDepartment = $this->input->get('department');
		if ($strDepartment == 'all')
		{
			$arrProduct	= $this->model->GetUniqueITSMProduct();
		}
		else
		{
			$arrProduct	= $this->model->GetProductByDepartment($strDepartment);
		}
		$strHTML = '';
		$arrData = array(
			'arrProduct' 		=> $arrProduct
			, 'strTagName' 		=> $strTagName
			, 'strTagId' 		=> $strTagId
			, 'strTagClass' 	=> $strTagClass
			, 'base_url'		=> $this->config->item('base_url')
		);

		$strHTML = $this->parser->parse('incident/ajax_templates/slt_product', $arrData, TRUE);
		echo $strHTML;
		exit;
	}

	// ------------------------------------------------------------------------------------------ //
	public function ajax_get_critical_asset_of_department($strTagName, $strTagId, $strTagClass, $strIdentifier = null)
	{
		global $arrDefined;
		$arrCriticalAsset = array();
		$iRequireCriticalAsset = 0;
		$strDepartment = $this->input->get('department');
		if ($strDepartment == 'all')
		{
			$arrCriticalAsset	= array();
		}
		else
		{
			$arrCriticalAsset	= $this->model->GetCriticalAssetByDepartment($strDepartment);
			if(!empty($strIdentifier) && in_array(strtolower($strDepartment), $arrDefined['critical_asset_require'])) {
				$iRequireCriticalAsset = 1;
			}
		}
		$strHTML = '';
		$arrData = array(
			'arrCriticalAsset' 	=> $arrCriticalAsset
			, 'strTagName' 		=> $strTagName
			, 'strTagId' 		=> $strTagId
			, 'strTagClass' 	=> $strTagClass
			, 'iRequireCA'		=> $iRequireCriticalAsset
			, 'base_url'		=> $this->config->item('base_url')
		);

		$strHTML = $this->parser->parse('incident/ajax_templates/slt_critical_asset', $arrData, TRUE);
		echo $strHTML;
		exit;
	}

	// ------------------------------------------------------------------------------------------ //
	public function ajax_get_department_of_product($strTagName, $strTagId, $strTagClass)
	{
		$strProduct = $this->input->get('product');
		$strSelectedDepartment = "";
		if(!empty($strProduct)){
			$oSelectedDepartment	= $this->model->GetDepartmentOfProduct($strProduct);
			if(!empty($oSelectedDepartment)){
				$strSelectedDepartment = $oSelectedDepartment->department_name;
			}
		}

		$arrDepartment	= $this->model->GetUniqueITSMDepartment();

		$strHTML = '';
		$arrData = array(
			'arrDepartment' 			=> $arrDepartment
			, 'strSelectedDepartment'	=> $strSelectedDepartment
			, 'strTagName' 				=> $strTagName
			, 'strTagId' 				=> $strTagId
			, 'strTagClass' 			=> $strTagClass
			, 'base_url'				=> $this->config->item('base_url')
		);

		//vd($oActiveDepartment);

		$strHTML = $this->parser->parse('incident/ajax_templates/slt_department', $arrData, TRUE);
		echo $strHTML;
		exit;
	}
	// ------------------------------------------------------------------------------------------ //
	public function transfer_shift(){
		$strUserIdTransfer = '499';
		$arrCurrentShiftInfo = $this->model->GetCurrentShiftInfo();
		$arrNextShiftInfo = $this->model->GetNextShiftInfo();
		$Result = false;
		foreach($arrCurrentShiftInfo as $oCurrShift)
		{
			if($oCurrShift->user_id == intval($strUserIdTransfer))
				$Result = true;
		}
		if($Result)
			$Result = $this->model->UpdateStatusShiftTransferInfo($arrCurrentShiftInfo[0]->shift_date, $arrCurrentShiftInfo[0]->shift_id, STATUS_ACCEPTED, STATUS_TRANSFER);
		if($Result){
			$arrShiftInfo = array('from_shift_date' => $arrNextShiftInfo[0]->current_shift_date,
									'from_shift_id' => $arrNextShiftInfo[0]->current_shift_id,
									'from_user_id' => $strUserIdTransfer,
									'to_shift_date' => $arrNextShiftInfo[0]->next_shift_date,
									'to_shift_id' => $arrNextShiftInfo[0]->next_shift_id,
									'status' => STATUS_INIT);
			$Result = $this->model->InsertShiftTransferInfo($arrShiftInfo);
		}

		if ($Result) {
			$this->session->set_flashdata('msg','Success! Transfer shift has been successful!');
			$this->session->set_flashdata('type_msg', 'success');
		}
		else {
			$this->session->set_flashdata('msg','Error! Can\'t transfer shift due to database error!');
			$this->session->set_flashdata('type_msg', 'error');
		}
		exit;

	}
	// ------------------------------------------------------------------------------------------ //
	public function accept_shift(){
		$strUserIdAccept = 335;
		$arrNextShiftInfo = $this->model->GetNextShiftInfo();
		$Result = false;
		foreach($arrNextShiftInfo as $oNextShift)
		{
			if($oNextShift->user_id == intval($strUserIdAccept))
				$Result = true;
		}

		//=======================================================Shift Info====================================================================//
		if($Result)
			$Result = $this->model->UpdateStatusShiftTransferInfo($arrNextShiftInfo[0]->next_shift_date, $arrNextShiftInfo[0]->next_shift_id, STATUS_INIT, STATUS_ACCEPTED);
		if($Result)
			$Result = $this->model->UpdateIsInShiftScheduleAssign($arrNextShiftInfo[0]->next_shift_date, $arrNextShiftInfo[0]->next_shift_id);
		//=======================================================Incident====================================================================//
		if($Result)
			$Result = $this->model->UpdateFollowShiftIdIncidentFollow($arrNextShiftInfo[0]->next_shift_id);
		$arrIncident = $this->model->GetListOpenIncident();

		if($Result)
			foreach($arrIncident AS $oInc){
				$Result = $this->model->InsertIncidentHistory($oInc->id, $arrNextShiftInfo[0]->next_shift_id, $arrNextShiftInfo[0]->next_shift_date);
			}

		//=======================================================Change====================================================================//
		if($Result)
			$Result = $this->model->UpdateFollowShiftIdtblChangeFollow($arrNextShiftInfo[0]->next_shift_id);
		$arrChange = $this->model->GetListOpenChangeFollow();
		if($Result)
			foreach($arrChange AS $oInc){
				$Result = $this->model->InsertChangeHistory($oInc->id, $arrNextShiftInfo[0]->next_shift_id, $arrNextShiftInfo[0]->next_shift_date);
			}
		vd($Result);
		if ($Result) {
			$this->session->set_flashdata('msg','Success! Transfer shift has been successful!');
			$this->session->set_flashdata('type_msg', 'success');
		}
		else {
			$this->session->set_flashdata('msg','Error! Can\'t transfer shift due to database error!');
			$this->session->set_flashdata('type_msg', 'error');
		}
		exit;
	}
	// ------------------------------------------------------------------------------------------ //
	public function reject_shift(){
		$strUserIdAccept = $this->session->userdata('userId');
		$arrNextShiftInfo = $this->model->GetNextShiftInfo();
			$Result = $this->model->UpdateStatusShiftTransferInfo($arrNextShiftInfo[0]->next_shift_date, $arrNextShiftInfo[0]->next_shift_id, STATUS_INIT, STATUS_REJECTED);
		if($Result)
			$Result = $this->model->UpdateStatusShiftTransferInfo($arrNextShiftInfo[0]->current_shift_date, $arrNextShiftInfo[0]->current_shift_id, STATUS_TRANSFER, STATUS_TRANSFER_REJECTED);
		if ($Result) {
			$this->session->set_flashdata('msg','Success! Transfer shift has been successful!');
			$this->session->set_flashdata('type_msg', 'success');
		}
		else {
			$this->session->set_flashdata('msg','Error! Can\'t transfer shift due to database error!');
			$this->session->set_flashdata('type_msg', 'error');
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function ajax_check_url_exists() {
		$strLink = @$_REQUEST['url'];
		if(!empty($strLink)){
			$strLink = base64_decode($strLink);
			$oHandle = curl_init($strLink);
			curl_setopt($oHandle,  CURLOPT_RETURNTRANSFER, TRUE);
			$oResponse = curl_exec($oHandle);
			$nHttpCode = curl_getinfo($oHandle, CURLINFO_HTTP_CODE);
			curl_close($oHandle);

			if($nHttpCode == 200) {
			    exit("1");
			}
		}
		exit("0");
	}
	// ------------------------------------------------------------------------------------------ //
	public function create_new_incident_submit()
	{
 		$arrParamKeys = array('auto_update_impact_level', 'customer_case'
						, 'area', 'subarea', 'department'
						, 'product', 'outage_start', 'downtime_start'
						, 'bugcategory', 'assignment_group', 'bugunit'
						, 'assignee', 'impact_level', 'location'
						, 'urgency_level', 'related_id', 'related_id_change'
						, 'ccu_time', 'user_impacted', 'knowledge_base'
						, 'is_cause_by_ext', 'cause_by_ext', 'title', 'description'
						, 'root_cause_category', 'is_downtime', 'detector'
						, 'attachments' , 'sdk_note', 'critical_asset');

		$arrParams = $this->GetParameter($arrParamKeys);
		// pd($arrParams);
		$arrParams['description'] = str_replace("\r\n", "\r", $arrParams['description']);
		$arrParams['is_cause_by_ext'] = isset($arrParams['is_cause_by_ext']) ? 'true' : 'false';
        if ($arrParams['is_downtime']=="")
        {
            $arrParams['is_downtime'] = NULL;
        } 
        //$arrParams['is_downtime'] = isset($arrParams['is_downtime']) ? 'true' : 'false';
		$arrParams['status'] = INCIDENT_STATUS_INITIALIZE;
		if(!isset($arrParams['auto_update_impact_level']))
			$arrParams['auto_update_impact_level'] = 0;
		# get current shift
		$oCurrentShift = $this->model->GetCurrentShiftInfo();
		$iCurrentShift = @$oCurrentShift->current_shift;
		$arrParams['shift_id'] = $iCurrentShift;
		$arrParams['created_by'] = $this->session->userdata('username');
		$arrParams['created_date'] = date('Y-m-d H:i:s');
		$arrParams['product_code'] = $arrParams['product'];
		$arrParams['product_alias'] = $arrParams['product'];
		$arrParams['department_code'] = $arrParams['department'];
		$arrParams['knowledge_base'] = isset($arrParams['knowledge_base']) && intval($arrParams['knowledge_base']) !== -1 ? intval($arrParams['knowledge_base']) : 0;
		// vd($arrParams);
		$bRes = $this->model->InsertIncident($arrParams);
		if ($bRes) {
			$this->session->set_flashdata('msg','Success! New incident has been created!');
			$this->session->set_flashdata('type_msg', 'success');
		}
		else {
			$this->session->set_flashdata('msg','Error! Can\'t create incident due to database error!');
			$this->session->set_flashdata('type_msg', 'error');
		}
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit;
	}
	// ------------------------------------------------------------------------------------------ //
	public function create_series() {
		$arrDepartment				= $this->model->GetUniqueITSMDepartment();
		$arrArea 					= $this->model->GetAllArea();
		$arrSubarea 				= $this->model->GetAllSubarea();
		$arrBugCategory				= $this->model->GetAllBugCategory();
		$arrBugUnit					= $this->model->GetAllBugUnit();
		// $arrService					= $this->model->GetUniqueITSMProduct();
		// $arrLocation				= $this->model->GetAllLocation();
		$arrCausedByDept			= $this->model->GetAllCauseExternalDept();
		// $arrNewRootCause			= $this->model->GetAllNewRootCause();
		$arrDetector				= $this->model->GetAllDetector();
		$this->loadview('incident/create_series', array(
						'arrDepartment' 	=> $arrDepartment,
						'arrArea'			=> $arrArea,
						'arrSubarea'		=> $arrSubarea,
						'arrBugCategory'	=> $arrBugCategory,
						'arrBugUnit'		=> $arrBugUnit,
						/* 'arrService'		=> $arrService,
						'arrLocation'		=> $arrLocation, */
						'arrCausedByDept'	=> $arrCausedByDept,
						/* 'arrNewRootCause'	=> $arrNewRootCause, */
						'arrDetector'		=> $arrDetector
		), 'layout_popup');
	}
	
	// ------------------------------------------------------------------------------------------ //
	public function ajax_get_product_list_of_department()
	{
		$arrProduct = array();
		$strDepartment = $this->input->get('department');
		$strDepartment = trim($strDepartment);
		if ($strDepartment != '')
			$arrProduct	= $this->model->GetProductByDepartment($strDepartment);
		// pd($arrProduct);
		$strHTML = '';
		$arrData = array(
			'arrProduct' 		=> $arrProduct
			, 'base_url'		=> $this->getBaseUrl()
		);

		$strHTML = $this->parser->parse('incident/ajax_templates/div_products_of_department', $arrData, TRUE);
		echo $strHTML;
		exit;
	}
	// ------------------------------------------------------------------------------------------ //
	public function ajax_get_assignees_of_product($strTagName, $strTagId, $strTagClass)
	{
		error_reporting(0);
		$strDepartment = trim($this->input->get('department'));
		$strProduct    = trim($this->input->get('product'));
		$arrAllAssignees = array();
		$oSelectedAssignmentGroupL1 = null;
		$arrAssigneesL1 = array();
		$oAssigneeL1 = null;

		if(!empty($strDepartment) && !empty($strProduct)){
			$arrAllAssignees = $this->model->GetAllAssignees();
			$arrSelectedAssignmentGroup	= $this->model->GetAssignmentGroupByProductAndDepartment($strDepartment, $strProduct);
			if ( count($arrSelectedAssignmentGroup) > 0) {
				foreach($arrSelectedAssignmentGroup as $oSelectedAssignmentGroup){
					// if(IEndWith($oSelectedAssignmentGroup->name, DEFAULT_ASSIGMENT_GROUP_LEVEL)) {
						$oSelectedAssignmentGroupL1 = $oSelectedAssignmentGroup;
						break;
					// }
				} 
			}
		}
		if(!empty($oSelectedAssignmentGroupL1)) {
			$arrAssigneesL1 = $this->model->GetAssigneeByAssignmentGroup(strval($oSelectedAssignmentGroupL1->name));
			if(count($arrAssigneesL1) > 0) {
				$oAssigneeL1 = $arrAssigneesL1[0];
			}
		}
		// pd($oAssigneeL1);
		$strHTML = '';
		$arrData = array(
			'arrAllAssignees'        	   => $arrAllAssignees
			, 'arrAssigneesL1'			   => $arrAssigneesL1
			, 'oAssigneeL1' 			   => $oAssigneeL1
			, 'strTagName'                 => $strTagName
			, 'strTagId'                   => $strTagId
			, 'strTagClass'                => $strTagClass
			, 'base_url'                   => $this->getBaseUrl()
		);

		$strHTML = $this->parser->parse('incident/ajax_templates/slt_assignees_of_product', $arrData, TRUE);
		echo $strHTML;
		exit;
	}

	// ------------------------------------------------------------------------------------------ //
	public function create_series_submit() {
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$arrParams = array();
			$iProductId = $strDepartment = '';
			if(isset($_POST['cboProduct']) && isset($_POST['drpdepartment'])) {
				$iProductId = intval($_POST['cboProduct']);
				$strDepartment = trim($_POST['drpdepartment']);
			}
			$oProduct = $this->model->getProductByDepartmentAndProductId($iProductId, $strDepartment);
			if(!empty($oProduct)) {		      	
		      	$arrParams['assignment_group'] = isset($_POST['drpassignmentgroup']) ? $_POST['drpassignmentgroup'] : NULL;
				$arrParams['assignee'] = isset($_POST['drpassignee']) ? $_POST['drpassignee'] : NULL;
				$arrParams['critical_asset'] = isset($_POST['drpcriticalasset']) ? $_POST['drpcriticalasset'] : NULL;
				$arrParams['product_type'] = (isset($_POST['drpproducttype']) && !empty($_POST['drpproducttype'])) ? $_POST['drpproducttype'] : NULL;
				$arrParams['bugcategory'] = (isset($_POST['drpbugcategory']) && !empty($_POST['drpbugcategory'])) ? $_POST['drpbugcategory'] : NULL;
				$arrParams['bugunit'] = (isset($_POST['drpbugunit']) && !empty($_POST['drpbugunit'])) ? $_POST['drpbugunit'] : NULL; 
				$arrParams['urgency_level'] = isset($_POST['drpurgencylevel']) ? $_POST['drpurgencylevel'] : NULL;
				$arrParams['impact_level'] = isset($_POST['drpimpactlevel']) ? $_POST['drpimpactlevel'] : NULL;
				$arrParams['area'] = (isset($_POST['drparea']) && !empty($_POST['drparea'])) ? $_POST['drparea'] : NULL; 
				$arrParams['subarea'] = (isset($_POST['drpsubarea']) && !empty($_POST['drpsubarea'])) ? $_POST['drpsubarea'] : NULL; 
				$arrParams['detector'] = (isset($_POST['drpsdkdetector']) && !empty($_POST['drpsdkdetector'])) ? $_POST['drpsdkdetector'] : NULL; 
				$arrParams['sdk_note'] = (isset($_POST['txtsdknote']) && !empty($_POST['txtsdknote'])) ? $_POST['txtsdknote'] : NULL;
				$arrParams['outage_start'] = isset($_POST['outagedate']) ? $_POST['outagedate'] : NULL;
				$arrParams['ccu_time'] = (isset($_POST['txtCCUTime']) && !empty($_POST['txtCCUTime'])) ? $_POST['txtCCUTime'] : NULL;
				$arrParams['customer_case'] = (isset($_POST['txtCustomerImpacted'])) ? $_POST['txtCustomerImpacted'] : NULL;
				$arrParams['user_impacted'] = (isset($_POST['txtUserImpacted']) && !empty($_POST['txtUserImpacted'])) ? $_POST['txtUserImpacted'] : NULL;
				
		      	$arrParams['title'] = isset($_POST['txttitle']) ? $_POST['txttitle'] : NULL;				
				$arrParams['description'] = isset($_POST['txtdescription']) ? str_replace("\r\n", "\r", $_POST['txtdescription']) : NULL;
				
				$strChkCauseByExt = $this->input->get_post('chkCauseByExt', TRUE);			
				$arrParams['is_cause_by_ext'] = ($strChkCauseByExt !== false && $strChkCauseByExt !== "") ? 'true' : 'false'; 
				$arrParams['cause_by_ext'] = ($arrParams['is_cause_by_ext']!=='false') ? $_POST['drpcausebyexternal'] : NULL; 
				
				$arrParams['is_downtime'] = (isset($_POST['downtimestart']) && !empty($_POST['downtimestart'])) ? 'true' : 'false';
				$arrParams['downtime_start'] = ($arrParams['is_downtime'] !== 'false') ? $_POST['downtimestart'] : NULL; 
				
				$arrParams['status'] = INCIDENT_STATUS_INITIALIZE;
				if(!isset($_POST['auto_update_impact_level']))
					$arrParams['auto_update_impact_level'] = 0;
				# get current shift
				$oCurrentShift = $this->model->GetCurrentShiftInfo();
				$iCurrentShift = @$oCurrentShift->current_shift;
				// $iCurrentShift = 0;
				$arrParams['shift_id'] = $iCurrentShift;
				$arrParams['created_by'] = $this->session->userdata('username');
				$arrParams['created_date'] = date('Y-m-d H:i:s');
				$arrParams['product_code'] = $oProduct->name;
				$arrParams['product_alias'] = $oProduct->name;
				$arrParams['department_code'] = $strDepartment;
		
				// vd($arrParams);
				$bRes = $this->model->InsertIncident($arrParams);
				if($bRes) {
					echo MESSAGE_TYPE_SUCCESS;
				} else {
					echo MESSAGE_TYPE_ERROR;
				}
				exit();
			}
		}
	}
	// ------------------------------------------------------------------------------------------ //
	public function contact_of_incident() {
		$arrUsers = array(); //contact result found
		$arrProducts = array();
		$arrProductId = array();
		$iDepartmentSelected = null;
		$iProductIdSelected = null;
		$arrDepartments = $this->contact_model->GetDepartmentListForContact();
		$strIncidentId = null;
		
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$strProduct = $this->input->get('product');
			$strIncidentId = $this->input->get('incident_id');
			$strProduct = strtolower($strProduct);
			$arrProductTemp = $this->contact_model->getProductFromITSMProduct($strProduct);
			// pd($arrProductTemp);
			if(!empty($arrProductTemp)) {
				/* foreach($arrProductTemp as $i=>$oProduct) { //version get all product found
					$arrProductId[] = intval($oProduct->productid);
					$iDepartmentSelected = intval($oProduct->department_id); //? what does product exist which belong to these different dept?? 
				} */
				$arrProductId[] = intval($arrProductTemp[0]->productid);
				$iProductIdSelected = $arrProductId[0];
				$iDepartmentSelected = intval($arrProductTemp[0]->department_id);
			}
			// pd($arrProductId);
			if(!empty($arrProductId)) {
				$arrUsers = $this->contact_model->getContactByProduct($arrProductId);
				if(!empty($arrUsers)) {
					foreach($arrUsers as $index=>$oneUser) {
						$arrHRDept = $this->contact_model->findUserViaEmail(trim($oneUser['email']));
		            	@$arrUsers[$index]['vng_dept'] = $arrHRDept['vng_dept']; 
					}
				}
			} 
			if(!empty($iDepartmentSelected)) {
				$arrProducts = $this->contact_model->GetProductListByDepartmentIdForContact($iDepartmentSelected);
			}
			
		} else { //do POST
			$iDepartmentSelected = $_POST['department'];
			$iProductIdSelected = $_POST['product'];
			$strIncidentId = $_POST['incident_id'];
			//p($_POST);
			if(isset($_POST['btnFilter'])) {
				$arrProducts = $this->contact_model->GetProductListByDepartmentIdForContact($iDepartmentSelected);
				if($iDepartmentSelected != -1) {
					$iDepartmentSelected = intval($iDepartmentSelected);
					$iProductIdSelected = intval($iProductIdSelected);
					if(!empty($iProductIdSelected)) {
						$arrUsers = $this->contact_model->getUsersByDepartmentProduct($iProductIdSelected);
						if(!empty($arrUsers)) {
							foreach($arrUsers as $index=>$oneUser) {
								$arrHRDept = $this->contact_model->findUserViaEmail(trim($oneUser['email']));
				            	@$arrUsers[$index]['vng_dept'] = $arrHRDept['vng_dept']; 
							}
						}

					}
				}
			} 
		}
		// vd($arrUsers);
		// pd($arrProductId[0]);
		$strContactView = $this->load->view('contact/tbl_contact_content', array('arrUsers' => $arrUsers, 'base_url' => $this->getBaseUrl()), true);
		
		$this->loadview('contact/contact_of_inc', array(
												'arrDepartments' => $arrDepartments,
												'iDepartmentSelected' => $iDepartmentSelected,
												'arrProducts' => $arrProducts,
												'iProductIdSelected' => $iProductIdSelected,
												'strContactView' => $strContactView,
												'strIncidentId' => $strIncidentId
						), 'layout_popup');
	}
	// ------------------------------------------------------------------------------------------ //
	public function contact_action_history() {
		$strIncidentId = $this->input->get('incident_id');
		$oActHistoryTemp = $oActHistoryRecords = array();
		$strStartTimeTemp = null;
		if(!empty($strIncidentId)) {
			$strIncidentId = trim($strIncidentId);
			$oActHistoryTemp = $this->model->getActionHistoryByIncidentId($strIncidentId);	
		}
		// p($oActHistoryTemp);
		if(!empty($oActHistoryTemp)) {
			foreach($oActHistoryTemp as $idx=>$oRecord) {
				if($oRecord->action_type === NOTIFY_ACTION_TYPE_CALL) {
					$strStartTimeTemp = $oRecord->created_date;
					// unset($oActHistoryRecords[$idx]);
				} else {
					if($oRecord->action_type !== CONTACT_ACTION_TYPE_SMS) {
						if($oActHistoryTemp[$idx-1]->action_type !== CONTACT_ACTION_TYPE_SMS && $oActHistoryTemp[$idx-1]->action_type !== NOTIFY_ACTION_TYPE_CALL) {
							if($oRecord->incident_id === $oActHistoryTemp[$idx-1]->incident_id && $oActHistoryTemp[$idx-1]->ip_action === $oRecord->ip_action) {
								unset($oActHistoryRecords[$idx-1]);
							}
						}
						$oActHistoryTemp[$idx]->connect_start = $strStartTimeTemp;
					} else {
						$oActHistoryTemp[$idx]->connect_start = '';
					}
					$oActHistoryRecords[$idx] = $oActHistoryTemp[$idx];
				}
			}
			
		}
		// vd($oActHistoryRecords);
		$this->loadview('contact/contact_history_of_incident', array('oHistory' => $oActHistoryRecords), 'layout_popup');
	}
	
	// ------------------------------------------------------------------------------------------ //
	public function close_alert_incident_closed_by_se() {
		$strIncidentId = null;
		$oResult = null;
		if(isset($_REQUEST['incident_id'])) {
			$strIncidentId = trim($_REQUEST['incident_id']);
			$oResult = $this->model->StopAlertIncidentClosedBySE($strIncidentId);
			// vd($oResult);
			if($oResult) {
				echo 'true';
			} else {
				echo 'false';
			}
		} else {
			echo 'false';
		}
		exit();
	
	}
	// ------------------------------------------------------------------------------------------ //
	public function ajax_get_kb_product($strTagId, $strTagName, $strTagClass) {
		$strProduct = isset($_REQUEST['product']) ? trim($_REQUEST['product']) : null;
		$arrKnowledgeBase = array();
		if(!empty($strProduct)) {
			$iProductId = $this->model->getProductIdByProductName($strProduct);
			if(!empty($iProductId)) {
				$iProductId = $iProductId->productid;
				$arrProductKB = array();
				$arrProductKBTemp = $this->model->GetListProductKB();
				// pd($arrProductKB);
				if(!empty($arrProductKBTemp)) {
					foreach($arrProductKBTemp as $oKB) {
						$arrProductKB[] = $oKB->product_id; 
					}
				}
				if(!empty($arrProductKB) && in_array($iProductId, $arrProductKB)) {
					$arrKnowledgeBase = $this->model->GetKnowledgeBaseByProduct($iProductId);
				}
			}
		}
		//pd($arrKnowledgeBase);
		$this->loadview('incident/ajax_templates/slt_knowledge_base', array('arrKnowledgeBase' => $arrKnowledgeBase,
																			'strTagId' => $strTagId,
																			'strTagName' => $strTagName,
																			'strTagClass' => $strTagClass
																	), 'layout_ajax');
	}
	// ------------------------------------------------------------------------------------------ //
	private function get_kb_product_list() {
		$arrProductKBTemp = $this->model->GetListProductKB();
		$arrProductKB = array();
		if(!empty($arrProductKBTemp)) {
			foreach($arrProductKBTemp as $oKB) {
				$arrProductKB[] = $oKB->product_name;
			}
		}
		return $arrProductKB;
	}
}

?>
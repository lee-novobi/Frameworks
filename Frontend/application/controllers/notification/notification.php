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

class Notification extends Base_controller {
/**
 *
 * Constructor: extends from Base controller Class
 *
 */
	public function __construct(){
		parent::__construct();
		$this->load->model('mysql_base_model', 'model');
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
		header('Location: '. $base_url.'notification/notification/NotificationList');
		exit;
	}


/**
 * GetNotiIncident
 *
 * Show list of opening incident
 *
 */
	public function NotificationList()
	{
		$arrIncidentNoti = $this->model->GetOpenNotiIncident();
		$strIncidentNoti = $this->load->view('notification/ajax_incident_noti',array(
		                                            'arrIncidentNoti' 	=> $arrIncidentNoti,
													'bIsAjax'			=> false,
													'row_alternate'		=> false
													), true);
		
		$arrSEReportNoti = $this->model->GetOpenNotiSEReport();
		$strSEReportNoti = $this->load->view('notification/ajax_se_report_noti',array(
		                                            'arrNotiSEReport' => $arrSEReportNoti,
													'bIsAjax'			=> false,
													'row_alternate'		=> false
													), true);
													
		$strNotiList = $this->load->view('notification/notification_list',array(
		                                            'strIncidentNoti' 	=> $strIncidentNoti,
		                                            'strSEReportNoti' 	=> $strSEReportNoti,
													'base_url'			=> base_url()
													), true);
		
		exit($strNotiList);
	}

	public function CloseNotiById()
	{
		$iNotiType  = intval($_REQUEST['noti_type']);
		$iNotiId  = intval($_REQUEST['noti_id']);
		// var_dump($iNotiId);
		$bResult  = $this->model->CloseNotiById($iNotiType,$iNotiId);
		exit($bResult);
	}
}

?>
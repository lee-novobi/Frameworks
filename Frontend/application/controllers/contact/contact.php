<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_controller.php';

/**
 *
 * Contact.php: This file contains Contact Class, the controller for Contact Point of Monitor_Assistant
 *
 **/

/**
 * Contact Class
 *
 *
 **/

class Contact extends Base_controller {
/**
 *
 * Constructor: extends from Base controller Class
 *
 */
 	var $strIpAddress = '';
	public function __construct(){
		parent::__construct();
		$this->load->model('contact/contact_model', 'contact_model');
		$this->load->model('alert/alert_model', 'alert_model');
		$this->load->model('incident/mysql_incident_model', 'incident_model');
		$this->strIpAddress = $this->get_ip();
	}
	
/**
 * Index
 *
 * Default page
 *
 */
	public function index()
	{
		$this->contact_list();
	}

//-----------------------------------------------------------------------------------------//
/**
 * 
 *
 */
	public function get_product_list_by_departmentid_json($nDepartmentId) {
		$arrProductsTemp = array();
		if(isset($nDepartmentId)) {
			$arrProductsTemp = $this->contact_model->GetProductListByDepartmentIdForContact($nDepartmentId);	
		}
		$arrProducts = array();
		if(!empty($arrProductsTemp)) {
			$arrProducts[0]['productid'] = "-1";
			$arrProducts[0]['product_name'] = STRING_LIST;
			foreach($arrProductsTemp as $index=>$one_prod) {
				$arrProducts[] = array('productid' => $one_prod['productid'], 'product_name' => $one_prod['name']);
			}
			
		} 
		$product_json_objs = json_encode($arrProducts); 
		echo $product_json_objs;
		exit();
	}

//-----------------------------------------------------------------------------------------//
/**
 * get_list_product_ajax : get list products when search by textbox
*
* 
*/
	public function get_list_product_ajax() {
		$strQuery = isset($_GET['query']) ? $_GET['query'] : FALSE;
		$arrResponse = array(
                    'query' => '',
                    'suggestions' => array(),
                    'data' => array(),
                );
        if($strQuery) {
            $objFound = $this->contact_model->getProductListByKeySearch($strQuery);
            $arrSuggestions = array();
            $arrData = array();
            if(is_array($objFound)) {
                foreach($objFound as $row) {
                    $arrSuggestions[] = $row['name'];
                    $arrData[] = $row['productid'];
                }
                
                $arrResponse = array(
                    'query' => $strQuery,
                    'suggestions' => $arrSuggestions,
                    'data' => $arrData,
                );
                
            }
            
        }
        echo json_encode($arrResponse);
        exit;

	}

//-----------------------------------------------------------------------------------------//
/**
 * popup send message
*
* @param $noUserId 
*/
    public function load_popup_sms() {
    	$noUserId = $_REQUEST['userid'];
		$strIncidentId = @$_REQUEST['incident_id'];
		$strAlertId = @$_REQUEST['alert_id'];
		$strAlertMsg = @$_REQUEST['alert_msg'];
		$strTimeAlert = isset($_REQUEST['time_alert']) ? trim($_REQUEST['time_alert']) : null;
		$strSMSIdentifier = isset($_REQUEST['sms_identifier']) ? trim($_REQUEST['sms_identifier']) : null;
		$strChangeId = isset($_REQUEST['change_id']) ? trim($_REQUEST['change_id']) : null;
    	$arrOrders = array('; ', ';', '/ ','/');
    	$arrChildOrders = array('. ', '- ', '.', '-');
		$arrOneUser = array();
		if($noUserId != null) {
			$arrOneUser = $this->contact_model->getUserById($noUserId);
			if(!empty($arrOneUser)) {
				$arrOneUser['mobile'] = str_replace($arrOrders, ', ', $arrOneUser['mobile']);
		    	$arrOneUser['mobile'] = str_replace($arrChildOrders, '', $arrOneUser['mobile']);
		        if(strpos($arrOneUser['mobile'], ',')!==false) {
		            $arrOneUser['mobile'] = explode(', ', $arrOneUser['mobile']);
		        } else {
		            $arrOneUser['mobile'] = array($arrOneUser['mobile']);
		        }
			}
		}
		$this->loadview('contact/popup_send_message', array('arrOneUser' => $arrOneUser, 'strIncidentId' => $strIncidentId, 'strAlertId' => $strAlertId,
															'strAlertMsg' => $strAlertMsg, 'strTimeAlert' => $strTimeAlert, 'sms_identifier' => $strSMSIdentifier,
															'strChangeId' => $strChangeId
		), 'layout_popup');
	}

//-----------------------------------------------------------------------------------------//
/**
 * send message to mobile
*
* 
*/
	public function send_sms() {
		$strIncidentId = $strAlertId = $strAlertMsg = $strAlertMsg = $strTimeAlert = $strSMSIdentifier = $strChangeId = null;
        $strMessage = $this->input->post("message");
        $strMobile = $this->input->post("mobile"); 
        $strUsrName = $this->input->post("user_name");
        $iUserId = $this->input->post("userid");
		if(!empty($_POST['incident_id'])) 
			$strIncidentId = $this->input->post("incident_id");
		if(!empty($_POST['alert_id']))
			$strAlertId = $this->input->post("alert_id");
		if(!empty($_POST['alert_msg'])) {
			$strAlertMsg = urldecode($_POST['alert_msg']);
			$strAlertMsg = urldecode($strAlertMsg);
		}
		if(!empty($_POST['time_alert'])) {
			$strTimeAlert = trim($_POST['time_alert']);
			if(strpos($strTimeAlert, ':') === FALSE) {
				$strTimeAlert = date(FORMAT_MYSQL_DATETIME, $_POST['time_alert']);	
			}
		}	
		if(!empty($_POST['change_id'])) {
			$strChangeId = trim($_POST['change_id']);
		}
		
        $iUserActionId = $_SESSION['userId'];
		
		$strTypeMsg = '';
        $strNote = '';                    
		$strContent = '';
		$strMessage = base64_encode(trim($strMessage));
		// vd(strlen($strMessage));
		if ($strMessage != '' && strlen($strMessage) <= 160) {
			$strPhone = str_replace(" ","",$strMobile);
			$strPhone = "84" . substr($strPhone, 1);
            
            $ch = curl_init(API_SEND_SMS . $strPhone . API_SEND_SMS_SUFFIX . $strMessage);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$objRes = curl_exec($ch);
			curl_close($ch);

            $curl_err_msg = 'unknown';
            if($objRes === false){
                $curl_err_msg = curl_error($ch);
            }

			$objRes = intval($objRes);
			switch ($objRes) {
				case 1:
					$strContent = "SMS to [" . $strPhone . "] with content: " . $this->input->post("message");
                    $strNote = STRING_NOTE;
                    $strTypeMsg = MESSAGE_TYPE_SUCCESS;
					break;
				case -1:
					$strContent = PHONE_NUMBER_INVALID_MESSAGE . $strPhone;
                    $strNote = MESSAGE_TYPE_ERROR;
					break;
				case -2:
					$strContent = LENGTH_MORE_THAN_160_CHARS_MESSAGE . $this->input->post("message");
                    $strNote = MESSAGE_TYPE_ERROR;
					break;
                case -3:
                    $strContent = OPERATION_INVALID_MESSAGE . $this->input->post("message");
                    $strNote = MESSAGE_TYPE_ERROR;
                    break;
                case -4:
                    $strContent = INVALID_SERVICEID_AND_COMMAND_CODE_MESSAGE . $this->input->post("message");
                    $strNote = MESSAGE_TYPE_ERROR;
                    break;
                case -5:
                    $strContent = DUPLICATE_MT_MESSAGE . $this->input->post("message");
                    $strNote = MESSAGE_TYPE_ERROR;
                    break;
                case -6:
                    $strContent = SYSTEM_BUSY_MESSAGE . $this->input->post("message");
                    $strNote = MESSAGE_TYPE_ERROR;
                    break;
                case -7:
                    $strContent = INVALID_SIG_PARTNER_MESSAGE . $this->input->post("message");
                    $strNote = MESSAGE_TYPE_ERROR;
                    break;
                case -8:
                    $strContent = INVALID_REQUESTID_MESSAGE . $this->input->post("message");
                    $strNote = MESSAGE_TYPE_ERROR;
                    break;
                case -9:
                    $strContent = LENGTH_PHONE_NUMBER_MORE_THAN_20_CHARS . $this->input->post("message");
                    $strNote = MESSAGE_TYPE_ERROR;
                    break;
                case -10:
                    $strContent = NULL_PHONE_NUMBER_MESSAGE . $this->input->post("message");
                    $strNote = MESSAGE_TYPE_ERROR;
                    break;
                case -11:
                    $strContent = NULL_MESSAGE_SENDED . $this->input->post("message");
                    $strNote = MESSAGE_TYPE_ERROR;
                    break;
                case -12:
                    $strContent = INVALID_PHONE_NUMBER . $this->input->post("message");
                    $strNote = MESSAGE_TYPE_ERROR;
                    break;
				default:
					$strContent = SYSTEM_ERROR_MESSAGE . $curl_err_msg;
                    $strNote = MESSAGE_TYPE_ERROR;
					break;
			}
		} else {
			$strContent = "Length of message > 160 characters: " . $this->input->post("message");
            $strNote = "error";
		}
        
        $dtCurrentDatetime = new DateTime(null, new DateTimeZone(TIMEZONE_HCM));
		$arrInsertData = array(
			'content' => $strContent,
			'action_type' => CONTACT_ACTION_TYPE_SMS,
			'user_action_id' => intval($iUserActionId),
			'incident_id' => $strIncidentId,
			'alert_id' => $strAlertId,
			'alert_message' => $strAlertMsg,
			'time_alert' => $strTimeAlert,
			'ip_action' => $this->strIpAddress,
			'ref_id' => $iUserId,
			'change_id' => $strChangeId,
            'created_date' => $dtCurrentDatetime->format('Y-m-d H:i:s')
		);
		$this->contact_model->insert_action_history($arrInsertData);
        echo json_encode(array('content' => $strContent, 'note' => $strNote, 'type_msg' => $strTypeMsg));
        exit();
    }

//-----------------------------------------------------------------------------------------//
/**
 * function::load_popup_list_mobile_user
* description: popup display list mobiles of one user
* @param $iUserId
*/
    public function load_popup_list_mobile_user() {
    	// pd($_REQUEST);
    	$iUserId = $_REQUEST['userid'];
		$strIncidentId = !empty($_REQUEST['incident_id']) ? $_REQUEST['incident_id'] : null;
		$strAlertId = !empty($_REQUEST['alert_id']) ? $_REQUEST['alert_id'] : null;
		$strAlertMsg = !empty($_REQUEST['alert_msg']) ? $_REQUEST['alert_msg'] : null;
		$strTimeAlert = !empty($_REQUEST['time_alert']) ? $_REQUEST['time_alert'] : null;
		$strChangeId = !empty($_REQUEST['change_id']) ? $_REQUEST['change_id'] : null;
    	$arrUser = array();
    	$arrOrders = array('; ', ';', '/ ','/');
    	$arrChildOrders = array('. ', '- ', '.', '-');
    	if(isset($iUserId)) {
    		$arrUser = $this->contact_model->getUserById($iUserId);
    		if(!empty($arrUser)) {
	    		$arrUser['mobile'] = str_replace($arrOrders, ', ', $arrUser['mobile']);
	    		$arrUser['mobile'] = str_replace($arrChildOrders, '', $arrUser['mobile']);
	        	$arrUser['mobile'] = explode(', ', $arrUser['mobile']);
				$this->call_service($arrUser, $strIncidentId, $strAlertId, $strAlertMsg, $strTimeAlert, $strChangeId);
	    	} else {
				$this->loadview('error/error_no_found_user', array(), 'layout_popup');	
			}
    	} else {
    		$iUserId = 0;
			$this->loadview('error/error_no_found_user', array(), 'layout_popup');
    	}
    }


//-----------------------------------------------------------------------------------------//
/**
 * function::load_popup_call_user_by_id
* description: popup execute action call user
* @param $iUserId
*/
    public function load_popup_call_user_by_id($iUserId, $strZbxHostName, $strIncidentId) {
    	$iUserActionId = $_SESSION['userId'];
		$arrUser = $this->contact_model->getUserById($iUserId);
		//write log for user calling
		$strContent = 'Called to '.$arrUser['full_name'];
		$oCurrentDatetime = new DateTime(null, new DateTimeZone(TIMEZONE_HCM));
		$arrInsertData = array(
			'content' => $strContent,
			'action_type' => NOTIFY_ACTION_TYPE_CALL,
			'user_action_id' => intval($iUserActionId),
            'created_date' => $oCurrentDatetime->format('Y-m-d H:i:s'),
			'host_name'	=> $strZbxHostName
		);
		$this->contact_model->insert_action_history($arrInsertData);
		$this->loadview('contact/popup_call_user', array('arrUser' => $arrUser,
												 'strZbxHostName'	=> $strZbxHostName,
												 'strIncidentId' => $strIncidentId), 'layout_popup');
	}
	
//-----------------------------------------------------------------------------------------//
/**
 * function::load_popup_call_user
* description: popup execute action call user
* @param $arrUser
*/
    public function load_popup_call_user($arrUser, $strIncidentId, $strAlertId, $strAlertMsg, $strTimeAlert, $strChangeId) {
    	// pd(count($arrUser['mobile']));
    	$iUserActionId = $_SESSION['userId'];
		//write log for user calling
		$strContent = 'Called to '.$arrUser['full_name'];
		if(!empty($strAlertMsg)) {
			$strAlertMsg = urldecode($strAlertMsg);
		}
		if(!empty($strTimeAlert)) {
			$strTimeAlert = date(FORMAT_MYSQL_DATETIME, $strTimeAlert);
		}
		$oCurrentDatetime = new DateTime(null, new DateTimeZone(TIMEZONE_HCM));
		$arrInsertData = array(
			'content' => $strContent,
			'action_type' => NOTIFY_ACTION_TYPE_CALL,
			'user_action_id' => intval($iUserActionId),
            'created_date' => $oCurrentDatetime->format('Y-m-d H:i:s'),
            'ref_id' => $arrUser['userid'],
			'incident_id' => $strIncidentId,
			'alert_id' => $strAlertId,
			'alert_message' => $strAlertMsg, 
			'time_alert' => $strTimeAlert,
			'change_id' => $strChangeId,
			'ip_action' => $this->strIpAddress
		);
		$this->contact_model->insert_action_history($arrInsertData);
		$this->loadview('contact/popup_call_user', array('arrUser' => $arrUser,
												 'strAlertId' => $strAlertId,
												 'strAlertMsg' => $strAlertMsg,
												 'strTimeAlert' => $strTimeAlert,
												 'strIncidentId' => $strIncidentId,
												 'strChangeId' => $strChangeId), 'layout_popup');
	}
	
//-----------------------------------------------------------------------------------------//
/**
 * function::call_user_success
*
* 
*/
	public function call_user_success() {
		$strIncidentId = $strAlertId = $strAlertMsg = $strTimeAlert = $iIdentifier = $strChangeId = null;
		$iUserActionId = $_SESSION['userId'];
		$iUserId	 = $this->input->post('iUserId');
		$arrUser = $this->contact_model->getUserById($iUserId);
		
		if(!empty($_POST['incident_id'])) {
			$strIncidentId = $this->input->post('incident_id');
		}
		if(!empty($_POST['alert_id'])) {
			$strAlertId = $this->input->post('alert_id');
		}
		if(!empty($_POST['alert_msg'])) {
			$strAlertMsg = trim($_POST['alert_msg']);
			$strAlertMsg = urldecode($strAlertMsg);
		}
		if(!empty($_POST['time_alert'])) {
			$strTimeAlert = trim($_POST['time_alert']);
			if(!empty($_POST['identifier'])) {
				$strTimeAlert = date(FORMAT_MYSQL_DATETIME, $strTimeAlert);
			}
		}
		if(!empty($_POST['change_id'])) {
			$strChangeId = trim($_POST['change_id']);
		}
		
		//write log for user calling
		$strContent = 'Called to '.@$arrUser['full_name'];
		$oCurrentDatetime = new DateTime(null, new DateTimeZone(TIMEZONE_HCM));
		$arrInsertData = array(
			'content' => $strContent,
			'action_type' => CONTACT_ACTION_TYPE_CALL_SUCCESS,
			'user_action_id' => intval($iUserActionId),
            'created_date' => $oCurrentDatetime->format('Y-m-d H:i:s'),
            'ref_id' => @$arrUser['userid'],
			'incident_id' => $strIncidentId,
			'alert_id' => $strAlertId,
			'alert_message' => $strAlertMsg,
			'time_alert' => $strTimeAlert,
			'change_id' => $strChangeId,
			'ip_action' => $this->strIpAddress
		);
		$this->contact_model->insert_action_history($arrInsertData);
		
		// insert number_of_call_success to incident_follow table
		if(!empty($strIncidentId)) {
			$iNumOfCall = $this->contact_model->getNumofCallFromInc($strIncidentId, CONTACT_ACTION_TYPE_CALL_SUCCESS);
			$iNumOfCall = isset($iNumOfCall) ? $iNumOfCall : 0;
			$this->incident_model->UpdateNumOfCallFromInc(YES, $strIncidentId, $iNumOfCall);
		}
	}
	
//-----------------------------------------------------------------------------------------//
/**
 * function::call_user_fail
*
* 
*/
	public function call_user_fail() {
		$strIncidentId = $strAlertId = $strAlertMsg = $strTimeAlert = $iIdentifier = null;
		$iUserActionId = $_SESSION['userId'];
		$iUserId	 = $this->input->post('iUserId');
		$arrUser = $this->contact_model->getUserById($iUserId);
		
		if(!empty($_POST['incident_id'])) {
			$strIncidentId = $this->input->post('incident_id');
		}
		if(!empty($_POST['alert_id'])) {
			$strAlertId = $this->input->post('alert_id');
		}
		if(!empty($_POST['alert_msg'])) {
			$strAlertMsg = trim($_POST['alert_msg']);
			$strAlertMsg = urldecode($strAlertMsg);
		}
		if(!empty($_POST['time_alert'])) {
			$strTimeAlert = trim($_POST['time_alert']);
			if(!empty($_POST['identifier'])) {
				$strTimeAlert = date(FORMAT_MYSQL_DATETIME, $strTimeAlert);
			}
		}
		if(!empty($_POST['change_id'])) {
			$strChangeId = trim($_POST['change_id']);
		}
		
		//write log for user calling
		$strContent = 'Called to '.@$arrUser['full_name'];
		$oCurrentDatetime = new DateTime(null, new DateTimeZone(TIMEZONE_HCM));
		$arrInsertData = array(
			'content' => $strContent,
			'action_type' => CONTACT_ACTION_TYPE_CALL_FAIL,
			'user_action_id' => intval($iUserActionId),
            'created_date' => $oCurrentDatetime->format('Y-m-d H:i:s'),
            'ref_id' => @$arrUser['userid'],
			'incident_id' => $strIncidentId,
			'alert_id' => $strAlertId,
			'alert_message' => $strAlertMsg,
			'time_alert' => $strTimeAlert,
			'change_id' => $strChangeId,
			'ip_action' => $this->strIpAddress
		);
		$this->contact_model->insert_action_history($arrInsertData);
		
		// insert number_of_call_fail to incident_follow table
		if(!empty($strIncidentId)) {
			$iNumOfCall = $this->contact_model->getNumofCallFromInc($strIncidentId, CONTACT_ACTION_TYPE_CALL_FAIL);
			$iNumOfCall = isset($iNumOfCall) ? $iNumOfCall : 0;
			$this->incident_model->UpdateNumOfCallFromInc(NO, $strIncidentId, $iNumOfCall);
		}
	}
	
//-----------------------------------------------------------------------------------------//
/**
 * function::call_service
*
* 
*/
	public function call_service($arrUser, $strIncidentId, $strAlertId, $strAlertMsg, $strTimeAlert, $strChangeId) {
		$oResult = '';
		$strMobile = '';
		$strUserName = '';
		if(!empty($arrUser)) {
			if(!isset($arrUser['cellphone'])) {
				$strMobile = $arrUser['mobile'][0];
				$strUserName = $arrUser['full_name'];	
			} else {
				$strMobile = $arrUser['cellphone'][0];
				$strUserName = $arrUser['fullname'];
			}
		}
		$strIpAddress = $this->get_ip();
		
		$oAvayaInfo = $this->contact_model->get_avaya_info_by_ip($strIpAddress);
		
		if ($oAvayaInfo != null) {
			$strExt = $oAvayaInfo->ext;
			$strPhoneToCall = str_replace(" ", "", $strMobile);
			$ch = curl_init(API_CALL_AVAYA . $strExt . API_CALL_AVAYA_SUFFIX . $strPhoneToCall);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			$oResult = curl_exec($ch);
			curl_close($ch);
			ob_clean();
			if($oResult) {
				if(!isset($arrUser['cellphone'])) {
					$this->load_popup_call_user($arrUser, $strIncidentId, $strAlertId, $strAlertMsg, $strTimeAlert, $strChangeId);
				} else {
					$this->load_popup_call_staff_VNGHR($arrUser, $strIncidentId, $strAlertId, $strAlertMsg, $strTimeAlert, $strChangeId);
				}
			} else {
				//show popup call contact fail
				$this->loadview('error/call_user_time_out', array('strUserName' => $strUserName), 'layout_popup');
			} 
		} else {
			// load view no found ip to call
			$this->loadview('error/error_no_found_avaya', array(), 'layout_popup');
		}
		
	}

//-----------------------------------------------------------------------------------------//
/**
 * function::call_user
*
* 
*/
	public function call_user() {
		// $strResult = 'FALSE';
		// $strZbxHostName = null;
		$strIncidentId = $strAlertId = $strRefId = $strAlertMsg = $strTimeAlert = $strChangeId = null;
		$oRs = array('msg' => '');
		// $strIpAddress = $this->get_ip();
		$strUserName = $this->input->post('user_name');
		$oAvayaInfo = $this->contact_model->get_avaya_info_by_ip($this->strIpAddress);
		$strMobile	 = $this->input->post('strMobile');
		// if(isset($_POST['zabbix_host_name'])) {
			// $strZbxHostName = $_POST['zabbix_host_name'];
		// }
		if(!empty($_POST['ref_id'])) {
			$strRefId = trim($_POST['ref_id']);
		}
		if(!empty($_POST['incident_id'])) {
			$strIncidentId = trim($_POST['incident_id']);
		}
		if(!empty($_POST['alert_id'])) {
			$strAlertId = trim($_POST['alert_id']);
		}
		if(!empty($_POST['alert_msg'])) {
			$strAlertMsg = trim($_POST['alert_msg']);
			$strAlertMsg = urldecode($strAlertMsg);
		}
		if(!empty($_POST['time_alert'])) {
			$strTimeAlert = trim($_POST['time_alert']);
		}
		if(!empty($_POST['change_id'])) {
			$strChangeId = trim($_POST['change_id']);
		}
		if(!empty($_POST['action_type']) && trim($_POST['action_type']) === 'recall') {
			$this->insert_call_action_history($strRefId, $strUserName, $strIncidentId, $strAlertId, $strAlertMsg, $strTimeAlert, $strChangeId, CONTACT_ACTION_TYPE_CALL_FAIL);			
		}
		
		if ($oAvayaInfo != null) {
			$strExt = $oAvayaInfo->ext;
			$strPhoneToCall = str_replace(" ", "", $strMobile);
			$ch = curl_init(API_CALL_AVAYA . $strExt . API_CALL_AVAYA_SUFFIX . $strPhoneToCall);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			$strResult = curl_exec($ch);
			curl_close($ch);
			ob_clean();
			if($strResult) {
				$oRs['msg'] = SUCCESS;
				$this->insert_call_action_history($strRefId, $strUserName, $strIncidentId, $strAlertId, $strAlertMsg, $strTimeAlert, $strChangeId, NOTIFY_ACTION_TYPE_CALL);
			} else {
				$oRs['msg'] = CALL_TIME_OUT;
			}
		} else {
			$oRs['msg'] = NO_FOUND_AVAYA;
		}
		echo json_encode($oRs); exit();
	}

//-----------------------------------------------------------------------------------------//
/**
 * get_list_email_ajax : get list email when search by textbox
*
* 
*/
	public function get_list_email_ajax() {
		$strQuery = isset($_GET['query']) ? $_GET['query'] : FALSE;
		$arrResponse = array(
                    'query' => '',
                    'suggestions' => array(),
                    'data' => array(),
                );
        if($strQuery) {
            $objFound = $this->contact_model->getEmailListByKeySearch($strQuery);
            $arrVNGStaffsFound = $this->contact_model->getUserEmailFromVNGStaffList($strQuery);
            $arrSuggestions = array();
            $arrData = array();
            if(is_array($objFound)) {
                foreach($objFound as $row) {
                    $arrSuggestions[] = $row['strfound'];
                    $arrData[] = $row['userid'];
                }
                
            }
            if(is_array($arrVNGStaffsFound)) {
            	foreach($arrVNGStaffsFound as $row) {
            		if(!in_array($row['strfound'], $arrSuggestions)) {
            			$arrSuggestions[] = $row['strfound'];
            			$arrData[] = $row['id'];
            		}
            	}
            }
            //sort($arrSuggestions);
            $arrResponse = array(
                    'query' => $strQuery,
                    'suggestions' => $arrSuggestions,
                    'data' => $arrData,
                );
        } 
        echo json_encode($arrResponse);
        exit;
	}

//-----------------------------------------------------------------------------------------//
/**
 * load_popup_user_information : launch popup contained users data result when searching
* @param $strUserDomain
* 
*/
	public function load_popup_user_information() {
		$strUserDomain = $_REQUEST['user'];
		$strIncidentId = $strAlertId = $strAlertMsg = $strTimeAlert = $strIdentifier = $strChangeId = null;
		if(!empty($_REQUEST['incident_id'])) {
			$strIncidentId = $_REQUEST['incident_id'];
		}
		if(!empty($_REQUEST['alert_id'])) {
			$strAlertId = $_REQUEST['alert_id'];
		}
		if(!empty($_REQUEST['alert_msg'])) {
			$strAlertMsg = trim($_REQUEST['alert_msg']);
			$strAlertMsg = urldecode($strAlertMsg);
		}
		if(!empty($_REQUEST['time_alert'])) {
			$strTimeAlert = trim($_REQUEST['time_alert']);
		}
		if(!empty($_REQUEST['identifier'])) {
			$strIdentifier = $_REQUEST['identifier'];
		}
		if(!empty($_REQUEST['change_id'])) {
			$strChangeId = $_REQUEST['change_id'];
		}
		$arrUsersInfo = $this->contact_model->getListUsersInfoByUserDomain($strUserDomain);
		//$arrVNGStaffList = $this->contact_model->getVNGStaffListByUserDomain('minht');
		$arrVNGStaffList = $this->contact_model->getVNGStaffListByUserDomain($strUserDomain);
		$arrEmails = array();
		$arrSearched = array();
		$arrVNGStaffListSearched = array();
		$t = 0;
		if(!empty($arrUsersInfo)) {
			$noUserIdTemp = $arrUsersInfo[0]['userid']; 
			$arrEmails[0] = $arrUsersInfo[0]['email'];
			foreach ($arrUsersInfo as $key => $oneUser) {
				if($noUserIdTemp != $oneUser['userid']) {
					$noUserIdTemp = $oneUser['userid'];
					$t = 0;
					$arrEmails[] = strtolower($oneUser['email']);
				} 
				$arrSearched[$noUserIdTemp][$t] = $oneUser;
				$t++;
			}

		} else {
			$arrSearched = array();
		}

		if(!empty($arrVNGStaffList)) {
			foreach ($arrVNGStaffList as $index => $oOneStaff) {
				if(!in_array(strtolower($oOneStaff->email), $arrEmails)) {
					$arrVNGStaffListSearched[] = $oOneStaff;
				}
			}
		} else {
			$arrVNGStaffListSearched = array();
		}
		$this->loadview('contact/popup_search_by_user_result', array('arrSearchUsersResult' => $arrSearched, 'arrVNGStaffs' => $arrVNGStaffListSearched, 'strIncidentId' => $strIncidentId, 'strAlertId' => $strAlertId,
																	 'strAlertMsg' => $strAlertMsg, 'strTimeAlert' => $strTimeAlert, 'strIdentifier' => $strIdentifier, 'strChangeId' => $strChangeId
		), 'layout_popup');
	}

	public function attached_comparison_info_for_contacts($arrUsers) {
		if(!empty($arrUsers)) {
			foreach($arrUsers as $index=>$oneUser) {
				$arrHRDept = $this->contact_model->findUserViaEmail(trim($oneUser['email']));
            	@$arrUsers[$index]['vng_dept'] = $arrHRDept['vng_dept']; 
			}
		}
		return $arrUsers;
	}

	public function contact_list() {
		$arrDepartments = array();
		$arrProducts = array();
		$arrUsers = array();
		$arrEscalationUsers = array();
		$arrDepartments = $this->contact_model->GetDepartmentListForContact();

		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			$noDepartmentId = $_POST['department'];
			$noProductId = $_POST['product'];
			//p($_POST);
			if(isset($_POST['btnFilter'])) {
				$arrProducts = $this->contact_model->GetProductListByDepartmentIdForContact($noDepartmentId);
				if($noDepartmentId != -1) {
					$noDepartmentId = intval($noDepartmentId);
					$noProductId = intval($noProductId);
					if(!empty($noProductId) && $noProductId != -1) {
						$arrUsers = $this->contact_model->getUsersByDepartmentProduct($noProductId);
						$arrUsers = $this->attached_comparison_info_for_contacts($arrUsers);

						$arrEscalationUsers = $this->contact_model->getEscalationUserEachLevel($noProductId);
						$arrEscalationUsers = $this->attached_comparison_info_for_contacts($arrEscalationUsers);

					} elseif($noProductId == -1) {
						$arrUsers = $this->contact_model->GetUsersByDepartmentId($noDepartmentId);
						if(!empty($arrUsers)) {
							foreach($arrUsers as $index=>$oneUser) {
								$arrUsers[$index]['role'] = NO_ROLE;
								$arrHRDept = $this->contact_model->findUserViaEmail(trim($oneUser['email']));
				            	@$arrUsers[$index]['vng_dept'] = $arrHRDept['vng_dept']; 
							}
						}
					}
				}
			} 
		}
		$this->loadview('contact/contact_list', array(
							'arrDepartments' => $arrDepartments,
							'arrProducts' => $arrProducts,
							'arrUsers' => $arrUsers,
							'arrEscalationUsers' => $arrEscalationUsers
						));
	}

	public function load_popup_contact_by_product() {
		$strProductName = $this->input->get('product_name');
		$strProductName = trim($strProductName);
		$iCallFrom = (isset($_REQUEST['call_from'])) ? 1 : null; 
		$strIncidentId = !empty($_REQUEST['incident_id']) ? $_REQUEST['incident_id'] : null;
		$strAlertId = !empty($_REQUEST['alert_id']) ? $_REQUEST['alert_id'] : null;
		$strAlertMsg = !empty($_REQUEST['alert_msg']) ? $_REQUEST['alert_msg'] : null;
		$strTimeAlert = !empty($_REQUEST['time_alert']) ? $_REQUEST['time_alert'] : null;
		$strChangeId = !empty($_REQUEST['change_id']) ? $_REQUEST['change_id'] : null;
		
		$arrUsers = array();
		if(!empty($strProductName)) {
			$arrUsers = $this->contact_model->getUsersByProduct($strProductName);
			$arrUsers = $this->attached_comparison_info_for_contacts($arrUsers);
		}
		$this->loadview('contact/popup_users_information_list', array('arrContacts' => $arrUsers, 'iCallFrom' => $iCallFrom,
																	'strIncidentId' => $strIncidentId,
																	'strAlertId' => $strAlertId,
																	'strAlertMsg' => $strAlertMsg,
																	'strTimeAlert' => $strTimeAlert,
																	'strChangeId' => $strChangeId
																	), 'layout_popup');
	}

//-----------------------------------------------------------------------------------------//
	public function childFuncSavingUserInfor($strFullName, $strMobile, $strEmail, $strExt, $iDepartmentId) {
		$strMsg = '';
		$iDepartmentId = intval($iDepartmentId);
		$iPos = strpos($strEmail, '@');
		if($iPos) {
			$strEmailPrefix = substr($strEmail, 0, $iPos);
			$iEmailExisted = $this->contact_model->checkEmailExisted($strEmailPrefix);
			if($iEmailExisted > 0) {
				$strMsg = ALREADY;
			} else {
				$iAffectedRowsAfterSavingContact = $this->contact_model->insertUserFromVNGStaffList($strFullName, $strMobile, $strEmail, $strExt, $iDepartmentId);
				if($iAffectedRowsAfterSavingContact > 0) {
					$strMsg = MESSAGE_TYPE_SUCCESS;
				} else {
					$strMsg = INSERTDB_ERROR;
				}
			}
		} else {
			$strMsg = INVALID_EMAIL;
		}
		return $strMsg;
	}

//-----------------------------------------------------------------------------------------//
/**
 * function::check_department_existed_in_contactSDK
*  
* @return json
*/
	public function check_department_existed_in_contactSDK() {
		$strMsg = '';
		if(isset($_POST['userid'])) {
			$iStaffId = $_POST['userid'];
			$oVNGStaff = $this->contact_model->getStaffVNGByUserId(intval($iStaffId));
			if(!empty($oVNGStaff)) {
				//pd($oVNGStaff);
				$strFullName = trim($oVNGStaff->fullname);
				$strMobile = trim(preg_replace('/\s+/', '', $oVNGStaff->cellphone));
				$strEmail = trim($oVNGStaff->email);
				$strExt = trim($oVNGStaff->extension);
				$strDepartmentName = trim($oVNGStaff->department);
				$strEmail = strtolower($strEmail);
				if(empty($strFullName)) {
					$strMsg = NO_NAME;
				} elseif(empty($strEmail) || $strEmail == NULL_STRING) {
					$strMsg = NO_EMAIL;
				} else {
					if($strExt == NULL_STRING) {
						$strExt = '';
					}
					if(empty($strDepartmentName)) {
						$strMsg = NO_DEPT;
					} else {
						$iPos = strpos($strEmail, '@');
						if($iPos) {
							$strEmailPrefix = substr($strEmail, 0, $iPos);
							$iEmailExisted = $this->contact_model->checkEmailExisted($strEmailPrefix);
							if($iEmailExisted > 0) {
								$strMsg = ALREADY;
							} else {
								$oResult = $this->contact_model->getDepartmentIdByDeptName($strDepartmentName);
								if(!empty($oResult)) {
									$iDepartmentId = $oResult->departmentid;
									if($strMobile == NULL_STRING) 
										$strMobile = '';
									$iAffectedRowsAfterSavingContact = $this->contact_model->insertUserFromVNGStaffList($strFullName, $strMobile, $strEmail, $strExt, $iDepartmentId);
									if($iAffectedRowsAfterSavingContact > 0) {
										$strMsg = MESSAGE_TYPE_SUCCESS;
									} else {
										$strMsg = INSERTDB_ERROR;
									}
								} else {
									$strMsg = NOT_EXIST_STRING;
								}
							} 
						} else {
							$strMsg = INVALID_EMAIL;
						}
						
						
					}
				}
			}
		} /*else {
			$strMsg = LOST_DATA;
		}*/
		
		echo json_encode(array('msg' => $strMsg));
		exit();
	}

//-----------------------------------------------------------------------------------------//
/**
 * function::load_popup_choose_department_contactSDK
*  
*
*/
	public function load_popup_choose_department_contactSDK() {
		$iUserId = trim($this->input->get('userid'));
		$oVNGStaff = $this->contact_model->getStaffVNGByUserId($iUserId);
		if(!empty($oVNGStaff)) {
			//pd($oVNGStaff);
			$strFullName = trim($oVNGStaff->fullname);
			$strMobile = trim(preg_replace('/\s+/', '', $oVNGStaff->cellphone));
			$strEmail = trim($oVNGStaff->email);
			$strExt = trim($oVNGStaff->extension);
			if($strExt == NULL_STRING || empty($strExt)) {
				$strExt = NO_EXT;
			}
			if($strMobile == NULL_STRING || empty($strMobile)) {
				$strMobile = NO_PHONE;
			}
			$arrDepartments = $this->contact_model->GetDepartmentListForContact();
			$this->loadview('contact/popup_save_user_info', array('arrDepartments' => $arrDepartments, 'strFullName' => $strFullName, 'strMobile' => $strMobile, 'strEmail' => $strEmail, 'strExt' => $strExt, 'iStaffId' => $iUserId), 'layout_popup');	
		}
		
	}
//-----------------------------------------------------------------------------------------//
/**
 * function::load_popup_choose_department_contactSDK
*  
*
*/
	public function save_user_info_from_VNGDB() {
		$strMsg = '';
		if(isset($_POST['departmentid']) && isset($_POST['staffid'])) {
			$iDepartmentId = $this->input->post('departmentid');
			$iStaffId = $this->input->post('staffid');
			$oVNGStaff = $this->contact_model->getStaffVNGByUserId(intval($iStaffId));
			if(!empty($oVNGStaff)) {
				$strFullName = trim($oVNGStaff->fullname);
				$strMobile = trim(preg_replace('/\s+/', '', $oVNGStaff->cellphone));
				$strEmail = trim($oVNGStaff->email);
				$strExt = trim($oVNGStaff->extension); 
				if(!empty($strFullName) && (!empty($strEmail) || $strEmail != NO_EMAIL) && !empty($iDepartmentId)) {
					if($strExt == NULL_STRING) {
						$strExt = '';
					}
					if($strMobile == NULL_STRING) 
						$strMobile = '';
					$strMsg = $this->childFuncSavingUserInfor($strFullName, $strMobile, $strEmail, $strExt, $iDepartmentId);
				} else {
					$strMsg = NOT_ENOUGH;
				}
			}
		}
		echo json_encode(array('msg' => $strMsg));
		exit();
	}

//-----------------------------------------------------------------------------------------//
/**
 * popup send message
*
* @param $noUserId 
*/
    public function load_popup_sms_vng_staff_list() {
    	$noUserId = $_REQUEST['userid'];
		$strIncidentId = !empty($_REQUEST['incident_id']) ? $_REQUEST['incident_id'] : null;
		$strAlertId = !empty($_REQUEST['alert_id']) ? $_REQUEST['alert_id'] : null; 
		$strAlertMsg = @$_REQUEST['alert_msg'];
		$strTimeAlert = !empty($_REQUEST['time_alert']) ? trim($_REQUEST['time_alert']) : null;
		$strSMSIdentifier = isset($_REQUEST['sms_identifier']) ? trim($_REQUEST['sms_identifier']) : null;
		$strChangeId = !empty($_REQUEST['change_id']) ? $_REQUEST['change_id'] : null;
		$arrOneUser = $this->contact_model->getStaffById($noUserId);
		$arrOrders = array('; ', ';', '/ ','/');
    	$arrChildOrders = array('. ', '- ', '.', '-');
    	if(!empty($arrOneUser)) {
    		$arrOneUser['cellphone'] = str_replace($arrOrders, ', ', $arrOneUser['cellphone']);
	    	$arrOneUser['cellphone'] = str_replace($arrChildOrders, '', $arrOneUser['cellphone']);
	        if(strpos($arrOneUser['cellphone'], ',')!==false) {
	            $arrOneUser['cellphone'] = explode(', ', $arrOneUser['cellphone']);
	        } else {
	            $arrOneUser['cellphone'] = array($arrOneUser['cellphone']);
	        }
    	} else {
    		$arrOneUser = array();
    	}
    	
		$this->loadview('contact/popup_send_message_VNGHR', array('arrOneUser' => $arrOneUser, 'strIncidentId' => $strIncidentId, 'strAlertId' => $strAlertId,
																  'strAlertMsg' => $strAlertMsg, 'strTimeAlert' => $strTimeAlert, 'sms_identifier' => $strSMSIdentifier,
																  'strChangeId' => $strChangeId
		), 'layout_popup');
	}


//-----------------------------------------------------------------------------------------//
/**
 * function::load_popup_list_mobile_user_vng_staff_list
* description: popup display list mobiles of one user
* @param $iUserId
*/
    public function load_popup_list_mobile_user_vng_staff_list() {
    	$iUserId = $_REQUEST['userid'];
		$strIncidentId = !empty($_REQUEST['incident_id']) ? $_REQUEST['incident_id'] : null;
		$strAlertId = !empty($_REQUEST['alert_id']) ? $_REQUEST['alert_id'] : null;
		$strAlertMsg = !empty($_REQUEST['alert_msg']) ? $_REQUEST['alert_msg'] : null;
		$strTimeAlert = !empty($_REQUEST['time_alert']) ? $_REQUEST['time_alert'] : null;
		$strChangeId = !empty($_REQUEST['change_id']) ? $_REQUEST['change_id'] : null;
    	$arrUser = array();
    	$arrOrders = array('; ', ';', '/ ','/');
    	$arrChildOrders = array('. ', '- ', '.', '-');
    	if(isset($iUserId)) {
    		$arrUser = $this->contact_model->getStaffById($iUserId);
			if(!empty($arrUser)) {
	    		$arrUser['cellphone'] = str_replace($arrOrders, ', ', $arrUser['cellphone']);
	    		$arrUser['cellphone'] = str_replace($arrChildOrders, '', $arrUser['cellphone']);	    		
	        	$arrUser['cellphone'] = explode(', ', $arrUser['cellphone']);
		        $this->call_service($arrUser, $strIncidentId, $strAlertId, $strAlertMsg, $strTimeAlert, $strChangeId);
			} else {
				$this->loadview('error/error_no_found_user', array(), 'layout_popup');	
			}
    	} else {
    		$iUserId = 0;
			$this->loadview('error/error_no_found_user', array(), 'layout_popup');
    	}
    }


//-----------------------------------------------------------------------------------------//
/**
 * function::load_popup_call_staff_VNGHR
* description: popup execute action call user
* @param $iUserId
*/
    public function load_popup_call_staff_VNGHR($arrUser, $strIncidentId, $strAlertId, $strAlertMsg, $strTimeAlert, $strChangeId) {
		//write log for user calling
		$this->insert_call_action_history($arrUser['id'], $arrUser['fullname'], $strIncidentId, $strAlertId, $strAlertMsg, $strTimeAlert, $strChangeId, NOTIFY_ACTION_TYPE_CALL);
		$this->loadview('contact/popup_call_vng_staff_list', array('arrUser' => $arrUser,
												'strIncidentId'		=> $strIncidentId,
												'strAlertId'		=> $strAlertId,
												'strAlertMsg' 		=> $strAlertMsg, 
												'strTimeAlert' 		=> $strTimeAlert,
												'strChangeId'		=> $strChangeId)
												, 'layout_popup');
	}
	
//-----------------------------------------------------------------------------------------//
/**
 * function::call_staff_success
*
* 
*/
	public function call_staff_success() {
		$iUserId	 = $this->input->post('iUserId');
		// $strZbxHostName	 = $this->input->post('strZbxHostName');
		$strIncidentId = $strAlertId = $strAlertMsg = $strTimeAlert = $strChangeId = null;
		if(!empty($_POST['incident_id'])) {
			$strIncidentId = $_POST['incident_id'];
		}
		if(!empty($_POST['alert_id'])) {
			$strAlertId = $_POST['alert_id'];
		}
		if(!empty($_POST['alert_msg'])) {
			$strAlertMsg = trim($_POST['alert_msg']);
			$strAlertMsg = urldecode($strAlertMsg);
		}
		if(!empty($_POST['time_alert'])) {
			$strTimeAlert = trim($_POST['time_alert']);
		}
		if(!empty($_POST['change_id'])) {
			$strChangeId = trim($_POST['change_id']);
		}

		$arrUser = $this->contact_model->getStaffById($iUserId);
		//write log for user calling
		$this->insert_call_action_history(@$arrUser['id'], @$arrUser['fullname'], $strIncidentId, $strAlertId, $strAlertMsg, $strTimeAlert, $strChangeId, CONTACT_ACTION_TYPE_CALL_SUCCESS);
		$this->SaveNumofCallFromInc($strIncidentId, YES, CONTACT_ACTION_TYPE_CALL_SUCCESS);
	}
	
//-----------------------------------------------------------------------------------------//
/**
 * function::call_staff_fail
*
* 
*/
	public function call_staff_fail() {
		$iAffectedCode = 0;
		$iUserId	 = $this->input->post('iUserId');
		// $strZbxHostName	 = $this->input->post('strZbxHostName');
		$strIncidentId = $strAlertId = $strAlertMsg = $strTimeAlert = $strChangeId = null;
		if(!empty($_POST['incident_id'])) {
			$strIncidentId = $_POST['incident_id'];
		}
		if(!empty($_POST['alert_id'])) {
			$strAlertId = $_POST['alert_id'];
		}
		if(!empty($_POST['alert_msg'])) {
			$strAlertMsg = trim($_POST['alert_msg']);
			$strAlertMsg = urldecode($strAlertMsg);
		}
		if(!empty($_POST['time_alert'])) {
			$strTimeAlert = trim($_POST['time_alert']);
		}
		if(!empty($_POST['change_id'])) {
			$strChangeId = trim($_POST['change_id']);
		}
		
		$arrUser = $this->contact_model->getStaffById($iUserId);
		//write log for user calling
		$iAffectedCode = $this->insert_call_action_history(@$arrUser['id'], @$arrUser['fullname'], $strIncidentId, $strAlertId, $strAlertMsg, $strTimeAlert, $strChangeId, CONTACT_ACTION_TYPE_CALL_FAIL);
		$this->SaveNumofCallFromInc($strIncidentId, NO, CONTACT_ACTION_TYPE_CALL_FAIL);
		return $iAffectedCode;
	}
	
	//-----------------------------------------------------------------------------------------//
	private function insert_call_action_history($strRefId, $strUserName, $strIncidentId, $strAlertId, $strAlertMsg, $strTimeAlert, $strChangeId, $strNoticeActionType) {
		$iUserActionId = $_SESSION['userId'];
		//write log for user calling
		$strContent = 'Called to ' . $strUserName;
		if(!empty($strAlertMsg)) {
			$strAlertMsg = urldecode($strAlertMsg);
		}
		if(!empty($strTimeAlert) && strpos($strTimeAlert, ':') === FALSE) {
			$strTimeAlert = date(FORMAT_MYSQL_DATETIME, $strTimeAlert);
		}
		$oCurrentDatetime = new DateTime(null, new DateTimeZone(TIMEZONE_HCM));
		$arrInsertData = array(
			'content' => $strContent,
			'action_type' => $strNoticeActionType,
			'user_action_id' => intval($iUserActionId),
            'created_date' => $oCurrentDatetime->format('Y-m-d H:i:s'),
			/* 'host_name'	=> $strZbxHostName, */
			'ref_id' => $strRefId,
			'incident_id' => $strIncidentId,
			'alert_id' => $strAlertId,
			'alert_message' => $strAlertMsg,
			'time_alert' => $strTimeAlert,
			'change_id' => $strChangeId,
			'ip_action' => $this->strIpAddress
		);
		return $this->contact_model->insert_action_history($arrInsertData);
	}
	//-----------------------------------------------------------------------------------------//
	public function view_action_history_of_user() { 
		$strRefId = null;
		$strStartTimeAlert = $strStartTimeInc = $strStartTimeNoLink = $strStartTimeChange = null;
		$iNoLinkCount = $iLinkIncCount = $iLinkAlertCount = $iLinkChangeCount = 0; 
		$arrResTemp = $arrResult = array();
		// $arrTemp = array('no_link' => null, 'alert' => null, 'inc' => null);
		if(isset($_REQUEST['ref_id'])) {
			$strRefId = trim($_REQUEST['ref_id']);
			$arrResTemp = $this->contact_model->getActionHistoryByRefId($strRefId, date(FORMAT_MYSQL_DATETIME));
		}
		if(!empty($arrResTemp)) {
			foreach($arrResTemp as $idx=>$oHistory) {
				$arrStartTimeCommon = array('no_link' => null, 'link_alert' => null, 'link_inc' => null, 'link_change' => null);
				if($oHistory->action_type === NOTIFY_ACTION_TYPE_CALL) {
					if(isset($oHistory->alert_id)) {
						$arrStartTimeCommon['link_alert'] = $oHistory->created_date;
						$strStartTimeAlert = $oHistory->created_date;
					} elseif(isset($oHistory->incident_id)) {
						$arrStartTimeCommon['link_inc'] = $oHistory->created_date;
						$strStartTimeInc = $oHistory->created_date;
					} elseif(isset($oHistory->change_id)) {
						$arrStartTimeCommon['link_change'] = $oHistory->created_date;
						$strStartTimeChange = $oHistory->created_date;
					} else {
						$arrStartTimeCommon['no_link'] = $oHistory->created_date;
						$strStartTimeNoLink = $oHistory->created_date;
					}
				} else {
					if($oHistory->action_type !== CONTACT_ACTION_TYPE_SMS) {
						//case in the range 24hours or backwards, not found record action Call
						// --> get extra record from the past time before 24hours
						$strAlertMsg = $strTimeAlert = $strIncidentId = $strChangeId = null;
						if(!empty($oHistory->alert_id) && !$arrStartTimeCommon['link_alert'] && isset($oHistory->alert_message) && isset($oHistory->time_alert)) {
							// check kiem tra xem co alert_id nao y vậy truoc do ko (record call), co thi ko can get them
							$bCheckSameAlert = false;
							for($j = $idx-1; $j >= 0; $j--) {
								if($arrResTemp[$j]->action_type === NOTIFY_ACTION_TYPE_CALL) {
									if(!empty($arrResTemp[$j]->alert_id) && $arrResTemp[$j]->alert_id === $oHistory->alert_id && $arrResTemp[$j]->ip_action === $oHistory->ip_action) {
										$bCheckSameAlert = true;
									}
									if($bCheckSameAlert) break;
								}
							}
							
							if(!$bCheckSameAlert) { 
								$strAlertMsg = trim($oHistory->alert_message);
								$strTimeAlert = $oHistory->time_alert;
								$strStartTimeAlert = $this->getExtraRecordCall($strRefId, $strAlertMsg, $strTimeAlert, $strIncidentId, $oHistory->created_date, $arrStartTimeCommon);
							} 
						}
						elseif(!empty($oHistory->incident_id) && !$arrStartTimeCommon['link_inc']) {
							// check kiem tra xem co incident_id nao y vậy truoc do ko (record call), co thi ko can get them 
							$bCheckSameInc = false;
							for($j = $idx-1; $j >= 0; $j--) {
								if($arrResTemp[$j]->action_type === NOTIFY_ACTION_TYPE_CALL) {
									if(!empty($arrResTemp[$j]->incident_id) && $arrResTemp[$j]->incident_id === $oHistory->incident_id && $arrResTemp[$j]->ip_action === $oHistory->ip_action) {
										$bCheckSameInc = true;
									}
									if($bCheckSameInc) break;
								}
							}
							if(!$bCheckSameInc) {
								$strIncidentId = trim($oHistory->incident_id);
								$strStartTimeInc = $this->getExtraRecordCall($strRefId, $strAlertMsg, $strTimeAlert, $strIncidentId, $strChangeId, $oHistory->created_date, $arrStartTimeCommon);
							} 
						} elseif(!empty($oHistory->incident_id) && !$arrStartTimeCommon['link_change']) {
							// check kiem tra xem co change_id nao y vậy truoc do ko (record call), co thi ko can get them 
							$bCheckSameChange = false;
							for($j = $idx-1; $j >= 0; $j--) {
								if($arrResTemp[$j]->action_type === NOTIFY_ACTION_TYPE_CALL) {
									if(!empty($arrResTemp[$j]->change_id) && $arrResTemp[$j]->change_id === $oHistory->change_id && $arrResTemp[$j]->ip_action === $oHistory->ip_action) {
										$bCheckSameChange = true;
									}
									if($bCheckSameChange) break;
								}
							}
							if(!$bCheckSameChange) {
								$strChangeId = trim($oHistory->change_id);
								$strStartTimeChange = $this->getExtraRecordCall($strRefId, $strAlertMsg, $strTimeAlert, $strIncidentId, $strChangeId, $oHistory->created_date, $arrStartTimeCommon);
							} 
						} else {
							// check kiem tra xem co record call nao y vậy truoc do ko (cug call cho 1 nguoi, cug ip action), co thi ko can get them
							$bCheckSameNoLink = false;
							for($j = $idx-1; $j >= 0; $j--) {
								if($arrResTemp[$j]->action_type === NOTIFY_ACTION_TYPE_CALL) {
									if(empty($arrResTemp[$j]->alert_id) && empty($arrResTemp[$j]->incident_id) && $arrResTemp[$j]->ip_action === $oHistory->ip_action) {
										$bCheckSameNoLink = true;
									}
									if($bCheckSameNoLink) break;
								}
							}
							if(!$bCheckSameNoLink) {
								$strStartTimeNoLink =  $this->getExtraRecordCall($strRefId, $strAlertMsg, $strTimeAlert, $strIncidentId, $strChangeId, $oHistory->created_date, $arrStartTimeCommon);
							} 
						} 
						
						if (!empty($oHistory->alert_id)) {
							$arrResTemp[$idx]->connect_start = $arrStartTimeCommon['link_alert'];
							$arrResTemp[$idx]->connect_start = $strStartTimeAlert;
						} elseif(!empty($oHistory->incident_id)) {
							$arrResTemp[$idx]->connect_start = $arrStartTimeCommon['link_inc'];
							$arrResTemp[$idx]->connect_start = $strStartTimeInc;
						} elseif(!empty($oHistory->change_id)) {
							$arrResTemp[$idx]->connect_start = $arrStartTimeCommon['link_change'];
							$arrResTemp[$idx]->connect_start = $strStartTimeChange;
						} else {
							$arrResTemp[$idx]->connect_start = $arrStartTimeCommon['no_link'];
							$arrResTemp[$idx]->connect_start = $strStartTimeNoLink;
						}
					} else {
						$arrResTemp[$idx]->connect_start = $oHistory->created_date; 
					}
					
					$arrResTemp[$idx]->pos = $idx;
					$arrResult[$idx] = $arrResTemp[$idx];
					
					if($oHistory->action_type !== CONTACT_ACTION_TYPE_SMS && $oHistory->action_type !== NOTIFY_ACTION_TYPE_CALL) {
						if (isset($oHistory->alert_id)) {
							$arrResult = $this->removeDuplicateRecordResult($idx, $arrResTemp, $arrResult, $oHistory->alert_id);
						} elseif(isset($oHistory->incident_id)) {
							$arrResult = $this->removeDuplicateRecordResult($idx, $arrResTemp, $arrResult, $oHistory->incident_id);
						} elseif(isset($oHistory->change_id)) {
							$arrResult = $this->removeDuplicateRecordResult($idx, $arrResTemp, $arrResult, $oHistory->change_id);
						} else { 
							$arrResult = $this->removeDuplicateRecordResult($idx, $arrResTemp, $arrResult);
						}
					}
				}
			}
		}
		$this->loadview('contact/contact_history', array('oHistory' => $arrResult), 'layout_popup');
	}
	/* public function view_action_history_of_user() { 
		$strRefId = null;
		$strStartTimeAlert = $strStartTimeInc = $strStartTimeNoLink = null;
		$iNoLinkCount = $iLinkIncCount = $iLinkAlertCount = 0; 
		$arrResTemp = $arrResult = array();
		$arrTemp = array('no_link' => null, 'alert' => null, 'inc' => null);
		if(isset($_REQUEST['ref_id'])) {
			$strRefId = trim($_REQUEST['ref_id']);
			$arrResTemp = $this->contact_model->getActionHistoryByRefId($strRefId, date(FORMAT_MYSQL_DATETIME));
		}
		if(!empty($arrResTemp)) {
			foreach($arrResTemp as $idx=>$oHistory) {
				$arrStartTimeCommon = array('no_link' => null, 'link_alert' => null, 'link_inc' => null);
				if($oHistory->action_type === NOTIFY_ACTION_TYPE_CALL) {
					if(isset($oHistory->alert_id)) {
						$arrStartTimeCommon['link_alert'] = $oHistory->created_date;
						$strStartTimeAlert = $oHistory->created_date;
					} elseif(isset($oHistory->incident_id)) {
						$arrStartTimeCommon['link_inc'] = $oHistory->created_date;
						$strStartTimeInc = $oHistory->created_date;
					} else {
						$arrStartTimeCommon['no_link'] = $oHistory->created_date;
						$strStartTimeNoLink = $oHistory->created_date;
					}
				} else {
					if($oHistory->action_type !== CONTACT_ACTION_TYPE_SMS) {
						//case in the range 24hours or backwards, not found record action Call
						// --> get extra record from the past time before 24hours
						$strAlertMsg = $strTimeAlert = $strIncidentId = null;
						if(!empty($oHistory->alert_id) && !$arrStartTimeCommon['link_alert'] && isset($oHistory->alert_message) && isset($oHistory->time_alert)) {
							// check kiem tra xem co alert_id nao y vậy truoc do ko (record call), co thi ko can get them
							$bCheckSameAlert = false;
							for($j = $idx-1; $j >= 0; $j--) {
								if($arrResTemp[$j]->action_type === NOTIFY_ACTION_TYPE_CALL) {
									if(!empty($arrResTemp[$j]->alert_id) && $arrResTemp[$j]->alert_id === $oHistory->alert_id && $arrResTemp[$j]->ip_action === $oHistory->ip_action) {
										$bCheckSameAlert = true;
									}
									if($bCheckSameAlert) break;
								}
							}
							
							if(!$bCheckSameAlert) { 
								$strAlertMsg = trim($oHistory->alert_message);
								$strTimeAlert = $oHistory->time_alert;
								$strStartTimeAlert = $this->getExtraRecordCall($strRefId, $strAlertMsg, $strTimeAlert, $strIncidentId, $oHistory->created_date, $arrStartTimeCommon);
							} 
						}
						elseif(!empty($oHistory->incident_id) && !$arrStartTimeCommon['link_inc']) {
							// check kiem tra xem co incident_id nao y vậy truoc do ko (record call), co thi ko can get them 
							$bCheckSameInc = false;
							for($j = $idx-1; $j >= 0; $j--) {
								if($arrResTemp[$j]->action_type === NOTIFY_ACTION_TYPE_CALL) {
									if(!empty($arrResTemp[$j]->incident_id) && $arrResTemp[$j]->incident_id === $oHistory->incident_id && $arrResTemp[$j]->ip_action === $oHistory->ip_action) {
										$bCheckSameInc = true;
									}
									if($bCheckSameInc) break;
								}
							}
							if(!$bCheckSameInc) {
								$strIncidentId = trim($oHistory->incident_id);
								$strStartTimeInc = $this->getExtraRecordCall($strRefId, $strAlertMsg, $strTimeAlert, $strIncidentId, $oHistory->created_date, $arrStartTimeCommon);
							} 
						} else {
							// check kiem tra xem co record call nao y vậy truoc do ko (cug call cho 1 nguoi, cug ip action), co thi ko can get them
							$bCheckSameNoLink = false;
							for($j = $idx-1; $j >= 0; $j--) {
								if($arrResTemp[$j]->action_type === NOTIFY_ACTION_TYPE_CALL) {
									if(empty($arrResTemp[$j]->alert_id) && empty($arrResTemp[$j]->incident_id) && $arrResTemp[$j]->ip_action === $oHistory->ip_action) {
										$bCheckSameNoLink = true;
									}
									if($bCheckSameNoLink) break;
								}
							}
							if(!$bCheckSameNoLink) {
								$strStartTimeNoLink =  $this->getExtraRecordCall($strRefId, $strAlertMsg, $strTimeAlert, $strIncidentId, $oHistory->created_date, $arrStartTimeCommon);
							} 
						} 
						
						if (!empty($oHistory->alert_id)) {
							$arrResTemp[$idx]->connect_start = $arrStartTimeCommon['link_alert'];
							$arrResTemp[$idx]->connect_start = $strStartTimeAlert;
						} elseif(!empty($oHistory->incident_id)) {
							$arrResTemp[$idx]->connect_start = $arrStartTimeCommon['link_inc'];
							$arrResTemp[$idx]->connect_start = $strStartTimeInc;
						} else {
							$arrResTemp[$idx]->connect_start = $arrStartTimeCommon['no_link'];
							$arrResTemp[$idx]->connect_start = $strStartTimeNoLink;
						}
					} else {
						$arrResTemp[$idx]->connect_start = $oHistory->created_date; 
					}
					
					$arrResTemp[$idx]->pos = $idx;
					$arrResult[$idx] = $arrResTemp[$idx];
					
					if($oHistory->action_type !== CONTACT_ACTION_TYPE_SMS && $oHistory->action_type !== NOTIFY_ACTION_TYPE_CALL) {
						if (isset($oHistory->alert_id)) {
							$arrResult = $this->removeDuplicateRecordResult($idx, $arrResTemp, $arrResult, $oHistory->alert_id);
						} elseif(isset($oHistory->incident_id)) {
							$arrResult = $this->removeDuplicateRecordResult($idx, $arrResTemp, $arrResult, $oHistory->incident_id);
						} else { 
							$arrResult = $this->removeDuplicateRecordResult($idx, $arrResTemp, $arrResult);
						}
					}
				}
			}
			// pd($arrResTemp);
		}
		$this->loadview('contact/contact_history', array('oHistory' => $arrResult), 'layout_popup');
	} */

	//-----------------------------------------------------------------------------------------//
	private function removeDuplicateRecordResult($iPos, $arrResTemp, $arrResult, $strIdentifier=null) {
		if($arrResTemp[$iPos-1]->action_type !== CONTACT_ACTION_TYPE_SMS && $arrResTemp[$iPos-1]->action_type !== NOTIFY_ACTION_TYPE_CALL) {
			if($strIdentifier) {
				if(substr($strIdentifier, 0, 2) === INC_PREFIX && !empty($arrResTemp[$iPos-1]->incident_id) && $strIdentifier === $arrResTemp[$iPos-1]->incident_id && $arrResTemp[$iPos-1]->ip_action === $arrResTemp[$iPos]->ip_action) {	
					unset($arrResult[$iPos-1]); 					
				} else {
					if(!empty($arrResTemp[$iPos-1]->alert_id) && $strIdentifier === $arrResTemp[$iPos-1]->alert_id && $arrResTemp[$iPos-1]->ip_action === $arrResTemp[$iPos]->ip_action) {
						unset($arrResult[$iPos-1]);
					} else {
						if(!empty($arrResTemp[$iPos-1]->change_id) && $strIdentifier === $arrResTemp[$iPos-1]->change_id && $arrResTemp[$iPos-1]->ip_action === $arrResTemp[$iPos]->ip_action) {
							unset($arrResult[$iPos-1]);
						}
					}
				}
			} else {
				if(empty($arrResTemp[$iPos-1]->alert_id) && empty($arrResTemp[$iPos-1]->incident_id) && empty($arrResTemp[$iPos-1]->change_id) && $arrResTemp[$iPos-1]->ip_action === $arrResTemp[$iPos]->ip_action) {
					unset($arrResult[$iPos-1]);
				}
			}
			
		}
		return $arrResult;
	}
	
	//-----------------------------------------------------------------------------------------//
	private function getExtraRecordCall($strRefId, $strAlertMsg, $strTimeAlert, $strIncidentId, $strChangeId, $strTimeMaker, $arrStartTimeCommon) {
		$oExtraAtionCall = $this->contact_model->getExtraActionHistoryTypeNotifyCall($strRefId, $strAlertMsg, $strTimeAlert, $strIncidentId, $strChangeId, $strTimeMaker);
		$strStartTime = null;					
		if(!empty($oExtraAtionCall)) {
			if(!empty($strAlertMsg) && !empty($strTimeAlert)) {
				$arrStartTimeCommon['link_alert'] = $oExtraAtionCall->created_date;
				
			} elseif(!empty($strIncidentId)) {
				$arrStartTimeCommon['link_inc'] = $oExtraAtionCall->created_date;
			} elseif(!empty($strChangeId)) {
				$arrStartTimeCommon['link_change'] = $oExtraAtionCall->created_date;
			} else {
				$arrStartTimeCommon['no_link'] = $oExtraAtionCall->created_date;
			}
			$strStartTime = $oExtraAtionCall->created_date;
		}
		return $strStartTime;
	}
	
	//-----------------------------------------------------------------------------------------//
	public function load_cause_link() {
		$iUserId = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : null;
		$iAction = isset($_REQUEST['action']) ? intval($_REQUEST['action']) : null;
		$strOption = isset($_REQUEST['option']) ? trim($_REQUEST['option']) : null;
		$this->loadview('contact/contact_action_link_chosen', array('iUserId' => $iUserId,
																'iAction' => $iAction,
																'strOption' => $strOption
															), 'layout_popup');
	}
	//-----------------------------------------------------------------------------------------//
	public function ajax_get_cause() {
		$arrRes = array();
		$arrCause = array();
		$strCause = $this->input->get('cause'); 
		if(!empty($strCause)) {
			$strCause = trim($strCause);
			if($strCause == 'alert') {
				$arrSort = array('clock' => -1);
				$arrMaintenanceHosts = $arrConditionNoACK = array();
				$arrConditionNoACK['is_show']         = ALERT_SHOW;
				$arrConditionNoACK['is_acked']        = IS_NO_ACKED;
				
				$arrMaintenanceHosts = $this->alert_model->GetMaintenanceHostsByClock(time(), $arrSort);
				if(!empty($arrMaintenanceHosts)) {
					$arrMaintenanceHosts = $arrMaintenanceHosts['hostid'];
					$arrConditionNoACK['zabbix_server_id'] = array('$nin' => $arrMaintenanceHosts);
				}
				$arrRes = $this->alert_model->GetAlerts($arrConditionNoACK, null, $arrSort);
			} elseif($strCause == 'incident') {
				$arrCurrentShiftInfo  = $this->incident_model->GetCurrentShiftInfo();

				$arrSelectedShiftInfo = array(
					'shift_date'       => empty($_REQUEST['shift_date']) ? @$arrCurrentShiftInfo[0]['shift_date']:$_REQUEST['shift_date'],
					'shift_id'         => empty($_REQUEST['shift_id']) ? @$arrCurrentShiftInfo[0]['shift_id']:$_REQUEST['shift_id'],
				);
				$arrSelectedShiftInfo['is_current_shift'] = (@$arrCurrentShiftInfo[0]['shift_date']==$arrSelectedShiftInfo['shift_date'] && @$arrCurrentShiftInfo['0']['shift_id']==$arrSelectedShiftInfo['shift_id']);
				
				$arrRes = $this->incident_model->GetCurrentIncidents($arrSelectedShiftInfo['is_current_shift'], $arrSelectedShiftInfo['shift_date'], intval($arrSelectedShiftInfo['shift_id']));
			}
			
			if(!empty($arrRes)) {
				if($strCause == 'alert') {
					foreach($arrRes as $oAlert) {
						$arrCause[] = array('label' => $oAlert['alert_message'], 'value' => $oAlert['alert_message'], 'alert_id' => strval($oAlert['_id']), 'time_alert'=> $oAlert['clock']);
					}
				} elseif($strCause == 'incident') {
					foreach($arrRes as $oInc) {
						$arrCause[] = array('label' => $oInc->itsm_incident_id.'-' . $oInc->title, 'value' => $oInc->title, 'incident_id' => $oInc->itsm_incident_id);
					}
				}
			}
		}
		echo json_encode($arrCause);
		exit();		
	}
	//-----------------------------------------------------------------------------------------//
	public function call_fail_and_sms() {
		$strIncidentId = $strAlertId = $strAlertMsg = $strTimeAlert = $iIdentifier = $strUserIn = null;
		$iUserActionId = $_SESSION['userId'];
		$iUserId	 = $this->input->post('iUserId');
		$arrUser = array();
		
		if(!empty($_POST['incident_id'])) {
			$strIncidentId = $this->input->post('incident_id');
		}
		if(!empty($_POST['alert_id'])) {
			$strAlertId = $this->input->post('alert_id');
		}
		if(!empty($_POST['alert_msg'])) {
			$strAlertMsg = trim($_POST['alert_msg']);
			$strAlertMsg = urldecode($strAlertMsg);
		}
		if(!empty($_POST['time_alert'])) {
			$strTimeAlert = trim($_POST['time_alert']);
			if(!empty($_POST['identifier'])) {
				$strTimeAlert = date(FORMAT_MYSQL_DATETIME, $strTimeAlert);
			}
		}
		if(!empty($_POST['user_in'])) {
			if($_POST['user_in'] === SDK) {
				$strUserIn = SDK;
				$arrUser = $this->contact_model->getUserById($iUserId);
			} elseif($_POST['user_in'] === HR_VNG) {
				$strUserIn = HR_VNG;
				$arrUser = $this->contact_model->getStaffById($iUserId);
			}
		}
		//write log for user calling
		$strContent = 'Called to '.($strUserIn === SDK) ? @$arrUser['full_name'] : (($strUserIn === HR_VNG) ? @$arrUser['fullname'] : '');
		$oCurrentDatetime = new DateTime(null, new DateTimeZone(TIMEZONE_HCM));
		$arrInsertData = array(
			'content' => $strContent,
			'action_type' => CONTACT_ACTION_TYPE_CALL_FAIL,
			'user_action_id' => intval($iUserActionId),
            'created_date' => $oCurrentDatetime->format('Y-m-d H:i:s'),
            'ref_id' => ($strUserIn === SDK) ? @$arrUser['userid'] : (($strUserIn === HR_VNG) ? @$arrUser['id'] : ''),
			'incident_id' => $strIncidentId,
			'alert_id' => $strAlertId,
			'alert_message' => $strAlertMsg,
			'time_alert' => $strTimeAlert,
			'ip_action' => $this->strIpAddress
		);
		$this->contact_model->insert_action_history($arrInsertData);
		
		
		$arrOrders = array('; ', ';', '/ ','/');
    	$arrChildOrders = array('. ', '- ', '.', '-');
		$strMobileElement = $strComposeMesgView = null;

		if($strUserIn === SDK) {
			$strMobileElement = 'mobile';
			$strComposeMesgView = 'contact/popup_send_message';
		} elseif($strUserIn === HR_VNG) {
			$strMobileElement = 'cellphone';
			$strComposeMesgView = 'contact/popup_send_message_VNGHR';
		} else {
			$strComposeMesgView = 'error/error_no_found_user';
		}
		if(!empty($iUserId)) {
			if(!empty($arrUser)) {
				$arrUser[$strMobileElement] = str_replace($arrOrders, ', ', $arrUser[$strMobileElement]);
		    	$arrUser[$strMobileElement] = str_replace($arrChildOrders, '', $arrUser[$strMobileElement]);
		        if(strpos($arrUser[$strMobileElement], ',')!==false) {
		            $arrUser[$strMobileElement] = explode(', ', $arrUser[$strMobileElement]);
		        } else {
		            $arrUser[$strMobileElement] = array($arrUser[$strMobileElement]);
		        }
			}
		}
		$this->loadview($strComposeMesgView, array('arrOneUser' => $arrUser, 'strIncidentId' => $strIncidentId, 'strAlertId' => $strAlertId,
															'strAlertMsg' => $strAlertMsg, 'strTimeAlert' => $strTimeAlert
		), 'layout_popup');
	}
	//-----------------------------------------------------------------------------------------//
	private function SaveNumofCallFromInc($strIncidentId, $iCallStatus, $strCallType) {
		// insert number_of_call_fail/call_success to incident_follow table
		if(!empty($strIncidentId)) {
			$iNumOfCall = $this->contact_model->getNumofCallFromInc($strIncidentId, $strCallType);
			$iNumOfCall = isset($iNumOfCall) ? $iNumOfCall : 0;
			$this->incident_model->UpdateNumOfCallFromInc($iCallStatus, $strIncidentId, $iNumOfCall);
		}
	}
	//-----------------------------------------------------------------------------------------//
}

?>
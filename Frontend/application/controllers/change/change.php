<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_controller.php';

/**
 *
 * change.php: This file contains Change Class, the controller for Changes of Monitor_Assistant
 *
 **/

/**
 * Change Class
 *
 *
 **/

class Change extends Base_controller {
/**
 *
 * Constructor: extends from Base controller Class
 *
 */
	public function __construct(){
		parent::__construct();
		$this->load->model('change/change_model', 'change_model');
		$this->load->model('contact/contact_model', 'contact_model');
	}
	
/**
 * Index
 *
 * Default page
 *
 */
	public function index()
	{
		$this->new_change();
	}
	
/**
 * Change List
 *
 * Show list of changes
 * NOTES: 
 *
 */
	public function new_change() {
		$this->CommonChangeView('new');
	}
	// --------------------------------------------------------------------------------------------- //
	public function change_detail() {
		$oChange = $strChangeId = $strChangeView = null;
		if(isset($_GET['changeid'])) {
			$strChangeId = trim($_GET['changeid']);
		}
		if(isset($_GET['change_view'])) {
			$strChangeView = trim($_GET['change_view']);
		}
		if(!empty($strChangeId)) {
			$oChange = $this->change_model->GetChangeDetailByChangeId($strChangeId);
		}
		$this->loadview('change/change_detail', array('oChange' => $oChange, 'strChangeView' => $strChangeView), 'layout_popup');
	}
	// --------------------------------------------------------------------------------------------- //
	public function update_follow_change() {
		$this->CommonUpdateTemplate('is_followed');
	}
	// --------------------------------------------------------------------------------------------- //
	public function move_all_changes() {
		$this->CommonUpdateTemplate('move_all');
	}
	// --------------------------------------------------------------------------------------------- //
	public function follow_change() {
		$this->CommonChangeView('follow');
	}
	// --------------------------------------------------------------------------------------------- //
	public function all_changes() {
		$this->CommonChangeView('all');
	}
	// --------------------------------------------------------------------------------------------- //
	private function CommonChangeView($strChangeViewType) {
		global $arrDefined;
		$arrChanges = array();
		$iTotal = null;
		$arrPagination = $this->GetPaginationRequest('limit_change_follow', 'page_change_follow');
		$arrFilter = $this->BuildConditionRequest();
		$iNewChangesTotal = $this->change_model->CountChangesByCondition($arrFilter, 0);
		$iFollowChangesTotal = $this->change_model->CountChangesByCondition($arrFilter, 1);
		$iAllChangesTotal = $this->change_model->CountChangesByCondition($arrFilter, null, 1);
		
		if(in_array($strChangeViewType, $arrDefined['change_view']) && $strChangeViewType === $arrDefined['change_view']['N']) {
			$arrChanges = $this->change_model->GetChangeFollow($arrFilter, $arrPagination, 0); 
			$iTotal = $iNewChangesTotal;
		} elseif (in_array($strChangeViewType, $arrDefined['change_view']) && $strChangeViewType === $arrDefined['change_view']['F']) {
			$arrChanges = $this->change_model->GetChangeFollow($arrFilter, $arrPagination, 1); 
			$iTotal = $iFollowChangesTotal;
		} elseif (in_array($strChangeViewType, $arrDefined['change_view']) && $strChangeViewType === $arrDefined['change_view']['A']) {
			$arrChanges = $this->change_model->GetChangeFollow($arrFilter, $arrPagination, null, 1); 
			$iTotal = $iAllChangesTotal;
		}
		
		$this->loadview('change/changed_list', array(
											'arrChangeFollow' => $arrChanges,
											'bRowAlternate' => false ,
											'iTotal' => $iTotal,
											'iNewChangesTotal' => $iNewChangesTotal,
											'iFollowChangesTotal' => $iFollowChangesTotal,
											'iAllChangesTotal' => $iAllChangesTotal,
											'iPageSizeChangeFollow' => $arrPagination['limit'],
											'iPageChangeFollow' => $arrPagination['page'],
											'strChangeViewType' => $strChangeViewType,
											'strChangeCtl' => $this->router->fetch_method()
		));
	}
	// --------------------------------------------------------------------------------------------- //
	private function CommonUpdateTemplate($strUpdateChangeType) {
		$oUpdated = $strChangeId = $strChangeView = null;
		if(isset($_POST['change_id'])) {
			$strChangeId = trim($_POST['change_id']);
		}
		if(isset($_POST['change_view'])) {
			$strChangeView = trim($_POST['change_view']);
		}
		if(!empty($strChangeId)) {
			if($strUpdateChangeType === 'is_followed') {
				$oUpdated = $this->change_model->UpdateChangeIsFollowed($strChangeId, $strChangeView);
			} elseif ($strUpdateChangeType === 'move_all') {
				$oUpdated = $this->change_model->MoveChangeToAllChanges($strChangeId);
			}
			if(!empty($oUpdated)) {
				if($oUpdated === AFFECTED_CODE) {
					echo UPDATED_STATUS;
				} else {
					echo NOT_UPDATED_STATUS;
				}
			}
		} else {
			echo NOT_UPDATED_STATUS;
		}
		exit();
	}
	// --------------------------------------------------------------------------------------------- //
	public function contact_of_change() {
		$arrUsers = array(); //contact result found
		$arrProducts = array();
		$arrProductId = array();
		$iDepartmentSelected = null;
		$iProductIdSelected = null;
		$arrDepartments = $this->contact_model->GetDepartmentListForContact();
		$strChangeId = null;
		
		if($_SERVER['REQUEST_METHOD'] == 'GET') {
			$strProduct = $this->input->get('product');
			$strChangeId = $this->input->get('change_id');
			$strProduct = strtolower($strProduct);
			$arrProductTemp = $this->contact_model->getProductFromITSMProduct($strProduct);
			// pd($arrProductTemp);
			if(!empty($arrProductTemp)) {
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
			$strChangeId = $_POST['change_id'];
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
		
		$this->loadview('contact/contact_of_change', array(
												'arrDepartments' => $arrDepartments,
												'iDepartmentSelected' => $iDepartmentSelected,
												'arrProducts' => $arrProducts,
												'iProductIdSelected' => $iProductIdSelected,
												'strContactView' => $strContactView,
												'strChangeId' => $strChangeId
						), 'layout_popup');
	}
	// --------------------------------------------------------------------------------------------- //
	private function BuildConditionRequest() {
		$arrFilter = array();
		if(isset($_REQUEST['search-product'])) {
			$arrFilter['service'] = $this->input->get_post('search-product');
		}
		
		return $arrFilter;
	}
	// --------------------------------------------------------------------------------------------- //
}

?>
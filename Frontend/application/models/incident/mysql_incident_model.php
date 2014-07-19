<?php

/*
* Created on Feb 27, 2013
*
* To change the template for this generated file go to
* Window - Preferences - PHPeclipse - PHP - Code Templates
*/
require_once "application/models/mysql_base_model.php";

class Mysql_incident_model extends Mysql_base_model
{
    var $arrParam2DbFieldDictionary = array();
    function __construct()
    {
        parent::__construct();
        $this->arrParam2DbFieldDictionary = array(
            'auto_update_impact_level' => array('name' => 'auto_update_impact_level', 'type' =>
                    DATA_TYPE_INTEGER),
            'sdk_update_to_itsm_status' => array('name' => 'sdk_update_to_itsm_status',
                    'type' => DATA_TYPE_INTEGER),
            'sdk_update_to_itsm_count' => array('name' => 'sdk_update_to_itsm_count',
                    'type' => DATA_TYPE_INTEGER),          
            'itsm_incident_id' => array('name' => 'itsm_incident_id', 'type' =>
                    DATA_TYPE_STRING),
            'status' => array('name' => 'status', 'type' => DATA_TYPE_INTEGER),
            'alert_id' => array('name' => 'alert_id', 'type' => DATA_TYPE_STRING),
            'src_from' => array('name' => 'src_from', 'type' => DATA_TYPE_STRING),
            'src_id' => array('name' => 'src_id', 'type' => DATA_TYPE_STRING),
            'shift_id' => array('name' => 'shift_id', 'type' => DATA_TYPE_INTEGER),
            'area' => array('name' => 'area', 'type' => DATA_TYPE_STRING),
            'subarea' => array('name' => 'subarea', 'type' => DATA_TYPE_STRING),
            'customer_case' => array('name' => 'customer_case', 'type' => DATA_TYPE_INTEGER),
            'department_alias' => array('name' => 'department_alias', 'type' =>
                    DATA_TYPE_STRING),
            'department_code' => array('name' => 'department_code', 'type' =>
                    DATA_TYPE_STRING),
            'product_alias' => array('name' => 'product_alias', 'type' => DATA_TYPE_STRING),
            'product_code' => array('name' => 'product_code', 'type' => DATA_TYPE_STRING),
            'outage_start' => array('name' => 'outage_start', 'type' => DATA_TYPE_DATETIME),
            'outage_end' => array('name' => 'outage_end', 'type' => DATA_TYPE_DATETIME),
            'downtime_start' => array('name' => 'downtime_start', 'type' =>
                    DATA_TYPE_DATETIME),
            'affected_ci' => array('name' => 'affected_ci', 'type' => DATA_TYPE_STRING),
            'bugcategory' => array('name' => 'bug_category', 'type' => DATA_TYPE_STRING),
            'bugunit' => array('name' => 'unit', 'type' => DATA_TYPE_STRING),
            'assignment_group' => array('name' => 'assignment_group', 'type' =>
                    DATA_TYPE_STRING),
            'assignee' => array('name' => 'assignee', 'type' => DATA_TYPE_STRING),
            'impact_level' => array('name' => 'impact_level', 'type' => DATA_TYPE_INTEGER),
            'location' => array('name' => 'location', 'type' => DATA_TYPE_STRING),
            'urgency_level' => array('name' => 'urgency_level', 'type' => DATA_TYPE_INTEGER),
            'related_id' => array('name' => 'related_id', 'type' => DATA_TYPE_STRING),
            'related_id_change' => array('name' => 'related_id_change', 'type' =>
                    DATA_TYPE_STRING),
            'ccu_time' => array('name' => 'ccutime', 'type' => DATA_TYPE_INTEGER),
            'user_impacted' => array('name' => 'user_impacted', 'type' => DATA_TYPE_INTEGER),
            'is_cause_by_ext' => array('name' => 'caused_by_external', 'type' =>
                    DATA_TYPE_STRING),
            'cause_by_ext' => array('name' => 'caused_by_external_dept', 'type' =>
                    DATA_TYPE_STRING),
            'critical_asset' => array('name' => 'critical_asset', 'type' => DATA_TYPE_STRING),
            'title' => array('name' => 'title', 'type' => DATA_TYPE_STRING),
            'description' => array('name' => 'description', 'type' => DATA_TYPE_STRING),
            'root_cause_category' => array('name' => 'rootcause_category', 'type' =>
                    DATA_TYPE_STRING),
            'is_downtime' => array('name' => 'is_downtime', 'type' => DATA_TYPE_STRING),
            'attachments' => array('name' => 'attachments', 'type' => DATA_TYPE_STRING),
            'link' => array('name' => 'link', 'type' => DATA_TYPE_STRING),
            'detector' => array('name' => 'detector', 'type' => DATA_TYPE_STRING),
            'sdk_note' => array('name' => 'sdknote', 'type' => DATA_TYPE_STRING),
            'created_by' => array('name' => 'created_by', 'type' => DATA_TYPE_STRING),
            'created_date' => array('name' => 'created_date', 'type' => DATA_TYPE_STRING),
            'updated_by' => array('name' => 'updated_by', 'type' => DATA_TYPE_STRING),
            'updated_time' => array('name' => 'updated_time', 'type' => DATA_TYPE_STRING),
            'open_by_sdk_tool' => array('name' => 'open_by_sdk_tool', 'type' =>
                    DATA_TYPE_STRING),
            'internal_status' => array('name' => 'internal_status', 'type' =>
                    DATA_TYPE_STRING),
            'resolvedby' => array('name' => 'resolved_by', 'type' => DATA_TYPE_STRING),
            'solution' => array('name' => 'solution', 'type' => DATA_TYPE_STRING),
            'incident_status' => array('name' => 'status', 'type' => DATA_TYPE_STRING),
            'closurecode' => array('name' => 'closurecode', 'type' => DATA_TYPE_STRING),
            'reopened_by' => array('name' => 'reopened_by', 'type' => DATA_TYPE_STRING),
            'reopen_time' => array('name' => 'reopen_time', 'type' => DATA_TYPE_DATETIME),
			'knowledge_base' => array('name' => 'kb_id', 'type' => DATA_TYPE_INTEGER));
    }

    // ------------------------------------------------------------------------------------------ //
    private function ConvertParam2Data($arrParams)
    {
        $arrData = array();

        foreach ($arrParams as $strKey => $strVal)
        {
            if (isset($this->arrParam2DbFieldDictionary[$strKey]))
            {

                $strField = $this->arrParam2DbFieldDictionary[$strKey]['name'];
                $strDataType = $this->arrParam2DbFieldDictionary[$strKey]['type'];
                switch ($strDataType)
                {
                    case DATA_TYPE_DATETIME:
                        if ($strVal != "")
                        {
                            $arrData[$strField] = strval($strVal);
                        } else
                        {
                            $arrData[$strField] = null;
                        }
                        break;
                    case DATA_TYPE_STRING:
                        $arrData[$strField] = strval($strVal);
                        break;
                    case DATA_TYPE_INTEGER:
                        if (is_numeric($strVal))
                        {
                            $arrData[$strField] = intval($strVal);
                        } elseif ($strVal == "" || $strVal == NULL)
                        {
                            $arrData[$strField] = null;
                        }
                        break;
                    case DATA_TYPE_FLOAT:
                        if (is_numeric($strVal))
                        {
                            $arrData[$strField] = floatval($strVal);
                        }
                        break;
                    default:
                        $arrData[$strField] = strval($strVal);
                        break;
                }
                ;
            }
        }
       # vd($arrData);
        return $arrData;
    }

    // ------------------------------------------------------------------------------------------ //
    public function InsertIncident($arrParams)
    {
    #vd($arrParams);
        $arrData = array();
        $arrData = $this->ConvertParam2Data($arrParams);
        #vd($arrData);
        $dbRet = $this->db_ma->insert($this->tblIncidentCreateHistory, $arrData);
        #pd($this->db_ma->last_query());
        if (!$dbRet)
        {
            $errNo = $this->db_ma->_error_number();
            $errMess = $this->db_ma->_error_message();
            log_message('error', sprintf('Error: %s Message: %s', $errNo, $errMess));
        }
        return $dbRet;
    }

    // ------------------------------------------------------------------------------------------ //
    public function UpdateIncident($arrParams)
    {
        unset($arrParams['src_from']);
        unset($arrParams['src_id']);
        unset($arrParams['shift_id']);

        $arrData = array();
        $arrData = $this->ConvertParam2Data($arrParams);
        $this->db_ma->where('itsm_incident_id', $arrParams['itsm_incident_id']);
        $this->db_ma->from($this->tblIncidentUpdateHistory);
        if ($this->db_ma->count_all_results() > 0)
        {
            $this->db_ma->where('itsm_incident_id', $arrParams['itsm_incident_id']);
            #vd($arrParams); exit;
            $dbRet = $this->db_ma->update($this->tblIncidentUpdateHistory, $arrData);
        } else
        {
            $dbRet = $this->db_ma->insert($this->tblIncidentUpdateHistory, $arrData);
        }
        if (!$dbRet)
        {
            $errNo = $this->db_ma->_error_number();
            $errMess = $this->db_ma->_error_message();
            log_message('error', sprintf('Error: %s Message: %s', $errNo, $errMess));
        }

        return $dbRet;
    }

    // ------------------------------------------------------------------------------------------ //
    public function UpdateIncidentFollow($arrParams)
    {
        $arrData = array();

        $arrData = $this->ConvertParam2Data($arrParams);
        if (isset($arrData['created_date']))                {   unset($arrData['created_date']); }  
        if (isset($arrData['product_code']))                {   unset($arrData['product_code']);    }
        if (isset($arrData['sdk_update_to_itsm_status']))   {   unset($arrData['sdk_update_to_itsm_status']);   }
        if (isset($arrData['sdk_update_to_itsm_count']))    {   unset($arrData['sdk_update_to_itsm_count']);   }
        if (isset($arrData['department_alias']))            {   unset($arrData['department_alias']);    }
        if (isset($arrData['src_from']))                    {   unset($arrData['src_from']);    }
        if (isset($arrData['src_id']))                      {   unset($arrData['src_id']);      }  
        if (isset($arrData['assignment_group']))
        {
            $arrData['assignment'] = $arrParams['assignment_group'];
            unset($arrData['assignment_group']);
        }
        if (isset($arrData['product_alias']))
        {
            $arrData['product'] = $arrData['product_alias'];
            unset($arrData['product_alias']);
        }
        if (isset($arrData['department_code']))
        {
            $arrData['department'] = $arrData['department_code'];
            unset($arrData['department_code']);
        }
       
        if (isset($arrData['is_downtime'])) {
            if (@$arrData['is_downtime'] == "")
            {
                $arrData['is_downtime'] = null;
            } else {
                $arrData['is_downtime'] = (@$arrData['is_downtime'] == 'false') ? 'f' : 't';
            }
       }
       if (isset($arrData['caused_by_external'])) {
             @$arrData['caused_by_external'] = ($arrData['caused_by_external'] == 'false' || 
                                            !isset($arrData['caused_by_external'])) ? 'f' : 't';
       }
#vd($arrData);
        $arrCurrentData = $this->GetIncidentById($arrParams['itsm_incident_id']);
        $this->TrackChanges($arrCurrentData, $arrData);
        unset($arrData['updated_by']);
        unset($arrData['updated_time']);
        #vd($arrData);
        $this->db_ma->where('itsm_incident_id', $arrParams['itsm_incident_id']);
        $this->db_ma->from($this->tblIncidentFollow);
        $dbRet = $this->db_ma->update($this->tblIncidentFollow, $arrData);
        return $dbRet;
    }
    
    // ------------------------------------------------------------------------------------------ //
    public function ListIncidentUpdateFailed() 
    {
        $this->db_ma->select(array('itsm_incident_id'));
        $this->db_ma->from($this->tblIncidentUpdateHistory);
        $this->db_ma->where(array('sdk_update_to_itsm_status' => INCIDENT_UPDATE_STATUS_FAIL));
        $query = $this->db_ma->get();

        if ($query->num_rows() > 0)
        {
            $result = $query->result();
            return $result;
        } else
            return null;
    }

    // ------------------------------------------------------------------------------------------ //
    private function TrackChanges($oCurrentIncident, $oUpdatedIncident)
    {
       $strUpdatedBy = $oUpdatedIncident['updated_by']; 
       unset($oUpdatedIncident['updated_by']);
       $strUpdatedTime =  $oUpdatedIncident['updated_time']; 
       unset($oUpdatedIncident['updated_time']);
       // vd($oUpdatedIncident);
       foreach ($oUpdatedIncident as $k => $v)
       {
            if (md5($v) != md5($oCurrentIncident[$k]))
            {
                $this->InsertIncidentTrackingChanges(array(
                    'updated_by' => $strUpdatedBy,
                    'updated_time' => $strUpdatedTime,
                    'field' => $k,
                    'old_value' => $oCurrentIncident[$k],
                    'new_value' => $v,
                    'itsm_incident_id' => $oUpdatedIncident['itsm_incident_id']));
            } 
       }
    }

    // ------------------------------------------------------------------------------------------ //
    private function InsertIncidentTrackingChanges($arrData)
    {
        $dbRet = $this->db_ma->insert($this->tblIncidentTrackingChanges, $arrData);
        // vd($this->db_ma->last_query());
        if (!$dbRet)
        {
            $errNo = $this->db_ma->_error_number();
            $errMess = $this->db_ma->_error_message();
            log_message('error', sprintf('Error: %s Message: %s', $errNo, $errMess));
        }
        return $dbRet;
    }

    // ------------------------------------------------------------------------------------------ //
    public function GetAffectedCIByProduct($strProduct)
    {
        $this->db_ma->select('*');
        $this->db_ma->from($this->tblAffectedCI);
        if (!empty($strProduct))
        {
            $this->db_ma->where(array('LOWER(logical_name)' => strtolower($strProduct)));
        }
        $query = $this->db_ma->get();
        //vd($this->db_ma->last_query());
        if ($query->num_rows() > 0)
        {
            $result = $query->result();
            return $result;
        } else
            return null;
    }

    // ------------------------------------------------------------------------------------------ //
    public function GetIncidentById($strIncidentId)
    {
        $this->db_ma->from($this->tblIncidentFollow);
        $this->db_ma->where(array('itsm_incident_id' => $strIncidentId));
        $query = $this->db_ma->get();
		if ($query->num_rows() > 0)
        {
	        $rowIncident = $query->row_array();
	        return $rowIncident;
		} 
		else 
			return null;
    }

    // ------------------------------------------------------------------------------------------ //
    public function GetAssigneeByAssignmentGroup($strAssignmentGroup)
    {
        $this->db_ma->select('*');
        $this->db_ma->from($this->tblAssignee);
        $this->db_ma->where(array('assignment_group' => strtolower($strAssignmentGroup)));
        $query = $this->db_ma->get();
        #vd($this->db_ma->last_query());
        if ($query->num_rows() > 0)
        {
            $result = $query->result();

            // Tim assignee L1 dua vao escalation

            return $result;
        } else
            return null;
    }

    // ------------------------------------------------------------------------------------------ //
    public function GetAssignmentGroupByProductAndDepartment($strDepartment = '', $strProduct =
        '')
    {
        if (!empty($strDepartment) && !empty($strProduct))
        {
	        $this->db_ma->like('LOWER(product)', strtolower($strProduct), 'none');
	        $this->db_ma->like('name', 'L1', 'before')->limit(1);
		}
		$query = $this->db_ma->get($this->tblAssignmentGroup);
        #vd($this->db_ma->last_query());
        if ($query->num_rows() > 0)
        {
            $result = $query->result();
            return $result;
        } else
            return null;
    }

    // ------------------------------------------------------------------------------------------ //
    public function GetCriticalAssetByDepartment($strDepartment = '')
    {
        $this->db_ma->select('*');
        $this->db_ma->from($this->tblCriticalAsset);
        if (!empty($strDepartment))
        {
            $this->db_ma->where(array('LOWER(department)' => strtolower($strDepartment)));
        }
        $query = $this->db_ma->get();
        #vd($this->db_ma->last_query());
        if ($query->num_rows() > 0)
        {
            $result = $query->result();
            return $result;
        } else
            return null;
    }


    // ------------------------------------------------------------------------------------------ //
    public function GetAllArea()
    {
        $this->db_ma->select('*');
        $this->db_ma->from($this->tblArea);
        $query = $this->db_ma->get();

        if ($query->num_rows() > 0)
        {
            $result = $query->result();
            return $result;
        } else
            return null;
    }

    // ------------------------------------------------------------------------------------------ //
    public function GetAllSubarea()
    {
        $this->db_ma->select('*');
        $this->db_ma->from($this->tblSubarea);
        $this->db_ma->where(array('active' => ACTIVE_YES));
        $query = $this->db_ma->get();

        if ($query->num_rows() > 0)
        {
            $result = $query->result();
            return $result;
        } else
            return null;
    }

    // ------------------------------------------------------------------------------------------ //
    public function GetSubareaByArea($strArea)
    {
        $this->db_ma->select('*');
        $this->db_ma->from($this->tblSubarea);
        $this->db_ma->where(array('LOWER(area)' => strtolower($strArea), 'active' =>
                ACTIVE_YES));
        $query = $this->db_ma->get();

        if ($query->num_rows() > 0)
        {
            $result = $query->result();
            return $result;
        } else
            return null;
    }

    // ------------------------------------------------------------------------------------------ //
    public function GetAllBugCategory()
    {
        $this->db_ma->select('*');
        $this->db_ma->from($this->tblBugCategory);
        $query = $this->db_ma->get();

        if ($query->num_rows() > 0)
        {
            $result = $query->result();
            return $result;
        } else
            return null;
    }

    // ------------------------------------------------------------------------------------------ //
    public function GetAllBugUnit()
    {
        $this->db_ma->select('*');
        $this->db_ma->from($this->tblBugUnit);
        $query = $this->db_ma->get();

        if ($query->num_rows() > 0)
        {
            $result = $query->result();
            return $result;
        } else
            return null;
    }

    // ------------------------------------------------------------------------------------------ //
    public function GetBugUnitByCategory($strBugCategoryKey)
    {
        $this->db_ma->select('*');
        $this->db_ma->from($this->tblBugUnit);
        $this->db_ma->where(array('LOWER(bug_category_key)' => strtolower($strBugCategoryKey)));
        $query = $this->db_ma->get();
        //vd($this->db_ma->last_query());
        if ($query->num_rows() > 0)
        {
            $result = $query->result();
            return $result;
        } else
            return null;
    }
    //-------------------------------------------------------------------------------------
    function GetCurrentShiftInfo()
    {
        $strSQL = "CALL sp_Get_Current_Shift_Info()";
        $oQuery = $this->db_ma->query($strSQL);
        $oResult = $oQuery->result_array();
        $oQuery->next_result();
        return $oResult;
    }
    //-------------------------------------------------------------------------------------
    function GetNextShiftInfo()
    {
        $strSQL = "CALL sp_Get_Next_Shift_Info(1)";
        $oQuery = $this->db_ma->query($strSQL);
        $oResult = $oQuery->result_array();
        $oQuery->next_result();
        return $oResult;
    }
    //-------------------------------------------------------------------------------------
    function ListIncidentByShift($arrSelectedShiftInfo)
    {
        $strSQL = "CALL sp_List_Incident_By_Shift(?,?,?,?)";
        $oQuery = $this->db_ma->query($strSQL, array(
            $arrSelectedShiftInfo['shift_date'],
            (int)$arrSelectedShiftInfo['shift_id'],
            (int)$arrSelectedShiftInfo['arrPagination']['offset'],
            (int)$arrSelectedShiftInfo['arrPagination']['limit']));
        $oResult = $oQuery->result_array();
        $oQuery->next_result();

        return $oResult;
    }
    //-------------------------------------------------------------------------------------
    function ListReviewIncidentByShift($arrSelectedShiftInfo)
    {
        $strSQL = "CALL sp_List_Review_Incident_By_Shift(?,?,?,?)";
        $oQuery = $this->db_ma->query($strSQL, array(
            $arrSelectedShiftInfo['shift_date'],
            (int)$arrSelectedShiftInfo['shift_id'],
            (int)$arrSelectedShiftInfo['arrPagination']['offset'],
            (int)$arrSelectedShiftInfo['arrPagination']['limit']));
        $oResult = $oQuery->result_array();
        $oQuery->next_result();

        return $oResult;
    }
    //-------------------------------------------------------------------------------------
    function ListOpenIncident($arrSelectedShiftInfo)
    {
        $strSQL = "CALL sp_List_Open_Incident(?,?)";
        $oQuery = $this->db_ma->query($strSQL, array((int)$arrSelectedShiftInfo['arrPagination']['offset'],
                (int)$arrSelectedShiftInfo['arrPagination']['limit']));
        $oResult = $oQuery->result_array();
        $oQuery->next_result();

        return $oResult;
    }
    //-------------------------------------------------------------------------------------
    function ListClosedIncidentWithoutSubarea($arrSelectedShiftInfo)
    {
        $strSQL = "CALL sp_List_Closed_Incident_Without_Subarea(?,?)";
        $oQuery = $this->db_ma->query($strSQL, array((int)$arrSelectedShiftInfo['arrPagination']['offset'],
                (int)$arrSelectedShiftInfo['arrPagination']['limit']));
        $oResult = $oQuery->result_array();
        $oQuery->next_result();
        return $oResult;
    }
    //-------------------------------------------------------------------------------------
    function ListIncidentJustClosedBySE($arrSelectedShiftInfo)
    {
        $strSQL = "CALL sp_List_Incident_Just_Closed_By_SE(?,?)";
        $oQuery = $this->db_ma->query($strSQL, array((int)$arrSelectedShiftInfo['arrPagination']['offset'],
                (int)$arrSelectedShiftInfo['arrPagination']['limit']));
        $oResult = $oQuery->result_array();
        $oQuery->next_result();
        return $oResult;
    }
    //=== Hieutt ===//
    //-------------------------------------------------------------------------------------
    function InsertShiftTransferInfo($arrShiftInfo)
    {
        $arrData = array(
            'from_shift_date' => $arrShiftInfo['from_shift_date'],
            'from_shift_id' => $arrShiftInfo['from_shift_id'],
            'from_user_id' => $arrShiftInfo['from_user_id'],
            'to_shift_date' => $arrShiftInfo['to_shift_date'],
            'to_shift_id' => $arrShiftInfo['to_shift_id'],
            'status' => $arrShiftInfo['status']);

        $this->db_ma->insert($this->tblShiftTransferInfo, $arrData);
        if ($this->db_ma->affected_rows() > 0)
        {
            return true;
        }
        return false;
    }
    //-------------------------------------------------------------------------------------
    function UpdateStatusShiftTransferInfo($dToShiftDate, $iToShiftId, $iOldStatus,
        $iNewStatus)
    {

        $arrData = array('status' => $iNewStatus);
        $this->db_ma->where('to_shift_date', $dToShiftDate);
        $this->db_ma->where('to_shift_id', $iToShiftId);
        $this->db_ma->where('status', $iOldStatus);
        $this->db_ma->update($this->tblShiftTransferInfo, $arrData);
        $e = $this->db_ma->_error_message();
        if ($this->db_ma->affected_rows() > 0)
        {
            return true;
        }
        return false;
    }
    //-------------------------------------------------------------------------------------
    function UpdateIsInShiftScheduleAssign($dShiftDate, $iShiftId)
    {

        $arrData = array('is_in_shift' => 0, 'in_shift_end' => date('Y-m-d H:i:s'));

        $this->db_ma->where('is_in_shift', 1);
        $this->db_ma->update($this->tblShiftScheduleAssign, $arrData);

        $arrData = array('is_in_shift' => 1, 'in_shift_begin' => date('Y-m-d H:i:s'));

        $this->db_ma->where('date', $dShiftDate);
        $this->db_ma->where('shiftschedule_id', $iShiftId);
        $this->db_ma->update($this->tblShiftScheduleAssign, $arrData);
        $e = $this->db_ma->_error_message();
        if ($this->db_ma->affected_rows() > 0)
        {
            return true;
        }
        return false;
    }
    //-------------------------------------------------------------------------------------
    function GetListOpenIncident()
    {
        $this->db_ma->where('internal_status', null);
        $this->db_ma->from($this->tblIncidentFollow);
        $query = $this->db_ma->get();
        $result = $query->result();
        return $result;
    }
    //-------------------------------------------------------------------------------------
    function UpdateFollowShiftIdIncidentFollow($iFollowShiftId)
    {

        $arrData = array('follow_shift_id' => $iFollowShiftId);

        $this->db_ma->where('internal_status', null);
        $this->db_ma->update($this->tblIncidentFollow, $arrData);
        $e = $this->db_ma->_error_message();
        if ($this->db_ma->affected_rows() > 0)
        {
            return true;
        }
        return false;
    }
    //-------------------------------------------------------------------------------------
    function InsertIncidentHistory($iIncId, $iHandleShiftId, $dHandleShiftDate)
    {

        $arrData = array(
            'incident_id' => $iIncId,
            'handle_shift_id' => $iHandleShiftId,
            'handle_shift_date' => $dHandleShiftDate,
            'created_date' => date("Y-m-d", time()));
        $this->db_ma->insert($this->tblIncidentHistory, $arrData);
        $e = $this->db_ma->_error_message();
        if ($this->db_ma->affected_rows() > 0)
        {
            return true;
        }
        return false;
    }
    //-------------------------------------------------------------------------------------
    function GetListOpenChangeFollow()
    {
        $this->db_ma->where('internal_status', null);
        $this->db_ma->from($this->tblChangeFollow);
        $query = $this->db_ma->get();
        $result = $query->result();
        return $result;
    }
    //-------------------------------------------------------------------------------------
    function UpdateFollowShiftIdtblChangeFollow($iFollowShiftId)
    {

        $arrData = array('follow_shift_id' => $iFollowShiftId);

        $this->db_ma->where('internal_status', null);
        $this->db_ma->update($this->tblChangeFollow, $arrData);
        $e = $this->db_ma->_error_message();
        if ($this->db_ma->affected_rows() > 0)
        {
            return true;
        }
        return false;
    }
    //-------------------------------------------------------------------------------------
    function InsertChangeHistory($iIncId, $iHandleShiftId, $dHandleShiftDate)
    {

        $arrData = array(
            'change_id' => $iIncId,
            'handle_shift_id' => $iHandleShiftId,
            'handle_shift_date' => $dHandleShiftDate,
            'created_date' => date("Y-m-d", time()));
        $this->db_ma->insert($this->tblChangeHistory, $arrData);
        $e = $this->db_ma->_error_message();
        if ($this->db_ma->affected_rows() > 0)
        {
            return true;
        }
        return false;
    }
    //-------------------------------------------------------------------------------------
    function UpdateIncFollowLinkedAlerts($strITSMId, $arrLinkedAlert)
    {
        $this->db_ma->where('itsm_incident_id', $strITSMId);
        $this->db_ma->from($this->tblIncidentFollow);
        $query = $this->db_ma->get();
        if ($query->num_rows() > 0)
        {
            $oIncFollow = $query->row();
            $arrCurrLinkedAlert = json_decode($oIncFollow->linked_alerts, true);
            if (count($arrCurrLinkedAlert) > 0)
            {
                foreach ($arrCurrLinkedAlert as $rowCurrLinkedAlert)
                {
                    foreach ($arrLinkedAlert as $iLinkedAlertKey => $rowLinkedAlert)
                    {
                        if ($rowLinkedAlert['src_from'] == $rowCurrLinkedAlert['src_from'] && $rowLinkedAlert['src_id'] ==
                            $rowCurrLinkedAlert['src_id'])
                        {
                            // pd($iLinkedAlertKey);
                            unset($arrLinkedAlert[$iLinkedAlertKey]);
                            break;
                        }
                    }
                }
                foreach ($arrLinkedAlert as $rowLinkedAlert)
                {
                    $arrCurrLinkedAlert[] = $rowLinkedAlert;
                }

                $arrData = array('linked_alerts' => json_encode($arrCurrLinkedAlert));
            } else
            {
                $arrData = array(
                    'source_id' => $arrLinkedAlert[0]['src_id'],
                    'source_from' => $arrLinkedAlert[0]['src_from'],
                    'linked_alerts' => json_encode($arrLinkedAlert));
            }
        }

        foreach ($arrLinkedAlert as $rowLinkedAlert)
        {
            $this->RemoveAlertFromInc($rowLinkedAlert['src_from'], $rowLinkedAlert['src_id']);
        }

        $this->db_ma->where('itsm_incident_id', $strITSMId);
        $this->db_ma->update($this->tblIncidentFollow, $arrData);
        $e = $this->db_ma->_error_message();
        if ($this->db_ma->affected_rows() > 0)
        {
            return true;
        }
        return false;
    }
    //-------------------------------------------------------------------------------------
    function IsIncFollowExist($strITSMId)
    {
        $this->db_ma->where('itsm_incident_id', $strITSMId);
        $this->db_ma->from($this->tblIncidentFollow);
        $query = $this->db_ma->get();
        if ($query->num_rows() > 0)
        {
            return true;
        }
        return false;
    }
    //-------------------------------------------------------------------------------------
    function GetIncFollowList()
    {
        $this->db_ma->from($this->tblIncidentFollow);
        $query = $this->db_ma->get();
        $arrResult = $query->result_array();
        return $arrResult;
    } //-------------------------------------------------------------------------------------
    function RemoveAlertFromInc($srtSrcFrom, $strSrcId)
    {
        $arrCurrLinkedAlert = array();
        $arrLinkedAlert = array();
        //=======================Find record============================
        $strSrcInfo = '{"src_from":"' . $srtSrcFrom . '","src_id":"' . $strSrcId . '"}';
        $this->db_ma->like('linked_alerts', $strSrcInfo);
        $this->db_ma->from($this->tblIncidentFollow);
        $query = $this->db_ma->get();
        if ($query->num_rows() > 0)
        {
            $oIncFollow = $query->row();
            $arrCurrLinkedAlert = json_decode($oIncFollow->linked_alerts, true);
            //====================Remove Alert info=======================
            if (!empty($arrCurrLinkedAlert))
            {
                foreach ($arrCurrLinkedAlert as $rowCurrLinkedAlert)
                {
                    if ($srtSrcFrom != $rowCurrLinkedAlert['src_from'] || $strSrcId != $rowCurrLinkedAlert['src_id'])
                    {
                        // pd($iLinkedAlertKey);
                        // unset($arrCurrLinkedAlert[$Key]);
                        // break;
                        $arrLinkedAlert[] = $rowCurrLinkedAlert;
                    }
                }
                $arrData = array('linked_alerts' => json_encode($arrLinkedAlert));
                //===================Update Linked Alerts============================
                $this->db_ma->where('itsm_incident_id', $oIncFollow->itsm_incident_id);
                $this->db_ma->update($this->tblIncidentFollow, $arrData);
                $e = $this->db_ma->_error_message();
                if ($this->db_ma->affected_rows() > 0)
                {
                    return true;
                }
                return false;
            }
        }
        return false;
    }
    // ------------------------------------------------------------------------------------------ //
    public function GetAllNewRootCause()
    {
        $this->db_ma->select('*');
        $this->db_ma->from($this->tblNewRootCause);
        $query = $this->db_ma->get();

        if ($query->num_rows() > 0)
        {
            $result = $query->result();
            return $result;
        } else
            return null;
    }
    // ------------------------------------------------------------------------------------------ //
    public function GetAllDetector()
    {
        $this->db_ma->select('*');
        $this->db_ma->from($this->tblDetector);
		$this->db_ma->where(array('delete' => NO));
        $query = $this->db_ma->get();

        if ($query->num_rows() > 0)
        {
            $result = $query->result();
            return $result;
        } else
            return null;
    }
    // ------------------------------------------------------------------------------------------ //
    public function GetAllCauseExternalDept()
    {
        $this->db_ma->select('*');
        $this->db_ma->from($this->tblCauseExternalDept);
        $query = $this->db_ma->get();

        if ($query->num_rows() > 0)
        {
            $result = $query->result();
            return $result;
        } else
            return null;
    }
    // ------------------------------------------------------------------------------------------ //
    public function GetAllLocation()
    {
        $this->db_ma->select('*');
        $this->db_ma->from($this->tblLocation);
        $query = $this->db_ma->get();

        if ($query->num_rows() > 0)
        {
            $result = $query->result();
            return $result;
        } else
            return null;
    }
    // ------------------------------------------------------------------------------------------ //
    public function GetAllAssignees()
    {
        $strSql = "SELECT a.*,u.userid AS userid FROM " . $this->tblAssignee . " AS a ";
        $strSql .= "LEFT JOIN (SELECT userid, f_get_simple_username(email) AS username, email, mobile FROM " .
            $this->tblUser . ") u ";
        $strSql .= "ON( a.name = u.username) ORDER BY a.assignment_group, a.name";

        $oQuery = $this->db_ma->query($strSql);

        if ($oQuery->num_rows() > 0)
        {
            $oResult = $oQuery->result();
            return $oResult;
        } else
            return null;
    }
	// ------------------------------------------------------------------------------------------ //
	public function StopAlertIncidentClosedBySE($strIncidentId) {
		$arrData = array();
		$arrData['notify_closed_by_se'] = '0';
		$this->db_ma->where('itsm_incident_id', $strIncidentId);
        $this->db_ma->update($this->tblIncidentFollow, $arrData);
        if ($this->db_ma->affected_rows() > 0)
        {
            return true;
        }
        return false;
	}
	// ------------------------------------------------------------------------------------------ //
	public function GetCurrentIncidents($isCurrentShift, $strShiftDate, $iShiftId) {
		$strSQL = '';
		if($isCurrentShift) {
			$strSQL = '(SELECT ifl.* 
					FROM '. $this->tblIncidentFollow . ' ifl 
					WHERE LOWER(`status`) NOT IN("closed","resolved","rejected") AND (internal_status IS NULL OR internal_status="") 
					ORDER BY itsm_incident_id DESC)
					UNION
					(SELECT ifl.*
					FROM '. $this->tblIncidentFollow . ' ifl 
					WHERE ((internal_status = "closed" AND LOWER(STATUS)!="rejected") OR LOWER(STATUS) = "closed") AND (AREA IS NULL OR AREA="" OR subarea IS NULL OR subarea="")
					ORDER BY itsm_incident_id DESC)
					UNION 
					(
					SELECT ifl.* 
					FROM '. $this->tblIncidentFollow . ' ifl 
					WHERE internal_status="closed" AND closed_by_se="1" AND notify_closed_by_se="1" 
					ORDER BY itsm_incident_id DESC
					)';
			
		} else {
			$strSQL = 'SELECT ifl.*
					FROM '. $this->tblIncidentHistory .' ih INNER JOIN '. $this->tblIncidentFollow .' ifl 
					ON(ih.incident_id=ifl.id) WHERE ih.handle_shift_date="'. $strShiftDate .'" AND ih.handle_shift_id=' . $iShiftId . ' ORDER BY itsm_incident_id DESC';
		}
		$oQuery = $this->db_ma->query($strSQL);

        if ($oQuery->num_rows() > 0) {
            $oResult = $oQuery->result();
            return $oResult;
        }
            return null;
	}
	// ------------------------------------------------------------------------------------------ //
	public function GetListProductKB() {
		$this->db_ma->select('product_id, product_name')->distinct();
		$oQuery = $this->db_ma->get($this->tblSdkServiceSupport);
		if ($oQuery->num_rows() > 0) {
			return $oQuery->result();
		}
		return null;
		
	}
	// ------------------------------------------------------------------------------------------ //
	public function GetKnowledgeBaseByProduct($iProductId) {
		$this->db_ma->where('product_id', $iProductId);
		$oQuery = $this->db_ma->get($this->tblSdkServiceSupport);
		if ($oQuery->num_rows() > 0) {
			return $oQuery->result();
		}
		return null;
	}
	// ------------------------------------------------------------------------------------------ //
	public function getKBLinkByKbId($iKbId) {
		$this->db_ma->where('id', $iKbId);
		$oQuery = $this->db_ma->get($this->tblSdkServiceSupport);
		if ($oQuery->num_rows() > 0) {
			return $oQuery->row();
		}
		return null;
	}
	// ------------------------------------------------------------------------------------------ //
	public function UpdateNumOfCallFromInc($iCallStatus, $strIncidentId, $iNumOfCall) {
		$arrData = array();
		if($iCallStatus === YES) { //call success
			$arrData['number_of_call_success'] = $iNumOfCall;
		} elseif($iCallStatus === NO) { //call fail
			$arrData['number_of_call_fail'] = $iNumOfCall;
		}
		if(!empty($arrData)) {
			$this->db_ma->where('itsm_incident_id', $strIncidentId);
			$this->db_ma->update($this->tblIncidentFollow, $arrData);
			// p($this->db_ma->last_query());
			// pd($this->db_ma->affected_rows());
			return $this->db_ma->affected_rows() > 0;	
		}
		
	}
	// ------------------------------------------------------------------------------------------ //
	public function getIncidentIdByAlert($strAlertId) {
		$this->db_ma->select('itsm_incident_id');
		$this->db_ma->where('alert_id', $strAlertId)->limit(1);
		$oQuery = $this->db_ma->get($this->tblIncidentCreateHistory);
		if ($oQuery->num_rows() > 0) {
			return $oQuery->row();
		}
		return null;
	}
	// ------------------------------------------------------------------------------------------ //
}

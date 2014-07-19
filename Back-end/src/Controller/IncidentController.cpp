#include "IncidentController.h"
#include "../Model/BaseModel.h"
#include "../Common/DBCommon.h"


CIncidentController::CIncidentController(void)
{
}
CIncidentController::~CIncidentController(void)
{
}

bool CIncidentController::InsertCSImpact(IncUpdateHistoryInfo stIncUpdHis)
{
	stringstream strValue;
	try{
		if(!IsCSImpactExisted(stIncUpdHis)){
			strValue <<"'"<<stIncUpdHis.strItsmIncId<<"', 0, 0, "<<stIncUpdHis.iImpactLvl<<", "<<stIncUpdHis.iCustomerCase;
			string strQuery = "INSERT INTO `incident_update_history`"
								" (`itsm_incident_id`,"
								" `sdk_update_to_itsm_status`,"
								" `sdk_update_to_itsm_count`,"
								" `impact_level`,"
								" `customer_case`)"
								" VALUES"
								" ("+strValue.str()+");";
			CUtilities::WriteErrorLog(strQuery);
			if(!Query(strQuery.c_str()))
			{
				return false;
			}
			return true;
		}
		return UpdateCSImpact(stIncUpdHis);
	}
	catch(exception& ex)
	{
		string strLog;
		stringstream strErrorMess;
		strErrorMess<<ex.what()<<"][";
		strErrorMess<< __FILE__<< "|" << __LINE__;
		strLog = CUtilities::FormatLog(BUG_MSG, "IncidentController", "InsertCSImpact",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	return false;
}

bool CIncidentController::UpdateCSImpact(IncUpdateHistoryInfo stIncUpdHis)
{
	stringstream strValue;
	try{
		strValue << "`sdk_update_to_itsm_status` = " << stIncUpdHis.iSdkUpdateToItsmStatus<< ",";
		strValue << " `impact_level` = " << stIncUpdHis.iImpactLvl << ",";
		strValue << " `customer_case` = " << stIncUpdHis.iCustomerCase;
		string strQuery = "UPDATE `incident_update_history`"
							" SET " + strValue.str() + " WHERE "
							  "`itsm_incident_id` = '"+stIncUpdHis.strItsmIncId+"';";
		CUtilities::WriteErrorLog(strQuery);
		if(!Query(strQuery.c_str()))
		{
			return false;
		}
		return true;
	}
	catch(exception& ex)
	{
		string strLog;
		stringstream strErrorMess;
		strErrorMess<<ex.what()<<"][";
		strErrorMess<< __FILE__<< "|" << __LINE__;
		strLog = CUtilities::FormatLog(BUG_MSG, "IncidentController", "UpdateCSImpact",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	return false;
}

bool CIncidentController::IsCSImpactExisted(IncUpdateHistoryInfo stIncUpdHis)
{
	try{
		string strQuery = "SELECT * FROM `incident_update_history` WHERE `itsm_incident_id` = '"+ stIncUpdHis.strItsmIncId +"' LIMIT 1";
		SelectQuery(strQuery.c_str());
		if(m_pResult->row_count==0)
		{
			return false;
		}
		return true;
	}
	catch(exception& ex)
	{
		string strLog;
		stringstream strErrorMess;
		strErrorMess<<ex.what()<<"][";
		strErrorMess<< __FILE__<< "|" << __LINE__;
		strLog = CUtilities::FormatLog(BUG_MSG, "IncidentController", "IsCSImpactExisted",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	return false;
}

bool CIncidentController::IsCSAlertCreateInc(string strItsmId, string strSourceId)
{
	try{
		string strQuery = "SELECT * FROM `incident_follow` WHERE `source_from` = 'CS' AND `source_id` = '"+ strSourceId +"' AND `itsm_incident_id` = '"+ strItsmId +"' AND REPLACE(`linked_alerts`, ' ', '') LIKE '%{\"src_from\":\"CS\",\"src_id\":\""+ strSourceId +"\"}%'";
		SelectQuery(strQuery.c_str());
		if(m_pResult->row_count==0)
		{
			return false;
		}
		return true;
	}
	catch(exception& ex)
	{
		string strLog;
		stringstream strErrorMess;
		strErrorMess<<ex.what()<<"][";
		strErrorMess<< __FILE__<< "|" << __LINE__;
		strLog = CUtilities::FormatLog(BUG_MSG, "IncidentController", "IsCSAlertCreateInc",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	return false;
}

bool CIncidentController::NotiImpactLvlUp(string strSrcFrom, string strTicketId, int iImpactLevel)
{
	stringstream strValue;
	try{
		strValue <<"'"<<strSrcFrom<<"', '"<<strTicketId<<"', 'Impact level up', 'Impact level up', "<<iImpactLevel<<", f_get_current_shift_date(), f_get_current_shift(), '"<< CUtilities::GetCurrTime() <<"' ,'"<< CUtilities::GetCurrTime() <<"' ,'OPEN'";
		string strQuery = "INSERT INTO `notification`"
							" (`notification_type`,"
							" `ref_id`,"
							" `content`,"
							" `action`,"
							" `level_impacted`,"
							" `shift_date`,"
							" `shift_id`,"
							" `created_date`,"
							" `last_updated`,"
							" `status`)"
							" VALUES"
							" ("+strValue.str()+");";
		CUtilities::WriteErrorLog(strQuery);
		if(!Query(strQuery.c_str()))
		{
			return false;
		}
		return true;
	}
	catch(exception& ex)
	{
		string strLog;
		stringstream strErrorMess;
		strErrorMess<<ex.what()<<"][";
		strErrorMess<< __FILE__<< "|" << __LINE__;
		strLog = CUtilities::FormatLog(BUG_MSG, "IncidentController", "NotiImpactLvlUp",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	return false;
}

#include "NotificationController.h"
#include "../Model/BaseModel.h"
#include "../Common/DBCommon.h"


CNotificationController::CNotificationController(void)
{
}

CNotificationController::~CNotificationController(void)
{
}

bool CNotificationController::InsertIncidentFollowNoti(string strItsmId, int iEscLvl, int iImpactLvl)
{
	
	stringstream strValue;
	try{
		strValue <<"'"<<strItsmId<<"', '"<<strItsmId<<"', 'Update impact-level - Lần "<<iEscLvl<<"', f_get_current_shift_date(), f_get_current_shift(), "
		<<iEscLvl<<", "<<iImpactLvl<<", '"<<CUtilities::GetCurrTime()<<"', 'OPEN'";
		string strQuery = "INSERT INTO `sdk_follow_incident_notification`"
							" (`ref_id`," // itsm_id
							" `content`," // itsm_id
							" `action`," 
							" `shift_date`,"
							" `shift_id`," 
							" `level_escalation`," // esc level
							" `level_impacted`," // impact lvl
							" `created_date`," 
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
		strLog = CUtilities::FormatLog(BUG_MSG, "IncidentController", "InsertCSImpact",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	return false;
}

bool CNotificationController::InsertIncidentFollowSEReportNoti(string strItsmId, int iEscLvl, int iImpactLvl)
{
	
	stringstream strValue;
	try{
		if(iEscLvl<=8)
		{
			strValue <<"'"<<strItsmId<<"', '"<<strItsmId<<"', 'Remind SE Update Incident Report - Lần "<<iEscLvl<<"', f_get_current_shift_date(), f_get_current_shift(), "
			<<iEscLvl<<", "<<iImpactLvl<<", '"<<CUtilities::GetCurrTime()<<"', 'OPEN'";
		}
		else
		{
			strValue <<"'"<<strItsmId<<"', '"<<strItsmId<<"', 'Escalate về việc SE chưa update Incident Report trong 72h', f_get_current_shift_date(), f_get_current_shift(), "
			<<iEscLvl<<", "<<iImpactLvl<<", '"<<CUtilities::GetCurrTime()<<"', 'OPEN'";
		}
		string strQuery = "INSERT INTO `se_report_incident_notification`"
							" (`ref_id`," // itsm_id
							" `content`," // itsm_id
							" `action`," 
							" `shift_date`,"
							" `shift_id`," 
							" `level_escalation`," // esc level
							" `level_impacted`," // impact lvl
							" `created_date`," 
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
		strLog = CUtilities::FormatLog(BUG_MSG, "IncidentController", "InsertCSImpact",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	return false;
}


bool CNotificationController::CloseNotiIncidentNotOpen(vector<string> v_strItsmId)
{
	try{
		string strValue = "";
		if(v_strItsmId.size() == 0)
			return false;
		for(int i = 0; i < v_strItsmId.size(); i++)
		{
			strValue = strValue + "'" + v_strItsmId[i] + "'";
			if(i != v_strItsmId.size() - 1)
				strValue = strValue + ",";
		}
		string strQuery = "UPDATE `sdk_follow_incident_notification` SET `status` = 'CLOSE' WHERE `content` NOT IN ("
							+ strValue + " )";
		if(!Query(strQuery.c_str()))
		{
			CUtilities::WriteErrorLog(CUtilities::FormatLog(LOG_MSG, "CNotificationController", "CloseNotiIncidentNotOpen", "FAIL_QUERY:"+strQuery));
			return false;
		}
		else if(AffectedRows() == 0)
		{
			CUtilities::WriteErrorLog(CUtilities::FormatLog(LOG_MSG, "CNotificationController", "CloseNotiIncidentNotOpen", "NO_ROWS_AFFECTED:"+strQuery));
			return false;
		}
		string strLog;
		strLog = CUtilities::FormatLog(LOG_MSG, "CNotificationController", "CloseNotiIncidentNotOpen",strQuery);
		CUtilities::WriteErrorLog(strLog);
		return true;
	}
	catch(exception& ex)
	{
		string strLog;
		stringstream strErrorMess;
		strErrorMess<<ex.what()<<"][";
		strErrorMess<< __FILE__<< "|" << __LINE__;
		strLog = CUtilities::FormatLog(LOG_MSG, "CNotificationController", "CloseNotiIncidentNotOpen",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	return false;
}

bool CNotificationController::CloseNotiSEReported(vector<string> v_strItsmId)
{
	try{
		string strValue = "";
		if(v_strItsmId.size() == 0)
			return false;
		for(int i = 0; i < v_strItsmId.size(); i++)
		{
			strValue = strValue + "'" + v_strItsmId[i] + "'";
			if(i != v_strItsmId.size() - 1)
				strValue = strValue + ",";
		}
		string strQuery = "UPDATE `se_report_incident_notification` SET `status` = 'CLOSE' WHERE `content` NOT IN ("
							+ strValue + " )";
		if(!Query(strQuery.c_str()))
		{
			CUtilities::WriteErrorLog(CUtilities::FormatLog(LOG_MSG, "CNotificationController", "CloseNotiSEReported", "FAIL_QUERY:"+strQuery));
			return false;
		}
		else if(AffectedRows() == 0)
		{
			CUtilities::WriteErrorLog(CUtilities::FormatLog(LOG_MSG, "CNotificationController", "CloseNotiSEReported", "NO_ROWS_AFFECTED:"+strQuery));
			return false;
		}
		string strLog;
		strLog = CUtilities::FormatLog(LOG_MSG, "CNotificationController", "CloseNotiSEReported",strQuery);
		CUtilities::WriteErrorLog(strLog);
		return true;
	}
	catch(exception& ex)
	{
		string strLog;
		stringstream strErrorMess;
		strErrorMess<<ex.what()<<"][";
		strErrorMess<< __FILE__<< "|" << __LINE__;
		strLog = CUtilities::FormatLog(LOG_MSG, "CNotificationController", "CloseNotiSEReported",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	return false;
}

bool CNotificationController::CloseNotiIncidentByItsmId(string strItsmId)
{
	try{
		string strQuery = "UPDATE `sdk_follow_incident_notification` SET `status` = 'CLOSE' WHERE `status` = 'OPEN' AND `content` = '"+ strItsmId + "'";
		if(!Query(strQuery.c_str()))
		{
			CUtilities::WriteErrorLog(CUtilities::FormatLog(LOG_MSG, "CNotificationController", "CloseNotiIncidentByItsmId", "FAIL_QUERY:"+strQuery));
			return false;
		}
		else if(AffectedRows() == 0)
		{
			CUtilities::WriteErrorLog(CUtilities::FormatLog(LOG_MSG, "CNotificationController", "CloseNotiIncidentByItsmId", "NO_ROWS_AFFECTED:"+strQuery));
			return false;
		}
		string strLog;
		strLog = CUtilities::FormatLog(LOG_MSG, "CNotificationController", "CloseNotiIncidentByItsmId",strQuery);
		CUtilities::WriteErrorLog(strLog);
		return true;
	}
	catch(exception& ex)
	{
		string strLog;
		stringstream strErrorMess;
		strErrorMess<<ex.what()<<"][";
		strErrorMess<< __FILE__<< "|" << __LINE__;
		strLog = CUtilities::FormatLog(LOG_MSG, "CNotificationController", "CloseNotiIncidentByItsmId",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	return false;
}
bool CNotificationController::CloseNotiSEByItsmId(string strItsmId)
{
	
	try{
		string strQuery = "UPDATE `se_report_incident_notification` SET `status` = 'CLOSE' WHERE `status` = 'OPEN' AND `content` = '" + strItsmId + "'";
		if(!Query(strQuery.c_str()))
		{
			CUtilities::WriteErrorLog(CUtilities::FormatLog(LOG_MSG, "CNotificationController", "CloseNotiSEByItsmId", "FAIL_QUERY:"+strQuery));
			return false;
		}
		else if(AffectedRows() == 0)
		{
			CUtilities::WriteErrorLog(CUtilities::FormatLog(LOG_MSG, "CNotificationController", "CloseNotiSEByItsmId", "NO_ROWS_AFFECTED:"+strQuery));
			return false;
		}
		string strLog;
		strLog = CUtilities::FormatLog(LOG_MSG, "CNotificationController", "CloseNotiSEByItsmId",strQuery);
		CUtilities::WriteErrorLog(strLog);
		return true;
	}
	catch(exception& ex)
	{
		string strLog;
		stringstream strErrorMess;
		strErrorMess<<ex.what()<<"][";
		strErrorMess<< __FILE__<< "|" << __LINE__;
		strLog = CUtilities::FormatLog(LOG_MSG, "CNotificationController", "CloseNotiSEByItsmId",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	return false;
}
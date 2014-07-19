#include "IncidentFollowController.h"
#include "../Model/BaseModel.h"
#include "../Common/DBCommon.h"


CIncidentFollowController::CIncidentFollowController(void)
{
	m_strTableName = ".incident_follow";
}

CIncidentFollowController::CIncidentFollowController(string strDBName)
{
	m_strTableName = strDBName + ".incident_follow";
}

CIncidentFollowController::~CIncidentFollowController(void)
{
}

bool CIncidentFollowController::FindIncidentInDay()
{
	try{
		string strQuery = "SELECT * FROM `incident_follow` WHERE `itsm_last_update_time` > DATE_SUB(NOW(), INTERVAL 1 DAY)";
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
		strLog = CUtilities::FormatLog(BUG_MSG, "IncidentFollowController", "FindIncidentInDay",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
}

bool CIncidentFollowController::FindIncidentOpen()
{
	try{
		string strQuery = "SELECT *, UNIX_TIMESTAMP(outage_start) AS unix_outage_start,`outage_end` FROM `incident_follow` WHERE `status` = 'Open' AND `auto_update_impact_level` != 1 AND `outage_end` IS NULL";
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
		strLog = CUtilities::FormatLog(BUG_MSG, "IncidentFollowController", "FindIncidentInDay",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
}

bool CIncidentFollowController::FindIncidentHighLevelWithoutSEReport()
{
	try{
		string strQuery = "SELECT * , UNIX_TIMESTAMP(outage_start) AS unix_outage_start FROM `incident_follow` WHERE `status` = 'Open' AND `impact_level` IN (1, 2) AND `se_reported` IS NULL OR `se_reported` != '1'";
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
		strLog = CUtilities::FormatLog(BUG_MSG, "IncidentFollowController", "FindIncidentInDay",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
}

void CIncidentFollowController::ResetModel()
{
	m_pModel->Reset();
	if(m_pResult!=NULL)
	{
		mysql_free_result(m_pResult);
		m_pResult = NULL;
	}
}
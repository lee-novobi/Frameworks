#include "ZabbixController.h"
#include "../Model/BaseModel.h"
#include "../Common/DBCommon.h"


CZabbixController::CZabbixController(void)
{
}

CZabbixController::~CZabbixController(void)
{
}

bool CZabbixController::FindDeletedTrigger()
{
	long long lCurrClock = CUtilities::UnixTimeFromString(CUtilities::GetCurrTime());
	string strClock = CUtilities::ConvertLongToString(lCurrClock - (SEC_PER_HOUR/2));
	try{
		string strQuery = "SELECT * FROM `auditlog` WHERE `clock` > " + strClock + " AND `action` = 2 AND `resourcetype`= 13";
		SelectQuery(strQuery.c_str());
		if(m_pResult->row_count==0)
		{
			return false;
		}
		string strLog;
		strLog = CUtilities::FormatLog(BUG_MSG, "CDeleteTriggerProcess", "FindDeletedTrigger",strQuery);
		CUtilities::WriteErrorLog(strLog);
		return true;
	}
	catch(exception& ex)
	{
		string strLog;
		stringstream strErrorMess;
		strErrorMess<<ex.what()<<"][";
		strErrorMess<< __FILE__<< "|" << __LINE__;
		strLog = CUtilities::FormatLog(BUG_MSG, "CDeleteTriggerProcess", "FindDeletedTrigger",strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
}

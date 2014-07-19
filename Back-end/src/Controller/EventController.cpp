#include "EventController.h"
#include "../Common/DBCommon.h"

CEventController::CEventController(void)
{
	m_strTableName = ".events";
}

CEventController::~CEventController(void)
{
}

bool CEventController::UpdateSynced(string strSourceId)
{
	string strLog;
	if(m_bIsConnected)
	{
		try
		{
			m_connDB.update(m_strTableName, QUERY(RECORD_ID<<OID(strSourceId)), BSON("$set"<<BSON(IS_SYNC<<1)), false, true);
			return true;
		}
		catch(exception& ex)
		{	
			stringstream strErrorMess;
			strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
			strLog = CUtilities::FormatLog(BUG_MSG, "CEventController", "UpdateSynced","exception:" + strErrorMess.str());
			CUtilities::WriteErrorLog(strLog);
		}
	}
	return false;
}
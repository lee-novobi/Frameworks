#include "TriggerController.h"
#include "../Common/DBCommon.h"

CTriggerController::CTriggerController(void)
{
	m_strTableName = ".triggers";
}

CTriggerController::~CTriggerController(void)
{
}

bool CTriggerController::DeleteTriggerByVTriggerId(vector<long long> v_lTriggerId, int iLocation)
{
	BSONArrayBuilder babTriggerId;
	if(m_bIsConnected)
	{
		try
		{
			if(!v_lTriggerId.empty())
			{
				for(int i = 0; i < v_lTriggerId.size(); i++)
					babTriggerId << v_lTriggerId[i];
				m_connDB.remove(m_strTableName, QUERY(ZBX_SERVER_ID<<iLocation<<TRIGGER_ID<<BSON("$in"<<babTriggerId.arr())));
			}
			return true;
		}
		catch(exception& ex)
		{
			stringstream strErrorMess;
			strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
			CUtilities::WriteErrorLog(strErrorMess.str());
		}
	}
	return false;
}


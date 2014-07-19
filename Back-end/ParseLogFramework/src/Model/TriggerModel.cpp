#include "TriggerModel.h"
#include "../Common/DBCommon.h"
CTriggerModel::CTriggerModel(void)
{
}

CTriggerModel::~CTriggerModel(void)
{
}

BSONObj CTriggerModel::GetUniqueTriggerBson()
{
	BSONObj Obj = m_pRecordBuilder->asTempObj();
	BSONObj bsonQueryResult = BSON(ZBX_SERVER_ID<<m_iZbxServerId<<TRIGGER_ID<<m_lTriggerId);
	return bsonQueryResult;
}


void CTriggerModel::PrepareRecord()
{
	try{
		m_pRecordBuilder->append(CLOCK, m_lClock);
		m_pRecordBuilder->append(ZBX_SERVER_ID, m_iZbxServerId);
		m_pRecordBuilder->append(STATUS, m_iStatus);
		m_pRecordBuilder->append(TRIGGER_ID, m_lTriggerId);
		m_pRecordBuilder->append(PARA_VALUE, m_iValue);
		m_pRecordBuilder->append(PRIORITY, m_iPriority);
		m_pRecordBuilder->append(EXPRESSION, m_strExpression);
		m_pRecordBuilder->append(DESCRIPTION, m_strDescription);
	}catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

void CTriggerModel::DestroyData()
{
	ReleaseBuilder();
	m_iZbxServerId = m_iStatus = m_iPriority = m_iValue = 0;
	m_lClock = m_lTriggerId = 0;
	m_strExpression = m_strDescription = "";
}
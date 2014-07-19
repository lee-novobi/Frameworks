#include "EventModel.h"
#include "../Common/DBCommon.h"
CEventModel::CEventModel(void)
{
}

CEventModel::~CEventModel(void)
{
}

BSONObj CEventModel::GetUniqueEventBson()
{
	BSONObj Obj = m_pRecordBuilder->asTempObj();
	BSONObj bsonQueryResult = BSON(ZBX_SERVER_ID<<m_iZbxServerId<<EVENT_ID<<m_lEventId);
	return bsonQueryResult;
}

void CEventModel::PrepareRecord()
{
	try{
		m_pRecordBuilder->append(CLOCK, m_lClock);
		m_pRecordBuilder->append(ZBX_SERVER_ID, m_iZbxServerId);
		m_pRecordBuilder->append(EVENT_ID, m_lEventId);
		m_pRecordBuilder->append(STATUS, m_iStatus);
		m_pRecordBuilder->append(TRIGGER_ID, m_lTriggerId);
		m_pRecordBuilder->append(HOST_ID, m_lHostId);
		m_pRecordBuilder->append(SERVER_ID, m_lServerId);
		m_pRecordBuilder->append(ITEM_ID, m_lItemId);
		m_pRecordBuilder->append(VALUE_CHANGED, m_iValueChanged);
		m_pRecordBuilder->append(IS_SYNC, 0);
	}catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

void CEventModel::DestroyData()
{
	ReleaseBuilder();
	m_iZbxServerId = m_iStatus = m_iValueChanged = 0;
	m_lClock = m_lEventId = m_lTriggerId = m_lHostId = m_lItemId = m_lServerId = 0; 
}
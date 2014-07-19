#include "WebInfoHistoryModel.h"
#include "../Common/DBCommon.h"
CWebInfoHistoryModel::CWebInfoHistoryModel(void)
{
}

CWebInfoHistoryModel::~CWebInfoHistoryModel(void)
{
}

BSONObj CWebInfoHistoryModel::GetKeysIndex()
{
	BSONObj bsonKeys = BSON(CLOCK<<1<<ITEM_ID<<1);
	return bsonKeys;
}

void CWebInfoHistoryModel::PrepareRecord()
{
	try{
		if(m_iFail != -1)
			m_pRecordBuilder->append(LAST_VALUE, m_iFail);
		if(m_iResCode != -1)
			m_pRecordBuilder->append(LAST_VALUE, m_iResCode);
		if(m_fSpeed != -1)
			m_pRecordBuilder->append(LAST_VALUE, m_fSpeed);
		if(m_fStepSpeed != -1)
			m_pRecordBuilder->append(LAST_VALUE, m_fStepSpeed);
		if(m_fResTime != -1)
			m_pRecordBuilder->append(LAST_VALUE, m_fResTime);
		if(m_strErrMsg != "")
			m_pRecordBuilder->append(LAST_VALUE, m_strErrMsg);
		
		if(!m_strWebKey.empty())
			m_pRecordBuilder->append(WEB_KEY, m_strWebKey);
		if(!m_strAppName.empty())
			m_pRecordBuilder->append(APP_NAME, m_strAppName);
		if(m_strUnit.compare("bps") == 0)
			m_pRecordBuilder->append(UNIT, m_strUnit);
		m_pRecordBuilder->append(STEP_NAME, m_strStepName);
		m_pRecordBuilder->append(CLOCK, m_lClock);
		m_pRecordBuilder->append(ITEM_ID, m_lItemId);
		m_pRecordBuilder->append(HOST_ID, m_lHostId);
		m_pRecordBuilder->append(SERVER_ID, m_lServerId);
		m_pRecordBuilder->append(ZBX_SERVER_ID, m_iZbxServerId);
		m_pRecordBuilder->append(HOST_NAME, m_strHostName);
	}catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

void CWebInfoHistoryModel::DestroyData()
{
	ReleaseBuilder();
	m_iFail = m_iResCode = m_iPreFail = m_iPreResCode = m_iZbxServerId = -1;
	m_fSpeed = m_fStepSpeed = m_fResTime = -1;
	m_fPreSpeed = m_fPreStepSpeed = m_fPreResTime = -1;
	m_lHostId = m_lServerId = m_lClock = -1;
	m_strUnit = m_strHostName = m_strWebKey = m_strErrMsg = m_strPreErrMsg = m_strAppName = m_strStepName = "";
}
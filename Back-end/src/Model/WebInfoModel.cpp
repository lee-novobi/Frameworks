#include "WebInfoModel.h"
#include "../Common/DBCommon.h"
CWebInfoModel::CWebInfoModel(void)
{
}

CWebInfoModel::~CWebInfoModel(void)
{
}

BSONObj CWebInfoModel::GetUniqueWebInfoBson()
{
	BSONObj Obj = m_pRecordBuilder->asTempObj();
	BSONObj bsonQueryResult = BSON(SERVER_ID<<m_lServerId<<ITEM_ID<<m_lItemId);
	return bsonQueryResult;
}

void CWebInfoModel::PrepareRecord()
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
		if(!m_strErrMsg.empty())
			m_pRecordBuilder->append(LAST_VALUE, m_strErrMsg);
			
		if(m_iPreFail != -1)
			m_pRecordBuilder->append(PRE_VALUE, m_iPreFail);
		if(m_iPreResCode != -1)
			m_pRecordBuilder->append(PRE_VALUE, m_iPreResCode);
		if(m_fPreSpeed != -1)
			m_pRecordBuilder->append(PRE_VALUE, m_fPreSpeed);
		if(m_fPreStepSpeed != -1)
			m_pRecordBuilder->append(PRE_VALUE, m_fPreStepSpeed);
		if(m_fPreResTime != -1)
			m_pRecordBuilder->append(PRE_VALUE, m_fPreResTime);
		if(!m_strPreErrMsg.empty())
			m_pRecordBuilder->append(PRE_VALUE, m_strPreErrMsg);
			
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

void CWebInfoModel::DestroyData()
{
	ReleaseBuilder();
	m_iFail = m_iResCode = m_iPreFail = m_iPreResCode = m_iZbxServerId = -1;
	m_fSpeed = m_fStepSpeed = m_fResTime = -1;
	m_fPreSpeed = m_fPreStepSpeed = m_fPreResTime = -1;
	m_lHostId = m_lServerId = m_lClock = -1;
	m_strUnit = m_strHostName = m_strWebKey = m_strErrMsg = m_strPreErrMsg = m_strAppName = m_strStepName = "";
}
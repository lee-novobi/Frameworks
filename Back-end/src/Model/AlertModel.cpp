#include "AlertModel.h"
#include "../Common/DBCommon.h"
CAlertModel::CAlertModel(void)
{
}

CAlertModel::~CAlertModel(void)
{
}

BSONObj CAlertModel::GetUniqueAlertBson()
{
	BSONObj Obj = m_pRecordBuilder->asTempObj();
	BSONObj bsonQueryResult = BSON(ZBX_SERVER_ID<<m_iZbxServerId<<EVENT_ID<<m_lEventId);
	return bsonQueryResult;
}


void CAlertModel::PrepareRecord()
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
		m_pRecordBuilder->append(CMDB_DEPT_ALIAS, m_strDeptAlias);
		m_pRecordBuilder->append(CMDB_PROD_ALIAS, m_strProdAlias);
		m_pRecordBuilder->append(HOST_NAME, m_strHost);
		m_pRecordBuilder->append(DESCRIPTION, m_strDescription);
		m_pRecordBuilder->append(KEY_, m_strKey);
		m_pRecordBuilder->append(ALERT_ID, m_iAlertId);
		m_pRecordBuilder->append(IS_SYNC, m_iIsSync);
		m_pRecordBuilder->append(PRIORITY, m_iPriority);
		m_pRecordBuilder->append(MAINTENANCE, m_iMaintenance);
	}catch(exception& ex)
	{
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

void CAlertModel::DestroyData()
{
	ReleaseBuilder();
	m_lTriggerId = m_lItemId = m_lHostId = m_lClock = m_lServerId = m_lEventId = 0;
	m_iMaintenance = m_iAlertId = m_iZbxServerId = m_iPriority = m_iStatus = m_iValueChanged = 0;
	m_strDescription = m_strKey = m_strHost = m_strDeptAlias = m_strProdAlias = "";
}
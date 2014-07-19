#include "AlertSyncModel.h"
#include "../Common/DBCommon.h"
CAlertSyncModel::CAlertSyncModel(void)
{
}

CAlertSyncModel::~CAlertSyncModel(void)
{
}

BSONObj CAlertSyncModel::GetUniqueAlertSyncBson()
{
	BSONObj bsonQueryResult = BSON(SOURCE_ID<<m_strSourceId<<SOURCE_FROM<<m_strSourceFrom<<IS_SHOW<<1);
	return bsonQueryResult;
}

BSONObj CAlertSyncModel::GetUniqueZbxAlertSyncBson()
{
	BSONObj bsonQueryResult = BSON(ZBX_SERVER_ID<<m_lZbxServerId<<ZBX_TRIGGER_ID<<m_lZbxTriggerId<<IS_SHOW<<1);
	return bsonQueryResult;
}

BSONObj CAlertSyncModel::GetServerIdZbxAlertSyncBson()
{
	BSONObj bsonQueryResult = BSON(ZBX_SERVER_ID<<m_lZbxServerId);
	return bsonQueryResult;
}

void CAlertSyncModel::PrepareRecord()
{
	long long lCreateDate = atol(CUtilities::GetCurrTimeStamp().c_str());
	try{
		m_pRecordBuilder->append(IS_SHOW, m_iIsShow);
		m_pRecordBuilder->append(CLOCK, m_lClock);
		m_pRecordBuilder->append(INTERNAL_STATUS, "");
		m_pRecordBuilder->append(EXTERNAL_STATUS, "");
		m_pRecordBuilder->append(NUM_OF_CASE, m_iNumOfCase);
		m_pRecordBuilder->append(IMPACT_LEVEL, m_iImpactLevel);
		m_pRecordBuilder->append(SOURCE_FROM, m_strSourceFrom);
		m_pRecordBuilder->append(SOURCE_ID, m_strSourceId);
		m_pRecordBuilder->append(TITLE, m_strTitle);
		m_pRecordBuilder->append(DESCRIPTION, m_strDescription);
		m_pRecordBuilder->append(DEPARTMENT, m_strDepartment);
		m_pRecordBuilder->append(PRODUCT, m_strProduct);
		m_pRecordBuilder->append(PRIORITY, m_strPriority);
		m_pRecordBuilder->append(ATTACHMENTS,m_strAttactment);
		m_pRecordBuilder->append(ALERT_MSG,m_strAlertMsg);
		m_pRecordBuilder->append(IS_ACKED,m_iIsAcked);
		m_pRecordBuilder->append(ACK_MSG,"");
		m_pRecordBuilder->append(ITSM_INC_ID,m_strItsmId);
		m_pRecordBuilder->append(UPDATE_DATE,"");
		m_pRecordBuilder->append(TICKET_ID,m_strTicketId);
		m_pRecordBuilder->append(ZBX_SERVER_ID,m_lZbxServerId);
		m_pRecordBuilder->append(ZBX_ZBX_SERVER_ID,m_iZbxZabbixServerId);
		m_pRecordBuilder->append(ZBX_HOST_ID,m_lZbxHostId);
		m_pRecordBuilder->append(ZBX_HOST,m_strZbxHost);
		m_pRecordBuilder->append(ZBX_ITEM_ID,m_lZbxItemId);
		m_pRecordBuilder->append(ZBX_ITEM_NAME,m_strZbxKey);
		m_pRecordBuilder->append(ZBX_EVENT_ID,m_lZbxEventId);
		m_pRecordBuilder->append(ZBX_TRIGGER_ID,m_lZbxTriggerId);
		m_pRecordBuilder->append(ZBX_TRIGGER_PRI,m_iZbxPriority);
		m_pRecordBuilder->append(ZBX_TRIGGER_DES,m_strZbxDescription);
		m_pRecordBuilder->append(CREATE_DATE, lCreateDate);
		m_pRecordBuilder->append(ZBX_MAINTENANCE, m_iZbxMaintenance);
		m_pRecordBuilder->append(AFFECTED_DEALS, m_strAffectedDeals);
	}catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}
	
void CAlertSyncModel::DestroyData()
{
	ReleaseBuilder();
	m_iIsAcked = 0;
	m_iIsShow = m_iNumOfCase = m_iImpactLevel = m_iZbxZabbixServerId = m_iZbxPriority = m_iZbxMaintenance = 0;
	m_lZbxTriggerId = m_lZbxItemId = m_lZbxHostId = m_lZbxEventId = m_lZbxServerId = m_lClock = 0;
	m_strSourceFrom = m_strSourceId = m_strTitle = m_strDescription = m_strDepartment = m_strProduct = m_strAttactment = m_strAlertMsg = m_strPriority = m_strAffectedDeals = m_strTicketId = m_strItsmId = "";
	m_strZbxDescription = m_strZbxKey = m_strZbxHost = "";
}

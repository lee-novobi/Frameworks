#include "HostWebModel.h"
#include "../Common/DBCommon.h"
CHostWebModel::CHostWebModel(void)
{
}

CHostWebModel::~CHostWebModel(void)
{
}

BSONObj CHostWebModel::GetUniqueHostWebBson()
{
	BSONObj bsonQueryResult = BSON(SERVER_ID<<m_lServerId);
	return bsonQueryResult;
}

void CHostWebModel::PrepareRecord()
{
	try{
		m_pRecordBuilder->append(IS_AVAILABLE, m_iAvailable);
		m_pRecordBuilder->append(STATUS, m_iStatus);
		m_pRecordBuilder->append(MAINTENANCE, m_iMaintenance);
		m_pRecordBuilder->append(MAINTENANCE_FROM, m_lMaintenanceFrom);
		m_pRecordBuilder->append(HOST_ID, m_lHostId);
		m_pRecordBuilder->append(SERVER_ID, m_lServerId);
		m_pRecordBuilder->append(ZBX_SERVER_ID, m_iZbxServerId);
		m_pRecordBuilder->append(HOST_NAME, m_strHost);
		m_pRecordBuilder->append(NAME, m_strName);
		m_pRecordBuilder->append(PRODUCT_ALIAS,"");
		m_pRecordBuilder->append(PRODUCT_CODE,"");
		m_pRecordBuilder->append(DEPARTMENT_ALIAS,"");
		m_pRecordBuilder->append(DEPARTMENT_CODE,"");
		m_pRecordBuilder->append(DELETE,m_iDelete);
	}catch(exception& ex)
	{	
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
		CUtilities::WriteErrorLog(strErrorMess.str());
	}
}

void CHostWebModel::DestroyData()
{
	ReleaseBuilder();
	m_iZbxServerId = m_iStatus = m_iAvailable = m_iMaintenance = m_iDelete = 0;
	m_lHostId = m_lMaintenanceFrom = m_lServerId = 0;
	m_strHost = m_strName = "";
}
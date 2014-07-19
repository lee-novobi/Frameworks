#include "ZbxAlertModel.h"
#include "../Common/DBCommon.h"

CZbxAlertModel::CZbxAlertModel(void)
{
}

CZbxAlertModel::~CZbxAlertModel(void)
{
}

void CZbxAlertModel::PrepareRecord()
{
	if(!m_strITSMStatus.empty())
		m_pRecordBuilder->append(ITSM_STATUS, m_strITSMStatus);
	if(m_iStatus != -1)
		m_pRecordBuilder->append(ITSM_STATUS_NOTI, m_iITSMSttNoti);
	if(m_iITSMSttNoti != -1)
		m_pRecordBuilder->append(STATUS, m_iStatus);
}

void CZbxAlertModel::DestroyData()
{
	ReleaseBuilder();
	m_iStatus = m_iITSMSttNoti = -1;
	m_strITSMStatus = "";
}
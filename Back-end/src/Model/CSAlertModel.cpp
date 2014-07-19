#include "CSAlertModel.h"
#include "../Common/DBCommon.h"
CCSAlertModel::CCSAlertModel(void)
{
}

CCSAlertModel::~CCSAlertModel(void)
{
}

void CCSAlertModel::PrepareRecord()
{
	if(!m_strITSMStatus.empty())
		m_pRecordBuilder->append(ITSM_STATUS, m_strITSMStatus);
	if(!m_strImpactUpdateTime.empty())
		m_pRecordBuilder->append(IMPACT_UPDATED_DATE_TIME, m_strImpactUpdateTime);
	if( m_iITSMSttNoti != -1)
		m_pRecordBuilder->append(ITSM_STATUS_NOTI, m_iITSMSttNoti);
	if(m_iStatus != -1)
		m_pRecordBuilder->append(STATUS, m_iStatus);
	if(m_iImpactLevel != -1)
		m_pRecordBuilder->append(IMPACT_LEVEL, m_iImpactLevel);
	if(m_iSdkItsmNoti != -1)
		m_pRecordBuilder->append(SDK_ITSM_NOTI, m_iSdkItsmNoti);
	if(m_lImpactUpdateUnixTime != -1)
		m_pRecordBuilder->append(IMPACT_UPDATED_UNIX, m_lImpactUpdateUnixTime);
	if(m_iItsmCase != -1)
		m_pRecordBuilder->append(ITSM_CASE, m_iItsmCase);
	if(!m_strTicketId.empty())
		m_pRecordBuilder->append(TICKET_ID, m_strTicketId);
	if(!m_strItsmId.empty())
		m_pRecordBuilder->append(ITSM_ID, m_strItsmId);
	if(!m_strRejectMsg.empty())
		m_pRecordBuilder->append(MSG, m_strRejectMsg);
}


Query CCSAlertModel::GetQueryCsReject()
{
	Query queryResult = QUERY(ITSM_STATUS<<"rejected");
	return queryResult;
}

void CCSAlertModel::DestroyData()
{
	ReleaseBuilder();
	m_iStatus = m_iITSMSttNoti = m_iImpactLevel = m_iSdkItsmNoti = m_iItsmCase = -1;
	m_lImpactUpdateUnixTime = -1;
	m_strITSMStatus = m_strImpactUpdateTime = m_strTicketId = m_strItsmId = m_strRejectMsg = "";
}
#pragma once
#include "MongodbModel.h"

class CCSAlertModel:public CMongodbModel
{
protected:
	string m_strITSMStatus, m_strImpactUpdateTime, m_strTicketId, m_strItsmId, m_strRejectMsg;
	int m_iITSMSttNoti, m_iStatus, m_iImpactLevel, m_iSdkItsmNoti, m_iItsmCase;
	long long m_lImpactUpdateUnixTime;
public:
	CCSAlertModel(void);
	~CCSAlertModel(void);
	void PrepareRecord();
	void DestroyData();
	Query GetQueryCsReject();
	
	inline void SetRejectMsg(string strRejectMsg)
	{
		m_strRejectMsg = strRejectMsg;
	}
	inline void SetITSMStatus(string strITSMStatus)
	{
		m_strITSMStatus = strITSMStatus;
	}
	inline void SetITSMSttNoti(int iITSMSttNoti)
	{
		m_iITSMSttNoti = iITSMSttNoti;
	}
	inline void SetStatus(int iStatus)
	{
		m_iStatus = iStatus;
	}
	inline void SetImpactLevel(int iImpactLevel)
	{
		m_iImpactLevel = iImpactLevel;
	}
	inline void SetImpactUpdateTime(string strImpactUpdateTime)
	{
		m_strImpactUpdateTime = strImpactUpdateTime;
	}
	inline void SetImpactUpdateUnixTime(long long lImpactUpdateUnixTime)
	{
		m_lImpactUpdateUnixTime = lImpactUpdateUnixTime;
	}
	inline void SetSdkItsmNoti(int iSdkItsmNoti)
	{
		m_iSdkItsmNoti = iSdkItsmNoti;
	}
	inline void SetItsmCase(int iItsmCase)
	{
		m_iItsmCase = iItsmCase;
	}
	inline void SetTicketId(string strTicketId)
	{
		m_strTicketId = strTicketId;
	}
	inline void SetItsmId(string strItsmId)
	{
		m_strItsmId = strItsmId;
	}
};

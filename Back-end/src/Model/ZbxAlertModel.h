#pragma once
#include "MongodbModel.h"

class CZbxAlertModel :
	public CMongodbModel
{
protected:
	string m_strITSMStatus;
	int m_iITSMSttNoti, m_iStatus;
public:
	CZbxAlertModel(void);
	~CZbxAlertModel(void);
	void PrepareRecord();
	void DestroyData();
	
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
};

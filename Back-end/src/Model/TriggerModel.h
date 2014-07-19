#pragma once
#include "MongodbModel.h"

class CTriggerModel:public CMongodbModel
{
protected:
	int m_iZbxServerId, m_iStatus, m_iPriority, m_iValue;
	long long m_lClock, m_lTriggerId;
	string m_strExpression, m_strDescription;
public:
	CTriggerModel(void);
	~CTriggerModel(void);
	
	BSONObj GetUniqueTriggerBson();
	void PrepareRecord();
	void DestroyData();
//=================================Set Get Propertise ==============================
	inline void SetZbxServerId(int iZbxServerId)
	{
		m_iZbxServerId = iZbxServerId;
	}
	inline void SetStatus(int iStatus)
	{
		m_iStatus = iStatus;
	}
	inline void SetPriority(int iPriority)
	{
		m_iPriority = iPriority;
	}
	inline void SetClock(long long lClock)
	{
		m_lClock = lClock;
	}
	inline void SetValue(int iValue)
	{
		m_iValue = iValue;
	}
	inline void SetTriggerId(long long lTriggerId)
	{
		m_lTriggerId = lTriggerId;
	}
	inline void SetExpression(string strExpression)
	{
		m_strExpression = strExpression;
	}
	inline void SetDescription(string strDescription)
	{
		m_strDescription = strDescription;
	}
};

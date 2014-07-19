#pragma once
#include "MongodbModel.h"

class CEventModel:public CMongodbModel
{
protected:
	int m_iZbxServerId, m_iStatus, m_iValueChanged;
	long long m_lClock, m_lEventId, m_lTriggerId, m_lHostId, m_lItemId, m_lServerId;
	
public:
	
	CEventModel(void);
	~CEventModel(void);
	
	BSONObj GetUniqueEventBson();
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
	inline void SetValueChanged(int iValueChanged)
	{
		m_iValueChanged = iValueChanged;
	}
	inline void SetClock(long long lClock)
	{
		m_lClock = lClock;
	}
	inline void SetEventId(long long lEventId)
	{
		m_lEventId = lEventId;
	}
	inline void SetTriggerId(long long lTriggerId)
	{
		m_lTriggerId = lTriggerId;
	}
	inline void SetHostId(long long lHostId)
	{
		m_lHostId = lHostId;
	}
	inline void SetItemId(long long lItemId)
	{
		m_lItemId = lItemId;
	}
	inline void SetServerId(long long lServerId)
	{
		m_lServerId = lServerId;
	}
};

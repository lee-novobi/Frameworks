#pragma once
#include "MongodbModel.h"

class CItemModel:public CMongodbModel
{
protected:
	int m_iZbxServerId, m_iStatus, m_iValueType;
	long long m_lClock, m_lTriggerId, m_lHostId, m_lItemId, m_lServerId;
	string m_strDescription, m_strKey, m_strUnits;
	
public:
	CItemModel(void);
	~CItemModel(void);
	
	BSONObj GetUniqueItemBson();
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
	inline void SetValueType(int iValueType)
	{
		m_iValueType = iValueType;
	}
	inline void SetClock(long long lClock)
	{
		m_lClock = lClock;
	}
	inline void SetDescription(string strDescription)
	{
		m_strDescription = strDescription;
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
	inline void SetKey(string strKey)
	{
		m_strKey = strKey;
	}
	inline void SetUnits(string strUnits)
	{
		m_strUnits = strUnits;
	}
};

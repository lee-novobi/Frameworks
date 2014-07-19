#pragma once
#include "MongodbModel.h"

class CAlertModel:public CMongodbModel
{
protected:
	string m_strDescription, m_strKey, m_strHost, m_strDeptAlias, m_strProdAlias;
	long long m_lTriggerId, m_lItemId, m_lHostId, m_lClock, m_lServerId, m_lEventId;
	int m_iMaintenance, m_iAlertId, m_iZbxServerId, m_iPriority, m_iStatus, m_iValueChanged, m_iIsSync;
	
public:
	CAlertModel(void);
	~CAlertModel(void);
	
	BSONObj GetUniqueAlertBson();
	void PrepareRecord();
	void DestroyData();
//=================================Set Get Propertise ==============================
	inline void SetMaintenance(int iMaintenance)
	{
		m_iMaintenance = iMaintenance;
	}
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
	inline void SetDescription(string strDescription)
	{
		m_strDescription = strDescription;
	}
	inline void SetKey(string strKey)
	{
		m_strKey = strKey;
	}
	inline void SetHost(string strHost)
	{
		m_strHost = strHost;
	}
	inline void SetDeptAlias(string strDeptAlias)
	{
		m_strDeptAlias = strDeptAlias;
	}
	inline void SetProdAlias(string strProdAlias)
	{
		m_strProdAlias = strProdAlias;
	}
	inline void SetAlertId(int iAlertId)
	{
		m_iAlertId = iAlertId;
	}
	inline void SetIsSync(int iIsSync)
	{
		m_iIsSync = iIsSync;
	}
	inline void SetPriority(int iPriority)
	{
		m_iPriority = iPriority;
	}
};

#pragma once
#include "MongodbModel.h"

class CHostWebModel:public CMongodbModel
{
protected:
	int m_iZbxServerId, m_iStatus, m_iAvailable, m_iMaintenance, m_iDelete;
	long long m_lHostId, m_lMaintenanceFrom, m_lServerId;
	string m_strHost, m_strName;
	
public:
	
	CHostWebModel(void);
	~CHostWebModel(void);
	
	BSONObj GetUniqueHostWebBson();
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
	inline void SetAvailable(int iAvailable)
	{
		m_iAvailable = iAvailable;
	}
	inline void SetMaintenance(int iMaintenance)
	{
		m_iMaintenance = iMaintenance;
	}
	inline void SetHostId(long long lHostId)
	{
		m_lHostId = lHostId;
	}
	inline void SetMaintenanceFrom(long long lMaintenanceFrom)
	{
		m_lMaintenanceFrom = lMaintenanceFrom;
	}
	inline void SetServerId(long long lServerId)
	{
		m_lServerId = lServerId;
	}
	inline void SetName(string strName)
	{
		m_strName = strName;
	}
	inline void SetHost(string strHost)
	{
		m_strHost = strHost;
	}
	inline void SetDelete(int iDelete)
	{
		m_iDelete = iDelete;
	}
};

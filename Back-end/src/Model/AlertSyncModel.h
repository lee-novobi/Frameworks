#pragma once
#include "MongodbModel.h"

class CAlertSyncModel:public CMongodbModel
{
protected:
	int m_iIsShow, m_iNumOfCase, m_iImpactLevel, m_iZbxZabbixServerId, m_iZbxPriority, m_iZbxMaintenance, m_iIsAcked;
	string m_strTicketId, m_strSourceFrom, m_strSourceId, m_strTitle, m_strDescription, m_strDepartment, m_strProduct, m_strAttactment,
			m_strAlertMsg, m_strZbxDescription, m_strZbxKey, m_strZbxHost, m_strPriority, m_strAffectedDeals, m_strItsmId;
	long long m_lZbxServerId, m_lZbxTriggerId, m_lZbxItemId, m_lZbxHostId, m_lZbxEventId, m_lClock;
public:
	CAlertSyncModel(void);
	~CAlertSyncModel(void);
	
	BSONObj GetUniqueAlertSyncBson();
	BSONObj GetUniqueZbxAlertSyncBson();
	BSONObj GetServerIdZbxAlertSyncBson();
	void PrepareRecord();
	void DestroyData();
	
//=================================Set Get Propertise ==============================
	inline void SetAcked(int iIsAcked)
	{
		m_iIsAcked = iIsAcked;
	}
	inline void SetItsmId(string strItsmId)
	{
		m_strItsmId = strItsmId;
	}
	inline void SetTicketId(string strTicketId)
	{
		m_strTicketId = strTicketId;
	}
	inline void SetAffectedDeals(string strAffectedDeals)
	{
		m_strAffectedDeals = strAffectedDeals;
	}
	inline void SetClock(int lClock)
	{
		m_lClock = lClock;
	}
	inline void SetIsShow(int iIsShow)
	{
		m_iIsShow = iIsShow;
	}
	inline void SetNumOfCase(int iNumOfCase)
	{
		m_iNumOfCase = iNumOfCase;
	}
	inline void SetImpactLevel(int iImpactLevel)
	{
		m_iImpactLevel = iImpactLevel;
	}
	inline void SetSourceFrom(string strSourceFrom)
	{
		m_strSourceFrom = strSourceFrom;
	}
	inline void SetSourceId(string strSourceId)
	{
		m_strSourceId = strSourceId;
	}
	inline void SetTitle(string strTitle)
	{
		m_strTitle = strTitle;
	}
	inline void SetDescription(string strDescription)
	{
		m_strDescription = strDescription;
	}
	inline void SetDepartment(string strDepartment)
	{
		m_strDepartment = strDepartment;
	}
	inline void SetProduct(string strProduct)
	{
		m_strProduct = strProduct;
	}
	inline void SetAttactment(string strAttactment)
	{
		m_strAttactment = strAttactment;
	}
	inline void SetAlertMsg(string strAlertMsg)
	{
		m_strAlertMsg = strAlertMsg;
	}
	inline void SetZbxZabbixServerId(int iZbxZabbixServerId)
	{
		m_iZbxZabbixServerId = iZbxZabbixServerId;
	}
	inline void SetZbxServerId(long long lZbxServerId)
	{
		m_lZbxServerId = lZbxServerId;
	}
	inline void SetZbxEventId(long long lZbxEventId)
	{
		m_lZbxEventId = lZbxEventId;
	}
	inline void SetZbxPriority(int iZbxPriority)
	{
		m_iZbxPriority = iZbxPriority;
	}
	inline void SetZbxDescription(string strZbxDescription)
	{
		m_strZbxDescription = strZbxDescription;
	}
	inline void SetZbxKey(string strZbxKey)
	{
		m_strZbxKey = strZbxKey;
	}
	inline void SetZbxHost(string strZbxHost)
	{
		m_strZbxHost = strZbxHost;
	}
	inline void SetZbxTriggerId(long long lZbxTriggerId)
	{
		m_lZbxTriggerId = lZbxTriggerId;
	}
	inline void SetZbxItemId(long long lZbxItemId)
	{
		m_lZbxItemId = lZbxItemId;
	}
	inline void SetZbxHostId(long long lZbxHostId)
	{
		m_lZbxHostId = lZbxHostId;
	}
	inline void SetZbxMaintenance(int iZbxMaintenance)
	{
		m_iZbxMaintenance = iZbxMaintenance;
	}
	inline void SetPriority(string strPriority)
	{
		m_strPriority = strPriority;
	}
};

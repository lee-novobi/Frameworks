#pragma once
#include "MongodbModel.h"

class CWebInfoHistoryModel:public CMongodbModel
{
protected:
	int m_iFail, m_iResCode, m_iPreFail, m_iPreResCode, m_iZbxServerId;
	float m_fSpeed, m_fStepSpeed, m_fResTime;
	float m_fPreSpeed, m_fPreStepSpeed, m_fPreResTime;
	long long m_lHostId, m_lServerId, m_lClock, m_lItemId;
	string m_strUnit, m_strHostName, m_strWebKey, m_strErrMsg, m_strPreErrMsg, m_strAppName, m_strStepName;
	
public:
	
	CWebInfoHistoryModel(void);
	~CWebInfoHistoryModel(void);
	
	BSONObj GetKeysIndex();
	void PrepareRecord();
	void DestroyData();
//=================================Set Get Propertise ==============================
	inline void SetFail(int iFail)
	{
		m_iFail = iFail;
	}
	inline void SetResCode(int iResCode)
	{
		m_iResCode = iResCode;
	}
	inline void SetSpeed(float fSpeed)
	{
		m_fSpeed = fSpeed;
	}
	inline void SetStepSpeed(float fStepSpeed)
	{
		m_fStepSpeed = fStepSpeed;
	}
	inline void SetResTime(float fResTime)
	{
		m_fResTime = fResTime;
	}
	inline void SetHostName(string strHostName)
	{
		m_strHostName = strHostName;
	}
	inline void SetHostId(long long lHostId)
	{
		m_lHostId = lHostId;
	}
	inline void SetWebKey(string strWebKey)
	{
		m_strWebKey = strWebKey;
	}
	inline void SetServerId(long long lServerId)
	{
		m_lServerId = lServerId;
	}
	inline void SetErrMsg(string strErrMsg)
	{
		m_strErrMsg = strErrMsg;
	}
	inline void SetZbxServerId(int iZbxServerId)
	{
		m_iZbxServerId = iZbxServerId;
	}
	inline void SetClock(int lClock)
	{
		m_lClock = lClock;
	}
	inline void SetItemId(int lItemId)
	{
		m_lItemId = lItemId;
	}
	inline void SetAppName(string strAppName)
	{
		m_strAppName = strAppName;
	}
	inline void SetStepName(string strStepName)
	{
		m_strStepName = strStepName;
	}
	inline void SetUnit(string strUnit)
	{
		m_strUnit = strUnit;
	}
};

#pragma once
#include "MongodbModel.h"

class CFunctionModel:public CMongodbModel
{
protected:
	int m_iZbxServerId;
	long long m_lFunctionId, m_lTriggerId, m_lItemId;
	string m_strFunction, m_strParameter;
	
public:
	CFunctionModel(void);
	~CFunctionModel(void);
	
	BSONObj GetUniqueFunctionBson();
	Query GetFunctionByTriggerIdBson();
	void PrepareRecord();
	void DestroyData();
//=================================Set Get Propertise ==============================
	inline void SetZbxServerId(int iZbxServerId)
	{
		m_iZbxServerId = iZbxServerId;
	}
	inline void SetFunction(string strFunction)
	{
		m_strFunction = strFunction;
	}
	inline void SetFunctionId(long long lFunctionId)
	{
		m_lFunctionId = lFunctionId;
	}
	inline void SetParameter(string strParameter)
	{
		m_strParameter = strParameter;
	}
	inline void SetTriggerId(long long lTriggerId)
	{
		m_lTriggerId = lTriggerId;
	}
	inline void SetItemId(long long lItemId)
	{
		m_lItemId = lItemId;
	}
};

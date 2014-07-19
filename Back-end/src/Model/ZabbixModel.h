#pragma once
#include "MongodbModel.h"

class CZabbixModel :
	public CMongodbModel
{
public:
	CZabbixModel(void);
	~CZabbixModel(void);

	void SetServerId(long long llServerId) { m_llServerId = llServerId; }
	void SetStatus(int iStatus) { m_iStatus = iStatus; }
protected:
	long long	m_llServerId;
	int			m_iStatus;
};

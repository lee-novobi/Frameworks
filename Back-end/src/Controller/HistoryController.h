#pragma once
#include "mysqlController.h"
#include "../Common/Common.h"

class CHistoryController:public CmysqlController
{
public:
	CHistoryController(void);
	CHistoryController(string strDBName);
	~CHistoryController(void);
	
	bool FindOne();
	void Save();
	void Update();
	string GetSelectQuery();
	string GetInsertQuery();
	string GetUpdateQuery();
	void ResetModel();
	
	inline void SetServerId(long long lServerId) { m_lServerId = lServerId; }
	inline long long GetServerId() { return m_lServerId; }
protected:
	long long m_lServerId;
};


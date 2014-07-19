#pragma once
#include "mysqlController.h"
#include "../Common/Common.h"

class CHostZabbixController:public CmysqlController
{
public:
	CHostZabbixController(void);
	CHostZabbixController(string strDBName);
	~CHostZabbixController(void);
	
	bool FindOne();
	void Save();
	void Update();
	string GetSelectQuery();
	string GetInsertQuery();
	string GetUpdateQuery();
	
	void ResetModel();
	inline void SetServerId(int lServerId) { m_lServerId = lServerId; }
	inline int GetServerId() { return m_lServerId; }
protected:
	int m_lServerId;
};


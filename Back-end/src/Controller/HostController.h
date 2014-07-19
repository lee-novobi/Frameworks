#pragma once
#include "mysqlController.h"
#include "../Common/Common.h"

class CHostController:public CmysqlController
{
public:
	CHostController(void);
	CHostController(string strDBName);
	~CHostController(void);
	
	bool FindOne();
	void Save();
	void Update();
	string GetSelectQuery();
	string GetInsertQuery();
	string GetUpdateQuery();

	inline void SetServerId(int lServerId) { m_lServerId = lServerId; }
	inline int GetServerId() { return m_lServerId; }
protected:
	int m_lServerId;
};


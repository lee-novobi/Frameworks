#pragma once
#include "mysqlController.h"
#include "../Common/Common.h"

class CItemController:public CmysqlController
{
public:
	CItemController(void);
	CItemController(string strDBName);
	~CItemController(void);
	bool FindOne();
	void Save();
	void Update();
	string GetSelectQuery();
	string GetInsertQuery();
	string GetUpdateQuery();

	inline void SetServerId(int iServerId) { m_iServerId = iServerId; }
	inline int GetServerId() { return m_iServerId; }
	inline void SetItemId(int lItemId) { m_lItemId = lItemId; }
	inline long long GetItemId() { return m_lItemId; }
protected:
	int m_iServerId;
	long long m_lItemId;
};

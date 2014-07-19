#pragma once
#include "mysqlController.h"
#include "../Common/Common.h"

class CZabbixController:public CmysqlController
{
public:
	CZabbixController(void);
	~CZabbixController(void);
	
	bool FindDeletedTrigger();
};


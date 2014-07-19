#pragma once
#include "mysqlController.h"
#include "../Common/Common.h"

class CIncidentFollowController:public CmysqlController
{
public:
	CIncidentFollowController(void);
	CIncidentFollowController(string strDBName);
	~CIncidentFollowController(void);
	
	bool FindIncidentInDay();
	bool FindIncidentOpen();
	bool FindIncidentHighLevelWithoutSEReport();
	void ResetModel();
};


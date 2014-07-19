#pragma once
#include "mysqlController.h"
#include "../Common/Common.h"

struct IncUpdateHistoryInfo
{
	int iSdkUpdateToItsmStatus;
	int iImpactLvl;
	int iCustomerCase;
	string strItsmIncId;
};

class CIncidentController:public CmysqlController
{
public:
	CIncidentController(void);
	~CIncidentController(void);
	
	bool InsertCSImpact(IncUpdateHistoryInfo stIncUpdHis);
	bool UpdateCSImpact(IncUpdateHistoryInfo stIncUpdHis);
	bool IsCSImpactExisted(IncUpdateHistoryInfo stIncUpdHis);
	bool IsCSAlertCreateInc(string strItsmId, string strSourceId);
	bool NotiImpactLvlUp(string strSrcFrom, string strTicketId, int iImpactLevel);
};


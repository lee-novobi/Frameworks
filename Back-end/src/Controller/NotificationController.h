#pragma once
#include "mysqlController.h"
#include "../Common/Common.h"

class CNotificationController:public CmysqlController
{
public:
	CNotificationController(void);
	~CNotificationController(void);
	
	bool InsertIncidentFollowNoti(string strItsmId, int iEscLvl, int iImpactLvl);
	bool InsertIncidentFollowSEReportNoti(string strItsmId, int iEscLvl, int iImpactLvl);
	bool CloseNotiIncidentNotOpen(vector<string> v_strItsmId);
	bool CloseNotiSEReported(vector<string> v_strItsmId);
	bool CloseNotiIncidentByItsmId(string strItsmId);
	bool CloseNotiSEByItsmId(string strItsmId);
};


#pragma once
#include "MongodbModel.h"

class CMaintenanceModel:public CMongodbModel
{
public:
	CMaintenanceModel(void);
	~CMaintenanceModel(void);
	Query GetListMaintenanceInfo();
	Query GetListHostMaintenanceInfo(long long lMaintenanceId);
	Query GetMaintenanceAlertInfo(long long lHostId, int iMaintenanceStatus);
	void PrepareRecord();
	void DestroyData();
};

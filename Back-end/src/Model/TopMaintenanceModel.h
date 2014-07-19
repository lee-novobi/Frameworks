#pragma once
#include "MongodbModel.h"

class CTopMaintenanceModel:public CMongodbModel
{
protected:
	int m_iClock;
	vector<long long> m_vtMaintenanceId;
public:
	CTopMaintenanceModel(void);
	~CTopMaintenanceModel(void);
	void Init();
	void PrepareRecord();
	void DestroyData();
	//Property
	int GetClock() { return m_iClock; }
	vector<long long> GetListMaintenance() { return m_vtMaintenanceId; }
	
	void SetClock(int iClock) { m_iClock = iClock; }
	void SetListMaintenance(vector<long long> vtMaintenanceId) { m_vtMaintenanceId = vtMaintenanceId; }
	Query QueryMaintenanceByClock(int iClock);
	Query QueryMaintenanceWithOutScope(int iTimeLimit);
};
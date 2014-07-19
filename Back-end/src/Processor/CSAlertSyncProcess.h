#pragma once
#include "../Common/Common.h"
#include "BaseAlertSyncProcess.h"

class CCSAlertSyncProcess: public CBaseAlertSyncProcess
{
public:
	CCSAlertSyncProcess(void);
	CCSAlertSyncProcess(string strCfgFile);
	~CCSAlertSyncProcess(void);
protected:
	int CreateModel();
};


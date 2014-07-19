#pragma once
#include "../Common/Common.h"
#include "BaseAlertSyncProcess.h"

class CG8AlertSyncProcess: public CBaseAlertSyncProcess
{
public:
	CG8AlertSyncProcess(void);
	CG8AlertSyncProcess(string strCfgFile);
	~CG8AlertSyncProcess(void);
protected:
	void CreateModel();
};
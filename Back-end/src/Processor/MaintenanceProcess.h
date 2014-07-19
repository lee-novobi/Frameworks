#pragma once
#include "../Common/Common.h"

class CConfigFileParse;
struct ConnectInfo;

class CMaintenanceProcess
{
public:
	CMaintenanceProcess(void);
	CMaintenanceProcess(string strCfgFile);
	~CMaintenanceProcess(void);
	ConnectInfo GetConnectInfo(string strDBType);
	void GoToWorkFlow(string strFunctionName);
protected:
	string m_strConfigFile;
	CConfigFileParse *m_pConfigFile;
};


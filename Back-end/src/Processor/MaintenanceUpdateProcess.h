#pragma once
#include "../LogParser/LogParser.h"

class CLogParserData;
class CConfigFileParse;
class CAlertSyncController;
class CAlertSyncModel;
struct ConnectInfo;

class CMaintenanceUpdateProcess: public LogParser
{
public:
	CMaintenanceUpdateProcess(void);
	CMaintenanceUpdateProcess(string strCfgFile);
	~CMaintenanceUpdateProcess(void);
	
	MA_RESULT ProcessParse();
protected:
	//============Function=============//
	MA_RESULT ParseLog();
	void Init();
	void Destroy();
	bool ControllerConnect();
	ConnectInfo GetConnectInfo(string DBType);
	char* PreParsing(int &iLen, int &iPos);
	bool CheckStop();
	//=========================//
	CAlertSyncController *m_pAlertSyncController;
	CAlertSyncModel *m_pAlertSyncModel;
	CLogParserData *m_pConfigObj;
	CConfigFileParse *m_pConfigFile;
	bool m_bIsHostPart;
};


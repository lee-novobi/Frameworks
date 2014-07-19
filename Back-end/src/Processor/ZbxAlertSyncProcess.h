#pragma once
#include "../Common/Common.h"

class CAlertController;
class CAlertACKController;
class CAlertSyncController;
class CAlertSyncModel;
class CConfigFileParse;
struct ConnectInfo;

class CZbxAlertSyncProcess
{
public:
	CZbxAlertSyncProcess(void);
	CZbxAlertSyncProcess(string strCfgFile);
	~CZbxAlertSyncProcess(void);
	MA_RESULT ProcessSync();
protected:
	/////////////Function//////////////
	void Init();
	void Destroy();
	bool ControllerConnect();
	bool Reconnect();
	ConnectInfo GetConnectInfo(string DBType);
	int CreateModel();
	//////////////////////////////////////////////

	CAlertSyncController *m_pAlertSyncController;
	CAlertController *m_pAlertController;
	CAlertACKController *m_pAlertACKController;
	
	CAlertSyncModel *m_pAlertSyncModel;
	CConfigFileParse *m_pConfigFile;
	string m_strInfo;
};
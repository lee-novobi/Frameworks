#pragma once
#include "../Common/Common.h"
class CAlertACKController;
class CDCAlertController;
class CAlertSyncController;
class CMapProductController;
class CAlertSyncModel;
class CConfigFileParse;
struct ConnectInfo;

class CDCAlertSyncProcess
{
public:
	CDCAlertSyncProcess(void);
	CDCAlertSyncProcess(string strCfgFile);
	~CDCAlertSyncProcess(void);
	MA_RESULT ProcessSync();
	
protected:
	/////////////Function//////////////
	void Init();
	void Destroy();
	bool ControllerConnect();
	bool Reconnect();
	ConnectInfo GetConnectInfo(string DBType);
	bool CreateModel();
	//////////////////////////////////////////////
	CAlertACKController *m_pAlertACKController;
	CAlertSyncController *m_pAlertSyncController;
	CDCAlertController *m_pDCAlertController;
	
	CAlertSyncModel *m_pAlertSyncModel;
	CConfigFileParse *m_pConfigFile;
	string m_strInfo;
};
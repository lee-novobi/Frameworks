#pragma once
#include "../Common/Common.h"

class CSO6AlertController;
class CAlertSyncController;
class CImpactLevelController;
class CMapProductController;
class CAlertSyncModel;
class CImpactLevelModel;
class CMapProductModel;
class CConfigFileParse;
struct ConnectInfo;

class CSO6AlertSyncProcess
{
public:
	CSO6AlertSyncProcess(void);
	CSO6AlertSyncProcess(string strCfgFile);
	~CSO6AlertSyncProcess(void);
	MA_RESULT ProcessSync();
	
protected:
	/////////////Function//////////////
	void Init();
	void Destroy();
	bool ControllerConnect();
	bool Reconnect();
	ConnectInfo GetConnectInfo(string DBType);
	bool CreateModel();
	string GetSO6Product(string strServerName);
	//////////////////////////////////////////////

	CAlertSyncController *m_pAlertSyncController;
	CSO6AlertController *m_pSO6AlertController;
	CImpactLevelController *m_pImpactLevelController;
	CMapProductController *m_pMapProductController;
	
	CAlertSyncModel *m_pAlertSyncModel;
	CImpactLevelModel *m_pImpactLevelModel;
	CMapProductModel *m_pMapProductModel;
	CConfigFileParse *m_pConfigFile;
	string m_strInfo;
};
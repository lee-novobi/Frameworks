#pragma once
#include "../Common/Common.h"
class CAlertACKController;
class CG8AlertController;
class CAlertSyncController;
// class CImpactLevelController;
class CMapProductController;
class CAlertSyncModel;
// class CImpactLevelModel;
class CMapProductModel;
class CConfigFileParse;
struct ConnectInfo;

class CG8AlertSyncProcess
{
public:
	CG8AlertSyncProcess(void);
	CG8AlertSyncProcess(string strCfgFile);
	~CG8AlertSyncProcess(void);
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
	CG8AlertController *m_pG8AlertController;
	// CImpactLevelController *m_pImpactLevelController;
	CMapProductController *m_pMapProductController;
	
	CAlertSyncModel *m_pAlertSyncModel;
	// CImpactLevelModel *m_pImpactLevelModel;
	CMapProductModel *m_pMapProductModel;
	CConfigFileParse *m_pConfigFile;
	string m_strInfo;
};
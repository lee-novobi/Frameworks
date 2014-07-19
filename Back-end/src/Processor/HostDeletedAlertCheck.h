#pragma once
#include "../Common/Common.h"

class CHostMongoController;
class CAlertSyncController;
class CConfigFileParse;
struct ConnectInfo;

class CHostDeletedAlertCheck
{
public:
	CHostDeletedAlertCheck(void);
	CHostDeletedAlertCheck(string strCfgFile);
	~CHostDeletedAlertCheck(void);
	MA_RESULT ProcessCheck();
	
protected:
	/////////////Function//////////////
	void Init();
	void Destroy();
	bool ControllerConnect();
	ConnectInfo GetConnectInfo(string DBType);
	//////////////////////////////////////////////
	
	CHostMongoController * m_pHostMongoController;
	CAlertSyncController *m_pAlertSyncController;
	CConfigFileParse *m_pConfigFile;

};
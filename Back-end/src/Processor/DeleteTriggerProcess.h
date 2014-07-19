#pragma once
#include "../Common/Common.h"

class CZabbixController;
class CAlertSyncController;
class CTriggerController;
class CConfigFileParse;
struct ConnectInfo;

class CDeleteTriggerProcess
{
public:
	CDeleteTriggerProcess(void);
	CDeleteTriggerProcess(string strCfgFile);
	~CDeleteTriggerProcess(void);
	MA_RESULT ProcessDelete();
protected:
	/////////////Function//////////////
	void Init();
	void Destroy();
	bool ControllerConnect();
	bool Reconnect();
	ConnectInfo GetConnectInfo(string DBType);
	//////////////////////////////////////////////
	
	CAlertSyncController *m_pAlertSyncController;
	CZabbixController * m_pZabbixController;
	CTriggerController *m_pTriggerController;
	CConfigFileParse *m_pConfigFile;
};
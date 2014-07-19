#pragma once
#include "../Common/Common.h"

class CEventController;
class CTriggerController;
class CAlertController;
class CHostZabbixController;
class CHostMDRController;

class CTriggerModel;
class CAlertModel;
class CAlertProcessData;
class CConfigFileParse;
struct ConnectInfo;

class CAlertProcess
{
public:
	CAlertProcess(void);
	CAlertProcess(string strCfgFile);
	~CAlertProcess(void);
	MA_RESULT ProcessParse();
	
protected:
	/////////////Function//////////////
	void Init();
	void Destroy();
	bool ControllerConnect();
	bool Reconnect();
	ConnectInfo GetConnectInfo(string DBType);
	//////////////////////////////////////////////

	CEventController *m_pEventController;
	CTriggerController *m_pTriggerController;
	CAlertController *m_pAlertController;
	CHostMDRController *m_pHostMDRController;
	CHostZabbixController *m_pHostZabbixController;
	
	CTriggerModel *m_pTriggerModel;
	CAlertModel *m_pAlertModel;
	CAlertProcessData *m_pDataObj;
	CConfigFileParse *m_pConfigFile;

};



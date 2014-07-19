#pragma once
#include "../Common/Common.h"

class CIncidentController;
class CImpactLevelController;
class CCSAlertController;
class CImpactLevelModel;
class CCSAlertModel;
class CSyncProcessData;
class CConfigFileParse;
struct ConnectInfo;

class CUpdateCSImpactProcess
{
public:
	CUpdateCSImpactProcess(void);
	CUpdateCSImpactProcess(string strCfgFile);
	~CUpdateCSImpactProcess(void);
	MA_RESULT ProcessUpdate();
	
protected:
	/////////////Function//////////////
	void Init();
	void Destroy();
	bool ControllerConnect();
	bool Reconnect();
	ConnectInfo GetConnectInfo(string DBType);
	//////////////////////////////////////////////
	
	CIncidentController * m_pIncidentController;
	CImpactLevelController *m_pImpactLevelController;
	CCSAlertController *m_pCSAlertController;
	CImpactLevelModel *m_pImpactLevelModel;
	CCSAlertModel *m_pCSAlertModel;
	CSyncProcessData *m_pDataObj;
	CConfigFileParse *m_pConfigFile;

};
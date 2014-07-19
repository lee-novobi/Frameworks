#pragma once
#include "../Common/Common.h"

class CAlertSyncController;
class CG8AlertController;
class CCSAlertController;
class CAlertController;
class CG8AlertModel;
class CCSAlertModel;
class CZbxAlertModel;
class CIncidentFollowController;
class CSyncProcessData;
class CConfigFileParse;
struct ConnectInfo;

struct AlertInfo
{
	string strSourceId;
	string strStatus;
	int iStatus;
	string strOutageEnd;
	string strMSG;
};

class CUpdateAlertSttProcess
{
public:
	CUpdateAlertSttProcess(void);
	CUpdateAlertSttProcess(string strCfgFile);
	~CUpdateAlertSttProcess(void);
	MA_RESULT ProcessUpdate();
	MA_RESULT ProcessRejectCSAlert();
	
protected:
	/////////////Function//////////////
	void Init();
	void Destroy();
	bool ControllerConnect();
	ConnectInfo GetConnectInfo(string DBType);
	bool UpdateCSAlertStatus(AlertInfo sAlertInf);
	bool UpdateG8AlertStatus(AlertInfo sAlertInf);
	//////////////////////////////////////////////
	
	CG8AlertController *m_pG8AlertController;
	CCSAlertController *m_pCSAlertController;
	CAlertController *m_pAlertController;
	CG8AlertModel *m_pG8AlertModel;
	CCSAlertModel *m_pCSAlertModel;
	CZbxAlertModel *m_pZbxAlertModel;
	CIncidentFollowController *m_pIncidentFollowController;
	CSyncProcessData *m_pDataObj;
	CConfigFileParse *m_pConfigFile;

};
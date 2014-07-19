#pragma once
#include "../Common/Common.h"

class CIncidentFollowController;
class CNotificationController;
class CConfigFileParse;
struct ConnectInfo;

class CIncidentFollowRemindProcess
{
public:
	CIncidentFollowRemindProcess(void);
	CIncidentFollowRemindProcess(string strCfgFile);
	~CIncidentFollowRemindProcess(void);
	MA_RESULT ProcessRemind();
	MA_RESULT ProcessRemindSEReport();
	
protected:
	/////////////Function//////////////
	void Init();
	void Destroy();
	bool ControllerConnect();
	bool Reconnect();
	ConnectInfo GetConnectInfo(string DBType);
	//////////////////////////////////////////////

	CIncidentFollowController *m_pIncidentFollowController;
	CNotificationController *m_pNotificationController;
	CConfigFileParse *m_pConfigFile;
	string m_strInfo;
};
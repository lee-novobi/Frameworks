#pragma once
#include "../Common/Common.h"

class CMongodbController;
class CAlertACKController;
class CAlertSyncController;
class CAlertSyncModel;
class CSyncProcessData;
class CConfigFileParse;
struct ConnectInfo;

class CBaseAlertSyncProcess
{
public:
	CBaseAlertSyncProcess();
	~CBaseAlertSyncProcess(void);
	MA_RESULT ProcessSync();
	
protected:
	/////////////Function//////////////
	void Init();
	void Destroy();
	bool ControllerConnect();
	bool Reconnect();
	ConnectInfo GetConnectInfo(string DBType);
	virtual int CreateModel()
	{
	}
	//////////////////////////////////////////////

	CAlertSyncController *m_pAlertSyncController;
	CMongodbController *m_pSourceController;
	CAlertACKController *m_pAlertACKController;
	
	CAlertSyncModel *m_pAlertSyncModel;
	CSyncProcessData *m_pDataObj;
	CConfigFileParse *m_pConfigFile;
	string m_strInfo;
};
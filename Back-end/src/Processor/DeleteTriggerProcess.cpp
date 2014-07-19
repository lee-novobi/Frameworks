#include "DeleteTriggerProcess.h"

#include "../Controller/ZabbixController.h"
#include "../Controller/AlertSyncController.h"
#include "../Controller/TriggerController.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

CDeleteTriggerProcess::CDeleteTriggerProcess(void)
{

}

CDeleteTriggerProcess::CDeleteTriggerProcess(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
}


CDeleteTriggerProcess::~CDeleteTriggerProcess(void)
{
	Destroy();
}

void CDeleteTriggerProcess::Init()
{
	//==========Construction===========//
	m_pAlertSyncController 		= new CAlertSyncController();
	m_pZabbixController 		= new CZabbixController();
	m_pTriggerController 		= new CTriggerController();
	//=============Connection============//
	ControllerConnect();
}

void CDeleteTriggerProcess::Destroy()
{
	delete m_pAlertSyncController;
	delete m_pZabbixController;
	delete m_pTriggerController;
	delete m_pConfigFile;
}

ConnectInfo CDeleteTriggerProcess::GetConnectInfo(string DBType)
{
	ConnectInfo CInfo;
	CInfo.strHost 	= m_pConfigFile->GetHost();
	CInfo.strUser 	= m_pConfigFile->GetUser();
	CInfo.strPass 	= m_pConfigFile->GetPassword();
	CInfo.strSource = m_pConfigFile->GetSource();
	if(m_pConfigFile->GetPort().compare("") != 0)
		CInfo.strHost = CInfo.strHost + ":" + m_pConfigFile->GetPort();
	return CInfo;
}

bool CDeleteTriggerProcess::ControllerConnect()
{
	//==============MySQL==============
	ConnectInfo CInfo = GetConnectInfo(MYSQL_ZBX);
	if(!m_pZabbixController->Connect(CInfo))
		return false;
	//=============MONGODB==============
	CInfo = GetConnectInfo(MONGODB_MA);
	if(!m_pAlertSyncController->Connect(CInfo))
		return false;
	if(!m_pTriggerController->Connect(CInfo))
		return false;
	cout<< "Connected\n";
	return true;
}

bool CDeleteTriggerProcess::Reconnect()
{
	if(!m_pZabbixController->Reconnect())
		return false;
	return true;
}

MA_RESULT CDeleteTriggerProcess::ProcessDelete()
{
	//==============Init===============
	vector<long long> v_lTriggerId;
	long long lTriggerId;
	int i, iLocation;
	while(true)
	{
		//============Destory==============
		m_pZabbixController->ResetModel();
		m_pTriggerController->DestroyData();
		m_pAlertSyncController->DestroyData();
		v_lTriggerId.clear();
		//====================================HOST======================================
		if(m_pZabbixController->FindDeletedTrigger())
		{
			m_pZabbixController->GetFieldName();
			while(true)
			{
				if(!m_pZabbixController->NextRow())
				{
					break;
				}
				lTriggerId = m_pZabbixController->ModelGetLong("resourceid");
				v_lTriggerId.push_back(lTriggerId); 
			}
			iLocation = m_pConfigFile->ReadIntValue(TRIGGERGROUP,"Location");
			if(!m_pTriggerController->DeleteTriggerByVTriggerId(v_lTriggerId, iLocation))
			{
				string strLog;
				strLog = CUtilities::FormatLog(BUG_MSG, "CDeleteTriggerProcess", "DeleteTriggerByVTriggerId","FAIL");
				CUtilities::WriteErrorLog(strLog);
			}
			if(!m_pAlertSyncController->HideAlertByVTriggerId(v_lTriggerId))
			{
				string strLog;
				strLog = CUtilities::FormatLog(BUG_MSG, "CDeleteTriggerProcess", "HideAlertByVTriggerId","FAIL");
				CUtilities::WriteErrorLog(strLog);
			}
		}
		sleep(300);
	}
}

#include "IncidentFollowRemindProcess.h"

#include "../Controller/IncidentFollowController.h"
#include "../Controller/NotificationController.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

CIncidentFollowRemindProcess::CIncidentFollowRemindProcess(void)
{
}

CIncidentFollowRemindProcess::CIncidentFollowRemindProcess(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	//=============================//
	Init();
}

CIncidentFollowRemindProcess::~CIncidentFollowRemindProcess(void)
{
	Destroy();
}

void CIncidentFollowRemindProcess::Init()
{
	m_pIncidentFollowController = new CIncidentFollowController();
	m_pNotificationController = new CNotificationController();
	ControllerConnect();
}

void CIncidentFollowRemindProcess::Destroy()
{
	delete m_pIncidentFollowController;
	delete m_pNotificationController;
	delete m_pConfigFile;
}

ConnectInfo CIncidentFollowRemindProcess::GetConnectInfo(string DBType)
{
	ConnectInfo CInfo;
	CInfo.strHost = m_pConfigFile->GetHost();
	CInfo.strUser = m_pConfigFile->GetUser();
	CInfo.strPass = m_pConfigFile->GetPassword();
	CInfo.strSource = m_pConfigFile->GetSource();
	
	if(m_pConfigFile->GetPort().compare("") != 0)
		CInfo.strHost = CInfo.strHost + ":" + m_pConfigFile->GetPort();

	return CInfo;
}

bool CIncidentFollowRemindProcess::ControllerConnect()
{
	ConnectInfo CInfo;
	//==================================Mysql SO6 Connection===================================
	CInfo = GetConnectInfo(MYSQL_MA);
	if(!m_pIncidentFollowController->Connect(CInfo))
		return false;
	if(!m_pNotificationController->Connect(CInfo))
		return false;
	return true;
}

bool CIncidentFollowRemindProcess::Reconnect()
{
	if(!m_pIncidentFollowController->Reconnect())
		return false;
	if(!m_pNotificationController->Reconnect())
		return false;
	return true;
}

MA_RESULT CIncidentFollowRemindProcess::ProcessRemind()
{
	vector<string> v_strItsmId;
	long long lOutageStart, lCurrTimeStamp;
	int iEscLvl, iImpactLvl;
	string strItsmId, strLog;
	stringstream ssLogInfo;
	int iWaitTime;
	//===========================Sync Alert================================
	while(true)
	{
		iEscLvl = -1;
		while(!Reconnect())
			sleep(5);
		v_strItsmId.clear();
		m_pIncidentFollowController->ResetModel();
		if(m_pIncidentFollowController->FindIncidentOpen())
		{
			sleep(10);
			continue;
		}
		m_pIncidentFollowController->GetFieldName();
		while(true)
		{
			m_pNotificationController->ResetModel();
			m_pIncidentFollowController->NextRow();
			strItsmId 		= m_pIncidentFollowController->ModelGetString(ITSM_INC_ID);
			iImpactLvl 		= m_pIncidentFollowController->ModelGetInt("impact_level");
			lOutageStart 	= m_pIncidentFollowController->ModelGetLong("unix_outage_start");
			v_strItsmId.push_back(strItsmId); 
			lCurrTimeStamp = atol(CUtilities::GetCurrTimeStamp().c_str());
			if((lCurrTimeStamp - lOutageStart)%(SEC_PER_DAY/2) < 10 ){
				iEscLvl = (int)(lCurrTimeStamp - lOutageStart)/(SEC_PER_DAY/2);
				m_pNotificationController->CloseNotiIncidentByItsmId(strItsmId);
				m_pNotificationController->InsertIncidentFollowNoti(strItsmId, iEscLvl, iImpactLvl);
				//==========================Write LOG=====================================
				ssLogInfo.str(string());
				ssLogInfo << strItsmId << "|EscTime:" << (lCurrTimeStamp - lOutageStart);
				strLog = CUtilities::FormatLog(LOG_MSG, "IncidentRemind", "ProcessRemind", ssLogInfo.str());
				CUtilities::WriteErrorLog(strLog);
			}
		}
		m_pNotificationController->CloseNotiIncidentNotOpen(v_strItsmId);
		sleep(10);
	}
	return MA_RESULT_SUCCESS;
}

MA_RESULT CIncidentFollowRemindProcess::ProcessRemindSEReport()
{
	vector<string> v_strItsmId;
	long long lOutageStart, lCurrTimeStamp;
	int iEscLvl, iImpactLvl;
	string strItsmId, strLog;
	stringstream ssLogInfo;
	int iWaitTime;
	//===========================Sync Alert================================
	while(true)
	{
		iEscLvl = -1;
		while(!Reconnect())
			sleep(5);
		v_strItsmId.clear();
		m_pIncidentFollowController->ResetModel();
		m_pIncidentFollowController->FindIncidentHighLevelWithoutSEReport();
		m_pIncidentFollowController->GetFieldName();
		while(true)
		{
			m_pNotificationController->ResetModel();
			if(!m_pIncidentFollowController->NextRow())
			{
				m_pIncidentFollowController->ResetModel();
				break;
			}
			strItsmId 		= m_pIncidentFollowController->ModelGetString(ITSM_INC_ID);
			iImpactLvl 		= m_pIncidentFollowController->ModelGetInt("impact_level");
			lOutageStart 	= m_pIncidentFollowController->ModelGetLong("unix_outage_start");
			v_strItsmId.push_back(strItsmId); 
			lCurrTimeStamp = atol(CUtilities::GetCurrTimeStamp().c_str());
			if((lCurrTimeStamp - lOutageStart)%(SEC_PER_DAY/3) < 10 ){
				iEscLvl = (int)(lCurrTimeStamp - lOutageStart)/(SEC_PER_DAY/3);
				if(iEscLvl <= 9)
				{
					m_pNotificationController->CloseNotiSEByItsmId(strItsmId);
					m_pNotificationController->InsertIncidentFollowSEReportNoti(strItsmId, iEscLvl, iImpactLvl);
				}
				//==========================Write LOG=====================================
				ssLogInfo.str(string());
				ssLogInfo << strItsmId << "|EscTime:" << (lCurrTimeStamp - lOutageStart);
				strLog = CUtilities::FormatLog(LOG_MSG, "RemindSEReport", "ProcessRemindSEReport", ssLogInfo.str());
				CUtilities::WriteErrorLog(strLog);
			}
		}
		m_pNotificationController->CloseNotiSEReported(v_strItsmId);
		sleep(10);
	}
	return MA_RESULT_SUCCESS;
}
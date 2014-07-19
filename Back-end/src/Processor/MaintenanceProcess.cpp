#include "MaintenanceProcess.h"
#include "../Common/DBCommon.h"
#include "../Config/ConfigFileParse.h"
#include "../Controller/MaintenanceController.h"

CMaintenanceProcess::CMaintenanceProcess(void)
{
	m_strConfigFile = "";
	m_pConfigFile = NULL;
}
CMaintenanceProcess::CMaintenanceProcess(string strCfgFile)
{
	m_strConfigFile = strCfgFile;
	m_pConfigFile = new CConfigFileParse(strCfgFile);
}
CMaintenanceProcess::~CMaintenanceProcess(void)
{
	delete m_pConfigFile;
}
ConnectInfo CMaintenanceProcess::GetConnectInfo(string DBType)
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
void CMaintenanceProcess::GoToWorkFlow(string strFunctionName)
{
	CMaintenanceController objMaintenanceController(m_strConfigFile);
	int iDelay;
	ConnectInfo CInfo;

	try
	{
		CInfo = GetConnectInfo(MONGODB_MA);
		objMaintenanceController.Connect(CInfo);
		if (strFunctionName == STEP_PUSH_TOP_MAINTENANCE)
		{
			iDelay = atoi(m_pConfigFile->ReadStringValue(MAINTENANCE_CONFIG, TIMER_TOP_MAINTENANCE_DELAY).c_str());
			while(true)
			{
				objMaintenanceController.SaveTopMaintenanceInfo(CInfo);
				sleep(iDelay);
			}
		}
		else if (strFunctionName == STEP_COMPUTE_MAINTENANCE)
		{
			iDelay = atoi(m_pConfigFile->ReadStringValue(MAINTENANCE_CONFIG, TIMER_COMPUTE_MAINTENANCE_DELAY).c_str());
			while(true)
			{
				objMaintenanceController.ComputeMaintenanceInfo(CInfo);
				sleep(iDelay);
			}
		}
		else if (strFunctionName == STEP_ROTATE_TOP_MAINTENANCE)
		{
			iDelay = atoi(m_pConfigFile->ReadStringValue(MAINTENANCE_CONFIG, TIMER_ROTATE_TOP_MAINTENANCE_DELAY).c_str());
			while(true)
			{
				objMaintenanceController.RotateTopMaintenanceInfo(CInfo);
				sleep(iDelay);
			}
		}
	}
	catch(exception& ex)
	{	
		string strFormatLog;
		stringstream strErrorMess;
		strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__;
		strFormatLog = CUtilities::FormatLog(ERROR_MSG, "CMaintenanceProcess", "GoToWorkFlow", strErrorMess.str());
		CUtilities::WriteErrorLog(strFormatLog);
	}

}

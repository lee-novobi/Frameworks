#include "HostInfoUpdater.h"
#include "../Controller/MDRController.h"
#include "../Config/LogParserConfig.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

CHostInfoUpdater::CHostInfoUpdater(void)
{
}

CHostInfoUpdater::CHostInfoUpdater(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	cout << strCfgFile << endl;
	Init();
}

CHostInfoUpdater::~CHostInfoUpdater(void)
{
	delete m_pMDRController;
	delete m_pConfigObj;
	delete m_pConfigFile;
}

void CHostInfoUpdater::ProcessPushData()
{
	while(true)
	{
		PushData();
		sleep(10);
	}
}

void CHostInfoUpdater::PushData()
{
	cout << "Starting .." << endl;
	m_pMDRController->GetAllMDRHost();
}

void CHostInfoUpdater::Init()
{
	BuildController();
}

ConnectInfo CHostInfoUpdater::GetConnectInfo(string DBType)
{
	ConnectInfo CInfo;
	CInfo.strHost = m_pConfigFile->GetData(DBType, HOST);
	CInfo.strUser = m_pConfigFile->GetData(DBType, USER);
	CInfo.strPass = m_pConfigFile->GetData(DBType, PASS);
	CInfo.strSource = m_pConfigFile->GetData(DBType, SRC);
	return CInfo;
}

bool CHostInfoUpdater::BuildController()
{
	ConnectInfo CInfo = GetConnectInfo(MYSQL_MDR);
	m_pMDRController = new CMDRController(CInfo);
	return true;
}
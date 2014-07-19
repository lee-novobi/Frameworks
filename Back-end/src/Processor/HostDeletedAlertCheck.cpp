#include "HostDeletedAlertCheck.h"

#include "../Controller/HostMongoController.h"
#include "../Controller/AlertSyncController.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

CHostDeletedAlertCheck::CHostDeletedAlertCheck(void)
{

}

CHostDeletedAlertCheck::CHostDeletedAlertCheck(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
}


CHostDeletedAlertCheck::~CHostDeletedAlertCheck(void)
{
	Destroy();
}

void CHostDeletedAlertCheck::Init()
{
	//==========Construction===========//
	m_pHostMongoController 			= new CHostMongoController();
	m_pAlertSyncController 		= new CAlertSyncController();
	//=============Connection============//
	ControllerConnect();
}

void CHostDeletedAlertCheck::Destroy()
{
	delete m_pHostMongoController;
	delete m_pAlertSyncController;
	delete m_pConfigFile;
}

ConnectInfo CHostDeletedAlertCheck::GetConnectInfo(string DBType)
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

bool CHostDeletedAlertCheck::ControllerConnect()
{
	//=============MONGODB==============
	ConnectInfo CInfo = GetConnectInfo(MONGODB_MA);
	if(!m_pAlertSyncController->Connect(CInfo))
		return false;
	if(!m_pHostMongoController->Connect(CInfo))
		return false;
	cout<< "Connected\n";
	return true;
}


MA_RESULT CHostDeletedAlertCheck::ProcessCheck()
{
	//==============Init===============
	vector<long long> v_lServerId;
	long long lServerId;
	int i;
	while(true)
	{
		//============Destory==============
		m_pHostMongoController->DestroyData();
		m_pAlertSyncController->DestroyData();
		v_lServerId.clear();
		//====================================HOST======================================
		if(m_pHostMongoController->FindDB(BSON(IS_DELETED<<1)))
		{
			while(true)
			{
				if(!m_pHostMongoController->NextRecord())
				{
					break;
				}
				lServerId = m_pHostMongoController->GetLongResultVal(HOST_ID);
				v_lServerId.push_back(lServerId); 
			}
		}
		if(!m_pAlertSyncController->HideAlertByVServerId(v_lServerId))
		{
			string strLog;
			strLog = CUtilities::FormatLog(BUG_MSG, "CHostDeletedAlertCheck", "ProcessCheck","FAIL");
			CUtilities::WriteErrorLog(strLog);
		}
		sleep(300);
	}
}

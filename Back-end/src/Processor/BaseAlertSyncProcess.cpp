#include "BaseAlertSyncProcess.h"

#include "../Controller/MongodbController.h"
#include "../Controller/AlertSyncController.h"
#include "../Controller/AlertACKController.h"

#include "../Model/MongodbModel.h"
#include "../Model/AlertSyncModel.h"

#include "../Config/SyncProcessData.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

#include "mongo/client/dbclient.h"
using namespace mongo;

CBaseAlertSyncProcess::CBaseAlertSyncProcess(void)
{
}

CBaseAlertSyncProcess::~CBaseAlertSyncProcess(void)
{
}

void CBaseAlertSyncProcess::Init()
{
	m_pAlertSyncController = new CAlertSyncController();
	m_pAlertACKController = new CAlertACKController();
	m_pAlertSyncModel = new CAlertSyncModel();
	m_pDataObj = new CSyncProcessData(m_strInfo);
	ControllerConnect();
	cout<<"Connected\n";
}

void CBaseAlertSyncProcess::Destroy()
{
	delete m_pAlertACKController;
	delete m_pSourceController;
	delete m_pAlertSyncController;
	delete m_pAlertSyncModel;
	delete m_pConfigFile;
	delete m_pDataObj;
}

ConnectInfo CBaseAlertSyncProcess::GetConnectInfo(string DBType)
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

bool CBaseAlertSyncProcess::ControllerConnect()
{
	//====================================MA Connection==================================
	ConnectInfo CInfo = GetConnectInfo(MONGODB_MA);
	
	if(!m_pAlertACKController->Connect(CInfo))
		return false;
	if(!m_pSourceController->Connect(CInfo))
		return false;
	if(!m_pAlertSyncController->Connect(CInfo))
		return false;
	
	return true;
}

bool CBaseAlertSyncProcess::Reconnect()
{
	if(!m_pSourceController->Reconnect())
		return false;
	if(!m_pAlertSyncController->Reconnect())
		return false;
	
	return true;
}

MA_RESULT CBaseAlertSyncProcess::ProcessSync()
{
	int iPos, iSeek, nAlertRecordNum;
	//===========================Sync Alert================================
	while(true)
	{
		m_pSourceController->DestroyData();
		if(m_pSourceController->FindDB(QUERY(IS_SYNC<<0)))
		{
			while(true)
			{
				m_pAlertACKController->DestroyData();
				m_pAlertSyncController->DestroyData();
				m_pAlertSyncModel->DestroyData();
				if(!m_pSourceController->NextRecord())
				{
					m_pSourceController->DestroyData();
					break;
				}
				// CreateModel() Function is defined in child class
				if(!CreateModel())
					continue;
				m_pAlertSyncModel->PrepareRecord();
				m_pAlertSyncController->InsertDB(m_pAlertSyncModel->GetUniqueAlertSyncBson(), m_pAlertSyncModel->GetRecordBson());
			}
			
			string strLog;
			stringstream strErrorMess;
			strErrorMess<<"RecordNum:"<<nAlertRecordNum;
			strErrorMess<< "|Position:" << iPos;
			strLog = CUtilities::FormatLog(LOG_MSG, "AlertSyncProcess", "ProcessSync",strErrorMess.str());
			CUtilities::WriteErrorLog(strLog);
			m_pDataObj->SetPosition(nAlertRecordNum);
			
		}
		else
			sleep(iParseSleepTime);
	}
	return MA_RESULT_SUCCESS;
}
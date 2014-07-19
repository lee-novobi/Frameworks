#include "MaintenanceUpdateProcess.h"
#include "../Controller/AlertSyncController.h"
#include "../Model/AlertSyncModel.h"
#include "../Config/LogParserData.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

CMaintenanceUpdateProcess::CMaintenanceUpdateProcess(void)
{
}

CMaintenanceUpdateProcess::CMaintenanceUpdateProcess(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
}


CMaintenanceUpdateProcess::~CMaintenanceUpdateProcess(void)
{
	Destroy();
}

void CMaintenanceUpdateProcess::Init()
{
	string strLog, strInfo;
	
	m_pAlertSyncModel = new CAlertSyncModel();
	//======Read Config File======//
	strLog = m_pConfigFile->ReadStringValue(UPDATE_MAINTENANCE_GROUP,LOGPATH);
	strInfo = m_pConfigFile->ReadStringValue(UPDATE_MAINTENANCE_GROUP,INFOPATH);
	m_bIsHostPart = false;
	if(m_pConfigFile->ReadStringValue(UPDATE_MAINTENANCE_GROUP,PARTITION).compare("true") == 0)
		m_bIsHostPart = true;
	//=============================//
	ControllerConnect();
	m_pConfigObj = new CLogParserData(strLog.c_str(),strInfo.c_str());
}

void CMaintenanceUpdateProcess::Destroy()
{
	delete m_pAlertSyncModel;
	delete m_pAlertSyncController;
	delete m_pConfigObj;
	delete m_pConfigFile;
}

ConnectInfo CMaintenanceUpdateProcess::GetConnectInfo(string DBType)
{
	ConnectInfo CInfo;
	CInfo.strHost = m_pConfigFile->GetHost();
	CInfo.strUser = m_pConfigFile->GetUser();
	CInfo.strPass = m_pConfigFile->GetPassword();
	CInfo.strSource = m_pConfigFile->GetSource();
	return CInfo;
}

bool CMaintenanceUpdateProcess::ControllerConnect()
{
	ConnectInfo CInfo = GetConnectInfo(MONGODB_MA);
	m_pAlertSyncController = new CAlertSyncController();

	if(!m_pAlertSyncController->Connect(CInfo))
		return false;
	return true;
}

char* CMaintenanceUpdateProcess::PreParsing(int &iLength, int &nCurPosition)
{
	char* Buffer;
	Buffer = NULL;
	
	nCurPosition = m_pConfigObj->GetPosition();
	if(nCurPosition == 0 && m_bIsHostPart == true)
	{
		m_pConfigObj->SetNewLogFile();
		iLength = m_pConfigObj->GetLength();
		Buffer = (char*)m_pConfigObj->GetBuffer();
	}
	else
	{
		iLength = m_pConfigObj->GetLength();
		if(nCurPosition < iLength)
		{
			Buffer = (char*)m_pConfigObj->GetBuffer();
		}
		else if(m_bIsHostPart == true)
		{	
			if(iLength == 0)
			{
				nCurPosition = 0;
				m_pConfigObj->SetNewLogFile();
				iLength = m_pConfigObj->GetLength();
				Buffer = (char*)m_pConfigObj->GetBuffer();
			}
			else if(nCurPosition == iLength)
			{
				m_pConfigObj->SetNewLogFile();
				iLength = m_pConfigObj->GetLength();
				if(nCurPosition != iLength) // new logfile of next day
				{
					nCurPosition = 0;
					Buffer = (char*)m_pConfigObj->GetBuffer();
				}
				else // sleep 1 second if nothing new
				{
					m_pConfigObj->SetPosition(nCurPosition);
					sleep(iParseSleepTime);
				}
			}
		}
		else
			sleep(iParseSleepTime);
	}
	return Buffer;
}

MA_RESULT CMaintenanceUpdateProcess::ParseLog()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;
	char* Buffer;
	int nCurPosition;
	int iLength;
	string strTemp;

	int iZbxServerId, iStatus, iAvailable, iMaintenance;
	long long lHostId, lMaintenanceFrom, lServerId;
	string strHost, strName;
	string strFormatLog;
	stringstream strErrorMess;
	
	Buffer = (char*)PreParsing(iLength, nCurPosition);
	
	if(nCurPosition != 0 &&  Buffer != NULL) 
		GoToNewLine((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		
	if(nCurPosition < iLength){
		strErrorMess.str(string());
		strErrorMess << "Begin:"<<nCurPosition<<"|"<<iLength<<endl;
		strFormatLog = CUtilities::FormatLog(BUG_MSG, "MaintenanceUpdateProcess", "ParserLog", strErrorMess.str());
		CUtilities::WriteErrorLog(strFormatLog);
	}
	
	//===========================Parse Log================================
	while(nCurPosition < iLength)
	{
		//cout<<"Length : "<<iLength<<endl;
		//cout << "nCurPosition: " << nCurPosition << endl;

		// Init database fields
		iZbxServerId = iStatus = iAvailable = iMaintenance = 0;
		lHostId = lMaintenanceFrom = lServerId = 0;
		strHost = strName = "";
		
		m_pAlertSyncController->DestroyData();
		m_pAlertSyncModel->DestroyData();
		//Parse ServerId
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				iZbxServerId = atoi(strTemp.c_str());
			}
			catch(exception& ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		

		//Parse HostId
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				lHostId = atol(strTemp.c_str());
			}
			catch(exception& ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		lServerId = ((lHostId - 10000) * 256) + iZbxServerId;
		m_pAlertSyncModel->SetZbxServerId(lServerId);
		
		// cout << "lServerId: " << lServerId << endl;
		//Parse Host
		strTemp = GetBlock((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			strHost = strTemp;
		}
		strHost = CUtilities::ReplaceBlockBracket(strHost);
		
		//Parse Name
		strTemp = GetBlock((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			strName = strTemp;
		}
		strName = CUtilities::ReplaceBlockBracket(strName);

		//Parse Status
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				iStatus = atoi(strTemp.c_str());
			}
			catch(exception& ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		//Parse Available
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				iAvailable = atoi(strTemp.c_str());
			}
			catch(exception& ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		//Parse Maintenance
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				iMaintenance = atoi(strTemp.c_str());
			}
			catch(exception& ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		m_pAlertSyncModel->SetZbxMaintenance(iMaintenance);

		//Parse MaintenanceFrom
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				lMaintenanceFrom = atol(strTemp.c_str());
			}
			catch(exception& ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		// cout << iZbxServerId << " " << lHostId << " " << strHost << " " << strName << " " << iStatus << " " << iAvailable << " " << iMaintenance << " " << lMaintenanceFrom << endl; 

		m_pAlertSyncModel->PrepareRecord();
		if(m_pAlertSyncController->UpdateMaintenance(Query(m_pAlertSyncModel->GetServerIdZbxAlertSyncBson()), m_pAlertSyncModel->GetRecordBson())){
			// cout << "iZbxServerId:"<<iZbxServerId<<endl;
			// cout << "lServerId:"<<lServerId<<endl;
			// cout << "iMaintenance:"<<iMaintenance<<endl;
		}
		else
		{
			// cout << "				iZbxServerId:"<<iZbxServerId<<endl;
			// cout << "				lServerId:"<<lServerId<<endl;
			// cout << "				iMaintenance:"<<iMaintenance<<endl;
		}

		if(nCurPosition >= iLength)
		{
			m_pConfigObj->SetPosition(iLength);
		}
	}
	if(!strFormatLog.empty())
	{
		strErrorMess.str(string());
		strErrorMess << "End:"<<nCurPosition<<"|"<<iLength<<endl;
		strFormatLog = CUtilities::FormatLog(BUG_MSG, "MaintenanceUpdateProcess", "ParserLog", strErrorMess.str());
		CUtilities::WriteErrorLog(strFormatLog);
	}
	
	m_pConfigObj->ClearMapMem();
	return eResult;
}

MA_RESULT CMaintenanceUpdateProcess::ProcessParse()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;
	while(true)
	{
		eResult = ParseLog();
		sleep(iParseSleepTime);
	}
	return eResult;
}
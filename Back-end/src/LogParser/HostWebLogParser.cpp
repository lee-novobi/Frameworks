#include "HostWebLogParser.h"
#include "../Controller/HostWebController.h"
#include "../Model/HostWebModel.h"
#include "../Config/LogParserData.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

CHostWebLogParser::CHostWebLogParser(void)
{
}

CHostWebLogParser::CHostWebLogParser(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
}


CHostWebLogParser::~CHostWebLogParser(void)
{
	Destroy();
}

void CHostWebLogParser::Init()
{
	string strLog, strInfo;

	//======Read Config File======//
	strLog = m_pConfigFile->ReadStringValue(HOST_WEB_GROUP,LOGPATH);
	strInfo = m_pConfigFile->ReadStringValue(HOST_WEB_GROUP,INFOPATH);
	m_bIsHostWebPart = false;
	if(m_pConfigFile->ReadStringValue(HOST_WEB_GROUP,PARTITION).compare("true") == 0)
		m_bIsHostWebPart = true;
	//=============================//
	m_pHostWebController = new CHostWebController();
	m_pHostWebModel = new CHostWebModel();
	m_pDataObj = new CLogParserData(strLog.c_str(),strInfo.c_str());
	ControllerConnect();
}

void CHostWebLogParser::Destroy()
{
	delete m_pHostWebController;
	delete m_pHostWebModel;
	delete m_pDataObj;
	delete m_pConfigFile;
}

ConnectInfo CHostWebLogParser::GetConnectInfo(string DBType)
{
	ConnectInfo CInfo;
	CInfo.strHost = m_pConfigFile->GetHost();
	CInfo.strUser = m_pConfigFile->GetUser();
	CInfo.strPass = m_pConfigFile->GetPassword();
	CInfo.strSource = m_pConfigFile->GetSource();
	return CInfo;
}

bool CHostWebLogParser::ControllerConnect()
{
	ConnectInfo CInfo = GetConnectInfo(MONGODB_ODA);

	if(!m_pHostWebController->Connect(CInfo))
		return false;
	return true;
}

char* CHostWebLogParser::PreParsing(int &iLength, int &nCurPosition)
{
	char* Buffer;
	Buffer = NULL;
	
	nCurPosition = m_pDataObj->GetPosition();
	if(nCurPosition == 0 && m_bIsHostWebPart == true)
	{
		m_pDataObj->SetNewLogFile();
		iLength = m_pDataObj->GetLength();
		Buffer = (char*)m_pDataObj->GetBuffer();
	}
	else
	{
		iLength = m_pDataObj->GetLength();
		if(nCurPosition < iLength)
		{
			Buffer = (char*)m_pDataObj->GetBuffer();
		}
		else if(m_bIsHostWebPart == true)
		{	
			if(iLength == 0)
			{
				nCurPosition = 0;
				m_pDataObj->SetNewLogFile();
				iLength = m_pDataObj->GetLength();
				Buffer = (char*)m_pDataObj->GetBuffer();
			}
			else if(nCurPosition == iLength)
			{
				m_pDataObj->SetNewLogFile();
				iLength = m_pDataObj->GetLength();
				if(nCurPosition != iLength) // new logfile of next day
				{
					nCurPosition = 0;
					Buffer = (char*)m_pDataObj->GetBuffer();
				}
				else // sleep 1 second if nothing new
				{
					m_pDataObj->SetPosition(nCurPosition);
					sleep(iParseSleepTime);
				}
			}
		}
		else
			sleep(iParseSleepTime);
	}
	return Buffer;
}

MA_RESULT CHostWebLogParser::ParseLog()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;
	char* Buffer;
	int nCurPosition;
	int iLength;
	string strTemp;

	int iZbxServerId, iStatus, iAvailable, iMaintenance;
	long long lHostId, lMaintenanceFrom, lServerId;
	string strHost, strName;

	Buffer = (char*)PreParsing(iLength, nCurPosition);
	//===========================Parse Log================================
	while(nCurPosition < iLength)
	{
		
		m_pHostWebController->DestroyData();
		m_pHostWebModel->DestroyData();

		// Init database fields
		iZbxServerId = iStatus = iAvailable = iMaintenance = 0;
		lHostId = lMaintenanceFrom= 0;
		strHost = strName = "";
		
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
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		//cout << "iZbxServerId: " << iZbxServerId << endl;
		m_pHostWebModel->SetZbxServerId(iZbxServerId);

		//Parse HostWebId
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
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		m_pHostWebModel->SetHostId(lHostId);
		
		lServerId = ((lHostId - 10000) * 256) + iZbxServerId;
		m_pHostWebModel->SetServerId(lServerId);

		//Parse HostWeb
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			strHost = strTemp;
		}
		strHost = strHost.substr(1,strHost.length()-2);
		m_pHostWebModel->SetHost(strHost);

		//Parse Name
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			strName = strTemp;
		}
		strName = strName.substr(1,strName.length()-2);
		m_pHostWebModel->SetName(strName);

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
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		m_pHostWebModel->SetStatus(iStatus);
		
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
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		m_pHostWebModel->SetAvailable(iAvailable);

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
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		m_pHostWebModel->SetMaintenance(iMaintenance);

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
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		m_pHostWebModel->SetMaintenanceFrom(lMaintenanceFrom);

		//cout << lServerId << " | " << iZbxServerId << " | " << lHostId << " | " << iStatus << " | " << strHost <<endl; 
		//m_pHostWebController->save(2, 23573, 1366957763, 10100, 37548, 10100);
		
		m_pHostWebModel->PrepareRecord();
		m_pHostWebController->InsertDB(m_pHostWebModel->GetUniqueHostWebBson(), m_pHostWebModel->GetRecordBson());
		
		if(nCurPosition >= iLength)
		{
			m_pDataObj->SetPosition(iLength);
		}
	}
	
	m_pDataObj->ClearMapMem();
	return eResult;
}

MA_RESULT CHostWebLogParser::ProcessParse()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;
	while(true)
	{
		eResult = ParseLog();
		sleep(iParseSleepTime);
	}
	return eResult;
}
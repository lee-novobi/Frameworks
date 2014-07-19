#include "HostLogParser.h"
#include "../Controller/HostController.h"
#include "../Config/LogParserConfig.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

CHostLogParser::CHostLogParser(void)
{
}

CHostLogParser::CHostLogParser(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
}


CHostLogParser::~CHostLogParser(void)
{
	Destroy();
}

void CHostLogParser::Init()
{
	string strLog, strInfo;

	//======Read Config File======//
	strLog = m_pConfigFile->GetData(HOSTGROUP,LOGPATH);
	strInfo = m_pConfigFile->GetData(HOSTGROUP,INFOPATH);
	m_bIsHostPart = false;
	if(m_pConfigFile->GetData(HOSTGROUP,PARTITION).compare("true") == 0)
		m_bIsHostPart = true;
	//=============================//
	ControllerConnect();
	m_pConfigObj = new CLogParserConfig(strLog.c_str(),strInfo.c_str());
}

void CHostLogParser::Destroy()
{
	delete m_pActiveHostController;
	delete m_pConfigObj;
	delete m_pConfigFile;
}

ConnectInfo CHostLogParser::GetConnectInfo(string DBType)
{
	ConnectInfo CInfo;
	CInfo.strHost = m_pConfigFile->GetData(DBType,HOST);
	CInfo.strUser = m_pConfigFile->GetData(DBType,USER);
	CInfo.strPass = m_pConfigFile->GetData(DBType,PASS);
	CInfo.strSource = m_pConfigFile->GetData(DBType,SRC);
	return CInfo;
}

bool CHostLogParser::ControllerConnect()
{
	ConnectInfo CInfo = GetConnectInfo(MYSQL_MDR);
	m_pActiveHostController = new CHostController(CInfo.strSource);

	if(!m_pActiveHostController->Connect(CInfo))
		return false;
	return true;
}

char* CHostLogParser::PreParsing(int &iLength, int &nCurPosition)
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

MA_RESULT CHostLogParser::ParseLog()
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
		strFormatLog = CUtilities::FormatLog(BUG_MSG, "HostParser", "ParserLog", strErrorMess.str());
		CUtilities::WriteErrorLog(strFormatLog);
	}
	
	//===========================Parse Log================================
	while(nCurPosition < iLength)
	{
		//cout<<"Length : "<<iLength<<endl;
		//cout << "nCurPosition: " << nCurPosition << endl;

		// Init database fields
		iZbxServerId = iStatus = iAvailable = iMaintenance = 0;
		lHostId = lMaintenanceFrom= 0;
		strHost = strName = "";
		
		m_pActiveHostController->ResetModel();
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
		
		//cout << "iZbxServerId: " << iZbxServerId << endl;
		m_pActiveHostController->ModelAppend(paraZbxServerId,iZbxServerId);

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

		m_pActiveHostController->ModelAppend(paraHostId,lHostId);
		
		lServerId = ((lHostId - 10000) * 256) + iZbxServerId;
		m_pActiveHostController->ModelAppend(paraServerId,lServerId);
		m_pActiveHostController->SetServerId(lServerId);
		
		//Parse Host
		strTemp = GetBlock((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			strHost = strTemp;
		}
		strHost = CUtilities::ReplaceBlockBracket(strHost);
		m_pActiveHostController->ModelAppend(paraHost, strHost);

		//Parse Name
		strTemp = GetBlock((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			strName = strTemp;
		}
		strName = CUtilities::ReplaceBlockBracket(strName);
		m_pActiveHostController->ModelAppend(paraName, strName);

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

		m_pActiveHostController->ModelAppend(paraStatus,iStatus);
		
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
		m_pActiveHostController->ModelAppend(paraAvailable,iAvailable);

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

		m_pActiveHostController->ModelAppend(paraMaintenance,iMaintenance);

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
		m_pActiveHostController->ModelAppend(paraMaintenanceFrom,lMaintenanceFrom);
		// cout << iZbxServerId << " " << lHostId << " " << strHost << " " << strName << " " << iStatus << " " << iAvailable << " " << iMaintenance << " " << lMaintenanceFrom << endl; 

		m_pActiveHostController->Save();

		if(nCurPosition >= iLength)
		{
			m_pConfigObj->SetPosition(iLength);
		}
	}
	if(!strFormatLog.empty())
	{
		strErrorMess.str(string());
		strErrorMess << "End:"<<nCurPosition<<"|"<<iLength<<endl;
		strFormatLog = CUtilities::FormatLog(BUG_MSG, "HostParser", "ParserLog", strErrorMess.str());
		CUtilities::WriteErrorLog(strFormatLog);
	}
	
	m_pConfigObj->ClearMapMem();
	return eResult;
}

MA_RESULT CHostLogParser::ProcessParse()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;
	while(true)
	{
		eResult = ParseLog();
		sleep(iParseSleepTime);
	}
	return eResult;
}
#include "TriggerLogParser.h"
#include "../Controller/TriggerController.h"
#include "../Model/TriggerModel.h"
#include "../Config/LogParserData.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

using namespace std;
CTriggerLogParser::CTriggerLogParser(void)
{
}

CTriggerLogParser::CTriggerLogParser(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
}

CTriggerLogParser::~CTriggerLogParser(void)
{
	Destroy();
}

void CTriggerLogParser::Init()
{
	string strLog, strInfo;
	
	//======Read Config File======//
	strLog = m_pConfigFile->ReadStringValue(TRIGGERGROUP,LOGPATH);
	strInfo = m_pConfigFile->ReadStringValue(TRIGGERGROUP,INFOPATH);
	m_bIsTriggerPart = false;
	if(m_pConfigFile->ReadStringValue(TRIGGERGROUP,PARTITION).compare("true") == 0)
		m_bIsTriggerPart = true;
	//=============================//
	
	m_pTriggerController = new CTriggerController();
	m_pTriggerModel = new CTriggerModel();
	m_pDataObj = new CLogParserData(strLog.c_str(),strInfo.c_str());
	ControllerConnect();
}

void CTriggerLogParser::Destroy()
{
	delete m_pTriggerController;
	delete m_pTriggerModel;
	delete m_pConfigFile;
	delete m_pDataObj;
}

ConnectInfo CTriggerLogParser::GetConnectInfo(string DBType)
{
	ConnectInfo CInfo;
	CInfo.strHost = m_pConfigFile->GetHost();
	CInfo.strUser = m_pConfigFile->GetUser();
	CInfo.strPass = m_pConfigFile->GetPassword();
	CInfo.strSource = m_pConfigFile->GetSource();
	CInfo.strHost = CInfo.strHost + ":" + m_pConfigFile->GetPort();
	return CInfo;
}

bool CTriggerLogParser::ControllerConnect()
{
	ConnectInfo CInfo = GetConnectInfo(MONGODB_MA);
	
	if(!m_pTriggerController->Connect(CInfo))
		return false;
	return true;
}

bool CTriggerLogParser::Reconnect()
{ 
	if(!m_pTriggerController->Reconnect())
		return false;
	return true;
}

void* CTriggerLogParser::PreParsing(int &iLength, int &nCurPosition)
{
	void *Buffer;
	Buffer = NULL;
	nCurPosition = m_pDataObj->GetPosition();
	if(nCurPosition == 0 && m_bIsTriggerPart == true)
	{
		m_pDataObj->SetNewLogFile();
		iLength = m_pDataObj->GetLength();
		Buffer = m_pDataObj->GetBuffer();
	}
	else
	{
		iLength = m_pDataObj->GetLength();
		if(nCurPosition < iLength)
		{
			Buffer = m_pDataObj->GetBuffer();
		}
		else if(m_bIsTriggerPart == true)
		{	
			if(iLength == 0)
			{
				nCurPosition = 0;
				m_pDataObj->SetNewLogFile();
				iLength = m_pDataObj->GetLength();
				Buffer = m_pDataObj->GetBuffer();
			}
			else if(nCurPosition == iLength)
			{
				m_pDataObj->SetNewLogFile();
				iLength = m_pDataObj->GetLength();
				if(nCurPosition != iLength) // new logfile of next day
				{
					nCurPosition = 0;
					Buffer = m_pDataObj->GetBuffer();
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

MA_RESULT CTriggerLogParser::ParseLog()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;
	void* Buffer;
	int nCurPosition, iLength, nMegaCount;
	string strTemp;
	int iZbxServerId, iStatus, iPriority, iValue;
	long long lClock, lTriggerId;
	string strExpression, strDescription;
	stringstream strErrorMess;
	
	Buffer = PreParsing(iLength, nCurPosition);
	
	// if(nCurPosition < iLength)
		// while(!Reconnect())
			// sleep(5);
	nMegaCount = (nCurPosition / 1000000) + 1;
	//===========================Parse Log================================
	while(nCurPosition < iLength)
	{
		m_pTriggerController->DestroyData();
		m_pTriggerModel->DestroyData();
		
		iPriority = iZbxServerId = iStatus = iValue = 0;
		lTriggerId = lClock = 0;
		strExpression = strDescription = "";
		
		//Parse ServerId
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{
				iZbxServerId = atoi(strTemp.c_str());
				m_pTriggerModel->SetZbxServerId(iZbxServerId);
			}
			catch(exception &ex)
			{	
				strErrorMess.str(string());
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		//Parse iTriggerId
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{								
				lTriggerId = atol(strTemp.c_str());
				m_pTriggerModel->SetTriggerId(lTriggerId);
			}
			catch(exception &ex)
			{	
				strErrorMess.str(string());
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		//Parse strExpression
		strTemp = GetExpression((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{	
			strExpression = strTemp;
			m_pTriggerModel->SetExpression(strExpression);
		}
		
		//Parse Description
		strTemp = GetTriggerDescription((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			strDescription = strTemp;
			m_pTriggerModel->SetDescription(strDescription);
		}
		else
		{
			GoToNewLine((const char*)Buffer, (int&)nCurPosition, (int)iLength);
			continue;
		}
		
		//Parse Status
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				iStatus = atoi(strTemp.c_str());
				m_pTriggerModel->SetStatus(iStatus);
			}
			catch(exception &ex)
			{	
				strErrorMess.str(string());
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		//Parse Value
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				iValue = atoi(strTemp.c_str());
				m_pTriggerModel->SetValue(iValue);
			}
			catch(exception &ex)
			{	
				strErrorMess.str(string());
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		//Parse Priority
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				iPriority = atoi(strTemp.c_str());
				m_pTriggerModel->SetPriority(iPriority);
			}
			catch(exception &ex)
			{	
				strErrorMess.str(string());
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		//Parse Clock
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				lClock = atol(strTemp.c_str());
				m_pTriggerModel->SetClock(lClock);
			}
			catch(exception &ex)
			{	
				strErrorMess.str(string());
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		// cout << iZbxServerId << " | " << lTriggerId << " | " << strExpression << " | " << strDescription << " | " << iStatus 
		// << " | " << iValue << " | " << iPriority << " | " << lClock << endl;
		m_pTriggerModel->PrepareRecord();
		m_pTriggerController->InsertDB(m_pTriggerModel->GetUniqueTriggerBson(), m_pTriggerModel->GetRecordBson());
		
		
		if(nCurPosition >= iLength || nCurPosition > 1000000*nMegaCount)
		{
			string strLog;
			stringstream strErrorMess;
			strErrorMess<<"Length:"<<iLength;
			strErrorMess<< "|nCurPosition:" << nCurPosition;
			strLog = CUtilities::FormatLog(LOG_MSG, "triggerProcess", "ParseLog",strErrorMess.str());
			CUtilities::WriteErrorLog(strLog);
			m_pDataObj->SetPosition(nCurPosition);
			nMegaCount++;
		}
	}
	if(Buffer != NULL)
		m_pDataObj->ClearMapMem();
	return eResult;
}

MA_RESULT CTriggerLogParser::ProcessParse()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;

	while(true)
	{
		eResult = ParseLog();
		sleep(iParseSleepTime);
	}
	return eResult;
}



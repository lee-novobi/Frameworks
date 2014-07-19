#include "EventLogParser.h"
#include "../Controller/EventController.h"
#include "../Model/EventModel.h"
#include "../Controller/TriggerController.h"
#include "../Model/TriggerModel.h"
#include "../Config/LogParserData.h"
#include "../Config/ConfigFileParse.h"
#include "../Common/DBCommon.h"

using namespace std;
CEventLogParser::CEventLogParser(void)
{
}

CEventLogParser::CEventLogParser(string strCfgFile)
{
	m_pConfigFile = new CConfigFileParse(strCfgFile);
	Init();
}

CEventLogParser::~CEventLogParser(void)
{
	Destroy();
}

void CEventLogParser::Init()
{
	string strLog, strInfo;
	
	//======Read Config File======//
	strLog = m_pConfigFile->ReadStringValue(EVENTGROUP,LOGPATH);
	strInfo = m_pConfigFile->ReadStringValue(EVENTGROUP,INFOPATH);
	m_bIsEventPart = false;
	if(m_pConfigFile->ReadStringValue(EVENTGROUP,PARTITION).compare("true") == 0)
		m_bIsEventPart = true;
	//=============================//
	
	m_pEventController = new CEventController();
	m_pEventModel = new CEventModel();
	m_pDataObj = new CLogParserData(strLog.c_str(),strInfo.c_str());
	
	ControllerConnect();
}

void CEventLogParser::Destroy()
{
	delete m_pEventController;
	delete m_pEventModel;
	delete m_pConfigFile;
	delete m_pDataObj;
}

ConnectInfo CEventLogParser::GetConnectInfo(string DBType)
{
	ConnectInfo CInfo;
	CInfo.strHost = m_pConfigFile->GetHost();
	CInfo.strUser = m_pConfigFile->GetUser();
	CInfo.strPass = m_pConfigFile->GetPassword();
	CInfo.strSource = m_pConfigFile->GetSource();
	CInfo.strHost = CInfo.strHost + ":" + m_pConfigFile->GetPort();
	return CInfo;
}

bool CEventLogParser::ControllerConnect()
{ 
	ConnectInfo CInfo = GetConnectInfo(MONGODB_MA);
	if(!m_pEventController->Connect(CInfo))
		return false;
	return true;
}

bool CEventLogParser::Reconnect()
{ 
	if(!m_pEventController->Reconnect())
		return false;
	return true;
}

void* CEventLogParser::PreParsing(int &iLength, int &nCurPosition)
{
	long long lTime;
	void *Buffer;
	Buffer = NULL;
	nCurPosition = m_pDataObj->GetPosition();
	if(nCurPosition == 0 && m_bIsEventPart == true)
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
		else if(m_bIsEventPart == true)
		{	
			if(iLength == 0)
			{
				nCurPosition = 0;
				m_pDataObj->SetNewLogFile();
				iLength = m_pDataObj->GetLength();
				Buffer 	= m_pDataObj->GetBuffer();
			}
			else if(nCurPosition == iLength)
			{
				lTime = atol(CUtilities::GetCurrTimeStamp().c_str());
				if( lTime%SEC_PER_DAY > 300 )
					m_pDataObj->SetNewLogFile();
				iLength = m_pDataObj->GetLength();
				if(nCurPosition != iLength) // new logfile of next day
				{
					if(nCurPosition > iLength)
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

MA_RESULT CEventLogParser::ParseLog()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;
	void* Buffer;
	int nCurPosition;
	int iLength;
	string strTemp;
	int iZbxServerId, iStatus, iValueChanged;
	long long lClock, lEventId, lTriggerId, lHostId, lItemId, lServerId;

	Buffer = PreParsing(iLength, nCurPosition);
	
	while(nCurPosition < iLength)
	{
		m_pEventController->DestroyData();
		m_pEventModel->DestroyData();
		
		// Init database fields
		iZbxServerId = iStatus = iValueChanged = 0;
		lClock = lEventId = lTriggerId = lHostId = lItemId = 0;
		
		//Parse Clock
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				lClock = atol(strTemp.c_str());
				m_pEventModel->SetClock(lClock);
			}
			catch(exception& ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		//Parse ZbxServerId
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				iZbxServerId = atoi(strTemp.c_str());
				m_pEventModel->SetZbxServerId(iZbxServerId);
			}
			catch(exception& ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		//Parse EventId
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				lEventId = atol(strTemp.c_str());
				m_pEventModel->SetEventId(lEventId);
			}
			catch(exception& ex)
			{
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		//Parse Status
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				iStatus = atoi(strTemp.c_str());
				m_pEventModel->SetStatus(iStatus);
			}
			catch(exception& ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		//Parse TriggerId
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				lTriggerId = atol(strTemp.c_str());
				m_pEventModel->SetTriggerId(lTriggerId);
			}
			catch(exception& ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
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
				m_pEventModel->SetHostId(lHostId);
			}
			catch(exception& ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		
		//ServerId
		lServerId = ((lHostId - 10000) * 256) + iZbxServerId;
		m_pEventModel->SetServerId(lServerId);
		
		//Parse ItemId
		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				lItemId = atol(strTemp.c_str());
				m_pEventModel->SetItemId(lItemId);
			}
			catch(exception& ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}

		strTemp = GetToken((const char*)Buffer, (int&)nCurPosition, (int)iLength);
		if(strTemp.compare("") != 0)
		{
			try
			{									
				iValueChanged = atoi(strTemp.c_str());
				m_pEventModel->SetValueChanged(iValueChanged);
			}
			catch(exception& ex)
			{	
				stringstream strErrorMess;
				strErrorMess << ex.what() << " " << __FILE__ << " " << __LINE__ << " | at : " <<  CUtilities::GetCurrTime() << endl;
				CUtilities::WriteErrorLog(strErrorMess.str());
			}
		}
		// cout << iZbxServerId << " | " << iStatus << " | " << lClock << " | " << lEventId << " | " << lTriggerId << " | " << lHostId << " | " << lItemId << endl;
		
		m_pEventModel->PrepareRecord();
		m_pEventController->InsertDB(m_pEventModel->GetUniqueEventBson(), m_pEventModel->GetRecordBson());
		
		if(nCurPosition >= iLength)
		{
			string strLog;
			stringstream strErrorMess;
			strErrorMess<<"Length:"<<iLength;
			strErrorMess<< "|nCurPosition:" << nCurPosition;
			strLog = CUtilities::FormatLog(LOG_MSG, "eventProcess", "ParseLog",strErrorMess.str());
			CUtilities::WriteErrorLog(strLog);
			m_pDataObj->SetPosition(iLength);
		}
	}
	
	if(Buffer != NULL)
		m_pDataObj->ClearMapMem();
	return eResult;
}

MA_RESULT CEventLogParser::ProcessParse()
{
	MA_RESULT eResult = MA_RESULT_UNKNOWN;

	while(true)
	{
		eResult = ParseLog();
		sleep(iParseSleepTime);
	}
	return eResult;
}




